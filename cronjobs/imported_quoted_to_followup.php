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


$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);


$where = "e.`type` = 1 AND e.`status` = ".Entity::STATUS_LQUOTED;

print  $where;
$rows = $daffny->DB->selectRows('e.id', Entity::TABLE . " e ", "WHERE " . $where);
  if(!empty($rows))
  {

	    $messages = "<p>Quoted Lead converted to Follow</p><br>";
       
$i=1;

        foreach ($rows as $row) {
			print "<br>-".$row['id'];
            $entity = new Entity($daffny->DB);
            $entity->load($row['id']);
			$entity->setStatus(Entity::STATUS_LFOLLOWUP);
			
           $entity->updateHeaderTable();
       }


		$numRows = sizeof($rows);

		print "numRows : ".$numRows;
  }
$_SESSION['iamcron'] = false;

//send mail to Super Admin

    require_once("done.php");

?>