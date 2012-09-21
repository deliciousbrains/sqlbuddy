<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

edit.php
- edit specific rows from a database table

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

requireDatabaseAndTableBeDefined();

if (isset($db))
	$conn->selectDB($db);

if (isset($table))
	$structureSql = $conn->describeTable($table);

if (isset($_POST['editParts'])) {
	$editParts = $_POST['editParts'];
	$editParts = explode("; ", $editParts);
	
	$totalParts = count($editParts);
	$counter = 0;
	
	$firstField = true;
	
	?>
	<script type="text/javascript" authkey="<?php echo $requestKey; ?>">
	
	if ($('EDITFIRSTFIELD')) {
		$('EDITFIRSTFIELD').focus();
	}
	
	</script>
	<?php
	
	foreach ($editParts as $part) {
		
		$part = trim($part);
		
		if ($part != "" && $part != ";") {
		
		?>
		
		<form id="editform<?php echo $counter; ?>" querypart="<?php echo $part; ?>" onsubmit="saveEdit('editform<?php echo $counter; ?>'); return false;" style="border-bottom: 1px solid grey; float: left;">
		<div class="errormessage" style="margin: 6px 12px 10px; width: 338px; display: none"></div>
		<div class="insert edit" style="width: 100%; float: left;">
		<?php
		
		if ($conn->isResultSet($structureSql) && $conn->getAdapter() == "mysql") {
			
			$dataSql = $conn->query("SELECT * FROM `" . $table . "` " . $part);
			$dataRow = $conn->fetchAssoc($dataSql);
			
			$count = 0;
			$column[0] = $column[1] = '';
			while ($structureRow = $conn->fetchAssoc($structureSql))
			{
				$count = 1-$count;
				
				preg_match("/^([a-z]+)(.([0-9]+).)?(.*)?$/", $structureRow['Type'], $matches);
				
				$curtype = $matches[1];
				$cursizeQuotes = $matches[2];
				$cursize = $matches[3];
				$curextra = $matches[4];
				
				$column[$count] .= '<div class="item-container">
					<div class="fieldheader"><span style="color: steelblue">';
				
				if ($structureRow['Key'] == 'PRI')
				{
					 $column[$count] .= '<u>'.$structureRow['Field'].'</u>';
				}
				else
				{
					$column[$count] .= $structureRow['Field'];
				}

				$column[$count] .= "</span> " . $curtype . $cursizeQuotes . ' ' . $structureRow['Extra'] . '</div>
					<div class="inputarea">';
				
				$showLargeEditor[] = "text";
				$showLargeEditor[] = "mediumtext";
				$showLargeEditor[] = "longtext";
				
				if (in_array($curtype, $showLargeEditor)) {
					$column[$count] .= '<textarea name="' . $structureRow['Field'] . '">' . htmlentities($dataRow[$structureRow['Field']], ENT_QUOTES, 'UTF-8') . '</textarea>';
				}
				elseif ($curtype == "enum") {
					$trimmed = substr($structureRow['Type'], 6, -2);
					$listOptions = explode("','", $trimmed);
					$column[$count] .=  '<select name="' . $structureRow['Field'] . '">';
					$column[$count] .=  '<option> - - - - - </option>';
					foreach ($listOptions as $option) {
						$column[$count] .=  '<option value="' . $option . '"';
						if ($option == $dataRow[$structureRow['Field']]) {
							$column[$count] .=  ' selected="selected"';
						}
						$column[$count] .=  '>' . $option . '</option>';
					}
					$column[$count] .=  '</select>';
				}
				elseif ($curtype == "set") {
					$trimmed = substr($structureRow['Type'], 5, -2);
					$listOptions = explode("','", $trimmed);
					foreach ($listOptions as $option) {
						$id = $option . rand(1, 1000);
						$column[$count] .=  '<label for="' . $id . '"><input name="' . $structureRow['Field'] . '[]" value="' . $option . '" id="' . $id . '" type="checkbox"';
						
						if (strpos($dataRow[$structureRow['Field']], $option) > -1)
							$column[$count] .=  ' checked="checked"';
						
						$column[$count] .=  '>' . $option . '</label><br />';
					}
				} else {
					$column[$count] .=  '<input type="text"';
					if ($firstField)
						$column[$count] .= ' id="EDITFIRSTFIELD"';
					$column[$count] .= ' name="' . $structureRow['Field'] . '" class="text" value="';
					
					if ($dataRow[$structureRow['Field']] && isset($binaryDTs) && in_array($curtype, $binaryDTs)) {
						$column[$count] .= "0x" . bin2hex($dataRow[$structureRow['Field']]);
					} else {
						$column[$count] .= htmlentities($dataRow[$structureRow['Field']], ENT_QUOTES, 'UTF-8');
					}
					
					$column[$count] .= '" />';
				}
				$column[$count] .= "</div></div>";
				
				$firstField = false;
				
			}
			
			echo "<div class='column' style='float:left'>".$column[1]."</div><div class='column' style='float:left; margin-right: 25px;'>".$column[0]."</div><br style='clear: left;'/></div>";
				
			$structureSql = $conn->describeTable($table);
			
		} else if (sizeof($structureSql) > 0 && $conn->getAdapter() == "sqlite") {
			
			$dataSql = $conn->query("SELECT * FROM '" . $table . "' " . $part);
			$dataRow = $conn->fetchAssoc($dataSql);
			
			foreach ($structureSql as $column) {
								
				echo '<tr>';
				echo '<td class="fieldheader"><span style="color: steelblue">';
				if (strpos($column[1], "primary key") > 0) echo '<u>';
				echo $column[0];
				if (strpos($column[1], "primary key") > 0) echo '</u>';
				echo "</span> " . $column[1] . '</td>';
				echo '</tr>';
				echo '<tr>';
				echo '<td class="inputarea">';
				
				if (strpos($column[1], "text") !== false) {
					echo '<textarea name="' . $column[0] . '">' . $dataRow[$column[0]] . '</textarea>';
				} else {
					echo '<input type="text"';
					if ($firstField)
						echo ' id="EDITFIRSTFIELD"';
					echo ' name="' . $column[0] . '" class="text" value="' . htmlentities($dataRow[$column[0]], ENT_QUOTES, 'UTF-8') . '" />';
				}
				
				$firstField = false;
				
				?>
				
				</div>
				
				<?php
			}
			
			$structureSql = $conn->describeTable($table);
			
		}
		
		?>
		<div style="float: left;">
		<label><input type="radio" name="SB_INSERT_CHOICE" value="SAVE" checked="checked" /><?php echo __("Save changes to original"); ?></label><br />
		<label><input type="radio" name="SB_INSERT_CHOICE" value="INSERT" /><?php echo __("Insert as new row"); ?></label>
		</div>
		<div style="padding-top: 10px; padding-bottom: 25px; float: left;">
		<input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" />&nbsp;&nbsp;<a onclick="cancelEdit('editform<?php echo $counter; ?>')"><?php echo __("Cancel"); ?></a>
		</div>
		</form>
		
		
		<?php
		
		$counter++;
		
		}
		
	}
	
}

?>