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
set_time_limit(800);
error_reporting(E_ALL | E_NOTICE);
require_once("init.php");
$_SESSION['iamcron'] = true; // Says I am cron for Full Access


$mm = new MembersManager($daffny->DB);
$em = new EntityManager($daffny->DB);
$aqs = new AutoQuotingSettings($daffny->DB);
//get Members
//$members = $mm->getMembers("`id` = `parent_id`");
$members = $mm->getMembers("`id` = `parent_id`");
foreach ($members as $member) {
    //get settings "Enable automated quoting" and
    $settings = $aqs->loadByOwnerId($member->parent_id);

    $license = new License($daffny->DB);
    $license->loadCurrentLicenseByMemberId($member->parent_id);
    if ( $settings->is_enabled == 1 && $license->addon_aq_id > 0 ) {
        //get company members
        $companymembers = Member::getCompanyMembers($daffny->DB, $member->parent_id);
        //get Not Quoted Leads
        $notquoted = $em->getNotQuotedLeads($companymembers);
        //Autoquote
        foreach ($notquoted as $entity) {
            $entity->autoQuoting();
            //echo "member id: ".$member->parent_id." autoquote id: " . $entity->id . "<br />";
        }
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