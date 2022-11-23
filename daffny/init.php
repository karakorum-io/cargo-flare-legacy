<?php
error_reporting(E_ALL && !E_STRICT);
define('ROOT_PATH', str_replace("\\", "/", dirname(dirname(__FILE__)))."/");
define('SITE_PATH', ($_SERVER['SERVER_PORT'] != 443 ? "http://" : "https://").$_SERVER['HTTP_HOST'].preg_replace("/[a-z_]+\.[a-z]+$/", "", $_SERVER['SCRIPT_NAME']));
define('BASE_PATH', ($_SERVER['SERVER_PORT'] != 443 ? "http://" : "https://").$_SERVER['HTTP_HOST']."/");

/**
* Daffny Engine Path
*/
define('DAFFNY_PATH', ROOT_PATH."daffny/");

/**
* Load Daffny Engine
*/
require_once(DAFFNY_PATH."daffny.php");

/**
* Check for defined files path
*/
if (!defined('FILES_DIR')) {
    Daffny::error("The folder with basic files are not defined.", "You must specify the name of the folder where the basic files are stored.");
}

define('SOURCE_PATH', ROOT_PATH.FILES_DIR."/sources/");
define('CLASS_PATH' , ROOT_PATH.FILES_DIR."/classes/");
define('TPL_PATH'   , ROOT_PATH.FILES_DIR."/templates/");

/**
* Uploads
*/
if (!defined('UPLOADS_DIR')) {
    define('UPLOADS_DIR', "uploads");
}
define('UPLOADS_PATH', ROOT_PATH."uploads/");

/**
* Load help functions
*/
require_once(DAFFNY_PATH."functions.php");

/**
* Load config file
*/
if (file_exists(ROOT_PATH.FILES_DIR."/config.php")) {
    require_once(ROOT_PATH.FILES_DIR."/config.php");
}
else {
    require_once(ROOT_PATH."config.php");
}

/**
* Scan for allow actions
*/
$act = array();
if ($handle = @opendir(SOURCE_PATH))
{
    while (false !== ($file = readdir($handle)))
    {
        if (!preg_match("/^[a-z_0-9\.]+\.php$/", $file)) continue;
        $act[] = str_replace(".php", "", $file);
    }
    closedir($handle);
}

/**
* Initialize Daffny Engine
*/
$daffny = new Daffny();
$daffny->cfg = $CONF;
$daffny->action_arr = $act;

/**
 * Register Class AutoLoader
 */
function autoLoader($className) {
	$matches = null;
	if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', $className, $matches)) {
		$filename = strtolower(implode("_", $matches[0]) . ".php");
		$filepath = ROOT_PATH."application/classes/".$filename;
		if (file_exists($filepath)) {
			require_once($filepath);
			return true;
		}
		$filepath = ROOT_PATH."cp/classes/".$filename;
		if (file_exists($filepath)) {
			require_once($filepath);
			return true;
		}
		$filepath = DAFFNY_PATH."libs/".$filename;
		if (file_exists($filepath)) {
			require_once($filepath);
			return true;
		}
		$filepath = DAFFNY_PATH."libs/anet/".$filename;
		if (file_exists($filepath)) {
			require_once($filepath);
			return true;
		}
	}
	return false;
}

function shutdownHandler() {
	$out = ob_get_clean();
	$error = error_get_last();
	if (!is_null($error) && $error['type'] != E_STRICT) {
		echo '<div style="padding: 10px;margin: 10px; border: 1px solid red; color: #444; font: 12px Verdana; background: #FFF;">';
		echo nl2br("<b>Error:</b> <pre>{$error['message']}</pre>\n<b>File:</b> {$error['file']}\n<b>Line:</b> {$error['line']}");
		echo "</div>";
	}
	echo $out;
	return true;
}
spl_autoload_register('autoLoader');
//register_shutdown_function('shutdownHandler', E_ALL);