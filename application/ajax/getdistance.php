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
$out = array('success' => false);
//ob_start();
if ($memberId > 0) {
    try {
        switch ($_POST['action']) {
            case 'getdistance':

                $distance = RouteHelper::getRouteDistance($_POST['ocity'] . "," . $_POST['ostate'] . ",US", $_POST['dcity'] . "," . $sql_arr['dstate'] . ",US");
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                $data[] = array(
                    'distance' => $distance,

                );

                $out = array('success' => true, 'data' => $data);

                break;
        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
//ob_clean();
echo $json->encode($out);
require_once "done.php";
