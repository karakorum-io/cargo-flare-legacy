<?php
@session_start();
ob_start();
require_once '../libs/mpdf/mpdf.php';
require_once '../libs/QuickBooks.php';
require_once "init.php";
$_SESSION['iamcron'] = true; // Says I am cron for Full Access
$memberId = (int) $_SESSION['member_id'];

$InvoiceData = GetInvoiceData($daffny);
// create folder if not exists
if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
    mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
}

// create file
$fileName = "Check-Recipts-v2-".date('Y-m-d his').".html";
$fullPath = ROOT_PATH."uploads/Invoices/Checks/".$fileName;

$content = '';
ob_start();
?>

<?php

    // update start value in check number
    $inp = $daffny->DB->selectRow('id,value', 'members_type_value', "WHERE member_id='".$_SESSION['member']['id']."' and type='QBPRINT'");
    $res = $daffny->DB->update('members_type_value', array('value' => $_POST['startNumber']), "id = '".$inp['id']."' ");
    

    // iterating UI starts
    for($i=0;$i<count($InvoiceData);$i++){

        $CarrierID = $InvoiceData[$i]['CarrierID'];
        $sql = "SELECT * FROM app_accounts WHERE `id` = ".$CarrierID;
        $respo = $daffny->DB->query($sql);
        $carrierD = mysqli_fetch_assoc($respo);
        
        if ($carrierD['print_check'] == 1) {
            $printName = ucfirst($carrierD['print_name']);
            $company_name = $printName;
            $address1 = $carrierD['print_address1'];
            $address2 = $carrierD['print_address2'];
            $city = $carrierD['print_city'];
            $state = $carrierD['print_state'];
            $zip_code = $carrierD['print_zip_code'];
        } else {
            $printName = ucfirst($carrierD['company_name']);
            $company_name = $printName;
            $address1 = $carrierD['address1'];
            $address2 = $carrierD['address2'];
            $city = $carrierD['city'];
            $state = $carrierD['state'];
            $zip_code = $carrierD['zip_code'];
        }

        $DispatchIds = $InvoiceData[$i]['DispatchIds'];
        $TotalAmount = $InvoiceData[$i]['TotalAmount'];
        $AmountWords = $InvoiceData[$i]['AmountWords'];

        if($i != 0){
            $stabilityHeight = "65px";
        } else {
            $stabilityHeight = "55px";
        }

        $content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">';
        $content .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <title>Untitled Document</title>
        <style>
            *{
                box-sizing:border-box;
            }
            .container {
                width: 670px;
                margin: auto;
                padding: 3px;
            }
            .row {
                clear: left;
                width: 100%;
                display:inline-block;
            }
            input {
                border: none;
                height: 20px;
                width: 100%;
            }
            </style>
        </head>';

        $content .= '<body onload="window.print()">';
        $content .= '<div class="container" style="height:1000px;">';

        $content .= '<div class="row" style="margin-top:'.$stabilityHeight.';">
            <div style="float:right; padding-right:2px;">'.date('m/d/Y').'</div>
        </div>';

        $content .= '<div class="row" style="margin-top:25px;">
            <div style="float:left; padding-left:35px;width:530px;">
                '.str_replace("'"," ",$company_name).'
            </div>
            <div style="float:right;padding-right:30px;">
            &nbsp;&nbsp;&nbsp;'.$TotalAmount.'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <div style="float:left;width:475px; padding-left:10px;">
            '.$AmountWords.'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:20px;">
            <div style="float:left; padding-left:50px;width:450px;">
            '.str_replace("'"," ",$company_name).'
            </div>
        </div>';

        $content .= '<div class="row" style="">
            <div style="float:left; padding-left:50px;width:310px;">
            '.$address1.' '.$address2.'
            </div>
        </div>';

        $content .= '<div class="row" style="">
            <div style="float:left; padding-left:50px;width:310px;">
            '.$city.', '.$state.' '.$zip_code.'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <div style="float:left; padding-left:30px;width:260px;">
            Dispatch ID '.$DispatchIds.'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:100px;">
            <div style="float:left; padding-left:5px;width:450px;">
            '.str_replace("'"," ",$company_name).'
            </div>
            <div style="float:right; padding-right:5px;width:120px;">
            '.date('m/d/Y').'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
                <td width="33%">Date</td>
                <td  width="33%" align="center">Reference</td>
                <td  width="34%"  align="right">Payment</td>
            </tr>
            <tr>
                <td>'.date('m/d/Y').'</td>
                <td align="center">#'.$DispatchIds.'</td>
                <td  align="right">'.$TotalAmount.'</td>
            </tr>
            <tr><td colspan="7">&nbsp;</td></tr>
            </table>
        </div>';
        
        $content .= '<div class="row" style="margin-top:100px;"></div>';

        $content .= '<div class="row" style="margin-top:155px;">
            <div style="float:left; padding-left:5px;width:295px;">
            '.str_replace("'"," ",$company_name).'
            </div>
            <div style="float:right; padding-right:5px;width:120px;">
            '.date('m/d/Y').'
            </div>
        </div>';

        $content .= '<div class="row" style="margin-top:10px;">
            <table width="100%" cellpadding="1" cellspacing="1">
            <tr>
                <td  width="33%">Date</td>
                <td  width="33%" align="center">Reference</td>
        
                <td  width="34%"  align="right">Payment</td>
            </tr>
            <tr>
                <td>'.date('m/d/Y').'</td>
        
                <td align="center">#'.$DispatchIds.'</td>
        
                <td align="right">'.number_format((float) $TotalAmount, 2, ".", ",").'</td>
            </tr>
            <tr><td colspan="7">&nbsp;</td></tr>
            </table>
        </div>';

        $content .= '<div class="row" style="margin-top:110px;"></div>';

        $content .= '</div>';
        $content .= '</body>';

        $inp = $daffny->DB->selectRow('id,value', 'members_type_value', "WHERE member_id='".$_SESSION['member']['id']."' and type='QBPRINT'");
        if (!empty($inp)) {
                       
            $individualAmount = explode(",",$InvoiceData[$i]['Amounts']);
            $amountIndex = 1;

            for($l=0;$l<count($InvoiceData[$i]['InvoiceID']);$l++){
                $sql = "SELECT CheckID FROM Invoices WHERE ID = ".$InvoiceData[$i]['InvoiceID'][$l];
                $checkID = $daffny->DB->query($sql);
                $checkID = mysqli_fetch_assoc($checkID)['CheckID'];
                
                $sql = "UPDATE app_payments_check SET check_number = '".$inp['value']."' WHERE id = ".$checkID;
                $res = $daffny->DB->query($sql);

                $sql = "SELECT check_number,PaymentID FROM app_payments_check WHERE id = ".$checkID;
                $res = $daffny->DB->query($sql);

                $check_number = 0;
                $PaymentID = 0;
                while($r = mysqli_fetch_assoc($res)){
                    $check_number = $r['check_number'];
                    $PaymentID = $r['PaymentID'];
                }

                $sql = "UPDATE app_payments SET `check` = '".$check_number."' WHERE id = ".$PaymentID;
                $res = $daffny->DB->query($sql);

                $entity = new Entity($daffny->DB);
                $entity->load($InvoiceData[$i]['EntityID'][$l]);

                //if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() ) {
		            //$entity->setStatus(Entity::STATUS_DELIVERED);
                //}
                
                // $sql = "UPDATE app_order_header SET status = 7, pre_status = 9 WHERE entityid = ".$_POST['EntityID'];
                // $res = $daffny->DB->query($sql);

                // $sql = "UPDATE app_entities SET status = 7, pre_status = 9 WHERE id = ".$_POST['EntityID'];
                // $res = $daffny->DB->query($sql);

                $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
                $NoteMessage = "<green>Carrier has been paid amount $ ".number_format((float) $individualAmount[$amountIndex], 2, ".", ",")." by Company Check #". $inp['value']." and mailed to ".$printName. " " .$address1. " " . $address2. " " . $city. " " .$state. " " .$zip_code;
                // $NoteMessage = "<green>Carrier has been paid amount $ ".number_format((float) $individualAmount[$amountIndex], 2, ".", ",")." by Company Check #". $inp['value'];
                $sql .= "VALUES( '".$InvoiceData[$i]['EntityID'][$l]."', '".$memberId."','3', '".$NoteMessage."', '1', '1')";
                $res = $daffny->DB->query($sql);
                $amountIndex++;

                //$entity->updateHeaderTable();
            }

            // Updating Start Check Number after printing
            $res = $daffny->DB->update('members_type_value', array('value' => ($inp['value'] + 1)), "id = '".$inp['id']."' ");
        }
    }
    // iterating UI ends

    file_put_contents($fullPath,$content);
    $out = array('URL' => $fileName);

    echo json_encode($out);
    die;
