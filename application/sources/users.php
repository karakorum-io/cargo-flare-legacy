<?php

class ApplicationUsers extends ApplicationAction
{

    public $title = "Manage Users";
    public $section = "Manage Users";

    public function construct()
    {
        if (!$this->check_access('users')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    /**
     * List all users
     *
     */
    public function idx()
    {
        $this->breadcrumbs = $this->getBreadCrumbs("Users");
        $this->tplname = "users.users.list";
        $this->daffny->tpl->emptyText = "Users not found.";

        $this->applyPager("members m", "", "WHERE m.parent_id='" . getParentID() . "'");
        $this->applyOrder("members");
        $sql = "SELECT m.* , DATE_FORMAT(m.reg_date, '%m/%d/%Y %H:%i:%s') reg_date FROM members m WHERE is_deleted = 0 AND m.parent_id='" . getParentID() . "'" . $this->order->getOrder() . $this->pager->getLimit();
        $this->getGridData($sql);
    }

    public function edit()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users.form";
        $this->title .= ($ID > 0 ? " - Edit" : " - Add");

        if ($ID <= 0) {
            //check if user add allowed
            $cp = new CompanyProfile($this->daffny->DB);
            $cp->getByOwnerId(getParentId());
            if (!$cp->AdditionalUsersAllowed()) {
                $this->setFlashError("You have reached maximum allowed additional users in your license.");
                redirect(getLink("users"));
            }
        }

        $this->check_access("user", "edit", array('id' => $ID));

        $this->input = $this->SaveFormVars();

        $update_history = false;
        if (isset($_POST['submit'])) {

            $sql_arr = $this->getTplPostValues();

            if ($ID > 0) {

                $old = $this->daffny->DB->select_one("status", Member::TABLE, " WHERE id = '" . $ID . "'");

                if ($sql_arr['status'] != $old["status"]) {
                    $update_history = true;
                }

                if ($sql_arr['status'] == 'Active') {

                    if ($old["status"] != 'Active') {
                        //check if user change status allowed
                        $cp = new CompanyProfile($this->daffny->DB);
                        $cp->getByOwnerId(getParentId());
                        if (!$cp->AdditionalUsersAllowed($ID)) {
                            $this->err[] = "You have reached maximum allowed additional users in your license.";
                        }
                    }
                }
            }

            $this->isEmpty("contactname", "Name");
            $this->isEmpty("username", "Username");
            $this->isEmpty("lead_multiple", "Lead Multiple");
            $this->isEmpty("email", "E-mail");
            //check email
            $this->checkEmail("email", "E-mail");
            if ($sql_arr['email'] != "") {
                $row = $this->daffny->DB->select_one("id", "members", "WHERE email = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['email']) . "' AND id <> '" . $ID . "'");
                if (!empty($row)) {
                    $this->err[] = "<strong>E-mail</strong> already registered.";
                }
            }

            //check username
            $row = $this->daffny->DB->select_one("id", "members", "WHERE username = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['username']) . "' AND id <> '" . $ID . "'");
            if (!empty($row)) {
                $this->err[] = "<strong>Username</strong> already registered.";
            }

            if ($ID <= 0) {

                if ($sql_arr['password'] != "" && $sql_arr['password_confirm'] != "" && $sql_arr['password'] != $sql_arr['password_confirm']) {
                    $this->err[] = "<strong>Passwords does not match.</strong>";
                }
            }
            if ($ID <= 0) {
                $this->isEmpty("password", "Password");
                $this->isEmpty("password_confirm", "Confirm password");
            }

            if (!count($this->err)) {
                $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sql_arr);
                if ($ID <= 0) {
                    unset($sql_arr1['password_confirm']);
                    if ($sql_arr1['`password`'] != "") {
                        $sql_arr1['`password`'] = md5($sql_arr1['`password`']);
                    } else {
                        unset($sql_arr1['password']);
                    }
                }
                if ($ID > 0) {
                    $this->updateHistory($sql_arr1, $ID);
                    $this->daffny->DB->update("members", $sql_arr1, "id = '" . $ID . "'");

                    $amp = array(
                        "username" => $sql_arr['contactname'],
                    );
                    $this->daffny->DB->update("app_arrowchat_members", $amp, "member_id = '" . $ID . "'");
                    $this->setFlashInfo("Information has been updated.");
                } else {
                    $sql_arr1['parent_id'] = getParentId();
                    $sql_arr1['chmod'] = 3;

                    $this->daffny->DB->insert("members", $sql_arr1);
                    $this->setFlashInfo("Information has been added.");
                    $ID = $this->daffny->DB->get_insert_id();
                    $update_history = true;

                    $amp = array(
                        "member_id" => $ID,
                        "username" => $sql_arr['contactname'],
                        "date" => date("Y-m-d H:i:s"),

                    );
                    $this->daffny->DB->insert("app_arrowchat_members", $amp);

                    if ($ID > 0) {

                        $query = " SELECT A.id as friend_member_id, B.id as member_id
											FROM  `members` A,  `members` B
											WHERE A.`parent_id` = '" . getParentId() . "'
											AND B.`parent_id` = '" . getParentId() . "'
											AND A.id = '" . $ID . "'";

                        $result = $this->daffny->DB->query($query);

                        if ($result) {
                            while ($row = $this->daffny->DB->fetch_row($result)) {

                                $afmp = array(
                                    "member_id" => $row['member_id'],
                                    "friend_member_id" => $row['friend_member_id'],
                                    "handshake" => 1,
                                    "date" => date("Y-m-d"),
                                );

                                $this->daffny->DB->insert("app_arrowchat_friends", $afmp);

                                $afmp = array(
                                    "member_id" => $row['friend_member_id'],
                                    "friend_member_id" => $row['member_id'],
                                    "handshake" => 1,
                                    "date" => date("Y-m-d"),
                                );

                                $this->daffny->DB->insert("app_arrowchat_friends", $afmp);

                            }
                        }
                    }
                }

                if ($update_history) {

                    $r1 = $this->daffny->DB->select_one("COUNT(id) AS cnt", "members", "WHERE parent_id='" . getParentId() . "' AND status='Active' AND id <> parent_id ");
                    $active_users = $r1["cnt"];
                    $license = new License($this->daffny->DB);
                    $paid_users = $license->getAdditionalLicenseUsersByMemberId(getParentId());

                    $mp = array(
                        "member_id" => $ID,
                        "update_date" => date("Y-m-d H:i:s"),
                        "action" => $sql_arr['status'] == "Active" ? "Activate" : "Inactivate",
                        "active_users" => $active_users,
                        "paid_users" => $paid_users,
                        "changed_by" => $_SESSION["member_id"],
                        "owner_id" => getParentId(),
                    );
                    $this->daffny->DB->insert("app_members_paid", $mp);
                }

                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("users", "show", "id", $ID));
            }
        } else {
            if ($ID > 0) {
                $sql = "SELECT m.*
                      FROM members m
                     WHERE m.id = '" . $ID . "' AND m.parent_id='" . getParentId() . "'";
                $row = $this->daffny->DB->selectRow($sql);
                $this->input = $row;
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($row['contactname']), '' => "Edit"));
            } else {

                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", '' => "Add New"));
            }
        }

