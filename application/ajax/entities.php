<?php

/**
 * entities.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 * 
 * @author Shahrukh
 * @copyright CargoFlare
 */


// loading dependencies
require_once "init.php";
require_once "../../libs/anet/AuthorizeNet.php";
require_once "../../libs/mdsip/mdsip.php";

// checking logged in user memeber ID
$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'ENTITY_STATE':

                    if($_POST['country'] == "US" || $_POST['country'] == "CA"){
                        $canadianStates = [
                            'AB' => 'Alberta',
                            'BC' => 'British Columbia',
                            'MB' => 'Manitoba',
                            'NB' => 'New Brunswick',
                            'NL' => 'Newfoundland',
                            'NT' => 'Northwest Territories',
                            'NS' => 'Nova Scotia',
                            'NU' => 'Nunavut',
                            'ON' => 'Ontario',
                            'PE' => 'Prince Edward Island',
                            'QC' => 'Quebec',
                            'SK' => 'Saskatchewan',
                            'YT' => 'Yukon',
                        ];
                        $html = '
                            <label for="'.$_POST['target'].'_state"><span class="text-danger">*</span>State/Zip:</label>
                            <select style="width:180px;" tabindex="15" name="'.$_POST['target'].'_state" class="form-box-combobox" id="'.$_POST['target'].'_state">
                            <option value="">Select One</option>
                            ';

                        $result = $daffny->DB->selectRows("code, name", "states", "ORDER BY name", "code");
                        $html .= '<optgroup label="United States">';
                        foreach ($result as $code => $values) {
                            $html .= '<option value="'.$code.'">'.$values['name'].'</option>';
                        }
                        $html .= '</optgroup>';
                        $html .= '<optgroup label="Canada">';
                        foreach ($canadianStates as $code => $values) {
                            $html .= '<option value="'.$code.'">'.$values.'</option>';
                        }
                        $html .= '</optgroup>';
                        $html .='</select>';
                    } else {
                        $html = '
                            <label for="'.$_POST['target'].'_state"><span class="text-danger"></span>State/Zip:</label>
                            <input style="width:180px;" tabindex="15" name="'.$_POST['target'].'_state" class="form-box-textfield form-control" id="'.$_POST['target'].'_state"/>
                        ';
                    }
                    
                    echo json_encode($html);
                    die;
                    break;
                case 'CONFIRM_ORDER':
                    $id = $_POST['entity_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load($id);

                    $entity->update(['status'=>1,'pre_status'=>99]);
                    $entity->updateHeaderTable();

                    $out = [
                        'success' => true
                    ];

                    echo json_encode($out);die;
                break;
                case 'UPDATE_PENDING_DISPATCH':
                    
                    $parentId = $_POST['parent'];
                    $query = "SELECT *, NOW() as `now` FROM `app_pending_dispatches` WHERE `parent_id` = '".$parentId."'";
                    $wallboards = $daffny->DB->query($query);
                    $data = array();

                    $i = 0;
                    while( $r = mysqli_fetch_assoc($wallboards) ){

                        $data[$i]['id'] = $r['id'];
                        $data[$i]['order_id'] = $r['order_id'];
                        $data[$i]['entity_id'] = $r['entity_id'];
                        $data[$i]['parent_id'] = $r['parent_id'];
                        $data[$i]['comment'] = $r['comment'];
                        $data[$i]['carrier_name'] = $r['carrier_name'];
                        $data[$i]['carrier_contact'] = $r['carrier_contact'];
                        $data[$i]['carrier_email'] = $r['carrier_email'];
                        $data[$i]['carrier_phone'] = format_phone_us($r['carrier_phone']);

                        $time  = humanTiming(strtotime( $r['created_at'] ));
                        $start_date = new DateTime($r['created_at']);
                        $since_start = $start_date->diff(new DateTime($r['now']));
                        $data[$i]['created_at'] = str_pad($since_start->h, 2, '0', STR_PAD_LEFT).":".str_pad($since_start->i, 2, '0', STR_PAD_LEFT).":".str_pad($since_start->s, 2, '0', STR_PAD_LEFT);
                        $data[$i]['updated_at'] = $r['updated_at'];
                        $data[$i]['deleted_at'] = $r['deleted_at'];
                        $i++;
                    }
                    echo json_encode($out = array(
                        'success' => true,
                        'data' => $data
                    ));
                    die;

                break;
                case 'REMOVE_PENDING_DISPATCH':

                    $entity = new Entity($daffny->DB);
                    $entity->load(filter_var($_POST['id']));
                    $daffny->DB->query("DELETE FROM app_pending_dispatches WHERE entity_id =".$entity->id);

                    $daffny->DB->query("UPDATE app_entities SET is_pending_dispatch = 0 WHERE id =".$entity->id);
                    
                    $out = array(
                        'success' => true
                    );
                break;
                case 'MARK_PENDING_DISPATCH':

                    $entity = new Entity($daffny->DB);
                    $entity->load(filter_var($_POST['id']));

                    $insert = array(
                        'order_id' => $entity->prefix."-".$entity->number,
                        'entity_id' => filter_var($_POST['id']),
                        'parent_id' => $entity->parentid,
                        'carrier_name' => filter_var($_POST['name']),
                        'carrier_contact' => filter_var($_POST['contact']),
                        'carrier_email' => filter_var($_POST['email']),
                        'carrier_phone' => filter_var($_POST['phone']),
                        'comment' => filter_var($_POST['comment'])
                    );

                    $daffny->DB->insert("app_pending_dispatches", $insert);
                    $daffny->DB->query("UPDATE app_entities SET is_pending_dispatch = 1 WHERE id =".$entity->id);

                    $out = array(
                        'success' => true
                    );
                break;
                
                // billing system
                case 'GetACHInvoice':
                    $startDate = explode("/",$_POST['startDate']);
                    $startDate = $startDate[2]."-".$startDate[0]."-".$startDate[1];
                    $endDate = explode("/",$_POST['endDate']);
                    $endDate = $endDate[2]."-".$endDate[0]."-".$endDate[1];

                    $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices WHERE MaturityDate > '".$startDate."' AND MaturityDate < '".$endDate."' AND Deleted = 0 AND Hold = 0 AND PaymentType = 24 ORDER BY Age DESC";

                    $res = $daffny->DB->query($sql);
                    $ACHInvoices = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $ACHInvoices[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'ACHInvoices' => $ACHInvoices,
                    );
                break;
                case 'GetInvoiceToBePaid':
                    $startDate = explode("/",$_POST['startDate']);
                    $startDate = $startDate[2]."-".$startDate[0]."-".$startDate[1];
                    $endDate = explode("/",$_POST['endDate']);
                    $endDate = $endDate[2]."-".$endDate[0]."-".$endDate[1];

                    $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices WHERE MaturityDate > '".$startDate."' AND MaturityDate < '".$endDate."' AND Deleted = 0 AND Hold = 0 AND PaymentType = 13 ORDER BY Age DESC";
                    $res = $daffny->DB->query($sql);
                    $AgedInvoices = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $AgedInvoices[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'AgedInvoice' => $AgedInvoices,
                    );
                break;
                case 'GetCarrierInvoiceAged':
                    $maturity = $_POST['maturity'];

                    $sql = 'SELECT *, (DATEDIFF(CURRENT_DATE,`CreatedAt`)) as Age FROM `Invoices` WHERE Hold = 0 AND Paid = 0 AND Deleted = 0 AND ( DATEDIFF(CURRENT_DATE,`CreatedAt`) < (CASE WHEN Age = 0 THEN '.$maturity.' ELSE Age END) ) AND PaymentType = 13 ORDER BY Age DESC';

                    $res = $daffny->DB->query($sql);
                    $AgedInvoices = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $AgedInvoices[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'AgedInvoice' => $AgedInvoices,
                    );
                break;
                case 'GetInvoiceData':
                    $searchValue = $_POST['searchValue'];
                    $searchFrom = $_POST['searchFrom'];

                    if ($searchFrom == 1) {
                        $OrderID = explode('-', $searchValue);
                        if (count($OrderID)) {
                            $entity_id = 0;
                            $sql = "SELECT id FROM app_entities WHERE prefix = '".$OrderID[0]."' AND `number` = '".$OrderID[1]."' ";
                            $entity_id = $daffny->DB->query($sql);

                            $entity_id = mysqli_fetch_assoc($entity_id);
                            $entity_id = $entity_id['id'];
                        }
                    } else {
                        $entity_id = $searchValue;
                    }

                    $entity = new Entity($daffny->DB);

                    try {
                        $entity->load($entity_id);

                        $sql = "SELECT count(*) AS `Exists` FROM `Invoices` WHERE OrderID = '".($entity->prefix."-".$entity->number)."'";
                        $exists = $daffny->DB->query($sql);
                        $exists = mysqli_fetch_assoc($exists);
                        $exists = $exists['Exists'];

                        $ds = $entity->getDispatchSheet();

                        $account = new Account($daffny->DB);
                        $account->load($entity->account_id);

                        $out = array(
                            'success' => true,
                            'CarrierName' => $ds->carrier_company_name,
                            'CompanyName' => $ds->address,
                            'AccountID' => $account->id,
                            'AccountName' => $account->first_name.' '.$account->last_name,
                            'OrderID' => $entity->prefix.'-'.$entity->number,
                            'EntityID' => $entity->id,
                            'Amount' => $entity->carrier_pay_stored,
                            'Exists' => $exists['Exists']
                        );
                    } catch (Exception $e) {
                        $out = array(
                            'success' => false,
                            'Message' => 'No Data Found',
                        );
                    }
                break;
                case 'GetInvoiceDataByID':
                    $sql = 'SELECT * FROM `Invoices` WHERE ID = '.$_POST['ID'];
                    $res = $daffny->DB->query($sql);
                    $InvoiceData = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $r['CreatedAt'] = date("m/d/Y", strtotime($r['CreatedAt']));
                        $InvoiceData[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'InvoiceData' => $InvoiceData,
                    ); 
                break;
                case 'PrintACHReceipts':
                    $InvoiceIds = implode(",",$_POST['IDs']);
                    $sql = "SELECT * FROM Invoices WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
                    $res = $daffny->DB->query($sql);
                    
                    $invoiceData = array();
                    while($r = mysqli_fetch_assoc($res)){
                        $invoiceData[] = $r;
                    }

                    $URL = GenerateACHPrintReceipt($invoiceData, $daffny);
                    $out = array('URL' => $URL);

                    $sql = "UPDATE Invoices SET Deleted = 1, Paid = 1 WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
                    $daffny->DB->query($sql);
                break;
                case 'GetInvoiceType':
                    $sql = "SELECT PaymentType FROM Invoices WHERE `ID` = ".$_POST['ID']." AND Hold =  0";
                    $res = $daffny->DB->query($sql);
                    
                    $type = array();
                    while($r = mysqli_fetch_assoc($res)){
                        $type[] = $r;
                    }

                    $out = array('success' => true, 'type' => $type[0]);
                break;
                case 'GenerateCheckPDFs':
                    $InvoiceData = GetInvoiceData($daffny);

                    // create folder if not exists
                    if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
                        mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
                    }

                    // create file
                    $fileName = "Check-Recipts-".date('Y-m-d his').".pdf";
                    $fullPath = ROOT_PATH."uploads/Invoices/Checks/".$fileName;

                    ob_start();
                    $mpdf = new mPDF('utf-8',array(195,300,0,0));
                    $content = "";

                    for($i=0;$i<count($InvoiceData);$i++){
                        
                        // add a page in PDF
                        $mpdf->AddPage();
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
                
                        $content .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">';
                        $content .= '<head>
                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
                        $content .= '<div class="container">';
                
                        $content .= '<div class="row" style="margin-top:-12px; ">
                                <div style="float:left; padding-left:35px;width:480px;">
                                    &nbsp;
                                </div>
                                <div style="float:right; margin-right:-10px;">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.date('m/d/Y').'
                                </div>
                        </div>';
                
                        $content .= '<div class="row" style="margin-top:25px;">
                            <div style="float:left; padding-left:35px;width:480px; ">
                                '.$company_name.'
                            </div>
                            <div style="float:right; margin-right:-14px;">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.number_format((float) $TotalAmount, 2, ".", ",").'
                            </div>
                        </div>';
                
                        $content .= '<div class="row" style="margin-top:10px;">
                            <div style="float:left;width:475px; padding-left:10px;">
                            '.$AmountWords.'
                            </div>
                        </div>';
                
                        $content .= '<div class="row" style="margin-top:20px;">
                            <div style="float:left; padding-left:50px;width:310px;">
                            '.$company_name.'
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
                
                        $content .= '<div class="row" style="margin-top:130px;">
                            <div style="float:left; padding-left:5px;width:380px;">
                            '.$company_name.'
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
                                <td  align="right">'.number_format((float) $TotalAmount, 2, ".", ",").'</td>
                            </tr>
                            <tr><td colspan="7">&nbsp;</td></tr>
                            </table>
                        </div>';
                        
                        $content .= '<div class="row" style="margin-top:100px;"></div>';
                
                        $content .= '<div class="row" style="margin-top:185px;">
                            <div style="float:left; padding-left:5px;width:295px;">
                            '.$company_name.'
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
                            // Updating Start Check Number after printing
                            $daffny->DB->update('members_type_value', array('value' => $_POST['startNumber']), "id = '".$inp['id']."' ");
                        }
                    }

                    $mpdf->WriteHTML($content);
                    // /ob_end_clean();

                    $current_date = date('Y-m-d h:i:s');
                    $sql = "UPDATE Invoices SET Deleted = 1, Paid = 1, PaidDate = '".$current_date."' WHERE `ID` IN (".implode(",",$_POST['selectedInvoices']).") AND Hold =  0";
                    $daffny->DB->query($sql);

                    $res = $mpdf->Output($fullPath);
                    $out = array('URL' => $fileName);
                break;
                case 'RePrintACHReceipts':
                    $sql = "SELECT * FROM Invoices WHERE `ID` = ".$_POST['IDs']." AND Hold =  0";
                    $res = $daffny->DB->query($sql);
                    
                    $invoiceData = array();
                    while($r = mysqli_fetch_assoc($res)){
                        $invoiceData[] = $r;
                    }

                    $URL = GenerateACHPrintReceipt($invoiceData, $daffny);
                    $out = array('URL' => $URL);

                    $sql = "UPDATE Invoices SET Deleted = 1, Paid = 1 WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
                    $daffny->DB->query($sql);
                break;
                case 'VoidInvoice':
                    $sql = "UPDATE app_payments_check SET Void = 1 WHERE PaymentID = ".$_POST['PaymentID'];
                    $res = $daffny->DB->query($sql);

                    $sql = "UPDATE app_payments SET Void = 1, deleted = 1 WHERE id = ".$_POST['PaymentID'];
                    $res = $daffny->DB->query($sql);

                    $sql = "UPDATE Invoices SET Void = 1, Paid = 0, Deleted = 0 WHERE PaymentID = ".$_POST['PaymentID'];
                    $res = $daffny->DB->query($sql);

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['EntityID']);

                    // $sql = "SELECT pre_status FROM app_entities WHERE id = ".$_POST['EntityID'];
                    // $res = $daffny->DB->query($sql);
                    // $preStatus = mysqli_fetch_assoc($res)['pre_status'];
                    
                    // when order status is delivered (i.e. 9) than changing status to issues / pending payments
                    if($entity->status == 9){
                        $sql = "UPDATE app_order_header SET status = 7, pre_status = 9 WHERE entityid = ".$_POST['EntityID'];
                        $res = $daffny->DB->query($sql);
    
                        $sql = "UPDATE app_entities SET status = 7, pre_status = 9 WHERE id = ".$_POST['EntityID'];
                        $res = $daffny->DB->query($sql);
                    }

                    $sql = "SELECT amount_format, check_number FROM app_payments_check WHERE PaymentID = ".$_POST['PaymentID'];
                    $res = $daffny->DB->query($sql);

                    $checkNumber = "";
                    $Amount = "";
                    while($r = mysqli_fetch_assoc($res)){
                        $checkNumber = $r['check_number'];
                        $Amount = $r['amount_format'];
                    }
                    
                    $NoteMessage = "<red>".$_SESSION['member']['contactname']." has been VOIDED amount $ ".$Amount."  Company Check #".$checkNumber;

                    $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
                    $sql .= "VALUES( '".$_POST['EntityID']."', '".$memberId."','3', '".$NoteMessage."', '1', '1')";
                    $res = $daffny->DB->query($sql);

                    $out = array('success'=> true);
                break;
                case 'GetHoldInvoice':
                    $startDate = explode("/",$_POST['startDate']);
                    $startDate = $startDate[2]."-".$startDate[0]."-".$startDate[1];
                    $endDate = explode("/",$_POST['endDate']);
                    $endDate = $endDate[2]."-".$endDate[0]."-".$endDate[1];

                    $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices WHERE MaturityDate > '".$startDate."' AND MaturityDate < '".$endDate."' AND Deleted = 0 AND Hold = 1  ORDER BY Age DESC";

                    $res = $daffny->DB->query($sql);
                    $ACHInvoices = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $ACHInvoices[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'ACHInvoices' => $ACHInvoices,
                    );
                break;
                case 'GET_PAID_INVOICE':
                    $startDate = explode("/",$_POST['startDate']);
                    $startDate = $startDate[2]."-".$startDate[0]."-".$startDate[1];
                    $endDate = explode("/",$_POST['endDate']);
                    $endDate = $endDate[2]."-".$endDate[0]."-".$endDate[1];

                    $sql = "SELECT *, Age as Term FROM Invoices WHERE PaidDate > '".$startDate."' AND PaidDate < '".$endDate."' AND Paid = 1  ORDER BY PaidDate DESC";

                    $res = $daffny->DB->query($sql);
                    $PaidInvoices = array();
                    while ($r = mysqli_fetch_assoc($res)) {
                        $PaidInvoices[] = $r;
                    }

                    $out = array(
                        'success' => true,
                        'Paid' => $PaidInvoices,
                    );
                break;
                case 'VALIDATE_EMPTY_LEDGER':
                    $invoiceID = implode(",", $_POST['InvoiceIDs']);
                    $sql = "SELECT ID, EntityID, OrderID, Amount FROM Invoices WHERE ID IN (".$invoiceID.")";
                    $res = $daffny->DB->query($sql);

                    $InvoiceData = array();
                    $i = 0;
                    while($r = mysqli_fetch_assoc($res)){
                        $InvoiceData[$i]['InvoiceID'] = $r['ID'];
                        $InvoiceData[$i]['EntityID'] = $r['EntityID'];
                        $InvoiceData[$i]['OrderID'] = $r['OrderID'];
                        $InvoiceData[$i]['Amount'] = $r['Amount'];
                        $i++;
                    }

                    $ids = array();
                    for($i=0;$i<count($InvoiceData);$i++){
                        $sql = "SELECT count(*) as `count` FROM app_payments_check WHERE entity_id = '".$InvoiceData[$i]['EntityID']."' AND Void = 0";
                        $res = $daffny->DB->query($sql);
                        $r = mysqli_fetch_assoc($res);

                        if($r['count'] == 0){

                        } else {
                            $ids[$i]['Entity'] = $InvoiceData[$i]['EntityID'];
                            $ids[$i]['OrderID'] = $InvoiceData[$i]['OrderID'];
                            $ids[$i]['InvoiceID'] = $InvoiceData[$i]['InvoiceID'];
                            $entityCount++;
                        }
                    }
                    $out = array(
                        'success' => true,
                        'IDs' => $ids,
                        'Presence' => count($ids)
                    );
                    echo json_encode($out);die;
                break;
                case 'UPDATE_ACH_TXN_ID':
                    if($_POST['InvoiceID'] != ""){
                        $sql = "UPDATE Invoices SET TxnID = '".$_POST['TxnID']."' WHERE ID = ".$_POST['InvoiceID'];
                        $res = $daffny->DB->query($sql);

                        $out = array(
                            'success' => true,
                            'Message' => 'Transaction ID updated successfully!',
                        );
                    } else {
                        $out = array(
                            'success' => true,
                            'Message' => 'Invalid Invoice selected',
                        );
                    }
                break;
                case 'InvoiceCheckClearUnclear':
                    if($_POST['InvoiceID'] != ""){
                        $sql = "UPDATE Invoices SET Clear = ".$_POST['Flag']." WHERE ID = ".$_POST['InvoiceID'];
                        $res = $daffny->DB->query($sql);

                        $out = array(
                            'success' => true,
                            'Message' => 'Operation successfull.',
                        );
                    } else {
                        $out = array(
                            'success' => true,
                            'Message' => 'Invalid Invoice selected',
                        );
                    }
                break;
                case 'REMOVE_PAID_CHECK_ORDERS':
                    $invoiceID = implode(",", $_POST['InvoiceIDs']);
                    $sql = "SELECT ID, EntityID, OrderID, Amount FROM Invoices WHERE ID IN (".$invoiceID.")";
                    $res = $daffny->DB->query($sql);

                    $InvoiceData = array();
                    $i = 0;
                    while($r = mysqli_fetch_assoc($res)){
                        $InvoiceData[$i]['InvoiceID'] = $r['ID'];
                        $InvoiceData[$i]['EntityID'] = $r['EntityID'];
                        $InvoiceData[$i]['OrderID'] = $r['OrderID'];
                        $InvoiceData[$i]['Amount'] = $r['Amount'];
                        $i++;
                    }

                    $toPrintInvoices = array();
                    $skippedInvoices = array();

                    $counter = 0;
                    for($i=0;$i<count($InvoiceData);$i++){
                        $sql = "SELECT count(*) as `count` FROM app_payments_check WHERE entity_id = '".$InvoiceData[$i]['EntityID']."' AND Void = 0";
                        $res = $daffny->DB->query($sql);
                        $r = mysqli_fetch_assoc($res);
                        
                        if($r['count'] == 0){
                            $toPrintInvoices[$counter]['ID'] = $InvoiceData[$i]['InvoiceID'];
                            $toPrintInvoices[$counter]['OrderID'] = $InvoiceData[$i]['OrderID'];
                            $toPrintInvoices[$counter]['EntityID'] = $InvoiceData[$i]['EntityID'];
                            $counter++;
                        } else {
                            $skippedInvoices[] = $InvoiceData[$i]['InvoiceID'];
                        }
                    }
                    
                    $out = array(
                        'success' => true,
                        'toPrint' => $toPrintInvoices,
                        'toSkip' => $skippedInvoices
                    );
                    echo  json_encode($out);
                    die;
                break;

                case "getBalancePaidByCount":
                    $user_ids = json_decode($_POST['users_ids']);

                    $billing = ',COUNT(IF(`balance_paid_by` IN (12, 13, 20, 21, 24),1,NULL)) AS BILLING';
                    $cop = ',COUNT(IF(`balance_paid_by` IN (8, 9),1,NULL)) AS COP ';
                    $cod = ',COUNT(IF(`balance_paid_by` IN (2, 3),1,NULL)) AS COD ';
                    $invoice = ',COUNT(IF(`balance_paid_by` IN (14, 15, 22, 23),1,NULL)) AS INVOICE ';
                    $assigned_cond = "`assigned_id` IN('" . implode("','", $user_ids) . "') " . (count($user_ids) == 0 ? 'OR 1=1' : '') . "";
                    $qryTotal = "select SUM(IF(`balance_paid_by` IN (2, 3, 8, 9), total_tariff_stored-carrier_pay_stored, total_tariff_stored)) as total_amount $billing $cop $cod $invoice from app_entities WHERE `dispatched` >= '" . $_POST['start'] . "' AND `dispatched` <= '" . $_POST['end'] . "' AND `type` = '3' AND `parentid` = " . $_SESSION['parent_id'] . " AND `dispatched` IS NOT NULL AND ($assigned_cond)";
                    $totalInvoiceAmount = $daffny->DB->selectrow($qryTotal);
                    $tInvoice = number_format($totalInvoiceAmount['total_amount'], 2, '.', ',');
                    $cod = $totalInvoiceAmount['COD'];
                    $cop = $totalInvoiceAmount['COP'];
                    $billing = $totalInvoiceAmount['BILLING'];
                    $invoice = $totalInvoiceAmount['INVOICE'];

                    $totalNumber = $cod + $cop + $billing + $invoice;

                    $out = array('success' => true, "cod" => $cod, "cop" => $cop, "billing" => $billing, 'invoice' => $invoice, 'totalNumber' => $totalNumber, 'tInvoice' => $tInvoice);
                break;
                case "getTermMSG":
                    $defaultsettings = $daffny->DB->selectRow("payments_terms_cod, payments_terms_cop, payments_terms_billing, payments_terms_invoice", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");
                    if (($_POST['balance_paid_by'] == 2) || ($_POST['balance_paid_by'] == 3)) {
                        $out = array('success' => true, "terms_condition" => $defaultsettings['payments_terms_cod']);
                    } else if (($_POST['balance_paid_by'] == 8) || ($_POST['balance_paid_by'] == 9)) {
                        $out = array('success' => true, "terms_condition" => $defaultsettings['payments_terms_cop']);
                    } else if (($_POST['balance_paid_by'] == 12) || ($_POST['balance_paid_by'] == 13) || ($_POST['balance_paid_by'] == 20) || ($_POST['balance_paid_by'] == 21)) {
                        $out = array('success' => true, "terms_condition" => $defaultsettings['payments_terms_billing']);
                    } else if (($_POST['balance_paid_by'] == 14) || ($_POST['balance_paid_by'] == 15) || ($_POST['balance_paid_by'] == 22) || ($_POST['balance_paid_by'] == 23)) {
                        $out = array('success' => true, "terms_condition" => $defaultsettings['payments_terms_invoice']);
                    } else {
                        $out = array('success' => true, "terms_condition" => 'No terms condition found.');
                    }

                break;
                case "createbill":
                    $out = array("success" => false, "message" => "Invaid API Action");
                break;
                case "createbillfix":
                    $out = array("success" => true, "message" => "Invaid API Action");
                break;
                case "createPayment":
                    $out = array("success" => true, "msg" => "Invalid API action");
                break;
                case "createInvoice":
                    $out = array("success" => true, "msg" => "Invalid API action");
                break;
                case "fixCreateInvoice":
                    $out = array("success" => true, "msg" => "Invalid API action");
                break;
                case "syncReceivedPayment":
                    $out = array("success" => true, "msg" => "Invalid API action");
                break;
                case "checkEditDispatch":
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);

                    if (($entity->blocked_by == $_SESSION['member_id']) || ((time() - $entity->blocked_time) > 300)) {
                        $out = array("success" => true);
                    } else {
                        $out = array("success" => false);
                    }
                break;
                case "sendConfirmation":
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new FDException("Invalid Entity ID");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->sendOrderConfirmation();
                    $out = array("success" => true);
                break;
                case "sendInvoice":
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new FDException("Invalid Entity ID");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->sendInvoice();
                    $out = array("success" => true);
                break;
                case "sendQuoteForm":
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new FDException("Invalid Entity ID");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->sendQuoteForm();
                    $out = array("success" => true);
                break;
                case "sendOrderForm":
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new FDException("Invalid Entity ID");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->sendOrderForm();
                    $out = array("success" => true);
                break;
                case "sendDispatchSheet":
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new FDException("Invalid Entity ID");
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    $entity->sendDispatchSheet();
                    $out = array("success" => true);
                break;
                case "followup":
                    $quoteIds = explode(",", $_POST['quote_ids']);

                    $followup = new FollowUp($daffny->DB);
                    foreach ($quoteIds as $quoteId) {
                        $followup->setFolowUp($_POST['followup_type'], rawurldecode($_POST['followup_when']), $quoteId);
                    }
                    $out = array('success' => true);
                break;
                case "print_quote":
                    $quote_id = $_POST['quote_id'];
                    $form_id = $_POST['form_id'];

                    $prnt = "";

                    $tmpl = new FormTemplate($daffny->DB);
                    $tmpl->load($form_id);

                    if ($tmpl->owner_id != getParentId()) {
                        $prnt = "Access Denied.";
                    } else {
                        $entity = new Entity($daffny->DB);
                        $entity->load($quote_id);

                        $val = $daffny->DB->selectRow("`name`, `body`", "`app_formtemplates`", "WHERE `id` = '" . $form_id . "'");

                        $params = $daffny->tpl->get_vars($val['body']);
                        $p = array();
                        foreach ($params as $param) {
                            $p[$param] = "";
                        }
                        $params = EmailTemplate::fillParams($entity, $p, EmailTemplate::SEND_TYPE_HTML);
                        $prnt = $daffny->tpl->get_parsed_from_array($val['body'], $params);
                    }
                    $out = array(
                        'success' => true,
                        'printform' => $prnt,
                    );
                break;
                case "print_order":
                    $order_id = $_POST['order_id'];
                    $form_id = $_POST['form_id'];

                    $prnt = "";

                    $tmpl = new FormTemplate($daffny->DB);
                    $tmpl->load($form_id);

                    if ($tmpl->owner_id != getParentId()) {
                        $prnt = "Access Denied.";
                    } else {
                        $entity = new Entity($daffny->DB);
                        $entity->load($order_id);

                        $val = $daffny->DB->selectRow("`name`, `body`", "`app_formtemplates`", "WHERE `id` = '" . $form_id . "'");

                        $params = $daffny->tpl->get_vars($val['body']);
                        $p = array();
                        foreach ($params as $param) {
                            $p[$param] = "";
                        }
                        $params = EmailTemplate::fillParams($entity, $p, EmailTemplate::SEND_TYPE_HTML);
                        $prnt = $daffny->tpl->get_parsed_from_array($val['body'], $params);
                    }
                    $out = array(
                        'success' => true,
                        'printform' => $prnt,
                    );
                break;
                case "update_check":
                    $checkId = $_POST['checkId'];
                    $checkNumber = trim($_POST['checkNumber']);

                    $daffny->DB->update("app_payments_check", array('check_number' => $checkNumber), "id = '" . $checkId . "' ");

                    $out = array(
                        'success' => true,
                    );
                break;
                case "print_check":
                    $order_id = $_POST['order_id'];
                    $prnt = "";

                    $entity = new Entity($daffny->DB);
                    $entity->load($order_id);

                    $member = new Member($daffny->DB);
                    $member->load($_SESSION['member_id']);

                    $carrier = $entity->getCarrier();

                    $check_number = '';
                    $inp = $daffny->DB->selectRow("id,value", "members_type_value", "WHERE member_id='" . $memberId . "' and type='QBPRINT'");
                    if (!empty($inp)) {
                        $check_number = $inp['value'];
                        $check_number++;
                        $daffny->DB->update("members_type_value", array('value' => $check_number), "id = '" . $inp["id"] . "' ");
                    }

                    if (!is_null($carrier)) {

                        $amount = (float) $entity->carrier_pay_stored;
                        $amountFormat = number_format((float) $entity->carrier_pay_stored, 2, ".", ",");
                        $obj = new toWords(number_format((float) $entity->carrier_pay_stored, 2, ".", ""), 'dollars', 'c');
                        $amountWords = $obj->words;
                        $printName = ucfirst($carrier->company_name);
                        if ($carrier->print_name != '') {
                            $printName = ucfirst($carrier->print_name);
                        }

                        $company_name = ucfirst($carrier->company_name);
                        $address1 = $carrier->address1;
                        $city = $carrier->city;
                        $state = $carrier->state;
                        $zip_code = $carrier->zip_code;

                        if ($carrier->print_check == 1) {
                            $printName = ucfirst($carrier->print_name);
                            $address1 = $carrier->print_address1;
                            $city = $carrier->print_city;
                            $state = $carrier->print_state;
                            $zip_code = $carrier->print_zip_code;
                        }
                    }

                    $ins_arr = array(
                        'entity_id' => $entity->id,
                        'entered_by' => $_SESSION['member_id'],
                        'entered_contactname' => $member->contactname,
                        'amount' => $entity->carrier_pay_stored,
                        'amount_format' => $amountFormat,
                        'amount_words' => $amountWords,
                        'check_number' => $check_number,
                        'print_name' => $printName,
                        'company_name' => $company_name,
                        'address1' => $address1,
                        'city' => $city,
                        'state' => $state,
                        'zip_code' => $zip_code,
                    );

                    $daffny->DB->insert("app_payments_check", $ins_arr);

                    $out = array('success' => true,'printform' => $prnt);
                break;
                case "validate_print_check_new":
                    $order_id = $_POST['order_id'];

                    $prnt = "";
                    $success = true;
                    $type = 1;

                    $entity = new Entity($daffny->DB);
                    $entity->load($order_id);

                    if ($entity->carrier_id <= 0) {
                        $prnt = "No carrier currently assigned and can't process your request at this time.";
                        $success = false;
                    } elseif ($entity->Ven_TxnID == '') {
                        $prnt = "Oops we noticed no Bill generated for this order. Please generate one now by Sync button.";
                        $success = false;
                    } else {
                        $inp = $daffny->DB->selectRow("id", "app_payments_check", "WHERE entity_id=" . $entity->id);
                        if (!empty($inp)) {
                            $prnt = "There is payment already recorded for this order. Do you want to create new payment in QuickBook?";
                            $success = false;
                            $type = 2;
                        }
                    }

                    $check_number = '';
                    $inp = $daffny->DB->selectRow("id,value", "members_type_value", "WHERE member_id='" . $memberId . "' and type='QBPRINT'");
                    if (!empty($inp)) {
                        $check_number = $inp['value'];

                        $check_number++;
                    }

                    $out = array(
                        'success' => $success,
                        'printform' => $prnt,
                        'type' => $type,
                        'check_number' => $check_number,
                    );

                break;
                case "print_check_new":
                    $order_id = $_POST['order_id'];
                    $carrier_check_number = $_POST['carrier_check_number'];
                    $prnt = "";
                    $success = true;

                    $entity = new Entity($daffny->DB);
                    $entity->load($order_id);

                    if ($entity->carrier_id <= 0) {
                        $prnt = "No carrier currently assigned and can't process your request at this time.";
                        $success = false;
                    } elseif ($entity->Ven_TxnID == '') {
                        $prnt = "Oops we noticed no Bill generated for this order. Please generate one now by Sync button.";
                        $success = false;
                    } else {

                        $member = new Member($daffny->DB);
                        $member->load($_SESSION['member_id']);

                        $carrier = $entity->getCarrier();

                        $check_number = '';
                        if (trim($carrier_check_number) == '') {
                            $inp = $daffny->DB->selectRow("id,value", "members_type_value", "WHERE member_id='" . $memberId . "' and type='QBPRINT'");
                            if (!empty($inp)) {
                                $check_number = $inp['value'];

                                $check_number++;
                                $daffny->DB->update("members_type_value", array('value' => $check_number), "id = '" . $inp["id"] . "' ");

                                if (!is_null($carrier)) {

                                    $amount = (float) $entity->carrier_pay_stored;
                                    $amountFormat = number_format((float) $entity->carrier_pay_stored, 2, ".", ",");
                                    $obj = new toWords(number_format((float) $entity->carrier_pay_stored, 2, ".", ""), 'dollars', 'c');
                                    $amountWords = $obj->words;
                                    $printName = ucfirst($carrier->company_name);
                                    if ($carrier->print_name != '') {
                                        $printName = ucfirst($carrier->print_name);
                                    }

                                    $company_name = ucfirst($carrier->company_name);
                                    $address1 = $carrier->address1;
                                    $city = $carrier->city;
                                    $state = $carrier->state;
                                    $zip_code = $carrier->zip_code;

                                    if ($carrier->print_check == 1) {
                                        $printName = ucfirst($carrier->print_name);
                                        $address1 = $carrier->print_address1;
                                        $city = $carrier->print_city;
                                        $state = $carrier->print_state;
                                        $zip_code = $carrier->print_zip_code;
                                    }
                                }
                                $ins_arr = array(
                                    'entity_id' => $entity->id,
                                    'entered_by' => $_SESSION['member_id'],
                                    'entered_contactname' => $member->contactname,
                                    'amount' => $entity->carrier_pay_stored,
                                    'amount_format' => $amountFormat,
                                    'amount_words' => $amountWords,
                                    'check_number' => $check_number,
                                    'print_name' => $printName,
                                    'company_name' => $company_name,
                                    'address1' => $address1,
                                    'city' => $city,
                                    'state' => $state,
                                    'zip_code' => $zip_code,
                                );

                                $daffny->DB->insert("app_payments_check", $ins_arr);
                            }
                        } else {
                            $check_number = $carrier_check_number;

                            if (!is_null($carrier)) {

                                $amount = (float) $entity->carrier_pay_stored;
                                $amountFormat = number_format((float) $entity->carrier_pay_stored, 2, ".", ",");
                                $obj = new toWords(number_format((float) $entity->carrier_pay_stored, 2, ".", ""), 'dollars', 'c');
                                $amountWords = $obj->words;
                                $printName = ucfirst($carrier->company_name);
                                if ($carrier->print_name != '') {
                                    $printName = ucfirst($carrier->print_name);
                                }

                                $company_name = ucfirst($carrier->company_name);
                                $address1 = $carrier->address1;
                                $city = $carrier->city;
                                $state = $carrier->state;
                                $zip_code = $carrier->zip_code;

                                if ($carrier->print_check == 1) {
                                    $address1 = $carrier->print_address1;
                                    $city = $carrier->print_city;
                                    $state = $carrier->print_state;
                                    $zip_code = $carrier->print_zip_code;
                                }
                            }
                            $ins_arr = array(
                                'entity_id' => $entity->id,
                                'entered_by' => $_SESSION['member_id'],
                                'entered_contactname' => $member->contactname,
                                'amount' => $entity->carrier_pay_stored,
                                'amount_format' => $amountFormat,
                                'amount_words' => $amountWords,
                                'check_number' => $check_number,
                                'print_name' => $printName,
                                'company_name' => $company_name,
                                'address1' => $address1,
                                'city' => $city,
                                'state' => $state,
                                'zip_code' => $zip_code,
                            );

                            $daffny->DB->insert("app_payments_check", $ins_arr);

                            $inp = $daffny->DB->selectRow("id,value", "members_type_value", "WHERE member_id='" . $memberId . "' and type='QBPRINT'");
                            if (!empty($inp)) {
                                $daffny->DB->update("members_type_value", array('value' => $check_number), "id = '" . $inp["id"] . "' ");
                            }
                        }
                    }
                    $out = array('success' => $success,'printform' => $prnt);
                break;
                case "changeStatus":
                    $entityManager = new entityManager($daffny->DB);
                    $entity_ids = explode(",", $_POST['entity_ids']);

                    $dispatchSheet = new DispatchSheet($daffny->DB);
                    $dispatchManager = new DispatchSheetManager($daffny->DB);

                    $postStatus = (int) $_POST['status'];

                    foreach ($entity_ids as $key => $value) {
                        $value = trim($value);
                        if ($_POST['status'] == Entity::STATUS_ARCHIVED) {
                            $inp = $daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $value . "' AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL");
                            if (!empty($inp)) {
                                $ds = $dispatchManager->getDispatchSheetByOrderId($value);
                                if (!is_null($ds) || $ds > 0) {
                                    $dispatchSheet->load($ds);
                                    $dispatchSheet->reject();
                                }
                            }
                        }

                        $entity = new Entity($daffny->DB);
                        $entity->load($value);

                        if (Entity::STATUS_ARCHIVED == $entity->status) {
                            if ($_POST['status'] == Entity::STATUS_ACTIVE) {
                                $NotesStr = " has uncancelled this order.";
                            }

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $value, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }

                        if ($entity->status == 2 && $entity->type == 1) {
                            if ($entity->pre_status > 0) {
                                $postStatus = $entity->pre_status;
                            }
                        }

                        $entityManager->changeStatusNew($value, $postStatus);
                    }

                    foreach ($entity_ids as $key => $value) {
                        $NotesStr = "";
                        if (Entity::STATUS_ONHOLD == (int) $_POST['status']) {
                            $NotesStr = " has put this order on hold.";
                        } elseif (Entity::STATUS_ARCHIVED == (int) $_POST['status']) {
                            $NotesStr = " has cancelled this order";
                        }

                        if ($NotesStr != "") {
                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $value, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }
                    }

                    $out = array('success' => true);
                break;
                // for cancellation of order with reason
                case "CancelThisOrder":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);

                    if (Entity::STATUS_ARCHIVED == $entity->status) {
                        $NotesStr = "";
                        if ($_POST['status'] == Entity::STATUS_ACTIVE){
                            $NotesStr = " has uncancelled this order.";
                        }

                        // Create Internal Notes
                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                    }

                    // if order is posted before cancelling send central dispatch removal email
                    if (Entity::STATUS_POSTED == $entity->status) {
                        sendDispatchMail($entity, $daffny, 4);
                    }

                    $entity->update(array('is_pending_dispatch' => 0,'archived'=> date('Y-m-d h:i:s'),'status'=>Entity::STATUS_ARCHIVED,'cancel_reason'=>$_POST['cancel_reason']));

                    $fields="status=3,archived='".date("Y-m-d h:i:s")."',dispatched='NULL',not_signed='NULL',cancel_reason='".$_POST['cancel_reason']."'";
                    $sql = "UPDATE app_order_header SET ".$fields." WHERE entityid = ".$entity->id;

                    $daffny->DB->query($sql);
                    $daffny->DB->query("DELETE FROM app_pending_dispatches WHERE entity_id = ".$entity->id);

                    // fetching sales email
                    $email = $entity->getAssigned()->getCompanyProfile()->email;
                    if($email != ""){
                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Subject =  $_SESSION['member']['contactname']." has cancelled order ".$entity->prefix."-".$entity->number." at ".date("Y-m-d h:i:s");
                        $mail->Body = "Dear Admin <br> User ".$_SESSION['member']['contactname']." has cancelled order ".$entity->prefix."-".$entity->number." at ".date("Y-m-d h:i:s")." <br> Reason: ".$_POST['cancel_reason'];
                        $mail->AddAddress($email);
                        //$mail->AddAddress('shahrukhusmaani@live.com');
                        $mail->SetFrom('noreply@transportmasters.net');
                        $mail->Send();
                    }

                    $NotesStr = "";
                    if (Entity::STATUS_ONHOLD == (int) $_POST['status']) {
                        $NotesStr = " has put this order on hold.";
                    } elseif (Entity::STATUS_POSTED == (int) $_POST['status']) {
                        $NotesStr = " has posted this order onto freight board(s).";
                        $daffny->DB->query("CALL fd_matching_carrier_queue_posted('" . $entity->id . "',100,1)");
                    } elseif (Entity::STATUS_ARCHIVED == (int) $_POST['status']) {
                        $NotesStr = " has canceled this order because :".$_POST['cancel_reason'];
                    }

                    if ($NotesStr != "") {
                        // Create Internal Notes
                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                    }

                    $out = array('success' => true);
                break;
                // for cancelling bulk order with reason
                case "CancelTheseOrders":
                    $entityManager = new entityManager($daffny->DB);
                    $entity_ids = explode(",", $_POST['entity_ids']);

                    $dispatchSheet = new DispatchSheet($daffny->DB);
                    $dispatchManager = new DispatchSheetManager($daffny->DB);

                    $postStatus = (int) $_POST['status'];
                    
                    $orderCount = 0;
                    foreach ($entity_ids as $key => $value) {
                        
                        $value = trim($value);
                        if ($_POST['status'] == Entity::STATUS_ARCHIVED) {
                            $inp = $daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $value . "' AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL");
                            if (!empty($inp)) {
                                $ds = $dispatchManager->getDispatchSheetByOrderId($value);
                                if (!is_null($ds) || $ds > 0) {
                                    $dispatchSheet->load($ds);
                                    $dispatchSheet->reject();
                                }
                            }
                        }

                        $entity = new Entity($daffny->DB);
                        $entity->load($value);

                        if (Entity::STATUS_ARCHIVED == $entity->status) {
                            if ($_POST['status'] == Entity::STATUS_ACTIVE)
                                $NotesStr = " has uncancelled this order.";

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $value, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }

                        //if order is posted before cancelling send central dispatch removal email
                        if (Entity::STATUS_POSTED == $entity->status) {
                            sendDispatchMail($entity, $daffny, 4);
                        }

                        if ($entity->status == 2 && $entity->type == 1)
                            if ($entity->pre_status > 0)
                                $postStatus = $entity->pre_status;
                        
                        $entityManager->CancelThisOrder($value, $postStatus, $_POST['cancel_reason']);
                        $orderIds .= $entity->prefix."-".$entity->number.", ";
                        $orderCount++;
                    }

                    // fetching sales email
                    $email = $entity->getAssigned()->getCompanyProfile()->email;
                    if($email != ""){
                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Subject =  $_SESSION['member']['contactname']." has cancelled (".$orderCount.") orders ";
                        $mail->Body = "Dear Admin <br> User ".$_SESSION['member']['contactname']." has cancelled order ".$orderIds." at ".date("Y-m-d h:i:s")." <br> Reason: ".$_POST['cancel_reason'];
                        $mail->AddAddress($email);
                        //$mail->AddAddress('shahrukhusmaani@live.com');
                        $mail->SetFrom('noreply@transportmasters.net');
                        $mail->Send();
                    }

                    foreach ($entity_ids as $key => $value) {
                        
                        $NotesStr = "";
                        if (Entity::STATUS_ONHOLD == (int) $_POST['status']) {
                            $NotesStr = " has put this order on hold.";
                        } elseif (Entity::STATUS_ARCHIVED == (int) $_POST['status']) {
							$NotesStr = $_SESSION['member']['contactname']. " has cancelled this order because :".$_POST['cancel_reason'];
                        }

                        if ($NotesStr != "") {
                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $value, 'text' => $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }
                    }

                    $out = array('success' => true);
                break;
                case "reassign":
                    try{
                        if (isset($_POST['entity_ids'])) {
                            $new_assigned = new Member($daffny->DB);
                            $new_assigned->load($_POST['assign_id']);
                            $email_sub = "";
                            foreach (explode(",", $_POST['entity_ids']) as $entity_id) {
                                $entity = new Entity($daffny->DB);
                                $entity->load($entity_id);

                                $name = $entity->getAssigned()->contactname;

                                $typeValue = "Order";

                                if ($entity->type == 1 || $entity->type == 4) {
                                    $typeValue = "Lead";
                                } elseif ($entity->type == 2) {
                                    $typeValue = "Quote";
                                }

                                // Create Internal Notes
                                $note = new Note($daffny->DB);
                                $note->create(array('entity_id' => $entity->id, 'text' => $typeValue . " assigned from User " . $name . " to " . $new_assigned->contactname, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                                // Create Internal Notes
                                $note = new Note($daffny->DB);
                                $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . " has reassigned this " . $typeValue . " to " . $new_assigned->contactname, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                                $entity->assign($_POST['assign_id'], true);

                                $strEmailMessage .= $_SESSION['member']['contactname'] . ' has assigned ' . $typeValue . ' Id  <b>' . $entity->getNumber() . '</b> to your account.<br><br>';

                                $entity->updateHeaderTable();
                                $email_sub = $typeValue;

                                $smtp_from_email = $entity->getAssigned()->getDefaultSettings()->smtp_from_email;
                            }

                            $mail = new FdMailer(true);
                            $mail->isHTML();
                            $mail->Body = $strEmailMessage;
                            $mail->Subject = 'New ' . $email_sub . ' assigned to you';
                            if ($entity->getAssigned()->parent_id == 1) {
                                $mail->AddAddress($new_assigned->email, $new_assigned->contactname);
                            } else {
                                $mail->AddAddress('junk@freightdragon.com');
                            }

                            if ($_SESSION['member']['parent_id'] == 1) {
                                $mail->setFrom('noreply@freightdragon.com');
                            } else {
                                $mail->setFrom($smtp_from_email);
                            }
                            $mail->send();

                        } else {
                            $entity = new Entity($daffny->DB);
                            $entity->load($_POST['entity_id']);
                            $typeValue = "Order";

                            if ($entity->type == 1 || $entity->type == 4) {
                                $typeValue = "Lead";
                            } elseif ($entity->type == 2) {
                                $typeValue = "Quote";
                            }

                            $name = $entity->getAssigned()->contactname;

                            $new_assigned = new Member($daffny->DB);
                            $new_assigned->load($_POST['assign_id']);

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $entity->id, 'text' => $typeValue . " assigned from User " . $name . " to " . $new_assigned->contactname, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                            $entity->assign($_POST['assign_id'], true);

                            $entity->updateHeaderTable();

                            $strEmailMessage = $_SESSION['member']['contactname'] . ' has assigned ' . $typeValue . ' ID ' . $entity->getNumber() . ' to your account.';

                            $mail = new FdMailer(true);
                            $mail->isHTML();
                            $mail->Body = $typeValue . ' ' . $entity->number . ' has just been assigned to you... get on it!';
                            $mail->Subject = $strEmailMessage;
                            
                            if ($entity->getAssigned()->parent_id == 1) {
                                $mail->AddAddress($new_assigned->email, $new_assigned->contactname);
                            } else {
                                $mail->AddAddress('junk@freightdragon.com');
                            }

                            if ($entity->getAssigned()->parent_id == 1) {
                                $mail->setFrom('noreply@freightdragon.com');
                            } else {
                                $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                            }

                            $mail->send();
                        }
                    } catch(Exception $e) {
                        die($e->getMessage());
                    }

                    $out = array('success' => true);
                break;
                case "setappointment":

                    if (isset($_POST['entity_ids'])) {

                        $email_sub = "";
                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {

                            $entity = new Entity($daffny->DB);
                            $entity->load($entity_id);
                            $reminder_time = $_POST['app_time'];
                            $timeArrAM_PM = explode("_", $reminder_time);
                            $AM_PM = $timeArrAM_PM[1];
                            $timeArr = explode(".", $timeArrAM_PM[0]);
                            $time = $timeArr[0];
                            if ($timeArr[0] == 0) {
                                $time = "00";
                            }

                            if ($timeArr[1] == 0) {
                                $time .= ":00:00";
                            } else {
                                $time .= ":" . $timeArr[1] . ":00";
                            }

                            $reminder_date = date("Y-m-d", strtotime(rawurldecode($_POST['app_date']))) . " " . $time;

                            $typeLead = 1;
                            if ($entity->type == ENTITY::TYPE_CLEAD) {
                                $typeLead = ENTITY::TYPE_CLEAD;
                            }

                            $followup = new FollowUp($daffny->DB);
                            $insert_arr = array(
                                'type' => $typeLead,
                                'created' => date("Y-m-d H:i:s"),
                                'followup' => $reminder_date,
                                'entity_id' => (int) $entity_id,
                                'app_time' => $_POST['app_time'],
                                'notes' => $_POST['notes'],
                            );
                            if (is_null($sender_id)) {
                                $insert_arr['sender_id'] = $_SESSION['member_id'];
                            }
                            $followup->create($insert_arr);

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $entity->id, 'text' => $typeValue . " assigned from User " . $name . " to " . $new_assigned->contactname, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                            $email_sub = $typeValue;
                            if ($entity->type == ENTITY::TYPE_CLEAD) {
                                $entity->setStatus(ENTITY::STATUS_CAPPOINMENT);
                            } else {
                                $entity->setStatus(ENTITY::STATUS_LAPPOINMENT);
                            }

                            $entity->updateHeaderTable();
                        }
                    }
                    $out = array('success' => true);
                break;
                case "getAppointments":

                    $rows = $daffny->DB->selectRows("*", "app_followups", "WHERE `sender_id` = " . (int) $memberId . " and now() >= `followup` And app_time!=''");

                    $data = array();

                    foreach ($rows as $row) {
                        $data[] = array('id' => $row['id'], 'entity' => $row['entity_id'], 'app_date' => $row['followup'], 'app_time' => $row['app_time'], 'app_notes' => $row['notes']);
                    }

                    $out = array('success' => true, 'data' => $data);
                break;
                case "appointmentCompleted":
                    if ($_POST["id"] == "") {
                        return;
                    }

                    $update_arr = array(
                        'status' => 1,
                    );

                    $daffny->DB->update("app_followups", $update_arr, "id = '" . $_POST["id"] . "' ");
                    if ($_POST["entity_id"] != "" && $_POST["notes"] != "") {

                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $_POST["entity_id"], 'text' => $_POST["notes"], 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                    }

                    $out = array('success' => true);
                break;
                case "appointmentSnooze":
                    if ($_POST["id"] == "") {
                        return;
                    }

                    $daffny->DB->query("update app_followups set snooze=1 , snooze_date=DATE_ADD( NOW( ) , INTERVAL 15 MINUTE ) where id = '" . $_POST["id"] . "'");

                    $out = array('success' => true);
                break;
                case "rePostToCD":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    
                    sendDispatchMail($entity, $daffny, 1);
                    //$entity->postToCentralDispatch(0);
                    $out = array('success' => true);
                break;
                case "setStatus":
                    try{
                        $entity = new Entity($daffny->DB);
                        $entity->load($_POST['entity_id']);

                        if (Entity::STATUS_ARCHIVED == $entity->status) {
                            $NotesStr = "";
                            if ($_POST['status'] == Entity::STATUS_ACTIVE) {
                                $NotesStr = " has uncancelled this order.";
                            }

                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }
                        $entity->setStatus((int) $_POST['status'], true);

                        $NotesStr = "";
                        if (Entity::STATUS_ONHOLD == (int) $_POST['status']) {
                            $NotesStr = " has put this order on hold.";
                        } elseif (Entity::STATUS_POSTED == (int) $_POST['status']) {
                            $NotesStr = " has posted this order onto freight board(s).";
                            //$daffny->DB->query("CALL fd_matching_carrier_queue_posted('" . $entity->id . "',100,1)");
                            sendDispatchMail($entity, $daffny, 5);
                        } elseif (Entity::STATUS_ARCHIVED == (int) $_POST['status']) {
                            $NotesStr = " has cancelled this order.";
                            if ($_SESSION['member']['parent_id'] == 1) {
                                if ($entity->TxnID != '' && $entity->EditSequence != '') {
                                    $where = "  entity_id = " . (int) $entity->id . " and fromid='2' and toid='1' and TxnID!='' AND `deleted` = 0";
                                    $paymentsArr = $daffny->DB->selectRows('id', "app_payments", "WHERE " . $where);
                                }
                            }
                        }
                        if ($NotesStr != "") {
                            // Create Internal Notes
                            $note = new Note($daffny->DB);
                            $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        }

                        $out = array('success' => true);
                    } catch(Exception $e) {
                        $out = array('success' => false, 'message' => $e->getMessage());
                    }
                    
                break;
                case "setStatusAndDate":
                    $updateDate = $_POST['pickdate'] . " " . date('H:i:s');
                    
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);

                    if ($entity->readonly) {
                        break;
                    }

                    try{
                        $entity->setStatusAndDate((int) $_POST['status'], $updateDate, true);
                    } catch(Exception $e) {
                        die($e->getMessage());
                    }
                    
                    $updateDate = $_POST['pickdate'];

                    $NotesStr = "";
                    if (Entity::STATUS_PICKEDUP == (int) $_POST['status']) {
                        $NotesStr = " marked this order as PICKED UP.".$updateDate;
                    } elseif (Entity::STATUS_DELIVERED == (int) $_POST['status']) {
                        $NotesStr = " marked this order as DELIVERED. : " . $updateDate;
                    }

                    if ($NotesStr != "") {
                        // Create Internal Notes
                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                    }

                    $out = array('success' => true);
                break;
                case "setStatusAndDateMultiple":

                    $updateDate = $_POST['pickdate'] . " " . date('H:i:s');
                    if (isset($_POST['entity_ids'])) {

                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {

                            $entity = new Entity($daffny->DB);
                            $entity->load($entity_id);
                            if ($entity->readonly) {
                                break;
                            }

                            $entity->setStatusAndDate((int) $_POST['status'], $updateDate, true);

                            $NotesStr = "";
                            if (Entity::STATUS_PICKEDUP == (int) $_POST['status']) {
                                $NotesStr = " marked this order as PICKED UP.";
                            } elseif (Entity::STATUS_DELIVERED == (int) $_POST['status']) {
                                $NotesStr = " marked this order as DELIVERED.";
                            }

                            if ($NotesStr != "") {
                                // Create Internal Notes
                                $note = new Note($daffny->DB);
                                $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                            }
                        } //for

                        $out = array('success' => true);
                    } // validation

                break;
                case "setStatusMultiple":
                    if (isset($_POST['entity_ids'])) {

                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {

                            $entity = new Entity($daffny->DB);
                            $entity->load($entity_id);
                            $entity->setStatus((int) $_POST['status'], true);

                            $NotesStr = "";
                            if (Entity::STATUS_ONHOLD == (int) $_POST['status']) {
                                $NotesStr = " has put this order on hold.";
                            } elseif (Entity::STATUS_POSTED == (int) $_POST['status']) {
                                $NotesStr = " has posted this order onto freight board(s).";
                                sendDispatchMail($entity, $daffny, 5);
                                //$daffny->DB->query("CALL fd_matching_carrier_queue_posted('" . $entity->id . "',100,1)");
                            } elseif (Entity::STATUS_ARCHIVED == (int) $_POST['status']) {
                                $NotesStr = " has cancelled this order";

                                if ($_SESSION['member']['parent_id'] == 1) {
                                    if ($entity->TxnID != '' && $entity->EditSequence != '') {
                                        $where = "  entity_id = " . (int) $entity->id . " and fromid='2' and toid='1' and TxnID!='' AND `deleted` = 0";
                                        $paymentsArr = $daffny->DB->selectRows('id', "app_payments", "WHERE " . $where);
                                    }
                                }
                            } elseif (Entity::STATUS_ARCHIVED == $entity->status) {

                                if ($_POST['status'] == Entity::STATUS_ACTIVE) {
                                    $NotesStr = " has uncancelled this order.";
                                }

                            }
                            if ($NotesStr != "") {
                                // Create Internal Notes
                                $note = new Note($daffny->DB);
                                $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $memberId, "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                            }
                        } //for

                        $out = array('success' => true);
                    } // validation

                break;
                case "split":
                    
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly) {
                        break;
                    }

                    if(in_array($entity->status, [5,6,8])){
                        $new_id = $entity->splitDispatched(explode(",", $_POST['vehicle_ids']));
                    } else {
                        $new_id = $entity->split(explode(",", $_POST['vehicle_ids']));
                    }
                    
                    if ($new_id > 0) {
                        
                        $accountShipper = $entity->getAccount();

                        $entity_new = new Entity($daffny->DB);
                        $entity_new->load($new_id);
                        if ($entity->esigned == 2 || $entity->esigned == 1) { //
                            if ($entity->esigned == 2) {
                                $files = $entity->getCommercialFilesShipper($accountShipper->id);
                            } else {
                                $files = $entity->getFiles($entity->id);
                            }

                            if (isset($files) && count($files)) {
                                foreach ($files as $file) {

                                    $str = "B2B";
                                    if ($entity->esigned == 2) {
                                        $pos = strpos($file['name_original'], "B2B");
                                    } else {
                                        $pos = strpos($file['name_original'], "Signed");
                                        $str = "Signed";
                                    }

                                    if ($pos === false) {

                                    } else {
                                        $fname = md5(mt_rand() . " " . time() . " " . $entity_new->id);
                                        $path = ROOT_PATH . "uploads/entity/" . $fname;
                                        if ($entity->esigned == 2) {
                                            $uploadpath = ROOT_PATH . "uploads/accounts/" . $file['name_on_server'];
                                        } else {
                                            $uploadpath = ROOT_PATH . "uploads/entity/" . $file['name_on_server'];
                                        }

                                        if (copy($uploadpath, $path)) {
                                            $ins_arr = array(
                                                'name_original' => $str . " Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                                'name_on_server' => $fname,
                                                'size' => filesize($uploadpath),
                                                'type' => "pdf",
                                                'date_uploaded' => date("Y-m-d H:i:s"),
                                                'owner_id' => $entity->getAssigned()->parent_id,
                                                'status' => 0,
                                                'esigned' => $entity->esigned,
                                            );

                                            $daffny->DB->insert("app_uploads", $ins_arr);
                                            $ins_id = $daffny->DB->get_insert_id();

                                            $daffny->DB->insert("app_entity_uploads", array("entity_id" => $entity_new->id, "upload_id" => $ins_id));
                                            // Update Entity
                                            $update_arr = array(
                                                'esigned' => $entity->esigned,
                                            );
                                            $entity_new->update($update_arr);
                                        }
                                    }
                                }
                            }
                        }
                    } // new entity_id check

                    $entity_new->updateHeaderTable();

                    $entity->updateHeaderTable();
                    if ($new_id > 0) {
                        $out = array('success' => true, "data" => $new_id);
                    } else {
                        $out = array('success' => false);
                    }
                    // $out = array('success' => false);
                break;
                case 'setDispatchStatus':
                    if (!isset($_POST['id'])) {
                        throw new FDException("Invalid Dispatch ID");
                    }

                    if (!isset($_POST['status']) || !in_array($_POST['status'], array('pickedup', 'delivered'))) {
                        throw new FDException("Invalid Status");
                    }

                    $dispatchSheet = new DispatchSheet($daffny->DB);
                    $dispatchSheet->load($_POST['id']);
                    $entity = $dispatchSheet->getOrder();
                    $newStatus = null;
                    switch ($_POST['status']) {
                        case 'pickedup':
                            $newStatus = Entity::STATUS_PICKEDUP;
                            break;
                        case 'delivered':
                            $newStatus = Entity::STATUS_DELIVERED;
                            break;
                    }
                    $entity->setStatus($newStatus);
                    $out = array('success' => true);
                break;
                case 'sendDispatchLink':
                    if (!isset($_POST['id'])) {
                        throw new FDException("Invalid Dispatch ID");
                    }
                    $dispatchSheet = new DispatchSheet($daffny->DB);
                    $dispatchSheet->load($_POST['id']);
                    $entity = $dispatchSheet->getOrder();

                    $dispatch_link = BASE_PATH . "order/dispatchnew/hash/" . $dispatchSheet->hash_link;
                    $entity->sendDispatchLink(array("dispatch_link" => $dispatch_link));
                    $out = array('success' => true);
                break;
                case "print":
                    $entity_ids = explode(",", $_POST['entity_ids']);
                    $the_data = array();
                    foreach ($entity_ids as $entity_id) {
                        $data = array();
                        $entity = new Entity($daffny->DB);
                        $entity->load($entity_id);
                        switch ($entity->type) {
                            case Entity::TYPE_LEAD:
                                $data['received'] = $entity->getReceived();
                                break;
                            case Entity::TYPE_QUOTE:
                                $data['quoted'] = $entity->getQuoted();
                                $data['tariff'] = $entity->getTotalTariff() . "<br/>T " . $entity->getCarrierPay() . "<br/>C " . $entity->getTotalDeposit();
                                break;
                            case Entity::TYPE_ORDER:
                                $data['ordered'] = $entity->getOrdered();
                                $data['tariff'] = $entity->getTotalTariff() . "<br/>T " . $entity->getCarrierPay() . "<br/>C " . $entity->getTotalDeposit();
                                break;
                        }
                        $data['id'] = $entity->getNumber();
                        $shipper = $entity->getShipper();
                        $data['shipper'] = $shipper->fname . " " . $shipper->lname . "<br/>" . $shipper->phone1 . "<br/>" . $shipper->email;
                        $vehicles = $entity->getVehicles();
                        $data['vehicle'] = "";
                        foreach ($vehicles as $vehicle) {
                            $data['vehicle'] .= $vehicle->make . " " . $vehicle->model . "<br/>" . $vehicle->year . " " . $vehicle->type . "<br/>";
                        }
                        $origin = $entity->getOrigin();
                        $destination = $entity->getDestination();
                        $data['origin_dest'] = $origin->city . ", " . $origin->state . " / " . $destination->city . ", " . $destination->state;
                        $data['est_ship'] = $entity->getShipDate();
                        $the_data[] = $data;
                    }
                    $out = array('success' => true, 'data' => $the_data);
                break;
                case "toQuote":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->status != Entity::STATUS_ACTIVE) {
                        break;
                    }

                    if ($entity->readonly) {
                        break;
                    }

                    $vehicles = $entity->getVehicles();
                    $break = false;
                    foreach ($vehicles as $vehicle) {
                        if ((float) $vehicle->tariff == 0 || (float) $vehicle->deposit == 0) {
                            $out = array('success' => false, 'reason' => 'You should provide tariff and deposit for all vehicles');
                            $break = true;
                            break;
                        }
                    }
                    if (!$break) {
                        $entity->convertToQuote();
                        $entity->updateHeaderTable();
                        $out = array('success' => true);
                    }
                break;
                case "toOrder":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->status != Entity::STATUS_ACTIVE) {
                        break;
                    }

                    if ($entity->readonly) {
                        break;
                    }

                    $entity->convertToOrder();
                    $entity->updateHeaderTable();
                    $out = array('success' => true);
                break;
                case "toOrderNew":
                    if (isset($_POST['entity_ids'])) {
                        $entity_id_used = 0;
                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($entity_id);
                            if ($entity->status != Entity::STATUS_ACTIVE) {
                                break;
                            }

                            if ($entity->readonly) {
                                break;
                            }

                            $entity->convertToOrder();
                            $entity_id_used = $entity_id;

                            $shipperData = $entity->getShipper();
                            $shipper = new Account($daffny->DB);
                            $shipperArr = array(
                                'owner_id' => $_SESSION['member']['parent_id'],
                                'company_name' => $shipperData->company,
                                'status' => 1,
                                'is_carrier' => 0,
                                'is_shipper' => 1,
                                'is_location' => 0,
                                'first_name' => $shipperData->fname,
                                'last_name' => $shipperData->lname,
                                'email' => $shipperData->email,
                                'phone1' => $shipperData->phone1,
                                'phone2' => $shipperData->phone2,
                                'cell' => $shipperData->mobile,
                                'fax' => $shipperData->fax,
                                'address1' => $shipperData->address1,
                                'address2' => $shipperData->address2,
                                'city' => $shipperData->city,
                                'state' => $shipperData->state,
                                'state_other' => $shipperData->state,
                                'zip_code' => $shipperData->zip,
                                'country' => $shipperData->country,
                                'shipper_type' => $shipperData->shipment_type,
                                'hours_of_operation' => $shipperData->shipper_hours,
                                'referred_by' => $entity->referred_by,
                                'referred_id' => $entity->referred_id,
                            );

                            if ($shipperData->company) {

                                $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->company) . "' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                            } else {

                                $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                            }

                            if (empty($rowShipper)) {

                                $shipper->create($shipperArr);
                                // Update Entity
                                $update_account_id_arr = array(
                                    'account_id' => $shipper->id,
                                );
                                $entity->update($update_account_id_arr);
                            } else {
                                if ($rowShipper["id"] != '' && $shipperData->company != "") {
                                    unset($shipperArr['referred_by']);
                                    unset($shipperArr['referred_id']);
                                    $upd_account_arr = $daffny->DB->PrepareSql("app_accounts", $shipperArr);
                                    $daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                                    // Update Entity
                                    $update_account_id_arr = array(
                                        'account_id' => $rowShipper["id"],
                                    );
                                    $entity->update($update_account_id_arr);
                                }
                            }

                            $entity->updateHeaderTable();
                        }
                        $out = array('success' => true, 'url' => '/application/orders/edit/id/' . $entity_id_used);
                    }

                break;
                case "LeadtoOrderNew":

                    if (isset($_POST['entity_ids'])) {
                        $entity_id_used = 0;
                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {

                            $entity = new Entity($daffny->DB);
                            $entity->load($entity_id);

                            if ($entity->readonly) {
                                break;
                            }

                            $entity->convertLeadToOrder();

                            $entity_id_used = $entity_id;
                            $update_information = array(
                                'information' => "",
                            );

                            $entity->update($update_information);

                            $shipperData = $entity->getShipper();
                            $shipper = new Account($daffny->DB);
                            $shipperArr = array(
                                'owner_id' => $_SESSION['member']['parent_id'],
                                'company_name' => $shipperData->company,
                                'status' => 1,
                                'is_carrier' => 0,
                                'is_shipper' => 1,
                                'is_location' => 0,
                                'first_name' => $shipperData->fname,
                                'last_name' => $shipperData->lname,
                                'email' => $shipperData->email,
                                'phone1' => $shipperData->phone1,
                                'phone2' => $shipperData->phone2,
                                'cell' => $shipperData->mobile,
                                'fax' => $shipperData->fax,
                                'address1' => $shipperData->address1,
                                'address2' => $shipperData->address2,
                                'city' => $shipperData->city,
                                'state' => $shipperData->state,
                                'state_other' => $shipperData->state,
                                'zip_code' => $shipperData->zip,
                                'country' => $shipperData->country,
                                'shipper_type' => $shipperData->shipment_type,
                                'hours_of_operation' => $shipperData->shipper_hours,
                                'referred_by' => $entity->referred_by,
                                'referred_id' => $entity->referred_id,
                            );

                            if ($shipperData->company != '') {

                                $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
									(`company_name` ='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->company) . "' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                            } else {

                                $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
									(`company_name` ='' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                            }

                            if (empty($rowShipper)) {

                                $shipper->create($shipperArr);
                                // Update Entity
                                $update_account_id_arr = array(
                                    'account_id' => $shipper->id,
                                );
                                $entity->update($update_account_id_arr);
                            } else {
                                if ($rowShipper["id"] != '') {
                                    unset($shipperArr['referred_by']);
                                    unset($shipperArr['referred_id']);
                                    $upd_account_arr = $daffny->DB->PrepareSql("app_accounts", $shipperArr);
                                    $daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                                    // Update Entity
                                    $update_account_id_arr = array(
                                        'account_id' => $rowShipper["id"],
                                    );
                                    $entity->update($update_account_id_arr);
                                }
                            }
                            $entity->updateHeaderTable();
                        }

                        $out = array('success' => true, 'url' => '/application/orders/edit/id/' . $entity_id_used);
                    }

                break;
                case "LeadtoOrderCreated":

                    try{
                        if (isset($_POST['entity_ids'])) {
                            $entity_id_used = 0;

                            foreach (explode(",", $_POST['entity_ids']) as $entity_id) {
                                $entity = new Entity($daffny->DB);
                                $entity->load($entity_id);

                                if ($entity->readonly) {
                                    break;
                                }

                                $salesPerson = 0;
                                if($entity->salesrepid){
                                    $salesPerson = $entity->salesrepid;
                                }

                                $carrierId = 0;
                                if($entity->carrier_id){
                                    $carrierId = $entity->carrier_id;
                                }

                                $sql = "INSERT INTO app_lead_tracking (entityid,number,prefix,parent_id,date_converted,creator_id,assigned_id,salesrepid,carrier_id,account_id, type, status) values ('" . $entity->id . "','" . $entity->number . "','" . $entity->prefix . "','" . $entity->parentid . "','" . date("Y-m-d") . "', '" . $entity->creator_id . "','" . $entity->assigned_id . "','" . $salesPerson . "','" . $carrierId . "','" . $entity->account_id . "','" . $entity->type . "','" . $entity->status . "')";
                                $result = $daffny->DB->query($sql);
                                $entity->convertCreatedLeadToOrder();
                                $entity_id_used = $entity_id;
                                $update_information = array(
                                    'information' => "",
                                );

                                $entity->update($update_information);

                                $shipperData = $entity->getShipper();
                                $shipper = new Account($daffny->DB);
                                $shipperArr = array(
                                    'owner_id' => $_SESSION['member']['parent_id'],
                                    'company_name' => $shipperData->company,
                                    'status' => 1,
                                    'is_carrier' => 0,
                                    'is_shipper' => 1,
                                    'is_location' => 0,
                                    'first_name' => $shipperData->fname,
                                    'last_name' => $shipperData->lname,
                                    'email' => $shipperData->email,
                                    'phone1' => $shipperData->phone1,
                                    'phone2' => $shipperData->phone2,
                                    'cell' => $shipperData->mobile,
                                    'fax' => $shipperData->fax,
                                    'address1' => $shipperData->address1,
                                    'address2' => $shipperData->address2,
                                    'city' => $shipperData->city,
                                    'state' => $shipperData->state,
                                    'state_other' => $shipperData->state,
                                    'zip_code' => $shipperData->zip,
                                    'country' => $shipperData->country,
                                    'shipper_type' => $shipperData->shipment_type,
                                    'hours_of_operation' => $shipperData->shipper_hours,
                                    'referred_by' => $entity->referred_by,
                                    'referred_id' => $entity->referred_id,
                                );

                                if ($shipperData->company) {

                                    $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
                                (`company_name` ='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->company) . "' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                                } else {

                                    $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
                                (`company_name` ='' AND state='" . $shipperData->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperData->lname) . "' AND `is_shipper` = 1)");
                                }

                                if (empty($rowShipper)) {

                                    $shipper->create($shipperArr);
                                    // Update Entity
                                    $update_account_id_arr = array(
                                        'account_id' => $shipper->id,
                                    );
                                    $entity->update($update_account_id_arr);
                                } else {
                                    if ($rowShipper["id"] != '' && $shipperData->company != "") {
                                        unset($shipperArr['referred_by']);
                                        unset($shipperArr['referred_id']);
                                        $upd_account_arr = $daffny->DB->PrepareSql("app_accounts", $shipperArr);
                                        $daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                                        // Update Entity
                                        $update_account_id_arr = array(
                                            'account_id' => $rowShipper["id"],
                                        );
                                        $entity->update($update_account_id_arr);
                                    }
                                }
                                $entity->updateHeaderTable();
                            }
                            $out = array('success' => true, 'url' => '/application/orders/edit/id/' . $entity_id_used);
                        }
                    } catch(Exception $e){
                        echo $daffny->DB->errorQuery;die;
                        $out = ['success'=>false, 'message'=>$e->getMessage()];
                    }

                break;
                case "getDispatchData":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    $data = array();
                    $data['entity_number'] = $entity->getNumber();
                    $data['ship_via'] = $entity->ship_via;
                    $price_fb = $entity->total_tariff + $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee;
                    $data['price_fb'] = number_format($price_fb, 2, '.', ',');
                    $data['load_date'] = $entity->getLoadDate("m/d/Y");
                    $data['load_date_type'] = $entity->load_date_type;
                    $data['delivery_date'] = $entity->getDeliveryDate("m/d/Y");
                    $data['delivery_date_type'] = $entity->delivery_date_type;
                    $data['booking_number'] = $entity->booking_number;
                    $data['buyer_number'] = $entity->buyer_number;
                    $data['payments_terms'] = $entity->payments_terms;

                    $paymentManager = new PaymentManager($daffny->DB);
                    $deposit_paid = $paymentManager->getDepositPaid($entity->id);

                    $company_owes_carrier = 0;
                    $carrier_owes_company = 0;
                    $carrier_pay_total = 0;

                    switch ($entity->balance_paid_by) {
                        case Entity::BALANCE_COD_TO_CARRIER_CASH:
                        case Entity::BALANCE_COD_TO_CARRIER_CHECK:
                            $company_owes_carrier = 0;
                            $carrier_owes_company = 0;
                            $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay;
                            break;
                        case Entity::BALANCE_COP_TO_CARRIER_CASH:
                        case Entity::BALANCE_COP_TO_CARRIER_CHECK:
                            $company_owes_carrier = 0;
                            $carrier_owes_company = 0;
                            $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay;
                            break;
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                            $company_owes_carrier = $entity->carrier_pay;
                            $carrier_owes_company = 0;
                            $carrier_pay_total = 0;

                            break;
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                            $company_owes_carrier = 0;
                            $carrier_owes_company = $entity->total_deposit;
                            $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay + $entity->total_deposit;
                            break;
                    }

                    //1) order_company_owes_carrier
                    $data['company_owes_carrier'] = number_format($company_owes_carrier, 2, '.', ',');
                    //2) order_carrier_ondelivery
                    $data['carrier_ondelivery'] = number_format($carrier_owes_company, 2, '.', ',');
                    //3) order_carrier_pay
                    $data['carrier_pay'] = number_format($carrier_pay_total, 2, '.', ',');
                    //!4) order_balance_paid_by
                    $data['balance_paid_by'] = $entity->balance_paid_by;
                    //!5) order_pickup_terminal_fee
                    $data['pickup_terminal_fee'] = number_format($entity->pickup_terminal_fee, 2, '.', ',');
                    //!6) order_dropoff_terminal_fee
                    $data['dropoff_terminal_fee'] = number_format($entity->dropoff_terminal_fee, 2, '.', ',');

                    $origin = $entity->getOrigin();
                    $data['pickup_name'] = $origin->name;
                    $data['pickup_name2'] = $origin->name2;
                    $data['pickup_company'] = $origin->company;
                    $data['pickup_phone1'] = $origin->phone1;
                    $data['pickup_phone2'] = $origin->phone2;
                    $data['pickup_phone3'] = $origin->phone3;
                    $data['pickup_phone_cell'] = $origin->phone_cell;
                    $data['pickup_address1'] = $origin->address1;
                    $data['pickup_address2'] = $origin->address2;
                    $data['pickup_city'] = $origin->city;
                    $data['pickup_state'] = $origin->state;
                    $data['pickup_zip'] = $origin->zip;
                    $data['pickup_country'] = $origin->country;
                    $data['from_booking_number'] = $origin->booking_number;
                    $data['from_buyer_number'] = $origin->buyer_number;

                    $destination = $entity->getDestination();
                    $data['deliver_name'] = $destination->name;
                    $data['deliver_name2'] = $destination->name2;
                    $data['deliver_company'] = $destination->company;
                    $data['deliver_phone1'] = $destination->phone1;
                    $data['deliver_phone2'] = $destination->phone2;
                    $data['deliver_phone3'] = $destination->phone3;
                    $data['deliver_phone_cell'] = $destination->phone_cell;
                    $data['deliver_address1'] = $destination->address1;
                    $data['deliver_address2'] = $destination->address2;
                    $data['deliver_city'] = $destination->city;
                    $data['deliver_state'] = $destination->state;
                    $data['deliver_zip'] = $destination->zip;
                    $data['deliver_country'] = $destination->country;
                    $data['to_booking_number'] = $destination->booking_number;
                    $data['to_buyer_number'] = $destination->buyer_number;

                    $notes = $entity->getNotes();
                    if (count($notes[Note::TYPE_FROM]) != 0) {
                        $data['include_shipper_comment'] = $entity->include_shipper_comment;
                        $data['notes_from_shipper'] = $notes[Note::TYPE_FROM][0]->text;
                    } else {
                        $data['include_shipper_comment'] = 0;
                        $data['notes_from_shipper'] = '';
                    }

                    $out = array('success' => true, 'data' => $data);
                break;
                case "dispatch":
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly) {
                        break;
                    }

                    if ($entity->getDispatchSheet() != null) {
                        break;
                    }

                    $errors = array();
                    foreach ($_POST as $key => $value) {
                        $_POST[$key] = trim($value);
                    }

                    $carrier_id = (trim($_POST['carrier_id']) != "") ? $_POST['carrier_id'] : "NULL";
                    $account_id = (trim($_POST['account_id']) != "") ? $_POST['account_id'] : "NULL";

                    $required_fields = array(
                        'carrier_company' => "Carrier: Company",
                        'carrier_address' => "Carrier: Address",
                        'carrier_city' => "Carrier: City",
                        'carrier_state' => "Carrier: State",
                        'carrier_zip' => "Carrier: Zip",
                        'carrier_country' => "Carrier: Country",
                        'carrier_email' => "Carrier: Email",
                        'carrier_phone1' => "Carrier: Phone (1)",
                        'carrier_print_name' => "Carrier: Print Check As",
                        'carrier_type' => "Carrier: Type",
                        'carrier_insurance_iccmcnumber' => "Carrier: ICC MC Number",
                        'order_load_date' => "Order: Load Date",
                        'order_load_date_type' => "Order: Load Date Type",
                        'order_delivery_date' => "Order: Delivery Date",
                        'order_delivery_date_type' => "Order: Delivery Date Type",
                        'order_ship_via' => "Order: Ship Via",
                        'order_carrier_pay' => "Order: Carrier Pay",
                        'order_carrier_ondelivery' => "Order: Carrier on Delivery",
                        'order_company_owes_carrier' => "Order: Company owes Carrier",
                        'pickup_name' => "Pickup From: Name",
                        'pickup_address1' => "Pickup From: Address",
                        'pickup_city' => "Pickup From: City",
                        'pickup_state' => "Pickup From: State",
                        'pickup_zip' => "Pickup From: Zip",
                        'pickup_country' => "Pickup From: Country",
                        'pickup_phone1' => "Pickup From: Phone (1)",
                        'deliver_name' => "Delivery To: Name",
                        'deliver_address1' => "Delivery To: Address",
                        'deliver_city' => "Delivery To: City",
                        'deliver_state' => "Delivery To: State",
                        'deliver_zip' => "Delivery To: Zip",
                        'deliver_country' => "Delivery To: Country",
                        'deliver_phone1' => "Delivery To: Phone (1)",
                    );

                    foreach ($required_fields as $field => $label) {
                        if (!isset($_POST[$field])) {
                            $errors[] = $label . " value required";
                            continue;
                        }
                        $errors = array_merge($errors, checkEmpty($_POST[$field], $label));
                    }
                    if (!validate_email($_POST['carrier_email'])) {
                        $errors[] = "Carrier: Email value invalid";
                    }
                    if (strtotime(date('Y-m-d')) > strtotime($_POST['order_load_date'])) {
                        $errors[] = "Load Date must be greator than today date.";
                    }
                    if (count($errors) > 0) {
                        $out = array('success' => false, 'errors' => $errors);
                    } else {
                        $daffny->DB->transaction('start');

                        //Insert new carrier
                        if (!isset($_POST['account_id']) || trim($_POST['account_id']) == "") {
                            if (isset($_POST["save_carrier"])) {
                                $carrier_arr = array(
                                    "member_id" => getMemberId(),
                                    "owner_id" => getParentId(),
                                    "is_carrier" => 1,
                                    "carrier_type" => (isset($_POST['carrier_type']) && $_POST['carrier_type'] != "") ? $_POST['carrier_type'] : null,
                                    "company_name" => $_POST['carrier_company'],
                                    "contact_name1" => $_POST['carrier_contact'],
                                    "phone1" => $_POST['carrier_phone1'],
                                    "phone2" => $_POST['carrier_phone2'],
                                    "cell" => $_POST['carrier_cell'],
                                    "fax" => $_POST['carrier_fax'],
                                    "email" => $_POST['carrier_email'],
                                    "address1" => $_POST['carrier_address'],
                                    "city" => $_POST['carrier_city'],
                                    "state" => $_POST['carrier_state'],
                                    "zip_code" => $_POST['carrier_zip'],
                                    "country" => $_POST['carrier_country'],
                                    "insurance_iccmcnumber" => $_POST['carrier_insurance_iccmcnumber'],
                                    "create_date" => date("Y-m-d H:i:s"),
                                    "donot_dispatch" => 0,
                                    "status" => Account::STATUS_ACTIVE,
                                    "print_name" => $_POST['carrier_print_name'],
                                );

                                $carrier = new Account($daffny->DB);
                                $carrier_id = $carrier->create($carrier_arr);
                                $account_id = $carrier_id;
                            }
                        }

                        $order_load_date = date("Y-m-d", strtotime($_POST['order_load_date']));
                        $order_delivery_date = date("Y-m-d", strtotime($_POST['order_delivery_date']));
                        // Update Entity
                        $update_arr = array(
                            'carrier_id' => $carrier_id,
                            'load_date' => $order_load_date,
                            'delivery_date' => $order_delivery_date,
                            'load_date_type' => (int) $_POST['order_load_date_type'],
                            'delivery_date_type' => (int) $_POST['order_delivery_date_type'],
                            'ship_via' => $_POST['order_ship_via'],
                            'dispatched' => date("Y-m-d H:i:s"),
                            'not_signed' => date("Y-m-d H:i:s"),
                            'payments_terms' => $_POST['payments_terms'],
                        );
                        $entity->update($update_arr);
                        // Update Origin
                        $update_arr = array(
                            'address1' => $_POST['pickup_address1'],
                            'address2' => $_POST['pickup_address2'],
                            'city' => $_POST['pickup_city'],
                            'state' => $_POST['pickup_state'],
                            'zip' => $_POST['pickup_zip'],
                            'country' => $_POST['pickup_country'],
                            'name' => $_POST['pickup_name'],
                            'company' => $_POST['pickup_company'],
                            'phone1' => $_POST['pickup_phone1'],
                            'phone2' => $_POST['pickup_phone2'],
                            'phone3' => $_POST['pickup_phone3'],
                            'phone_cell' => $_POST['pickup_cell'],
                            'booking_number' => $_POST['from_booking_number'],
                            'buyer_number' => $_POST['from_buyer_number'],
                        );
                        $entity->getOrigin()->update($update_arr);
                        // Update Destination
                        $update_arr = array(
                            'address1' => $_POST['deliver_address1'],
                            'address2' => $_POST['deliver_address2'],
                            'city' => $_POST['deliver_city'],
                            'state' => $_POST['deliver_state'],
                            'zip' => $_POST['deliver_zip'],
                            'country' => $_POST['deliver_country'],
                            'name' => $_POST['deliver_name'],
                            'company' => $_POST['deliver_company'],
                            'phone1' => $_POST['deliver_phone1'],
                            'phone2' => $_POST['deliver_phone2'],
                            'phone3' => $_POST['deliver_phone3'],
                            'phone_cell' => $_POST['deliver_cell'],
                            'booking_number' => $_POST['to_booking_number'],
                            'buyer_number' => $_POST['to_buyer_number'],
                        );
                        $entity->getDestination()->update($update_arr);
                        // Create Dispatch Sheet
                        $company = $entity->getAssigned()->getCompanyProfile();
                        $notes = $entity->getNotes();
                        if (($entity->include_shipper_comment == 1) && isset($notes[Note::TYPE_FROM][0])) {
                            $instructions = $notes[Note::TYPE_FROM][0]->text;
                        } else {
                            $instructions = "";
                        }

                        $payments_terms_dispatch = $_POST['payments_terms'];

                        if (in_array($entity->balance_paid_by, array(2, 3, 16, 17))) {
                            $payments_terms_dispatch = "COD";
                        }
                        $insert_arr = array(
                            'entity_id' => $_POST['entity_id'],
                            'account_id' => $account_id,
                            'order_number' => $entity->getNumber(),
                            'c_companyname' => $company->companyname,
                            'c_address1' => $company->address1,
                            'c_address2' => $company->address2,
                            'c_city' => $company->city,
                            'c_state' => $company->state,
                            'c_zip_code' => $company->zip_code,
                            'c_phone' => $company->phone,
                            'c_dispatch_contact' => $company->dispatch_contact,
                            'c_dispatch_phone' => $company->dispatch_phone,
                            'c_dispatch_fax' => $company->dispatch_fax,
                            'c_dispatch_accounting_fax' => $company->dispatch_accounting_fax,
                            'c_icc_mc_number' => $company->icc_mc_number,
                            'carrier_id' => (trim($_POST['carrier_id']) != "") ? $_POST['carrier_id'] : "NULL",
                            'carrier_company_name' => $_POST['carrier_company'],
                            'carrier_contact_name' => $_POST['carrier_contact'],
                            'carrier_phone_1' => $_POST['carrier_phone1'],
                            'carrier_phone_2' => $_POST['carrier_phone2'],
                            'carrier_phone_cell' => $_POST['carrier_cell'],
                            'carrier_fax' => $_POST['carrier_fax'],
                            'carrier_driver_name' => $_POST['carrier_driver'],
                            'carrier_driver_phone' => $_POST['carrier_driver_phone'],
                            'carrier_address' => $_POST['carrier_address'],
                            'carrier_print_name' => $_POST['carrier_print_name'],
                            'carrier_insurance_iccmcnumber' => $_POST['carrier_insurance_iccmcnumber'],
                            'carrier_type' => (trim($_POST['carrier_type']) != "") ? $_POST['carrier_type'] : "NULL",
                            'carrier_city' => $_POST['carrier_city'],
                            'carrier_state' => $_POST['carrier_state'],
                            'carrier_zip' => $_POST['carrier_zip'],
                            'carrier_country' => $_POST['carrier_country'],
                            'carrier_email' => $_POST['carrier_email'],
                            'entity_load_date' => date("Y-m-d", strtotime($_POST['order_load_date'])),
                            'entity_load_date_type' => $_POST['order_load_date_type'],
                            'entity_delivery_date' => date("Y-m-d", strtotime($_POST['order_delivery_date'])),
                            'entity_delivery_date_type' => $_POST['order_delivery_date_type'],
                            'entity_ship_via' => $_POST['order_ship_via'],
                            'entity_carrier_pay' => $_POST['order_carrier_pay'],
                            'entity_carrier_pay_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "*COD" : (in_array($entity->balance_paid_by, array(8, 9)) ? "*COP" : "")),
                            'entity_odtc' => $_POST['order_carrier_ondelivery'],
                            'entity_coc' => $_POST['order_company_owes_carrier'],
                            'entity_coc_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "after COD is paid" : (in_array($entity->balance_paid_by, array(8, 9)) ? "after COP is paid" : "")),
                            'entity_booking_number' => $_POST['order_booking_number'],
                            'entity_buyer_number' => $_POST['order_buyer_number'],
                            'entity_pickup_terminal_fee' => $_POST['order_pickup_terminal_fee'],
                            'entity_dropoff_terminal_fee' => $_POST['order_dropoff_terminal_fee'],
                            'entity_balance_paid_by' => $_POST['order_balance_paid_by'],
                            'from_name' => $_POST['pickup_name'],
                            'from_company' => $_POST['pickup_company'],
                            'from_address' => $_POST['pickup_address1'],
                            'from_address2' => $_POST['pickup_address2'],
                            'from_city' => $_POST['pickup_city'],
                            'from_state' => $_POST['pickup_state'],
                            'from_zip' => $_POST['pickup_zip'],
                            'from_country' => $_POST['pickup_country'],
                            'from_phone_1' => $_POST['pickup_phone1'],
                            'from_phone_2' => $_POST['pickup_phone2'],
                            'from_phone_3' => $_POST['pickup_phone3'],
                            'from_phone_cell' => $_POST['pickup_cell'],
                            'from_booking_number' => $_POST['from_booking_number'],
                            'from_buyer_number' => $_POST['from_buyer_number'],
                            'to_name' => $_POST['deliver_name'],
                            'to_company' => $_POST['deliver_company'],
                            'to_address' => $_POST['deliver_address1'],
                            'to_address2' => $_POST['deliver_address2'],
                            'to_city' => $_POST['deliver_city'],
                            'to_state' => $_POST['deliver_state'],
                            'to_zip' => $_POST['deliver_zip'],
                            'to_country' => $_POST['deliver_country'],
                            'to_phone_1' => $_POST['deliver_phone1'],
                            'to_phone_2' => $_POST['deliver_phone2'],
                            'to_phone_3' => $_POST['deliver_phone3'],
                            'to_phone_cell' => $_POST['deliver_cell'],
                            'to_booking_number' => $_POST['to_booking_number'],
                            'to_buyer_number' => $_POST['to_buyer_number'],
                            'dispatch_terms' => $company->getDefaultSettings()->dispatch_terms,
                            'hash_link' => getAlmostUniqueHash($_POST['entity_id'], $entity->getNumber()),
                            'payments_terms' => $payments_terms_dispatch,
                            'expired' => date("Y-m-d H:i:s", time() + (7 * 60 * 60 * 24)),
                        );
                        if (isset($_POST['order_include_shipper_comment'])) {
                            $insert_arr['instructions'] = $_POST['order_notes_from_shipper'];

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => Note::TYPE_FROM,
                                "text" => $_POST['order_notes_from_shipper']);
                            $note = new Note($daffny->DB);
                            $note->create($note_array);
                        } else {
                            $insert_arr['instructions'] = '';
                        }
                        $dispatchSheet = new DispatchSheet($daffny->DB);
                        $dispatch_id = $dispatchSheet->create($insert_arr);
                        $vehicleManager = new VehicleManager($daffny->DB);
                        $vehicles = $vehicleManager->getVehicles($entity->id);
                        foreach ($vehicles as $vehicle) {
                            /* @var Vehicle $vehicle */
                            $vehicle->cloneForDispatch($dispatch_id);
                        }
                        $entity->setStatus(Entity::STATUS_NOTSIGNED);
                        $entity->sendOrderDispatched();
                        $dispatch_link = BASE_PATH . "order/dispatchnew/hash/" . $insert_arr["hash_link"];
                        //TODO: uncomment this in future
                        $entity->sendDispatchLink(array("dispatch_link" => $dispatch_link));
                        $daffny->DB->transaction('commit');

                        //send letter to the carrier

                        $NotesStr = " has dispatched this order to " . $_POST['carrier_company'] . " for pickup " . $order_load_date . " and drop off on " . $order_delivery_date;
                        // Create Internal Notes
                        $note = new Note($daffny->DB);
                        $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));
                        $entity->updateHeaderTable();

                        if (isset($account_id) && trim($account_id) != "") {

                            $radius = 30;
                            $AccountRouteObj = new AccountRoute($daffny->DB);

                            $AccountRouteArr = array();
                            $AccountRouteArr['type'] = "ORG";
                            $AccountRouteArr['account_id'] = $account_id;
                            $AccountRouteArr['origin'] = $_POST['pickup_city'] . "," . $_POST['pickup_state'] . "," . $_POST['pickup_zip'];
                            $AccountRouteArr['destination'] = $_POST['deliver_city'] . "," . $_POST['deliver_state'] . "," . $_POST['deliver_zip'];
                            $AccountRouteArr['ocity'] = $_POST['pickup_city'];
                            $AccountRouteArr['ostate'] = $_POST['pickup_state'];
                            $AccountRouteArr['ozip'] = $_POST['pickup_zip'];
                            $AccountRouteArr['dcity'] = $_POST['deliver_city'];
                            $AccountRouteArr['dstate'] = $_POST['deliver_state'];
                            $AccountRouteArr['dzip'] = $_POST['deliver_zip'];
                            $AccountRouteArr['radius'] = $radius;

                            $AccountRouteObj->create($AccountRouteArr);
                            $AccountRouteID = $AccountRouteObj->id;
                            $RouteObj = new Route($daffny->DB);
                            $RouteObj->routeMapping($AccountRouteID, "ORG", $_POST['pickup_city'], $_POST['pickup_state'], $_POST['pickup_zip'], $radius);

                            $RouteObj->routeMapping($AccountRouteID, "DES", $_POST['deliver_city'], $_POST['deliver_state'], $_POST['deliver_zip'], $radius);
                        }

                        /*                         * ************************************************************* */

                        $out = array('success' => true);
                    }
                break;
                case 'saveQuotes':
                    $data = $json->decode(stripcslashes($_POST['data']));
                    if (!is_array($data)) {
                        break;
                    }

                    foreach ($data as $key => $value) {

                        try {

                            $entity = new Entity($daffny->DB);
                            $entity->load($value->entity_id);
                            if ($entity->readonly) {
                                continue;
                            }

                            if ($value->tariff != 0 && $value->deposit != 0) {
                                $vehicles = $entity->getVehicles();
                                $vehicles[0]->update(array(
                                    'tariff' => $value->tariff,
                                    'carrier_pay' => ($value->tariff - $value->deposit),
                                    'deposit' => $value->deposit,
                                ));
                            }
                            if ($entity->type == Entity::TYPE_CLEAD && ($entity->status == Entity::STATUS_CACTIVE || $entity->status == Entity::STATUS_CASSIGNED)) {

                                $sql = "INSERT INTO app_lead_tracking (entityid,number,prefix,	date_converted,creator_id,assigned_id,salesrepid,carrier_id,account_id, type, status) values ('" . $entity->id . "','" . $entity->number . "','" . $entity->prefix . "','" . date("Y-m-d") . "', '" . $entity->creator_id . "','" . $entity->assigned_id . "','" . $entity->salesrepid . "','" . $entity->carrier_id . "','" . $entity->account_id . "','" . $entity->type . "','" . $entity->status . "')";
                                $result = $daffny->DB->query($sql);
                                $entity->convertCreatedLleadToQuote();
                            } else {
                                $entity->convertToQuote();
                            }

                            $entity->updateHeaderTable();
                        } catch (FDException $e) {

                        }

                        if ($_POST['email'] == 1) {
                            $entity->sendInitialQuote();
                        }
                    }
                    $out = array('success' => true);
                break;
                case 'saveQuotesNew':
                    $data = $json->decode(stripcslashes($_POST['data']));
                    if (!is_array($data)) {
                        break;
                    }

                    foreach ($data as $key => $value) {

                        try {
                            $entity = new Entity($daffny->DB);
                            $entity->load($value->entity_id);
                            if ($entity->readonly) {
                                continue;
                            }
                            if ($entity->type == Entity::TYPE_CLEAD) {

                                if(!$entity->salesrepid){
                                    $sales_person = "";
                                } else {
                                    $sales_person = $entity->salesrepid;
                                }

                                if(!$entity->carrier_id){
                                    $carrier_id = 0;
                                } else {
                                    $carrier_id = $entity->carrier_id;
                                }

                                $sql = "INSERT INTO app_lead_tracking (entityid,number,prefix,	date_converted,creator_id,parent_id,assigned_id,salesrepid,carrier_id,account_id, type, status)values('" . $entity->id . "','" . $entity->number . "','" . $entity->prefix . "','" . date("Y-m-d") . "','" . $entity->creator_id . "','" . $entity->parentid . "','" . $entity->assigned_id . "','" . $sales_person . "','" . $carrier_id . "','" . $entity->account_id . "','" . $entity->type . "','" . $entity->status . "')";
                                $result = $daffny->DB->query($sql);
                                $entity->convertCreatedLleadToQuote();
                                $entity->update(array("total_tariff_stored" => $value->tariff, "carrier_pay_stored" => ($value->tariff - $value->deposit)));
                            } else {
                                if ($value->tariff != 0 && $value->deposit != 0) {
                                    $vehicles = $entity->getVehicles();
                                    $vehicles[0]->update(array(
                                        'tariff' => $value->tariff,
                                        'carrier_pay' => ($value->tariff - $value->deposit),
                                        'deposit' => $value->deposit,
                                    ));
                                }
                                $entity->convertToQuoteNew();
                            }

                            $entity->updateHeaderTable();
                        } catch (FDException $e) {
                            print_r($e);
                            echo $daffny->DB->errorQuery;
                            die("Error!");
                        }

                        if ($_POST['email'] == 1) {
                            $entity->sendInitialQuote();
                        }
                    }
                    $out = array('success' => true);
                break;
                case 'emailQuote':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    if ($entity->type != Entity::TYPE_QUOTE) {
                        throw new RuntimeException("Invalid Entity Type");
                    }

                    $entity->sendSelectedQuoteTemplate($form_id);
                    $out = array('success' => true);
                break;
                case 'emailQuoteMultiple':
                    if (!isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];

                    if (isset($_POST['entity_ids'])) {
                        foreach (explode(",", $_POST['entity_ids']) as $entity_id) {
                            print "entity_id: " . $entity_id;
                            $entity = new Entity($daffny->DB);
                            $entity->load((int) $entity_id);
                            if ($entity->type != Entity::TYPE_QUOTE) {
                                throw new RuntimeException("Invalid Entity Type");
                            }

                            $entity->sendSelectedQuoteTemplate($form_id);
                        }
                    }

                    $out = array('success' => true);
                break;
                case 'emailQuoteNew':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);

                    $emailContentArr = array();
                    $emailContentArr = $entity->sendSelectedQuoteTemplateNew($form_id);

                    $out = array('success' => true, 'emailContent' => $emailContentArr);
                break;
                case 'emailQuoteNewSend':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) ) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];

                    $emailArr = array();
                    $emailArr['to'] = $_POST['mail_to'];
                    $emailArr['cc'] = $_POST['mail_cc'];
                    $emailArr['bcc'] = $_POST['mail_bcc'];
                    $emailArr['mail_extra'] = $_POST['mail_extra'];
                    $emailArr['subject'] = $_POST['mail_subject'];
                    $emailArr['body'] = $_POST['mail_body'];
                    $emailArr['attach_type'] = $_POST['attach_type'];

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);

                    $entity->sendSelectedQuoteTemplateNewCustomSend($form_id, $emailArr);

                    $out = array('success' => true, "message" => "File has been sent.");
                break;
                case 'emailOrder':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    if ($entity->type != Entity::TYPE_ORDER) {
                        throw new RuntimeException("Invalid Entity Type");
                    }

                    $entity->sendSelectedOrderTemplate($form_id);
                    $out = array('success' => true);
                break;
                case 'emailOrderNew':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);
                    if ($entity->type != Entity::TYPE_ORDER) {
                        throw new RuntimeException("Invalid Entity Type");
                    }

                    $entity->updateHash();

                    $emailContentArr = array();
                    $emailContentArr = $entity->sendSelectedOrderTemplateNew($form_id);
                    $out = array('success' => true, 'emailContent' => $emailContentArr);
                break;
                case 'emailOrderNewSend':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id']) || !isset($_POST['form_id']) || !ctype_digit((string) $_POST['form_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $form_id = (int) $_POST['form_id'];

                    $emailArr = array();
                    $emailArr['to'] = $_POST['mail_to'];
                    $emailArr['cc'] = $_POST['mail_cc'];
                    $emailArr['bcc'] = $_POST['mail_bcc'];
                    $emailArr['mail_extra'] = $_POST['mail_extra'];
                    $emailArr['subject'] = $_POST['mail_subject'];
                    $emailArr['body'] = $_POST['mail_body'];
                    $emailArr['attach_type'] = $_POST['attach_type'];

                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $_POST['entity_id']);

                    $shipper = $entity->getShipper();
                    $emailArr['toname'] = $shipper->fname . " " . $shipper->lname;

                    if ($form_id == 110) {
                        $entity->update(array('esigned_date' => date('Y-m-d H:i:s')));
                    } elseif ($form_id == 677) {
                        $entity->update(array('bsigned_date' => date('Y-m-d H:i:s')));
                    }

                    if ($form_id == 109) {
                        $entity->update(array('invoice_status' => 1));
                    }

                    $entity->sendSelectedOrderTemplateNewCustomSend($form_id, $emailArr);

                    if ($entity->assigned_id == $memberId) {
                        // Update Entity
                        $update_arr = array(
                            'last_activity_date' => date('Y-m-d H:i:s'),
                        );
                        $entity->update($update_arr);
                    }
                    $entity->updateHeaderTable();
                    $out = array('success' => true, "message" => "File has been sent.");
                break;
                case 'checkBlock':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        break;
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly || $entity->isBlocked()) {
                        break;
                    }

                    $out = array('success' => true);

                break;
                case 'setBlock':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        break;
                    }

                    $type = $_POST['type'];
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly || $entity->isBlocked()) {
                        break;
                    }

                    if ($type == 1) {
                        $entity->setBlock();
                    } else {
                        $entity->updateBlock();
                    }

                    if ($type == 1) {
                        $sql = "select  id from member_blocked_page WHERE owner_id = '" . getParentId() . "' and member_id = '" . $_SESSION['member_id'] . "' and entity_id = '" . $entity->id . "' and status=1";
                        $result = $daffny->DB->query($sql);

                        if ($daffny->DB->num_rows() <= 0) {

                            $date = date("Y-m-d H:i:s");
                            $currentTime = strtotime($date);
                            $futureDate = $currentTime + (51 * 1);
                            $page_heartbeat = date("Y-m-d H:i:s", $futureDate);

                            $sql = "INSERT INTO member_blocked_page (owner_id,member_id ,entity_id,page_heartbeat)values
										('" . getParentId() . "','" . $_SESSION['member_id'] . "','" . $entity->id . "','" . $page_heartbeat . "')";
                            $result = $daffny->DB->query($sql);
                        }
                    }

                    $out = array('success' => true);
                break;
                case 'unsetBlock':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        break;
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly || $entity->isBlocked()) {
                        break;
                    }

                    $entity->unBlock();
                    $out = array('success' => true);
                break;
                case 'setDispatchDate':
                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        break;
                    }

                    if (!isset($_POST['type'])) {
                        break;
                    }

                    if (!isset($_POST['value'])) {
                        break;
                    }

                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    if ($entity->readonly || $entity->isBlocked()) {
                        break;
                    }

                    $entity->update(array($_POST['type'] => date("Y-m-d", strtotime($_POST['value']))));
                    $entity->updateHeaderTable();
                    $out = array('success' => true);
                break;
                case 'getReferrerCommission':
                    if (!isset($_POST['referred_by']) || !ctype_digit((string) $_POST['referred_by'])) {
                        break;
                    }

                    $referrerManager = new ReferrerManager($daffny->DB);
                    $referrers = $referrerManager->get(null, null, " id=" . $_POST['referred_by'] . " AND `status` = " . Referrer::STATUS_ACTIVE);
                    $data = array();
                    foreach ($referrers as $referrer) {
                        $data['commission'] = $referrer->commission;
                        $data['intial_percentage'] = $referrer->intial_percentage;
                        $data['residual_percentage'] = $referrer->residual_percentage;
                    }

                    $out = array('success' => true, 'commission' => $data);
                break;
                case 'payCommission':
                    if (!isset($_POST['comm_id']) || !ctype_digit((string) $_POST['comm_id'])) {
                        break;
                    }

                    if (!isset($_POST['user_id']) || !ctype_digit((string) $_POST['user_id'])) {
                        break;
                    }

                    if (!isset($_POST['comm_type']) || !ctype_digit((string) $_POST['comm_type'])) {
                        break;
                    }

                    $typeWhere = "";
                    $paidWhere = "";
                    if ($_POST['comm_type'] == 1) {
                        $typeWhere = " AND creator_id=" . $_POST['user_id'];
                        $paidWhere = " AND commission_payed=0";
                        $commissionArr = array(
                            'commission_payed' => 1,
                        );
                    }
                    if ($_POST['comm_type'] == 2) {
                        $typeWhere = " AND assigned_id=" . $_POST['user_id'];
                        $paidWhere = " AND commission_payed_assigned=0";
                        $commissionArr = array(
                            'commission_payed_assigned' => 1,
                        );
                    }
                    $rowCommissionCheck = $daffny->DB->selectRow("id", "app_entity_commission", "WHERE  id = '" . $_POST['comm_id'] . "'  " . $typeWhere . " " . $paidWher);
                    if (!empty($rowCommissionCheck) && count($rowCommissionCheck) == 1) {
                        $daffny->DB->update("app_entity_commission", $commissionArr, "id = '" . $_POST['comm_id'] . "'  " . $typeWhere . " " . $paidWhere);
                    }

                    $out = array('success' => true);
                break;
                case 'getPaymentData':
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    $data = array();
                    $balances = array(
                        'shipper' => 0,
                        'carrier' => 0,
                        'pterminal' => 0,
                        'dterminal' => 0,
                    );

                    $carrierRemains = 0;
                    $depositRemains = 0;
                    $shipperRemains = 0;
                    $type = 0;
                    $paymentManager = new PaymentManager($daffny->DB);
                    // We owe them
                    switch ($entity->balance_paid_by) {
                        case Entity::BALANCE_COP_TO_CARRIER_CASH:
                        case Entity::BALANCE_COP_TO_CARRIER_CHECK:
                        case Entity::BALANCE_COP_TO_CARRIER_COMCHECK:
                        case Entity::BALANCE_COP_TO_CARRIER_QUICKPAY:
                        case Entity::BALANCE_COD_TO_CARRIER_CASH:
                        case Entity::BALANCE_COD_TO_CARRIER_CHECK:
                        case Entity::BALANCE_COD_TO_CARRIER_COMCHECK:
                        case Entity::BALANCE_COD_TO_CARRIER_QUICKPAY:
                            $shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
                            $balances['we_carrier'] = 0;
                            $balances['we_shipper'] = 0;
                            $balances['they_carrier'] = 0;
                            $balances['they_shipper'] = $entity->getTotalDeposit(false) - $shipperPaid;
                            $balances['they_shipper_paid'] = $shipperPaid;

                            $depositRemains = $entity->getTotalDeposit(false) - $shipperPaid;

                            $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;

                            $carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
                            $carrierRemains = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;

                            $type = 1;
                        break;
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
                        case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:

                            $carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
                            $shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);

                            $balances['they_carrier'] = 0;
                            $balances['we_shipper'] = 0;
                            $balances['we_carrier'] = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;
                            $balances['we_carrier_paid'] = $carrierPaid;
                            $balances['they_shipper'] = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;
                            $balances['they_shipper_paid'] = $shipperPaid;

                            $carrierRemains = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;
                            $depositRemains = $entity->getTotalDeposit(false) - $shipperPaid;
                            $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;
                            $type = 2;
                        break;
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
                        case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
                            $carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
                            $balances['we_shipper'] = 0;
                            $balances['we_carrier'] = 0;
                            $balances['they_shipper'] = 0;

                            $balances['they_carrier'] = $entity->getTotalDeposit(false) - $carrierPaid;
                            $balances['they_carrier_paid'] = $carrierPaid;

                            $depositRemains = $entity->getTotalDeposit(false) - $carrierPaid;
                            $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $carrierPaid;

                            $carrierRemains = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;

                            $type = 3;
                        break;
                        default:
                            $balances['we_carrier'] = 0;
                            $balances['we_shipper'] = 0;
                            $balances['they_carrier'] = 0;
                            $balances['they_shipper'] = 0;
                        break;
                    }

                    if ($depositRemains < 0) {
                        $depositRemains = 0;
                    }

                    if ($shipperRemains < 0) {
                        $shipperRemains = 0;
                    }

                    if ($carrierRemains < 0) {
                        $carrierRemains = 0;
                    }

                    $data['carrierRemains'] = $carrierRemains;
                    $data['depositRemains'] = $depositRemains;
                    $data['shipperRemains'] = $shipperRemains;
                    $data['type'] = $type;

                    $assigned = $entity->getAssigned();
                    $shipper = $entity->getShipper();
                    $origin = $entity->getOrigin();
                    $destination = $entity->getDestination();
                    $vehicles = $entity->getVehicles();
                    $creator_contactname = "";
                    if ($entity->creator_id != 0) {
                        $creator = $entity->getCreator();
                        $creator_contactname = $creator->contactname;
                    }
                    $assigned = $entity->getAssigned();

                    $str = '
                        <h3>Order #' . $entity->getNumber() . ' </h3>

                            <div class="order-info" style="width: 445px; margin-bottom: 10px;">
                            <p class="block-title">Order Information</p>
                            <div>
                                <strong>Assigned to: </strong>' . $assigned->contactname . '<br/>
                                <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="vertical-align:top;">
                                            <strong>Shipper: </strong><br/>
                                            ' . $shipper->fname . ' ' . $shipper->lname . '<br/>
                                            Phone: ' . $shipper->phone1 . '<br/>
                                            ' . $shipper->email . '<br/>
                                            Company: <b>' . $shipper->company . '</b><br/>
                                            Fax: ' . $shipper->fax . '<br/>

                                        </td>
                                        <td style="vertical-align:top;">
                                            <strong>Origin: </strong>' . $origin->getFormatted() . '<br/>
                                            <strong>Destination: </strong>' . $destination->getFormatted() . '<br/>
                                            <strong>Assigned To: </strong>' . $assigned->contactname . '<br/>
                                            <strong>Source: </strong>' . $creator_contactname . '<br/>

                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>';

                    $isColor = $entity->isPaidOffColor();
                    $Dcolor = "black";
                    $Ccolor = "black";
                    $Tcolor = "black";

                    if ($isColor['carrier'] == 1) {
                        $Ccolor = "green";
                    } elseif ($isColor['carrier'] == 2) {
                        $Ccolor = "red";
                    }

                    if ($isColor['deposit'] == 1) {
                        $Dcolor = "green";
                    } elseif ($isColor['deposit'] == 2) {
                        $Dcolor = "red";
                    }

                    if ($isColor['total'] == 1) {
                        $Tcolor = "green";
                    } elseif ($isColor['total'] == 2) {
                        $Tcolor = "red";
                    }

                    $str .= '<div class="quote-info" style="width: 270px;float:left;margin-left:10px;">
                        <p class="block-title">Payment Terms</p>
                        <div>
                            <img style="vertical-align: middle" src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/> <strong>Total Tariff amount: </strong><span class="' . $Tcolor . '">' . $entity->getTotalTariff() . '</span><br />
                            <img style="vertical-align: middle" src="' . SITE_IN . 'images/icons/truck.png" alt="Tariff to Shipper" title="Tariff to Shipper" width="16" height="16"/> <strong>To Carrier: </strong><span class="' . $Ccolor . '">' . $entity->getCarrierPay() . '</span><br />
                            <img style="vertical-align: middle" src="' . SITE_IN . 'images/icons/person.png" alt="Tariff by Customer" title="Tariff by Customer" width="16" height="16"/> <strong>Deposit amount: </strong><span class="' . $Dcolor . '">' . $entity->getTotalDeposit() . '</span><br />';

                    $str .= '</div>
                    </div>';

                    $data['blocks'] = $str;

                    /* CC cards */
                    /* Prefill CC */
                    $entityCreditCard = $entity->getCreditCard();

                    $creditDetails = array();
                    $paymentCard = new Paymentcard($daffny->DB);

                    $paymentCard->key = $daffny->cfg['security_salt'];
                    $paymentCard->loadLastCC((int) $_POST['entity_id'], getParentId());
                    if ($paymentCard->isLoaded()) {

                        $creditDetails['cc_fname'] = $paymentCard->cc_fname;
                        $creditDetails['cc_lname'] = $paymentCard->cc_lname;
                        $creditDetails['cc_address'] = $paymentCard->cc_address;
                        $creditDetails['cc_city'] = $paymentCard->cc_city;
                        $creditDetails['cc_state'] = $paymentCard->cc_state;
                        $creditDetails['cc_zip'] = $paymentCard->cc_zip;
                        $creditDetails['cc_cvv2'] = $paymentCard->cc_cvv2;
                        $creditDetails['cc_number'] = $paymentCard->cc_number;
                        $creditDetails['cc_type'] = $paymentCard->cc_type;
                        $creditDetails['cc_month'] = $paymentCard->cc_month;
                        $creditDetails['cc_year'] = $paymentCard->cc_year;
                    } else {

                        $creditDetails['cc_fname'] = $entityCreditCard->fname;
                        $creditDetails['cc_lname'] = $entityCreditCard->lname;
                        $creditDetails['cc_address'] = $entityCreditCard->address;
                        $creditDetails['cc_city'] = $entityCreditCard->city;
                        $creditDetails['cc_state'] = $entityCreditCard->state;
                        $creditDetails['cc_zip'] = $entityCreditCard->zip;
                        $creditDetails['cc_cvv2'] = $entityCreditCard->cvv2;
                        $creditDetails['cc_number'] = $entityCreditCard->number;
                        $creditDetails['cc_type'] = $entityCreditCard->type;
                        $creditDetails['cc_month'] = $entityCreditCard->month;
                        $creditDetails['cc_year'] = $entityCreditCard->year;
                    }

                    $data['payments'] = $creditDetails;
                    $out = array('success' => true, 'data' => $data);
                break;
                case 'payment':
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    $data = array();
                    $errors = array();
                    switch ($_POST['payment_type']) {
                        case "internally":
                            $insert_arr['entity_id'] = $_POST['entity_id'];
                            $insert_arr['number'] = Payment::getNextNumber($_POST['entity_id'], $daffny->DB);
                            $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received']));
                            $from_to = explode("-", $_POST['from_to']);
                            $insert_arr['fromid'] = $from_to[0];
                            $insert_arr['toid'] = $from_to[1];
                            $insert_arr['entered_by'] = $_SESSION['member_id'];
                            $insert_arr['amount'] = number_format((float) $_POST['amount'], 2, '.', '');
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
                                $noteText = "<green>Shipper paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];

                                if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                    if ($_POST['ch_number'] != "") {
                                        $noteText .= " #" . $_POST['ch_number'];
                                    }

                                } elseif ($nmethod == 9) {
                                    if ($_POST['cc_numb'] != "") {
                                        $noteText .= " ending in #" . $_POST['cc_numb'];
                                    }

                                }
                            } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                $noteText = $noteText = $company->companyname . " paid Shipper $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                $noteText = $noteText = "<green>Carrier paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            }

                            $payment = new Payment($daffny->DB);
                            if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                                $payment->load($_POST['payment_id']);
                                unset($insert_arr['entity_id']);
                                $payment->update($insert_arr);
                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);
                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            } else {

                                $payment->create($insert_arr);
                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            }
                            $entity->updateHeaderTable();
                        break;
                        case "carrier":
                            $insert_arr['entity_id'] = $_POST['entity_id'];
                            $insert_arr['number'] = Payment::getNextNumber($_POST['entity_id'], $daffny->DB);
                            $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_carrier']));
                            $from_to = explode("-", $_POST['from_to_carrier']);
                            $insert_arr['fromid'] = $from_to[0];
                            $insert_arr['toid'] = $from_to[1];
                            $insert_arr['entered_by'] = $_SESSION['member_id'];
                            $insert_arr['amount'] = number_format((float) $_POST['amount_carrier'], 2, '.', '');
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

                            $nmethod = $_POST['method'];
                            $noteText = '';
                            if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                if ($_POST['transaction_id'] != "") {
                                    $noteText = " #" . $_POST['transaction_id'];
                                } elseif ($_POST['ch_number'] != "") {
                                    $noteText = " #" . $_POST['ch_number'];
                                }

                            } elseif ($nmethod == 9) {
                                if ($_POST['cc_numb'] != "") {
                                    $noteText = " ending in #" . $_POST['cc_numb'];
                                }

                            }

                            $payment = new Payment($daffny->DB);
                            if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                                $payment->load($_POST['payment_id']);
                                unset($insert_arr['entity_id']);
                                $payment->update($insert_arr);

                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            } else {

                                $payment->create($insert_arr);
                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            }
                            $entity->updateHeaderTable();
                        break;
                        case "terminal":
                            $insert_arr['entity_id'] = $_POST['entity_id'];
                            $insert_arr['number'] = Payment::getNextNumber($_POST['entity_id'], $daffny->DB);
                            $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_terminal']));
                            $from_to = explode("-", $_POST['from_to_terminal']);
                            $insert_arr['fromid'] = $from_to[0];
                            $insert_arr['toid'] = $from_to[1];
                            $insert_arr['entered_by'] = $_SESSION['member_id'];
                            $insert_arr['amount'] = number_format((float) $_POST['amount_terminal'], 2, '.', '');
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

                            if ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY) {
                                $noteText = "<green>Pickup Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P) {
                                $noteText = "<green>Pickup Terminal paid " . $company->companyname . " paid Pickup Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY) {
                                $noteText = "<green>Delivery Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D) {
                                $noteText = "<green>Pickup Terminal paid " . $company->companyname . " paid Delivery Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            }

                            $payment = new Payment($daffny->DB);
                            if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                                $payment->load($_POST['payment_id']);
                                unset($insert_arr['entity_id']);
                                $payment->update($insert_arr);
                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            } else {

                                $payment->create($insert_arr);
                                $data['success'] = "Your payment has been processed.";
                                $out = array('success' => true, 'data' => $data);

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $_POST['entity_id'],
                                    "sender_id" => $_SESSION['member_id'],
                                    "status" => 1,
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => $noteText);

                                $note = new Note($daffny->DB);
                                $note->create($note_array);
                            }

                            $entity->updateHeaderTable();
                        break;
                        case "gateway":
                            
                            $defaultSettings = new DefaultSettings($daffny->DB);
                            $defaultSettings->getByOwnerId(getParentId());

                            if (in_array($defaultSettings->current_gateway, array(1, 2, 3, 9))) {

                                if ($defaultSettings->current_gateway == 1) { //PayPal
                                    if (trim($defaultSettings->paypal_api_username) == "" || trim($defaultSettings->paypal_api_password) == "" || trim($defaultSettings->paypal_api_signature) == ""
                                    ) {
                                        $errors[] = "PayPal: Please complete API Credentials under 'My Profile > Default Settings'";
                                    }
                                }

                                if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                    if (trim($defaultSettings->anet_api_login_id) == "" || trim($defaultSettings->anet_trans_key) == ""
                                    ) {
                                        $errors[] = "Autorize.net: Please complete API Credentials under 'My Profile > Default Settings'";
                                    }
                                }

                                if ($defaultSettings->current_gateway == 3) { //Authorize.net
                                    if (trim($defaultSettings->gateway_api_username) == "" || trim($defaultSettings->gateway_api_password) == ""
                                    ) {
                                        $errors[] = "Payment Gateway: Please complete API Credentials under 'My Profile > Default Settings'";
                                    }
                                }

                                if ($defaultSettings->current_gateway == 9) { //Easy Pay
                                    if (trim($defaultSettings->easy_pay_key) == "") {
                                        $this->err[] = "Payment Gateway: Please complete API Credentials under 'My Profile > Default Settings'";
                                    }
                                }
                            } else {
                                $this->err[] = "There is no active Payments Gateway under 'My Profile > Default Settings'";
                            }

                            $amount = 0;

                            if (isset($_POST['gw_pt_type']) && in_array($_POST['gw_pt_type'], array("other", "deposit", "balance"))) {
                                switch (post_var("gw_pt_type")) {
                                    case "deposit":
                                        $amount = (float) post_var("deposit_pay");
                                        break;
                                    case "balance":
                                        $amount = (float) post_var("tariff_pay");
                                        break;
                                    case "other":
                                        $amount = (float) post_var("other_amount");
                                        break;
                                }
                            } else {
                                $errors[] = 'Please choose Payment Amount';
                            }

                            if ($amount == 0) {
                                $errors[] = 'Amount can not be $0.00.';
                            }

                            foreach ($_POST as $key => $value) {
                                $_POST[$key] = trim($value);
                            }

                            $required_fields = array(
                                'cc_number' => "CC Number",
                                'cc_type' => "CC Type",
                                'cc_month' => "Exp. Month",
                                'cc_year' => "Exp. Year",
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
                                    , "cc_type_name" => Payment::getCCTypeById(post_var("cc_type")),
                                );

                                $pay_arr = $arr + array(
                                    "amount" => (float) $amount
                                    , "paypal_api_username" => trim($defaultSettings->paypal_api_username)
                                    , "paypal_api_password" => trim($defaultSettings->paypal_api_password)
                                    , "paypal_api_signature" => trim($defaultSettings->paypal_api_signature)
                                    , "anet_api_login_id" => trim($defaultSettings->anet_api_login_id)
                                    , "anet_trans_key" => trim($defaultSettings->anet_trans_key)
                                    , "gateway_api_username" => trim($defaultSettings->gateway_api_username)
                                    , "gateway_api_password" => trim($defaultSettings->gateway_api_password)
                                    , "notify_email" => trim($defaultSettings->notify_email)
                                    , "order_number" => trim($entity->getNumber()),
                                );

                                $ret = array();
                                /* Process payments */
                                if (!count($errors)) {
                                    if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                        $ret = processAuthorize($pay_arr);
                                    }
                                    if ($defaultSettings->current_gateway == 1) { //PayPal
                                        $ret = processPayPal($pay_arr);
                                    }
                                    if ($defaultSettings->current_gateway == 3) { //MDSIP
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
                                            'shipper_hours' => $shipper->shipper_hours,
                                        );

                                        $ret = processMDSIP($pay_arr1);
                                    }
                                    // Easy Pay
                                    if ($defaultSettings->current_gateway == 9) {
                                        $defaultSettings = new DefaultSettings($daffny->DB);
                                        $defaultSettings->getByOwnerId(getParentId());

                                        $ePay = new EasyPay();
                                        $ePay->setLogin($defaultSettings->easy_pay_key);
                                        $ePay->setBilling(
                                            post_var("cc_fname"),
                                            post_var("cc_lname"),
                                            $shipper->company,
                                            post_var("cc_address"),
                                            $shipper->address2, 
                                            post_var("cc_city"),
                                            post_var("cc_state"),
                                            post_var("cc_zip"),
                                            $shipper->country,
                                            $shipper->mobile,
                                            $shipper->fax,
                                            $shipper->email, 
                                            null
                                        );

                                        $ePay->setOrder($entity->prefix."-".$entity->number,"Powered by CargoFlare",0, 0, "XXXXX",$_SERVER['REMOTE_ADDR']);
                                        $ePay->doSale($pay_arr['amount'],$pay_arr['cc_number'], $pay_arr['cc_month'].substr($pay_arr['cc_year'], -2), $_POST['cc_cvv2']);
                                        
                                        if($ePay->responses['responsetext'] == "SUCCESS"){
                                            $ret['success'] = true;
                                            $ret['transaction_id'] = $ePay->responses['transactionid'];
                                        } else {
                                            $ret['success'] = false;
                                            $ret['error'] = $ePay->responses['responsetext'];
                                        }
                                    }

                                    //place
                                    if (isset($ret['success']) && $ret['success'] == true) {
                                        //insert
                                        $insert_arr['entity_id'] = (int) $_POST['entity_id'];
                                        $insert_arr['number'] = Payment::getNextNumber($_POST['entity_id'], $daffny->DB);
                                        $insert_arr['date_received'] = date("Y-m-d H:i:s");
                                        $insert_arr['fromid'] = Payment::SBJ_SHIPPER;
                                        $insert_arr['toid'] = Payment::SBJ_COMPANY;
                                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                                        $insert_arr['amount'] = number_format((float) $pay_arr['amount'], 2, '.', '');
                                        $insert_arr['notes'] = ($defaultSettings->current_gateway == 2 ? "Authorize.net " : "PayPal ") . $ret['transaction_id'];
                                        $insert_arr['method'] = Payment::M_CC;
                                        $insert_arr['transaction_id'] = $ret['transaction_id'];
                                        $insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
                                        $insert_arr['cc_type'] = $pay_arr['cc_type_name'];
                                        $insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
                                        $payment = new Payment($daffny->DB);
                                        $payment->create($insert_arr);

                                        $paymentcard = new Paymentcard($daffny->DB);
                                        $pc_arr = $pay_arr;
                                        $pc_arr['entity_id'] = (int) $_POST['entity_id'];
                                        $pc_arr['owner_id'] = getParentId();
                                        $paymentcard->key = $daffny->cfg['security_salt'];

                                        $paymentcard->create($pc_arr);

                                        if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) == '' && trim($entity->archived) == '') {
                                            $entity->setStatus(Entity::STATUS_DELIVERED);
                                        }

                                        /* UPDATE NOTE */
                                        $note_array = array(
                                            "entity_id" => $entity->id,
                                            "sender_id" => $_SESSION['member_id'],
                                            "status" => 1,
                                            "type" => 3,
                                            "system_admin" => 1,
                                            "text" => "<green>" . "CREDIT CARD PROCESSED FOR THE AMOUNT OF $ " . number_format((float) $pay_arr['amount'], 2, '.', ''));
                                        $note = new Note($daffny->DB);
                                        $note->create($note_array);

                                        $data['success'] = "Your payment has been processed.";
                                        $out = array('success' => true, 'data' => $data);
                                    } else {
                                        $errors[] = $ret['error'];
                                        /* UPDATE NOTE */
                                        $note_array = array(
                                            "entity_id" => $entity->id,
                                            "sender_id" => $_SESSION['member_id'],
                                            "type" => 3,
                                            "system_admin" => 1,
                                            "text" => "<red>Payment Error:" . $ret['error']
                                        );
                                        $note = new Note($daffny->DB);
                                        $note->create($note_array);
                                        $out = array('success' => false, 'errors' => $errors);

                                        $mail = new FdMailer(true);
                                        $mail->isHTML();
                                        $mail->Body = 'Credit card is declined for ORDER# ' . $entity->number;
                                        $mail->Subject = "Payment Error:" . $ret['error'];
                                        $mail->AddAddress($entity->getAssigned()->email, $entity->getAssigned()->contactname);
                                        $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                                        
                                        try {
                                            $mail->send();
                                        } catch (Exception $e) {
                                            $note_array = array(
                                                "entity_id" => $entity->id,
                                                "sender_id" => $_SESSION['member_id'],
                                                "type" => 3,
                                                "system_admin" => 1,
                                                "text" => "<red>SMTP ERROR: Mail not Sent");

                                            $note = new Note($this->daffny->DB);
                                            $note->create($note_array);
                                        }
                                    }
                                }

                                $entity->updateHeaderTable();
                            }
                        break;
                        default:
                            $out = array("success" => false, "message" => "Cant Process payments");
                        break;
                    }
                break;
                case 'Unpost':
                    $entity = new Entity($daffny->DB);
                    $entity->load($_POST['entity_id']);
                    $entity->setStatus(1, true);
                    
                    sendDispatchMail($entity, $daffny, 2);
                    $out = array('success' => true);
                break;
                case 'UnpostMultiple':
                    if (isset($_POST['entity_ids'])) {
                        foreach ($_POST['entity_ids'] as $entity_id) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($_POST['entity_id']);
                            $entity->setStatus(1, true);
                            
                            sendDispatchMail($entity, $daffny, 2);
                        }

                        $out = array('success' => true);
                    }
                break;
                default:
                    $out = array("success" => false, "message" => "Invaid API Action");
                break;
            }
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'getDoc':
                    if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                        break;
                    }

                    $doc = new EntityDoc($daffny->DB);
                    $doc->load($_GET['id']);
                    $entity = new Entity($daffny->DB);
                    $entity->load($doc->entity_id);
                    $filePath = UPLOADS_PATH . "entities" . DIRECTORY_SEPARATOR . $doc->entity_id . DIRECTORY_SEPARATOR . $doc->filename;
                    if (file_exists($filePath)) {
                        header('Content-Type: application/pdf');
                        header("Content-Disposition: attachment; filename=\"" . $doc->name . ".pdf\"");
                        header("Content-Description: \"" . $doc->name . "\"");
                        header('Content-Length: ' . filesize($filePath));
                        header("Expires: 0");
                        header("Cache-Control: private");
                        header("Pragma: cache");
                        readfile($filePath);
                        exit;
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
        echo $e->getMessage();die;
        $out['message'] = $e;
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

function checkEmpty($value, $title) 
{
    $errors = array();
    if (trim($value) == "") {
        $errors[] = $title . " value required";
    }

    return $errors;
}

function processAuthorize($pay) 
{
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
            , 'invoice_num' => $pay['order_number'],
        )
    );
    $response = $transaction->authorizeAndCapture();
    if ($response->approved) {
        return array("success" => true
            , "transaction_id" => $response->transaction_id,
        );
    } else {
        return array("success" => false
            , "error" => $response->response_reason_text,
        );
    }
}

