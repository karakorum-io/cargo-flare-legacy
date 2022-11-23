<?php

class ApplicationBilling extends ApplicationAction
{

    public $title = "My Billing";
    public $section = "My Billing";
    public $tplname = "myaccount.billing.billing";

    public function construct()
    {
        if (!$this->check_access('preferences')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Profile", '' => "Billing"));
            $this->check_access("settings");
            if (isset($_POST['submit'])) {
                $sql_arr = array(
                    "billing_autopay" => (post_var("billing_autopay") == "1" ? 1 : 0)
                    , "billing_cc_id" => post_var("billing_cc_id"),
                );

                if ($sql_arr['billing_autopay'] == 1) {
                    $this->isZero("billing_cc_id", "Use Card");
                }

                if (!count($this->err)) {
                    $upd_arr = $this->daffny->DB->PrepareSql("app_defaultsettings", $sql_arr);
                    $this->daffny->DB->update("app_defaultsettings", $upd_arr, "owner_id = '" . getParentId() . "'");
                    if ($this->dbError()) {
                        return;
                    } else {
                        $this->setFlashInfo("Company profile has been updated.");
                        redirect(getLink("billing"));
                    }
                } else {
                    $inp = $sql_arr;
                }
            } else {
                $inp = $this->daffny->DB->selectRow("*", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");
            }

            foreach ($inp as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $billingm = new BillingManager($this->daffny->DB);
            $billingm->key = $this->daffny->cfg['security_salt'];
            $billingm->owner_id = getParentId();

            $this->form->CheckBox("billing_autopay", array(), "Enable Autopay", "&nbsp;");
            $this->form->ComboBox("billing_cc_id", $billingm->getCards(), array(), "Use Card", "</td><td>");
            $this->form->ComboBox("addon_aq_billing_cc_id", $billingm->getCards(), array(), "Use Card", "</td><td>");
            $this->form->TextField("cc_fname", 50, array(), $this->requiredTxt . "First Name", "</td><td>");
            $this->form->TextField("cc_lname", 50, array(), $this->requiredTxt . "Last Name", "</td><td>");
            $this->form->TextField("cc_address", 255, array(), $this->requiredTxt . "Address", "</td><td>");
            $this->form->TextField("cc_city", 100, array(), $this->requiredTxt . "City", "</td><td>");
            $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), $this->requiredTxt . "State", "</td><td>");
            $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), $this->requiredTxt . "Zip Code", "</td><td>");
            $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), $this->requiredTxt . "CVV", "</td><td>");
            $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");

            //Info
            $cur_bal = $billingm->getCurrentBalance();
            $this->input['current_balance'] = number_format($cur_bal, 2, ".", ",");
            if ($cur_bal >= 0) {
                $this->input['bal_style'] = "color:#3B67A6;";
            } else {
                $this->input['bal_style'] = "color:#BB0000;";
            }

            $this->input['last_payment_amount'] = number_format($billingm->getLastPaymentAmount(), 2, ".", ",");
            $this->input['last_payment_date'] = $billingm->getLastPaymentDate();

            $cp = new CompanyProfile($this->daffny->DB);
            $cp->getByOwnerId(getParentId());

            $license = new License($this->daffny->DB);
            $license->loadCurrentLicenseByMemberId(getParentId());

            $this->input["is_frozen"] = $cp->getAccountStatus();
            $this->input["license_name"] = $license->getLicenseName();

            $this->input["storage_name"] = $license->getStorageName();
            $this->input["addon_aq_name"] = $license->getAddonAQName();

            $this->daffny->tpl->buy_addon_aq = false;
            if ($this->input["addon_aq_name"] == License::DEFAULT_NONE) {
                $this->daffny->tpl->buy_addon_aq = true;
            }

            $cur_space = $license->getCurrentStorageSpace();
            $used_space = $license->getUsedStorageSpace();

            $this->input["used_space"] = get_file_size($used_space);
            $this->input["rest_space"] = get_file_size($cur_space - $used_space);

            $this->input["renewal_storage_name"] = $license->getNextStorageName(true);
            $this->input["renewal_addon_aq_name"] = $license->getNextAddonAqName(true);
            $this->input["renewal_name"] = $license->getNextLicenseName();
            $this->input["additional_users"] = $license->users;
            $this->input["renewal_users"] = $license->renewal_users;
            $this->input["new_license"] = $license->renewal_product_id;

            //Change license
            $p = new Product($this->daffny->DB);
            $this->daffny->tpl->products = $p->getRenewalProducts();
            $this->daffny->tpl->additional = $p->getRenewalAdditionalProducts();
            $this->daffny->tpl->storages = $p->getRenewalStoragesProducts();
            $this->daffny->tpl->addon_aq = $p->getRenewalAddonAqProducts();
            $this->daffny->tpl->additional_number = $license->renewal_users;

            $this->input["additional_number"] = $license->renewal_users;
            $this->form->TextField("additional_number", 3, array("style" => "width:30px;  text-align: right;", "class" => "digit-only"), "Additional Users", '</td><td>');

            $this->daffny->tpl->shownote = false;
            if ($license->users == 0 || ($license->renewal_users - $license->users) == 0) {
                $this->daffny->tpl->shownote = false;
                $this->input['next_inactive_users'] = "No one";
            } else {
                $this->daffny->tpl->shownote = true;
                if (($license->renewal_users - $license->users) < 0) {
                    $m = new Member($this->daffny->DB);
                    $this->input['next_inactive_users'] = $m->getNextInactiveUsers(getParentId(), $license->renewal_users);
                } else {
                    $this->input['next_inactive_users'] = "No one";
                }
            }

            $this->daffny->tpl->renewal_product_id = $license->renewal_product_id;
            $this->daffny->tpl->renewal_addon_aq_id = $license->renewal_addon_aq_id;

            //Change users
            $this->input["license_name_for_users"] = $license->getLicenseNameForChangeUsers()->name;
            $this->input["license_price_for_users"] = number_format($license->getLicenseNameForChangeUsers()->price, 2, ".", ",");

            //Buy addon AQ

            $this->input["addon_aq_name_for_period"] = $license->getAddonAQForBuy()->name;
            $this->input["addon_aq_price_for_period"] = number_format($license->getAddonAQForBuy()->price, 2, ".", ",");

            //Calculate Additional User product price
            $this->input["until_price_for_users"] = Product::calculateRestPrice($license->getLicenseNameForChangeUsers()->price, $license->expire, $license->period_type);

            //Calculate Addon AQ product price
            $this->input["until_price_for_addon_aq"] = Product::calculateRestPrice($license->getAddonAQForBuy()->price, $license->expire, $license->period_type);

            $this->input["license_expire"] = date('m/d/Y', strtotime($license->expire) + 86400);
            $this->daffny->tpl->expired = false;
            if ((strtotime($license->expire) + 86400) < time()) {
                $this->daffny->tpl->expired = true;
            }

            $this->input["license_payment"] = $license->getLicensePeriodicPayment();

            $this->input["license_payment_type"] = $license->getNextLicenseType();
            $this->input['next_billing_date'] = date('m/d/Y', strtotime($license->expire) + 86400);

            //Recent transactons
            $this->daffny->tpl->transactions = array();
            $billings = $billingm->getLast();
            $this->daffny->tpl->transactions = $billings;

            //Credit Cards
            if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                $results = $this->daffny->DB->selectRows("*, DES_DECRYPT(cc_number, '" . $this->daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
            } else {
                $results = $this->daffny->DB->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "'");
            }
            $this->daffny->tpl->cards = array();
            if (!empty($results)) {
                foreach ($results as $key => $value) {
                    $value['cc_number'] = (isset($_SESSION['admin_here']) && $_SESSION['admin_here'] === true) ? $value['cc_number'] : hideCCNumber($value['cc_number']);
                    $this->daffny->tpl->cards[] = $value;
                }
            }
        } catch (FDException $e) {
            $this->setFlashError("My Billings: Internal error. Please try later.");
            redirect(getLink(SITE_IN));
        }
    }

    public function history()
    {
        $this->tplname = "myaccount.billing.history";
        $this->title .= " - Billing History";
        $this->applyOrder(Billing::TABLE);
        $this->order->setDefault('id', 'desc');

        $billingm = new BillingManager($this->daffny->DB);
        $billingm->owner_id = getParentId();
        $billings = $billingm->get($this->order->getOrder(), $_SESSION['per_page'], " owner_id='" . getParentId() . "'");
        $this->setPager($billingm->getPager());
        $this->daffny->tpl->transactions = $billings;
    }

    public function deletecc()
    {
        $out = array('success' => false);
        $this->daffny->DB->transaction("start");
        try {
            $ID = $this->checkId();
            $this->daffny->DB->delete("app_creditcards", "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
            $this->daffny->DB->update("app_defaultsettings", array("billing_cc_id" => "NULL"), "billing_cc_id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
            if ($this->dbError()) {
                $this->daffny->DB->transaction("rollback");
                $out = array('success' => false);
            } else {
                $this->daffny->DB->transaction("commit");
                $out = array('success' => true);
            }
        } catch (Exception $e) {
            $this->daffny->DB->transaction("rollback");
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

    public function onetime()
    {
        $this->tplname = "myaccount.billing.onetime";
        $this->daffny->tpl->pgw = $this->daffny->cfg['pgw'];
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Profile", getLink("billing") => "Billing", '' => "One time payment"));
        if (!in_array($this->daffny->cfg['pgw'], array(1, 2))) {
            $this->setFlashError("There is no active Payment Gateways.");
            redirect("billing");
        }

        try {
            if (isset($_POST['submit'])) {
                $amount = (float) post_var("amount");
                if ($amount == 0) {
                    $this->err[] = 'Amount can not be $0.00.';
                }
                $this->isZero("cc_id", "Use Card");
                $ret = array();
                /* Process one time payment */
                if (!count($this->err)) {
                    $creditcard = new Creditcard($this->daffny->DB);
                    $creditcard->key = $this->daffny->cfg['security_salt'];

                    $cc = $creditcard->load((int) post_var("cc_id"), getParentId());
                    $cc_arr = array(
                        "cc_fname" => $creditcard->cc_fname
                        , "cc_lname" => $creditcard->cc_lname
                        , "cc_address" => $creditcard->cc_address
                        , "cc_city" => $creditcard->cc_city
                        , "cc_state" => $creditcard->cc_state
                        , "cc_zip" => $creditcard->cc_zip
                        , "cc_cvv2" => $creditcard->cc_cvv2
                        , "cc_number" => $creditcard->cc_number
                        , "cc_type" => $creditcard->cc_type
                        , "cc_month" => $creditcard->cc_month
                        , "cc_year" => $creditcard->cc_year
                        , "cc_type_name" => Creditcard::getCCTypeById($creditcard->cc_type),
                    );
                    $pay_arr = $cc_arr + array(
                        "amount" => $amount
                        , "paypal_api_username" => trim($this->daffny->cfg['paypal_api_username'])
                        , "paypal_api_password" => trim($this->daffny->cfg['paypal_api_password'])
                        , "paypal_api_signature" => trim($this->daffny->cfg['paypal_api_signature'])
                        , "anet_api_login_id" => trim($this->daffny->cfg['anet_api_login_id'])
                        , "anet_trans_key" => trim($this->daffny->cfg['anet_trans_key'])
                        , "notify_email" => ""
                        , "order_number" => "",
                    );

                    if ($this->daffny->cfg['pgw'] == 2) { //Authorize.net
                        $ret = $this->processAuthorize($pay_arr);
                    }
                    if ($this->daffny->cfg['pgw'] == 1) { //PayPal
                        $ret = $this->processPayPal($pay_arr);
                    }

                    if (isset($ret['success']) && $ret['success'] == true) {
                        //insert
                        $insert_arr['owner_id'] = getParentId();
                        $insert_arr['added'] = date("Y-m-d H:i:s");
                        $insert_arr['amount'] = $pay_arr['amount'];
                        $insert_arr['type'] = Billing::TYPE_PAYMENT;
                        $insert_arr['transaction_id'] = $ret['transaction_id'];
                        $insert_arr['description'] = ($this->daffny->cfg['pgw'] == 2 ? "Authorize.net " : "PayPal ") . $ret['transaction_id'];
                        $billing = new Billing($this->daffny->DB);
                        $billing->create($insert_arr);

                        $this->setFlashInfo("Your payment has been processed.");
                        redirect(getLink("billing"));
                    } else {
                        $this->setFlashError($ret['error']);
                    }
                }
            }

            $this->input = array(
                "amount" => @post_var("amount")
                , "cc_id" => @post_var("cc_id"),
            );
            $billingm = new BillingManager($this->daffny->DB);
            $billingm->key = $this->daffny->cfg['security_salt'];
            $billingm->owner_id = getParentId();
            $this->input['current_balance'] = number_format($billingm->getCurrentBalance(), 2, ".", ",");
            $this->form->ComboBox("cc_id", $billingm->getCards(), array(), "Use Card", "</td><td>");
            $this->form->MoneyField('amount', 16, array(), 'Amount', '</td><td>');
        } catch (FDException $e) {
            redirect(getLink('billing'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function buyadditional()
    {
        if (isset($_POST["buyusers"]) && (int) $_POST["buyusers"] > 0) {

            $cp = new CompanyProfile($this->daffny->DB);
            $cp->getByOwnerId(getParentId());
            //get current balanse
            $billingm = new BillingManager($this->daffny->DB);
            $billingm->key = $this->daffny->cfg['security_salt'];
            $billingm->owner_id = getParentId();
            $cur_bal = $billingm->getCurrentBalance();

            //get current license
            $license = new License($this->daffny->DB);
            $license->loadCurrentLicenseByMemberId(getParentId());

            $qty_users = (int) $_POST["buyusers"];

            $price = Product::calculateRestPrice($license->getLicenseNameForChangeUsers()->price, $license->expire, $license->period_type);
            $amount = $price * $qty_users;
            $current_balance = $cur_bal;
            if ($current_balance < $amount) {
                $creditcard = new Creditcard($this->daffny->DB);
                $creditcard->key = $this->daffny->cfg['security_salt'];
                $creditcard->load((int) post_var("billing_cc_id"), getParentId());

                $cc_arr = array(
                    "cc_fname" => $creditcard->cc_fname
                    , "cc_lname" => $creditcard->cc_lname
                    , "cc_address" => $creditcard->cc_address
                    , "cc_city" => $creditcard->cc_city
                    , "cc_state" => $creditcard->cc_state
                    , "cc_zip" => $creditcard->cc_zip
                    , "cc_cvv2" => $creditcard->cc_cvv2
                    , "cc_number" => $creditcard->cc_number
                    , "cc_type" => $creditcard->cc_type
                    , "cc_month" => $creditcard->cc_month
                    , "cc_year" => $creditcard->cc_year
                    , "cc_type_name" => Creditcard::getCCTypeById($creditcard->cc_type),
                );
                $pay_arr = $cc_arr + array(
                    "amount" => $amount
                    , "paypal_api_username" => trim($this->daffny->cfg['paypal_api_username'])
                    , "paypal_api_password" => trim($this->daffny->cfg['paypal_api_password'])
                    , "paypal_api_signature" => trim($this->daffny->cfg['paypal_api_signature'])
                    , "anet_api_login_id" => trim($this->daffny->cfg['anet_api_login_id'])
                    , "anet_trans_key" => trim($this->daffny->cfg['anet_trans_key'])
                    , "notify_email" => ""
                    , "order_number" => "",
                );

                if ($this->daffny->cfg['pgw'] == 2) { //Authorize.net
                    $ret = $this->processAuthorize($pay_arr);
                } elseif ($this->daffny->cfg['pgw'] == 1) { //PayPal
                    $ret = $this->processPayPal($pay_arr);
                }

                if (isset($ret['success']) && $ret['success'] == true) {
                    //insert
                    $insert_arr['owner_id'] = getParentId();
                    $insert_arr['added'] = date("Y-m-d H:i:s");
                    $insert_arr['amount'] = $pay_arr['amount'];
                    $insert_arr['type'] = Billing::TYPE_PAYMENT;
                    $insert_arr['transaction_id'] = $ret['transaction_id'];
                    $insert_arr['description'] = "Buy Additional user(s): " . $qty_users;
                    $billing = new Billing($this->daffny->DB);
                    $billing->create($insert_arr);
                    $this->setFlashInfo("Your payment has been processed.");
                } else {
                    $this->err[] = $ret['error'];
                }
            }
            if (!count($this->err)) {
                $this->daffny->DB->transaction("start");
                try {
                    //insert charge
                    if ($amount > 0) {
                        $insert_arr = array();
                        $insert_arr['owner_id'] = getParentId();
                        $insert_arr['added'] = date("Y-m-d H:i:s");
                        $insert_arr['amount'] = $amount;
                        $insert_arr['type'] = Billing::TYPE_CHARGE;
                        $insert_arr['transaction_id'] = "";
                        $insert_arr['description'] = "Buy Additional user(s): " . $qty_users;
                        $billing = new Billing($this->daffny->DB);
                        $billing->create($insert_arr);
                    }

                    //update current license
                    $upd_license = array(
                        "users" => $license->users + $qty_users
                        , "renewal_users" => $license->users + $qty_users,
                    );
                    $license->update($upd_license);

                    //insert order

                    $orderData = array(
                        'member_id' => getParentId(),
                        'status' => Orders::STATUS_PROCESSED,
                        'amount' => $amount,
                        'coupon_id' => null,
                        'company' => $cp->companyname,
                    );
                    $order = new Orders($this->daffny->DB);
                    $order->create($orderData);

                    $additional = $license->getLicenseNameForChangeUsers();
                    if ($qty_users > 0) {
                        $this->daffny->DB->insert('orders_details', array('order_id' => $order->id, 'product_id' => $additional->id, 'quantity' => $qty_users, 'price' => $additional->price, 'total' => $additional->price * $qty_users));
                    }

                    $this->daffny->DB->transaction("commit");

                    //send email

                    $this->sendEmail($_SESSION["member"]["contactname"], $_SESSION["member"]["email"], "Additional user(s) purchased", 'additionalusers', array("" => ""));

                    $this->setFlashInfo("Order has been processed. Please add new user(s) under 'Manage -> Users'.");
                    redirect(getLink("billing"));
                } catch (Exception $e) {
                    $this->daffny->DB->transaction("rollback");
                    $this->setFlashInfo("Undefined error.");
                    redirect(getLink("billing"));
                }
            } else {
                $this->setFlashError($this->err);
                redirect(getLink("billing"));
            }
        } else {
            $this->setFlashError("Please check additional users quantity.");
            redirect(getLink("billing"));
        }
    }

    public function buyaddonaq()
    {
        $cp = new CompanyProfile($this->daffny->DB);
        $cp->getByOwnerId(getParentId());
        //get current balanse
        $billingm = new BillingManager($this->daffny->DB);
        $billingm->key = $this->daffny->cfg['security_salt'];
        $billingm->owner_id = getParentId();
        $cur_bal = $billingm->getCurrentBalance();

        //get current license
        $license = new License($this->daffny->DB);
        $license->loadCurrentLicenseByMemberId(getParentId());

        $amount = Product::calculateRestPrice($license->getAddonAQForBuy()->price, $license->expire, $license->period_type);
        $current_balance = $cur_bal;
        if ($current_balance < $amount) {
            $creditcard = new Creditcard($this->daffny->DB);
            $creditcard->key = $this->daffny->cfg['security_salt'];
            $creditcard->load((int) post_var("addon_aq_billing_cc_id"), getParentId());

            $cc_arr = array(
                "cc_fname" => $creditcard->cc_fname,
                "cc_lname" => $creditcard->cc_lname,
                "cc_address" => $creditcard->cc_address,
                "cc_city" => $creditcard->cc_city,
                "cc_state" => $creditcard->cc_state,
                "cc_zip" => $creditcard->cc_zip,
                "cc_cvv2" => $creditcard->cc_cvv2,
                "cc_number" => $creditcard->cc_number,
                "cc_type" => $creditcard->cc_type,
                "cc_month" => $creditcard->cc_month,
                "cc_year" => $creditcard->cc_year,
                "cc_type_name" => Creditcard::getCCTypeById($creditcard->cc_type),
            );
            $pay_arr = $cc_arr + array(
                "amount" => $amount,
                "paypal_api_username" => trim($this->daffny->cfg['paypal_api_username']),
                "paypal_api_password" => trim($this->daffny->cfg['paypal_api_password']),
                "paypal_api_signature" => trim($this->daffny->cfg['paypal_api_signature']),
                "anet_api_login_id" => trim($this->daffny->cfg['anet_api_login_id']),
                "anet_trans_key" => trim($this->daffny->cfg['anet_trans_key']),
                "notify_email" => "",
                "order_number" => "",
            );

            if ($this->daffny->cfg['pgw'] == 2) { //Authorize.net
                $ret = $this->processAuthorize($pay_arr);
            } elseif ($this->daffny->cfg['pgw'] == 1) { //PayPal
                $ret = $this->processPayPal($pay_arr);
            }
            if (isset($ret['success']) && $ret['success'] == true) {
                //insert
                $insert_arr['owner_id'] = getParentId();
                $insert_arr['added'] = date("Y-m-d H:i:s");
                $insert_arr['amount'] = $pay_arr['amount'];
                $insert_arr['type'] = Billing::TYPE_PAYMENT;
                $insert_arr['transaction_id'] = $ret['transaction_id'];
                $insert_arr['description'] = "Buy Automate Quoting Addon";
                $billing = new Billing($this->daffny->DB);
                $billing->create($insert_arr);
                $this->setFlashInfo("Your payment has been processed.");
            } else {
                $this->err[] = $ret['error'];
            }
        }
        if (!count($this->err)) {
            $this->daffny->DB->transaction("start");
            try {
                //insert charge
                if ($amount > 0) {
                    $insert_arr = array();
                    $insert_arr['owner_id'] = getParentId();
                    $insert_arr['added'] = date("Y-m-d H:i:s");
                    $insert_arr['amount'] = $amount;
                    $insert_arr['type'] = Billing::TYPE_CHARGE;
                    $insert_arr['transaction_id'] = "";
                    $insert_arr['description'] = "Buy Automate Quoting Addon";
                    $billing = new Billing($this->daffny->DB);
                    $billing->create($insert_arr);
                }

                //update current license
                $upd_license = array(
                    "addon_aq_id" => $license->getAddonAQForBuy()->id,
                    "renewal_addon_aq_id" => $license->getAddonAQForBuy()->id,
                );
                $license->update($upd_license);

                //insert order

                $orderData = array(
                    'member_id' => getParentId(),
                    'status' => Orders::STATUS_PROCESSED,
                    'amount' => $amount,
                    'coupon_id' => null,
                    'company' => $cp->companyname,
                );
                $order = new Orders($this->daffny->DB);
                $order->create($orderData);

                $addon = $license->getAddonAQForBuy();
                $this->daffny->DB->insert('orders_details', array('order_id' => $order->id, 'product_id' => $addon->id, 'quantity' => 1, 'price' => $addon->price, 'total' => $addon->price * 1));

                $this->daffny->DB->transaction("commit");

                //send email
                $this->sendEmail($_SESSION["member"]["contactname"], $_SESSION["member"]["email"], "Automate Quoting Addon purchased", 'buyaddon_aq', array("" => ""));

                $this->setFlashInfo("Order has been processed.");
                redirect(getLink("billing"));
            } catch (Exception $e) {
                $this->daffny->DB->transaction("rollback");
                $this->setFlashInfo("Undefined error.");
                redirect(getLink("billing"));
            }
        } else {
            $this->setFlashError($this->err);
            redirect(getLink("billing"));
        }

    }

    public function changelicense()
    {
        if (isset($_POST["changelicense"])) {

            $cp = new CompanyProfile($this->daffny->DB);
            $cp->getByOwnerId(getParentId());
            $license = new License($this->daffny->DB);
            $license->loadCurrentLicenseByMemberId(getParentId());

            $renewal_users = (int) $_POST["additional_number"];
            $renewal_product_id = (int) $_POST["products"];
            $renewal_storage_id = (int) $_POST["storages"];
            $renewal_addon_aq_id = (int) $_POST["addon_aq"];

            if (!count($this->err)) {
                $this->daffny->DB->transaction("start");
                try {
                    //update current license
                    $upd_license = array(
                        "renewal_product_id" => $renewal_product_id,
                        "renewal_storage_id" => $renewal_storage_id > 0 ? $renewal_storage_id : 'NULL',
                        "renewal_addon_aq_id" => $renewal_addon_aq_id > 0 ? $renewal_addon_aq_id : 'NULL',
                        "renewal_users" => $renewal_users,
                    );
                    $license->update($upd_license);
                    $this->daffny->DB->transaction("commit");
                    //send email
                    $this->sendEmail($_SESSION["member"]["contactname"], $_SESSION["member"]["email"], "Renewal license changed", 'changelicense', array("" => ""));

                    $this->setFlashInfo("Renewal license type has been changed.");
                    redirect(getLink("billing"));
                } catch (Exception $e) {
                    $this->daffny->DB->transaction("rollback");
                    $this->setFlashInfo("Undefined error.");
                    redirect(getLink("billing"));
                }
            } else {
                $this->setFlashError($this->err);
                redirect(getLink("billing"));
            }
        } else {
            $this->setFlashError("Please check form data.");
            redirect(getLink("billing"));
        }
    }

    public function renew()
    {

        $this->tplname = "myaccount.billing.renew";
        $this->daffny->tpl->pgw = $this->daffny->cfg['pgw'];
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Profile", getLink("billing") => "Billing", '' => "Renew"));
        if (!in_array($this->daffny->cfg['pgw'], array(1, 2))) {
            $this->setFlashError("There is no active Payment Gateways.");
            redirect("billing");
        }

        //check if current license is active
        $currentlicense = new License($this->daffny->DB);
        if ($currentlicense->loadCurrentLicenseByMemberId(getParentId())) { //load current license
            if (strtotime($currentlicense->expire) > time()) { // License is still active?
                $this->setFlashError("License is active.");
                redirect("billing");
            }
        } else {
            $this->setFlashError("There are no licenses.");
            redirect("billing");
        }

        $amount = 0;
        $this->daffny->tpl->products = array();

        $product = new Product($this->daffny->DB);
        $product->load($currentlicense->renewal_product_id);

        $this->daffny->tpl->products[] = array(
            'item' => $product->code
            , 'product' => $product->name
            , 'quantity' => 1
            , 'price' => $product->price
            , 'total' => $product->price * 1,
        );
        $amount = $product->price * 1;

        if ($currentlicense->renewal_users > 0) {
            $additionalId = $this->daffny->DB->selectField('id', Product::TABLE, "WHERE `is_online` = 1 AND `is_delete` = 0 AND `type_id` = " . Product::TYPE_ADDITIONAL . " AND `period_id` = '" . (int) $product->period_id . "'");
            if ($additionalId) {
                $additional = new Product($this->daffny->DB);
                $additional->load($additionalId);
                $this->daffny->tpl->products[] = array(
                    'item' => $additional->code
                    , 'product' => $additional->name
                    , 'quantity' => $currentlicense->renewal_users
                    , 'price' => $additional->price
                    , 'total' => $additional->price * $currentlicense->renewal_users,
                );
                $amount += $additional->price * $currentlicense->renewal_users;
            } else {
                $this->setFlashError("Additional Product not found.");
                redirect("billing");
            }
        }

        if ($currentlicense->renewal_storage_id > 0) {
            $storage = new Product($this->daffny->DB);
            $storage->load($currentlicense->renewal_storage_id);

            $this->daffny->tpl->products[] = array(
                'item' => $storage->code
                , 'product' => $storage->name
                , 'quantity' => 1
                , 'price' => $storage->price
                , 'total' => $storage->price,
            );

            $amount += (float) $storage->price;
        }

        if ($currentlicense->renewal_addon_aq_id > 0) {
            $addon_aq = new Product($this->daffny->DB);
            $addon_aq->load($currentlicense->renewal_addon_aq_id);

            $this->daffny->tpl->products[] = array(
                'item' => $addon_aq->code
                , 'product' => $addon_aq->name
                , 'quantity' => 1
                , 'price' => $addon_aq->price
                , 'total' => $addon_aq->price,
            );

            $amount += (float) $addon_aq->price;
        }

        try {
            if (isset($_POST['submit'])) {

                $member = new Member($this->daffny->DB);
                $member->load(getParentId());
                $profile = $member->getCompanyProfile();

                if ($amount == 0) {
                    $this->err[] = 'Amount can not be $0.00.';
                }
                $this->isZero("cc_id", "Use Card");
                $ret = array();
                /* Process one time payment */
                if (!count($this->err)) {
                    $creditcard = new Creditcard($this->daffny->DB);
                    $creditcard->key = $this->daffny->cfg['security_salt'];
                    $creditcard->load((int) post_var("cc_id"), getParentId());

                    $renewalArr = array(
                        //cc info
                        "cc_fname" => $creditcard->cc_fname
                        , "cc_lname" => $creditcard->cc_lname
                        , "cc_address" => $creditcard->cc_address
                        , "cc_city" => $creditcard->cc_city
                        , "cc_state" => $creditcard->cc_state
                        , "cc_zip" => $creditcard->cc_zip
                        , "cc_cvv2" => $creditcard->cc_cvv2
                        , "cc_number" => $creditcard->cc_number
                        , "cc_type" => $creditcard->cc_type
                        , "cc_month" => $creditcard->cc_month
                        , "cc_year" => $creditcard->cc_year
                        , "cc_type_name" => Creditcard::getCCTypeById($creditcard->cc_type)
                        , "amount" => $amount
                        //for order
                        , "member_id" => getParentId()
                        , "company" => $profile->companyname
                        , "status" => Orders::STATUS_PROCESSED
                        , "first_name" => $creditcard->cc_fname
                        , "last_name" => $creditcard->cc_lname
                        , "address" => $creditcard->cc_address
                        , "city" => $creditcard->cc_city
                        , "state" => $creditcard->cc_state
                        , "zip" => $creditcard->cc_zip
                        , "card_type_id" => $creditcard->cc_type
                        , "card_first_name" => trim($creditcard->cc_fname)
                        , "card_last_name" => trim($creditcard->cc_lname)
                        , "card_number" => $creditcard->cc_number
                        , "card_expire" => $creditcard->cc_month . substr($creditcard->cc_year, -2)
                        , "card_cvv2" => $creditcard->cc_cvv2
                        //gateway
                        , "paypal_api_username" => trim($this->daffny->cfg['paypal_api_username'])
                        , "paypal_api_password" => trim($this->daffny->cfg['paypal_api_password'])
                        , "paypal_api_signature" => trim($this->daffny->cfg['paypal_api_signature'])
                        , "anet_api_login_id" => trim($this->daffny->cfg['anet_api_login_id'])
                        , "anet_trans_key" => trim($this->daffny->cfg['anet_trans_key'])
                        , "notify_email" => ""
                        , "order_number" => "",
                    );

                    $this->daffny->DB->transaction('start');

                    //insert billing
                    $billingArr = array(
                        'type' => Billing::TYPE_CHARGE,
                        'owner_id' => getParentId(),
                        'added' => date('Y-m-d H:i:s'),
                        'amount' => $renewalArr['amount'],
                        'description' => 'License Renewal',
                        'transaction_id' => '',
                    );
                    $billing = new Billing($this->daffny->DB);
                    $billing->create($billingArr);

                    $billingArr = array();
                    $billingArr['owner_id'] = getParentId();
                    $billingArr['added'] = date("Y-m-d H:i:s");
                    $billingArr['amount'] = $renewalArr['amount'];
                    $billingArr['type'] = Billing::TYPE_PAYMENT;
                    $billingArr['transaction_id'] = $ret['transaction_id'];
                    $billingArr['description'] = "Renew license " . ($this->daffny->cfg['pgw'] == 2 ? "Authorize.net " : "PayPal ") . $ret['transaction_id'];
                    $billing = new Billing($this->daffny->DB);
                    $billing->create($billingArr);

                    $renewalorder = new Orders($this->daffny->DB);
                    $renewalorder->create($renewalArr);
                    $renewalArr["order_number"] = $renewalorder->id;

                    $this->daffny->DB->insert('orders_details',
                        array('order_id' => $renewalorder->id
                            , 'product_id' => $product->id
                            , 'quantity' => 1
                            , 'price' => $product->price
                            , 'total' => $product->price)
                    );

                    if ($currentlicense->renewal_users > 0) {

                        $this->daffny->DB->insert('orders_details',
                            array(
                                'order_id' => $renewalorder->id
                                , 'product_id' => $additional->id
                                , 'quantity' => $currentlicense->renewal_users
                                , 'price' => $additional->price
                                , 'total' => $additional->price * $currentlicense->renewal_users));
                    }

                    if ($currentlicense->renewal_storage_id > 0 && isset($storage)) {

                        $this->daffny->DB->insert('orders_details',
                            array(
                                'order_id' => $renewalorder->id
                                , 'product_id' => $storage->id
                                , 'quantity' => 1
                                , 'price' => $storage->price
                                , 'total' => $storage->price));
                    }

                    if ($currentlicense->renewal_addon_aq_id > 0 && isset($addon_aq)) {

                        $this->daffny->DB->insert('orders_details',
                            array(
                                'order_id' => $renewalorder->id
                                , 'product_id' => $addon_aq->id
                                , 'quantity' => 1
                                , 'price' => $addon_aq->price
                                , 'total' => $addon_aq->price));
                    }

                    $newlicense = new License($this->daffny->DB);
                    $expire = new DateTime();
                    $expire->add(new DateInterval('P1' . ($product->period_id == Product::PERIOD_MONTH ? 'M' : 'Y')));
                    $newlicense->create(array(
                        'owner_id' => $member->id,
                        'order_id' => $renewalorder->id,
                        'users' => $currentlicense->renewal_users,
                        'expire' => $expire->format('Y-m-d'),
                        'period_type' => $product->period_id,
                        'product_id' => $product->id,
                        'renewal_product_id' => $currentlicense->renewal_product_id,
                        'renewal_users' => $currentlicense->renewal_users,
                        'storage_id' => isset($storage) ? $storage->id : 'NULL',
                        'addon_aq_id' => isset($addon_aq) ? $addon_aq->id : 'NULL',
                        'renewal_storage_id' => $currentlicense->renewal_storage_id,
                        'renewal_addon_aq_id' => $currentlicense->renewal_addon_aq_id,
                    ));

                    //change user count
                    $member->getNextInactiveUsers($member->id, $currentlicense->renewal_users, true);

                    if ($this->daffny->cfg['pgw'] == 2) { //Authorize.net
                        $ret = $this->processAuthorize($renewalArr);
                    }
                    if ($this->daffny->cfg['pgw'] == 1) { //PayPal
                        $ret = $this->processPayPal($renewalArr);
                    }

                    if (isset($ret['success']) && $ret['success'] == true) {
                        $this->daffny->DB->transaction("commit");
                        $tplData = array(
                            'first_name' => $renewalorder->first_name,
                            'last_name' => $renewalorder->last_name,
                            'expire' => date('m/d/Y', strtotime($newlicense->expire)),
                            'renewal_receipt' => $this->daffny->tpl->build('myaccount.billing.invoice', array("" => "")),
                            'system_phone' => $this->daffny->cfg["phone"],
                            'info_email' => $this->daffny->cfg["info_email"],
                        );
                        $profile->update(array(
                            'is_frozen' => 0,
                        ));
                        $_SESSION['is_frozen'] = false;

                        $this->sendEmail($member->contactname, $member->email, "Your Freight Dragon license has been renewed", "renewal_success", $tplData);
                        $this->setFlashInfo("Your payment has been processed.");
                        redirect(getLink("billing"));
                    } else {
                        $this->daffny->DB->transaction('rollback');
                        $this->setFlashError($ret['error']);
                        redirect(getLink("billing", "renew"));
                    }
                } else {
                    $this->setFlashError($this->err);
                }
            }

            $this->input["cc_id"] = @post_var("cc_id");
            $billingm = new BillingManager($this->daffny->DB);
            $billingm->key = $this->daffny->cfg['security_salt'];
            $billingm->owner_id = getParentId();
            $this->form->ComboBox("cc_id", $billingm->getCards(), array(), "Use Card", "</td><td>");
        } catch (FDException $e) {
            $this->setFlashError($e->getMessage());
            $this->daffny->DB->transaction('rollback');
            redirect(getLink('billing'));
        }
    }

    /**
     * SMS
     *
     */
    public function sms1()
    {
        try {

            $this->tplname = "myaccount.sms.sms_main";
            $this->title .= "Sms Account";

            $creditValue = 0;
            $query = "select sum(credit) as credit from app_sms_account_payments where member_id='" . getParentId() . "'";
            $result = $this->daffny->DB->query($query);
            if ($result) {
                while ($row = $this->daffny->DB->fetch_row($result)) {
                    $creditValue = $row['credit'];
                }
            }

            $query = "select count(*) as num_sms_used from app_sms_logs where owner_id='" . getParentId() . "'";
            $result = $this->daffny->DB->query($query);
            if ($result) {
                while ($row = $this->daffny->DB->fetch_row($result)) {
                    $smsCreditUsedValue = $row['num_sms_used'];
                }
            }
            $this->daffny->tpl->creditValueUsed = $smsCreditUsedValue * 0.01;
            $this->daffny->tpl->creditValue = $creditValue - ($smsCreditUsedValue * 0.01);

            $this->daffny->tpl->paymentDetail = array();
            $query = "select credit,date_format(transaction_date,'%m/%d/%Y') as transaction_date from app_sms_account_payments where member_id='" . getParentId() . "'";
            $result = $this->daffny->DB->query($query);
            if ($result) {
                while ($row = $this->daffny->DB->fetch_row($result)) {
                    $this->daffny->tpl->paymentDetail['credit'] = $row['credit'];
                    $this->daffny->tpl->paymentDetail['transaction_date'] = $row['transaction_date'];
                }
            }

            $results = $this->daffny->DB->selectRows("*", "app_sms_account_users", "WHERE owner_id='" . (int) $_SESSION['member_id'] . "' and status=1");
            $this->daffny->tpl->smsUser = array();
            if (!empty($results)) {
                foreach ($results as $key => $value) {

                    $this->daffny->tpl->smsUser[] = $value;
                }
            }

            $rows_sms = $this->daffny->DB->selectRows("A.`id`,A.`parent_id`,A.`contactname`", " `members` A ", "WHERE A.`parent_id` = " . getParentId() . " and A.status = 'Active' and A.id not in ( SELECT `user_id` from `app_sms_account_users` B where B.`owner_id` = " . getParentId() . " and B.status = 1)");
            $this->daffny->tpl->company_members_sms = $rows_sms;

        } catch (FDException $e) {
            redirect(getLink('billing/sms1'));
        }
    }

    /**
     * SMS
     *
     */
    public function sms()
    {
        try {

            $this->tplname = "myaccount.sms.sms";
            $this->title .= "Add New Phone Numbers";
            // print_r($_POST);
            if (isset($_POST['submit1']) && $_POST['submit1'] == "shownumbers") {

                $strUrl = "?action=getPhoneNumbers&state=" . $_POST['state'] . "&country_iso=US&pattern=" . $_POST['area_code'];
                $getdata = file_get_contents('https://cargoflare.com/sms/sms_response.php' . $strUrl);
                $response = json_decode($getdata);


                $_SESSION['SMS']['api_id'] = $response->api_id;
                $_SESSION['SMS']['phoneNumbers'] = $response->objects;

                $this->daffny->tpl->phoneNumbers = $_SESSION['SMS']['phoneNumbers'];

                $numberObj = $response->objects;

            } elseif (isset($_POST['submit1']) && $_POST['submit1'] == "selectnumbers") {

                $phoneNumbers = $_POST['phonenumbers'];
                $users = $_POST['users'];
                $credit = $_POST['credit'];

                $usersSelected = array();
                if ($credit <= 0 || $credit == '') {
                    $credit = 0;
                }

                if (is_array($phoneNumbers) && sizeof($phoneNumbers) <= 0) {
                    $this->err[] = "Please select Phone Numbers ";
                } else {
                    foreach ($phoneNumbers as $keyNumbers => $valueNumbers) {
                        if (array_key_exists($keyNumbers, $users)) {
                            if ($users[$keyNumbers] == '') {
                                $this->err[] = "Please select User for Phone Number - " . $keyNumbers;
                            } else {
                                $usersSelected[$keyNumbers] = $users[$keyNumbers];
                            }

                        }
                    }
                }

                $this->daffny->tpl->phoneNumbers = $_SESSION['SMS']['phoneNumbers'];
                $this->daffny->tpl->phoneNumbersSelected = $phoneNumbers;
                $this->daffny->tpl->usersSelected = $users;
                $this->daffny->tpl->credit = $credit;

                if (!count($this->err)) {
                    $_SESSION['SMS']['users'] = $_POST['users'];
                    $_SESSION['SMS']['phoneNumbersSelected'] = $phoneNumbers;
                    $_SESSION['SMS']['usersSelected'] = $usersSelected;
                    $_SESSION['SMS']['credit'] = $credit;
                    $this->tplname = "myaccount.sms.sms_order_confirmation";
                    $this->title .= "New Phone Numbers Order Confirmation";

                    //Credit Cards
                    if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                        $results = $this->daffny->DB->selectRows("*, DES_DECRYPT(cc_number, '" . $this->daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' order by id desc limit 0,1");
                    } else {
                        $results = $this->daffny->DB->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' order by id desc limit 0,1");
                    }
                    $this->daffny->tpl->cards = array();
                    if (!empty($results)) {
                        foreach ($results as $key => $value) {
                            $value['cc_number'] = (isset($_SESSION['admin_here']) && $_SESSION['admin_here'] === true) ? $value['cc_number'] : hideCCNumber($value['cc_number']);
                            $this->daffny->tpl->cards[] = $value;
                        }
                    }
                }
            } elseif (isset($_POST['submit1']) && $_POST['submit1'] == "processsmsorders") {
                $phoneNumbersSelected = $_SESSION['SMS']['phoneNumbersSelected'];
                $usersSelected = $_SESSION['SMS']['usersSelected'];
                $credit = $_SESSION['SMS']['credit'];

                if (!isset($_SESSION['SMS']) || !isset($_SESSION['SMS']['phoneNumbersSelected'])) {
                    throw new FDException("Invalid Form Data", getLink('billing/sms'));
                }

                if (!isset($phoneNumbersSelected) || sizeof($phoneNumbersSelected) <= 0) {
                    throw new FDException("Invalid Form Data-1", getLink('billing/sms'));
                }

                if (!isset($usersSelected) || sizeof($usersSelected) <= 0) {
                    throw new FDException("Invalid Form Data-2", getLink('billing/sms'));
                }

                $this->tplname = "myaccount.sms.sms_order_confirmation";
                $this->title .= "New Phone Numbers Order Confirmation";

                $amount = 0;
                $numberPrice = 4.99;
                $numberOfPhones = sizeof($phoneNumbersSelected);
                $amount = ($numberOfPhones * $numberPrice) + $credit;

                //Credit Cards
                if ($GLOBALS['CONF']['DES_ENCRYPT']) {
                    $results = $this->daffny->DB->selectRows("*, DES_DECRYPT(cc_number, '" . $this->daffny->cfg['security_salt'] . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' order by id desc limit 0,1");
                } else {
                    $results = $this->daffny->DB->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . getParentId() . "' order by id desc limit 0,1");
                }

                $creditCard = '';
                $this->daffny->tpl->cards = array();

                if (!empty($results)) {
                    foreach ($results as $key => $value) {
                        $creditCard = $value;
                        $value['cc_number'] = (isset($_SESSION['admin_here']) && $_SESSION['admin_here'] === true) ? $value['cc_number'] : hideCCNumber($value['cc_number']);
                        $this->daffny->tpl->cards[] = $value;
                    }
                }

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

                if ($amount == 0) {
                    $this->err[] = 'Amount can not be $0.00.';
                }

                if (sizeof($creditCard) <= 0) {
                    $this->err[] = 'Credit card values not found.';
                }

                $ret = array();
                /* Process payments */
                if (!count($this->err)) {
                    if ($defaultSettings->current_gateway == 2) { //Authorize.net
                    }
                    if ($defaultSettings->current_gateway == 1) { //PayPal
                    }

                    if (1) {
                        $firstTimePurchase = true;
                        $row = $this->daffny->DB->selectRow("id", "app_sms_account_subscribed", "WHERE member_id = '" . $_SESSION['member_id'] . "'");
                        if (!empty($row)) {
                            $firstTimePurchase = false;
                        }

                        $ins_arr = array(
                            'member_id' => $_SESSION['member_id'],
                            'total_amount' => $amount,
                            'credit' => $credit,
                            'phone_amount' => $numberPrice,
                            'transaction_id' => $ret['transaction_id'],
                        );
                        $smspayments = new SmsPayments($this->daffny->DB);
                        $smspayments_ins_id = $smspayments->create($ins_arr);

                        foreach ($phoneNumbersSelected as $key => $value) {

                            $ins_arr = array(
                                'payment_id' => $smspayments_ins_id,
                                'product_id' => 306,
                                'item_desc' => "SMS Purchased Phone",
                                'quantity' => 1,
                                'price' => $numberPrice,
                                'type' => 1,
                            );
                            $smsPaymentsItem = new SmsPaymentsItem($this->daffny->DB);
                            $smsPaymentsItem->create($ins_arr);

                        }

                        if ($credit > 0) {

                            $ins_arr = array(
                                'payment_id' => $smspayments_ins_id,
                                'product_id' => 306,
                                'item_desc' => "SMS Credit Purchased",
                                'quantity' => 1,
                                'price' => $credit,
                                'type' => 2,
                            );
                            $smsPaymentsItem = new SmsPaymentsItem($this->daffny->DB);
                            $smsPaymentsItem->create($ins_arr);
                        }
                        //make api call to purchase numbers
                        if ($firstTimePurchase == true) {
                            $ins_arr = array(
                                'member_id' => $_SESSION['member_id'],
                                'status' => 1,
                            );

                            $smsSubscribed = new SmsSubscribed($this->daffny->DB);
                            $smsSubscribed->create($ins_arr);
                        }

                        # Plivo AUTH ID
                        $AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
                        # Plivo AUTH TOKEN
                        $AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';

                        foreach ($phoneNumbersSelected as $key => $value) {

                            $url = "https://api.plivo.com/v1/Account/" . $AUTH_ID . "/PhoneNumber/" . $value . "/";

                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                            curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                            $response = curl_exec($ch);

                            $purchase_response = json_decode($response);
                            if (isset($purchase_response->error)) {
                                //die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
                            } else {
                                $url = "https://api.plivo.com/v1/Account/" . $AUTH_ID . "/Number/" . $value . "/";
                                $data = array("app_id" => "23430985543428118");
                                $data_string = json_encode($data);
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                                //curl_setopt($ch, CURLOPT_HEADER, true);
                                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                                curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                                $response = curl_exec($ch);
                                curl_close($ch);
                            }

                            $ins_arr = array(
                                'user_id' => $usersSelected[$key],
                                'owner_id' => getParentId(),
                                'phone' => $value,
                                'api_id' => $_SESSION['SMS']['api_id'],
                                'status' => 1,
                                'buy_response' => $response,
                            );
                            $smsUsers = new SmsUsers($this->daffny->DB);
                            $smsUsers->create($ins_arr);

                        }

                        $this->setFlashInfo("Your payment has been processed.");
                        redirect(getLink("billing", "sms_order_confirmation"));
                    } else {
                        $this->err[] = $ret['error'];
                    }
                }

            } else {
                unset($_SESSION['SMS']);
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("billing") => "Billing", '' => "SMS"));

            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }

            //build assigns

            $this->input['area_code'] = $_POST['area_code'];
            $this->input['state'] = $_POST['state'];

            $this->form->ComboBox('state', array('' => "Select One", 'United States' => $this->getStates()), array('style' => 'width:130px;', 'tabindex' => 15, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), "State/Zip", "</td><td>", true);

            $this->daffny->tpl->assigns = array();
            $assignsTemp = array();
            $sql = "SELECT A.`id`,A.`parent_id`,A.`contactname` FROM `members` A WHERE A.parent_id = '" . getParentId() . "' AND A.`is_deleted` <> 1  AND A.`status` = 'Active' and A.id not in ( SELECT `user_id` from `app_sms_account_users` B where B.`owner_id` = " . getParentId() . " and B.status = 1)";
            $q = $this->daffny->DB->query($sql);

            while ($assigns = $this->daffny->DB->fetch_row($q)) {

                $this->daffny->tpl->assigns[] = $assigns;
                $id = $assigns['id'];
                $assignsTemp[$id] = $assigns;
            }

            $_SESSION['SMS']['assigns'] = $assignsTemp;

            $this->form->TextField("area_code", 4, array("style" => "width:100px;"), $this->requiredTxt . "Area Code", "</td><td>");

        } catch (FDException $e) {
            redirect(getLink('billing/sms'));
        }
    }

    public function sms_order_confirmation()
    {
        try {

            $this->tplname = "myaccount.sms.sms_order_thankyou";
            $this->title .= "New Phone Numbers Order Confirmation";

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("billing") => "Billing", 'billing/sms' => "SMS", '' => "SMS Order Confirmation"));
            $this->setFlashInfo("Thank You , Your Order Processed");

        } catch (FDException $e) {
            redirect(getLink('billing/sms'));
        }
    }

    public function deleteSmsUser()
    {
        $out = array('success' => false);
        $this->daffny->DB->transaction("start");
        try {
            # Plivo AUTH ID
            $AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
            # Plivo AUTH TOKEN
            $AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
            $ID = $this->checkId();

            $url = "https://api.plivo.com/v1/Account/" . $AUTH_ID . "/Number/" . $_GET['ph'] . "/"; // 19547156943

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

            $response = curl_exec($ch);
            $purchase_response = json_decode($response);
            curl_close($ch);

            if (isset($purchase_response->error)) {
                $out = array('success' => false);
            } else {
                $this->daffny->DB->update("app_sms_account_users", array("status" => "0"), "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
                if ($this->dbError()) {
                    $this->daffny->DB->transaction("rollback");
                    $out = array('success' => false);
                } else {
                    $this->daffny->DB->transaction("commit");
                    $out = array('success' => true);
                }
            }

        } catch (Exception $e) {
            $this->daffny->DB->transaction("rollback");
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

}
