<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

edituser.php
- change permissions, password

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

$conn->selectDB("mysql");

if (isset($_POST['editParts']))
{
	$editParts = $_POST['editParts'];
	
	$editParts = explode("; ", $editParts);
	
	$totalParts = count($editParts);
	$counter = 0;
	
	$firstField = true;
	
	foreach ($editParts as $part)
	{
		
		$part = trim($part);
		
		if ($part != "" && $part != ";")
		{
			
			list($user, $host) = explode("@", $part);
			
			$userSQL = $conn->query("SELECT * FROM `user` WHERE `User`='" . $user . "' AND `Host`='" . $host . "'");
			
			if ($conn->rowCount($userSQL))
			{
				$userRow = $conn->fetchAssoc($userSQL);
				
				$allPrivs = true;
				
				foreach ($userRow as $key=>$value)
				{
					if (substr($key, -5) == "_priv" && $key != "Grant_priv" && $value == "N")
					{
						$allPrivs = false;
					}
				}
				
				echo '<form id="editform' . $counter . '" querypart="' . $part . '" onsubmit="saveUserEdit(\'editform' . $counter . '\'); return false;">';
				echo '<div class="edituser">';
				echo '<div class="errormessage" style="margin: 0 7px 13px; display: none"></div>';
				echo '<table class="edit" cellspacing="0" cellpadding="0">';
				
				?>
				
				<tr>
					<td class="secondaryheader" width="30"><?php echo __("User"); ?>:</td>
					<td><strong><?php echo $part; ?></strong></td>
				</tr>
				</table>
				
				<div style="padding-top: 5px">
				<label><input type="radio" name="CHOICE" value="ALL" onchange="updatePane('EDITPRIVSELECTED<?php echo $counter; ?>', 'editprivilegepane<?php echo $counter; ?>')" onclick="updatePane('EDITPRIVSELECTED<?php echo $counter; ?>', 'editprivilegepane<?php echo $counter; ?>')" <?php if ($allPrivs) echo 'checked="checked"'; ?> /><?php echo __("All privileges"); ?></label><br />
				<label><input type="radio" name="CHOICE" value="SELECTED" id="EDITPRIVSELECTED<?php echo $counter; ?>" onchange="updatePane('EDITPRIVSELECTED<?php echo $counter; ?>', 'editprivilegepane<?php echo $counter; ?>')" onclick="updatePane('EDITPRIVSELECTED<?php echo $counter; ?>', 'editprivilegepane<?php echo $counter; ?>')" <?php if (!$allPrivs) echo 'checked="checked"'; ?> /><?php echo __("Selected privileges"); ?></label>
				</div>
				
				<div id="editprivilegepane<?php echo $counter; ?>" <?php if ($allPrivs) echo 'style="display: none"'; ?>>
				<div class="paneheader">
				<?php echo __("User privileges"); ?>&nbsp;&nbsp;[<a onclick="paneCheckAll('edituserprivs<?php echo $counter; ?>')"><?php echo __("All"); ?></a> / <a onclick="paneCheckNone('edituserprivs<?php echo $counter; ?>')"><?php echo __("None"); ?></a>]
				</div>
				<table cellpadding="0" id="edituserprivs<?php echo $counter; ?>">
				<tr>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="SELECT" <?php if ($userRow['Select_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Select"); ?></label>
					</td>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="INSERT" <?php if ($userRow['Insert_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Insert"); ?></label>
					</td>
					<td width="34%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="UPDATE" <?php if ($userRow['Update_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Update"); ?></label>
					</td>
				</tr>
				<tr>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="DELETE" <?php if ($userRow['Delete_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Delete"); ?></label>
					</td>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="INDEX" <?php if ($userRow['Index_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Index"); ?></label>
					</td>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="ALTER" <?php if ($userRow['Alter_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Alter"); ?></label>
					</td>
				</tr>
				<tr>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="CREATE" <?php if ($userRow['Create_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Create"); ?></label>
					</td>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="DROP" <?php if ($userRow['Drop_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Drop"); ?></label>
					</td>
					<td>
					<label><input type="checkbox" name="PRIVILEGES[]" value="CREATE TEMPORARY TABLES" <?php if ($userRow['Create_tmp_table_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Temp tables"); ?></label>
					</td>
				</tr>
				</table>
				<div class="paneheader">
				<?php echo __("Administrator privileges"); ?>&nbsp;&nbsp;[<a onclick="paneCheckAll('editadminprivs<?php echo $counter; ?>')"><?php echo __("All"); ?></a> / <a onclick="paneCheckNone('editadminprivs<?php echo $counter; ?>')"><?php echo __("None"); ?></a>]
				</div>
				<table cellpadding="0" id="editadminprivs<?php echo $counter; ?>">
				<tr>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="FILE" <?php if ($userRow['File_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("File"); ?></label>
					</td>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="PROCESS" <?php if ($userRow['Process_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Process"); ?></label>
					</td>
					<td width="34%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="RELOAD" <?php if ($userRow['Reload_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Reload"); ?></label>
					</td>
				</tr>
				<tr>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="SHUTDOWN" <?php if ($userRow['Shutdown_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Shutdown"); ?></label>
					</td>
					<td width="33%">
					<label><input type="checkbox" name="PRIVILEGES[]" value="SUPER" <?php if ($userRow['Super_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Super"); ?></label>
					</td>
					<td width="34%">
					</td>
				</tr>
				</table>
				</div>
				<div class="paneheader">
				<?php echo __("Options"); ?>
				</div>
				<label><input type="checkbox" name="GRANTOPTION" value="true" <?php if ($userRow['Grant_priv'] == "Y") echo 'checked="checked"'; ?> /><?php echo __("Grant option"); ?></label>
				
				<div style="margin-top: 10px; height: 22px; padding: 4px 0">
					<input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" />&nbsp;&nbsp;<a onclick="cancelEdit('editform<?php echo $counter; ?>')"><?php echo __("Cancel"); ?></a>
				</div>
				</div>
				</form>
			
			<?php
			
			}
			else
			{
				echo __("User not found!");
			}
			
			$counter++;
		}
	}
	
}

?>