<?php

class ApplicationUsers_groups extends ApplicationAction
{

    public $title = "User Groups";
    public $section = "User Groups";

    public function construct()
    {
        if (!$this->check_access('users')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    /**
     * List all user groups
     *
     */
    public function idx()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", '' => "User's Groups"));
        $this->tplname = "users.users_groups.list";

        $this->applyPager("app_members_groups", "", "WHERE owner_id='" . getParentID() . "'");
        $this->applyOrder("app_members_groups");

        $sql = "SELECT *
				, DATE_FORMAT(create_date, '%m/%d/%Y %H:%i:%s') create_date
				, DATE_FORMAT(update_date, '%m/%d/%Y %H:%i:%s') update_date
				FROM app_members_groups
				WHERE owner_id='" . getParentID() . "' " . $this->order->getOrder() . $this->pager->getLimit();

        $this->getGridData($sql, false);
    }

    /**
     * Edit
     *
     */
    public function edit()
    {
        $ID = (int) get_var("id");
        $this->tplname = "users.users_groups.form";
        $this->title .= ($ID > 0 ? " - Edit" : " - Add");

        //check access
        $this->check_access("users_groups", "edit", array("id" => $ID));

        $sql_arr = array(
            'access_leads' => post_var("access_leads")
            , 'access_quotes' => post_var("access_quotes")
            , 'access_orders' => post_var("access_orders")
            , 'access_accounts' => post_var("access_accounts")
            , 'access_dispatch' => (post_var("access_dispatch") == "1" ? 1 : 0)
            , 'access_payments' => (post_var("access_payments") == "1" ? 1 : 0)
            , 'access_lead_sources' => (post_var("access_lead_sources") == "1" ? 1 : 0)
            , 'access_reports' => (post_var("access_reports") == "1" ? 1 : 0)
            , 'access_users' => (post_var("access_users") == "1" ? 1 : 0)
            , 'access_preferences' => (post_var("access_preferences") == "1" ? 1 : 0)
            , 'name' => post_var("name"),
        );
        $this->input = $sql_arr;

        if (isset($_POST['submit'])) {
            $sql_arr1 = $this->daffny->DB->PrepareSql('app_members_groups', $sql_arr);
            $this->isEmpty("name", "Name");
            if (!count($this->err)) {
                $sql_arr['owner_id'] = getParentId();
                if ($ID > 0) {
                    $sql_arr1['update_date'] = date("Y-m-d H:i:s");
                    $this->daffny->DB->update("app_members_groups", $sql_arr1, "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
                    $this->setFlashInfo("Information has been updated.");
                } else {
                    $sql_arr1['create_date'] = date("Y-m-d H:i:s");
                    $sql_arr1['owner_id'] = getParentId();
                    $this->daffny->DB->insert("app_members_groups", $sql_arr1);
                    $this->setFlashInfo("Group has been added.");
                    $id = $this->daffny->DB->get_insert_id();
                }

                if ($this->dbError()) {
                    return;
                }

                redirect(getLink("users_groups"));
            }
        } else {
            if ($ID > 0) {
                $sql = "SELECT *
                         FROM app_members_groups
                        WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'";
                $this->input = $this->daffny->DB->selectRow($sql);
            }
        }
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("users") => "Users", getLink("users_groups") => "User's Groups", '' => ($ID > 0 ? $this->input['name'] : "Add New")));

        foreach ($this->input as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }

        $this->form->TextField("name", 100, array(), $this->requiredTxt . "Group Name", "</td><td>");
        $this->form->helperLeads('access_leads');
        $this->form->helperLeads('access_quotes');
        $this->form->helperLeads('access_orders');
        $this->form->helperShippers('access_accounts');
        $this->form->CheckBox("access_dispatch", array(), "Perform Others' Dispatch Activities", "&nbsp;");
        $this->form->CheckBox("access_payments", array(), "Edit Carrier Payment Terms", "&nbsp;");
        $this->form->CheckBox("access_lead_sources", array(), "Access Lead Sources", "&nbsp;");
        $this->form->CheckBox("access_reports", array(), "View reports", "&nbsp;");
        $this->form->CheckBox("access_users", array(), "Add / Edit Users, Groups and Privileges ", "&nbsp;");
        $this->form->CheckBox("access_preferences", array(), "Edit company Preferences", "&nbsp;");
    }

    public function delete()
    {
        $ID = $this->checkId();
        $this->daffny->DB->delete("app_members_groups", "id = $ID");
        $this->daffny->DB->update("members", array("group_id" => 'NULL'), "group_id = $ID");
        exit();
    }

}
