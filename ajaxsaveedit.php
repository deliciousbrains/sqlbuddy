<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxsaveedit.php
- saves data to the database

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

if (isset($db))
	mysql_select_db($db);

if ($_POST && isset($table))
{
	
	$insertChoice = "";
	
	if (isset($_POST['SB_INSERT_CHOICE']))
	{
		$insertChoice = $_POST['SB_INSERT_CHOICE'];
	}
	
	$structureSql = mysql_query("DESCRIBE `$table`");
	
	while ($structureRow = mysql_fetch_assoc($structureSql))
	{
		$pairs[$structureRow['Field']] = '';
	}
	
	foreach ($_POST as $key=>$value)
	{
		if ($key != "SB_INSERT_CHOICE")
		{	
			if (is_array($value))
			{
				$value = implode(",", $value);
			}
			
			$pairs[$key] = mysql_real_escape_string($value);
		}
	}
	
	if (isset($pairs))
	{
		
		if ($insertChoice != "INSERT")
		{
			$updates = "";
			
			foreach ($pairs as $keyname=>$value)
			{
				$updates .= "`" . $keyname . "`='" . $value . "',";
			}
			
			$updates = substr($updates, 0, -1);
			
			if (isset($_GET['queryPart']))
				$queryPart = $_GET['queryPart'];
			else
				$queryPart = "";
			
			$query = "UPDATE `$table` SET " . $updates . " " . $queryPart;
			
		}
		else
		{
			$columns = "";
			$values = "";
			
			foreach ($pairs as $keyname=>$value)
			{
				$columns .= "`" . $keyname . "`,";
				$values .= "'" . $value . "',";
			}
			
			$columns = substr($columns, 0, -1);
			$values = substr($values, 0, -1);
			
			$query = "INSERT INTO `$table` ($columns) VALUES ($values)";
		}
		
		mysql_query($query) or ($mysqlError = mysql_error());
		
		echo "{\n";
		echo "    \"formupdate\": \"" . $_GET['form'] . "\",\n";
		echo "    \"errormess\": \"";
		if (isset($mysqlError))
			echo $mysqlError;
		echo "\"\n";
		echo '}';
		
	}
}

?>