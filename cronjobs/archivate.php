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
 
set_time_limit(0);
@session_start();
error_reporting(E_ALL|E_NOTICE);
require_once("init.php");

ob_start();
$_SESSION['iamcron'] = true; // Says I am cron for Full Access
$days_def = 21; //By default
$mm = new MembersManager($daffny->DB);
$em = new EntityManager($daffny->DB);
//get Members
$members = $mm->getMembers("`id` = `parent_id`");
foreach ($members as $member) {
    //get settings "mark as expired" days
    $settings = $member->getDefaultSettings();
    $days = (int) $settings->mark_as_expired;
	$deliveredDays = (int)$settings->mark_assumed_delivered;
    if (empty($days)) {
        $days = $days_def; //if empty set default days
    }
    //get company members
    $companymembers = Member::getCompanyMembers($daffny->DB, $member->parent_id);
    //get Not Archived Entities
    $notarchived = $em->getNotArchived($companymembers);
	$assumedDelivered = $em->getAssumedDelivered($companymembers);
    //set Archived
    foreach ($notarchived as $entity) {
        $date_arr = array();
        //Check type and get created/quoted/ordered date
        switch ($entity->type) {
            case Entity::TYPE_LEAD :
                if (!is_null($entity->created)) {
                    $date_arr = explode("-", substr($entity->created, 0, 10));
                }
                break;
            case Entity::TYPE_QUOTE :
                if (!is_null($entity->quoted)) {
                    $date_arr = explode("-", substr($entity->quoted, 0, 10));
                }
                break;
            case Entity::TYPE_ORDER :
                if (!is_null($entity->ordered)) {
                    $date_arr = explode("-", substr($entity->ordered, 0, 10));
                }
                break;
            default:
                break;
        }
        echo "id: " . $entity->id." (".@$date_arr[0]."-".@$date_arr[1]."-".@$date_arr[2].")<br />";
        //Check if need archivate
        if (count($date_arr) == 3) {
            $duetime = mktime(23, 59, 59, $date_arr[1], $date_arr[2] + $days, $date_arr[0]);
            if (time() > $duetime) {
                //Archivate if need
                $entity->setStatus(Entity::STATUS_ARCHIVED);
                echo " new status: Archivated after ".$days." day(s)<br />";
            }
        }
    }

	// set Delivered
	foreach ($assumedDelivered as $entity) {
		/* @var $entity Entity */
		$date_arr = explode("-", substr($entity->ordered, 0, 10));
		if (count($date_arr) == 3) {
			$duetime = mktime(23, 59, 59, $date_arr[1], $date_arr[2] + $deliveredDays, $date_arr[0]);
			if (time() > $duetime) {
				$entity->setStatus(Entity::STATUS_DELIVERED);
			}
		}
	}
}
$_SESSION['iamcron'] = false;

//send mail to Super Admin
$body = ob_get_clean();
try {
    $mail = new FdMailer(true);
    $mail->isHTML();
    $mail->Body = $body;
    $mail->Subject = "Archivated cron.";
    $mail->AddAddress($daffny->cfg['suadminemail']);
    $mail->SetFrom($daffny->cfg['info_email']);
    $mail->Send();
} catch (Exception $exc) {
   echo $exc->getTraceAsString();
}
require_once("done.php");
?>