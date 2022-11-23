<?php
require 'libs/phpmailer/class.phpmailer.php';

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'outlook.office365.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'noreply@americancartransporters.com';                 // SMTP username
$mail->Password = 'Password1';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to


$mail->AddReplyTo('kim.oglesby@americancartransporters.com', 'Kim Oglesby');
$mail->setFrom('noreply@freightdragon.com', 'American Car Transporters Customer Service');
$mail->addAddress('neeraj@freightdragon.com', 'Neeraj');
$mail->addAddress('freightdragon@gmail.com', 'Neeraj');
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'American Car Transporters Vehicle Transportation e-Sign Request';
$mail->Body    = 'Dear neerajleads Rojas444,<br/>
<br/>
The attached file contains your invoice for shipping your 2018 make3 model Operable from GRIMES546, IA to Miami, IN.<br/>
<br/>
Please review the information carefully. If any of the information is in error, please contact us immediately at 561-594-0647, so we may correctly process your order.<br/>
<br/>
Sincerely,<br/>
<br/>
Kim Denis<br/>
American Car Transporters<br/>
561-571-9699</br>
kim.oglesby@americancartransporters.com';
//$mail->AltBody = 'This is just a test to make sure OUTLOOK365 exchange services is working';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent.';
}

//noreply@americancartransporters.com-----outlook.office365.com-587-tls-noreply@americancartransporters.com-Password1SMTP Error: Data not accepted.