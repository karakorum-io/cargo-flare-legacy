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

$where = " type = 1 and  sent = 0 order by id desc "; 

print  $where;

$rows = $daffny->DB->selectRows('*'," app_mail_sent ", "WHERE " . $where);

  if(!empty($rows))
  {
	    $messages = "<p>Order ID/Entity Id resposted</p><br>";
        $entities = array();

        foreach ($rows as $row) {
      
            $entity = new Entity($daffny->DB);

            $entity->load($row['entity_id']);

				$body = "<p>RePosted date : ". date("Y-m-d H:i:s")."</p><br>";
				
				print "<br>---entityId - ".$row['entity_id']." : ".$row['toAddress'];
				
				try {
			
					$mail = new FdMailer(true);
					$mail->isHTML();
			        $mail->Body = $row['body'];
			
					$mail->SetFrom($row['fromAddress']);
			
					//$mail->Send();
					
					$mail->Subject = $row['subject'];
					//$mail->AddAddress("neeraj@freightdragon.com");
					$mail->AddAddress($row['toAddress']);
					$ret = $mail->SendToCD();
					
					if($ret){
						 $daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"),"sent"=>1), "id = '" . $row['id'] . "' ");
					}
			
				} catch (Exception $exc) {
			
					echo  "-----".$exc;
			
				}
		}
  }

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>