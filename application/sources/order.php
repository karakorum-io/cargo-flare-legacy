<?php

require_once DAFFNY_PATH . "libs/upload.php";

class ApplicationOrder extends ApplicationAction
{
    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('orders.common');
        parent::construct();
    }

    public function create()
    {
        try {
            
            $this->tplname = "entity.order.create";
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Create'));
            $this->title = "Create Order";
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

            $this->input['total_tariff'] = "$ 0.00";
            $this->input['total_deposit'] = "$ 0.00";
            $this->input['carrier_pay'] = "$ 0.00";
            $this->input['include_shipper_comment'] = 0;

            if (trim($_POST['payments_terms']) == "") {
                $this->input['payments_terms'] = '';
            } else {
                $this->input['payments_terms'] = $_POST['payments_terms'];
            }

            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->getEditForm($referrer_status);

        } catch (Exception $e) {
            print_r($e);
        }
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
        $this->form->TextField("shipper_phone1", 12, array('style' => 'width:130px;', 'tabindex' => 7, "class" => "phone elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("shipper_phone2", 12, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 8, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone', 'tabindex' => 9, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('class' => 'phone', 'tabindex' => 10, "$readonly" => "$readonly"), "Fax", "</td><td>");
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
        $this->form->ComboBox('origin_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:140px;', 'tabindex' => 21, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 10, array('style' => 'width:70px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 22, "$readonly" => "$readonly"), "", "");
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
        $this->form->TextField("origin_phone1", 12, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 32, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("origin_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone2", 12, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 33, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("origin_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone3", 12, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 34, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("origin_phone3_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_phone4", 12, array('style' => 'width:130px;', 'class' => 'phone', 'tabindex' => 34, "$readonly" => "$readonly"), "Phone 4", "</td><td>");
        $this->form->TextField("origin_phone4_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("origin_mobile", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 35, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("origin_mobile2", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 35, "$readonly" => "$readonly"), "Mobile2", "</td><td>");
        $this->form->TextField("origin_fax", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 36, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("origin_fax2", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 36, "$readonly" => "$readonly"), "Fax2", "</td><td>");

        /* DESTINATION */
        $this->form->TextField("destination_address1", 255, array('tabindex' => 37, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("destination_address2", 255, array('tabindex' => 38, "$readonly" => "$readonly"), "&nbsp;", "</td><td>");
        $this->form->TextField("destination_city", 255, array('class' => 'geo-city', 'tabindex' => 39, "elementname" => "input", "class" => "elementname", "$readonly" => "$readonly"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:140px;', 'tabindex' => 40, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 10, array('style' => 'width:70px;margin-left:5px;', 'class' => 'zip', 'tabindex' => 41, "$readonly" => "$readonly"), "", "");
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
        $this->form->TextField("destination_phone1", 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 51, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("destination_phone1_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone2", 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 52, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("destination_phone2_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone3", 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 53, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("destination_phone3_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_phone4", 12, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 53, "$readonly" => "$readonly"), "Phone 4", "</td><td>");
        $this->form->TextField("destination_phone4_ext", 10, array('style' => 'width:70px;margin-left:5px;', 'placeholder' => 'Ext.', 'tabindex' => 7, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), "", "</td><td>");
        $this->form->TextField("destination_mobile", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 54, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("destination_mobile2", 255, array('style' => 'width:140px;', 'class' => 'phone', 'tabindex' => 54, "$readonly" => "$readonly"), "Mobile2", "</td><td>");
        $this->form->TextField("destination_fax", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 55, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("destination_fax2", 32, array('class' => 'phone', 'style' => 'width:140px;', 'tabindex' => 55, "$readonly" => "$readonly"), "Fax2", "</td><td>");

        /* SHIPPING INFORMATION */
        $this->form->TextField("avail_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, "$readonly" => "$readonly"), $this->requiredTxt . "1st Avail. Pickup Date", "</td><td>");
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
        $this->form->TextArea("notes_for_shipper", 2, 10, array('style' => 'height:40px;', "onkeyup" => "countChar(this)", "maxlength" => "60", 'tabindex' => 63, "$disabled" => "$disabled"), "Add special Note to appeared on FreightBoard ", "</td><td>");
        $this->form->TextArea("notes_from_shipper", 2, 10, array('style' => 'height:40px;', 'tabindex' => 64, "$disabled" => "$disabled"), "Special Dispatch Instructions", "</td><td>");
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

        $this->form->TextField("pickup_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 66, "$disabled" => "$disabled"), "Pickup Terminal Fee", "</td><td>$&nbsp;");
        $this->form->TextField("delivery_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 67, "$disabled" => "$disabled"), "Delivery Terminal Fee", "</td><td>$&nbsp;");

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
        $this->form->TextField("e_cc_zip", 11, array('tabindex' => 80, "class" => "zip", "style" => "width:100px;", "$disabled" => "$disabled"), "Zip Code", "</td><td>");
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
        $this->form->TextArea("note_to_shipper", 4, 10, array('style' => 'height: 80px;width:800px;', 'tabindex' => 56, "$disabled" => "$disabled"), "", "</td><td align='center'>");
    }
}