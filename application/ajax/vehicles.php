<?php

/**
 * vehicles.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 * 
 * @author Shahrukh
 * @copyright CargoFlare
 */

 // loading dependencies
require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
$errors = array();

try {
    if ($memberId > 0) {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case "updateVehicleData":
                    $result = $daffny->DB->query("UPDATE `app_order_header` SET  `TotalVehicle` = '" . $_POST['numberOfVehicles'] . "' WHERE `entityid` = '" . $_POST['entity_id'] . "' ");
                    $out = array('success' => true, 'query' => "UPDATE `app_order_header` SET  `TotalVehicle` = '" . $_POST['numberOfVehicles'] . "' WHERE `entityid` = '" . $_POST['entity_id'] . "' ");
                break;
                case "getVehicleCount":
                    $result = $daffny->DB->query("SELECT count(*) as numberOfVehicles FROM `app_vehicles` WHERE `entity_id` = '" . $_POST['entity_id'] . "' AND deleted =0 ");
                    $row = mysqli_fetch_assoc($result);
                    $out = array('success' => true, 'count' => $row['numberOfVehicles']);
                break;
                case "getAllIds":
                    $result = $daffny->DB->query("SELECT `id` FROM `app_vehicles` WHERE `entity_id` = '" . $_POST['entity_id'] . "' ");

                    $vehicleIds = array();
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $vehicleIds[$i] = $row['id'];
                        $i++;
                    }
                    $out = array('success' => true, 'ids' => $vehicleIds);
                break;
                case "checkVehicle":
                    $result = $daffny->DB->query("SELECT count(*) as `existance`, `id` FROM app_vehicles WHERE entity_id = '" . $_POST['entity_id'] . "' AND id = '" . $_POST['id'] . "' ");
                    $row = mysqli_fetch_assoc($result);
                    /* Returning JSON response */
                    $out = array('success' => true, 'id' => $row['id'], 'existance' => $row['existance']);
                break;
                case "add":

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['vehicleEntityId']);

                    $vehicle_data = array(
                        'year' => $_POST['year'],
                        'make' => $_POST['make'],
                        'model' => $_POST['model'],
                        'type' => $_POST['type'],
                        'tariff' => $_POST['carrier_pay'] + $_POST['deposit'],
                        'deposit' => $_POST['deposit'],
                        'carrier_pay' => $_POST['carrier_pay'],
                        'vin' => $_POST['vin'],
                        'inop' => $_POST['inop'],
                        'entity_id' => $_POST['vehicleEntityId']
                    );

                    $vehicleNew = new Vehicle($daffny->DB);
                    $vehicleNew->create($vehicle_data);

                    /* updateing vehicle count in app_order_header */
                    $totalVehicles = getTotalVehicles($daffny->DB, $_POST['vehicleEntityId']) + 1;
                    updateTotalVehicles($daffny->DB, $_POST['vehicleEntityId'], $totalVehicles);
                    updateOrderCosts($daffny->DB, $_POST['vehicleEntityId']);

                    /* Triggering matching carrier stored procedure */
                    $daffny->DB->query("INSERT INTO `chetu_vehicle_popup_match_carrier` (`entity_id`) VALUES('" . $_POST['entity_id'] . "')");

                    /* Returning JSON response */
                    $out = array('success' => true, 'msg' => 'Vehicle Added Successfully!');
                break;
                case "getVehicleList":

                    if ($_POST['noOfVehicles0'] == '1') {
                        $tableField = "id` = '" . $_POST['id'] . "";
                    } else {
                        $tableField = "`entity_id` = '" . $_POST['entity_id'] . "'";
                    }

                    $query = "select * from `app_vehicles` WHERE " . $tableField . " AND deleted = 0";
                    $result = $daffny->DB->query($query);

                    $data = '';
                    $i = 1;
                    $totalTariff = 0;
                    $deposit = 0;
                    $carrier = 0;

                    while ($vehicleData = mysqli_fetch_assoc($result)) {

                        if ($result->num_rows > 1) {
                            $delete = '<img onclick="deleteOnScreen(' . $i . ')" src="/images/icons/delete.png" title="Delete" alt="Delete" class="deleteVehicle" width="16" height="16">';
                        } else {
                            $delete = '<span style="font-weight: bold;" class="hint--left hint--rounded hint--bounce hint--error" data-hint="Cant Delete Single Order"><img src="/images/icons/delete.png" title="Cannot Delete" class="deleteVehicle" width="16" height="16"></span>';
                        }

                        $totalTariff = $totalTariff + $vehicleData['carrier_pay'];
                        $deposit = $deposit + $vehicleData['deposit'];
                        $carrier = $carrier + $vehicleData['carrier_pay'] + $vehicleData['deposit'];
                        $data .= '<tr class="vehiclePopupRow" id="rowid' . $i . '">
                                    <td align="center">
                                            <input id="radio' . $i . '" row="' . $i . '" entity = "' . $_POST['entity_id'] . '" name="vehicleId" type="radio" class="vehicleId" value="' . $vehicleData['id'] . '"></td>
                                             <td><span >' . $i . '</span></td>
                                    <td><span id="year' . $i . '">' . $vehicleData['year'] . '</span></td>'
                            . '<td><span id="model' . $i . '">' . $vehicleData['model'] . '</span></td>'
                            . '<td id="make' . $i . '">' . $vehicleData['make'] . '</td>
                                    <td id="vType' . $i . '">' . $vehicleData['type'] . '</td>'
                            . '<td id="vin' . $i . '">' . $vehicleData['vin'] . '</td>'
                            . '<td id="inop' . $i . '">' . ($vehicleData['inop'] == 0 ? "No" : "Yes") . '</td>'
                            . '<td id="tariff' . $i . '">' . $vehicleData['carrier_pay'] . '</td>
                                    <td id="deposite' . $i . '">' . $vehicleData['deposit'] . '</td>
                                    <td align="center">
                                        <img onclick="fillEditForm(' . $i . ')" src="/images/icons/edit.png" title="Edit" alt="Edit" width="16" height="16">
                                        &nbsp;&nbsp;
                                        ' . $delete . '
                                    </td>
                                </tr>';
                        $i++;
                    }

                    $query = "SELECT `number`,`prefix`,`shipperfname`,`shipperlname`,`shippercompany`,`shipperemail` FROM `app_order_header` WHERE entityid = '" . $_POST['entity_id'] . "' AND type = 3 AND deleted = 0";
                    $result = $daffny->DB->query($query);
                    $row = mysqli_fetch_assoc($result);
                    $out = array(
                        'success' => true,
                        'data' => $data,
                        'netTariff' => $carrier,
                        'netCarrierPay' => $totalTariff,
                        'netDeposite' => $deposit,
                        'number' => $row['number'],
                        'prefix' => $row['prefix'],
                        'shipperlname' => $row['shipperlname'],
                        'shipperfname' => $row['shipperfname'],
                        'shippercompany' => $row['shippercompany'],
                        'shipperemail' => $row['shipperemail'],
                    );

                break;
                case "getVehicles":
                    $vehicleManager = new VehicleManager($daffny->DB);
                    $vehicles = $vehicleManager->getVehiclesArrData($_POST['id']);
                    $data = '<table class="table table-bordered"><tr><td  style="padding:3px;"><b><p>Year</p></b></td><td  style="padding:3px;"><b><p>Make</p></b></td><td  style="padding:3px;"><b><p>Model</p></b></td><td  style="padding:3px;"><b><p>Type</p></b></td><td  style="padding:3px;"><b><p>Vin#</p></b></td><td  style="padding:3px;"><b><p>Inop</p></b></td></tr>';

                    foreach ($vehicles as $key => $vehicle) {

                        $data .= '<tr><td bgcolor="#ffffff" style="padding:3px;">' . $vehicle['year'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $vehicle['make'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $vehicle['model'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $vehicle['type'] . '</td><td bgcolor="#ffffff" style="padding:3px;"> ' . $vehicle['vin'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;">' . ($vehicle['inop'] == 0 ? "No" : "Yes") . '</td></tr>';
                    }

                    $data .= '</table>';

                    $out = array('success' => true, 'data' => $data);

                break;
                case "get":
                    $vehicle = new Vehicle($daffny->DB);
                    $vehicle->load($_POST['id']);
                    $data = array(
                        'year' => $vehicle->year,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'type' => $vehicle->type,
                        'carrier_pay' => $vehicle->tariff,
                        'deposit' => $vehicle->deposit,
                        'color' => $vehicle->color,
                        'plate' => $vehicle->plate,
                        'state' => $vehicle->state,
                        'vin' => $vehicle->vin,
                        'lot' => $vehicle->lot,
                        'inop' => $vehicle->inop,
                    );
                    foreach ($data as $key => $value) {
                        $data[$key] = rawurlencode(strip_tags($value));
                    }
                    $out = array('success' => true, 'data' => $data);
                break;
                case "save":

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);

                    $entity_old = new Entity($daffny->DB);
                    $entity_old->load($_POST['entity_id']);
                    $entity_old->getVehicles();

                    $vehicle_data = array(
                        'year' => rawurldecode($_POST['year']),
                        'make' => rawurldecode($_POST['make']),
                        'model' => rawurldecode($_POST['model']),
                        'type' => rawurldecode($_POST['type']),
                        'color' => rawurldecode($_POST['color']),
                        'plate' => rawurldecode($_POST['plate']),
                        'state' => rawurldecode($_POST['state']),
                        'vin' => rawurldecode($_POST['vin']),
                        'lot' => rawurldecode($_POST['lot']),
                        'inop' => rawurldecode($_POST['inop']),
                        'entity_id' => $_POST['entity_id']);

                    $vehicle_data += array(
                        'tariff' => (float) rawurldecode($_POST['carrier_pay']),
                        'deposit' => rawurldecode($_POST['deposit']),
                        'carrier_pay' => rawurldecode($_POST['carrier_pay']) - (float) rawurldecode($_POST['deposit']),
                    );

                    foreach ($vehicle_data as $key => $value) {
                        if (
                            trim($value) == "" &&
                            !in_array(
                                $key, array(
                                    'color',
                                    'plate',
                                    'state',
                                    'vin',
                                    'lot',
                                )
                            )
                        ) {
                            $errors[] = ucfirst($key) . " value is required";
                        }
                    }

                    if (count($errors) != 0) {

                        $out = array('success' => false, 'data' => $errors);
                        break;
                    }

                    $vehicle = new Vehicle($daffny->DB);
                    if (isset($_POST['id']) && !is_null($_POST['id']) && ctype_digit((string) $_POST['id'])) {

                        $vehicle->load($_POST['id']);

                        $NotesStr = "";

                        if ($vehicle->tariff != (float) rawurldecode($_POST['carrier_pay'])) {

                            $NotesStr = "Total tarrif amount changed $" . $vehicle->tariff . " to $" . number_format((float) rawurldecode($_POST['carrier_pay']), 2, '.', '');
                        }

                        if ($vehicle->deposit != rawurldecode($_POST['deposit'])) {

                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }

                            $NotesStr .= "Deposit amount changed $" . $vehicle->deposit . " to $" . number_format((float) rawurldecode($_POST['deposit']), 2, '.', '');
                        }

                        if ($NotesStr != "") {
                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr);

                            $note = new Note($daffny->DB);
                            $note->create($note_array);

                            /* updating order costs */
                            updateOrderCosts($daffny->DB, $_POST['entity_id']);
                        }

                        $vehicle->update($vehicle_data);

                        /* use to send mail to matching carriers */

                        if ($entity->checkVehicleChange($entity_old)) {

                            $entity->update(array("vehicle_update" => 1));
                        }
                    } else {
                        $vehicle->create($vehicle_data);

                        /* use to send mail to matching carriers */
                        $entity->update(array("vehicle_update" => 1));
                    }
                    $data = array();
                    $vehicles = $entity->getVehicles();
                    foreach ($vehicles as $k => $vehicle) {

                        $data[] = array(
                            'id' => (int) $vehicle->id,
                            'year' => $vehicle->year,
                            'make' => $vehicle->make,
                            'model' => $vehicle->model,
                            'type' => $vehicle->type,
                            'tariff' => $vehicle->carrier_pay + $vehicle->deposit,
                            'deposit' => $vehicle->deposit,
                            'carrier_pay' => $vehicle->carrier_pay,
                            'vin' => $vehicle->vin,
                            'lot' => $vehicle->lot,
                            'inop' => $vehicle->inop,
                        );
                    }
                    foreach ($data as $key => $value) {

                        foreach ($value as $i => $v) {
                            $data[$key][$i] = rawurlencode(addslashes(strip_tags($v)));
                        }
                    }

                    $entity->attributes['pickup_terminal_fee'] = @$_POST['pickup_terminal_fee'];
                    $entity->attributes['dropoff_terminal_fee'] = @$_POST['delivery_terminal_fee'];
                    $entity->getVehicles(true);

                    /* Triggering matching carrier stored procedure */
                    $daffny->DB->query("INSERT INTO `chetu_vehicle_popup_match_carrier` (`entity_id`) VALUES('" . $_POST['entity_id'] . "')");

                    $out = array(
                        'success' => true,
                        'data' => $data,
                        'msg' => 'Vehicle Saved Successfully!',
                    );

                    $out['total_tariff'] = rawurlencode($entity->getTotalTariff());
                    $out['total_deposit'] = rawurlencode($entity->getTotalDeposit());
                    $out['carrier_pay'] = rawurlencode($entity->getCarrierPay());

                break;
                case "del":

                    $vehicle = new Vehicle($daffny->DB);
                    $vehicle->load((int) $_POST['id']);

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);

                    if (count($entity->getVehicles()) < 2) {
                        break;
                    }

                    /* updateing vehicle count in app_order_header */
                    $totalVehicles = getTotalVehicles($daffny->DB, $_POST['entity_id']) - 1;
                    updateTotalVehicles($daffny->DB, $_POST['entity_id'], $totalVehicles);

                    /* deleting vehicle */
                    $vehicle->delete((int) $_POST['id']);

                    $data = array();

                    foreach ($entity->getVehicles(true) as $k => $vehicle) {
                        $data[] = array(
                            'id' => (int) $vehicle->id,
                            'year' => $vehicle->year,
                            'make' => $vehicle->make,
                            'model' => $vehicle->model,
                            'type' => $vehicle->type,
                            'tariff' => $vehicle->tariff,
                            'carrier_pay' => $vehicle->carrier_pay,
                            'deposit' => $vehicle->deposit,
                            'vin' => $vehicle->vin,
                            'lot' => $vehicle->lot,
                            'inop' => $vehicle->inop,
                        );
                    }

                    foreach ($data as $key => $value) {
                        foreach ($value as $i => $v) {
                            $data[$key][$i] = rawurlencode(addslashes(strip_tags($v)));
                        }
                    }

                    updateOrderCosts($daffny->DB, $_POST['entity_id']);

                    $out = array("success" => true, "data" => $data);

                    if ($entity->type != Entity::TYPE_LEAD) {

                        $out['total_tariff'] = rawurlencode($entity->getTotalTariff());
                        $out['total_deposit'] = rawurlencode($entity->getTotalDeposit());
                        $out['carrier_pay'] = rawurlencode($entity->getCarrierPay());
                    }

                    /* use to send mail to matching carriers */
                    $entity->update(array("vehicle_update" => 1));

                    /* Triggering matching carrier stored procedure */
                    $daffny->DB->query("INSERT INTO `chetu_vehicle_popup_match_carrier` (`entity_id`) VALUES('" . $_POST['entity_id'] . "')");

                break;
                case "saveSearch":
                    parse_str(rawurldecode($_POST['data']), $data);
                    $savedSearch = new SavedSearch($daffny->DB);
                    $savedSearch->create(array('member_id' => $_SESSION['member_id'], 'name' => rawurldecode($_POST['name']), 'data' => serialize($data)));
                    $out = array('success' => true);
                break;
                case "loadSearch":
                    $savedSearch = new SavedSearch($daffny->DB);
                    $savedSearch->load($_POST['id']);
                    if ($_SESSION['member_id'] != $savedSearch->member_id) {
                        break;
                    }

                    $out = array('success' => true, 'data' => unserialize($savedSearch->data));

                break;
                case "searchCompany":
                    $data = CompanyProfile::searchByName($daffny->DB, rawurldecode($_POST['search']));
                    $out = array('success' => true, 'data' => $data);
                break;
                case "getCompanies":
                    $data = CompanyProfile::getCompanies($daffny->DB, explode(",", rawurldecode($_POST['ids'])));
                    $out = array('success' => true, 'data' => $data);
                break;
                case "copy":

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);

                    $vehicle = new Vehicle($daffny->DB);
                    $vehicle->load($_POST['id']);

                    $vehicle_update = array(
                        'vin' => $_POST['vin'],
                        'tariff' => (float) rawurldecode($_POST['tariff']),
                        'deposit' => (float) rawurldecode($_POST['deposit']),
                        'carrier_pay' => ((float) rawurldecode($_POST['tariff']) - (float) rawurldecode($_POST['deposit'])));

                    $vehicle->update($vehicle_update);

                    $vehicle_data = array(
                        'year' => $vehicle->year,
                        'make' => $vehicle->make,
                        'model' => $vehicle->model,
                        'type' => $vehicle->type,
                        'tariff' => $vehicle->carrier_pay + $vehicle->deposit,
                        'deposit' => $vehicle->deposit,
                        'carrier_pay' => $vehicle->carrier_pay,
                        'color' => $vehicle->color,
                        'plate' => $vehicle->plate,
                        'state' => $vehicle->state,
                        'vin' => $vehicle->vin,
                        'lot' => $vehicle->lot,
                        'inop' => $vehicle->inop,
                        'tariff' => $vehicle->tariff,
                        'deposit' => $vehicle->deposit,
                        'carrier_pay' => $vehicle->carrier_pay,
                        'entity_id' => $_POST['entity_id']
                    );

                    $vehicleNew = new Vehicle($daffny->DB);
                    $vehicleNew->create($vehicle_data);

                    /* updateing vehicle count and order costs in app_order_header */
                    $totalVehicles = getTotalVehicles($daffny->DB, $_POST['entity_id']) + 1;
                    updateTotalVehicles($daffny->DB, $_POST['entity_id'], $totalVehicles);
                    updateOrderCosts($daffny->DB, $_POST['entity_id']);

                    $data = array();
                    $vehicles = $entity->getVehicles(true);
                    foreach ($vehicles as $k => $vehicle) {
                        $data[] = array(
                            'id' => (int) $vehicle->id,
                            'year' => $vehicle->year,
                            'make' => $vehicle->make,
                            'model' => $vehicle->model,
                            'type' => $vehicle->type,
                            'tariff' => $vehicle->carrier_pay + $vehicle->deposit,
                            'deposit' => $vehicle->deposit,
                            'carrier_pay' => $vehicle->carrier_pay,
                            'vin' => $vehicle->vin,
                            'lot' => $vehicle->lot,
                            'inop' => $vehicle->inop,
                        );
                    }

                    foreach ($data as $key => $value) {
                        foreach ($value as $i => $v) {
                            $data[$key][$i] = $v;
                        }
                    }

                    /* Triggering matching carrier stored procedure */
                    $daffny->DB->query("INSERT INTO `chetu_vehicle_popup_match_carrier` (`entity_id`) VALUES('" . $_POST['entity_id'] . "')");

                    $out = array('success' => true, 'data' => $data);
                    $out['total_tariff'] = rawurlencode($entity->getTotalTariff());
                    $out['total_deposit'] = rawurlencode($entity->getTotalDeposit());
                    $out['carrier_pay'] = rawurlencode($entity->getCarrierPay());

                break;
            }
        }
    }
} catch (FDException $e) {
    echo $e->getMessage();
}