function processPayPal($pay) 
{
    $paypal = new DoDirectPayment();
    $paypal->Environment = $daffny->cfg['paypal_environment'];
    $paypal->apiUserName = $pay['paypal_api_username'];
    $paypal->apiPassword = $pay['paypal_api_password'];
    $paypal->apiSignature = $pay['paypal_api_signature'];

    $paypal->creditCardType = $pay["cc_type_name"];
    $paypal->creditCardNumber = $pay["cc_number"];
    $paypal->expDate = $pay["cc_month"] . $pay["cc_year"];
    $paypal->CVV2 = $pay["cc_cvv2"];

    $paypal->firstName = $pay["cc_fname"];
    $paypal->lastName = $pay["cc_lname"];
    $paypal->street = $pay["cc_address"];
    $paypal->city = $pay["cc_city"];
    $paypal->state = $pay['cc_state'];
    $paypal->countryCode = "US";
    $paypal->zip = $pay['cc_zip'];

    $paypal->amount = number_format($pay['amount'], 2, '.', ',');

    $response = $paypal->sendRequest();
    if ($response['ACK'] != "Success") {
        return array("success" => false
            , "error" => @$response['L_ERRORCODE0'] . " " . @$response['L_SHORTMESSAGE0'] . " " . @$response['L_LONGMESSAGE0'],
        );
    }

    return array("success" => true
        , "transaction_id" => @$response['TRANSACTIONID'],
    );
}

