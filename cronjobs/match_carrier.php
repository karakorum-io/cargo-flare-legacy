<?php

/* *************************************************************************************************
 * Cron RepostToCd
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:		2011-04-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * *************************************************************************************************/

@session_start();
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();

require_once("init.php");
$_SESSION['iamcron'] = true;  // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '512M');
ini_set('post_max_size', '512M');
ini_set('max_input_time', 80000000);

$where = " mail_status = 0 ";

$rows = $daffny->DB->selectRows('id,email,entity_id,owner_id','app_match_carrier', "WHERE " . $where);
  	if(!empty($rows)){
	    
        $i=0;
        foreach ($rows as $row) {
          $email = trim($row['email']);
		   
				  if($email !='')
				  {
					$MatchCarrierObj = new MatchCarrier($daffny->DB);
					$MatchCarrierObj->load($row['id']);
					
					$entity = new Entity($daffny->DB);
					$entity->load($row['entity_id']);
					
					$MatchCarrierObj->update(array('mail_status'=>1));
					//$email = "shahrukhk@freightdragon.info"; 
					
					try {
						echo $row['owner_id']."<br>";
						  $template = 707;
						    if($row['owner_id']==159)
						    $template = 713;
						    
						    if ($row['owner_id'] == 255) {
                            	$template = 738;
                           	}
							
							/**
							 * when owner id =1 than calling custom SMTP function
							 */
							if($row['owner_id']==1){
								$entity->sendMatchCarrierCustomSMTP(
									$template, 
									array(), 
									false,
									$email,
									$MatchCarrierObj->account_id
								);
							} else {
								$entity->sendEmailMatchCarrierUsingLocalSetting(
									$template, 
									array(), 
									false,
									$email,
									$MatchCarrierObj->account_id
								);
							}
					   } catch (FDException $e) {
							print "<br>".$e->getMessage();
							$MatchCarrierObj->update(array('err_msg'=>$e->getMessage()));
						}
				  }
			    
			 $i++;
			 fflush();
        }    
  	}
  	$numRows = sizeof($rows);
	print "<br>numRows : ".$numRows;

$_SESSION['iamcron'] = false;
//send mail to Super Admin
require_once("done.php");

?>