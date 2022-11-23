<?php

class Applicationreferrers extends ApplicationAction
{
    public $title = "Referrers";
    public $section = "Referrers";
    public $tplname = "accounts.referrers.list";

    /**
     * List all
     *
     */
    public function idx()
    {
        try {
            $this->breadcrumbs = $this->getBreadCrumbs(array('' => "Referrers"));
            $this->tplname = "accounts.referrers.list";
            $referrerManager = new ReferrerManager($this->daffny->DB);
            $this->applyOrder("app_referrers");
            $referrers = $referrerManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId());
            $this->setPager($referrerManager->getPager());
            $this->daffny->tpl->referrers = $referrers;

            $row = $this->daffny->DB->select_one("referrer_status", "app_defaultsettings", "WHERE  owner_id = '" . getParentId() . "'");
            if (!empty($row)) {
                $this->daffny->tpl->referrer_status = $row['referrer_status'];
            } else {
                $this->daffny->tpl->referrer_status = 0;
            }

        } catch (FDException $e) {
    		redirect(getLink(''));
        }
    }
    /**
     * Edit
     *
     */
    public function edit()
    {
        try {
            $ID = (int) get_var("id");
            $referrer = new Referrer($this->daffny->DB);
            $this->tplname = "accounts.referrers.form";
            $this->title .= ($ID > 0 ? " - Edit Referrer" : " - Add New Referrer");
            $this->check_access("referrers", "edit", array("id" => $ID));
            $sql_arr = array(
                'name' => post_var("name")
                , 'status' => post_var("status")
                , 'salesrep' => post_var("salesrep")
                , 'description' => post_var("description")
                , 'commission' => post_var("commission")
                , 'intial_percentage' => post_var("intial_percentage")
                , 'residual_percentage' => post_var("residual_percentage"),
            );

            $this->input = $sql_arr;
            if (isset($_POST['submit'])) {
                $this->isEmpty("name", "Name");
                $this->isEmpty("status", "Status");

                if (!count($this->err)) {
                    if ($ID > 0) {
                        $referrer->update($sql_arr, $ID);
                        $this->setFlashInfo("Referrer has been updated.");
                    } else {
                        $sql_arr['owner_id'] = getParentId();
                        $referrer->create($sql_arr);
                        $this->setFlashInfo("Referrer has been added.");
                        $ID = $referrer->id;
                    }
                    if ($this->dbError()) {
                        return;
                    }
                    redirect(getLink("referrers"));
                }
            } else {
                if ($ID > 0) {
                    $referrer->load($ID);
                    if ($referrer->owner_id != getParentId()) {
                        $this->setFlashError("Access denied.");
                        redirect(getLink('referrers'));
                    }
                    $this->input = $referrer->getAttributes();

                    $this->daffny->tpl->salesrep = $referrer->salesrep;
                }
            }

            $commissionArr = array();
            for ($i = 1; $i <= 100; $i++) {
                $commissionArr[$i] = $i . " %";
            }

            $commissionArrInitial = array();
            for ($j = 1; $j <= 100; $j++) {
                $commissionArrInitial[$j] = $j . " %";
            }

            $commissionArrResidual = array();
            for ($k = 1; $k <= 100; $k++) {
                $commissionArrResidual[$k] = $k . " %";
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("referrers") => "Referrers", '' => ($ID > 0 ? $referrer->name : "Add New Referrer")));
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->form->TextField("name", 255, array(), $this->requiredTxt . "Name", "</td><td>");
            $this->form->TextArea("description", 15, 10, array("style" => "height:100px;"), "Description", "</td><td>");
            $this->form->ComboBox("status", array(Referrer::STATUS_ACTIVE => Referrer::$status_name[Referrer::STATUS_ACTIVE], Referrer::STATUS_INACTIVE => Referrer::$status_name[Referrer::STATUS_INACTIVE]), array('style' => ""), $this->requiredTxt . "Status", "</td><td>");
            $this->form->ComboBox("intial_percentage", array("" => "Select Intial Commission") + $commissionArrInitial, array("style" => ""), $this->requiredTxt . "Initial %age", "</td><td>");
            $this->form->ComboBox("residual_percentage", array("" => "Select Residual Commission") + $commissionArrResidual, array("style" => ""), $this->requiredTxt . "Residual %age", "</td><td>");
            $this->form->ComboBox("commission", array("" => "Select Commission") + $commissionArr, array("style" => ""), "Sales Rep Commission", "</td><td>");

        } catch (FDException $e) {
            redirect(getLink('referrers'));
        }
    }

    public function delete()
    {
        $out = array('success' => false);
        try {
            $ID = $this->checkId();
            $this->check_access("referrers", "delete", array("id" => $ID));
            $referrer = new Referrer($this->daffny->DB);
            $referrer->delete($ID);
            $out = array('success' => true);
        } catch (FDException $e) {}
        die(json_encode($out));
    }

    public function status()
    {
        $out = array('success' => false);
        try {
            $id = $this->checkId();
            $this->check_access("referrers", "update", array("id" => $id));
            $referrer = new Referrer($this->daffny->DB);
            $referrer->load($id);
            $referrer->update(array('status' => ($referrer->status == 1) ? 0 : 1), $id);
            $out = array('success' => true);
        } catch (FDException $e) {}
        die(json_encode($out));
    }
}
