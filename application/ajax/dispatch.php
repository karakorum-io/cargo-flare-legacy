<?php

/**
 * dispatch.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 * 
 * @author Shahrukh
 * @copyright CargoFlare
 */


 // loading dependencies
require_once("init.php");

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

ob_start();
if ($memberId > 0) {
    try {

        if (!isset($_POST['action']) && !isset($_GET['action'])){
            throw new FDException("Invalid action");
        }
            
        if (!isset($_POST['id']) && !isset($_GET['id'])) {
            throw new FDException("Invalid Dispatch ID");
        }
            
        $action = (isset($_POST['action'])) ? $_POST['action'] : $_GET['action'];

        switch ($action) {
            case 'accept':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);
                $dispatch->accept();
                $out = array("success" => true);
            break;
            case 'reject':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);
                $order = $dispatch->getOrder();
                $dispatch->reject();
                $dispatch_email = $order->getAssigned()->getCompanyProfile()->dispatch_email;
                if ($dispatch_email != "") {
                    $mail = new FdMailer(true);
                    $mail->isHTML();
                    $mail->Body = $dispatch->carrier_contact_name . ' has rejected order #' . $order->getNumber() . ' on ' . date("Y-m-d H:i:s") . '.';
                    $mail->Subject = 'Order #' . $order->getNumber() . ' has been rejected.';
                    $mail->AddAddress($dispatch_email, "Dispatch Department");
                    $mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                    if ($order->getAssigned()->parent_id == 1) {
                        $mail->setFrom('noreply@transportmasters.net');
                    } else {
                        $mail->setFrom($order->getAssigned()->getDefaultSettings()->smtp_from_email);
                    }
                    $mail->send();
                }
                $out = array("success" => true);
            break;
            case 'cancel':
                
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                    
                $update_arr = array(
                    'dispatched' => null,
                    'carrier_id' => null,
                    'not_signed' => null,
                );
                
                $entity->update($update_arr);
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);        
                $dispatch->cancel();

                $entity = $dispatch->getOrder();
                
                $note_array = array(
                    "entity_id" => $entity->id,
                    "sender_id" => $_SESSION['member_id'],
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 1,
                    "text" => "Order undispatched by " . $_SESSION['member']['contactname']
                );

                $note = new Note($daffny->DB);
                $note->create($note_array);

                $carrier_email = $dispatch->carrier_email;
                $carrier_contact_name = $dispatch->carrier_contact_name;
                if ($carrier_email != "") {
                    $mail = new FdMailer(true);
                    $mail->isHTML();
                    $mail->Body = 'Order #' . $entity->getNumber() . '  Undispatched.';
                    $mail->Subject = 'Order #' . $entity->getNumber() . ' Undispatched.';
                    $mail->AddAddress($carrier_email, $carrier_contact_name);
                    
                    $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                        
                    $mail->send();
                }
                sendDispatchMail($entity, $daffny, 4);
                $out = array("success" => true);
            break;
            case 'cancelNew':
                $entity = new Entity($daffny->DB);
                $entity->load($_POST['id']);
                $ds = $entity->getDispatchSheet();
                
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($ds->id);
                $dispatch->cancel();

                $note_array = array(
                    "entity_id" => $_POST['id'],
                    "sender_id" => $_SESSION['member_id'],
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 1,
                    "text" => "Order undispatched by " . $_SESSION['member']['contactname']
                );

                $note = new Note($daffny->DB);
                $note->create($note_array);

                $carrier_email = $dispatch->carrier_email;
                $carrier_contact_name = $dispatch->carrier_contact_name;

                $q = "UPDATE app_entities SET dispatched = NULL WHERE id = ".$entity->id;
                $resp = $daffny->DB->hardQuery($q);

                $q1 = "DELETE FROM wallboard WHERE entity_id =".$entity->id;
                $daffny->DB->hardQuery($q1);
                
                if($resp) {
                    if ($carrier_email != "") {
                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Body = 'Order #' . $entity->getNumber() . '  Undispatched.';
                        $mail->Subject = 'Order #' . $entity->getNumber() . ' Undispatched.';
                        $mail->AddAddress($carrier_email, $carrier_contact_name);
                        if ($entity->getAssigned()->parent_id == 1)
                            $mail->setFrom('noreply@transportmasters.net');
                        else
                            $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                        $mail->send();
                    }
                    sendDispatchMail($entity, $daffny, 4);
    
                    $entity->updateHeaderTable();
                    $out = array("success" => true);
                } else {
                    $out = array("success" => true, 'message' => 'something went wrong');
                }
                
            break;
            case 'getPDF':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_GET['id']);
                $dispatch->getPdf();
                exit;
            break;
            case 'getHtml':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);
                $out = array('success' => true, 'html' => $dispatch->getHtml($daffny->tpl));
            break;
            case 'editcarrier':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);

                $carrier_company_name = trim($_POST['carrier_company_name']);
                $carrier_contact_name = trim($_POST['carrier_contact_name']);
                $carrier_phone_1 = trim($_POST['carrier_phone_1']);
                $carrier_phone_2 = trim($_POST['carrier_phone_2']);
                $carrier_fax = trim($_POST['carrier_fax']);
                $carrier_email = trim($_POST['carrier_email']);
                $hours_of_operation = trim($_POST['hours_of_operation']);
                $carrier_driver_name = trim($_POST['carrier_driver_name']);
                $carrier_driver_phone = trim($_POST['carrier_driver_phone']);

                $update_arr = array(
                    'carrier_company_name' => $carrier_company_name,
                    'carrier_contact_name' => $carrier_contact_name,
                    'carrier_phone_1' => $carrier_phone_1,
                    'carrier_phone_2' => $carrier_phone_2,
                    'carrier_fax' => $carrier_fax,
                    'carrier_email' => $carrier_email,
                    'hours_of_operation' => $hours_of_operation,
                    'carrier_driver_name' => $carrier_driver_name,
                    'carrier_driver_phone' => $carrier_driver_phone
                );
                $dispatch->update($update_arr);

                $out = array('success' => true);
            break;
            case 'get':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);
                $entity = $dispatch->getOrder();
                $data = $dispatch->getAttributes();
                $vehicles = $dispatch->getVehicles();
                unset(
                        $data['entity_id'], $data['created'], $data['accepted'], $data['rejected'], $data['cancelled'], $data['signed'], $data['status'], $data['deleted']
                );
                $data['order_number'] = $entity->getNumber();
                $company = $entity->getAssigned()->getCompanyProfile();
                $data['entity_ship_via'] = Entity::$ship_via_string[$data['entity_ship_via']];
                $data['entity_load_date_type'] = Entity::$date_type_string[$data['entity_load_date_type']];
                $data['entity_delivery_date_type'] = Entity::$date_type_string[$data['entity_delivery_date_type']];
                $data['entity_load_date'] = $dispatch->getPickupDate("m/d/Y");
                $data['entity_delivery_date'] = $dispatch->getDeliveryDate("m/d/Y");
                $data['dispatch_date'] = $dispatch->getCreated("m/d/Y");
                $data['company_name'] = $company->companyname;
                $data['company_address'] = trim($company->address1 . " " . $company->address2);
                $data['company_city'] = $company->city;
                $data['company_state'] = $company->state;
                $data['company_zip'] = $company->zip_code;
                $data['company_country'] = $company->country;
                $data['company_phone'] = $company->phone;
                $data['company_contact'] = $company->contactname;
                $data['company_dispatch_phone'] = $company->dispatch_phone;
                $data['company_dispatch_fax'] = $company->dispatch_fax;
                foreach ($data as $k => $v) {
                    $data[$k] = htmlspecialchars($v);
                }
                $vehicles_data = array();
                foreach ($vehicles as $vehicle) {
                    $vehicles_data[] = $vehicle->getAttributes();
                }
                $out = array("success" => true, "data" => $data, "vehicles" => $vehicles_data);
            break;
            case 'history':
                $dispatch = new DispatchSheet($daffny->DB);
                $dispatch->load($_POST['id']);
                $entity = new Entity($daffny->DB);
                $entity->load($dispatch->entity_id);
                $realDispatch = $entity->getDispatchSheet();
                $changes = array();
                $attributes = $dispatch->getAttributes();
                foreach ($realDispatch->getAttributes() as $attr => $val) {
                    if ($attributes[$attr] != $val) {
                        $changes[] = $attr;
                    }
                }
                if ($dispatch->getVehiclesHtml() != $realDispatch->getVehiclesHtml()) {
                    $changes[] = "vehicles";
                }
                $out = array('success' => true, 'html' => $dispatch->getHtml($daffny->tpl), 'changes' => $changes);
            break;
            default:
                throw new RuntimeException("Invalid action");
            break;
        }
        
    } catch (FDException $e) {
        if ($daffny->DB->isTransaction) {
            $daffny->DB->transaction('rollback');
        }
        echo $daffny->DB->errorQuery;
        echo $e->getMessage();die;
    }
}

