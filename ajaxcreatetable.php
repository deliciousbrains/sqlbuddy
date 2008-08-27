<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxcreatetable.php
- called from dboverview.php to create a new table

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

if (isset($db))
	mysql_select_db($db);

if (isset($_POST['query']))
{
	
	$queryList = splitQueryText($_POST['query']);
	
	foreach ($queryList as $query)
	{
		$sql = mysql_query($query) or ($mysqlError = mysql_error());
	}
	
	if (isset($mysqlError))
	{
		echo $mysqlError;
	}
	
}

?>