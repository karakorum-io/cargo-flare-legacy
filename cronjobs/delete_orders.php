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

//require_once("../libs/phpmailer/class.phpmailer.php");

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



//$em = new EntityManager($daffny->DB);

//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB(now(),INTERVAL 1 DAY),'%Y-%m-%d') ";
//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB('2014-10-05',INTERVAL 1 DAY),'%Y-%m-%d') order by e.id desc limit 60,90"; 

//$where =  "e.id = 4348";

$where = "e.`type` = 3 AND e.`status` = '3' AND (s.`fname` LIKE('%neeraj%')

										OR s.`lname` LIKE('%neeraj%') OR s.`phone1` LIKE('%neeraj%') 

										OR s.`phone2` LIKE('%neeraj%') 

										OR s.`mobile` LIKE('%neeraj%') 

										OR s.`email` LIKE('%neeraj%') 

										OR s.`company` LIKE('%neeraj%') ) limit 0,50";
print  $where;

$rows = $daffny->DB->selectRows('e.id', Entity::TABLE . " e LEFT JOIN " . Shipper::TABLE . " s ON (e.`shipper_id` = s.`id`) ", "WHERE " . $where);

  if(!empty($rows))
  {
	    $messages = "<p>Order ID/Entity Id resposted</p><br>";
        $entities = array();
        $i=1;
        foreach ($rows as $row) {
      
            $entity = new Entity($daffny->DB);

            $entity->load($row['id']);
			
			print "<br>".$i."-e-".$entity->id."-s-".$entity->shipper_id."-o-".$entity->origin_id."-d-".$entity->destination_id;
			print "<br>==============================<br>";
			
			
			$entity->delete($entity->id, true);
			
			$daffny->DB->query("DELETE FROM app_shippers WHERE `id` = ".$entity->shipper_id."");
			
			$daffny->DB->query("DELETE FROM app_locations WHERE `id` = ".$entity->origin_id."");
			
			$daffny->DB->query("DELETE FROM app_locations WHERE `id` = ".$entity->destination_id."");
			
			$daffny->DB->query("DELETE FROM app_vehicles WHERE `entity_id` = ".$entity->id."");
			
			
			$daffny->DB->query("DELETE FROM app_notes WHERE `entity_id` = ".$entity->id."");
			
            
			
			
            $i++;
			fflush();
        }


    
		
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;

    
  }
  
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>