/**
 *  Function to send disoatch to central dispatch email
 * 
 *  @param Object $ref, Entity loaded class object
 *  @param integer $posttype Flag, 1: repost 2: unpost 3: dispatch
 */
function sendDispatchMail($ref, $daffny, $posttype) {

    $settings = $ref->getAssigned()->getDefaultSettings();
    $central_dispatch_uid = $settings->central_dispatch_uid;
    $central_dispatch_post = $settings->central_dispatch_post;
    $email_from = $ref->getAssigned()->getCompanyProfile()->email;

    $vehicles = $ref->getVehicles(); //for calculate carrier pay

    if ($central_dispatch_uid != "" && $email_from != "" && $central_dispatch_post == 1) {

        //build import string

        $command_uid = "UID(" . $central_dispatch_uid . ")*";
        //Command
        $command_act = "DELETE(" . $ref->getNumber() . ")*";
        //1. Order ID:
        $command = addcslashes(trim($ref->getNumber()), ",") . ",";
        //2. Pickup City:
        $command .= addcslashes(trim($ref->getOrigin()->city), ",") . ",";
        //3. Pickup State:
        $command .= strtoupper(addcslashes($ref->state2Id(trim($ref->getOrigin()->state)), ",")) . ",";
        //4. Pickup Zip:
        $command .= addcslashes(trim($ref->getOrigin()->zip), ",") . ",";
        //5. Delivery City:
        $command .= addcslashes(trim($ref->getDestination()->city), ",") . ",";
        //6. Delivery State:
        $command .= strtoupper(addcslashes($ref->state2Id(trim($ref->getDestination()->state)), ",")) . ",";
        //7. Delivery Zip:
        $command .= addcslashes(trim($ref->getDestination()->zip), ",") . ",";
        //8. Carrier Pay:
        $command .= $ref->carrier_pay . ",";

        $codcops = array(
            Entity::BALANCE_COD_TO_CARRIER_CASH,
            Entity::BALANCE_COD_TO_CARRIER_CHECK,
            Entity::BALANCE_COP_TO_CARRIER_CASH,
            Entity::BALANCE_COP_TO_CARRIER_CHECK,
        );

        $cash_certified_funds = array(
            Entity::BALANCE_COD_TO_CARRIER_CASH,
            Entity::BALANCE_COP_TO_CARRIER_CASH,
        );
        $check = array(
            Entity::BALANCE_COD_TO_CARRIER_CHECK,
            Entity::BALANCE_COP_TO_CARRIER_CHECK,
        );

        $pickup = array(
            Entity::BALANCE_COP_TO_CARRIER_CASH,
            Entity::BALANCE_COP_TO_CARRIER_CHECK,
        );

        $codcop_amount = $ref-> total_tariff + $ref->pickup_terminal_fee + $ref->dropoff_terminal_fee - $ref->total_deposit;
       
        if ($codcop_amount > 0) {
            if (in_array($ref->balance_paid_by, $codcops)) {
                //9. COD/COP Amount:
                $command .= $codcop_amount . ",";
                //10. COD/COP Method:
                if (in_array($ref->balance_paid_by, $cash_certified_funds)) {
                    $command .= "cash/certified funds,";
                } else {
                    $command .= "check,";
                }
                //11. COD/COP Timing:
                if (in_array($ref->balance_paid_by, $pickup)) {
                    $command .= "pickup,";
                } else {
                    $command .= "delivery,";
                }
            } else {
                //9. COD/COP Amount:
                $command .= "0.00,";
                //10. COD/COP Method:
                $command .= "cash/certified funds,";
                //11. COD/COP Timing:
                $command .= "delivery,";
            }
        } else {
            //9. COD/COP Amount:
            $command .= "0.00,";
            //10. COD/COP Method:
            $command .= "cash/certified funds,";
            //11. COD/COP Timing:
            $command .= "delivery,";
        }

        //12. Remaining Balance Payment Method:
        $command .= "none,";
        //13. Ship Method:
        $command .= strtolower($ref->getShipVia()) . ",";
        
        //18. Vehicle(s):

        $vs = array();
        $inopValue = "operable";
        if (count($vehicles) > 0) {
            foreach ($vehicles as $vehicle) {
                /*if (in_array($vehicle->type, array("Boat", "Car", "Motorcycle", "Pickup", "RV", "SUV", "Travel Trailer", "Van"))) {
                    $type = $vehicle->type;
                } else {
                    $type = "Other: " . $vehicle->type;
                }
                */

                $valueVT = $ref->getVehicleType($vehicle->type);
                if($valueVT != -1){
                     $type = $valueVT;
                } else {
                    $type = "Other: " . $vehicle->type;
                }

                if($vehicle->inop == 1)
                   $inopValue = "inop";
                   
                $vs[] = addcslashes($vehicle->year . "|" . $vehicle->make . "|" . $vehicle->model . "|" . $type, ",");
            }
            //$command .= implode(";", $vs);
        }
        
        $firstAvail = $ref->getPostDate("Y-m-d");
        if(strtotime(trim($ref->getFirstAvail("Y-m-d"))) >= strtotime(date('Y-m-d')) )
             $firstAvail = $ref->getFirstAvail("Y-m-d");
             
        //14. Vehicle Operable:
        $command .= $inopValue . ",";//$this->getInopExportName() . ",";
        //15. First Available (YYYY-MM-DD):
        $command .=  $firstAvail . ",";//$this->getFirstAvail("Y-m-d") . ",";
        //16. Display Until:
        $command .= date("Y-m-d",strtotime(date("Y-m-d", strtotime($firstAvail)) . "+1 month")) . ",";//$firstAvail . ",";//$this->getFirstAvail("Y-m-d") . ",";
        //17. Additional Info:
        
        $command .= addcslashes(substr($ref->information, 0, 60), ",").",";
        
        
        if (count($vehicles) > 0) {
            $command .= implode(";", $vs);
        }
        
        //strip asterisks
        //end of command
        $message = "";

        if($posttype == 1){
            $message = $command_uid . $command_act . str_replace("*", "", $command) . "*";
        } else if($posttype == 2){
            $message = $command_uid . $command_act;
        } else if($posttype == 3){
            $message = $command_uid . $command_act;
        } else if($posttype == 4){
            $message = $command_uid . $command_act;
        } else {
            $message = $command_uid . $command_act . str_replace("*", "", $command) . "*";
        }


        $subject = "";

        if($posttype == 1){
            $subject = "re-posting request to CD for ID " . $ref->getNumber() . "";
        } else if($posttype == 2){
            $subject = "unpost request to CD for ID " . $ref->getNumber() . "";
        } else if($posttype == 3){
            $subject = "dispatch request to CD for ID " . $ref->getNumber() . "";
        } else if($posttype == 4){
            $subject = "cancel request to CD for ID " . $ref->getNumber() . "";
        } else {
            $subject = "posting request to CD for ID " . $ref->getNumber() . "";
        }

        try{
            $mail = new FdMailer(true);
            $mail->IsHTML(false);
            $mail->Body = $message;
            $mail->Subject = $subject;
            $mail->SetFrom("Info@transportmasters.net");
            $mail->AddAddress(Entity::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
            $mail->AddBCC("junk@cargoflare.com");
            $mail->AddBCC("shahrukhusmaani@live.com");
            
            $mail->Send();
            History::add($daffny->DB, $ref->id, "CENTRAL DISPATCH", ($posttype == 1 ? "REPOST ORDER" : "ADD ORDER TO CD"), date("Y-m-d H:i:s"));
        } catch (Exception $e) {
            print_r($e);
        }
    }
}

ob_clean();
echo $json->encode($out);
require_once("done.php");
