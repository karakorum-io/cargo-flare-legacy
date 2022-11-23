<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

/* @var Daffny $daffny */
require_once "init.php";
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
ob_start();
if ($memberId > 0) {
    try {
        switch ($_GET['action']) {

            case 'delete':
                $sql = "delete from app_payments_check where id=" . $_GET['id'];

                $daffny->DB->query($sql);
                $out = (array('success' => true));

                break;

        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
ob_clean();
echo $json->encode($out);
require_once "done.php";
