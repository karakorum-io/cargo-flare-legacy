<?php



//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(-1);
set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

define('FILES_DIR', "review");
require_once("../daffny/init.php");

require_once(ROOT_PATH."functions.php");

require_once(ROOT_PATH."app/classes/main.php");

$main = $daffny->load_class(CLASS_PATH, "main.php", "ReviewMain");

ob_start();

$main->init();

$_SESSION['member_chmod'] = 1;
$_SESSION['member_id'] = 1;
$main->run();

$main->done();