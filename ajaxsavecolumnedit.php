<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxsavecolumnedit.php
- saves the details of a table column

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

if (isset($db))
	mysql_select_db($db);


if (isset($_POST['runQuery']))
{
	$query = $_POST['runQuery'];
	
	mysql_query($query) or ($mysqlError = mysql_error());
	
	echo "{\n";
	echo "    \"formupdate\": \"" . $_GET['form'] . "\",\n";
	echo "    \"errormess\": \"";
	if (isset($mysqlError))
		echo $mysqlError;
	echo "\"\n";
	echo '}';
	
}

?>