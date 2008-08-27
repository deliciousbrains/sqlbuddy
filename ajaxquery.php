<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxquery.php
- used for a variety of ajax functionality - runs a background query

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
		$sql = mysql_query($query);
	}
}

//return the first field from the first row
if (!isset($_POST['silent']))
{
	$row = @mysql_fetch_row($sql);
	echo nl2br(htmlentities($row[0], ENT_QUOTES, 'UTF-8'));
}

?>