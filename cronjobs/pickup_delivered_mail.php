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



set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

function getAlmostUniqueHash($id, $number)
	{
		return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
	}

$where = " type = 3 AND status =6  AND   date_format(load_date,'%Y-%m-%d') >=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) ,'%Y-%m-%d') AND  date_format(load_date,'%Y-%m-%d') <=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d') and id='116129'";  //and parentid=159

//and parentid=159 limit 0,1 and id='42380'
print  $where;

$rows = $daffny->DB->selectRows('id', Entity::TABLE , "WHERE " . $where);
  if(!empty($rows))
  {
        $i=1;
        foreach ($rows as $row) {
      
				$entity = new Entity($daffny->DB);
				$entity->load($row['id']);

			    print "<br>".$i."--".$entity->id."--".$entity->number."--".$entity->status."==".$entity->load_date."==".$entity->parentid;
				print "<br>";
				
				try{
					// Update Entity
						$update_arr = array(
							'pickup_hash' => getAlmostUniqueHash($entity->id, $entity->getNumber())
						);
						$entity->update($update_arr);
						
					//$entity->sendOrderPickupEmail(EmailTemplate::BULK_MAIL_TYPE_PICKUP,EmailTemplate::SYS_ORDER_PICKUP_MAIL,array(), true,$entity->parentid);
					
					print "--pickup mail data loged--";
					 $i++;
				 } catch (Exception $exc) {
								echo  "<br><br>-----".$exc."<br><br>";
				}
				 fflush();
         }
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;
  }
  
 print "<br><br>==============Delivered================<br><br>"; 
 
  $where = " type = 3 AND status =8  AND date_format(delivery_date,'%Y-%m-%d') >=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 90 DAY) ,'%Y-%m-%d') AND  date_format(delivery_date,'%Y-%m-%d') <=  DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d') and id=116129";  //and parentid=159
  // and parentid=159 limit 0,1 
  print  $where;
  $rows1 = $daffny->DB->selectRows('id', Entity::TABLE , "WHERE " . $where);
  if(!empty($rows1))
  {
        $i=1;
        foreach ($rows1 as $row) {
      
            $entity = new Entity($daffny->DB);
            $entity->load($row['id']);

			    print "<br>".$i."--".$entity->id."--".$entity->number."--".$entity->status."==".$entity->delivery_date."==".$entity->parentid;
				print "<br>";
				try{
					// Update Entity
						$update_arr = array(
							'delivered_hash' => getAlmostUniqueHash($entity->id, $entity->getNumber())
						);
						$entity->update($update_arr);
						
					$entity->sendOrderDeliveredEmail(EmailTemplate::BULK_MAIL_TYPE_DELIVER,EmailTemplate::SYS_ORDER_DELIVERED_MAIL,array(), true,$entity->parentid);
					
					print "--deliver mail data loged--";
					
						
					
					 $i++;
				 } catch (Exception $exc) {
					echo  "<br><br>-----".$exc."<br><br>";
				}
				 fflush();
         }
		$numRows = sizeof($rows1);
		print "numRows : ".$numRows;
  }
  

$_SESSION['iamcron'] = false;
    require_once("done.php");


?>