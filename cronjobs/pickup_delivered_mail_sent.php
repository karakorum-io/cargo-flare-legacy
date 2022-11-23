<?php

@session_start();

require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");

ob_start();

$_SESSION['iamcron'] = true;

$rows = $daffny->DB->selectRows('*'," app_mail_sent ", "WHERE " . $where);

if(!empty($rows))
{
	$messages = "<p>Order ID/Entity Id</p><br>";
	$entities = array();

	foreach ($rows as $row) {
		
		$form_id = (int)$row['form_id'];
		$member_id = (int)$row['member_id'];

		$entity = new Entity($daffny->DB);
		$entity->load($row['entity_id']);
		
		try {
			if($row['type']==EmailTemplate::BULK_MAIL_TYPE_PICKUP || $row['type']==EmailTemplate::BULK_MAIL_TYPE_DELIVER){
				$entity->sendOrderUpdatePickupDeliveredEmail($row['type'],$form_id,$add = array(),$row['is_default'],$member_id);
				$daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"),"sent"=>1), "id = '" . $row['id'] . "' ");
				
				print "Sent to Entity ID : ".$row['entity_id']." To Address : ".$row['toAddress']."<br>";
			}
		} catch (Exception $exc) {
			echo "<br>Exception : ".$exc->getMessage()."<br>";
			$daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"),"errorMsg"=>$exc), "id = '" . $row['id'] . "' ");
		}
	}
} else {
	echo "<br> No Emails to send";
}

$_SESSION['iamcron'] = false;
//send mail to Super Admin
require_once("done.php");
?>