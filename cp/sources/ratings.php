<?php

/* * *************************************************************************************************
 * Manage Ratings
 *
 *
 * Client: 	FreightDragon
 * Version: 	1.0
 * Date:    	2011-02-17
 * Author:  	C.A.W., Inc. dba INTECHCENTER
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:
 * @property mixed breadcrumbs
  techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

class CpRatings extends CpAction {

    public $title = "Ratings";
    public $tplname = "ratings.list";

    /**
     * List all
     *
     */
    public function idx() {
        $this->tplname = "ratings.list";
        $this->applyOrder(Rating::TABLE);
        $this->order->setDefault('id', 'desc');
        $rm = new RatingManager($this->daffny->DB);
        $where = array();

        if (isset($_GET["member_id"])) {
            $where[] = "WHERE to_id = '" . (int) $_GET["member_id"] . "'";
            $this->title .= " (By Company)";
        }

        if (isset($_GET['start_date']) && trim($_GET['start_date']) != "") {
            $this->input["start_date"] = str_replace('-', '/',$_GET['start_date']);
            $start_date = $this->validateDate($this->input["start_date"], "Start Date") . " 00:00:00";
            $where[] = " added >= '" . $start_date . "'";
        }

        if (isset($_GET['end_date']) && trim($_GET['end_date']) != "") {
            $this->input["end_date"] = str_replace('-', '/',$_GET['end_date']);
            $end_date = $this->validateDate($this->input["end_date"], "End Date") . " 00:00:00";
            $where[] = " added <= '" . $end_date . "'";
        }

        if (isset($_GET['status']) && trim($_GET['status']) != "") {
            $where[] = "status = '".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['status'])."'";
            $this->input['status'] = $_GET['status'];
        }

        if (isset($_GET['from']) && trim($_GET['from']) != "") {
            $this->input['from'] = $_GET['from'];
            $from = $this->daffny->DB->selectValue("id", "app_company_profile", "WHERE companyname LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['from'])."%'");
            $where[] = "from_id = "  . $from ? $from : 'null' ;
        }

        if (isset($_GET['to']) && trim($_GET['to']) != "") {
            $this->input['to'] = $_GET['to'];
            $from = $this->daffny->DB->selectValue("id", "app_company_profile", "WHERE companyname LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['to'])."%'");
            $where[] = "from_id = "  . $from ? $from : 'null' ;
        }

        $whereStr = "";
        if (count($where)) {
            $whereStr = 'WHERE '.implode(' AND ', $where);
        }

        $ratings = $rm->get($this->order->getOrder(), $_SESSION['per_page'], $whereStr);
        $this->setPager($rm->getPager());
        $this->daffny->tpl->data = $ratings;


        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->ComboBox('status', array(
            Rating::STATUS_ACTIVE => 'Active',
            Rating::STATUS_INACTIVE => 'Inactive',
            Rating::STATUS_PENDING => 'Pending',
            null => '',
        ), array(), "Status", "</td><td>");
        $this->form->TextField('from', 32, array(), 'From', "</td><td>");
        $this->form->TextField('to', 32, array(), 'To', "</td><td>");
    }

    /**
     * Edit
     *
     */
    public function editrating() {
        try {
            $ID = (int) get_var("id");
            $this->tplname = "ratings.form";
            $this->title .= " - Edit";
            $rating = new Rating($this->daffny->DB);
            $rating->load($ID);
            $comments = array();
            if (isset($_POST['comments'])){
                foreach ($_POST['comments'] as $key => $value) {
                    $comments[] = $value;
                }
            }
            $sql_arr = array(
                  'type' => (int) post_var("type")
                , "commentids" => implode(",", $comments)
                , 'status' => (int) post_var("status")
            );
            $this->input = $sql_arr;
            if (isset($_POST['submit'])) {
                $this->isEmpty("type", "Rating");
                $this->isEmpty("status", "Status");
                if (!count($this->err)) {
                    $rating->update($sql_arr);
                    $this->setFlashInfo("Rating has been updated.");
                    redirect(getLink("ratings"));
                }
            } else {
                $this->input = array(
                    "type" => $rating->type
                    , "commentids" => $rating->commentids
                    , "status" => $rating->status
                );
            }
            
            $this->input["to"] = $rating->getTo()->companyname;
            $this->input["from"] = $rating->getFrom()->companyname;
                    
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->form->ComboBox("type", Rating::$type_name, array('style' => "width: 100px;"), $this->requiredTxt . "Rating", "</td><td>");
            $this->form->ComboBox("status", Rating::$status_name, array('style' => "width: 100px;"), $this->requiredTxt . "Status", "</td><td>");


            //Build comments
            $this->daffny->tpl->comments = array();
            $ch_arr = @explode(",", $this->input["commentids"]);
            $rc = new RatingcommentsManager($this->daffny->DB);
            $coms = $rc->getCommentsList();
            
            foreach ($coms AS $c) {
                $comments = array();
                $comments["id"] = $c->id;
                $comments["name"] = $c->name;
                if (in_array($c->id, $ch_arr)) {
                    $comments['ch'] = "checked=\"checked\"";
                } else {
                    $comments['ch'] = "";
                }
                $this->daffny->tpl->comments[] = $comments;
            }
        } catch (FDException $e) {
            $this->setFlashError("System Error. Please try later.");
            redirect(getLink("ratings"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function deleterating() {
        try {
            $ID = $this->checkId();
            $rating = new Rating($this->daffny->DB);
            $rating->load($ID);
            $rating->delete(null, true);
            $out = array('success' => true);
        } catch (FdException $e) {
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

}

?>