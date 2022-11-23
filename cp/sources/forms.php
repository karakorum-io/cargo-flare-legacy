<?php

/***************************************************************************************************
* Forms CP Class                                                                 *
*                                                                              					   *
*                                                                                                  *
* Client: 	FreightDragon                                                                          *
* Version: 	1.0                                                                                    *
* Date:    	2011-09-29                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
****************************************************************************************************/

class CpForms extends CpAction {

	public function feedback() {
		$this->title = "Feedback";
		$this->tplname = "forms.feedback";

		if (($id = (int)get_var("info"))
		&& ($row = $this->daffny->DB->selectRow("*, DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date", "feedback", "WHERE id = ".$id))) {
			$this->title .= " - View";
			$this->tplname .= "_info";

			$row['comments'] = nl2br($row['comments']);
			$this->input = $row;
			return;
		}
		else if (($id = (int)get_var("delete"))) {
			$this->daffny->DB->delete("feedback", "id = ".$id);
			exit();
		}

		$where = "";
		$this->applyPager("feedback", "", $where);
        $this->applyOrder("feedback")->setDefault("id", "desc");

        $sql = "
			SELECT *
				 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date_show
			  FROM feedback"
				 . $where
				 . $this->order->getOrder()
				 . $this->pager->getLimit();

		$this->daffny->tpl->data = $this->getGridData($sql, false);
	}

	public function contactus() {
		$this->title = "Contact Us";
		$this->tplname = "forms.contactus";

		$fields = "*
				 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date_show";
		$tables = "contactus";

		if (($id = (int)get_var("info"))
		&& ($row = $this->daffny->DB->selectRow($fields, $tables, "WHERE id = ".$id))) {
			$this->title .= " - View";
			$this->tplname .= "_info";

			$this->input = $row;
			return;
		}
		else if (($id = (int)get_var("delete"))) {
			$this->daffny->DB->delete("contactus", "id = ".$id);
			echo json_encode(array('success' => true));
			exit();
		}

		$where = "";
		$this->applyPager("contactus", "", $where);
		$this->applyOrder("contactus")->setDefault("reg_date", "desc");

		$sql = "
			SELECT *
				 , DATE_FORMAT(reg_date, '%m/%d/%Y') AS reg_date_show
			  FROM contactus"
				 . $where
				 . $this->order->getOrder()
				 . $this->pager->getLimit();

		$this->daffny->tpl->data = $this->getGridData($sql, false);
	}
}
