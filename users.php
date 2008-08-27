<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

users.php
- manage users page

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

mysql_select_db("mysql");

if ($_POST)
{
	
	if (isset($_POST['NEWHOST']))
		$newHost = $_POST['NEWHOST'];
	else
		$newHost = "localhost";
	
	if (isset($_POST['NEWNAME']))
		$newName = $_POST['NEWNAME'];
	
	if (isset($_POST['NEWPASS']))
		$newPass = $_POST['NEWPASS'];
	
	if (isset($_POST['NEWCHOICE']))
		$newChoice = $_POST['NEWCHOICE'];
	
	if (isset($_POST['NEWPRIVILEGES']))
		$newPrivileges = $_POST['NEWPRIVILEGES'];
	
	if (isset($newName))
	{
		
		if ($newChoice == "ALL")
		{
			$privList = "ALL";
		}
		else
		{
			if (count($newPrivileges) > 0)
				$privList = implode(", ", $newPrivileges);
			else
				$privList = "USAGE";
			
		}
		
		$newQuery = "GRANT " . $privList . " ON *.* TO '" . $newName . "'@'" . $newHost . "'";
		
		if ($newPass)
			$newQuery .= " IDENTIFIED BY '" . $newPass . "'";
		
		if (isset($_POST['GRANTOPTION']))
			$newQuery .= " WITH GRANT OPTION";
		
		mysql_query($newQuery) or ($mysqlError = mysql_error());
		mysql_query("FLUSH PRIVILEGES") or ($mysqlError = mysql_error());
		
	}
}

// delete users

if (isset($_POST['deleteUsers']))
{
	$deleteUsers = $_POST['deleteUsers'];
	
	// boom!
	$userList = explode(";", $deleteUsers);
	
	foreach ($userList as $each)
	{
		$split = explode("@", $each, 2);
		
		if (isset($split[0]))
			$user = trim($split[0]);
		
		if (isset($split[1]))
			$host = trim($split[1]);
		
		if (isset($user) && isset($host))
		{
			mysql_query("REVOKE ALL PRIVILEGES ON *.* FROM '$user'@'$host'");
			mysql_query("REVOKE GRANT OPTION ON *.* FROM '$user'@'$host'");
			mysql_query("DELETE FROM `user` WHERE `User`='$user' AND HOST='$host'");
		}
	}
	mysql_query("FLUSH PRIVILEGES");
}

if (isset($mysqlError))
{
	echo '<div class="errormessage" style="margin: 6px 12px 10px 7px; width: 550px">';
	echo '<strong>' . __("Error performing operation") . '</strong><p>' . $mysqlError . '</p>';
	echo '</div>';
}

$userSql = mysql_query("SELECT * FROM `user`");

?>

<div class="users">

<?php

if (@mysql_num_rows($userSql))
{
	
	?>
	
	<table class="browsenav">
	<tr>
	<td class="options">
	<?php
	
	echo __("Select") . ':&nbsp;&nbsp;<a onclick="checkAll()">' . __("All") . '</a>&nbsp;&nbsp;<a onclick="checkNone()">' . __("None") . '</a>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __("With selected") . ':&nbsp;&nbsp;<a onclick="editSelectedRows()">' . __("Edit") . '</a>&nbsp;&nbsp;<a onclick="deleteSelectedUsers()">' . __("Delete") . '</a>';
	
	?>
	
	</td>
	</tr>
	</table>
	
	<?php
	
	echo '<div class="grid">';
	
	echo '<div class="emptyvoid">&nbsp;</div>';
	
	echo '<div class="gridheader impotent">';
		echo '<div class="gridheaderinner">';
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr>';
		echo '<td><div column="1" class="headertitle column1">' . __("Host") . '</div></td>';
		echo '<td><div class="columnresizer"></div></td>';
		echo '<td><div column="2" class="headertitle column2">' . __("User") . '</div></td>';
		echo '<td><div class="columnresizer"></div></td>';
		echo '</tr>';
		echo '</table>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="leftchecks" style="max-height: 400px">';
	
	$m = 0;
	
	while ($userRow = mysql_fetch_array($userSql))
	{
		$queryBuilder = $userRow['User'] . "@" . $userRow['Host'];
		echo '<dl class="manip';
		
		if ($m % 2 == 1)
			echo ' alternator';
		else 
			echo ' alternator2';
		
		echo '"><dt><input type="checkbox" class="check' . $m . '" onclick="rowClicked(' . $m++ . ')" querybuilder="' . $queryBuilder . '" /></dt></dl>';
	}
	
	echo '</div>';
	
	mysql_data_seek($userSql, 0);
	
	echo '<div class="gridscroll withchecks" style="overflow-x: hidden; max-height: 400px">';
	
	if (@mysql_num_rows($userSql))
	{
		$m = 0;
		
		while ($userRow = mysql_fetch_assoc($userSql))
		{
			
			echo '<div class="row' . $m . ' browse';
			
			if ($m % 2 == 1)
			{ echo ' alternator'; }
			else 
			{ echo ' alternator2'; }
			
			echo '">';
			echo '<table cellspacing="0" cellpadding="0">';
			echo '<tr>';
			echo '<td><div class="item column1">' . $userRow['Host'] . '</div></td>';
			echo '<td><div class="item column2">' . $userRow['User'] . '</div></td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
			
			$m++;
		}
	}
	
	echo '</div>';
	echo '</div>';

}