function processMDSIP($pay) 
{
    $api_login = $pay['gateway_api_username'];
    $api_pwd = $pay['gateway_api_password'];
    $api_amount = $pay['amount'];

    $pay_success = false;
    $pay_reason = "";
    $transaction_id = "";

    $gw = new gwapi;
    $gw->setLogin($api_login, $api_pwd);

    $gw->setBilling(
        $pay['cc_fname'], $pay['cc_lname'], $pay['company'], $pay['cc_address'], $pay['address2'], $pay['cc_city'], $pay['cc_state'], $pay['cc_zip'], "US", $pay['phone1'], $pay['phone2'], $pay['email'], "www.freightdragon.com");

    $gw->setShipping($pay['cc_fname'], $pay['cc_lname'], $pay['company'], $pay['cc_address'], $pay['address2'], $pay['cc_city'], $pay['cc_state'], $pay['cc_zip'], "US", $pay['email'], "www.freightdragon.com");

    $gw->setOrder($pay['orderid'], $pay['orderdescription'], $pay['tax'], $pay['shipping'], $pay['cc_zip'], $pay['ipaddress']);

    $r = $gw->doSale($api_amount, $pay["cc_number"], $pay["cc_month"] . $pay["cc_year"], $pay["cc_cvv2"]);

    $response = $gw->responses['responsetext'];
    if ($response == "APPROVED") {
        return array("success" => true
            , "transaction_id" => $gw->responses['transactionid'],
        );
    } else {
        return array("success" => false
            , "error" => $gw->responses['responsetext'],
        );
    }
}

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

