<?php

/**
 * CRONJOB Functionality to import leads after reading and parsing from email address
 * 
 * @author Chetu Inc.
 * @version 1.0
 */


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("gl_functions.php");
include_once("gl_default_parser.php");
set_time_limit(270);
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once "../core/libs/autoQuotes-v2.php";

echo "cronjob started at : ".date('y-m-d h:i:s')."<br>";

//get Members
$mm = new MembersManager($daffny->DB);
$members = $mm->getMembers("`status` = 'Active' AND chmod <> 1 AND id = 463");

function getLeadSourceEmail($daffny,$leadSourceId) {
    // fetched assigned members
    $res = $daffny->DB->query("SELECT member_id FROM app_defaultsettings_ass WHERE leadsource_id = ".$leadSourceId);
    
    $members = [];
    while($r = mysqli_fetch_assoc($res)){
        $members[] = $r['member_id'];
    }

    // comma seperated members
    $members = implode(",", $members);

    $res = $daffny->DB->query("SELECT email,contactname FROM members WHERE id IN (".$members.")");
    $emails = [];

    $i = 0;
    while($r = mysqli_fetch_assoc($res)){
        $emails[$i]['mail'] = $r['email'];
        $emails[$i]['name'] = $r['contactname'];

        $i++;
    }

    return $emails;
}

