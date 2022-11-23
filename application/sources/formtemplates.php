<?php

/**
 * Class managing the form templates inside the system
 * predefined html layouts with hooksto hold different values
 * 
 * @author Karakoram
 * @version 1.0.2
 */
class ApplicationFormtemplates extends ApplicationAction
{

    // class level variables holding screen window information
    public $title = "Settings";
    public $section = "Form Templates";
    public $tplname = "settings.formtemplates";

    /**
     * default constructor of the form template class
     * @version     1.0.2
     * @return      Void
     */
    public function construct()
    {
        // access validation
        if (!$this->check_access('preferences')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    /**
     * List all form templates in the system
     * 
     * @version     1.0.2
     * @return      Void
     */
    public function idx()
    {
        // loading breadcrumbs
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", '' => "Form templates"));
        // loading view file
        $this->tplname = "settings.formtemplates.list";
        // applying pagination
        $this->applyPager("app_formtemplates", "", "WHERE owner_id='" . getParentID() . "'");
        $this->applyOrder("app_formtemplates");

        // applying pagination to the fetched data
        $sql = "SELECT * FROM app_formtemplates WHERE owner_id = '" . getParentID() . "' " . $this->order->getOrder() . $this->pager->getLimit();

        // setting up data on the view
        $this->getGridData($sql, false);
    }

    /**
     * Edit the system templates individually
     * 
     * @version     1.0.2
     * @return      Void
     */
    public function edit()
    {
        // pivot template id
        $ID = (int) get_var("id");

        // loading templates
        $this->tplname = "settings.formtemplates.form";
        // setting page title
        $this->title .= ($ID > 0 ? " - Edit Template" : " - Add New Template");
        // check edit access
        $this->check_access("formtemplates", "edit", array("id" => $ID));

        //check if editable
        $row = array();
        $is_system = false;
        if ($ID > 0) {
            $sql = "SELECT * FROM app_formtemplates WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'";
            $row = $this->daffny->DB->selectRow($sql);
            if ($row['is_system'] == 1) {
                $is_system = true;
            }
        }

        if (!$is_system) {
            $sql_arr = array(
                'name' => post_var("name"),
                'description' => post_var("description"),
                'usedfor' => post_var("usedfor"),
                'body' => post_var("body")
            );
            $this->input = $sql_arr;
        } else {
            $sql_arr = array(
                'body' => post_var("body")
            );
            $this->input = $sql_arr;
        }

        if (isset($_POST['submit'])) {
            $sql_arr1 = $this->daffny->DB->PrepareSql('app_formtemplates', $sql_arr);

            if (!$is_system) {
                $this->isEmpty("name", "Name");
                $this->isEmpty("description", "Description");
                $this->isEmpty("usedfor", "Used for");
            }

            if (!count($this->err)) {
                if ($ID > 0) {

                    $this->daffny->DB->update("app_formtemplates", $sql_arr1, "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
                    $this->setFlashInfo("Template has been updated.");
                } else {
                    $sql_arr1['owner_id'] = getParentId();
                    $this->daffny->DB->insert("app_formtemplates", $sql_arr1);
                    $this->setFlashInfo("Template has been added.");
                    $id = $this->daffny->DB->get_insert_id();
                }
                if ($this->dbError()) {
                    return;
                }
                redirect(getLink("formtemplates"));
            }
        } else {
            if ($ID > 0) {
                $this->input = $row;
            }
        }

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("formtemplates") => "Form Templates", '' => ($ID > 0 ? $this->input['name'] : "Add New")));

        foreach ($this->input as $key => $value) {
            $this->input[$key] = htmlspecialchars($value);
        }
        $this->input['usedfor_txt'] = $this->input['usedfor'];
        if ($this->input['usedfor_txt'] == "") {
            $this->input['usedfor_txt'] = "orders";
        }

        if (!$is_system) {
            $this->form->TextField("name", 255, array(), "", "");
            $this->form->TextField("description", 255, array(), "", "");
            $this->form->ComboBox("usedfor", array("orders" => "Orders", "quotes" => "Quotes"), array(), "", "");
        }
        $this->form->TextArea("body", 700, 300);
        $this->daffny->tpl->is_system = $is_system;

        $this->daffny->tpl->attachments = array();
        $sql = "SELECT e.name FROM app_emailtemplates_att a
						LEFT JOIN app_emailtemplates e ON e.id = a.template_id
						WHERE a.owner_id = '" . getParentId() . "' AND a.form_id = '" . $ID . "'";
        $q = $this->daffny->DB->query($sql);
        while ($attachments = $this->daffny->DB->fetch_row($q)) {
            $this->daffny->tpl->attachments[] = $attachments['name'];
        }

    }

    /**
     * Details page for a specific template based on id
     * 
     * @version     1.0.2
     * @return      Void
     */
    public function show()
    {
        $ID = $this->checkId();
        $this->check_access("formtemplates", "show", array("id" => $ID));
        $sql = "SELECT * FROM app_formtemplates WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'";
        $row = $this->daffny->DB->selectRow($sql);

        if (!empty($row)) {
            echo $row['body'];
        }

        exit;
    }

    /**
     * action to revert back the changed being done in the template
     * 
     * @version     1.0.2
     * @return      Void
     */
    public function revert()
    {
        $ID = $this->checkId();
        $this->check_access("formtemplates", "show", array("id" => $ID));
        $sql = "SELECT *
                         FROM app_formtemplates
                        WHERE id = (SELECT sys_id
                                     FROM app_formtemplates
                                     WHERE id='" . $ID . "' AND owner_id = '" . getParentId() . "')";
        $row = $this->daffny->DB->selectRow($sql);
        if (!empty($row)) {

            $this->daffny->DB->update("app_formtemplates", array("body" => $row['body']), "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
            $this->setFlashInfo("Temlates' body has been reverted to original.");
            redirect(getLink("formtemplates", "edit", "id", $ID));
        } else {
            $this->setFlashError("Access denied");
            redirect(getLink("formtemplates"));
        }
    }

    /**
     * action to delete specific template based on template id
     * 
     * @version     1.0.2
     * @return      Void
     */
    public function delete()
    {
        $ID = $this->checkId();
        $out = array('success' => false);
        try {
            $this->daffny->DB->delete("app_formtemplates", "id = '" . $ID . "' AND is_system <> 1 AND owner_id = '" . getParentId() . "'");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            } else {
                $this->daffny->DB->delete("app_emailtemplates_att", "form_id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
                if ($this->daffny->DB->isError) {
                    throw new Exception($this->getDBErrorMessage());
                } else {
                    $out = array('success' => true);
                }
            }
        } catch (FDException $e) {

        }
        die(json_encode($out));
    }

}
