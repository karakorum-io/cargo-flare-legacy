<?php

/*
 * Cron  First quote follow-up: days since quoted
 *
 * Client:  FreightDragon
 * Version: 1.0
 * Date:    2012-05-01
 * Author:  C.A.W., Inc. dba INTECHCENTER
 * Address: 11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:  techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * 
 */

ob_start();
set_time_limit(0);
@session_start();
error_reporting(E_ALL | E_NOTICE);
require_once("init.php");
$_SESSION['iamcron'] = true; // Says I am cron for Full Access

    $mm = new MembersManager($daffny->DB);
    $em = new EntityManager($daffny->DB);
    //get Members
    $members = $mm->getMembers("`id` = `parent_id`");
    foreach ($members as $member) {
        //get settings "First quote follow-up:"
        $settings = $member->getDefaultSettings();
        $days = (int) $settings->first_quote_followup;
        if ($days > 0) {
            //get company members
            $companymembers = Member::getCompanyMembers($daffny->DB, $member->parent_id);
            //get Quoted Entities
            $notfupped = $em->getNotFirstFollowUpped($companymembers);
            //Make first follow up
            foreach ($notfupped as $entity) {
                echo "id: " . $entity->id . "<br />";
                $date_arr = array();
                //Check quoted date
                if (!is_null($entity->quoted)) {
                    $date_arr = explode("-", substr($entity->quoted, 0, 10));
                }
                //Check if need followup
                if (count($date_arr) == 3) {
                    $duetime = mktime(23, 59, 59, $date_arr[1], $date_arr[2] + $days, $date_arr[0]);
                    if (time() > $duetime) {
                        //Create FollowUp if need
                        try {
                            $daffny->DB->query("START TRANSACTION");
                            $fl = new FollowUp($daffny->DB);
                            $fl->setFolowUp(EmailTemplate::SYS_QUOTE_FL_EMAIL, date("Y-m-d H:i:s"), $entity->id);
                            $entity->setFirstFollowUpped();
                            echo " firstfollowupped after " . $days . " day(s) quoted <br />";
                            $daffny->DB->query("COMMIT");
                        } catch (Exception $exc) {
                            $daffny->DB->query("ROLLBACK");
                        }
                    }
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
    $mail->Subject = "First followups cron.";
    $mail->AddAddress($daffny->cfg['suadminemail']);
    $mail->SetFrom($daffny->cfg['info_email']);
    $mail->Send();
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}
  
require_once("done.php");
?>