<?php

/*
 * Cron follow-ups
 *
 * Client:  FreightDragon
 * Version: 1.0
 * Date:    2012-04-25
 * Author:  C.A.W., Inc. dba INTECHCENTER
 * Address: 11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:  techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * 
 */
ob_start();
set_time_limit(0);
@session_start();
error_reporting(E_ALL|E_NOTICE);
require_once("init.php");
$_SESSION['iamcron'] = true; // Says I am cron for Full Access
$followupManager = new FollowUpManager($daffny->DB);
//get followaps
$followups = $followupManager->getFollowUps("`followup` >= '".date("Y-m-d")."'");
foreach ($followups as $followup) {
	$_SESSION['member'] = $daffny->DB->selectRow("*", "members", "WHERE `id` = ".$followup['sender_id']);
    if ($followup->type != 0) {
        $quote = new Entity($daffny->DB);
        $quote->load($followup->entity_id);
        try {
            //try to send email
            $quote->sendFollowUpQuote(FollowUp::$template_id[$followup->type]);
            $followup->delete($followup->id, true);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
	unset($_SESSION['member']);
    printt( $quote->id );
}
$_SESSION['iamcron'] = false;

//send mail to Super Admin

$body = ob_get_clean();
try {
    $mail = new FdMailer(true);
    $mail->isHTML();
    $mail->Body = $body;
    $mail->Subject = "FollowUp cron.". date("Y-m-d H:i:s");
    $mail->AddAddress($daffny->cfg['suadminemail']);
    $mail->SetFrom($daffny->cfg['info_email']);
    $mail->Send();
} catch (Exception $exc) {
   echo $exc->getTraceAsString();
}

require_once("done.php");
?>