function updateOrderCosts($connection, $entityId)
{
    $getCostsFromVehicles = "Select SUM(tariff) as tariff, SUM(carrier_pay) as carrier_pay, SUM(deposit) as deposit from app_vehicles WHERE `entity_id` = '" . $entityId . "' AND `deleted` = 0";
    $result = $connection->hardQuery($getCostsFromVehicles);
    $costs = mysqli_fetch_assoc($result);

    /* updated amounts */
    $newTariff = $costs['tariff'];
    $newCarrierPay = $costs['carrier_pay'];
    $newDeposite = $costs['deposit'];

    /* updating app order header */
    $query = " UPDATE
    `app_order_header`
    SET
    `total_tariff` = '" . $newTariff . "',
    `total_tariff_stored` = '" . $newTariff . "',
    `carrier_pay_stored` = '" . $newCarrierPay . "',
    `total_carrier_pay` = '" . $newCarrierPay . "',
    `total_deposite` = '" . $newDeposite . "'
     WHERE `entityid` = '" . $entityId . "'";
    $connection->hardQuery($query);

    /* updating app entities */
    $query = " UPDATE
      `app_entities`
    SET
      `total_tariff_stored` = '" . $newTariff . "',
      `carrier_pay_stored` = '" . $newCarrierPay . "'
    WHERE `id` = '" . $entityId . "'
    ";
    $connection->hardQuery($query);
}

function updateTotalVehicles($connection, $entityId, $vehicles)
{

    $query = "UPDATE `app_order_header` SET `TotalVehicle` = '" . $vehicles . "' WHERE `entityid` = '" . $entityId . "'";
    $connection->hardQuery($query);
}

function getTotalVehicles($connection, $entityId)
{
    $rawVehicleCount = $connection->hardQuery(
        "SELECT `TotalVehicle` FROM `app_order_header` WHERE `entityid` = '" . $entityId . "' "
    );
    return mysqli_fetch_assoc($rawVehicleCount)['TotalVehicle'];
}

echo json_encode($out);
require_once "done.php";
