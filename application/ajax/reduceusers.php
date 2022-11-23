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
$out = array('success' => false, 'message' => "", 'data' => '');
$users = (int) post_var("users");
$renewal_users = (int) post_var("renewal_users");
if ($memberId <= 0) {
    $out = array('success' => false);
} else {

    try {

        if (($renewal_users - $users) < 0) {
            $m = new Member($daffny->DB);
            $data = $m->getNextInactiveUsers(getParentId(), $users, $renewal_users);
        } else {
            $data = "No one";
        }
        $out = array(
            'success' => true
            , 'data' => $data
            , 'message' => "",
        );
    } catch (Exception $e) {
        $out = array(
            'success' => false
            , 'data' => ""
            , 'message' => "Access denied.",
        );
    }
}
echo json_encode($out);
require_once "done.php";
