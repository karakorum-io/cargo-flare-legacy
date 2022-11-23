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

$where = "e.`type` = 3 order by e.id desc limit 8500,500";
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

			print "<br>-".$entity->id."--".$entity->number;
			//$messages .= "<p>".$entity->id."  : ".$entity->number." : </p><br>";
			
			
			$entity->updateHeaderTable();
            print "<br>==============================<br>";
			
			//fflush();
			
        }


    
		
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;
		
		
		

		//print "---".$daffny->cfg['suadminemail']."-----";
		
		
		
    
  }
  
  

$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    require_once("done.php");

?>