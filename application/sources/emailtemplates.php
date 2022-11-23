<?php

class ApplicationEmailtemplates extends ApplicationAction
{

    public $title = "Settings";
    public $section = "Email Templates";
    public $tplname = "settings.emailtemplates";

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
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", '' => "Form templates"));
        $this->tplname = "settings.emailtemplates.list";

        $this->applyPager("app_emailtemplates", "", "WHERE owner_id='" . getParentID() . "'");
        $this->applyOrder("app_emailtemplates");

        $sql = "SELECT * ,IF(send_type=0, 'TEXT', 'HTML') AS send_type FROM app_emailtemplates WHERE owner_id = '" . getParentID() . "' " . $this->order->getOrder() . $this->pager->getLimit();
        $this->getGridData($sql, false);
    }

    public function edit()
    {
        
            $ID = (int) get_var("id");

            $this->tplname = "settings.emailtemplates.form";
            $this->title .= ($ID > 0 ? " - Edit Template" : " - Add New Template");
            $this->check_access("emailtemplates", "edit", array("id" => $ID));

            //check if editable
            $emailTemplate = array();
            $is_system = false;
            if ($ID > 0) {
                $sql = "SELECT *
                                FROM app_emailtemplates
                            WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'";
                $emailTemplate = $this->daffny->DB->selectRow($sql);
                if ($emailTemplate['is_system'] == 1) {
                    $is_system = true;
                }
            }

            if (!$is_system) {
                $sql_arr = array(
                    'name' => post_var("name")
                    , 'description' => post_var("description")
                    , 'usedfor' => post_var("usedfor")
                    , 'is_followup' => (post_var("is_followup") == "1" ? 1 : 0)
                    , 'to_address' => post_var("to_address")
                    , 'from_address' => post_var("from_address")
                    , 'from_name' => post_var("from_name")
                    , 'subject' => post_var("subject")
                    , 'send_type' => post_var("send_type")
                    , 'body_text' => post_var("body_text")
                    , 'body_html' => post_var("body_html")
                    , 'bcc_addresses' => post_var("bcc_addresses")
                    , 'body' => post_var("body"),
                );
                $this->input = $sql_arr;
            } else {
                $sql_arr = array(
                    'to_address' => post_var("to_address")
                    , 'from_address' => post_var("from_address")
                    , 'from_name' => post_var("from_name")
                    , 'subject' => post_var("subject")
                    , 'send_type' => post_var("send_type")
                    , 'body_text' => post_var("body_text")
                    , 'body_html' => post_var("body_html")
                    , 'bcc_addresses' => post_var("bcc_addresses")
                    , 'body' => post_var("body"),
                );
                $this->input = $sql_arr;
            }

            if (isset($_POST['submit'])) {

                if (!$is_system) {
                    $this->isEmpty("name", "Name");
                    $this->isEmpty("usedfor", "Used for");
                }

                $this->isEmpty("to_address", "To Address");
                $this->isEmpty("from_address", "From Address");
                $this->isEmpty("from_name", "From Name");
                $this->isEmpty("subject", "Subject");
                $this->isEmpty("send_type", "Send Email Using");

                //check BCC emails
                if (trim(post_var('bcc_addresses')) != "") {
                    $email_bcc = "";
                    $emails = @explode(",", post_var('bcc_addresses'));
                    if (count($emails) > 0) {
                        $er = false;
                        foreach ($emails as $key => $email) {
                            if (!validate_email(trim($email))) {
                                $er = true;
                            } else {
                                $email_bcc[] = trim($email);
                            }
                        }

                        if ($er) {
                            $this->err[] = "Field <strong>'BCC Address(es):'</strong> has invalid emails.";
                        } else {
                            $sql_arr['bcc_addresses'] = implode(", ", $email_bcc);
                        }
                    } else {
                        $this->err[] = "Field <strong>'BCC Address(es):'</strong> has bad format. Separate multiple addresses with commas.";
                    }
                }

                if (!count($this->err)) {
                    try{
                    $sql_arr1 = $this->daffny->DB->PrepareSql('app_emailtemplates', $sql_arr);

                    if ($ID > 0) {
                        $this->daffny->DB->update("app_emailtemplates", $sql_arr1, "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
                        $this->setFlashInfo("Template has been updated.");
                    } else {
                        
                        $sql_arr1['owner_id'] = getParentId();
                        $this->daffny->DB->insert("app_emailtemplates", $sql_arr1);
                        $this->setFlashInfo("Template has been added.");
                        $ID = $this->daffny->DB->get_insert_id();
                    }
                    if ($this->dbError()) {
                        return;
                    }

                    //Save attachments
                    if ($_POST['attach_type'] > 0) {
                        $sql = "UPDATE app_emailtemplates SET attach_type=1 WHERE owner_id=" . getParentId();
                        $this->daffny->DB->query($sql);
                    }

                    if ($_POST['attach_type'] == 0) {
                        $sql = "UPDATE app_emailtemplates SET attach_type=0 WHERE owner_id=" . getParentId();
                        $this->daffny->DB->query($sql);
                    }

                    $this->daffny->DB->delete("app_emailtemplates_att", "template_id='" . $ID . "'");
                    if (isset($_POST['attachments'])) {
                        foreach ($_POST['attachments'] as $key => $value) {
                            if (trim($value) != "") {
                                $arr = array("template_id" => $ID
                                    , "form_id" => (int) $value
                                    , "owner_id" => getParentId(),
                                );
                                $this->daffny->DB->insert("app_emailtemplates_att", $arr);
                            }
                        }
                    }

                    } catch(Exception $e){
                        echo "<pre>";
                        print_r($e);
                        die("Exception Occured");
                    }
                    redirect(getLink("emailtemplates"));
                }
            } else {
                if ($ID > 0) {
                    $this->input = $emailTemplate;
                }
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("emailtemplates") => "Email Templates", '' => ($ID > 0 ? $this->input['name'] : "Add new")));

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
                $this->form->ComboBox("usedfor", array("orders" => "Orders", "quotes" => "Quotes"), array("style" => "width:75px;"), "", "");
                $this->form->CheckBox("is_followup", array(), "Use as a follow-up", "&nbsp;");
            }
            $this->form->TextField("to_address", 255, array(), $this->requiredTxt . "To Address", "</td><td>");
            $this->form->TextField("from_address", 255, array(), $this->requiredTxt . "From Address", "</td><td>");
            $this->form->TextField("from_name", 255, array(), $this->requiredTxt . "From Name", "</td><td>");
            $this->form->TextField("subject", 255, array(), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->helperSendType("send_type", array());

            $this->form->Editor("body_html", 500, 200);
            $this->form->TextArea("body_text", 15, 10, array("class"=>"ckeditor","style" => "height:200px; width:500px;"), "Body (TEXT)", "</td><td>");
            $this->form->TextField("bcc_addresses", 255, array(), "BCC Address(es)", "</td><td>");
            $this->daffny->tpl->is_system = $is_system;

            //check for the type for sending
            $sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =" . getParentId();
            $q = $this->daffny->DB->query($sql);

            while ($row = $this->daffny->DB->fetch_row($q)) {
                if ($row['attach_type'] > 0) {
                    $this->daffny->tpl->attach_typePDF = "checked=\"checked\"";
                } else {
                    $this->daffny->tpl->attach_typeHTML = "checked=\"checked\"";
                }
            }

            //build attachments
            $ch_arr = array();
            if (isset($_POST['attachments'])) {
                foreach ($_POST['attachments'] as $key => $value) {
                    if (trim($value) != "") {
                        $ch_arr[] = (int) $value;
                    }
                }
            } else {
                $q = $this->daffny->DB->select("*", "app_emailtemplates_att", "WHERE template_id = '" . $ID . "'");
                while ($row = $this->daffny->DB->fetch_row($q)) {
                    $ch_arr[] = $row['form_id'];
                }
            }
            $this->daffny->tpl->attachments = array();
            if ($emailTemplate['sys_id'] != EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $sql = "SELECT * FROM app_formtemplates WHERE owner_id = '" . getParentId() . "' ORDER BY id";
                $q = $this->daffny->DB->query($sql);
                while ($attachments = $this->daffny->DB->fetch_row($q)) {
                    if (in_array($attachments['id'], $ch_arr)) {
                        $attachments['ch'] = "checked=\"checked\"";
                    } else {
                        $attachments['ch'] = "";
                    }
                    $this->daffny->tpl->attachments[] = $attachments;
                }
            } else {
                $this->daffny->tpl->attachments[] = "Dispatch Sheet";
            }
    }

    public function show()
    {
        $ID = $this->checkId();
        $this->check_access("emailtemplates", "show", array("id" => $ID));
        $sql = "SELECT * FROM app_emailtemplates WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'";
        $row = $this->daffny->DB->selectRow($sql);
        if (!empty($row)) {
            if ($row['send_type'] == 0) {
                echo nl2br($row['body_text']);
            } else {
                echo $row['body_html'];
            }
        }
        exit;
    }

    public function revert()
    {
        $ID = $this->checkId();
        $this->check_access("emailtemplates", "show", array("id" => $ID));
        $sql = "SELECT * FROM app_emailtemplates WHERE id = (SELECT sys_id FROM app_emailtemplates WHERE id='" . $ID . "' AND owner_id = '" . getParentId() . "')";
        $row = $this->daffny->DB->selectRow($sql);
        if (!empty($row)) {
            $upd_arr = array(
                "to_address" => $row['to_address']
                , "description" => $row['description']
                , "from_address" => $row['from_address']
                , "from_name" => $row['from_name']
                , "subject" => $row['subject']
                , "body_text" => $row['body_text']
                , "body_html" => $row['body_html']
                , "bcc_addresses" => $row['bcc_addresses'],
            );
            $this->daffny->DB->update("app_emailtemplates", $upd_arr, "id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
            $this->setFlashInfo("Temlates' body has been reverted to original.");
            redirect(getLink("emailtemplates", "edit", "id", $ID));
        } else {
            $this->setFlashError("Access denied");
            redirect(getLink("emailtemplates"));
        }
    }

    public function delete()
    {

        $ID = $this->checkId();
        $out = array('success' => false);
        try {
            $this->daffny->DB->delete("app_emailtemplates", "id = '" . $ID . "' AND is_system <> 1 AND owner_id = '" . getParentId() . "'");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            } else {
                $this->daffny->DB->delete("app_emailtemplates_att", "template_id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
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
