<?php

/**
 * This is the CRON file created to send emails to the shipper for matched carrier
 * with order / leads information. and a seperate email template is being used in
 * as an email content.
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

@session_start();

/**
 * Including the involved core functionality libraries
 */
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once("core/template.php");

ob_start();
$_SESSION['iamcron'] = true;

/**
 * Fetching the unsent email from the app_match_carrier table in database on the
 * basis of mail_status and storing in an array
 */
$where = " mail_status = 0 ";

$rows = $daffny->DB->selectRows('id,email,entity_id,owner_id','app_match_carrier', "WHERE " . $where);
//$rows = $daffny->DB->selectRows('id,email,entity_id,owner_id','app_match_carrier', " WHERE id IN (1650823,1650822,1650821,1650817)");

/**
 * For each of the unsent mail getting mail raw data
 */
$MatchCarrierObj = new MatchCarrier($daffny->DB);
$entity = new Entity($daffny->DB);

$i =1;
foreach($rows as $row){

    $MatchCarrierObj->load($row['id']);
    $entity->load($row['entity_id']);
    
    $vehicles = $daffny->DB->selectRows(' *','app_vehicles', "WHERE entity_id = ".$row['entity_id']." AND deleted = 0");
    $origin = $daffny->DB->selectRows(' city,state,zip ','app_locations', "WHERE id = ".$entity->origin_id);
    $destination = $daffny->DB->selectRows(' city,state,zip ','app_locations', "WHERE id = ".$entity->destination_id);    
    $company = $daffny->DB->selectRows(' companyname,phone,email ','app_company_profile', "WHERE owner_id = ".$row['owner_id']);    
    
    /**
     * Update the mail sent status in database table to sent
     */
    $MatchCarrierObj->update(array('mail_status'=>1));
    try{
        //$email = "shahrukhk@chetu.com";
        /**
         * triggering template creation and send email
         */
        $subject = "HOT LOAD Available now, load ID ".$entity->prefix."-".$entity->number;
        
        $template = new TemplateEngine();
        $body = $template->matchCarrierTemplate($entity,$vehicles,$origin,$destination,$company);
        
        $entity->sendMatchCarrierEmail($email,$body,$subject);
        echo $i."#- "." Email Sent on :".$email;
        $i++;
    } catch (FDException $e) {
        print "<br>".$e->getMessage();
        /**
         * update the mail sent status in database table to unsent again
         */
        $MatchCarrierObj->update(array('err_msg'=>$e->getMessage()));
        $MatchCarrierObj->update(array('mail_status'=>0));
    }
}

$_SESSION['iamcron'] = false;
require_once("done.php");