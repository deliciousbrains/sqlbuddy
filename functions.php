<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

functions.php
- gets the page setup with the variables it needs

MIT license

2008 Calvin Lough <http://calv.in>

*/

error_reporting(E_ALL);

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Greenwich');

if (!session_id())
	session_start();

define("MAIN_DIR", dirname(__FILE__) . "/");
define("INCLUDES_DIR", MAIN_DIR . "includes/");

include MAIN_DIR . "config.php";
include INCLUDES_DIR . "types.php";
include INCLUDES_DIR . "class/GetTextReader.php";

if (version_compare(PHP_VERSION, "5.0.0", "<"))
	include INCLUDES_DIR . "class/Sql-php4.php";
else
	include INCLUDES_DIR . "class/Sql.php";

define("VERSION_NUMBER", "1.3.3");
define("PREVIEW_CHAR_SIZE", 75);

$adapterList[] = "mysql";

if (function_exists("sqlite_open") || (class_exists("PDO") && in_array("sqlite", PDO::getAvailableDrivers()))) {
	$adapterList[] = "sqlite";
}

$cookieLength = time() + (60*24*60*60);

$langList['en_US'] = "English";

if (isset($_COOKIE['sb_lang']) && array_key_exists($_COOKIE['sb_lang'], $langList)) {
	$lang = preg_replace("/[^a-z0-9_]/i", "", $_COOKIE['sb_lang']);
} else {
	$lang = "en_US";
}

if ($lang != "en_US") {
	// extend the cookie length
	setcookie("sb_lang", $lang, $cookieLength);
} else if (isset($_COOKIE['sb_lang'])) {
	// cookie not needed for en_US
	setcookie("sb_lang", "", time() - 10000);
}

$themeList["simple"] = "Simple";

if (isset($_COOKIE['sb_theme'])) {
	$currentTheme = preg_replace("/[^a-z0-9_]/i", "", $_COOKIE['sb_theme']);

	if (array_key_exists($currentTheme, $themeList)) {
		$theme = $currentTheme;

		// extend the cookie length
		setcookie("sb_theme", $theme, $cookieLength);
	} else {
		$theme = "simple";
		setcookie("sb_theme", "", time() - 10000);
	}
} else {
	$theme = "simple";
}

$gt = new GetTextReader($lang . ".pot");

if (isset($_SESSION['SB_LOGIN_STRING'])) {
	$user = (isset($_SESSION['SB_LOGIN_USER'])) ? $_SESSION['SB_LOGIN_USER'] : "";
	$pass = (isset($_SESSION['SB_LOGIN_PASS'])) ? $_SESSION['SB_LOGIN_PASS'] : "";
	$conn = new SQL($_SESSION['SB_LOGIN_STRING'], $user, $pass);
}

// unique identifer for this session, to validate ajax requests.
// document root is included because it is likely a difficult value
// for potential attackers to guess
$requestKey = substr(md5(session_id() . $_SERVER["DOCUMENT_ROOT"]), 0, 16);

if (isset($conn) && $conn->isConnected()) {
	if (isset($_GET['db']))
		$db = $conn->escapeString($_GET['db']);

	if (isset($_GET['table']))
		$table = $conn->escapeString($_GET['table']);
	
	if ($conn->hasCharsetSupport()) {
		
		$charsetSql = $conn->listCharset();
		if ($conn->isResultSet($charsetSql)) {
			while ($charsetRow = $conn->fetchAssoc($charsetSql)) {
				$charsetList[] = $charsetRow['Charset'];
			}
		}
	
		$collationSql = $conn->listCollation();
		if ($conn->isResultSet($collationSql)) {
			while ($collationRow = $conn->fetchAssoc($collationSql)) {
				$collationList[$collationRow['Collation']] = $collationRow['Charset'];
			}
		}
	}
}

// undo magic quotes, if necessary
if (get_magic_quotes_gpc()) {
	$_GET = stripslashesFromArray($_GET);
	$_POST = stripslashesFromArray($_POST);
	$_COOKIE = stripslashesFromArray($_COOKIE);
	$_REQUEST = stripslashesFromArray($_REQUEST);
}

function stripslashesFromArray($value) {
    $value = is_array($value) ?
                array_map('stripslashesFromArray', $value) :
                stripslashes($value);

    return $value;
}

function loginCheck($validateReq = true) {
	if (!isset($_SESSION['SB_LOGIN'])){
		if (isset($_GET['ajaxRequest']))
			redirect("login.php?timeout=1");
		else
			redirect("login.php");
		exit;
	}
	if ($validateReq) {
		if (!validateRequest()) {
			exit;
		}
	}

	startOutput();
}

