<?php
/***************************************************************************************************
* Ajax Source Init
*
* Client: 	FreightDragon
* Version: 	1.0
* Date:    	2011-10-03
* Author:  	C.A.W., Inc. dba INTECHCENTER
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:	techsupport@intechcenter.com
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
define('FILES_DIR', "application");
require_once("../../daffny/init.php");
$daffny->DB = $daffny->load_lib("mysql");
$daffny->DB->connect($CONF['my_host'], $CONF['my_user'], $CONF['my_pass'], $CONF['my_base'], $CONF['my_pref']);
$daffny->html = $daffny->load_lib("html");
$daffny->auth = $daffny->load_lib("auth");
$daffny->form = $daffny->load_lib("form");
$daffny->tpl = $daffny->load_lib("template");
$daffny->auth->type = "email";
$daffny->auth->authorise();
$daffny->tpl  = $daffny->load_lib("template");
require_once(ROOT_PATH."libs/JSON.php");
require_once(ROOT_PATH."functions.php");
$json = new Services_JSON();

?>