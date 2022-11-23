<?php

/*
 * Class made specifically to deal with the account level stuff in the generic scope
 * 
 * @author  Chetu Inc.
 * @lastUpdatedDate 15012018 
 */
require_once(ROOT_PATH . "/libs/signature/signature.php");
require_once(ROOT_PATH . "/libs/mpdf/mpdf.php");
require_once(ROOT_PATH . "/libs/QuickBooks.php");

class AppAccount extends AppAction{
    
    /*
     * This is the thank you page rendere function which comes after sucessfull
     * completion of contract upload
     */
    public function acknoledgement(){
        try{
            /* calling HTML view */
            $this->daffny->tpl->status = "You are all set!";
            $this->daffny->tpl->message = "B2B Account Contract Updated Successfully!, for more information<br> or some query please contact your member.";
            $this->tplname = "account.success";
        } catch(Exception $e){
            print_r($e);
        }
    }    

    /**
     * This function provide UI to update the
     * 
     * @version 1.0
     * @author Chetu Inc.
     */
    public function B2BOrderTerms(){
        
        $id = $_GET['id'];
        $id = substr($id, 10);
        
        $account = new Account($this->daffny->DB);
        $account->load($id);
        
        $data = $this->daffny->DB->query("SELECT `owner_id`,`commercial_terms`,`commercial_terms_updated_at`,`commercial_terms_updated_by` FROM `app_defaultsettings` WHERE `owner_id` = (SELECT `parent_id` FROM `members` WHERE `id` = '".$account->owner_id."') ");
        $data = mysqli_fetch_assoc($data);
        
        //die(print_r($data));
        
        $this->daffny->tpl->account = $account;
        if(isset($_POST['submit'])){
            
            $keyData = $this->daffny->DB->query("SELECT `accessKey` FROM `api_access_key` WHERE `status` = 1");
            $keyData = mysqli_fetch_assoc($keyData);
            $key = $keyData['accessKey'];
            
            /* error array */
            $error = array();
            
            /* validations section 1*/
            if(isset($_POST['sfname']) && $_POST['sfname']==""){
                $error[] = "Shipper first name cannot be empty.";
            }
            if(isset($_POST['slname']) && $_POST['slname']==""){
                $error[] = "Shipper last name cannot be empty.";
            }
            if(isset($_POST['scompany']) && $_POST['scompany']==""){
                $error[] = "Shipper Company cannot be empty.";
            }
            if(isset($_POST['shours']) && $_POST['shours']==""){
                $error[] = "Shipper Hours cannot be empty.";
            }
            if(isset($_POST['sEmail']) && $_POST['sEmail']==""){
                $error[] = "Shipper Email cannot be empty.";
            }
            if(isset($_POST['sphone']) && $_POST['sphone']==""){
                $error[] = "Shipper Phone cannot be empty.";
            }
            
            /* validations section 2*/
            if(isset($_POST['fname1']) && $_POST['fname1']==""){
                $error[] = "Company First Name cannot be empty.";
            }
            if(isset($_POST['lname1']) && $_POST['lname1']==""){
                $error[] = "Company Last Name cannot be empty.";
            }
            if(isset($_POST['title1']) && $_POST['title1']==""){
                $error[] = "Company Title cannot be empty.";
            }
            if(isset($_POST['email1']) && $_POST['email1']==""){
                $error[] = "Company Email cannot be empty.";
            }
            
            /* validations section 3*/
            if(isset($_POST['ein']) && $_POST['ein']==""){
                $error[] = "EIN cannot be empty.";
            }
            
            if(isset($_POST['payment_method']) && $_POST['payment_method']==""){
                $error[] = "Payment Method cannot be empty.";
            } else {
                
                /* when payment method is credit card */
                if($_POST['payment_method']=="3"){
                    if(isset($_POST['ccFname']) && $_POST['ccFname']==""){
                        $error[] = "Credit card first name cannot be empty.";
                    }
                    if(isset($_POST['ccLname']) && $_POST['ccLname']==""){
                        $error[] = "Credit card last name cannot be empty.";
                    }
                    if(isset($_POST['ccType']) && $_POST['ccType']==""){
                        $error[] = "Credit card type cannot be empty.";
                    }
                    if(isset($_POST['ccCvv']) && $_POST['ccCvv']==""){
                        $error[] = "Credit card CVV cannot be empty.";
                    }
                    if(isset($_POST['ccMonth']) && $_POST['ccMonth']==""){
                        $error[] = "Credit card expiry month cannot be empty.";
                    }
                    if(isset($_POST['ccYear']) && $_POST['ccYear']==""){
                        $error[] = "Credit card expiry year cannot be empty.";
                    }
                    if(isset($_POST['ccNumber']) && $_POST['ccNumber']==""){
                        $error[] = "Credit card number cannot be empty.";
                    }
                }
                /* when payment method is ACH */
                if($_POST['payment_method']=="4"){
                    if(isset($_POST['bName']) && $_POST['bName']==""){
                        $error[] = "Banking Name cannot be empty.";
                    }
                    if(isset($_POST['bAccountNumber']) && $_POST['bAccountNumber']==""){
                        $error[] = "Bank Account Number cannot be empty.";
                    }
                    if(isset($_POST['bRouting']) && $_POST['bRouting']==""){
                        $error[] = "Bank Routing Number cannot be empty.";
                    }             
                }
            }
            
            /* validations section signature*/
            if(isset($_POST['sign_name']) && $_POST['sign_name']==""){
                $error[] = "Signature cannot be empty.";
            }
            
            $this->daffny->tpl->errors = $error;
            
            if(count($error) == 0){
            
            $signature = new Signature();
            $signature->setFontFile(ROOT_PATH . "libs/signature/jenna_sue.ttf");
            $sign = $signature->create(400, 100, "text", $_POST['sign_name']);
            
            $signatureTempImage = md5(mt_rand() . time()).'.png';
            file_put_contents($signatureTempImage, $sign, FILE_APPEND | LOCK_EX);
            $sign = '<img src="'.ROOT_PATH.'/'.$signatureTempImage.'">';
            
            if ($_POST['payment_method'] == 3) {
                $creditCardInfoUI = '<dt class="creditCardInfo">Credit Card Information</dt>
            <dd class="creditCardInfo">
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label">First Name:</td>
                            <td  class="">'.$_POST['ccFname'].'</td>
                            <td class="label">Last Name:</td>
                            <td  class="">'.$_POST['ccLname'].'</td>
                        </tr>
                        <tr>
                            <td class="label">Type:</td>
                            <td  class="">
                                '.$_POST['ccType'].'
                            </td>
                            <td class="label">Card Number:</td>
                            <td  class="">'.$_POST['ccNumber'].'</td>
                        </tr>
                        <tr>
                            <td class="label">CVV:</td>
                            <td  class="">
                                '.$_POST['ccCvv'].'
                            </td>
                            <td class="label">Exp. Date:</td>
                            <td  class="">
                                '.$_POST['ccMonth'].' / '.$_POST['ccYear'].'
                        </tr>
                        <tr>
                            <td class="label">Address:</td>
                            <td  class="">'.$_POST['ccAddress'].'</td>
                            <td class="label">City:</td>
                            <td  class="">'.$_POST['ccCity'].'</td>
                        </tr>
                        <tr>
                            <td class="label">State:</td>
                            <td  class="">'.$_POST['ccState'].'</td>
                            <td class="label">Zip Code:</td>
                            <td  class="">'.$_POST['ccZip'].'</td>
                        </tr>
                    </tbody>
                </table>
            </dd>';
            } elseif($_POST['payment_method'] == 4) {
                $creditCardInfoUI = '<dt class="creditCardInfo">ACH Information</dt>
            <dd class="creditCardInfo">
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label">Banking Name:</td>
                            <td  class="">'.$_POST['bName'].'</td>
                            <td class="label">Account Number:</td>
                            <td  class="">'.$_POST['bAccountNumber'].'</td>
                        </tr>
                        <tr>
                            <td class="label">Bank Routing Number:</td>
                            <td  class="">'.$_POST['bRouting'].'</td>
                            <td class="label">Bank Address:</td>
                            <td  class="">'.$_POST['bAddress'].'</td>
                        </tr> 
                    </tbody>
                </table>
            </dd>';
            } else {
                $creditCardInfoUI = '';
            }
            
            if ($_POST['payment_method'] == 1) {
                $paymentMethod = "Company Check";
            } elseif ($_POST['payment_method'] == 2)  {
                $paymentMethod = "Wire Check";
            } elseif ($_POST['payment_method'] == 3) {
                $paymentMethod = "Credit Card";
            } else {
                $paymentMethod = "ACH";
            }
            
            /* section 1 PDF UI*/
            $shipperInformation= '<dt>1. Shipper Information.</dt>
            <dd>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label" >First Name: <span class="required">*</span></td>
                            <td class="">
                                '.$_POST['sfname'].'
                            </td>
                            <td class="label" >Last Name: <span class="required">*</span></td>
                            <td class=""  >
                                '.$_POST['slname'].'
                            </td>
                        </tr>
                         <tr>
                            <td class="label" >Company: <span class="required">*</span></td>
                            <td class="">
                                '.$_POST['scompany'].'
                            </td>
                            <td class="label" >Shipper Type: <span class="required">*</span></td>
                            <td class=""  >
                                Commercial
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >Hours: <span class="required">*</span></td>
                            <td class="">
                                '.$_POST['shours'].'
                            </td>
                            <td class="label" >Email: <span class="required">*</span></td>
                            <td class=""  >
                                '.$_POST['sEmail'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >Phone: <span class="required">*</span></td>
                            <td class="">
                                '.$_POST['sphone'].'
                            </td>
                            <td class="label" >Phone2:</td>
                            <td class=""  >
                                '.$_POST['sphone2'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >Mobile:</td>
                            <td class="">
                                '.$_POST['sMobile'].'
                            </td>
                            <td class="label" >Fax:</td>
                            <td class=""  >
                                '.$_POST['sFax'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >Address:</td>
                            <td class="">
                                '.$_POST['sAddress'].'
                            </td>
                            <td class="label" >Address2:</td>
                            <td class=""  >
                                '.$_POST['sAddress2'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >City:</td>
                            <td class="">
                                '.$_POST['sCity'].'
                            </td>
                            <td class="label" >State:</td>
                            <td class=""  >
                                '.$_POST['sState'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >Zip:</td>
                            <td class="">
                                '.$_POST['sZip'].'
                            </td>
                            <td class="label" >Country:</td>
                            <td class=""  >
                                '.$_POST['sCountry'].'
                            </td>
                        </tr>
                        </tbody>
                </table>
            </dd>';
            
            /* section 2 PDF UI*/
            $additionalCompanyInformation = '<dt>2. Additional Company Information.</dt>
            <dd>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label" >1-First Name: <span class="required">*</span></td>
                            <td class="">
                                '.$_POST['fname1'].'
                            </td>
                            <td class="label" >1-Last Name: <span class="required">*</span></td>
                            <td class=""  >
                                '.$_POST['lname1'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >1-Title: <span class="required">*</span></td>
                            <td class="" >
                                '.$_POST['title1'].'
                            </td>
                            <td class="label" >1-Email: <span class="required">*</span></td>
                            <td class="" >
                                '.$_POST['email1'].'
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label" >2-First Name:</td>
                            <td class="">
                                '.$_POST['fname2'].'
                            </td>
                            <td class="label" >2-Last Name:</td>
                            <td class=""  >
                                '.$_POST['lname2'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >2-Title:</td>
                            <td class="" >
                                '.$_POST['title2'].'
                            </td>
                            <td class="label" >2-Email:</td>
                            <td class="" >
                                '.$_POST['email2'].'
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label" >3-First Name:</td>
                            <td class="">
                                '.$_POST['fname3'].'
                            </td>
                            <td class="label" >3-Last Name:</td>
                            <td class=""  >
                               '.$_POST['lname3'].'
                            </td>
                        </tr>
                        <tr>
                            <td class="label" >3-Title:</td>
                            <td class="" >
                                '.$_POST['title3'].'
                            </td>
                            <td class="label" >3-Email:</td>
                            <td class="" >
                                '.$_POST['email3'].'
                            </td>
                        </tr>
                    </tbody>
                </table>
            </dd>';
            
            /* section 3 PDF UI*/
            $paymentInfoUI = '<dt>3. Payment Method.</dt>
            <dd>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label" >Payment Method: <span class="required">*</span></td>
                            <td class="">
                                '.$paymentMethod.'
                            </td>
                            <td class="label" >&nbsp;</td>
                            <td class=""  >&nbsp;</td>
                        </tr>                        
                    </tbody>
                </table>                
            </dd>'.$creditCardInfoUI;
            
            $html = '
                <style type="text/css">
                    body {
                        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif; font-size: 13px; color: #444; margin: 3px; padding: 0px;
                    }
                    .wrapper {
                        width: 860px;
                        margin: auto;
                        padding: 1em 0;
                        background: url(@company_logo@) .5em .86em no-repeat;
                    }
                    .left { float: left; padding-right: 10px; }
                    .right { float: left; }
                    .underline, .underline_strong { border-bottom: 1px dotted #bbb; font-size: 14px; }
                    .underline_strong { font-weight: bold; }
                    .label, .label_strong { text-align: right; font-size: 12px; font-weight: bold; white-space: nowrap; }
                    .label_strong { color: #000; }
                    .form-header {
                        font-weight: bold;
                        font-size: 220%;
                        color: #891;
                        text-align: center;
                        padding: .5em;

                    }
                    dl.form-list {
                        list-style: none;
                        margin: 0;
                        padding: .5em 0;
                    }
                    dl.form-list dt {
                        display: block;
                        clear: both;
                        margin: 1em 0;
                        padding: .4em .8em;
                        font-weight: bold;
                        font-size: 120%;
                        color: #fff;
                        border-radius: 5px;
                        background: #38b;
                    }
                    dl.form-list dd {
                        display: block;
                        margin: 0;
                        padding: 0 0 2em;
                    }
                    dl.form-list dd:before, dl.form-list dd:after { content: ""; display: table; }
                    dl.form-list dd:after { clear: both; }
                    dl.form-list dd { *zoom: 1; }
                    .form-fields {
                        width: 100%;
                    }
                    .form-fields td {
                        padding: .2em 1em;
                    }
                    .print-section {
                        border-radius: 5px;
                        border: 1px solid #e5e7e0;

                    }
                    .print_section_header2 {
                        text-align: center;
                        font-weight: bold;
                        font-style: italic;
                        font-size: 120%;
                        color: #891;

                    }
                    .form_header { width: 670px; font-weight: bold; font-size: 16px; text-align: center; }
                    .company_address_info { width: 420px; float: left; vertical-align: top; padding-bottom: 0px; }
                    .company_name { font-size: 150%; font-weight: bold; vertical-align: top; }
                    .sales_contact_info { width: 260px; padding-bottom: 0px; }
                    .opening_info { padding: 1em; }
                    .customer_info { width: 401px; float: left; vertical-align: top; }
                    .customer_info td { text-align: left; vertical-align: middle; }
                    .customer_info .right { padding-left: 10px; }
                    .customer_info .left .underline { width: 120px }
                    .customer_info .right .underline { width: 130px; }
                    .price_and_ship { width: 290px; float: right; vertical-align: top; }
                    .price_and_ship td { width: 115px; vertical-align: middle; }
                    .price_and_ship .right .underline { width: 90px; }
                    .payments { padding-top: 0px; padding-bottom: 0px; }
                    .transit_directives td { vertical-align: middle; }
                    .transit_directives .right { padding-left: 50px; }
                    .transit_directives .left .underline, .transit_directives .right .underline { width: 200px; }
                    .vehicle-table { border-collapse: collapse }
                    .vehicle-table td, .vehicle-table th { border: 1px solid #38b; }
                    .vehicle-table td { background-color: #fff; text-align: left; padding: 3px 5px }
                    .vehicle-table th { background-color: #eee; text-align: center; font-weight: bold; padding: 3px 20px }
                    .deposit .left .underline { width: 230px; }
                    .deposit .right .underline { width: 200px; }
                    .agree .underline { width: 200px; }
                    .agree .underline_long { width: 350px; border-bottom: 1px solid #000000; }
                    .required{float:right !important; margin-left: 1px;}
                </style>
            <div class="form-header">Commercial Contract</div>
            <div class="print-section" style="border:1px solid #ccc; padding:10px;">
            <table class="form-fields">
                <colgroup>
                    <col span="2" width="50%" />
                </colgroup>
                <tbody>
                    <tr valign="top">
                        <td width="50%">
                            <div class="company_address_info">
                                <span class="company_name">'.$account->company_name.'</span><br />
                                '.$account->address1.'<br />
                                '.$account->city.', '.$account->state.' '.$account->zip_code.'</div>
                        </td>
                        <td width="50%"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <dl class="form-list">
            '.$shipperInformation.'
            '.$additionalCompanyInformation.'
            '.$paymentInfoUI.'            
            <dt>4. Additional Information</dt>
            <dd>
                <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                    <tbody>
                        <tr>
                            <td class="label">EIN #:</td>
                            <td  class="">'.$_POST['ein'].'</td>
                            <td class="label">DUNS:</td>
                            <td  class="">'.$_POST['duns'].'</td>
                        </tr>                        
                    </tbody>
                </table>
            </dd>            
            <dt>
                Updated Commercial Contract</dt>
            <dd style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                '.$data['commercial_terms'].'
            </dd>
            <br>
            <dt>Agreed &amp; Accepted</dt>
            <p style="text-align:left; width:100%;">I have read, and understand, the attached Terms and Conditions and I intend, and agree, to be bound by them</p>
            <p style="text-align:right; width:100%;">'.$sign.'</p>
            <p style="text-align:right; width:100%;"> '.date('m/d/Y').'</p>
        </dl> 
           ';
            
            $mpdf=new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML();
            $mpdf->WriteHTML(utf8_encode($html));
            
            $mpdf->SetAuthor('RW');
            $mpdf->SetSubject("B2B Contract");
            $mpdf->SetTitle("B2B Contract");
            $mpdf->SetCreator("FreightDragon.com");
            
            /* naming pdf file*/
            $fileName = md5(mt_rand() . time()).".pdf";
            $filePath = UPLOADS_PATH . 'accounts/contracts/'.$fileName;
            makePath(UPLOADS_PATH . 'accounts/contracts/', false);
            
            /* uploading file in contracts folder*/            
            unlink($signatureTempImage);
            
            $mpdf->Output($filePath, 'F');
            
            $this->daffny->DB->query("UPDATE app_account_contracts SET `status` =0 WHERE `account_id` ='".$account->id."' ");
            
            $ins_arr = array(
                'owner_id' => $account->owner_id,
                'account_id' => $account->id,
                'name_original' => "B2B Contract " . date("Y-m-d") . ".pdf",
                'name_on_server' => $fileName,
                'size' => filesize($filePath),
                'type' => "pdf",
                'uploaded_at' => date("Y-m-d H:i:s"), 
                'uploaded_by' => $account->id, 
                'uploaded_by_name' => 'Shipper: '.$account->first_name." ".$account->last_name, 
                'version'=>$data['commercial_terms_updated_at'],
                'status'=>1
            );

            $this->daffny->DB->insert("app_account_contracts", $ins_arr);
            
            $ins_arr = array(
                'account_id' => $account->id,
                'sfname' => $_POST['sfname'],
                'slname' => $_POST['slname'],
                'scompany' => $_POST['scompany'],
                'shours' => $_POST['shours'],
                'sEmail' => $_POST['sEmail'],
                'sphone' => $_POST['sphone'],
                'sphone2' => $_POST['sphone2'],
                'sMobile' => $_POST['sMobile'],
                'sFax' => $_POST['sFax'],
                'sAddress' => $_POST['sAddress'],
                'sAddress2' => $_POST['sAddress2'],
                'sCity' => $_POST['sCity'],
                'sState' => $_POST['sState'],
                'sZip' => $_POST['sZip'],
                'sCountry' => $_POST['sCountry'],                
                'fname1' => $_POST['fname1'],
                'lname1' => $_POST['lname1'],
                'title1' => $_POST['title1'],
                'email1' => $_POST['email1'],
                'fname2' => $_POST['fname2'],
                'lname2' => $_POST['lname2'],
                'title2' => $_POST['title2'],
                'email2' => $_POST['email2'],
                'fname3' => $_POST['fname3'],
                'lname3' => $_POST['lname3'],
                'title3' => $_POST['title3'],
                'email3' => $_POST['email3'],                
                'ein' => $_POST['ein'],
                'duns' => $_POST['duns'],
                'payment_type' => $_POST['payment_method'],
                'eSigner' => $_POST['sign_name']
            );            
            $this->daffny->DB->insert("app_account_contract_data", $ins_arr);
            
            if( $_POST['payment_method'] == 3){
                 $ins_arr = array(
                    'account_id' => $account->id,
                    'payment_type' => $_POST['payment_method'],
                    'ccFname' => $_POST['ccFname'],
                    'ccLname' => $_POST['ccLname'],
                    'ccType' => $_POST['ccType'],
                    'ccNumber' => $_POST['ccNumber'],
                    'ccCvv' => $_POST['ccCvv'],
                    'ccMonth' => $_POST['ccMonth'],
                    'ccYear' => $_POST['ccYear'],
                    'ccAddress' => $_POST['ccAddress'],
                    'ccCity' => $_POST['ccCity'],
                    'ccState' => $_POST['ccState'],
                    'ccZip' => $_POST['ccZip'],
                );
                 $encryptionWithQuery = "INSERT INTO `app_account_contract_payment_info` "
                    . "( account_id, payment_type, ccFname, ccLname, ccType, ccNumber, ccCvv, ccMonth, ccYear, ccAddress, ccCity, ccState, ccZip)"
                    . " VALUES ('".$account->id."','".$_POST['payment_method']."', "
                    . "AES_ENCRYPT('".$_POST['ccFname']."', '".$key."'), AES_ENCRYPT('".$_POST['ccLname']."', '".$key."'),"
                    . "AES_ENCRYPT('".$_POST['ccType']."', '".$key."'), AES_ENCRYPT('".$_POST['ccNumber']."', '".$key."'),"
                    . "AES_ENCRYPT('".$_POST['ccCvv']."', '".$key."'), AES_ENCRYPT('".$_POST['ccMonth']."', '".$key."'),"
                    . "AES_ENCRYPT('".$_POST['ccYear']."', '".$key."'), '".$_POST['ccAddress']."', '".$_POST['ccCity']."', '".$_POST['ccState']."', '".$_POST['ccZip']."' )";
            } elseif ($_POST['payment_method'] == 4) {
                $encryptionWithQuery = "INSERT INTO `app_account_contract_payment_info` "
                    . "( account_id, payment_type, bName, bAccountNumber, bRouting)"
                    . " VALUES ('".$account->id."','".$_POST['payment_method']."', "
                    . "AES_ENCRYPT('".$_POST['bName']."', '".$key."'), AES_ENCRYPT('".$_POST['bAccountNumber']."', '".$key."'),"
                    . "AES_ENCRYPT('".$_POST['bRouting']."', '".$key."') )";
            } else {               
                $encryptionWithQuery = "INSERT INTO `app_account_contract_payment_info` "
                    . "( account_id, payment_type)"
                    . " VALUES ('".$account->id."','".$_POST['payment_method']."')";
            }
            
            $this->daffny->DB->hardQuery($encryptionWithQuery);
            
            redirect(getLink('account','acknoledgement'));
        }            
        }
        $this->daffny->tpl->contract = $data['commercial_terms'];  
        $this->daffny->tpl->parent_id = $data['owner_id'];  
        $this->tplname = "account.b2bAccountContractRenew";
    }
    
     /**
     * This function is used to view the account contract on the browser based on
     * the upload id
     * 
     * @return header pdf file in the http header
     * @version 1.0
     * @author Chetu Inc.
     */
    public function contractPreview(){        
        $ID = (int) get_var("id");
        $file = $this->daffny->DB->select_one("*", "app_account_contracts", "WHERE id = '" . $ID ."'");
        if (!empty($file)) {
            $file_path = UPLOADS_PATH . "accounts/contracts/" . $file["name_on_server"];

            $file_name = $file["name_original"];
            $file_size = $file["size"];
            if (file_exists($file_path)) {
                header("Content-Type: application; filename=\"" . $file_name . "\"");
                header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                header("Content-Description: \"" . $file_name . "\"");
                header("Content-length: " . $file_size);
                header("Expires: 0");
                header("Cache-Control: private");
                header("Pragma: cache");
                $fptr = @fopen($file_path, "r");
                $buffer = @fread($fptr, filesize($file_path));
                @fclose($fptr);
                echo $buffer;
                exit(0);
            }
        }
        header("HTTP/1.0 404 Not Found");
        exit(0);
    }
}