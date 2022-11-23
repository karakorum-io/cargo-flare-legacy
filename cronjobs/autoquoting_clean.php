<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
 * ************************************************************************************************** */
 
session_start();

require_once("init.php");
$daffny->DB->delete("app_autoquoting_quotes", "`date` < DATE(DATE_SUB(NOW(), INTERVAL 2 MONTH))");
require_once("done.php");