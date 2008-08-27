<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxfulltext.php
- fetches full text for browse tab

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

if (@mysql_num_rows($sql))
{
	$row = @mysql_fetch_assoc($sql);
	foreach ($row as $key => $value)
	{
		echo "<div class=\"fulltexttitle\">" . $key . "</div>";
		echo "<div class=\"fulltextbody\">" . nl2br(htmlentities($value, ENT_QUOTES, 'UTF-8')) . "</div>";
	}
}

?>