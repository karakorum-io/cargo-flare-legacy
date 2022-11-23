<?php

/**
 * CRONJOB Functionality to import leads after reading and parsing from email address
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

include_once("gl_functions.php");
include_once("gl_default_parser.php");
set_time_limit(270);
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");

//get Members
$mm = new MembersManager($daffny->DB);
$members = $mm->getMembers("`status` = 'Active' AND chmod <> 1 AND id = parent_id");
$lsm = new LeadsourceManager($daffny->DB);

foreach ($members as $m => $member) {
    $leadsources = $lsm->get(null, null, "WHERE `owner_id` = '" . (int) $member->id . "'");

    foreach ($leadsources as $l => $ls) {
        $source_id = $ls->id;
        $email = $ls->email_to;
        if (trim($email) == "") {
            continue;
        }
        
        printt($email);

        $mailbox = imap_open($daffny->cfg['MAILSTRING'], $email, $daffny->cfg['MAILPWD']);

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
                $source_id = $parsed['source'];
                
                //3. create Lead
                $lead_arr = array(
                    'type' => Entity::TYPE_LEAD,
                    'parentid' => $member->id,
                    'creator_id' => $member->id,
                    'created' => date("Y-m-d H:i:s"),
                    'received' => date("Y-m-d H:i:s"),
                    'source_id' => (!is_null($source_id) ? $source_id : NULL),
                    'assigned_id' => $member->id,
                    'est_ship_date' => date("Y-m-d"), //Estimated Date
                    'ship_via' => ($parsed['ship_via'] == 2 ? 2 : 1), //1-Open, 2-Enclosed, 3-Driveaway
                    'referred_by' => NULL,
                );

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
                    if ($member->id == 159){
                        $upd_arr['status'] = Entity::STATUS_LQUOTED;
                    } else {
                        $upd_arr['status'] = Entity::STATUS_ACTIVE;
                    }
                    echo "STATUS: OK " . "<br />";                    
                } else {
                    echo "STATUS: UNREADABLE " . $entity->id . "<br />";
                }
                
                $entity->update($upd_arr);
                $entity->checkDuplicate('');

                /************Get Assigned ************ */
                $Assigned_id = $member->id;
                $qCheck = $daffny->DB->selectRows(
                        "*",
                        "app_defaultsettings_ass",
                        "WHERE owner_id = '" . $member->id . "' and leadsource_id='" . $ls->id . "' limit 0,1"
                );

                if (!empty($qCheck)) {                    
                    $qCheckReinitiate = $daffny->DB->selectRows(
                            "*",
                            "app_defaultsettings_ass", 
                            "WHERE owner_id = '" . $member->id . "' and leadsource_id='" . $ls->id . "' and status=0 order by ord limit 0,1"
                    );
                    
                    if (empty($qCheckReinitiate)) {                        
                        print "<br>- Reinitiate------<br>";
                        $batch_count = 0;
                        $arr = array("batch_count" => $batch_count);
                        $daffny->DB->update("app_leadsources_queue", $arr, "  leadsource_id='" . $ls->id . "' ");
                        $status = 0;
                        $arrLeadSource = array("status" => 0);
                        $daffny->DB->update("app_defaultsettings_ass", $arrLeadSource, "leadsource_id='" . $ls->id . "' ");
                    }
                } else {
                    $Assigned_id = $member->id;
                }
                
                $q = $daffny->DB->selectRows(
                        "*",
                        "app_defaultsettings_ass",
                        "WHERE owner_id = '" . $member->id . "' and leadsource_id='" . $ls->id . "' and status=0 order by ord limit 0,1"
                );

                if (!empty($q)) {
                    
                    foreach ($q as $row) {
                        
                        $StatusLeadSource = $row['status'];
                        $BatchLeadSource = $row['batch'];
                        
                        $rows = $daffny->DB->selectRows(
                                '*',
                                " app_leadsources_queue ",
                                "WHERE member_id='" . $row['member_id'] . "' and leadsource_id='" . $ls->id . "' "
                        );
                        
                        if (!empty($rows)) {
                            
                            foreach ($rows as $rowQueue) {
                                
                                print "<br> ---assigned to : " . $rowQueue['member_id'] . "<br>";
                                
                                if (!$entity->duplicate) {
                                    $batch_count = $rowQueue['batch_count'] + 1;
                                    $arr = array("batch_count" => $batch_count);
                                    $daffny->DB->update("app_leadsources_queue", $arr, " member_id='" . $row['member_id'] . "' and leadsource_id='" . $ls->id . "' ");
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
                                        "member_id='" . $row['member_id'] . "' and leadsource_id='" . $ls->id . "' "
                                );
                                
                                $Assigned_id = $row['member_id'];
                            }
                        } else {
                            
                            $batch_count = 1;
                            
                            print "<br>- assigned to : " . $row['member_id'] . "<br>";
                            
                            $arr = array("leadsource_id" => $ls->id
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
                                    "member_id='" . $row['member_id'] . "' and leadsource_id='" . $ls->id . "' "
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

                /**
                 * Transaction Over Here
                 */
                $daffny->DB->query("COMMIT");

                if ($member->id == 159) {
                    // TCP port to connect to			
                    $From = 'noreply@americancartransporters.com'; // Please add a from email address here
                    $To = $parsed['shipper_email'];  // Add a recipient
                    $Subject = 'Thank you for your recent quote request from American Car Transporters';
                    $Body = "<p>" . $parsed["first_name"] . ",</p>
		<p>Thank you for you interest in <strong>American Car Transporters</strong>!</p>
		<p>We show your departure date as&nbsp;" . date("m/d/Y", strtotime($parsed["moving_date"])) . " and your departure city " . $parsed["pickup_city"] . ", " . $parsed["pickup_state"] . " going to " . $parsed["delivery_city"] . ", " . $parsed["delivery_state"] . " Please give us a call at 877-238-4718 so we can quote and schedule your transport.</p>
		<ul>
                                                <li>
                                                    Personalized shipping experience by assigning a personal logistics coordinator to work with you from beginning to end.&nbsp;&nbsp;
                                                </li>
                                                <li>
                                                    Our experienced staff is highly trained and has a combined<strong> 25 years of knowledge and experience</strong> in the industry.&nbsp; 
                                                </li>
                                                <li>
                                                    We are committed to making your shipping experience a pleasant one.
                                                </li>
                                        </ul>
		<p>&nbsp;</p>
		<p><strong>Our prices are ALL-INCLUSIVE and cover full insurance, taxes, tolls, and door-to-door service with no hidden fees!</strong></p>
		<p><br /><strong>Get your free quote NOW 877-238-4718</span> and ask about the $100 gift card!&nbsp; </strong></p>
		<br/>";

                    $mailData = array(
                        'entity_id' => $entity->id,
                        'member_id' => $Assigned_id,
                        'fromAddress' => $From,
                        'toAddress' => $To,
                        'cc' => "",
                        'bcc' => "",
                        'subject' => $Subject,
                        'body' => $Body,
                        'type' => 8,
                        'sent' => 0
                    );
                    
                    $daffny->DB->insert('app_mail_sent', $mailData);

                    /**********************************************************/
                    if ($source_id == 88 || $source_id == 109 || $source_id == 110)  {
                        
                        $posting_url = "https://manage.callshaper.com/lead/post/0b466ddbe5ce3d9463fbfa8c20997908c9c91cab";
                        
                        $fields = array();
                        
                        $fields['DeliveryCity'] = isset($parsed['delivery_city']) ? $parsed['delivery_city'] : '';
                        $fields['DeliveryState'] = isset($parsed['delivery_state']) ? $parsed['delivery_state'] : '';
                        $fields['EmailAddress'] = isset($parsed['shipper_email']) ? $parsed['shipper_email'] : '';
                        $fields['FirstName'] = isset($parsed['first_name']) ? $parsed['first_name'] : '';
                        $fields['InquiryDate'] = date('Y-m-d');
                        $fields['LastName'] = isset($parsed['last_name']) ? $parsed['last_name'] : '';
                        $fields['PhoneNumber'] = isset($parsed['phone1']) ? preg_replace("/[^A-Za-z0-9 ]/", '', $parsed['phone1']) : '';
                        $fields['PickUpCity'] = isset($parsed['pickup_city']) ? $parsed['pickup_city'] : '';
                        $fields['PickUpState'] = isset($parsed['pickup_state']) ? $parsed['pickup_state'] : '';
                        $fields['PubID'] = "WEB";
                        $fields['ShipOnDate'] = isset($parsed['moving_date']) ? $parsed['moving_date'] : '';
                        $fields['SubID'] = "ACT";
                        $fields['VehicleMake'] = isset($parsed['vehicles'][0]['make']) ? $parsed['vehicles'][0]['make'] : '';
                        $fields['VehicleModel'] = isset($parsed['vehicles'][0]['model']) ? $parsed['vehicles'][0]['model'] : '';
                        $fields['VehicleYear'] = isset($parsed['vehicles'][0]['year']) ? $parsed['vehicles'][0]['year'] : '';
                        $fields['ExpressConsent'] = 1;
                        $fields['UTMCampaign'] = isset($parsed['utm_campaign']) ? $parsed['utm_campaign'] : '';
                        $fields['UTMContent'] = isset($parsed['utm_content']) ? $parsed['utm_content'] : '';
                        $fields['UTMMedium'] = isset($parsed['utm_medium']) ? $parsed['utm_medium'] : '';
                        $fields['UTMSource'] = isset($parsed['utm_source']) ? $parsed['utm_source'] : '';
                        $fields['UTMTerm'] = isset($parsed['utm_term']) ? $parsed['utm_term'] : '';

                        $fields_string = "";
                        foreach ($fields as $key => $value) {
                            $fields_string .= $key . '=' . $value . '&';
                        }
                        rtrim($fields_string, '&');

                        //open connection
                        $ch = curl_init();

                        //set the url, number of POST vars, POST data
                        curl_setopt($ch, CURLOPT_URL, $posting_url);
                        curl_setopt($ch, CURLOPT_POST, count($fields));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                        //execute post
                        //$result = "CallShaper: " . curl_exec($ch);
                        
                        //close connection
                        curl_close($ch);
                    }
                    /**********************************************************/
                }
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
                $daffny->DB->query("ROLLBACK");
            }
        }

        imap_expunge($mailbox);
        imap_close($mailbox);
    }
}
require_once("done.php");