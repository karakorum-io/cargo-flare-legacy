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



//$em = new EntityManager($daffny->DB);

//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB(now(),INTERVAL 1 DAY),'%Y-%m-%d') ";
//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB('2014-10-05',INTERVAL 1 DAY),'%Y-%m-%d') order by e.id desc limit 60,90"; 

//$where =  "e.id = 4348";

$where = "e.`type` = 3 AND e.`status` = 3  AND e.delivered IS NOT NULL  limit 0,800";
print  $where;

$rows = $daffny->DB->selectRows('e.id', Entity::TABLE . " e ", "WHERE " . $where);

  if(!empty($rows))
  {
	    $messages = "<p>Order ID/Entity Id resposted</p><br>";
        $entities = array();
$i=1;
        foreach ($rows as $row) {
      
            $entity = new Entity($daffny->DB);

            $entity->load($row['id']);

			
			
			if ($entity->status == Entity::STATUS_ARCHIVED && $entity->isPaidOff() && $ds = $entity->getDispatchSheet()) {//&& trim($entity->archived) == ''
		          // $entity->setStatus(Entity::STATUS_DELIVERED);
					print "<br>".$i."--".$entity->id."--".$entity->number."--".$entity->status."==".$entity->delivered;
					print "<br>==============================<br>";
			         $i++;
			        fflush();
	            }
			
            
			
        }


    
		
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;

    
  }
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>