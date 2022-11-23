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

print "----------------------";
ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);


$_SESSION['iamcron'] = true; // Says I am cron for Full Access


//$body = ob_get_clean();
        $body = "<p>RePosted date : ". date("Y-m-d H:i:s")."</p><br>";
		
		//print "---".$daffny->cfg['suadminemail'];
		
		try {
	print "================";
			$mail = new FdMailer(true);
			print "  ================";
	
			$mail->isHTML();
	
			$mail->Body = "Testing";
	
			//$mail->Subject = "Reposting job on Central dispatch cron " . date("Y-m-d H:i:s");
	
			//$mail->AddAddress("admin@ritewayautotransport.com");
			//$mail->AddCC("neeraj@freightdragon.com");
	
			$mail->SetFrom("kumar@freightdragon.com");
	
			//$mail->Send();
			print $daffny->cfg['info_email'];
			$mail->Subject = "Testing mail";
			$mail->AddAddress("neeraj@freightdragon.com");
			$ret = $mail->SendToCD();
			
	
		} catch (Exception $exc) {
	
			echo  "-----".$exc;
	
		}
 

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>