function redirect($url) {
	if (isset($_GET['ajaxRequest']) || headers_sent()) {
		global $requestKey;
		?>
		<script type="text/javascript" authkey="<?php echo $_GET['requestKey']; ?>">

		document.location = "<?php echo $url; ?>" + window.location.hash;

		</script>
		<?php
	} else {
		header("Location: $url");
	}
	exit;
}

function validateRequest() {
	global $requestKey;
	if (isset($_GET['requestKey']) && $_GET['requestKey'] != $requestKey) {
		return false;
	}
	return true;
}

function startOutput() {
	global $sbconfig;
	
	if (!headers_sent()) {
		if (extension_loaded("zlib") && ((isset($sbconfig['EnableGzip']) && $sbconfig['EnableGzip'] == true) || !isset($sbconfig['EnableGzip'])) && !ini_get("zlib.output_compression") && ini_get("output_handler") != "ob_gzhandler") {
			ob_start("ob_gzhandler");
		} else {
			ob_start();
		}
		
		register_shutdown_function("finishOutput");
	}
}

function finishOutput() {	
	global $conn;
	
	ob_end_flush();
	
	if (isset($conn) && $conn->isConnected()) {
		$conn->disconnect();
		unset($conn);
	}
}

function outputPage() {

global $requestKey;
global $sbconfig;
global $conn;
global $lang;

require 'views/layout.php';
}

function outputError($errorText) {
    require 'views/error.php';
}

function requireDatabaseAndTableBeDefined() {
	global $db, $table;

	if (!isset($db)) {
        outputError(__("For some reason, the database parameter was not included with your request."));
		exit;
	}

	if (!isset($table)) {
        outputError(__("For some reason, the table parameter was not included with your request."));
		exit;
	}

}

function formatForOutput($text) {
	$text = nl2br(htmlentities($text, ENT_QUOTES, 'UTF-8'));
	if (utf8_strlen($text) > PREVIEW_CHAR_SIZE) {
		$text = utf8_substr($text, 0, PREVIEW_CHAR_SIZE) . " <span class=\"toBeContinued\">[...]</span>";
	}
	return $text;
}

function formatDataForCSV($text) {
	$text = str_replace('"', '""', $text);
	return $text;
}

function splitQueryText($query) {
	// the regex needs a trailing semicolon
	$query = trim($query);

	if (substr($query, -1) != ";")
		$query .= ";";

	// i spent 3 days figuring out this line
	preg_match_all("/(?>[^;']|(''|(?>'([^']|\\')*[^\\\]')))+;/ixU", $query, $matches, PREG_SET_ORDER);

	$querySplit = "";

	foreach ($matches as $match) {
		// get rid of the trailing semicolon
		$querySplit[] = substr($match[0], 0, -1);
	}

	return $querySplit;
}

function memoryFormat($bytes) {
	if ($bytes < 1024)
		$dataString = $bytes . " B";
	else if ($bytes < (1024 * 1024))
		$dataString = round($bytes / 1024) . " KB";
	else if ($bytes < (1024 * 1024 * 1024))
		$dataString = round($bytes / (1024 * 1024)) . " MB";
	else
		$dataString = round($bytes / (1024 * 1024 * 1024)) . " GB";

	return $dataString;
}

function themeFile($filename) {
	global $theme;
	return smartCaching("themes/" . $theme . "/" . $filename);
}

function assetFile($filename) {
	return smartCaching("assets/" . $filename);
}

function smartCaching($filename) {
	return $filename . "?ver=" . str_replace(".", "_", VERSION_NUMBER);
}

function __($t) {
	global $gt;
	return $gt->getTranslation($t);
}

function __p($singular, $plural, $count) {
	global $gt;
	if ($count == 1) {
		return $gt->getTranslation($singular);
	} else {
		return $gt->getTranslation($plural);
	}
}

function utf8_substr($str, $from, $len) {
# utf8 substr
# www.yeap.lv
  return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
                       '$1',$str);
}

function utf8_strlen($str) {
    $i = 0;
    $count = 0;
    $len = strlen ($str);
    while ($i < $len) {
    $chr = ord ($str[$i]);
    $count++;
    $i++;
    if ($i >= $len)
        break;

    if ($chr & 0x80) {
        $chr <<= 1;
        while ($chr & 0x80) {
        $i++;
        $chr <<= 1;
        }
    }
    }
    return $count;
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

?>