function sendQuoteCreateNotification($mails, $assignedEmail, $leadData){

    foreach ($mails as $key => $m) {
        try {
            if(strtolower($assignedEmail[0]) == strtolower($m['mail'])){
                $mail = new FdMailer(true);
                $mail->isHTML();

                $vehicle = '
                            <table border="1">
                                <tr>
                                    <td>Year</td> <td>Make</td> <td>Model</td> <td>Type</td>
                                </tr>
                            ';
                
                foreach ($leadData['vehicles'] as $k => $v) {
                    
                    $vehicle .= '<tr>
                                    <td>'.$v['year'].'</td>
                                    <td>'.$v['make'].'</td>
                                    <td>'.$v['model'].'</td>
                                    <td>'.$v['type'].'</td>
                                </tr>';
                }

                $vehicle .= '</table>';

                $body = '
                    Lead ID: #'.$leadData['lead_id'].' <br/>
                    Contact Name: '.$leadData['contact_name'].'<br/>
                    Phone Number: '.$leadData['phone'].'<br/>
                    Email: '.$leadData['email'].' <br/>
                    Pickup: '.$leadData['pickup'].' <br/>
                    Dropoff: '.$leadData['dropoff'].' <br/>
                    Vehicle(s): '.$vehicle.' <br/>
                    Link: <a target="_blank" href="https://cargoflare.com/application/leads/showimported/id/'.$leadData['id'].'">Click Here</a>
                ';

                $mail->Body = $body;
                $mail->Subject = "New Lead";

                $mail->AddAddress($m['mail']);
                // $mail->AddAddress('shahrukhusmaani@live.com');
                $mail->SetFrom('noreply@transportmasters.net', 'CargoFlare');
                
                $mail->Send();

                echo "<br>Sent for :".$m['mail']."<br>";
            } else {
                echo "<br>Not sending for : ".$m['mail']."<br>";
            }
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
    }
}

function getMemberEmail($daffny, $memberId) {
    $res = $daffny->DB->query("SELECT email FROM members WHERE id = ".$memberId);
    
    $emails = [];
    while($r = mysqli_fetch_assoc($res)){
        $emails[] = $r['email'];
    }
    return $emails;
}

function autoQuoteLead($daffny, $leadId) {

    $sql = "SELECT `parentid`,`entityid`,`origin_id`,`destination_id`,`ship_via`,`vehicleid` FROM `app_order_header` WHERE entityid IN (" . $leadId . ")";
    $result = $daffny->DB->query($sql);
    $params = [];
    $i = 0;

    /**
     * Initializing dependencies
     */

    $entity = new Entity($daffny->DB);
    $destination = new Destination($daffny->DB);
    $origin = new Origin($daffny->DB);
    $shipper = new Shipper($daffny->DB);

    $on_status = false;
    $on_status_mail = false;
    $email_template_id = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $sql = "SELECT `order_deposit`,`order_deposit_type`,`auto_quote_api_pin`,`auto_quote_api_key`,`on_off_auto_quoting`,`on_off_auto_quoting_email`,`aq_email_template` FROM `app_defaultsettings` WHERE `owner_id` = (" . $row['parentid'] . ")";
        $settingsData = $daffny->DB->query($sql);
        $settings = mysqli_fetch_assoc($settingsData);

        $on_status = $settings['on_off_auto_quoting'];
        $on_status_mail = $settings['on_off_auto_quoting_email'];
        $email_template_id = $settings['aq_email_template'];

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

    if($on_status){
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
                . "`quoted` = '" . date('Y/m/d h:i:s') . "' WHERE "
                    . "entityid = '" . $response[$i]['enitity_id'] . "'";
                $updateQuotedDate = $daffny->DB->query($sql);

                $sql = "";
                $sql = "UPDATE `app_entities` SET  type=2,`quoted` = '" . date('Y/m/d h:i:s') . "' WHERE id = '" . $response[$i]['enitity_id'] . "' ";
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

            if($on_status_mail) {
                if($email_template_id){
                    
                    $sql = "SELECT `id`,`exclude_from_auto_quote` FROM `app_leadsources` WHERE `id` = 309";
                    $source = $daffny->DB->selectRows($sql);
                    if($source[0]['exclude_from_auto_quote']){
                        sendEmailToShipper($daffny, $leadId, $email_template_id);
                    }  
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
    }
}

function sendEmailToShipper($daffny, $lead_id, $email_template_id) {
    $entity = new Entity($daffny->DB);
    $entity->load((int) $lead_id);
    $entity->sendAQTemplate($email_template_id);
}

$MAils = getLeadSourceEmail($daffny,291);

$lsm = new LeadsourceManager($daffny->DB);

foreach ($members as $m => $member) {

    $assignedEmail = getMemberEmail($daffny, $member->id);

    $leadsources = $lsm->get(null, null, "WHERE `owner_id` = '" . (int) $member->id . "'");

    foreach ($leadsources as $l => $ls) {
        $source_id = 309;
        
        // if (trim($email) == "") {
        //     continue;
        // }
        
        // printt($email);

        $mailbox = imap_open($daffny->cfg['MAILSTRING'], 'transport_masters_usa_llc_309_463@cargoflare.com', 'Chetu2018?#Freightdragon');

        if (!$mailbox) {
            echo "Failed to open mail box";
            continue;
        }

        $check = imap_check($mailbox);
        $qty_emails = $check->Nmsgs;

        printt("Number of recent messages : " . $qty_emails . "<br />");

        for ($e = 1; $e <= $qty_emails; $e++) {
            try {
                
                /**
                 * Transaction Starts Here
                 */
                $daffny->DB->query("START TRANSACTION");
                
                echo "Get email #" . $e . "<br />";
                $header = imap_header($mailbox, $e);
                $body = imap_body($mailbox, $e);
                $subject = $header->subject;
                $to = $header->toaddress;
                $cc = $header->ccaddress;
                $bcc = $header->bccaddress;
                $from = $header->fromaddress;
                $body = imap_body($mailbox, $e);
                
                //2. Parse Body
                //Vehicles parse array
                $parsed = array();
                $save_body = $body;
				
                $parsed = default_parser($body, $daffny->DB);

                /**************Get Assigned End ************ */
                $is_good = true; // Unreadable flag
                //$source_id = $parsed['source'];
                $source_id = 309;
                
                //3. create Lead
                $lead_arr = array(
                    'type' => Entity::TYPE_LEAD,
                    'parentid' => 463,
                    'creator_id' => $member->id,
                    'created' => date("Y-m-d H:i:s"),
                    'received' => date("Y-m-d H:i:s"),
                    'source_id' => (!is_null($source_id) ? $source_id : NULL),
                    'assigned_id' => $member->id,
                    'est_ship_date' => date("Y-m-d"), //Estimated Date
                    'ship_via' => ($parsed['ship_via'] == 2 ? 2 : 1), //1-Open, 2-Enclosed, 3-Driveaway
                    'referred_by' => NULL,
                );

                echo "<pre>";
                print_r($lead_arr);
                echo "</pre>";

                $entity = new Entity($daffny->DB);
                $entity->create($lead_arr);

                //4. insert Original Email
                $oriemail = new LeadEmail($daffny->DB);

                $oriemail_arr = array(
                    'received' => date("Y-m-d H:i:s"),
                    'to_address' => $to,
                    'from_address' => $from,
                    'subject' => $subject,
                    'body' => $save_body,
                );

                $oriemail->create($oriemail_arr);

                //5. Create Shipper
                $shipper = new Shipper($daffny->DB);
                $shipper_arr = array(
                    'fname' => $parsed['first_name'],
                    'lname' => $parsed['last_name'],
                    'company' => "",
                    'email' => $parsed['shipper_email'],
                    'phone1' => str_replace("-", "", $parsed['phone1']),
                    'phone2' => str_replace("-", "", $parsed['phone2']),
                    'fax' => $parsed['fax'],
                    'mobile' => str_replace("-", "", $parsed['mobile']),
                    'address1' => $parsed['address'],
                    'address2' => $parsed['address2'],
                    'city' => $parsed['city'],
                    'state' => $parsed['state'],
                    'zip' => $parsed['zip'],
                    'country' => (trim($parsed['country']) == "" ? "US" : $parsed['country']),
                );

                $shipper->create($shipper_arr, $entity->id);

                if ($shipper_arr['fname'] == "") {
                    $is_good = false;
                    echo "fname<br />";
                }

                //6. Create Origin
                $origin = new Origin($daffny->DB);
                
                $origin_arr = array(
                    'city' => $parsed['pickup_city']
                    , 'state' => $parsed['pickup_state']
                    , 'zip' => $parsed['pickup_zip']
                    , 'country' => (trim($parsed['pickup_country']) == "" ? "US" : $parsed['pickup_country'])
                );

                $origin->create($origin_arr, $entity->id);

                if ($origin_arr['city'] == "" || $origin_arr['state'] == "") {
                    $is_good = false;
                    echo "city state<br />";
                }

                //7. Create Destination
                $destination = new Destination($daffny->DB);

                $destination_arr = array(
                    'city' => $parsed['delivery_city']
                    , 'state' => $parsed['delivery_state']
                    , 'zip' => $parsed['delivery_zip']
                    , 'country' => (trim($parsed['delivery_country']) == "" ? "US" : $parsed['delivery_country'])
                );

                $destination->create($destination_arr, $entity->id);

                if ($destination_arr['city'] == "" || $destination_arr['state'] == "") {
                    $is_good = false;
                    echo "dcity dstate<br />";
                }

                //8. Create Vehicles
                foreach ($parsed['vehicles'] as $k => $v) {
                    $vehicle = new Vehicle($daffny->DB);
                    
                    if ($v['run'] == "Yes") {
                        $v["state"] = "Operable";
                        $v['inop'] = 0;
                    }

                    if ($v['run'] == "No") {
                        $v["state"] = "Inoperable";
                        $v['inop'] = 1;
                    }
                    
                    $vehicle_arr = array(
                        'entity_id' => $entity->id,
                        'year' => $v['year'],
                        'make' => $v['make'],
                        'model' => $v['model'],
                        'type' => $v['type'],
                        'vin' => $v['vin'],
                        'lot' => $v['lot'],
                        'plate' => $v['plate'],
                        'color' => $v['color'],
                        'state' => $v['state'],
                        'inop' => $v['inop'],
                    );

                    $vehicle->create($vehicle_arr);
                }

                if (count($parsed['vehicles']) <= 0) {
                    $is_good = false;
                    echo "no vehicles<br />";
                }
                
                $distance = RouteHelper::getRouteDistance(
                        $origin->city . "," . $origin->state . "," . $origin->country, 
                        $destination->city . "," . $destination->state . "," . $destination->country
                );

                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                //9. Update Lead
                $upd_arr = array(
                    'email_id' => $oriemail->id,
                    'shipper_id' => $shipper->id,
                    'origin_id' => $origin->id,
                    'destination_id' => $destination->id,
                    'est_ship_date' => $parsed['moving_date'],
                    'distance' => $distance
                );

                if ($is_good) {                    
                    if ($member->id == 463){
                        $upd_arr['status'] = Entity::STATUS_ACTIVE;
                    } else {
                        $upd_arr['status'] = Entity::STATUS_ACTIVE;
                    }
                    echo "STATUS: OK " . "<br />";                    
                } else {
                    echo "STATUS: UNREADABLE " . $entity->id . "<br />";
                }
                
                $entity->update($upd_arr);
                //$entity->checkDuplicate('');

                /************Get Assigned ************ */
                $Assigned_id = $member->id;
                $qCheck = $daffny->DB->selectRows(
                        "*",
                        "app_defaultsettings_ass",
                        "WHERE owner_id = '" . $member->id . "' and leadsource_id='309' limit 0,1"
                );

                if (!empty($qCheck)) {                    
                    $qCheckReinitiate = $daffny->DB->selectRows(
                            "*",
                            "app_defaultsettings_ass", 
                            "WHERE owner_id = '" . $member->id . "' and leadsource_id='309' and status=0 order by ord limit 0,1"
                    );
                    
                    if (empty($qCheckReinitiate)) {                        
                        print "<br>- Reinitiate------<br>";
                        $batch_count = 0;
                        $arr = array("batch_count" => $batch_count);
                        $daffny->DB->update("app_leadsources_queue", $arr, "  leadsource_id='309' ");
                        $status = 0;
                        $arrLeadSource = array("status" => 0);
                        $daffny->DB->update("app_defaultsettings_ass", $arrLeadSource, "leadsource_id='309' ");
                    }
                } else {
                    $Assigned_id = $member->id;
                }
                
                $q = $daffny->DB->selectRows(
                        "*",
                        "app_defaultsettings_ass",
                        "WHERE owner_id = '" . $member->id . "' and leadsource_id='309' and status=0 order by ord limit 0,1"
                );

                if (!empty($q)) {
                    
                    foreach ($q as $row) {
                        
                        $StatusLeadSource = $row['status'];
                        $BatchLeadSource = $row['batch'];
                        
                        $rows = $daffny->DB->selectRows(
                                '*',
                                " app_leadsources_queue ",
                                "WHERE member_id='" . $row['member_id'] . "' and leadsource_id='309' "
                        );
                        
                        if (!empty($rows)) {
                            
                            foreach ($rows as $rowQueue) {
                                
                                print "<br> ---assigned to : " . $rowQueue['member_id'] . "<br>";
                                
                                if (!$entity->duplicate) {
                                    $batch_count = $rowQueue['batch_count'] + 1;
                                    $arr = array("batch_count" => $batch_count);
                                    $daffny->DB->update("app_leadsources_queue", $arr, " member_id='" . $row['member_id'] . "' and leadsource_id='309' ");
                                } else {
                                    $batch_count = $rowQueue['batch_count'];
                                }

                                $status = 0;
                                
                                print "" . $batch_count . "- BatchLeadSource: " . $BatchLeadSource . "<br>";
                                
                                if ($batch_count >= $BatchLeadSource){
                                    $status = 1;
                                }
                                
                                $arrLeadSource = array("status" => $status);
                                $daffny->DB->update(
                                        "app_defaultsettings_ass",
                                        $arrLeadSource,
                                        "member_id='" . $row['member_id'] . "' and leadsource_id='309' "
                                );
                                
                                $Assigned_id = $row['member_id'];
                            }
                        } else {
                            
                            $batch_count = 1;
                            
                            print "<br>- assigned to : " . $row['member_id'] . "<br>";
                            
                            $arr = array("leadsource_id" => 309
                                , "member_id" => (int) $row['member_id']
                                , "batch_count" => $batch_count
                            );

                            $daffny->DB->insert("app_leadsources_queue", $arr);

                            $status = 0;
                            print "BatchLeadSource: " . $BatchLeadSource . "<br>";
                            if ($BatchLeadSource <= 1){
                                $status = 1;
                            }
                            
                            $arrLeadSource = array("status" => $status);
                            $daffny->DB->update(
                                    "app_defaultsettings_ass",
                                    $arrLeadSource,
                                    "member_id='" . $row['member_id'] . "' and leadsource_id='309' "
                            );
                            
                            $Assigned_id = $row['member_id'];                            
                        }
                        
                        print "----------------------------------------<br><br>";
                    }
                }

                if ($entity->duplicate) {
                    
                    print "<br>- Duplicate------<br>";
                    $upd_arr_new = array(
                        'assigned_id' => $Assigned_id,
                        'status' => Entity::STATUS_LDUPLICATE
                    );
                    
                } else {                    
                    $upd_arr_new = array(
                        'assigned_id' => $Assigned_id
                    );                    
                }
                
                $entity->update($upd_arr_new);
                $entity->updateHeaderTable();
                
                //10. Delete email from server
                imap_delete($mailbox, $e);

                // auto quote here
                autoQuoteLead($daffny, $entity->id);

                /**
                 * Transaction Over Here
                 */
                $daffny->DB->query("COMMIT");

                $leadData = [
                    'id' => $entity->id,
                    'lead_id' => $entity->number,
                    'contact_name' => $parsed['first_name']."".$parsed['last_name'],
                    'phone' => $parsed['phone1'],
                    'email' => $parsed['shipper_email'],
                    'pickup' => $parsed['pickup_city'].", ".$parsed['pickup_state'].", ".$parsed['pickup_zip'],
                    'dropoff' => $parsed['delivery_city'].", ".$parsed['delivery_state'].", ".$parsed['delivery_zip'],
                    'vehicles' => $parsed['vehicles']
                ];

                sendQuoteCreateNotification($MAils,$assignedEmail, $leadData);

            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                $daffny->DB->query("ROLLBACK");
            }
        }

        imap_expunge($mailbox);
        imap_close($mailbox);
    }
}

echo "cronjob ended at : ".date('y-m-d h:i:s')."<br>";
require_once("done.php");
