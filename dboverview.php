<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

dboverview.php
- database main page - list tables and create new table

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

if (isset($db))
{

mysql_select_db($db);

//run delete queries

if (isset($_POST['runQuery']))
{
	
	$runQuery = $_POST['runQuery'];
	
	$queryList = splitQueryText($runQuery);
	
	foreach ($queryList as $query)
	{
		$query = trim($query);
		
		if ($query != "")
		{
			mysql_query($query) or ($mysqlError = mysql_error());
			
			// make a list of the tables that were dropped/emptied
			if (substr($query, 0, 12) == "DROP TABLE `")
				$droppedList[] = substr($query, 12, -1);
			
			if (substr($query, 0, 10) == "TRUNCATE `")
				$emptiedList[] = substr($query, 10, -1);
			
		}
	}
}

// if tables were dropped, remove them from the side menu
if (isset($droppedList) && isset($db))
{
	?>
	
	<script type="text/javascript" authkey="<?php echo $requestKey; ?>">
	
	var targ = $(getSubMenuId('<?php echo $db . "','" . $droppedList[0]; ?>'));
	while (!targ.hasClass("sublist"))
	{
		targ = targ.parentNode;
	}
	var toRecalculate = targ.id;
	
	<?php
	for ($mn=0; $mn<count($droppedList); $mn++)
	{
	?>
		$(getSubMenuId('<?php echo $db . "','" . $droppedList[$mn]; ?>')).dispose();
	<?php
	}
	?>
	
	recalculateSubmenuHeight(toRecalculate);
	
	</script>
	
	<?php
}

// if tables were emptied, reset their counts in js
if (isset($emptiedList) && isset($db))
{
	?>
	
	<script type="text/javascript" authkey="<?php echo $requestKey; ?>">
	<?php
	
	for ($mn=0; $mn<count($emptiedList); $mn++)
	{
		echo "sb.tableRowCounts[\"" . $db . "_" . $emptiedList[$mn] . "\"] = \"0\";\n";
		echo "var sideA = $(getSubMenuId('" . $db . "', '" . $emptiedList[$mn] . "'));\n";
		echo 'var subc = $E(".subcount", sideA);';
		echo "\nsubc.set(\"text\", \"(0)\");\n";
	}
	
	?>
	</script>
	
	<?php
}


if (isset($mysqlError))
{
	echo '<div class="errormessage" style="margin: 6px 12px 10px 7px; width: 550px"><strong>';
	echo __("Error performing operation");
	echo '</strong><p>' . $mysqlError . '</p></div>';
}

?>

<table cellpadding="0" class="dboverview" width="700" style="margin: 5px 7px 0">
<tr>
<td>

<?php

$tableSql = mysql_query("SHOW TABLES");

if (@mysql_num_rows($tableSql))
{
	
	echo '<div style="margin-bottom: 15px">';
	
	echo '<table class="browsenav">';
	echo '<tr>';
	echo '<td class="options">';
	
	echo __("Select") . ':&nbsp;&nbsp;<a onclick="checkAll()">' . __("All") . '</a>&nbsp;&nbsp;<a onclick="checkNone()">' . __("None") . '</a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __("With selected") . ':&nbsp;&nbsp;<a onclick="emptySelectedTables()">' . __("Empty") . '</a>&nbsp;&nbsp;<a onclick="dropSelectedTables()">' . __("Drop") . '</a>&nbsp;&nbsp;<a onclick="optimizeSelectedTables()">' . __("Optimize") . '</a>';
	
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	
	echo '<div class="grid">';
	
	echo '<div class="emptyvoid">&nbsp;</div>';
	
	echo '<div class="gridheader impotent">';
		echo '<div class="gridheaderinner">';
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr>';
		echo '<td><div column="1" class="headertitle column1">' . __("Table") . '</div></td>';
		echo '<td><div class="columnresizer"></div></td>';
		echo '<td><div column="2" class="headertitle column2">' . __("Rows") . '</div></td>';
		echo '<td><div class="columnresizer"></div></td>';
		
		if (isset($charsetList) && isset($collationList))
		{
			echo '<td><div column="3" class="headertitle column3">' . __("Charset") . '</div></td>';
			echo '<td><div class="columnresizer"></div></td>';
			echo '<td><div column="4" class="headertitle column4">' . __("Overhead") . '</div></td>';
			echo '<td><div class="columnresizer"></div></td>';
		}
		else
		{
			echo '<td><div column="3" class="headertitle column4">' . __("Overhead") . '</div></td>';
			echo '<td><div class="columnresizer"></div></td>';
		}
		
		echo '<td><div class="emptyvoid" style="border-right: 0">&nbsp;</div></td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="leftchecks" style="max-height: 400px">';
	
	$m = 0;
	
	while ($tableRow = mysql_fetch_array($tableSql))
	{
		echo '<dl class="manip';
		
		if ($m % 2 == 1)
			echo ' alternator';
		else 
			echo ' alternator2';
		
		echo '"><dt><input type="checkbox" class="check' . $m . '" onclick="rowClicked(' . $m++ . ')" querybuilder="' . $tableRow[0] . '" /></dt></dl>';
	}
	
	echo '</div>';
	
	mysql_data_seek($tableSql, 0);
	
	echo '<div class="gridscroll withchecks" style="overflow-x: hidden; max-height: 400px">';
	
	$m = 0;
	
	while ($tableRow = mysql_fetch_row($tableSql))
	{
		
		$countSql = mysql_query("SELECT COUNT(*) AS `RowCount` FROM `" . $tableRow[0] . "`");
		$rowCount = (int)(@mysql_result($countSql, 0, "RowCount"));
		
		$infoSql = mysql_query("SHOW TABLE STATUS LIKE '" . $tableRow[0] . "'");
		$infoRow = mysql_fetch_assoc($infoSql);
		
		$overhead = $infoRow["Data_free"];
		
		$formattedOverhead = "";
		
		if ($overhead > 0)
			$formattedOverhead = memoryFormat($overhead);
		
		echo '<div class="row' . $m . ' browse';
		
		if ($m % 2 == 1)
		{ echo ' alternator'; }
		else 
		{ echo ' alternator2'; }
		
		echo '">';
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr>';
		echo '<td><div class="item column1"><img src="images/goto.png" class="goto" onclick="subTabLoad(\'' . $db . '\', \'' . $tableRow[0] . '\')" align="right" />' . $tableRow[0] . '</div></td>';
		echo '<td><div class="item column2">' . number_format($rowCount) . '</div></td>';
		
		if (isset($collationList) && array_key_exists("Collation", $infoRow))
		{
			echo '<td><div class="item column3">' . $collationList[$infoRow['Collation']] . '</div></td>';
			echo '<td><div class="item column4">' . $formattedOverhead . '</div></td>';
		}
		else
		{
			echo '<td><div class="item column4">' . $formattedOverhead . '</div></td>';
		}
		
		echo '</tr>';
		echo '</table>';
		echo '</div>';
		
		$m++;
	}
	
	echo '</div>';
	echo '</div>';
	
	echo '<br />';
	
}

?>

<div class="inputbox" style="width: 275px; margin-bottom: 15px">
<h4><?php echo __("Options"); ?></h4>

<a onclick="confirmDropDatabase()"><?php printf(__("Drop the '%s' database"), $db); ?></a>
</div>

<?php

if (isset($charsetList))
{

$currentChar = "";
$currentCharSql = mysql_query("SHOW VARIABLES LIKE 'character_set_database'");

if (@mysql_num_rows($currentCharSql))
{
	$currentChar = mysql_result($currentCharSql, 0, "Value");
}

?>

<div class="inputbox" style="width: 325px; margin-bottom: 15px">
<h4><?php echo __("Edit database"); ?></h4>

<div id="editDatabaseMessage"></div>
<form onsubmit="editDatabase(); return false">
<table cellpadding="4">

<?php

	echo "<tr>";
	echo "<td class=\"secondaryheader\">";
	echo __("Charset") . ":";
	echo "</td>";
	echo "<td class=\"inputarea\">";
	echo "<select id=\"DBRECHARSET\" style=\"width: 145px\">";
	echo "<option></option>";
	foreach ($charsetList as $charset)
	{
		echo "<option value=\"" . $charset . "\"";
		
		if (isset($currentChar) && $charset == $currentChar)
			echo " selected=\"selected\"";
		
		echo ">" . $charset . "</option>";
	}
	echo "</select>";
	echo "</td>";
	echo '<td align="left" style="padding-left: 10px">';
	echo '<input type="submit" class="inputbutton" value="' . __("Submit") . '" />';
	echo '</td>';
	echo "</tr>";

?>

</table>
</form>
</div>

<?php

}

?>

<div id="reporterror" class="errormessage" style="display: none; margin-bottom: 15px"></div>

<div class="inputbox">
	<h4><?php echo __("Create a new table"); ?></h4>
	
	<form onsubmit="createTable(); return false">
	<table cellpadding="0" style="width: 300px">
	<tr>
		<td class="secondaryheader" style="width: 80px">
		<?php echo __("Name") ?>:
		</td>
		<td>
		<input type="text" class="text" id="TABLENAME" style="width: 150px" />
		</td>
	</tr>
	<?php
	
	if (isset($charsetList))
	{
		echo "<tr>";
		echo "<td class=\"secondaryheader\" style=\"width: 60px\">";
		echo __("Charset") . ":";
		echo "</td>";
		echo "<td>";
		echo "<select id=\"TABLECHARSET\" style=\"width: 155px\">";
		echo "<option></option>";
		foreach ($charsetList as $charset)
		{
			echo "<option value=\"" . $charset . "\"";
			
			if (isset($currentChar) && $charset == $currentChar)
				echo " selected=\"selected\"";
			
			echo ">" . $charset . "</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
	}
	
	?>
	<tr>
		<td style="padding-top: 5px; color: gray" colspan="2">
		<?php echo __("Setup the fields for the table below"); ?>:
		</td>
	</tr>
	</table>
	<div id="fieldlist">
		
		<div class="fieldbox">
		<table cellpadding="0" class="overview">
		<tr>
		<td colspan="4" class="fieldheader">
		<span class="fieldheadertitle">&lt;<?php echo __("New field"); ?>&gt;</span>
		<a class="fieldclose" onclick="removeField(this)"></a>
		</td>
		</tr>
		<tr>
		<td class="secondaryheader">
		<?php echo __("Name"); ?>:
		</td>
		<td>
		<input type="text" class="text" name="NAME" onkeyup="updateFieldName(this)" />
		</td>
		<td class="secondaryheader" style="padding-left: 5px">
		<?php echo __("Type"); ?>:
		</td>
		<td>
		<select name="TYPE" onchange="updateFieldName(this); toggleValuesLine(this)">
		<?php
		
		foreach ($typeList as $type)
		{
			echo '<option value="' . $type . '">' . $type . '</option>';
		}
		
		?>
		</select>
		</td>
		</tr>
		<tr class="valueline" style="display: none">
		<td class="secondaryheader">
		<?php echo __("Values"); ?>:
		</td>
		<td class="inputarea">
		<input type="text" class="text" name="VALUES" onkeyup="updateFieldName(this)" />
		</td>
		<td colspan="2" style="color: gray">
		<?php echo __("Enter in the format: ('1','2')"); ?>
		</td>
		</tr>
		<tr>
		<td class="secondaryheader">
		<?php echo __("Size") ?>:
		</td>
		<td class="inputarea">
		<input type="text" class="text" name="SIZE" onkeyup="updateFieldName(this)" />
		</td>
		<td class="secondaryheader" style="padding-left: 5px">
		<?php echo __("Key"); ?>:
		</td>
		<td class="inputarea">
		<select name="KEY" onchange="updateFieldName(this)">
		<option value=""></option>
		<option value="primary"><?php echo __("primary"); ?></option>
		<option value="unique"><?php echo __("unique"); ?></option>
		<option value="index"><?php echo __("index"); ?></option>
		</select>
		</td>
		</tr>
		<tr>
		<td class="secondaryheader">
		<?php echo __("Default") ?>:
		</td>
		<td class="inputarea">
		<input type="text" class="text" name="DEFAULT" onkeyup="updateFieldName(this)" />
		</td>
		<?php
		
		if (isset($charsetList))
		{
			echo "<td class=\"secondaryheader\" style=\"padding-left: 5px\">";
			echo __("Charset") . ":";
			echo "</td>";
			echo "<td class=\"inputarea\">";
			echo "<select name=\"CHARSET\" onchange=\"updateFieldName(this)\">";
			echo "<option></option>";
			foreach ($charsetList as $charset)
			{
				echo "<option value=\"" . $charset . "\">" . $charset . "</option>";
			}
			echo "</select>";
			echo "</td>";
		}
		else
		{
			echo "<td></td>";
			echo "<td></td>";
		}
		
		?>
		</tr>
		<tr>
		<td class="secondaryheader">
		<?php echo __("Other"); ?>:
		</td>
		<td colspan="3">
		<label><input type="checkbox" name="UNSIGN" onchange="updateFieldName(this)"><?php echo __("Unsigned"); ?></label>
		<label><input type="checkbox" name="BINARY" onchange="updateFieldName(this)"><?php echo __("Binary"); ?></label>
		<label><input type="checkbox" name="NOTNULL" onchange="updateFieldName(this)"><?php echo __("Not Null"); ?></label>
		<label><input type="checkbox" name="AUTO" onchange="updateFieldName(this)"><?php echo __("Auto Increment"); ?></label>
		</td>
		</tr>
		</table>
		</div>
		
	</div>
		
	<table cellpadding="0" width="370" id="fieldcontrols">
	<tr>
	<td style="padding: 5px 0 4px">
	<input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" />
	</td>
	<td style="padding: 0px 4px 0" align="right" valign="top">
	<a onclick="addTableField()" style="font-size: 11px !important"><?php echo __("Add field"); ?></a><div style="visibility: hidden; height: 0"><input type="submit" /></div>
	</td>
	</tr>
	</table>
	</form>
	
</div>

</td>
</table>

<script type="text/javascript" authkey="<?php echo $requestKey; ?>">
setTimeout("startGrid()", 1);
</script>

<?php

}else{
	
	?>
	
	<div class="errorpage">
	<h4><?php echo __("Oops"); ?></h4>
	<p><?php echo __("For some reason, the database parameter was not included with your request."); ?></p>
	</div>
	
	<?php
	exit;
	
}

?>