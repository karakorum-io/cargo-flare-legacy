<?php

define('FILES_DIR', "cp");
require_once("../daffny/init.php");
require_once(ROOT_PATH."functions.php");

require_once(ROOT_PATH."app/classes/main.php");

$main = $daffny->load_class(CLASS_PATH, "main.php", "CpMain");

$main->init();

$main->run();
$main->done();