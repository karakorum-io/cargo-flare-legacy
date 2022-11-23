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

@session_start();

require_once("init.php");

//require_once("../libs/phpmailer/class.phpmailer.php");

ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);

require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

print "---0";
$sql1 = "update app_accounts set status = 2 where is_shipper=1 ";
$result = $daffny->DB->query($sql1);
print "---1";
$sql2 = "update app_accounts X INNER JOIN
(
	 SELECT B.id as id ,B.Company_name,
	   B.first_name,B.last_name, B.city,B.state,
	   max(date_format(A.created,'%Y-%m-%d')), count(*)
	FROM `app_accounts` B LEFT OUTER JOIN
	`app_entities` A
	ON 
	B.ID = A.account_id
	WHERE B.is_shipper=1 
	AND date_format(A.created,'%Y-%m-%d') <= date_format(NOW(),'%Y-%m-%d') 
	AND date_format(A.created,'%Y-%m-%d') >= date_format(DATE_SUB(NOW(), INTERVAL 45 day),'%Y-%m-%d')
	GROUP BY B.ID, B.Company_name,
	B.first_name,B.last_name, B.city,B.state
) AS Y
ON X.Id = Y.id
SET status = 1";
$result = $daffny->DB->query($sql2);
print "---2";
$sql3 = "update app_accounts set status = 0 where is_shipper=1 and status = 2";
$result = $daffny->DB->query($sql3);
  print "---3";

$sql4 = "UPDATE app_accounts X INNER JOIN (
SELECT B.id AS id, B.Company_name, B.first_name, B.last_name, B.city, B.state, MAX( DATE_FORMAT( A.created,  '%Y-%m-%d' ) ) AS LastOrderDate, COUNT( * ) 
FROM  `app_accounts` B
LEFT OUTER JOIN  `app_entities` A ON B.ID = A.account_id
WHERE B.is_shipper =1
GROUP BY B.ID, B.Company_name, B.first_name, B.last_name, B.city, B.state
) AS Y ON X.Id = Y.id
SET last_order_date = Y.LastOrderDate WHERE is_shipper =1";
$result = $daffny->DB->query($sql4);
print "---4";
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>