// function to print ACh Invoices from Invoice Manager Screen
function GenerateACHPrintReceipt($InvoiceData, $daffny)
{

    // create folder if not exists
    if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
        mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
    }

    // create file
    $fileName = "ACH-Recipts-".date('Y-m-d his').".pdf";
    $fullPath = ROOT_PATH."uploads/Invoices/ACH/".$fileName;
    
    ob_start();
    $mpdf = new mPDF('utf-8', array(190,95) );
    for($i=0; $i<count($InvoiceData); $i++){
        //$mpdf->AddPage();
        $fileContents = "<h2>Freight Dragon</h2>";
        $fileContents .= "<p>ACH Reciept</p>";
        $fileContents .= "<p>Issued On : ".date('m-d-Y')."</p>";
        $fileContents .= "<table style='width:100%;'>";
        $fileContents .= "<tr>";
        $fileContents .= "<td>Order ID:</td><td>".$InvoiceData[$i]['OrderID']."</td>";
        $fileContents .= "</tr>";
        $fileContents .= "<tr>";
        $fileContents .= "<td>Carrier:</td><td>".$InvoiceData[$i]['CarrierName']."</td>";
        $fileContents .= "</tr>";
        $fileContents .= "<tr>";
        $fileContents .= "<td>Amount:</td><td>$".$InvoiceData[$i]['Amount']."</td>";
        $fileContents .= "</tr>";
        $fileContents .= "<tr>";
        $fileContents .= "<td>Age:</td><td>".$InvoiceData[$i]['Age']." Days</td>";
        $fileContents .= "</tr>";
        $fileContents .= "<tr>";
        $fileContents .= "<td>Upload Date:</td><td>".$InvoiceData[$i]['CreatedAt']."</td>";
        $fileContents .= "</tr>";
        $fileContents .= "</table>";
        $mpdf->WriteHTML($fileContents);

        $sql = "SELECT count(*) as `Number` FROM app_payments WHERE `entity_id` = ".$InvoiceData[$i]['EntityID'];
        $PaymentNumber = $daffny->DB->query($sql);
        $PaymentNumber = mysqli_fetch_assoc($PaymentNumber)['Number'];

        $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount,method, transaction_id,entered_by)";
        $sql .= "VALUES( '".$InvoiceData[$i]['EntityID']."', '".($PaymentNumber+1)."', '".date('Y-m-d')."', '1', '3', '".$InvoiceData[$i]['Amount']."','6','".$InvoiceData[$i]['TxnID']."','".$_SESSION['member']['id']."' )";
        $res = $daffny->DB->query($sql);
        $insertedPayID = $daffny->DB->get_insert_id();

        $currentDate = date('Y-m-d h:i:s');
        $sql = "UPDATE Invoices SET PaidDate = '".$currentDate."', PaymentID = '".$insertedPayID."', TxnID = '".$InvoiceData[$i]['TxnID']."' WHERE `ID` = ".$InvoiceData[$i]['ID'];
        $PaymentNumber = $daffny->DB->query($sql);
        
        $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
        $NoteMessage = "<green>Carrier has been paid amount $ ".number_format((float) $InvoiceData[$i]['Amount'], 2, ".", ",")." by Electronic transfer";
        $sql .= "VALUES( '".$InvoiceData[$i]['EntityID']."', '".$_SESSION['member_id']."','3', '".$NoteMessage."', '1', '1')";
        $res = $daffny->DB->query($sql);        
    }
    
    ob_end_clean();
    $res = $mpdf->Output($fullPath);

    return $fileName;
}

// Fetch Invoice Data
function GetInvoiceData($daffny)
{
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
            $invoiceDataArray[$i]['DispatchIds'] = $dispatchIds;

            $totalAmount = $totalAmount + $Amount;

            // update in payments table
            $sql = "SELECT count(*) as `Number` FROM app_payments WHERE `entity_id` = ".$EntityID;
            $PaymentNumber = $daffny->DB->query($sql);
            $PaymentNumber = mysqli_fetch_assoc($PaymentNumber)['Number'];

            $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount,method,entered_by)";
            $sql .= "VALUES( '".$EntityID."', '".($PaymentNumber+1)."', '".date('Y-m-d')."', '1', '3', '".$Amount."','2','".$_SESSION['member']['id']."' )";
            $res = $daffny->DB->query($sql);
            
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

// function to format phone number in US format
function format_phone_us($phone)
{
    // note: making sure we have something
    if (!isset($phone{3})) {return '';}
    // note: strip out everything but numbers
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);
    switch ($length) {
        case 7:
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
            break;
        case 10:
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
            break;
        case 11:
            return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1($2) $3-$4", $phone);
            break;
        default:
            return $phone;
            break;
    }
}

echo $json->encode($out);
require_once "done.php";
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
