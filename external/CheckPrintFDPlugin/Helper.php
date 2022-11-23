<?php

require 'ToWord.php';

// fetch invoice Data
function GetInvoiceData($daffny){
    
    $InvoiceIds = implode(",",$_POST['selectedInvoices']);
    $sql = "SELECT CarrierID,EntityID,ID, Amount FROM `Invoices` WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
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
                    'Amount' => $invoiceData[$j]['Amount'],
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

            $entity = new Entity($daffny->DB);
            $entity->load($EntityID);

            $Amount = 0;
            $number = $entity->number;

            $Amount = $value['InvoiceID'];
            
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

            $account = new Account($daffny->DB);
            $account->load($entity->carrier_id);

            if ($account->print_check == 1) {
                $printName = ucfirst($account->print_name);
                $company_name = $printName;
                $address1 = $account->print_address1;
                $address2 = $account->print_address2;
                $city = $account->print_city;
                $state = $account->print_state;
                $zip_code = $account->print_zip_code;
            } else {
                $printName = ucfirst($account->company_name);
                $company_name = $printName;
                $address1 = $account->address1;
                $address2 = $account->address2;
                $city = $account->city;
                $state = $account->state;
                $zip_code = $account->zip_code;
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

