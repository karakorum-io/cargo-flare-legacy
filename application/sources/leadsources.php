<?php

require_once ROOT_PATH . "libs/xmlapi.php";

class Applicationleadsources extends ApplicationAction
{

    public $title = "Lead Sources";
    public $section = "Lead Sources";
    public $tplname = "accounts.leadsources.list";

    public function construct()
    {
        if (!$this->check_access('leadsources')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    /**
     * List all
     *
     */
    public function idx()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array('' => "Lead Sources"));
            $this->tplname = "accounts.leadsources.list";
            $leadsourceManager = new LeadsourceManager($this->daffny->DB);
            $this->applyOrder("app_leadsources");
            $where = "";
            $leadsources = $leadsourceManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId());
            $this->setPager($leadsourceManager->getPager());
            $this->daffny->tpl->leadsources = $leadsources;
        } catch (FDException $e) {
            redirect(SITE_IN);
        }
    }

    /**
     * Function to add edit lead sources in the web application
     *
     * @author Chetu Inc.
     * @return type
     */
    public function edit()
    {
        try {

            $member = new Member($this->daffny->DB);
            $member->load($_SESSION['parent_id']);
            $companyProfile = $member->getCompanyProfile();
            $companyabbr = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $companyProfile->companyname));
            $email_to = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $member->username)) . "." . $companyabbr . "@" . $this->daffny->cfg['MAILDOMAIN'];

            $ID = (int) get_var("id");
            $leadsource = new Leadsource($this->daffny->DB);
            $this->tplname = "accounts.leadsources.form";
            $this->title .= ($ID > 0 ? " - Edit Lead Source" : " - Add New Lead Source");
            $this->check_access("leadsources", "edit", array("id" => $ID));

            $_POST['domain'] = GetDomain(post_var("domain"));

            $sql_arr = array(
                "company_name" => post_var("company_name")
                , "is_send_copy" => (post_var("is_send_copy") == "1" ? 1 : 0)
                , "exclude_from_auto_quote" => (post_var("exclude_from_auto_quote") == "1" ? 1 : 0)
                , "phone" => post_var("phone")
                , "domain" => post_var("domain")
                , "email_to" => $email_to
                , "email_forward" => post_var("email_forward"),
            );

            $this->input = $sql_arr;
            if (isset($_POST['submit'])) {
                $this->isEmpty("company_name", "Company Name");

                $this->isEmpty("domain", "Domain");
                $this->checkEmail("email_to", "To Address");
                $this->checkEmail("email_forward", "Forward Email");
                if ($sql_arr['is_send_copy'] == 1) {
                    $this->isEmpty("email_forward", "Forward Email");
                }

                if ($ID <= 0) {
                    $dr1 = array();
                    $dr1 = $this->daffny->DB->select_one("*", Leadsource::TABLE, "WHERE domain = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['domain']) . "' AND status=1 AND `owner_id` = " . getParentId());
                    if (!empty($dr1)) {
                        $this->err[] = "Field <strong>domain</strong> already exist.";
                    }
                }

                if (!count($this->err)) {
                    $sql_arr['status'] = 1;
                    if ($ID > 0) {
                        $leadsource->update($sql_arr, $ID);
                        $this->setFlashInfo("Lead Source has been updated.");
                    } else {

                        //making company name email user friendly
                        $company_name_for_email = strtolower(
                            str_replace(" ", "_", $_SESSION['member']['companyname'])
                        );

                        $sql_arr['cron_email'] = $company_name_for_email;
                        $sql_arr['create_date'] = date("Y-m-d H:i:s");
                        $sql_arr['owner_id'] = getParentId();
                        $created_lead_source_id = $leadsource->create($sql_arr);

                        $cPanel = new ServerHandler(
                            $this->daffny->cfg['cPanel_usr'], $this->daffny->cfg['cPanel_pwd'], $this->daffny->cfg['cPanel_id'], $this->daffny->cfg['MAILDOMAIN']
                        );

                        //configuration for dynamic cronjob and email address
                        $cron_email = $company_name_for_email . "_" . $created_lead_source_id . "_" . $_SESSION['parent_id'] . "@" . $this->daffny->cfg['MAILDOMAIN'];

                        // dynamic email and cronjob
                        $response = $cPanel->add_email_account($cron_email);

                        if ($response === 1) {
                            ini_set('display_errors', 1);
                            ini_set('display_startup_errors', 1);
                            error_reporting(E_ALL);

                            $code = file_get_contents("../cronjobs/leadsource/codebase/get_leads_dynamic_cron_code.php");

                            //tag replacement
                            $code = str_replace("@cron_email@", $cron_email, $code);
                            $code = str_replace("@cron_email_password@", ServerHandler::DEFAULT_EMAIL_PASSWORD, $code);
                            $code = str_replace("@parent_id@", $_SESSION['parent_id'], $code);
                            $code = str_replace("@lead_source@", $created_lead_source_id, $code);
                            $cPanel->create_cronjob($_SESSION['parent_id'], $created_lead_source_id, $code);
                            $this->setFlashInfo(LEAD_SOURCE_ADD);
                        } else {
                            $this->setFlashError($response);
                        }
                    }
                    $dr = array();
                    $dr = $this->daffny->DB->select_one("*", Leadsource::TABLE, "WHERE domain = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['domain']) . "' AND status=1");

                    if (!empty($dr) && isset($dr['id'])) {
                        $leadsource->update(array("status" => 1), $ID);
                    } else {
                        redirect(getLink("leadsources", "original", "id", $ID));
                    }

                    if ($this->dbError()) {
                        return;
                    }

                    $this->daffny->DB->delete("app_defaultsettings_ass", "leadsource_id='" . $ID . "'");
                    foreach ($_POST['assigns'] as $key => $value) {
                        if (trim($value) != "") {
                            $arr = array("leadsource_id" => $ID
                                , "owner_id" => (int) getParentId()
                                , "member_id" => (int) $value
                                , "ord" => $_POST['ords'][(int) $value]
                                , "batch" => $_POST['batches'][(int) $value],
                            );
                            $this->daffny->DB->insert("app_defaultsettings_ass", $arr);
                        }
                    }
                    redirect(getLink("leadsources", "details", "id", $ID));
                }
            } else {
                if ($ID > 0) {
                    $leadsource->load($ID);
                    if ($leadsource->owner_id != getParentId()) {
                        $this->setFlashError("Access denied.");
                        redirect(getLink('leadsources'));
                    }
                    $this->input = $leadsource->getAttributes();
                }
            }
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leadsources") => "Lead Sources", '' => ($ID > 0 ? htmlspecialchars($this->input['company_name']) : "Add New Lead Source")));
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->form->TextField("company_name", 255, array(), $this->requiredTxt . "Source Name", "</td><td>");
            $this->form->TextField("domain", 255, array(), $this->requiredTxt . "Domain", "</td><td>");
            $this->form->TextField("phone", 255, array("class" => "phone"), "Phone", "</td><td>");
            $this->input["email_to"] = $email_to;
            $this->form->TextField("email_to", 255, array(), $this->requiredTxt . "Address To", "</td><td>");

            $this->form->TextField("email_forward", 30, array(), "", "");
            $this->form->helperSendCopy("is_send_copy");

            $this->form->CheckBox("exclude_from_auto_quote", array(), "&nbsp;&nbsp;Exclude from autoquote Email", "") . "";

            $ch_arr = array();
            $batches = array();
            $ords = array();

            if (isset($_POST['assigns'])) {
                foreach ($_POST['assigns'] as $key => $value) {
                    if (trim($value) != "") {
                        $ch_arr[] = (int) $value;
                        $batches[$value] = $_POST['batches'][$value];
                        $ords[$value] = $_POST['ords'][$value];
                    }
                }
            } else {
                if ($ID > 0) {
                    $q = $this->daffny->DB->select("*", "app_defaultsettings_ass", "WHERE owner_id = '" . getParentId() . "' and leadsource_id='" . $ID . "'");
                    while ($row = $this->daffny->DB->fetch_row($q)) {
                        $ch_arr[] = $row['member_id'];
                        $batches[$row['member_id']] = $row['batch'];
                        $ords[$row['member_id']] = $row['ord'];
                    }
                }
            }

            $this->daffny->tpl->assigns = array();
            $sql = "SELECT * FROM members WHERE parent_id = '" . getParentId() . "' AND `is_deleted` <> 1  AND `status` = 'Active' ORDER BY id";
            $q = $this->daffny->DB->query($sql);

            while ($assigns = $this->daffny->DB->fetch_row($q)) {
                if (in_array($assigns['id'], $ch_arr)) {
                    $assigns['ch'] = "checked=\"checked\"";
                } else {
                    $assigns['ch'] = "";
                }
                if (isset($batches[$assigns['id']])) {
                    $assigns['batch'] = $batches[$assigns['id']];
                } else {
                    $assigns['batch'] = 0;
                }
                if (isset($ords[$assigns['id']])) {
                    $assigns['ord'] = $ords[$assigns['id']];
                } else {
                    $assigns['ord'] = 0;
                }
                $this->daffny->tpl->assigns[] = $assigns;
            }
        } catch (FDException $e) {
            redirect(getLink('leadsources'));
        }
    }

    /**
     * Functionality to access leads source affiliate portal account
     *
     * @author Shahrukh
     * @version 1.0
     */
    public function accessAccount()
    {
        $ID = (int) get_var("id");
        $leadsource = new Leadsource($this->daffny->DB);
        $this->tplname = "accounts.leadsources.accountAccess";
        $this->title .= ($ID > 0 ? " - Edit Lead Source" : " - Add New Lead Source");
        $this->check_access("leadsources", "edit", array("id" => $ID));
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink("leadsources") => "Lead Sources",
                getLink("leadsources", "details", "id", $ID) => "Access Account",
            )
        );

        /** When form is submitted! */
        if (isset($_POST['submit'])) {

            /** validating fields */
            $this->isEmpty("username", "Username");
            $this->isEmpty("commision", "Commision");
            $this->isEmpty("first_name", "First Name");
            $this->isEmpty("last_name", "Last Name");
            $this->isEmpty("cost", "Cost");
            $this->isEmpty("mobile", "Phone");
            $this->checkEmail("email", "Email");
            $this->isEmpty("password", "Password");
            $this->isEmpty("c_password", "Confirm Password");

            $_POST['username'] = str_replace(' ', '_', $_POST['username']);

            /** check existing username */
            $sql = "SELECT count(username) as `exists` FROM app_leadsources WHERE username = '" . $_POST['username'] . "' AND `id` != " . $_GET['id'];
            $q = $this->daffny->DB->query($sql);
            $r = $this->daffny->DB->fetch_row($q);

            if ($r['exists'] != 0) {
                $this->err[] = "Username already in use! Try using some other.";
            }

            if ($_POST['password'] != $_POST['c_password']) {
                $this->err[] = "Password & Confirm Password <strong>do not match</strong>.";
            }

            /** when no error */
            if (!count($this->err)) {
                if ($ID > 0) {
                    $sql_arr = array(
                        "username" => $_POST['username'],
                        "commision" => $_POST['commision'],
                        "cost" => $_POST['cost'],
                        "first_name" => $_POST['first_name'],
                        "last_name" => $_POST['last_name'],
                        "mobile" => $_POST['mobile'],
                        "email" => $_POST['email'],
                        "password" => md5($_POST['password']),
                        "weekly_report" => $_POST['weekly_report'],
                    );

                    $leadsource->update($sql_arr, $ID);
                    $this->setFlashInfo("Lead Source has been updated.");
                } else {
                    $this->setFlashError("Unable to update Lead Source, Try again later!");
                }
            }
        }

        /** loading leads data */
        $leadsource->load($ID);

        $this->input["username"] = $leadsource->username;
        $this->input["commision"] = $leadsource->commision;
        $this->input["cost"] = $leadsource->cost;
        $this->input["first_name"] = $leadsource->first_name;
        $this->input["last_name"] = $leadsource->last_name;
        $this->input["mobile"] = $leadsource->mobile;
        $this->input["email"] = $leadsource->email;
        $this->input["password"] = $leadsource->password;
        $this->input["c_password"] = $leadsource->password;

        if ($leadsource->weekly_report == 1) {
            $report = array("checked" => "checked");
        } else {
            $report = array();
        }

        $this->form->TextField("username", 255, array(), $this->requiredTxt . "Username", "</td><td>");
        $this->form->TextField("commision", 255, array(), $this->requiredTxt . "Commision", "</td><td>");
        $this->form->TextField("cost", 255, array(), $this->requiredTxt . "Cost", "</td><td>");
        $this->form->TextField("first_name", 255, array(), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("last_name", 255, array(), $this->requiredTxt . "Last Name", "</td><td>");
        $this->form->TextField("mobile", 255, array(), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("email", 255, array(), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("password", 255, array(), $this->requiredTxt . "Password", "</td><td>");
        $this->form->TextField("c_password", 255, array(), $this->requiredTxt . "Confirm Pasword", "</td><td>");
        $this->form->CheckBox("weekly_report", $report, "Send weekly performance report", "&nbsp;");
    }

    private function createMailBox($id)
    {
        try {
            $member = new Member($this->daffny->DB);
            $member->load($id);
            $companyProfile = $member->getCompanyProfile();
            $companyabbr = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $companyProfile->companyname));
            $email = strtolower(preg_replace("/[^a-zA-Z0-9]/", "", $member->username)) . "." . $companyabbr . "@" . $this->daffny->cfg['MAILDOMAIN'];

            $account = $this->daffny->cfg['cPanel_usr'];
            $account_pass = $this->daffny->cfg['cPanel_pwd'];
            $ip = $this->daffny->cfg['MAILDOMAIN'];

            $emailData = explode('@', $email);

            $email_user = $emailData[0];
            $email_password = $this->daffny->cfg['MAILPWD'];
            $email_domain = $this->daffny->cfg['MAILDOMAIN'];
            $email_quota = "1";

            $xmlApi = new xmlapi($ip, $account, $account_pass);
            $xmlApi->set_output('json');
            $xmlApi->set_debug(0);
            $xmlApi->set_port(2083);
            $xmlApi->api1_query($account, "Email", "addpop", array($email_user, $email_password, $email_quota, $email_domain));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function details()
    {
        try {
            $ID = (int) get_var("id");
            $this->tplname = "accounts.leadsources.details";
            $this->title = "leadsource Details";
            $this->check_access("leadsources", "edit", array("id" => $ID));

            $leadsource = new Leadsource($this->daffny->DB);
            $leadsource->load($ID);
            if ($leadsource->owner_id != getParentId()) {
                $this->setFlashError("Access denied.");
                redirect(getLink('leadsources'));
            }
            $this->input = $leadsource->getAttributes();
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leadsources") => "Lead Sources", getLink("leadsources", "edit", "id", $ID) => htmlspecialchars($leadsource->company_name), '' => "Lead Source Details"));
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            if (trim($this->input['email_to']) != "") {
                $this->input['email_to'] = "<a href=\"mailto:" . $this->input['email_to'] . "\">" . $this->input['email_to'] . "</a>";
            }
            $this->input['email_forward'] = "<a href=\"mailto:" . $leadsource->cron_email . "_" . $leadsource->id . "_" . $leadsource->owner_id . "@" . $this->daffny->cfg['MAILDOMAIN'] . "\">" . $leadsource->cron_email . "_" . $leadsource->id . "_" . $leadsource->owner_id . "@" . $this->daffny->cfg['MAILDOMAIN'] . "</a>";
            
            //build assigns
            $ch_arr = array();
            $batches = array();
            $ords = array();

            if ($ID > 0) {
                $q = $this->daffny->DB->select("*", "app_defaultsettings_ass", "WHERE owner_id = '" . getParentId() . "' and leadsource_id='" . $ID . "'");
                while ($row = $this->daffny->DB->fetch_row($q)) {
                    $ch_arr[] = $row['member_id'];
                    $batches[$row['member_id']] = $row['batch'];
                    $ords[$row['member_id']] = $row['ord'];
                }
            }
            $this->daffny->tpl->assigns = array();
            $sql = "SELECT * FROM members WHERE parent_id = '" . getParentId() . "' AND `is_deleted` <> 1 ORDER BY id";
            $q = $this->daffny->DB->query($sql);
            while ($assigns = $this->daffny->DB->fetch_row($q)) {

                if (in_array($assigns['id'], $ch_arr)) {
                    $assigns['ch'] = "checked=\"checked\"";
                    if (isset($batches[$assigns['id']])) {
                        $assigns['batch'] = $batches[$assigns['id']];
                    }
                    if (isset($ords[$assigns['id']])) {
                        $assigns['ord'] = $ords[$assigns['id']];
                    }
                    $this->daffny->tpl->assigns[] = $assigns;
                }
            }
        } catch (FDException $e) {
            redirect(getLink('leadsources'));
        }
    }

    public function original()
    {
        try {
            $ID = (int) get_var("id");
            $this->tplname = "accounts.leadsources.original";
            $this->title = "Submit Original Email";
            $this->check_access("leadsources", "edit", array("id" => $ID));

            $leadsource = new Leadsource($this->daffny->DB);
            $leadsource->load($ID);

            if ($leadsource->owner_id != getParentId()) {
                $this->setFlashError("Access denied.");
                redirect(getLink('leadsources'));
            }

            //Submit
            if (isset($_POST['submit'])) {
                $this->isEmpty("original_email", "Original Email");
                if (!count($this->err)) {
                    $sql_arr = array("original_email" => post_var("original_email"));
                    $leadsource->update($sql_arr, $ID);

                    if ($this->dbError()) {
                        return;
                    }
                    $this->setFlashInfo("Original Email has been submited.");
                    $leadsource->load($ID);

                    $profile = new CompanyProfile($this->daffny->DB);
                    $profile->getByOwnerId(getParentId());

                    $eml_arr = array(
                        "company_name" => $leadsource->company_name
                        , "phone" => $leadsource->phone
                        , "domain" => $leadsource->domain
                        , "original_email" => nl2br($leadsource->original_email)
                        , "customer" => htmlspecialchars($profile->companyname),
                    );
                    $snd = $this->sendEmail("Admin", $this->daffny->cfg['info_email'], "Lead Source Request", "newleadsource", $eml_arr);
                    $this->daffny->tpl->path = ROOT_PATH . "application/templates/";

                    redirect(getLink("leadsources", "details", "id", $ID));
                }
            }

            $this->input = $leadsource->getAttributes();
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leadsources") => "Lead Sources", getLink("leadsources", "details", "id", $ID) => htmlspecialchars($leadsource->domain), '' => "Submit Original Email"));

            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->form->TextArea("original_email", 15, 30, array("style" => "height:250px; width:500px;"), "Copy the text of any email you have received from them and paste it into the box below.", "<br />");
        } catch (FDException $e) {
            redirect(getLink('leadsources'));
        }
    }

    public function delete()
    {
        $out = array('success' => false);
        try {
            $ID = $this->checkId();
            $this->check_access("leadsources", "delete", array("id" => $ID));
            $leadsource = new Leadsource($this->daffny->DB);
            $leadsource->load($ID);

            $cron_email_prefix = $leadsource->cron_email;
            $owner = $leadsource->owner_id;

            $leadsource->delete($ID, true);

            //delete cron and email from cPanel
            $cPanel = new ServerHandler(
                $this->daffny->cfg['cPanel_usr'], $this->daffny->cfg['cPanel_pwd'], $this->daffny->cfg['cPanel_id'], $this->daffny->cfg['MAILDOMAIN']
            );

            $email_to_delete = $cron_email_prefix . "_" . $ID . "_" . $owner . "@" . $this->daffny->cfg['MAILDOMAIN'];

            $cPanel->delete_cronjob($ID);
            $cPanel->delete_email($email_to_delete);

            $out = array('success' => true);
        } catch (FDException $e) {

        }
        die(json_encode($out));
    }

    public function requests()
    {
        $this->tplname = "accounts.leadsources.requests";
        $this->title .= " - Requests";
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('leadsources') => 'Lead Sources', '' => "Requests"));
            $leadsourceManager = new LeadsourceManager($this->daffny->DB);
            $this->applyOrder("app_leadsources");
            $leadsources = $leadsourceManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId() . " AND status = 0");
            $this->setPager($leadsourceManager->getPager());
            $this->daffny->tpl->leadsources = $leadsources;
        } catch (FDException $e) {
            redirect(SITE_IN);
        }
    }

    public function assigneduser()
    {

        $this->tplname = "accounts.leadsources.assigneduser";
        $this->title = " Assign User";

        try {
            if (isset($_POST['submit'])) {
                if (isset($_POST['assigns'])) {
                    $this->daffny->DB->delete("app_leadsources_assign", "leadsource_id='" . $_POST['leadsource_id'] . "'");
                    foreach ($_POST['assigns'] as $key => $value) {
                        if (trim($value) != "") {
                            $arr = array("leadsource_id" => $_POST['leadsource_id']
                                , "assigned_id" => (int) $value
                                , "orderseq" => $_POST['ords'][(int) $value]
                                , "batch" => $_POST['batches'][(int) $value],
                            );
                            $this->daffny->DB->insert("app_leadsources_assign", $arr);
                        }
                    }
                }
            }

            $this->form->ComboBox("leadsource_id", $this->getLeadSourceSelector("--Select one--"), array(), "Leadsources", "</td><td>");
            $this->form->ComboBox("assign_type", array("single" => "Single User", "distribute" => "Distribute"), array("style" => "width:113px;"), "Choose algorithm", "</td><td>");
            $this->form->ComboBox("assign_leads_id", $this->getUserSelector("--Select one--"), array(), "Assign to", "</td><td>");

            //build assigns
            $ch_arr = array();
            $batches = array();
            $ords = array();
            if (isset($_POST['assigns'])) {
                foreach ($_POST['assigns'] as $key => $value) {
                    if (trim($value) != "") {
                        $ch_arr[] = (int) $value;
                        $batches[$value] = $_POST['batches'][$value];
                        $ords[$value] = $_POST['ords'][$value];
                    }
                }
            } else {
                $q = $this->daffny->DB->select("*", "app_defaultsettings_ass", "WHERE owner_id = '" . getParentId() . "'");
                while ($row = $this->daffny->DB->fetch_row($q)) {
                    $ch_arr[] = $row['member_id'];
                    $batches[$row['member_id']] = $row['batch'];
                    $ords[$row['member_id']] = $row['ord'];
                }
            }
            $this->daffny->tpl->assigns = array();
            $sql = "SELECT * FROM members WHERE parent_id = '" . getParentId() . "' AND `is_deleted` <> 1 ORDER BY id";
            $q = $this->daffny->DB->query($sql);
            while ($assigns = $this->daffny->DB->fetch_row($q)) {

                if (in_array($assigns['id'], $ch_arr)) {
                    $assigns['ch'] = "checked=\"checked\"";
                } else {
                    $assigns['ch'] = "";
                }

                if (isset($batches[$assigns['id']])) {
                    $assigns['batch'] = $batches[$assigns['id']];
                } else {
                    $assigns['batch'] = 0;
                }

                if (isset($ords[$assigns['id']])) {
                    $assigns['ord'] = $ords[$assigns['id']];
                } else {
                    $assigns['ord'] = 0;
                }
                $this->daffny->tpl->assigns[] = $assigns;
            }
        } catch (FDException $e) {
            redirect(getLink('leadsources'));
        }
    }

    protected function getUserSelector($empty = "")
    {
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

    protected function getLeadSourceSelector($empty = "")
    {
        $users = array();
        if ($empty != "") {
            $users = array("" => $empty);
        }
        $sqlu = "SELECT m.id , m.company_name FROM app_leadsources m WHERE  m.owner_id='" . getParentID() . "'";
        $uq = $this->daffny->DB->query($sqlu);
        while ($rowu = $this->daffny->DB->fetch_row($uq)) {
            $users[$rowu['id']] = $rowu['company_name'];
        }
        return $users;
    }

}
