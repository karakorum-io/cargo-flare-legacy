<?php

// script to send custom email from customer portal using FD SMTP settings

set_time_limit(0);
@session_start();
error_reporting(E_ALL|E_NOTICE);
require_once("init.php");

// code to send email finishes here

$to = $_POST['to'];
$subject = $_POST['subject'];
$message = $_POST['message'];

if($to == NULL || $subject == NULL || $message == NULL){
	print_r(json_encode(array("STATUS"=>FALSE,"MESSAGE"=>"Mandatory fields are empty")));
	die;	
} else {

	//sending email
	$mail = new FdMailer(true);
	$mail->isHTML();
	$mail->Body = $message;

	$mail->SetFrom("info@freightdragon.com");
	$mail->Subject = $subject;
	$mail->AddAddress($to);

	$ret = $mail->SendToCD();

	if($ret){
		print_r(json_encode(array("STATUS"=>TRUE,"MESSAGE"=>"Mail Sent!")));
		die;
	} else {
		print_r(json_encode(array("STATUS"=>FALSE,"MESSAGE"=>"Mail Not Sent!")));
		die;
	}
}




// code to send email finishes here

$_SESSION['iamcron'] = false;
require_once("done.php");
?>