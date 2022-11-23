<?php

require_once DAFFNY_PATH . "libs/upload.php";

class ApplicationBatchpayment extends ApplicationAction
{

    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('orders.common');

        $this->daffny->tpl->form_templates = $this->form->ComboBox('form_templates', array('' => 'Select One') + $this->getFormTemplates("orders"), array('style' => 'width:130px;', 'onChange' => 'printSelectedOrderForm()'), "", "", true);
        $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One') + $this->getEmailTemplates("orders"), array('style' => 'width:130px;', 'onChange' => 'emailSelectedOrderFormNew()'), "", "", true);

        parent::construct();
    }

    public function idx()
    {
        try {
            $this->tplname = "batch.batch";
            $this->title = "Batch Payments";

            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Enter Order ID", "</td><td>");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function batchsubmit()
    {
        try {
            $this->search();

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        }
    }

    public function onhold()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_ONHOLD);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function archived()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_ARCHIVED);
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

            $info = "Unarchive order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);

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

            $this->loadOrdersPageNew(Entity::STATUS_ARCHIVED);

        } catch (FDException $e) {

            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders/archived'));
        }
    }

    public function posted()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_POSTED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function notsigned()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_NOTSIGNED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function dispatched()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_DISPATCHED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function pickedup()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_PICKEDUP);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function delivered()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_DELIVERED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        }
    }

    public function issues()
    {
        try {
            $this->loadOrdersPageNew(Entity::STATUS_ISSUES);
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
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment updated internally for the amount of $ " . number_format((float) $_POST['amount'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment processed internally for the amount of $ " . number_format((float) $_POST['amount'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
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
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment updated internally for the amount of $ " . number_format((float) $_POST['amount_carrier'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                        } else {

                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment processed internally for the amount of $ " . number_format((float) $_POST['amount_carrier'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
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
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string) $_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment updated internally for the amount of $ " . number_format((float) $_POST['amount_terminal'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                        } else {

                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $_GET['id'],
                                "sender_id" => $_SESSION['member_id'],
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 1,
                                "text" => "Payment processed internally for the amount of $ " . number_format((float) $_POST['amount_terminal'], 2, '.', ''));

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }
                        break;
                    case "gateway":

                        $defaultSettings = new DefaultSettings($this->daffny->DB);
                        $defaultSettings->getByOwnerId(getParentId());

                        if (in_array($defaultSettings->current_gateway, array(1, 2))) {

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
                            , "notify_email" => trim($defaultSettings->notify_email)
                            , "order_number" => trim($entity->getNumber()),
                        );

                        $ret = array();

                        if (!count($this->err)) {
                            if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                $ret = $this->processAuthorize($pay_arr);
                            }
                            if ($defaultSettings->current_gateway == 1) { //PayPal
                                $ret = $this->processPayPal($pay_arr);
                            }

                            //place
                            if (isset($ret['success']) && $ret['success'] == true) {

                                //insert
                                $insert_arr['entity_id'] = (int) $_GET['id'];
                                $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
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
                                $payment = new Payment($this->daffny->DB);
                                $payment->create($insert_arr);

                                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) != '' && trim($entity->archived) == '') {
                                    $entity->setStatus(Entity::STATUS_DELIVERED);
                                }

                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "CREDIT CARD PROCESSED FOR THE AMOUNT OF $ " . number_format((float) $pay_arr['amount'], 2, '.', ''));
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);

                                try {
                                    $paymentcard = new Paymentcard($this->daffny->DB);
                                    $pc_arr = $pay_arr;
                                    $pc_arr['entity_id'] = (int) $_GET['id'];
                                    $pc_arr['owner_id'] = getParentId();
                                    $paymentcard->key = $this->daffny->cfg['security_salt'];
                                    $paymentcard->create($pc_arr);
                                } catch (Exception $exc) {}

                                $this->setFlashInfo("Your payment has been processed.");
                                redirect(getLink("orders", "payments", "id", (int) $_GET['id']));
                            } else {
                                $this->err[] = $ret['error'];
                                /* UPDATE NOTE */
                                $note_array = array(
                                    "entity_id" => $entity->id,
                                    "sender_id" => $_SESSION['member_id'],
                                    "type" => 3,
                                    "system_admin" => 1,
                                    "text" => "Payment Error:" . $ret['error']);
                                $note = new Note($this->daffny->DB);
                                $note->create($note_array);
                            }
                        }
                        break;
                    default:
                        throw new UserException("Invalid form data", getLink('orders/payments/id/' . $_GET['id']));
                        break;
                }

                if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) != '' && trim($entity->archived) == '') {
                    $entity->setStatus(Entity::STATUS_DELIVERED);
                }

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

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => 'Orders', getLink('orders/show/id/' . $_GET['id']) => 'Order #' . $entity->getNumber(), '' => 'Payments'));
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
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");

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
            $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
            $this->form->TextField("e_cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
            $this->form->TextField("e_cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
            $this->form->TextField("e_cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");

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

            $balances = array(
                'shipper' => 0,
                'carrier' => 0,
                'pterminal' => 0,
                'dterminal' => 0,
            );

            $depositRemains = 0;
            $shipperRemains = 0;

            // We owe them
            switch ($entity->balance_paid_by) {
                //case Entity::BALANCE_INVOICE_CARRIER:
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

                    $depositRemains = $entity->getTotalDeposit(false) - $shipperPaid;
                    $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;

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

            $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));

            foreach ($balances as $key => $balance) {
                if (stripos($key, '_paid') === false) {
                    if (isset($balances[$key . '_paid'])) {
                        if ($balance > 0) {
                            $this->input[$key] = "<span class='red'>$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '';
                        }
                        if ($balances[$key . '_paid'] > 0) {
                            $this->input[$key . '_paid'] = "<span class='green'>$ " . number_format(abs($balances[$key . '_paid']), 2) . "</span>";
                        } else {
                            $this->input[$key . '_paid'] = '';
                        }
                    } else {
                        if ($balance > 0) {
                            $this->input[$key] = "<span class='red'>$ " . number_format(abs($balance), 2) . "</span>";
                        } else {
                            $this->input[$key] = '$ 0.00';
                        }
                        $this->input[$key . '_paid'] = '';
                    }
                }
            }

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

    public function create()
    {
        try {
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
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];
                    }
                }

                // Create Entity
                $entity = new Entity($this->daffny->DB);
                $insert_arr = array(
                    'type' => Entity::TYPE_ORDER,
                    'creator_id' => $_SESSION['member']['id'],
                    'assigned_id' => $_SESSION['member']['id'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'avail_pickup_date' => empty($sql_arr['avail_pickup_date']) ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date'])),
                    'ship_via' => $sql_arr['shipping_ship_via'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'ordered' => date('Y-m-d H:i:s'),
                    'payments_terms' => $sql_arr['payments_terms'],

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
                    'phone1' => $sql_arr['origin_phone1'],
                    'phone2' => $sql_arr['origin_phone2'],
                    'phone3' => $sql_arr['origin_phone3'],
                    'phone_cell' => $sql_arr['origin_mobile'],
                    'company' => $sql_arr['origin_company_name'],
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
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
                    'phone1' => $sql_arr['destination_phone1'],
                    'phone2' => $sql_arr['destination_phone2'],
                    'phone3' => $sql_arr['destination_phone3'],
                    'phone_cell' => $sql_arr['destination_mobile'],
                    'company' => $sql_arr['destination_company_name'],
                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
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
                        'tariff' => $_POST['carrier_pay'][$key],
                        'deposit' => $_POST['deposit'][$key],
                        'carrier_pay' => $_POST['carrier_pay'][$key] - $_POST['deposit'][$key],
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
                        'phone1' => $sql_arr['shipper_phone1'],
                        'phone2' => $sql_arr['shipper_phone2'],
                        'cell' => $sql_arr['shipper_mobile'],
                        'fax' => $sql_arr['shipper_fax'],
                        'address1' => $sql_arr['shipper_address1'],
                        'address2' => $sql_arr['shipper_address2'],
                        'city' => $sql_arr['shipper_city'],
                        'state' => $sql_arr['shipper_state'],
                        'state_other' => $sql_arr['shipper_state'],
                        'zip_code' => $sql_arr['shipper_zip'],
                        'country' => $sql_arr['shipper_country'],
                        'shipper_type' => $sql_arr['shipper_type'],
                        'hours_of_operation' => $sql_arr['shipper_hours'],
                        'referred_by' => $referrer_name_value,
                        'referred_id' => $sql_arr['referred_by'],

                    );

                    if ($sql_arr['shipper_company']) {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_company']) . "' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");

                    } else {

                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");

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
                        'phone1' => $sql_arr['origin_phone1'],
                        'phone2' => $sql_arr['origin_phone2'],
                        'cell' => $sql_arr['origin_mobile'],
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

                    if ($sql_arr['origin_company_name'] != "") {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_company_name']) . "'  AND state='" . $sql_arr['origin_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['origin_city']) . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $terminal->create($originArr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
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
                        'phone1' => $sql_arr['destination_phone1'],
                        'phone2' => $sql_arr['destination_phone2'],
                        'cell' => $sql_arr['destination_mobile'],
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

                    if ($sql_arr['destination_company_name'] != "") {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE  `company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_company_name']) . "' AND state='" . $sql_arr['destination_state'] . "'  AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['destination_city']) . "' AND `is_location` = 1");

                        if (empty($rowDestLocation)) {
                            $terminal->create($destinationArr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
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

                $accountShipper = $entity->getAccount();
                if ($accountShipper->esigned == 2 && $sql_arr['shipper_type'] == "Commercial") {

                    $files = $entity->getCommercialFilesShipper($accountShipper->id);
                    if (isset($files) && count($files)) {
                        foreach ($files as $file) {

                            $pos = strpos($file['name_original'], "B2B");
                            if ($pos === false) {} else {
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
                $this->setFlashInfo("Order Created");
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
                $this->input['payments_terms'] = $payments_terms;
            } else {
                $this->input['payments_terms'] = $_POST['payments_terms'];
            }

            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), "Carrier Payment Terms", "</td><td>");
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
            redirect('orders');
        } catch (UserException $e) {

            $this->daffny->DB->transaction('rollback');

            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
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
            if ($entity->readonly) {
                throw new UserException("Access Denied", getLink('orders'));
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

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditForm(false, $settings->referrer_status, $entity->status)) {

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
                    'phone1' => $sql_arr['shipper_phone1'],
                    'phone2' => $sql_arr['shipper_phone2'],
                    'mobile' => $sql_arr['shipper_mobile'],
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

                if (isset($_POST['save_shipper'])) {
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
                        'phone1' => $update_arr['phone1'],
                        'phone2' => $update_arr['phone2'],
                        'cell' => $update_arr['mobile'],
                        'fax' => $update_arr['fax'],
                        'address1' => $update_arr['address1'],
                        'address2' => $update_arr['address2'],
                        'city' => $update_arr['city'],
                        'state' => $update_arr['state'],
                        'zip_code' => $update_arr['zip'],
                        'country' => $update_arr['country'],
                        'shipper_type' => $sql_arr['shipper_type'],
                        'hours_of_operation' => $sql_arr['shipper_hours'],
                        'referred_by' => $referrer_name_value,
                        'referred_id' => $sql_arr['referred_by'],
                    );

                    if ($update_arr['company'] != "") {

                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						(`company_name` ='" . $update_arr['company'] . "' AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "'  AND first_name='" . $update_arr['fname'] . "' AND last_name='" . $update_arr['lname'] . "' AND `is_shipper` = 1)");
                    } else {
                        $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						(`company_name` ='' AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "'  AND first_name='" . $update_arr['fname'] . "' AND last_name='" . $update_arr['lname'] . "' AND `is_shipper` = 1)");
                    }
                    if (empty($rowShipper)) {
                        $account->create($account_arr);
                    } else {

                        if ($rowShipper["id"] != '') {
                            unset($account_arr['referred_by']);
                            unset($account_arr['referred_id']);
                            $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $account_arr);
                            $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");
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
                    'phone1' => $sql_arr['origin_phone1'],
                    'phone2' => $sql_arr['origin_phone2'],
                    'phone3' => $sql_arr['origin_phone3'],
                    'phone_cell' => $sql_arr['origin_mobile'],
                    'name2' => $sql_arr['origin_contact_name2'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'fax' => $sql_arr['origin_fax'],
                    'location_type' => $sql_arr['origin_type'],
                    'hours' => $sql_arr['origin_hours'],
                );
                $origin->update($update_arr);

                if (isset($_POST['save_origin'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'phone1' => $update_arr['phone1'],
                        'phone2' => $update_arr['phone2'],
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

                    if ($update_arr['company'] != "") {
                        $rowOriginLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						`company_name` ='" . $update_arr['company'] . "' AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowOriginLocation)) {
                            $account->create($account_arr);
                        } else {
                            if ($rowOriginLocation["id"] != '') {
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
                    'phone1' => $sql_arr['destination_phone1'],
                    'phone2' => $sql_arr['destination_phone2'],
                    'phone3' => $sql_arr['destination_phone3'],
                    'phone_cell' => $sql_arr['destination_mobile'],

                    'name2' => $sql_arr['destination_contact_name2'],
                    'auction_name' => $sql_arr['destination_auction_name'],
                    'booking_number' => $sql_arr['destination_booking_number'],
                    'buyer_number' => $sql_arr['destination_buyer_number'],
                    'fax' => $sql_arr['destination_fax'],
                    'location_type' => $sql_arr['destination_type'],
                    'hours' => $sql_arr['destination_hours'],
                );

                $destination->update($update_arr);

                if (isset($_POST['save_destination'])) {
                    $account = new Account($this->daffny->DB);
                    $account_arr = array(
                        'owner_id' => $_SESSION['member_id'],
                        'is_carrier' => 0,
                        'is_shipper' => 0,
                        'is_location' => 1,
                        'company_name' => $update_arr['company'],
                        'phone1' => $update_arr['phone1'],
                        'phone2' => $update_arr['phone2'],
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

                    if ($update_arr['company'] != "") {
                        $rowDestLocation = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						`company_name` ='" . $update_arr['company'] . "'  AND state='" . $update_arr['state'] . "'  AND city='" . $update_arr['city'] . "' AND `is_location` = 1");

                        if (empty($rowDestLocation)) {
                            $account->create($account_arr);
                        } else {
                            if ($rowDestLocation["id"] != '') {
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

                // Create Internal Notes
                if (trim($sql_arr['note_to_shipper']) != "") {

                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));
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
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'payments_terms' => $sql_arr['payments_terms'],

                );

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
                        'from_phone_1' => $sql_arr['origin_phone1'],
                        'from_phone_2' => $sql_arr['origin_phone2'],
                        'from_phone_cell' => $sql_arr['origin_mobile'],

                        'to_name' => $sql_arr['destination_contact_name'],
                        'to_company' => $sql_arr['destination_company_name'],
                        'to_address' => $sql_arr['destination_address1'],
                        'to_address2' => $sql_arr['destination_address2'],
                        'to_city' => $sql_arr['destination_city'],
                        'to_state' => $sql_arr['destination_state'],
                        'to_zip' => $sql_arr['destination_zip'],
                        'to_country' => $sql_arr['destination_country'],
                        'to_phone_1' => $sql_arr['destination_phone1'],
                        'to_phone_2' => $sql_arr['destination_phone2'],
                        'to_phone_cell' => $sql_arr['destination_mobile'],
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
                $this->input['shipper_phone1'] = $shipper->phone1;
                $this->input['shipper_phone2'] = $shipper->phone2;
                $this->input['shipper_mobile'] = $shipper->mobile;
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
                $this->input['origin_phone1'] = $origin->phone1;
                $this->input['origin_phone2'] = $origin->phone2;
                $this->input['origin_phone3'] = $origin->phone3;
                $this->input['origin_mobile'] = $origin->phone_cell;
                $this->input['origin_buyer_number'] = $entity->buyer_number;
                $this->input['origin_booking_number'] = $entity->booking_number;

                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
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
                $this->input['destination_phone1'] = $destination->phone1;
                $this->input['destination_phone2'] = $destination->phone2;
                $this->input['destination_phone3'] = $destination->phone3;
                $this->input['destination_mobile'] = $destination->phone_cell;

                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['destination_booking_number'] = $destination->booking_number;
                $this->input['destination_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
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
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['pickup_terminal_fee'] = $entity->pickup_terminal_fee;
                $this->input['delivery_terminal_fee'] = $entity->dropoff_terminal_fee;
                $this->input['payments_terms'] = $entity->payments_terms;

                /* Load Shipper Note */
                $notes = $entity->getNotes();
                if (isset($notes[Note::TYPE_FROM][0])) {
                    $this->input['notes_from_shipper'] = $notes[Note::TYPE_FROM][0]->text;
                } else {
                    $this->input['notes_from_shipper'] = "";
                }

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
            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($settings->referrer_status);

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

            /********** Log ********/
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
            $notes = $entity->getNotes(false, " order by id desc ");

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
            $notes = $entity->getNotes(false, " order by id desc ");

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
            //print $e;
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

        $this->form->ComboBox('issue_type',
            array('' => 'Select One', '1' => 'By Customer', '2' => 'By Carrier', '3' => 'Partial payments'), array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "getElementById('issue_form').submit();"), '', '</td><td>');

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

        $this->form->ComboBox('issue_type',
            array('' => 'Select One', '1' => 'By Customer', '2' => 'By Carrier', '3' => 'Partial payments'), array("elementname" => "select", "style" => "width:150px;", "class" => "elementname", "onchange" => "getElementById('issue_form').submit();"), '', '</td><td>');

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
        $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");

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
        $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
        $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Body", "</td><td>");

        // Dispatch Sheet Fields
        $this->form->TextField('carrier_company', 255, array('tabindex' => 10), $this->requiredTxt . "Carrier", "</td><td>");
        $this->form->TextField('carrier_print_name', 255, array('tabindex' => 15), $this->requiredTxt . "Print Check As", "</td><td>");
        $this->form->TextField('carrier_insurance_iccmcnumber', 255, array('tabindex' => 20), $this->requiredTxt . "ICC MC Number", "</td><td>");
        $this->form->TextField('carrier_address', 255, array('tabindex' => 25), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('carrier_city', 255, array('class' => 'geo-city', 'tabindex' => 30), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('carrier_state', array('' => 'Select One') + $this->getAllStates(), array('style' => 'width:150px', 'tabindex' => 40), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('carrier_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 50), "", "");
        $this->form->ComboBox('carrier_country', $this->getCountries(), array('tabindex' => 60), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('carrier_email', 255, array('tabindex' => 70), $this->requiredTxt . "Email", "</td><td>");

        $this->form->ComboBox('carrier_type', array('' => 'Select One') + Account::$carrier_types, array('style' => 'width:100px', 'tabindex' => 75), "", "");
        $this->form->TextField('carrier_contact', 255, array('tabindex' => 80), "Contact", "</td><td>");
        $this->form->TextField('carrier_phone1', 255, array('class' => 'phone', 'tabindex' => 90), $this->requiredTxt . "Phone (1)", "</td><td>");
        $this->form->TextField('carrier_phone2', 255, array('class' => 'phone', 'tabindex' => 100), "Phone (2)", "</td><td>");
        $this->form->TextField('carrier_fax', 255, array('class' => 'phone', 'tabindex' => 110), "Phone (Fax)", "</td><td>");
        $this->form->TextField('carrier_cell', 255, array('class' => 'phone', 'tabindex' => 120), "Phone (Cell)", "</td><td>");
        $this->form->TextField('carrier_driver', 255, array('tabindex' => 130), "Driver", "</td><td>");
        $this->form->TextField('carrier_driver_phone', 255, array('class' => 'phone', 'tabindex' => 140), "Driver Phone", "</td><td>");

        $this->form->ComboBox('order_load_date_type', array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px', 'tabindex' => 150), $this->requiredTxt . "Load Date", "</td><td>");
        $this->form->TextField('order_load_date', 10, array('style' => 'width: 100px;', 'tabindex' => 160));
        $this->form->ComboBox('order_delivery_date_type', array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px', 'tabindex' => 170), $this->requiredTxt . "Delivery Date", "</td><td>");
        $this->form->TextField('order_delivery_date', 10, array('style' => 'width: 100px;', 'tabindex' => 180));
        $this->form->ComboBox('order_ship_via', Entity::$ship_via_string, array('tabindex' => 190), $this->requiredTxt . "Ship Via", "</td><td>");
        $this->form->TextArea('order_notes_from_shipper', 3, 5, array('style' => 'width:215px;height:50px', 'tabindex' => 215), 'Dispatch Instructions', '</td><td>');
        $this->form->TextArea('payments_terms', 3, 5, array('style' => 'width:215px;height:50px', 'tabindex' => 215), 'Payment Terms', '</td><td>');
        $this->form->CheckBox('order_include_shipper_comment', array('tabindex' => 216), 'Include Shipper Comment on Dispatch Sheet', '&nbsp;');

        $this->form->MoneyField('order_company_owes_carrier', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 220), "Company owes Carrier", "</td><td>");
        $this->form->MoneyField('order_carrier_ondelivery', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 230), "Carrier owes Company", "</td><td>");
        $this->form->MoneyField('order_carrier_pay', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 230), "Carrier Pay (total)", "</td><td>");
        $this->form->ComboBox("order_balance_paid_by", Entity::$balance_paid_by_string, array('tabindex' => 240), $this->requiredTxt . "Balance Paid By", "</td><td>");
        $this->form->MoneyField('order_pickup_terminal_fee', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 242), "Pickup Terminal Fee", "</td><td>");
        $this->form->MoneyField('order_dropoff_terminal_fee', 16, array( /*'readonly' => 'readonly',*/'tabindex' => 243), "Delivery Terminal Fee", "</td><td valign='top'>");

        $this->form->TextField('pickup_name', 255, array('tabindex' => 250), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField('pickup_company', 255, array('tabindex' => 260), "Company", "</td><td>");
        $this->form->TextField('pickup_address1', 255, array('tabindex' => 270), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('pickup_address2', 255, array('tabindex' => 280), "Address (2)", "</td><td>");
        $this->form->TextField('pickup_city', 255, array('class' => 'geo-city', 'tabindex' => 290), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('pickup_state', array('' => 'Select One') + $this->getAllStates(), array('style' => 'width:160px', 'tabindex' => 300), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('pickup_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 310), "", "");
        $this->form->ComboBox('pickup_country', $this->getCountries(), array('tabindex' => 320), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('pickup_phone1', 32, array('class' => 'phone', 'tabindex' => 330), $this->requiredTxt . "Phone 1", "</td><td>");
        $this->form->TextField('pickup_phone2', 32, array('class' => 'phone', 'tabindex' => 340), "Phone 2", "</td><td>");
        $this->form->TextField('pickup_cell', 32, array('class' => 'phone', 'tabindex' => 350), "Cell", "</td><td>");
        $this->form->TextField("from_booking_number", 20, array('tabindex' => 24), "Booking Number", "</td><td>");
        $this->form->TextField("from_buyer_number", 20, array('tabindex' => 25), "Buyer Number", "</td><td>");

        $this->form->TextField('deliver_name', 255, array('tabindex' => 360), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField('deliver_company', 255, array('tabindex' => 370), "Company", "</td><td>");
        $this->form->TextField('deliver_address1', 255, array('tabindex' => 380), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField('deliver_address2', 255, array('tabindex' => 390), "Address (2)", "</td><td>");
        $this->form->TextField('deliver_city', 255, array('class' => 'geo-city', 'tabindex' => 400), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('deliver_state', array('' => 'Select One') + $this->getAllStates(), array('style' => 'width:160px', 'tabindex' => 410), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('deliver_zip', 10, array('class' => 'zip', 'style' => 'width: 65px', 'tabindex' => 420), "", "");
        $this->form->ComboBox('deliver_country', $this->getCountries(), array('tabindex' => 430), $this->requiredTxt . "Country", "</td><td>");
        $this->form->TextField('deliver_phone1', 32, array('class' => 'phone', 'tabindex' => 440), $this->requiredTxt . "Phone 1", "</td><td>");
        $this->form->TextField('deliver_phone2', 32, array('class' => 'phone', 'tabindex' => 450), "Phone 2", "</td><td>");
        $this->form->TextField('deliver_cell', 32, array('class' => 'phone', 'tabindex' => 460), "Cell", "</td><td>");

        $this->form->TextField("to_booking_number", 20, array('tabindex' => 24), "Booking Number", "</td><td>");
        $this->form->TextField("to_buyer_number", 20, array('tabindex' => 25), "Buyer Number", "</td><td>");
    }

    protected function getEditForm($referrer_status = 0)
    {
        $member = new Member($this->daffny->DB);
        $member->load($_SESSION['member_id']);
        $this->daffny->tpl->isAutoQuoteAlowed = $member->isAutoQuoteAllowed();
        /* SHIPPER */
        $this->form->ComboBox("shipper", array("" => "New Shipper"), array('style' => 'width:190px;'), "Select Shipper", "</td><td>");
        $this->form->TextField("shipper_fname", 32, array('tabindex' => 1, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array('tabindex' => 2, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "Last Name", "</td><td>");

        $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");
        $this->form->ComboBox('shipper_type',
            array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . 'Shipper Type    ', '</td><td>');

        $this->form->TextField("shipper_hours", 200, array('tabindex' => 5), "Hours", "</td><td>");
        $this->form->TextField("shipper_email", 100, array('class' => 'email', 'tabindex' => 6, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "Email", "</td><td>");

        $this->form->TextField("shipper_phone1", 32, array('class' => 'phone', 'tabindex' => 7, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone2", 32, array('class' => 'phone', 'tabindex' => 8), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone', 'tabindex' => 9), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('class' => 'phone', 'tabindex' => 10), "Fax", "</td><td>");

        $this->form->TextField("shipper_address1", 64, array('tabindex' => 12), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array('tabindex' => 13), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city', 'tabindex' => 14), "City", "</td><td>");

        $this->form->ComboBox('shipper_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:130px;', 'tabindex' => 15, "elementname" => "select", "class" => "elementname"), "State/Zip", "</td><td>", true);

        $this->form->TextField("shipper_zip", 8, array('style' => 'width:70px;margin-left:7px;', 'class' => 'zip', 'tabindex' => 16), "", "");
        $this->form->ComboBox("shipper_country", $this->getCountries(), array('tabindex' => 17), "Country", "</td><td>");
        /* ORIGIN */
        $this->form->TextField("origin_address1", 255, array('tabindex' => 18), "Address", "</td><td>");
        $this->form->TextField("origin_address2", 255, array('tabindex' => 19), "&nbsp;", "</td><td>");
        $this->form->TextField("origin_city", 255, array('class' => 'geo-city', 'tabindex' => 20, "elementname" => "input", "class" => "elementname"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('origin_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:140px;', 'tabindex' => 21, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 10, array('style' => 'width:70px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 22), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array('tabindex' => 23), "Country", "</td><td>");
        $this->form->ComboBox('origin_type',
            array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 24, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("origin_hours", 200, array('tabindex' => 25), "Hours", "</td><td>");
        /* ORIGIN CONTACT */
        $this->form->CheckBox("origin_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("origin_contact_name", 255, array('tabindex' => 26), "Contact Name", "</td><td>");
        $this->form->TextField("origin_contact_name2", 255, array('tabindex' => 27), "Contact Name 2", "</td><td>");
        $this->form->TextField("origin_company_name", 255, array('tabindex' => 28), "Company Name", "</td><td>");
        $this->form->TextField("origin_auction_name", 255, array('tabindex' => 29), "Auction Name", "</td><td>");
        $this->form->TextField("origin_booking_number", 100, array('tabindex' => 30), "Booking Number", "</td><td>");
        $this->form->TextField("origin_buyer_number", 100, array('tabindex' => 31), "Buyer Number", "</td><td>");
        $this->form->TextField("origin_phone1", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 32), "Phone 1", "</td><td>");
        $this->form->TextField("origin_phone2", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 33), "Phone 2", "</td><td>");
        $this->form->TextField("origin_phone3", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 34), "Phone 3", "</td><td>");
        $this->form->TextField("origin_mobile", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 35), "Mobile", "</td><td>");

        $this->form->TextField("origin_fax", 32, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 36), "Fax", "</td><td>");

        /* DESTINATION */
        $this->form->TextField("destination_address1", 255, array('tabindex' => 37), "Address", "</td><td>");
        $this->form->TextField("destination_address2", 255, array('tabindex' => 38), "&nbsp;", "</td><td>");
        $this->form->TextField("destination_city", 255, array('class' => 'geo-city', 'tabindex' => 39, "elementname" => "input", "class" => "elementname"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:140px;', 'tabindex' => 40, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 10, array('style' => 'width:70px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 41), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array('tabindex' => 42), "Country", "</td><td>");
        $this->form->ComboBox('destination_type',
            array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 43, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("destination_hours", 200, array('tabindex' => 44), "Hours", "</td><td>");
        /* DESTINATION CONTACT */
        $this->form->CheckBox("destination_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("destination_contact_name", 255, array('tabindex' => 45), "Contact Name", "</td><td>");
        $this->form->TextField("destination_contact_name2", 255, array('tabindex' => 46), "Contact Name 2", "</td><td>");
        $this->form->TextField("destination_company_name", 255, array('tabindex' => 47), "Company Name", "</td><td>");
        $this->form->TextField("destination_auction_name", 255, array('tabindex' => 48), "Auction Name", "</td><td>");
        $this->form->TextField("destination_booking_number", 100, array('tabindex' => 49), "Booking Number", "</td><td>");
        $this->form->TextField("destination_buyer_number", 100, array('tabindex' => 50), "Buyer Number", "</td><td>");
        $this->form->TextField("destination_phone1", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 51), "Phone 1", "</td><td>");
        $this->form->TextField("destination_phone2", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 52), "Phone 2", "</td><td>");
        $this->form->TextField("destination_phone3", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 53), "Phone 3", "</td><td>");
        $this->form->TextField("destination_mobile", 255, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 54), "Mobile", "</td><td>");

        $this->form->TextField("destination_fax", 32, array('class' => 'phone', 'style' => 'width: 160px', 'tabindex' => 55), "Fax", "</td><td>");

        /* SHIPPING INFORMATION */
        $this->form->TextField("avail_pickup_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 56), $this->requiredTxt . "1st Avail. Pickup Date", "</td><td>");
        $this->form->ComboBox("load_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 57), "Load Date", "</td><td>");
        $this->form->TextField("load_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 58));
        $this->form->ComboBox("delivery_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 59), "Delivery Date", "</td><td>");
        $this->form->TextField("delivery_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 60));
        $this->form->ComboBox("shipping_vehicles_run", array('' => 'Select One') + Entity::$vehicles_run_string, array('tabindex' => 61, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . "Vehicle(s) Run", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", array('' => 'Select One') + Entity::$ship_via_string, array('tabindex' => 62, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . "Ship Via", "</td><td valign=\"top\">");
        $this->form->TextArea("notes_for_shipper", 2, 10, array('style' => 'height:40px;', 'tabindex' => 63), "Information for Shipper", "</td><td>");
        $this->form->TextArea("notes_from_shipper", 2, 10, array('style' => 'height:40px;', 'tabindex' => 64), "Dispatch Instructions", "</td><td>");
        $shipper_comment_attr = array('tabindex' => 65);
        if (isset($this->input['include_shipper_comment']) && $this->input['include_shipper_comment'] == "1") {
            $shipper_comment_attr["checked"] = "checked";
        }

        $this->form->CheckBox("include_shipper_comment", $shipper_comment_attr, "Include Shipper Comment on Dispatch Sheet", "&nbsp;");
        /* PRICING INFORMATION */
        $balance_paid_by = array(
            '' => 'Select One',
            //Entity::BALANCE_ADDITIONAL => 'Additional Shipper Pre-payment',
            'Cash on Delivery to Carrier' => array(
                Entity::BALANCE_COD_TO_CARRIER_CASH => '1 - Cash/Certified Funds',
                Entity::BALANCE_COD_TO_CARRIER_CHECK => '2 - Check',
            ),

            'Cash on Pickup to Carrier' => array(
                Entity::BALANCE_COP_TO_CARRIER_CASH => '1 - Cash/Certified Funds',
                Entity::BALANCE_COP_TO_CARRIER_CHECK => '2 - Check',
            ),
            'Broker on Pickup to Carrier' => array(
                Entity::BALANCE_COMPANY_OWES_CARRIER_CASH => '1 - Cash/Certified Funds',
                Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK => '2 - Check',
                Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => '3 - Comcheck',
                Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => '4 - QuickPay',
                Entity::BALANCE_COMPANY_OWES_CARRIER_ACH => '5 - ACH',
            ),
            'Carrier Payment to Broker' => array(
                Entity::BALANCE_CARRIER_OWES_COMPANY_CASH => '1 - Cash/Certified Funds',
                Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK => '2 - Check',
                Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => '3 - Comcheck',
                Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => '4 - QuickPay',
            ),
        );
        $this->form->ComboBox("balance_paid_by", $balance_paid_by, array('tabindex' => 68, "elementname" => "select", "class" => "elementname"), $this->requiredTxt . "How is the carrier getting paid?", "</td><td>");

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

        $this->form->ComboBox("customer_balance_paid_by", $costomer_balance_paid_by, array('tabindex' => 69, "elementname" => "select", "class" => "elementname", "onchange" => "selectPayment();"), $this->requiredTxt . "How is the customer paying us?", "</td><td>");

        $this->form->TextField("pickup_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 66), "Pickup Terminal Fee", "</td><td>$&nbsp;");
        $this->form->TextField("delivery_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 67), "Delivery Terminal Fee", "</td><td>$&nbsp;");

        //Credit Card Information
        $this->form->TextField("e_cc_fname", 50, array('tabindex' => 70), "First Name", "</td><td>");
        $this->form->TextField("e_cc_lname", 50, array('tabindex' => 71), "Last Name", "</td><td>");
        $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array('tabindex' => 72, "style" => "width:150px;"), "Type", "</td><td>");

        $this->form->TextField("e_cc_number", 16, array('tabindex' => 73, "class" => "creditcard"), "Card Number", "</td><td>");
        $this->form->TextField("e_cc_cvv2", 4, array('tabindex' => 74, "class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
        $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array('tabindex' => 75, "style" => "width:50px;"), "Exp. Date", "</td><td>");
        $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array('tabindex' => 76, "style" => "width:75px;"), "", "");

        $this->form->TextField("e_cc_address", 255, array('tabindex' => 77), "Address", "</td><td>");
        $this->form->TextField("e_cc_city", 100, array('tabindex' => 78), "City", "</td><td>");
        $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array('tabindex' => 79, "style" => "width:150px;"), "State", "</td><td>");
        $this->form->TextField("e_cc_zip", 11, array('tabindex' => 80, "class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");

        if ($this->input['referred_by'] == "" || $this->input['referred_by'] == 0) {
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

        $this->form->ComboBox("referred_by", $referrers_arr, array('tabindex' => 11), $this->requiredTxt . "Referred By", "</td><td>");
        $this->form->TextArea("note_to_shipper", 4, 10, array('style' => 'height: 80px;width:800px;', 'tabindex' => 56), "", "</td><td align='center'>");
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
            'origin_zip' => 'Pickup Zip',
            'origin_type' => 'Origin Location Type',
            'destination_type' => 'Destination Location Type',
            'destination_city' => "Delivery City",
            'destination_country' => 'Delivery Country',
            'destination_zip' => 'Delivery Zip',
            'avail_pickup_date' => '1st Avail. Pickup Date',
            'shipping_ship_via' => 'Ship Via',
            'balance_paid_by' => 'Balance Paid By',
        );

        {
            $checkEmpty['referred_by'] = "Referred By";
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
        if ((trim(post_var("origin_state")) == "") && (trim(post_var("origin_state2")) == "")) {
            $this->isEmpty('origin_state', "Pickup State");
        }
        if ((trim(post_var("destination_state")) == "") && (trim(post_var("destination_state2")) == "")) {
            $this->isEmpty('destination_state', "Delivery State");
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

    public function search()
    {

        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->tplname = "orders.main";
            $data_tpl = "orders.orders";
            $this->title = "Orders search results";
            $this->daffny->tpl->status = "Archived";

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));

            $this->daffny->tpl->entities = $entityManager->searchbatch(Entity::TYPE_ORDER, $_POST['batch_order_ids'], $_SESSION['per_page']);
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
            $this->getPaymentForm();
        } catch (FDException $e) {
            redirect(getLink("orders"));
        } catch (UserException $e) {
            redirect($e->getRedirectUrl());
        }
    }

    public function searchIssue()
    {

        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->initGlobals();
            print_r($_POST);
            $this->tplname = "orders.main";
            $data_tpl = "orders.ordersissue";
            $this->title = "Orders search results";
            $this->daffny->tpl->status = "Archived";

            $info = "Search Issues Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));

            $this->daffny->tpl->entities = $entityManager->searchIssue(Entity::TYPE_ORDER, $_POST['issue_type'], $_SESSION['per_page']);

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

    /* Upload documents */
    public function upload_file()
    {
        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "entity/";
        $upload->max_file_size = 100 * 1024 * 1024;
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
            if ($entity->readonly) {
                throw new UserException("Access Denied", getLink('orders'));
            }

            $info = "Duplicate Order";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->DB->transaction();

            // Create Entity
            $entityNew = new Entity($this->daffny->DB);
            $insert_arr = array(
                'type' => $entity->type,
                'creator_id' => $entity->assigned_id,
                'assigned_id' => $entity->assigned_id,
                'email_id' => $entity->email_id,
                'source_id' => $entity->source_id,
                'before_assigned_id' => $entity->before_assigned_id,
                'carrier_id' => $entity->carrier_id,
                'received' => $entity->received,
                'distance' => $entity->distance,
                'est_ship_date' => $entity->est_ship_date,
                'load_date' => $entity->load_date,
                'load_date_type' => $entity->load_date_type,
                'delivery_date' => $entity->delivery_date,
                'delivery_date_type' => $entity->delivery_date_type,
                'avail_pickup_date' => $entity->avail_pickup_date,
                'actual_pickup_date' => $entity->actual_pickup_date,
                'vehicles_run' => $entity->vehicles_run,
                'ship_via' => $entity->ship_via,
                'referred_by' => $entity->referred_by,
                'buyer_number' => $entity->buyer_number,
                'booking_number' => $entity->booking_number,
                'status' => $entity->status,
                'status_update' => $entity->status_update,
                'created' => $entity->created,
                'quoted' => $entity->quoted,
                'ordered' => $entity->ordered,
                'posted' => $entity->posted,
                'pickedup' => $entity->pickedup,
                'delivered' => $entity->delivered,
                'dispatched' => $entity->dispatched,
                'archived' => $entity->archived,
                'not_signed' => $entity->not_signed,
                'issue_date' => $entity->issue_date,
                'hold_date' => $entity->hold_date,

                'information' => $entity->information,
                'include_shipper_comment' => $entity->include_shipper_comment,
                'balance_paid_by' => $entity->balance_paid_by,
                'blocked_by' => $entity->blocked_by,
                'blocked_time' => $entity->blocked_time,
                'is_reimbursable' => $entity->is_reimbursable,
                'pickup_terminal_fee' => $entity->pickup_terminal_fee,
                'dropoff_terminal_fee' => $entity->dropoff_terminal_fee,
                'total_tariff_stored' => $entity->total_tariff_stored,
                'carrier_pay_stored' => $entity->carrier_pay_stored,
                'is_firstfollowup' => $entity->is_firstfollowup,
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

            $notes = $entity->getNotes();
            // Create Notes
            if (count($notes[Note::TYPE_INTERNAL]) > 0) {
                foreach ($notes[Note::TYPE_INTERNAL] as $note) {
                    $noteNew = new Note($this->daffny->DB);
                    $noteNew->create(array('entity_id' => $entityNew->id, 'sender_id' => $note->sender_id, 'text' => $note->text, 'type' => $note->type));
                }
            }

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

            $inp = $this->daffny->DB->selectRow("id", "app_dispatch_sheets", "WHERE entity_id='" . $entity->id . "' limit 0,1");

            if (!empty($inp)) {
                $ds = $entity->getDispatchSheet();

                $order_sql_arr = array(
                    'carrier_id' => $ds->carrier_id,
                    'entity_id' => $entityNew->id,
                    'order_number' => $entityNew->getNumber(),
                    'c_companyname' => $ds->c_companyname,
                    'c_address1' => $ds->c_address1,
                    'c_address2' => $ds->c_address2,
                    'c_city' => $ds->c_city,
                    'c_state' => $ds->c_state,
                    'c_zip_code' => $ds->c_zip_code,
                    'c_phone' => $ds->c_phone,
                    'c_dispatch_contact' => $ds->c_dispatch_contact,
                    'c_dispatch_phone' => $ds->c_dispatch_phone,
                    'c_dispatch_fax' => $ds->c_dispatch_fax,
                    'c_icc_mc_number' => $ds->c_icc_mc_number,
                    'carrier_company_name' => $ds->carrier_company_name,
                    'carrier_contact_name' => $ds->carrier_contact_name,
                    'carrier_email' => $ds->carrier_email,
                    'carrier_phone_1' => $ds->carrier_phone_1,
                    'carrier_phone_2' => $ds->carrier_phone_2,
                    'carrier_phone_cell' => $ds->carrier_phone_cell,
                    'carrier_fax' => $ds->carrier_fax,
                    'carrier_driver_name' => $ds->carrier_driver_name,
                    'carrier_driver_phone' => $ds->carrier_driver_phone,
                    'carrier_address' => $ds->carrier_address,
                    'carrier_city' => $ds->carrier_city,
                    'carrier_state' => $ds->carrier_state,
                    'carrier_zip' => $ds->carrier_zip,
                    'carrier_country' => $ds->carrier_country,
                    'carrier_print_name' => $ds->carrier_print_name,
                    'carrier_insurance_iccmcnumber' => $ds->carrier_insurance_iccmcnumber,
                    'carrier_type' => $ds->carrier_type,
                    'entity_load_date' => $ds->entity_load_date,
                    'entity_load_date_type' => $ds->entity_load_date_type,
                    'entity_delivery_date' => $ds->entity_delivery_date,
                    'entity_delivery_date_type' => $ds->entity_delivery_date_type,
                    'entity_ship_via' => $ds->entity_ship_via,
                    'entity_vehicles_run' => $ds->entity_vehicles_run,
                    'entity_price' => $ds->entity_price,
                    'entity_carrier_pay' => $ds->entity_carrier_pay,
                    'entity_carrier_pay_c' => $ds->entity_carrier_pay_c,
                    'entity_odtc' => $ds->entity_odtc,
                    'entity_coc' => $ds->entity_coc,
                    'entity_coc_c' => $ds->entity_coc_c,
                    'entity_booking_number' => $ds->entity_booking_number,
                    'entity_buyer_number' => $ds->entity_buyer_number,
                    'entity_pickup_terminal_fee' => $ds->entity_pickup_terminal_fee,
                    'entity_dropoff_terminal_fee' => $ds->entity_dropoff_terminal_fee,
                    'entity_balance_paid_by' => $ds->entity_balance_paid_by,
                    'from_name' => $ds->from_name,
                    'from_company' => $ds->from_company,
                    'origin_auction_name' => $ds->origin_auction_name,
                    'from_address' => $ds->from_address,
                    'from_address2' => $ds->from_address2,
                    'from_city' => $ds->from_city,
                    'from_state' => $ds->from_state,
                    'from_zip' => $ds->from_zip,
                    'from_country' => $ds->from_country,
                    'from_phone_1' => $ds->from_phone_1,
                    'from_phone_2' => $ds->from_phone_2,
                    'from_phone_cell' => $ds->from_phone_cell,
                    'to_name' => $ds->to_name,
                    'to_company' => $ds->to_company,
                    'to_address' => $ds->to_address,
                    'to_address2' => $ds->to_address2,
                    'to_city' => $ds->to_city,
                    'to_state' => $ds->to_state,
                    'to_zip' => $ds->to_zip,
                    'to_country' => $ds->to_country,
                    'to_phone_1' => $ds->to_phone_1,
                    'to_phone_2' => $ds->to_phone_2,
                    'to_phone_cell' => $ds->to_phone_cell,
                    'instructions' => $ds->instructions,
                    'dispatch_terms' => $ds->dispatch_terms,
                    'created' => $ds->created,
                    'accepted' => $ds->accepted,
                    'rejected' => $ds->rejected,
                    'cancelled' => $ds->cancelled,
                    'signed' => $ds->signed,
                    'status' => $ds->status,
                    'deleted' => $ds->deleted,
                    'modified_by' => $ds->modified_by,
                    'modified_ip' => $ds->modified_ip,
                    'signature' => $ds->signature,
                    'hash_link' => $ds->hash_link,
                    'expired' => $ds->expired,
                );

                $dispatchSheetNew = new DispatchSheet($this->daffny->DB);
                $dispatch_id = $dispatchSheetNew->create($order_sql_arr);

                $vehicleManager = new VehicleManager($this->daffny->DB);
                $vehicles = $vehicleManager->getVehicles($entity->id);
                foreach ($vehicles as $vehicle) {

                    $vehicle->cloneForDispatch($dispatch_id);
                }

            }

            $this->daffny->DB->transaction("commit");
            redirect(getLink('orders/edit/id/' . $entityNew->id));

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            redirect($e->getRedirectUrl());
        }
    }

}
