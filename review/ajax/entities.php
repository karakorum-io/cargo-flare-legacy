<?php

/* * *************************************************************************************************
 * Actions with entities *
 * Client:              FreightDragon
 * Version:             1.0
 * Date:                2017-03-01
 * Author:              Chetu Inc
 * CopyRight 2017       FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */
require_once("init.php");

$out = array('success' => false);

if (isset($_POST['action'])) {
    switch ($_POST['action']) {

        /* chetu added case */
        case "addComment":
            $comment = $_POST['comment'];
            $comment2 = $_POST['comment2'];
            $star = $_POST['star'];
            $star2 = $_POST['star2'];
            $entity_id = $_POST['entity_id'];
            $update = $_POST['update'];
            if (($_POST['star'] != "") || ($_POST['comment'] != "") || $_POST['star2'] || $_POST['comment2']) {

                $data = $daffny->DB->selectRow("account_id, number, prefix, parentid, carrier_id, shipper_id", "app_entities", "WHERE id='" . $entity_id . "'");
                $shipper_id = $data['shipper_id'] . "";
                $carrier_id = $data['carrier_id'] . "";
                $ownerId = $data['parentid']."";
                $number = $data['number']."";
                $prefix = $data['prefix']."";
                $account_id = $data['account_id'];
                
                $customerData = $daffny->DB->selectRow("first_name, last_name, company_name, phone1, phone2, email ", "app_accounts", "WHERE id='" . $account_id . "'");
                $extras = $daffny->DB->selectRow("number, prefix, assigned_id", "app_entities", "WHERE id='" . $entity_id . "'");
                $assignedName = $daffny->DB->selectRow("contactname", "members", "WHERE id='" . $extras['assigned_id'] . "'");
                $carrierName = $daffny->DB->selectRow("company_name, contact_name1, phone1, email", "app_accounts", "WHERE id='" . $carrier_id . "'");
                
                
                
                $ins_arr = array(
                    'entity_id' => $entity_id,
                    'orderId' => $extras['prefix']."-".$extras['number'],
                    'assignedName' => $assignedName['contactname'],
                    'ratings' => $star,
                    'comment' => $comment,
                    'carrier_id' => $carrier_id,
                    'company_name' => $carrierName['company_name'],
                    'carrierName' => $carrierName['contact_name1'],
                    'phone1' => $carrierName['phone1'],
                    'carrierEmail' => $carrierName['email'],
                    'shipper_id' => $shipper_id,
                    'car_rating' => $star2,
                    'car_comment' => $comment2
                );
                
                //print_r($ins_arr);
                
                $settings = $daffny->DB->selectRow("thresholdRating, reviewNotificationEmail", "app_defaultsettings", "WHERE owner_id='" . $ownerId . "'");
                $thresholdRating = $settings['thresholdRating'];
                $reviewNotificationEmail = $settings['reviewNotificationEmail'];
                
                if( $star <= $thresholdRating || $star2 <= $thresholdRating ){
                    
                    
                    $carrierInfo = $daffny->DB->selectRow("company_name, contact_name1, phone1, phone2, email", "app_accounts", "WHERE id='" . $carrier_id . "'");
                   
                    //notify admin for bad rating
                    $message ="<b>Order Information</b><br> Order Id: ".$prefix."-".$number ."<br>"
                            . "<b>Assigned To: </b>".$assignedName['contactname']." "
                            . "<b><br><br>Customer Information</b><br>"
                            . "Name: ".$customerData['first_name']." ".$customerData['last_name']."<br>"
                            . "Company Name: ".$customerData['company_name']."<br>"
                            . "Phone Number: ".$customerData['phone1']."<br>"
                            . "Phone Number2: ".$customerData['phone2']."<br>"
                            . "Email Address: ".$customerData['email']."<br><br>"
                            . "<b>Carrier Information<br></b>"
                            . "Conatct Name: ".$carrierInfo['contact_name1']."<br>"
                            . "Company Name: ".$carrierInfo['company_name']."<br>"
                            . "Phone1: ".$carrierInfo['phone1']."<br>"
                            . "Phone2: ".$carrierInfo['phone2']."<br>"
                            . "Email: ".$carrierInfo['email']."<br><br>"
                            . "<br><b>Reviews: </b><br>Order Rating:".$star."<br>"
                            . "<b>Order Comment: </b>".$comment."<br><br> "
                            . "Carrier Rating:".$star2."<br>"
                            . "<b>Carrier Comment: </b>".$comment2."<br>";
                    try {
                        $mail = new FdMailer(true);
                        $mail->IsHTML(true);
                        $mail->Body = $message;
                        $mail->Subject = "Negative Review Given";
                        $mail->SetFrom("noreply@freightdragon.com");
                        $mail->AddAddress($reviewNotificationEmail, "Admin");
                        ob_start();
                        $ret = $mail->SendToCD();
                        $mailer_output = ob_get_clean();
                        
                    } catch (phpmailerException $e) {
                        $ret = $e->getMessage();
                    } catch (Exception $e) { }
                }
                
                
                
                if ($update == 1) {
                    $daffny->DB->update("app_reviews", array('ratings' => $star, 'comment' => $comment), "entity_id = '" . $entity_id . "' ");
                    $out = array('success' => true, "message" => 'Comment Updated');
                } else {
                    $daffny->DB->insert("app_reviews", $ins_arr);
                    $ins_id = $daffny->DB->get_insert_id();
                    if ($ins_id != "") {
                        $out = array('success' => true, "message" => 'Comment Added');
                    } else {
                        $out = array('success' => true, "message" => 'Unable Add Comment');
                    }
                }
            } else {
                $out = array('success' => true, "message" => 'Unable Add Comment');
            }
            break;

        case "getComment":
            $entity_id = $_POST['entity_id'];
            $data = $daffny->DB->selectRow("id", "app_reviews", "WHERE entity_id='" . $entity_id . "'");
            
            $data1 = $daffny->DB->selectRow("parentid, assigned_id", "app_entities", "WHERE id='" . $entity_id . "'");
            $carrier_id = $data1['parentid'] . "";
            $assignedId = $data1['assigned_id'] . "";
            
            $carrierInfo = $daffny->DB->selectRow("companyname", "members", "WHERE id='" . $carrier_id . "'");
            $assignedInfo = $daffny->DB->selectRow("contactname", "members", "WHERE id='" . $assignedId . "'");
            
            $out = array('success' => true, "message" => 'Value Fetched', "id" => $data['id'], "companyName" => $carrierInfo['companyname'], "contactName" => $assignedInfo['contactname']);
            break;

        case "getEntityData":
            $entity_id = $_POST['entity_id'];
            $data = $daffny->DB->selectRow("*", "app_reviews", "WHERE entity_id='" . $entity_id . "'");
            
            if($data == NULL){
                $out = array('success' => 'FALSE');
            } else {
                $out = array('success' => true, "message" => 'Value Fetched', "data" => $data, "createdAt"=> date("m/d/Y h:i:s a", strtotime($data['created_at'])));
            }            
            
            break;

        case "getAverageRating":
            $data = $daffny->DB->selectRow("AVG(ratings) as orderRatings, AVG(car_rating) as carrierRatings", "app_reviews","");
            $out = array('success' => true, "message" => 'Success', "data" => $data);
            break;
        /* chetu added case */

        default:
            break;
    }

    echo json_encode($out);
} 