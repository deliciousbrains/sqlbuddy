<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

sql.php
- sql class

MIT license

2008 Calvin Lough <http://calv.in>

*/

class SQL {

	var $adapter = "";
	var $conn = "";
	var $db = "";
	var $options = "";
	var $errorMessage = "";

	function SQL($connString, $user = "", $pass = "")
	{
		list($adapt, $options) = explode(":", $connString, 2);

		$optionsList = explode(";", $options);

		foreach ($optionsList as $option)
		{
			list($a, $b) = explode("=", $option);
			$opt[$a] = $b;
		}

		$this->options = $opt;

		if ($adapt == "sqlite")
		{
			$this->adapter = "sqlite";
			$database = (array_key_exists("database", $opt)) ? $opt['database'] : "";
			$this->conn = @sqlite_open($database, 0666, $sqliteError);
		}
		else
		{
			$this->adapter = "mysql";
			$host = (array_key_exists("host", $opt)) ? $opt['host'] : "";
			$this->conn = @mysql_connect($host, $user, $pass);
			$this->query("SET NAMES 'utf8'");
		}

		if (!$this->conn)
		{
			return false;
		}
	}

	function selectDB($db)
	{
		if ($this->conn)
		{
			$this->db = $db;
			if ($this->adapter == "mysql")
			{
				mysql_select_db($db);
			}
			else if ($this->adapter == "sqlite")
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}

	function query($queryText)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				$queryResult = mysql_query($queryText, $this->conn);

				if (!$queryResult)
				{
					$this->errorMessage = mysql_error();
				}

				return $queryResult;
			}
			else if ($this->adapter == "sqlite")
			{
				$queryResult = sqlite_query($this->conn, $queryText);

				if (!$queryResult)
				{
					$this->errorMessage = sqlite_error_string(sqlite_last_error($this->conn));
				}

				return $queryResult;
			}
		}
		else
		{
			return false;
		}
	}

	function rowCount($resultSet)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return @mysql_num_rows($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return @sqlite_num_rows($resultSet);
			}
		}
	}

	function fetchArray($resultSet)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_fetch_row($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_fetch_array($resultSet, SQLITE_NUM);
			}
		}
	}

	function fetchAssoc($resultSet)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_fetch_assoc($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_fetch_array($resultSet, SQLITE_ASSOC);
			}
		}
	}

	function affectedRows($resultSet)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_affected_rows($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_changes($resultSet);
			}
		}
	}

	function dataSeek($resultSet, $targetRow)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_data_seek($resultSet, $targetRow);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_seek($resultSet, $targetRow);
			}
		}
	}

	function result($resultSet, $targetRow, $targetColumn)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_result($resultSet, $targetRow, $targetColumn);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_column($resultSet, $targetColumn);
			}
		}
	}

	function listDatabases()
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return $this->query("SHOW DATABASES");
			}
			else if ($this->adapter == "sqlite")
			{
				$database = (array_key_exists("database", $this->options)) ? $this->options['database'] : "";
				return $database;
			}
		}
	}

	function listTables()
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return $this->query("SHOW TABLES");
			}
			else if ($this->adapter == "sqlite")
			{
				return $this->query("SELECT name FROM sqlite_master WHERE type = 'table'");
			}
		}
	}

	function listCharset()
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return $this->query("SHOW CHARACTER SET");
			}
			else if ($this->adapter == "sqlite")
			{
				return "";
			}
		}
	}
	
	function listCollation()
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return $this->query("SHOW COLLATION");
			}
			else if ($this->adapter == "sqlite")
			{
				return "";
			}
		}
	}
	
	function insertId($resultSet)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_insert_id($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_last_insert_rowid($resultSet);
			}
		}
	}

	function escapeString($toEscape)
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				return mysql_real_escape_string($toEscape);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_escape_string($toEscape);
			}
		}
	}

	function getVersion()
	{
		if ($this->conn)
		{
			if ($this->adapter == "mysql")
			{
				$verSql = mysql_get_server_info();
				$version = explode("-", $verSql);
				return $version[0];
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_libversion();
			}
		}

	}

	/*
		Return names, row counts etc for every database, table and view in a JSON string
	*/
	function getMetadata()
	{
		$output = '';
		if ($this->conn)
		{
			if ($this->adapter == "mysql" && version_compare($this->getVersion(), "5.0.0", ">"))
			{
				$this->selectDB("INFORMATION_SCHEMA");
				$schemaSql = $this->query("SELECT SCHEMA_NAME FROM SCHEMATA ORDER BY SCHEMA_NAME");
				if ($this->rowCount($schemaSql))
				{
					while ($schema = $this->fetchAssoc($schemaSql))
					{
						$output .= '{"name": "' . $schema['SCHEMA_NAME'] . '"';
						// other interesting columns: TABLE_TYPE, ENGINE, TABLE_COLUMN and many more
						$tableSql = $this->query("SELECT TABLE_NAME, TABLE_ROWS FROM TABLES WHERE TABLE_SCHEMA='" . $schema['SCHEMA_NAME'] . "' ORDER BY TABLE_NAME");
						if ($this->rowCount($tableSql))
						{
							$output .= ',"items": [';
							while ($table = $this->fetchAssoc($tableSql))
							{
								$output .= '{';
								$output .= '"name":"' . $table['TABLE_NAME'] . '",';
								$output .= '"rowcount":' . ($table['TABLE_ROWS'] > 0 ? $table['TABLE_ROWS'] : '0');
								$output .= '},';
							}
							$output = substr($output, 0, -1);
							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			}
			else if ($this->adapter == "mysql")
			{
				$schemaSql = $this->listDatabases();
				
				if ($this->rowCount($schemaSql))
				{
					while ($schema = $this->fetchArray($schemaSql))
					{
						$output .= '{"name": "' . $schema[0] . '"';
						
						$this->selectDB($schema[0]);
						$tableSql = $this->listTables();
						
						if ($this->rowCount($tableSql))
						{
							$output .= ',"items": [';
							while ($table = $this->fetchArray($tableSql))
							{
								$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $table[0] . "`");
								$rowCount = (int)($this->result($countSql, 0, "RowCount"));
								$output .= '{"name":"' . $table[0] . '","rowcount":' . $rowCount . '},';
							}
							$output = substr($output, 0, -1);
							$output .= ']';
						}
						$output .= '},';
					}
					$output = substr($output, 0, -1);
				}
			}
			else if ($this->adapter == "sqlite")
			{
				$database = (array_key_exists("database", $this->options)) ? $this->options['database'] : "";
				
				$output .= '{"name": "' . $database . '"';
				
				$tableSql = $this->listTables();

				if ($this->rowCount($tableSql))
				{
					$output .= ',"items": [';
					while ($tableRow = $this->fetchArray($tableSql))
					{
						$countSql = $this->query("SELECT COUNT(*) AS `RowCount` FROM `" . $tableRow[0] . "`");
						$rowCount = (int)($this->result($countSql, 0, "RowCount"));
						$output .= '{"name":"' . $tableRow[0] . '","rowcount":' . $rowCount . '},';
					}
					$output = substr($output, 0, -1);
					$output .= ']';
				}
				$output .= '}';
			}
		}
		return $output;
	}

	function error()
	{
		return $this->errorMessage;
	}

}