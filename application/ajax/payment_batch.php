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
function checkEmpty($value, $title)
{
    $errors = array();
    if (trim($value) == "") {
        $errors[] = $title . " value required";
    }

    return $errors;
}

if ($memberId > 0) {
    try {

        if (isset($_POST['action'])) {

            switch ($_POST['action']) {
                case 'paymentBatch':

                    $data = array();
                    $errors = array();

                    $required_fields = array(
                        'date_received' => "Date Received",
                        'from_to' => "Payment From/To",

                    );
                    foreach ($required_fields as $field => $label) {
                        if (!isset($_POST[$field])) {
                            $errors[] = $label . " value required";
                            continue;
                        }
                        $errors = array_merge($errors, checkEmpty($_POST[$field], $label));
                    }

                    if (count($errors) > 0) {
                        $out = array('success' => false, 'errors' => $errors);
                    } else {

                        $batch_order_ids_arr = explode(",", trim($_POST['batch_order_ids']));
                        $arrAmount_arr = explode(",", trim($_POST['arrAmount']));
                        $arrAmountFlag_arr = explode(",", trim($_POST['arrAmountFlag']));

                        $batch_order_ids_arrSize = sizeof($batch_order_ids_arr);
                        if ($batch_order_ids_arrSize > 0) {
                            for ($i = 0; $i < $batch_order_ids_arrSize; $i++) {

                                if ($arrAmountFlag_arr[$i] == 1) {
                                    $entity = new Entity($daffny->DB);
                                    $entity->load($batch_order_ids_arr[$i]);

                                    $insert_arr['entity_id'] = $batch_order_ids_arr[$i];
                                    $insert_arr['number'] = Payment::getNextNumber($batch_order_ids_arr[$i], $daffny->DB);
                                    $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received']));
                                    $from_to = explode("-", $_POST['from_to']);
                                    $insert_arr['fromid'] = $from_to[0];
                                    $insert_arr['toid'] = $from_to[1];
                                    $insert_arr['entered_by'] = $_SESSION['member_id'];
                                    $insert_arr['amount'] = number_format((float) $arrAmount_arr[$i], 2, '.', '');
                                    $insert_arr['method'] = $_POST['method'];
                                    $insert_arr['transaction_id'] = $_POST['transaction_id'];

                                    switch ($_POST['method']) {
                                        case "9":
                                            $insert_arr['cc_number'] = $_POST['cc_numb'];
                                            if ($_POST['cc_type'] != 0) {
                                                $insert_arr['cc_type'] = $_POST['cc_type'];
                                            } else {
                                                $insert_arr['cc_type'] = $_POST['cc_type_other'];
                                            }
                                            $insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
                                            $insert_arr['cc_auth'] = $_POST['cc_auth'];
                                            break;
                                        case "1":
                                        case "2":
                                        case "3":
                                        case "4":
                                            $insert_arr['check'] = $_POST['ch_number'];
                                            break;
                                    }
                                    $noteText = "";
                                    $member = new Member($daffny->DB);
                                    $member->load($_SESSION['member_id']);
                                    $company = $member->getCompanyProfile();
                                    $nmethod = $_POST['method'];

                                    if ($_POST['from_to'] == Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY) {
                                        $noteText = "<green>Shipper paid " . $company->companyname . " $ " . $insert_arr['amount'] . " by " . Payment::$method_name[$nmethod];
                                        if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                            if ($_POST['ch_number'] != "") {
                                                $noteText .= " #" . $_POST['ch_number'];
                                            }

                                        } elseif ($nmethod == 9) {
                                            if ($_POST['cc_numb'] != "") {
                                                $noteText .= " ending in #" . $_POST['cc_numb'];
                                            }

                                        }

                                    } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                        $noteText = $noteText = "<green>Carrier paid " . $company->companyname . " $ " . $insert_arr['amount'] . " by " . Payment::$method_name[$nmethod];
                                    } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                        $noteText = $noteText = "<green>" . $company->companyname . " paid Shipper $ " . $insert_arr['amount'] . " by " . Payment::$method_name[$nmethod];
                                    }

                                    $payment = new Payment($daffny->DB);

                                    $payment->create($insert_arr);
                                    $data['success'] = "Your payment has been processed.";
                                    $out = array('success' => true, 'data' => $data);

                                    $method = $_POST['method'];
                                    $note_array = array(
                                        "entity_id" => $batch_order_ids_arr[$i],
                                        "sender_id" => $_SESSION['member_id'],
                                        "status" => 1,
                                        "type" => 3,
                                        "system_admin" => 1,
                                        "text" => $noteText); //"Payment processed by ".Payment::$method_name[$method]." internally for the amount of $ ".number_format((float)$arrAmount_arr[$i], 2, '.', ''));

                                    $note = new Note($daffny->DB);
                                    $note->create($note_array);

                                    $entity->updateHeaderTable();
                                } //if flag
                            }
                        } else {
                            $out = array('success' => false, 'errors' => "Orders not found for payment.");
                        }
                    }

                    break;
                case 'paymentBatchNew':

                    $data = array();
                    $errors = array();

                    $required_fields = array(
                        'date_received' => "Date Received",
                        'from_to' => "Payment From/To",

                    );

                    foreach ($required_fields as $field => $label) {
                        if (!isset($_POST[$field])) {
                            $errors[] = $label . " value required";
                            continue;
                        }
                        $errors = array_merge($errors, checkEmpty($_POST[$field], $label));
                    }

                    if (count($errors) > 0) {
                        $out = array('success' => false, 'errors' => $errors);
                    } else {

                        $batch_order_ids_arr = explode(",", trim($_POST['batch_order_ids']));
                        $arrAmount_arr = explode(",", trim($_POST['arrAmount']));
                        $arrAmountFlag_arr = explode(",", trim($_POST['arrAmountFlag']));
                        $checkPaymentFlag_arr = explode(",", trim($_POST['checkPaymentFlag']));
                        $batch_order_ids_arrSize = sizeof($batch_order_ids_arr);

                        if ($batch_order_ids_arrSize > 0) {

                            for ($i = 0; $i < $batch_order_ids_arrSize; $i++) {

                                if ($arrAmountFlag_arr[$i] == 1) {
                                    $entity = new Entity($daffny->DB);
                                    $entity->load($batch_order_ids_arr[$i]);
                                    $insert_arr['entity_id'] = $batch_order_ids_arr[$i];
                                    $insert_arr['number'] = Payment::getNextNumber($batch_order_ids_arr[$i], $daffny->DB);
                                    $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received']));
                                    $from_to = explode("-", $_POST['from_to']);
                                    $insert_arr['fromid'] = $from_to[0];
                                    $insert_arr['toid'] = $from_to[1];
                                    $insert_arr['entered_by'] = $_SESSION['member_id'];
                                    $insert_arr['amount'] = number_format((float) $arrAmount_arr[$i], 2, '.', '');
                                    $insert_arr['method'] = $_POST['method'];

                                    switch ($_POST['method']) {

                                        case "9":
                                            $insert_arr['cc_number'] = $_POST['cc_numb'];
                                            if ($_POST['cc_type'] != 0) {
                                                $insert_arr['cc_type'] = $_POST['cc_type'];
                                            } else {
                                                $insert_arr['cc_type'] = $_POST['cc_type_other'];
                                            }

                                            $insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
                                            $insert_arr['cc_auth'] = $_POST['cc_auth'];
                                            break;

                                        case "1":
                                        case "2":
                                        case "3":
                                        case "4":
                                            $insert_arr['check'] = $checkPaymentFlag_arr[$i];
                                            break;

                                    }

                                    $payment = new Payment($daffny->DB);
                                    $payment->create($insert_arr);

                                    $data['success'] = "Your payment has been processed.";
                                    $out = array('success' => true, 'data' => $data);
                                    $method = $_POST['method'];
                                    $noteText = " #" . $checkPaymentFlag_arr[$i];

                                    $note_array = array(
                                        "entity_id" => $batch_order_ids_arr[$i],
                                        "sender_id" => $_SESSION['member_id'],
                                        "status" => 1,
                                        "type" => 3,
                                        "system_admin" => 1,
                                        "text" => "<green>Carrier has been paid amount $ " . number_format((float) $arrAmount_arr[$i], 2, '.', '') . " by " . Payment::$method_name[$method] . $noteText);

                                    $note = new Note($daffny->DB);
                                    $note->create($note_array);
                                    $entity->updateHeaderTable();

                                } //if flag
                            }

                        } else {
                            $out = array('success' => false, 'errors' => "Orders not found for payment.");
                        }
                    }
				break;
                default:
				break;
            }
        }
    } catch (Exception $e) {

        if ($daffny->DB->isTransaction) {
            $daffny->DB->transaction('rollback');
        }
		
        $out['message'] = $e->getMessage();
    }
}

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

//ob_clean();
echo $json->encode($out);
//require_once("done.php");
