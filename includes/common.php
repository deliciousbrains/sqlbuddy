<?php
/*

SQL Buddy - Web based MySQL administration
http://www.sqlbuddy.com/

common.php
- gets the page setup with the variables it needs

MIT license

2008 Calvin Lough <http://calv.in>

*/

if (!session_id())
	session_start();

include "../config.php";
include "helpers.php";
include "types.php";
include "class/GetTextReader.php";
include "class/Sql.php";

define("VERSION_NUMBER", "1.3.0");
define("PREVIEW_CHAR_SIZE", 65);

$adapterList[] = "mysql";

if (function_exists("sqlite_open"))
{
	$adapterList[] = "sqlite";
}

$cookieLength = time() + (60*24*60*60);

$langList['ca_AD'] = "Català";
$langList['cs_CZ'] = "Čeština";
$langList['de_DE'] = "Deutsch";
$langList['en_US'] = "English";
$langList['es_ES'] = "Español";
$langList['es_AR'] = "Español (Argentina)";
$langList['fr_FR'] = "Français";
$langList['it_IT'] = "Italiano";
$langList['lo_LA'] = "Lao";
$langList['hu_HU'] = "Magyar";
$langList['nl_NL'] = "Nederlands";
$langList['pl_PL'] = "Polski";
$langList['pt_BR'] = "Português (Brasil)";
$langList['pt_PT'] = "Português (Portugal)";
$langList['ru_RU'] = "Русский";
$langList['sk_SK'] = "Slovenčina";
$langList['sl_SI'] = "Slovenski";
$langList['fi_FI'] = "Suomi";
$langList['sv_SE'] = "Svenska";
$langList['tl_PH'] = "Tagalog";
$langList['tr_TR'] = "Türkçe";
$langList['zh_CN'] = "中文 (简体)";
$langList['zh_TW'] = "中文 (繁體)";
$langList['ja_JP'] = "日本語";

if (isset($_COOKIE['sb_lang']) && array_key_exists($_COOKIE['sb_lang'], $langList))
{
	$lang = preg_replace("/[^a-z0-9_]/i", "", $_COOKIE['sb_lang']);
}
else
{
	$lang = "en_US";
}

if ($lang != "en_US")
{
	// extend the cookie length
	setcookie("sb_lang", $lang, $cookieLength);
}
else if (isset($_COOKIE['sb_lang']))
{
	// cookie not needed for en_US
	setcookie("sb_lang", "", time() - 10000);
}

$themeList["classic"] = "Classic";
$themeList["bittersweet"] = "Bittersweet";

if (isset($_COOKIE['sb_theme']))
{
	$currentTheme = preg_replace("/[^a-z0-9_]/i", "", $_COOKIE['sb_theme']);

	if (array_key_exists($currentTheme, $themeList))
	{
		$theme = $currentTheme;

		// extend the cookie length
		setcookie("sb_theme", $theme, $cookieLength);
	}
	else
	{
		$theme = "bittersweet";
		setcookie("sb_theme", "", time() - 10000);
	}
}
else
{
	$theme = "bittersweet";
}

$gt = new GetTextReader($lang . ".pot");

if (isset($_SESSION['SB_LOGIN_STRING']))
{
	$user = (isset($_SESSION['SB_LOGIN_USER'])) ? $_SESSION['SB_LOGIN_USER'] : "";
	$pass = (isset($_SESSION['SB_LOGIN_PASS'])) ? $_SESSION['SB_LOGIN_PASS'] : "";
	$conn = new SQL($_SESSION['SB_LOGIN_STRING'], $user, $pass);
}

// unique identifer for this session, to validate ajax requests.
// document root is included because it is likely a difficult value
// for potential attackers to guess
$requestKey = substr(md5(session_id() . $_SERVER["DOCUMENT_ROOT"]), 0, 16);

if (isset($conn) && $conn->isConnected())
{
	if (isset($_GET['db']))
		$db = $conn->escapeString($_GET['db']);

	if (isset($_GET['table']))
		$table = $conn->escapeString($_GET['table']);

	$charsetSql = $conn->listCharset();
	if ($conn->rowCount($charsetSql))
	{
		while ($charsetRow = $conn->fetchAssoc($charsetSql))
		{
			$charsetList[] = $charsetRow['Charset'];
		}
	}

	$collationSql = $conn->listCollation();
	if ($conn->rowCount($collationSql))
	{
		while ($collationRow = $conn->fetchAssoc($collationSql))
		{
			$collationList[$collationRow['Collation']] = $collationRow['Charset'];
		}
	}
}

// undo magic quotes, if necessary
if (get_magic_quotes_gpc())
{
	$_GET = stripslashesFromArray($_GET);
	$_POST = stripslashesFromArray($_POST);
	$_COOKIE = stripslashesFromArray($_COOKIE);
	$_REQUEST = stripslashesFromArray($_REQUEST);
}

?>