<?php

/**
 * autoquote.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
$out = array('success' => false);

$origin = new Origin($daffny->DB);
$origin->setAttributes(array(
    'city' => $_POST['origin_city'],
    'state' => $_POST['origin_state'],
    'zip' => $_POST['origin_zip'],
    'country' => $_POST['origin_country'],
));
$origin->loaded = true;
$destination = new Destination($daffny->DB);
$destination->setAttributes(array(
    'city' => $_POST['destination_city'],
    'state' => $_POST['destination_state'],
    'zip' => $_POST['destination_zip'],
    'country' => $_POST['destination_country'],
));
$destination->loaded = true;
$estShipDate = date('Y-m-d', strtotime($_POST['shipping_est_date']));
$enclosed = $_POST['shipping_ship_via'] == '2';
$member = new Member($daffny->DB);
$member->load($_SESSION['member_id']);
$autoQuotes = array();
$aqm = new AutoQuotingManager($daffny->DB);
$autoQuoted = 0;
if (isset($_POST['quote_id'])) {
    $quote = new Entity($daffny->DB);
    $quote->load($_POST['quote_id']);
    $deposit = (float) $quote->getAssigned()->getDefaultSettings()->order_deposit;
    $deposit_type = $quote->getAssigned()->getDefaultSettings()->order_deposit_type;
    foreach ($quote->getVehicles() as $vehicle) {
        /** @var Vehicle $vehicle */
        $amount = (float) $aqm->getChargeAmount($origin, $destination, $vehicle, $quote->getAssigned()->getParent()->id, $estShipDate, $enclosed);
        if ($amount > 0) {
            $depositAmount = ($deposit_type == "amount") ? $deposit : (0.01 * $amount * $deposit);
            $vehicle->update(array(
                'carrier_pay' => $amount,
                'tariff' => $amount + $depositAmount,
                'deposit' => $depositAmount,
            ));
            $autoQuoted++;
        }
    }
    $quote->getVehicles(true);
    $autoQuotes = array(
        'total_tariff' => $quote->getTotalTariff(),
        'total_deposit' => $quote->getTotalDeposit(),
        'carrier_pay' => $quote->getCarrierPay(),
        'message' => $autoQuoted . ' vehicle(s) quoted.',
    );
} else {
    if (!isset($_POST['vehicles'])) {
        $autoQuotes = array(
            'message' => 'No vehicles for quote',
        );
    } else {
        $member = new Member($daffny->DB);
        $member->load(getMemberId());
        $deposit_type = $member->getDefaultSettings()->order_deposit_type;
        $deposit = (float) $member->getDefaultSettings()->order_deposit;
        foreach ($_POST['vehicles'] as $type) {
            $vehicle = new Vehicle($daffny->DB);
            $vehicle->loaded = true;
            $vehicle->setAttributes(array(
                'type' => $type,
            ));
            $amount = $aqm->getChargeAmount($origin, $destination, $vehicle, $member->parent_id, $estShipDate, $enclosed);
            $depositAmount = ($deposit_type == "amount") ? $deposit : (0.01 * $amount * $deposit);
            $autoQuotes[] = array(
                'carrier_pay' => number_format((float) $amount, 2, '.', ''),
                'deposit' => number_format((float) $depositAmount, 2, '.', ''),
            );
        }
    }
}
echo $json->encode($autoQuotes);
require_once "done.php";
