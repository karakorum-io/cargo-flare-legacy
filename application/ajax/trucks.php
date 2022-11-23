<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once 'init.php';
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
if ($memberId > 0) {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'delete':
                    if (!isset($_POST['id'])) {
                        break;
                    }

                    $truck = new Truck($daffny->DB);
                    $truck->delete($_POST['id']);
                    $out = array('success' => true);
                    break;
                default:
                    break;
            }
        } catch (FDException $e) {}
    }
}
echo $json->encode($out);
require_once "done.php";
