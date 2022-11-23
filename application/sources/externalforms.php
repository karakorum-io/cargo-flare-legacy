<?php

class ApplicationExternalforms extends ApplicationAction
{
    public $title = "Settings";
    public $section = "External Forms";
    public $tplname = "settings.externalforms.show";

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
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", "External Forms"));
        $this->check_access("settings");
        $inp = array();
        if (isset($_POST['submit'])) {
            $sql_arr = array(
                "redirect_url" => post_var('redirect_url')
                , "header" => post_var('header')
                , "footer" => post_var('footer'),
            );
            if (trim(post_var("redirect_url") != "")) {
                if (strpos(post_var("redirect_url"), "http://") === false && strpos(post_var("redirect_url"), "https://") === false) {
                    $this->err[] = "Redirect URL should start with http:// or https://";
                }
            }
            if (!count($this->err)) {
                $upd_arr = $this->daffny->DB->PrepareSql("app_externalforms", $sql_arr);
                $this->daffny->DB->update("app_externalforms", $upd_arr, "owner_id = '" . getParentId() . "'");
                if ($this->dbError()) {
                    return;
                } else {
                    $this->setFlashInfo("information has been updated.");
                    redirect(getLink("externalforms"));
                }
            } else {
                $inp = $sql_arr;
            }
        } else {
            $inp = $this->daffny->DB->selectRow("*", "app_externalforms", "WHERE owner_id='" . getParentId() . "'");
            if (empty($inp)) {
                $this->setFlashError("Access Denied.");
                redirect(getLink("companyprofile"));
            }
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        $this->form->TextField("redirect_url", 255, array(), "Redirect URL", "</td><td>");
        $this->form->TextArea("header", 15, 10, array("style" => "height:100px; width:430px;"), "Header", "<br />");
        $this->form->TextArea("footer", 15, 10, array("style" => "height:100px; width:430px;"), "Footer", "<br />");

    }

    public function download()
    {
        $arr = array();
        $arr['hash'] = get_var("hash");

        $st_arr = array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates());
        $arr['shipper_state'] = $this->daffny->html->select("shipper_state", $st_arr);
        $arr['shipper_country'] = $this->daffny->html->select("shipper_country", $this->getCountries());
        $arr['origin_state'] = $this->daffny->html->select("origin_state", $st_arr);
        $arr['origin_country'] = $this->daffny->html->select("origin_country", $this->getCountries());
        $arr['destination_state'] = $this->daffny->html->select("destination_state", $st_arr);
        $arr['destination_country'] = $this->daffny->html->select("destination_country", $this->getCountries());

        $veh_arr = $this->getVehiclesTypes(true);
        for ($i = 1; $i <= 8; $i++) {
            $arr['type' . $i] = $this->daffny->html->select("type[]", $veh_arr, "", array("id" => 'type' . $i));
            $arr['inop' . $i] = $this->daffny->html->select("inop[]", array('0' => 'No', '1' => 'Yes'), "", array("id" => 'inop' . $i));
        }
        /*referrers*/
        $this->daffny->tpl->referrers = array();
        $qr = $this->daffny->DB->select("*", "app_referrers", "WHERE owner_id='" . getParentId() . "' AND status = 1");
        while ($rr = $this->daffny->DB->fetch_row($qr)) {
            $this->daffny->tpl->referrers[] = $rr;
        }

