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

set_time_limit(800);

error_reporting(E_ALL | E_NOTICE);

require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access


$em = new EntityManager($daffny->DB);

//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.avail_pickup_date,'%Y-%m-%d') = date_format(DATE_SUB(now(),INTERVAL 1 DAY),'%Y-%m-%d') limit 0,1";

$where =  "e.id = 4368";
//print  $where;

$rows = $daffny->DB->selectRows('e.id', Entity::TABLE . " e ", "WHERE " . $where);
  if(!empty($rows))
  {
        $entities = array();

        foreach ($rows as $row) {

            $entity = new Entity($daffny->DB);

            $entity->loadForeignEntity($row['id']);

			print "<br>-".$entity->id."--".$entity->avail_pickup_date;
			
			
			if($entity->repostToCentralDispatch(2))
			{
				
			     $daffny->DB->update(Entity::TABLE, array("avail_pickup_date" => date("Y-m-d H:i:s")), "id = '" . $entity->id . "' ");
				 print "<br>--- Avail Pickup Date Updated -";
			  	
			}
			else
			{
			     print "<br>--- Not Updated -";	
		    }
			
			
            print "<br>==============================<br>";
        }
  }
  
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

/*

    $body = ob_get_clean();

    try {

        $mail = new FdMailer(true);

        $mail->isHTML();

        $mail->Body = $body;

        $mail->Subject = "Autoquoting cron." . date("Y-m-d H:i:s");

        $mail->AddAddress($daffny->cfg['suadminemail']);

        $mail->SetFrom($daffny->cfg['info_email']);

        $mail->Send();

    } catch (Exception $exc) {

        echo $exc->getTraceAsString();

    }

*/

    require_once("done.php");

?>