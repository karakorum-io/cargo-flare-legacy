<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once("init.php");

ob_start();

// payment processing functionality

$EntityID = $_GET["EntityID"];
$AccountID = $_POST["AccountID"];


$entity = new Entity($daffny->DB);
$entity->load($EntityID);

$defaultSettings = new DefaultSettings($daffny->DB);
$defaultSettings->getByOwnerId($entity->parentid);

if (in_array($defaultSettings->current_gateway, array(1, 2,3))) {

    // paypal credentials
    if ($defaultSettings->current_gateway == 1) {
        if (trim($defaultSettings->paypal_api_username) == ""
            || trim($defaultSettings->paypal_api_password) == ""
            || trim($defaultSettings->paypal_api_signature) == ""
        ) {
            echo json_encode(array("success"=>false,"Error"=>"Incomplete Paypal Information!"));die;
        }
    }

    // authorize.net
    if ($defaultSettings->current_gateway == 2) {
        if (trim($defaultSettings->anet_api_login_id) == ""
            || trim($defaultSettings->anet_trans_key) == ""
        ) {
            echo json_encode(array("success"=>false, "Error"=>"Incomplete Authorize.net Information!"));die;
        }
    }
    
    // authorize.net MDSIP
    if ($defaultSettings->current_gateway == 3) {
        if (trim($defaultSettings->gateway_api_username) == ""
            || trim($defaultSettings->gateway_api_password) == ""
        ) {
            echo json_encode(array("success"=>false,"Error"=>"Incomplete Payment Gateway Information!"));die;
        }
    }
} else {
    echo json_encode(array("success"=>false,"Error"=>"There is not Active gateway for this account"));die;
}

$amount = 0;
if (isset($_POST['gw_pt_type']) && in_array($_POST['gw_pt_type'], array("other", "deposit", "balance"))) {
    switch (post_var("gw_pt_type")) {
        case "deposit":
            $amount = (float)post_var("deposit_pay");//$entity->total_deposit;
            break;
        case "balance":
            $amount = (float)post_var("tariff_pay");//$entity->total_tariff;
            break;
        case "other":
            $amount = (float)post_var("other_amount");
            break;
    }
} else {
    echo json_encode(array("success"=>false,"Error"=>"Please Choose Payment Amount"));die;
}

if ($amount == 0) {
    echo json_encode(array("Error"=>"Amount cannot be 0.00"));die;
}

$arr = array(
    "other_amount" => post_var("other_amount")
    , "gw_pt_type" => post_var("gw_pt_type")
    , "cc_fname" => post_var("cc_fname")
    , "cc_lname" => post_var("cc_lname")
    , "cc_address" => post_var("cc_address")
    , "cc_city" => post_var("cc_city")
    , "cc_state" => post_var("cc_state")
    , "cc_zip" => post_var("cc_zip")
    , "cc_cvv2" => post_var("cc_cvv2")
    , "cc_number" => post_var("cc_number")
    , "cc_type" => post_var("cc_type")
    , "cc_month" => post_var("cc_month")
    , "cc_year" => post_var("cc_year")
    , "cc_type_name" => Payment::getCCTypeById(post_var("cc_type"))
);

$pay_arr = $arr + array(
    "amount" => (float)$amount
    , "paypal_api_username" => trim($defaultSettings->paypal_api_username)
    , "paypal_api_password" => trim($defaultSettings->paypal_api_password)
    , "paypal_api_signature" => trim($defaultSettings->paypal_api_signature)
    , "anet_api_login_id" => trim($defaultSettings->anet_api_login_id)
    , "anet_trans_key" => trim($defaultSettings->anet_trans_key)
    , "gateway_api_username" => trim($defaultSettings->gateway_api_username)
    , "gateway_api_password" => trim($defaultSettings->gateway_api_password)
    , "notify_email" => trim($defaultSettings->notify_email)
    , "order_number" => trim($entity->getNumber())
);

// authorize.net
if ($defaultSettings->current_gateway == 2) {
    // adding library authorize.net librray when needed
    require("../../libs/anet/AuthorizeNet.php");
    $response = ProcessAuthorize($daffny, $pay_arr);
}

