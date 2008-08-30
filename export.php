<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

export.php
- export data in sql and csv formats

MIT license

2008 Calvin Lough <http://calv.in>

*/

include "functions.php";

loginCheck();

if ($_POST)
{
	
	$outputBuffer = "";
	
	if (isset($db))
	{
		$dbs[] = $db;
		
		if (isset($table))
			$tables[] = $table;
		else if (isset($_POST['EXPORTTABLE']))
			$tables = $_POST['EXPORTTABLE'];
	}
	else
	{
		if (isset($_POST['EXPORTDB']))
			$dbs = $_POST['EXPORTDB'];
		$exportDb = true;
	}
	
	if (isset($_POST['FORMAT']))
		$format = strtoupper($_POST['FORMAT']);
	
	if (isset($_POST['STRUCTURE']))
		$exportStructure = $_POST['STRUCTURE'];
	
	if (isset($_POST['DATA']))
		$exportData = $_POST['DATA'];
	
	if (isset($_POST['DELIMITER']))
		$delimiter = $_POST['DELIMITER'];
	
	if (isset($_POST['FIELDNAMES']))
		$printFieldnames = $_POST['FIELDNAMES'];
	
	if (isset($_POST['INSERTTYPE']))
		$insertType = $_POST['INSERTTYPE'];
	
	if (isset($_POST['OUTPUT']))
		$output = $_POST['OUTPUT'];
	
	if (isset($_POST['OUTPUTFILETEXT']))
	{
		$outputFile = "exports/" . basename($_POST['OUTPUTFILETEXT']);
	}
	
	if (!isset($delimiter) || $delimiter == "TAB")
		$delimiter = "\t";
	else if ($delimiter == "SEMICOLON")
		$delimiter = ";";
	else if ($delimiter == "SPACE")
		$delimiter = " ";
	else
		$delimiter = ",";
	
	// for the next three - it has to be one or the other
	// this way, if we get fed garbage, just go with a default
	if (!isset($format) || $format != "CSV")
		$format = "SQL";
	
	if (!isset($output) || $output != "FILE" || !isset($outputFile))
		$output = "BROWSER";
	
	if (!isset($insertType) || $insertType != "COMPLETE")
		$insertType = "COMPACT";
	
	if (isset($format) && $format == "SQL" && !isset($exportStructure) && !isset($exportData))
	{
		$error = __("You must export either structure, data, or both") . ".";
	}
	else if (!isset($dbs))
	{
		$error = __("Please select the databases that you would like to export") . ".";
	}
	else if (isset($db) && !isset($tables))
	{
		$error = __("Please select the tables that you would like to export") . ".";
	}
	else
	{
	
		if ($format == "SQL")
		{
			
			$version = $conn->getVersion();
			
			$outputBuffer .= "--\r\n";
			$outputBuffer .= "-- MySQL " . $version . "\r\n";
			$outputBuffer .= "-- " . date("r") . "\r\n";
			$outputBuffer .= "--\r\n\r\n";
		}
		
		foreach ($dbs as $d)
		{
			
			$conn->selectDB($d);
			
			// this checks to see if we are exporting an entire db with all tables
			if (isset($exportDb) && $exportDb == true)
			{
				
				if ($format == "SQL")
				{
					
					$outputBuffer .= "CREATE DATABASE `$d`";
					
					$currentChar = "";
					$currentCharSql = $conn->query("SHOW VARIABLES LIKE 'character_set_database'");
					
					if (@$conn->rowCount($currentCharSql))
					{
						$currentChar = $conn->result($currentCharSql, 0, "Value");
						
						$outputBuffer .= " DEFAULT CHARSET " . $currentChar;
					}
					
					$outputBuffer .= ";\r\n\r\n";
					
					$outputBuffer .= "USE `$d`;\r\n\r\n";
					
				}
				
				$tableSql = $conn->query("SHOW TABLES");
				
				$tables = "";
				
				if (@$conn->rowCount($tableSql))
				{
					while ($tableRow = $conn->fetchArray($tableSql))
					{
						$tables[] = $tableRow[0];
					}
				}
			}
			
			foreach ($tables as $t)
			{
				
				if ($format == "SQL")
				{
					
					$structureSQL = $conn->query("SHOW FULL FIELDS FROM `$t`");
					
					$tableEngine = "";
					$tableCharset = "";
					
					if (isset($exportStructure))
					{
						
						if (@$conn->rowCount($structureSQL))
						{
							
							$outputBuffer .= "CREATE TABLE `$t` (";
							
							$infoSql = $conn->query("SHOW TABLE STATUS LIKE '$t'");
							
							if (@$conn->rowCount($infoSql) == 1)
							{
								
								$infoRow = $conn->fetchAssoc($infoSql);
								
								$tableEngine = (array_key_exists("Type", $infoRow)) ? $infoRow['Type'] : $infoRow['Engine'];
								
								if (array_key_exists('Collation', $infoRow) && isset($collationList))
								{
									$tableCharset = $collationList[$infoRow['Collation']];
								}
							
							}
							
							$first = true;
							
							while ($structureRow = $conn->fetchAssoc($structureSQL))
							{
								if (!$first)
									$outputBuffer .= ",";
								
								$outputBuffer .= "\r\n   `" . $structureRow['Field'] . "` " . $structureRow['Type'];
								
								if (isset($collationList) && $structureRow['Collation'] != "NULL" && !is_null($structureRow['Collation']))
								{
									if ($collationList[$structureRow['Collation']] != $tableCharset)
									{
										$outputBuffer .= " CHARSET " . $collationList[$structureRow['Collation']];
									}
								}
								
								if ($structureRow['Null'] != "YES")
									$outputBuffer .= " NOT NULL";
								
								if ($structureRow['Default'] == "CURRENT_TIMESTAMP")
								{
									$outputBuffer .= " DEFAULT CURRENT_TIMESTAMP";
								}
								else if ($structureRow['Default'])
								{
									$outputBuffer .= " DEFAULT '" . $structureRow['Default'] . "'";
								}
								
								if ($structureRow['Extra'])
									$outputBuffer .= " " . $structureRow['Extra'];
								
								$first = false;
							}
							
							// dont forget about the keys
							$keySQL = $conn->query("SHOW INDEX FROM `$t`");
							
							if (@$conn->rowCount($keySQL))
							{
								$currentKey = "";
								while ($keyRow = $conn->fetchAssoc($keySQL))
								{
									// if this is the start of a key
									if ($keyRow['Key_name'] != $currentKey)
									{	
										// finish off the last key first, if necessary
										if ($currentKey != "")
											$outputBuffer .= ")";
										
										if ($keyRow['Key_name'] == "PRIMARY")
											$outputBuffer .= ",\r\n   PRIMARY KEY (";
										elseif ($keyRow['Non_unique'] == "0")
											$outputBuffer .= ",\r\n   UNIQUE KEY (";
										else
											$outputBuffer .= ",\r\n   KEY `" . $keyRow['Key_name'] . "` (";
										
										$outputBuffer .= "`" . $keyRow['Column_name'] . "`";
									}
									else
									{
										$outputBuffer .= ",`" . $keyRow['Column_name'] . "`";
									}
									
									$currentKey = $keyRow['Key_name'];
								}
								
								if (isset($currentKey) && $currentKey != "")
									$outputBuffer .= ")";
								
							}
							
							$outputBuffer .= "\r\n)";
							
							if ($tableEngine)
							{
								$outputBuffer .= ' ENGINE=' . $tableEngine;
							}
							
							if ($tableCharset)
							{
								$outputBuffer .= ' DEFAULT CHARSET ' . $tableCharset;
							}
							
							$outputBuffer .= ";\r\n\r\n";
						}
					}
					
					@$conn->dataSeek($structureSQL, 0);
					
					if (isset($exportData))
					{
						$dataSQL = $conn->query("SELECT * FROM `$t`");
						
						$columnList = array();
						
						// put the column names in an array
						if (@$conn->rowCount($structureSQL))
						{
							while ($structureRow = $conn->fetchAssoc($structureSQL))
							{
								$columnList[] = $structureRow['Field'];
								$type[] = $structureRow['Type'];
							}
						}
						
						$columnImplosion = implode("`, `", $columnList);
						
						if (@$conn->rowCount($dataSQL))
						{
							
							if ($insertType == "COMPACT")
								$outputBuffer .= "INSERT INTO `$t` (`$columnImplosion`) VALUES \r\n";
							
							$firstLine = true;
							
							while ($dataRow = $conn->fetchAssoc($dataSQL))
							{
								
								if ($insertType == "COMPLETE")
								{
									$outputBuffer .= "INSERT INTO `$t` (`$columnImplosion`) VALUES ";
								}
								else
								{
									if (!$firstLine)
										$outputBuffer .= ",\r\n";
								}
								
								$outputBuffer .= "(";
								
								$first = true;
								
								for ($i=0; $i<sizeof($columnList); $i++)
								{
									if (!$first)
										$outputBuffer .= ", ";
									
									$currentData = $dataRow[$columnList[$i]];
									
									if (isset($type) && $currentData && ((isset($binaryDTs) && in_array($type[$i], $binaryDTs)) || stristr($type[$i], "binary") !== false))
									{
										$outputBuffer .= "0x" . bin2hex($currentData);
									}
									else
									{
										$outputBuffer .= "'" . formatDataForExport($currentData) . "'";
									}
									
									$first = false;
								}
								
								$outputBuffer .= ")";
								
								if ($insertType == "COMPLETE")
									$outputBuffer .= ";\r\n";
								
								$firstLine = false;
								
							}
							
							if ($insertType == "COMPACT")
								$outputBuffer .= ";\r\n";
							
						}
						else
						{
							$outputBuffer .= "-- [" . sprintf(__("Table `%s` is empty"), $t) . "]\r\n";
						}
					}
					
					$outputBuffer .= "\r\n";
					
				}
				else if ($format == "CSV")
				{
					
					if (isset($printFieldnames))
					{
						$structureSQL = $conn->query("DESCRIBE `$t`");
							
						if (@$conn->rowCount($structureSQL))
						{
							$first = true;
							while ($structureRow = $conn->fetchArray($structureSQL))
							{
								if (!$first)
									$outputBuffer .= $delimiter;
								
								$outputBuffer .= "\"" . $structureRow[0] . "\"";
								
								$first = false;
							}
							$outputBuffer .= "\r\n";
						}
					}
					
					$dataSQL = $conn->query("SELECT * FROM `$t`");
					
					if (@$conn->rowCount($dataSQL))
					{
						while ($dataRow = $conn->fetchArray($dataSQL))
						{
							$data = array(); // empty the array
							foreach ($dataRow as $each)
							{
								$data[] = "\"" . formatDataForCSV($each) . "\"";
							}
							
							$dataLine = implode($delimiter, $data);
							
							$outputBuffer .= $dataLine . "\r\n";
						}
					}
					
				}
				
			}
		
		}
		
		$outputBuffer = trim($outputBuffer);
		
		if ($outputBuffer)
		{
			if ($output == "BROWSER")
			{
				echo "<div id=\"EXPORTWRAPPER\">";
					echo "<strong>" . __("Results:") . "</strong> [<a onclick=\"$('EXPORTRESULTS').select()\">" . __("Select all") . "</a>]";
					echo "<textarea id=\"EXPORTRESULTS\">$outputBuffer</textarea>";
				echo "</div>";
			}
			else
			{
				
				if (!$handle = @fopen($outputFile, "w"))
				{
					$error = __("The file could not be opened") . ".";
				}
				else
				{
					if (fwrite($handle, $outputBuffer) === false)
					{
						$error = __("Could not write to file") . ".";
					}
					else
					{
						echo '<div style="margin: 10px 12px 5px 14px; color: rgb(100, 100, 100)">';
						echo __("Successfully wrote content to file") . '. <a href="' . $outputFile . '">' . __("Download") . '</a><br /><strong>' . __("Note") . ':</strong>' . __("If this is a public server, you should delete this file from the server after you download it") . '.</div>';
					}
				}
				
				@fclose($handle);
				
			}
		}
		
	}
}

