<?php

require_once DAFFNY_PATH . "libs/upload.php";

class ApplicationOrders extends ApplicationAction
{
    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('orders.common');
        $this->daffny->tpl->form_templates = $this->form->ComboBox('form_templates', array('' => 'Select One') + $this->getFormTemplates("orders"), array('style' => 'width:130px;', 'onChange' => 'printSelectedOrderForm()'), "", "", true);
        $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One') + $this->getEmailTemplates("orders"), array('style' => 'width:130px;', 'onChange' => 'emailSelectedOrderFormNew()'), "", "", true);
        $this->daffny->tpl->form_templates_quotes = $this->form->ComboBox('form_templates_quotes', array('' => 'Select One') + $this->getFormTemplates("quotes"), array('style' => 'width:130px;'), "", "", true);
        $this->daffny->tpl->email_templates_quotes = $this->form->ComboBox('email_templates_quotes', array('' => 'Select One') + $this->getEmailTemplates("quotes"), array('style' => 'width:130px;'), "", "", true);
        parent::construct();
    }

    public function idx()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_ACTIVE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function ordersnew()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_ACTIVE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function all()
    {
        redirect(getLink('orders'));
    }

    public function onhold()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_ONHOLD);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function archived()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_ARCHIVED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function unarchived()
    {
        try {

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID");
            }

            /********** Log ********/
            $info = "Unarchive order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            /******** If Dispatch sheet created then go to not sighned section ***********/
            $inp = $this->daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $entity->id . "' AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL");

            if (!empty($inp)) {
                $dispatchSheet = new DispatchSheet($this->daffny->DB);
                $dispatchManager = new DispatchSheetManager($this->daffny->DB);
                $dispatchSheet->load($dispatchManager->getDispatchSheetByOrderId($entity->id));
                $dispatchSheet->reject();

            } else {
                $status = Entity::STATUS_ACTIVE;
                $update_arr = array(
                    'status' => $status,
                );

                $entity->update($update_arr);
            }

            $entity->updateHeaderTable();

            $this->ordersnewlist(Entity::STATUS_ARCHIVED);

        } catch (FDException $e) {

            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders/archived'));
        }
    }

    public function posted()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_POSTED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function notsigned()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_NOTSIGNED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function dispatched()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_DISPATCHED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function pickedup()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_PICKEDUP);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function delivered()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_DELIVERED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function pending()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_PENDING);
        } catch (FDException $e) {
            print_r($e);die("Fucked here");
        }
    }

    public function issues()
    {
        try {
            $this->ordersnewlist(Entity::STATUS_ISSUES);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function payments()
    {
        $this->check_access('payments');
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException('Invalid Order ID', getLink('orders'));
            }

            $this->tplname = "orders.payments";
            $this->title = "Order Payments";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            if (isset($_POST['payment_type'])) {
                $info = "Order Payment";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $insert_arr = array();
                switch ($_POST['payment_type']) {
                    case "internally":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
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
                        $member = new Member($this->daffny->DB);
                        $member->load($_SESSION['member_id']);
                        $company = $member->getCompanyProfile();

                        $nmethod = $_POST['method'];

                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");
                            $noteText = "<green>Payment " . $payment->getFrom() . " to " . $payment->getTo() . " is modified from $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . $member->contactname . " on " . date("m/d/Y h:i A");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );
                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            if ($_POST['from_to'] == Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY) {
                                $noteText = "<green>Shipper paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                                if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                    if ($_POST['transaction_id'] != "") {
                                        $noteText .= " #" . $_POST['transaction_id'];
                                    } elseif ($_POST['ch_number'] != "") {
                                        $noteText .= " #" . $_POST['ch_number'];
                                    }

                                } elseif ($nmethod == 9) {
                                    if ($_POST['cc_numb'] != "") {
                                        $noteText .= " ending in #" . substr($_POST['cc_numb'], -4, 4);
                                    }
                                }
                            } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                $noteText = $noteText = "<green>" . $company->companyname . " paid Shipper $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                $noteText = $noteText = "<green>Carrier paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            }

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                            if ($_POST['from_to'] == Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY) {
                                // do nothing
                            } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                // do nothing
                            } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                // do nothing
                            }
                        }
                    break;

                    case "carrier":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                        $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_carrier']));
                        $from_to = explode("-", $_POST['from_to_carrier']);
                        $insert_arr['fromid'] = $from_to[0];
                        $insert_arr['toid'] = $from_to[1];
                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                        $insert_arr['amount'] = number_format((float) $_POST['amount_carrier'], 2, '.', '');
                        $insert_arr['method'] = $_POST['method'];
                        $insert_arr['transaction_id'] = $_POST['transaction_id'];

                        $noteText = '';
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

                                if ($_POST['cc_numb'] != "") {
                                    $noteText = " ending in #" . substr($_POST['cc_numb'], -4, 4);
                                }
                            break;
                            case "1":
                            case "2":
                            case "3":
                            case "4":
                                $insert_arr['check'] = $_POST['ch_number'];
                                if ($_POST['transaction_id'] != "") {
                                    $noteText = " #" . $_POST['transaction_id'];
                                } elseif ($_POST['ch_number'] != "") {
                                    $noteText = " #" . $_POST['ch_number'];
                                }
                            break;
                        }

                        $nmethod = $_POST['method'];
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                            $note = new Note($this->daffny->DB);
                            file_put_contents('DuplicationDetection.log', date('d-m-Y h:m:s') . " Point 1 : Entity ID: " . $_GET['id'] . " -- " . PHP_EOL, FILE_APPEND | LOCK_EX);
                            $note->create($note_array);

                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            $noteStr = "";
                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                            $note = new Note($this->daffny->DB);
                            file_put_contents('DuplicationDetection.log', date('d-m-Y h:m:s') . " Point 2 : Entity ID: " . $_GET['id'] . " -- " . PHP_EOL, FILE_APPEND | LOCK_EX);
                            $note->create($note_array);

                            if ($_POST['send_carrier_mail'] == 1) {
                                try {
                                    $member = $entity->getAssigned();
                                    $company = $member->getCompanyProfile();
                                } catch (Exception $e) {
                                    echo "<pre>";
                                    print_r($e);
                                    die;
                                }
                                try {
                                    $dispatch = $entity->getDispatchSheet();
                                } catch (Exception $e) {
                                    echo "<pre>";
                                    print_r($e);
                                    die;
                                }
                                try {
                                    $emailBody = '';
                                    if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' in the amount of $ ' . number_format((float) $_POST['amount_carrier'], 2, '.', '') . ' and will be mailed today.</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    } elseif ($nmethod == 9 || $nmethod == 8 || $nmethod == 7 || $nmethod == 5) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' by ' . Payment::$method_name[$nmethod] . ' for the amount of $ ' . number_format((float) $_POST['amount_carrier'], 2, '.', '') . '.</p><p>
                                                    <br /><br />
                                                    Confirmation Number is ' . $_POST['transaction_id'] . '</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    } elseif ($nmethod == 6) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' by electronic transfer and has been issued to the account provided by your company.</p><p>
                                                    <br /><br />
                                                    Confirmation Number is ' . $_POST['transaction_id'] . '</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    }

                                    $mail = new FdMailer(true);
                                    $mail->isHTML();
                                    $mail->Body = $emailBody;
                                    $mail->Subject = 'Payment issued for Order# ' . $entity->number;
                                    $mail->AddAddress($dispatch->carrier_email, $dispatch->carrier_contact_name);
                                    $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                                    $mail->send();

                                    $noteStr = "Carrier payment notification has been sent to " . $dispatch->carrier_email;
                                    /* UPDATE NOTE */
                                    $note_array_mail = array(
                                        "entity_id" => $_GET['id'],
                                        "sender_id" => $_SESSION['member_id'],
                                        "status" => 1,
                                        "type" => 3,
                                        "system_admin" => 1,
                                        "text" => $noteStr);

                                    $note = new Note($this->daffny->DB);
                                    $note->create($note_array_mail);

                                } catch (Exception $e) {}
                            }
                        }
                    break;

                    case "terminal":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
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
                        $member = new Member($this->daffny->DB);
                        $member->load($_SESSION['member_id']);
                        $company = $member->getCompanyProfile();

                        $nmethod = $_POST['method'];

                        if ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY) {
                            $noteText = "<green>Pickup Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P) {
                            $noteText = "<green>" . $company->companyname . " paid Pickup Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY) {
                            $noteText = "<green>Delivery Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D) {
                            $noteText = "<green>" . $company->companyname . " paid Delivery Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        }

                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }
                    break;

                    case "gateway":

                        $defaultSettings = new DefaultSettings($this->daffny->DB);
                        $defaultSettings->getByOwnerId(getParentId());

                        if (in_array($defaultSettings->current_gateway, array(1, 2, 3, 9))) {

                            if ($defaultSettings->current_gateway == 1) { //PayPal
                                if (trim($defaultSettings->paypal_api_username) == ""
                                    || trim($defaultSettings->paypal_api_password) == ""
                                    || trim($defaultSettings->paypal_api_signature) == ""
                                ) {
                                    $this->err[] = "PayPal: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }

                            if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                if (trim($defaultSettings->anet_api_login_id) == ""
                                    || trim($defaultSettings->anet_trans_key) == ""
                                ) {
                                    $this->err[] = "Autorize.net: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }

                            if ($defaultSettings->current_gateway == 3) { //Authorize.net
                                if (trim($defaultSettings->gateway_api_username) == ""
                                    || trim($defaultSettings->gateway_api_password) == ""
                                ) {
                                    $this->err[] = "Payment Gateway: Please complete API Credentials under 'My Profile > Default Settings'";
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
                                    $amount = (float) post_var("deposit_pay"); //$entity->total_deposit;
                                    break;
                                case "balance":
                                    $amount = (float) post_var("tariff_pay"); //$entity->total_tariff;
                                    break;
                                case "other":
                                    $amount = (float) post_var("other_amount");
                                    break;
                            }
                        } else {
                            $this->err[] = 'Please choose Payment Amount';
                        }

                        if ($amount == 0) {
                            $this->err[] = 'Amount can not be $0.00.';
                        }

                        $this->isEmpty("cc_number", "CC Number");
                        $this->isEmpty("cc_type", "CC Type");
                        $this->isEmpty("cc_month", "Exp. Month");
                        $this->isEmpty("cc_year", "Exp. Year");

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
                        $this->input = $arr;

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
                        if (!count($this->err)) {

                            // credit card number
                            $cardNumber = substr($pay_arr['cc_number'], -4);

                            //Authorize.net
                            if ($defaultSettings->current_gateway == 2) {
                                $ret = $this->processAuthorize($pay_arr);
                            }

                            //PayPal
                            if ($defaultSettings->current_gateway == 1) {
                                $ret = $this->processPayPal($pay_arr);
                            }

                            //MDSIP
                            if ($defaultSettings->current_gateway == 3) {
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

                                $ret = $this->processMDSIP($pay_arr1);
                            }

                            // Easy Pay
                            if ($defaultSettings->current_gateway == 9) {
                                
                                $defaultSettings = new DefaultSettings($this->daffny->DB);
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

                                $origin = $entity->getOrigin();
                                $destination = $entity->getDestination();
                                $description = $entity->prefix."-".$entity->number." ".$origin->city." ".$origin->state." to ".$destination->city." ".$destination->state." Powered by CargoFlare";
                                
                                $ePay->setOrder($entity->prefix."-".$entity->number, $description, 0, 0, "XXXXX", $_SERVER['REMOTE_ADDR']);
                                $ePay->doSale($pay_arr['amount'],$pay_arr['cc_number'], $pay_arr['cc_month'].substr($pay_arr['cc_year'], -2), $_POST['cc_cvv2']);
                                
                                if($ePay->responses['responsetext'] == "SUCCESS" || $ePay->responses['responsetext'] == "Approved"){
                                    $ret['success'] = true;
                                    $ret['transaction_id'] = $ePay->responses['transactionid'];
                                } else {
                                    $ret['success'] = false;
                                    $ret['error'] = $ePay->responses['responsetext'];
                                }
                                
                            }

                            //place
                            if (isset($ret['success']) && $ret['success'] == true) {
                                if($defaultSettings->current_gateway == 1){
                                    $gateway = "Paypal";
                                }
                                
                                if($defaultSettings->current_gateway == 2){
                                    $gateway = "Authorize.net";
                                }
                                
                                if($defaultSettings->current_gateway == 9){
                                    $gateway = "EasyPay";
                                }

                                //insert
                                $insert_arr['entity_id'] = (int) $_GET['id'];
                                $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                                $insert_arr['date_received'] = date("Y-m-d H:i:s");
                                $insert_arr['fromid'] = Payment::SBJ_SHIPPER;
                                $insert_arr['toid'] = Payment::SBJ_COMPANY;
                                $insert_arr['entered_by'] = $_SESSION['member_id'];
                                $insert_arr['amount'] = number_format((float) $pay_arr['amount'], 2, '.', '');
                                $insert_arr['notes'] = $gateway . $ret['transaction_id'];
                                $insert_arr['method'] = Payment::M_CC;
                                $insert_arr['gateway'] = $defaultSettings->current_gateway;
                                $insert_arr['transaction_id'] = $ret['transaction_id'];
                                $insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
                                $insert_arr['cc_type'] = $pay_arr['cc_type_name'];
                                $insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
                                $payment = new Payment($this->daffny->DB);
                                $payment->create($insert_arr);

                                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) == '' && trim($entity->archived) == '') {
                                    $entity->setStatus(Entity::STATUS_DELIVERED);
                                }

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<green>CREDIT CARD ENDING WITH ".$cardNumber." PROCESSED FOR THE AMOUNT OF $ " . number_format((float) $pay_arr['amount'], 2, '.', ''),
                                );
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);

                                try {
                                    $paymentcard = new Paymentcard($this->daffny->DB);
                                    $pc_arr = $pay_arr;
                                    $pc_arr['entity_id'] = (int) $_GET['id'];
                                    $pc_arr['owner_id'] = getParentId();
                                    $paymentcard->key = $this->daffny->cfg['security_salt'];
                                    $paymentcard->create($pc_arr);
                                } catch (Exception $e) {}

                                $this->setFlashInfo("Payment has been processed successfully.");
                                redirect(getLink("orders", "payments", "id", (int) $_GET['id']));
                            } else {
                                $this->err[] = $ret['error'];
                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<red>CREDIT CARD  ENDING WITH ".$cardNumber." PROCESSING ERROR:" . $ret['error']);
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);

                                $mail = new FdMailer(true);
                                $mail->isHTML();

                                $body = 'ORDER# ' . $entity->number . '<br/>Payment Type: Credit Card<br/>Transaction Amount: ' . number_format((float) $pay_arr['amount'], 2, '.', '') . '<br/>Error: ' . $ret['error'];
                                $body .= "<br><br>Sincerely,<br>".$entity->getAssigned()->contactname;
                                $body .= "<br>".$entity->getAssigned()->email;
                                $body .= "<br>".$entity->getAssigned()->phone;

                                $mail->Body = $body;
                                $mail->Subject = "Payment Error: " . $ret['error'];
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
                    break;

                    default:
                        throw new UserException("Invalid form data", getLink('orders/payments/id/' . $_GET['id']));
                    break;
                }

                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff()) {
                    $entity->setStatus(Entity::STATUS_DELIVERED);
                }
                //$entity->updateHeaderTable();
            }

            $entity->getVehicles();
            $this->applyOrder(Payment::TABLE);

            $this->order->setDefault('date_received', 'desc');
            $paymentManager = new PaymentManager($this->daffny->DB);
            $this->daffny->tpl->payments = $paymentManager->getPayments($_GET['id'], $this->order->getOrder(), $_SESSION['per_page']);
            $this->pager = $paymentManager->getPager();

            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );

            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['pager'] = $pager_html;
            $this->daffny->tpl->entity = $entity;

            $this->daffny->tpl->checks = $this->getPrintedChecks($_GET['id']);

            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
            $this->daffny->tpl->notes = $notes;

            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => 'Orders', getLink('orders/show/id/' . $_GET['id']) => 'Order #' . $entity->getNumber(), '' => 'Payments'));

            if (!isset($_POST['date_received']) || $_POST['date_received'] == "") {
                $this->input['date_received'] = date('m/d/Y');
            } else {
                $this->input['date_received'] = $_POST['date_received'];
            }

            $this->input['amount'] = $entity->getTotalTariff(false);
            $this->form->TextField('date_received', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
            $this->form->TextField('amount', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->TextField('transaction_id', '32', array(), "Transaction ID", "</td><td>");

            if (!isset($_POST['method']) || $_POST['method'] == "") {
                $this->input['method'] = 2;
            } else {
                $this->input['method'] = $_POST['method'];
            }

            $from_to_options = array(
                '' => 'Select One',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
                Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company',
            );
            $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array('class' => 'methods'), "Method", "</td><td>");

            if (!isset($_POST['date_received_carrier']) || $_POST['date_received_carrier'] == "") {
                $this->input['date_received_carrier'] = date('m/d/Y');
            } else {
                $this->input['date_received_carrier'] = $_POST['date_received_carrier'];
            }

            $from_to_options_carrier = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_CARRIER => 'Shipper to Carrier',
            );

            $this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->ComboBox('from_to_carrier', $from_to_options_carrier, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->TextField('date_received_carrier', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

            $from_to_options_terminal = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
                Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
                Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company',
            );

            $this->form->TextField('amount_terminal', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->ComboBox('from_to_terminal', $from_to_options_terminal, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->TextField('date_received_terminal', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

            /* Prefill CC */
            $entityCreditCard = $entity->getCreditCard();

            if (!isset($_POST['submit'])) {
                $this->input['gw_pt_type'] = 'balance';

                $paymentCard = new Paymentcard($this->daffny->DB);

                $paymentCard->key = $this->daffny->cfg['security_salt'];
                $paymentCard->loadLastCC((int) $_GET['id'], getParentId());
                if ($paymentCard->isLoaded()) {
                    $this->input['cc_fname'] = $paymentCard->cc_fname;
                    $this->input['cc_lname'] = $paymentCard->cc_lname;
                    $this->input['cc_address'] = $paymentCard->cc_address;
                    $this->input['cc_city'] = $paymentCard->cc_city;
                    $this->input['cc_state'] = $paymentCard->cc_state;
                    $this->input['cc_zip'] = $paymentCard->cc_zip;
                    $this->input['cc_cvv2'] = $paymentCard->cc_cvv2;
                    $this->input['cc_number'] = $paymentCard->cc_number;
                    $this->input['cc_type'] = $paymentCard->cc_type;
                    $this->input['cc_month'] = $paymentCard->cc_month;
                    $this->input['cc_year'] = $paymentCard->cc_year;
                } else {

                    $this->input['cc_fname'] = $entityCreditCard->fname;
                    $this->input['cc_lname'] = $entityCreditCard->lname;
                    $this->input['cc_address'] = $entityCreditCard->address;
                    $this->input['cc_city'] = $entityCreditCard->city;
                    $this->input['cc_state'] = $entityCreditCard->state;
                    $this->input['cc_zip'] = $entityCreditCard->zip;
                    $this->input['cc_cvv2'] = $entityCreditCard->cvv2;
                    $this->input['cc_number'] = $entityCreditCard->number;
                    $this->input['cc_type'] = $entityCreditCard->type;
                    $this->input['cc_month'] = $entityCreditCard->month;
                    $this->input['cc_year'] = $entityCreditCard->year;
                }
            }

            /** Invoice Upload Submit */
            if (isset($_POST['invoiceUpload'])) {
                // inserting notes
                $noteText = "<green>Carrier bill has been uploaded for this order</green>";

                /* UPDATE NOTE */
                $note_array = array(
                    "entity_id" => $_GET['id'],
                    "sender_id" => $_SESSION['member_id'],
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 0,
                    "text" => $noteText,
                );
                $note = new Note($this->daffny->DB);
                $note->create($note_array);

                $res = $this->daffny->DB->query("SELECT * FROM app_order_header WHERE entityid = " . $entity->id);
                while ($r = mysqli_fetch_assoc($res)) {
                    $res = $r;
                }

                $uploadDate = explode("/", $_POST['UploadDate']);
                $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                $sql_arr = array(
                    'EntityID' => $res['entityid'],
                    'OrderID' => $res['prefix'] . "-" . $res['number'],
                    'AccountID' => $res['account_id'],
                    'MemberID' => $res['assigned_id'],
                    'CarrierID' => $res['carrier_id'],
                    'CarrierName' => $this->daffny->tpl->dispatchSheet->carrier_company_name,
                    'Amount' => $_POST['Amount'],
                    'ProcessingFees' => $_POST['ProcessingFees'],
                    'FeesType' => $_POST['FeesType'],
                    'PaymentType' => $_POST['PaymentType'],
                    'Age' => $_POST['Age'],
                    'MaturityDate' => date('Y-m-d', strtotime($uploadDate . ' + ' . $_POST['Age'] . ' days')),
                    'Invoice' => 'In New Table',
                    'UploaderID' => $_SESSION['member']['id'],
                    'UploaderName' => $_SESSION['member']['contactname'],
                    'CreatedAt' => $uploadDate,
                );

                $res = $entity->update(array('balance_paid_by' => $_POST['PaymentType']));
                if (isset($_POST['UploadDate']) && ($_POST['UploadDate'] != "")) {
                    $sql_arr['CreatedAt'] = date("Y-m-d h:i:s", strtotime($_POST['UploadDate']));
                }

                $ins_arr = $this->daffny->DB->PrepareSql('Invoices', $sql_arr);
                $this->daffny->DB->insert('Invoices', $ins_arr);

                echo $insid = $this->daffny->DB->get_insert_id();
                die;
            }
            /** Invoice Upload Submit Closed */

            /** fetch Invoice Check Type*/
            $respInvoice = $this->daffny->DB->query("SELECT * FROM Invoices WHERE `EntityID`= " . $_GET['id'] . " AND PaymentType = 13");

            $carrierInvoiceCheck = array();
            while ($r = mysqli_fetch_assoc($respInvoice)) {
                $carrierInvoiceCheck[] = $r;
            }
            $existanceCheck = $this->daffny->DB->query("SELECT count(*) AS `Exists` FROM `Invoices` WHERE OrderID = '" . ($entity->prefix . "-" . $entity->number) . "' AND PaymentType = 13");

            while ($r = mysqli_fetch_assoc($existanceCheck)) {
                $existanceCheck = $r['Exists'];
            }
            $this->daffny->tpl->carrierInvoiceCheck = $carrierInvoiceCheck;
            $this->daffny->tpl->existanceCheck = $existanceCheck;

            /** fetch Invoice ACH Type*/
            $respInvoice = $this->daffny->DB->query("SELECT * FROM Invoices WHERE `EntityID`= " . $_GET['id'] . " AND PaymentType = 24");

            $carrierInvoiceACH = array();
            while ($r = mysqli_fetch_assoc($respInvoice)) {
                $carrierInvoiceACH[] = $r;
            }
            $existanceACH = $this->daffny->DB->query("SELECT count(*) AS `Exists` FROM `Invoices` WHERE OrderID = '" . ($entity->prefix . "-" . $entity->number) . "' AND PaymentType = 24");

            while ($r = mysqli_fetch_assoc($existanceACH)) {
                $existanceACH = $r['Exists'];
            }
            $this->daffny->tpl->carrierInvoiceACH = $carrierInvoiceACH;
            $this->daffny->tpl->existanceACH = $existanceACH;

            $this->form->TextField("cc_fname", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
            $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
            $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
            $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");

            $this->input['e_cc_fname'] = $entityCreditCard->fname;
            $this->input['e_cc_lname'] = $entityCreditCard->lname;
            $this->input['e_cc_address'] = $entityCreditCard->address;
            $this->input['e_cc_city'] = $entityCreditCard->city;
            $this->input['e_cc_state'] = $entityCreditCard->state;
            $this->input['e_cc_zip'] = $entityCreditCard->zip;
            $this->input['e_cc_cvv2'] = $entityCreditCard->cvv2;
            if ($_SESSION['member']['id'] == $_SESSION['member']['parent_id']) {
                $this->input['e_cc_number'] = $entityCreditCard->number;
            } else {
                $last_four = substr($entityCreditCard->number, strlen($entityCreditCard->number) - 5, 4);
                $this->input['e_cc_number'] = "000000000000" . $last_four;
            }
            $this->input['e_cc_type'] = $entityCreditCard->type;
            $this->input['e_cc_month'] = $entityCreditCard->month;
            $this->input['e_cc_year'] = $entityCreditCard->year;

            $this->form->TextField("e_cc_fname", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("e_cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("e_cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("e_cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array("style" => ""), "State", "</td><td>");
            $this->form->TextField("e_cc_zip", 11, array("class" => "zip", "style" => ""), "Zip Code", "</td><td>");
            $this->form->TextField("e_cc_cvv2", 4, array("class" => "cvv", "style" => ""), "CVV", "</td><td>");
            $this->form->TextField("e_cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => ""), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array("style" => ""), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array("style" => ""), "", "");

            if (!isset($_POST['payment_type']) || $_POST['payment_type'] == "") {
                $this->input['payment_type_selector'] = 'carrier';
            } else {
                $this->input['payment_type_selector'] = $_POST['payment_type'];
            }

            $is_carrier = 1;
            if ($company->is_carrier == 1) {
                $is_carrier = 0;
            }

            $this->form->helperGWType("payment_type_selector", array(), $is_carrier);
            $this->daffny->tpl->is_carrier = $is_carrier;

            $balances = array(
                'shipper' => 0,
                'carrier' => 0,
                'pterminal' => 0,
                'dterminal' => 0,
            );

            $carrierRemains = 0;
            $depositRemains = 0;
            $shipperRemains = 0;
            $amountType = 0;

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

                    $amountType = 1;
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
                    $amountType = 2;
                    break;
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
                    $carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
                    $shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
                    $balances['we_shipper'] = 0;
                    $balances['we_carrier'] = 0;
                    $balances['they_shipper'] = 0;
                    $balances['they_shipper_paid'] = ($shipperPaid > 0) ? $shipperPaid : 0;
                    $balances['they_carrier'] = $entity->getTotalDeposit(false) - $shipperPaid;
                    $balances['they_carrier_paid'] = $carrierPaid;

                    $depositRemains = $entity->getTotalDeposit(false) - $carrierPaid;
                    $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $carrierPaid;
                    $carrierRemains = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;
                    $amountType = 3;
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

            if ($amountType == 1 || $amountType == 3) {
                $shipperRemains = $depositRemains;
            }

            $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));
            $this->input['amount_carrier'] = $carrierRemains;
            $this->daffny->tpl->carrierRemains = $carrierRemains;

            $this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');

            foreach ($balances as $key => $balance) {
                if (stripos($key, '_paid') === false) {
                    if (isset($balances[$key . '_paid'])) {
                        if ($balance > 0) {
                            $this->input[$key] = "$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '';
                        }
                        if ($balances[$key . '_paid'] > 0) {
                            $this->input[$key . '_paid'] = "$ " . number_format(abs($balances[$key . '_paid']), 2) . "</span>";
                        } else {
                            $this->input[$key . '_paid'] = '';
                        }
                    } else {
                        if ($balance > 0) {
                            $this->input[$key] = "$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '$ 0.00';
                        }
                        $this->input[$key . '_paid'] = '';
                    }
                }
            }
            $entity->updateHeaderTable();
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders','show','id',$_GET['id']));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders','show','id',$_GET['id']));
        }
    }

    public function payments2()
    {
        $this->check_access('payments');
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException('Invalid Order ID', getLink('orders'));
            }

            $this->tplname = "orders.payments_new";
            $this->title = "Order Payments";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            if (isset($_POST['payment_type'])) {
                $info = "Order Payment";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $insert_arr = array();
                switch ($_POST['payment_type']) {
                    case "internally":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
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
                        $member = new Member($this->daffny->DB);
                        $member->load($_SESSION['member_id']);
                        $company = $member->getCompanyProfile();

                        $nmethod = $_POST['method'];

                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");
                            $noteText = "<green>Payment " . $payment->getFrom() . " to " . $payment->getTo() . " is modified from $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . $member->contactname . " on " . date("m/d/Y h:i A");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );
                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            if ($_POST['from_to'] == Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY) {
                                $noteText = "<green>Shipper paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                                if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                    if ($_POST['transaction_id'] != "") {
                                        $noteText .= " #" . $_POST['transaction_id'];
                                    } elseif ($_POST['ch_number'] != "") {
                                        $noteText .= " #" . $_POST['ch_number'];
                                    }

                                } elseif ($nmethod == 9) {
                                    if ($_POST['cc_numb'] != "") {
                                        $noteText .= " ending in #" . substr($_POST['cc_numb'], -4, 4);
                                    }
                                }
                            } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                $noteText = $noteText = "<green>" . $company->companyname . " paid Shipper $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                $noteText = $noteText = "<green>Carrier paid " . $company->companyname . " $ " . number_format((float) $_POST['amount'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                            }

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                            if ($_POST['from_to'] == Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY) {
                                // do nothing
                            } elseif ($_POST['from_to'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER) {
                                // do nothing
                            } elseif ($_POST['from_to'] == Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY) {
                                // do nothing
                            }
                        }
                    break;

                    case "carrier":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                        $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_carrier']));
                        $from_to = explode("-", $_POST['from_to_carrier']);
                        $insert_arr['fromid'] = $from_to[0];
                        $insert_arr['toid'] = $from_to[1];
                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                        $insert_arr['amount'] = number_format((float) $_POST['amount_carrier'], 2, '.', '');
                        $insert_arr['method'] = $_POST['method'];
                        $insert_arr['transaction_id'] = $_POST['transaction_id'];

                        $noteText = '';
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

                                if ($_POST['cc_numb'] != "") {
                                    $noteText = " ending in #" . substr($_POST['cc_numb'], -4, 4);
                                }
                            break;
                            case "1":
                            case "2":
                            case "3":
                            case "4":
                                $insert_arr['check'] = $_POST['ch_number'];
                                if ($_POST['transaction_id'] != "") {
                                    $noteText = " #" . $_POST['transaction_id'];
                                } elseif ($_POST['ch_number'] != "") {
                                    $noteText = " #" . $_POST['ch_number'];
                                }
                            break;
                        }

                        $nmethod = $_POST['method'];
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                            $note = new Note($this->daffny->DB);
                            file_put_contents('DuplicationDetection.log', date('d-m-Y h:m:s') . " Point 1 : Entity ID: " . $_GET['id'] . " -- " . PHP_EOL, FILE_APPEND | LOCK_EX);
                            $note->create($note_array);

                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            $noteStr = "";
                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "<green>Carrier has been paid amount $ " . number_format((float) $_POST['amount_carrier'], 2, '.', '') . " by " . Payment::$method_name[$nmethod] . $noteText);

                            $note = new Note($this->daffny->DB);
                            file_put_contents('DuplicationDetection.log', date('d-m-Y h:m:s') . " Point 2 : Entity ID: " . $_GET['id'] . " -- " . PHP_EOL, FILE_APPEND | LOCK_EX);
                            $note->create($note_array);

                            if ($_POST['send_carrier_mail'] == 1) {
                                try {
                                    $member = $entity->getAssigned();
                                    $company = $member->getCompanyProfile();
                                } catch (Exception $e) {
                                    echo "<pre>";
                                    print_r($e);
                                    die;
                                }
                                try {
                                    $dispatch = $entity->getDispatchSheet();
                                } catch (Exception $e) {
                                    echo "<pre>";
                                    print_r($e);
                                    die;
                                }
                                try {
                                    $emailBody = '';
                                    if ($nmethod == 1 || $nmethod == 2 || $nmethod == 3 || $nmethod == 4) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' in the amount of $ ' . number_format((float) $_POST['amount_carrier'], 2, '.', '') . ' and will be mailed today.</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    } elseif ($nmethod == 9 || $nmethod == 8 || $nmethod == 7 || $nmethod == 5) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' by ' . Payment::$method_name[$nmethod] . ' for the amount of $ ' . number_format((float) $_POST['amount_carrier'], 2, '.', '') . '.</p><p>
                                                    <br /><br />
                                                    Confirmation Number is ' . $_POST['transaction_id'] . '</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    } elseif ($nmethod == 6) {
                                        $emailBody = '<p>' . $company->companyname . ' has processed payment for order# ' . $entity->number . ' by electronic transfer and has been issued to the account provided by your company.</p><p>
                                                    <br /><br />
                                                    Confirmation Number is ' . $_POST['transaction_id'] . '</p><p>
                                                    <br />
                                                    Thank you,<br />
                                                    ' . $company->companyname . '<br />
                                                    ' . $company->phone_tollfree . '<br />
                                                    ' . $company->fax . '<br />
                                                    ' . $company->email . '</p>';
                                    }

                                    $mail = new FdMailer(true);
                                    $mail->isHTML();
                                    $mail->Body = $emailBody;
                                    $mail->Subject = 'Payment issued for Order# ' . $entity->number;
                                    $mail->AddAddress($dispatch->carrier_email, $dispatch->carrier_contact_name);
                                    $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
                                    $mail->send();

                                    $noteStr = "Carrier payment notification has been sent to " . $dispatch->carrier_email;
                                    /* UPDATE NOTE */
                                    $note_array_mail = array(
                                        "entity_id" => $_GET['id'],
                                        "sender_id" => $_SESSION['member_id'],
                                        "status" => 1,
                                        "type" => 3,
                                        "system_admin" => 1,
                                        "text" => $noteStr);

                                    $note = new Note($this->daffny->DB);
                                    $note->create($note_array_mail);

                                } catch (Exception $e) {}
                            }
                        }
                    break;

                    case "terminal":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
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
                        $member = new Member($this->daffny->DB);
                        $member->load($_SESSION['member_id']);
                        $company = $member->getCompanyProfile();

                        $nmethod = $_POST['method'];

                        if ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY) {
                            $noteText = "<green>Pickup Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P) {
                            $noteText = "<green>" . $company->companyname . " paid Pickup Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY) {
                            $noteText = "<green>Delivery Terminal paid " . $company->companyname . " $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        } elseif ($_POST['from_to_terminal'] == Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D) {
                            $noteText = "<green>" . $company->companyname . " paid Delivery Terminal $ " . number_format((float) $_POST['amount_terminal'], 2, '.', '') . " by " . Payment::$method_name[$nmethod];
                        }

                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Payment has been updated successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Payment has been processed successfully.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => $noteText);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }
                    break;

                    case "gateway":

                        $defaultSettings = new DefaultSettings($this->daffny->DB);
                        $defaultSettings->getByOwnerId(getParentId());

                        if (in_array($defaultSettings->current_gateway, array(1, 2, 3, 9))) {

                            if ($defaultSettings->current_gateway == 1) { //PayPal
                                if (trim($defaultSettings->paypal_api_username) == ""
                                    || trim($defaultSettings->paypal_api_password) == ""
                                    || trim($defaultSettings->paypal_api_signature) == ""
                                ) {
                                    $this->err[] = "PayPal: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }

                            if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                if (trim($defaultSettings->anet_api_login_id) == ""
                                    || trim($defaultSettings->anet_trans_key) == ""
                                ) {
                                    $this->err[] = "Autorize.net: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }

                            if ($defaultSettings->current_gateway == 3) { //Authorize.net
                                if (trim($defaultSettings->gateway_api_username) == ""
                                    || trim($defaultSettings->gateway_api_password) == ""
                                ) {
                                    $this->err[] = "Payment Gateway: Please complete API Credentials under 'My Profile > Default Settings'";
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
                                    $amount = (float) post_var("deposit_pay"); //$entity->total_deposit;
                                    break;
                                case "balance":
                                    $amount = (float) post_var("tariff_pay"); //$entity->total_tariff;
                                    break;
                                case "other":
                                    $amount = (float) post_var("other_amount");
                                    break;
                            }
                        } else {
                            $this->err[] = 'Please choose Payment Amount';
                        }

                        if ($amount == 0) {
                            $this->err[] = 'Amount can not be $0.00.';
                        }

                        $this->isEmpty("cc_number", "CC Number");
                        $this->isEmpty("cc_type", "CC Type");
                        $this->isEmpty("cc_month", "Exp. Month");
                        $this->isEmpty("cc_year", "Exp. Year");

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
                        $this->input = $arr;

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
                        if (!count($this->err)) {

                            // credit card number
                            $cardNumber = substr($pay_arr['cc_number'], -4);

                            //Authorize.net
                            if ($defaultSettings->current_gateway == 2) {
                                $ret = $this->processAuthorize($pay_arr);
                            }

                            //PayPal
                            if ($defaultSettings->current_gateway == 1) {
                                $ret = $this->processPayPal($pay_arr);
                            }

                            //MDSIP
                            if ($defaultSettings->current_gateway == 3) {
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

                                $ret = $this->processMDSIP($pay_arr1);
                            }

                            // Easy Pay
                            if ($defaultSettings->current_gateway == 9) {
                                
                                $defaultSettings = new DefaultSettings($this->daffny->DB);
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

                                $origin = $entity->getOrigin();
                                $destination = $entity->getDestination();
                                $description = $entity->prefix."-".$entity->number." ".$origin->city." ".$origin->state." to ".$destination->city." ".$destination->state." Powered by CargoFlare";
                                
                                $ePay->setOrder($entity->prefix."-".$entity->number, $description, 0, 0, "XXXXX", $_SERVER['REMOTE_ADDR']);
                                $ePay->doSale($pay_arr['amount'],$pay_arr['cc_number'], $pay_arr['cc_month'].substr($pay_arr['cc_year'], -2), $_POST['cc_cvv2']);
                                
                                if($ePay->responses['responsetext'] == "SUCCESS" || $ePay->responses['responsetext'] == "Approved"){
                                    $ret['success'] = true;
                                    $ret['transaction_id'] = $ePay->responses['transactionid'];
                                } else {
                                    $ret['success'] = false;
                                    $ret['error'] = $ePay->responses['responsetext'];
                                }
                                
                            }

                            //place
                            if (isset($ret['success']) && $ret['success'] == true) {
                                if($defaultSettings->current_gateway == 1){
                                    $gateway = "Paypal";
                                }
                                
                                if($defaultSettings->current_gateway == 2){
                                    $gateway = "Authorize.net";
                                }
                                
                                if($defaultSettings->current_gateway == 9){
                                    $gateway = "EasyPay";
                                }

                                //insert
                                $insert_arr['entity_id'] = (int) $_GET['id'];
                                $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                                $insert_arr['date_received'] = date("Y-m-d H:i:s");
                                $insert_arr['fromid'] = Payment::SBJ_SHIPPER;
                                $insert_arr['toid'] = Payment::SBJ_COMPANY;
                                $insert_arr['entered_by'] = $_SESSION['member_id'];
                                $insert_arr['amount'] = number_format((float) $pay_arr['amount'], 2, '.', '');
                                $insert_arr['notes'] = $gateway . $ret['transaction_id'];
                                $insert_arr['method'] = Payment::M_CC;
                                $insert_arr['gateway'] = $defaultSettings->current_gateway;
                                $insert_arr['transaction_id'] = $ret['transaction_id'];
                                $insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
                                $insert_arr['cc_type'] = $pay_arr['cc_type_name'];
                                $insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
                                $payment = new Payment($this->daffny->DB);
                                $payment->create($insert_arr);

                                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) == '' && trim($entity->archived) == '') {
                                    $entity->setStatus(Entity::STATUS_DELIVERED);
                                }

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<green>CREDIT CARD ENDING WITH ".$cardNumber." PROCESSED FOR THE AMOUNT OF $ " . number_format((float) $pay_arr['amount'], 2, '.', ''),
                                );
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);

                                try {
                                    $paymentcard = new Paymentcard($this->daffny->DB);
                                    $pc_arr = $pay_arr;
                                    $pc_arr['entity_id'] = (int) $_GET['id'];
                                    $pc_arr['owner_id'] = getParentId();
                                    $paymentcard->key = $this->daffny->cfg['security_salt'];
                                    $paymentcard->create($pc_arr);
                                } catch (Exception $e) {}

                                $this->setFlashInfo("Payment has been processed successfully.");
                                redirect(getLink("orders", "payments", "id", (int) $_GET['id']));
                            } else {
                                $this->err[] = $ret['error'];
                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "<red>CREDIT CARD  ENDING WITH ".$cardNumber." PROCESSING ERROR:" . $ret['error']);
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);

                                $mail = new FdMailer(true);
                                $mail->isHTML();

                                $body = 'ORDER# ' . $entity->number . '<br/>Payment Type: Credit Card<br/>Transaction Amount: ' . number_format((float) $pay_arr['amount'], 2, '.', '') . '<br/>Error: ' . $ret['error'];
                                $body .= "<br><br>Sincerely,<br>".$entity->getAssigned()->contactname;
                                $body .= "<br>".$entity->getAssigned()->email;
                                $body .= "<br>".$entity->getAssigned()->phone;

                                $mail->Body = $body;
                                $mail->Subject = "Payment Error: " . $ret['error'];
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
                    break;

                    default:
                        throw new UserException("Invalid form data", getLink('orders/payments/id/' . $_GET['id']));
                    break;
                }

                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff()) {
                    $entity->setStatus(Entity::STATUS_DELIVERED);
                }
                //$entity->updateHeaderTable();
            }

            $entity->getVehicles();
            $this->applyOrder(Payment::TABLE);

            $this->order->setDefault('date_received', 'desc');
            $paymentManager = new PaymentManager($this->daffny->DB);
            $this->daffny->tpl->payments = $paymentManager->getPayments($_GET['id'], $this->order->getOrder(), $_SESSION['per_page']);
            $this->pager = $paymentManager->getPager();

            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );

            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['pager'] = $pager_html;
            $this->daffny->tpl->entity = $entity;

            $this->daffny->tpl->checks = $this->getPrintedChecks($_GET['id']);

            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");
            $this->daffny->tpl->notes = $notes;

            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => 'Orders', getLink('orders/show/id/' . $_GET['id']) => 'Order #' . $entity->getNumber(), '' => 'Payments'));

            if (!isset($_POST['date_received']) || $_POST['date_received'] == "") {
                $this->input['date_received'] = date('m/d/Y');
            } else {
                $this->input['date_received'] = $_POST['date_received'];
            }

            $this->input['amount'] = $entity->getTotalTariff(false);
            $this->form->TextField('date_received', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
            $this->form->TextField('amount', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->TextField('transaction_id', '32', array(), "Transaction ID", "</td><td>");

            if (!isset($_POST['method']) || $_POST['method'] == "") {
                $this->input['method'] = 2;
            } else {
                $this->input['method'] = $_POST['method'];
            }

            $from_to_options = array(
                '' => 'Select One',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
                Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company',
            );
            $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array('class' => 'methods'), "Method", "</td><td>");

            if (!isset($_POST['date_received_carrier']) || $_POST['date_received_carrier'] == "") {
                $this->input['date_received_carrier'] = date('m/d/Y');
            } else {
                $this->input['date_received_carrier'] = $_POST['date_received_carrier'];
            }

            $from_to_options_carrier = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_CARRIER => 'Shipper to Carrier',
            );

            $this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->ComboBox('from_to_carrier', $from_to_options_carrier, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->TextField('date_received_carrier', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

            $from_to_options_terminal = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
                Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
                Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company',
            );

            $this->form->TextField('amount_terminal', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->ComboBox('from_to_terminal', $from_to_options_terminal, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->TextField('date_received_terminal', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

            /* Prefill CC */
            $entityCreditCard = $entity->getCreditCard();

            if (!isset($_POST['submit'])) {
                $this->input['gw_pt_type'] = 'balance';

                $paymentCard = new Paymentcard($this->daffny->DB);

                $paymentCard->key = $this->daffny->cfg['security_salt'];
                $paymentCard->loadLastCC((int) $_GET['id'], getParentId());
                if ($paymentCard->isLoaded()) {
                    $this->input['cc_fname'] = $paymentCard->cc_fname;
                    $this->input['cc_lname'] = $paymentCard->cc_lname;
                    $this->input['cc_address'] = $paymentCard->cc_address;
                    $this->input['cc_city'] = $paymentCard->cc_city;
                    $this->input['cc_state'] = $paymentCard->cc_state;
                    $this->input['cc_zip'] = $paymentCard->cc_zip;
                    $this->input['cc_cvv2'] = $paymentCard->cc_cvv2;
                    $this->input['cc_number'] = $paymentCard->cc_number;
                    $this->input['cc_type'] = $paymentCard->cc_type;
                    $this->input['cc_month'] = $paymentCard->cc_month;
                    $this->input['cc_year'] = $paymentCard->cc_year;
                } else {

                    $this->input['cc_fname'] = $entityCreditCard->fname;
                    $this->input['cc_lname'] = $entityCreditCard->lname;
                    $this->input['cc_address'] = $entityCreditCard->address;
                    $this->input['cc_city'] = $entityCreditCard->city;
                    $this->input['cc_state'] = $entityCreditCard->state;
                    $this->input['cc_zip'] = $entityCreditCard->zip;
                    $this->input['cc_cvv2'] = $entityCreditCard->cvv2;
                    $this->input['cc_number'] = $entityCreditCard->number;
                    $this->input['cc_type'] = $entityCreditCard->type;
                    $this->input['cc_month'] = $entityCreditCard->month;
                    $this->input['cc_year'] = $entityCreditCard->year;
                }
            }

            /** Invoice Upload Submit */
            if (isset($_POST['invoiceUpload'])) {
                // inserting notes
                $noteText = "<green>Carrier bill has been uploaded for this order</green>";

                /* UPDATE NOTE */
                $note_array = array(
                    "entity_id" => $_GET['id'],
                    "sender_id" => $_SESSION['member_id'],
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 0,
                    "text" => $noteText,
                );
                $note = new Note($this->daffny->DB);
                $note->create($note_array);

                $res = $this->daffny->DB->query("SELECT * FROM app_order_header WHERE entityid = " . $entity->id);
                while ($r = mysqli_fetch_assoc($res)) {
                    $res = $r;
                }

                $uploadDate = explode("/", $_POST['UploadDate']);
                $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                $sql_arr = array(
                    'EntityID' => $res['entityid'],
                    'OrderID' => $res['prefix'] . "-" . $res['number'],
                    'AccountID' => $res['account_id'],
                    'MemberID' => $res['assigned_id'],
                    'CarrierID' => $res['carrier_id'],
                    'CarrierName' => $this->daffny->tpl->dispatchSheet->carrier_company_name,
                    'Amount' => $_POST['Amount'],
                    'ProcessingFees' => $_POST['ProcessingFees'],
                    'FeesType' => $_POST['FeesType'],
                    'PaymentType' => $_POST['PaymentType'],
                    'Age' => $_POST['Age'],
                    'MaturityDate' => date('Y-m-d', strtotime($uploadDate . ' + ' . $_POST['Age'] . ' days')),
                    'Invoice' => 'In New Table',
                    'UploaderID' => $_SESSION['member']['id'],
                    'UploaderName' => $_SESSION['member']['contactname'],
                    'CreatedAt' => $uploadDate,
                );

                $res = $entity->update(array('balance_paid_by' => $_POST['PaymentType']));
                if (isset($_POST['UploadDate']) && ($_POST['UploadDate'] != "")) {
                    $sql_arr['CreatedAt'] = date("Y-m-d h:i:s", strtotime($_POST['UploadDate']));
                }

                $ins_arr = $this->daffny->DB->PrepareSql('Invoices', $sql_arr);
                $this->daffny->DB->insert('Invoices', $ins_arr);

                echo $insid = $this->daffny->DB->get_insert_id();
                die;
            }
            /** Invoice Upload Submit Closed */

            /** fetch Invoice Check Type*/
            $respInvoice = $this->daffny->DB->query("SELECT * FROM Invoices WHERE `EntityID`= " . $_GET['id'] . " AND PaymentType = 13");

            $carrierInvoiceCheck = array();
            while ($r = mysqli_fetch_assoc($respInvoice)) {
                $carrierInvoiceCheck[] = $r;
            }
            $existanceCheck = $this->daffny->DB->query("SELECT count(*) AS `Exists` FROM `Invoices` WHERE OrderID = '" . ($entity->prefix . "-" . $entity->number) . "' AND PaymentType = 13");

            while ($r = mysqli_fetch_assoc($existanceCheck)) {
                $existanceCheck = $r['Exists'];
            }
            $this->daffny->tpl->carrierInvoiceCheck = $carrierInvoiceCheck;
            $this->daffny->tpl->existanceCheck = $existanceCheck;

            /** fetch Invoice ACH Type*/
            $respInvoice = $this->daffny->DB->query("SELECT * FROM Invoices WHERE `EntityID`= " . $_GET['id'] . " AND PaymentType = 24");

            $carrierInvoiceACH = array();
            while ($r = mysqli_fetch_assoc($respInvoice)) {
                $carrierInvoiceACH[] = $r;
            }
            $existanceACH = $this->daffny->DB->query("SELECT count(*) AS `Exists` FROM `Invoices` WHERE OrderID = '" . ($entity->prefix . "-" . $entity->number) . "' AND PaymentType = 24");

            while ($r = mysqli_fetch_assoc($existanceACH)) {
                $existanceACH = $r['Exists'];
            }
            $this->daffny->tpl->carrierInvoiceACH = $carrierInvoiceACH;
            $this->daffny->tpl->existanceACH = $existanceACH;

            $this->form->TextField("cc_fname", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
            $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
            $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
            $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");

            $this->input['e_cc_fname'] = $entityCreditCard->fname;
            $this->input['e_cc_lname'] = $entityCreditCard->lname;
            $this->input['e_cc_address'] = $entityCreditCard->address;
            $this->input['e_cc_city'] = $entityCreditCard->city;
            $this->input['e_cc_state'] = $entityCreditCard->state;
            $this->input['e_cc_zip'] = $entityCreditCard->zip;
            $this->input['e_cc_cvv2'] = $entityCreditCard->cvv2;
            if ($_SESSION['member']['id'] == $_SESSION['member']['parent_id']) {
                $this->input['e_cc_number'] = $entityCreditCard->number;
            } else {
                $last_four = substr($entityCreditCard->number, strlen($entityCreditCard->number) - 5, 4);
                $this->input['e_cc_number'] = "000000000000" . $last_four;
            }
            $this->input['e_cc_type'] = $entityCreditCard->type;
            $this->input['e_cc_month'] = $entityCreditCard->month;
            $this->input['e_cc_year'] = $entityCreditCard->year;

            $this->form->TextField("e_cc_fname", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("e_cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("e_cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("e_cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array("style" => ""), "State", "</td><td>");
            $this->form->TextField("e_cc_zip", 11, array("class" => "zip", "style" => ""), "Zip Code", "</td><td>");
            $this->form->TextField("e_cc_cvv2", 4, array("class" => "cvv", "style" => ""), "CVV", "</td><td>");
            $this->form->TextField("e_cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => ""), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array("style" => ""), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array("style" => ""), "", "");

            if (!isset($_POST['payment_type']) || $_POST['payment_type'] == "") {
                $this->input['payment_type_selector'] = 'carrier';
            } else {
                $this->input['payment_type_selector'] = $_POST['payment_type'];
            }

            $is_carrier = 1;
            if ($company->is_carrier == 1) {
                $is_carrier = 0;
            }

            $this->form->helperGWType("payment_type_selector", array(), $is_carrier);
            $this->daffny->tpl->is_carrier = $is_carrier;

            $balances = array(
                'shipper' => 0,
                'carrier' => 0,
                'pterminal' => 0,
                'dterminal' => 0,
            );

            $carrierRemains = 0;
            $depositRemains = 0;
            $shipperRemains = 0;
            $amountType = 0;

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

                    $amountType = 1;
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
                    $amountType = 2;
                    break;
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
                    $carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
                    $shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
                    $balances['we_shipper'] = 0;
                    $balances['we_carrier'] = 0;
                    $balances['they_shipper'] = 0;
                    $balances['they_shipper_paid'] = ($shipperPaid > 0) ? $shipperPaid : 0;
                    $balances['they_carrier'] = $entity->getTotalDeposit(false) - $shipperPaid;
                    $balances['they_carrier_paid'] = $carrierPaid;

                    $depositRemains = $entity->getTotalDeposit(false) - $carrierPaid;
                    $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $carrierPaid;
                    $carrierRemains = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;
                    $amountType = 3;
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

            if ($amountType == 1 || $amountType == 3) {
                $shipperRemains = $depositRemains;
            }

            $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));
            $this->input['amount_carrier'] = $carrierRemains;
            $this->daffny->tpl->carrierRemains = $carrierRemains;

            $this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');

            foreach ($balances as $key => $balance) {
                if (stripos($key, '_paid') === false) {
                    if (isset($balances[$key . '_paid'])) {
                        if ($balance > 0) {
                            $this->input[$key] = "$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '';
                        }
                        if ($balances[$key . '_paid'] > 0) {
                            $this->input[$key . '_paid'] = "$ " . number_format(abs($balances[$key . '_paid']), 2) . "</span>";
                        } else {
                            $this->input[$key . '_paid'] = '';
                        }
                    } else {
                        if ($balance > 0) {
                            $this->input[$key] = "$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '$ 0.00';
                        }
                        $this->input[$key . '_paid'] = '';
                    }
                }
            }
            $entity->updateHeaderTable();
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders','show','id',$_GET['id']));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders','show','id',$_GET['id']));
        }
    }

    public function create()
    {
        try {
            
            // $notification = new Notification($this->daffny->DB);
            // $notification->add(2, [
            //     'title' => 'This is the title',
            //     'message' => 'This is the alert messsage',
            //     'link' => 'http://facebook.com'
            // ]);

            $this->tplname = "orders.create";
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Create'));
            $this->title = "Create new Order";
            $this->daffny->tpl->create = true;
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
            $this->daffny->tpl->referrers_arr = $referrers_arr;

            $referrer_status = 0;
            $payments_terms = "";
            $row = $this->daffny->DB->select_one("referrer_status,payments_terms", "app_defaultsettings", "WHERE  owner_id = '" . getParentId() . "'");
            if (!empty($row)) {
                $referrer_status = $row['referrer_status'];
                $payments_terms = $row['payments_terms'];
            }

            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm(true, $referrer_status)) {

                // create log
                $info = "Create Order";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();
                $distance = RouteHelper::getRouteDistance($sql_arr['origin_city'] . "," . $sql_arr['origin_state'] . "," . $sql_arr['origin_country'], $sql_arr['destination_city'] . "," . $sql_arr['destination_state'] . "," . $sql_arr['destination_country']);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                $referrer_name_value = "";
                $salesrep = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];
                        $salesrep = $row_referrer['salesrep'];
                    }
                }

                // Create Entity
                $entity = new Entity($this->daffny->DB);
                $insert_arr = array(
                    'type' => Entity::TYPE_ORDER,
                    'creator_id' => $_SESSION['member']['id'],
                    'assigned_id' => $_SESSION['member']['id'],
                    'parentid' => getParentId(),
                    'salesrepid' => $salesrep,
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'avail_pickup_date' => empty($sql_arr['avail_pickup_date']) ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date'])),
                    'ship_via' => $sql_arr['shipping_ship_via'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['notes_from_shipper'],
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'ordered' => date('Y-m-d H:i:s'),
                    'payments_terms' => $sql_arr['payments_terms'],
                    'account_payble_contact' => $sql_arr['account_payble_contact'],
                    'auto_payment' => empty($sql_arr['auto_payment']) ? 0 : $sql_arr['auto_payment'],
                    'match_carrier' => $sql_arr['match_carrier'],
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );
                $entity->create($insert_arr);
                // Create Shipper
                $shipper = new Shipper($this->daffny->DB);
                $insert_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                    'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
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
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['origin_company'],
                    'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['origin_phone4']),
                    'phone1_ext' => $sql_arr['origin_phone1_ext'],
                    'phone2_ext' => $sql_arr['origin_phone2_ext'],
                    'phone3_ext' => $sql_arr['origin_phone3_ext'],
                    'phone4_ext' => $sql_arr['origin_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['origin_mobile2']),
                    'company' => $sql_arr['origin_company_name'],
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'fax2' => $sql_arr['origin_fax2'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
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
                    'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['destination_phone4']),
                    'phone1_ext' => $sql_arr['destination_phone1_ext'],
                    'phone2_ext' => $sql_arr['destination_phone2_ext'],
                    'phone3_ext' => $sql_arr['destination_phone3_ext'],
                    'phone4_ext' => $sql_arr['destination_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['destination_mobile2']),
                    'company' => $sql_arr['destination_company_name'],
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'fax2' => $sql_arr['destination_fax2'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                );
                $destination->create($insert_arr, $entity->id);
                // Create Notes
                if (trim($sql_arr['notes_from_shipper']) != "") {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }
                // Create Internal Notes
                if (trim($sql_arr['note_to_shipper']) != "") {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));
                }
                // custom notes functionality
                if ($_POST['balance_paid_by'] == 24) {
                    $amount = 0;
                    $carrier_fee = 0;
                    foreach ($_POST['year'] as $key => $val) {
                        $carrier_fee = $carrier_fee + ($_POST['tariff'][$key] - $_POST['deposit'][$key]);
                    }

                    // ACH payment fees type options
                    if ($_POST['fee_type'] == 1) {
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with no fee';
                    } elseif ($_POST['fee_type'] == 2) {
                        $amount = ((int) $carrier_fee * 0.03) + 12;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 3) {
                        $amount = ((int) $carrier_fee * 0.05) + 12;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 4) {
                        $amount = ((int) $carrier_fee * 0.03) + 0;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 5) {
                        $amount = ((int) $carrier_fee * 0.05) + 0;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } else {
                        $customNote = 'Invalid Fee Type Selected';
                    }

                    $note_array = array(
                        "entity_id" => $entity->id,
                        "sender_id" => $_SESSION['member_id'],
                        "type" => 3,
                        "text" => $customNote,
                    );
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);
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
                        'lot' => $_POST['lot'][$key],
                        'vin' => $_POST['vin'][$key],
                        'plate' => $_POST['plate'][$key],
                        'state' => $_POST['state'][$key],
                        'color' => $_POST['color'][$key],
                        'inop' => $_POST['inop'][$key],
                        'tariff' => $_POST['tariff'][$key],
                        'deposit' => $_POST['deposit'][$key],
                        'carrier_pay' => $_POST['tariff'][$key] - $_POST['deposit'][$key],
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

                $accT = new Account($this->daffny->DB);
                $accountArray = array(
                    'owner_id' => getParentId(),
                    'company_name' => $sql_arr['shipper_company'],
                    'status' => Account::STATUS_ACTIVE,
                    'is_carrier' => 0,
                    'is_shipper' => 1,
                    'is_location' => 0,
                    'first_name' => $sql_arr['shipper_fname'],
                    'last_name' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                    'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                    'cell' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fax']),
                    'address1' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_address1']),
                    'address2' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_address2']),
                    'city' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']),
                    'state' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_state']),
                    'state_other' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_state']),
                    'zip_code' => $sql_arr['shipper_zip'],
                    'country' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_country']),
                    'shipper_type' => $sql_arr['shipper_type'],
                    'hours_of_operation' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_hours']),
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'account_payble_contact' => $sql_arr['account_payble_contact'],
                );

                if ($sql_arr['shipper_company']) {
                    $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_company']) . "' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                } else {
                    $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                }

                if (empty($rowShipper)) {
                    $accT->create($accountArray);

                    // Update Entity
                    $update_account_id_arr = array(
                        'account_id' => $accT->id,
                    );
                    $entity->update($update_account_id_arr);
                } else {
                    $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $accountArray);
                    $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");
    
                    // Update Entity
                    $update_account_id_arr = array(
                        'account_id' => $rowShipper["id"],
                    );
                    $entity->update($update_account_id_arr);
                }

                // if (post_var('save_shipper') == 1 || post_var('update_shipper') == 1) {

                    

                //     if ($sql_arr['shipper_company']) {
                //         $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_company']) . "' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                //     } else {
                //         $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                //     }

                //     if (empty($rowShipper) && post_var('save_shipper') == 1) {
                //         $shipper->create($shipperArr);

                //         // Update Entity
                //         $update_account_id_arr = array(
                //             'account_id' => $shipper->id,
                //         );
                //         $entity->update($update_account_id_arr);
                        
                //     } elseif (post_var('update_shipper') == 1) {
                //         if ($rowShipper["id"] != '' && $sql_arr['shipper_company'] != "") {

                //             $shipper->load($rowShipper["id"]);
                //             unset($shipperArr['referred_by']);
                //             unset($shipperArr['referred_id']);
                //             $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $shipperArr);
                //             $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                //             // Update Entity
                //             $update_account_id_arr = array(
                //                 'account_id' => $rowShipper["id"],
                //             );
                //             $entity->update($update_account_id_arr);
                //         }
                //     }
                // }

                if (post_var('save_location1') == 1) {
                    $terminal = new Account($this->daffny->DB);
                    $originArr = array(
                        'owner_id' => getParentId(),
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $sql_arr['origin_company_name'],
                        'contact_name1' => $sql_arr['origin_contact_name'],
                        'contact_name2' => $sql_arr['origin_contact_name2'],
                        'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                        'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                        'phone1_ext' => $sql_arr['origin_phone1_ext'],
                        'phone2_ext' => $sql_arr['origin_phone2_ext'],
                        'cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                        'address1' => $sql_arr['origin_address1'],
                        'address2' => $sql_arr['origin_address2'],
                        'city' => $sql_arr['origin_city'],
                        'state' => $sql_arr['origin_state'],
                        'state_other' => $sql_arr['origin_state'],
                        'zip_code' => $sql_arr['origin_zip'],
                        'country' => $sql_arr['origin_country'],
                        'location_type' => $sql_arr['origin_type'],
                        'hours_of_operation' => $sql_arr['origin_hours'],
                    );

                    if ($sql_arr['origin_id'] != 0) {
                        $this->updateAccountHistory($originArr, $sql_arr['origin_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['origin_id'] . "' ");
                    } elseif ($sql_arr['origin_company_name'] != "" || $sql_arr['origin_contact_name'] != '') {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_contact_name']) . "'  AND
						`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_company_name']) . "'  AND state='" . $sql_arr['origin_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_city']) . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $terminal->create($originArr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
                                $this->updateAccountHistory($originArr, $rowOriginLocation["id"]);
                                $upd_origin_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                                $this->daffny->DB->update("app_accounts", $upd_origin_arr, "id = '" . $rowOriginLocation["id"] . "' ");
                            }
                        }
                    }
                }

                if (post_var('save_location2') == 1) {
                    $terminal = new Account($this->daffny->DB);
                    $destinationArr = array(
                        'owner_id' => getParentId(),
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $sql_arr['destination_company_name'],
                        'contact_name1' => $sql_arr['destination_contact_name'],
                        'contact_name2' => $sql_arr['destination_contact_name2'],
                        'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                        'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                        'phone1_ext' => $sql_arr['destination_phone1_ext'],
                        'phone2_ext' => $sql_arr['destination_phone2_ext'],
                        'cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                        'address1' => $sql_arr['destination_address1'],
                        'address2' => $sql_arr['destination_address2'],
                        'city' => $sql_arr['destination_city'],
                        'state' => $sql_arr['destination_state'],
                        'state_other' => $sql_arr['destination_state'],
                        'zip_code' => $sql_arr['destination_zip'],
                        'country' => $sql_arr['destination_country'],
                        'location_type' => $sql_arr['destination_type'],
                        'hours_of_operation' => $sql_arr['destination_hours'],
                    );

                    if ($sql_arr['destination_id'] != 0) {
                        $this->updateAccountHistory($destinationArr, $sql_arr['destination_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['destination_id'] . "' ");
                    } elseif ($sql_arr['destination_company_name'] != "" || $sql_arr['destination_contact_name'] != '') {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_contact_name']) . "'  AND `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_company_name']) . "' AND state='" . $sql_arr['destination_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_city']) . "' AND `is_location` = 1");
                        if (empty($rowDestLocation)) {
                            $terminal->create($destinationArr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
                                $this->updateAccountHistory($destinationArr, $rowDestLocation["id"]);
                                $upd_destination_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                                $this->daffny->DB->update("app_accounts", $upd_destination_arr, "id = '" . $rowDestLocation["id"] . "' ");
                            }
                        }
                    }

                }

                if (isset($_POST['e_cc_number']) && isset($_POST['e_cc_cvv2']) && isset($_POST['e_cc_type'])) {
                    $card_arr = array(
                        'entity_id' => $entity->id,
                        'fname' => $_POST['e_cc_fname'],
                        'lname' => $_POST['e_cc_lname'],
                        'address' => $_POST['e_cc_address'],
                        'city' => $_POST['e_cc_city'],
                        'state' => $_POST['e_cc_state'],
                        'zip' => $_POST['e_cc_zip'],
                        'number' => $_POST['e_cc_number'],
                        'cvv2' => $_POST['e_cc_cvv2'],
                        'type' => $_POST['e_cc_type'],
                        'month' => $_POST['e_cc_month'],
                        'year' => $_POST['e_cc_year'],
                    );

                    $cc = $entity->getCreditCard(true);
                    unset($card_arr['entity_id']);
                    $cc->update($card_arr);
                }

                // B2B Commercial
                $accountShipper = $entity->getAccount();
                if ($accountShipper->esigned == 2 && $sql_arr['shipper_type'] == "Commercial") {
                    $files = $entity->getCommercialFilesShipper($accountShipper->id);
                    if (isset($files) && count($files)) {
                        foreach ($files as $file) {
                            $pos = strpos($file['name_original'], "B2B");
                            if ($pos === false) {

                            } else {
                                $fname = md5(mt_rand() . " " . time() . " " . $entity->id);
                                $path = ROOT_PATH . "uploads/entity/" . $fname;
                                $uploadpath = ROOT_PATH . "uploads/accounts/" . $file['name_on_server'];
                                if (copy($uploadpath, $path)) {
                                    $ins_arr = array(
                                        'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                        'name_on_server' => $fname,
                                        'size' => filesize($uploadpath),
                                        'type' => "pdf",
                                        'date_uploaded' => date("Y-m-d H:i:s"),
                                        'owner_id' => $entity->getAssigned()->parent_id,
                                        'status' => 0,
                                        'esigned' => 2,
                                    );

                                    $this->daffny->DB->insert("app_uploads", $ins_arr);
                                    $ins_id = $this->daffny->DB->get_insert_id();
                                    $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $entity->id, "upload_id" => $ins_id));
                                    // Update Entity
                                    $update_arr = array(
                                        'esigned' => 2,
                                    );
                                    $entity->update($update_arr);
                                }
                            }
                        }
                    }
                }
                // send email
                if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
                    try {
                        if ($sql_arr['shipper_type'] == "Commercial" && $accountShipper->esigned != 2 && $accountShipper->id != 0) {
                            $entity->sendCommercialOrderConfirmation();
                        } else {
                            $entity->sendOrderConfirmation();
                        }
                    } catch (Exception $e) {
                        $this->setFlashError("Failed to send Email");
                    }
                }
                
                $this->daffny->DB->query("CALL Update_Account_Status_by_EntityID('" . $entity->id . "')");
                $entity->make_payment();
                $entity->updateHeaderTable();

                // Commit transaction
                $this->daffny->DB->transaction('commit');
                $this->setFlashInfo("Order has been successfully saved");

                if ($sql_arr['match_carrier'] == 1) {
                    $entity->getVehicles(true);
                    $entity->update(array("vehicle_update" => 0));

                    ini_set('max_execution_time', 120);
                    try {
                        $this->daffny->DB->query("INSERT INTO app_rematch_carrier_trigger (member_id, entity_id) VALUES('" . $_SESSION['member_id'] . "', '" . $entity->id . "')");
                    } catch (Exception $e) {
                        echo "<pre>";
                        print_r($e);
                        die("<br>Unable to Match Carrier");
                    }
                }
                redirect(getLink('orders/show/id', $entity->id));

            } else {
                if (isset($_POST['submit']) && isset($_POST['tariff'])) {
                    $total_tariff = 0;
                    $total_deposit = 0;
                    foreach ($_POST['tariff'] as $k => $tariff) {
                        $total_tariff += $tariff;
                        $total_deposit += $_POST['deposit'][$k];
                    }
                    $this->input['total_tariff'] = '$ ' . number_format($total_tariff, 2);
                    $this->input['total_deposit'] = '$ ' . number_format($total_deposit, 2);
                    $carrier_pay = $total_tariff - $total_deposit + (float) $_POST['pickup_terminal_fee'] + (float) $_POST['delivery_terminal_fee'];
                    $this->input['carrier_pay'] = '$ ' . number_format($carrier_pay, 2);
                    $this->input['include_shipper_comment'] = (isset($_POST['include_shipper_comment'])) ? 1 : 0;
                } else {
                    $this->input['total_tariff'] = "$ 0.00";
                    $this->input['total_deposit'] = "$ 0.00";
                    $this->input['carrier_pay'] = "$ 0.00";
                    $this->input['include_shipper_comment'] = 0;
                }
            }

            if (trim($_POST['payments_terms']) == "") {
                $this->input['payments_terms'] = '';
            } else {
                $this->input['payments_terms'] = $_POST['payments_terms'];
            }

            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($referrer_status);
        } catch (FDException $e) {
            $this->daffny->DB->transaction('rollback');
            if (isset($_SESSION['queryError']) && $_SESSION['queryError'] != "") {
                $applog = new Applog($this->daffny->DB);
                $applog->createException($_SESSION['queryError']);
                $_SESSION['queryError'] = "";
            }
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);

            print_r($e->getMessage());
            die("<br>Execution stopped");
            //redirect('orders');
        } catch (Exception $e) {
            $this->daffny->DB->transaction('rollback');
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());

            print_r($e->getMessage());
            die("<br>Execution stopped");
            //redirect($e->getRedirectUrl());
        }
    }

    public function create_wizard()
    {
        try {
            $this->tplname = "orders.create-wizard";
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Create'));
            $this->title = "Create new Order";
            $this->daffny->tpl->create = true;
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
            $this->daffny->tpl->referrers_arr = $referrers_arr;

            $referrer_status = 0;
            $payments_terms = "";
            $row = $this->daffny->DB->select_one("referrer_status,payments_terms", "app_defaultsettings", "WHERE  owner_id = '" . getParentId() . "'");
            if (!empty($row)) {
                $referrer_status = $row['referrer_status'];
                $payments_terms = $row['payments_terms'];
            }

            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm(true, $referrer_status)) {

                // create log
                $info = "Create Order";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();
                $distance = RouteHelper::getRouteDistance($sql_arr['origin_city'] . "," . $sql_arr['origin_state'] . "," . $sql_arr['origin_country'], $sql_arr['destination_city'] . "," . $sql_arr['destination_state'] . "," . $sql_arr['destination_country']);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                $referrer_name_value = "";
                $salesrep = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];
                        $salesrep = $row_referrer['salesrep'];
                    }
                }

                // Create Entity
                $entity = new Entity($this->daffny->DB);
                $insert_arr = array(
                    'type' => Entity::TYPE_ORDER,
                    'creator_id' => $_SESSION['member']['id'],
                    'assigned_id' => $_SESSION['member']['id'],
                    'parentid' => getParentId(),
                    'salesrepid' => $salesrep,
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'avail_pickup_date' => empty($sql_arr['avail_pickup_date']) ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date'])),
                    'ship_via' => $sql_arr['shipping_ship_via'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['notes_from_shipper'],
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'ordered' => date('Y-m-d H:i:s'),
                    'payments_terms' => $sql_arr['payments_terms'],
                    'account_payble_contact' => $sql_arr['account_payble_contact'],
                    'auto_payment' => empty($sql_arr['auto_payment']) ? 0 : $sql_arr['auto_payment'],
                    'match_carrier' => $sql_arr['match_carrier'],
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );
                $entity->create($insert_arr);
                // Create Shipper
                $shipper = new Shipper($this->daffny->DB);
                $insert_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                    'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
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
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['origin_company'],
                    'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['origin_phone4']),
                    'phone1_ext' => $sql_arr['origin_phone1_ext'],
                    'phone2_ext' => $sql_arr['origin_phone2_ext'],
                    'phone3_ext' => $sql_arr['origin_phone3_ext'],
                    'phone4_ext' => $sql_arr['origin_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['origin_mobile2']),
                    'company' => $sql_arr['origin_company_name'],
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'fax2' => $sql_arr['origin_fax2'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
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
                    'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['destination_phone4']),
                    'phone1_ext' => $sql_arr['destination_phone1_ext'],
                    'phone2_ext' => $sql_arr['destination_phone2_ext'],
                    'phone3_ext' => $sql_arr['destination_phone3_ext'],
                    'phone4_ext' => $sql_arr['destination_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['destination_mobile2']),
                    'company' => $sql_arr['destination_company_name'],
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'fax2' => $sql_arr['destination_fax2'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                );
                $destination->create($insert_arr, $entity->id);
                // Create Notes
                if (trim($sql_arr['notes_from_shipper']) != "") {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }
                // Create Internal Notes
                if (trim($sql_arr['note_to_shipper']) != "") {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));
                }
                // custom notes functionality
                if ($_POST['balance_paid_by'] == 24) {
                    $amount = 0;
                    $carrier_fee = 0;
                    foreach ($_POST['year'] as $key => $val) {
                        $carrier_fee = $carrier_fee + ($_POST['tariff'][$key] - $_POST['deposit'][$key]);
                    }

                    // ACH payment fees type options
                    if ($_POST['fee_type'] == 1) {
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with no fee';
                    } elseif ($_POST['fee_type'] == 2) {
                        $amount = ((int) $carrier_fee * 0.03) + 12;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 3) {
                        $amount = ((int) $carrier_fee * 0.05) + 12;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 4) {
                        $amount = ((int) $carrier_fee * 0.03) + 0;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } elseif ($_POST['fee_type'] == 5) {
                        $amount = ((int) $carrier_fee * 0.05) + 0;
                        $amount = number_format($amount, 2);
                        $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                    } else {
                        $customNote = 'Invalid Fee Type Selected';
                    }

                    $note_array = array(
                        "entity_id" => $entity->id,
                        "sender_id" => $_SESSION['member_id'],
                        "type" => 3,
                        "text" => $customNote,
                    );
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);
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
                        'lot' => $_POST['lot'][$key],
                        'vin' => $_POST['vin'][$key],
                        'plate' => $_POST['plate'][$key],
                        'state' => $_POST['state'][$key],
                        'color' => $_POST['color'][$key],
                        'inop' => $_POST['inop'][$key],
                        'tariff' => $_POST['tariff'][$key],
                        'deposit' => $_POST['deposit'][$key],
                        'carrier_pay' => $_POST['tariff'][$key] - $_POST['deposit'][$key],
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

                if (post_var('save_shipper') == 1 || post_var('update_shipper') == 1) {

                    $shipper = new Account($this->daffny->DB);
                    $shipperArr = array(
                        'owner_id' => getParentId(),
                        'company_name' => $sql_arr['shipper_company'],
                        'status' => Account::STATUS_ACTIVE,
                        'is_carrier' => 0,
                        'is_shipper' => 1,
                        'is_location' => 0,
                        'first_name' => $sql_arr['shipper_fname'],
                        'last_name' => $sql_arr['shipper_lname'],
                        'email' => $sql_arr['shipper_email'],
                        'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                        'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                        'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                        'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                        'cell' => str_replace("-", "", $sql_arr['shipper_mobile']),
                        'fax' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fax']),
                        'address1' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_address1']),
                        'address2' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_address2']),
                        'city' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']),
                        'state' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_state']),
                        'state_other' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_state']),
                        'zip_code' => $sql_arr['shipper_zip'],
                        'country' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_country']),
                        
                        'payable_first_name' => $sql_arr['payable_first_name'],
                        'payable_last_name' => $sql_arr['payable_last_name'],
                        'payable_email' => $sql_arr['payable_email'],
                        'payable_phone' => $sql_arr['payable_phone'],
                        'payable_phone_ext' => $sql_arr['payable_phone_ext'],
                        
                        'shipper_type' => $sql_arr['shipper_type'],
                        'hours_of_operation' => mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_hours']),
                        'referred_by' => $referrer_name_value,
                        'referred_id' => $sql_arr['referred_by'],
                        'account_payble_contact' => $sql_arr['account_payble_contact'],
                    );

                    if ($sql_arr['shipper_company']) {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_company']) . "' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                    } else {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");
                    }

                    if (empty($rowShipper) && post_var('save_shipper') == 1) {
                        $shipper->create($shipperArr);

                        // Update Entity
                        $update_account_id_arr = array(
                            'account_id' => $shipper->id,
                        );
                        $entity->update($update_account_id_arr);
                        
                    } elseif (post_var('update_shipper') == 1) {
                        if ($rowShipper["id"] != '' && $sql_arr['shipper_company'] != "") {

                            $shipper->load($rowShipper["id"]);
                            unset($shipperArr['referred_by']);
                            unset($shipperArr['referred_id']);
                            $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $shipperArr);
                            $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                            // Update Entity
                            $update_account_id_arr = array(
                                'account_id' => $rowShipper["id"],
                            );
                            $entity->update($update_account_id_arr);
                        }
                    }
                }

                if (post_var('save_location1') == 1) {
                    $terminal = new Account($this->daffny->DB);
                    $originArr = array(
                        'owner_id' => getParentId(),
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $sql_arr['origin_company_name'],
                        'contact_name1' => $sql_arr['origin_contact_name'],
                        'contact_name2' => $sql_arr['origin_contact_name2'],
                        'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                        'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                        'phone1_ext' => $sql_arr['origin_phone1_ext'],
                        'phone2_ext' => $sql_arr['origin_phone2_ext'],
                        'cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                        'address1' => $sql_arr['origin_address1'],
                        'address2' => $sql_arr['origin_address2'],
                        'city' => $sql_arr['origin_city'],
                        'state' => $sql_arr['origin_state'],
                        'state_other' => $sql_arr['origin_state'],
                        'zip_code' => $sql_arr['origin_zip'],
                        'country' => $sql_arr['origin_country'],
                        'location_type' => $sql_arr['origin_type'],
                        'hours_of_operation' => $sql_arr['origin_hours'],
                    );

                    if ($sql_arr['origin_id'] != 0) {
                        $this->updateAccountHistory($originArr, $sql_arr['origin_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['origin_id'] . "' ");
                    } elseif ($sql_arr['origin_company_name'] != "" || $sql_arr['origin_contact_name'] != '') {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_contact_name']) . "'  AND
						`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_company_name']) . "'  AND state='" . $sql_arr['origin_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_city']) . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $terminal->create($originArr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
                                $this->updateAccountHistory($originArr, $rowOriginLocation["id"]);
                                $upd_origin_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                                $this->daffny->DB->update("app_accounts", $upd_origin_arr, "id = '" . $rowOriginLocation["id"] . "' ");
                            }
                        }
                    }
                }

                if (post_var('save_location2') == 1) {
                    $terminal = new Account($this->daffny->DB);
                    $destinationArr = array(
                        'owner_id' => getParentId(),
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $sql_arr['destination_company_name'],
                        'contact_name1' => $sql_arr['destination_contact_name'],
                        'contact_name2' => $sql_arr['destination_contact_name2'],
                        'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                        'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                        'phone1_ext' => $sql_arr['destination_phone1_ext'],
                        'phone2_ext' => $sql_arr['destination_phone2_ext'],
                        'cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                        'address1' => $sql_arr['destination_address1'],
                        'address2' => $sql_arr['destination_address2'],
                        'city' => $sql_arr['destination_city'],
                        'state' => $sql_arr['destination_state'],
                        'state_other' => $sql_arr['destination_state'],
                        'zip_code' => $sql_arr['destination_zip'],
                        'country' => $sql_arr['destination_country'],
                        'location_type' => $sql_arr['destination_type'],
                        'hours_of_operation' => $sql_arr['destination_hours'],
                    );

                    if ($sql_arr['destination_id'] != 0) {
                        $this->updateAccountHistory($destinationArr, $sql_arr['destination_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['destination_id'] . "' ");
                    } elseif ($sql_arr['destination_company_name'] != "" || $sql_arr['destination_contact_name'] != '') {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_contact_name']) . "'  AND `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_company_name']) . "' AND state='" . $sql_arr['destination_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_city']) . "' AND `is_location` = 1");
                        if (empty($rowDestLocation)) {
                            $terminal->create($destinationArr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
                                $this->updateAccountHistory($destinationArr, $rowDestLocation["id"]);
                                $upd_destination_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                                $this->daffny->DB->update("app_accounts", $upd_destination_arr, "id = '" . $rowDestLocation["id"] . "' ");
                            }
                        }
                    }

                }

                if (isset($_POST['e_cc_number']) && isset($_POST['e_cc_cvv2']) && isset($_POST['e_cc_type'])) {
                    $card_arr = array(
                        'entity_id' => $entity->id,
                        'fname' => $_POST['e_cc_fname'],
                        'lname' => $_POST['e_cc_lname'],
                        'address' => $_POST['e_cc_address'],
                        'city' => $_POST['e_cc_city'],
                        'state' => $_POST['e_cc_state'],
                        'zip' => $_POST['e_cc_zip'],
                        'number' => $_POST['e_cc_number'],
                        'cvv2' => $_POST['e_cc_cvv2'],
                        'type' => $_POST['e_cc_type'],
                        'month' => $_POST['e_cc_month'],
                        'year' => $_POST['e_cc_year'],
                    );

                    $cc = $entity->getCreditCard(true);
                    unset($card_arr['entity_id']);
                    $cc->update($card_arr);
                }

                // B2B Commercial
                $accountShipper = $entity->getAccount();
                if ($accountShipper->esigned == 2 && $sql_arr['shipper_type'] == "Commercial") {
                    $files = $entity->getCommercialFilesShipper($accountShipper->id);
                    if (isset($files) && count($files)) {
                        foreach ($files as $file) {
                            $pos = strpos($file['name_original'], "B2B");
                            if ($pos === false) {

                            } else {
                                $fname = md5(mt_rand() . " " . time() . " " . $entity->id);
                                $path = ROOT_PATH . "uploads/entity/" . $fname;
                                $uploadpath = ROOT_PATH . "uploads/accounts/" . $file['name_on_server'];
                                if (copy($uploadpath, $path)) {
                                    $ins_arr = array(
                                        'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                        'name_on_server' => $fname,
                                        'size' => filesize($uploadpath),
                                        'type' => "pdf",
                                        'date_uploaded' => date("Y-m-d H:i:s"),
                                        'owner_id' => $entity->getAssigned()->parent_id,
                                        'status' => 0,
                                        'esigned' => 2,
                                    );

                                    $this->daffny->DB->insert("app_uploads", $ins_arr);
                                    $ins_id = $this->daffny->DB->get_insert_id();
                                    $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $entity->id, "upload_id" => $ins_id));
                                    // Update Entity
                                    $update_arr = array(
                                        'esigned' => 2,
                                    );
                                    $entity->update($update_arr);
                                }
                            }
                        }
                    }
                }
                // send email
                if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
                    try {
                        if ($sql_arr['shipper_type'] == "Commercial" && $accountShipper->esigned != 2 && $accountShipper->id != 0) {
                            $entity->sendCommercialOrderConfirmation();
                        } else {
                            $entity->sendOrderConfirmation();
                        }
                    } catch (Exception $e) {
                        $this->setFlashError("Failed to send Email");
                    }
                }
                // Commit transaction
                $this->daffny->DB->transaction('commit');
                $this->setFlashInfo("Order has been successfully saved");
                $this->daffny->DB->query("CALL Update_Account_Status_by_EntityID('" . $entity->id . "')");
                $entity->make_payment();
                $entity->updateHeaderTable();

                if ($sql_arr['match_carrier'] == 1) {
                    $entity->getVehicles(true);
                    $entity->update(array("vehicle_update" => 0));

                    ini_set('max_execution_time', 120);
                    try {
                        $this->daffny->DB->query("INSERT INTO app_rematch_carrier_trigger (member_id, entity_id) VALUES('" . $_SESSION['member_id'] . "', '" . $entity->id . "')");
                    } catch (Exception $e) {
                        echo "<pre>";
                        print_r($e);
                        die("<br>Unable to Match Carrier");
                    }
                }
                redirect(getLink('orders/show/id', $entity->id));

            } else {
                if (isset($_POST['submit']) && isset($_POST['tariff'])) {
                    $total_tariff = 0;
                    $total_deposit = 0;
                    foreach ($_POST['tariff'] as $k => $tariff) {
                        $total_tariff += $tariff;
                        $total_deposit += $_POST['deposit'][$k];
                    }
                    $this->input['total_tariff'] = '$ ' . number_format($total_tariff, 2);
                    $this->input['total_deposit'] = '$ ' . number_format($total_deposit, 2);
                    $carrier_pay = $total_tariff - $total_deposit + (float) $_POST['pickup_terminal_fee'] + (float) $_POST['delivery_terminal_fee'];
                    $this->input['carrier_pay'] = '$ ' . number_format($carrier_pay, 2);
                    $this->input['include_shipper_comment'] = (isset($_POST['include_shipper_comment'])) ? 1 : 0;
                } else {
                    $this->input['total_tariff'] = "$ 0.00";
                    $this->input['total_deposit'] = "$ 0.00";
                    $this->input['carrier_pay'] = "$ 0.00";
                    $this->input['include_shipper_comment'] = 0;
                }
            }

            if (trim($_POST['payments_terms']) == "") {
                $this->input['payments_terms'] = '';
            } else {
                $this->input['payments_terms'] = $_POST['payments_terms'];
            }

            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($referrer_status);
        } catch (FDException $e) {
            $this->daffny->DB->transaction('rollback');
            if (isset($_SESSION['queryError']) && $_SESSION['queryError'] != "") {
                $applog = new Applog($this->daffny->DB);
                $applog->createException($_SESSION['queryError']);
                $_SESSION['queryError'] = "";
            }
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);

            print_r($e->getMessage());
            die("<br>Execution stopped");
            //redirect('orders');
        } catch (Exception $e) {
            $this->daffny->DB->transaction('rollback');
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());

            print_r($e->getMessage());
            die("<br>Execution stopped");
            //redirect($e->getRedirectUrl());
        }
    }

    public function processOrder()
    {
        $shipperInfo = [];
        $_POST['step1'] = json_decode($_POST['step1']);
        foreach ($_POST['step1'] as $key => $value) {
            $shipperInfo[$value->name] = $value->value;
        }

        $originInfo = [];
        $_POST['step2'] = json_decode($_POST['step2']);
        foreach ($_POST['step2'] as $key => $value) {
            $originInfo[$value->name] = $value->value;
        }

        $destinationInfo = [];
        $_POST['step3'] = json_decode($_POST['step3']);
        foreach ($_POST['step3'] as $key => $value) {
            $destinationInfo[$value->name] = $value->value;
        }

        $shippingInfo = [];
        $_POST['step4'] = json_decode($_POST['step4']);
        foreach ($_POST['step4'] as $key => $value) {
            $shippingInfo[$value->name] = $value->value;
        }
        
        $vehicleInfo = json_decode($_POST['step5']);
        $vehicles = [];
        
        $index = 0;
        foreach ($vehicleInfo as $vehicle) {
            if($vehicle->name == "year[]"){
                $vehicles[$index]['year'] = $vehicle->value;
            }

            if($vehicle->name == "make[]"){
                $vehicles[$index]['make'] = $vehicle->value;
            }

            if($vehicle->name == "model[]"){
                $vehicles[$index]['model'] = $vehicle->value;
            }

            if($vehicle->name == "type[]"){
                $vehicles[$index]['type'] = $vehicle->value;
            }

            if($vehicle->name == "lot[]"){
                $vehicles[$index]['lot'] = $vehicle->value;
            }

            if($vehicle->name == "inop[]"){
                $vehicles[$index]['inop'] = $vehicle->value;
            }

            if($vehicle->name == "plate[]"){
                $vehicles[$index]['plate'] = $vehicle->value;
            }

            if($vehicle->name == "state[]"){
                $vehicles[$index]['state'] = $vehicle->value;
            }

            if($vehicle->name == "color[]"){
                $vehicles[$index]['color'] = $vehicle->value;
            }

            if($vehicle->name == "vin[]"){
                $vehicles[$index]['vin'] = $vehicle->value;
            }

            if($vehicle->name == "carrier_pay[]"){
                $vehicles[$index]['carrier_pay'] = $vehicle->value;
            }

            if($vehicle->name == "tariff[]"){
                $vehicles[$index]['tariff'] = $vehicle->value;
            }

            if($vehicle->name == "deposit[]"){
                $vehicles[$index]['deposit'] = $vehicle->value;
                $index++;
            }
        }

        $pricingPaymentInfo = [];
        $_POST['step6'] = json_decode($_POST['step6']);
        foreach ($_POST['step6'] as $key => $value) {
            $pricingPaymentInfo[$value->name] = $value->value;
        }

        $notesInfo = [];
        $_POST['step7'] = json_decode($_POST['step7']);
        foreach ($_POST['step7'] as $key => $value) {
            $notesInfo[$value->name] = $value->value;
        }

        $this->createOrder($shipperInfo, $originInfo, $destinationInfo, $shippingInfo, $vehicles, $pricingPaymentInfo, $notesInfo);
    }

    public function createOrder($shipperInfo, $originInfo, $destinationInfo, $shippingInfo, $vehicleInfo, $pricingPaymentInfo, $notesInfo)
    {
        try {

            $applog = new Applog($this->daffny->DB);
            $applog->createInformation("Create Order");

            $this->daffny->DB->transaction();

            $distance = RouteHelper::getRouteDistance($originInfo['origin_city'] . "," . $originInfo['origin_state'] . "," . $originInfo['origin_country'], $destinationInfo['destination_city'] . "," . $destinationInfo['destination_state'] . "," . $destinationInfo['destination_country']);
            if (!is_null($distance)) {
                $distance = RouteHelper::getMiles((float) $distance);
            } else {
                $distance = 'NULL';
            }

            $referrer_name_value = "";
            $salesrep = "";
            if ($shipperInfo['referred_by'] != "") {
                $row_referrer = $this->daffny->DB->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $shipperInfo['referred_by'] . "'");
                if (!empty($row_referrer)) {
                    $referrer_name_value = $row_referrer['name'];
                    $salesrep = $row_referrer['salesrep'];
                }
            }

            // Create Entity
            $entity = new Entity($this->daffny->DB);
            $insert_arr = array(
                'type' => Entity::TYPE_ORDER,
                'creator_id' => $_SESSION['member']['id'],
                'assigned_id' => $_SESSION['member']['id'],
                'parentid' => getParentId(),
                'salesrepid' => $salesrep,
                'buyer_number' => $originInfo['origin_buyer_number'],
                'booking_number' => $originInfo['origin_booking_number'],
                'avail_pickup_date' => empty($sql_arr['avail_pickup_date']) ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date'])),
                'ship_via' => $sql_arr['shipping_ship_via'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $sql_arr['referred_by'],
                'distance' => $distance,
                'notes_from_shipper' => $sql_arr['notes_from_shipper'],
                'information' => $sql_arr['notes_for_shipper'],
                'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                'balance_paid_by' => $sql_arr['balance_paid_by'],
                'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                'ordered' => date('Y-m-d H:i:s'),
                'payments_terms' => $sql_arr['payments_terms'],
                'account_payble_contact' => $sql_arr['account_payble_contact'],
                'auto_payment' => empty($sql_arr['auto_payment']) ? 0 : $sql_arr['auto_payment'],
                'match_carrier' => $sql_arr['match_carrier'],
                'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
            );
            $entity->create($insert_arr);

            // Create Shipper
            $shipper = new Shipper($this->daffny->DB);
            $insert_arr = array(
                'fname' => $shipperInfo['shipper_fname'],
                'lname' => $shipperInfo['shipper_lname'],
                'email' => $shipperInfo['shipper_email'],
                'company' => $shipperInfo['shipper_company'],
                'phone1' => str_replace("-", "", $shipperInfo['shipper_phone1']),
                'phone2' => str_replace("-", "", $shipperInfo['shipper_phone2']),
                'phone1_ext' => $shipperInfo['shipper_phone1_ext'],
                'phone2_ext' => $shipperInfo['shipper_phone2_ext'],
                'mobile' => str_replace("-", "", $shipperInfo['shipper_mobile']),
                'fax' => $shipperInfo['shipper_fax'],
                'address1' => $shipperInfo['shipper_address1'],
                'address2' => $shipperInfo['shipper_address2'],
                'city' => $shipperInfo['shipper_city'],
                'state' => $shipperInfo['shipper_state'],
                'zip' => $shipperInfo['shipper_zip'],
                'country' => $shipperInfo['shipper_country'],
                'shipper_type' => $shipperInfo['shipper_type'],
                'shipper_hours' => $shipperInfo['shipper_hours'],
            );
            $shipper->create($insert_arr, $entity->id);

            // Create Origin
            $origin = new Origin($this->daffny->DB);
            $insert_arr = array(
                'address1' => $originInfo['origin_address1'],
                'address2' => $originInfo['origin_address2'],
                'city' => $originInfo['origin_city'],
                'state' => $originInfo['origin_state'],
                'zip' => $originInfo['origin_zip'],
                'country' => $originInfo['origin_country'],
                'name' => $originInfo['origin_contact_name'],
                'auction_name' => $originInfo['origin_auction_name'],
                'company' => $originInfo['origin_company'],
                'phone1' => str_replace("-", "", $originInfo['origin_phone1']),
                'phone2' => str_replace("-", "", $originInfo['origin_phone2']),
                'phone3' => str_replace("-", "", $originInfo['origin_phone3']),
                'phone4' => str_replace("-", "", $originInfo['origin_phone4']),
                'phone1_ext' => $originInfo['origin_phone1_ext'],
                'phone2_ext' => $originInfo['origin_phone2_ext'],
                'phone3_ext' => $originInfo['origin_phone3_ext'],
                'phone4_ext' => $originInfo['origin_phone4_ext'],
                'phone_cell' => str_replace("-", "", $originInfo['origin_mobile']),
                'phone_cell2' => str_replace("-", "", $originInfo['origin_mobile2']),
                'company' => $originInfo['origin_company_name'],
                'name2' => $originInfo['origin_contact_name2'],
                'booking_number' => $originInfo['origin_booking_number'],
                'buyer_number' => $originInfo['origin_buyer_number'],
                'fax' => $originInfo['origin_fax'],
                'fax2' => $originInfo['origin_fax2'],
                'location_type' => $originInfo['origin_type'],
                'hours' => $originInfo['origin_hours'],
            );
            $origin->create($insert_arr, $entity->id);

            // Create Destination
            $destination = new Destination($this->daffny->DB);
            $insert_arr = array(
                'address1' => $destinationInfo['destination_address1'],
                'address2' => $destinationInfo['destination_address2'],
                'city' => $destinationInfo['destination_city'],
                'state' => $destinationInfo['destination_state'],
                'zip' => $destinationInfo['destination_zip'],
                'country' => $destinationInfo['destination_country'],
                'name' => $destinationInfo['destination_contact_name'],
                'company' => $destinationInfo['destination_company'],
                'phone1' => str_replace("-", "", $destinationInfo['destination_phone1']),
                'phone2' => str_replace("-", "", $destinationInfo['destination_phone2']),
                'phone3' => str_replace("-", "", $destinationInfo['destination_phone3']),
                'phone4' => str_replace("-", "", $destinationInfo['destination_phone4']),
                'phone1_ext' => $destinationInfo['destination_phone1_ext'],
                'phone2_ext' => $destinationInfo['destination_phone2_ext'],
                'phone3_ext' => $destinationInfo['destination_phone3_ext'],
                'phone4_ext' => $destinationInfo['destination_phone4_ext'],
                'phone_cell' => str_replace("-", "", $destinationInfo['destination_mobile']),
                'phone_cell2' => str_replace("-", "", $destinationInfo['destination_mobile2']),
                'company' => $destinationInfo['destination_company_name'],
                'name2' => $destinationInfo['destination_contact_name2'],
                'auction_name' => $destinationInfo['destination_auction_name'],
                'booking_number' => $destinationInfo['destination_booking_number'],
                'buyer_number' => $destinationInfo['destination_buyer_number'],
                'fax' => $destinationInfo['destination_fax'],
                'fax2' => $destinationInfo['destination_fax2'],
                'location_type' => $destinationInfo['destination_type'],
                'hours' => $destinationInfo['destination_hours'],
            );
            $destination->create($insert_arr, $entity->id);

            // Create Notes
            if (trim($shippingInfo['notes_from_shipper']) != "") {
                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $shippingInfo['notes_from_shipper'], 'type' => Note::TYPE_FROM));
            }

            // Create Internal Notes
            if (trim($notesInfo['note_to_shipper']) != "") {
                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $notesInfo['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));
            }

            // custom notes functionality
            if ($pricingPaymentInfo['balance_paid_by'] == 24) {
                $amount = 0;
                $carrier_fee = 0;
                foreach ($vehicleInfo as $key => $val) {
                    $carrier_fee = $carrier_fee + ($val['tariff'] - $val['deposit']);
                }

                // ACH payment fees type options
                if ($pricingPaymentInfo['fee_type'] == 1) {
                    $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with no fee';
                } elseif ($pricingPaymentInfo['fee_type'] == 2) {
                    $amount = ((int) $carrier_fee * 0.03) + 12;
                    $amount = number_format($amount, 2);
                    $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                } elseif ($pricingPaymentInfo['fee_type'] == 3) {
                    $amount = ((int) $carrier_fee * 0.05) + 12;
                    $amount = number_format($amount, 2);
                    $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                } elseif ($pricingPaymentInfo['fee_type'] == 4) {
                    $amount = ((int) $carrier_fee * 0.03) + 0;
                    $amount = number_format($amount, 2);
                    $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                } elseif ($pricingPaymentInfo['fee_type'] == 5) {
                    $amount = ((int) $carrier_fee * 0.05) + 0;
                    $amount = number_format($amount, 2);
                    $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                } else {
                    $customNote = 'Invalid Fee Type Selected';
                }

                $note_array = array(
                    "entity_id" => $entity->id,
                    "sender_id" => $_SESSION['member_id'],
                    "type" => 3,
                    "text" => $customNote,
                );
                $note = new Note($this->daffny->DB);
                $note->create($note_array);
            }

            // Create Vehicles
            foreach ($vehicleInfo as $key => $val) {
                $vehicle = new Vehicle($this->daffny->DB);
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'year' => $val['year'],
                    'make' => $val['make'],
                    'model' => $val['model'],
                    'type' => $val['type'],
                    'lot' => $val['lot'] == "undefined" ? "" : $val['lot'],
                    'vin' => $val['vin'] == "null" ? null : $val['vin'],
                    'plate' => $val['plate'],
                    'state' => $val['state'],
                    'color' => $val['color'],
                    'inop' => $val['inop'] == "null" ? null : $val['inop'],
                    'tariff' => $val['tariff'],
                    'deposit' => $val['deposit'],
                    'carrier_pay' => $val['tariff'] - $val['deposit'],
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


            if ($shipperInfo['save_shipper'] == 1 || $shipperInfo['update_shipper'] == 1) {

                $shipper = new Account($this->daffny->DB);
                $shipperArr = array(
                    'owner_id' => getParentId(),
                    'company_name' => $shipperInfo['shipper_company'],
                    'status' => Account::STATUS_ACTIVE,
                    'is_carrier' => 0,
                    'is_shipper' => 1,
                    'is_location' => 0,
                    'first_name' => $shipperInfo['shipper_fname'],
                    'last_name' => $shipperInfo['shipper_lname'],
                    'email' => $shipperInfo['shipper_email'],
                    'phone1' => str_replace("-", "", $shipperInfo['shipper_phone1']),
                    'phone2' => str_replace("-", "", $shipperInfo['shipper_phone2']),
                    'phone1_ext' => $shipperInfo['shipper_phone1_ext'],
                    'phone2_ext' => $shipperInfo['shipper_phone2_ext'],
                    'cell' => str_replace("-", "", $shipperInfo['shipper_mobile']),
                    'fax' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_fax']),
                    'address1' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_address1']),
                    'address2' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_address2']),
                    'city' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_city']),
                    'state' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_state']),
                    'state_other' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_state']),
                    'zip_code' => $shipperInfo['shipper_zip'],
                    'country' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_country']),

                    'payable_first_name' => $shipperInfo['payable_first_name'],
                    'payable_last_name' => $shipperInfo['payable_last_name'],
                    'payable_email' => $shipperInfo['payable_email'],
                    'payable_phone' => $shipperInfo['payable_phone'],
                    'payable_phone_ext' => $shipperInfo['payable_phone_ext'],

                    'shipper_type' => $shipperInfo['shipper_type'],
                    'hours_of_operation' => mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_hours']),
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $shipperInfo['referred_by'],
                    'account_payble_contact' => $shipperInfo['account_payble_contact'],
                );
        
                $rowShipper = null;
                if ($shipperInfo['shipper_company']) {
                    $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_company']) . "' AND state='" . $shipperInfo['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_lname']) . "' AND `is_shipper` = 1)");
                } else {
                    $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  (`company_name` ='' AND state='" . $shipperInfo['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $shipperInfo['shipper_lname']) . "' AND `is_shipper` = 1)");
                }
        
                if (empty($rowShipper) && $shipperInfo['save_shipper'] == 1) {
                    $shipper->create($shipperArr);
        
                    // Update Entity
                    $update_account_id_arr = array(
                        'account_id' => $shipper->id,
                    );
                    $entity->update($update_account_id_arr);
                    
                } elseif (post_var('update_shipper') == 1) {
                    if ($rowShipper["id"] != '' && $sql_arr['shipper_company'] != "") {
        
                        $shipper->load($rowShipper["id"]);
                        unset($shipperArr['referred_by']);
                        unset($shipperArr['referred_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $shipperArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");
        
                        // Update Entity
                        $update_account_id_arr = array(
                            'account_id' => $rowShipper["id"],
                        );
                        $entity->update($update_account_id_arr);
                    }
                } else {
                    if ($rowShipper["id"] != '') {
        
                        $shipper->load($rowShipper["id"]);
                        unset($shipperArr['referred_by']);
                        unset($shipperArr['referred_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $shipperArr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                        // Update Entity
                        $update_account_id_arr = array(
                            'account_id' => $rowShipper["id"],
                        );
                        $entity->update($update_account_id_arr);
                    }
                }
            }

            if ($originInfo['save_location1'] == 1) {
                $terminal = new Account($this->daffny->DB);
                $originArr = array(
                    'owner_id' => getParentId(),
                    'is_carrier' => 0,
                    'is_shipper' => 0,
                    'is_location' => 1,
                    'company_name' => $originInfo['origin_company_name'],
                    'contact_name1' => $originInfo['origin_contact_name'],
                    'contact_name2' => $originInfo['origin_contact_name2'],
                    'phone1' => str_replace("-", "", $originInfo['origin_phone1']),
                    'phone2' => str_replace("-", "", $originInfo['origin_phone2']),
                    'phone1_ext' => $originInfo['origin_phone1_ext'],
                    'phone2_ext' => $originInfo['origin_phone2_ext'],
                    'cell' => str_replace("-", "", $originInfo['origin_mobile']),
                    'address1' => $originInfo['origin_address1'],
                    'address2' => $originInfo['origin_address2'],
                    'city' => $originInfo['origin_city'],
                    'state' => $originInfo['origin_state'],
                    'state_other' => $originInfo['origin_state'],
                    'zip_code' => $originInfo['origin_zip'],
                    'country' => $originInfo['origin_country'],
                    'location_type' => $originInfo['origin_type'],
                    'hours_of_operation' => $originInfo['origin_hours'],
                );
        
                if ($originInfo['origin_id'] != 0) {
                    $this->updateAccountHistory($originArr, $originInfo['origin_id']);
                    $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                    $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $originInfo['origin_id'] . "' ");
                } elseif ($originInfo['origin_company_name'] != "" || $originInfo['origin_contact_name'] != '') {
                    $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $originInfo['origin_contact_name']) . "'  AND
                    `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $originInfo['origin_company_name']) . "'  AND state='" . $originInfo['origin_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $originInfo['origin_city']) . "' AND `is_location` = 1");
        
                    if (empty($rowOriginLocation)) {
                        $terminal->create($originArr);
                    } else {
                        if ($rowOriginLocation["id"] != '') {
                            $this->updateAccountHistory($originArr, $rowOriginLocation["id"]);
                            $upd_origin_arr = $this->daffny->DB->PrepareSql("app_accounts", $originArr);
                            $this->daffny->DB->update("app_accounts", $upd_origin_arr, "id = '" . $rowOriginLocation["id"] . "' ");
                        }
                    }
                }
            }

            if ($destinationInfo['save_location2'] == 1) {
                $terminal = new Account($this->daffny->DB);
                $destinationArr = array(
                    'owner_id' => getParentId(),
                    'is_carrier' => 0,
                    'is_shipper' => 0,
                    'is_location' => 1,
                    'company_name' => $destinationInfo['destination_company_name'],
                    'contact_name1' => $destinationInfo['destination_contact_name'],
                    'contact_name2' => $destinationInfo['destination_contact_name2'],
                    'phone1' => str_replace("-", "", $destinationInfo['destination_phone1']),
                    'phone2' => str_replace("-", "", $destinationInfo['destination_phone2']),
                    'phone1_ext' => $destinationInfo['destination_phone1_ext'],
                    'phone2_ext' => $destinationInfo['destination_phone2_ext'],
                    'cell' => str_replace("-", "", $destinationInfo['destination_mobile']),
                    'address1' => $destinationInfo['destination_address1'],
                    'address2' => $destinationInfo['destination_address2'],
                    'city' => $destinationInfo['destination_city'],
                    'state' => $destinationInfo['destination_state'],
                    'state_other' => $destinationInfo['destination_state'],
                    'zip_code' => $destinationInfo['destination_zip'],
                    'country' => $destinationInfo['destination_country'],
                    'location_type' => $destinationInfo['destination_type'],
                    'hours_of_operation' => $destinationInfo['destination_hours'],
                );
        
                if ($destinationInfo['destination_id'] != 0) {
                    $this->updateAccountHistory($destinationArr, $destinationInfo['destination_id']);
                    $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                    $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $destinationInfo['destination_id'] . "' ");
                } elseif ($destinationInfo['destination_company_name'] != "" || $destinationInfo['destination_contact_name'] != '') {
                    $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $destinationInfo['destination_contact_name']) . "'  AND `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $destinationInfo['destination_company_name']) . "' AND state='" . $destinationInfo['destination_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $destinationInfo['destination_city']) . "' AND `is_location` = 1");
                    if (empty($rowDestLocation)) {
                        $terminal->create($destinationArr);
                    } else {
                        if ($rowDestLocation["id"] != '') {
                            $this->updateAccountHistory($destinationArr, $rowDestLocation["id"]);
                            $upd_destination_arr = $this->daffny->DB->PrepareSql("app_accounts", $destinationArr);
                            $this->daffny->DB->update("app_accounts", $upd_destination_arr, "id = '" . $rowDestLocation["id"] . "' ");
                        }
                    }
                }
        
            }

            if (isset($pricingPaymentInfo['e_cc_number']) && isset($pricingPaymentInfo['e_cc_cvv2']) && isset($pricingPaymentInfo['e_cc_type'])) {
                $card_arr = array(
                    'entity_id' => $entity->id,
                    'fname' => $pricingPaymentInfo['e_cc_fname'],
                    'lname' => $pricingPaymentInfo['e_cc_lname'],
                    'address' => $pricingPaymentInfo['e_cc_address'],
                    'city' => $pricingPaymentInfo['e_cc_city'],
                    'state' => $pricingPaymentInfo['e_cc_state'],
                    'zip' => $pricingPaymentInfo['e_cc_zip'],
                    'number' => $pricingPaymentInfo['e_cc_number'],
                    'cvv2' => $pricingPaymentInfo['e_cc_cvv2'],
                    'type' => $pricingPaymentInfo['e_cc_type'],
                    'month' => $pricingPaymentInfo['e_cc_month'],
                    'year' => $pricingPaymentInfo['e_cc_year'],
                );
        
                $cc = $entity->getCreditCard(true);
                unset($card_arr['entity_id']);
                $cc->update($card_arr);
            }

            // B2B Commercial
            $accountShipper = $entity->getAccount();
            if ($accountShipper->esigned == 2 && $shipperInfo['shipper_type'] == "Commercial") {
                $files = $entity->getCommercialFilesShipper($accountShipper->id);
                if (isset($files) && count($files)) {
                    foreach ($files as $file) {
                        $pos = strpos($file['name_original'], "B2B");
                        if ($pos === false) {

                        } else {
                            $fname = md5(mt_rand() . " " . time() . " " . $entity->id);
                            $path = ROOT_PATH . "uploads/entity/" . $fname;
                            $uploadpath = ROOT_PATH . "uploads/accounts/" . $file['name_on_server'];
                            if (copy($uploadpath, $path)) {
                                $ins_arr = array(
                                    'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                    'name_on_server' => $fname,
                                    'size' => filesize($uploadpath),
                                    'type' => "pdf",
                                    'date_uploaded' => date("Y-m-d H:i:s"),
                                    'owner_id' => $entity->getAssigned()->parent_id,
                                    'status' => 0,
                                    'esigned' => 2,
                                );

                                $this->daffny->DB->insert("app_uploads", $ins_arr);
                                $ins_id = $this->daffny->DB->get_insert_id();
                                $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $entity->id, "upload_id" => $ins_id));
                                // Update Entity
                                $update_arr = array(
                                    'esigned' => 2,
                                );
                                $entity->update($update_arr);
                            }
                        }
                    }
                }
            }

            // send email
            if (isset($notesInfo['send_email']) && $notesInfo['send_email'] == '1') {
                try {
                    if ($shipperInfo['shipper_type'] == "Commercial" && $accountShipper->esigned != 2 && $accountShipper->id != 0) {
                        $entity->sendCommercialOrderConfirmation();
                    } else {
                        $entity->sendOrderConfirmation();
                    }
                } catch (Exception $e) {
                    $this->setFlashError("Failed to send Email");
                }
            }

            $this->daffny->DB->query("CALL Update_Account_Status_by_EntityID('" . $entity->id . "')");
            $entity->make_payment();
            $entity->updateHeaderTable();

            // Commit transaction
            $this->daffny->DB->transaction('commit');

            if ($notesInfo['match_carrier'] == 1) {
                $entity->getVehicles(true);
                $entity->update(array("vehicle_update" => 0));
        
                ini_set('max_execution_time', 120);
                try {
                    $this->daffny->DB->query("INSERT INTO app_rematch_carrier_trigger (member_id, entity_id) VALUES('" . $_SESSION['member_id'] . "', '" . $entity->id . "')");
                } catch (Exception $e) {
                    echo "<pre>";
                    print_r($e);
                    die("<br>Unable to Match Carrier");
                }
            }

            echo json_encode(['success'=>true, 'message'=>'Order Created', 'id'=>$entity->id]);die;
        } catch (Exception $e) {
            $this->daffny->DB->transaction('rollback');
            echo json_encode(['success'=>false]);die;
        }
    }

    public function edit()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.edit";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            $sql = "SELECT blocked_by_carrier FROM app_entities WHERE id = " . $entity->id;
            $res = $this->daffny->DB->query($sql);
            $blocked_by_carrier = mysqli_fetch_assoc($res)['blocked_by_carrier'];

            if ($entity->isBlocked()) {
                $blockedBy = $entity->blockedByMember();
                $this->setFlashError($blockedBy . ' is editing this order at this moment, please try again later.');
                redirect(getLink('orders', 'show', 'id', $entity->id));
            }

            /**
             * 17042018 - Chetu Added patch to prevent user to modify dispatched
             * orders if no access assigned
             */
            $accessDispatchedLeads = $_SESSION['member']['access_dispatch_orders'];
            if (!checkDispatchOrderEditAccess($entity->status, $accessDispatchedLeads)) {
                $this->setFlashError("Access Denied!");
                redirect(getLink("orders", "show", "id", $entity->id));
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", getLink('orders/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Edit"));
            $this->title = "Edit Order #" . $entity->number;

            //Ask if need to post to CD?
            $ask_post_to_cd = false;
            $settings = $entity->getAssigned()->getDefaultSettings();
            if ($entity->status == Entity::STATUS_POSTED) {

                if ($settings->central_dispatch_uid != "" && $settings->central_dispatch_post == 1) {
                    $ask_post_to_cd = true;
                }
            }

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditForm(false, $settings->referrer_status, $entity->status)) {

                $startLogTime = date('d-m-Y h:m:s');

                $entityOldMatchCarrier = new Entity($this->daffny->DB);
                $entityOldMatchCarrier->load((int) $_GET['id']);
                $entityOldMatchCarrier->getVehicles();
                $entityOldMatchCarrier->getOrigin();
                $entityOldMatchCarrier->getDestination();

                /********** Log ********/
                $info = "Edit Order-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();

                $referrer_name_value = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }

                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                if ($sql_arr['shipper_country'] != "US") {
                    $sql_arr['shipper_state'] = $sql_arr['shipper_state2'];
                }
                $update_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                    'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
                );
                $shipper->update($update_arr);

                $account = new Account($this->daffny->DB);

                $account_arr = array(
                    'owner_id' => $_SESSION['member_id'],
                    'is_carrier' => 0,
                    'is_shipper' => 1,
                    'is_location' => 0,
                    'company_name' => $update_arr['company'],
                    'first_name' => $update_arr['fname'],
                    'last_name' => $update_arr['lname'],
                    'email' => $update_arr['email'],
                    'phone1' => str_replace("-", "", $update_arr['phone1']),
                    'phone2' => str_replace("-", "", $update_arr['phone2']),
                    'phone1_ext' => $update_arr['phone1_ext'],
                    'phone2_ext' => $update_arr['phone2_ext'],
                    'cell' => str_replace("-", "", $update_arr['mobile']),
                    'fax' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fax']),
                    'address1' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['address1']),
                    'address2' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['address2']),
                    'city' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']),
                    'state' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']),
                    'zip_code' => $update_arr['zip'],
                    'country' => $update_arr['country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'hours_of_operation' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['shipper_hours']),
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'account_payble_contact' => $sql_arr['account_payble_contact'],

                );

                if (isset($_POST['save_shipper']) && $_POST['save_shipper'] == 1) {

                    unset($account_arr['referred_by']);
                    unset($account_arr['referred_id']);
                    $account_id_update = $entity->account_id;
                    if ($_POST['edit_shipper_id'] > 0) {
                        $account_id_update = $_POST['edit_shipper_id'];
                    }

                    $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                    $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $account_id_update . "' ");
                    $account->load($account_id_update);

                } else {

                    if ($update_arr['company'] != "") {

                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "' AND state='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']) . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']) . "'  AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['lname']) . "' AND `is_shipper` = 1)");
                    } else {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='' AND state='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']) . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']) . "'  AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['lname']) . "' AND `is_shipper` = 1)");
                    }
                    if (empty($rowShipper)) {

                        $account->create($account_arr);
                        $update_account_id_arr = array(
                            'account_id' => $account->id,
                        );
                        $entity->update($update_account_id_arr);
                        
                    } else {

                        if ($rowShipper["id"] != '') {
                            unset($account_arr['referred_by']);
                            unset($account_arr['referred_id']);
                            $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                            $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                            $account->load($rowShipper["id"]);

                            $update_account_id_arr = array(
                                'account_id' => $account->id,
                            );
                            $entity->update($update_account_id_arr);

                        }
                    }
                }

                /* UPDATE ORIGIN */
                $origin = $entity->getOrigin();
                if ($sql_arr['origin_country'] != "US") {
                    $sql_arr['origin_state'] = $sql_arr['origin_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['origin_address1'],
                    'address2' => $sql_arr['origin_address2'],
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                    'name' => $sql_arr['origin_contact_name'],
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['origin_company_name'],
                    'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['origin_phone4']),
                    'phone1_ext' => $sql_arr['origin_phone1_ext'],
                    'phone2_ext' => $sql_arr['origin_phone2_ext'],
                    'phone3_ext' => $sql_arr['origin_phone3_ext'],
                    'phone4_ext' => $sql_arr['origin_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['origin_mobile2']),
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'fax2' => $sql_arr['origin_fax2'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
                );
                $origin->update($update_arr);

                if (isset($_POST['save_location1'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'contact_name1' => $sql_arr['origin_contact_name'],
                        'contact_name2' => $sql_arr['origin_contact_name2'],
                        'phone1' => str_replace("-", "", $update_arr['phone1']),
                        'phone2' => str_replace("-", "", $update_arr['phone2']),
                        'phone1_ext' => $update_arr['phone1_ext'],
                        'phone2_ext' => $update_arr['phone2_ext'],
                        'cell' => $update_arr['phone_cell'],
                        'address1' => $update_arr['address1'],
                        'address2' => $update_arr['address2'],
                        'city' => $update_arr['city'],
                        'state' => $update_arr['state'],
                        'zip_code' => $update_arr['zip'],
                        'country' => $update_arr['country'],
                        'location_type' => $sql_arr['origin_type'],
                        'hours_of_operation' => $sql_arr['origin_hours'],
                    );

                    if ($sql_arr['origin_id'] != 0) {
                        $this->updateAccountHistory($account_arr, $sql_arr['origin_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['origin_id'] . "' ");

                    } elseif ($update_arr['company'] != "" || $sql_arr['origin_contact_name'] != '') {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_contact_name']) . "'  AND
						`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "' AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $account->create($account_arr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
                                $this->updateAccountHistory($account_arr, $rowOriginLocation["id"]);
                                $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                                $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowOriginLocation["id"] . "' ");

                            }
                        }
                    }
                }
                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['destination_country'] != "US") {
                    $sql_arr['destination_state'] = $sql_arr['destination_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['destination_address1'],
                    'address2' => $sql_arr['destination_address2'],
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                    'name' => $sql_arr['destination_contact_name'],
                    'company' => $sql_arr['destination_company_name'],
                    'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['destination_phone4']),
                    'phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['destination_mobile2']),
                    'phone1_ext' => $sql_arr['destination_phone1_ext'],
                    'phone2_ext' => $sql_arr['destination_phone2_ext'],
                    'phone3_ext' => $sql_arr['destination_phone3_ext'],
                    'phone4_ext' => $sql_arr['destination_phone4_ext'],
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'fax2' => $sql_arr['destination_fax2'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                );

                $destination->update($update_arr);

                if (isset($_POST['save_location2'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'contact_name1' => $sql_arr['destination_contact_name'],
                        'contact_name2' => $sql_arr['destination_contact_name2'],
                        'phone1' => str_replace("-", "", $update_arr['phone1']),
                        'phone2' => str_replace("-", "", $update_arr['phone2']),
                        'phone1_ext' => $update_arr['phone1_ext'],
                        'phone2_ext' => $update_arr['phone2_ext'],
                        'cell' => $update_arr['phone_cell'],
                        'address1' => $update_arr['address1'],
                        'address2' => $update_arr['address2'],
                        'city' => $update_arr['city'],
                        'state' => $update_arr['state'],
                        'zip_code' => $update_arr['zip'],
                        'country' => $update_arr['country'],
                        'location_type' => $sql_arr['destination_type'],
                        'hours_of_operation' => $sql_arr['destination_hours'],
                    );

                    if ($sql_arr['destination_id'] != 0) {

                        $this->updateAccountHistory($account_arr, $sql_arr['destination_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['destination_id'] . "' ");

                    } elseif ($update_arr['company'] != "" || $sql_arr['destination_contact_name'] != '') {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_contact_name']) . "'  AND  `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "'  AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowDestLocation)) {
                            if ($_GET['id'] == 126205) {
                                print_r($account_arr);
                            }

                            $account->create($account_arr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
                                $this->updateAccountHistory($account_arr, $rowDestLocation["id"]);
                                $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                                $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowDestLocation["id"] . "' ");
                            }
                        }
                    }
                }

                /* UPDATE NOTE */
                $notes = $entity->getNotes();
                if (count($notes[Note::TYPE_FROM]) != 0) {
                    $note = $notes[Note::TYPE_FROM][0];
                    $note->update(array('text' => $sql_arr['notes_from_shipper']));
                } else {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                $update_arr = array(
                    'ship_via' => (int) $sql_arr['shipping_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                    'load_date_type' => (int) $sql_arr['load_date_type'],
                    'delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['notes_from_shipper'],
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'payments_terms' => $sql_arr['payments_terms'],
                    'blocked_by' => 'NULL',
                    'blocked_time' => 'NULL',
                    'account_payble_contact' => $sql_arr['account_payble_contact'],
                    'auto_payment' => empty($sql_arr['auto_payment']) ? 0 : $sql_arr['auto_payment'],
                    'match_carrier' => empty($sql_arr['match_carrier']) ? 0 : $sql_arr['match_carrier'],
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );

                if (($sql_arr['balance_paid_by'] != $entity->balance_paid_by) || $entity->delivery_credit != $_POST['fee_type']) {
                    if ($sql_arr['balance_paid_by'] == 24) {
                        $amount = 0;
                        if ($_POST['fee_type'] == 1) {
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with no fee";
                        } else if ($_POST['fee_type'] == 2) {
                            $amount = (((int) $entity->total_tariff - (int) $entity->total_deposit) * 0.03) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with a $" . $amount . " processing Fee";
                        } else if ($_POST['fee_type'] == 3) {
                            $amount = (((int) $entity->total_tariff - (int) $entity->total_deposit) * 0.05) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with a $" . $amount . " processing Fee";
                        } else {
                            $customNote = "Invalid Fee Type Selected";
                        }

                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => 3,
                            "text" => $customNote,
                        );
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                }

                if (is_null($this->input['source_id']) || $this->input['source_id'] == '') {
                    $update_arr['source_id'] = $sql_arr['source_id'];
                } else {
                    $update_arr['referred_by'] = $referrer_name_value;
                    $update_arr['referred_id'] = $sql_arr['referred_by'];
                }

                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }

                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }
                        if ($NotesStr != "") {

                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'vin' => $_POST['vin'][$key],
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );

                        $vehicleValue->update($insert_arr);
                    }
                }

                //RE POST TO CENTRAL DISPATCH
                if ($entity->status == Entity::STATUS_POSTED && isset($_POST["post_to_cd"]) && $_POST["post_to_cd"] == 1) {
                    $entity->postToCentralDispatch(2);
                }

                if (isset($_POST['e_cc_number']) && isset($_POST['e_cc_cvv2']) && isset($_POST['e_cc_type'])) {
                    $card_arr = array(
                        'entity_id' => $entity->id,
                        'fname' => $_POST['e_cc_fname'],
                        'lname' => $_POST['e_cc_lname'],
                        'address' => $_POST['e_cc_address'],
                        'city' => $_POST['e_cc_city'],
                        'state' => $_POST['e_cc_state'],
                        'zip' => $_POST['e_cc_zip'],
                        'number' => $_POST['e_cc_number'],
                        'cvv2' => $_POST['e_cc_cvv2'],
                        'type' => $_POST['e_cc_type'],
                        'month' => $_POST['e_cc_month'],
                        'year' => $_POST['e_cc_year'],
                    );

                    $cc = $entity->getCreditCard(true);
                    unset($card_arr['entity_id']);
                    $cc->update($card_arr);

                }

                $this->daffny->DB->transaction("commit");
                if (post_var('send_email') == '1') {
                    $entity->sendOrderConfirmation();
                }
                $_POST = array();
                $entity->getVehicles(true);

                $inp = $this->daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $entity->id . "'");

                if (!empty($inp)) {
                    $payments_terms_dispatch = $sql_arr['payments_terms'];

                    $entity_odtc = 0;
                    $entity_coc = 0;
                    $entity_carrier_pay = $entity->carrier_pay_stored;

                    if (in_array($entity->balance_paid_by, array(2, 3, 16, 17, 8, 9))) {
                        $entity_odtc = $entity->carrier_pay_stored;
                        $payments_terms_dispatch = "COD";
                    }

                    if (in_array($entity->balance_paid_by, array(12, 13, 20, 21))) {
                        $entity_coc = $entity->carrier_pay_stored;
                    }

                    if (in_array($entity->balance_paid_by, array(14, 15, 22, 23))) {
                        $entity_odtc = $entity->total_tariff;
                        $entity_coc = $entity->total_deposit;
                    }

                    $order_sql_arr = array(
                        'entity_load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                        'entity_load_date_type' => (int) $sql_arr['load_date_type'],
                        'entity_delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                        'entity_delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                        'entity_ship_via' => (int) $sql_arr['shipping_ship_via'],
                        'entity_booking_number' => $sql_arr['origin_buyer_number'],
                        'entity_buyer_number' => $sql_arr['origin_booking_number'],
                        'entity_pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                        'entity_dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                        'entity_balance_paid_by' => $sql_arr['balance_paid_by'],
                        'from_name' => $sql_arr['origin_contact_name'],
                        'from_company' => $sql_arr['origin_company_name'],
                        'origin_auction_name' => $sql_arr['origin_auction_name'],
                        'from_booking_number' => $sql_arr['origin_booking_number'],
                        'from_buyer_number' => $sql_arr['origin_buyer_number'],
                        'from_address' => $sql_arr['origin_address1'],
                        'from_address2' => $sql_arr['origin_address2'],
                        'from_city' => $sql_arr['origin_city'],
                        'from_state' => $sql_arr['origin_state'],
                        'from_zip' => $sql_arr['origin_zip'],
                        'from_country' => $sql_arr['origin_country'],
                        'from_phone_1' => str_replace("-", "", $sql_arr['origin_phone1']),
                        'from_phone_2' => str_replace("-", "", $sql_arr['origin_phone2']),
                        'from_phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                        'to_name' => $sql_arr['destination_contact_name'],
                        'to_company' => $sql_arr['destination_company_name'],
                        'to_address' => $sql_arr['destination_address1'],
                        'to_address2' => $sql_arr['destination_address2'],
                        'to_city' => $sql_arr['destination_city'],
                        'to_state' => $sql_arr['destination_state'],
                        'to_zip' => $sql_arr['destination_zip'],
                        'to_country' => $sql_arr['destination_country'],
                        'to_phone_1' => str_replace("-", "", $sql_arr['destination_phone1']),
                        'to_phone_2' => str_replace("-", "", $sql_arr['destination_phone2']),
                        'to_phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                        'to_auction_name' => $sql_arr['destination_auction_name'],
                        'to_booking_number' => $sql_arr['destination_booking_number'],
                        'to_buyer_number' => $sql_arr['destination_buyer_number'],
                        'payments_terms' => $payments_terms_dispatch,
                        'instructions' => $sql_arr['notes_from_shipper'],
                        'entity_carrier_pay' => $entity_carrier_pay,
                        'entity_odtc' => $entity_odtc,
                        'entity_coc' => $entity_coc,
                        'entity_carrier_pay_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "*COD" : (in_array($entity->balance_paid_by, array(8, 9)) ? "*COP" : "")),
                        'entity_coc_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "after COD is paid" : (in_array($entity->balance_paid_by, array(8, 9)) ? "after COP is paid" : "")),
                    );

                    $upd_order_arr = $this->daffny->DB->PrepareSql("app_dispatch_sheets", $order_sql_arr);
                    $this->daffny->DB->update("app_dispatch_sheets", $upd_order_arr, "entity_id='" . $entity->id . "' ");
                }

                if ($entity->isBlocked()) {
                    $entity->unsetBlock();
                }

                if ($entity->match_carrier == 1) {

                    if ($entity->check_match_carrier($entityOldMatchCarrier)) {
                        $entity->update(array("vehicle_update" => 0));
                        $this->daffny->DB->query("INSERT INTO app_rematch_carrier_trigger (member_id, entity_id) VALUES('" . $_SESSION['member_id'] . "', '" . $entity->id . "')");
                    }
                }

                $this->setFlashInfo("Order Updated");
                $this->daffny->DB->query("CALL Set_Batch_ReferredBy_EntityID('" . $entity->id . "')");
                $entity->updateHeaderTable();

                $date1 = date_create($startLogTime);
                $date2 = date_create(date('Y-m-d h:i:s'));
                $diff = date_diff($date1, $date2);
                redirect(getLink("orders", "show", "id", (int) $_GET['id']));
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

                if (count($this->err)) {
                    $this->setFlashError($this->err);
                }
            }
            if (count($_POST) == 0) {
                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = formatPhone($shipper->phone1);
                $this->input['shipper_phone2'] = formatPhone($shipper->phone2);
                $this->input['shipper_phone1_ext'] = $shipper->phone1_ext;
                $this->input['shipper_phone2_ext'] = $shipper->phone2_ext;
                $this->input['shipper_mobile'] = formatPhone($shipper->mobile);
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_state2'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;
                $this->input['shipper_type'] = $shipper->shipper_type;
                $this->input['shipper_hours'] = $shipper->shipper_hours;
                $this->input['account_payble_contact'] = $entity->account_payble_contact;

                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['origin_address1'] = $origin->address1;
                $this->input['origin_address2'] = $origin->address2;
                $this->input['origin_city'] = $origin->city;
                $this->input['origin_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['origin_zip'] = $origin->zip;
                $this->input['origin_country'] = $origin->country;
                $this->input['origin_contact_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['origin_company_name'] = $origin->company;
                $this->input['origin_phone1'] = formatPhone($origin->phone1);
                $this->input['origin_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['origin_phone4'] = formatPhone($origin->phone4);
                $this->input['origin_phone1_ext'] = $origin->phone1_ext;
                $this->input['origin_phone2_ext'] = $origin->phone2_ext;
                $this->input['origin_phone3_ext'] = $origin->phone3_ext;
                $this->input['origin_phone4_ext'] = $origin->phone4_ext;
                $this->input['origin_mobile'] = formatPhone($origin->phone_cell);
                $this->input['origin_mobile2'] = formatPhone($origin->phone_cell2);
                $this->input['origin_buyer_number'] = $entity->buyer_number;
                $this->input['origin_booking_number'] = $entity->booking_number;
                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_fax2'] = $origin->fax2;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;

                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['destination_address1'] = $destination->address1;
                $this->input['destination_address2'] = $destination->address2;
                $this->input['destination_city'] = $destination->city;
                $this->input['destination_state'] = $destination->state;
                $this->input['destination_state2'] = $destination->state;
                $this->input['destination_zip'] = $destination->zip;
                $this->input['destination_country'] = $destination->country;
                $this->input['destination_contact_name'] = $destination->name;
                $this->input['destination_company_name'] = $destination->company;
                $this->input['destination_phone1'] = formatPhone($destination->phone1);
                $this->input['destination_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['destination_phone4'] = formatPhone($destination->phone4);
                $this->input['destination_phone1_ext'] = $destination->phone1_ext;
                $this->input['destination_phone2_ext'] = $destination->phone2_ext;
                $this->input['destination_phone3_ext'] = $destination->phone3_ext;
                $this->input['destination_phone4_ext'] = $destination->phone4_ext;
                $this->input['destination_mobile'] = formatPhone($destination->phone_cell);
                $this->input['destination_mobile2'] = formatPhone($destination->phone_cell2);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['destination_booking_number'] = $destination->booking_number;
                $this->input['destination_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_fax2'] = $destination->fax2;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;

                /* Load Shipping Information */
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['load_date_type'] = $entity->load_date_type;
                $this->input['delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['delivery_date_type'] = $entity->delivery_date_type;
                $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
                $this->input['shipping_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['referred_by'] = $entity->referred_id;
                $this->input['source_id'] = $entity->source_id;
                $this->input['notes_from_shipper'] = $entity->notes_from_shipper;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['pickup_terminal_fee'] = $entity->pickup_terminal_fee;
                $this->input['delivery_terminal_fee'] = $entity->dropoff_terminal_fee;
                $this->input['payments_terms'] = $entity->payments_terms;
                $this->input['fee_type'] = $entity->delivery_credit;
                $this->input['auto_payment'] = $entity->auto_payment;
                
                // Show value in payment block
                $entityCreditCard = $entity->getCreditCard();
                $this->input['e_cc_fname'] = $entityCreditCard->fname;
                $this->input['e_cc_lname'] = $entityCreditCard->lname;
                $this->input['e_cc_address'] = $entityCreditCard->address;
                $this->input['e_cc_city'] = $entityCreditCard->city;
                $this->input['e_cc_state'] = $entityCreditCard->state;
                $this->input['e_cc_zip'] = $entityCreditCard->zip;
                $this->input['e_cc_cvv2'] = $entityCreditCard->cvv2;
                $this->input['e_cc_number'] = $entityCreditCard->number;
                $this->input['e_cc_type'] = $entityCreditCard->type;
                $this->input['e_cc_month'] = $entityCreditCard->month;
                $this->input['e_cc_year'] = $entityCreditCard->year;
            }

            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();

            $this->daffny->tpl->ask_post_to_cd = $ask_post_to_cd;
            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:100px;width:100%;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($settings->referrer_status, $entity->status);

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            print_r($e->getMessage());
            die("<br>Exception Found");
            redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            die($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function edit2()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.edit_new";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            $sql = "SELECT blocked_by_carrier FROM app_entities WHERE id = " . $entity->id;
            $res = $this->daffny->DB->query($sql);
            $blocked_by_carrier = mysqli_fetch_assoc($res)['blocked_by_carrier'];

            if ($entity->isBlocked()) {
                $blockedBy = $entity->blockedByMember();
                $this->setFlashError($blockedBy . ' is editing this order at this moment, please try again later.');
                redirect(getLink('orders', 'show', 'id', $entity->id));
            }

            /**
             * 17042018 - Chetu Added patch to prevent user to modify dispatched
             * orders if no access assigned
             */
            $accessDispatchedLeads = $_SESSION['member']['access_dispatch_orders'];
            if (!checkDispatchOrderEditAccess($entity->status, $accessDispatchedLeads)) {
                $this->setFlashError("Access Denied!");
                redirect(getLink("orders", "show", "id", $entity->id));
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", getLink('orders/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Edit"));
            $this->title = "Edit Order #" . $entity->number;

            //Ask if need to post to CD?
            $ask_post_to_cd = false;
            $settings = $entity->getAssigned()->getDefaultSettings();
            if ($entity->status == Entity::STATUS_POSTED) {

                if ($settings->central_dispatch_uid != "" && $settings->central_dispatch_post == 1) {
                    $ask_post_to_cd = true;
                }
            }

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditForm(false, $settings->referrer_status, $entity->status)) {

                $startLogTime = date('d-m-Y h:m:s');

                $entityOldMatchCarrier = new Entity($this->daffny->DB);
                $entityOldMatchCarrier->load((int) $_GET['id']);
                $entityOldMatchCarrier->getVehicles();
                $entityOldMatchCarrier->getOrigin();
                $entityOldMatchCarrier->getDestination();

                /********** Log ********/
                $info = "Edit Order-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();

                $referrer_name_value = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }

                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                if ($sql_arr['shipper_country'] != "US") {
                    $sql_arr['shipper_state'] = $sql_arr['shipper_state2'];
                }
                $update_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'phone1_ext' => $sql_arr['shipper_phone1_ext'],
                    'phone2_ext' => $sql_arr['shipper_phone2_ext'],
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
                );
                $shipper->update($update_arr);

                $account = new Account($this->daffny->DB);

                $account_arr = array(
                    'owner_id' => $_SESSION['member_id'],
                    'is_carrier' => 0,
                    'is_shipper' => 1,
                    'is_location' => 0,
                    'company_name' => $update_arr['company'],
                    'first_name' => $update_arr['fname'],
                    'last_name' => $update_arr['lname'],
                    'email' => $update_arr['email'],
                    'phone1' => str_replace("-", "", $update_arr['phone1']),
                    'phone2' => str_replace("-", "", $update_arr['phone2']),
                    'phone1_ext' => $update_arr['phone1_ext'],
                    'phone2_ext' => $update_arr['phone2_ext'],
                    'cell' => str_replace("-", "", $update_arr['mobile']),
                    'fax' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fax']),
                    'address1' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['address1']),
                    'address2' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['address2']),
                    'city' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']),
                    'state' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']),
                    'zip_code' => $update_arr['zip'],
                    'country' => $update_arr['country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'hours_of_operation' => mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['shipper_hours']),
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'account_payble_contact' => $sql_arr['account_payble_contact'],

                );

                if (isset($_POST['save_shipper']) && $_POST['save_shipper'] == 1) {

                    unset($account_arr['referred_by']);
                    unset($account_arr['referred_id']);
                    $account_id_update = $entity->account_id;
                    if ($_POST['edit_shipper_id'] > 0) {
                        $account_id_update = $_POST['edit_shipper_id'];
                    }

                    $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                    $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $account_id_update . "' ");
                    $account->load($account_id_update);

                } else {

                    if ($update_arr['company'] != "") {

                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "' AND state='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']) . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']) . "'  AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['lname']) . "' AND `is_shipper` = 1)");
                    } else {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='' AND state='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['state']) . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['city']) . "'  AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['lname']) . "' AND `is_shipper` = 1)");
                    }
                    if (empty($rowShipper)) {

                        $account->create($account_arr);
                        $update_account_id_arr = array(
                            'account_id' => $account->id,
                        );
                        $entity->update($update_account_id_arr);
                        
                    } else {

                        if ($rowShipper["id"] != '') {
                            unset($account_arr['referred_by']);
                            unset($account_arr['referred_id']);
                            $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                            $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                            $account->load($rowShipper["id"]);

                            $update_account_id_arr = array(
                                'account_id' => $account->id,
                            );
                            $entity->update($update_account_id_arr);

                        }
                    }
                }

                /* UPDATE ORIGIN */
                $origin = $entity->getOrigin();
                if ($sql_arr['origin_country'] != "US") {
                    $sql_arr['origin_state'] = $sql_arr['origin_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['origin_address1'],
                    'address2' => $sql_arr['origin_address2'],
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                    'name' => $sql_arr['origin_contact_name'],
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['origin_company_name'],
                    'phone1' => str_replace("-", "", $sql_arr['origin_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['origin_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['origin_phone4']),
                    'phone1_ext' => $sql_arr['origin_phone1_ext'],
                    'phone2_ext' => $sql_arr['origin_phone2_ext'],
                    'phone3_ext' => $sql_arr['origin_phone3_ext'],
                    'phone4_ext' => $sql_arr['origin_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['origin_mobile2']),
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'fax2' => $sql_arr['origin_fax2'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
                );
                $origin->update($update_arr);

                if (isset($_POST['save_location1'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'contact_name1' => $sql_arr['origin_contact_name'],
                        'contact_name2' => $sql_arr['origin_contact_name2'],
                        'phone1' => str_replace("-", "", $update_arr['phone1']),
                        'phone2' => str_replace("-", "", $update_arr['phone2']),
                        'phone1_ext' => $update_arr['phone1_ext'],
                        'phone2_ext' => $update_arr['phone2_ext'],
                        'cell' => $update_arr['phone_cell'],
                        'address1' => $update_arr['address1'],
                        'address2' => $update_arr['address2'],
                        'city' => $update_arr['city'],
                        'state' => $update_arr['state'],
                        'zip_code' => $update_arr['zip'],
                        'country' => $update_arr['country'],
                        'location_type' => $sql_arr['origin_type'],
                        'hours_of_operation' => $sql_arr['origin_hours'],
                    );

                    if ($sql_arr['origin_id'] != 0) {
                        $this->updateAccountHistory($account_arr, $sql_arr['origin_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['origin_id'] . "' ");

                    } elseif ($update_arr['company'] != "" || $sql_arr['origin_contact_name'] != '') {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_contact_name']) . "'  AND
						`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "' AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $account->create($account_arr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
                                $this->updateAccountHistory($account_arr, $rowOriginLocation["id"]);
                                $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                                $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowOriginLocation["id"] . "' ");

                            }
                        }
                    }
                }
                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['destination_country'] != "US") {
                    $sql_arr['destination_state'] = $sql_arr['destination_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['destination_address1'],
                    'address2' => $sql_arr['destination_address2'],
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                    'name' => $sql_arr['destination_contact_name'],
                    'company' => $sql_arr['destination_company_name'],
                    'phone1' => str_replace("-", "", $sql_arr['destination_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['destination_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['destination_phone4']),
                    'phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['destination_mobile2']),
                    'phone1_ext' => $sql_arr['destination_phone1_ext'],
                    'phone2_ext' => $sql_arr['destination_phone2_ext'],
                    'phone3_ext' => $sql_arr['destination_phone3_ext'],
                    'phone4_ext' => $sql_arr['destination_phone4_ext'],
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'fax2' => $sql_arr['destination_fax2'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                );

                $destination->update($update_arr);

                if (isset($_POST['save_location2'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'contact_name1' => $sql_arr['destination_contact_name'],
                        'contact_name2' => $sql_arr['destination_contact_name2'],
                        'phone1' => str_replace("-", "", $update_arr['phone1']),
                        'phone2' => str_replace("-", "", $update_arr['phone2']),
                        'phone1_ext' => $update_arr['phone1_ext'],
                        'phone2_ext' => $update_arr['phone2_ext'],
                        'cell' => $update_arr['phone_cell'],
                        'address1' => $update_arr['address1'],
                        'address2' => $update_arr['address2'],
                        'city' => $update_arr['city'],
                        'state' => $update_arr['state'],
                        'zip_code' => $update_arr['zip'],
                        'country' => $update_arr['country'],
                        'location_type' => $sql_arr['destination_type'],
                        'hours_of_operation' => $sql_arr['destination_hours'],
                    );

                    if ($sql_arr['destination_id'] != 0) {

                        $this->updateAccountHistory($account_arr, $sql_arr['destination_id']);
                        $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                        $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $sql_arr['destination_id'] . "' ");

                    } elseif ($update_arr['company'] != "" || $sql_arr['destination_contact_name'] != '') {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						contact_name1='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_contact_name']) . "'  AND  `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $update_arr['company']) . "'  AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowDestLocation)) {
                            if ($_GET['id'] == 126205) {
                                print_r($account_arr);
                            }

                            $account->create($account_arr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
                                $this->updateAccountHistory($account_arr, $rowDestLocation["id"]);
                                $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                                $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowDestLocation["id"] . "' ");
                            }
                        }
                    }
                }

                /* UPDATE NOTE */
                $notes = $entity->getNotes();
                if (count($notes[Note::TYPE_FROM]) != 0) {
                    $note = $notes[Note::TYPE_FROM][0];
                    $note->update(array('text' => $sql_arr['notes_from_shipper']));
                } else {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }

                $update_arr = array(
                    'ship_via' => (int) $sql_arr['shipping_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                    'load_date_type' => (int) $sql_arr['load_date_type'],
                    'delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['notes_from_shipper'],
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'payments_terms' => $sql_arr['payments_terms'],
                    'blocked_by' => 'NULL',
                    'blocked_time' => 'NULL',
                    'account_payble_contact' => $sql_arr['account_payble_contact'],
                    'auto_payment' => empty($sql_arr['auto_payment']) ? 0 : $sql_arr['auto_payment'],
                    'match_carrier' => empty($sql_arr['match_carrier']) ? 0 : $sql_arr['match_carrier'],
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );

                if (($sql_arr['balance_paid_by'] != $entity->balance_paid_by) || $entity->delivery_credit != $_POST['fee_type']) {
                    if ($sql_arr['balance_paid_by'] == 24) {
                        $amount = 0;
                        if ($_POST['fee_type'] == 1) {
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with no fee";
                        } else if ($_POST['fee_type'] == 2) {
                            $amount = (((int) $entity->total_tariff - (int) $entity->total_deposit) * 0.03) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with a $" . $amount . " processing Fee";
                        } else if ($_POST['fee_type'] == 3) {
                            $amount = (((int) $entity->total_tariff - (int) $entity->total_deposit) * 0.05) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = "<b>" . $_SESSION['member']['contactname'] . "</b> has selected to pay the carrier by ACH with a $" . $amount . " processing Fee";
                        } else {
                            $customNote = "Invalid Fee Type Selected";
                        }

                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => 3,
                            "text" => $customNote,
                        );
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                }

                if (is_null($this->input['source_id']) || $this->input['source_id'] == '') {
                    $update_arr['source_id'] = $sql_arr['source_id'];
                } else {
                    $update_arr['referred_by'] = $referrer_name_value;
                    $update_arr['referred_id'] = $sql_arr['referred_by'];
                }

                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }

                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }
                        if ($NotesStr != "") {

                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'vin' => $_POST['vin'][$key],
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );

                        $vehicleValue->update($insert_arr);
                    }
                }

                //RE POST TO CENTRAL DISPATCH
                if ($entity->status == Entity::STATUS_POSTED && isset($_POST["post_to_cd"]) && $_POST["post_to_cd"] == 1) {
                    $entity->postToCentralDispatch(2);
                }

                if (isset($_POST['e_cc_number']) && isset($_POST['e_cc_cvv2']) && isset($_POST['e_cc_type'])) {
                    $card_arr = array(
                        'entity_id' => $entity->id,
                        'fname' => $_POST['e_cc_fname'],
                        'lname' => $_POST['e_cc_lname'],
                        'address' => $_POST['e_cc_address'],
                        'city' => $_POST['e_cc_city'],
                        'state' => $_POST['e_cc_state'],
                        'zip' => $_POST['e_cc_zip'],
                        'number' => $_POST['e_cc_number'],
                        'cvv2' => $_POST['e_cc_cvv2'],
                        'type' => $_POST['e_cc_type'],
                        'month' => $_POST['e_cc_month'],
                        'year' => $_POST['e_cc_year'],
                    );

                    $cc = $entity->getCreditCard(true);
                    unset($card_arr['entity_id']);
                    $cc->update($card_arr);

                }

                $this->daffny->DB->transaction("commit");
                if (post_var('send_email') == '1') {
                    $entity->sendOrderConfirmation();
                }
                $_POST = array();
                $entity->getVehicles(true);

                $inp = $this->daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $entity->id . "'");

                if (!empty($inp)) {
                    $payments_terms_dispatch = $sql_arr['payments_terms'];

                    $entity_odtc = 0;
                    $entity_coc = 0;
                    $entity_carrier_pay = $entity->carrier_pay_stored;

                    if (in_array($entity->balance_paid_by, array(2, 3, 16, 17, 8, 9))) {
                        $entity_odtc = $entity->carrier_pay_stored;
                        $payments_terms_dispatch = "COD";
                    }

                    if (in_array($entity->balance_paid_by, array(12, 13, 20, 21))) {
                        $entity_coc = $entity->carrier_pay_stored;
                    }

                    if (in_array($entity->balance_paid_by, array(14, 15, 22, 23))) {
                        $entity_odtc = $entity->total_tariff;
                        $entity_coc = $entity->total_deposit;
                    }

                    $order_sql_arr = array(
                        'entity_load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                        'entity_load_date_type' => (int) $sql_arr['load_date_type'],
                        'entity_delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                        'entity_delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                        'entity_ship_via' => (int) $sql_arr['shipping_ship_via'],
                        'entity_booking_number' => $sql_arr['origin_buyer_number'],
                        'entity_buyer_number' => $sql_arr['origin_booking_number'],
                        'entity_pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                        'entity_dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                        'entity_balance_paid_by' => $sql_arr['balance_paid_by'],
                        'from_name' => $sql_arr['origin_contact_name'],
                        'from_company' => $sql_arr['origin_company_name'],
                        'origin_auction_name' => $sql_arr['origin_auction_name'],
                        'from_booking_number' => $sql_arr['origin_booking_number'],
                        'from_buyer_number' => $sql_arr['origin_buyer_number'],
                        'from_address' => $sql_arr['origin_address1'],
                        'from_address2' => $sql_arr['origin_address2'],
                        'from_city' => $sql_arr['origin_city'],
                        'from_state' => $sql_arr['origin_state'],
                        'from_zip' => $sql_arr['origin_zip'],
                        'from_country' => $sql_arr['origin_country'],
                        'from_phone_1' => str_replace("-", "", $sql_arr['origin_phone1']),
                        'from_phone_2' => str_replace("-", "", $sql_arr['origin_phone2']),
                        'from_phone_cell' => str_replace("-", "", $sql_arr['origin_mobile']),
                        'to_name' => $sql_arr['destination_contact_name'],
                        'to_company' => $sql_arr['destination_company_name'],
                        'to_address' => $sql_arr['destination_address1'],
                        'to_address2' => $sql_arr['destination_address2'],
                        'to_city' => $sql_arr['destination_city'],
                        'to_state' => $sql_arr['destination_state'],
                        'to_zip' => $sql_arr['destination_zip'],
                        'to_country' => $sql_arr['destination_country'],
                        'to_phone_1' => str_replace("-", "", $sql_arr['destination_phone1']),
                        'to_phone_2' => str_replace("-", "", $sql_arr['destination_phone2']),
                        'to_phone_cell' => str_replace("-", "", $sql_arr['destination_mobile']),
                        'to_auction_name' => $sql_arr['destination_auction_name'],
                        'to_booking_number' => $sql_arr['destination_booking_number'],
                        'to_buyer_number' => $sql_arr['destination_buyer_number'],
                        'payments_terms' => $payments_terms_dispatch,
                        'instructions' => $sql_arr['notes_from_shipper'],
                        'entity_carrier_pay' => $entity_carrier_pay,
                        'entity_odtc' => $entity_odtc,
                        'entity_coc' => $entity_coc,
                        'entity_carrier_pay_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "*COD" : (in_array($entity->balance_paid_by, array(8, 9)) ? "*COP" : "")),
                        'entity_coc_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "after COD is paid" : (in_array($entity->balance_paid_by, array(8, 9)) ? "after COP is paid" : "")),
                    );

                    $upd_order_arr = $this->daffny->DB->PrepareSql("app_dispatch_sheets", $order_sql_arr);
                    $this->daffny->DB->update("app_dispatch_sheets", $upd_order_arr, "entity_id='" . $entity->id . "' ");
                }

                if ($entity->isBlocked()) {
                    $entity->unsetBlock();
                }

                if ($entity->match_carrier == 1) {

                    if ($entity->check_match_carrier($entityOldMatchCarrier)) {
                        $entity->update(array("vehicle_update" => 0));
                        $this->daffny->DB->query("INSERT INTO app_rematch_carrier_trigger (member_id, entity_id) VALUES('" . $_SESSION['member_id'] . "', '" . $entity->id . "')");
                    }
                }

                $this->setFlashInfo("Order Updated");
                $this->daffny->DB->query("CALL Set_Batch_ReferredBy_EntityID('" . $entity->id . "')");
                $entity->updateHeaderTable();

                $date1 = date_create($startLogTime);
                $date2 = date_create(date('Y-m-d h:i:s'));
                $diff = date_diff($date1, $date2);
                redirect(getLink("orders", "show", "id", (int) $_GET['id']));
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

                if (count($this->err)) {
                    $this->setFlashError($this->err);
                }
            }
            if (count($_POST) == 0) {
                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = formatPhone($shipper->phone1);
                $this->input['shipper_phone2'] = formatPhone($shipper->phone2);
                $this->input['shipper_phone1_ext'] = $shipper->phone1_ext;
                $this->input['shipper_phone2_ext'] = $shipper->phone2_ext;
                $this->input['shipper_mobile'] = formatPhone($shipper->mobile);
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_state2'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;
                $this->input['shipper_type'] = $shipper->shipper_type;
                $this->input['shipper_hours'] = $shipper->shipper_hours;
                $this->input['account_payble_contact'] = $entity->account_payble_contact;

                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['origin_address1'] = $origin->address1;
                $this->input['origin_address2'] = $origin->address2;
                $this->input['origin_city'] = $origin->city;
                $this->input['origin_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['origin_zip'] = $origin->zip;
                $this->input['origin_country'] = $origin->country;
                $this->input['origin_contact_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['origin_company_name'] = $origin->company;
                $this->input['origin_phone1'] = formatPhone($origin->phone1);
                $this->input['origin_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['origin_phone4'] = formatPhone($origin->phone4);
                $this->input['origin_phone1_ext'] = $origin->phone1_ext;
                $this->input['origin_phone2_ext'] = $origin->phone2_ext;
                $this->input['origin_phone3_ext'] = $origin->phone3_ext;
                $this->input['origin_phone4_ext'] = $origin->phone4_ext;
                $this->input['origin_mobile'] = formatPhone($origin->phone_cell);
                $this->input['origin_mobile2'] = formatPhone($origin->phone_cell2);
                $this->input['origin_buyer_number'] = $entity->buyer_number;
                $this->input['origin_booking_number'] = $entity->booking_number;
                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_fax2'] = $origin->fax2;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;

                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['destination_address1'] = $destination->address1;
                $this->input['destination_address2'] = $destination->address2;
                $this->input['destination_city'] = $destination->city;
                $this->input['destination_state'] = $destination->state;
                $this->input['destination_state2'] = $destination->state;
                $this->input['destination_zip'] = $destination->zip;
                $this->input['destination_country'] = $destination->country;
                $this->input['destination_contact_name'] = $destination->name;
                $this->input['destination_company_name'] = $destination->company;
                $this->input['destination_phone1'] = formatPhone($destination->phone1);
                $this->input['destination_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['destination_phone4'] = formatPhone($destination->phone4);
                $this->input['destination_phone1_ext'] = $destination->phone1_ext;
                $this->input['destination_phone2_ext'] = $destination->phone2_ext;
                $this->input['destination_phone3_ext'] = $destination->phone3_ext;
                $this->input['destination_phone4_ext'] = $destination->phone4_ext;
                $this->input['destination_mobile'] = formatPhone($destination->phone_cell);
                $this->input['destination_mobile2'] = formatPhone($destination->phone_cell2);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['destination_booking_number'] = $destination->booking_number;
                $this->input['destination_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_fax2'] = $destination->fax2;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;

                /* Load Shipping Information */
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['load_date_type'] = $entity->load_date_type;
                $this->input['delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['delivery_date_type'] = $entity->delivery_date_type;
                $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
                $this->input['shipping_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['referred_by'] = $entity->referred_id;
                $this->input['source_id'] = $entity->source_id;
                $this->input['notes_from_shipper'] = $entity->notes_from_shipper;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['pickup_terminal_fee'] = $entity->pickup_terminal_fee;
                $this->input['delivery_terminal_fee'] = $entity->dropoff_terminal_fee;
                $this->input['payments_terms'] = $entity->payments_terms;
                $this->input['fee_type'] = $entity->delivery_credit;
                $this->input['auto_payment'] = $entity->auto_payment;
                
                // Show value in payment block
                $entityCreditCard = $entity->getCreditCard();
                $this->input['e_cc_fname'] = $entityCreditCard->fname;
                $this->input['e_cc_lname'] = $entityCreditCard->lname;
                $this->input['e_cc_address'] = $entityCreditCard->address;
                $this->input['e_cc_city'] = $entityCreditCard->city;
                $this->input['e_cc_state'] = $entityCreditCard->state;
                $this->input['e_cc_zip'] = $entityCreditCard->zip;
                $this->input['e_cc_cvv2'] = $entityCreditCard->cvv2;
                $this->input['e_cc_number'] = $entityCreditCard->number;
                $this->input['e_cc_type'] = $entityCreditCard->type;
                $this->input['e_cc_month'] = $entityCreditCard->month;
                $this->input['e_cc_year'] = $entityCreditCard->year;
            }

            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();

            $this->daffny->tpl->ask_post_to_cd = $ask_post_to_cd;
            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:100px;width:100%;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($settings->referrer_status, $entity->status);

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            print_r($e->getMessage());
            die("<br>Exception Found");
            redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            die($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.history";
            $this->title = "Order History";
            $this->section = "Orders";
            $this->applyOrder("app_history");
            $this->order->setDefault('change_date', 'desc');

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", getLink("orders/show/id/" . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "History"));
            
            $historyManager = new HistoryManager($this->daffny->DB);
            $this->daffny->tpl->history = $historyManager->getHistory($this->order->getOrder(), $_SESSION['per_page'], " `entity_id` = " . (int) $_GET['id']);
            $this->pager = $historyManager->getPager();

            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );

            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function show()
    {
        try {

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.detail";
            $this->title = "Order Details";

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Order Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");

            $this->daffny->tpl->notes = $notes;

            // fetch tasks based on task type
            $taskManager = new TaskManager($this->daffny->DB);
            $where = "";
            if ($_GET['task'] == 2) {
                $where = " AND completed = 1";
            } elseif ($_GET['task'] == 3) {
                $where = " AND deleted = 1 ";
            } else {
                $where = " AND completed = 0 AND deleted = 0 ";
            }

            $where .= " ORDER BY id DESC";
            
            $this->daffny->tpl->tasks = $taskManager->get_user_entity_task($entity->id, $_SESSION['member_id'], $where);
            $this->daffny->tpl->vehicles = $entity->getVehicles();

            $this->input['task_entity_id'] = $entity->id;
            $this->form->TextField("task_entity_id", 255, array('style' => "width: 300px;  display:none;"), " ", "</td><td colspan=\"3\">");
            $this->form->TextField("task", 255, array('style' => "width: 300px;"), "Subject", "</td><td colspan=\"3\">");
            $this->form->TextField("taskdate", 10, array('style' => ""), "Start Date", "</td><td>");
            $this->form->TextField("duedate", 10, array('style' => ""), "Due Date", "</td><td>");
            $this->form->CheckBox("reminder", array(), "Reminder", "</td><td>");
            $this->form->TextField("reminder_date", 10, array('style' => ""), "", "</td><td>");
            $this->form->TextArea("taskdata", 15, 10, array("style" => "height:100px; width:300px;"), "Task", "</td ><td colspan='3'>");

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();
            
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function show2()
    {
        try {

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.show";
            $this->title = "Order Details";

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Order Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");

            $this->daffny->tpl->notes = $notes;

            // fetch tasks based on task type
            $taskManager = new TaskManager($this->daffny->DB);
            $where = "";
            if ($_GET['task'] == 2) {
                $where = " AND completed = 1";
            } elseif ($_GET['task'] == 3) {
                $where = " AND deleted = 1 ";
            } else {
                $where = " AND completed = 0 AND deleted = 0 ";
            }

            $where .= " ORDER BY id DESC";
            
            $this->daffny->tpl->tasks = $taskManager->get_user_entity_task($entity->id, $_SESSION['member_id'], $where);
            $this->daffny->tpl->vehicles = $entity->getVehicles();

            $this->input['task_entity_id'] = $entity->id;
            $this->form->TextField("task_entity_id", 255, array('style' => "width: 300px;  display:none;"), " ", "</td><td colspan=\"3\">");
            $this->form->TextField("task", 255, array('style' => "width: 300px;"), "Subject", "</td><td colspan=\"3\">");
            $this->form->TextField("taskdate", 10, array('style' => ""), "Start Date", "</td><td>");
            $this->form->TextField("duedate", 10, array('style' => ""), "Due Date", "</td><td>");
            $this->form->CheckBox("reminder", array(), "Reminder", "</td><td>");
            $this->form->TextField("reminder_date", 10, array('style' => ""), "", "</td><td>");
            $this->form->TextArea("taskdata", 15, 10, array("style" => "height:100px; width:300px;"), "Task", "</td ><td colspan='3'>");

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();
            
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function showsearch()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.detailsearch";
            $this->title = "Order Details";

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Order Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");

            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function dispatch()
    {
        $this->check_access('dispatch');
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.dispatch";
            $this->title = "Dispatch Sheet";
            
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            if (is_null($entity->dispatched)) {
                throw new FDException("Order not Dispathced");
            }

            $dispatchSheet = new DispatchSheet($this->daffny->DB);
            $dispatchManager = new DispatchSheetManager($this->daffny->DB);
            $dispatchManager->getDispatchSheetByOrderId($_GET['id']);
            $dispatchSheet->load($dispatchManager->getDispatchSheetByOrderId($_GET['id']));
            if (!is_null($dispatchSheet->rejected)) {
                throw new FDException("Dispatch Sheet rejected");
            }

            if (!is_null($dispatchSheet->cancelled)) {
                throw new FDException("Dispatch Sheet cancelled");
            }

            $this->daffny->tpl->dispatch = $dispatchSheet;
            $this->daffny->tpl->entity = $entity;
            $member = new Member($this->daffny->DB);
            $member->load($_SESSION['member_id']);
            $this->daffny->tpl->company = $member->getCompanyProfile();
            $rows = $this->daffny->DB->selectRows("`id`, `created`", "`app_dispatch_sheets`", "WHERE `entity_id` = " . (int) $_GET['id'] . " AND (`rejected` IS NOT NULL OR `cancelled` IS NOT NULL)");
            
            if (!is_array($rows) || $this->daffny->DB->isError) {
                throw new FDException("DB query error");
            }

            if (count($rows) > 0) {
                $dispatchHistory = array();
                foreach ($rows as $val) {
                    $dispatchHistory[$val['id']] = date("m/d/Y", strtotime($val['created']));
                }
                $this->daffny->tpl->dispatch_history = $dispatchHistory;
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", getLink('orders/show/id/' . $_GET['id']) => 'Order #' . $entity->getNumber(), '' => 'Dispatch Sheet'));
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    protected function loadOrdersPage($status)
    {

        $this->tplname = "orders.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = "orders.orders";

        $this->applyOrder(Entity::TABLE);
        $this->order->Fields[] = 'shipper';
        $this->order->Fields[] = 'origin';
        $this->order->Fields[] = 'destination';
        $this->order->Fields[] = 'avail';
        $this->order->Fields[] = 'tariff';

        switch ($status) {
            case Entity::STATUS_ACTIVE:
                $this->title = "My Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders"));
                $this->order->setDefault('created', 'desc');
                break;
            case Entity::STATUS_ONHOLD:
                $this->title = "Orders On Hold";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Hold'));
                $this->order->setDefault('hold_date', 'desc');
                break;
            case Entity::STATUS_ARCHIVED:
                $this->title = "Orders Archived";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Archived'));
                $this->order->setDefault('archived', 'desc');
                break;
            case Entity::STATUS_POSTED:
                $this->title = "Orders Posted to FB";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Posted CD'));
                $this->order->setDefault('posted', 'desc');
                break;
            case Entity::STATUS_NOTSIGNED:
                $this->title = "Orders Notsigned";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Not Signed'));
                $this->order->setDefault('not_signed', 'desc');
                break;
            case Entity::STATUS_DISPATCHED:
                $this->title = "Orders Dispatched";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Dispatched'));
                $this->order->setDefault('dispatched', 'desc');
                break;
            case Entity::STATUS_PICKEDUP:
                $this->title = "Orders Picked Up";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Picked Up'));
                $this->order->setDefault('actual_pickup_date', 'desc');
                break;
            case Entity::STATUS_DELIVERED:
                $this->title = "Orders Delviered";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Delivered'));
                $this->order->setDefault('delivered', 'desc');
                break;
            case Entity::STATUS_ISSUES:
                $this->title = "Orders Issues";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Issue'));
                $this->order->setDefault('issue_date', 'desc');
                break;
            default:
                $this->title = "All Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'All'));
                $this->order->setDefault('id', 'desc');
                break;
        }

        $info = "Show order listing: " . $this->title;
        $applog = new Applog($this->daffny->DB);
        $applog->createInformation($info);

        $entityManager = new EntityManager($this->daffny->DB);

        switch ($this->order->CurrentOrder) {
            case 'created':
                $this->order->setTableIndex('e');
                break;
        }

        if (!is_null($status)) {
            $this->daffny->tpl->entities = $entityManager->getEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $status, $_SESSION['per_page']);
        } else {
            $this->daffny->tpl->entities = $entityManager->getAllEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $_SESSION['per_page']);
        }

        $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);

        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal,
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);

        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));

        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

        $this->form->ComboBox('issue_type', array('' => 'Select One', '1' => 'By Customer', '2' => 'By Carrier', '3' => 'Partial payments'), array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "getElementById('issue_form').submit();"), '', '</td><td>');

        $this->getDispatchForm();
        $this->getPaymentForm();
    }

    protected function loadOrdersPageNew($status)
    {

        $this->tplname = "orders.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = "orders.ordersnew";

        $this->applyOrder(Entity::TABLE);
        $this->order->Fields[] = 'shipper';
        $this->order->Fields[] = 'origin';
        $this->order->Fields[] = 'destination';
        $this->order->Fields[] = 'avail';
        $this->order->Fields[] = 'tariff';

        switch ($status) {
            case Entity::STATUS_ACTIVE:
                $this->title = "My Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders"));
                $this->order->setDefault('created', 'desc');
                break;
            case Entity::STATUS_ONHOLD:
                $this->title = "Orders On Hold";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Hold'));
                $this->order->setDefault('hold_date', 'desc');
                break;
            case Entity::STATUS_ARCHIVED:
                $this->title = "Orders Archived";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Archived'));
                $this->order->setDefault('archived', 'desc');
                break;
            case Entity::STATUS_POSTED:
                $this->title = "Orders Posted to FB";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Posted CD'));
                $this->order->setDefault('posted', 'desc');
                break;
            case Entity::STATUS_NOTSIGNED:
                $this->title = "Orders Notsigned";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Not Signed'));
                $this->order->setDefault('not_signed', 'desc');
                break;
            case Entity::STATUS_DISPATCHED:
                $this->title = "Orders Dispatched";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Dispatched'));
                $this->order->setDefault('dispatched', 'desc');
                break;
            case Entity::STATUS_PICKEDUP:
                $this->title = "Orders Picked Up";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Picked Up'));
                $this->order->setDefault('actual_pickup_date', 'desc');
                break;
            case Entity::STATUS_DELIVERED:
                $this->title = "Orders Delviered";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Delivered'));
                $this->order->setDefault('delivered', 'desc');
                break;
            case Entity::STATUS_ISSUES:
                $this->title = "Orders Issues";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Issue'));
                $this->order->setDefault('issue_date', 'desc');
                break;
            default:
                $this->title = "All Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'All'));
                $this->order->setDefault('id', 'desc');
                break;
        }

        $info = "Show order listing: " . $this->title;
        $applog = new Applog($this->daffny->DB);
        $applog->createInformation($info);

        $entityManager = new EntityManager($this->daffny->DB);

        switch ($this->order->CurrentOrder) {
            case 'created':
                $this->order->setTableIndex('e');
                break;
        }

        if (!is_null($status)) {
            $this->daffny->tpl->entities = $entityManager->getEntitiesArrData(Entity::TYPE_ORDER, $this->order->getOrder(), $status, $_SESSION['per_page']);
        } else {
            $this->daffny->tpl->entities = $entityManager->getAllEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $_SESSION['per_page']);
        }

        $sumAmount = $entityManager->getSumAmounts(Entity::TYPE_ORDER, $status);
        $this->daffny->tpl->sumAmount = $sumAmount;

        $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);

        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal,
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);

        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));

        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

        $this->form->ComboBox('issue_type', array('' => 'Select One', '1' => 'By Customer', '2' => 'By Carrier', '3' => 'Partial payments'), array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "getElementById('issue_form').submit();"), '', '</td><td>');

        $this->getDispatchForm();
        $this->getPaymentForm();
    }

    protected function getPaymentForm()
    {
        if (!isset($_POST['payment_type']) || $_POST['payment_type'] == "") {
            $this->input['payment_type_selector'] = 'internally';
        } else {
            $this->input['payment_type_selector'] = $_POST['payment_type'];
        }

        $member = new Member($this->daffny->DB);
        $member->load($_SESSION['member_id']);

        $company = $member->getCompanyProfile();
        $is_carrier = 1;
        if ($company->is_carrier == 1) {
            $is_carrier = 0;
        }

        $this->form->helperGWType("payment_type_selector", array(), $is_carrier);
        $this->daffny->tpl->is_carrier = $is_carrier;

        if (!isset($_POST['date_received']) || $_POST['date_received'] == "") {
            $this->input['date_received'] = date('m/d/Y');
        } else {
            $this->input['date_received'] = $_POST['date_received'];
        }
        $this->form->TextField('date_received', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

        $this->form->TextField('amount', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
        $this->form->TextField('transaction_id', '32', array(), "Transaction ID", "</td><td>");
        $from_to_options = array(
            '' => 'Select One',
            Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
            Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
            Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company',
        );
        $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");

        if (!isset($_POST['method']) || $_POST['method'] == "") {
            $this->input['method'] = 2;
        } else {
            $this->input['method'] = $_POST['method'];
        }

        $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");

        $from_to_options_carrier = array(
            '' => 'Select One',
            Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
            Payment::SBJ_SHIPPER . '-' . Payment::SBJ_CARRIER => 'Shipper to Carrier',
        );

        $this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
        $this->form->ComboBox('from_to_carrier', $from_to_options_carrier, array(), $this->requiredTxt . "Payment From/To", "</td><td>");

        if (!isset($_POST['date_received_carrier']) || $_POST['date_received_carrier'] == "") {
            $this->input['date_received_carrier'] = date('m/d/Y');
        } else {
            $this->input['date_received_carrier'] = $_POST['date_received_carrier'];
        }

        $this->form->TextField('date_received_carrier', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");

        $from_to_options_terminal = array(
            '' => 'Select One',
            Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
            Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
            Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
            Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company',
        );

        $this->form->TextField('amount_terminal', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
        $this->form->ComboBox('from_to_terminal', $from_to_options_terminal, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
        $this->form->TextField('date_received_terminal', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
        $this->form->TextField("cc_fname", 50, array("elementname" => "input"), "First Name", "</td><td>");
        $this->form->TextField("cc_lname", 50, array(), "Last Name", "</td><td>");
        $this->form->TextField("cc_address", 255, array(), "Address", "</td><td>");
        $this->form->TextField("cc_city", 100, array(), "City", "</td><td>");
        $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
        $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
        $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
        $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
        $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;", "id" => "cc_type_1"), $this->requiredTxt . "Type", "</td><td>");
        $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
        $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");
        $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));
    }

    protected function getDispatchForm()
    {

        // Mail dialog fields
        $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
        $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
        $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

        // Dispatch Sheet Fields
        $this->form->TextField('carrier_company', 255, array('tabindex' => 10), $this->requiredTxt . "Carrier", "</td><td>");
        $this->form->TextField('carrier_print_name', 255, array('tabindex' => 15), $this->requiredTxt . "Print Check As", "</td><td>");
        $this->form->TextField('carrier_insurance_iccmcnumber', 255, array('tabindex' => 20), $this->requiredTxt . "ICC MC Number", "</td><td>");
        $this->form->TextField('carrier_address', 255, array('tabindex' => 25), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('carrier_city', 255, array('class' => 'geo-city', 'tabindex' => 30), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('carrier_state', array('' => 'Select One') + $this->getStates(), array('style' => 'width:150px', 'tabindex' => 40), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('carrier_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 50), "", "");
        $this->form->ComboBox('carrier_country', $this->getCountries(), array('tabindex' => 60), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('carrier_email', 255, array('tabindex' => 70), $this->requiredTxt . "Email", "</td><td>");
        $this->form->ComboBox('carrier_type', array('' => 'Select One') + Account::$carrier_types, array('style' => 'width:100px', 'tabindex' => 75), "", "");
        $this->form->TextField('carrier_contact', 255, array('tabindex' => 80), "Contact", "</td><td>");
        $this->form->TextField('carrier_phone1', 12, array('class' => 'phone', 'tabindex' => 90), $this->requiredTxt . "Phone (1)", "</td><td>");
        $this->form->TextField('carrier_phone2', 12, array('class' => 'phone', 'tabindex' => 100), "Phone (2)", "</td><td>");
        $this->form->TextField('carrier_fax', 255, array('class' => 'phone', 'tabindex' => 110), "Phone (Fax)", "</td><td>");
        $this->form->TextField('carrier_cell', 255, array('class' => 'phone', 'tabindex' => 120), "Phone (Cell)", "</td><td>");
        $this->form->TextField('carrier_driver_name', 255, array('tabindex' => 130), $this->requiredTxt . "Driver", "</td><td>");
        $this->form->TextField('carrier_driver_phone', 255, array('class' => 'phone', 'tabindex' => 140), $this->requiredTxt . "Driver Phone", "</td><td>");
        $this->form->TextField("carrier_phone1_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("carrier_phone2_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");

        $this->form->ComboBox("insurance_type", array(
            "0" => "--Select Insurance--",
            "3" => "Cargo & Liability",
            "1" => "Cargo Insurance",
            "2" => "Liability Insurance",
        ), array("style" => "width:180px;"), "Insurance Type", "</td><td>");

        $this->form->FileFiled("carrier_ins_doc", array(), "Upload Insurance Certificates", "</td><td>");
        $this->form->TextField("carrier_ins_expire", 10, array('style' => 'width: 100px;', 'tabindex' => 56), "Certificate Expire Date", "</td><td>");
        $this->form->ComboBox('order_load_date_type', array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px', 'tabindex' => 150), $this->requiredTxt . "Load Date", "</td><td>");
        $this->form->TextField('order_load_date', 10, array('style' => 'width: 100px;', 'tabindex' => 160));
        $this->form->ComboBox('order_delivery_date_type', array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px', 'tabindex' => 170), $this->requiredTxt . "Delivery Date", "</td><td>");
        $this->form->TextField('order_delivery_date', 10, array('style' => 'width: 100px;', 'tabindex' => 180));
        $this->form->ComboBox('order_ship_via', Entity::$ship_via_string, array('tabindex' => 190), $this->requiredTxt . "Ship Via", "</td><td>");
        $this->form->TextArea('order_notes_from_shipper', 3, 5, array('style' => 'width:580px;height:70px', 'tabindex' => 215), 'Dispatch Instructions', '</td><td>');
        $this->form->TextArea('payments_terms', 3, 5, array('style' => 'width:215px;height:50px', 'tabindex' => 215), 'Payment Terms', '</td><td>');
        $this->form->CheckBox('order_include_shipper_comment', array('tabindex' => 216), 'Include Shipper Comment on Dispatch Sheet', '&nbsp;');
        $this->form->MoneyField('order_company_owes_carrier', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 220), "Company owes Carrier", "</td><td>");
        $this->form->MoneyField('order_carrier_ondelivery', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 230), "Carrier owes Company", "</td><td>");
        $this->form->MoneyField('order_carrier_pay', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 230), "Carrier Pay (total)", "</td><td>");
        $this->form->ComboBox("order_balance_paid_by", Entity::$balance_paid_by_string, array('tabindex' => 240), $this->requiredTxt . "Balance Paid By", "</td><td>");
        $this->form->MoneyField('order_pickup_terminal_fee', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 242), "Pickup Terminal Fee", "</td><td>");
        $this->form->MoneyField('order_dropoff_terminal_fee', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 243), "Delivery Terminal Fee", "</td><td valign='top'>");
        $this->form->TextField('pickup_name', 255, array('style' => 'width:140px;', 'tabindex' => 250), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField('pickup_company', 255, array('style' => 'width:140px;', 'tabindex' => 260), "Company", "</td><td>");
        $this->form->TextField('pickup_address1', 255, array('tabindex' => 270), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('pickup_address2', 255, array('tabindex' => 280), "Address (2)", "</td><td>");
        $this->form->TextField('pickup_city', 255, array('class' => 'geo-city', 'tabindex' => 290), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('pickup_state', $this->getStates(), array('style' => 'width:160px', 'tabindex' => 300), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('pickup_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 310), "", "");
        $this->form->ComboBox('pickup_country', $this->getCountries(), array('tabindex' => 320), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('pickup_phone1', 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 330), $this->requiredTxt . "Phone 1", "</td><td>");
        $this->form->TextField('pickup_phone2', 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 340), "Phone 2", "</td><td>");
        $this->form->TextField('pickup_cell', 32, array('style' => 'width:140px;', 'style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 350), "Cell", "</td><td>");
        $this->form->TextField("from_booking_number", 20, array('style' => 'width:140px;', 'tabindex' => 24), "Booking Number", "</td><td>");
        $this->form->TextField("from_buyer_number", 20, array('style' => 'width:140px;', 'tabindex' => 25), "Buyer Number", "</td><td>");
        $this->form->TextField('pickup_phone4', 12, array('style' => 'width:140px;', 'style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 340), "Phone 4", "</td><td>");
        $this->form->TextField('pickup_cell2', 32, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 350), "Cell2", "</td><td>");
        $this->form->TextField("pickup_fax2", 20, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 25), "Fax2", "</td><td>");
        $this->form->TextField("pickup_phone1_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("pickup_phone2_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("pickup_phone3_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("pickup_phone4_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField('deliver_name', 255, array('style' => 'width:140px;', 'tabindex' => 360), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField('deliver_company', 255, array('style' => 'width:140px;', 'tabindex' => 370), "Company", "</td><td>");
        $this->form->TextField('deliver_address1', 255, array('tabindex' => 380), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('deliver_address2', 255, array('tabindex' => 390), "Address (2)", "</td><td>");
        $this->form->TextField('deliver_city', 255, array('class' => 'geo-city', 'tabindex' => 400), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('deliver_state', $this->getStates(), array('style' => 'width:160px', 'tabindex' => 410), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('deliver_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 420), "", "");
        $this->form->ComboBox('deliver_country', $this->getCountries(), array('tabindex' => 430), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('deliver_phone1', 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 440), $this->requiredTxt . "Phone 1", "</td><td>");
        $this->form->TextField('deliver_phone2', 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 450), "Phone 2", "</td><td>");
        $this->form->TextField('deliver_cell', 32, array('style' => 'width:140px;', 'style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 460), "Cell", "</td><td>");
        $this->form->TextField("to_booking_number", 20, array('style' => 'width:140px;', 'tabindex' => 24), "Booking Number", "</td><td>");
        $this->form->TextField("to_buyer_number", 20, array('style' => 'width:140px;', 'tabindex' => 25), "Buyer Number", "</td><td>");
        $this->form->TextField('deliver_phone4', 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 450), "Phone 4", "</td><td>");
        $this->form->TextField('deliver_cell2', 32, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 460), "Cell2", "</td><td>");
        $this->form->TextField("deliver_fax2", 20, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 25), "Fax2", "</td><td>");
        $this->form->TextField("deliver_phone1_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("deliver_phone2_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("deliver_phone3_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
        $this->form->TextField("deliver_phone4_ext", 32, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), "", "</td><td>");
    }

    protected function getEditForm($referrer_status = 0, $ostatus = -3)
    {
        $readonly = 'readonly';
        $disabled = 'disabled';
        if ($_SESSION['member']['access_dispatch_orders'] == 1
            || $ostatus == Entity::STATUS_ACTIVE
            || $ostatus == Entity::STATUS_ONHOLD
            || $ostatus == Entity::STATUS_POSTED
            || $ostatus == Entity::STATUS_NOTSIGNED
            || $ostatus == -3
        ) {
            $readonly = '';
            $disabled = '';
        }

        $member = new Member($this->daffny->DB);
        $member->load($_SESSION['member_id']);
        $this->daffny->tpl->isAutoQuoteAlowed = $member->isAutoQuoteAllowed();
        
        /* SHIPPER */
        $this->form->ComboBox("shipper", array("" => "New Shipper"), array('style' => 'width:190px;', "$disabled" => "$disabled"), "Select Shipper", "</td><td>");
        $this->form->TextField("shipper_fname", 32, array('tabindex' => 1, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array('tabindex' => 2, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "Last Name", "</td><td>");
        $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model', "$readonly" => "$readonly"), "Company", "<span class='required' id='shipper_company-span' style='display:none;'>*</span></td><td>");
        $this->form->ComboBox('shipper_type', array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled", 'onChange' => 'typeselected();'), $this->requiredTxt . 'Shipper Type    ', '</td><td>');
        $this->form->TextField("shipper_hours", 200, array('tabindex' => 5, "$readonly" => "$readonly"), "Hours", "</td><td>");
        $this->form->TextField("shipper_email", 100, array('class' => 'email', 'tabindex' => 6, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("shipper_phone1", 14, array('style' => 'width:130px;', 'tabindex' => 7, "class" => "phone elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("shipper_phone2", 14, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 8, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("shipper_mobile", 14, array('class' => 'phone', 'tabindex' => 9, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 14, array('class' => 'phone', 'tabindex' => 10, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("shipper_address1", 64, array('tabindex' => 12, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array('tabindex' => 13, "$readonly" => "$readonly"), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city', 'tabindex' => 14, "$readonly" => "$readonly"), "City", "</td><td>");
        $this->form->ComboBox('shipper_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:130px;', 'tabindex' => 15, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), "State/Zip", "</td><td>", true);
        $this->form->TextField("shipper_zip", 8, array('style' => 'width:70px;margin-left:7px;', 'class' => 'zip', 'tabindex' => 16, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("shipper_country", $this->getCountries(), array('tabindex' => 17, "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->TextField("account_payble_contact", 32, array('tabindex' => 18, "$readonly" => "$readonly"), "Account Payble Contact", "</td><td id='account_payble_contact_div'>");
        
        /* ORIGIN */
        $this->form->TextField("origin_address1", 255, array('tabindex' => 18, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("origin_address2", 255, array('tabindex' => 19, "$readonly" => "$readonly"), "&nbsp;", "</td><td>");
        $this->form->TextField("origin_city", 255, array('class' => 'geo-city', 'tabindex' => 20, "elementname" => "input", "class" => "elementname", "$readonly" => "$readonly"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('origin_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:117px;margin-right: 13px;', 'tabindex' => 21, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 10, array('style' => 'width:60px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 22, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array('tabindex' => 23, "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->ComboBox('origin_type', array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 24, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled", 'onChange' => 'origintypeselected();'), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("origin_hours", 200, array('tabindex' => 25, "$readonly" => "$readonly"), "Hours", "<span class='required' id='origin_hour' style='display:none;'>*</span></td><td>");
        
        /* ORIGIN CONTACT */
        $this->form->CheckBox("origin_use_as_contact", array('style' => 'width:140px;'), "Use as contact", "&nbsp;");
        $this->form->TextField("origin_contact_name", 255, array('style' => 'width:140px;', 'tabindex' => 26, "$readonly" => "$readonly"), "Contact Name", "</td><td>");
        $this->form->TextField("origin_contact_name2", 255, array('style' => 'width:140px;', 'tabindex' => 27, "$readonly" => "$readonly"), "Contact Name 2", "</td><td>");
        $this->form->TextField("origin_company_name", 255, array('style' => 'width:140px;', 'tabindex' => 28, "$readonly" => "$readonly"), "Company Name", "<span class='required' id='origin_company-span' style='display:none;'>*</span></td><td>");
        $this->form->TextField("origin_auction_name", 255, array('style' => 'width:140px;', 'tabindex' => 29, "$readonly" => "$readonly"), "Auction Name", "<span class='required' id='origin_auction-span' style='display:none;'>*</span></td><td>");
        $this->form->TextField("origin_booking_number", 100, array('style' => 'width:140px;', 'tabindex' => 30, "$readonly" => "$readonly"), "Booking Number", "</td><td>");
        $this->form->TextField("origin_buyer_number", 100, array('style' => 'width:140px;', 'tabindex' => 31, "$readonly" => "$readonly"), "Buyer Number", "</td><td>");
        $this->form->TextField("origin_phone1", 14, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 32, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("origin_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 32, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone2", 14, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 33, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("origin_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 33, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone3", 14, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 34, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("origin_phone3_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 34, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone4", 14, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 34, "$readonly" => "$readonly"), "Phone 4", "</td><td>");
        $this->form->TextField("origin_phone4_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 34, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_mobile", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 35, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("origin_mobile2", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 35, "$readonly" => "$readonly"), "Mobile2", "</td><td>");
        $this->form->TextField("origin_fax", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 36, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("origin_fax2", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 37, "$readonly" => "$readonly"), "Fax2", "</td><td>");

        /* DESTINATION */ 
        $this->form->TextField("destination_address1", 255, array('tabindex' => 37, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("destination_address2", 255, array('tabindex' => 38, "$readonly" => "$readonly"), "&nbsp;", "</td><td>");
        $this->form->TextField("destination_city", 255, array('class' => 'geo-city', 'tabindex' => 39, "elementname" => "input", "class" => "elementname", "$readonly" => "$readonly"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:117px;margin-right: 13px;', 'tabindex' => 40, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 10, array('style' => 'width:60px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 41, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array('tabindex' => 42, "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->ComboBox('destination_type', array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 43, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("destination_hours", 200, array('tabindex' => 44, "$readonly" => "$readonly"), "Hours", "</td><td>");
        
        /* DESTINATION CONTACT */
        $this->form->CheckBox("destination_use_as_contact", array('style' => 'width:140px;'), "Use as contact", "&nbsp;");
        $this->form->TextField("destination_contact_name", 255, array('style' => 'width:140px;', 'tabindex' => 45, "$readonly" => "$readonly"), "Contact Name", "</td><td>");
        $this->form->TextField("destination_contact_name2", 255, array('style' => 'width:140px;', 'tabindex' => 46, "$readonly" => "$readonly"), "Contact Name 2", "</td><td>");
        $this->form->TextField("destination_company_name", 255, array('style' => 'width:140px;', 'tabindex' => 47, "$readonly" => "$readonly"), "Company Name", "</td><td>");
        $this->form->TextField("destination_auction_name", 255, array('style' => 'width:140px;', 'tabindex' => 48, "$readonly" => "$readonly"), "Auction Name", "</td><td>");
        $this->form->TextField("destination_booking_number", 100, array('style' => 'width:140px;', 'tabindex' => 49, "$readonly" => "$readonly"), "Booking Number", "</td><td>");
        $this->form->TextField("destination_buyer_number", 100, array('style' => 'width:140px;', 'tabindex' => 50, "$readonly" => "$readonly"), "Buyer Number", "</td><td>");
        $this->form->TextField("destination_phone1", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 51, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("destination_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 51, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone2", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 52, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("destination_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 52, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone3", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 53, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("destination_phone3_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 53, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone4", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 53, "$readonly" => "$readonly"), "Phone 4", "</td><td>");
        $this->form->TextField("destination_phone4_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 53, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_mobile", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 54, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("destination_mobile2", 14, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 54, "$readonly" => "$readonly"), "Mobile2", "</td><td>");
        $this->form->TextField("destination_fax", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 55, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("destination_fax2", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 55, "$readonly" => "$readonly"), "Fax2", "</td><td>");

        /* SHIPPING INFORMATION */
        $this->form->TextField("avail_pickup_date", 10, array('tabindex' => 56, "$readonly" => "$readonly"), $this->requiredTxt . "1st Avail. Pickup Date", "</td><td>");
        $this->form->ComboBox("load_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 57, "$disabled" => "$disabled"), "Load Date", "</td><td>");
        $this->form->TextField("load_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 58, "$readonly" => "$readonly"));
        $this->form->ComboBox("delivery_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 59, "$disabled" => "$disabled"), "Delivery Date", "</td><td>");
        $this->form->TextField("delivery_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 60, "$readonly" => "$readonly"));
        $this->form->ComboBox("shipping_vehicles_run", array('' => 'Select One') + Entity::$vehicles_run_string, array('tabindex' => 61, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "Vehicle(s) Run", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", array('' => 'Select One') + Entity::$ship_via_string, array('tabindex' => 62, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "Ship Via", "</td><td valign=\"top\">");

        $match_carrier = array('tabindex' => 65);
        if (isset($this->input['match_carrier']) && $this->input['match_carrier'] == "1") {
            $match_carrier["checked"] = "checked";
        }

        $this->form->CheckBox("match_carrier", $match_carrier, "Automatically match carrier on this route.", "&nbsp;");
        $this->form->TextArea("notes_for_shipper", 2, 10, array('style' => 'height:100px;', "onkeyup" => "countChar(this)", "maxlength" => "60", 'tabindex' => 63, "$disabled" => "$disabled"), 'Add special Note to appeared on FreightBoard <i class="fas fa-info-circle" style="padding-top:10px; padding-left:0px; color:#646b99;" data-toggle="tooltip" title="FreightBoard : (  Please enter KEY information that the driver should know. Simple &amp; Sweet! )"></i> ', "</td><td>");
        $this->form->TextArea("notes_from_shipper", 2, 10, array('style' => 'height:100px;', 'tabindex' => 64, "$disabled" => "$disabled"), 'Special Dispatch Instructions <i class="fas fa-info-circle" style="padding-top:10px; padding-left:0px; color:#646b99;" data-toggle="tooltip" title="FreightBoard : (  Please enter KEY information that the driver should know. Simple &amp; Sweet! )"></i> ', "</td><td>");
        $shipper_comment_attr = array('tabindex' => 65);

        if (isset($this->input['include_shipper_comment']) && $this->input['include_shipper_comment'] == "1") {
            $shipper_comment_attr["checked"] = "checked";
        }

        $this->form->CheckBox("include_shipper_comment", $shipper_comment_attr, "Include Shipper Comment on Dispatch Sheet", "&nbsp;");

        /* PRICING INFORMATION */
        $balance_paid_by = array(
            '' => 'Select One',
            'Cash on Delivery to Carrier' => array(
                Entity::BALANCE_COD_TO_CARRIER_CASH => 'COD - Cash/Certified Funds',
                Entity::BALANCE_COD_TO_CARRIER_CHECK => 'COD - Check',
            ),
            'Cash on Pickup to Carrier' => array(
                Entity::BALANCE_COP_TO_CARRIER_CASH => 'COP - Cash/Certified Funds',
                Entity::BALANCE_COP_TO_CARRIER_CHECK => 'COP - Check',
            ),
            'Broker is paying Carrier' => array(
                Entity::BALANCE_COMPANY_OWES_CARRIER_CASH => 'Billing - Cash/Certified Funds',
                Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK => 'Billing - Check',
                Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => 'Billing - Comcheck',
                Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => 'Billing - QuickPay',
            ),
            'Carrier is paying Broker' => array(
                Entity::BALANCE_CARRIER_OWES_COMPANY_CASH => 'Invoice - Cash/Certified Funds',
                Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK => 'Invoice - Check',
                Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => 'Invoice - Comcheck',
                Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => 'Invoice - QuickPay',
            ),
        );
        if ($_SESSION['member']['parent_id'] == 1) {
            $balance_paid_by['Broker is paying Carrier'][Entity::BALANCE_COMPANY_OWES_CARRIER_ACH] = 'Billing - ACH';
        }
        $this->form->ComboBox("balance_paid_by", $balance_paid_by, array('tabindex' => 68, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled", 'onChange' => 'paid_by_ach_selected();'), $this->requiredTxt . "How is the carrier getting paid?", "</td><td>");
        $fee_type = array(
            '0' => 'Select One',
            1 => 'No Fee',
            2 => '3% processing Fee + $12 ACH Fee',
            4 => '3% processing Fee + No ACH Fee',
            3 => '5% processing Fee + $12 ACH Fee',
            5 => '5% processing Fee + No ACH Fee',
        );
        $this->form->ComboBox("fee_type", $fee_type, array('tabindex' => 68, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "Fee Type", "</td><td id='fee_type_div'>");

        /* PAYMENT INFORMATION */
        $costomer_balance_paid_by = array(
            '' => 'Select One',
            Entity::ACH => '1 - ACH',
            Entity::COMPANY_CHECK => '2 - Company Check',
            Entity::CREDIT_CARD => '3 - Credit Card',
            Entity::MONEY_ORDER => '4 - Money Order',
            Entity::PARSONAL_CHECK => '5 - Personal Check',
            Entity::WIRE_TRANSFER => '6 - Wire - Transfer',
        );

        $this->form->ComboBox("customer_balance_paid_by", $costomer_balance_paid_by, array('tabindex' => 69, "elementname" => "select", "class" => "elementname", "onchange" => "selectPayment();", "$disabled" => "$disabled"), $this->requiredTxt . "How is the customer paying us?", "</td><td>");

        $this->form->TextField("pickup_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 66, "$disabled" => "$disabled"), "Pickup Terminal Fee", "$&nbsp;");
        $this->form->TextField("delivery_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 67, "$disabled" => "$disabled"), "Delivery Terminal Fee", "$&nbsp;");

        //Credit Card Information
        $this->form->TextField("e_cc_fname", 50, array('tabindex' => 70, "$disabled" => "$disabled"), "First Name", "</td><td>");
        $this->form->TextField("e_cc_lname", 50, array('tabindex' => 71, "$disabled" => "$disabled"), "Last Name", "</td><td>");
        $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array('tabindex' => 72, "style" => "width:150px;", "$disabled" => "$disabled"), "Type", "</td><td>");

        $this->form->TextField("e_cc_number", 16, array('tabindex' => 73, "class" => "creditcard", "$disabled" => "$disabled"), "Card Number", "</td><td>");
        $this->form->TextField("e_cc_cvv2", 4, array('tabindex' => 74, "class" => "cvv", "style" => "width:75px;", "$disabled" => "$disabled"), "CVV", "</td><td>");
        $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array('tabindex' => 75, "style" => "width:50px;", "$disabled" => "$disabled"), "Exp. Date", "</td><td>");
        $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array('tabindex' => 76, "style" => "width:75px;", "$disabled" => "$disabled"), "", "");

        $this->form->TextField("e_cc_address", 255, array('tabindex' => 77, "$disabled" => "$disabled"), "Address", "</td><td>");
        $this->form->TextField("e_cc_city", 100, array('tabindex' => 78, "$disabled" => "$disabled"), "City", "</td><td>");
        $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array('tabindex' => 79, "style" => "width:150px;", "$disabled" => "$disabled"), "State", "</td><td>");
        $this->form->ComboBox("e_cc_country", $this->getCountries(), array('tabindex' => 79, "style" => "width:150px;", "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->TextField("e_cc_zip", 11, array('tabindex' => 80, "class" => "zip", "style" => "width:70px;", "$disabled" => "$disabled"), "", "");
        $this->form->CheckBox("auto_payment", array(), "Do not process Automatically", "&nbsp;");

        if ($this->input['referred_by'] == "" || $this->input['referred_by'] == 0) {
            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array('' => 'Select One');
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
        } else {
            if ($_SESSION['member_id'] == getParentId()) {
                // Additional
                $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
                $referrers_arr = array('' => 'Select One');
                foreach ($referrers as $referrer) {
                    $referrers_arr[$referrer->id] = $referrer->name;
                }
            } else {
                $referrer_name_value = "";
                if ($this->input['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $this->input['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }
                $referrers_arr = array($this->input['referred_by'] => $referrer_name_value);
            }
        }

        $this->form->ComboBox("referred_by", $referrers_arr, array('tabindex' => 11, "$disabled" => "$disabled"), $this->requiredTxt . "Source", "</td><td>");
        if (is_null($this->input['source_id']) || $this->input['source_id'] == '') {
            // Additional
            $sources = Entity::getSources($this->daffny->DB);
            $sources_arr = array('' => 'Select One');
            foreach ($sources as $source) {
                $sources_arr[$source->id] = $source->company_name;
            }

        } else {
            $source_name_value = "";
            if ($this->input['source_id'] != "") {
                $row_source = $this->daffny->DB->select_one("domain,company_name", "app_leadsources", "WHERE  id = '" . $this->input['source_id'] . "'");
                if (!empty($row_source)) {
                    $source_name_value = $row_source['company_name'];
                }
            }
            $sources_arr = array($this->input['source_id'] => $source_name_value);
        }

        $this->form->ComboBox("source_id", $sources_arr, array('tabindex' => 11, "$disabled" => "$disabled"), $this->requiredTxt . "Sources", "</td><td>");
        $this->form->TextArea("note_to_shipper", 4, 10, array('style' => 'height: 80px; width:1118px;', 'tabindex' => 56, "$disabled" => "$disabled"), "", "</td><td align='center'>");
    }

    protected function checkEditForm($create = false, $referrer_status = 0, $status = -2)
    {
        $sql_arr = $_POST;
        $sql_arr['shipper_email'] = trim($sql_arr['shipper_email']);
        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_lname' => "Shipper Last Name",
            'shipper_email' => "Shipper Email",
            'shipper_phone1' => "Shipper Phone",
            'shipper_type' => "Shipper Type",
            'origin_city' => "Pickup City",
            'origin_country' => 'Pickup Country',
            //'origin_zip' => 'Pickup Zip',
            'origin_type' => 'Origin Location Type',
            'destination_type' => 'Destination Location Type',
            'destination_city' => "Delivery City",
            'destination_country' => 'Delivery Country',
            //'destination_zip' => 'Delivery Zip',
            'avail_pickup_date' => '1st Avail. Pickup Date',
            'shipping_ship_via' => 'Ship Via',
            'balance_paid_by' => 'Balance Paid By',
            'payments_terms' => 'Payment Terms',
        );

        if ($sql_arr['source_id'] != '') {
            $checkEmpty['source_id'] = "Source";
        } else {
            $checkEmpty['referred_by'] = "Referred By";
        }

        if (post_var('balance_paid_by') == Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) {
            $checkEmpty['fee_type'] = "Fee Type";
        }

        $checkEmpty['customer_balance_paid_by'] = "Customer Payment Options";

        if ($create) {
            if (!isset($_POST['year'])) {
                $this->err[] = "You must add at least one vehicle";
            }

            unset($checkEmpty['load_date']);
            unset($checkEmpty['load_date_type']);
            unset($checkEmpty['delivery_date']);
            unset($checkEmpty['delivery_date_type']);
        }
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        // when pickup country is USA and Canada
        if ((trim(post_var("origin_country")) == "US") || (trim(post_var("origin_country")) == "CA")) {
            if ((trim(post_var("origin_state")) == "")) {
                $this->isEmpty('origin_state', "Pickup State");
            }

            if ((trim(post_var("origin_zip")) == "")) {
                $this->isEmpty('origin_zip', "Pickup Zipcode");
            }
        }

        // when destination country is USA and Canada
        if ((trim(post_var("destination_country")) == "US") || (trim(post_var("destination_country")) == "CA")) {
            if ((trim(post_var("destination_state")) == "")) {
                $this->isEmpty('destination_state', "Delivery State");
            }

            if ((trim(post_var("destination_zip")) == "")) {
                $this->isEmpty('destination_zip', "Delivery Zipcode");
            }
        }

        if (post_var("shipper_type") == "Commercial" && trim(post_var("shipper_hours")) == "") {
            $this->err[] = "Field <strong>Hours</strong> is mandatory for Commercial shippers.";
        }

        if (post_var("shipper_type") == "Commercial" && trim(post_var("shipper_company")) == "") {
            $this->err[] = "Field <strong>Company</strong> is mandatory for Commercial shippers.";
        }

        if (post_var("origin_type") == "Commercial" && trim(post_var("origin_company_name")) == "") {
            $this->err[] = "Field <strong>Company</strong> is mandatory for Commercial Origin.";
        }

        if (post_var("destination_type") == "Commercial" && trim(post_var("destination_company_name")) == "") {
            $this->err[] = "Field <strong>Company</strong> is mandatory for Commercial Destination.";
        }

        $this->checkEmail('shipper_email', "Shipper E-mail");
        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    public function searchIssue()
    {

        $this->tplname = "orders.main";

        $data_tpl = "orders.ordersissues";

        $this->applyOrder("app_order_header e");
        $this->order->Fields[] = 'shipperfname';
        $this->order->Fields[] = 'Origincity';
        $this->order->Fields[] = 'Destinationcity';
        $this->order->Fields[] = 'avail_pickup_date';
        $this->order->Fields[] = 'dispatched';
        $this->order->Fields[] = 'delivered';
        $this->order->Fields[] = 'posted';
        $this->order->Fields[] = 'not_signed';
        $this->order->Fields[] = 'issue_date';
        $this->order->Fields[] = 'hold_date';
        $this->order->Fields[] = 'created';

        $issue_type = $_GET['issue_type'];

        $info = "Show order listing: " . $this->title;
        $applog = new Applog($this->daffny->DB);
        $applog->createInformation($info);

        $entityManager = new EntityManager($this->daffny->DB);
        $orderStr = null;
        if ($this->order->CurrentOrder) {
            $orderStr = " Order by e." . $this->order->CurrentOrder;
        }
        switch ($this->order->CurrentOrder) {
            case 'created':
                break;
        }

        $dispatch_pickup_date = date('Y-m-d');
        if (isset($_POST['dispatch_pickup_date'])) {
            $dispatch_pickup_date = $_POST['dispatch_pickup_date'];
        }

        $todayDispatched = $entityManager->getDispatchedOrders(Entity::TYPE_ORDER, $dispatch_pickup_date);
        $this->daffny->tpl->todayDispatched = $todayDispatched;

        $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_ORDER, $this->order->getOrder(), Entity::STATUS_ISSUES, $_SESSION['per_page'], '', '', $issue_type);
        $entities_count = $entityManager->getCountHeader(Entity::TYPE_ORDER);

        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal
            , 'records_totaldisp' => $dispactched_ord_today,
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
        $this->input['issue_type'] = $issue_type;
        $this->form->ComboBox('issue_type', array('' => 'Issues Sort by', '1' => 'Customer Owe Us', '2' => 'We Owe the Carrier'), array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "makeActionType();"), 'Filter', '</td><td>');
        $this->input['dispatch_pickup_date'] = $dispatch_pickup_date;
        $this->form->TextField("dispatch_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, ""), "", "</td><td>");

        $this->getDispatchForm();
        $this->getPaymentForm();
    }

    public function uploads()
    {
        $ID = (int) get_var("id");
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            throw new UserException("Invalid Order ID");
        }

        $this->tplname = "orders.uploads";
        $this->title = "Documents";
        $entity = new Entity($this->daffny->DB);
        $entity->load($_GET['id']);
        $this->daffny->tpl->entity = $entity;
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", getLink("orders/show/id/" . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Documents"));

        $this->daffny->tpl->files = $this->getFiles($ID);
        $this->form->FileFiled("files_upload", array(), "Upload", "</td><td>");

        $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
        $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");
    }

    public function upload_file()
    {
        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "entity/";
        $upload->max_file_size = 50 * 1024 * 1024;
        $upload->form_field = "file";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "tiff", "wpd");
        $upload->save_as_file_name = md5(time() . "-" . rand()) . time();
        $upload->upload_process();

        switch ($upload->error_no) {
            case 0:
                {
                    //check storage space
                    $license = new License($this->daffny->DB);
                    $license->loadCurrentLicenseByMemberId(getParentId());
                    $space = $license->getCurrentStorageSpace();
                    $used = $license->getUsedStorageSpace();

                    if ($used > $space) {
                        die("ERROR:Storage space exceeded.");
                    } else {

                        $sql_arr = array(
                            'name_original' => $_FILES[$upload->form_field]['name'],
                            'name_on_server' => $upload->save_as_file_name,
                            'size' => $_FILES[$upload->form_field]['size'],
                            'type' => $upload->file_extension,
                            'date_uploaded' => "now()",
                            'owner_id' => getParentId(),
                            'status' => 0,
                        );
                        $ins_arr = $this->daffny->DB->PrepareSql("app_uploads", $sql_arr);
                        $this->daffny->DB->insert("app_uploads", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();

                        $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $id, "upload_id" => $insid));

                        $out = getFileImageByType($upload->file_extension) . " ";
                        $out .= '<a href="' . getLink("orders", "getdocs", "id", $insid) . '">' . $_FILES[$upload->form_field]['name'] . '</a>';
                        $out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") ";
                        $out .= '&nbsp;&nbsp;<a href="#" onclick="sendFile(\'' . $insid . '\', \'' . $sql_arr['name_original'] . '\')">Email</a>';
                        $out .= "&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"return deleteFile('" . getLink("orders", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
                        die("<li id=\"file-" . $insid . "\">" . $out . "</li>");
                    }
                }
            case 1:
                die("ERROR:File not selected or empty.");
            case 2:
            case 5:
                die("ERROR:Invalid File Extension");
            case 3:
                die("ERROR:File too big");
            case 4:
                die("ERROR:Cannot move uploaded file");
        }
        exit;
    }

    public function delete_file()
    {
        $out = array('success' => false);
        $id = (int) get_var('id');
        try {
            if ($row = $this->daffny->DB->selectRow('*', "app_uploads", "WHERE id = '$id' AND owner_id = '" . getParentId() . "'")) {
                if ($this->daffny->DB->isError) {
                    throw new Exception($this->getDBErrorMessage());
                } else {
                    $file_path = UPLOADS_PATH . "entity/" . $row["name_on_server"];
                    $this->daffny->DB->delete('app_uploads', "id = '" . $id . "'");
                    $this->daffny->DB->delete('app_entity_uploads', "upload_id = '" . $id . "'");
                    $out = array('success' => true);
                    @unlink($file_path);
                }
            }
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
        }
        die(json_encode($out));
    }

    protected function getFiles($id)
    {
        $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
                 ORDER BY u.date_uploaded";
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
        if (!empty($file)) {

            $file_path = UPLOADS_PATH . "entity/" . $file["name_on_server"];
            $file_name = $file["name_original"];
            $file_size = $file["size"];
            if (file_exists($file_path)) {
                if (strtolower($file["type"]) == "pdf") {
                    header("Content-Type: application/pdf; filename=\"" . $file_name . "\"");
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

    public function import()
    {
        $this->tplname = "orders.import";
        $this->title = "Import Orders";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Import'));
        $companyMembers = $this->getCompanyMembers();
        $membersData = array();
        foreach ($companyMembers as $member) {
            $membersData[$member->id] = $member->contactname;
        }
        $this->form->ComboBox("assigned_id", $membersData, array('style' => 'width:185px;'), "Assign Orders to", "</td><td>");
        if (count($_FILES)) {
            $upload = new upload();
            $upload->out_file_dir = UPLOADS_PATH . "entity/";
            $upload->max_file_size = 50 * 1024 * 1024;
            $upload->form_field = "import";
            $upload->make_script_safe = 1;
            $upload->allowed_file_ext = array("xls", "xlsx", "csv");
            $upload->save_as_file_name = md5(time() . mt_rand()) . '_import';
            $upload->upload_process();
            try {
                switch ($upload->error_no) {
                    case 0:
                        $import = new Import();
                        $result = $import->importOrders($upload->saved_upload_name, post_var('assigned_id'), $this->daffny->DB);
                        $this->input['success'] = $result['success'];
                        $this->input['failed'] = $result['failed'];
                        break;
                    case 1:
                        throw new RuntimeException('File not selected or empty.');
                    case 2:
                    case 5:
                        throw new RuntimeException('Invalid File Extension');
                    case 3:
                        throw new RuntimeException('File too big');
                    case 4:
                        throw new RuntimeException('Cannot move uploaded file');
                    default:

                }
            } catch (RuntimeException $e) {
                if (file_exists($upload->saved_upload_name)) {
                    unlink($upload->saved_upload_name);
                }
                die('ERROR: ' . $e->getMessage());
            }
        }
    }

    public function duplicateOrder()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            $info = "Duplicate Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->DB->transaction();

            $payments_terms = $entity->getTermMSG();

            // Create Entity
            $entityNew = new Entity($this->daffny->DB);
            $insert_arr = array(
                'type' => $entity->type,
                'creator_id' => $entity->creator_id,
                'assigned_id' => $entity->assigned_id,
                'email_id' => $entity->email_id,
                'source_id' => $entity->source_id,
                'before_assigned_id' => $entity->before_assigned_id,
                'carrier_id' => $entity->carrier_id,
                'received' => $entity->received,
                'distance' => $entity->distance,
                'est_ship_date' => $entity->est_ship_date,
                'avail_pickup_date' => $entity->avail_pickup_date,
                'actual_pickup_date' => $entity->actual_pickup_date,
                'vehicles_run' => $entity->vehicles_run,
                'ship_via' => $entity->ship_via,
                'salesrepid' => $entity->salesrepid,
                'referred_by' => $entity->referred_by,
                'referred_id' => $entity->referred_id,
                'account_id' => $entity->account_id,
                'buyer_number' => $entity->buyer_number,
                'booking_number' => $entity->booking_number,
                'status' => Entity::STATUS_ACTIVE,
                'status_update' => $entity->status_update,
                'created' => date("Y-m-d H:i:s"),
                'quoted' => $entity->quoted,
                'ordered' => date("Y-m-d H:i:s"),
                'information' => $entity->information,
                'include_shipper_comment' => $entity->include_shipper_comment,
                'balance_paid_by' => $entity->balance_paid_by,
                'is_reimbursable' => $entity->is_reimbursable,
                'pickup_terminal_fee' => $entity->pickup_terminal_fee,
                'dropoff_terminal_fee' => $entity->dropoff_terminal_fee,
                'total_tariff_stored' => $entity->total_tariff_stored,
                'carrier_pay_stored' => $entity->carrier_pay_stored,
                'is_firstfollowup' => $entity->is_firstfollowup,
                'customer_balance_paid_by' => $entity->customer_balance_paid_by,
                'parentid' => $entity->parentid,
                'payments_terms' => $payments_terms,
            );
            $entityNew->create($insert_arr);

            $shipper = $entity->getShipper();
            // Create Shipper
            $shipperNew = new Shipper($this->daffny->DB);
            $insert_arr = array(
                'fname' => $shipper->fname,
                'lname' => $shipper->lname,
                'email' => $shipper->email,
                'company' => $shipper->company,
                'phone1' => $shipper->phone1,
                'phone2' => $shipper->phone2,
                'mobile' => $shipper->mobile,
                'fax' => $shipper->fax,
                'address1' => $shipper->address1,
                'address2' => $shipper->address2,
                'state' => $shipper->state,
                'zip' => $shipper->zip,
                'country' => $shipper->country,
                'city' => $shipper->city,
                'shipper_type' => $shipper->shipper_type,
                'shipper_hours' => $shipper->shipper_hours,
                'created' => $shipper->created,

            );

            $shipperNew->create($insert_arr, $entityNew->id);

            $origin = $entity->getOrigin();
            // Create Origin
            $originNew = new Origin($this->daffny->DB);
            $insert_arr = array(
                'address1' => $origin->address1,
                'address2' => $origin->address2,
                'city' => $origin->city,
                'state' => $origin->state,
                'zip' => $origin->zip,
                'country' => $origin->country,
                'name' => $origin->name,
                'auction_name' => $origin->auction_name,
                'company' => $origin->company,
                'phone1' => $origin->phone1,
                'phone2' => $origin->phone2,
                'phone3' => $origin->phone3,
                'phone_cell' => $origin->phone_cell,
                'company' => $origin->company,
                'name2' => $origin->name2,
                'booking_number' => $origin->booking_number,
                'buyer_number' => $origin->buyer_number,
                'fax' => $origin->fax,
                'location_type' => $origin->location_type,
                'hours' => $origin->hours,
                'created' => $origin->created,
            );
            $originNew->create($insert_arr, $entityNew->id);

            $destination = $entity->getDestination();
            // Create Destination
            $destinationNew = new Destination($this->daffny->DB);
            $insert_arr = array(
                'address1' => $destination->address1,
                'address2' => $destination->address2,
                'city' => $destination->city,
                'state' => $destination->state,
                'zip' => $destination->zip,
                'country' => $destination->country,
                'name' => $destination->name,
                'company' => $destination->company,
                'phone1' => $destination->phone1,
                'phone2' => $destination->phone2,
                'phone3' => $destination->phone3,
                'phone_cell' => $destination->phone_cell,
                'company' => $destination->company,
                'name2' => $destination->name2,
                'booking_number' => $destination->booking_number,
                'buyer_number' => $destination->buyer_number,
                'fax' => $destination->fax,
                'location_type' => $destination->location_type,
                'hours' => $destination->hours,
                'created' => $destination->created,
            );
            $destinationNew->create($insert_arr, $entityNew->id);
            $vehicles = $entity->getVehicles();

            foreach ($vehicles as $key => $vehicle) {
                $vehicleNew = new Vehicle($this->daffny->DB);

                $insert_arr = array(
                    'entity_id' => $entityNew->id,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'type' => $vehicle->type,
                    'lot' => $vehicle->lot,
                    'vin' => $vehicle->vin,
                    'plate' => $vehicle->plate,
                    'state' => $vehicle->state,
                    'color' => $vehicle->color,
                    'inop' => $vehicle->inop,
                    'tariff' => $vehicle->tariff,
                    'deposit' => $vehicle->deposit,
                    'carrier_pay' => $vehicle->carrier_pay,
                );
                $vehicleNew->create($insert_arr);
            }

            // Update Entity
            $update_arr = array(
                'shipper_id' => $shipperNew->id,
                'origin_id' => $originNew->id,
                'destination_id' => $destinationNew->id,
                'prefix' => $entityNew->getNewPrefix(),
            );
            $entityNew->update($update_arr);

            $entityCreditCard = $entity->getCreditCard();
            $card_arr = array(
                'entity_id' => $entityNew->id,
                'fname' => $entityCreditCard->fname,
                'lname' => $entityCreditCard->lname,
                'address' => $entityCreditCard->address,
                'city' => $entityCreditCard->city,
                'state' => $entityCreditCard->state,
                'zip' => $entityCreditCard->zip,
                'number' => $entityCreditCard->number,
                'cvv2' => $entityCreditCard->cvv2,
                'type' => $entityCreditCard->type,
                'month' => $entityCreditCard->month,
                'year' => $entityCreditCard->year,

            );

            $creditCard = new EntityCreditcard($this->daffny->DB);
            $creditCard->create($card_arr);

            $accountShipper = $entity->getAccount();
            if ($accountShipper->esigned == 2) {

                $files = $entity->getCommercialFilesShipper($accountShipper->id);
                if (isset($files) && count($files)) {
                    foreach ($files as $file) {

                        $pos = strpos($file['name_original'], "B2B");
                        if ($pos === false) {} else {
                            $fname = md5(mt_rand() . " " . time() . " " . $entityNew->id);
                            $path = ROOT_PATH . "uploads/entity/" . $fname;
                            $uploadpath = ROOT_PATH . "uploads/accounts/" . $file['name_on_server'];
                            if (copy($uploadpath, $path)) {
                                $ins_arr = array(
                                    'name_original' => "B2B Order Form " . date("Y-m-d H-i-s") . ".pdf",
                                    'name_on_server' => $fname,
                                    'size' => filesize($uploadpath),
                                    'type' => "pdf",
                                    'date_uploaded' => date("Y-m-d H:i:s"),
                                    'owner_id' => $entity->getAssigned()->parent_id,
                                    'status' => 0,
                                    'esigned' => 2,
                                );

                                $this->daffny->DB->insert("app_uploads", $ins_arr);
                                $ins_id = $this->daffny->DB->get_insert_id();

                                $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $entityNew->id, "upload_id" => $ins_id));
                                // Update Entity
                                $update_arr = array(
                                    'esigned' => 2,
                                );
                                $entityNew->update($update_arr);

                            }
                        }
                    }
                }
            }

            $this->daffny->DB->transaction("commit");
            $entityNew->updateHeaderTable();
            redirect(getLink('orders/edit/id/' . $entityNew->id));
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function searchall()
    {
        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->initGlobals();
            $this->tplname = "orders.main";
            $data_tpl = "orders.orders_search_all";
            $this->title = "Orders search results";
            $this->daffny->tpl->status = "Archived";

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));
            if (!isset($_POST['orders_search_combo'])) {
                $this->daffny->tpl->entities = $entityManager->search_lead_quote_order(Entity::TYPE_ORDER, $_POST['search_type'], $_POST['search_string'], $_SESSION['per_page']);
            } else {
                $this->daffny->tpl->entities = $entityManager->search_lead_quote_order(Entity::TYPE_ORDER, $_POST['search_type'], $_POST['search_string'], $_SESSION['per_page'], null, $_POST['orders_search_combo']);
            }

            $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
            $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
            $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            // Mail dialog fields
            $this->form->TextField("mail_to_new_quote", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new_quote", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new_quote", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

            $status_name = array(
                '-1' => "All",
                Entity::STATUS_ACTIVE => "Active",
                Entity::STATUS_ONHOLD => "OnHold",
                Entity::STATUS_ARCHIVED => "Archived",
                Entity::STATUS_POSTED => "Posted to FD",
                Entity::STATUS_NOTSIGNED => "Not Signed",
                Entity::STATUS_DISPATCHED => "Dispatched",
                Entity::STATUS_ISSUES => "Issues",
                Entity::STATUS_PICKEDUP => "Picked Up",
                Entity::STATUS_DELIVERED => "Delivered",
            );

            if (!isset($_POST['orders_search_combo'])) {
                $this->input['orders_search_combo'] = -1;
            } else {
                $this->input['orders_search_combo'] = $_POST['orders_search_combo'];
            }

            $this->form->ComboBox('orders_search_combo', array('' => 'Select One') + $status_name, array('style' => 'width:130px;', 'onChange' => 'searchAll();'), "", "", true);

            $this->getPaymentForm();

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("orders"));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function batch()
    {
        try {
            $this->tplname = "orders.batch";
            $this->title = "Batch Payments";
            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");
            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function batchsubmit()
    {

        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->initGlobals();
            $this->tplname = "orders.orders_batch";
            $data_tpl = "orders.orders_batch";
            $this->title = "Batch Payments";
            $this->daffny->tpl->status = "Archived";

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));
            $this->daffny->tpl->entities = $entityManager->searchBatch(Entity::TYPE_ORDER, $_POST['shipper_company'], $_POST['batch_order_ids'], $_SESSION['per_page']);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $batch_order_ids_arr = explode(",", trim($_POST['batch_order_ids']));
            $this->daffny->tpl->batch_order_ids_arr = $batch_order_ids_arr;

            if ($_POST['submit'] == "Start Processing") {
                $this->getPaymentForm();
            }

            $this->input['batch_order_ids'] = $_POST['batch_order_ids'];
            $this->input['shipper_company'] = $_POST['shipper_company'];
            $this->input['shipper_company_id'] = $_POST['shipper_company_id'];
            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");
            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            echo $this->daffny->DB->errorQuery;
            die("<br>MySQL Error");
            //redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }

    }

    public function batchconfirm()
    {

        try {
            $this->initGlobals();
            $this->tplname = "orders.orders_batch_confirm";
            $data_tpl = "orders.orders_batch_confirm";
            $this->title = "Batch Payments Confirmation";
            $this->daffny->tpl->status = "Archived";

            $info = "Batch Payments Confirmation";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Batch Payments Confirmation'));
            if ($_GET['ids'] != '') {
                $this->daffny->tpl->entities = $entityManager->searchBatchConfirm(Entity::TYPE_ORDER, $_GET['ids'], $_SESSION['per_page']);

                $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
                $this->section = "Orders";
                $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

                $ids_arr = explode(",", trim($_GET['ids']));
                $this->daffny->tpl->batch_order_ids_arr = $ids_arr;
            }

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }

    }

    public function dispatchnew()
    {
        try {
            
            if (isset($_GET['acc']) && ctype_digit((string) $_GET['acc'])) {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_GET['acc']);
                if ($_SESSION['member']['parent_id'] == $accountData->owner_id) {
                    //nothing to do
                } else {
                    $existing = $this->daffny->DB->select(" `id` ", "app_accounts", " WHERE owner_id = '" . $_SESSION['member']['parent_id'] . "' AND copy_owner_id = '" . $accountData->owner_id . "' AND contact_name1 = '" . $accountData->contact_name1 . "' AND phone1 = '" . $accountData->phone1 . "' ORDER BY ID DESC LIMIT 1");
                    $existsData = mysqli_fetch_assoc($existing);
                    if ($existing->num_rows) {
                        $_GET['acc'] = $existsData['id'];
                    } else {
                        $carrier_arrTemp = array(
                            "member_id" => $accountData->member_id,
                            "owner_id" => $_SESSION['member']['parent_id'],
                            "copy_owner_id" => $accountData->owner_id,
                            "is_carrier" => 1,
                            "carrier_type" => $accountData->carrier_type,
                            "company_name" => $accountData->company_name,
                            "contact_name1" => $accountData->contact_name1,
                            "phone1" => $accountData->phone1,
                            "phone2" => $accountData->phone2,
                            "phone1_ext" => $accountData->phone1_ext,
                            "phone2_ext" => $accountData->phone2_ext,
                            "cell" => $accountData->cell,
                            "fax" => $accountData->fax,
                            "email" => $accountData->email,
                            "address1" => $accountData->address1,
                            "city" => $accountData->city,
                            "state" => $accountData->state,
                            "zip_code" => $accountData->zip_code,
                            "country" => $accountData->country,
                            "insurance_iccmcnumber" => $accountData->insurance_iccmcnumber,
                            "create_date" => date('Y-m-d H:i:s'),
                            "donot_dispatch" => $accountData->donot_dispatch,
                            "status" => $accountData->status,
                            "print_name" => $accountData->print_name,
                        );
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arrTemp);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $_GET['acc'] = $insid;
                    }
                }
            }

            if (isset($_GET['member']) && ctype_digit((string) $_GET['member'])) {
                $carrierMember = new Member($this->daffny->DB);
                $carrierMember->load($_GET['member']);
                $carrierMemberCompany = $carrierMember->getCompanyProfile();

                $newCarrier = array(
                    "member_id" => $carrierMember->id,
                    "owner_id" => $_SESSION['member']['parent_id'],
                    "copy_owner_id" => 0,
                    "is_carrier" => 1,
                    "carrier_type" => null,
                    "company_name" => $carrierMemberCompany->companyname,
                    "contact_name1" => null,
                    "phone1" => $carrierMemberCompany->dispatch_phone,
                    "phone2" => formatPhone($carrierMemberCompany->phone),
                    "phone1_ext" => null,
                    "phone2_ext" => null,
                    "cell" => formatPhone($carrierMemberCompany->phone_cell),
                    "fax" => $carrierMemberCompany->dispatch_fax,
                    "email" => $carrierMemberCompany->dispatch_email,
                    "address1" => trim($carrierMemberCompany->address1),
                    "city" => $carrierMemberCompany->city,
                    "state" => $carrierMemberCompany->state,
                    "zip_code" => $carrierMemberCompany->zip_code,
                    "country" => $carrierMemberCompany->country,
                    "insurance_iccmcnumber" => $carrierMemberCompany->mc_number,
                    "create_date" => date('Y-m-d H:i:s'),
                    "donot_dispatch" => 0,
                    "status" => 1,
                    "print_name" => null,
                );

                // check if not existing.
                $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE company_name LIKE '.$carrierMemberCompany->companyname);
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();
                    $_GET['acc'] = $insid;
                }
            }

            if (isset($_GET['fsmca']) && ctype_digit((string) $_GET['fsmca'])) {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://saferwebapi.com/v2/mcmx/snapshot/'.$_GET['fsmca']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'x-api-key: 169ebdecadf6464f9aa24b49638877d4',
                ));

                $fsmcaCarriers = curl_exec($curl);
                curl_close($curl);

                $fsmcaCarriers = json_decode($fsmcaCarriers);

                $newCarrier = array(
                    "member_id" => $_SESSION['member']['parent_id'],
                    "owner_id" => $_SESSION['member']['parent_id'],
                    "copy_owner_id" => 0,
                    "is_carrier" => 1,
                    "carrier_type" => null,
                    "company_name" => $fsmcaCarriers->legal_name,
                    "contact_name1" => null,
                    "phone1" => $fsmcaCarriers->phone,
                    "phone2" => formatPhone($fsmcaCarriers->phone),
                    "phone1_ext" => null,
                    "phone2_ext" => null,
                    "cell" => formatPhone($fsmcaCarriers->phone),
                    "fax" => $carrierMemberCompany->dispatch_fax,
                    "email" => $carrierMemberCompany->dispatch_email,
                    "address1" => trim(explode(",",$fsmcaCarriers->mailing_address)[0]),
                    "city" => $carrierMemberCompany->city,
                    "state" => explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[1],
                    "zip_code" => explode("-", explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[3])[0],
                    "country" => "US",
                    "insurance_iccmcnumber" => $_GET['fsmca'],
                    "create_date" => date('Y-m-d H:i:s'),
                    "donot_dispatch" => 0,
                    "status" => 1,
                    "print_name" => $fsmcaCarriers->legal_name,
                );

                $this->daffny->tpl->createMember = "no";
                $cMemExt = $this->daffny->DB->selectRow('id', 'app_company_profile', 'WHERE icc_mc_number LIKE '.$_GET['fsmca'] . ' OR mc_number LIKE '.$_GET['fsmca']);
                if(!isset($cMemExt['id'])){
                    $this->daffny->tpl->createMember = "yes";
                }

                // check if not existing.
                $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE insurance_iccmcnumber LIKE '.$_GET['fsmca']);
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();

                    // TODO:: insert in members and app_company

                    $_GET['acc'] = $insid;
                }
            }

            if (isset($_GET['dot']) && ctype_digit((string) $_GET['dot'])) {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://saferwebapi.com/v2/usdot/snapshot/'.$_GET['dot']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'x-api-key: 169ebdecadf6464f9aa24b49638877d4',
                ));

                $fsmcaCarriers = curl_exec($curl);
                curl_close($curl);

                $fsmcaCarriers = json_decode($fsmcaCarriers);

                $mc = "";
                if($fsmcaCarriers->mc_mx_ff_numbers){
                    $mc = explode("-", $fsmcaCarriers->mc_mx_ff_numbers)[1];
                    $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', "WHERE insurance_iccmcnumber LIKE '".$mc."' OR us_dot LIKE '".$_GET['dot']."'");
                } else {
                    $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', "WHERE  us_dot LIKE '".$_GET['dot']."'");
                }
                
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    // TODO:: insert in members and app_company
                    $newCarrier = array(
                        "member_id" => $_SESSION['member']['parent_id'],
                        "owner_id" => $_SESSION['member']['parent_id'],
                        "copy_owner_id" => 0,
                        "is_carrier" => 1,
                        "carrier_type" => null,
                        "company_name" => $fsmcaCarriers->legal_name,
                        "contact_name1" => null,
                        "phone1" => $fsmcaCarriers->phone,
                        "phone2" => formatPhone($fsmcaCarriers->phone),
                        "phone1_ext" => null,
                        "phone2_ext" => null,
                        "cell" => formatPhone($fsmcaCarriers->phone),
                        "fax" => $carrierMemberCompany->dispatch_fax,
                        "email" => $carrierMemberCompany->dispatch_email,
                        "address1" => trim(explode(",",$fsmcaCarriers->mailing_address)[0]),
                        "city" => $carrierMemberCompany->city,
                        "state" => explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[1],
                        "zip_code" => explode("-", explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[3])[0],
                        "country" => "US",
                        "insurance_iccmcnumber" => $mc,
                        "us_dot" => $_GET['dot'],
                        "create_date" => date('Y-m-d H:i:s'),
                        "donot_dispatch" => 0,
                        "status" => 1,
                        "print_name" => $fsmcaCarriers->legal_name,
                    );

                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();
                    $_GET['acc'] = $insid;
                }
            }

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.create_dispatch_new";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            if ( $entity->status == Entity::STATUS_NOTSIGNED || $entity->status == Entity::STATUS_DISPATCHED || $entity->status == Entity::STATUS_ISSUES || $entity->status == STATUS_PICKEDUP || $entity->status == Entity::STATUS_DELIVERED ) {
                throw new UserException("Order Already Dispatched.", getLink('orders/show/id', $entity->id));
            }

            $account_id = 0;
            if (trim($_POST['carrier_id']) != "") {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_POST['carrier_id']);
                $account_id = $accountData->id;

                if ($accountData->insurance_doc_id > 0) {
                    $this->daffny->tpl->filesCargo = $this->getInsuranceCertificate($accountData->insurance_doc_id);
                }
            }
            if (isset($_GET['acc']) && ctype_digit((string) $_GET['acc'])) {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_GET['acc']);
                $account_id = $accountData->id;
                if ($accountData->insurance_doc_id > 0) {
                    $this->daffny->tpl->filesCargo = $this->getInsuranceCertificate($accountData->insurance_doc_id);
                }
            }
            if ($entity->readonly) {
                throw new UserException("Access Denied", getLink('orders'));
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", getLink('orders/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Dispatch Order"));
            $this->title = "Dispatch Order #" . $entity->number;
            $ask_post_to_cd = false;
            $settings = $entity->getAssigned()->getDefaultSettings();

            if ($entity->status == Entity::STATUS_POSTED) {
                if ($settings->central_dispatch_uid != "" && $settings->central_dispatch_post == 1) {
                    $ask_post_to_cd = true;
                }
            }

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditFormNewDispatch(false, $settings->referrer_status, $entity->status)) {

                //pending dispatch clearance
                $entity->update(
                    array(
                        'is_pending_dispatch' => 0,
                    )
                );
                $this->daffny->DB->query("DELETE FROM app_pending_dispatches WHERE entity_id = " . $entity->id);

                // Application Logging
                $info = "Dispatch Order-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                //Insert new carrier
                $carrier_arr = array(
                    "member_id" => getMemberId(),
                    "owner_id" => getParentId(),
                    "is_carrier" => 1,
                    "carrier_type" => (isset($_POST['carrier_type']) && $_POST['carrier_type'] != "") ? $_POST['carrier_type'] : null,
                    "company_name" => $_POST['carrier_company'],
                    "contact_name1" => $_POST['carrier_contact'],
                    "phone1" => str_replace("-", "", $_POST['carrier_phone1']),
                    "phone2" => str_replace("-", "", $_POST['carrier_phone2']),
                    "phone1_ext" => $_POST['carrier_phone1_ext'],
                    "phone2_ext" => $_POST['carrier_phone2_ext'],
                    "cell" => str_replace("-", "", $_POST['carrier_cell']),
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

                if (isset($_POST['save_carrier']) && $_POST['save_carrier'] == 1) {
                    if (isset($account_id) && $account_id != 0) {
                        $carrier = new Account($this->daffny->DB);

                        $carrier->load($account_id);

                        unset($carrier_arr['member_id']);
                        unset($carrier_arr['owner_id']);
                        $carrier->update($carrier_arr, $account_id);

                        if ($_POST['insurance_type'] != '') {
                            $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        }
                    }
                } elseif (!isset($_POST['carrier_id']) || trim($_POST['carrier_id']) == "") {
                    $carrierFlag = $this->validateSaveCarrier($carrier_arr);
                    if ($carrierFlag == 1) {
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arr);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $carrier_id = $insid;
                        $account_id = $insid;

                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        
                    } elseif ($carrierFlag > 1) { // carrier id found
                        $carrier_id = $carrierFlag;
                        $account_id = $carrier_id;
                    }
                } else {
                    $carrierFlag = $this->validateSaveCarrier($carrier_arr, $_POST['carrier_id']);

                    if ($carrierFlag == 1) {
                        $carrier_id = $_POST['carrier_id'];
                        $carrierOld = new Account($this->daffny->DB);
                        $carrierOld->load($carrier_id);
                        $carrier_arr['copy_owner_id'] = $carrierOld->owner_id;
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arr);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $carrier_id = $insid;
                        $account_id = $insid;

                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);

                        if ($_SESSION['member']['parent_id'] == 1) {
                            // nothing to do here
                        }
                    } elseif ($carrierFlag > 1) {
                        // carrier id found
                        $carrier_id = $carrierFlag;
                        $account_id = $carrier_id;
                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        unset($carrier_arr['member_id']);
                        unset($carrier_arr['owner_id']);
                        $carrier->update($carrier_arr, $carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        
                    }
                }
                
                $this->daffny->DB->transaction();
                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                /* UPDATE ORIGIN */

                $origin = $entity->getOrigin();
                if ($sql_arr['pickup_country'] != "US") {
                    $sql_arr['pickup_state'] = $sql_arr['pickup_state2'];
                }

                $update_arr = array(
                    'address1' => $sql_arr['pickup_address1'],
                    'address2' => $sql_arr['pickup_address2'],
                    'city' => $sql_arr['pickup_city'],
                    'state' => $sql_arr['pickup_state'],
                    'zip' => $sql_arr['pickup_zip'],
                    'country' => $sql_arr['pickup_country'],
                    'name' => $sql_arr['pickup_name'],
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['pickup_company'],
                    'phone1' => str_replace("-", "", $sql_arr['pickup_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['pickup_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['pickup_phone4']),
                    'phone1_ext' => $sql_arr['pickup_phone1_ext'],
                    'phone2_ext' => $sql_arr['pickup_phone2_ext'],
                    'phone3_ext' => $sql_arr['pickup_phone3_ext'],
                    'phone4_ext' => $sql_arr['pickup_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['pickup_cell']),
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['from_booking_number'],
                    'buyer_number' => $sql_arr['from_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
                );
                $origin->update($update_arr);

                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['deliver_country'] != "US") {
                    $sql_arr['deliver_state'] = $sql_arr['ddeliver_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['deliver_address1'],
                    'address2' => $sql_arr['deliver_address2'],
                    'city' => $sql_arr['deliver_city'],
                    'state' => $sql_arr['deliver_state'],
                    'zip' => $sql_arr['deliver_zip'],
                    'country' => $sql_arr['deliver_country'],
                    'name' => $sql_arr['deliver_name'],
                    'company' => $sql_arr['deliver_company'],
                    'phone1' => str_replace("-", "", $sql_arr['deliver_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['deliver_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['deliver_phone4']),
                    'phone1_ext' => $sql_arr['deliver_phone1_ext'],
                    'phone2_ext' => $sql_arr['deliver_phone2_ext'],
                    'phone3_ext' => $sql_arr['deliver_phone3_ext'],
                    'phone4_ext' => $sql_arr['deliver_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['deliver_cell']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['deliver_cell2']),
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['from_booking_number'],
                    'buyer_number' => $sql_arr['from_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                    'fax2' => $sql_arr['deliver_fax2'],
                );
                $destination->update($update_arr);

                /* UPDATE NOTE */
                $notes = $entity->getNotes();
                if (count($notes[Note::TYPE_FROM]) != 0) {
                    $note = $notes[Note::TYPE_FROM][0];
                    $note->update(array('text' => $sql_arr['order_notes_from_shipper']));
                } else {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['order_notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
                $update_arr = array(
                    'ship_via' => (int) $sql_arr['order_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['order_load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['order_load_date'])),
                    'load_date_type' => (int) $sql_arr['order_load_date_type'],
                    'delivery_date' => empty($sql_arr['order_delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['order_delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['order_delivery_date_type'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['order_notes_from_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['order_pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['order_dropoff_terminal_fee'],
                    'payments_terms' => $sql_arr['payments_terms_carrier'],
                    'blocked_by' => 'NULL',
                    'blocked_time' => 'NULL',
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );

                $amount = 0;

                if (($sql_arr['balance_paid_by'] != $entity->balance_paid_by) || $entity->delivery_credit != $_POST['fee_type']) {
                    if ($sql_arr['balance_paid_by'] == 24) {
                        // AHC payment methods added
                        if ($_POST['fee_type'] == 1) {
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with no fee';
                        } elseif ($_POST['fee_type'] == 2) {
                            $amount = ((int) $carrier_fee * 0.03) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 3) {
                            $amount = ((int) $carrier_fee * 0.05) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 4) {
                            $amount = ((int) $carrier_fee * 0.03) + 0;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 5) {
                            $amount = ((int) $carrier_fee * 0.05) + 0;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } else {
                            $customNote = 'Invalid Fee Type Selected';
                        }

                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => 3,
                            "text" => $customNote,
                        );
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                }
                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        // update notes
                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }
                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }
                        if ($NotesStr != "") {
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );
                        $vehicleValue->update($insert_arr);
                    }
                }

                // dispatch logic
                $order_load_date = date("Y-m-d", strtotime($_POST['order_load_date']));
                $order_delivery_date = date("Y-m-d", strtotime($_POST['order_delivery_date']));

                $current_time = date("Y-m-d H:i:s");
                if($entity->original_dispatched == ""){
                    // Update Entity
                    $update_arr = array(
                        'carrier_id' => $account_id,
                        'load_date' => $order_load_date,
                        'delivery_date' => $order_delivery_date,
                        'load_date_type' => (int) $_POST['order_load_date_type'],
                        'delivery_date_type' => (int) $_POST['order_delivery_date_type'],
                        'ship_via' => $_POST['order_ship_via'],
                        'dispatched' => $current_time,
                        'original_dispatched' => $current_time,
                        'is_dispatched' => 1,
                        'not_signed' => $current_time,
                     );
                } else {
                    $update_arr = array(
                        'carrier_id' => $account_id,
                        'load_date' => $order_load_date,
                        'delivery_date' => $order_delivery_date,
                        'load_date_type' => (int) $_POST['order_load_date_type'],
                        'delivery_date_type' => (int) $_POST['order_delivery_date_type'],
                        'ship_via' => $_POST['order_ship_via'],
                        'dispatched' => $current_time,
                        'is_dispatched' => 0,
                        'not_signed' => $current_time,
                     );
                }
                
                $entity->update($update_arr);
                // Create Dispatch Sheet
                $company = $entity->getAssigned()->getCompanyProfile();
                $notes = $entity->getNotes();
                if (($entity->include_shipper_comment == 1) && isset($notes[Note::TYPE_FROM][0])) {
                    $instructions = $notes[Note::TYPE_FROM][0]->text;
                } else {
                    $instructions = "";
                }
                $payments_terms_dispatch = $_POST['payments_terms_carrier'];
                if (in_array($entity->balance_paid_by, array(2, 3, 16, 17))) {
                    $payments_terms_dispatch = "COD";
                }
                $entity->getVehicles(true);

                $company_owes_carrier = 0;
                $carrier_owes_company = 0;
                $carrier_pay_total = 0;

                switch ($entity->balance_paid_by) {
                    case Entity::BALANCE_COD_TO_CARRIER_CASH:
                    case Entity::BALANCE_COD_TO_CARRIER_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored;
                        break;
                    case Entity::BALANCE_COP_TO_CARRIER_CASH:
                    case Entity::BALANCE_COP_TO_CARRIER_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored;
                        break;
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                        $company_owes_carrier = $entity->carrier_pay_stored;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = 0;

                        break;
                    case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                    case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = $entity->total_tariff_stored - $entity->carrier_pay_stored;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored + ($entity->total_tariff_stored - $entity->carrier_pay_stored);
                        break;
                }

                // dispatch data to be saved in dispatch sheet
                $insert_arr = array(
                    'entity_id' => $_GET['id'],
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
                    'carrier_id' => (trim($account_id) != "") ? $account_id : "NULL",
                    'carrier_company_name' => $_POST['carrier_company'],
                    'carrier_contact_name' => $_POST['carrier_contact'],
                    'carrier_phone_1' => str_replace("-", "", $_POST['carrier_phone1']),
                    'carrier_phone_2' => str_replace("-", "", $_POST['carrier_phone2']),
                    'carrier_phone1_ext' => $_POST['carrier_phone1_ext'],
                    'carrier_phone2_ext' => $_POST['carrier_phone2_ext'],
                    'carrier_phone_cell' => str_replace("-", "", $_POST['carrier_cell']),
                    'carrier_fax' => $_POST['carrier_fax'],
                    'carrier_driver_name' => $_POST['carrier_driver_name'],
                    'carrier_driver_phone' => str_replace("-", "", $_POST['carrier_driver_phone']),
                    'us_dot' => $_POST['us_dot'],
                    'carrier_address' => $_POST['carrier_address'],
                    'carrier_address_2' => $_POST['carrier_address_2'],
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
                    'entity_carrier_pay' => number_format($carrier_pay_total, 2, '.', ','),
                    'entity_carrier_pay_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "*COD" : (in_array($entity->balance_paid_by, array(8, 9)) ? "*COP" : "")),
                    'entity_odtc' => number_format($carrier_owes_company, 2, '.', ','),
                    'entity_coc' => number_format($company_owes_carrier, 2, '.', ','),
                    'entity_coc_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "after COD is paid" : (in_array($entity->balance_paid_by, array(8, 9)) ? "after COP is paid" : "")),
                    'entity_booking_number' => $_POST['order_booking_number'],
                    'entity_buyer_number' => $_POST['order_buyer_number'],
                    'entity_pickup_terminal_fee' => number_format($entity->pickup_terminal_fee, 2, '.', ','),
                    'entity_dropoff_terminal_fee' => number_format($entity->dropoff_terminal_fee, 2, '.', ','),
                    'entity_balance_paid_by' => $_POST['balance_paid_by'],
                    'information' => $sql_arr['order_notes_from_shipper'],
                    'from_name' => $_POST['pickup_name'],
                    'from_name2' => $_POST['origin_contact_name2'],
                    'from_company' => $_POST['pickup_company'],
                    'from_address' => $_POST['pickup_address1'],
                    'from_address2' => $_POST['pickup_address2'],
                    'from_city' => $_POST['pickup_city'],
                    'from_state' => $_POST['pickup_state'],
                    'from_zip' => $_POST['pickup_zip'],
                    'from_country' => $_POST['pickup_country'],
                    'from_phone_1' => str_replace("-", "", $_POST['pickup_phone1']),
                    'from_phone_2' => str_replace("-", "", $_POST['pickup_phone2']),
                    'from_phone_3' => str_replace("-", "", $_POST['origin_phone3']),
                    'from_phone_4' => str_replace("-", "", $_POST['pickup_phone4']),
                    'from_phone1_ext' => $_POST['pickup_phone1_ext'],
                    'from_phone2_ext' => $_POST['pickup_phone2_ext'],
                    'from_phone3_ext' => $_POST['pickup_phone3_ext'],
                    'from_phone4_ext' => $_POST['pickup_phone4_ext'],
                    'from_phone_cell' => str_replace("-", "", $_POST['pickup_cell']),
                    'from_booking_number' => $_POST['from_booking_number'],
                    'from_buyer_number' => $_POST['from_buyer_number'],
                    'origin_auction_name' => $_POST['origin_auction_name'],
                    'from_phone_cell2' => str_replace("-", "", $_POST['pickup_cell2']),
                    'from_fax' => $_POST['origin_fax'],
                    'from_fax2' => $_POST['pickup_fax2'],
                    'to_name' => $_POST['deliver_name'],
                    'to_name2' => $_POST['destination_contact_name2'],
                    'to_company' => $_POST['deliver_company'],
                    'to_address' => $_POST['deliver_address1'],
                    'to_address2' => $_POST['deliver_address2'],
                    'to_city' => $_POST['deliver_city'],
                    'to_state' => $_POST['deliver_state'],
                    'to_zip' => $_POST['deliver_zip'],
                    'to_country' => $_POST['deliver_country'],
                    'to_phone_1' => str_replace("-", "", $_POST['deliver_phone1']),
                    'to_phone_2' => str_replace("-", "", $_POST['deliver_phone2']),
                    'to_phone_3' => str_replace("-", "", $_POST['destination_phone3']),
                    'to_phone_4' => str_replace("-", "", $_POST['deliver_phone4']),
                    'to_phone1_ext' => $_POST['deliver_phone1_ext'],
                    'to_phone2_ext' => $_POST['deliver_phone2_ext'],
                    'to_phone3_ext' => $_POST['deliver_phone3_ext'],
                    'to_phone4_ext' => $_POST['deliver_phone4_ext'],
                    'to_phone_cell' => str_replace("-", "", $_POST['deliver_cell']),
                    'to_phone_cell2' => str_replace("-", "", $_POST['deliver_cell2']),
                    'to_booking_number' => $_POST['to_booking_number'],
                    'to_buyer_number' => $_POST['to_buyer_number'],
                    'to_auction_name' => $_POST['destination_auction_name'],
                    'to_fax' => $_POST['destination_fax'],
                    'to_fax2' => $_POST['deliver_fax2'],
                    'dispatch_terms' => $company->getDefaultSettings()->dispatch_terms,
                    'hash_link' => $this->getAlmostUniqueHash($_POST['entity_id'], $entity->getNumber()),
                    'payments_terms' => $payments_terms_dispatch,
                    'expired' => date("Y-m-d H:i:s", time() + (7 * 60 * 60 * 24)),
                );

                if (isset($_POST['order_include_shipper_comment'])) {
                    if ($_POST['order_notes_from_shipper'] != "") {
                        $insert_arr['instructions'] = $_POST['order_notes_from_shipper'];

                        /* UPDATE NOTE */
                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => Note::TYPE_FROM,
                            "text" => $_POST['order_notes_from_shipper']);
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                } else {
                    $insert_arr['instructions'] = '';
                }

                $dispatchSheet = new DispatchSheet($this->daffny->DB);
                $dispatch_id = $dispatchSheet->create($insert_arr);

                $vehicleManager = new VehicleManager($this->daffny->DB);
                $vehiclesDis = $vehicleManager->getVehicles($entity->id);
                foreach ($vehiclesDis as $vehicle1) {
                    /* @var Vehicle $vehicle */
                    $vehicle1->cloneForDispatch($dispatch_id);
                }

                $entity->setStatus(Entity::STATUS_NOTSIGNED);

                $sql = "INSERT INTO wallboard (entity_id,agent_name,dispatched_on) VALUES('".$entity->id."','".$entity->assigned_id."','".$current_time."')";
                $this->daffny->DB->query($sql);

                // check settings to send dispatch notification email
                $owner_id = $_SESSION['member']['parent_id'];
                $query = "SELECT dispatch_email_triggers FROM app_defaultsettings WHERE owner_id = " . $owner_id;
                $res = $this->daffny->DB->query($query);
                $emailFlag = mysqli_fetch_assoc($res)['dispatch_email_triggers'];

                if ($emailFlag == 0) {
                    // sending dispatch notification email
                    //$entity->sendOrderDispatched();
                }

                if ($entity->isBlocked()) {
                    $entity->unsetBlock();
                }

                // send dispatch link to carrier
                $dispatch_link = BASE_PATH . "order/dispatchnew/hash/" . $insert_arr["hash_link"];
                $entity->sendDispatchLink(array("dispatch_link" => $dispatch_link));
                
                //send letter to the carrier

                // Add dispatch Notes
                $NotesStr = " has dispatched this order to " . $_POST['carrier_company'] . " for pickup " . $order_load_date . " and drop off on " . $order_delivery_date;
                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                // Send CD
                $this->sendCentralDispatchEmail($entity, 3);
                
                $this->daffny->DB->transaction("commit");
                $entity->make_payment();
                $entity->updateHeaderTable();

                // create member is enabled
                // if($_POST['make_member'] == 'yes'){
                    
                //     $mbr = new Member($this->daffny->DB);
                //     $memberID = $mbr->create([
                //         "email" => $_POST['carrier_email'],
                //         "company_name" => $_POST['carrier_contact'],
                //         "company_name" => $_POST['carrier_company'],
                //         "phone1" => str_replace("-", "", $_POST['carrier_phone1']),
                //         "phone2" => str_replace("-", "", $_POST['carrier_phone2']),
                //         "address1" => $_POST['carrier_address'],
                //         "city" => $_POST['carrier_city'],
                //         "state" => $_POST['carrier_state'],
                //         "zip_code" => $_POST['carrier_zip'],
                //         "country" => $_POST['carrier_country'],
                //         "insurance_iccmcnumber" => $_POST['carrier_insurance_iccmcnumber'],
                //     ]);
                // }

                $this->setFlashInfo("Order dispatched successfully.");
                redirect(getLink("orders", "show", "id", (int) $_GET['id']));
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

                if (count($this->err)) {
                    $this->setFlashError($this->err);
                }
            }
            if (count($_POST) == 0) {
                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                if (isset($_GET['acc']) || ctype_digit((string) $_GET['acc'])) {

                    $this->input['carrier_contact'] = $accountData->contact_name1;
                    $this->input['carrier_phone2'] = $accountData->phone2;
                    $this->input['carrier_company'] = $accountData->company_name;
                    $this->input['carrier_email'] = $accountData->email;
                    $this->input['carrier_phone1'] = formatPhone($accountData->phone1);
                    $this->input['carrier_phone2'] = formatPhone($accountData->phone2);
                    $this->input['carrier_phone1'] = formatPhone($accountData->phone1);
                    $this->input['carrier_phone2'] = formatPhone($accountData->phone2);
                    $this->input['carrier_phone1_ext'] = $accountData->carrier_phone1_ext;
                    $this->input['carrier_phone2_ext'] = $accountData->carrier_phone2_ext;
                    $this->input['carrier_cell'] = formatPhone($accountData->cell);
                    $this->input['carrier_fax'] = $accountData->fax;
                    $this->input['carrier_address'] = $accountData->address1;
                    $this->input['carrier_city'] = $accountData->city;
                    $this->input['carrier_state'] = $accountData->state;
                    $this->input['carrier_zip'] = $accountData->zip_code;
                    $this->input['carrier_country'] = $accountData->country;
                    $this->input['carrier_print_name'] = $accountData->print_name;
                    $this->input['carrier_insurance_iccmcnumber'] = $accountData->insurance_iccmcnumber;
                    $this->input['carrier_type'] = $accountData->carrier_type;
                }

                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['pickup_address1'] = $origin->address1;
                $this->input['pickup_address2'] = $origin->address2;
                $this->input['pickup_city'] = $origin->city;
                $this->input['pickup_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['pickup_zip'] = $origin->zip;
                $this->input['pickup_country'] = $origin->country;
                $this->input['pickup_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['pickup_company'] = $origin->company;
                $this->input['pickup_phone1'] = formatPhone($origin->phone1);
                $this->input['pickup_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['pickup_phone4'] = formatPhone($origin->phone4);
                $this->input['pickup_phone1_ext'] = $origin->phone1_ext;
                $this->input['pickup_phone2_ext'] = $origin->phone2_ext;
                $this->input['pickup_phone3_ext'] = $origin->phone3_ext;
                $this->input['pickup_phone4_ext'] = $origin->phone4_ext;
                $this->input['pickup_cell'] = formatPhone($origin->phone_cell);
                $this->input['from_buyer_number'] = $entity->buyer_number;
                $this->input['from_booking_number'] = $entity->booking_number;
                $this->input['pickup_cell2'] = formatPhone($origin->phone_cell2);
                $this->input['pickup_fax2'] = $origin->fax2;
                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;
                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['deliver_address1'] = $destination->address1;
                $this->input['deliver_address2'] = $destination->address2;
                $this->input['deliver_city'] = $destination->city;
                $this->input['deliver_state'] = $destination->state;
                $this->input['deliver_state2'] = $destination->state;
                $this->input['deliver_zip'] = $destination->zip;
                $this->input['deliver_country'] = $destination->country;
                $this->input['deliver_name'] = $destination->name;
                $this->input['deliver_company'] = $destination->company;
                $this->input['deliver_phone1'] = formatPhone($destination->phone1);
                $this->input['deliver_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['deliver_phone4'] = formatPhone($destination->phone4);
                $this->input['deliver_phone1_ext'] = $destination->phone1_ext;
                $this->input['deliver_phone2_ext'] = $destination->phone2_ext;
                $this->input['deliver_phone3_ext'] = $destination->phone3_ext;
                $this->input['deliver_phone4_ext'] = $destination->phone4_ext;
                $this->input['deliver_cell'] = formatPhone($destination->phone_cell);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['to_booking_number'] = $destination->booking_number;
                $this->input['to_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;
                $this->input['deliver_cell2'] = formatPhone($destination->phone_cell2);
                $this->input['deliver_fax2'] = $destination->fax2;

                /* Load Shipping Information */
                $this->input['price_fb'] = number_format($price_fb, 2, '.', ',');
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['order_load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['order_load_date_type'] = $entity->load_date_type;
                $this->input['order_delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['order_delivery_date_type'] = $entity->delivery_date_type;
                $this->input['order_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['order_notes_from_shipper'] = $entity->notes_from_shipper;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['payments_terms_carrier'] = $entity->payments_terms;
                $this->input['fee_type'] = $entity->delivery_credit;
            }
            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();
            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            $this->daffny->tpl->ask_post_to_cd = $ask_post_to_cd;
            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'desc');
            $notes = $entity->getNotes(true, " order by convert(created,datetime) desc ");
            $this->daffny->tpl->notes = $notes;
            $this->form->TextArea("payments_terms_carrier", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($settings->referrer_status);
            $this->getDispatchForm();
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            die($e->getMessage());
            //redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            die($e->getMessage());
            //redirect($e->getRedirectUrl());
        }
    }

    public function dispatch_order()
    {
        try {
            
            if (isset($_GET['acc']) && ctype_digit((string) $_GET['acc'])) {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_GET['acc']);
                if ($_SESSION['member']['parent_id'] == $accountData->owner_id) {
                    //nothing to do
                } else {
                    $existing = $this->daffny->DB->select(" `id` ", "app_accounts", " WHERE owner_id = '" . $_SESSION['member']['parent_id'] . "' AND copy_owner_id = '" . $accountData->owner_id . "' AND contact_name1 = '" . $accountData->contact_name1 . "' AND phone1 = '" . $accountData->phone1 . "' ORDER BY ID DESC LIMIT 1");
                    $existsData = mysqli_fetch_assoc($existing);
                    if ($existing->num_rows) {
                        $_GET['acc'] = $existsData['id'];
                    } else {
                        $carrier_arrTemp = array(
                            "member_id" => $accountData->member_id,
                            "owner_id" => $_SESSION['member']['parent_id'],
                            "copy_owner_id" => $accountData->owner_id,
                            "is_carrier" => 1,
                            "carrier_type" => $accountData->carrier_type,
                            "company_name" => $accountData->company_name,
                            "contact_name1" => $accountData->contact_name1,
                            "phone1" => $accountData->phone1,
                            "phone2" => $accountData->phone2,
                            "phone1_ext" => $accountData->phone1_ext,
                            "phone2_ext" => $accountData->phone2_ext,
                            "cell" => $accountData->cell,
                            "fax" => $accountData->fax,
                            "email" => $accountData->email,
                            "address1" => $accountData->address1,
                            "city" => $accountData->city,
                            "state" => $accountData->state,
                            "zip_code" => $accountData->zip_code,
                            "country" => $accountData->country,
                            "insurance_iccmcnumber" => $accountData->insurance_iccmcnumber,
                            "create_date" => date('Y-m-d H:i:s'),
                            "donot_dispatch" => $accountData->donot_dispatch,
                            "status" => $accountData->status,
                            "print_name" => $accountData->print_name,
                        );
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arrTemp);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $_GET['acc'] = $insid;
                    }
                }
            }

            if (isset($_GET['member']) && ctype_digit((string) $_GET['member'])) {
                $carrierMember = new Member($this->daffny->DB);
                $carrierMember->load($_GET['member']);
                $carrierMemberCompany = $carrierMember->getCompanyProfile();

                $newCarrier = array(
                    "member_id" => $carrierMember->id,
                    "owner_id" => $_SESSION['member']['parent_id'],
                    "copy_owner_id" => 0,
                    "is_carrier" => 1,
                    "carrier_type" => null,
                    "company_name" => $carrierMemberCompany->companyname,
                    "contact_name1" => null,
                    "phone1" => $carrierMemberCompany->dispatch_phone,
                    "phone2" => formatPhone($carrierMemberCompany->phone),
                    "phone1_ext" => null,
                    "phone2_ext" => null,
                    "cell" => formatPhone($carrierMemberCompany->phone_cell),
                    "fax" => $carrierMemberCompany->dispatch_fax,
                    "email" => $carrierMemberCompany->dispatch_email,
                    "address1" => trim($carrierMemberCompany->address1),
                    "city" => $carrierMemberCompany->city,
                    "state" => $carrierMemberCompany->state,
                    "zip_code" => $carrierMemberCompany->zip_code,
                    "country" => $carrierMemberCompany->country,
                    "insurance_iccmcnumber" => $carrierMemberCompany->mc_number,
                    "create_date" => date('Y-m-d H:i:s'),
                    "donot_dispatch" => 0,
                    "status" => 1,
                    "print_name" => null,
                );

                // check if not existing.
                $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE company_name LIKE '.$carrierMemberCompany->companyname);
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();
                    $_GET['acc'] = $insid;
                }
            }

            if (isset($_GET['fsmca']) && ctype_digit((string) $_GET['fsmca'])) {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://saferwebapi.com/v2/mcmx/snapshot/'.$_GET['fsmca']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'x-api-key: 169ebdecadf6464f9aa24b49638877d4',
                ));

                $fsmcaCarriers = curl_exec($curl);
                curl_close($curl);

                $fsmcaCarriers = json_decode($fsmcaCarriers);

                $newCarrier = array(
                    "member_id" => $_SESSION['member']['parent_id'],
                    "owner_id" => $_SESSION['member']['parent_id'],
                    "copy_owner_id" => 0,
                    "is_carrier" => 1,
                    "carrier_type" => null,
                    "company_name" => $fsmcaCarriers->legal_name,
                    "contact_name1" => null,
                    "phone1" => $fsmcaCarriers->phone,
                    "phone2" => formatPhone($fsmcaCarriers->phone),
                    "phone1_ext" => null,
                    "phone2_ext" => null,
                    "cell" => formatPhone($fsmcaCarriers->phone),
                    "fax" => $carrierMemberCompany->dispatch_fax,
                    "email" => $carrierMemberCompany->dispatch_email,
                    "address1" => trim(explode(",",$fsmcaCarriers->mailing_address)[0]),
                    "city" => $carrierMemberCompany->city,
                    "state" => explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[1],
                    "zip_code" => explode("-", explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[3])[0],
                    "country" => "US",
                    "insurance_iccmcnumber" => $_GET['fsmca'],
                    "create_date" => date('Y-m-d H:i:s'),
                    "donot_dispatch" => 0,
                    "status" => 1,
                    "print_name" => $fsmcaCarriers->legal_name,
                );

                // check if not existing.
                $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE insurance_iccmcnumber LIKE '.$_GET['fsmca']);
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();

                    // TODO:: insert in members and app_company

                    $_GET['acc'] = $insid;
                }
            }

            if (isset($_GET['dot']) && ctype_digit((string) $_GET['dot'])) {
                $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, 'https://saferwebapi.com/v2/usdot/snapshot/'.$_GET['dot']);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'x-api-key: 169ebdecadf6464f9aa24b49638877d4',
                ));

                $fsmcaCarriers = curl_exec($curl);
                curl_close($curl);

                $fsmcaCarriers = json_decode($fsmcaCarriers);

                $mc = "";
                if($fsmcaCarriers->mc_mx_ff_numbers){
                    $mc = explode("-", $fsmcaCarriers->mc_mx_ff_numbers)[1];
                    $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE insurance_iccmcnumber LIKE '.$mc.' OR us_dot LIKE '.$_GET['dot']);
                } else {
                    $cAccExst = $this->daffny->DB->selectRow('id', 'app_accounts', 'WHERE  us_dot LIKE '.$_GET['dot']);
                }
                
                if(isset($cAccExst['id'])){
                    $_GET['acc'] = $cAccExst['id'];
                } else {
                    // TODO:: insert in members and app_company
                    $newCarrier = array(
                        "member_id" => $_SESSION['member']['parent_id'],
                        "owner_id" => $_SESSION['member']['parent_id'],
                        "copy_owner_id" => 0,
                        "is_carrier" => 1,
                        "carrier_type" => null,
                        "company_name" => $fsmcaCarriers->legal_name,
                        "contact_name1" => null,
                        "phone1" => $fsmcaCarriers->phone,
                        "phone2" => formatPhone($fsmcaCarriers->phone),
                        "phone1_ext" => null,
                        "phone2_ext" => null,
                        "cell" => formatPhone($fsmcaCarriers->phone),
                        "fax" => $carrierMemberCompany->dispatch_fax,
                        "email" => $carrierMemberCompany->dispatch_email,
                        "address1" => trim(explode(",",$fsmcaCarriers->mailing_address)[0]),
                        "city" => $carrierMemberCompany->city,
                        "state" => explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[1],
                        "zip_code" => explode("-", explode(" ", explode(",",$fsmcaCarriers->mailing_address)[1])[3])[0],
                        "country" => "US",
                        "insurance_iccmcnumber" => $mc,
                        "us_dot" => $_GET['dot'],
                        "create_date" => date('Y-m-d H:i:s'),
                        "donot_dispatch" => 0,
                        "status" => 1,
                        "print_name" => $fsmcaCarriers->legal_name,
                    );
                    $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $newCarrier);
                    $this->daffny->DB->insert("app_accounts", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();

                    $_GET['acc'] = $insid;
                }
            }

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.dispatch-order";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

            if (
                $entity->status == Entity::STATUS_NOTSIGNED ||
                $entity->status == Entity::STATUS_DISPATCHED ||
                $entity->status == Entity::STATUS_ISSUES ||
                $entity->status == STATUS_PICKEDUP ||
                $entity->status == Entity::STATUS_DELIVERED
            ) {
                throw new UserException("Order Already Dispatched.", getLink('orders/show/id', $entity->id));
            }

            $account_id = 0;

            if (trim($_POST['carrier_id']) != "") {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_POST['carrier_id']);
                $account_id = $accountData->id;

                if ($accountData->insurance_doc_id > 0) {
                    $this->daffny->tpl->filesCargo = $this->getInsuranceCertificate($accountData->insurance_doc_id);
                }
            }

            if (isset($_GET['acc']) && ctype_digit((string) $_GET['acc'])) {
                $accountData = new Account($this->daffny->DB);
                $accountData->load($_GET['acc']);
                $account_id = $accountData->id;
                if ($accountData->insurance_doc_id > 0) {
                    $this->daffny->tpl->filesCargo = $this->getInsuranceCertificate($accountData->insurance_doc_id);
                }
            }

            if ($entity->readonly) {
                throw new UserException("Access Denied", getLink('orders'));
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", getLink('orders/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Dispatch Order"));
            $this->title = "Dispatch Order #" . $entity->number;
            $ask_post_to_cd = false;
            $settings = $entity->getAssigned()->getDefaultSettings();

            if ($entity->status == Entity::STATUS_POSTED) {
                if ($settings->central_dispatch_uid != "" && $settings->central_dispatch_post == 1) {
                    $ask_post_to_cd = true;
                }
            }

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditFormNewDispatch(false, $settings->referrer_status, $entity->status)) {

                //pending dispatch clearance
                $entity->update(
                    array(
                        'is_pending_dispatch' => 0,
                    )
                );
                $this->daffny->DB->query("DELETE FROM app_pending_dispatches WHERE entity_id = " . $entity->id);

                // Application Logging
                $info = "Dispatch Order-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                //Insert new carrier
                $carrier_arr = array(
                    "member_id" => getMemberId(),
                    "owner_id" => getParentId(),
                    "is_carrier" => 1,
                    "carrier_type" => (isset($_POST['carrier_type']) && $_POST['carrier_type'] != "") ? $_POST['carrier_type'] : null,
                    "company_name" => $_POST['carrier_company'],
                    "contact_name1" => $_POST['carrier_contact'],
                    "phone1" => str_replace("-", "", $_POST['carrier_phone1']),
                    "phone2" => str_replace("-", "", $_POST['carrier_phone2']),
                    "phone1_ext" => $_POST['carrier_phone1_ext'],
                    "phone2_ext" => $_POST['carrier_phone2_ext'],
                    "cell" => str_replace("-", "", $_POST['carrier_cell']),
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

                if (isset($_POST['save_carrier']) && $_POST['save_carrier'] == 1) {
                    if (isset($account_id) && $account_id != 0) {
                        $carrier = new Account($this->daffny->DB);

                        $carrier->load($account_id);

                        unset($carrier_arr['member_id']);
                        unset($carrier_arr['owner_id']);
                        $carrier->update($carrier_arr, $account_id);

                        if ($_POST['insurance_type'] != '') {
                            $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        }
                    }
                } elseif (!isset($_POST['carrier_id']) || trim($_POST['carrier_id']) == "") {
                    $carrierFlag = $this->validateSaveCarrier($carrier_arr);
                    if ($carrierFlag == 1) {
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arr);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $carrier_id = $insid;
                        $account_id = $insid;

                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        
                    } elseif ($carrierFlag > 1) { // carrier id found
                        $carrier_id = $carrierFlag;
                        $account_id = $carrier_id;
                    }
                } else {
                    $carrierFlag = $this->validateSaveCarrier($carrier_arr, $_POST['carrier_id']);

                    if ($carrierFlag == 1) {
                        $carrier_id = $_POST['carrier_id'];
                        $carrierOld = new Account($this->daffny->DB);
                        $carrierOld->load($carrier_id);
                        $carrier_arr['copy_owner_id'] = $carrierOld->owner_id;
                        $ins_arr = $this->daffny->DB->PrepareSql("app_accounts", $carrier_arr);
                        $this->daffny->DB->insert("app_accounts", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();
                        $carrier_id = $insid;
                        $account_id = $insid;

                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);

                        if ($_SESSION['member']['parent_id'] == 1) {
                            // nothing to do here
                        }
                    } elseif ($carrierFlag > 1) {
                        // carrier id found
                        $carrier_id = $carrierFlag;
                        $account_id = $carrier_id;
                        $carrier = new Account($this->daffny->DB);
                        $carrier->load($carrier_id);

                        unset($carrier_arr['member_id']);
                        unset($carrier_arr['owner_id']);
                        $carrier->update($carrier_arr, $carrier_id);

                        $this->upload_insurance_file("carrier_ins_doc", $carrier, $entity, $_POST['insurance_type']);
                        
                    }
                }
                
                $this->daffny->DB->transaction();
                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                /* UPDATE ORIGIN */

                $origin = $entity->getOrigin();
                if ($sql_arr['pickup_country'] != "US") {
                    $sql_arr['pickup_state'] = $sql_arr['pickup_state2'];
                }

                $update_arr = array(
                    'address1' => $sql_arr['pickup_address1'],
                    'address2' => $sql_arr['pickup_address2'],
                    'city' => $sql_arr['pickup_city'],
                    'state' => $sql_arr['pickup_state'],
                    'zip' => $sql_arr['pickup_zip'],
                    'country' => $sql_arr['pickup_country'],
                    'name' => $sql_arr['pickup_name'],
                    'auction_name' => $sql_arr['origin_auction_name'],
                    'company' => $sql_arr['pickup_company'],
                    'phone1' => str_replace("-", "", $sql_arr['pickup_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['pickup_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['origin_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['pickup_phone4']),
                    'phone1_ext' => $sql_arr['pickup_phone1_ext'],
                    'phone2_ext' => $sql_arr['pickup_phone2_ext'],
                    'phone3_ext' => $sql_arr['pickup_phone3_ext'],
                    'phone4_ext' => $sql_arr['pickup_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['pickup_cell']),
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['from_booking_number'],
                    'buyer_number' => $sql_arr['from_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
                );
                $origin->update($update_arr);

                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['deliver_country'] != "US") {
                    $sql_arr['deliver_state'] = $sql_arr['ddeliver_state2'];
                }
                $update_arr = array(
                    'address1' => $sql_arr['deliver_address1'],
                    'address2' => $sql_arr['deliver_address2'],
                    'city' => $sql_arr['deliver_city'],
                    'state' => $sql_arr['deliver_state'],
                    'zip' => $sql_arr['deliver_zip'],
                    'country' => $sql_arr['deliver_country'],
                    'name' => $sql_arr['deliver_name'],
                    'company' => $sql_arr['deliver_company'],
                    'phone1' => str_replace("-", "", $sql_arr['deliver_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['deliver_phone2']),
                    'phone3' => str_replace("-", "", $sql_arr['destination_phone3']),
                    'phone4' => str_replace("-", "", $sql_arr['deliver_phone4']),
                    'phone1_ext' => $sql_arr['deliver_phone1_ext'],
                    'phone2_ext' => $sql_arr['deliver_phone2_ext'],
                    'phone3_ext' => $sql_arr['deliver_phone3_ext'],
                    'phone4_ext' => $sql_arr['deliver_phone4_ext'],
                    'phone_cell' => str_replace("-", "", $sql_arr['deliver_cell']),
                    'phone_cell2' => str_replace("-", "", $sql_arr['deliver_cell2']),
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['from_booking_number'],
                    'buyer_number' => $sql_arr['from_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                    'fax2' => $sql_arr['deliver_fax2'],
                );
                $destination->update($update_arr);

                /* UPDATE NOTE */
                $notes = $entity->getNotes();
                if (count($notes[Note::TYPE_FROM]) != 0) {
                    $note = $notes[Note::TYPE_FROM][0];
                    $note->update(array('text' => $sql_arr['order_notes_from_shipper']));
                } else {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['order_notes_from_shipper'], 'type' => Note::TYPE_FROM));
                }

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
                $update_arr = array(
                    'ship_via' => (int) $sql_arr['order_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['order_load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['order_load_date'])),
                    'load_date_type' => (int) $sql_arr['order_load_date_type'],
                    'delivery_date' => empty($sql_arr['order_delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['order_delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['order_delivery_date_type'],
                    'distance' => $distance,
                    'notes_from_shipper' => $sql_arr['order_notes_from_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['order_pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['order_dropoff_terminal_fee'],
                    'payments_terms' => $sql_arr['payments_terms_carrier'],
                    'blocked_by' => 'NULL',
                    'blocked_time' => 'NULL',
                    'delivery_credit' => ($sql_arr['balance_paid_by'] != Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) ? 0 : $sql_arr['fee_type'],
                );

                $amount = 0;

                if (($sql_arr['balance_paid_by'] != $entity->balance_paid_by) || $entity->delivery_credit != $_POST['fee_type']) {
                    if ($sql_arr['balance_paid_by'] == 24) {
                        // AHC payment methods added
                        if ($_POST['fee_type'] == 1) {
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with no fee';
                        } elseif ($_POST['fee_type'] == 2) {
                            $amount = ((int) $carrier_fee * 0.03) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 3) {
                            $amount = ((int) $carrier_fee * 0.05) + 12;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 4) {
                            $amount = ((int) $carrier_fee * 0.03) + 0;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } elseif ($_POST['fee_type'] == 5) {
                            $amount = ((int) $carrier_fee * 0.05) + 0;
                            $amount = number_format($amount, 2);
                            $customNote = '<b>' . $_SESSION['member']['contactname'] . '</b> has selected to pay the carrier by ACH with a $' . $amount . ' processing Fee';
                        } else {
                            $customNote = 'Invalid Fee Type Selected';
                        }

                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => 3,
                            "text" => $customNote,
                        );
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                }
                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        // update notes
                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }
                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }
                        if ($NotesStr != "") {
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr,
                            );

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );
                        $vehicleValue->update($insert_arr);
                    }
                }

                // dispatch logic
                $order_load_date = date("Y-m-d", strtotime($_POST['order_load_date']));
                $order_delivery_date = date("Y-m-d", strtotime($_POST['order_delivery_date']));

                // Update Entity
                $update_arr = array(
                    'carrier_id' => $account_id,
                    'load_date' => $order_load_date,
                    'delivery_date' => $order_delivery_date,
                    'load_date_type' => (int) $_POST['order_load_date_type'],
                    'delivery_date_type' => (int) $_POST['order_delivery_date_type'],
                    'ship_via' => $_POST['order_ship_via'],
                    'dispatched' => date("Y-m-d H:i:s"),
                    'not_signed' => date("Y-m-d H:i:s"),
                );
                $entity->update($update_arr);
                // Create Dispatch Sheet
                $company = $entity->getAssigned()->getCompanyProfile();
                $notes = $entity->getNotes();
                if (($entity->include_shipper_comment == 1) && isset($notes[Note::TYPE_FROM][0])) {
                    $instructions = $notes[Note::TYPE_FROM][0]->text;
                } else {
                    $instructions = "";
                }
                $payments_terms_dispatch = $_POST['payments_terms_carrier'];
                if (in_array($entity->balance_paid_by, array(2, 3, 16, 17))) {
                    $payments_terms_dispatch = "COD";
                }
                $entity->getVehicles(true);

                $company_owes_carrier = 0;
                $carrier_owes_company = 0;
                $carrier_pay_total = 0;

                switch ($entity->balance_paid_by) {
                    case Entity::BALANCE_COD_TO_CARRIER_CASH:
                    case Entity::BALANCE_COD_TO_CARRIER_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored;
                        break;
                    case Entity::BALANCE_COP_TO_CARRIER_CASH:
                    case Entity::BALANCE_COP_TO_CARRIER_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored;
                        break;
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                        $company_owes_carrier = $entity->carrier_pay_stored;
                        $carrier_owes_company = 0;
                        $carrier_pay_total = 0;

                        break;
                    case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                    case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                        $company_owes_carrier = 0;
                        $carrier_owes_company = $entity->total_tariff_stored - $entity->carrier_pay_stored;
                        $carrier_pay_total = $entity->pickup_terminal_fee + $entity->dropoff_terminal_fee + $entity->carrier_pay_stored + ($entity->total_tariff_stored - $entity->carrier_pay_stored);
                        break;
                }

                $insert_arr = array(
                    'entity_id' => $_GET['id'],
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
                    'carrier_id' => (trim($account_id) != "") ? $account_id : "NULL",
                    'carrier_company_name' => $_POST['carrier_company'],
                    'carrier_contact_name' => $_POST['carrier_contact'],
                    'carrier_phone_1' => str_replace("-", "", $_POST['carrier_phone1']),
                    'carrier_phone_2' => str_replace("-", "", $_POST['carrier_phone2']),
                    'carrier_phone1_ext' => $_POST['carrier_phone1_ext'],
                    'carrier_phone2_ext' => $_POST['carrier_phone2_ext'],
                    'carrier_phone_cell' => str_replace("-", "", $_POST['carrier_cell']),
                    'carrier_fax' => $_POST['carrier_fax'],
                    'carrier_driver_name' => $_POST['carrier_driver_name'],
                    'carrier_driver_phone' => str_replace("-", "", $_POST['carrier_driver_phone']),
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
                    'entity_carrier_pay' => number_format($carrier_pay_total, 2, '.', ','),
                    'entity_carrier_pay_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "*COD" : (in_array($entity->balance_paid_by, array(8, 9)) ? "*COP" : "")),
                    'entity_odtc' => number_format($carrier_owes_company, 2, '.', ','),
                    'entity_coc' => number_format($company_owes_carrier, 2, '.', ','),
                    'entity_coc_c' => (in_array($entity->balance_paid_by, array(2, 3)) ? "after COD is paid" : (in_array($entity->balance_paid_by, array(8, 9)) ? "after COP is paid" : "")),
                    'entity_booking_number' => $_POST['order_booking_number'],
                    'entity_buyer_number' => $_POST['order_buyer_number'],
                    'entity_pickup_terminal_fee' => number_format($entity->pickup_terminal_fee, 2, '.', ','),
                    'entity_dropoff_terminal_fee' => number_format($entity->dropoff_terminal_fee, 2, '.', ','),
                    'entity_balance_paid_by' => $_POST['balance_paid_by'],
                    'information' => $sql_arr['order_notes_from_shipper'],
                    'from_name' => $_POST['pickup_name'],
                    'from_name2' => $_POST['origin_contact_name2'],
                    'from_company' => $_POST['pickup_company'],
                    'from_address' => $_POST['pickup_address1'],
                    'from_address2' => $_POST['pickup_address2'],
                    'from_city' => $_POST['pickup_city'],
                    'from_state' => $_POST['pickup_state'],
                    'from_zip' => $_POST['pickup_zip'],
                    'from_country' => $_POST['pickup_country'],
                    'from_phone_1' => str_replace("-", "", $_POST['pickup_phone1']),
                    'from_phone_2' => str_replace("-", "", $_POST['pickup_phone2']),
                    'from_phone_3' => str_replace("-", "", $_POST['origin_phone3']),
                    'from_phone_4' => str_replace("-", "", $_POST['pickup_phone4']),
                    'from_phone1_ext' => $_POST['pickup_phone1_ext'],
                    'from_phone2_ext' => $_POST['pickup_phone2_ext'],
                    'from_phone3_ext' => $_POST['pickup_phone3_ext'],
                    'from_phone4_ext' => $_POST['pickup_phone4_ext'],
                    'from_phone_cell' => str_replace("-", "", $_POST['pickup_cell']),
                    'from_booking_number' => $_POST['from_booking_number'],
                    'from_buyer_number' => $_POST['from_buyer_number'],
                    'origin_auction_name' => $_POST['origin_auction_name'],
                    'from_phone_cell2' => str_replace("-", "", $_POST['pickup_cell2']),
                    'from_fax' => $_POST['origin_fax'],
                    'from_fax2' => $_POST['pickup_fax2'],
                    'to_name' => $_POST['deliver_name'],
                    'to_name2' => $_POST['destination_contact_name2'],
                    'to_company' => $_POST['deliver_company'],
                    'to_address' => $_POST['deliver_address1'],
                    'to_address2' => $_POST['deliver_address2'],
                    'to_city' => $_POST['deliver_city'],
                    'to_state' => $_POST['deliver_state'],
                    'to_zip' => $_POST['deliver_zip'],
                    'to_country' => $_POST['deliver_country'],
                    'to_phone_1' => str_replace("-", "", $_POST['deliver_phone1']),
                    'to_phone_2' => str_replace("-", "", $_POST['deliver_phone2']),
                    'to_phone_3' => str_replace("-", "", $_POST['destination_phone3']),
                    'to_phone_4' => str_replace("-", "", $_POST['deliver_phone4']),
                    'to_phone1_ext' => $_POST['deliver_phone1_ext'],
                    'to_phone2_ext' => $_POST['deliver_phone2_ext'],
                    'to_phone3_ext' => $_POST['deliver_phone3_ext'],
                    'to_phone4_ext' => $_POST['deliver_phone4_ext'],
                    'to_phone_cell' => str_replace("-", "", $_POST['deliver_cell']),
                    'to_phone_cell2' => str_replace("-", "", $_POST['deliver_cell2']),
                    'to_booking_number' => $_POST['to_booking_number'],
                    'to_buyer_number' => $_POST['to_buyer_number'],
                    'to_auction_name' => $_POST['destination_auction_name'],
                    'to_fax' => $_POST['destination_fax'],
                    'to_fax2' => $_POST['deliver_fax2'],
                    'dispatch_terms' => $company->getDefaultSettings()->dispatch_terms,
                    'hash_link' => $this->getAlmostUniqueHash($_POST['entity_id'], $entity->getNumber()),
                    'payments_terms' => $payments_terms_dispatch,
                    'expired' => date("Y-m-d H:i:s", time() + (7 * 60 * 60 * 24)),
                );

                if (isset($_POST['order_include_shipper_comment'])) {
                    if ($_POST['order_notes_from_shipper'] != "") {
                        $insert_arr['instructions'] = $_POST['order_notes_from_shipper'];

                        /* UPDATE NOTE */
                        $note_array = array(
                            "entity_id" => $entity->id,
                            "sender_id" => $_SESSION['member_id'],
                            "type" => Note::TYPE_FROM,
                            "text" => $_POST['order_notes_from_shipper']);
                        $note = new Note($this->daffny->DB);
                        $note->create($note_array);
                    }
                } else {
                    $insert_arr['instructions'] = '';
                }

                $dispatchSheet = new DispatchSheet($this->daffny->DB);
                $dispatch_id = $dispatchSheet->create($insert_arr);

                $vehicleManager = new VehicleManager($this->daffny->DB);
                $vehiclesDis = $vehicleManager->getVehicles($entity->id);
                foreach ($vehiclesDis as $vehicle1) {
                    /* @var Vehicle $vehicle */
                    $vehicle1->cloneForDispatch($dispatch_id);
                }

                $entity->setStatus(Entity::STATUS_NOTSIGNED);

                // check settings to send dispatch notification email
                $owner_id = $_SESSION['member']['parent_id'];
                $query = "SELECT dispatch_email_triggers FROM app_defaultsettings WHERE owner_id = " . $owner_id;
                $res = $this->daffny->DB->query($query);
                $emailFlag = mysqli_fetch_assoc($res)['dispatch_email_triggers'];

                if ($emailFlag == 0) {
                    // sending dispatch notification email
                    //$entity->sendOrderDispatched();
                }

                if ($entity->isBlocked()) {
                    $entity->unsetBlock();
                }

                // send dispatch link to carrier
                $dispatch_link = BASE_PATH . "order/dispatchnew/hash/" . $insert_arr["hash_link"];
                $entity->sendDispatchLink(array("dispatch_link" => $dispatch_link));
                
                //send letter to the carrier

                // Add dispatch Notes
                $NotesStr = " has dispatched this order to " . $_POST['carrier_company'] . " for pickup " . $order_load_date . " and drop off on " . $order_delivery_date;
                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $_SESSION['member']['contactname'] . $NotesStr, 'sender_id' => $_SESSION['member_id'], "status" => 1, "system_admin" => 1, 'type' => Note::TYPE_INTERNAL));

                // Send CD
                $this->sendCentralDispatchEmail($entity, 3);
                
                $this->daffny->DB->transaction("commit");
                $entity->make_payment();
                $entity->updateHeaderTable();

                $this->setFlashInfo("Order dispatched successfully.");
                redirect(getLink("orders", "show", "id", (int) $_GET['id']));
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

                if (count($this->err)) {
                    $this->setFlashError($this->err);
                }
            }
            if (count($_POST) == 0) {
                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                if (isset($_GET['acc']) || ctype_digit((string) $_GET['acc'])) {

                    $this->input['carrier_contact'] = $accountData->contact_name1;
                    $this->input['carrier_phone2'] = $accountData->phone2;
                    $this->input['carrier_company'] = $accountData->company_name;
                    $this->input['carrier_email'] = $accountData->email;
                    $this->input['carrier_phone1'] = formatPhone($accountData->phone1);
                    $this->input['carrier_phone2'] = formatPhone($accountData->phone2);
                    $this->input['carrier_phone1'] = formatPhone($accountData->phone1);
                    $this->input['carrier_phone2'] = formatPhone($accountData->phone2);
                    $this->input['carrier_phone1_ext'] = $accountData->carrier_phone1_ext;
                    $this->input['carrier_phone2_ext'] = $accountData->carrier_phone2_ext;
                    $this->input['carrier_cell'] = formatPhone($accountData->cell);
                    $this->input['carrier_fax'] = $accountData->fax;
                    $this->input['carrier_address'] = $accountData->address1;
                    $this->input['carrier_city'] = $accountData->city;
                    $this->input['carrier_state'] = $accountData->state;
                    $this->input['carrier_zip'] = $accountData->zip_code;
                    $this->input['carrier_country'] = $accountData->country;
                    $this->input['carrier_print_name'] = $accountData->print_name;
                    $this->input['carrier_insurance_iccmcnumber'] = $accountData->insurance_iccmcnumber;
                    $this->input['carrier_type'] = $accountData->carrier_type;
                }

                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['pickup_address1'] = $origin->address1;
                $this->input['pickup_address2'] = $origin->address2;
                $this->input['pickup_city'] = $origin->city;
                $this->input['pickup_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['pickup_zip'] = $origin->zip;
                $this->input['pickup_country'] = $origin->country;
                $this->input['pickup_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['pickup_company'] = $origin->company;
                $this->input['pickup_phone1'] = formatPhone($origin->phone1);
                $this->input['pickup_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['pickup_phone4'] = formatPhone($origin->phone4);
                $this->input['pickup_phone1_ext'] = $origin->phone1_ext;
                $this->input['pickup_phone2_ext'] = $origin->phone2_ext;
                $this->input['pickup_phone3_ext'] = $origin->phone3_ext;
                $this->input['pickup_phone4_ext'] = $origin->phone4_ext;
                $this->input['pickup_cell'] = formatPhone($origin->phone_cell);
                $this->input['from_buyer_number'] = $entity->buyer_number;
                $this->input['from_booking_number'] = $entity->booking_number;
                $this->input['pickup_cell2'] = formatPhone($origin->phone_cell2);
                $this->input['pickup_fax2'] = $origin->fax2;
                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;
                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['deliver_address1'] = $destination->address1;
                $this->input['deliver_address2'] = $destination->address2;
                $this->input['deliver_city'] = $destination->city;
                $this->input['deliver_state'] = $destination->state;
                $this->input['deliver_state2'] = $destination->state;
                $this->input['deliver_zip'] = $destination->zip;
                $this->input['deliver_country'] = $destination->country;
                $this->input['deliver_name'] = $destination->name;
                $this->input['deliver_company'] = $destination->company;
                $this->input['deliver_phone1'] = formatPhone($destination->phone1);
                $this->input['deliver_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['deliver_phone4'] = formatPhone($destination->phone4);
                $this->input['deliver_phone1_ext'] = $destination->phone1_ext;
                $this->input['deliver_phone2_ext'] = $destination->phone2_ext;
                $this->input['deliver_phone3_ext'] = $destination->phone3_ext;
                $this->input['deliver_phone4_ext'] = $destination->phone4_ext;
                $this->input['deliver_cell'] = formatPhone($destination->phone_cell);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['to_booking_number'] = $destination->booking_number;
                $this->input['to_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;
                $this->input['deliver_cell2'] = formatPhone($destination->phone_cell2);
                $this->input['deliver_fax2'] = $destination->fax2;

                /* Load Shipping Information */
                $this->input['price_fb'] = number_format($price_fb, 2, '.', ',');
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['order_load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['order_load_date_type'] = $entity->load_date_type;
                $this->input['order_delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['order_delivery_date_type'] = $entity->delivery_date_type;
                $this->input['order_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['order_notes_from_shipper'] = $entity->notes_from_shipper;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['payments_terms_carrier'] = $entity->payments_terms;
                $this->input['fee_type'] = $entity->delivery_credit;
            }
            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();
            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            $this->daffny->tpl->ask_post_to_cd = $ask_post_to_cd;
            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'desc');
            $notes = $entity->getNotes(true, " order by convert(created,datetime) desc ");
            $this->daffny->tpl->notes = $notes;
            $this->form->TextArea("payments_terms_carrier", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($settings->referrer_status);
            $this->getDispatchForm();
        } catch (FDException $e) {
            echo $this->daffny->DB->errorQuery;
            echo $e->getMessage();
            die;
            // $this->daffny->DB->transaction("rollback");
            // $applog = new Applog($this->daffny->DB);
            // $applog->createException($e);
            // $this->setFlashError($e->getMessage());
            // redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            echo $this->daffny->DB->errorQuery;
            echo $e->getMessage();
            die;
            // $this->daffny->DB->transaction("rollback");
            // $applog = new Applog($this->daffny->DB);
            // $applog->createException($e);
            // $this->setFlashError($e->getMessage());
            // redirect($e->getRedirectUrl());
        }
    }

    /**
     *  Function to send disoatch to central dispatch email
     * 
     *  @param Object $ref, Entity loaded class object
     *  @param integer $posttype Flag, 1: repost 2: unpost 3: dispatch 4: cancel 5: posting
     */
    public function sendCentralDispatchEmail($ref, $posttype) {

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
            }
            
            $firstAvail = $ref->getPostDate("Y-m-d");
            if(strtotime(trim($ref->getFirstAvail("Y-m-d"))) >= strtotime(date('Y-m-d')) )
                $firstAvail = $ref->getFirstAvail("Y-m-d");
                 
            //14. Vehicle Operable:
            $command .= $inopValue . ",";
            //15. First Available (YYYY-MM-DD):
            $command .=  $firstAvail . ",";
            //16. Display Until:
            $command .= date("Y-m-d",strtotime(date("Y-m-d", strtotime($firstAvail)) . "+1 month")) . ",";
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
                echo "<br/>";
                echo $mail->Body = $message;
                echo "<br/>";
                echo $mail->Subject = $subject;
                echo "<br/>";
                $mail->SetFrom("Info@transportmasters.net");
                $mail->AddAddress(Entity::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
                $mail->AddBCC("junk@cargoflare.com");
                $mail->AddBCC("shahrukhusmaani@live.com");
                
                $mail->Send();
                History::add( $this->daffny->DB, $ref->id, "CENTRAL DISPATCH", ($posttype == 1 ? "REPOST ORDER" : "ADD ORDER TO CD" ), date("Y-m-d H:i:s"));
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    public function getAlmostUniqueHash($id, $number)
    {
        return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
    }

    protected function checkEditFormNewDispatch($create = false, $referrer_status = 0, $status = -2)
    {

        $sql_arr = $_POST;
        $sql_arr['carrier_email'] = trim($sql_arr['carrier_email']);
        $checkEmpty = array(
            'carrier_company' => "Carrier: Company",
            'carrier_address' => "Carrier: Address",
            'carrier_city' => "Carrier: City",
            'carrier_state' => "Carrier: State",
            'carrier_zip' => "Carrier: Zip",
            'carrier_country' => "Carrier: Country",
            'carrier_email' => "Carrier: Email",
            'carrier_phone1' => "Carrier: Phone (1)",
            'carrier_driver_name' => "Carrier: Driver Name",
            'carrier_driver_phone' => "Carrier: Driver Phone",
            'carrier_print_name' => "Carrier: Print Check As",
            'carrier_type' => "Carrier: Type",
            'pickup_city' => "Pickup City",
            'pickup_country' => 'Pickup Country',
            //'pickup_zip' => 'Pickup Zip',
            'origin_type' => 'Origin Location Type',
            'destination_type' => 'Destination Location Type',
            'deliver_city' => "Delivery City",
            'deliver_country' => 'Delivery Country',
            //'deliver_zip' => 'Delivery Zip',
            'order_load_date' => "Order: Load Date",
            'order_load_date_type' => "Order: Load Date Type",
            'order_delivery_date' => "Order: Delivery Date",
            'order_delivery_date_type' => "Order: Delivery Date Type",
            'order_ship_via' => "Order: Ship Via",
            'payments_terms_carrier' => "Carrier Payment Terms",
        );

        if ($create) {
            if (!isset($_POST['year'])) {
                $this->err[] = "You must add at least one vehicle";
            }

            unset($checkEmpty['load_date']);
            unset($checkEmpty['load_date_type']);
            unset($checkEmpty['delivery_date']);
            unset($checkEmpty['delivery_date_type']);
        }

        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        // when pickup country is USA and Canada
        if ((trim(post_var("pickup_country")) == "US") || (trim(post_var("pickup_country")) == "CA")) {
            if ((trim(post_var("pickup_state")) == "")) {
                $this->isEmpty('pickup_state', "Pickup State");
            }

            if ((trim(post_var("pickup_zip")) == "")) {
                $this->isEmpty('pickup_zip', "Pickup Zipcode");
            }
        }

        // when destination country is USA and Canada
        if ((trim(post_var("destination_country")) == "US") || (trim(post_var("destination_country")) == "CA")) {
            if ((trim(post_var("destination_state")) == "")) {
                $this->isEmpty('destination_state', "Delivery State");
            }

            if ((trim(post_var("destination_zip")) == "")) {
                $this->isEmpty('destination_zip', "Delivery Zipcode");
            }
        }
        
        if ($_FILES["carrier_ins_doc"]['name'] != "" && $_FILES["carrier_ins_doc"]['size'] && ($_FILES["carrier_ins_doc"]['name'] != "none")) {
            $this->isEmpty('carrier_ins_expire', "Cargo Expire Date");
        }

        if (post_var('balance_paid_by') == Entity::BALANCE_COMPANY_OWES_CARRIER_ACH) {
            
            if (post_var('fee_type') == '') {
                $this->err[] = "Field <strong>Fee Type</strong> is empty.";
            }

        }
        
        if (trim(post_var("avail_pickup_date")) != "") {
            if (strtotime(trim(post_var("order_load_date"))) < strtotime(trim(post_var("avail_pickup_date")))) {
                $this->err[] = "<strong>Load Date</strong> should be more than or equal to First Available Date.";
            }
        }

        $this->checkEmail('carrier_email', "Carrier E-mail");
        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    protected function ordersnewlist($status)
    {

        $this->tplname = "orders.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = "orders.ordersnewlist";

        $this->applyOrder("app_order_header e");
        $this->order->Fields[] = 'shipperfname';
        $this->order->Fields[] = 'Origincity';
        $this->order->Fields[] = 'Destinationcity';
        $this->order->Fields[] = 'avail_pickup_date';
        $this->order->Fields[] = 'dispatched';
        $this->order->Fields[] = 'delivered';
        $this->order->Fields[] = 'posted';
        $this->order->Fields[] = 'not_signed';
        $this->order->Fields[] = 'issue_date';
        $this->order->Fields[] = 'hold_date';
        $this->order->Fields[] = 'created';
        $this->order->Fields[] = 'load_date';
        $this->order->Fields[] = 'delivery_date';
        $this->order->Fields[] = 'total_tariff';
        $this->order->Fields[] = 'archived';

        switch ($status) {
            case Entity::STATUS_ACTIVE:
                $this->title = "My Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders"));
                $this->order->setDefault('created', 'desc');
                break;
            case Entity::STATUS_ONHOLD:
                $this->title = "Orders On Hold";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Hold'));
                $this->order->setDefault('hold_date', 'desc');
                break;
            case Entity::STATUS_ARCHIVED:
                $this->title = "Orders Archived";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Archived'));
                $this->order->setDefault('archived', 'desc');
                break;
            case Entity::STATUS_POSTED:
                $this->title = "Orders Posted to FB";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Posted CD'));
                $this->order->setDefault('posted', 'desc');
                break;
            case Entity::STATUS_NOTSIGNED:
                $this->title = "Orders Notsigned";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Not Signed'));
                $this->order->setDefault('not_signed', 'desc');
                break;
            case Entity::STATUS_DISPATCHED:
                $this->title = "Orders Dispatched";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Dispatched'));
                $this->order->setDefault('dispatched', 'desc');
                break;
            case Entity::STATUS_PICKEDUP:
                $this->title = "Orders Picked Up";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Picked Up'));
                $this->order->setDefault('actual_pickup_date', 'desc');
                break;
            case Entity::STATUS_DELIVERED:
                $this->title = "Orders Delviered";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Delivered'));
                $this->order->setDefault('delivered', 'desc');
                break;
            case Entity::STATUS_PENDING:
                $this->title = "Orders Pending";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('pending') => "Pendings", '' => 'Pendings'));
                $this->order->setDefault('created', 'desc');
                break;
            case Entity::STATUS_ISSUES:
                $this->title = "Orders Issues";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Issue'));
                $this->order->setDefault('issue_date', 'desc');
                break;
            default:
                $this->title = "All Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'All'));
                $this->order->setDefault('id', 'desc');
                break;
        }

        $info = "Show order listing: " . $this->title;
        $applog = new Applog($this->daffny->DB);
        $applog->createInformation($info);

        $entityManager = new EntityManager($this->daffny->DB);

        switch ($this->order->CurrentOrder) {
            case 'created':
                $this->order->setTableIndex('e');
                break;
        }

        $dispatch_pickup_date = date('Y-m-d');
        if (isset($_POST['dispatch_pickup_date'])) {
            $dispatch_pickup_date = $_POST['dispatch_pickup_date'];
        }

        $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_ORDER, $this->order->getOrder(), $status, $_SESSION['per_page']);
        $todayDispatched = $entityManager->getDispatchedOrders(Entity::TYPE_ORDER, $dispatch_pickup_date);
        $this->daffny->tpl->todayDispatched = $todayDispatched;
        $entities_count = $entityManager->getCountHeader(Entity::TYPE_ORDER);

        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['pending_count'] = $entities_count[Entity::STATUS_PENDING];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal,
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));

        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

        $this->form->ComboBox(
            'issue_type',
            array('' => 'Issues Sort by', '1' => 'Customer Owe Us', '2' => 'We Owe the Carrier'), 
            array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "makeActionType();"),
            'Filter', '</td><td>'
        );

        $this->input['dispatch_pickup_date'] = $dispatch_pickup_date;
        $this->form->TextField("dispatch_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, ""), "", "</td><td>");

        $this->getDispatchForm();
        $this->getPaymentForm();
    }

    public function search()
    {
        try {
            $this->initGlobals();
            $this->tplname = "orders.main";
            $data_tpl = "orders.ordersnewlist";
            $this->title = "Orders search results";
            $this->daffny->tpl->status = "Archived";

            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'avail_pickup_date';
            $this->order->Fields[] = 'dispatched';
            $this->order->Fields[] = 'delivered';
            $this->order->Fields[] = 'posted';
            $this->order->Fields[] = 'not_signed';
            $this->order->Fields[] = 'issue_date';
            $this->order->Fields[] = 'hold_date';
            $this->order->Fields[] = 'created';
            $this->order->Fields[] = 'total_tariff';
            $this->order->setDefault('created', 'desc');

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $search_type = array();
            $search_type[] = $_GET['type1'];
            $search_type[] = $_GET['type2'];
            $search_type[] = $_GET['type3'];
            $search_type[] = $_GET['type4'];
            $search_type[] = $_GET['type5'];
            $search_type[] = $_GET['type6'];
            $search_type[] = $_GET['type7'];
            $search_type[] = $_GET['type8'];
            $search_type[] = $_GET['type9'];
            $search_type[] = $_GET['type10'];
            $search_type[] = $_GET['type11'];
            $search_type[] = $_GET['type12'];
            $search_type[] = $_GET['type13'];

            $ruri = rawurldecode($_SERVER['REQUEST_URI']);
            $arrStr1 = explode("search_string/", $ruri);
            $arrStr2 = explode("/", $arrStr1[1]);
            $search_string = $arrStr2[0];

            $mtype_string = "";
            $arrMtype1 = explode("mtype/", $ruri);
            if (is_array($arrMtype1) && sizeof($arrMtype1) > 1) {
                $arrMtype2 = explode("/", $arrMtype1[1]);
                if (is_array($arrMtype2) && sizeof($arrMtype2) > 0) {
                    if ($arrMtype2[0] != "") {
                        $mtype_string = " AND e.status=" . $arrMtype2[0];
                    }

                }
            }

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataHeader(Entity::TYPE_ORDER, $search_type, $search_string, $mtype_string, $_SESSION['per_page'], $this->order->getOrder());

            $entitiesCount = $entityManager->getEntitiesArrDataHeaderCount(Entity::TYPE_ORDER, $search_type, $search_string, $mtype_string, $_SESSION['per_page'], $this->order->getOrder());
            $this->daffny->tpl->entitiesCount = $entitiesCount;

            $dispatch_pickup_date = date('Y-m-d');
            if (isset($_POST['dispatch_pickup_date'])) {
                $dispatch_pickup_date = $_POST['dispatch_pickup_date'];
            }

            $todayDispatched = $entityManager->getDispatchedOrders(Entity::TYPE_ORDER, $dispatch_pickup_date);
            $this->daffny->tpl->todayDispatched = $todayDispatched;

            $entities_count = $entityManager->getCountHeader(Entity::TYPE_ORDER);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
            $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
            $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $this->input['dispatch_pickup_date'] = $dispatch_pickup_date;
            $this->form->TextField("dispatch_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, ""), "", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->getPaymentForm();
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function batchnew()
    {
        try {
            $this->tplname = "orders.batch_new";
            $this->title = "Batch Payments";
            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");
            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function batchsubmitnew()
    {

        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->initGlobals();
            $this->tplname = "orders.orders_batch_new";
            $data_tpl = "orders.orders_batch";
            $this->title = "Batch Payments";
            $this->daffny->tpl->status = "Archived";

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));

            $this->input['batch_order_ids'] = $_POST['batch_order_ids'];
            $this->input['shipper_company'] = $_POST['shipper_company'];
            $this->input['shipper_company_id'] = $_POST['shipper_company_id'];

            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");
            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");

            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $batch_order_ids_arr = explode(",", trim($_POST['batch_order_ids']));
            $this->daffny->tpl->batch_order_ids_arr = $batch_order_ids_arr;

            if ($_POST['submit'] == "Start Processing") {
                $this->getPaymentForm();
            }

            $this->daffny->tpl->entities = $entityManager->searchBatch(Entity::TYPE_ORDER, $_POST['shipper_company'], $_POST['batch_order_ids'], $_SESSION['per_page']);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("orders"));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function batchconfirmnew()
    {

        try {
            $this->initGlobals();

            $this->tplname = "orders.orders_batch_confirm_new";
            $data_tpl = "orders.orders_batch_confirm";
            $this->title = "Batch Payments Confirmation";
            $this->daffny->tpl->status = "Archived";

            $info = "Batch Payments Confirmation";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Batch Payments Confirmation'));
            if ($_GET['ids'] != '') {
                $this->daffny->tpl->entities = $entityManager->searchBatchConfirm(Entity::TYPE_ORDER, $_GET['ids'], $_SESSION['per_page']);
                $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
                $this->section = "Orders";
                $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

                $ids_arr = explode(",", trim($_GET['ids']));
                $this->daffny->tpl->batch_order_ids_arr = $ids_arr;
            }
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("orders"));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function upload_insurance_file($element, $carrier, $entity, $type = 0)
    {

        $id = (int) $carrier->id;
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "accounts/insurance/";
        $upload->max_file_size = 3 * 1024 * 1024;
        $upload->form_field = $element;
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf", "gif", "jpeg", "jpg", "jpe", "png");
        $upload->save_as_file_name = md5(time() . "-" . rand()) . time();
        $upload->upload_process();
        $error = "";

        switch ($upload->error_no) {
            case 0:{

                    $insurance_expirationdate = date("Y-m-d", strtotime($_POST['carrier_ins_expire']));

                    $sql_arr = array(
                        'name_original' => $_FILES[$upload->form_field]['name'],
                        'name_on_server' => $upload->save_as_file_name,
                        'size' => $_FILES[$upload->form_field]['size'],
                        'type' => $upload->file_extension,
                        'date_uploaded' => "now()",
                        'owner_id' => getParentId(),
                        'status' => 0,
                        'insurance' => 1,
                        'insurance_type' => $type,
                        "insurance_expirationdate" => $insurance_expirationdate,
                    );
                    $ins_arr = $this->daffny->DB->PrepareSql("app_uploads", $sql_arr);
                    $this->daffny->DB->insert("app_uploads", $ins_arr);
                    $insid = $this->daffny->DB->get_insert_id();

                    $this->daffny->DB->insert("app_accounts_uploads", array(
                        "account_id" => $id,
                        "upload_id" => $insid,
                    ));

                    $carrier_arr1 = array(
                        "insurance_doc_id" => $insid,
                        'insurance_type' => $type,
                        "insurance_expirationdate" => date("Y-m-d", strtotime($_POST['carrier_ins_expire'])),
                    );
                    $carrier->update($carrier_arr1, $carrier->id);
                }
            case 1:
                $error = "ERROR:File not selected or empty";
            case 2:
            case 5:
                $error = "ERROR:Invalid File Extension";
            case 3:
                $error = "ERROR:File too big";
            case 4:
                $error = "ERROR:Cannot move uploaded file";
        }

        if ($error != "") {
            $error = $error . " | account id:" . $id . " | entity_id:" . $entity->id;
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($error);
        }
    }

    protected function getInsuranceCertificate($id)
    {
        $sql = "SELECT u.* FROM  app_uploads u WHERE u.id = '" . $id . "' AND u.owner_id = '" . getParentId() . "' ";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }

    public function searchorders_old()
    {
        try {
            $this->initGlobals();
            $this->tplname = "orders.main";
            $data_tpl = "orders.ordersnewlist";
            $this->title = "Orders search results";

            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'avail_pickup_date';
            $this->order->Fields[] = 'dispatched';
            $this->order->Fields[] = 'delivered';
            $this->order->Fields[] = 'posted';
            $this->order->Fields[] = 'not_signed';
            $this->order->Fields[] = 'issue_date';
            $this->order->Fields[] = 'hold_date';
            $this->order->Fields[] = 'created';
            $this->order->Fields[] = 'total_tariff';
            $this->order->Fields[] = 'archived';
            $this->order->Fields[] = 'status';
            if ($_GET['type1'] == "carrier") {
                $this->order->setDefault('status', 'asc');
            } else {
                $this->order->setDefault('created', 'desc');
            }

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $search_type = array();
            $search_type[] = $_GET['type1'];
            $search_type[] = $_GET['type2'];
            $search_type[] = $_GET['type3'];
            $search_type[] = $_GET['type4'];
            $search_type[] = $_GET['type5'];
            $search_type[] = $_GET['type6'];
            $search_type[] = $_GET['type12'];
            $search_type[] = $_GET['type7'];
            $search_type[] = $_GET['type8'];
            $search_type[] = $_GET['type9'];
            $search_type[] = $_GET['type10'];
            $search_type[] = $_GET['type11'];
            $search_type[] = $_GET['type12'];
            $search_type[] = $_GET['type13'];
            $search_type[] = $_GET['type14'];

            $ruri = rawurldecode($_SERVER['REQUEST_URI']);
            $arrStr1 = explode("search_string/", $ruri);
            
            $arrStr2 = explode("/", $arrStr1[1]);
            $search_string = $arrStr2[0];

            $op = Entity::STATUS_CACTIVE;
            if (isset($_GET['mtype']) && trim($_GET['mtype']) != '' && ctype_digit((string) $_GET['mtype']) && !isset($_GET['tab'])) {
                $mtype_string = " AND e.status=" . $_GET['mtype'];
                $status = $_GET['mtype'];
            } else {
                $mtype_string = "";
                $arrMtype1 = explode("mtype/", $ruri);
                if (is_array($arrMtype1) && sizeof($arrMtype1) > 1) {
                    $arrMtype2 = explode("/", $arrMtype1[1]);
                    if (is_array($arrMtype2) && sizeof($arrMtype2) > 0) {
                        if ($arrMtype2[0] != "") {
                            $mtype_string = " AND e.status=" . $arrMtype2[0];
                            $status = $arrMtype2[0];
                        }
                    }
                }
            }

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));
            $this->daffny->tpl->entities = $entityManager->getEntitiesArrSearch(Entity::TYPE_ORDER, $search_type, $search_string, $mtype_string, $_SESSION['per_page'], $this->order->getOrder(), $op);
            
            if (count($this->daffny->tpl->entities) == 1) {
                $entityOne = $this->daffny->tpl->entities[0];
                $status = $entityOne['status'];
            }

            $this->daffny->tpl->status = $status;
            $entities_count = $entityManager->getEntitiesArrSearchCount(Entity::TYPE_ORDER, $search_type, $search_string, '', $_SESSION['per_page'], $this->order->getOrder());
            $dispatch_pickup_date = date('Y-m-d');

            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
            $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
            $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
            $this->input['imported_lead_count'] = $entities_count[51];
            $this->input['created_lead_count'] = $entities_count[52];
            $this->input['order_count'] = $entities_count[53];

            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $this->input['dispatch_pickup_date'] = $dispatch_pickup_date;
            $this->form->TextField("dispatch_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, ""), "", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->getPaymentForm();

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            echo $this->daffny->DB;
            die("Error Query");
            //redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            echo $this->daffny->DB;
            die("Error Query");
            //$this->setFlashError($e->getMessage());
            //redirect($e->getRedirectUrl());
        }
    }

    public function searchorders()
    {
        try {
            $this->initGlobals();
            $this->tplname = "orders.main";
            $data_tpl = "orders.ordersnewlist";
            $this->title = "Orders search results";

            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'avail_pickup_date';
            $this->order->Fields[] = 'dispatched';
            $this->order->Fields[] = 'delivered';
            $this->order->Fields[] = 'posted';
            $this->order->Fields[] = 'not_signed';
            $this->order->Fields[] = 'issue_date';
            $this->order->Fields[] = 'hold_date';
            $this->order->Fields[] = 'created';
            $this->order->Fields[] = 'total_tariff';
            $this->order->Fields[] = 'archived';
            $this->order->Fields[] = 'status';
            if ($_GET['type1'] == "carrier") {
                $this->order->setDefault('status', 'asc');
            } else {
                $this->order->setDefault('created', 'desc');
            }

            $info = "Search Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $search_type = array();
            $search_type[] = $_GET['type1'];
            $search_type[] = $_GET['type2'];
            $search_type[] = $_GET['type3'];
            $search_type[] = $_GET['type4'];
            $search_type[] = $_GET['type5'];
            $search_type[] = $_GET['type6'];
            $search_type[] = $_GET['type12'];
            $search_type[] = $_GET['type7'];
            $search_type[] = $_GET['type8'];
            $search_type[] = $_GET['type9'];
            $search_type[] = $_GET['type10'];
            $search_type[] = $_GET['type11'];
            $search_type[] = $_GET['type12'];
            $search_type[] = $_GET['type13'];
            $search_type[] = $_GET['type14'];

            $ruri = rawurldecode($_SERVER['REQUEST_URI']);
            $arrStr1 = explode("search_string/", $ruri);
            
            $arrStr2 = explode("/", $arrStr1[1]);
            $search_string = $arrStr2[0];

            $op = Entity::STATUS_CACTIVE;
            if (isset($_GET['mtype']) && trim($_GET['mtype']) != '' && ctype_digit((string) $_GET['mtype']) && !isset($_GET['tab'])) {
                $mtype_string = " AND e.status=" . $_GET['mtype'];
                $status = $_GET['mtype'];
            } else {
                $mtype_string = "";
                $arrMtype1 = explode("mtype/", $ruri);
                if (is_array($arrMtype1) && sizeof($arrMtype1) > 1) {
                    $arrMtype2 = explode("/", $arrMtype1[1]);
                    if (is_array($arrMtype2) && sizeof($arrMtype2) > 0) {
                        if ($arrMtype2[0] != "") {
                            $mtype_string = " AND e.status=" . $arrMtype2[0];
                            $status = $arrMtype2[0];
                        }
                    }
                }
            }

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));
            $this->daffny->tpl->entities = $entityManager->getEntitiesArrSearch(Entity::TYPE_ORDER, $search_type, $search_string, $mtype_string, $_SESSION['per_page'], $this->order->getOrder(), $op);
            
            if (count($this->daffny->tpl->entities) == 1) {
                $entityOne = $this->daffny->tpl->entities[0];
                $status = $entityOne['status'];
            }

            $this->daffny->tpl->status = $status;
            $entities_count = $entityManager->getEntitiesArrSearchCount(Entity::TYPE_ORDER, $search_type, $search_string, '', $_SESSION['per_page'], $this->order->getOrder());

            $dispatch_pickup_date = date('Y-m-d');

            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
            $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
            $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
            $this->input['imported_lead_count'] = $entities_count[51];
            $this->input['created_lead_count'] = $entities_count[52];
            $this->input['order_count'] = $entities_count[53];
            $this->input['quotes_count'] = $entities_count[54];

            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $this->input['dispatch_pickup_date'] = $dispatch_pickup_date;
            $this->form->TextField("dispatch_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, ""), "", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->getPaymentForm();

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            echo $this->daffny->DB;
            die("Error Query");
            //redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            echo $this->daffny->DB;
            die("Error Query");
            //$this->setFlashError($e->getMessage());
            //redirect($e->getRedirectUrl());
        }
    }

    public function matchcarrier()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "orders.match_carrier";
            $this->title = "Order Match Carrier";
            $this->section = "Orders";

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $matchCarrierManager = new MatchCarrierManager($this->daffny->DB);
            $this->daffny->tpl->MatchCarrier = $matchCarrierManager->getMatchCarrier("order by created desc", $_SESSION['per_page'], " owner_id =" . getParentId() . " and entity_id=" . $_GET['id']);
            $this->pager = $matchCarrierManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation(),
                'current_page' => $this->pager->CurrentPage,
                'pages_total' => $this->pager->PagesTotal,
                'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);

            $this->daffny->tpl->entity = $entity;

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect($e->getRedirectUrl());
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect($e->getRedirectUrl());
        }
    }

    public function getPrintedChecks($entity_id)
    {
        $sql = "SELECT * FROM app_payments_check pc WHERE pc.entity_id = '" . $entity_id . "' ORDER BY pc.created";
        $checkList = $this->daffny->DB->selectRows($sql);

        return $checkList;
    }

    public function validateSaveCarrier($sql_arr, $id = 0)
    {

        $carrierFlag = 0;
        {
            if ($sql_arr['company_name'] != "") {
                $rowCarrier = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['company_name']) . "' AND state='" . $sql_arr['state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['city']) . "'  AND `is_carrier` = 1 AND owner_id ='" . getParentId() . "')");
            } else {
                $rowCarrier = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='' AND state='" . $sql_arr['state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['city']) . "' AND `is_carrier` = 1 AND owner_id ='" . getParentId() . "')");
            }
            if (empty($rowCarrier)) {
                $carrierFlag = 1;
            } else {
                if ($rowCarrier["id"] != '' && $sql_arr['company_name'] != "") {
                    $carrierFlag = $rowCarrier["id"];
                }
            }
        }

        return $carrierFlag;
    }

    public function updateAccountHistory($new_arr, $ID)
    {
        $ins_arr = array();
        $old_arr = $this->daffny->DB->select_one("*", "app_accounts", "WHERE id = '" . $ID . "'");
        $change_date = date("Y-m-d H:i:s");
        $changed_by = $_SESSION['member_id'];
        foreach ($old_arr as $key => $value) {
            if (isset($new_arr[$key])) {
                if ($new_arr[$key] != $old_arr[$key]) {
                    $ins_arr[] = array(
                        "account_id" => $ID,
                        "field_name" => $key,
                        "old_value" => $old_arr[$key],
                        "new_value" => $new_arr[$key],
                        "change_date" => $change_date,
                        "changed_by" => $changed_by,
                    );
                }
            }
        }

        if (!empty($ins_arr)) {
            foreach ($ins_arr as $arr) {
                $this->daffny->DB->insert("app_accounts_history", $arr);
            }
        }
    }

    public function review()
    {

        if (isset($_GET['id']) && $_GET['id'] != null) {

            $this->tplname = "orders.reviewDetails";
            $this->title = "Order Review Details";

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Order Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->tpl->entity = $entity;

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();

        } else {
            redirect(getLink('') . "orders/");
        }
    }

    public function mail_history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $this->tplname = "orders.mail_history";
            $this->title = "Order Mail History";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            //$notes = $entity->getNotes(false, $this->order->getOrder());
            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            $sql = "SELECT * FROM entity_email_log WHERE entity_id = '" . $_GET['id'] . "' ORDER BY created_at DESC";
            $history = $this->daffny->DB->selectRows($sql);
            $this->daffny->tpl->history = $history;
            
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));

        } catch (FDException $e) {
            redirect(getLink("quotes"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink("quotes"));
        }
    }
    public function track_n_trace()
    {
        $TRACE_ACTIVITY = array(
            0 => 'NEW RECORD ADDED',
            1 => 'VALUES UPDATED',
            2 => 'RECORD DELETED',
            3 => 'INVALID ACTIVITY',
        );

        try {
            /**
             * Validating URL parsed parameters
             */
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            /**
             * Loading HTML template
             */
            $this->tplname = "orders.track_n_trace";
            $this->title = "Track & Trace";

            /**
             * Loading entity Data
             */
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $notes = $entity->getNotes(false, " order by convert(created,datetime) desc ");

            if ($ds = $entity->getDispatchSheet()) {
                $this->daffny->tpl->dispatchSheet = $ds;
            }

            $this->daffny->tpl->notes = $notes;
            /**
             * Geo location Google APi key
             */
            $key = 'AIzaSyB6dx80YTn7l6imjRElosj-yAH7LsXBmrU';

            // Submit form functionality starts
            if (isset($_POST['submit'])) {

                $state = str_replace(" ", "%20", $_POST['state']);
                $city = str_replace(" ", "%20", $_POST['city']);
                $zip_code = str_replace(" ", "%20", $_POST['zip_code']);
                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $city . "," . $state . "," . $zip_code . "&key=" . $key;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $output = curl_exec($ch);
                curl_close($ch);

                $output = json_decode($output);
                $lat = $output->results[0]->geometry->location->lat;
                $lng = $output->results[0]->geometry->location->lng;
                $entered_by = $_SESSION['member']['id'];
                $entered_by_name = $_SESSION['member']['contactname'];

                $fields = "(entity_id,state,city,zip_code,lat,lng,carrier_id,entered_by_id,entered_by_name,entered_by_comment)";
                $values = "VALUES ( {$_GET['id']},'{$_POST['state']}', '{$_POST['city']}', '{$_POST['zip_code']}', '{$lat}', '{$lng}','{$entity->carrier_id}','{$entered_by}','{$entered_by_name}','{$_POST['comment']}')";
                $sql = "INSERT INTO `app_order_track_n_trace` {$fields} {$values}";

                $this->daffny->DB->query($sql);
                $inserted_id = $this->daffny->DB->get_insert_id();
                $this->log_trace($this->daffny->DB, $_GET['id'], $inserted_id, $TRACE_ACTIVITY[0]);
            }
            // submit form functionality ends

            $sql = "SELECT * FROM `app_order_track_n_trace` WHERE `entity_id` = {$_GET['id']} AND deleted = 0 ORDER BY `id` DESC ";
            $data = $this->daffny->DB->query($sql);
            $track_records = array();

            while ($row = mysqli_fetch_assoc($data)) {
                $track_records[] = $row;
            }

            $sql = "SELECT `Origincity`,`Originstate`,`Originzip`,`Destinationcity`,`Destinationstate`,`Destinationzip` "
                . "FROM `app_order_header` WHERE `entityid` = {$_GET['id']}";
            $data = $this->daffny->DB->query($sql);
            $data = mysqli_fetch_assoc($data);

            $city = str_replace(" ", "%20", $data['Originzip']);
            $state = str_replace(" ", "%20", $data['Originstate']);
            $zip_code = str_replace(" ", "%20", $data['Originzip']);
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $city . "," . $state . "," . $zip_code . "&key=" . $key;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($output);

            $oLat = $output->results[0]->geometry->location->lat;
            $oLng = $output->results[0]->geometry->location->lng;

            $city = str_replace(" ", "%20", $data['Destinationcity']);
            $state = str_replace(" ", "%20", $data['Destinationstate']);
            $zip_code = str_replace(" ", "%20", $data['Destinationzip']);
            $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $city . "," . $state . "," . $zip_code . "&key=" . $key;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);

            $output = json_decode($output);

            $dLat = $output->results[0]->geometry->location->lat;
            $dLng = $output->results[0]->geometry->location->lng;

            $sql = "SELECT * FROM `states`";
            $states = $this->daffny->DB->query($sql);

            $states_array = array();
            while ($row = mysqli_fetch_assoc($states)) {
                $states_array[] = $row;
            }

            $track_records[] = array(
                'lat' => $oLat,
                'lng' => $oLng,
            );

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->tracks = $track_records;
            $this->daffny->tpl->oLat = $oLat;
            $this->daffny->tpl->oLng = $oLng;
            $this->daffny->tpl->dLat = $dLat;
            $this->daffny->tpl->dLng = $dLng;
            $this->daffny->tpl->states = $states_array;

            $this->breadcrumbs = $this->getBreadCrumbs(
                array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber(), "" => "Track & Trace")
            );

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function log_trace($connection, $entity_id, $track_id, $activity)
    {
        $fields = "(track_id,entity_id,activity,logged_by,logged_by_name)";
        $values = "VALUES ( {$track_id},'{$entity_id}', '{$activity}', '{$_SESSION['member']['id']}', '{$_SESSION['member']['contactname']}')";
        $sql = "INSERT INTO `app_tract_track_history` {$fields} {$values}";

        $connection->query($sql);
    }

    public function track_n_trace_history()
    {
        try {

            /**
             * Loading HTML template
             */
            $this->tplname = "orders.track_n_trace_history";
            $this->title = "Track & Trace";

            /**
             * Loading entity Data
             */
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $sql = "SELECT * FROM `app_tract_track_history` WHERE `entity_id` = {$_GET['id']} ORDER BY `id` DESC ";
            $data = $this->daffny->DB->query($sql);
            $track_records = array();

            while ($row = mysqli_fetch_assoc($data)) {
                $track_records[] = $row;
            }

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->tracks = $track_records;
            $this->breadcrumbs = $this->getBreadCrumbs(
                array(
                    getLink("orders") => "Orders",
                    'Order #' . $entity->getNumber(),
                    getLink("orders", "track_n_trace", "id", $_GET['id']) => "Track & Trace",
                    "" => "History",
                )
            );
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    /**
     * Function to delete carrier invoice
     *
     * @author Shahrukh
     * @version 1.0
     */
    public function DeleteCarrierInvoice()
    {
        if (isset($_GET['id']) && isset($_GET['InvoiceID'])) {
            $res = $this->daffny->DB->query("SELECT Invoice FROM Invoices WHERE ID = " . $_GET['InvoiceID']);
            $Invoice = mysqli_fetch_assoc($res)['Invoice'];

            $sql = "DELETE FROM Invoices WHERE ID = " . $_GET['InvoiceID'] . " AND EntityID = " . $_GET['id'];
            $res = $this->daffny->DB->query($sql);

            unlink('../uploads/Invoices/' . $Invoice);
            redirect(getLink('orders', 'payments', 'id', $_GET['id']));

        } else {
            redirect(getLink('orders'));
        }
    }

    /**
     * Function to update carrier invoice data
     *
     * @author Shahrukh
     * @version 1.0
     */
    public function UpdateCarrierInvoice()
    {
        if (isset($_POST['UpdateInvoiceID'])) {
            if (isset($_POST['CarInvoiceAmount']) && ($_POST['CarInvoiceAmount'] != 0)) {
                $entity = new Entity($this->daffny->DB);
                try {
                    $entity->load($_POST['UpdateInvoiceEntityID']);
                } catch (Exception $e) {
                    $this->setFlashError('Invalid Order ID');
                }
                $uploadDate = explode("/", $_POST['CarInvoiceCreated']);
                $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                $update_arr = array(
                    'CarrierName' => $_POST['CarInvoiceName'],
                    'Amount' => $_POST['CarInvoiceAmount'],
                    'ProcessingFees' => $_POST['ProcessingFees'],
                    'FeesType' => $_POST['FeesType'],
                    'PaymentType' => $_POST['CarPayType'],
                    'Age' => $_POST['CarInvoiceAge'],
                    'MaturityDate' => date('Y-m-d', strtotime($uploadDate . ' + ' . $_POST['CarInvoiceAge'] . ' days')),
                    'CreatedAt' => date("Y-m-d", strtotime($_POST['CarInvoiceCreated'])),
                );

                $res = $entity->update(array('balance_paid_by' => $_POST['CarPayType']));

                if ($_FILES['CarInvoiceDoc']['size'] > 0) {
                    // cut file extension
                    $fileExtention = $_FILES['CarInvoiceDoc']['name'];
                    $fileExtention = explode(".", $fileExtention);
                    $fileExtention = $fileExtention[1];

                    $newFileName = $entity->prefix . "-" . $entity->number . "-" . date('Ymdhis') . "." . $fileExtention;

                    // moving uploaded file
                    $targetPath = UPLOADS_PATH . "/Invoices/" . $newFileName;
                    move_uploaded_file($_FILES['CarInvoiceDoc']['tmp_name'], $targetPath);

                    $update_arr['Invoice'] = $newFileName;
                }

                $this->daffny->DB->update('Invoices', $update_arr, "ID = '" . $_POST['UpdateInvoiceID'] . "' ");

            } else {
                $this->setFlashError('Amount cannot be 0');
            }
        }
        redirect(getLink('orders', 'payments', 'id', $_POST['UpdateInvoiceEntityID']));
    }

    /**
     * Funciton to update carrier incvoice status
     *
     * @author Shahrukh
     * @version 1.0
     */
    public function CarrierInvoiceStatusUpdate()
    {
        if (isset($_GET['id']) && isset($_GET['InvoiceID'])) {
            $res = $this->daffny->DB->query("SELECT Hold FROM Invoices WHERE ID = " . $_GET['InvoiceID']);
            $Hold = mysqli_fetch_assoc($res)['Hold'];

            if ($Hold == 0) {
                $this->daffny->DB->query("UPDATE Invoices SET Hold = 1 WHERE ID = " . $_GET['InvoiceID']);
            } else {
                $this->daffny->DB->query("UPDATE Invoices SET Hold = 0 WHERE ID = " . $_GET['InvoiceID']);
            }
            redirect(getLink('orders', 'payments', 'id', $_GET['id']));

        } else {
            redirect(getLink('orders'));
        }
    }

    /**
     * Function to check whether the entity status is being edited by something else or not
     * 
     * @return JSON Response
     * @author Shahrukh
     */
    public function checkBlocked()
    {
        $e = new Entity($this->daffny->DB);
        $e->load($_POST['id']);

        JSONResponse([
            "status"=>true,
            "code"=>200,
            "data"=>[
                "blocked_by" => $e->blocked_by,
                "blocked__by_time" => $e->blocked_time,
                "blocked_by_carrier" => $e->blocked_by_carrier,
                "blocked_by_carrier_time" => $e->blocked_by_carrier_time
            ],
            "message"=>"Test JSON Response"
        ]);
    }
}
