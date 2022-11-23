<?php
require 'libs/phpmailer/class.phpmailer.php';
$mail = new PHPMailer;

$mail->IsSMTP();  

// Set mailer to use SMTP
//$mail->SMTPDebug = 4;
$mail->Host = 'mail.cargoflare.com'; 
$mail->Port = '465';                                     // Set the SMTP port
$mail->SMTPAuth = true;                                  // Enable SMTP authentication
$mail->SMTPSecure = 'ssl';
$mail->Username = 'noreply@cargoflare.com';           // SMTP username
$mail->Password = '5Xy-#XCT*ZgP'; // SMTP password


$mail->FromName = 'CargoFlare Tech Support';
$mail->SetFrom('noreply@cargoflare.com');          // Add a sender
$mail->AddAddress('stefano.madrigal@gmail.com');              // Add a recipient
                      
$mail->IsHTML(true);                                     // Set email format to HTML
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