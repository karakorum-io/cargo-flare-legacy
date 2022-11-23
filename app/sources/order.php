<?php

require_once ROOT_PATH . "/libs/signature/signature.php";
require_once ROOT_PATH . "/libs/mpdf/mpdf.php";

class AppOrder extends AppAction
{
    public function confirm_old()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);
            $formTemplate = new FormTemplate($this->daffny->DB);
            $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER, $order->getAssigned()->parent_id);
            $formData = EmailTemplate::fillParams($order, array_fill_keys($this->daffny->tpl->get_vars($formTemplate->body), ''), EmailTemplate::SEND_TYPE_HTML, true);
            $this->input['form'] = $this->daffny->tpl->get_parsed_from_array($formTemplate->body, $formData);

            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.confirm";
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function confirm()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $data = $this->daffny->DB->selectRow(' `id`, `hash`, `esigned`, `esigned_doc` ', 'app_entities', "WHERE `expired_hash` LIKE '" .$_GET['hash'] . "'");

            if($data['esigned'] == 1){
                redirect(getLink('order','esignthanks','hash', $data['hash'],'&id=',$data['esigned_doc']));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);

            $formTemplate = new FormTemplate($this->daffny->DB);
            $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER, $order->getAssigned()->parent_id);
            
            $formTags = array_fill_keys($this->daffny->tpl->get_vars($formTemplate->body), '');
            $formData = EmailTemplate::fillParams($order, $formTags, EmailTemplate::SEND_TYPE_HTML, true);

            $configData['company_logo'] = null;
            $configData = EmailTemplate::fillParams($order, $configData, EmailTemplate::SEND_TYPE_HTML, true);

            $this->input['company_logo'] = getTagData($configData, "company_logo");
            foreach($formData as $key => $value){
                $this->input[$key] = $value;
            }
            
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.e-sign-confirmation";
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function confirm_total()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);
            $formTemplate = new FormTemplate($this->daffny->DB);
            $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER_ESIGN_TOTAL, $order->getAssigned()->parent_id);
            $formData = EmailTemplate::fillParams($order, array_fill_keys($this->daffny->tpl->get_vars($formTemplate->body), ''), EmailTemplate::SEND_TYPE_HTML, true);
            $this->input['form'] = $this->daffny->tpl->get_parsed_from_array($formTemplate->body, $formData);

            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.confirm_esign_total";
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function esignthanks_old()
    {

        try {
            //if (!isset($_GET['hash']))
            //redirect(getLink(''));

            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);

            $companyname = "";
            try {

                $member = $order->getAssigned();
                $company = $member->getCompanyProfile();
                $companyname = $company->companyname;

            } catch (Exception $e) {}

            $this->daffny->tpl->companyname = $companyname;

            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.esignthanks";

        } catch (FDException $e) {
            //redirect(getLink(''));
            //print $e;
        }

    }

    public function esignthanks()
    {

        try {
            
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);

            $formTemplate = new FormTemplate($this->daffny->DB);
            $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER, $order->getAssigned()->parent_id);

            $configData['company_logo'] = null;
            $configData = EmailTemplate::fillParams($order, $configData, EmailTemplate::SEND_TYPE_HTML, true);

            $this->input['company_logo'] = getTagData($configData, "company_logo");

            $companyname = "";
            try {

                $member = $order->getAssigned();
                $company = $member->getCompanyProfile();
                $companyname = $company->companyname;

            } catch (Exception $e) {}

            $this->daffny->tpl->companyname = $companyname;
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.e-sign-thanks";

        } catch (FDException $e) {
            redirect(getLink(''));
        }

    }

    public function bbthanks()
    {
        try {
            //if (!isset($_GET['hash']))
            //redirect(getLink(''));
            $order = new Entity($this->daffny->DB);
            $order->loadByHash($_GET['hash']);

            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.bbthanks";
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function confirmCommercial()
    {

        try {

            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);

            $order->loadByHash($_GET['hash']);
            if ($order->account_id != 0) {
                $accountShipper = $order->getAccount();
            }

            $contract_firstname1 = trim($_POST['contract_firstname1']);
            $contract_firstname2 = trim($_POST['contract_firstname2']);
            $contract_firstname3 = trim($_POST['contract_firstname3']);
            $contract_lastname1 = trim($_POST['contract_lastname1']);
            $contract_lastname2 = trim($_POST['contract_lastname2']);
            $contract_lastname3 = trim($_POST['contract_lastname3']);
            $contract_title1 = trim($_POST['contract_title1']);
            $contract_title2 = trim($_POST['contract_title2']);
            $contract_title3 = trim($_POST['contract_title3']);
            $contract_email1 = trim($_POST['contract_email1']);
            $contract_email2 = trim($_POST['contract_email2']);
            $contract_email3 = trim($_POST['contract_email3']);
            $ein = trim($_POST['ein']);
            $duns = trim($_POST['duns']);
            $payment_method = trim($_POST['payment_method']);

            $e_cc_type = trim($_POST['e_cc_type']);
            $e_cc_address = trim($_POST['e_cc_address']);
            $e_cc_fname = trim($_POST['e_cc_fname']);
            $e_cc_number = trim($_POST['e_cc_number']);
            $e_cc_city = trim($_POST['e_cc_city']);
            $e_cc_lname = trim($_POST['e_cc_lname']);
            $e_cc_cvv2 = trim($_POST['e_cc_cvv2']);
            $e_cc_state = trim($_POST['e_cc_state']);
            $e_cc_month = trim($_POST['e_cc_month']);
            $e_cc_year = trim($_POST['e_cc_year']);
            $e_cc_zip = trim($_POST['e_cc_zip']);

            $terms = trim($_POST['terms']);
            $signature_terms = trim($_POST['signature_terms']);

            $sign_name = trim($_POST['sign_name']);
            $notes = trim($_POST['notes']);

            if ($_POST['submit'] == "Save") //&& $order->esigned!=2
            {
                $shipper_email = trim($_POST['shipper_email']);
                $shipper_fname = trim($_POST['shipper_fname']);
                $shipper_lname = trim($_POST['shipper_lname']);
                $shipper_phone1 = trim($_POST['shipper_phone1']);
                $shipper_address2 = trim($_POST['shipper_address2']);
                $shipper_phone2 = trim($_POST['shipper_phone2']);
                $shipper_city = trim($_POST['shipper_city']);
                $shipper_company = trim($_POST['shipper_company']);
                $shipper_mobile = trim($_POST['shipper_mobile']);
                $shipper_state = trim($_POST['shipper_state']);
                $shipper_zip = trim($_POST['shipper_zip']);
                $shipper_type = trim($_POST['shipper_type']);
                $shipper_fax = trim($_POST['shipper_fax']);
                $shipper_country = trim($_POST['shipper_country']);
                $shipper_hours = trim($_POST['shipper_hours']);

                $successMsg = "";
                $errorMsg = "";

                if ($shipper_fname == "") {
                    $errorMsg .= "<li>Please fill Shipper First Name</li>";
                } elseif ($shipper_lname == "") {
                    $errorMsg .= "<li>Please fill Shipper Last name</li>";
                } elseif ($shipper_email == "") {
                    $errorMsg .= "<li>Please fill Shipper Email</li>";
                } elseif ($shipper_phone1 == "") {
                    $errorMsg .= "<li>Please fill Shipper Phone</li>";
                } elseif ($contract_firstname1 == "") {
                    $errorMsg .= "<li>Please fill Contract Firstname1</li>";
                } elseif ($contract_lastname1 == "") {
                    $errorMsg .= "<li>Please fill Contract Lastname1</li>";
                } elseif ($contract_title1 == "") {
                    $errorMsg .= "<li>Please fill Contract Title1</li>";
                } elseif ($contract_email1 == "") {
                    $errorMsg .= "<li>Please fill Contract Email1</li>";
                } elseif ($ein == "") {
                    $errorMsg .= "<li>Please fill EIN#</li>";
                } elseif ($payment_method == "") {
                    $errorMsg .= "<li>Please select Payment Method#</li>";
                } elseif ($payment_method == 3) {

                    if ($e_cc_fname == "") {
                        $errorMsg .= "<li>Please fill Firstname on Credit Card</li>";
                    }

                    if ($e_cc_lname == "") {
                        $errorMsg .= "<li>Please fill Lastname on Credit Card</li>";
                    }

                    if ($e_cc_type == "") {
                        $errorMsg .= "<li>Please select Credit Card type</li>";
                    }

                    if ($e_cc_number == "") {
                        $errorMsg .= "<li>Please fill Credit Card number</li>";
                    }

                    if ($e_cc_cvv2 == "") {
                        $errorMsg .= "<li>Please fill Credit Card cvv</li>";
                    }

                    if ($e_cc_year == "" || $e_cc_month == "") {
                        $errorMsg .= "<li>Please select Credit Card exp date</li>";
                    }

                    if ($e_cc_address == "") {
                        $errorMsg .= "<li>Please fill Credit Card address</li>";
                    }

                    if ($e_cc_city == "" || $e_cc_state == "" || $e_cc_zip == "") {
                        $errorMsg .= "<li>Please fill Credit Card city/state/zip</li>";
                    }

                }

                if ($sign_name == "") {
                    $errorMsg .= "<li>Please fill Signature</li>";
                }

                if (trim($errorMsg) == "") {
                    $this->daffny->DB->transaction();
                    $shipper = $order->getShipper();

                    $update_arr = array(
                        'fname' => $shipper_fname,
                        'lname' => $shipper_lname,
                        'email' => $shipper_email,
                        'company' => $shipper_company,
                        'phone1' => $shipper_phone1,
                        'phone2' => $shipper_phone2,
                        'mobile' => $shipper_mobile,
                        'fax' => $shipper_fax,
                        'address1' => $shipper_address1,
                        'address2' => $shipper_address2,
                        'city' => $shipper_city,
                        'state' => $shipper_state,
                        'zip' => $shipper_zip,
                        'country' => $shipper_country,
                    );
                    $shipper->update($update_arr);

                    $signature = new Signature();
                    $signature->setFontFile(ROOT_PATH . "libs/signature/jenna_sue.ttf");

                    if ($sign = $signature->create(400, 100, "text", $sign_name)) {

                        $entityDoc = new EntityDoc($this->daffny->DB);
                        do {
                            $fileName = md5(mt_rand() . time());
                            $filePath = UPLOADS_PATH . 'entities/' . $order->id . '/' . $fileName;
                        } while (file_exists($filePath));

                        $formTemplate = new FormTemplate($this->daffny->DB); //SYS_ORDER_COMMERCIAL
                        $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER, $order->getAssigned()->parent_id);
                        $formData = EmailTemplate::fillParams($order, array_fill_keys($this->daffny->tpl->get_vars($formTemplate->body), ''), EmailTemplate::SEND_TYPE_HTML, false, $sign);
                        //$form = $this->daffny->tpl->get_parsed_from_array($formTemplate->body, $formData);

                        $payment_value = "";
                        if ($payment_method == 1) {
                            $payment_value = "Company Check";
                        } elseif ($payment_method == 2) {
                            $payment_value = "Wire-Check";
                        } elseif ($payment_method == 3) {
                            $payment_value = "Credit Card";
                        }

                        $form = '<p>
                            <title></title>
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
                                .agree .underline_long { width: 350px; border-bottom: 1px solid #000000; }	</style>
                        </p>
                        <div class="wrapper">
                            <div class="form-header">
                                @company_name@ Service Agreement</div>
                            <div class="print-section">
                                <table class="form-fields">
                                    <colgroup>
                                        <col span="2" width="50%" />
                                    </colgroup>
                                    <tbody>
                                        <tr valign="top">
                                            <td width="50%">
                                                <div class="company_address_info">
                                                    <span class="company_name">@company_name@</span><br />
                                                    @company_address@<br />
                                                    @company_city@, @company_state@ @company_zip@</div>
                                            </td>
                                            <td width="50%">
                                                <table class="form-fields">
                                                    <tbody>
                                                        <tr>
                                                            <td class="label">
                                                                Salesperson:</td>
                                                            <td class="right">
                                                                @user_name@</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label" valign="top">
                                                                Phone:</td>
                                                            <td class="right">
                                                                @user_phone@</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label">
                                                                Fax:</td>
                                                            <td class="right">
                                                                @company_phone_fax@</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="label">
                                                                Email:</td>
                                                            <td class="right">
                                                                @user_email@</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <dl class="form-list">
                                <dt>
                                    1. Shipper Information.</dt>
                                <dd>
                                    <table class="form-fields">
                                        <tbody>
                                            <tr>
                                                <td class="label" width="15%">
                                                    First Name:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_first_name@</td>
                                                <td class="label" width="15%">
                                                    Company:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_company_name@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Last Name:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_last_name@</td>
                                                <td class="label" width="15%">
                                                    Address:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_address@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Phone 1:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_phone@</td>
                                                <td class="label" width="15%">
                                                    Address 2:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_address2@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Phone 2:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_phone2@</td>
                                                <td class="label" width="15%">
                                                    City:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_city@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Cell:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_phone_cell@</td>
                                                <td class="label" width="15%">
                                                    State/Zip:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_state@ @shipper_zip@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Fax:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_phone_fax@</td>
                                                <td class="label" width="15%">
                                                    Country:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_country@</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Email:</td>
                                                <td class="underline" width="35%">
                                                    @shipper_email@</td>
                                                <td class="label" width="15%">
                                                    Hours:</td>
                                                <td class="underline" width="35%">
                                                    ' . $shipper_hours . '</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </dd>
                                <dt>
                                    2. Company Contact Information.</dt>
                                <dd>
                                    <table class="form-fields">
                                        <tbody>

                                            <tr>
                                                    <td class="label" width="12%">
                                                        1-First Name:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_firstname1 . '</td>
                                                    <td class="label" width="12%">
                                                        2-First Name:</td>
                                                    <td class="underline"  width="22%">
                                                        ' . $contract_firstname2 . '</td>
                                                    <td  class="label" width="12%">
                                                        3-First Name:</td>
                                                    <td  width="22%" class="underline" >
                                                        ' . $contract_firstname3 . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="label" width="12%">
                                                        Last Name:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_lastname1 . '</td>
                                                    <td class="label" width="12%">
                                                        Last Name:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_lastname2 . '</td>
                                                    <td  class="label" width="12%">
                                                        Last Name:</td>
                                                    <td width="22%" class="underline" >
                                                        ' . $contract_lastname3 . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="label" width="12%">
                                                        Title:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_title1 . '</td>
                                                    <td class="label" width="12%">
                                                        Title:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_title2 . '</td>
                                                    <td  class="label" width="12%">
                                                        Title:</td>
                                                    <td width="22%" class="underline" >
                                                        ' . $contract_title3 . '</td>
                                                </tr>
                                                <tr>
                                                    <td class="label" width="12%">
                                                        Email:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_email1 . '</td>
                                                    <td class="label" width="12%">
                                                        Email:</td>
                                                    <td class="underline" width="22%">
                                                        ' . $contract_email2 . '</td>
                                                    <td  class="label" width="12%">
                                                        Email:</td>
                                                    <td class="underline"  width="22%">
                                                        ' . $contract_email3 . '</td>
                                                </tr>

                                        </tbody>
                                    </table>
                                </dd>

                                <dt>
                                    3. Additional Information</dt>
                                <dd>
                                    <table class="form-fields">
                                            <tbody>
                                                <tr>
                                                    <td class="label">
                                                        EIN #:</td>
                                                    <td  class="underline">
                                                        ' . $ein . '</td>
                                                    <td class="label">
                                                        DUNS:</td>
                                                    <td  class="underline">
                                                        ' . $duns . '</td>
                                                    <td class="label">
                                                        Payment Method:</td>
                                                    <td  class="underline">
                                                        ' . $payment_value . '</td>
                                                </tr>
                                            </tbody>
                                        </table></dd>
                                <dt>
                                    4. Payment Options.</dt>
                                <dd>
                                    <table class="form-fields">
                                        <tbody>

                                            <tr>
                                                <td class="label" width="15%">
                                                    Credit Card Number:</td>
                                                <td class="underline" width="35%">
                                                    ' . $e_cc_number . '</td>
                                                <td class="label" width="15%">
                                                    Exp. Date:</td>
                                                <td class="underline" width="35%">
                                                    ' . $e_cc_month . ' - ' . $e_cc_year . '</td>
                                            </tr>

                                            <tr>
                                                <td class="label" width="15%">
                                                    Name On Card:</td>
                                                <td class="underline" width="35%">
                                                    ' . $e_cc_lname . '</td>
                                                <td class="label" width="15%">
                                                    Security Code:</td>
                                                <td class="underline" width="35%">
                                                    ' . $e_cc_cvv2 . '</td>
                                            </tr>
                                            <tr>
                                                <td class="label" width="15%">
                                                    Card Billing Address:</td>
                                                <td class="underline" width="35%">
                                                    ' . $e_cc_address . ' ' . $e_cc_city . ' , ' . $e_cc_state . ' ' . $e_cc_zip . '</td>
                                                <td class="label" rowspan="2" width="15%">
                                                    </td>
                                                <td rowspan="2" width="35%">
                                                    </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </dd>
                                <dt>
                                    5. @company_name@ Commercial Information</dt>
                                <dd>

                                    <b>INVOICES MUST BE PAID WITHIN 14 DAYS FROM DATE OF INVOICE</b><br />
                                                            <br />
                                                            1. By signing this document you declare that you are the owner, or an authorized agent of the owner to make arrangements for shipping the owner&rsquo;s vehicle(s) (Hereinafter referred to as Client or Shipper). Client warrants that it is the registered legal owner of the vehicle, or that it has been duly authorized by the legal owners to enter into this agreement with RITE WAY AUTO TRANSPORT (hereinafter referred to as RITE WAY).<br />
                                                            <br />
                                                            2. RITE WAY, a vehicle transportation broker, will arrange for the client, the transportation of Client&rsquo;s vehicle(s) on trucks/trailers with licensed and insured vehicle carriers (Hereinafter referred to as &ldquo;carrier&rdquo;) selected based on the Client&rsquo;s specified pick up location and specified drop off destination outlined in the shipping order.<br />
                                                            <br />
                                                            3. By client&rsquo;s signature or client&rsquo;s agent signature, RITE WAY and carrier transporting the vehicle(s) and their employees are authorized to operate and transport vehicle(s) during transport, pick up/delivery or as needed to facilitate the transport of the vehicle(s).<br />
                                                            <br />
                                                            4. All outstanding freight charges must be paid without deduction, regardless of loss or damage, so before the vehicle(s) can be taken off the truck, the total amount Client owes Carrier must be paid in full with Cash or Certified Bank Funds. Any damages MUST be properly noted on the Bill of Lading upon delivery. While the driver is there, Client shall obtain the necessary information from the driver. Damage claims must be made within 7 days of delivery with pictures of specified damages claimed.<br />
                                                            <br />
                                                            5. All claims for damage must be taken up directly with the carrier, and if there is any damage, the liability for damages lies solely with the carrier. RITE WAY will assist Client by providing necessary carrier information (name, phone numbers of that particular motor carrier used for transport).<br />
                                                            <br />
                                                            6. Signing the Bill of Lading at destination, without notation of damage, will be evidence of satisfactory delivery of vehicle. Under no circumstances can Client make a claim if no damages were noted at delivery. Client or Client&rsquo;s agent is required to check the transported vehicle(s) over very carefully before signing the Bill of Lading.<br />
                                                            <br />
                                                            7. THIS AGREEMENT DOES NOT CREATE ANY WARRANTIES OF ANY KIND, NEITHER EXPRESS NOR IMPLIED.<br />
                                                            <br />
                                                            8. This Agreement shall be construed in accordance with the laws of the State of Florida.<br />
                                                            <br />
                                                            9. The parties agree that all actions or proceedings arising in connection with this agreement shall be tried and litigated exclusively in the State or Federal (if permitted by law and a party elects to file an action in federal court) courts located in Broward County, Florida. This choice of venue is intended by the parties to be mandatory and not permissive in nature, and to preclude the possibility of litigation between the parties with respect to, or arising out of, this Agreement in any jurisdiction other than that specified in this section. Each party waives any right it may have to assert the doctrine of forum non conveniens or similar doctrine or to object to venue with respect to any proceeding brought in accordance with this section. By action of this provision, the parties agree to submit to the personal jurisdiction in a court of competent jurisdiction located in Broward County, Florida.<br />
                                                            <br />
                                                            10. All shipments are handles in accordance with these Terms &amp; Conditions and include &quot;a general and continuing lien on any and all property of Customer coming into Rite Way Auto Transport&#39;s actual or constructive possession or control&hellip;&quot; which customer hereby expressly agrees to grant to Rite Way Auto Transport as an enforceable security interest under the Uniform Commercial Code. Applicant acknowledges and agrees that Rite Way Auto Transport general lien and security interest apply irrespective of whether Rite Way Auto Transport issues a bill of lading, air waybill or other contract of carriage.<br />
                                                            <br />
                                                            11. This Agreement is subject to approval and verification of information provided by the customer. Customer agrees to pay all amounts in U.S. dollar to Rite Way Auto Transport upon date of receipt of invoice terms. Customer may pay by Credit Card or by company check or Wire Transfer. By submitting this agreement to Rite Way Auto Transport you are authorizing us to process payment when required. Rite Way Auto Transport reserves the right to terminate the customer&rsquo;s services, without limitation for any reason, including but not limited to: if payment is not received. A service charge of 1.5% per month (18%/year) or the maximum rate allowed by law, will be applied to all past due balances. This agreement will automatically renew on the first day of each month following the start date, Rite Way Auto Transport reserves the right to initiate collection proceedings against any party affiliated with an account which becomes delinquent at any time.<br />
                                                            <br />
                                                            12. If for any reason payment is not made in full when due, the customer agrees to pay any and all Attorney and Collection fees and any Court cost incurred by Rite Way Auto Transport for any court action related to this agreement. Customer acknowledges and agrees that this agreement and any account established hereby shall be governed in accordance with laws of the commonwealth of Florida. Any case of action arising under this agreement shall be adjudicated exclusively in a court in Coral Springs, FL.

                                </dd>
                                <dt>
                                    6. Agreed &amp; Accepted</dt>
                                <dd>
                                    <div style="text-align: center;">
                                        <p>
                                            <em>I have read, and understand, the attached Terms and Conditions and I intend, and agree, to be bound by them.</em></p>
                                        @sign_this_form@<br />

                                    </div>
                                </dd>

                            </dl>

                        </div>

                        ';

                        $form = $this->daffny->tpl->get_parsed_from_array($form, $formData);
                        $form = str_replace(array("\t", "\n", "\r"), '', $form);

                        $pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
                        $pdf->SetAuthor($order->getShipper()->fname . ' ' . $order->getShipper()->lname);
                        $pdf->SetSubject("B2B Order Form");
                        $pdf->SetTitle("B2B Order Form");
                        $pdf->SetCreator("transportmasters.net");

                        //print_r($form);

                        //$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print.css")."</style>", 1);
                        if (preg_match('|<style type="text/css">(.*)</style>|mi', $form, $match)) {
                            $form = str_replace($match[1], '', $form);
                            $pdf->writeHTML('<style>' . $match[1] . '</style>', 1);
                        }
                        $pdf->writeHTML(html_entity_decode($form, ENT_NOQUOTES, 'UTF-8'), 2);

                        //print "--1";
                        //                            file_put_contents(UPLOADS_PATH.$fileName, $sign);
                        //                            $pdf->image(UPLOADS_PATH.$fileName, 10, $pdf->y);
                        //                            unlink(UPLOADS_PATH.$fileName);
                        makePath(UPLOADS_PATH . 'entities/' . $order->id, false);
                        $pdf->Output($filePath, 'F');

                        $entityDoc->create(array(
                            'entity_id' => $order->id,
                            'name' => 'B2B Order Form (' . date('m/d/Y') . ')',
                            'filename' => $fileName,
                            'signature' => $sign,
                        ));

                        //
                        // Put to the Documents Tab
                        //
                        do {
                            $fname = md5(mt_rand() . " " . time() . " " . $order->id);
                            $path = ROOT_PATH . "uploads/entity/" . $fname;
                            $uploadpath = ROOT_PATH . "uploads/accounts/" . $fname;
                        } while (file_exists($path));
                        $pdf->Output($path, 'F');

                        $ins_arr = array(
                            'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                            'name_on_server' => $fname,
                            'size' => filesize($path),
                            'type' => "pdf",
                            'date_uploaded' => date("Y-m-d H:i:s"),
                            'owner_id' => $order->getAssigned()->parent_id,
                            'status' => 0,
                            'esigned' => 2,
                        );

                        $this->daffny->DB->insert("app_uploads", $ins_arr);
                        $ins_id = $this->daffny->DB->get_insert_id();

                        $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $order->id, "upload_id" => $ins_id));

                        $accountShipperID = 0;
                        if ($order->account_id != 0) {
                            // Update Account
                            $update_arr = array(
                                'esigned' => 2,
                            );
                            //$accountShipper->update($update_arr);
                            //$accountShipperID = $accountShipper->id;

                            $this->daffny->DB->update("app_accounts", $update_arr, "id='" . $order->account_id . "'");
                            $accountShipperID = $order->account_id;
                        }

                        // Update Entity
                        $update_arr = array(
                            'esigned' => 2,
                        );
                        $order->update($update_arr);

                        // Create Internal Notes
                        $note = new Note($this->daffny->DB);
                        $note->create(array('entity_id' => $order->id, 'text' => "B2B Order form e-signed.", 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));

                        // Create Internal Notes
                        if (trim($_POST['notes']) != "") {

                            $note = new Note($this->daffny->DB);
                            $note->create(array('entity_id' => $order->id, 'text' => "Customer: " . trim($_POST['notes']), 'sender_id' => $order->getAssigned()->parent_id, "status" => 1, "system_admin" => 2, 'type' => Note::TYPE_INTERNAL));
                        }

                        $pdf->Output($uploadpath, 'F');

                        $sql_arr = array(
                            'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                            'name_on_server' => $fname,
                            'size' => filesize($path),
                            'type' => "pdf",
                            'date_uploaded' => date("Y-m-d H:i:s"),
                            'owner_id' => $order->getAssigned()->parent_id,
                            'status' => 0,
                            'esigned' => 2,
                        );

                        $ins_arr = $this->daffny->DB->PrepareSql("app_uploads", $sql_arr);
                        $this->daffny->DB->insert("app_uploads", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();

                        $this->daffny->DB->insert("app_accounts_uploads", array(
                            "account_id" => $accountShipperID,
                            "upload_id" => $insid,
                        ));

                        $this->daffny->DB->transaction("commit");

                        $successMsg = "Order successfully B2B signed.";
                        $this->daffny->tpl->successMsg = $successMsg;
                        $order->make_payment();
                        $order->updateHeaderTable();

                        //
                        // End
                        //

                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Body = 'B2B Order attached';
                        $mail->Subject = 'B2B Signed Order';
                        $mail->AddAddress($order->getShipper()->email, $order->getShipper()->fname . ' ' . $order->getShipper()->lname);
                        $mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                        $mail->setFrom('noreply@transportmasters.net');
                        $mail->AddAttachment($filePath, 'B2BOrder.pdf');
                        $mail->send();

                    }
                } else {
                    $this->daffny->tpl->errorMsg = $errorMsg;
                }

            }

            $shipper = $order->getShipper();
            $state_data = '<select class="elementname form-box-combobox" elementname="select" id="" name="shipper_state" style="width:130px;" tabindex="12">';
            $result = $this->daffny->DB->selectRows("code, name", "states", "ORDER BY name", "code");
            foreach ($result as $code => $values) {
                $selected = "";
                if ($shipper->state == $code) {
                    $selected = " selected=selected ";
                }

                $state_data .= '<option value="' . $code . '" ' . $selected . '>' . $values['name'] . '</option>';
            }
            $state_data .= '</select>';

            $formTemplate = new FormTemplate($this->daffny->DB);

            $formTemplate->loadByOwnerId(FormTemplate::SYS_ORDER_COMMERCIAL, $order->getAssigned()->parent_id);

            $formData = EmailTemplate::fillParams($order, array_fill_keys($this->daffny->tpl->get_vars($formTemplate->body), ''), EmailTemplate::SEND_TYPE_HTML, true);
            $formData['shipper_state_data'] = $state_data;

            $formData['contract_firstname1'] = $contract_firstname1;
            $formData['contract_firstname2'] = $contract_firstname2;
            $formData['contract_firstname3'] = $contract_firstname3;
            $formData['contract_lastname1'] = $contract_lastname1;
            $formData['contract_lastname2'] = $contract_lastname2;
            $formData['contract_lastname3'] = $contract_lastname3;
            $formData['contract_title1'] = $contract_title1;
            $formData['contract_title2'] = $contract_title2;
            $formData['contract_title3'] = $contract_title3;
            $formData['contract_email1'] = $contract_email1;
            $formData['contract_email2'] = $contract_email2;
            $formData['contract_email3'] = $contract_email3;

            $formData['ein'] = $ein;
            $formData['duns'] = $duns;

            $pay_selected = "";
            $payment_html = '<select class="form-box-combobox" id="payment_method" name="payment_method" tabindex="29">';

            $payment_html .= '<option selected="selected" value="">Select One</option>';
            if ($payment_method == 1) {
                $pay_selected = " selected=selected ";
            }

            $payment_html .= '<option value="1" ' . $pay_selected . '>Company Check</option>';
            $pay_selected = "";
            if ($payment_method == 2) {
                $pay_selected = " selected=selected ";
            }

            $payment_html .= '<option value="2" ' . $pay_selected . '>Wire-Check</option>';
            $pay_selected = "";
            if ($payment_method == 3) {
                $pay_selected = " selected=selected ";
            }

            $payment_html .= '<option value="3" ' . $pay_selected . '>Credit Card</option>';
            $payment_html .= '</select>';

            $formData['payment_method'] = $payment_html;

            $terms_checked = "";
            if ($terms == 1) {
                $terms_checked = " checked=checked ";
            }

            $terms_html = '<input class="field checkbox" id="terms" name="terms" required="required"  type="checkbox" value="1"  tabindex="41" ' . $terms_checked . ' />';
            $signature_terms_checked = "";
            if ($signature_terms == 1) {
                $signature_terms_checked = " checked=checked ";
            }

            $signature_terms_html = '<input class="field checkbox" id="signature_terms" name="signature_terms" required="required" tabindex="42"  type="checkbox" value="1"  ' . $signature_terms_checked . '/>';

            $formData['e_cc_type'] = $e_cc_type;
            $formData['e_cc_address'] = $e_cc_address;
            $formData['e_cc_fname'] = $e_cc_fname;
            $formData['e_cc_number'] = $e_cc_number;
            $formData['e_cc_city'] = $e_cc_city;
            $formData['e_cc_lname'] = $e_cc_lname;
            $formData['e_cc_cvv2'] = $e_cc_cvv2;
            $formData['e_cc_state'] = $e_cc_state;
            $formData['e_cc_month'] = $e_cc_month;
            $formData['e_cc_year'] = $e_cc_year;
            $formData['e_cc_zip'] = $e_cc_zip;

            $formData['terms'] = $terms_html;
            $formData['signature_terms'] = $signature_terms_html;

            $this->input['form'] = $this->daffny->tpl->get_parsed_from_array($formTemplate->body, $formData);

            $this->daffny->tpl->entity = $order;

            $this->renderParentLayout = false;

            $this->tplname = "order.confirmcommercial";

        } catch (FDException $e) {

            redirect(getLink(''));
            //print $e;

        }

    }

    public function idx()
    {
        redirect(getLink());
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $this->renderParentLayout = false;
            $this->tplname = "order.new";
            $this->input['title'] = "Post new Order";
            $row = $this->daffny->DB->selectRow("*", "app_externalforms", "WHERE `hash` = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['hash']) . "'");
            if (!$row) {
                redirect(getLink(''));
            }

            $member = new Member($this->daffny->DB);
            $member->load($row['owner_id']);
            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm()) {
                $this->daffny->DB->transaction();
                $distance = RouteHelper::getRouteDistance($sql_arr['origin_city'] . "," . $sql_arr['origin_state'] . "," . $sql_arr['origin_country'], $sql_arr['destination_city'] . "," . $sql_arr['destination_state'] . "," . $sql_arr['destination_country']);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
                /* Create Order*/
                $entity = new Entity($this->daffny->DB);
                $insert_arr = array(
                    'type' => Entity::TYPE_ORDER,
                    'assigned_id' => $member->id,
                    'avail_pickup_date' => date("Y-m-d", strtotime($sql_arr['avail_pickup_date'])),
                    'status' => Entity::STATUS_ACTIVE,
                    'ship_via' => $sql_arr['ship_via'],
                    'reffered_by' => $sql_arr['referred_by'],
                    'distance' => $distance,
                );
                $entity->create($insert_arr);
                // Create Shipper
                $shipper = new Shipper($this->daffny->DB);
                $insert_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => $sql_arr['shipper_phone1'],
                    'phone2' => $sql_arr['shipper_phone2'],
                    'mobile' => $sql_arr['shipper_mobile'],
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                );
                $shipper->create($insert_arr, $entity->id);
                // Create Origin
                $origin = new Origin($this->daffny->DB);
                $insert_arr = array(
                    'address1' => $sql_arr['origin_address1'],
                    'address2' => $sql_arr['origin_address2'],
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                    'name' => $sql_arr['origin_contact_name'],
                    'company' => $sql_arr['origin_company'],
                    'phone1' => $sql_arr['origin_phone1'],
                    'phone2' => $sql_arr['origin_phone2'],
                    'phone3' => $sql_arr['origin_phone3'],
                    'phone_cell' => $sql_arr['origin_mobile'],
                );
                $origin->create($insert_arr, $entity->id);
                // Create Destination
                $destination = new Destination($this->daffny->DB);
                $insert_arr = array(
                    'address1' => $sql_arr['destination_address1'],
                    'address2' => $sql_arr['destination_address2'],
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                    'name' => $sql_arr['destination_contact_name'],
                    'company' => $sql_arr['destination_company'],
                    'phone1' => $sql_arr['destination_phone1'],
                    'phone2' => $sql_arr['destination_phone2'],
                    'phone3' => $sql_arr['destination_phone3'],
                    'phone_cell' => $sql_arr['destination_mobile'],
                );
                $destination->create($insert_arr, $entity->id);
                // Create Notes
                if (trim($sql_arr['notes_from_shipper']) != "") {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }
                // Create Vehicles
                foreach ($_POST['year'] as $key => $val) {
                    $vehicle = new Vehicle($this->daffny->DB);
                    $insert_arr = array(
                        'entity_id' => $entity->id,
                        'year' => $_POST['year'][$key],
                        'make' => $_POST['make'][$key],
                        'model' => $_POST['model'][$key],
                        'type' => $_POST['type'][$key],
                        'tariff' => $_POST['tariff'][$key],
                        'deposit' => $_POST['deposit'][$key],
                    );
                    $vehicle->create($insert_arr);
                }
                // Update Entity
                $update_arr = array(
                    'shipper_id' => $shipper->id,
                    'origin_id' => $origin->id,
                    'destination_id' => $destination->id,
                    'prefix' => $entity->getNewPrefix(),
                );
                $entity->update($update_arr);
                // Commit transaction
                $this->daffny->DB->transaction('commit');

                $entity->updateHeaderTable();

            } else {
                foreach ($_POST as $key => $val) {
                    if (!is_array($val)) {
                        $this->input[$key] = htmlspecialchars($val);
                    } else {
                        foreach ($val as $key2 => $val2) {
                            $this->input[$key][$key2] = htmlspecialchars($val2);
                        }
                    }
                }
            }
            $company = $member->getCompanyProfile();
            $tpl_vars = array(
                'company_name' => $company->companyname,
                'company_logo' => "<img src=\"" . SITE_IN . "uploads/company/" . $member->id . "_small.jpg?" . mt_rand() . "\" alt=\"{$company->companyname} Logo\" />",
                'year' => date("Y"),
            );
            $this->input['header'] = $this->daffny->tpl->get_parsed_from_array($row['header'], $tpl_vars);
            $this->input['footer'] = $this->daffny->tpl->get_parsed_from_array($row['footer'], $tpl_vars);
            $this->getEditForm();
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    protected function getEditForm()
    {
        /* SHIPPER */
        $this->form->ComboBox("shipper", array("" => "New Shipper"), array('style' => 'width:190px;'), "Select Shipper", "</td><td>");
        $this->form->TextField("shipper_fname", 32, array(), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array(), $this->requiredTxt . "Last Name", "</td><td>");
        $this->form->TextField("shipper_email", 32, array('class' => 'email'), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("shipper_company", 64, array(), "Company", "</td><td>");
        $this->form->TextField("shipper_phone1", 32, array('class' => 'phone'), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone2", 32, array('class' => 'phone'), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone'), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('class' => 'phone'), "Fax", "</td><td>");
        $this->form->TextField("shipper_address1", 64, array(), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array(), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city'), "City", "</td><td>");
        $this->form->ComboBox("shipper_state", array("" => "Select State") + $this->getStates(), array('style' => 'wiSSSdth:150px;'), "State/Zip", "</td><td>");
        $this->form->TextField("shipper_zip", 8, array('style' => 'width:50px;margin-left:7px;', 'class' => 'zip'), "", "");
        $this->form->ComboBox("shipper_country", $this->getCountries(), array(), "Country", "</td><td>");
        /* ORIGIN */
        $this->form->TextField("origin_address1", 255, array(), "Address", "</td><td>");
        $this->form->TextField("origin_address2", 255, array(), "&nbsp;", "</td><td>");
        $this->form->TextField("origin_city", 255, array('class' => 'geo-city'), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox("origin_state", array("" => "Select State") + $this->getStates(), array('style' => 'width:160px;'), $this->requiredTxt . "State/Zip", "</td><td>");
        $this->form->TextField("origin_zip", 10, array('style' => 'width:50px;margin-left:5px;', 'class' => 'zip'), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array(), "Country", "</td><td>");
        /* ORIGIN CONTACT */
        $this->form->ComboBox("origin_select_contact", array("" => "New Contact"), array('style' => 'width:160px;'), "Select Contact", "</td><td colspan=\"5\">");
        $this->form->ComboBox("origin_select_terminal", array("" => "New Location"), array('style' => 'width:160px;'), "Select Terminal", "</td><td colspan=\"5\">");
        $this->form->CheckBox("origin_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("origin_contact_name", 255, array(), "Contact Name", "</td><td>");
        $this->form->TextField("origin_company_name", 255, array(), "Company Name", "</td><td>");
        $this->form->TextField("origin_buyer_number", 255, array(), "Buyer Number", "</td><td>");
        $this->form->TextField("origin_phone1", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 1", "</td><td>");
        $this->form->TextField("origin_phone2", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 2", "</td><td>");
        $this->form->TextField("origin_phone3", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 3", "</td><td>");
        $this->form->TextField("origin_mobile", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Mobile", "</td><td>");
        /* DESTINATION */
        $this->form->TextField("destination_address1", 255, array(), "Address", "</td><td>");
        $this->form->TextField("destination_address2", 255, array(), "&nbsp;", "</td><td>");
        $this->form->TextField("destination_city", 255, array('class' => 'geo-city'), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox("destination_state", array("" => "Select State") + $this->getStates(), array('style' => 'width:160px;'), $this->requiredTxt . "State/Zip", "</td><td>");
        $this->form->TextField("destination_zip", 10, array('style' => 'width:50px;margin-left:5px;', 'class' => 'zip'), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array(), "Country", "</td><td>");
        /* DESTINATION CONTACT */
        $this->form->ComboBox("destination_select_contact", array("" => "New Contact"), array('style' => 'width:160px;'), "Select Contact", "</td><td colspan=\"5\">");
        $this->form->ComboBox("destination_select_terminal", array("" => "New Location"), array('style' => 'width:160px;'), "Select Terminal", "</td><td colspan=\"5\">");
        $this->form->CheckBox("destination_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("destination_contact_name", 255, array(), "Contact Name", "</td><td>");
        $this->form->TextField("destination_company_name", 255, array(), "Company Name", "</td><td>");
        $this->form->TextField("destination_phone1", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 1", "</td><td>");
        $this->form->TextField("destination_phone2", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 2", "</td><td>");
        $this->form->TextField("destination_phone3", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Phone 3", "</td><td>");
        $this->form->TextField("destination_mobile", 255, array('class' => 'phone', 'style' => 'width: 160px'), "Mobile", "</td><td>");
        /* SHIPPING INFORMATION */
        $this->form->TextField("avail_pickup_date", 8, array('class' => 'datepicker'), $this->requiredTxt . "1st Avail. Pickup Date", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", Entity::$ship_via_string, array(), $this->requiredTxt . "Ship Via", "</td><td valign=\"top\">");
        $this->form->TextArea("notes_from_shipper", 2, 10, array('style' => 'height:40px;'), "Notes from Shipper", "</td><td>");
        $this->form->TextArea("notes_for_shipper", 2, 10, array('style' => 'height:40px;'), "Information for Shipper", "</td><td>");
        $this->form->CheckBox("include_shipper_comment", array(), "Include Shipper Comment on Dispatch Sheet", "&nbsp;");
        /* PRICING INFORMATION */
        $this->form->TextField("carrier_pay", 8, array('class' => 'decimal', 'style' => 'width:120px;'), $this->requiredTxt . "Carrier Pay", "</td><td>$");
        $this->form->ComboBox("balance_paid_by", Entity::$balance_paid_by_string, array(), $this->requiredTxt . "Balance Paid By", "</td><td>");
        $this->form->TextField("pickup_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;'), "Pickup Terminal Fee", "</td><td>$");
        $this->form->TextField("delivery_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;'), "Delivery Terminal Fee", "</td><td>$");

    }

    protected function checkEditForm()
    {
        return false;
    }

    public function dispatch()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $sheet = new DispatchSheet($this->daffny->DB);
            $sheet->loadByHash($_GET['hash']);

            if (strtotime($sheet->expired) < time() || $sheet->cancelled != null) {
                $this->renderParentLayout = false;
                $this->tplname = "order.link_expired";

            } else {

                $order = new Entity($this->daffny->DB);
                $order->load($sheet->entity_id);
                $this->daffny->tpl->entity = $order;
                $this->daffny->tpl->dispatch = $sheet;
                $this->renderParentLayout = false;
                $this->tplname = "order.dispatch_external";
                $this->daffny->tpl->files = $this->getFiles($order->id);

            }

        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function dispatchnew_old()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $sheet = new DispatchSheet($this->daffny->DB);
            $sheet->loadByHash($_GET['hash']);

            if (strtotime($sheet->expired) < time() || $sheet->cancelled != null) {
                $this->renderParentLayout = false;
                $this->tplname = "order.link_expired";

            } else {

                $order = new Entity($this->daffny->DB);
                $order->load($sheet->entity_id);
                $this->daffny->tpl->entity = $order;
                $this->daffny->tpl->dispatch = $sheet;
                $this->renderParentLayout = false;
                $this->tplname = "order.dispatch_external_new";
                $this->daffny->tpl->files = $this->getFiles($order->id);

                $notes = $order->getNotes(false, " order by id desc ");
                $this->daffny->tpl->notes = $notes;
            }

        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function dispatchnew()
    {
        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $sheet = new DispatchSheet($this->daffny->DB);
            $sheet->loadByHash($_GET['hash']);

            $data = $sheet->getDispatchESignData();

            $cocd = $data['data']['entity_odtc'];
            if($data['data']['entity_odtc'] == "0.00"){
                $cocd = $data['data']['entity_coc'];
            }

            if (strtotime($sheet->expired) < time() || $sheet->cancelled != null) {
                $this->renderParentLayout = false;
                $this->tplname = "order.link_expired";

            } else {

                $order = new Entity($this->daffny->DB);
                $order->load($sheet->entity_id);
                
                $company = $order->getAssigned()->getCompanyProfile();
                $logo = $company->getCompanyLogo();

                $terms = "";
                if(in_array($order->balance_paid_by, array(2, 3 , 16 , 17)))
                    $terms = $order->getAssigned()->getDefaultSettings()->payments_terms_cod;
                
                if(in_array($order->balance_paid_by, array(8, 9 , 18 , 19)))
                    $terms = $order->getAssigned()->getDefaultSettings()->payments_terms_cop;
                
                if(in_array($order->balance_paid_by, array(12, 13 , 20 , 21,24)))
                    $terms = $order->getAssigned()->getDefaultSettings()->payments_terms_billing;
                
                if(in_array($order->balance_paid_by, array(14, 15 , 22 , 23)))
                    $terms = $order->getAssigned()->getDefaultSettings()->payments_terms_invoice;

                $this->daffny->tpl->entity = $order;
                $this->renderParentLayout = false;

                $this->input['company_logo'] = $logo;
                $this->input['dispatch_terms'] = $data['data']['dispatch_terms'];
                $this->input['payment_terms'] = $terms;
                $this->input['entity_coc'] = $cocd;

                $this->input['carrier_company_name'] = $data['data']['carrier_company_name'];
                $this->input['carrier_print_name'] = $data['data']['carrier_print_name'];
                $this->input['carrier_phone_1'] = $data['data']['carrier_phone_1'];
                $this->input['carrier_phone_2'] = $data['data']['carrier_phone_2'];
                $this->input['carrier_fax'] = $data['data']['carrier_fax'];
                $this->input['carrier_email'] = $data['data']['carrier_email'];
                $this->input['ship_via'] = $data['data']['ship_via'];

                $this->input['instructions'] = $data['data']['instructions'];
                $this->input['information'] = $data['data']['information'];

                $this->input['origin'] = $data['data']['origin'];
                $this->input['destination'] = $data['data']['destination'];
                
                $this->input['vehicle_table'] = $data['data']['vehicles'];

                $this->input['sheet_id'] = $sheet->id;
                
                $this->tplname = "order.e-sign-dispatch";
                $this->daffny->tpl->files = $this->getFiles($order->id);

                $notes = $order->getNotes(false, " order by id desc ");
                $this->daffny->tpl->notes = $notes;
            }

        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function dispatchthank_old()
    {

        try {

            $order = new Entity($this->daffny->DB);

            $order->load($_GET['eid']);
            $companyname = "";
            try {

                $member = $order->getAssigned();
                $company = $member->getCompanyProfile();
                $companyname = $company->companyname;

            } catch (Exception $e) {}

            $this->daffny->tpl->companyname = $companyname;
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;

            $this->tplname = "order.dispatchthanks";

        } catch (FDException $e) {

            redirect(getLink(''));

        }

    }

    public function dispatchthanks()
    {

        try {

            $order = new Entity($this->daffny->DB);

            $order->load($_GET['eid']);
            $companyname = "";
            
            try {

                $member = $order->getAssigned();
                $company = $member->getCompanyProfile();
                $companyname = $company->companyname;

                $logo = $company->getCompanyLogo();
                $this->input['company_logo'] = $logo;

            } catch (Exception $e) {}

            $this->daffny->tpl->companyname = $companyname;
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;

            $this->tplname = "order.e-sign-thanks-dispatch";

        } catch (FDException $e) {
            redirect(getLink(''));
        }

    }

    protected function getFiles($id)
    {
        $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
                 ORDER BY u.date_uploaded desc limit 0,1 ";
        //
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }

    public function getdocs()
    {

        $ID = (int) get_var("id");
        $file = $this->daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
        //$file = $this->daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $ID . "' ");
        if (!empty($file)) {

            $file_path = UPLOADS_PATH . "entity/" . $file["name_on_server"];
            $file_name = $file["name_original"];
            $file_size = $file["size"];
            if (file_exists($file_path)) {
                if (strtolower($file["type"]) == "pdf") {
                    header("Content-Type: application/pdf; filename=\"" . $file_name . "\"");
                    //header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                } else {
                    header("Content-Type: application; filename=\"" . $file_name . "\"");
                    header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                }
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

    public function carrierUnsubscribed()
    {

        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Account($this->daffny->DB);
            $order->loadByHash($_GET['hash']);

            $order->update(array('unsubscribe' => 1));
            $this->tplname = "order.carrier_unsubscribed";
        } catch (FDException $e) {
            redirect(getLink(''));
            //print $e;
        }
    }

    public function pickup()
    {

        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHashColumn($_GET['hash'], 'pickup_hash');

            if ($order->status > Entity::STATUS_DISPATCHED) {
                redirect(getLink("order", "pickup_delivered_already", "hash", $_GET['hash'], "type", "p"));
            }
            $pickup_delivered_date = trim($_POST['pickup_delivered_date']);
            if (isset($_POST['submit']) && $pickup_delivered_date != '') {
                $applog = new Applog($this->daffny->DB);
                $info = "app -> source -> order -> order.php -> pickup function | Update Order-" . $order->number . "( " . $order->id . " ) | Status changed :" . $order->status;
                try {

                    $updateDate = $pickup_delivered_date . " " . date('H:i:s');
                    if ($order->status == Entity::STATUS_DISPATCHED) {
                        $order->setStatusAndDate(Entity::STATUS_PICKEDUP, $updateDate, true);
                    }

                    /*
                    // Update Entity
                    $update_arr = array(
                    'pickup_hash' => ''
                    );
                    $order->update($update_arr);
                     */
                    $notesText = "Carrier has marked this order as PICKED UP on " . $updateDate;
                    $note_array = array(
                        "entity_id" => $order->id,
                        "sender_id" => $order->getAssigned()->parent_id,
                        "type" => 3,
                        "priority" => 1,
                        "status" => 1,
                        "system_admin" => 2,
                        "text" => $notesText);
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);

                    if (trim($_POST['internal_note']) != '') {
                        $notesText = "Carrier Note: " . (rawurldecode($_POST['internal_note']));
                        $note_array = array(
                            "entity_id" => $order->id,
                            "sender_id" => $order->getAssigned()->parent_id,
                            "type" => 3,
                            "priority" => 1,
                            "status" => 1,
                            "system_admin" => 2,
                            "text" => $notesText);
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }

                    $order->updateHeaderTable();
                    /********** Log ********/
                    $info .= " | date: " . $updateDate . " | hash: " . $_GET['hash'];

                    $applog->createInformation($info);

                    /***************************/
                    $dispatch_email = $order->getAssigned()->getCompanyProfile()->dispatch_email;
                    if ($dispatch_email != "") {
                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Subject = "FreightDragon Message: Order#" . $order->getNumber() . " status changed on " . $updateDate . " by the carrier";
                        $mail->Body = "Carrier has marked this order# " . $order->getNumber() . " as PICKED UP on " . $updateDate;
                        $mail->AddAddress($dispatch_email, "Dispatch Department");
                        $mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                        $mail->setFrom('noreply@transportmasters.net');
                        $mail->send();
                    }
                    /**************************/

                } catch (Exception $e) {
                    $info .= " | Exception: " . $e;
                    $applog->createException($e);
                }
                $this->setFlashInfo("Your order status has been updated.");
                redirect(getLink("order", "pickup_delivered_thanks", "hash", $_GET['hash'], "type", "p"));

            }
            $company = "";
            try {

                $company = $order->getAssigned()->getCompanyProfile();

            } catch (Exception $e) {}

            $this->daffny->tpl->company = $company;

            $this->form->TextField("pickup_delivered_date", 8, array('class' => 'datepicker'), $this->requiredTxt . "Pickup Date", "</td><td>");
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.pickupthanks";

        } catch (FDException $e) {
            redirect(getLink(''));
            //print $e;
        }

    }

    public function delivered()
    {

        try {
            if (!isset($_GET['hash'])) {
                redirect(getLink(''));
            }

            $order = new Entity($this->daffny->DB);
            $order->loadByHashColumn($_GET['hash'], 'delivered_hash');
            if ($order->status != Entity::STATUS_PICKEDUP) {
                redirect(getLink("order", "pickup_delivered_already", "hash", $_GET['hash'], "type", "d"));
            }

            $pickup_delivered_date = trim($_POST['pickup_delivered_date']);

            if (isset($_POST['submit']) && $pickup_delivered_date != '') {

                $applog = new Applog($this->daffny->DB);
                $info = "app -> source -> order -> order.php -> deilvered function | Update Order-" . $order->number . "( " . $order->id . " ) | Status changed :" . $order->status;
                try {

                    $updateDate = $pickup_delivered_date . " " . date('H:i:s');

                    if ($order->status > Entity::STATUS_DISPATCHED) {
                        $order->setStatusAndDate(Entity::STATUS_DELIVERED, $updateDate, true);
                    }

                    $notesText = "Carrier has marked this order as DELIVERED on " . $updateDate;
                    $note_array = array(
                        "entity_id" => $order->id,
                        "sender_id" => $order->getAssigned()->parent_id,
                        "type" => 3,
                        "priority" => 1,
                        "status" => 1,
                        "system_admin" => 2,
                        "text" => $notesText);
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);

                    if (trim($_POST['internal_note']) != '') {
                        $notesText = "Carrier Note: " . (rawurldecode($_POST['internal_note']));
                        $note_array = array(
                            "entity_id" => $order->id,
                            "sender_id" => $order->getAssigned()->parent_id,
                            "type" => 3,
                            "priority" => 1,
                            "status" => 1,
                            "system_admin" => 2,
                            "text" => $notesText);
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }

                    $order->updateHeaderTable();
                    /*
                    // Update Entity
                    $update_arr = array(
                    'delivered_hash' => ''
                    );
                    $order->update($update_arr);
                     */
                    /********** Log ********/
                    $info .= " | date: " . $updateDate . " | hash: " . $_GET['hash'];

                    $applog->createInformation($info);

                    /***************************/
                    $dispatch_email = $order->getAssigned()->getCompanyProfile()->dispatch_email;
                    if ($dispatch_email != "") {
                        $mail = new FdMailer(true);
                        $mail->isHTML();
                        $mail->Subject = "FreightDragon Message: Order#" . $order->getNumber() . " status changed on " . $updateDate . " by the carrier";
                        $mail->Body = "Carrier has marked this order# " . $order->getNumber() . " as DELIVERED on " . $updateDate;
                        $mail->AddAddress($dispatch_email, "Dispatch Department");
                        $mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                        $mail->setFrom('noreply@transportmasters.net');
                        $mail->send();
                    }
                    /**************************/

                } catch (Exception $e) {
                    $info .= " | Exception: " . $e;
                    $applog->createException($e);
                    //print $e;
                }
                $this->setFlashInfo("Your order status has been updated.");

                redirect(getLink("order", "pickup_delivered_thanks", "hash", $_GET['hash'], "type", "d"));
            }
            $company = "";
            try {

                $company = $order->getAssigned()->getCompanyProfile();

            } catch (Exception $e) {}

            $this->daffny->tpl->company = $company;
            $this->form->TextField("pickup_delivered_date", 8, array('class' => 'datepicker'), $this->requiredTxt . "Delivered Date", "</td><td>");
            $this->daffny->tpl->entity = $order;
            $this->renderParentLayout = false;
            $this->tplname = "order.pickupthanks";

        } catch (FDException $e) {
            redirect(getLink(''));
            //print $e;
        }

    }

    public function pickup_delivered_thanks()
    {
        try {
            //if (!isset($_GET['hash']))
            //redirect(getLink(''));
            $order = new Entity($this->daffny->DB);
            if ($_GET['type'] == 'p') {
                $order->loadByHashColumn($_GET['hash'], 'pickup_hash');
                $update_arr = array(
                    'pickup_hash' => '',
                );
            } else {
                $order->loadByHashColumn($_GET['hash'], 'delivered_hash');
                $update_arr = array(
                    'delivered_hash' => '',
                );
            }
            $order->update($update_arr);

            $this->daffny->tpl->entity = $order;
            $company = "";
            try {

                $company = $order->getAssigned()->getCompanyProfile();

            } catch (Exception $e) {}

            $this->daffny->tpl->company = $company;

            $this->renderParentLayout = false;
            $this->tplname = "order.pickup_delivered_thanks";
        } catch (FDException $e) {
            redirect(getLink(''));
            ///print $e;
        }
    }

    public function pickup_delivered_already()
    {
        try {
            //if (!isset($_GET['hash']))
            //redirect(getLink(''));
            $order = new Entity($this->daffny->DB);
            if ($_GET['type'] == 'p') {
                $order->loadByHashColumn($_GET['hash'], 'pickup_hash');
                /*
            $update_arr = array(
            'pickup_hash' => ''
            );
             */
            } else {
                $order->loadByHashColumn($_GET['hash'], 'delivered_hash');
                /*
            $update_arr = array(
            'delivered_hash' => ''
            );
             */
            }
            //$order->update($update_arr);

            $this->daffny->tpl->entity = $order;
            $company = "";
            try {

                $company = $order->getAssigned()->getCompanyProfile();

            } catch (Exception $e) {}

            $this->daffny->tpl->company = $company;

            $this->renderParentLayout = false;
            $this->tplname = "order.pickup_delivered_already";
        } catch (FDException $e) {
            redirect(getLink(''));
            ///print $e;
        }
    }
}
