<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

home.php
- create a new database, links, etc

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "includes/common.php";

loginCheck();

?>
<table class="hometable">
<tr>
	<td>
	<h4><?php echo __("Welcome to SQL Buddy!"); ?></h4>
	</td>
</tr>
<tr>
	<td style="padding: 0 0 13px 10px">
	
	<?php
	
	$version = $conn->getVersion();
	
	if ($conn->getAdapter() == "mysql")
	{
		
		$message = "";
		
		if (isset($_SESSION['SB_LOGIN_USER']) && $conn->getOptionValue("host"))
		{
			$message = sprintf(__("You are connected to MySQL %s with the user %s."), $version, $_SESSION['SB_LOGIN_USER'] . "@" . $conn->getOptionValue("host"));
		}
			
		$statusSql = $conn->query("SHOW STATUS");
		
		if ($conn->isResultSet($statusSql))
		{
			
			while ($statusRow = $conn->fetchAssoc($statusSql))
			{
				$varn = $statusRow['Variable_name'];
				if ($varn == "Uptime" || $varn == "Bytes_sent" || $varn == "Bytes_received")
					$status[$varn] = $statusRow['Value'];
			}
			
			$uptime = (int)($status['Uptime']);
			
			$translated = time() - $uptime;
			
			if (date("Y") != date("Y", $translated))
				$startDate = date("F jS, Y", $translated);
			else
				$startDate = date("F jS", $translated);
			
			$totalDataS = (int)($status['Bytes_sent']);
			$totalDataR = (int)($status['Bytes_received']);
			
			$totalData = $totalDataS + $totalDataR;
			
			$dataString = memoryFormat($totalData);
			
			$message .= " " . sprintf(__("The database server has been running since %s and has transferred approximately %s of data."), $startDate, $dataString);
			
		}
		
	}
	else if ($conn->getAdapter() == "sqlite")
	{
		$message = sprintf(__("You are connected to %s."), "SQLite " . $version);
	}
	
	echo "<p>" . $message . "</p>";
	
	?>
	
	<table cellspacing="0" cellpadding="0" class="newdb">
	<tr>
	<td class="inputfield">
		<?php echo __("Language"); ?>:
	</td>
	<td>
	<?php
	
	if (sizeof($langList) > 0)
	{
	 	
		echo '<select id="langSwitcher" onchange="switchLanguage()">';
		
		foreach ($langList as $langCode => $langName)
		{
			echo '<option value="' . $langCode . '"';
			
			if ($lang == $langCode)
				echo " selected";
			
			echo '>' . $langName . '</option>';
		}
		
		echo '</select>';
		
	}
	
	?>
	</td>
	</tr>
	<tr>
	<td class="inputfield">
		<?php echo __("Theme"); ?>:
	</td>
	<td>
	<select id="themeSwitcher" onchange="switchTheme()">
	<?php
	
	foreach ($themeList as $t => $n)
	{
		echo '<option value="' . $t . '"';
		
		if ($theme == $t)
		{
			echo " selected";
		}
		
		echo '>' . $n . '</option>';
	}
	
	?>
	</select>
	</td>
	</tr>
	</table>
	
	</td>
</tr>
<?php

if (function_exists("curl_init") && isset($sbconfig['EnableUpdateCheck']) && $sbconfig['EnableUpdateCheck'] == true)
{
	
	//check for a new version
	$crl = curl_init();
	$timeout = 5;
	$url = "http://www.sqlbuddy.com/versioncheck2.php";
	curl_setopt($crl, CURLOPT_URL, $url);
	curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$content = curl_exec($crl);
	curl_close($crl);
	
	if (strlen($content) > 0)
	{
		$content = strip_tags($content);
		
		list($version, $notes) = explode("\n", $content, 2);
		
		if ($version && $notes)
		{
			
			if (version_compare(VERSION_NUMBER, $version) == "-1")
			{
				?>
				
				<tr>
				<td style="padding-top: 15px">	
				<h4><?php echo __("A new version of SQL Buddy is available!"); ?></h4>
				</td>
				</tr>
				<tr>
				<td style="padding: 0 0 15px 10px;">
				
					<p><b style="color: rgb(110, 110, 110)"><?php echo sprintf(__("You have SQL Buddy %s installed and %s is now available."), VERSION_NUMBER, $version); ?></b></p>
					
					<p style="line-height: 15px"><?php echo '"' . $notes . '"'; ?></p>
					
					<p class="upgradelinks"><a href="http://www.sqlbuddy.com/download/dl.php"><?php echo __("Download"); ?></a>
				
				</td>
				</tr>
				
				<?php
			}
			
		}
	}
	
}

?>
<tr>
	<td>
	<h4><?php echo __("Getting started"); ?></h4>
	</td>