        $this->input['password'] = "";
        $this->input['password_confirm'] = "";

        if (!isset($this->input['lead_multiple']) || $this->input['lead_multiple'] == "") {
            $this->input['lead_multiple'] = 0;
        }

        if ($ID > 0) {
            $this->input["contactname_txt"] = $this->input["contactname"];
            $this->input["username_txt"] = $this->input["username"];
        }

        $this->form->TextField("contactname", 255, array(), $this->requiredTxt . "Name", "</td><td>");
        $this->form->TextField("username", 255, array(), $this->requiredTxt . "Username", "</td><td>");
        $this->form->TextField("phone", 255, array("class" => "phone"), "Phone", "</td><td>");
        $this->form->TextField("lead_multiple", 11, array("style" => "", "class" => "digit-only"), $this->requiredTxt . "Lead Multiple", "</td><td>");
        // Login Information
        $this->form->TextField("email", 255, array("class" => "email"), $this->requiredTxt . "E-mail", "</td><td>");

        if ($ID <= 0) {
            $this->form->PasswordField("password", 15, array(), ($ID > 0 ? "Change password" : $this->requiredTxt . "Password"), "</td><td>");
            $this->form->PasswordField("password_confirm", 15, array(), ($ID > 0 ? "Confirm password" : $this->requiredTxt . "Confirm password"), "</td><td>");
        }

