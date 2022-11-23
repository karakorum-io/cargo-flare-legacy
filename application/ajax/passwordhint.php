<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
$out = array('success' => false, 'message' => "");
$email = trim($_POST['email']);
if ($email != "" && validate_email($email)) {
    try {
        $member = new Member($daffny->DB);
        $hint = $member->getPasswordHint($email);
        $out = array('success' => true, 'message' => $hint);
    } catch (FDException $e) {
        $out = array('success' => true, 'message' => "No data");
    }
} else {
    if ($email == "") {
        $out = array('success' => true, 'message' => "Empty email");
    } else {
        $out = array('success' => true, 'message' => "Invalid email: " . $email);
    }
}

echo json_encode($out);
require_once "done.php";
