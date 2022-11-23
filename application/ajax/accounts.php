<?php

require_once "init.php";
require_once "../../libs/QuickBooks.php";
require_once "../../core/libs/autoQuotes.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

//ob_start();
if ($memberId > 0) {
    try {
        switch ($_POST['action']) {
            case 'ValidateExistingCarrier':
                $accountManager = new AccountManager($daffny->DB);
                $accounts = $accountManager->searchMyCarrier($_POST['text'], $_SESSION['member']['id'], getParentId());

                if(count($accounts) > 0){
                    $out = array('success' => true, "account_id"=>$accounts[0]['id']);
                } else {
                    $out = array('success' => false);
                }
            break;
            case 'ValidateExistingMemberAndCarrier':

                if($_POST['filter'] == "us_dot") {
                    $accountManager = new AccountManager($daffny->DB);
                    $accounts = $accountManager->searchMyCarrier($_POST['usDot'], $_SESSION['member']['id'], getParentId());

                    $sql = "SELECT m.`id` FROM members m JOIN `app_company_profile` cp ON cp.`owner_id` = m.`id` WHERE (cp.`us_dot` LIKE '%" . $_POST['text'] . "%') AND m.`parent_id` = m.`id` AND cp.`is_carrier` = 1";
                    $result = $daffny->DB->query($sql);
                    $members = [];

                    if ($result) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $members[] = $row;
                        }
                    }
                    
                    $out = array('success' => true, "member_id"=>$members[0]['id'], "account_id"=>$accounts[0]['id']);
                } else {
                    $accountManager = new AccountManager($daffny->DB);
                    $accounts = $accountManager->searchMyCarrier($_POST['mcNumber'], $_SESSION['member']['id'], getParentId());
    
                    $sql = "SELECT m.`id` FROM members m JOIN `app_company_profile` cp ON cp.`owner_id` = m.`id` WHERE (cp.`mc_number` LIKE '%" . $_POST['text'] . "%' OR cp.`icc_mc_number` LIKE '%" . $_POST['text'] . "%') AND m.`parent_id` = m.`id` AND cp.`is_carrier` = 1";
                    $result = $daffny->DB->query($sql);
                    if ($result) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $members[] = $row;
                        }
                    }
    
                    $members = [];
                    $out = array('success' => true, "member_id"=>$members[0]['id'], "account_id"=>$accounts[0]['id']);
                }
            break;
            case 'AllCards':
                $Query = "SELECT * FROM `AccountsCCInformation` WHERE AccountID = ".$_POST['AccountID']." AND  Status = 1";
                $SQL = $daffny->DB->query($Query);

                $data = array();

                $count = 0;
                while($row = mysqli_fetch_assoc($SQL)){
                    $data[$count]['CardId'] = $row['CardId'];
                    $data[$count]['AccountID'] = $row['AccountID'];
                    $data[$count]['Number'] = $row['Number'];
                    $data[$count]['FirstName'] = $row['FirstName'];
                    $data[$count]['LastName'] = $row['LastName'];
                    $data[$count]['CVV'] = $row['CVV'];
                    $data[$count]['Type'] = $row['Type'];
                    $data[$count]['ExpiryMonth'] = $row['ExpiryMonth'];
                    $data[$count]['ExpiryYear'] = $row['ExpiryYear'];
                    $data[$count]['Address'] = $row['Address'];
                    $data[$count]['City'] = $row['City'];
                    $data[$count]['State'] = $row['State'];
                    $data[$count]['Zipcode'] = $row['Zipcode'];
                    $data[$count]['Created'] = $row['Created'];
                    $data[$count]['Updated'] = $row['Updated'];
                    $data[$count]['Recent'] = $row['Recent'];
                    $data[$count]['Status'] = $row['Status'];
                    $count++;
                }
                $out = array("success"=>true,"Cards"=>$data);
            break;
            case 'AddCards':
                $Query = "INSERT INTO `AccountsCCInformation` (AccountID,Number,FirstName,LastName,ExpiryMonth,ExpiryYear,CVV,Type,Address,City,State,Zipcode) ";
                $Query .= "VALUES( '".$_POST['AccountId']."','".$_POST['Number']."','".$_POST['FirstName']."', '".$_POST['LastName']."', '".$_POST['ExpiryMonth']."', '".$_POST['ExpiryYear']."', '".$_POST['CVV']."', '".$_POST['Type']."', ";
                $Query .= " '".$_POST['Address']."', '".$_POST['City']."', '".$_POST['State']."', '".$_POST['Zipcode']."' )";
                $SQL = $daffny->DB->query($Query);
                $inserted_id = $daffny->DB->get_insert_id();

                $Query = "SELECT * FROM `AccountsCCInformation` WHERE `CardId` = {$inserted_id}";
                $SQL = $daffny->DB->query($Query);
                $Data = mysqli_fetch_assoc($SQL);
                $out = array("success"=>true,"data"=>$Data);
            break;
            case 'GetSavedCards':
                $Query = "SELECT * FROM `AccountsCCInformation` WHERE `CardId` = {$_POST['CardId']}";
                $SQL = $daffny->DB->query($Query);
                $Data = mysqli_fetch_assoc($SQL);
                $out = array("success"=>true,"data"=>$Data);
            break;
            case 'GetCard':
                $Query = "SELECT * FROM `AccountsCCInformation` WHERE `CardId` = {$_POST['CardId']}";
                $SQL = $daffny->DB->query($Query);
                $Data = mysqli_fetch_assoc($SQL);
                $out = array("success"=>true,"data"=>$Data);
            break;
            case 'DeleteCard':
                $Query = "DELETE FROM `AccountsCCInformation` WHERE `CardId` = {$_POST['CardId']}";
                $SQL = $daffny->DB->query($Query);
                $Data = mysqli_fetch_assoc($SQL);
                $out = array("success"=>true,"data"=>$Data);
            break;
            case 'UpdateCardData':
                $Query = "UPDATE `AccountsCCInformation` SET `Number`='{$_POST['Number']}', `FirstName` = '".$_POST['FirstName']."', `LastName` = '".$_POST['LastName']."',`ExpiryMonth` = '".$_POST['ExpiryMonth']."',`ExpiryYear` = '".$_POST['ExpiryYear']."',`CVV` = '".$_POST['CVV']."',`Type` = '".$_POST['Type']."',`Address` = '".$_POST['Address']."',`State` = '".$_POST['State']."',`City` = '".$_POST['City']."',`Zipcode` = '".$_POST['Zipcode']."',`Updated` = '".date('Y-m-d h:i:s')."' WHERE `CardId` = {$_POST['CardId']}";
                $SQL = $daffny->DB->query($Query);
                $Data = mysqli_fetch_assoc($SQL);
                $out = array("success"=>true,"data"=>$Data);
            break;
            case 'sync-wallboard':
                $query = "SELECT `id` FROM `app_wallboards` WHERE `hash` = '" . $_POST['hash'] . "'";
                $sql = $daffny->DB->query($query);
                $data = mysqli_fetch_assoc($sql);

                $query = "SELECT `agent_id`,`agent_name` FROM `app_wallboard_agents` WHERE `wallboard_id` = '" . $data['id'] . "'";
                $assignedAgents = $daffny->DB->query($query);

                $agents = array();
                $agentIds = array();
                $agentnameAndId = array();

                $iterations = 0;
                while ($row = mysqli_fetch_assoc($assignedAgents)) {
                    $agents[] = $row;
                    $agentIds[] = $row['agent_id'];
                    $agentnameAndId[$iterations]['id'] = $row['agent_id'];
                    $agentnameAndId[$iterations]['name'] = $row['agent_name'];
                    $iterations++;
                }

                $date = date('Y/m/d');
                $ts = strtotime($date);
                $dow = date('w', $ts);
                $offset = $dow - 1;

                if ($offset < 0) {
                    $offset = 6;
                }

                $ts = $ts - $offset * 86400;

                $dates = array();
                for ($i = 0; $i < 7; $i++, $ts += 86400) {
                    $newdate = strtotime('-1 day', $ts);
                    $dates[] = date("Y-m-d", $newdate);
                }

                $updatedUI = "";
                $commaSeperatedDates = implode(",", $dates);
                $commaSeperatedAgents = implode(",", $agentIds);

                $sql = "CALL wallboardData('" . $commaSeperatedAgents . "','" . $commaSeperatedDates . "');";
                $result = $daffny->DB->query($sql);

                $agentCount = 1;
                $dateCount = 0;
                $weekCount = 1;
                $id = 0;
                $weektotal = 0;
                $weekCountTotal = 0;

                $dataArray = array();

                while ($row = mysqli_fetch_assoc($result)) {

                    if ($agentCount == 1) {
                        $dataArray[]['agent_id'] = $row['agent_id'];
                        $agentCount = 2;
                    }
                    if ($dateCount <= 6) {
                        $dataArray[$id]['deposit'][$dateCount]['amount'] = $row['deposite'];
                        $dataArray[$id]['deposit'][$dateCount]['count'] = $row['orderCount'];
                        $weektotal = $weektotal + $row['deposite'];
                        $weekCountTotal = $weekCountTotal + $row['orderCount'];
                        $dateCount++;
                        if ($dateCount == 7) {
                            $dataArray[$id]['weekTotal'] = $weektotal;
                            $dataArray[$id]['weekCountTotal'] = $weekCountTotal;
                            $weektotal = 0;
                            $weekCountTotal = 0;
                            $agentCount = 1;
                            $dateCount = 0;
                            $id++;
                        }
                    }
                }

                $grandTotal = 0;
                $grandCountTotal = 0;

                for ($j = 0; $j < count($dataArray); $j++) {

                    $updatedUI .= "<tr>";
                    $updatedUI .= "<td style='text-align:left;'>" . agentName($agentnameAndId, $dataArray[$j]['agent_id']) . "</td>";
                    for ($k = 0; $k < 7; $k++) {

                        $grandTotal = $grandTotal + $dataArray[$j]['deposit'][$k]['amount'];

                        if ($dataArray[$j]['deposit'][$k]['count'] == 0) {
                            $background = "style='background:#F3CCC4;text-align:right;'";
                        } else {
                            $background = "style='text-align:right;'";
                        }
                        $updatedUI .= "<td " . $background . ">" . $dataArray[$j]['deposit'][$k]['amount'] . " (" . $dataArray[$j]['deposit'][$k]['count'] . ")</td>";
                    }
                    $updatedUI .= "<td style='text-align:right;'>" . $dataArray[$j]['weekTotal'] . "(" . $dataArray[$j]['weekCountTotal'] . ")</td>";
                    $updatedUI .= "</tr>";
                    $grandCountTotal = $grandCountTotal + $dataArray[$j]['weekCountTotal'];

                }
                $updatedUI .= "<tr>"
                    . "<td colspan='8' style='text-align:right;'>Grand Total: </td>"
                    . "<td style='text-align:right;'><b>" . $grandTotal . " (" . $grandCountTotal . ")</b></td>"
                    . "</tr>";

                $out = array(
                    'success' => 'true',
                    'updatedUI' => $updatedUI,
                );
            break;
            case 'autoAuoteFromDetailLeads':
                $entity = $_POST['requested']['entity'];
                $sql = "SELECT `parentid`,`entityid`,`ship_via`,`vehicleid` FROM `app_order_header` WHERE entityid = (" . $entity . ")";
                $result = $daffny->DB->query($sql);

                $params;
                $i = 0;

                /**
                 * Initializing dependencies
                 */
                $entity = new Entity($daffny->DB);
                $destination = new Destination($daffny->DB);
                $origin = new Origin($daffny->DB);
                $shipper = new Shipper($daffny->DB);

                while ($row = mysqli_fetch_assoc($result)) {

                    $sql = "SELECT `order_deposit`,`order_deposit_type`,`auto_quote_api_pin`,`auto_quote_api_key` FROM `app_defaultsettings` WHERE `owner_id` = (" . $row['parentid'] . ")";
                    $settingsData = $daffny->DB->query($sql);
                    $settings = mysqli_fetch_assoc($settingsData);

                    $entity->load($row['entityid']);
                    $destination->load($entity->destination_id);
                    $origin->load($entity->origin_id);

                    $sql = "SELECT `id`,`year`,`make`,`model` FROM `app_vehicles` WHERE `entity_id` = (" . $row['entityid'] . ")";
                    $resultVehicles = $daffny->DB->query($sql);

                    $vehiclesData = [];
                    $j = 0;

                    while ($vehicles = mysqli_fetch_assoc($resultVehicles)) {
                        $vehiclesData[$j] = array(
                            'v_id' => $vehicles['id'],
                            'v_year' => $vehicles['year'],
                            'v_make' => $vehicles['make'],
                            'v_model' => $vehicles['model'],
                            'veh_op' => 1,
                        );
                        $j++;
                    }

                    if ($row['ship_via'] == 1) {
                        $carrier = 'Open';
                    } elseif ($row['ship_via'] == 2) {
                        $carrier = 'Close';
                    } else {
                        $carrier = 'Drive Away';
                    }

                    $params[$i]['Transport'] = array(
                        'Carrier' => $carrier,
                        'Origin' => array(
                            "City" => $origin->city,
                            "State" => $origin->state,
                            "Zipcode" => $origin->zip,
                        ),
                        'Destination' => array(
                            "City" => $destination->city,
                            "State" => $destination->state,
                            "Zipcode" => $destination->zip,
                        ),
                        'Vehicles' => $vehiclesData,
                    );
                    $params[$i]['Additional'] = array(
                        "order_deposit" => $settings['order_deposit'],
                        "order_deposit_type" => $settings['order_deposit_type'],
                        "auto_quote_api_pin" => $settings['auto_quote_api_pin'],
                        "auto_quote_api_key" => $settings['auto_quote_api_key'],
                        "entity_id" => $row['entityid'],
                    );
                    $i++;
                }

                $auotQuotes = new AutoQuotes();
                $curlStatus = $auotQuotes->checkCURLStatus();

                if ($curlStatus) {

                    /**
                     * prepare parameters array for sending to Auto quote API
                     */
                    $response = $auotQuotes->getAutoQuotesImportedLeads($params);
                    for ($i = 0; $i < count($response); $i++) {

                        $netTariff = 0;
                        $netCarrierPay = 0;
                        $netDeposit = 0;

                        for ($j = 0; $j < count($response[$i]); $j++) {

                            $netTariff += $response[$i][$j]['tariff'];
                            $netCarrierPay += $response[$i][$j]['carrirerPay'];
                            $netDeposit += $response[$i][$j]['deposit'];

                            $sql = "";
                            $sql = "UPDATE `app_vehicles` SET "
                                . "`tariff` = '" . $response[$i][$j]['tariff'] . "',"
                                . "`carrier_pay` = '" . $response[$i][$j]['carrirerPay'] . "',"
                                . "`deposit` = '" . $response[$i][$j]['deposit'] . "' WHERE `id` = '" . $response[$i][$j]['vehicle_id'] . "' " . "";
                            $resultVehicles = $daffny->DB->query($sql);
                        }

                        $sql = "";
                        $sql = "UPDATE `app_order_header` SET "
                        . "carrier_pay_stored='" . $netCarrierPay . "',"
                        . "total_tariff_stored='" . $netTariff . "',"
                        . "status='21',"
                        . "`quoted` = '" . date('Y/m/d h:i:s') . "' WHERE "
                            . "entityid = '" . $response[$i]['enitity_id'] . "'";
                        $updateQuotedDate = $daffny->DB->query($sql);

                        $sql = "";
                        $sql = "UPDATE `app_entities` SET `status`=21, `quoted` = '" . date('Y/m/d h:i:s') . "' WHERE id = '" . $response[$i]['enitity_id'] . "' ";
                        $updateQuotedDateEntities = $daffny->DB->query($sql);

                        /**
                         * Send Email
                         */
                        $entity->sendInitialQuote();
                    }

                    $out = array(
                        'success' => 'true',
                    );

                } else {

                    /**
                     * when curl extension is not enabled
                     */
                    $out = array(
                        'success' => 'false',
                        'response' => array(
                            'message' => 'Curl not enabled',
                        ),
                    );
                }
            break;
            case 'getAutoQuotingParameters':
                ini_set('max_execution_time', 300);
                $commaSeperatedEntity = implode(",", $_POST['requested']['entity']);
                $sql = "SELECT `parentid`,`entityid`,`origin_id`,`destination_id`,`ship_via`,`vehicleid` FROM `app_order_header` WHERE entityid IN (" . $commaSeperatedEntity . ")";
                $result = $daffny->DB->query($sql);

                $params;
                $i = 0;

                /**
                 * Initializing dependencies
                 */

                $entity = new Entity($daffny->DB);
                $destination = new Destination($daffny->DB);
                $origin = new Origin($daffny->DB);
                $shipper = new Shipper($daffny->DB);

                $on_status = false;

                while ($row = mysqli_fetch_assoc($result)) {

                    $sql = "SELECT `order_deposit`,`order_deposit_type`,`auto_quote_api_pin`,`auto_quote_api_key`,`on_off_auto_quoting` FROM `app_defaultsettings` WHERE `owner_id` = (" . $row['parentid'] . ")";
                    $settingsData = $daffny->DB->query($sql);
                    $settings = mysqli_fetch_assoc($settingsData);

                    $on_status = $settings['on_off_auto_quoting'];
                    
                    try {
                        $entity->load($row['entityid']);
                    } catch (Exception $e) {
                        file_put_contents('auto-quoting-log.txt', $row['entityid'] . PHP_EOL, FILE_APPEND | LOCK_EX);
                        continue;
                    }

                    $destination->load($entity->destination_id);
                    $origin->load($entity->origin_id);

                    $sql = "SELECT `id`,`year`,`make`,`model` FROM `app_vehicles` WHERE `entity_id` = (" . $row['entityid'] . ")";
                    $resultVehicles = $daffny->DB->query($sql);

                    $vehiclesData = [];
                    $j = 0;

                    while ($vehicles = mysqli_fetch_assoc($resultVehicles)) {
                        $vehiclesData[$j] = array(
                            'v_id' => $vehicles['id'],
                            'v_year' => $vehicles['year'],
                            'v_make' => $vehicles['make'],
                            'v_model' => $vehicles['model'],
                            'veh_op' => 1,
                        );
                        $j++;
                    }

                    if ($row['ship_via'] == 1) {
                        $carrier = 'Open';
                    } elseif ($row['ship_via'] == 2) {
                        $carrier = 'Close';
                    } else {
                        $carrier = 'Drive Away';
                    }

                    $params[$i]['Transport'] = array(
                        'Carrier' => $carrier,
                        'Origin' => array(
                            "City" => $origin->city,
                            "State" => $origin->state,
                            "Zipcode" => $origin->zip,
                        ),
                        'Destination' => array(
                            "City" => $destination->city,
                            "State" => $destination->state,
                            "Zipcode" => $destination->zip,
                        ),
                        'Vehicles' => $vehiclesData,
                    );
                    $params[$i]['Additional'] = array(
                        "order_deposit" => $settings['order_deposit'],
                        "order_deposit_type" => $settings['order_deposit_type'],
                        "auto_quote_api_pin" => $settings['auto_quote_api_pin'],
                        "auto_quote_api_key" => $settings['auto_quote_api_key'],
                        "entity_id" => $row['entityid'],
                    );
                    $i++;
                }

                //if($on_status){
                    $auotQuotes = new AutoQuotes();
                    $curlStatus = $auotQuotes->checkCURLStatus();

                    if ($curlStatus) {

                        /**
                         * prepare parameters array for sending to Auto quote API
                         */
                        $response = $auotQuotes->getAutoQuotesImportedLeads($params);
                        for ($i = 0; $i < count($response); $i++) {

                            $netTariff = 0;
                            $netCarrierPay = 0;
                            $netDeposit = 0;

                            for ($j = 0; $j < count($response[$i]); $j++) {

                                $netTariff += $response[$i][$j]['tariff'];
                                $netCarrierPay += $response[$i][$j]['carrirerPay'];
                                $netDeposit += $response[$i][$j]['deposit'];

                                $sql = "";
                                $sql = "UPDATE `app_vehicles` SET "
                                    . "`tariff` = '" . $response[$i][$j]['tariff'] . "',"
                                    . "`carrier_pay` = '" . $response[$i][$j]['carrirerPay'] . "',"
                                    . "`deposit` = '" . $response[$i][$j]['deposit'] . "' WHERE `id` = '" . $response[$i][$j]['vehicle_id'] . "' " . "";
                                $resultVehicles = $daffny->DB->query($sql);
                            }

                            $sql = "";
                            $sql = "UPDATE `app_order_header` SET "
                            . "carrier_pay_stored='" . $netCarrierPay . "',"
                            . "total_tariff_stored='" . $netTariff . "',"
                            . "type=2,"
                            . "`quoted` = '" . date('Y-m-d h:i:s') . "' WHERE "
                                . "entityid = '" . $response[$i]['enitity_id'] . "'";
                            $updateQuotedDate = $daffny->DB->query($sql);

                            $sql = "";
                            $sql = "UPDATE `app_entities` SET type=2,`quoted` = '" . date('Y-m-d h:i:s') . "' WHERE id = '" . $response[$i]['enitity_id'] . "' ";
                            $updateQuotedDateEntities = $daffny->DB->query($sql);

                            /**
                             * Send Email
                             */
                            if ($updateQuotedDate && $updateQuotedDateEntities) {
                                //$entity->sendInitialQuote();
                                $sql = "INSERT INTO `app_auto_quoting_mails` (`entity_id`) VALUES('" . $response[$i]['enitity_id'] . "')";
                                $daffny->DB->query($sql);
                            } else {
                                file_put_contents('Auto-quoting-log.txt', '#- ' . date('Y/m/d h:i:s') . " Entity or Order header not updated for Entity ID: " . print_r($$response[$i]['enitity_id'], true) . PHP_EOL, FILE_APPEND | LOCK_EX);
                            }

                        }
                        $out = array(
                            'success' => 'true',
                            //'not_available'=>$notInEntities
                        );

                    } else {

                        /**
                         * when curl extension is not enabled
                         */
                        $out = array(
                            'success' => 'false',
                            'response' => array(
                                'message' => 'Curl not enabled',
                            ),
                        );
                    }
                // } else {
                //     $out = array(
                //         'success' => 'false',
                //         'response' => array(
                //             'message' => 'Autoquoting not enabled',
                //         ),
                //     );
                // }
            break;
            case 'getOrderDeposite':
                $sql = "SELECT `auto_quote_api_pin`,`auto_quote_api_key`,`order_deposit`,`order_deposit_type` FROM `app_defaultsettings` WHERE owner_id ='" . $_POST['owner_id'] . "'";
                $result = $daffny->DB->query($sql);
                $row = mysqli_fetch_assoc($result);
                $out = array(
                    'success' => true,
                    'response' => $row,
                );
            break;
            case 'shiiperInfo':
                $colorCode = array(0 => 'black',
                    1 => 'green',
                    2 => 'red',
                );
                if ($_POST['shipperID'] == 'null') {
                    $out = array('success' => false);
                    break;
                }

                if (isset($_POST['orderBy'])) {
                    $orderBy = $_POST['orderBy'];
                }

                if (isset($_POST['columnName'])) {
                    $columnName = $_POST['columnName'];
                }

                $orderByColumn = "ORDER BY " . $columnName . " " . $orderBy;

                $data['shipperInfo'] = $daffny->DB->selectRows(' id,first_name,last_name,company_name,email,phone1,referred_by', 'app_accounts', "WHERE `id` = '" . $_POST['shipperID'] . "'");
                $data['orderData'] = $daffny->DB->selectRows(' *', 'app_order_header', "WHERE `account_id` = '" . $_POST['shipperID'] . "' " . $orderByColumn . " ");
                foreach ($data['orderData'] as $index => $id) {
                    /*****************************************************************/
                    try {
                        $date_type_string = array(1 => "Estimated", 2 => "Exactly", 3 => "Not Earlier Than", 4 => "Not Later Than");
                        $Date1 = "";
                        $Date2 = "";
                        if ($id['status'] == 4 || $id['status'] == 1) {
                            if (strtotime($id['avail_pickup_date']) > 0) {
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($id['avail_pickup_date'])) . "<br>";
                            }
                            if (strtotime($id['posted']) > 0) {
                                $Date2 = "<b>Posted:</b><br>" . date("m/d/y", strtotime($id['posted']));
                            }
                        } elseif ($id['status'] == 3) {

                            if (strtotime($id['avail_pickup_date']) > 0) {
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($id['avail_pickup_date']));
                            }

                            if ($id['archived'] != "") {
                                $Date2 = "<b>Cancelled:</b><br>" . date("m/d/y", strtotime($id['archived']));
                            }

                        } elseif ($id['status'] == 7 || $id['status'] == 9) {
                            if (strtotime($id['load_date']) == 0) {
                                $abbr = "N/A";
                            } else {
                                $abbr = $id['load_date_type'] > 0 ? $date_type_string[(int) $id['load_date_type']] :
                                "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($id['load_date']));
                            }

                            if (strtotime($id['delivery_date']) == 0) {
                                $abbr = "N/A";
                            } else {
                                $abbr = $id['delivery_date_type'] > 0 ? $date_type_string[(int) $id['delivery_date_type']] :
                                "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($id['delivery_date']));
                            }
                        } elseif ($id['status'] == 5 || $id['status'] == 6) {
                            if (strtotime($id['load_date']) == 0) {
                                $abbr = "N/A";
                            } else {
                                $abbr = $id['load_date_type'] > 0 ? $date_type_string[(int) $id['load_date_type']] :
                                "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($id['load_date']));
                            }

                            if (strtotime($id['delivery_date']) == 0) {
                                $abbr = "N/A";
                            } else {
                                $abbr = $id['delivery_date_type'] > 0 ? $date_type_string[(int) $id['delivery_date_type']] :
                                "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($id['delivery_date']));
                            }
                        } elseif ($id['status'] == 8) {
                            if (strtotime($id['actual_pickup_date']) > 0) {
                                $Date1 = "<b>Pickup:</b><br>" . date("m/d/y", strtotime($id['actual_pickup_date']));
                            }

                            if (strtotime($id['delivery_date']) == 0) {
                                $abbr = "N/A";
                            } else {
                                $abbr = $id['delivery_date_type'] > 0 ? $date_type_string[(int) $id['delivery_date_type']] : "";
                                $Date2 = $abbr . "<br />" . date("m/d/y", strtotime($id['delivery_date']));
                            }
                        } elseif ($id['status'] == 2) {
                            if (strtotime($id['avail_pickup_date']) > 0) {
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($id['avail_pickup_date']));
                            }

                            if ($id['hold_date'] != "") {
                                $Date2 = "<b>Hold:</b><br>" . date("m/d/y", strtotime($id['hold_date']));
                            }

                        } else {
                            $Date1 = "N/A";
                            $Date2 = "N/A";
                        }

                        $data['orderData'][$index]['avail_pickup_date'] = $Date1;
                        $data['orderData'][$index]['delivery_date'] = $Date2;

                        $entity = new Entity($daffny->DB);
                        $entity->load($id['entityid']);
                        $color = $entity->isPaidOffColor();
                        $id['ordered'] = $entity->getOrdered();
                    } catch (Exception $e) {

                    }
                    $data['orderData'][$index]['colorValue']['total'] = $colorCode[$color['total']];
                    $data['orderData'][$index]['colorValue']['deposit'] = $colorCode[$color['deposit']];
                    $data['orderData'][$index]['colorValue']['carrier'] = $colorCode[$color['carrier']];
                    if ($id['esigned'] == 2) {
                        $sql = "SELECT u.id,u.type,u.name_original FROM app_entity_uploads au LEFT JOIN app_uploads u ON au.upload_id = u.id WHERE "
                            . "au.entity_id = '" . $id['entityid'] . "' AND `name_original` LIKE  'B2B%' ORDER BY u.date_uploaded Desc limit 0,1";
                        $files = $daffny->DB->selectRows($sql);
                        if (count($files) > 0) {
                            $data['orderData'][$index]['uploadId'] = $files[0]['id'];
                            $data['orderData'][$index]['docType'] = "B2B";
                        }
                    } else {

                        if ($id['esigned'] == 1) {
                            $sql = "SELECT u.id,u.type,u.name_original
                                    FROM app_entity_uploads au
                                    LEFT JOIN app_uploads u ON au.upload_id = u.id
                                    WHERE au.entity_id = '" . $id['entityid'] . "'
                                    AND u.owner_id = '" . getParentId() . "'
                                    AND `name_original` LIKE  'Signed%'
                                    ORDER BY u.date_uploaded Desc limit 0,1";
                            $files = $daffny->DB->selectRows($sql);
                            if (count($files) > 0) {
                                $data['orderData'][$index]['uploadId'] = $files[0]['id'];
                                $data['orderData'][$index]['docType'] = "eSigned";
                            }
                        } else {
                            $data['orderData'][$index]['uploadId'] = 0;
                            $data['orderData'][$index]['docType'] = "NOTHING";
                        }
                    }
                    $data['orderData'][$index]['baseUrl'] = $_SERVER['HTTP_ORIGIN'];
                }
                $out = array('success' => true, 'shipperInfo' => $data['shipperInfo'], 'orderData' => $data['orderData']);
            break;
            case 'search':
                $accountManager = new AccountManager($daffny->DB);
                $accounts = $accountManager->searchAccount($_POST['text'], getParentId(), $_POST['type']);
                $data = array();
                $curr_date = date("m/d/y");
                foreach ($accounts as $account) {
                    $show_ins_doc = 0;
                    $bgcolor = '';
                    if ($account->insurance_doc_id > 0) {
                        $diff = strtotime($account->insurance_expirationdate) - strtotime($curr_date);
                        $date_diff = floor($diff / (60 * 60 * 24));
                        if ($date_diff <= 30 && $date_diff >= 0) {
                            $bgcolor = '#F0FF1A';
                        } elseif ($date_diff < 0) {
                            $bgcolor = '#FF1A24';
                        }

                        $member_check = $daffny->DB->selectRows('`id`', 'members', "WHERE `parent_id` = '" . getParentId() . "' and id=" . $account->owner_id);
                        if (count($member_check) > 0) {
                            $show_ins_doc = 1;
                        }
                    }
                    $insurance_expirationdate = '--';
                    if ($account->insurance_expirationdate != '') {
                        $insurance_expirationdate = date("m/d/y", strtotime($account->insurance_expirationdate));
                    }

                    $data[] = array(
                        'id' => $account->id,
                        'member_id' => $account->member_id,
                        'company_name' => $account->company_name,
                        'carrier_type' => $account->carrier_type,
                        'contact_name1' => $account->contact_name1,
                        'contact_name2' => $account->contact_name2,
                        'first_name' => $account->first_name,
                        'last_name' => $account->last_name,
                        'phone1' => formatPhone($account->phone1),
                        'phone2' => formatPhone($account->phone2),
                        'cell' => formatPhone($account->cell),
                        'fax' => $account->fax,
                        'email' => $account->email,
                        'address1' => $account->address1,
                        'address2' => $account->address2,
                        'city' => $account->city,
                        'state' => ($account->country == "US") ? $account->state : $account->state_other,
                        'zip' => $account->zip_code,
                        'country' => $account->country,
                        'print_name' => $account->print_name,
                        'insurance_iccmcnumber' => $account->insurance_iccmcnumber,
                        'shipper_type' => $account->shipper_type,
                        'location_type' => $account->location_type,
                        'hours_of_operation' => $account->hours_of_operation,
                        'referred_id' => $account->referred_id,
                        'referred_by' => $account->referred_by,
                        'donot_dispatch' => $account->donot_dispatch,
                        'expired' => !is_null($account->insurance_expirationdate) && (strtotime($account->insurance_expirationdate) < time()),
                        'account_payble_contact' => $account->account_payble_contact,
                        'insurance_expirationdate' => $insurance_expirationdate,
                        'insurance_doc_id' => ($show_ins_doc == 1) ? $account->insurance_doc_id : 0,
                        'insurance_type' => Account::$ins_tupe_name[$account->insurance_type],
                        'rowcolor' => $bgcolor,
                    );
                }
                if (empty($data) && $_POST['type'] == Account::TYPE_CARRIER) {
                    $sql = "SELECT m.`id`
                            FROM members m
                            JOIN `app_company_profile` cp ON cp.`owner_id` = m.`id`
                            WHERE cp.`companyname` LIKE '%" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['text']) . "%'
                            AND m.`parent_id` = m.`id`
                            AND cp.`is_carrier` = 1";
                    $result = $daffny->DB->query($sql);
                    if ($result) {
                        while ($row = $daffny->DB->fetch_row($result)) {

                            $bgcolor = '';
                            $diff = strtotime($account->insurance_expirationdate) - strtotime($curr_date);
                            $date_diff = floor($diff / (60 * 60 * 24));
                            if ($date_diff <= 30 && $date_diff >= 0) {
                                $bgcolor = '#F0FF1A';
                            } elseif ($date_diff < 0) {
                                $bgcolor = '#FF1A24';
                            }

                            $member = new Member($daffny->DB);
                            $member->load($row['id']);
                            $company = $member->getCompanyProfile();

                            $insurance_expirationdate = '--';
                            if ($account->insurance_expirationdate != '') {
                                $insurance_expirationdate = date("m/d/y", strtotime($account->insurance_expirationdate));
                            }

                            $data[] = array(
                                'id' => null,
                                'member_id' => $member->id,
                                'company_name' => $member->companyname,
                                'carrier_type' => null,
                                'contact_name1' => $company->contactname,
                                'contact_name2' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone1' => $company->dispatch_phone,
                                'phone2' => formatPhone($company->phone),
                                'cell' => formatPhone($company->phone_cell),
                                'fax' => $company->dispatch_fax,
                                'email' => $company->dispatch_email,
                                'address1' => trim($company->address1),
                                'address2' => trim($company->address2),
                                'city' => $company->city,
                                'state' => $company->state,
                                'zip' => $company->zip_code,
                                'country' => $company->country,
                                'print_name' => null,
                                'insurance_iccmcnumber' => $company->icc_mc_number,
                                'insurance_expirationdate' => $insurance_expirationdate,
                                'insurance_doc_id' => $account->insurance_doc_id,
                                'insurance_type' => Account::$ins_tupe_name[$account->insurance_type],
                                'rowcolor' => $bgcolor,
                            );
                        }
                    }
                }

                $out = array('success' => true, 'data' => $data);
            break;
            case 'searchCarrier':

                try{

                    $filter = [
                        'companyname' => 'company_name',
                        'mc_number' => 'insurance_iccmcnumber',
                        'email' => 'email',
                        'phone1' => 'phone1',
                        'phone2' => 'phone2',
                        'address1' => 'address1',
                    ];

                    $accountManager = new AccountManager($daffny->DB);
                    $accounts = $accountManager->searchCarrier($_POST['text'],$filter[$_POST['filter']] , $_SESSION['member']['id'], getParentId());
                    
                    $data = [];
                    $curr_date = date("m/d/y");
                    
                    foreach ($accounts['myCarriers'] as $account) {
                        $show_ins_doc = 0;
                        $bgcolor = '';
                        if ($account->insurance_doc_id > 0) {
                            $diff = strtotime($account->insurance_expirationdate) - strtotime($curr_date);
                            $date_diff = floor($diff / (60 * 60 * 24));
                            if ($date_diff <= 30 && $date_diff >= 0) {
                                $bgcolor = '#F0FF1A';
                            } elseif ($date_diff < 0) {
                                $bgcolor = '#FF1A24';
                            }
    
                            $member_check = $daffny->DB->selectRows('`id`', 'members', "WHERE `parent_id` = '" . getParentId() . "' and id=" . $account->owner_id);
                            if (count($member_check) > 0) {
                                $show_ins_doc = 1;
                            }
                        }
                        $insurance_expirationdate = '--';
                        if ($account->insurance_expirationdate != '') {
                            $insurance_expirationdate = date("m/d/y", strtotime($account->insurance_expirationdate));
                        }
    
                        $data['myCarriers'][] = array(
                            'id' => $account->id,
                            'member_id' => $account->member_id,
                            'company_name' => $account->company_name,
                            'carrier_type' => $account->carrier_type,
                            'contact_name1' => $account->contact_name1,
                            'contact_name2' => $account->contact_name2,
                            'first_name' => $account->first_name,
                            'last_name' => $account->last_name,
                            'phone1' => formatPhone($account->phone1),
                            'phone2' => formatPhone($account->phone2),
                            'cell' => formatPhone($account->cell),
                            'fax' => $account->fax,
                            'email' => $account->email,
                            'address1' => $account->address1,
                            'address2' => $account->address2,
                            'city' => $account->city,
                            'state' => ($account->country == "US") ? $account->state : $account->state_other,
                            'zip' => $account->zip_code,
                            'country' => $account->country,
                            'print_name' => $account->print_name,
                            'insurance_iccmcnumber' => $account->insurance_iccmcnumber,
                            'shipper_type' => $account->shipper_type,
                            'location_type' => $account->location_type,
                            'hours_of_operation' => $account->hours_of_operation,
                            'referred_id' => $account->referred_id,
                            'referred_by' => $account->referred_by,
                            'donot_dispatch' => $account->donot_dispatch,
                            'expired' => !is_null($account->insurance_expirationdate) && (strtotime($account->insurance_expirationdate) < time()),
                            'account_payble_contact' => $account->account_payble_contact,
                            'insurance_expirationdate' => $insurance_expirationdate,
                            'insurance_doc_id' => ($show_ins_doc == 1) ? $account->insurance_doc_id : 0,
                            'insurance_type' => Account::$ins_tupe_name[$account->insurance_type],
                            'rowcolor' => $bgcolor,
                        );
                    }

                    foreach ($accounts['otherCarriers'] as $account) {
                        $show_ins_doc = 0;
                        $bgcolor = '';
                        if ($account->insurance_doc_id > 0) {
                            $diff = strtotime($account->insurance_expirationdate) - strtotime($curr_date);
                            $date_diff = floor($diff / (60 * 60 * 24));
                            if ($date_diff <= 30 && $date_diff >= 0) {
                                $bgcolor = '#F0FF1A';
                            } elseif ($date_diff < 0) {
                                $bgcolor = '#FF1A24';
                            }
    
                            $member_check = $daffny->DB->selectRows('`id`', 'members', "WHERE `parent_id` = '" . getParentId() . "' and id=" . $account->owner_id);
                            if (count($member_check) > 0) {
                                $show_ins_doc = 1;
                            }
                        }
                        $insurance_expirationdate = '--';
                        if ($account->insurance_expirationdate != '') {
                            $insurance_expirationdate = date("m/d/y", strtotime($account->insurance_expirationdate));
                        }
    
                        $data['otherCarriers'][] = array(
                            'id' => $account->id,
                            'member_id' => $account->member_id,
                            'company_name' => $account->company_name,
                            'carrier_type' => $account->carrier_type,
                            'contact_name1' => $account->contact_name1,
                            'contact_name2' => $account->contact_name2,
                            'first_name' => $account->first_name,
                            'last_name' => $account->last_name,
                            'phone1' => formatPhone($account->phone1),
                            'phone2' => formatPhone($account->phone2),
                            'cell' => formatPhone($account->cell),
                            'fax' => $account->fax,
                            'email' => $account->email,
                            'address1' => $account->address1,
                            'address2' => $account->address2,
                            'city' => $account->city,
                            'state' => ($account->country == "US") ? $account->state : $account->state_other,
                            'zip' => $account->zip_code,
                            'country' => $account->country,
                            'print_name' => $account->print_name,
                            'insurance_iccmcnumber' => $account->insurance_iccmcnumber,
                            'shipper_type' => $account->shipper_type,
                            'location_type' => $account->location_type,
                            'hours_of_operation' => $account->hours_of_operation,
                            'referred_id' => $account->referred_id,
                            'referred_by' => $account->referred_by,
                            'donot_dispatch' => $account->donot_dispatch,
                            'expired' => !is_null($account->insurance_expirationdate) && (strtotime($account->insurance_expirationdate) < time()),
                            'account_payble_contact' => $account->account_payble_contact,
                            'insurance_expirationdate' => $insurance_expirationdate,
                            'insurance_doc_id' => ($show_ins_doc == 1) ? $account->insurance_doc_id : 0,
                            'insurance_type' => Account::$ins_tupe_name[$account->insurance_type],
                            'rowcolor' => $bgcolor,
                        );
                    }

                    //$sql = "SELECT m.`id` FROM members m JOIN `app_company_profile` cp ON cp.`owner_id` = m.`id` WHERE (cp.`companyname` LIKE '%" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['text']) . "%' OR cp.`mc_number` LIKE '%" . mysqli_real_escape_string($daffny->DB->connection_id, $_POST['text']) . "%') AND m.`parent_id` = m.`id` AND cp.`is_carrier` = 1";
                    $sql = "SELECT m.`id` FROM members m JOIN `app_company_profile` cp ON cp.`owner_id` = m.`id` WHERE (cp.`".$_POST['filter']."` LIKE '%" . $_POST['text'] . "%') AND m.`parent_id` = m.`id` AND cp.`is_carrier` = 1";
                    $result = $daffny->DB->query($sql);

                    if ($result) {
                        while ($row = $daffny->DB->fetch_row($result)) {

                            $bgcolor = '';
                            $diff = strtotime($account->insurance_expirationdate) - strtotime($curr_date);
                            $date_diff = floor($diff / (60 * 60 * 24));

                            if ($date_diff <= 30 && $date_diff >= 0) {
                                $bgcolor = '#F0FF1A';
                            } elseif ($date_diff < 0) {
                                $bgcolor = '#FF1A24';
                            }

                            $member = new Member($daffny->DB);
                            $member->load($row['id']);
                            $company = $member->getCompanyProfile();

                            $insurance_expirationdate = '--';
                            if ($account->insurance_expirationdate != '') {
                                $insurance_expirationdate = date("m/d/y", strtotime($account->insurance_expirationdate));
                            }

                            $data['systemCarrier'][] = array(
                                'id' => $member->id,
                                'member_id' => $member->id,
                                'company_name' => $member->companyname,
                                'carrier_type' => null,
                                'contact_name1' => $company->contactname,
                                'contact_name2' => null,
                                'first_name' => null,
                                'last_name' => null,
                                'phone1' => $company->dispatch_phone,
                                'phone2' => formatPhone($company->phone),
                                'cell' => formatPhone($company->phone_cell),
                                'fax' => $company->dispatch_fax,
                                'email' => $company->dispatch_email,
                                'address1' => trim($company->address1),
                                'address2' => trim($company->address2),
                                'city' => $company->city,
                                'state' => $company->state,
                                'zip' => $company->zip_code,
                                'country' => $company->country,
                                'print_name' => null,
                                'insurance_iccmcnumber' => $company->mc_number,
                                'insurance_expirationdate' => $insurance_expirationdate,
                                'insurance_doc_id' => $account->insurance_doc_id,
                                'insurance_type' => Account::$ins_tupe_name[$account->insurance_type],
                                'rowcolor' => $bgcolor,
                            );
                        }
                    }
                    $out = array('success' => true, 'data' => $data);
                } catch(Exception $e) {
                    $out = array('success' => false, 'data' => $e->getMessage());
                }
            break;
            case 'searchShipper':
                $show_all_option = true;
                if ($_SESSION["member"]["access_orders"] == 0) {
                    $show_all_option = false;
                }
                if ($show_all_option) {
                    $specificIds = $_SESSION['member']['specific_user_access'];
                    $specific_member = explode(",", $specificIds);
                }

                $accessType = "none";
                if ($show_all_option) {
                    $accessType = "all";
                    if ($specific_member[0] != "") {
                        $accessType = "specific";
                    }
                }

                $accountManager = new AccountManager($daffny->DB);
                ini_set('max_execution_time', 3000);
                $accounts = $accountManager->searchAccountShipper($_POST['text'], getParentId(), $_POST['type']);
                $shipperData = $accounts['shipperData'];
                $shipperOrderData = $accounts['shipperOrderData'];
                $shipperLeadsData = $accounts['shipperLeadsData'];

                $j = 0;

                foreach ($shipperData as $sD) {
                    $i = 0;
                    foreach ($shipperLeadsData as $sLD) {
                        if ($sLD['id'] == $sD['id']) {
                            $shipperLeadsData[$i]['fName'] = $sD['first_name'];
                            $shipperLeadsData[$i]['lName'] = $sD['last_name'];
                            $shipperLeadsData[$i]['company'] = $sD['company_name'];
                            $shipperLeadsData[$i]['state'] = $sD['state'];
                            $shipperLeadsData[$i]['country'] = $sD['country'];
                            $shipperLeadsData[$i]['email'] = $sD['email'];
                            $shipperLeadsData[$i]['referredBy'] = $sD['referred_by'];
                            $shipperLeadsData[$i]['zip_code'] = $sD['zip_code'];
                            $shipperLeadsData[$i]['phone1'] = $sD['phone1'];
                            $shipperLeadsData[$i]['phone2'] = $sD['phone2'];
                            $shipperLeadsData[$i]['cell'] = $sD['cell'];
                            $shipperLeadsData[$i]['fax'] = $sD['fax'];
                        }
                        $i++;
                    }

                    foreach ($shipperOrderData as $sOD) {
                        if ($sOD['id'] == $sD['id']) {
                            $shipperData[$j]['shipperId'] = $sOD['id'];
                            $shipperData[$j]['orderEntityId'] = $sOD['entityid'];
                            $shipperData[$j]['totalAmount'] = $sOD['totalAmount'];
                            $shipperData[$j]['pendingAmount'] = $sOD['pendingAmount'];
                            $shipperData[$j]['created'] = $sOD['created'];
                            $shipperData[$j]['prefix'] = $sOD['prefix'];
                            $shipperData[$j]['number'] = $sOD['number'];
                            $shipperData[$j]['assignedName'] = $sOD['assignedName'];
                            $shipperData[$j]['assignedDate'] = $sOD['assignedDate'];

                            if ($accessType == "all") {
                                $shipperData[$j]['accessOrder'] = "haveAccess";
                            } else {
                                if (in_array($sOD['assignedId'], $specific_member, true)) {
                                    $shipperData[$j]['accessOrder'] = "haveAccess";
                                } else {
                                    $shipperData[$j]['accessOrder'] = "dontHaveAccess";
                                }
                            }

                        }
                    }
                    $j++;
                }
                $data['shipper_data'] = $shipperData;
                $data['shipper_leads_data'] = $shipperLeadsData;
                $out = array('success' => true, 'data' => $data);
            break;
            case 'globalSearch':
                $cpm = new CompanyProfileManager($daffny->DB);
                $add = null;
                switch ($_POST['type']) {
                    case Account::TYPE_CARRIER:
                        $add = "`is_carrier` = 1";
                        break;
                }
                $data = array();
                $companies = $cpm->searchByName($_POST['company'], $add);
                foreach ($companies as $company) {
                    $data[] = array(
                        'member_id' => $company->owner_id,
                        'contact_name' => $company->contactname,
                        'company_name' => $company->companyname,
                        'company_address1' => trim($company->address1),
                        'company_address2' => trim($company->address2),
                        'company_city' => $company->city,
                        'company_state' => $company->state,
                        'company_zip' => $company->zip_code,
                        'company_country' => $company->country,
                        'company_email' => $company->dispatch_email,
                        'dispatch_phone' => $company->dispatch_phone,
                        'company_phone' => formatPhone($company->phone),
                        'company_cell' => formatPhone($company->phone_cell),
                        'company_fax' => $company->dispatch_fax,
                        'insurance_companyname' => $company->insurance_company,
                        'insurance_expirationdate' => substr($company->insurance_expdate, 0, 10),
                        'insurance_iccmcnumber' => $company->icc_mc_number,
                        'insurance_policynumber' => $company->insurance_policy_number,
                        'insurance_agentname' => $company->insurance_agent_name,
                        'insurance_agentphone' => $company->insurance_agent_phone,
                    );
                }
                $out = array('success' => true, 'data' => $data);
            break;
            case "deleteDuplicate":

                $account_assign_ids = $_POST['account_assign_ids'];
                $account_merge_id = $_POST['account_merge_id'];
                $type = $_POST['type'];

                foreach ($account_assign_ids as $key => $value) {
                    $daffny->DB->query("CALL fd_delete_account_and_update_orders(" . getParentId() . "," . $type . "," . $value . "," . $account_merge_id . ")");
                }

                $out = array('success' => true);
            break;
        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
//ob_clean();
echo $json->encode($out);
require_once "done.php";

function agentName($agents, $id)
{
    $agentname = "";
    for ($i = 0; $i < count($agents); $i++) {
        if ($agents[$i]['id'] == $id) {
            $agentname = $agents[$i]['name'];
        }
    }
    return $agentname;
}
