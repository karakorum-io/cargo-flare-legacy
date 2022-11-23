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
$out = array('success' => false, 'message' => "");
if ($memberId <= 0) {
    $out = array('success' => false);
} else {
    switch ($_POST['action']) {
        case "get":{
                $member_row = $daffny->DB->select_one("*", "members", "WHERE id='" . $memberId . "'");
                if ($member_row['read_id'] == "") {
                    $member_row['read_id'] = 0;
                }

                $results = $daffny->DB->selectRow("*", "app_sysmessages", "WHERE id > '" . $member_row['read_id'] . "' AND added > '" . $member_row['reg_date'] . "'");
                if (!empty($results)) {
                    $message = '<div align="right" style="height:20px;"><img onClick="closeSysMessage(\'' . $results['id'] . '\');" src="' . SITE_IN . 'images/icons/close.png" /></div> <div align="left" style="color:red;"><b><u>System Message:</u></b></div><div><br>' . $results['message'] . '</div>';
                    $out = array('success' => true, 'message' => $message);
                }
            }
            break;
        case "close":{
                if (isset($_POST['id']) && (int) $_POST['id'] > 0) {
                    $daffny->DB->update("members", array('read_id' => (int) $_POST['id']), " id='" . $memberId . "'");
                    $out = array('success' => true);
                }
            }
            break;
    }
}
echo $json->encode($out);
require_once "done.php";
