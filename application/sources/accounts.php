<?php

require_once DAFFNY_PATH . "libs/upload.php";

class Applicationaccounts extends ApplicationAction
{
    public $title = "Accounts";
    public $section = "Accounts";
    public $tplname = "accounts.accounts.list";

    public function construct()
    {

        if ($_GET['accounts'] != "shippers" &&
            $_GET['accounts'] != "edit" &&
            $_GET['accounts'] != "details" &&
            $_GET['accounts'] != "accounthistory" &&
            $_GET['accounts'] != "uploads" &&
            !$this->check_access('accounts')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        $this->loadAccountPage('all');
    }

    public function import()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink('accounts') => "Accounts",
                getLink('accounts/carriers') => 'Carriers',
                'Import',
            ));
            $this->tplname = "accounts.accounts.import";
            $this->title .= " - Import Account";
            if (count($_POST) > 0) {
                $this->isEmpty("company_name", "Company Name");
                $this->isEmpty("status", "Status");
                $this->isEmpty("member_id", "Carrier Account");
                if (post_var('email') != "") {
                    $this->checkEmail('email', "Email");
                }
                if (!count($this->err)) {
                    $insert_arr = $this->getInsertArray();
                    $insert_arr['is_carrier'] = 1;
                    $insert_arr['owner_id'] = getParentId();
                    $insert_arr['member_id'] = post_var('member_id');
                    $account = new Account($this->daffny->DB);
                    $account->create($insert_arr);
                    if (!$this->daffny->DB->isError) {
                        $this->setFlashInfo("Account has been added.");
                    } else {
                        $this->setFlashError("Can't create account. Try again later, please.");
                    }
                }
            }
            $this->form->TextField("company_name", 255, array("readonly" => "readonly"), "Company Name", "</td><td>");
            $this->form->TextField("contact_name", 255, array("readonly" => "readonly"), "Contact Name", "</td><td>");
            $this->form->ComboBox("status", Account::$status_name, array('style' => ""), $this->requiredTxt . "Status", "</td><td>");
            $this->form->TextArea("notes", 15, 10, array("style" => "height:100px; width:470px;"), "Notes", "</td><td>");
            $this->form->TextField("address1", 255, array("readonly" => "readonly"), "Address 1", "</td><td>");
            $this->form->TextField("address2", 255, array("readonly" => "readonly"), "Address 2", "</td><td>");
            $this->form->TextField("city", 255, array("readonly" => "readonly"), "City", "</td><td>");
            $this->form->TextField("state", 50, array("readonly" => "readonly"), "State", "</td><td>");
            $this->form->TextField("zip", 10, array("readonly" => "readonly"), "Zip/Postal Code", "</td><td>");
            $this->form->TextField("country", 255, array("readonly" => "readonly"), "Country", "</td><td>");
            $this->form->TextField("phone1", 100, array("readonly" => "readonly"), "Phone 1", "</td><td>");
            $this->form->TextField("phone2", 100, array("readonly" => "readonly"), "Phone 2", "</td><td>");
            $this->form->TextField("cell", 100, array("readonly" => "readonly"), "Cell Phone", "</td><td>");
            $this->form->TextField("fax", 100, array("readonly" => "readonly"), "Fax", "</td><td>");
            $this->form->TextField("email", 255, array("readonly" => "readonly"), "Email", "</td><td>");
            $this->form->CheckBox("donot_dispatch", array(), "Ban Carrier/Shipper/Location", "</td><td>");

            $this->form->TextField("insurance_companyname", 255, array(), "Name", "</td><td>");
            $this->form->TextField("insurance_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("insurance_phone", 255, array(), "Company Phone", "</td><td>");
            $this->form->ComboBox("insurance_holder", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Certificate Holder", "</td><td>");
            $this->form->ComboBox("insurance_insured", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Additionally Insured", "</td><td>");
            $this->form->TextField("insurance_agentname", 255, array(), "Agent Name", "</td><td>");
            $this->form->TextField("insurance_agentphone", 255, array(), "Agent Phone", "</td><td>");
            $this->form->TextField("insurance_policynumber", 30, array(), "Policy Number", "</td><td>");
            $this->form->TextField("insurance_expirationdate", 255, array("style" => ""), "Expiration Date", "</td><td>");
            $this->form->ComboBox("insurance_contract", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Broker/Carrier Contract", "</td><td>");
            $this->form->TextField("insurance_iccmcnumber", 30, array(), $this->requiredTxt . "ICC MC Number", "</td><td>");
        } catch (FDException $e) {
            redirect(getLink('accounts'));
        }
    }

    public function carriers()
    {
        $this->importAccounts('carrier');
        $this->loadAccountPage('carrier');
    }

    public function locations()
    {
        $this->importAccounts('location');
        $this->loadAccountPage('location');
    }

    public function shippers()
    {
        $this->importAccounts('shipper');
        $this->loadAccountPageNewShipper('shipper');
    }
    public function carriersnew()
    {
        $this->importAccounts('carrier');
        $this->loadAccountPageNew('carrier');
    }

    public function locationsnew()
    {
        $this->importAccounts('location');
        $this->loadAccountPageNew('location');
    }

    public function shippersnew()
    {
        $this->importAccounts('shipper');
        $this->loadAccountPage('shipper');
    }

    protected function importAccounts($type)
    {
        if (count($_FILES)) {
            $upload = new upload();
            $upload->out_file_dir = UPLOADS_PATH . "temp/";
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
                        switch ($type) {
                            case 'carrier':
                                $result = $import->importCarriers($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
                                break;
                            case 'location':
                                $result = $import->importLocations($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
                                break;
                            case 'shipper':
                                $result = $import->importShippers($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
                                break;
                            default:
                                die('ERROR: Invalid import type');
                                break;
                        }
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

    public function inactive()
    {
        $this->loadAccountPage('inactive');
    }

    private function loadAccountPage($type)
    {
        try {
            $this->tplname = "accounts.accounts.list";

            $accountManager = new AccountManager($this->daffny->DB);

            $this->applyOrder("app_accounts");
            $where = "";
            $active_inactive = "";
            
            // Chetu added this code to check privilege of user
            // for diffrent accounts and show data accordingly
            $tempWhere = [];
            if ($_SESSION['member']['access_carriers'] != 0) {
                $tempWhere[] = ' ( is_carrier=1 ) ';
            }

            if ($_SESSION['member']['access_locations'] != 0) {
                $tempWhere[] = ' ( is_location = 1 ) ';
            }

            switch ($_SESSION['member']['access_shippers']) {
                case 0:
                    $tempWhere[] = ' is_shipper != 1';
                    break;
                case 1:
                    $tempWhere[] = '(is_shipper = 1 AND owner_id = "'
                        . $_SESSION['member']['id'] . '")';
                    break;
            }
            if (count($tempWhere) > 0) {
                $where .= ' AND (' . implode(' OR ', $tempWhere) . ')';
            }

            switch ($type) {
                case 'all':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts"));
                    break;
                case 'carrier':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Carriers'));
                    $active_inactive = " AND is_carrier=1 ";
                    $where .= " AND is_carrier=1 ";
                    break;
                case 'location':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Locations'));
                    $active_inactive = " AND is_location=1 ";
                    $where .= " AND is_location=1 ";
                    break;
                case 'shipper':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));
                    if (isset($_GET['shipper_type']) && $_GET['shipper_type'] != "" && $_GET['shipper_type'] != "all") {
                        $where .= " AND shipper_type='" . $_GET['shipper_type'] . "' ";
                    }

                    $where .= " AND is_shipper=1 ";

                    $active_inactive = " AND is_shipper=1 ";
                    break;
                case 'inactive':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
                    $where .= " AND status=" . Account::STATUS_INACTIVE;
                    break;

            }
            if (isset($_POST['searchval'])) {
                $searchVal = trim(post_var('searchval'));
                $where .= " AND (company_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR first_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR last_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR email LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%') ";
                $this->input['searchval'] = $searchVal;
            }
            $this->form->TextField("searchval", 50);

            $owner_id = " WHERE `owner_id` IN (" . implode(', ', Member::getCompanyMembers($this->daffny->DB, $_SESSION['member']['parent_id'])) . ")";

            $accounts = $accountManager->get($this->order->getOrder(), $_SESSION['per_page'], $owner_id . " " . $where);

            $this->setPager($accountManager->getPager());
            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;

            if (isset($_GET['shipper_type'])) {
                $this->input['shipper_type'] = $_GET['shipper_type'];
            }

            $this->form->ComboBox('shipper_type',
                array('all' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", 'onChange' => 'filterAll();'), 'Filter ', '');

            $accountsActive = $accountManager->getActive($owner_id . " " . $active_inactive);
            $this->daffny->tpl->accountsActive = $accountsActive;

        } catch (FDException $e) {
            echo $this->daffny->DB->errorQuery;die;
            $this->setFlashError("Cannot search this shipper ! ");
            redirect(SITE_IN);
        }
    }

    private function loadAccountPageNewShipper($type)
    {

        try {
            $this->tplname = "accounts.accounts.list_new";
            $data_tpl = "accounts.accounts.list_new";
            $accountManager = new AccountManager($this->daffny->DB);

            $this->applyOrder("`app_accounts`");
            $this->order->Fields[] = 'A.company_name';
            $this->order->Fields[] = 'A.status';
            $this->order->Fields[] = 'A.first_name';
            $this->order->Fields[] = 'A.last_name';
            $this->order->Fields[] = 'A.referred_by';
            $this->order->Fields[] = 'A.contact_name1';
            $this->order->Fields[] = 'M.contactname';

            $where = "";
            $active_inactive = "";
            switch ($type) {

                case 'shipper':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));
                    if (isset($_GET['shipper_type']) && $_GET['shipper_type'] != "" && $_GET['shipper_type'] != "all") {
                        $where .= " AND A.shipper_type='" . $_GET['shipper_type'] . "' ";
                    }

                    if (isset($_GET['salesman']) && $_GET['salesman'] != "" && $_GET['salesman'] != "all") {
                        $where .= " AND B.members_id='" . $_GET['salesman'] . "' ";
                    }

                    $where .= " AND A.is_shipper=1 ";

                    $active_inactive = " AND is_shipper=1 ";
                    break;
                case 'inactive':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
                    $where .= " AND status=" . Account::STATUS_INACTIVE;
                    break;

            }
            if (isset($_POST['searchval'])) {
                $searchVal = trim(post_var('searchval'));
                $where .= " AND (A.company_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR A.first_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR A.last_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%') ";
                $this->input['searchval'] = $searchVal;
            }
            $this->form->TextField("searchval", 50);

            if ($_SESSION['member_id'] != getParentId()) {
                $additionalCond = $this->getSippersPrivilegedFilter();
            }

            if ($type == 'shipper' || $type == 'carrier' || $type == 'location') {
                $accounts = $accountManager->getShippersAccount($this->order->getOrder(), $_SESSION['per_page'], " A.`owner_id` IN (" . implode(",", Member::getCompanyMembers($this->daffny->DB, $_SESSION['member']['parent_id'])) . ") " . $additionalCond . $where, "`owner_id` = " . getParentId() . " " . $active_inactive);
            } else {
                $accounts = $accountManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId() . " " . $where);
            }

            $accountsActive = $accountManager->getActiveShippers(" A.`owner_id` IN (" . implode(",", Member::getCompanyMembers($this->daffny->DB, $_SESSION['member']['parent_id'])) . ") " . $additionalCond . $where);
            $this->daffny->tpl->accountsActive = $accountsActive;

            $this->setPager($accountManager->getPager());
            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;

            if (isset($_GET['shipper_type'])) {
                $this->input['shipper_type'] = $_GET['shipper_type'];
            }

            $this->form->ComboBox('shipper_type',
                array('all' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", 'onChange' => 'filterAll();'), 'Filter ', '');

        } catch (FDException $e) {
        }
    }

    /**
     * This function will return where condition based on user have privileges
     * access_shippers session values will be treated as follows
     * 0 for none
     * 1 for view mine / edit mine
     * 2 for view all / edit mine
     * 3 for view all / edit all
     * 4 for view all
     * @return string this will be condition that for shippers privilege
     */
    private function getSippersPrivilegedFilter()
    {
        $whereCond = ' AND';
        switch ($_SESSION['member']['access_shippers']) {
            case 0:
                $whereCond .= ' 1=0';
                break;
            case 1:
                $whereCond .= ' `owner_id` = ' . $_SESSION['member_id'];
                break;
            case 2;
                $whereCond .= ' 1=1';
                break;
            case 3:
                $whereCond .= ' 1=1';
                break;
            case 4:
                $whereCond .= ' 1=1';
                break;
            default:
                $whereCond = '';
                break;
        }
        return $whereCond;
    }

    private function loadAccountPageNew($type)
    {

        try {
            $this->tplname = "accounts.accounts.list_new";
            $data_tpl = "accounts.accounts.list_new";
            $accountManager = new AccountManager($this->daffny->DB);

            $this->applyOrder("`app_accounts`");

            $where = "";
            $active_inactive = "";
            switch ($type) {
                case 'all':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts"));
                    break;
                case 'carrier':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Carriers'));
                    $active_inactive = " AND is_carrier=1 ";
                    $where .= " AND B.is_carrier=1 ";
                    break;
                case 'location':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Locations'));
                    $active_inactive = " AND is_location=1 ";
                    $where .= " AND B.is_location=1 ";
                    break;
                case 'shipper':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));
                    if (isset($_GET['shipper_type']) && $_GET['shipper_type'] != "" && $_GET['shipper_type'] != "all") {
                        $where .= " AND B.shipper_type='" . $_GET['shipper_type'] . "' ";
                    }

                    $where .= " AND B.is_shipper=1 ";

                    $active_inactive = " AND is_shipper=1 ";
                    break;
                case 'inactive':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
                    $where .= " AND status=" . Account::STATUS_INACTIVE;
                    break;

            }
            if (isset($_POST['searchval'])) {
                $searchVal = trim(post_var('searchval'));
                $where .= " AND (company_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR first_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%' ";
                $where .= " OR last_name LIKE '%" . mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal) . "%') ";
                $this->input['searchval'] = $searchVal;
            }
            $this->form->TextField("searchval", 50);

            if ($type == 'shipper' || $type == 'carrier' || $type == 'location') {
                $accounts = $accountManager->getAccount($this->order->getOrder(), $_SESSION['per_page'], "AND B.`owner_id` = " . getParentId() . " " . $where, "`owner_id` = " . getParentId() . " " . $active_inactive);
            } else {
                $accounts = $accountManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId() . " " . $where);
            }

            $accountsActive = $accountManager->getActive("`owner_id` = " . getParentId() . " " . $active_inactive);
            $this->daffny->tpl->accountsActive = $accountsActive;

            $this->setPager($accountManager->getPager());
            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;

            if (isset($_GET['shipper_type'])) {
                $this->input['shipper_type'] = $_GET['shipper_type'];
            }

            $this->form->ComboBox('shipper_type',
                array('all' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", 'onChange' => 'filterAll();'), 'Filter ', '');

        } catch (FDException $e) {
            redirect(SITE_IN);
        }
    }

    public function edit()
    {
        try {

            $ID = (int) get_var("id");
            $account = new Account($this->daffny->DB);
            $this->tplname = "accounts.accounts.form";
            $this->title .= ($ID > 0 ? " - Edit Account" : " - Add New Account");

            $this->check_access("accounts", "edit", array("id" => $ID));

            if ($ID > 0) {
                $account->load($ID);
            }

            $sql_arr = $this->getInsertArray();

            if ($sql_arr['country'] == "US") {
                $sql_arr['state_other'] = "";
            } else {
                $sql_arr['state'] = "";
            }

            if ($sql_arr['insurance_expirationdate'] != "") {
                $sql_arr['insurance_expirationdate'] = $this->validateDate($sql_arr['insurance_expirationdate'], "Expiration Date");
            }

            $this->input = $sql_arr;
            if (isset($_POST['submit'])) {

                if ($sql_arr['is_shipper'] == 0) {
                    $this->isEmpty("company_name", "Company Name");
                }

                if ($sql_arr['is_shipper'] == 1) {
                    $this->isEmpty("shipper_type", "Shipper Type");
                }

                if ($sql_arr['is_shipper'] == 1) {
                    if ($sql_arr['shipper_type'] == "Commercial" && trim($sql_arr['hours_of_operation']) == "") {
                        $this->err[] = "Hours of operation is mandatory for Commercial shippers";
                    }
                }

                if ($sql_arr['is_carrier'] == 1) {
                    $this->isEmpty("carrier_type", "Carrier Type");
                    $this->isEmpty("insurance_iccmcnumber", "ICC MC Number and DLT");
                }

                $this->isEmpty("status", "Status");

                if ($sql_arr['is_carrier'] == 0 && $sql_arr['is_shipper'] == 0 && $sql_arr['is_location'] == 0) {
                    $this->err[] = "Please choose account <strong>'Type'</strong>";
                }

                $this->checkEmail("email", "Email");
                if ($sql_arr['is_location'] == 1) {
                    $this->isEmpty("city", "City");
                    $this->isEmpty("location_type", "Location Type");

                    if (post_var('country') == "US") {
                        $this->isEmpty("state", "State");
                    } else {
                        $this->isEmpty("state_other", "State/Province");
                    }
                    $this->isEmpty("zip_code", "Zip/Postal Code");
                    $this->isEmpty("country", "Country");
                }

                if (!count($this->err)) {
                    if ($ID > 0) {

                        // when image is posted
                        if($_FILES['cp_login_banner']['name']){
                            $_FILES['cp_login_banner']['name'] = date('Ymdhis')."-".$_FILES['cp_login_banner']['name'];
                            $target_path = "/home/gecko/public_html/uploads/CP_Login_Banners/";  
                            $target_path = $target_path.basename( $_FILES['cp_login_banner']['name']);   
                            move_uploaded_file($_FILES['cp_login_banner']['tmp_name'], $target_path);
                            $sql_arr['login_banner'] = $_FILES['cp_login_banner']['name'];
                        }

                        $this->updateHistory($sql_arr, $ID);
                        $referred_id_old = $account->referred_id;
                        $account->update($sql_arr, $ID);
                        
                        if ($sql_arr['referred_id'] != $referred_id_old) {

                            $member = new Member($this->daffny->DB);
                            $member->load($account->owner_id);
                            $where .= " AND ae.`assigned_id` IN (" . implode(', ', $member->getCompanyMembers($this->daffny->DB, $member->getParent()->id)) . ")";

                            $query = " SELECT ac.id AS account_id, ae.id AS entity_id, ash.id AS shipper_id, ac.referred_id, ac.referred_by
                                    FROM  `app_accounts` AS ac
                                    INNER JOIN app_shippers AS ash
                                    ON ash.company = ac.company_name
                                    INNER JOIN app_entities AS ae
                                    ON ae.shipper_id=ash.id AND ae.type=3 " . $where . "
                                    WHERE ac.`is_shipper` =1
                                    AND ac.`referred_id` !=0
                                    AND ac.id='" . $account->id . "'";

                            $result = $this->daffny->DB->query($query);
                            if ($result) {
                                while ($row = $this->daffny->DB->fetch_row($result)) {

                                    $entity = new Entity($this->daffny->DB);
                                    $entity->load($row['entity_id']);

                                    $update_arr = array(
                                        'referred_by' => $row['referred_by'],
                                        'referred_id' => $row['referred_id'],
                                    );
                                    $entity->update($update_arr);
                                }
                            }
                        }
                        $this->setFlashInfo("Account has been updated.");
                    } else {

                        $_FILES['cp_login_banner']['name'] = date('Ymdhis')."-".$_FILES['cp_login_banner']['name'];
                        $target_path = "/home/gecko/public_html/uploads/CP_Login_Banners/";  
                        $target_path = $target_path.basename( $_FILES['cp_login_banner']['name']);   
                        move_uploaded_file($_FILES['cp_login_banner']['tmp_name'], $target_path);

                        $sql_arr['login_banner'] = $_FILES['cp_login_banner']['name'];

                        $sql_arr['owner_id'] = getParentId();
                        $account->create($sql_arr);
                        if ($_POST['insurance_type'] != '') {
                            $this->upload_insurance_file("carrier_ins_doc", $account, $_POST['insurance_type']);
                        }

                        $this->setFlashInfo("Account has been added.");
                        $ID = $account->id;

                    }
                    if ($this->dbError()) {
                        return;
                    }
                    redirect(getLink("accounts", "details", "id", $ID));
                }
            } else {
                if ($ID > 0) {
                    $this->input = $account->getAttributes();
                    $this->daffny->tpl->login_banner = $account->getAttributes()['login_banner'];
                }
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink("accounts") => "Accounts",
                '' => ($ID > 0 ? htmlspecialchars($this->input['company_name']) : "Add New Account"),
            ));

            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }

            $this->daffny->tpl->accountType = $this->input['type'];

            $sql = "SELECT ac.id as id,
                            ac.reffered_by as shipper_id,
                            ac.reffered_by as members_id,
                            r.name as reffered_by,
                            ac.commision as commision,
                            ac.create_date as create_date,
                            m.contactname as contactname,
                            aa.company_name as company_name,
                            ac.primary_entry
                    FROM app_commision ac
                    LEFT JOIN app_accounts aa ON ac.shipper_id = aa.id
                    LEFT JOIN members m ON ac.members_id = m.id
                    LEFT JOIN app_referrers as r ON r.id = ac.reffered_by
                    WHERE ac.is_deleted=0 and shipper_id='" . $ID . "'
                    ORDER BY ac.id desc";

            $rows = $this->daffny->DB->selectRows($sql);

            if (!is_array($rows) || $this->daffny->DB->isError) {
                throw new FDException("DB query error");
            }

            if (count($rows) > 0) {
                $commissionData = array();
                foreach ($rows as $row) {
                    $tempArr = array();
                    $tempArr['id'] = $row['id'];
                    $tempArr['shipper_id'] = $row['shipper_id'];
                    $tempArr['members_id'] = $row['members_id'];
                    $tempArr['reffered_by'] = $row['reffered_by'];
                    $tempArr['commision'] = $row['commision'];
                    $tempArr['create_date'] = $row['create_date'];
                    $tempArr['contactname'] = $row['contactname'];
                    $tempArr['company_name'] = $row['company_name'];
                    $tempArr['primary'] = $row['primary_entry'];
                    $commissionData[] = $tempArr;
                }
                $this->daffny->tpl->commissionData = $commissionData;
            }
            if ($ID > 0) {
                if ($account->insurance_doc_id > 0) {
                    $this->daffny->tpl->files = $this->getInsuranceCertificate($account->insurance_doc_id);
                }

            }

            $this->input['insurance_expirationdate'] = $this->getFormattedDate($this->input['insurance_expirationdate']);
            $this->form->TextField("company_name", 255, array(), $this->requiredTxtCompany . "Company Name", "</td><td>");
            $this->form->TextArea("notes", 15, 10, array("style" => "height:100px; width:750px;"), "Notes", "</td><td>");
            $this->form->ComboBox("status", Account::$status_name, array('style' => ""), $this->requiredTxt . "Status", "</td><td>");
            $this->form->CheckBox("is_carrier", array(), "Carrier", "&nbsp;");
            $this->form->CheckBox("is_shipper", array(), "Shipper", "&nbsp;");
            $this->form->CheckBox("is_location", array(), "Location", "&nbsp;");
            $this->form->CheckBox("donot_dispatch", array(), "Ban Carrier/Shipper/Location", "&nbsp;");
            $this->form->TextField("rating", 1, array("style" => ""), "Rating", "</td><td>");
            $this->form->TextField("first_name", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("last_name", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("tax_id_num", 30, array(), "Tax ID", "</td><td>");
            $this->form->TextField("contact_name1", 100, array(), "Contact 1", "</td><td>");
            $this->form->TextField("contact_name2", 100, array(), "Contact 2", "</td><td>");
            $this->form->TextField("phone1", 100, array("class" => "phone"), "Phone 1", "</td><td>");
            $this->form->TextField("phone2", 100, array("class" => "phone"), "Phone 2", "</td><td>");
            $this->form->TextField("cell", 100, array("class" => "phone"), "Cell Phone", "</td><td>");
            $this->form->TextField("fax", 100, array("class" => "phone"), "Fax", "</td><td>");
            $this->form->TextField("email", 255, array(), "Email", "</td><td>");
            $this->form->TextField("address1", 255, array(), "Address 1", "</td><td>");
            $this->form->TextField("address2", 255, array(), "Address 2", "</td><td>");
            $this->form->CheckBox("unsubscribe", array(), "Unsubscribe", "&nbsp;&nbsp;");

            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }

            $this->form->ComboBox("referred_id", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55), "Source", "</td><td>");
            $this->form->TextField("city", 255, array("class" => "geo-city"), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "City", "</td><td>");
            $this->form->ComboBox("state", array("" => "Select State") + $this->getStates(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State", "</td><td>");
            $this->form->TextField("state_other", 50, array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State/Province", "</td><td>");
            $this->form->TextField("zip_code", 10, array('style' => 'width:100px'), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Zip/Postal Code", "</td><td>");
            $this->form->ComboBox("country", $this->getCountries(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Country", "</td><td>");
            $this->form->TextField("print_name", 255, array(), "Print on check As", "</td><td>");
            $this->form->CheckBox("print_check", array(), "Use this address for print check", "&nbsp;");
            $this->form->TextField("print_address1", 255, array(), "Address 1", "</td><td>");
            $this->form->TextField("print_address2", 255, array(), "Address 2", "</td><td>");
            $this->form->TextField("print_city", 255, array("class" => "geo-city"), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "City", "</td><td>");
            $this->form->ComboBox("print_state", array("" => "Select State") + $this->getStates(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State", "</td><td>");
            $this->form->TextField("print_state_other", 50, array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State/Province", "</td><td>");
            $this->form->TextField("print_zip_code", 10, array('style' => 'width:100px'), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Zip/Postal Code", "</td><td>");
            $this->form->ComboBox("print_country", $this->getCountries(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Country", "</td><td>");
            $this->form->TextField("insurance_companyname", 255, array(), "Name", "</td><td>");
            $this->form->TextField("insurance_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("insurance_phone", 255, array(), "Company Phone", "</td><td>");
            $this->form->ComboBox("insurance_holder", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Certificate Holder", "</td><td>");
            $this->form->ComboBox("insurance_insured", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Additionally Insured", "</td><td>");
            $this->form->TextField("insurance_agentname", 255, array(), "Agent Name", "</td><td>");
            $this->form->TextField("insurance_agentphone", 255, array(), "Agent Phone", "</td><td>");
            $this->form->TextField("insurance_policynumber", 30, array(), "Policy Number", "</td><td>");
            $this->form->TextField("insurance_expirationdate", 255, array("style" => ""), "Expiration Date", "</td><td>");
            $this->form->ComboBox("insurance_contract", array(
                "" => "--Select one--",
                "1" => "Yes",
                "0" => "No",
            ), array("style" => ""), "Broker/Carrier Contract", "</td><td>");
            $this->form->TextField("insurance_iccmcnumber", 30, array(), $this->requiredTxt . "ICC MC Number", "</td><td>");
            $this->form->TextField("us_dot", 30, array(), $this->requiredTxt . "US Dot", "</td><td>");
            $this->form->FileFiled("insurance_doc", array(), "Upload Doc", "</td><td>");
            $this->form->FileFiled("carrier_ins_doc", array(), "Upload Insurance Doc", "</td><td>");
            $this->form->ComboBox("insurance_type", array(
                "0" => "--Select Insurance--",
                "3" => "Cargo & Liability",
                "1" => "Cargo Insurance",
                "2" => "Liability Insurance",
            ), array("style" => ""), "Insurance Type", "</td><td>");

            $this->form->TextField("hours_of_operation", 255, array(), $this->requiredTxt . "Hours of operation", "</td><td>");
            $this->form->TextField("location_type", 255, array(), $this->requiredTxt . "Location Type", "</td><td>");
            $this->form->ComboBox("carrier_type", array("" => "--Select one--") + Account::$carrier_types, array("style" => "width:221px;"), $this->requiredTxt . "Carrier Type", "</td><td>");
            $this->form->ComboBox("shipper_type", array("" => "--Select one--") + Account::$shipper_types, array("style" => "width:221px;"), $this->requiredTxt . "Shipper Type", "</td><td>");
            $this->form->MoneyField("insurance_liability_amount", 15, array(), "Liability Amount $", "</td><td>");
            $this->form->MoneyField("insurance_coverage", 15, array(), "Insurance Coverage $", "</td><td>");
            $this->form->MoneyField("insurance_cargo_deductible", 15, array(), "Cargo Deductible $", "</td><td>");
        } catch (FDException $e) {
            redirect(getLink('accounts'));
        }
    }

    public function details()
    {
        try {
            $ID = (int) get_var("id");
            $this->tplname = "accounts.accounts.details";
            $this->title = "Account Details";

            $this->check_access("accounts", "view", array("id" => $ID));

            $account = new Account($this->daffny->DB);
            $account->load($ID);

            $attributeArr = $account->getAttributes();
            $this->input = $attributeArr;
            $this->daffny->tpl->accountType = $attributeArr['type'];
            $this->daffny->tpl->isShipper = $attributeArr['is_shipper'];
            $this->daffny->tpl->isCarrier = $attributeArr['is_carrier'];

            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink("accounts") => "Accounts",
                getLink("accounts", "edit", "id", $ID) => htmlspecialchars($account->company_name),
                '' => "Account Details",
            ));
            $this->input['country_name'] = $this->getCountryByCode($this->input['country']);
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->input['insurance_expirationdate'] = $this->getFormattedDate($this->input['insurance_expirationdate']);
            $this->input['insurance_holder'] = $this->getYesNo($this->input['insurance_holder']);
            $this->input['insurance_insured'] = $this->getYesNo($this->input['insurance_insured']);
            $this->input['insurance_contract'] = $this->getYesNo($this->input['insurance_contract']);

            if (trim($this->input['email']) != "") {
                $this->input['email'] = "<a href=\"mailto:" . $this->input['email'] . "\">" . $this->input['email'] . "</a>";
            }

            $sql = "SELECT d.id as id, d.entity_id as entity_id , d.created as created ,
                                d.order_number as order_number ,
                                d.accepted as accepted ,
                                d.hash_link as hash_link ,
                                d.carrier_company_name as carrier_company_name ,
                                d.carrier_contact_name as carrier_contact_name ,
                                d.carrier_phone_1 as carrier_phone_1 ,
                                d.carrier_phone_2 as carrier_phone_2 ,
                                d.carrier_fax as carrier_fax ,
                                d.carrier_email as carrier_email ,
                                d.carrier_driver_name as carrier_driver_name ,
                                d.carrier_driver_phone as carrier_driver_phone
                                FROM app_dispatch_sheets as d
                                WHERE d.accepted IS NOT NULL AND d.rejected is null
                                AND d.`deleted` = 0
                                And d.account_id = " . $ID . " group by d.entity_id order by d.created desc";

            $rows = $this->daffny->DB->selectRows($sql);
            if (!is_array($rows) || $this->daffny->DB->isError) {
                throw new FDException("DB query error");
            }

            if (count($rows) > 0) {
                $commissionData = array();
                foreach ($rows as $row) {
                    $files = $this->getFilesCarrier($row['entity_id']);
                    $tempArr = array();
                    $tempArr['id'] = $row['id'];
                    $tempArr['entity_id'] = $row['entity_id'];
                    $tempArr['created'] = $row['created'];
                    $tempArr['order_number'] = $row['order_number'];
                    $tempArr['accepted'] = $row['accepted'];
                    $tempArr['hash_link'] = $row['hash_link'];
                    $tempArr['carrier_company_name'] = $row['carrier_company_name'];
                    $tempArr['carrier_contact_name'] = $row['carrier_contact_name'];
                    $tempArr['carrier_phone_1'] = $row['carrier_phone_1'];
                    $tempArr['carrier_phone_2'] = $row['carrier_phone_2'];
                    $tempArr['carrier_fax'] = $row['carrier_fax'];
                    $tempArr['carrier_email'] = $row['carrier_email'];
                    $tempArr['carrier_driver_name'] = $row['carrier_driver_name'];
                    $tempArr['carrier_driver_phone'] = $row['carrier_driver_phone'];
                    $entity = new Entity($this->daffny->DB);
                    $entity->load($row['entity_id']);
                    $tempArr['entities'] = $entity;
                    $tempArr['files'] = $files;
                    $commissionData[] = $tempArr;
                }
                $this->daffny->tpl->commissionData = $commissionData;
            }
        } catch (FDException $e) {
            redirect(getLink('accounts'));
        }
    }

    protected function getFilesCarrier($id)
    {
        $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
                 ORDER BY u.date_uploaded desc limit 0,1";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }

    public function accounthistory()
    {
        $ID = (int) get_var("id");
        $this->tplname = "accounts.accounts.accounthistory";
        $this->title .= " - Account History";

        if ($_SESSION['member']['access_shippers'] != 1) {
            $this->check_access("accounts", "show", array('id' => $ID));
        }

        $account = new Account($this->daffny->DB);
        $account->load($ID);
        if ($account->owner_id != getParentId() && $account->is_shipper != 1) {
            $this->setFlashError("Access denied.");
            redirect(getLink('accounts'));
        }
        $this->input = $account->getAttributes();
        $this->breadcrumbs = $this->getBreadCrumbs(array(
            getLink("accounts") => "Accounts",
            getLink("accounts", "details", "id", $ID) => htmlspecialchars($this->input['company_name']),
            '' => "Account History",
        ));

        $this->applyPager("app_accounts_history h", "", "WHERE h.account_id='" . $ID . "'");
        $this->applyOrder("app_accounts_history");

        $sql = "SELECT h.*
                , m.contactname AS changed_by_name
                , f.commonname AS field_name
                , DATE_FORMAT(h.change_date, '%m/%d/%Y %H:%i:%s') change_date
            FROM app_accounts_history h
            LEFT JOIN members m ON h.changed_by = m.id
            LEFT JOIN app_fields f ON f.name = h.field_name AND f.table_name = 'app_accounts'
            WHERE h.account_id='" . $ID . "' " . $this->order->getOrder() . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function delete()
    {
        $out = array('success' => false);
        try {
            $ID = $this->checkId();
            $this->check_access("accounts", "delete", array("id" => $ID));
            $account = new Account($this->daffny->DB);
            $account->delete($ID, true);
            $out = array('success' => true);
        } catch (FDException $e) {
        }
        die(json_encode($out));
    }
 
    public function status()
    {
        $out = array('success' => false);
        try {
            $id = $this->checkId();
            $this->check_access("accounts", "update", array("id" => $id));
            $account = new Account($this->daffny->DB);
            $account->load($id);
            $this->updateHistory(array('status' => ($account->status == 1) ? 0 : 1), $id);
            $account->update(array('status' => ($account->status == 1) ? 0 : 1), $id);
            $out = array('success' => true);
        } catch (FDException $e) {
        }
        die(json_encode($out));
    }

    protected function updateHistory($new_arr, $ID)
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

    protected function getInsertArray()
    {

        $referrer_name_value = "";

        if (post_var("referred_id") != "") {
            $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . post_var("referred_id") . "'");
            if (!empty($row_referrer)) {
                $referrer_name_value = $row_referrer['name'];

            }
        }

        $insert_arr = array(
            "company_name" => post_var("company_name"),
            "status" => (int) post_var("status"),
            "is_carrier" => (post_var("is_carrier") == "1" ? 1 : 0),
            "is_shipper" => (post_var("is_shipper") == "1" ? 1 : 0),
            "is_location" => (post_var("is_location") == "1" ? 1 : 0),
            "first_name" => post_var("first_name"),
            "last_name" => post_var("last_name"),
            "tax_id_num" => post_var("tax_id_num"),
            "notes" => post_var("notes"),
            "contact_name1" => post_var("contact_name1"),
            "contact_name2" => post_var("contact_name2"),
            "phone1" => post_var("phone1"),
            "phone2" => post_var("phone2"),
            "cell" => post_var("cell"),
            "fax" => post_var("fax"),
            "email" => post_var("email"),
            "address1" => post_var("address1"),
            "address2" => post_var("address2"),
            "city" => post_var("city"),
            "state" => post_var("state"),
            "state_other" => post_var("state_other"),
            "zip_code" => post_var("zip_code"),
            "country" => post_var("country"),
            "print_name" => post_var("print_name"),
            "insurance_companyname" => post_var("insurance_companyname"),
            "insurance_address" => post_var("insurance_address"),
            "insurance_phone" => post_var("insurance_phone"),
            "insurance_holder" => post_var("insurance_holder"),
            "insurance_insured" => post_var("insurance_insured"),
            "insurance_agentname" => post_var("insurance_agentname"),
            "insurance_agentphone" => post_var("insurance_agentphone"),
            "insurance_policynumber" => post_var("insurance_policynumber"),
            "insurance_expirationdate" => post_var("insurance_expirationdate"),
            "insurance_contract" => post_var("insurance_contract"),
            "insurance_iccmcnumber" => post_var("insurance_iccmcnumber"),
            "rating" => (int) post_var("rating"),
            "donot_dispatch" => (post_var("donot_dispatch") == "1" ? 1 : 0),
            "carrier_type" => post_var("carrier_type"),
            "shipper_type" => post_var("shipper_type"),
            "location_type" => post_var("location_type"),
            "hours_of_operation" => post_var("hours_of_operation"),
            "insurance_liability_amount" => post_var("insurance_liability_amount"),
            "insurance_coverage" => post_var("insurance_coverage"),
            "insurance_cargo_deductible" => post_var("insurance_cargo_deductible"),
            "referred_by" => $referrer_name_value,
            'referred_id' => post_var("referred_id"),
            'ocity' => post_var("ocity"),
            'ostate' => post_var("ostate"),
            'ozip_code' => post_var("ozip_code"),
            'dcity' => post_var("dcity"),
            'dstate' => post_var("dstate"),
            'dzip_code' => post_var("dzip_code"),
            'radius' => post_var("radius"),
            'unsubscribe' => (post_var("unsubscribe") == "1" ? 1 : 0),
            'print_check' => (post_var("print_check") == "1" ? 1 : 0),
            "print_address1" => post_var("print_address1"),
            "print_address2" => post_var("print_address2"),
            "print_city" => post_var("print_city"),
            "print_state" => post_var("print_state"),
            "print_state_other" => post_var("print_state_other"),
            "print_zip_code" => post_var("print_zip_code"),
            "print_country" => post_var("print_country"),
        );

        return $insert_arr;
    }

    public function uploads()
    {
        $ID = (int) get_var("id");
        $this->tplname = "accounts.accounts.uploads";
        $this->title .= " - Account Documents";

        if ($_SESSION['member']['access_shippers'] != 1) {
            $this->check_access("accounts", "show", array('id' => $ID));
        }

        $account = new Account($this->daffny->DB);
        $account->load($ID);

        if ($account->owner_id != getParentId()) {

        }

        $this->breadcrumbs = $this->getBreadCrumbs(array(
            getLink("accounts") => "Accounts",
            getLink("accounts", "details", "id", $ID) => htmlspecialchars($this->input['company_name']),
            '' => "Account Documents",
        ));

        $this->daffny->tpl->files = $this->getFiles($ID);
        $this->form->FileFiled("files_upload", array(), "Upload", "</td><td>");

        $this->form->TextField("mail_to", 255, array("style" => ""), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("mail_subject", 255, array("style" => ""), $this->requiredTxt . "Subject", "</td><td>");
        $this->form->TextArea("mail_body", 15, 10, array("style" => ""), $this->requiredTxt . "Body", "</td><td>");
    }

    public function upload_file()
    {

        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "accounts/";
        $upload->max_file_size = 3 * 1024 * 1024;
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
                    if (false) {
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

                        $this->daffny->DB->insert("app_accounts_uploads", array(
                            "account_id" => $id,
                            "upload_id" => $insid,
                        ));

                        $out = getFileImageByType($upload->file_extension) . " ";
                        $out .= '<a href="' . getLink("accounts", "getdocs", "id", $insid) . '">' . $_FILES[$upload->form_field]['name'] . '</a>';
                        $out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") ";
                        $out .= '&nbsp;&nbsp;<a href="#" onclick="sendFile(\'' . $insid . '\', \'' . $sql_arr['name_original'] . '\')">Email</a>';
                        $out .= "&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"return deleteFile('" . getLink("accounts", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
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
                    $file_path = UPLOADS_PATH . "accounts/" . $row["name_on_server"];
                    $this->daffny->DB->delete('app_uploads', "id = '" . $id . "'");
                    $this->daffny->DB->delete('app_accounts_uploads', "upload_id = '" . $id . "'");

                    $update_arr_acc = array(
                        'insurance_doc_id' => 0,
                        'insurance_expirationdate' => '',
                    );
                    $this->daffny->DB->update("app_accounts", $update_arr_acc, "insurance_doc_id = '" . $id . "' ");

                    $out = array('success' => true);
                    @unlink($file_path);
                }
            }
        } catch (FDException $e) {
        }
        die(json_encode($out));
    }

    protected function getFiles($id)
    {
        $sql
        = "SELECT u.*
                  FROM app_accounts_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.account_id = '" . $id . "'
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
            $file_path = UPLOADS_PATH . "accounts/" . $file["name_on_server"];

            $type = $_GET['type'];
            if ($type == 1) {
                $file_path = UPLOADS_PATH . "accounts/insurance/" . $file["name_on_server"];
            }

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

    public function shippersComm()
    {
        $this->commision('shipper');
    }

    public function shippersCommEdit()
    {
        $this->commisionEdit('shipper');
    }

    public function commision($type)
    {
        try {
            $this->tplname = "accounts.accounts.commision";
            $this->title = "Account Commission";

            $ID = (int) get_var("id");
            if ($ID == "") {
                $ID = get_var("shipper");
            }

            $accountManager = new AccountManager($this->daffny->DB);
            $where .= " AND is_shipper=1 ";
            $accounts = $accountManager->get(" order by id desc", 10000, "`owner_id` = " . getParentId() . " " . $where);
            $this->daffny->tpl->accounts = $accounts;

            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }

            $commissionArr = array();
            for ($i = 1; $i <= 100; $i++) {
                $commissionArr[$i] = $i . " %";
            }

            $sql_arr = array(
                "shipper_id" => (int) post_var("account"),
                "members_id" => (int) post_var("salesman"),
                "reffered_by" => post_var("referred_by"),
                "commision" => (int) post_var("commission"),
                "create_date" => date("Y-m-d H:i:s"),
            );

            if (isset($_POST['submit'])) {

                if ($sql_arr['account'] == "") {
                    $this->isEmpty("account", "Shipper");
                }

                if ($sql_arr['salesman'] == "") {
                    $this->isEmpty("salesman", "User/Salesman");
                }

                if (!count($this->err)) {
                    $rows = $this->daffny->DB->selectRow("`id`", "`app_commision`", " where shipper_id='" . $sql_arr['shipper_id'] . "' AND members_id='" . $sql_arr['members_id'] . "'");

                    if (!count($rows) > 0) {

                        $this->daffny->DB->insert("app_commision", $sql_arr);

                        if ($this->dbError()) {
                            return;
                        }

                        $this->setFlashInfo("Commission setting saved.");
                        redirect(getLink("accounts", "edit", "id", $ID));
                    } else {
                        $this->setFlashInfo("Record already exist.");
                    }

                }

            }

            if (count($_POST)) {
                $this->input['referred_by'] = $_POST['referred_by'];
                $this->input['commission'] = $_POST['commission'];
            }

            $this->form->ComboBox("referred_by", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55, "onchange" => "selectReferred();"), "Referred By", "</td><td>");

        } catch (FDException $e) {
            print $e;
        }
    }

    public function commisionEdit($type)
    {
        try {
            $this->tplname = "accounts.accounts.commisionedit";
            $this->title = "Account Commission Edit";

            $shipperID = get_var("shipper");

            $accountManager = new AccountManager($this->daffny->DB);
            $where .= " AND is_shipper=1 ";
            $accounts = $accountManager->get(" order by id desc", 10000, "`owner_id` = " . getParentId() . " " . $where);
            $this->daffny->tpl->accounts = $accounts;

            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }

            $commissionArr = array();
            for ($i = 1; $i <= 100; $i++) {
                $commissionArr[$i] = $i . " %";
            }

            $sql_arr = array(
                "shipper_id" => (int) post_var("account"),
                "members_id" => (int) post_var("salesman"),
                "reffered_by" => post_var("referred_by"),
                "create_date" => date("Y-m-d H:i:s"),
                "primary_entry" => post_var("primary"),
            );

            if (isset($_POST['submit'])) {

                if ($sql_arr['account'] == "") {
                    $this->isEmpty("account", "Shipper");
                }

                if ($sql_arr['salesman'] == "") {
                    $this->isEmpty("salesman", "User/Salesman");
                }
                
                if (!count($this->err)) {

                    if ($_POST['primary'] == 1) {
                        $dateArr = array();
                        $dateArr['primary_entry'] = 0;
                        $upd_sales_arr = $this->daffny->DB->PrepareSql("app_commision", $dateArr);
                        $this->daffny->DB->update("app_commision", $upd_sales_arr, "shipper_id = '" . $shipperID . "' ");
                    }

                    $this->daffny->DB->update("app_commision", $sql_arr, "id='" . (int) $_GET['id'] . "'");

                    if ($this->dbError()) {
                        return;
                    }

                    $this->setFlashInfo("Commission setting updated.");
                    redirect(getLink("accounts", "edit", "id", $shipperID));
                }

            }

            if (count($_POST) == 0) {
                $rows = $this->daffny->DB->selectRow("`id`, `shipper_id`, `members_id`, `reffered_by`, `commision`,`create_date`", "`app_commision`", " where id=" . $_GET['id']);

                if (!is_array($rows) || $this->daffny->DB->isError) {
                    throw new FDException("DB query error");
                }

                if (count($rows) > 0) {

                    $this->input['referred_by'] = $rows['reffered_by'];
                    $this->input['commission'] = $rows['commision'];
                    $this->daffny->tpl->shipper_id = $rows['shipper_id'];
                    $this->daffny->tpl->members_id = $rows['members_id'];
                }
            } else {
                $this->input['referred_by'] = $_POST['referred_by'];
                $this->input['commission'] = $_POST['commission'];
                $this->daffny->tpl->shipper_id = $_POST['account'];
                $this->daffny->tpl->members_id = $_POST['salesman'];
            }

            $this->form->ComboBox("referred_by", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55, "onchange" => "selectReferred();"), "Referred By", "</td><td>");
            $status_name = array("1" => "Yes", "0" => "No");
            $this->form->ComboBox("primary", $status_name, array('style' => ""), $this->requiredTxt . "Primary", "</td><td>");

        } catch (FDException $e) {
        }
    }

    public function shippersCommDelete()
    {
        $out = array('success' => false);
        try {
            $ID = $this->checkId();
            $this->check_access("accounts", "delete", array("id" => $ID));

            $this->daffny->DB->update("app_commision", array("is_deleted" => 1), "id = $ID");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            }
            $out = array('success' => true);
            die(json_encode($out));
        } catch (FDException $e) {
            print $e;
        }
    }

    public function route()
    {

        try {

            $ID = (int) get_var("id");
            $RID = (int) get_var("rid");

            $account = new Account($this->daffny->DB);
            $RouteObj = new Route($this->daffny->DB);

            $this->tplname = "accounts.accounts.route";

            $this->title .= ($ID > 0 ? " - Edit Account Route" : " - Add New Account Route");

            $this->check_access("accounts", "edit", array("id" => $ID));
            
            if ($ID > 0) {
                try{
                    $account->load($ID);
                } catch(Exception $e){
                    print_r($e);
                    die("Try again later");
                }
                
            }

            if ($RID > 0) {
                try{
                    $RouteObj->load($RID);
                } catch(Exception $e){
                    print_r($e);
                    die("Try again later");
                }
            }

            $sql_arr = $this->getInsertArray();

            $this->input = $sql_arr;

            if (isset($_POST['submit'])) {

                $this->isEmpty("ocity", "City");
                $this->isEmpty("ostate", "State");

                $this->isEmpty("dcity", "City");
                $this->isEmpty("dstate", "State");

                if (!count($this->err)) {

                    if ($RID > 0) {

                        $this->updateHistory($sql_arr, $ID);

                        $RouteArr = array();
                        $RouteArr['city'] = $sql_arr['ocity'];
                        $RouteArr['state'] = $sql_arr['ostate'];
                        $RouteArr['zip_code'] = $sql_arr['ozip_code'];
                        $RouteArr['city'] = $sql_arr['dcity'];
                        $RouteArr['state'] = $sql_arr['dstate'];
                        $RouteArr['zip_code'] = $sql_arr['dzip_code'];

                        $RouteObj->update($RouteArr);

                        $this->setFlashInfo("Account route has been updated.");

                    } else {

                        $AccountRouteObj = new AccountRoute($this->daffny->DB);

                        $AccountRouteArr = array();
                        $AccountRouteArr['type'] = "ORG";
                        $AccountRouteArr['account_id'] = $ID;
                        $AccountRouteArr['origin'] = $sql_arr['ocity'] . "," . $sql_arr['ostate'] . "," . $sql_arr['ozip_code'];
                        $AccountRouteArr['destination'] = $sql_arr['dcity'] . "," . $sql_arr['dstate'] . "," . $sql_arr['dzip_code'];
                        $AccountRouteArr['ocity'] = $sql_arr['ocity'];
                        $AccountRouteArr['ostate'] = $sql_arr['ostate'];
                        $AccountRouteArr['ozip'] = $sql_arr['ozip_code'];
                        $AccountRouteArr['dcity'] = $sql_arr['dcity'];
                        $AccountRouteArr['dstate'] = $sql_arr['dstate'];
                        $AccountRouteArr['dzip'] = $sql_arr['dzip_code'];
                        $AccountRouteArr['radius'] = $sql_arr['radius'];

                        $AccountRouteObj->create($AccountRouteArr);
                        $AccountRouteID = $AccountRouteObj->id;

                        $RouteArr = array();
                        $RouteArr['route_id'] = $AccountRouteID;
                        $RouteArr['type'] = "ORG";
                        $RouteArr['city'] = $sql_arr['ocity'];
                        $RouteArr['state'] = $sql_arr['ostate'];
                        $RouteArr['zip'] = $sql_arr['ozip_code'];

                        $RouteObj->create($RouteArr);

                        $RouteArr1 = array();
                        $RouteArr1['route_id'] = $AccountRouteID;
                        $RouteArr1['type'] = "DES";
                        $RouteArr1['city'] = $sql_arr['dcity'];
                        $RouteArr1['state'] = $sql_arr['dstate'];
                        $RouteArr1['zip'] = $sql_arr['dzip_code'];

                        $RouteObj->create($RouteArr1);
                        $RouteObj->routeMapping($AccountRouteID, "ORG", $sql_arr['ocity'], $sql_arr['ostate'], $sql_arr['ozip_code'], $sql_arr['radius']);
                        $RouteObj->routeMapping($AccountRouteID, "DES", $sql_arr['dcity'], $sql_arr['dstate'], $sql_arr['dzip_code'], $sql_arr['radius']);

                        $this->setFlashInfo("Account route has been added.");
                    }
                    redirect(getLink("accounts", "route", "id", $ID));

                }

            } else {

                if ($ID > 0) {

                    if ($account->owner_id != getParentId()) {
                        $this->setFlashError("Access denied. Account Owner and parent ids do not match");
                        redirect(getLink('accounts', "route"));
                    }

                    $this->input = $account->getAttributes();
                }

            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink("accounts") => "Accounts",
                '' => ($ID > 0 ? htmlspecialchars($this->input['company_name']) : "Add New Account"),
            ));

            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }

            $this->daffny->tpl->accountType = $this->input['type'];

            $sql = "SELECT * FROM  app_account_route where account_id='" . $ID . "' ORDER BY id desc";

            $rows = $this->daffny->DB->selectRows($sql);
            if (!is_array($rows) || $this->daffny->DB->isError) {
                throw new FDException("DB query error");
            }

            if (count($rows) > 0) {
                $routeData = array();
                foreach ($rows as $row) {
                    $tempArr = array();
                    $tempArr['id'] = $row['id'];
                    $tempArr['account_id'] = $row['account_id'];
                    $tempArr['origin'] = $row['origin'];
                    $tempArr['destination'] = $row['destination'];
                    $tempArr['ocity'] = $row['ocity'];
                    $tempArr['ostate'] = $row['ostate'];
                    $tempArr['ozip'] = $row['ozip'];
                    $tempArr['dcity'] = $row['dcity'];
                    $tempArr['dstate'] = $row['dstate'];
                    $tempArr['dzip'] = $row['dzip'];
                    $tempArr['radius'] = $row['radius'];
                    $routeData[] = $tempArr;
                }
                $this->daffny->tpl->routeData = $routeData;
            }

            $radiusArr = array("10" => "10", "20" => "20", "25" => "25", "30" => "30", "35" => "35");
            $this->form->TextField("ocity", 255, array("class" => "geo-city"), $this->requiredTxt . "City", "</td><td>");
            $this->form->ComboBox("ostate", array("" => "Select State") + $this->getStates(), array(), $this->requiredTxt . "State", "</td><td>");
            $this->form->TextField("ozip_code", 10, array('style' => ''), "" . "Zip/Postal Code", "</td><td>");
            $this->form->TextField("dcity", 255, array("class" => "geo-city"), $this->requiredTxt . "City", "</td><td>");
            $this->form->ComboBox("dstate", array("" => "Select State") + $this->getStates(), array(), $this->requiredTxt . "State", "</td><td>");
            $this->form->TextField("dzip_code", 10, array('style' => ''), "" . "Zip/Postal Code", "</td><td>");
            $this->form->ComboBox("radius", array("" => "Select Radius") + $radiusArr, array(), $this->requiredTxt . "Radius", "</td><td>");
        } catch (FDException $e) {
            $this->setFlashError("Something went wrong try again later");
            redirect(getLink('accounts', "route"));
        }
    }

    public function routeDelete()
    {

        $out = array('success' => false);

        try {

            $ID = $this->checkId();
            $this->check_access("accounts", "delete", array("id" => $ID));
            $this->daffny->DB->delete('app_account_route', "id = '" . $ID . "'");
            $this->daffny->DB->delete('app_route', "route_id = '" . $ID . "'");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            }

            $out = array('success' => true);
            die(json_encode($out));
        } catch (FDException $e) {
            print $e;
        }
    }

    public function upload_insurance()
    {

        $id = (int) get_var("id");
        $insurance_type = $_POST['insurance_type'];

        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "accounts/insurance/";
        $upload->max_file_size = 3 * 1024 * 1024;
        $upload->form_field = "insurance_doc";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf");
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
                            'insurance' => 1,
                            'insurance_type' => $insurance_type,
                            "insurance_expirationdate" => date("Y-m-d", strtotime($_POST['insurance_expirationdate'])),
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
                            "insurance_type" => $insurance_type,
                            "insurance_expirationdate" => date("Y-m-d", strtotime($_POST['insurance_expirationdate'])),
                        );
                        
                        $this->daffny->DB->update("app_accounts", $carrier_arr1, "id = '" . $id . "' ");

                        if ($insurance_type);
                        $out .= Account::$ins_tupe_name[$insurance_type] . ": ";
                        $out .= getFileImageByType($upload->file_extension) . " ";
                        $out .= '<a href="' . getLink("accounts", "getdocs", "id", $insid, "type", 1) . '">View ' . date("m/d/Y", strtotime($_POST['insurance_expirationdate'])) . '</a>';
                        $out .= "&nbsp;&nbsp;&nbsp;<a  href=\"#\" onclick=\"return deleteFile('" . getLink("accounts", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
                        print "<li id=\"file-" . $insid . "\">" . $out . "</li>";
                        exit;
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

    public function delete_file_insurance()
    {
        $out = array('success' => false);
        $id = (int) get_var('id');
        try {
            if ($row = $this->daffny->DB->selectRow('*', "app_uploads", "WHERE id = '$id' AND owner_id = '" . getParentId() . "'")) {
                if ($this->daffny->DB->isError) {
                    throw new Exception($this->getDBErrorMessage());
                } else {
                    $file_path = UPLOADS_PATH . "accounts/insurance/" . $row["name_on_server"];
                    $this->daffny->DB->delete('app_uploads', "id = '" . $id . "'");
                    $this->daffny->DB->delete('app_accounts_uploads', "upload_id = '" . $id . "'");

                    $update_arr_acc = array(
                        'insurance_doc_id' => 0,
                        'insurance_expirationdate' => '',
                    );
                    $this->daffny->DB->update("app_accounts", $update_arr_acc, "insurance_doc_id = '" . $id . "' ");

                    $out = array('success' => true);
                    @unlink($file_path);
                }
            }
        } catch (FDException $e) {
        }
        die(json_encode($out));
    }

    protected function getFilesInsurance($id)
    {
        $sql
        = "SELECT u.*
                  FROM app_accounts_uploads au
                  inner JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.account_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "' and u.insurance =1
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

    protected function getInsuranceCertificate($id)
    {
        $sql
        = "SELECT u.*
                  FROM  app_uploads u
                 WHERE u.id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "' ";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }

    public function duplicateCarriers()
    {
        if (!$_SESSION['member']['access_duplicate_carriers']) {
            $this->setFlashError("Access Denied.");
            redirect(getLink());
        }
        $this->duplicateAccounts('carrier');
    }

    public function duplicateShippers()
    {
        if (!$_SESSION['member']['access_duplicate_shippers']) {
            $this->setFlashError("Access Denied.");
            redirect(getLink());
        }
        $this->duplicateAccounts('shipper');
    }

    private function duplicateAccounts($type)
    {

        try {
            $this->tplname = "accounts.accounts.list_duplicate";
            $data_tpl = "accounts.accounts.list_duplicate";
            $accountManager = new AccountManager($this->daffny->DB);

            $this->applyOrder("`app_accounts`");
            $this->order->Fields[] = 'A.company_name';

            $where = "";
            $active_inactive = "";
            $accType = 0;
            switch ($type) {

                case 'shipper':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));

                    $where .= " AND A.is_shipper=1 ";
                    $accType = 2;
                    break;
                case 'carrier':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Carrier'));

                    $where .= " AND A.is_carrier=1 ";
                    $accType = 1;
                    break;
                case 'inactive':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
                    break;

            }

            $owner_id = " ";
            if ($_SESSION['member_id'] != getParentId()) {
                $owner_id = " AND A.`owner_id` = " . $_SESSION['member_id'];
            }

            $accounts = $accountManager->getDuplicateAccount($this->order->getOrder(), $_SESSION['per_page'], $where, " `owner_id` = " . getParentId(), $accType);
            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;
            $this->daffny->tpl->accType = $accType;

        } catch (FDException $e) {
            redirect(SITE_IN);
        }
    }

    public function duplicateDetails()
    {

        try {
            $ID = (int) get_var("id");

            $account = new Account($this->daffny->DB);
            $this->tplname = "accounts.accounts.list_duplicate_detail";
            $data_tpl = "accounts.accounts.list_duplicate_detail";
            $this->title .= " - Duplicate Account Details";

            $this->check_access("accounts", "edit", array("id" => $ID));

            if ($ID > 0) {
                $account->load($ID);
            }

            $this->applyOrder("`app_accounts`");
            $this->order->Fields[] = 'A.company_name';

            $type = 2;
            $accType = 0;
            $accountManager = new AccountManager($this->daffny->DB);
            if ($account->is_carrier == 1) {
                $where = " A.`is_carrier` = 1  and A.company_name = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $account->company_name) . "'";
                $accType = 1;
            }
            if ($account->is_shipper == 1) {
                $where = " A.`is_shipper` = 1  and A.company_name = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $account->company_name) . "' and A.first_name = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $account->first_name) . "' and A.last_name = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $account->last_name) . "'";
                $accType = 2;
                $type = 1;
            }
            $accounts = $accountManager->getDuplicateAccountDetails($this->order->getOrder(), $_SESSION['per_page'], $where, "", $accType);

            $this->daffny->tpl->accountOrig = $account;
            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;
            $this->daffny->tpl->accType = $accType;

        } catch (FDException $e) {
            redirect(SITE_IN);
        }
    }

    public function upload_insurance_file($element, $carrier, $type = 0)
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
            case 0:
                {

                    $insurance_expirationdate = date("Y-m-d", strtotime($_POST['insurance_expirationdate']));

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
                        "insurance_expirationdate" => $insurance_expirationdate,
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

    public function advDuplicateCarriers()
    {
        $this->advancedDuplicateAccounts('carrier');
    }

    public function advDuplicateShippers()
    {
        $this->advancedDuplicateAccounts('shipper');
    }

    public function advancedDuplicateAccounts($type)
    {
        try {

            if (!$_SESSION['member']['access_payments']) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->tplname = "accounts.accounts.advance_search_duplicate";
            $this->title = "Search Duplicate";

            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");
            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");

            $this->daffny->tpl->accountType = $type;
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink(''));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function advancedDuplicateAccountsSubmit()
    {
        try {
            if (count($_POST) == 0) {
                throw new UserException('Access Deined', getLink('orders'));
            }

            $this->initGlobals();
            $this->tplname = "accounts.accounts.list_duplicate_detail_search";
            $data_tpl = "accounts.accounts.list_duplicate_detail_search";

            $this->title = "List Search Accounts";

            $accountManager = new AccountManager($this->daffny->DB);

            $this->applyOrder("`app_accounts`");
            $this->order->Fields[] = 'A.company_name';

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("Accounts") => "", 'Accounts' => 'Duplicate Accounts Search'));

            $batch_order_ids_arr = explode(",", trim($_POST['batch_order_ids']));
            $this->daffny->tpl->batch_order_ids_arr = $batch_order_ids_arr;

            $this->input['batch_order_ids'] = $_POST['batch_order_ids'];
            $this->input['shipper_company'] = $_POST['shipper_company'];
            $this->input['shipper_company_id'] = $_POST['shipper_company_id'];

            $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");
            $this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:200px;"), $this->requiredTxt . "", "</td><td>");

            $type = $_POST['accountType'];
            $where = "";
            $active_inactive = "";
            $accType = 0;
            switch ($type) {

                case 'shipper':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));

                    $where .= "  A.is_shipper=1 ";
                    $accType = 1;
                    break;
                case 'carrier':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Carrier'));

                    $where .= " A.is_carrier=1 ";
                    $accType = 2;
                    break;
                case 'inactive':
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
                    break;

            }

            $owner_id = " ";
            if ($_SESSION['member_id'] != getParentId()) {
                $owner_id = " AND A.`owner_id` = " . $_SESSION['member_id'];
            }

            $accounts = $accountManager->searchDuplicateAccounts($this->order->getOrder(), $_SESSION['per_page'], $where, $accType, $_POST['shipper_company'], $_POST['orders_list']);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";

            $this->daffny->tpl->accounts = $accounts;
            $this->daffny->tpl->accountType = $type;
            $this->daffny->tpl->accType = $accType;

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    /*
     * This function is to display the upload B2B contract UI and its respective
     * functionality
     *
     * @module B2B
     * @author  Chetu Inc.
     * @lastUpdated 09012018
     */
    public function accountConract()
    {
        try {
            /*
             * Loading account data
             */
            $ID = (int) get_var("id");
            $account = new Account($this->daffny->DB);
            $account->load($ID);

            $this->tplname = "accounts.accounts.accountContract";
            $this->title .= ($ID > 0 ? " - Shipper Activity" : " - Account Contract");

            $result = $this->daffny->DB->query("SELECT * FROM app_account_contracts WHERE account_id = '" . $_GET['id'] . "' ORDER BY ID DESC");

            $documents = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $documents[] = $row;
            }

            $this->daffny->tpl->document = $documents;

            $this->daffny->tpl->files = $this->getAccountContractsAsAttachment($ID);
            $this->form->FileFiled("files_upload", array(), "Upload", "</td><td>");
            $this->form->TextField("mail_to", 255, array("style" => ""), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => ""), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => " "), $this->requiredTxt . "Body", "</td><td>");

        } catch (FDException $error) {
            redirect(getLink('accounts'));
        }
    }

    /*
     * This function pulls account contracts as attachments
     *
     * @module B2B
     * @author  Chetu Inc.
     * @lastUpdated 09012018
     */
    protected function getAccountContractsAsAttachment($accountId)
    {
        $sql = "SELECT * FROM `app_account_contracts` where `account_id` = '" . $accountId . "'";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }

        return $files;
    }

    /*
     * This function uploads the account contracts at the account level
     *
     * @author Chetu Inc.
     * @lastUpdatedAt 09-01-2018
     */
    public function uploadAccountContract()
    {

        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "accounts/contracts/";
        $upload->max_file_size = 3 * 1024 * 1024;
        $upload->form_field = "file";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "tiff", "wpd");
        $upload->save_as_file_name = md5(time() . "-" . rand()) . time();
        $upload->upload_process();

        switch ($upload->error_no) {
            case 0:{
                    //check storage space
                    $license = new License($this->daffny->DB);
                    $license->loadCurrentLicenseByMemberId(getParentId());
                    $space = $license->getCurrentStorageSpace();
                    $used = $license->getUsedStorageSpace();

                    if ($used > $space) {
                        die("ERROR:Storage space exceeded.");
                    } else {

                        $res = $this->daffny->DB->query("SELECT `commercial_terms_updated_at` FROM app_defaultsettings WHERE `owner_id`='" . $_SESSION['member']['parent_id'] . "' ");
                        $row = mysqli_fetch_assoc($res);

                        $this->daffny->DB->query("UPDATE app_account_contracts SET status = 0 WHERE account_id = '" . $_GET['id'] . "'");

                        $sql_arr = array(
                            'owner_id' => getParentId(),
                            'account_id' => $_GET['id'],
                            'name_original' => $_FILES[$upload->form_field]['name'],
                            'name_on_server' => $upload->save_as_file_name,
                            'size' => $_FILES[$upload->form_field]['size'],
                            'type' => $upload->file_extension,
                            'uploaded_by' => $_SESSION['member']['id'],
                            'uploaded_by_name' => "Member: " . $_SESSION['member']['contactname'],
                            'version' => $row['commercial_terms_updated_at'],
                            'status' => 1,
                        );

                        $ins_arr = $this->daffny->DB->PrepareSql("app_account_contracts", $sql_arr);
                        $this->daffny->DB->insert("app_account_contracts", $ins_arr);
                        die;
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

    /**
     * Accounts controller function to fetch accounts credit card & render respective UI
     *
     * @author Shahrukh
     * @version 1.0
     */
    public function SavedCards()
    {
        try {
            $ID = (int) get_var("id");

            /** Loading Template */
            $this->tplname = "accounts.accounts.SavedCards.List";

            $account = new Account($this->daffny->DB);
            $account->load($ID);
            $this->input = $account->getAttributes();

            /** Setting Bread Crumps */
            $this->breadcrumbs = $this->getBreadCrumbs(array(
                getLink("accounts") => "Accounts",
                getLink("accounts", "details", "id", $ID) => htmlspecialchars($this->input['company_name']),
                getLink("accounts", "SavedCards", "id", $ID) => "Saved Cards",
            ));

            /** Setting Title */
            $this->title = "SavedCards";

            $this->applyPager("AccountsCCInformation h", "", "WHERE h.AccountID ='" . $ID . "'");
            $this->applyOrder("AccountsCCInformation");

            $sql = "SELECT * FROM AccountsCCInformation WHERE AccountID ='" . $ID . "' " . $this->order->getOrder() . $this->pager->getLimit();
            $this->getGridData($sql, false);

        } catch (FDException $error) {
            redirect(getLink('accounts'));
        }
    }
}