?>

<div class="inputbox" style="margin-top: 15px">
	<h4><?php echo __("Add a new user"); ?></h4>
		
	<form id="NEWUSERFORM" onsubmit="submitForm('NEWUSERFORM'); return false">
	<table cellpadding="0">
	<tr>
		<td class="secondaryheader"><?php echo __("Host"); ?>:</td>
		<td><input type="text" class="text" name="NEWHOST" value="localhost" /></td>
	</tr>
	<tr>
		<td class="secondaryheader"><?php echo __("Name"); ?>:</td>
		<td><input type="text" class="text" name="NEWNAME" /></td>
	</tr>
	<tr>
		<td class="secondaryheader"><?php echo __("Password"); ?>:</td>
		<td><input type="password" class="text" name="NEWPASS" /></td>
	</tr>
	</table>
	
	<div style="padding-top: 5px">
	<label><input type="radio" name="NEWCHOICE" value="ALL" onchange="updatePane('PRIVSELECTED', 'privilegepane')" onclick="updatePane('PRIVSELECTED', 'privilegepane')" checked="checked" /><?php echo __("All privileges"); ?></label><br />
	<label><input type="radio" name="NEWCHOICE" value="SELECTED" id="PRIVSELECTED" onchange="updatePane('PRIVSELECTED', 'privilegepane')" onclick="updatePane('PRIVSELECTED', 'privilegepane')" /><?php echo __("Selected privileges"); ?></label>
	</div>
	
	<div id="privilegepane" style="display: none">
	<div class="paneheader">
	<?php echo __("User privileges"); ?>&nbsp;&nbsp;[<a onclick="paneCheckAll('userprivs')"><?php echo __("All"); ?></a> / <a onclick="paneCheckNone('userprivs')"><?php echo __("None"); ?></a>]
	</div>
	<table cellpadding="0" id="userprivs">
	<tr>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="SELECT" /><?php echo __("Select"); ?></label>
		</td>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="INSERT" /><?php echo __("Insert"); ?></label>
		</td>
		<td width="34%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="UPDATE" /><?php echo __("Update"); ?></label>
		</td>
	</tr>
	<tr>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="DELETE" /><?php echo __("Delete"); ?></label>
		</td>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="INDEX" /><?php echo __("Index"); ?></label>
		</td>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="ALTER" /><?php echo __("Alter"); ?></label>
		</td>
	</tr>
	<tr>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="CREATE" /><?php echo __("Create"); ?></label>
		</td>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="DROP" /><?php echo __("Drop"); ?></label>
		</td>
		<td>
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="CREATE TEMPORARY TABLES" /><?php echo __("Temp tables"); ?></label>
		</td>
	</tr>
	</table>
	<div class="paneheader">
	<?php echo __("Administrator privileges"); ?>&nbsp;&nbsp;[<a onclick="paneCheckAll('adminprivs')"><?php echo __("All"); ?></a> / <a onclick="paneCheckNone('adminprivs')"><?php echo __("None"); ?></a>]
	</div>
	<table cellpadding="0" id="adminprivs">
	<tr>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="FILE" /><?php echo __("File"); ?></label>
		</td>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="PROCESS" /><?php echo __("Process"); ?></label>
		</td>
		<td width="34%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="RELOAD" /><?php echo __("Reload"); ?></label>
		</td>
	</tr>
	<tr>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="SHUTDOWN" /><?php echo __("Shutdown"); ?></label>
		</td>
		<td width="33%">
		<label><input type="checkbox" name="NEWPRIVILEGES[]" value="SUPER" /><?php echo __("Super"); ?></label>
		</td>
		<td width="34%">
		</td>
	</tr>
	</table>
	</div>
	<div class="paneheader">
	<?php echo __("Options"); ?>
	</div>
	<label><input type="checkbox" name="GRANTOPTION" value="true" /><?php echo __("Grant option"); ?></label>
	
	<div style="margin-top: 10px; height: 22px; padding: 4px 0">
		<input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" />
	</div>
	</form>
</div>

</div>