        $this->form->ComboBox("status", array("Active" => "Active", "Inactive" => "Inactive"), array("style" => ""), $this->requiredTxt . "Status", "</td><td>");
    }

    public function changepassword()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users.formpassword";
        $this->title .= ($ID > 0 ? " - Change Password" : " - Add");

        $this->check_access("user", "edit", array('id' => $ID));

        $this->input = $this->SaveFormVars();

        $update_history = false;
        if (isset($_POST['submit'])) {

            $sql_arr = $this->getTplPostValues();

            $this->isEmpty("password", "Password");
            $this->isEmpty("password_confirm", "Confirm password");
            if ($sql_arr['password'] != "" && $sql_arr['password_confirm'] != "" && $sql_arr['password'] != $sql_arr['password_confirm']) {
                $this->err[] = "<strong>Passwords does not match.</strong>";
            }

            if (!count($this->err)) {
                $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sql_arr);
                $sql_arr1['`password`'] = md5($sql_arr1['`password`']);
                if ($ID > 0) {
                    $this->updateHistory($sql_arr1, $ID);
                    $this->daffny->DB->update("members", $sql_arr1, "id = '" . $ID . "'");
                    $this->setFlashInfo("Information has been updated.");
                } else {
                    $sql_arr1['parent_id'] = getParentId();
                    $sql_arr1['chmod'] = 3;
                    $this->daffny->DB->insert("members", $sql_arr1);
                    $this->setFlashInfo("Information has been added.");
                    $ID = $this->daffny->DB->get_insert_id();
                    $update_history = true;
                }

                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("users", "show", "id", $ID));
            }
        }

        $this->form->PasswordField("password", 15, array(), ($ID > 0 ? "Change password" : $this->requiredTxt . "Password"), "</td><td>");
        $this->form->PasswordField("password_confirm", 15, array(), ($ID > 0 ? "Confirm password" : $this->requiredTxt . "Confirm password"), "</td><td>");

    }

    public function privileges()
    {
        $ID = (int) get_var("id");

        if ($ID <= 0) {
            $this->setFlashError("Bad user ID.");
            redirect(getLink("users"));
        }
        $this->tplname = "users.users.privileges";
        $this->title = "Individual Privileges";
        $this->check_access("user", "edit", array('id' => $ID));
        $this->input = $this->SaveFormVars();

        if (isset($_POST['submit'])) {

            $arr = post_var("users_ids");

            if ($_POST['leadsT'] == 2) {
                if (!in_array($_GET['id'], $arr)) {
                    $arr[] = $_GET['id'];
                }
                $leads = implode(",", $arr);
            } else {
                $leads = "";
            }

            $arr3 = post_var("users_ids3");

            if ($_POST['ordersT'] == 2) {
                if (!in_array($_GET['id'], $arr3)) {
                    $arr3[] = $_GET['id'];
                }
                $orders = implode(",", $arr3);
            } else {
                $orders = "";
            }

            $sql_arr = array(
                'access_leads' => post_var("access_leads")
                , 'access_quotes' => post_var("access_quotes")
                , 'access_orders' => post_var("access_orders")
                , 'access_carriers' => post_var("access_carriers")
                , 'access_locations' => post_var("access_locations")
                , 'access_shippers' => post_var("access_shippers")
                , 'access_duplicate_carriers' => post_var("access_duplicate_carriers")
                , 'access_duplicate_shippers' => post_var("access_duplicate_shippers")
                , 'access_notes' => post_var("access_notes")
                , 'access_dispatch' => (post_var("access_dispatch") == "1" ? 1 : 0)
                , 'access_payments' => (post_var("access_payments") == "1" ? 1 : 0)
                , 'access_lead_sources' => (post_var("access_lead_sources") == "1" ? 1 : 0)
                , 'hide_lead_sources' => (post_var("hide_lead_sources") == "1" ? 1 : 0)
                , 'access_reports' => (post_var("access_reports") == "1" ? 1 : 0)
                , 'access_users' => (post_var("access_users") == "1" ? 1 : 0)
                , 'access_preferences' => (post_var("access_preferences") == "1" ? 1 : 0)
                , 'access_dispatch_orders' => (post_var("access_dispatch_orders") == "1" ? 1 : 0)
                , 'specific_user_access' => $orders
                , 'specific_user_access_leads' => $leads
                , 'specific_user_access_quotes' => $quotes,
            );

            if (!count($this->err)) {
                $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sql_arr);
                $this->updateHistory($sql_arr1, $ID);
                $this->daffny->DB->update("members", $sql_arr1, "id = '" . $ID . "'");
                $this->setFlashInfo("Information has been updated.");
                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("users", "show", "id", $ID));
            }
        } else {
            $sql = "SELECT m.*
                            FROM members m
                            WHERE m.id = '" . $ID . "' AND m.parent_id='" . getParentId() . "'";
            $row = $this->daffny->DB->selectRow($sql);

            $specificOrderUser = explode(",", $row['specific_user_access']);
            $specificLeadUser = explode(",", $row['specific_user_access_leads']);
            $specificQuoteUser = explode(",", $row['specific_user_access_quotes']);

            $this->input = $row;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($row['contactname']), '' => "Privileges"));
        }

        $attr = array();
        if ($ID == $_SESSION['member']['parent_id']) {
            $attr['disabled'] = 'disabled';
        }

        //echo $row['access_orders'];die;
        $this->form->customValueShare1("access_leads_custom", $row['access_leads']);
        $this->form->customValueShare1("access_quotes_custom", $row['access_quotes']);
        $this->form->customValueShare1("access_orders_custom", $row['access_orders']);
        $this->form->customValueShare("specificLeads", $specificLeadUser);
        $this->form->customValueShare("specificOrders", $specificOrderUser);
        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $specificLeadUser, array("id" => "users_ids", "multiple" => "multiple"), "", "");
        $this->form->helperMLTPL("users_ids2[]", $this->getUsersByStatus(" status ='Active' "), $specificQuoteUser, array("id" => "users_ids2", "multiple" => "multiple"), "", "");
        $this->form->helperMLTPL("users_ids3[]", $this->getUsersByStatus(" status ='Active' "), $specificOrderUser, array("id" => "users_ids3", "multiple" => "multiple"), "", "");
        $this->form->helperLeadsCustom('access_leads', $attr, "Leads");
        $this->form->helperLeadsCustom('access_quotes', $attr, "Quotes");
        $this->form->helperLeadsCustom('access_orders', $attr, "Orders");
        $this->form->helperCarriers('access_locations', $attr);
        $this->form->helperCarriers('access_carriers', $attr);

        // shippers for access control
        $this->form->helperDuplicateAccounts('access_duplicate_carriers', $attr);
        $this->form->helperDuplicateAccounts('access_duplicate_shippers', $attr);
        $this->form->helperShippersNew('access_shippers', $attr);
        $this->form->helperNotes('access_notes', $attr);
        $this->form->CheckBox("access_dispatch", $attr, "Perform Others' Dispatch Activities", "&nbsp;");
        $this->form->CheckBox("access_dispatch_orders", $attr, "Allow Access to Edit Dispatch", "&nbsp;");
        $this->form->CheckBox("access_payments", $attr, "Access Process Payments", "&nbsp;");
        $this->form->CheckBox("access_lead_sources", $attr, "Access Lead Sources", "&nbsp;");
        $this->form->CheckBox("hide_lead_sources", $attr, "Hide Lead Sources", "&nbsp;");
        $this->form->CheckBox("access_reports", $attr, "View reports", "&nbsp;");
        $this->form->CheckBox("access_users", $attr, "Add / Edit Users, Groups and Privileges ", "&nbsp;");
        $this->form->CheckBox("access_preferences", $attr, "Edit company Preferences", "&nbsp;");
        $this->form->ComboBox("group_id", $this->getGroups(true), $attr, $this->requiredTxt . "Group", "</td><td>");
    }

    public function assign_privileges()
    {
        if (isset($_POST['submit_ap'])) {
            $ID = (int) get_var("id");
            $this->check_access("user", "edit", array('id' => $ID));
            $this->isZero('group_id', "Group");
            if (!count($this->err)) {
                $row = $this->daffny->DB->selectRow("*", "app_members_groups", "WHERE id='" . (int) $_POST['group_id'] . "' ");

                $sql_arr = array(
                    'access_leads' => $row['access_leads']
                    , 'access_quotes' => $row['access_quotes']
                    , 'access_orders' => $row['access_orders']
                    , 'access_accounts' => $row['access_accounts']
                    , 'access_dispatch' => $row['access_dispatch']
                    , 'access_payments' => $row['access_payments']
                    , 'access_lead_sources' => $row['access_lead_sources']
                    , 'access_reports' => $row['access_reports']
                    , 'access_users' => $row['access_users']
                    , 'access_preferences' => $row['access_preferences']
                    , 'group_id' => $row['id'],
                );

                $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sql_arr);
                $this->updateHistory($sql_arr1, $ID);
                $this->daffny->DB->update("members", $sql_arr1, "id = '" . $ID . "'");
                $this->setFlashInfo("Group Privileges have been assigned.");
                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("users", "privileges", "id", $ID));
            }
            $this->setFlashError($this->err);
            redirect(getLink("users", "privileges", "id", $ID));
        } else {
            $this->setFlashError("Bad request.");
            redirect(getLink("users"));
        }
    }

    public function show()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users.details";
        $this->title .= " - Details";

        $this->check_access("user", "show", array('id' => $ID));

        $sql = "SELECT m.*
					 , DATE_FORMAT(m.reg_date, '%m/%d/%Y %H:%i:%s') reg_date
                       FROM members m
                     WHERE m.id = '" . $ID . "' AND m.parent_id='" . getParentId() . "'";
        $row = $this->daffny->DB->selectRow($sql);
        if (!empty($row)) {
            $this->input = $row;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($this->input['contactname']), '' => "Details"));

            $this->daffny->tpl->logins = array();
            $sql = "SELECT l.*
                     , DATE_FORMAT(l.logintime, '%m/%d/%Y %H:%i:%s') logintime
                  FROM app_members_login_history l
                 WHERE l.member_id='" . $ID . "'
                 LIMIT 0,10
                 ";
            $this->daffny->tpl->logins = $this->daffny->DB->selectRows($sql);
            //login restrictions
            if ($row['loginr_enable'] == 1) {
                $this->input['days_allowed'] = ($row['loginr_day1'] ? "Monday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day2'] ? "Tuesday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day3'] ? "Wednesday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day4'] ? "Thursday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day5'] ? "Friday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day6'] ? "Saturday, " : "");
                $this->input['days_allowed'] .= ($row['loginr_day7'] ? "Sunday, " : "");
                if ($this->input['days_allowed'] == "") {
                    $this->input['days_allowed'] = "None";
                } else {
                    $this->input['days_allowed'] = substr($this->input['days_allowed'], 0, -2);
                }
                $this->input['time_allowed'] = $row['loginr_time_from'] . " - " . $row['loginr_time_to'];
            } else {
                $this->input['days_allowed'] = "All";
                $this->input['time_allowed'] = "All";
            }
        } else {
            $this->setFlashError("Bad user ID.");
            redirect(getLink("users"));
        }
    }

    public function loginhistory()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users.login_history";
        $this->title .= " - Login History";

        $this->check_access("user", "show", array('id' => $ID));

        $sql = "SELECT
					   m.contactname
					 , m.username
                      FROM members m
                     WHERE m.id = '" . $ID . "'";
        $row = $this->daffny->DB->selectRow($sql);
        $this->input = $row;
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($this->input['contactname']), '' => "Login History"));

        $this->applyPager("app_members_login_history l", "", "WHERE l.member_id='" . $ID . "'");
        $this->applyOrder("app_members_login_history");

        $sql = "SELECT l.*
                     , DATE_FORMAT(l.logintime, '%m/%d/%Y %H:%i:%s') logintime
                  FROM app_members_login_history l
                 WHERE l.member_id='" . $ID . "' "
        . $this->order->getOrder()
        . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function restrictions()
    {
        $ID = (int) get_var("id");

        if ($ID <= 0) {
            $this->setFlashError("Bad user ID.");
            redirect(getLink("users"));
        }
        $this->tplname = "users.users.login_restrictions";
        $this->title = "Login Restrictions";
        $this->check_access("user", "edit", array('id' => $ID));
        $this->input = $this->SaveFormVars();
        $dsbl = array();

        if (isset($_POST['submit'])) {
            $sql_arr = array(
                'loginr_enable' => (post_var("loginr_enable") == "1" ? 1 : 0)
                , 'loginr_day1' => (post_var("loginr_day1") == "1" ? 1 : 0)
                , 'loginr_day2' => (post_var("loginr_day2") == "1" ? 1 : 0)
                , 'loginr_day3' => (post_var("loginr_day3") == "1" ? 1 : 0)
                , 'loginr_day4' => (post_var("loginr_day4") == "1" ? 1 : 0)
                , 'loginr_day5' => (post_var("loginr_day5") == "1" ? 1 : 0)
                , 'loginr_day6' => (post_var("loginr_day6") == "1" ? 1 : 0)
                , 'loginr_day7' => (post_var("loginr_day7") == "1" ? 1 : 0)
                , 'loginr_time_from' => post_var("loginr_time_from")
                , 'loginr_time_to' => post_var("loginr_time_to"),
            );

            if (!count($this->err)) {
                $sql_arr1 = $this->daffny->DB->PrepareSql("members", $sql_arr);
                $this->updateHistory($sql_arr1, $ID);
                $this->daffny->DB->update("members", $sql_arr1, "id = $ID");
                $this->setFlashInfo("Information has been updated.");
                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("users", "show", "id", $ID));
            }
        } else {
            $sql = "SELECT m.*
                      FROM members m
                     WHERE m.id = '" . $ID . "' AND m.parent_id='" . getParentId() . "'";
            $row = $this->daffny->DB->selectRow($sql);
            $this->input = $row;
            if ($this->input['id'] == $this->input['parent_id']) {
                $this->input['loginr_enable'] = 0;
                $dsbl = array("disabled" => "disabled"); //Disable if admin
            }
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($row['contactname']), '' => "Login Restrictions "));
        }

        $this->form->CheckBox("loginr_enable", $dsbl, "Enable login restrictions", "&nbsp;");
        $this->form->CheckBox("loginr_day1", $dsbl, "Monday", "&nbsp;");
        $this->form->CheckBox("loginr_day2", $dsbl, "Tuesday", "&nbsp;");
        $this->form->CheckBox("loginr_day3", $dsbl, "Wednesday", "&nbsp;");
        $this->form->CheckBox("loginr_day4", $dsbl, "Thursday", "&nbsp;");
        $this->form->CheckBox("loginr_day5", $dsbl, "Friday", "&nbsp;");
        $this->form->CheckBox("loginr_day6", $dsbl, "Saturday", "&nbsp;");
        $this->form->CheckBox("loginr_day7", $dsbl, "Sunday", "&nbsp;");
        $this->form->TextField("loginr_time_from", 5, $dsbl + array("style" => "", "" => ""), "From", "</td><td>");
        $this->form->TextField("loginr_time_to", 5, $dsbl + array("style" => "", "" => ""), "To", "</td><td>");
    }

    public function userhistory()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users.userhistory";
        $this->title .= " - History";
        $this->check_access("user", "show", array('id' => $ID));
        $sql = "SELECT
					   m.contactname
					 , m.username
                      FROM members m
                     WHERE m.id = '" . $ID . "'";
        $row = $this->daffny->DB->selectRow($sql);
        $this->input = $row;
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users", "show", "id", $ID) => htmlspecialchars($this->input['contactname']), '' => "User History"));

        $this->applyPager("app_members_history h", "", "WHERE h.member_id='" . $ID . "'");
        $this->applyOrder("app_members_history");

        $sql = "SELECT h.*
					 , m.contactname AS changed_by
					 , f.commonname AS field_name
                     , DATE_FORMAT(h.change_date, '%m/%d/%Y %H:%i:%s') change_date
                  FROM app_members_history h
                  LEFT JOIN members m ON h.changed_by = m.id
                  LEFT JOIN app_fields f ON f.name = h.field_name AND f.table_name = 'members'
                 WHERE h.member_id='" . $ID . "' "
        . $this->order->getOrder()
        . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function history()
    {
        $this->tplname = "users.users.history";
        $this->title .= " - Users History";
        $this->check_access("user", "history", array());
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", '' => "User's History"));

        $this->applyPager("app_members_paid p", "", "WHERE p.owner_id='" . getParentId() . "'");
        $this->applyOrder("app_members_paid");

        $sql = "SELECT
		               p.*
					 , m2.username AS username
					 , m.contactname AS changed_by
                     , DATE_FORMAT(p.update_date, '%m/%d/%Y %H:%i:%s') update_date
                  FROM app_members_paid p
                  LEFT JOIN members m ON p.changed_by = m.id
                  LEFT JOIN members m2 ON p.member_id = m2.id
                 WHERE p.owner_id='" . getParentId() . "' "
        . $this->order->getOrder()
        . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function prepareDelete()
    {
        $out = array('success' => false);
        $id = mysqli_real_escape_string($this->daffny->DB->connection_id, post_var('id'));
        $parent_id = $this->daffny->DB->selectValue('parent_id', Member::TABLE, 'WHERE id != parent_id AND id = ' . $id);
        if (!$parent_id) {
            throw new FDException('Invalid User ID');
        }
        $members = $this->daffny->DB->selectRows('id, contactname as name', Member::TABLE, 'WHERE parent_id = ' . $parent_id . ' AND id != ' . $id);
        if ($members) {
            $out = array(
                'success' => true,
                'members' => $members,
            );
        }
        die(json_encode($out));
    }

    public function delete()
    {
        $out = array('success' => false);
        $this->daffny->DB->transaction("start");
        try {
            $ID = mysqli_real_escape_string($this->daffny->DB->connection_id, post_var('id'));
            $this->check_access("user", "delete", array('id' => $ID));
            $this->daffny->DB->update(Entity::TABLE, array('assigned_id' => post_var('leads_id')), 'assigned_id = ' . $ID . ' AND type = ' . Entity::TYPE_LEAD);
            $this->daffny->DB->update(Entity::TABLE, array('assigned_id' => post_var('quotes_id')), 'assigned_id = ' . $ID . ' AND type = ' . Entity::TYPE_QUOTE);
            $this->daffny->DB->update(Entity::TABLE, array('assigned_id' => post_var('orders_id')), 'assigned_id = ' . $ID . ' AND type = ' . Entity::TYPE_ORDER);

            if ($this->daffny->DB->delete(Member::TABLE, "id = '$ID' AND parent_id <> '$ID'")) {
                $out = array('success' => true);
                $this->daffny->DB->transaction("commit");
            }

        } catch (Exception $e) {
            $this->daffny->DB->transaction("rollback");
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

    protected function updateHistory($new_arr, $ID)
    {
        $ins_arr = array();
        $old_arr = $this->daffny->DB->select_one("*", "members", "WHERE id = '" . $ID . "'");
        $change_date = date("Y-m-d H:i:s");
        $changed_by = $_SESSION['member_id'];
        foreach ($old_arr as $key => $value) {
            if (isset($new_arr[$key])) {

                if ($new_arr[$key] != $old_arr[$key]) {
                    if ($key == 'group_id') {
                        $old_arr[$key] = $this->getGroupNameById($old_arr[$key]);
                        $new_arr[$key] = $this->getGroupNameById($new_arr[$key]);
                    }
                    $ins_arr[] = array(
                        "member_id" => $ID
                        , "field_name" => $key
                        , "old_value" => ($key == 'password' ? 'Old value' : $old_arr[$key])
                        , "new_value" => ($key == 'password' ? 'New value' : $new_arr[$key])
                        , "change_date" => $change_date
                        , "changed_by" => $changed_by,
                    );
                }
            }
        }

        if (!empty($ins_arr)) {
            foreach ($ins_arr as $key => $arr) {
                $this->daffny->DB->insert("app_members_history", $arr);
            }
        }
    }

    protected function getGroups($empty = false)
    {
        $groups = array();
        if ($empty) {
            $groups[''] = "--Select one--";
        }
        $result = $this->daffny->DB->selectRows("id, name", "app_members_groups", "WHERE owner_id='" . getParentId() . "' ORDER BY name", "id");
        foreach ($result as $key => $values) {
            $groups[$key] = htmlspecialchars($values['name']);
        }
        return $groups;
    }

    protected function getGroupNameById($id)
    {
        return $this->daffny->DB->selectField("name", "app_members_groups", "WHERE id = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $id) . "'");
    }
    private function getUsersByStatus($where)
    {
        $member = new Member();
        return $member->getCompanyMembersByStatus($this->daffny->DB, getParentId(), $where, true);
    }

}