if (isset($error))
{
	echo '<div class="errormessage" style="margin: 14px 12px 7px 14px; width: 340px">' . $error . '</div>';
}

?>

<div class="export">
	
	<h4><?php echo __("Export"); ?></h4>
	
	<form id="EXPORTFORM" onsubmit="submitForm('EXPORTFORM'); return false">
	<table cellpadding="0">
	<?php
	
	if (isset($db) && !isset($table))
	{
	
	?>
	<tr>
		<td class="secondaryheader"><?php echo __("Tables"); ?>:<br />&nbsp;<a onclick="selectAll('exportTable')"><?php echo __("All"); ?></a> / <a onclick="selectNone('exportTable')"><?php echo __("None"); ?></a></td>
		<td>
		<select name="EXPORTTABLE[]" id="exportTable" multiple="multiple" size="10">
		<?php
		
		$conn->selectDB($db);
		
		$tableSql = $conn->query("SHOW TABLES");
		
		if (@$conn->rowCount($tableSql))
		{
			while ($tableRow = $conn->fetchArray($tableSql))
			{
				echo '<option value="' . $tableRow[0] . '"';
				
				if (isset($tables) && in_array($tableRow[0], $tables))
					echo ' selected="selected"';
									
				echo '>' . $tableRow[0] . '</option>';
			}
		}
		
		?>
		</select>
		</td>
	</tr>
	<?php
	
	}
	else if (!isset($db))
	{
	?>
	
	<tr>
		<td class="secondaryheader"><?php echo __("Databases"); ?>:<br />&nbsp;<a onclick="selectAll('exportDb')"><?php echo __("All"); ?></a> / <a onclick="selectNone('exportDb')"><?php echo __("None"); ?></a></td>
		<td>
		<select name="EXPORTDB[]" id="exportDb" multiple="multiple" size="10">
		<?php
		
		$dbSql = $conn->query("SHOW DATABASES");
		
		if (@$conn->rowCount($dbSql))
		{
			while ($dbRow = $conn->fetchArray($dbSql))
			{
				echo '<option value="' . $dbRow[0] . '"';
				
				if (isset($dbs) && in_array($dbRow[0], $dbs))
					echo ' selected="selected"';
				
				echo '>' . $dbRow[0] . '</option>';
			}
		}
		
		?>
		</select>
		</td>
	</tr>
	
	<?php
	}
	else if (isset($db) && isset($table))
	{
	
	?>
	<tr>
		<td class="secondaryheader"><?php echo __("Format"); ?>:</td>
		<td>
			<label><input type="radio" name="FORMAT" id="SQLTOGGLE" value="SQL" onchange="updatePane('SQLTOGGLE', 'sqlpane', 'csvpane')" onclick="updatePane('SQLTOGGLE', 'sqlpane', 'csvpane')" <?php if ((isset($format) && $format == "SQL")|| !isset($format)) echo 'checked="checked"'; ?> /><?php echo __("SQL"); ?></label><br />
			<label><input type="radio" name="FORMAT" value="CSV" onchange="updatePane('SQLTOGGLE', 'sqlpane', 'csvpane')" onclick="updatePane('SQLTOGGLE', 'sqlpane', 'csvpane')" <?php if (isset($format) && $format == "CSV") echo 'checked="checked"'; ?> /><?php echo __("CSV"); ?></label>
		</td>
	</tr>
	<?php
	
	}
	
	?>
	</table>
	
	<div class="exportseperator"></div>
	
	<table cellpadding="0" id="sqlpane"<?php if (isset($format) && $format == "CSV") echo ' style="display: none"'; ?>>
	<tr>
		<td class="secondaryheader"><?php echo __("Export"); ?>:</td>
		<td>
			<label><input type="checkbox" name="STRUCTURE" value="STRUCTURE" <?php if (isset($exportStructure) || !($_POST)) echo 'checked="checked"'; ?> /><?php echo __("Structure"); ?></label><br />
			<label><input type="checkbox" name="DATA" value="DATA" <?php if (isset($exportData) || !($_POST)) echo 'checked="checked"'; ?> /><?php echo __("Data"); ?></label>
		</td>
	</tr>
	<tr>
		<td class="secondaryheader"><?php echo __("Options"); ?>:</td>
		<td>
			<label><input type="radio" name="INSERTTYPE" value="COMPACT" <?php if ((isset($insertType) && $insertType == "COMPACT") || !isset($insertType)) echo 'checked="checked"'; ?> /><?php echo __("Compact inserts"); ?></label><br />
			<label><input type="radio" name="INSERTTYPE" value="COMPLETE" <?php if (isset($insertType) && $insertType == "COMPLETE") echo 'checked="checked"'; ?> /><?php echo __("Complete inserts"); ?></label>
		</td>
	</tr>
	</table>
	
	<table cellpadding="0" id="csvpane"<?php if ((isset($format) && $format == "SQL") || !isset($format)) echo ' style="display: none"'; ?>>
	<tr>
		<td class="secondaryheader"><?php echo __("Delimiter"); ?>:</td>
		<td>
			<label><input type="radio" name="DELIMITER" value="COMMA"<?php if (isset($delimiter) && $delimiter == "," || !isset($delimiter)) echo ' checked="checked"'; ?> /><?php echo __("Comma"); ?></label><br />
			<label><input type="radio" name="DELIMITER" value="TAB"<?php if (isset($delimiter) && $delimiter == "\t") echo ' checked="checked"'; ?> /><?php echo __("Tab"); ?></label><br />
			<label><input type="radio" name="DELIMITER" value="SEMICOLON"<?php if (isset($delimiter) && $delimiter == ";") echo ' checked="checked"'; ?> /><?php echo __("Semicolon"); ?></label><br />
			<label><input type="radio" name="DELIMITER" value="SPACE"<?php if (isset($delimiter) && $delimiter == " ") echo ' checked="checked"'; ?> /><?php echo __("Space"); ?></label>
		</td>
	</tr>
	<tr>
		<td class="secondaryheader"><?php echo __("Options"); ?>:</td>
		<td>
			<label><input type="checkbox" name="FIELDNAMES" value="TRUE"<?php if (isset($printFieldnames)) echo ' checked="checked"'; ?> /><?php echo __("Print field names on first line"); ?></label><br />
		</td>
	</tr>
	</table>
	
	<div class="exportseperator"></div>
	
	<table cellpadding="0">
	<tr>
		<td class="message" colspan="2">
		<?php echo __("If you are exporting a large number of rows, it is recommended that you output the results to a text file"); ?>.
		</td>
	</tr>
	<tr>
		<td class="secondaryheader"><?php echo __("Output to"); ?>:</td>
		<td>
			<label><input type="radio" name="OUTPUT" value="BROWSER"<?php if (isset($output) && $output == "BROWSER" || !isset($output)) echo ' checked="checked"'; ?> /><?php echo __("Browser"); ?></label><br />
			<label><input type="radio" name="OUTPUT" id="OUTPUTFILE" value="FILE" onchange="exportFilePrep()"<?php if (isset($output) && $output == "FILE") echo ' checked="checked"'; ?> /><?php echo __("Text file"); ?>:</label><input type="text" class="text" name="OUTPUTFILETEXT" id="OUTPUTFILETEXT" value="<?php if (isset($outputFile)){ echo basename($outputFile); } else if (isset($format) && $format == "CSV") { echo strtolower(__("Export")) . ".csv"; } else { echo strtolower(__("Export")) . ".sql"; } ?>" style="vertical-align: middle; margin-left: 5px" />
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" class="inputbutton" value="<?php echo __("Submit"); ?>" /></td>
	</tr>
	</table>
	
	</form>
	
</div>