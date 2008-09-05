<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

serve.php
- serves files

MIT license

2008 Calvin Lough <http://calv.in>

*/

function cleanHex($color)
{
	if (strlen($color) == 6)
	{
		// some hex colors can be shortened to 3 characters
		//	#ffcc00 is the same as #fc0
		if ((substr($color, 0, 1) == substr($color, 1, 1)) && (substr($color, 2, 1) == substr($color, 3, 1)) 
			&& (substr($color, 4, 1) == substr($color, 5, 1)))
		{
			$color = substr($color, 0, 1) . substr($color, 2, 1) . substr($color, 4, 1);
		}
		
	}
	
	return '#' . strtolower($color);
}

function fixHex($matches)
{
	$color = $matches[1];
	
	return cleanHex($color);
}

function rgb2hex($matches)
{
	$r = intval($matches[1]);
	$g = intval($matches[2]);
	$b = intval($matches[3]);
	
	$r = dechex($r<0?0:($r>255?255:$r));
	$g = dechex($g<0?0:($g>255?255:$g));
	$b = dechex($b<0?0:($b>255?255:$b));
	
	$color = (strlen($r) < 2?'0':'').$r;
	$color .= (strlen($g) < 2?'0':'').$g;
	$color .= (strlen($b) < 2?'0':'').$b;
	
	return cleanHex($color);
}

function removeSpacesNewlines($input)
{
	$input = str_replace("  ", " ", $input);
	$input = str_replace("\t", "", $input);
	$input = str_replace("\n ", "\n", $input);
	$input = str_replace(" {", "{", $input);
	$input = str_replace("{ ", "{", $input);
	$input = str_replace("\n{", "{", $input);
	$input = str_replace("{\n", "{", $input);
	$input = str_replace(" }", "}", $input);
	$input = str_replace("} ", "}", $input);
	$input = str_replace(": ", ":", $input);
	$input = str_replace(" :", ":", $input);
	$input = str_replace(";\n", ";", $input);
	$input = str_replace(" ;", ";", $input);
	$input = str_replace("; ", ";", $input);
	$input = str_replace("\n\n", "\n", $input);
	$input = str_replace(", ", ",", $input);
	
	return $input;
}

function compressCSS($input)
{
	$input = preg_replace_callback("/[\#]([0-9a-fA-F]{3,6})/", "fixhex", $input);
	
	// convert rgb() values to hex
	$input = preg_replace_callback("/rgb\(([0-9]{1,3})\,[ ]*([0-9]{1,3})\,[ ]*([0-9]{1,3})\)/", "rgb2hex", $input);
	
	// remove comments
	$input = preg_replace("/\/\*.*\*\//Us", "", $input);
	
	$input = str_replace(":0px", ":0", $input);
	$input = str_replace(":0em", ":0", $input);
	$input = str_replace(" 0px", " 0", $input);
	$input = str_replace(" 0em", " 0", $input);
	
	while ($input != removeSpacesNewlines($input))
	{
		$input = removeSpacesNewlines($input);
	}
	
	$input = trim($input);
	
	$input = str_replace(";}", "}", $input);
	
	return $input;
}

if (isset($_GET['file']))
{
	
	$filename = $_GET['file'];
	
	if (!(strpos($filename, "css/") === 0 || strpos($filename, "themes/") === 0))
		exit;
	
	if (strpos($filename, "..") !== false)
		exit;
	
	if (file_exists($filename))
	{
		if (extension_loaded('zlib'))
		{
			ob_start("ob_gzhandler");
			header("Content-Encoding: gzip");
		}
		else
		{
			ob_start();
		}
		
		header("Last-Modified: " . date("r", filemtime($filename)));
		
		$contents = file_get_contents($filename);
		
		if (substr($filename, -4) == ".css")
		{
			header("Content-Type: text/css");
			$contents = compressCSS($contents);
		}
		else if (substr($filename, -3) == ".js")
		{
			header("Content-Type: application/x-javascript");
		}
		
		echo $contents;
		
		ob_end_flush();
	}
	
}

?>