// paypal
if ($defaultSettings->current_gateway == 1) {
    // adding paypal library when needed
    require("../../app/classes/paypalpro.php");
    $paypal = new PayPalPro();
    
    $paypal->api_username = $defaultSettings->paypal_api_username;
    $paypal->api_password = $defaultSettings->paypal_api_password;
    $paypal->api_signature = $defaultSettings->paypal_api_signature;

    $paypal->dataAmount = $amount;
    $paypal->dataCreditCardType = Payment::getCCTypeById(post_var("cc_type"));
    $paypal->dataCreditCardNumber = post_var("cc_number");
    $paypal->dataExpMonth = post_var("cc_month");
    $paypal->dataExpYear = post_var("cc_year");
    $paypal->dataCVV2 = post_var("cc_cvv2");

    $paypal->dataFirstName = post_var("cc_fname");
    $paypal->dataLastName = post_var("cc_lname");
    $paypal->dataStreet = post_var("cc_address");
    $paypal->dataCity = post_var("cc_city");
    $paypal->dataState = post_var("cc_state");
    $paypal->dataZip = post_var("cc_zip");
    $paypal->dataProductID = trim($entity->getNumber());

    $res = $paypal->hash_call();

    if($res["ACK"] == "Failure"){
        $response = array(
            "Success"=>false,
            "Error"=>$res
        );
    } else {
        $response = array(
            "Success"=>true,
            "Error"=>$res
        );
    }
}

// MDSIP
if($defaultSettings->current_gateway == 3){
    $shipper = $entity->getShipper();
								
    $pay_arr1 = $pay_arr + array(
        'orderid' => $entity->id,
        'orderdescription' => $entity->getNumber(),
        'tax' => '',
        'shipping' => 1,
        'ponumber' => 2,					
        'ipaddress' => '',	
        
        'fname' => $shipper->fname,
        'lname' => $shipper->lname,
        'email' => $shipper->email,
        'company' => $shipper->company,
        'phone1' => formatPhone($shipper->phone1),
        'phone2' => formatPhone($shipper->phone2),
        'mobile' => $shipper->mobile,
        'fax' => $shipper->fax,
        'address1' => $shipper->address1,
        'address2' => $shipper->address2,
        'city' => $shipper->city,
        'state' => $shipper->state,
        'zip' => $shipper->zip,
        'country' => $shipper->country,
        'shipper_type' => $shipper->shipper_type,
        'shipper_hours' => $shipper->shipper_hours
    );

    // adding library when needed
    require_once("../../libs/mdsip/mdsip.php");
    $response = ProcessMDSIP($daffny, $pay_arr1);
}

if($response['Success']){
    
    $insert_arr['entity_id'] = $EntityID;
    $insert_arr['number'] = Payment::getNextNumber($EntityID, $daffny->DB);
    $insert_arr['date_received'] = date("Y-m-d H:i:s");
    $insert_arr['fromid'] = Payment::SBJ_SHIPPER;
    $insert_arr['toid'] =  Payment::SBJ_COMPANY;
    $insert_arr['payment_type'] = 1;
    $insert_arr['entered_by'] = $AccountID;
    $insert_arr['amount'] = number_format((float)$pay_arr['amount'], 2, '.', '');

    // when gateway are different, so different respective notes
    if($defaultSettings->current_gateway == 1){
        $notes = "PayPal".$response['transaction_id'];
    } else if($defaultSettings->current_gateway == 2) {
        $notes = "Authorize.net".$response['transaction_id'];
    } else {
        $notes = "MDSIP".$response['transaction_id'];
    }

    $insert_arr['notes'] = $notes;
    $insert_arr['method'] = Payment::M_CC;
    $insert_arr['transaction_id'] = "12321";
    $insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
    $insert_arr['cc_type'] = $pay_arr['cc_type_name'];
    $insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
    
    $payment = new Payment($daffny->DB);
    $payment->create($insert_arr);

    if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) == '' && trim($entity->archived) == ''){
        $entity->setStatus(Entity::STATUS_DELIVERED);
    }
    
    /* UPDATE NOTE */
    $note_array = array(
        "entity_id" => $entity->id,
        "sender_id" => 0,
        "sender_customer_portal" => $AccountID,
        "type" => 3,
        "system_admin" => 1,
        "text" => "<green>CREDIT CARD PROCESSED FOR THE AMOUNT OF $ ".number_format((float)$pay_arr['amount'], 2, '.', '')
    );
    $note = new Note($daffny->DB);
    $note->create($note_array);

    // quickbooks trigger
    if($entity->parentid == 1) { 
        $objQuickbook = new QueueQuickbook();
        $objQuickbook->queueReceivedPayment($daffny->cfg['dsn'],$payment->id);
    }
} else {
    /* UPDATE NOTE */
    $note_array = array(
        "entity_id" => $entity->id,
        "sender_id" => $AccountID,
        "type" => 3,
        "system_admin" => 1,
        "text" => "<red>CREDIT CARD PROCESSING ERROR:".$response['Error']
    );    
    $note = new Note($daffny->DB);
    $note->create($note_array);
}

