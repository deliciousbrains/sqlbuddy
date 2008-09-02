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
	$conn->selectDB($db);

if (isset($_POST['query']))
{
	$queryList = splitQueryText($_POST['query']);
	foreach ($queryList as $query)
	{
		$sql = $conn->query($query);
	}
}

if ($conn->rowCount($sql))
{
	$row = @$conn->fetchAssoc($sql);
	foreach ($row as $key => $value)
	{
		echo "<div class=\"fulltexttitle\">" . $key . "</div>";
		echo "<div class=\"fulltextbody\">" . nl2br(htmlentities($value, ENT_QUOTES, 'UTF-8')) . "</div>";
	}
}

?>