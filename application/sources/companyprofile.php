<?php

require_once DAFFNY_PATH . "libs/upload.php";
require_once DAFFNY_PATH . "libs/cropper.php";

class ApplicationCompanyprofile extends ApplicationAction
{

    public $title = "Company Profile";
    public $section = "Company Profile";
    public $tplname = "settings.companyprofile";

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
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", '' => "Company Profile"));
        $this->check_access("settings");
        $companyProfile = $this->daffny->DB->selectRow("*", "app_company_profile", "WHERE owner_id='" . getParentId() . "'");
        if (isset($_POST['submit'])) {
            $this->isEmpty("address1", "Address");
            $this->isEmpty("city", "City");
            if (post_var('country') == "US") {
                $this->isEmpty("state", "State");
            } else {
                $this->isEmpty("state_other", "State/Province");
            }
            $this->isEmpty("zip_code", "Zip/Postal Code");
            $this->isEmpty("timezone", "Timezone");
            $this->isEmpty("phone", "Main Phone");
            $this->isEmpty("email", "E-mail");
            $this->checkEmail("email", "E-mail");

            $this->isEmpty("sales_email", "Sales Email");
            $this->checkEmail("sales_email", "Sales Email");
            $this->checkEmail("dispatch_email", "Dispatch E-mail");
            $this->checkEmail("support_email", "Support E-mail");

            $this->isEmpty('icc_mc_number', 'ICC-MC#');
            if ($companyProfile['is_carrier']) {
                $this->isEmpty('insurance_company', 'Insurance Company');
                $this->isEmpty('insurance_policy_number', 'Policy Number');
                $this->isEmpty('insurance_agent_name', 'Agent Name');
                $this->isEmpty('insurance_agent_phone', 'Agent Phone');
                $this->isEmpty('liability_amount', 'Liability Amount');
                $this->isEmpty('insurance_coverage', 'Insurance Coverage');
                $this->isEmpty('cargo_deductible', 'Cargo Deductible');
                $this->isEmpty('insurance_expdate', 'Expiration Date');
            }

            if (trim(post_var('email')) != "") {
                if ($this->checkDuplicateCompanyEmail(trim(post_var('email')), getParentId())) {
                    $this->err[] = "<strong>E-mail</strong> already registered.";
                }
            }

            $sql_arr = array(
                "owner" => post_var('owner')
                , "address1" => post_var('address1')
                , "address2" => post_var('address2')
                , "city" => post_var('city')
                , "state" => post_var('state')
                , "state_other" => post_var('state_other')
                , "zip_code" => post_var('zip_code')
                , "country" => post_var('country')
                , "timezone" => post_var('timezone')
                , "phone_local" => post_var('phone_local')
                , "phone_tollfree" => post_var('phone_tollfree')
                , "phone_cell" => post_var('phone_cell')
                , "fax" => post_var('fax')
                , "email" => post_var('email')
                , "site" => post_var('site')
                , "mc_number" => post_var('mc_number')
                , "description" => post_var('description')
                , "sales_phone" => post_var('sales_phone')
                , "sales_fax" => post_var('sales_fax')
                , "sales_email" => post_var('sales_email')
                , "sales_email_bcc" => post_var('sales_email_bcc')
                , "dispatch_contact" => post_var('dispatch_contact')
                , "dispatch_email" => post_var('dispatch_email')
                , "delivery_confirmation_mail" => post_var('delivery_confirmation_mail')
                , "dispatch_phone" => post_var('dispatch_phone')
                , "dispatch_fax" => post_var('dispatch_fax')
                , "dispatch_accounting_fax" => post_var('dispatch_accounting_fax')
                , "support_phone" => post_var('support_phone')
                , "support_fax" => post_var('support_fax')
                , "support_email" => post_var('support_email')
                , "contactname" => post_var('contactname')
                , "phone" => post_var('phone')
                , "established" => post_var('established')
                , "icc_mc_number" => post_var('icc_mc_number')
                , "ref1_name" => post_var('ref1_name')
                , "ref1_phone" => post_var('ref1_phone')
                , "ref2_name" => post_var('ref2_name')
                , "ref2_phone" => post_var('ref2_phone')
                , "ref3_name" => post_var('ref3_name')
                , "ref3_phone" => post_var('ref3_phone')
                , "insurance_company" => post_var('insurance_company')
                , "insurance_policy_number" => post_var('insurance_policy_number')
                , "insurance_agent_name" => post_var('insurance_agent_name')
                , "insurance_agent_phone" => post_var('insurance_agent_phone')
                , "liability_amount" => post_var('liability_amount')
                , "insurance_coverage" => post_var('insurance_coverage')
                , "cargo_deductible" => post_var('cargo_deductible')
                , "brocker_bond_name" => post_var('brocker_bond_name')
                , "brocker_bond_phone" => post_var('brocker_bond_phone')
                , "hours_or_operation" => post_var('hours_or_operation')
                , "preferred_contact_method" => post_var('preferred_contact_method')
                , "sync" => (post_var('sync') == 1) ? 1 : 0,
            );

            $sql_arr['insurance_expdate'] = $this->validateDate(post_var("insurance_expdate"), "Expiration Date");
            $inp = array();
            if (!count($this->err)) {

                if ($sql_arr['country'] == "US") {
                    $sql_arr['state_other'] = "";
                } else {
                    $sql_arr['state'] = "";
                }

                $upd_arr = $this->daffny->DB->PrepareSql("app_company_profile", $sql_arr);
                $this->daffny->DB->update("app_company_profile", $upd_arr, "owner_id = '" . getParentId() . "'");
                $_SESSION['timezone'] = post_var('timezone');
                if ($this->dbError()) {
                    return;
                } else {
                    $this->setFlashInfo("Company profile has been updated.");
                    if ($sql_arr['sync'] == 1) {
                        $updateArr = array(
                            'contact_name1' => $companyProfile['contactname'],
                            'company_name' => $companyProfile['companyname'],
                            'address1' => trim($sql_arr['address1']),
                            'address2' => trim($sql_arr['address2']),
                            'city' => $sql_arr['city'],
                            'state' => $sql_arr['state'],
                            'state_other' => $sql_arr['state_other'],
                            'zip_code' => $sql_arr['zip_code'],
                            'country' => $sql_arr['country'],
                            'email' => $sql_arr['dispatch_email'],
                            'phone1' => $sql_arr['phone'],
                            'cell' => $sql_arr['phone_cell'],
                            'fax' => $sql_arr['dispatch_fax'],
                            'insurance_companyname' => $sql_arr['insurance_company'],
                            'insurance_expirationdate' => substr($sql_arr['insurance_expdate'], 0, 10),
                            'insurance_iccmcnumber' => $sql_arr['icc_mc_number'],
                            'insurance_policynumber' => $sql_arr['insurance_policy_number'],
                            'insurance_agentname' => $sql_arr['insurance_agent_name'],
                            'insurance_agentphone' => $sql_arr['insurance_agent_phone'],
                        );
                        $this->daffny->DB->update('app_accounts', $updateArr, 'member_id = ' . getParentId());
                    }
                    redirect(getLink("companyprofile"));
                }
            } else {
                $inp = $sql_arr;
                $inp['is_carrier'] = $companyProfile['is_carrier'];
                $inp['is_broker'] = $companyProfile['is_broker'];
                $inp['companyname'] = $companyProfile['companyname'];
            }
        } else {
            $inp = $companyProfile;
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        $this->daffny->tpl->highlight = $this->required;

        $this->form->TextField("owner", 255, array(), "Owner/Manager", "</td><td>");
        $this->form->TextField("address1", 255, array(), $this->requiredTxt . "Address", "</td><td>");
        $this->form->TextField("address2", 255, array(), "&nbsp;", "</td><td>");

        $this->form->TextField("city", 255, array(), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox("state", array("" => "Select State") + $this->getStates(), array(), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField("state_other", 50, array(), $this->requiredTxt . "State/Province", "</td><td>");
        $this->form->TextField("zip_code", 10, array('style' => 'width:100px', "class" => "zip"), $this->requiredTxt . "Zip/Postal Code", "</td><td>");
        $this->form->ComboBox("country", $this->getCountries(), array(), "Country", "</td><td>");
        $this->form->ComboBox("timezone", $this->getTimeZones(), array(), $this->requiredTxt . "Timezone", "</td><td>");

        $this->form->TextField("phone_local", 25, array("class" => "phone"), "Phone (local)", "</td><td>");
        $this->form->TextField("phone_tollfree", 25, array("class" => "phone"), "Phone (toll-free)", "</td><td>");
        $this->form->TextField("phone_cell", 25, array("class" => "phone"), "Phone (cell)", "</td><td>");
        $this->form->TextField("fax", 25, array("class" => "phone"), "Fax", "</td><td>");
        $this->form->TextField("email", 255, array(), $this->requiredTxt . "E-mail", "</td><td>");
        $this->form->TextField("site", 255, array(), "Website", "</td><td>");
        $this->form->TextField("mc_number", 255, array(), "MC Number", "</td><td>");
        $this->form->TextArea("description", 15, 10, array("style" => "height:100px; width:220px;"), "Company Description<br /><em>(up to 500 characters)</em>", "</td><td>");
        $this->form->FileFiled("image", array(), "Logo", "</td><td>");
        $this->daffny->tpl->image_file = $this->getImage();

        $this->form->TextField("sales_phone", 25, array("class" => "phone"), "Sales Phone", "</td><td>");
        $this->form->TextField("sales_fax", 25, array("class" => "phone"), "Sales Fax", "</td><td>");
        $this->form->TextField("sales_email", 255, array(), $this->requiredTxt . "Sales E-mail", "</td><td>");
        $this->form->CheckBox("sales_email_bcc", array(), "BCC this address when a quote is converted", "&nbsp;");

        $this->form->TextField("dispatch_contact", 255, array(), "Dispatch Contact", "</td><td>");
        $this->form->TextField("dispatch_email", 255, array(), "Dispatch E-mail", "</td><td>");
        $this->form->TextField("dispatch_phone", 25, array("class" => "phone"), "Dispatch Phone", "</td><td>");
        $this->form->TextField("dispatch_fax", 25, array("class" => "phone"), "Dispatch Fax", "</td><td>");
        $this->form->TextField("dispatch_accounting_fax", 25, array("class" => "phone"), "Accounting Fax", "</td><td>");
        $this->form->TextField("delivery_confirmation_mail", 255, array(), "Delivery Confirmation", "</td><td>");

        $this->form->TextField("support_phone", 25, array("class" => "phone"), "Support Phone", "</td><td>");
        $this->form->TextField("support_fax", 255, array("class" => "phone"), "Support Fax", "</td><td>");
        $this->form->TextField("support_email", 255, array(), "Support E-mail", "</td><td>");

        $this->form->TextField("contactname", 255, array(), "Contact Name", "</td><td>");
        $this->form->TextField("phone", 25, array("class" => "phone"), $this->requiredTxt . "Main Phone", "</td><td>");
        $this->form->TextField("established", 50, array(), "Established in", "</td><td>");
        $this->form->TextField("icc_mc_number", 50, array(), "ICC-MC#", "</td><td>");
        $this->form->TextField("ref1_name", 255, array(), "Business Reference #1", "</td><td>");
        $this->form->TextField("ref2_name", 255, array(), "Business Reference #2", "</td><td>");
        $this->form->TextField("ref3_name", 255, array(), "Business Reference #3", "</td><td>");
        $this->form->TextField("ref1_phone", 25, array("class" => "phone"), "(Phone)", "</td><td>");
        $this->form->TextField("ref2_phone", 25, array("class" => "phone"), "(Phone)", "</td><td>");
        $this->form->TextField("ref3_phone", 25, array("class" => "phone"), "(Phone)", "</td><td>");
        $this->form->TextField("insurance_company", 255, array(), "Insurance Company", "</td><td>");
        $this->form->TextField("insurance_policy_number", 255, array(), "Policy Number", "</td><td>");
        $this->form->TextField("insurance_agent_name", 255, array(), "Agent Name", "</td><td>");
        $this->form->TextField("insurance_agent_phone", 255, array(), "Agent Phone", "</td><td>");
        $this->form->MoneyField("liability_amount", 15, array(), "Liability Amount $", "</td><td>");
        $this->form->MoneyField("insurance_coverage", 15, array(), "Insurance Coverage $", "</td><td>");
        $this->input['insurance_expdate'] = $this->getFormattedDate($this->input['insurance_expdate']);
        $this->form->TextField("insurance_expdate", 10, array("style" => "width:75px;"), "Expiration Date", "</td><td>");
        $this->form->MoneyField("cargo_deductible", 15, array(), "Cargo Deductible $", "</td><td>");
        $this->form->TextField("brocker_bond_name", 255, array(), "Surety Bonding Agent", "</td><td>");
        $this->form->TextField("brocker_bond_phone", 25, array("class" => "phone"), "Bonding Company Phone", "</td><td>");
        $this->form->TextField("hours_or_operation", 255, array(), "Hours or Operation", "</td><td>");
        $this->form->TextField("preferred_contact_method", 255, array(), "Preferred Contact Method", "</td><td>");

        $this->form->CheckBox("is_broker", array("disabled" => "disabled"), "Broker/Dealership", "&nbsp;");
        $this->form->CheckBox("is_carrier", array("disabled" => "disabled"), "Carrier", "&nbsp;");
        $this->form->CheckBox("sync", array(), "Allow Sync", "&nbsp;");
    }

    /* Upload logo */

    protected function uploadFile()
    {
        $id = getParentId();
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "company/";
        $upload->max_file_size = 10000000;
        $upload->form_field = "image";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("jpg", "gif", "png", "jpeg");
        $upload->save_as_file_name = md5(time() . "-" . rand());
        $upload->upload_process();
        switch ($upload->error_no) {
            case 0:{
                    $filename = $upload->save_as_file_name;
                    
                    file_put_contents( UPLOADS_PATH . "company/" . $id . ".jpg" , file_get_contents(UPLOADS_PATH . "company/" . $filename));
                    file_put_contents( UPLOADS_PATH . "company/" . $id . "_small.jpg" , file_get_contents(UPLOADS_PATH . "company/" . $filename));
                    
                    // $cropper = new ImageCropper;
                    // $size = getimagesize(UPLOADS_PATH . "company/" . $filename);
                    // $cropper->resize_and_crop(UPLOADS_PATH . "company/" . $filename, UPLOADS_PATH . "company/" . $id . ".jpg", @$size[0], @$size[1]);
                    // $cropper->resize_and_crop(UPLOADS_PATH . "company/" . $filename, UPLOADS_PATH . "company/" . $id . "_small.jpg", 200, 50);

                    @unlink(UPLOADS_PATH . "company/" . $filename);
                    $out = "<div id=\"logo-file\"><img src=\"" . SITE_IN . "uploads/company/" . $id . "_small.jpg?" . rand(10000, 99999) . "\" width=\"200\" height=\"50\" alt=\"Logo\" style=\"border:#999 1px solid;\" /><br />";
                    $out .= ' <img src="' . SITE_IN . 'images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16" /> <span class="like-link" onclick="return deleteLogo(\'' . getLink("companyprofile", "delete-file") . '\');">Remove logo</span></div>';
                    return $out;
                }

            case 1:
                return "ERROR:File not selected or empty.";
            case 2:
            case 5:
                return "ERROR:Invalid File Extension";
            case 3:
                return "ERROR:File too big";
            case 4:
                return "ERROR:Cannot move uploaded file";
        }
    }

    public function upload_file()
    {
        $result = $this->uploadFile();
        echo $result;
        exit();
    }

    protected function getImage()
    {
        $id = getParentId();
        $image = UPLOADS_PATH . "company/" . $id . ".jpg";
        if (file_exists($image)) {
            $out = "<div id=\"logo-file\"><img src=\"" . SITE_IN . "uploads/company/" . $id . "_small.jpg\" alt=\"Logo\" width=\"200\" height=\"50\" style=\"border:#999 1px solid;\" /><br />";
            $out .= ' <img src="' . SITE_IN . 'images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16" /> <span class="like-link" onclick="return deleteLogo(\'' . getLink("companyprofile", "delete-file") . '\');">Remove logo</span></div>';
            return $out;
        } else {
            return "";
        }
    }

    public function delete_file()
    {
        $id = getParentId();
        $file = UPLOADS_PATH . "company/" . $id . ".jpg";
        if (file_exists($file)) {
            @unlink($file);
        }
        $file = UPLOADS_PATH . "company/" . $id . "_small.jpg";
        if (file_exists($file)) {
            @unlink($file);
        }
        $result = "";
        echo $result;
        exit();
    }

    public function contract()
    {
        $this->title = "My Contract";
        $this->section = "Company Profile";
        $this->tplname = "myaccount.contract";

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Company Profile", '' => "Contract"));
        $this->check_access("settings");
        if (isset($_POST['submit'])) {
            $sql_arr = array(
                "contract" => post_var('contract'),
            );

            if (!count($this->err)) {
                $upd_arr = $this->daffny->DB->PrepareSql("app_company_profile", $sql_arr);
                $this->daffny->DB->update("app_company_profile", $upd_arr, "owner_id = '" . getParentId() . "'");
                if ($this->dbError()) {
                    $this->setFlashError("Access denied.");
                    redirect(getLink("companyprofile"));
                } else {
                    $this->setFlashInfo("'My Contract' has been updated.");
                    redirect(getLink("companyprofile"));
                }
            } else {
                $inp = $sql_arr;
            }
        } else {
            $inp = $this->daffny->DB->selectRow("*", "app_company_profile", "WHERE owner_id='" . getParentId() . "'");
        }

        foreach ($inp as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        $this->form->TextArea("contract", 15, 10, array("style" => "height:400px; width:500px;"), "My Contract", "</td><td>");
    }

}
