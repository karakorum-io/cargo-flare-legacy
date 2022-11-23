<?php

/**
 * payments.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

// loading dependencies
require_once "init.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

//ob_start();
if ($memberId > 0) {
    try {
        switch ($_GET['action']) {
            case 'get':
                if (!isset($_POST['entity_id']) || !isset($_POST['payment_id'])) {
                    throw new FDException("Invalid Params");
                }

                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $payment = new Payment($daffny->DB);
                $payment->load($_POST['payment_id']);

                if ($payment->entity_id != $entity->id) {
                    throw new FDException("Invalid Payment");
                }

                $out = array(
                    'success' => true,
                    'data' => array(
                        'number' => $payment->number,
                        'date_received' => $payment->getDate(),
                        'from_to' => $payment->fromid . "-" . $payment->toid,
                        'amount' => $payment->amount,
                        'method' => $payment->method,
                        'transaction_id' => $payment->transaction_id,
                        'notes' => $payment->notes,
                        'cc_numb' => $payment->cc_number,
                        'cc_type' => $payment->cc_type,
                        'cc_exp' => $payment->cc_exp,
                        'cc_auth' => $payment->cc_auth,
                        'check' => $payment->check,
                    ),
                );
                break;
            case 'delete':
                $payment = new Payment($daffny->DB);
                $payment->load($_GET['id']);
                $entity = new Entity($daffny->DB);
                $entity->load($payment->entity_id);

                $member = new Member($daffny->DB);
                $member->load($_SESSION['member_id']);

                /* UPDATE NOTE */
                $noteText = "Payment " . $payment->getFrom() . " to " . $payment->getTo() . " of $ " . $payment->amount . " is deleted by " . $member->contactname . " on " . date("m/d/Y h:i A");
                $note_array = array(
                    "entity_id" => $entity->id,
                    "sender_id" => $member->id,
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 1,
                    "text" => $noteText);

                $note = new Note($daffny->DB);
                $note->create($note_array);
                $payment->delete();
                $out = (array('success' => true));
                break;
            case 'saveEntityCreditCard':
                if (!isset($_POST['entity_id'])) {
                    throw new FDException("Invalid Params");
                }

                $entity = new Entity($daffny->DB);
                $entity->load($_POST['entity_id']);
                $cc = $entity->getCreditCard();
                unset($_POST['entity_id']);
                $cc->update($_POST);

                if ($daffny->DB->isError) {
                    throw new FDException("MySQL query error");
                }

                $out = array(
                    'success' => true,
                );
                break;
        }

        switch ($_POST['action']) {
            case 'REFUND_EASY_PAY':
                $defaultSettings = new DefaultSettings($daffny->DB);
                $defaultSettings->getByOwnerId(getParentId());
                $ePay = new EasyPay();
                $ePay->setLogin($defaultSettings->easy_pay_key);
                //$response = $ePay->doRefund($_POST['transaction_id'],$_POST['amount']);
                $response = $ePay->doVoid($_POST['transaction_id']);

                $out = array(
                    'success' => true,
                    'message' => $response,
                );
            break;
            case 'VOID_EASY_PAY':
                $defaultSettings = new DefaultSettings($daffny->DB);
                $defaultSettings->getByOwnerId(getParentId());
                $ePay = new EasyPay();
                $ePay->setLogin($defaultSettings->easy_pay_key);
                //$response = $ePay->doRefund($_POST['transaction_id'],$_POST['amount']);
                $response = $ePay->doVoid($_POST['transaction_id']);

                $out = array(
                    'success' => true,
                    'message' => $response,
                );
            break;
        }

    } catch (FDException $e) {
        echo $e->getMessage();
    }
}
//ob_clean();
echo $json->encode($out);
require_once "done.php";
