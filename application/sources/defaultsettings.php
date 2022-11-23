<?php

class ApplicationDefaultsettings extends ApplicationAction {


    public $title = "Settings";
    public $section = "Default Settings";
    public $tplname = "settings.defaultsettings";

    public function construct() {
       

        if (!$this->check_access('preferences')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }

        return parent::construct();
    }

    public function idx() {
        try{
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", '' => "Default Settings"));
        $this->check_access("settings");

        if (isset($_POST['submit'])) {

            $checkAccountContract = $this->daffny->DB->selectRow("commercial_terms_updated_at, commercial_terms_updated_by, commercial_terms", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");

            $this->isEmpty("order_deposit_type", "Order deposit type");
            $this->checkEmail("notify_email", "Email Notification");

            //check emails
            if (trim(post_var('email_blind')) != "") {
                $email_blind = "";
                $emails = @explode(",", post_var('email_blind'));
                if (count($emails) > 0) {

                    $er = false;

                    foreach ($emails AS $key => $email) {

                        if (!validate_email(trim($email))) {
                            $er = true;
                        } else {
                            $email_blind[] = trim($email);
                        }
                    }

                    if ($er) {
                        $this->err[] = "Field <strong>'Send a blind carbon copy of outgoing e-mail to:'</strong> has invalid emails.";
                    } else {
                        $_POST['email_blind'] = implode(", ", $email_blind);
                    }
                } else {
                    $this->err[] = "Field <strong>'Send a blind carbon copy of outgoing e-mail to:'</strong> has bad format. Separate multiple addresses with commas.";
                }
            }

            $mark_posted = 0;
            $mark_dispatched = 0;
            $mark_pickedup = 0;
            $mark_deivered = 0;
            $send_customer = 0;
            $send_admin = 0;

            if (isset($_POST['mark_posted'])) {
                $mark_posted = 1;
            }

            if (isset($_POST['mark_dispatched'])) {
                $mark_dispatched = 1;
            }

            if (isset($_POST['mark_pickedup'])) {
                $mark_pickedup = 1;
            }

            if (isset($_POST['mark_deivered'])) {
                $mark_deivered = 1;
            }

            if (isset($_POST['send_customer'])) {
                $send_customer = 1;
            }

            if (isset($_POST['send_admin'])) {
                $send_admin = 1;
            }

            $sql_arr = array(
                "lead_start_number" => (int) post_var('lead_start_number')
                , "on_off_auto_quoting" => (post_var('on_off_auto_quoting') == "1" ? 1 : 0)
                , "on_off_auto_quoting_email" => (post_var('on_off_auto_quoting_email') == "1" ? 1 : 0)
                , "auto_quote_api_pin" => post_var('auto_quote_api_pin')
                , "aq_email_template" => post_var('aq_email_template')
                , "auto_quote_api_key" => post_var('auto_quote_api_key')
                , "order_deposit" => post_var('order_deposit')
                , "order_deposit_type" => post_var('order_deposit_type')
                , "first_quote_followup" => (int) post_var('first_quote_followup')
                , "mark_as_expired" => (int) post_var('mark_as_expired')
                , "mark_assumed_delivered" => (int) post_var('mark_assumed_delivered')
                , "assign_unverified_orders_id" => post_var('assign_unverified_orders_id')
                , "logout_h" => (int) post_var('logout_h')
                , "logout_m" => (int) post_var('logout_m')
                , "payments_terms" => post_var('payments_terms')
                , "payments_terms_cod" => post_var('payments_terms_cod')
                , "payments_terms_cop" => post_var('payments_terms_cop')
                , "payments_terms_billing" => post_var('payments_terms_billing')
                , "payments_terms_invoice" => post_var('payments_terms_invoice')
                , "zoom_level" => post_var('zoom_level')
                , "carrier_pmt_terms_id" => (int) post_var('carrier_pmt_terms_id')
                , "carrier_pmt_terms_begin_id" => (int) post_var('carrier_pmt_terms_begin_id')
                , "carrier_pmt_method_id" => (int) post_var('carrier_pmt_method_id')
                , "email_blind" => post_var('email_blind')
                , "order_terms" => post_var('order_terms')
                , "commercial_terms" => post_var('commercial_terms')
                , "dispatch_terms" => post_var('dispatch_terms')
                , "show_new_order" => (post_var("show_new_order") == "1" ? 1 : 0)
                , "allow_replace_cod" => (post_var("allow_replace_cod") == "1" ? 1 : 0)
                , "show_vehicle_pricing" => (post_var("show_vehicle_pricing") == "1" ? 1 : 0)
                , "assign_leads_id" => post_var('assign_leads_id')
                , "assign_type" => post_var('assign_type')
                , "paypal_api_username" => post_var('paypal_api_username')
                , "paypal_api_password" => post_var('paypal_api_password')
                , "paypal_api_signature" => post_var('paypal_api_signature')
                , "anet_api_login_id" => post_var('anet_api_login_id')
                , "anet_trans_key" => post_var('anet_trans_key')
                , "gateway_api_username" => post_var('gateway_api_username')
                , "gateway_api_password" => post_var('gateway_api_password')
                , "gateway_api_signature" => post_var('gateway_api_signature')
                , "easy_pay_key" => post_var('easy_pay_key')
                , "current_gateway" => post_var('current_gateway')
                , "notify_email" => post_var('notify_email')
                , "central_dispatch_uid" => post_var('central_dispatch_uid')
                , "central_dispatch_post" => (post_var("central_dispatch_post") == "1" ? 1 : 0)
                , "hide_orders" => (post_var("hide_orders") == "1" ? 1 : 0)
                , "smtp_server_name" => post_var('smtp_server_name')
                , "smtp_server_port" => post_var('smtp_server_port')
                , "smtp_use_ssl" => post_var('smtp_use_ssl')
                , "smtp_user_name" => post_var('smtp_user_name')
                , "smtp_user_password" => post_var('smtp_user_password')
                , "smtp_from_email" => post_var('smtp_from_email')
                , "quickbooks_id" => post_var('quickbooks_id')
                , "mark_posted" => $mark_posted
                , "mark_dispatched" => $mark_dispatched
                , "mark_pickedup" => $mark_pickedup
                , "mark_deivered" => $mark_deivered
                , "send_customer" => $send_customer
                , "send_admin" => $send_admin
                , "card_batch_payment" => ($_POST['card_batch_payment'] == '' ? 0 : 1)
                , "card_batch" => $_POST['card_batch']
                , "card_payment_esigned" => ($_POST['card_payment_esigned'] == '' ? 0 : 1)
                , "thresholdRating" => post_var('thresholdRating')
                , "reviewNotificationEmail" => post_var('reviewNotificationEmail')
                , "tai_user" => post_var('tai_user')
                , "tai_password" => post_var('tai_password')
            );

            if ($checkAccountContract['commercial_terms'] != post_var('commercial_terms')) { 
                $sql_arr['commercial_terms_updated_at'] = date('Y-m-d h:m:s');
                $sql_arr['commercial_terms_updated_by'] = $_SESSION['member']['id'];
            }

            if (!count($this->err)) {

                $upd_arr = $this->daffny->DB->PrepareSql("app_defaultsettings", $sql_arr);
                $this->daffny->DB->update("app_defaultsettings", $upd_arr, "owner_id = '" . getParentId() . "'");

                $this->setFlashInfo("Default Settings have been updated.");
                redirect(getLink("defaultsettings"));
            } else {
                $inp = $sql_arr;
            }
        } else {

            $inp = $this->daffny->DB->selectRow("*", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");

            if (empty($inp)) {

                $this->setFlashError("Bad request.");
                redirect(getLink("defaultsettings"));
            }
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }
        
        $this->daffny->tpl->data = array("commercialTermsUpdatedAt" => $inp['commercial_terms_updated_at'], "commercialTermsUpdatedBy" => $this->getUserName($inp['commercial_terms_updated_by']));

        $this->form->TextField("commercialTermsUpdatedBy", 11, array("style" => "width:100px; text-align:right;"));
        $this->form->TextField("lead_start_number", 11, array("style" => "width:100px; text-align:left;"), "Start number of new lead", "</td><td>");
        $this->form->MoneyField("order_deposit", 11, array(), "Order deposit required", "</td><td>");
        $this->form->TextField("first_quote_followup", 3, array("style" => "width:50px;"), "First quote follow-up", "</td><td>");
        $this->form->TextField("mark_as_expired", 3, array("style" => "width:50px;padding-right:0;"), "Mark lead/quote/order as expired", "</td><td>");
        $this->form->TextField("mark_assumed_delivered", 3, array("style" => "width:50px;padding-right:0;"), "Mark as assumed delivered", "</td><td>");
        $this->form->TextField("logout_h", 2, array("style" => "width:70px;"), "Log out users after", "</td><td>");
        $this->form->TextField("logout_m", 2, array("style" => "width:70px;"), "", "&nbsp;");
        $this->form->TextField("payments_terms", 200, array(), "Payment Terms", "</td><td>");
        $this->form->TextField("payments_terms_cod", 200, array(), "Payment Terms COD", "</td><td>");
        $this->form->TextField("payments_terms_cop", 200, array(), "Payment Terms COP", "</td><td>");
        $this->form->TextField("payments_terms_billing", 200, array(), "Payment Terms Billing", "</td><td>");
        $this->form->TextField("payments_terms_invoice", 200, array(), "Payment Terms Invoice", "</td><td>");
        $this->form->ComboBox("zoom_level", array("100" => "100%", "125" => "125%", "150" => "150%"), array("style" => "width:113px;"), "Website Zoom Level", "</td><td>");
        $this->form->ComboBox("order_deposit_type", array("amount" => "amount", "percentage" => "percentage"), array("style" => "width:113px;"), "", "&nbsp;");
        $this->form->ComboBox("assign_unverified_orders_id", $this->getUserSelector("No specific user"), array(), "Assign unverified orders to", "</td><td>");
        $this->form->ComboBox("carrier_pmt_terms_id", $this->getPmtTermsSelector(), array(), "Carrier Pmt. Terms", "</td><td>");
        $this->form->ComboBox("carrier_pmt_terms_begin_id", $this->getPmtTermsBeginSelector(), array(), "Carrier Pmt. Terms Begin", "</td><td>");
        $this->form->ComboBox("carrier_pmt_method_id", $this->getPmtMethodSelector(), array(), "Carrier Pmt. Method", "</td><td>");
        $this->form->CheckBox("show_new_order", array(), "Show external \"New Order\" button on Place Order page Check this option to display the \"New Order\" button on the external Place Order page", "&nbsp;");
        $this->form->CheckBox("allow_replace_cod", array(), "Allow to replace the COD amount with lower Carrier Pay Check this option if you want to use Carrier Pay instead of COD if COD exceeds Carrier Pay when posting to CD", "&nbsp;");
        $this->form->CheckBox("show_vehicle_pricing", array(), "Show vehicle pricing information when dispatching. Check this option if you don't charge a deposit until an order is dispatched.", "&nbsp;");
        $this->form->TextArea("email_blind", 15, 10, array("style" => "height:50px; width:430px;"), "Send a blind carbon copy of outgoing e-mail to", "<br />");
        $this->form->Editor("order_terms", 900, 200);
        $this->form->Editor("commercial_terms", 900, 200);
        $this->form->Editor("dispatch_terms", 900, 200);
        $this->form->ComboBox("assign_type", array("single" => "Single User", "distribute" => "Distribute"), array("style" => "width:113px;"), "Choose algorithm", "</td><td>");
        $this->form->ComboBox("assign_leads_id", $this->getUserSelector("--Select one--"), array(), "Assign to", "</td><td>");
        $this->form->ComboBox("current_gateway", array("" => "NONE", "1" => "PayPal", "2" => "Authorize.net", "3" => "Mdsip", "9" => "Easy Pay"), array("style" => "width:100px;"), "Use Gateway", "</td><td>");
        $this->form->TextField("notify_email", 255, array(), "Email Notification", "</td><td>");
        $this->form->TextField("paypal_api_username", 255, array(), "API Username", "</td><td>");
        $this->form->TextField("paypal_api_password", 255, array(), "API Password", "</td><td>");
        $this->form->TextField("paypal_api_signature", 255, array(), "API Signature", "</td><td>");
        $this->form->TextField("anet_api_login_id", 255, array(), "API Login ID", "</td><td>");
        $this->form->TextField("anet_trans_key", 255, array(), "Transaction Key", "</td><td>");
        $this->form->TextField("gateway_api_username", 255, array(), "Gateway API Username", "</td><td>");
        $this->form->TextField("gateway_api_password", 255, array(), "Gateway API Password", "</td><td>");
        $this->form->TextField("gateway_api_signature", 255, array(), "Gateway API Signature", "</td><td>");
        $this->form->TextField("easy_pay_key", 255, array(), "EasyPay Key", "</td><td>");
        $this->form->CheckBox('hide_orders', array(), 'Hide my Orders in Freight Board', '&nbsp;&nbsp;&nbsp;');
        $this->form->TextField("central_dispatch_uid", 255, array(), "Your UID", "</td><td>");
        $this->form->ComboBox("central_dispatch_post", array("0" => "Only FreightDragon Freightboard", "1" => "FreightDragon And CentralDispatch"), array("style" => "width:225px;"), "Post to", "</td><td>");
        $this->form->TextField("smtp_server_name", 255, array(), "Smtp Server Name", "</td><td>");
        $this->form->TextField("smtp_server_port", 10, array("style" => "width:50px;"), "Smtp Server Port", "</td><td>");
        $this->form->ComboBox("smtp_use_ssl", array("1" => "Yes", "0" => "No"), array("style" => "width:113px;"), "Smtp Use Ssl", "</td><td>");
        $this->form->TextField("smtp_user_name", 100, array(), "Smtp User Name", "</td><td>");
        $this->form->TextField("smtp_user_password", 32, array(), "Smtp User Password", "</td><td>");
        $this->form->TextField("smtp_from_email", 100, array(), "Smtp From Email", "</td><td>");
        $this->form->TextField("quickbooks_id", 100, array(), "Quickbooks ID", "</td><td>");
        $this->form->CheckBox("mark_posted", array(), "Marked as Posted", "</td><td>");
        $this->form->CheckBox("mark_dispatched", array(), "Marked as Dispatched", "</td><td>");
        $this->form->CheckBox("mark_pickedup", array(), "Marked as Picked Up", "</td><td>");
        $this->form->CheckBox("mark_deivered", array(), "Marked as Delivered", "</td><td>");
        $this->form->CheckBox("send_customer", array(), "Customer", "</td><td>");
        $this->form->CheckBox("send_admin", array(), "Administrator of Account", "</td><td>");
        $this->form->CheckBox("card_batch_payment", array(), "<em>Credit Card Batch Payment</em>", "") . "";
        $card_batch = '<tr><td>' . $this->form->Radio('card_batch', array('value' => '1'), 'Order Created', '', false);
        $card_batch .= '</td></tr><tr><td>' . $this->form->Radio('card_batch', array('value' => '6'), 'Vehicle dispatched, signed', '', false);
        $card_batch .= '</td></tr><tr><td>' . $this->form->Radio('card_batch', array('value' => '5'), 'Vehicle dispatched, NOT signed', '', false);
        $card_batch .= '</td></tr><tr><td>' . $this->form->Radio('card_batch', array('value' => '8'), 'Order Picked up', '', false);
        $card_batch .= '</td></tr><tr><td>' . $this->form->Radio('card_batch', array('value' => '7'), 'Order Delivered', '', false);
        $this->input['card_batch'] = $card_batch;
        $this->form->CheckBox("card_payment_esigned", array(), "<em>esign/B2B on file</em>", "") . "";
        //build assigns

        $this->form->Hidden("thresholdRating", 255, array(), "", "</td><td>");
        $this->form->TextField("reviewNotificationEmail", 255, array(), "Email Notification", "</td><td>");
        
        /**
         * Auto Quoting API setting Fields
         */
        $this->form->TextField("auto_quote_api_pin", 255, array(), "API PIN", "</td><td>");
        $this->form->TextField("auto_quote_api_key", 255, array(), "API KEY", "</td><td>");
        $this->form->CheckBox("on_off_auto_quoting", array(), "&nbsp;&nbsp;On/Off Cron Auto Quoting", "") . "";
        $this->form->CheckBox("on_off_auto_quoting_email", array(), "&nbsp;&nbsp;Auto Quoting Mail", "") . "";
        $this->form->ComboBox("aq_email_template", array("" => "--Select One--") + $this->getEmailTemplates("quotes"), array(), "", "</td><td>");

        //TAI Input fields
        $this->form->TextField("tai_user", 255, array(), "TAI User", "</td><td>");
        $this->form->TextField("tai_password", 255, array(), "TAI Password", "</td><td>");
        } catch(Exception $e) {
            die(print_r($e));
        }
    }

    protected function getUserSelector($empty = "") {

        $users = array();

        if ($empty != "") {
            $users = array("" => $empty);
        }

        $sqlu = "SELECT m.id , m.contactname FROM members m WHERE m.status = 'Active' AND m.parent_id='" . getParentID() . "' AND `is_deleted` <> 1";

        $uq = $this->daffny->DB->query($sqlu);

        while ($rowu = $this->daffny->DB->fetch_row($uq)) {

            $users[$rowu['id']] = $rowu['contactname'];
        }

        return $users;
    }

    protected function getPmtTermsSelector() {

        return array("" => "--Select--"
            , "1" => "immediately"
            , "2" => "2 business days (Quick Pay)"
            , "5" => "5 business days"
            , "10" => "10 business days"
            , "15" => "15 business days"
            , "30" => "30 business days"
        );
    }

    protected function getPmtTermsBeginSelector() {

        return array("1" => "pickup"
            , "2" => "delivery"
            , "3" => "receiving a signed Bill of Lading"
        );
    }

    protected function getPmtMethodSelector() {

        return array("1" => "Cash"
            , "2" => "Certified Funds"
            , "3" => "Company Check"
            , "4" => "Comchek"
            , "5" => "TCH"
        );
    }

    /**
     * Function to get the Company Contact name on the basis of Member ID
     * 
     * @param int $memberID
     * @author Chetu Inc.
     * @return String
     */
    protected function getUserName($memberID) {
        $result = $this->daffny->DB->query("SELECT contactname FROM members WHERE id = '" . $memberID . "'");
        $row = mysqli_fetch_assoc($result);
        return $row['contactname'];
    }

}
