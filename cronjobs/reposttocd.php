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

require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);



$em = new EntityManager($daffny->DB);

$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB(now(),INTERVAL 1 DAY),'%Y-%m-%d') ";
//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB('2015-02-06',INTERVAL 1 DAY),'%Y-%m-%d') order by e.id desc ";

//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and e.id=606273 order by e.id desc ";
 
//$where =  "e.id = 4348";
print  $where;

$rows = $daffny->DB->selectRows('e.id', Entity::TABLE . " e ", "WHERE " . $where);

  if(!empty($rows))
  {
	    $messages = "<p>Order ID/Entity Id resposted</p><br>";
        $entities = array();

        foreach ($rows as $row) {
      
            $entity = new Entity($daffny->DB);

            $entity->load($row['id']);

			print "<br>-".$entity->id."--".$entity->number."--".$entity->posted;
			$messages .= "<p>".$entity->id."  : ".$entity->number." : ".$entity->posted."</p><br>";
			
			
			if($entity->repostToCentralDispatch(2))
			{
				
			     $daffny->DB->update(Entity::TABLE, array("posted" => date("Y-m-d H:i:s")), "id = '" . $entity->id . "' ");
				 print "<br>--- Posted Date Updated -<br>";
				 
				// usleep(4000000);
			  	
			}
			else
			{
			     print "<br>--- Not Updated -";	
		    }
			
            print "<br>==============================<br>";
			
			//fflush();
			
        }


    
		
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;
		
		
		//$body = ob_get_clean();
        $body = "<p>RePosted date : ". date("Y-m-d H:i:s")."</p><br>";
		$body .= "<p>How many reposted : ". $numRows."</p><br>";
		$body .= $messages;

		//print "---".$daffny->cfg['suadminemail']."-----";
		
		try {
	
			$mail = new FdMailer(true);
	
			$mail->isHTML();
	
			$mail->Body = $body;
	
			$mail->Subject = "Reposting job on Central dispatch cron " . date("Y-m-d H:i:s");
	
			$mail->AddAddress("admin@ritewayautotransport.com");
			$mail->AddCC("neeraj@freightdragon.com");
	
			$mail->SetFrom($daffny->cfg['info_email']);
	
			$mail->Send();
			
			/*$mailData = array(
                            'from' => $daffny->cfg['info_email'],
                            'to' => "admin@ritewayautotransport.com",
                            'cc' => "neeraj@freightdragon.com",
                            'bcc' => "",
                            'subject' => "Reposting job on Central dispatch cron " . date("Y-m-d H:i:s"),
                            'body' => $body,
						    'type' => 1,
							'sent' => 0
                    )
			$ret = $mail->SendToCD($mailData,1);
	      */
		} catch (Exception $exc) {
	
			echo print "-----".$exc->getTraceAsString();
	
		}
		
    
  }
  
  

$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    require_once("done.php");

?>