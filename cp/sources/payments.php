<?php

/* * *************************************************************************************************
 * Show Payments
 *
 *
 * Client: 	FreightDragon
 * Version: 	1.0
 * Date:    	2012-02-13
 * Author:  	C.A.W., Inc. dba INTECHCENTER
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:	techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

class CpPayments extends CpAction {

    public $title = "Payments";
    public $section = "Payments";
    public $tplname = "payments.list";

    public function idx() {
        try {
            $ID = (int) get_var("member_id"); //member ID
            $this->tplname = "payments.list";
            $this->title = "Payments";

            $this->applyOrder(Billing::TABLE);
            $this->order->setDefault('id', 'desc');

            //build where
            $where_arr = array();
            $where = "";
            if (isset($_GET['start_date']) && trim($_GET['start_date']) != "") {
                $this->input["start_date"] = str_replace('-', '/',$_GET['start_date']);
                $start_date = $this->validateDate($this->input["start_date"], "Start Date") . " 00:00:00";
                $where_arr[] = " added >= '" . $start_date . "'";
            }
            if (isset($_GET['end_date']) && trim($_GET['end_date']) != "") {
                $this->input["end_date"] = str_replace('-', '/',$_GET['end_date']);
                $end_date = $this->validateDate($this->input["end_date"], "End Date") . " 23:59:59";
                $where_arr[] = " added <= '" . $end_date . "'";
            }
            if (isset($_GET['type']) && trim($_GET['type']) != "") {
                $this->input["type"] = $_GET['type'];
                $where_arr[] = "type = '" . (int) $_GET['type'] . "'";
            }

            if ($ID > 0) { //by member
                $where_arr[] = "owner_id='" . $ID . "'";
                $this->title .= " (By Company)";
            }

            if (count($where) > 0) {
                $where = implode(" AND ", $where_arr);
            }

            $this->daffny->tpl->transactions = array();
            if (!count($this->err)) {
                $billingm = new BillingManager($this->daffny->DB);
                $billings = $billingm->get($this->order->getOrder(), $_SESSION['per_page'], $where);
                $this->setPager($billingm->getPager());
                $this->daffny->tpl->transactions = $billings;
            } else {
                $this->input["pager"] = "";
            }
            //input fields
            
            $this->form->DateField("start_date", 10, array(), "", "");
            $this->form->DateField("end_date", 10, array(), "", "");
            $this->form->ComboBox("type", array("" => "--All Types--") + Billing::$type_name, array("style" => "width:150px;"), "Type", "</td><td colspan=\"3\">");
            
        } catch (FDException $e) {
            $this->setFlashError("Internal error. Please try later.");
            redirect(SITE_IN . "cp/");
        }
    }

}

?>