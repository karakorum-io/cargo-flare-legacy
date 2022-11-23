<?php
require 'libs/phpmailer/class.phpmailer.php';
$mail = new PHPMailer;

$mail->IsSMTP();  

// Set mailer to use SMTP
$mail->SMTPDebug = 4;  
//$mail->Host = 'smtp.office365.com';                 // Specify main and backup server
//$mail->Host = 'ritewayautotransport-com.mail.protection.outlook.com';                 // Specify main and backup server
$mail->Host = 'americancartransporters-com.mail.protection.outlook.com'; 
$mail->Port = '25';                                    // Set the SMTP port
$mail->SMTPAuth = false;                            // Enable SMTP authentication
$mail->SMTPSecure = 'tls';
//$mail->Username = 'noreply@ritewayautotransport.com';                // SMTP username
//$mail->Password = 'Hello2016?';                 // SMTP password



$mail->FromName = 'freightdragon Tech Support';
//$mail->AddAddress('aztest@gmail.com');  // Add a recipient
$mail->SetFrom('admin@americancartransporters.com');
//$mail->AddReplyTo('admin@ritewayautotransport.com');
$mail->AddAddress('stefano.madrigal@gmail.com');  // Add a recipient	




$mail->IsHTML(true);                                  // Set email format to HTML
$mail->Subject = 'Internal SMTP Server Test Email';
$mail->Body    = 'This is a test using Internal Server <strong>SMTP SERVICES!</strong>';
$mail->AltBody = 'This is a test using Internal Server SMTP SERVICES ';

if(!$mail->Send()) {
   echo '<br>Message could not be sent.';
   echo '<br>Mailer Error: ' . $mail->ErrorInfo;
   exit;
}

echo 'Message has been sent';
?>