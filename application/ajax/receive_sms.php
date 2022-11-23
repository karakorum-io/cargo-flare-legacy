<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

/* @var Daffny $daffny */
require_once "init.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'receive_sms':
            // Sender's phone numer
            $from_number = $_REQUEST["From"];
            // Receiver's phone number - Plivo number
            $to_number = $_REQUEST["To"];
            // The SMS text message which was received
            $text = $_REQUEST["Text"];
            // Output the text which was received, you could also store the text in a database.

            $sql = "INSERT INTO app_sms_logs (FromPhone,ToPhone,Message,status,send_recieve,view,notification) values ('" . $from_number . "','" . $to_number . "','" . mysqli_real_escape_string($daffny->DB->connection_id, $text) . "','1','1','1','1')";
            $result = $daffny->DB->query($sql);

            break;
        default:
            break;
    }
}

echo $json->encode($out);
require_once "done.php";
