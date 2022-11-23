<?php

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

define('FILES_DIR', "application");
require_once("../daffny/init.php");
require_once(ROOT_PATH."functions.php");
require_once(ROOT_PATH."app/classes/main.php");

$main = $daffny->load_class(CLASS_PATH, "main.php", "ApplicationMain");

ob_start();
$main->init();
$main->run();
$main->done();