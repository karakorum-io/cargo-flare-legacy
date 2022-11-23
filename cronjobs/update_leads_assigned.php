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


$counter = 1;

for($i=1;$i<=46000;$i++)
{
	
	 $sql2 = "update `app_entities` A , 

				`external_data` B
				
				set A.status = 22,
				
				A.`number` = LEFT ( A.`information` ,
				INSTR(A.`information`, '-') -1) ,
				
				A.`prefix` = Right ( A.`information` ,
				length(A.`information`) - INSTR(A.`information`, '-')),
				
				A.`assigned_id` = case B.`assigned_to` 
				
				when 'justin' then '165'
				
				when 'bmiadmin' then '159'
				
				when 'todd' then '166'
				
				when 'paul' then '164'
				
				when 'sam' then '161'
				
				when 'admin' then '159'
				
				when 'jonathan' then '163'
				
				when 'robert' then '178'
				
				when 'brett' then '160'
				
				when 'whalen' then '178'
				
				when 'kim' then '174'
				
				when 'zsean' then '178'
				
				when 'ashley' then '167'
				
				else '159' end
				
				WHERE A.information = B.number
				
				and A.`parentid` = 159
				
				and B.id between 1 to 500
				
				and B.status = 1
				
				and B.id >= ".$counter."

                and B.id < ".($counter + 500);
    
	 //$result = $daffny->DB->query($sql2);

     $sql3 = "update `app_order_header` A , 

`external_data` B

set A.Status = 22,

A.`number` = LEFT ( A.`information` ,
INSTR(A.`information`, '-') -1) ,

A.`prefix` = Right ( A.`information` ,
length(A.`information`) - INSTR(A.`information`, '-')),

A.`assigned_id` = case B.`assigned_to` 

when 'justin' then '165'

when 'bmiadmin' then '159'

when 'todd' then '166'

when 'paul' then '164'

when 'sam' then '161'

when 'admin' then '159'

when 'jonathan' then '163'

when 'robert' then '178'

when 'brett' then '160'

when 'whalen' then '178'

when 'kim' then '174'

when 'zsean' then '178'

when 'ashley' then '167'

else '159' end

WHERE A.information = B.number

and A.`parentid` = 159

and B.id between 1 to 500

and B.status = 1

and B.id >= ".$counter."

and B.id < ".($counter + 500);

     $counter = $counter + 500;
	 
    // $result = $daffny->DB->query($sql3);
}



$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>