?>

<?php

function GetInvoiceData($daffny){
    $InvoiceIds = implode(",",$_POST['selectedInvoices']);
    $sql = "SELECT CarrierID,EntityID,ID FROM Invoices WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
    $res = $daffny->DB->query($sql);

    $invoiceData = array();
    while($r = mysqli_fetch_assoc($res)){
        $invoiceData[] = $r;
    }

    $dataArray = array();
    $accountIds = array();
    for($i=0;$i<count($invoiceData);$i++){
        $accountIds[]  =  $invoiceData[$i]['CarrierID'];
    }

    $accountIds  = array_unique($accountIds);
    $accountIdsSorted = array();

    foreach($accountIds as $key => $value){
        $accountIdsSorted[] = $value;
    }

    $dataArray= array();
    $j = 0;

    for($i=0; $i<count($accountIdsSorted);$i++){
        $dataArray[$i]['CarrierID'] = $accountIdsSorted[$i];

        for($j=0;$j<count($invoiceData);$j++){
            if($invoiceData[$j]['CarrierID'] == $accountIdsSorted[$i]){
                $dataArray[$i]['EntityData'][$j] = array(
                    'InvoiceID' => $invoiceData[$j]['ID'],
                    'EntityID' => $invoiceData[$j]['EntityID'],
                );
            }
        }
    }

    $invoiceDataArray = array();
    for($i=0;$i<count($dataArray);$i++){
        $totalAmount = 0;
        $dispatchIds = "";

        $invoiceDataArray[$i]['CarrierID'] = $dataArray[$i]['CarrierID'];
        $j=0;
        $invoiceDataArray[$i]['Amounts'] = "";
        foreach($dataArray[$i]['EntityData'] as $key => $value){

            $EntityID = $value['EntityID'];
            $InvoiceID = $value['InvoiceID'];

            $sql = "SELECT `number` FROM app_order_header WHERE `entityid` = ".$EntityID;
            $respo = $daffny->DB->query($sql);
            $Amount = 0;
            
            while($r = mysqli_fetch_assoc($respo)){
                $number = $r['number'];
            }

            $sql = "SELECT `Amount` FROM `Invoices` WHERE `ID` = ".$InvoiceID;
            $respo = $daffny->DB->query($sql);
            while($r = mysqli_fetch_assoc($respo)){
                $Amount = $r['Amount'];
            }
            
            $dispatchIds .= " ".$number." ";

            $invoiceDataArray[$i]['EntityID'][] = $EntityID;
            $invoiceDataArray[$i]['Amounts'] .= ",".$Amount;
            $invoiceDataArray[$i]['DispatchIds'] = $dispatchIds;
            $invoiceDataArray[$i]['InvoiceID'][] = $InvoiceID;

            $totalAmount = $totalAmount + $Amount;

            // update in payments table
            $sql = "SELECT count(*) as `Number` FROM app_payments WHERE `entity_id` = ".$EntityID;
            $PaymentNumber = $daffny->DB->query($sql);
            $PaymentNumber = mysqli_fetch_assoc($PaymentNumber)['Number'];

            $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount,method,entered_by)";
            $sql .= "VALUES( '".$EntityID."', '".($PaymentNumber+1)."', '".date('Y-m-d')."', '1', '3', '".$Amount."','2','".$_SESSION['member']['id']."' )";
            $res = $daffny->DB->query($sql);
            $insertedPayID = $daffny->DB->get_insert_id();

            // inserting in app_payments_check
            $ent = new Entity($daffny->DB);
            $ent->load($EntityID);

            $sql = "SELECT * FROM app_accounts WHERE `id` = ".$ent->carrier_id;
            $respo = $daffny->DB->query($sql);
            $carrierD = mysqli_fetch_assoc($respo);

            if ($carrierD['print_check'] == 1) {
                $printName = ucfirst($carrierD['print_name']);
                $company_name = $printName;
                $address1 = $carrierD['print_address1'];
                $address2 = $carrierD['print_address2'];
                $city = $carrierD['print_city'];
                $state = $carrierD['print_state'];
                $zip_code = $carrierD['print_zip_code'];
            } else {
                $printName = ucfirst($carrierD['company_name']);
                $company_name = $printName;
                $address1 = $carrierD['address1'];
                $address2 = $carrierD['address2'];
                $city = $carrierD['city'];
                $state = $carrierD['state'];
                $zip_code = $carrierD['zip_code'];
            }

            $object = new toWords(number_format((float) $Amount, 2, ".", ""), 'dollars', 'c');
            $values = " '".$EntityID."', '".$_SESSION['member']['id']."', '".$_SESSION['member']['contactname']."', 1, 3, '".$Amount."', '".number_format((float) $Amount, 2, ".", "")."', '".ucwords($object->words)."', '".str_replace("'"," ",$printName)."', '".str_replace("'"," ",$company_name)."', '".$address1."', '".$city."', '".$state."', '".$zip_code."', 0, '".$insertedPayID."' ";

            $sql = "INSERT INTO app_payments_check (entity_id, entered_by, entered_contactname, fromid, toid, amount, amount_format, amount_words, print_name, company_name, address1, city, state, zip_code,Void, PaymentID) VALUES(".$values.") ";
            $res = $daffny->DB->query($sql);
            $insertedCheckID = $daffny->DB->get_insert_id();
            
            $timestamp = date('Y-m-d h:i:s');
            $sql = "UPDATE Invoices SET Deleted = 1, Paid = 1,Void = 0, PaidDate = '".$timestamp."',CheckID = '".$insertedCheckID."', PaymentID = '".$insertedPayID."' WHERE `ID` = ".$InvoiceID." AND Hold =  0";
           
            $daffny->DB->query($sql);
            
            // Sending EMail
            if($_POST['send_carrier_mail'] == 1){
                $entity = new Entity($daffny->DB);

                try{
                    $entity->load($EntityID);
                    $member = $entity->getAssigned();
                    $company = $member->getCompanyProfile();
                    $dispatch = $entity->getDispatchSheet();
                    
                    // preparing email body
                    $emailBody = '<p>'.$company->companyname.' has processed payment for order# '.$entity->number.' in the amount of $ '.number_format((float) $_POST['amount_carrier'], 2, '.', '').' and will be mailed today.</p><p>
                    <br />
                    Thank you,<br />
                    '.$company->companyname.'<br />
                    '.$company->phone_tollfree.'<br />
                    '.$company->fax.'<br />
                    '.$company->email.'</p>';

                    $mail = new FdMailer(true);
                    $mail->isHTML();
                    $mail->Body = $emailBody;
                    $mail->Subject = 'Payment issued for Order# '.$entity->number;
                    $mail->AddAddress($dispatch->carrier_email, $dispatch->carrier_contact_name);
                    if ($_SESSION['member']['parent_id'] == 1) {
                        $mail->setFrom('noreply@freightdragon.com');
                    } else {
                        $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                    }

                    //$mail->send();
                } catch( Exception $e ){
                    print_r($e);
                }
            }

            $j++;
        }
       
        $invoiceDataArray[$i]['TotalAmount'] = $totalAmount;
        $obj = new toWords(number_format((float) $totalAmount, 2, ".", ""), 'dollars', 'c');
        $invoiceDataArray[$i]['AmountWords'] = ucwords($obj->words);
    }
    return $invoiceDataArray;
}

