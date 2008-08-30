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
			$file = (array_key_exists("file", $opt)) ? $opt['file'] : "";
			$this->conn = sqlite_open($file, 0666, $sqliteError);
		}
		else
		{
			$this->adapter = "mysql";
			$host = (array_key_exists("host", $opt)) ? $opt['host'] : "";
			$this->conn = mysql_connect($host, $user, $pass);
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
				return mysql_num_rows($resultSet);
			}
			else if ($this->adapter == "sqlite")
			{
				return sqlite_num_rows($resultSet);
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
				$file = (array_key_exists("file", $this->options)) ? $this->options['file'] : "";
				return $file;
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
	
	function error()
	{
		return $this->errorMessage;
	}
	
}