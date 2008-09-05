<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

ajaxsavecolumnedit.php
- saves the details of a table column

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "includes/common.php";

loginCheck();

if (isset($_GET['user']))
	$user = $_GET['user'];
	
if (isset($_POST['CHOICE']))
	$choice = $_POST['CHOICE'];

if (isset($_POST['PRIVILEGES']))
	$privileges = $_POST['PRIVILEGES'];

if (isset($user))
{
	
	if ($choice == "ALL")
	{
		$privList = "ALL";
	}
	else
	{
		if (isset($privileges) && count($privileges) > 0)
			$privList = implode(", ", $privileges);
		else
			$privList = "USAGE";
		
	}
	
	$split = explode("@", $user);
	
	if (isset($split[0]))
		$name = $split[0];
	
	if (isset($split[1]))
		$host = $split[1];
	
	if (isset($name) && isset($host))
	{
		$user = "'" . $name . "'@'" . $host . "'";
		
		$query = "GRANT " . $privList . " ON *.* TO " . $user;
		
		if (isset($_POST['GRANTOPTION']))
			$query .= " WITH GRANT OPTION";
		
		$conn->query("REVOKE ALL PRIVILEGES ON *.* FROM " . $user);
		$conn->query("REVOKE GRANT OPTION ON *.* FROM " . $user);
		$conn->query($query) or ($dbError = $conn->error());
		$conn->query("FLUSH PRIVILEGES") or ($dbError = $conn->error());
		
		echo "{\n";
		echo "    \"formupdate\": \"" . $_GET['form'] . "\",\n";
		echo "    \"errormess\": \"";
		if (isset($dbError))
			echo $dbError;
		echo "\"\n";
		echo '}';
	}
}

?>