echo json_encode($response);die;

// payment processing over

ob_clean();
echo $json->encode($out);
require_once("done.php");

/**
 * Functionality to process payment via Authorize.Net
 * 
 * @author Shahrukh
 * @version 1.0
 * @return void
 */
function ProcessAuthorize($daffny,$pay){

    $api_login = $pay['anet_api_login_id'];
    $api_pwd = $pay['anet_trans_key'];
    $api_amount = $pay['amount'];
    $pay_success = false;
    $pay_reason = "";
    $transaction_id = "";

    $transaction = new AuthorizeNetAIM($api_login, $api_pwd);
    $transaction->setSandbox($daffny->cfg['anet_sandbox']);

    $transaction->setFields(
        array(
            'amount' => $api_amount
            , 'card_num' => $pay['cc_number']
            , 'exp_date' => $pay['cc_month'] . "/" . $pay['cc_year']
            , 'card_code' => $pay['cc_cvv2']
            , 'first_name' => $pay['cc_fname']
            , 'last_name' => $pay['cc_lname']
            , 'address' => $pay['cc_address']
            , 'city' => $pay['cc_city']
            , 'state' => $pay['cc_state']
            , 'zip' => $pay['cc_zip']
            , 'description' => "Freight Dragon: Order#" . $pay['order_number']
            , 'invoice_num' => $pay['order_number']
        )
    );

    $response = $transaction->authorizeAndCapture();
    if ($response->approved) {
            return array(
                "Success"=>true,
                "Message"=>$response->response_reason_text,
                "TransactionID"=>$response->transaction_id
            );
    } else {
        return array("Success"=>false,"Error"=>$response->response_reason_text);
    }
}

/**
 * Function processing payments via MDSIP
 * 
 * @author Shahrukh
 * @version 1.0
 * @return void
 */
function ProcessMDSIP($daffny, $pay){
    $api_login = $pay['gateway_api_username'];
    $api_pwd = $pay['gateway_api_password'];
    $api_amount = $pay['amount'];

    $pay_success = false;
    $pay_reason = "";
    $transaction_id = "";

    $gw = new gwapi;
    $gw->setLogin($api_login, $api_pwd);

    $gw->setBilling(
        $pay['cc_fname'],
        $pay['cc_lname'],
        $pay['company'],
        $pay['cc_address'],
        $pay['address2'], 
        $pay['cc_city'],
        $pay['cc_state'],
        $pay['cc_zip'],
        "US",
        $pay['phone1'],
        $pay['phone2'],
        $pay['email'],
        "www.freightdragon.com"
    );

    $gw->setShipping($pay['cc_fname'],
        $pay['cc_lname'],
        $pay['company'],
        $pay['cc_address'],
        $pay['address2'], 
        $pay['cc_city'],
        $pay['cc_state'],
        $pay['cc_zip'],
        "US",
        $pay['email'],
        "www.freightdragon.com"
    );

    $gw->setOrder(
        $pay['orderid'],
        $pay['orderdescription'],
        $pay['tax'],
        $pay['shipping'],
        $pay['cc_zip'],
        $pay['ipaddress']
    );

    $r = $gw->doSale(
        $api_amount,
        $pay["cc_number"],
        $pay["cc_month"] . $pay["cc_year"],
        $pay["cc_cvv2"]
    );

    // obatining gateway response
    $response = $gw->responses['responsetext'];
    
    if ($response == "APPROVED") {
        return array(
            "Success"=>true,
            "Message"=>$gw->responses['responsetext'],
            "TransactionID"=>$gw->responses['transactionid']
        );
    } else {
        return array("Success"=>false,"Error"=>$gw->responses['responsetext']);
    }
}

?>