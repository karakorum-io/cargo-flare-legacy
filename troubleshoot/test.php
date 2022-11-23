<?php
require 'class.phpmailer.php'; // path to the PHPMailer class
       require 'class.smtp.php';
           $mail = new PHPMailer();
           $mail->IsSMTP();  // telling the class to use SMTP
           $mail->SMTPDebug = 2;
           $mail->Mailer = "smtp";
           $mail->Host = "mail.cargoflare.com";
           $mail->Port = 465;
           $mail->SMTPAuth = true; // turn on SMTP authentication
           $mail->Username = "noreply@cargoflare.com"; // SMTP username
           $mail->Password = "ZC9zgu3_sYy5"; // SMTP password
           $Mail->Priority = 1;
           $mail->AddAddress("noreply@cargoflare.com","Name");
           $mail->SetFrom($noreply@cargoflare.com, $name);
           $mail->AddReplyTo($aztest@gmail.com,$name);
           $mail->Subject  = "This is a Test Message";
           $mail->Body     = $user_message;
           $mail->WordWrap = 50;
           if(!$mail->Send()) {
           echo 'Message was not sent.';
           echo 'Mailer error: ' . $mail->ErrorInfo;
           } else {
           echo 'Message has been sent.';
           }
?>
