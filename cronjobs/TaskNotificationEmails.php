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
 
set_time_limit(0);
@session_start();

echo "Started_".date('h:i:s')."<br>";

require_once("init.php");

ob_start();
$_SESSION['iamcron'] = true;

// getting member ids and task ids
$sql = "SELECT * FROM app_task_emails WHERE sent = 0";
$result = $daffny->DB->selectRows($sql);

foreach($result as $r){
	
	// getting assigned member contact name
	$member = new Member($daffny->DB);
	$member->load($r['member_id']);
	$member_name = $member->contactname;
	$member_email = $member->email;

	$sql = "SELECT `id`, `sender_id`, `date` FROM `app_tasks` WHERE `id` = ".$r['task_id'];
	$tasks = $daffny->DB->selectRows($sql);

	// getting creator memeber contact name
	$member->load($tasks[0]['sender_id']);
	$assigner_name = $member->contactname;
	$assigner_phone = $member->phone;

	//sending email
	try {
	    $mail = new FdMailer(true);
	    $mail->isHTML();
	    $mail->Body = "Hello ".$member_name.",<br>".$assigner_name." has assigned you ".$tasks[0]['id']." on ".$tasks[0]['date']."<br><br> Please contact ".$assigner_name." if you have any questions at: ".$assigner_phone;
	    $mail->Subject = "New Task Assigned to you by ".$assigner_name;

	    $mail->AddAddress("charlie@yopmail.com");
	    $mail->SetFrom("info@freightdragon.com");
	    $mail->SendToCD();

	} catch (Exception $exc) {
	    die($exc->getTraceAsString());
	}

	$sql = "UPDATE `app_task_emails` SET `sent` = 1 WHERE `id` = ".$r['id'];
	$daffny->DB->selectRows($sql);
}

echo "Ended_".date('h:i:s');
$_SESSION['iamcron'] = false;
require_once("done.php");
?>