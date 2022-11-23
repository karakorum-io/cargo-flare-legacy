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
 
echo "CRON Execution started";

@session_start();

require_once "init.php";
require_once "../libs/phpmailer/class.phpmailer.php";

ob_start();

set_time_limit(0);

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$_SESSION['iamcron'] = true;

$where = " type = 5 AND sent = 0 AND errorMsg IS NULL order by id desc ";
$rows = $daffny->DB->selectRows('*', " app_mail_sent ", "WHERE " . $where);

$combine = 0;

if (!empty($rows)) {
    echo $messages = "<p>Order ID/Entity Id</p><br>";
    $entities = array();
    $orderID = array();

    foreach ($rows as $row) {

        $form_id = (int) $row['form_id'];
        $member_id = (int) $row['member_id'];

        $row['entity_id'];

        $_SESSION['member_id'] = $member_id;
        $entity = new Entity($daffny->DB);

        $entity->load($row['entity_id']);

        $orderID[] = $entity->prefix . "-" . $entity->number;

        print "<br>EntityId - " . $row['entity_id'] . " : " . $row['toAddress'] . "";
        $emailArr = array();
        $emailArr['to'] = $row['toAddress'];
        $emailArr['cc'] = $row['cc'];
        $emailArr['bcc'] = $row['bcc'];
        $emailArr['subject'] = $row['subject'];
        $emailArr['mail_extra'] = "";
        $emailArr['body'] = $row['body'];
        $emailArr['attach_type'] = $row['attach_type'];
        $emailArr['member_id'] = $row['member_id'];

        try {
            if ($row['type'] == 5 && $row['combine'] == 0) {
                $entity->sendSelectedTemplateBulkSend($form_id, $emailArr, $member_id);
                $daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"), "sent" => 1), "id = '" . $row['id'] . "' ");
            } else {
                $combine = $row['combine'];
                $tplName = $entity->sendSelectedTemplateBulkSendcombine($form_id, $emailArr, $member_id);
                $daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"), "sent" => 1), "id = '" . $row['id'] . "' ");
            }

        } catch (Exception $exc) {
            $daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"), "errorMsg" => $exc), "id = '" . $row['id'] . "' ");
        }

    } //end foreach

} //end if

if ($combine == 1) {
    $entity->combineattchformdb($emailArr, $member_id, $orderID, $tplName);
    foreach ($rows as $row) {
        $daffny->DB->update("app_mail_sent", array("sentDate" => date("Y-m-d H:i:s"), "sent" => 1), "id = '" . $row['id'] . "' ");
    }
}

$_SESSION['member'] = null;
$_SESSION['iamcron'] = false;

echo "<br>CRON Execution ended";

require_once "done.php";
