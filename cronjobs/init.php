<?php
/***************************************************************************************************
* CronJobs Source Init
*
* Client: 	FreightDragon
* Version: 	1.0
* Date:    	2011-10-28
* Author:  	C.A.W., Inc. dba INTECHCENTER
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:	techsupport@intechcenter.com
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
define('FILES_DIR', "application");
require_once("../daffny/init.php");
require_once(ROOT_PATH."functions.php");

require_once(ROOT_PATH."app/classes/main.php");
$main = $daffny->load_class(CLASS_PATH, "main.php", "ApplicationMain");

$main->init();

require_once(ROOT_PATH."app/classes/action.php");

$action = new AppAction();
$action->daffny = $daffny;

?>