        $data = $this->daffny->tpl->build("settings.externalforms.download", $arr);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($data));
        header("Content-type: text/html");
        header("Content-Disposition: attachment; filename=form.html");
        echo $data;
        exit();
    }

    public function build()
    {
        $this->tplname = "settings.externalforms.build";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("externalforms") => "External Forms", '' => "Build your own quote request form"));
        $this->check_access("settings");
        $inp = array();
        if (isset($_POST['submit'])) {
            if (trim(post_var("cust_return_url") != "")) {
                if (strpos(post_var("cust_return_url"), "http://") === false && strpos(post_var("cust_return_url"), "https://") === false) {
                    $this->err[] = "Return URL should start with http:// or https://";
                }
            }

            $sql_arr = array(
                "cust_company" => (post_var("cust_company") == "1" ? 1 : 0)
                , "cust_cell" => (post_var("cust_cell") == "1" ? 1 : 0)
                , "cust_phone2" => (post_var("cust_phone2") == "1" ? 1 : 0)
                , "cust_fax" => (post_var("cust_fax") == "1" ? 1 : 0)
                , "cust_address" => (post_var("cust_address") == "1" ? 1 : 0)
                , "cust_address2" => (post_var("cust_address2") == "1" ? 1 : 0)
                , "cust_city" => (post_var("cust_city") == "1" ? 1 : 0)
                , "cust_state" => (post_var("cust_state") == "1" ? 1 : 0)
                , "cust_zip" => (post_var("cust_zip") == "1" ? 1 : 0)
                , "cust_country" => (post_var("cust_country") == "1" ? 1 : 0)
                , "cust_vehicles_qty" => (int) post_var("cust_vehicles_qty")
                , "cust_color" => (post_var("cust_color") == "1" ? 1 : 0)
                , "cust_tariff" => (post_var("cust_tariff") == "1" ? 1 : 0)
                , "cust_deposit_required" => (post_var("cust_deposit_required") == "1" ? 1 : 0)
                , "cust_shipper_comments" => (post_var("cust_shipper_comments") == "1" ? 1 : 0)
                , "cust_pickup_zip" => (post_var("cust_pickup_zip") == "1" ? 1 : 0)
                , "cust_dropoff_zip" => (post_var("cust_dropoff_zip") == "1" ? 1 : 0)
                , "cust_return_url" => post_var("cust_return_url")
                , "cust_return_errors" => (post_var("cust_return_errors") == "1" ? 1 : 0)
                , "cust_use_ssl" => (post_var("cust_use_ssl") == "1" ? 1 : 0),
            );

            $cust_referrers = array();
            if (isset($_POST['ref'])) {
                foreach ($_POST['ref'] as $val) {
                    $cust_referrers[] = $val;
                }
            }

            $sql_arr['cust_referrers'] = implode(",", $cust_referrers);

            if (!count($this->err)) {
                $upd_arr = $this->daffny->DB->PrepareSql("app_externalforms", $sql_arr);
                $this->daffny->DB->update("app_externalforms", $upd_arr, "owner_id = '" . getParentId() . "'");
                if ($this->dbError()) {
                    return;
                } else {
                    $this->setFlashInfo("Form has been generated.");
                    redirect(getLink("externalforms", "custom"));
                }
            } else {
                $inp = $sql_arr;
            }
        } else {
            $inp = $this->daffny->DB->selectRow("*", "app_externalforms", "WHERE owner_id='" . getParentId() . "'");
            if (empty($inp)) {
                $this->setFlashError("Access Denied.");
                redirect(getLink("companyprofile"));
            }
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        $this->form->CheckBox("cust_first_name", array("disabled" => "disabled"), "First Name" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_last_name", array("disabled" => "disabled"), "Last Name" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_phone", array("disabled" => "disabled"), "Phone" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_email", array("disabled" => "disabled"), "E-mail" . $this->requiredTxtLeft, "&nbsp;");

        $this->form->CheckBox("cust_company", array(), "Company", "&nbsp;");
        $this->form->CheckBox("cust_cell", array(), "Cell Phone", "&nbsp;");
        $this->form->CheckBox("cust_phone2", array(), "Phone 2", "&nbsp;");
        $this->form->CheckBox("cust_fax", array(), "Fax", "&nbsp;");
        $this->form->CheckBox("cust_address", array(), "Address", "&nbsp;");
        $this->form->CheckBox("cust_address2", array(), "Address 2", "&nbsp;");
        $this->form->CheckBox("cust_city", array(), "City", "&nbsp;");
        $this->form->CheckBox("cust_state", array(), "State", "&nbsp;");
        $this->form->CheckBox("cust_zip", array(), "Zip", "&nbsp;");
        $this->form->CheckBox("cust_country", array(), "Country", "&nbsp;");

        $this->form->ComboBox("cust_vehicles_qty", $this->getQty(1, 30), array("style" => "width:50px;"), "", "");
        $this->form->CheckBox("cust_year", array("disabled" => "disabled"), "Year" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_make", array("disabled" => "disabled"), "Make" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_model", array("disabled" => "disabled"), "Model" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_vehicle_type", array("disabled" => "disabled"), "Vehicle Type" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_vehicle_inop", array("disabled" => "disabled"), "Vehicle Inop" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_tariff", array(), "Tariff", "&nbsp;");
        $this->form->CheckBox("cust_deposit_required", array(), "Deposit Required", "&nbsp;");

        $this->form->CheckBox("cust_estimated_ship_date", array("disabled" => "disabled"), "Estimated Ship Date" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_ship_via", array("disabled" => "disabled"), "Ship Via" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_shipper_comments", array(), "Shipper Comments", "&nbsp;");

        $this->form->CheckBox("cust_pickup_city", array("disabled" => "disabled"), "City" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_pickup_state", array("disabled" => "disabled"), "State" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_pickup_country", array("disabled" => "disabled"), "Country" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_pickup_zip", array(), "Zip", "&nbsp;");
        $this->form->CheckBox("cust_dropoff_city", array("disabled" => "disabled"), "City" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_dropoff_state", array("disabled" => "disabled"), "State" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_dropoff_country", array("disabled" => "disabled"), "Country" . $this->requiredTxtLeft, "&nbsp;");
        $this->form->CheckBox("cust_dropoff_zip", array(), "Zip", "&nbsp;");

        $this->form->TextField("cust_return_url", 255, array(), "Return URL", "</td><td>");
        $this->form->CheckBox("cust_return_errors", array(), "Return Errors (Return URL required)", "&nbsp;");
        $this->form->CheckBox("cust_use_ssl", array(), "Use SSL Connection", "&nbsp;");

        $this->daffny->tpl->referrers = array();
        $qr = $this->daffny->DB->select("*", "app_referrers", "WHERE owner_id='" . getParentId() . "' AND status = 1");
        $refs = explode(",", $this->input['cust_referrers']);
        while ($rr = $this->daffny->DB->fetch_row($qr)) {
            $rr['ch'] = "";
            if (in_array($rr['id'], $refs)) {
                $rr['ch'] = "checked=\"checked\"";
            }
            $this->daffny->tpl->referrers[] = $rr;
        }
    }

    public function custom()
    {
        $this->tplname = "settings.externalforms.custom";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("externalforms") => "External Forms", getLink("externalforms", "build") => "Build your own quote request form", '' => "Show"));
        $this->check_access("settings");

        $row = $this->daffny->DB->selectRow("*", "app_externalforms", "WHERE owner_id='" . getParentId() . "'");
        if (empty($row)) {
            $this->setFlashError("Access Denied.");
            redirect(getLink("companyprofile"));
        }

        $this->input['custom_form'] = $this->generate_form($row);
        $this->form->TextArea("custom_form", 15, 10, array("onClick" => "SelectAll();", "style" => "height:200px; width:700px;"), "", "", true);
    }

    protected function generate_form($row)
    {

        $this->daffny->tpl->r = $row;
        $st_arr = array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates());
        $row['shipper_state'] = $this->daffny->html->select("shipper_state", $st_arr);
        $row['shipper_country'] = $this->daffny->html->select("shipper_country", $this->getCountries());
        $row['origin_state'] = $this->daffny->html->select("origin_state", $st_arr);
        $row['origin_country'] = $this->daffny->html->select("origin_country", $this->getCountries());
        $row['destination_state'] = $this->daffny->html->select("destination_state", $st_arr);
        $row['destination_country'] = $this->daffny->html->select("destination_country", $this->getCountries());

        $veh_arr = $this->getVehiclesTypes(true);
        for ($i = 1; $i <= (int) $row['cust_vehicles_qty']; $i++) {
            $row['type' . $i] = $this->daffny->html->select("type[]", $veh_arr, "", array("id" => 'type' . $i));
            $row['inop' . $i] = $this->daffny->html->select("inop[]", array('0' => 'No', '1' => 'Yes'), "", array("id" => 'inop' . $i));
        }
        /*referrers*/
        $this->daffny->tpl->referrers = array();
        if (trim($row['cust_referrers']) != "") {
            $qr = $this->daffny->DB->select("*", "app_referrers", "WHERE id IN(" . $row['cust_referrers'] . ") AND owner_id='" . getParentId() . "' AND status = 1");
            while ($rr = $this->daffny->DB->fetch_row($qr)) {
                $this->daffny->tpl->referrers[] = $rr;
            }
        }
        return $this->daffny->tpl->build("settings.externalforms.generate", $row);
    }

}
