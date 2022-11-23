<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
 * ************************************************************************************************** */

@session_start();

require_once("init.php");

//require_once("../libs/phpmailer/class.phpmailer.php");

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

$where = " status=1 ";

//print  $where;

$rows = $daffny->DB->selectRows('*', " app_sms_account_users ", "WHERE ".$where);
$insertStr = "";
  if(!empty($rows))
  {
	     foreach ($rows as $row) {
			 
			// $row['id']
			 
			 $sql = "SELECT DISTINCT


Case ToPhone

when '".$row['phone']."' then FromPhone

else ToPhone end 

as FromPhone
				FROM app_sms_logs
				WHERE STATUS =1 AND (FromPhone='".$row['phone']."' OR ToPhone = '".$row['phone']."') 
				ORDER BY SmsDate DESC ";
				
				


				//and send_recieve=1
				//print_r($numOfSms);
								   $result = $daffny->DB->query($sql);
				                    
									if ($daffny->DB->num_rows() > 0) {
										while ($rowSms = $daffny->DB->fetch_row($result)) {
											
											 $insertStr .= "('".$row['user_id']."','".$row['owner_id']."','".$rowSms['FromPhone']."'),";
									  
										}
										
									}
									
			 $numRows = $daffny->DB->num_rows();
		print "<br>".$row['phone']." : numRows : ".$numRows;
		  }
		
  }
 
		$insertStr = substr($insertStr,0,-1);
									
  
  
  print "<br>". $sql = "INSERT INTO app_sms_phone (member_id,owner_id,phone)values".$insertStr;
									  //$result = $daffny->DB->query($sql);
									  

$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    require_once("done.php");

?>