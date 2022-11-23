<?php



/* * ************************************************************************************************

 * Cron RepostToCd

 *

 * Client:		FreightDragon

 * Version:		1.0

 * Date:			2011-04-26

 * Author:		C.A.W., Inc. dba INTECHCENTER

 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

 * E-mail:		techsupport@intechcenter.com

 * CopyRight 2011 FreightDragon. - All Rights Reserved

 * ************************************************************************************************** */

@session_start();

require_once("init.php");

require_once("../libs/phpmailer/class.phpmailer.php");


ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);


$_SESSION['iamcron'] = true; // Says I am cron for Full Access

//$where = " type = 1 and  sent = 0 order by id desc "; 

//print  $where;

try {
	$mail = new FdMailer(true);
                $mail->IsHTML(true);
	     /*
                $mail = new FdMailer(true);
                $mail->IsHTML(false);
                $mail->Body = "5645474767 4564575";
                $mail->Subject = "Removal Request from CD for ID ";
                $mail->SetFrom("posting@freightdragon.com");
                //$mail->AddAddress(self::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
				$mail->AddAddress("neeraj@freightdragon.com", "Central Dispatch Test");
				//$mail->AddBCC("posting@freightdragon.com");
				//$mail->AddCC("neeraj@freightdragon.com");
                //$mail->AddBCC(self::CENTRAL_DISPATCH_EMAIL_BCC);
                ob_start();
                //$ret = $mail->Send();
			    print $ret = $mail->SendToCD();
				print "mail sent";
				*/
				/*
				    $mail->Host = 'smtp.mailgun.org';  // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = 'noreply@americancartransporters.com';                 // SMTP username
					$mail->Password = 'Welcome1';                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;                                    // TCP port to connect to			
					$mail->SetFrom('noreply@americancartransporters.com', 'American Car Transporters'); // Please add a from email address here
					$mail->AddAddress("neeraj@freightdragon.com");  // Add a recipient
					$mail->Body = "5645474767 4564575";
                    $mail->Subject = "Removal Request from CD for ID ";
					$mail->Send();
				*/
				
				 $mail->Host = 'smtp.mailgun.org';  // Specify main and backup SMTP servers
					$mail->SMTPAuth = true;                               // Enable SMTP authentication
					$mail->Username = 'quotes@americancartransporters.com';                 // SMTP username
					$mail->Password = 'Welcome1';                           // SMTP password
					$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
					$mail->Port = 587;                                    // TCP port to connect to			
					$mail->SetFrom('quotes@americancartransporters.com', 'American Car Transporters'); // Please add a from email address here
					$mail->AddAddress("neeraj@freightdragon.com");  // Add a recipient
					$mail->Body = "5645474767 4564575 3446456555555555";
                    $mail->Subject = "Removal Request from CD for ID ";
					$mail->Send();
					print "mail sent";
			  //exit;
                
            } catch (phpmailerException $e) {
                print $ret = $e->getMessage();
            } catch (Exception $e) {

            }

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>