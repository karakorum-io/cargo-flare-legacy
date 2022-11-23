<?php
/* *************************************************************************************************

* Cron To Manage Older Quickbook Logs

*

* Client:		FreightDragon

* Version:		1.0

* Date:		2017-02-07

* Author:		Chetu, Inc.

* ************************************************************************************************** 

*/

@session_start();
require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

$today = date("F j, Y");
$previousDate = date("Y-m-t", strtotime("-2 months"));

$where1 = "`log_datetime` < '".$previousDate."'";
$where2 = "`enqueue_datetime` < '".$previousDate."'";

$result = $daffny->DB->delete('`quickbooks_log`',$where1);
$result = $daffny->DB->delete('`quickbooks_queue`',$where2);

if($result == 1){
    echo "Crone Successfully! Data Older than $previousDate is deleted";
} else {
    echo "Crone Job Unsuccesfull!";
}

$_SESSION['iamcron'] = false;
require_once("done.php");