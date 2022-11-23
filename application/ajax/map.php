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
        switch ($_POST['action']) {
            case "getRoute":
                try {
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    $origin = $entity->getOrigin();
                    $destination = $entity->getDestination();
                    $map_url = "http://maps.google.com/maps?";
                    $saddr = $origin->city . "," . $origin->state . "," . $origin->zip . "," . $origin->country;
                    $daddr = $destination->city . "," . $destination->state . "," . $destination->zip . "," . $destination->country;
                    $map_url .= 'saddr="' . $saddr . '"&daddr="' . $daddr . '"';
                    $out = array('success' => true, 'data' => rawurlencode($map_url));
                } catch (FDException $e) {
                    break;
                }
                break;
            default:
                break;
        }
    }
}
echo $json->encode($out);
require_once "done.php";
