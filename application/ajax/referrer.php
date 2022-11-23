<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false, 'message' => $memberId);
ob_start();
if ($memberId > 0) {
    try {

        switch ($_POST['action']) {
            case "referral_check":

                $referrerStatus = $_POST['referrerStatus'];
                if ($referrerStatus >= 0) {
                    $daffny->DB->update("app_defaultsettings", array("referrer_status" => $referrerStatus), "owner_id = '" . $memberId . "' ");
                    $out = array("success" => true, "message" => "Referrer status updated.");
                } else {
                    $out = array("success" => true, "message" => "Referrer status not updated.");
                }

                break;
            default:
                break;
        }

    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
ob_clean();
echo $json->encode($out);
require_once "done.php";