define("MAJOR", 'pounds');
define("MINOR", 'p');
class toWords
{
    public $pounds;
    public $pence;
    public $major;
    public $minor;
    public $words = '';
    public $number;
    public $magind;
    public $units = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine');
    public $teens = array('ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
    public $tens = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
    public $mag = array('', 'thousand', 'million', 'billion', 'trillion');
    
    public function toWords($amount, $major = MAJOR, $minor = MINOR)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->number = number_format($amount, 2);
        list($this->pounds, $this->pence) = explode('.', $this->number);
        $this->words = " $this->major $this->pence $this->minor";
        if ($this->pounds == 0) {
            $this->words = "Zero $this->words";
        } else {
            $groups = explode(',', $this->pounds);
            $groups = array_reverse($groups);
            for ($this->magind = 0; $this->magind < count($groups); $this->magind++) {
                if (($this->magind == 1) && (strpos($this->words, 'hundred') === false) && ($groups[0] != '000')) {
                    $this->words = ' and ' . $this->words;
                }

                $this->words = $this->_build($groups[$this->magind]) . $this->words;
            }
        }
    }
    
    public function _build($n)
    {
        $res = '';
        $na = str_pad("$n", 3, "0", STR_PAD_LEFT);
        if ($na == '000') {
            return '';
        }

        if ($na{0} != 0) {
            $res = ' ' . $this->units[$na{0}] . ' hundred';
        }

        if (($na{1} == '0') && ($na{2} == '0')) {
            return $res . ' ' . $this->mag[$this->magind];
        }

        $res .= $res == '' ? '' : ' and';
        $t = (int) $na{1};
        $u = (int) $na{2};
        switch ($t) {
            case 0:$res .= ' ' . $this->units[$u];
                break;
            case 1:$res .= ' ' . $this->teens[$u];
                break;
            default:$res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u];
                break;
        }
        $res .= ' ' . $this->mag[$this->magind];
        return $res;
    }
}
?>