</tr>
<tr>
	<td style="padding: 1px 0 15px 10px">
	
	<ul>
	<li><a href="http://www.sqlbuddy.com/help/"><?php echo __("Help"); ?></a></li>
	<li><a href="http://www.sqlbuddy.com/translations/"><?php echo __("Translations"); ?></a></li>
	<li><a href="http://www.sqlbuddy.com/releasenotes/"><?php echo __("Release Notes"); ?></a></li>
	<li><a href="http://www.sqlbuddy.com/contact/"><?php echo __("Contact"); ?></a></li>
	</ul>
	
	</td>
</tr>
<?php

if ($conn->getAdapter() != "sqlite")
{

?>
<tr>
	<td>
	<h4><?php echo __("Create a new database"); ?></h4>
	</td>
</tr>
<tr>
	<td style="padding: 0px 0 20px 10px">
	
	<form onsubmit="createDatabase(); return false;">
	<table cellspacing="0" cellpadding="0" class="newdb">
	<tr>
	<td class="inputfield">
		<?php echo __("Name"); ?>:
	</td>
	<td>
		<input type="text" class="text" id="DBNAME">
	</td>
	</tr>
	<?php
	
	if (isset($charsetList))
	{
		echo "<tr>";
		echo "<td class=\"inputfield\">";
		echo __("Charset") . ":";
		echo "</td>";
		echo "<td>";
		echo "<select id=\"DBCHARSET\">";
		echo "<option></option>";
		
		$defaultCharSql = $conn->query("SHOW VARIABLES LIKE 'character_set_server'");
		
		if ($conn->isResultSet($defaultCharSql))
		{
			$defaultCharset = $conn->result($defaultCharSql, 0, "Value");
		}
		
		foreach ($charsetList as $charset)
		{
			echo "<option value=\"" . $charset . "\"";
			
			if (isset($defaultCharset) && $charset == $defaultCharset)
			{
				echo ' selected="selected"';
			}
			
			echo ">" . $charset . "</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
	}
	
	?>
	<tr>
		<td></td>
		<td>
		<input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" />
		</td>
	</tr>
	</table>
	</form>
	
	</td>
</tr>
<?php

}

?>
<tr>
	<td>
	<h4><?php echo __("Did you know..."); ?></h4>
	</td>
</tr>
<tr>
	<td style="padding: 0 0 10px 10px;">
	
	<p><?php
	
	$didYouKnow[] = __("There is an easier way to select a large group of items when browsing table rows. Check the first row, hold the shift key, and check the final row. The checkboxes between the two rows will be automatically checked for you.");
	$didYouKnow[] = __("The columns in the browse and query tabs are resizable. Adjust them to as wide or narrow as you like.");
	$didYouKnow[] = __("The login page is based on a default user of root@localhost. By editing config.php, you can change the default user and host to whatever you want.");
	
	$rand = rand(0, count($didYouKnow) - 1);
	
	echo $didYouKnow[$rand];
	
	?></p>
			
	</td>
</tr>
<tr>
	<td>
	<h4><?php echo __("Keyboard shortcuts"); ?></h4>
	</td>
</tr>
<tr>
	<td style="padding: 4px 0 5px 10px">
	
	<table class="keyboardtable">
	<tr>
		<th><?php echo __("Press this key..."); ?></th>
		<th><?php echo __("...and this will happen"); ?></th>
	</tr>
	<tr>
		<td>a</td>
		<td><?php echo __("select all"); ?></td>
	</tr>
	<tr>
		<td>n</td>
		<td><?php echo __("select none"); ?></td>
	</tr>
	<tr>
		<td>e</td>
		<td><?php echo __("edit selected items"); ?></td>
	</tr>
	<tr>
		<td>d</td>
		<td><?php echo __("delete selected items"); ?></td>
	</tr>
	<tr>
		<td>r</td>
		<td><?php echo __("refresh page"); ?></td>
	</tr>
	<tr>
		<td>q</td>
		<td><?php echo __("load the query tab"); ?></td>
	</tr>
	<tr>
		<td>f</td>
		<td><?php echo __("browse tab - go to first page of results"); ?></td>
	</tr>
	<tr>
		<td>l</td>
		<td><?php echo __("browse tab - go to last page of results"); ?></td>
	</tr>
	<tr>
		<td>g</td>
		<td><?php echo __("browse tab - go to previous page of results"); ?></td>
	</tr>
	<tr>
		<td>h</td>
		<td><?php echo __("browse tab - go to next page of results"); ?></td>
	</tr>
	<tr>
		<td>o</td>
		<td><?php echo __("optimize selected tables"); ?></td>
	</tr>
	</table>
</tr>
</table>