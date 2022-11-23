<?php

/* * *************************************************************************************************
 * Control Panel - reports                                                                                 *
 *                                                                                                  *
 * Client: 	PitBullTax                                                                             *
 * Version: 	1.1                                                                                    *
 * Date:    	2010-05-31                                                                             *
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:	techsupport@intechcenter.com                                                           *
 * CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
 * ************************************************************************************************** */

class CpReports extends CpAction {
	public $title = "Reports";
	public static $weekDays
		= array(
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
			'Sunday',
			'Summary',
			'MonthSummary'
		);

	public function sales() {
		$this->title = "Sales Report";
		$select = "o.`id`, o.`register_date`, o.`status`, IF(o.`status` = 2, o.`amount`, NULL) as amount, o.`first_name`, o.`last_name`, p.`name` as `product_name`, l.`users`";
		$from = "orders o LEFT JOIN " . License::TABLE . " l ON l.`order_id` = o.`id`, product p, `orders_details` od";
		$where = "WHERE od.`order_id` = o.`id` AND od.`product_id` = p.`id`";
		$this->applyOrder('orders');
		if (isset($_GET['start_date'])) {
			$_GET['start_date'] = str_replace('_', '/', $_GET['start_date']);
		}
		if (isset($_GET['end_date'])) {
			$_GET['end_date'] = str_replace('_', '/', $_GET['end_date']);
		}
		$period = (isset($_GET['period']) ? $_GET['period'] : 'current_month');
		switch ($period) {
			case 'current_month':
				$where .= " AND MONTH(o.`register_date`) = MONTH(NOW())";
				break;
			case 'last_month':
				$where .= " AND MONTH(o.`register_date`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
				break;
			case 'year':
				$where .= " AND YEAR(o.`register_date`) = YEAR(NOW())";
				break;
			case 'date_range':
				if (isset($_GET['start_date'])) {
					$dateFrom = date('Y-m-d', strtotime(get_var('start_date')));
					$where .= " AND DATE_FORMAT(o.`register_date`, '%Y-%m-%d') >= '" . $dateFrom . "'";
				}
				if (isset($_GET['end_date'])) {
					$dateTo = date('Y-m-d', strtotime(get_var('end_date')));
					$where .= " AND DATE_FORMAT(o.`register_date`, '%Y-%m-%d') <= '" . $dateTo . "'";
				}
				break;
			default:
				break;
		}
		if (isset($_GET['member_id'])) {
			$where .= " AND l.`owner_id` = " . (int)$_GET['member_id'];
		}

		if (isset($_GET['who_help']) && $_GET["who_help"] != "") {
			$where .= " AND o.`who_help` = '" . $_GET['who_help'] . "' ";
		}

		if (isset($_GET['item'])) {
			$where .= " AND p.`code` = '" . (int)$_GET['item'] . "' ";
		}

		if (isset($_GET['order_id'])) {
			$where .= " AND o.`id` = " . (int)$_GET['order_id'];
		}
		if (isset($_GET['order_ids'])) {
			$where .= " AND o.`id` IN ("
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['order_ids']) . ")";
		}
		$where .= $this->order->getOrder();
		$where .= ' GROUP BY o.id';
		if (!isset($_GET['export'])) {
			$this->applyPager($from, 'o.id', $where);
			$where .= $this->pager->getLimit();
		}
		$this->daffny->tpl->orders = $this->daffny->DB->selectRows($select, $from, $where);
		if (!$this->daffny->tpl->orders) {
			$this->daffny->tpl->orders = array();
		}
		if (isset($_GET['export'])) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=sales.csv');
			$file = fopen('php://output', 'w');
			fputcsv($file, array(
				"Date, Time",
				"Order #",
				"Status",
				"Customer's Name",
				"Product purchased",
				"Number of Users",
				"Order Amount",
			));
			foreach ($this->daffny->tpl->orders as $order) {
				fputcsv($file, array(
					date('m/d/Y H:i:s', strtotime($order['register_date'])),
					$order['id'],
					Orders::getStatusLabel($order['status']),
					$order['first_name'] . " " . $order['last_name'],
					$order['product_name'],
					$order['users'],
					'$ ' . number_format($order['amount'], 2),
				));
			}
			fclose($file);
			exit;
		}
		//		printt($this->daffny->tpl->orders);exit;
		$this->tplname = 'reports.sales';
		$this->input = array_merge($this->input, $_GET);
		$this->form->DateField("start_date", 10, array(), "", "");
		$this->form->DateField("end_date", 10, array(), "", "");
		$this->form->ComboBox('period', array(
			'current_month' => 'Current Month',
			'last_month'    => 'Last Month',
			'year'          => 'Current Year',
			'date_range'    => 'Date Range',
			'all'           => 'All',
		), array(), 'Period', '</td><td>');
		$this->form->TextField('member_id', '10', array('class' => 'integer'), "Customer's Account #", '</td><td>');
		$this->form->TextField('item', '3', array('class' => 'integer'), "Item #", '</td><td>');
		$this->form->TextField('order_id', '10', array('class' => 'integer'), "Order #", '</td><td>');
		$this->form->ComboBox("who_help",
			array("---Select One---") + $this->getCustomerServiceNames(), array(), "Representative", "</td><td>");
	}

	public function users() {
		$this->title = "Users Report";
		$select = "m.*, cp.id as company_id";
		$from = Member::TABLE . " m LEFT JOIN " . CompanyProfile::TABLE ." cp ON m.`parent_id` = cp.`owner_id`";
		$where = "WHERE m.is_deleted != 1 ";
		if (isset($_GET['id'])) {
			$where .= " AND m.`id` = " . (int)$_GET['id'];
		}
		if (isset($_GET['contactname'])) {
			$where .= " AND m.`contactname` LIKE '%"
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['contactname']) . "%'";
		}
		if (isset($_GET['companyname'])) {
			$where .= " AND m.`contactname` LIKE '%"
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['companyname']) . "%'";
		}
		if (isset($_GET['zip'])) {
			$from .= ", " . CompanyProfile::TABLE . " cp";
			$where .= " AND cp.`owner_id` = m.`parent_id`";
			$where .= " AND cp.`zip_code` LIKE '%"
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['zip']) . "%'";
		}
		if (isset($_GET['email'])) {
			$where .= " AND m.`email` LIKE '%"
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['email']) . "%'";
		}


        $this->applyOrder(Member::TABLE);
        switch ($this->order->CurrentOrder) {
            case 'id':
                $this->order->setTableIndex('cp');
                $this->order->CurrentOrder = 'id';
                break;
        }
        $where .= $this->order->getOrder();
        if (!isset($_GET['export'])) {
            $this->applyPager($from, 'm.id', $where);
            $where .= $this->pager->getLimit();
        }
        
        $this->daffny->tpl->members = $this->daffny->DB->selectRows($select, $from, $where);
		if (!$this->daffny->tpl->members) {
			$this->daffny->tpl->members = array();
		}
		if (isset($_GET['export'])) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=users.csv');
			$file = fopen('php://output', 'w');
			fputcsv($file, array(
				"Account #",
				"Contact Name",
				"Company Name",
				"Registered",
				"Last Login",
				"Status",
			));
			foreach ($this->daffny->tpl->members as $member) {
				fputcsv($file, array(
					$member['id'],
					$member['contactname'],
					$member['companyname'],
					date('m/d/Y H:i:s', strtotime($member['reg_date'])),
					date('m/d/Y H:i:s', strtotime($member['last_login'])),
					$member['status'],
				));
			}
			fclose($file);
			exit;
		}
		$this->tplname = "reports.users";
		$this->input = array_merge($this->input, $_GET);
		$this->form->TextField('id', 10, array('class' => 'integer'), "Account #", '</td><td>');
		$this->form->TextField('contactname', 32, array(), 'Contact Name', '</td><td>');
		$this->form->TextField('companyname', 32, array(), 'Company Name', '</td><td>');
		$this->form->TextField('zip', 10, array(), 'Zip Code', '</td><td>');
		$this->form->TextField('email', 64, array(), 'E-mail address', '</td><td>');
	}

	public function licenses() {

		if (isset($_GET["id"]) && (isset($_GET["close"]) || isset($_GET["reactivate"]) || isset($_GET["cancel"]))) {

			$m = new Member($this->daffny->DB);
			$m->load((int)$_GET["id"]);
			$cp = $m->getCompanyProfile();
			$ds = new DefaultSettings($this->daffny->DB);
			$ds->getByOwnerId((int)$_GET["id"]);

			if (isset($_GET["close"])) {
				$cp->update(array("is_frozen" => 1));
				$m->update(array("status" => "Inactive"));
				$this->setFlashInfo("Access has been disabled.");
				redirect(getLink("reports", "licenses"));
			}
			if (isset($_GET["reactivate"])) {
				$cp->update(array("is_frozen" => 0));
				$m->update(array("status" => "Active"));
				$this->setFlashInfo("Access has been reactivated.");
				$this->setFlashInfo("");
				redirect(getLink("reports", "licenses"));
			}
			if (isset($_GET["cancel"])) {
				$ds->update(array("billing_autopay" => 0));
				$this->setFlashInfo("Auto Renewal has been disabled.");
				redirect(getLink("reports", "licenses"));
			}
		}

		$this->title = "Licenses Report";
		$select
			= "l.`id`
				, p.`name` as `product_name`
				, l.`users`
				, m.`id` as `member_id`
				, m.`contactname`
				, m.`companyname`
				, l.`created`
				, l.`expire`
				, ds.`billing_autopay`
				, IF(l.`expire` > NOW(), 'Active', 'Expired') as status
				, cp.is_frozen
		";

		$from = "licenses l
				, orders o
				,product p
				, `orders_details` od
				, member m
				, " . DefaultSettings::TABLE . " ds
				, " . CompanyProfile::TABLE . " cp ";
		$where
			=
			"WHERE l.`order_id` = o.`id` AND l.`owner_id` = m.`id` AND od.`order_id` = o.`id` AND od.`product_id` = p.`id` AND cp.`owner_id` = m.`id` AND ds.`owner_id` = m.`id` AND p.`type_id` != 2";
		$this->applyOrder();
		$this->order->Fields[] = 'p.name';
		$this->order->Fields[] = 'l.users';
		$this->order->Fields[] = 'l.id';
		$this->order->Fields[] = 'm.contactname';
		$this->order->Fields[] = 'm.companyname';
		$this->order->Fields[] = 'l.created';
		$this->order->Fields[] = 'l.expire';
		$this->order->Fields[] = 'ds.billing_autopay';
		$period = (isset($_GET['period']) ? $_GET['period'] : 'current_month');
		switch ($period) {
			case 'current_month':
				$where .= " AND MONTH(l.`created`) = MONTH(NOW())";
				break;
			case 'last_month':
				$where .= " AND MONTH(l.`created`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
				break;
			case 'year':
				$where .= " AND YEAR(l.`created`) = YEAR(NOW())";
				break;
			case 'date_range':
				if (isset($_GET['start_date'])) {
					$dateFrom = date('Y-m-d', strtotime(get_var('start_date')));
					$where .= " AND DATE_FORMAT(l.`created`, '%Y-%m-%d') >= '" . $dateFrom . "'";
				}
				if (isset($_GET['end_date'])) {
					$dateTo = date('Y-m-d', strtotime(get_var('end_date')));
					$where .= " AND DATE_FORMAT(l.`created`, '%Y-%m-%d') <= '" . $dateTo . "'";
				}
				break;
			default:
				break;
		}
		if (isset($_GET['status']) && $_GET['status'] != "") {
			if ($_GET['status'] == 1) {
				$where .= " AND l.`expire` > NOW() ";
			}
			if ($_GET['status'] == 2) {
				$where .= " AND l.`expire` < NOW() ";
			}
			if ($_GET['status'] == 3) {
				$where .= " AND cp.is_frozen = 1 ";
			}
		}

		if (isset($_GET['member_id'])) {
			$where .= " AND l.`owner_id` = " . (int)$_GET['member_id'];
		}
		if (isset($_GET['contactname'])) {
			$where .= " AND m.`contactname` LIKE'%" . $_GET['contactname'] . "%'";
		}
		if (isset($_GET['license_ids'])) {
			$where .= " AND l.`id` IN ("
				. mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['license_ids']) . ")";
		}

		//		$where .= " GROUP BY l.`id`";
		$where .= $this->order->getOrder();
		if (!isset($_GET['export'])) {
			$this->applyPager($from, 'l.id', $where);
			$where .= $this->pager->getLimit();
		}
		$this->daffny->tpl->licenses = $this->daffny->DB->selectRows($select, $from, $where);
		if (!$this->daffny->tpl->licenses) {
			$this->daffny->tpl->licenses = array();
		}
		if (isset($_GET['export'])) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=licenses.csv');
			$file = fopen('php://output', 'w');
			fputcsv($file, array(
				"Product Name",
				"# of Users / Account #",
				"Contact Name",
				"Company Name",
				"Register Date",
				"Expiration Date",
				'Auto Renewal'
			));
			foreach ($this->daffny->tpl->licenses as $license) {
				fputcsv($file, array(
					$license['product_name'],
					$license['users'] . "/" . $license['member_id'],
					$license['contactname'],
					$license['companyname'],
					date('m/d/Y', strtotime($license['created'])),
					date('m/d/Y', strtotime($license['expire'])),
					$license['billing_autopay'] ? 'Yes' : 'No',
				));
			}
			fclose($file);
			exit;
		}
		$this->tplname = 'reports.licenses';
		$this->input = array_merge($this->input, $_GET);
		$this->form->DateField("start_date", 10, array(), "", "");
		$this->form->DateField("end_date", 10, array(), "", "");
		$this->form->ComboBox('period', array(
			'current_month' => 'Current Month',
			'last_month'    => 'Last Month',
			'year'          => 'Current Year',
			'date_range'    => 'Date Range',
			'all'           => 'All',
		), array(), 'Period', '</td><td>');
		$this->form->TextField('contactname', 32, array(), 'Contact Name', '</td><td>');
		$this->form->TextField('member_id', 10, array('class' => 'integer'), 'Account #', '</td><td>');
		$this->form->ComboBox("status", array(
			"" => "All",
			"1" => "Active",
			"2" => "Expired",
			"3" => "Closed"
		), array(), "Status", "</td><td>");
	}

	public function printinvoices() {
		$this->title = "Print Invoices";
		$this->tplname = "orders.printseveral";
		$this->daffny->tpl->hideprint = 1;
		$where = " 1 = 2 ";
		$where_details = " 1 = 2 ";
		if (isset($_GET['ids']) && trim($_GET['ids']) != "") {
			$where = " o.`id` IN (" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['ids']) . ")";
		}

		$sql
			= "SELECT o.*
										, DATE_FORMAT(o.register_date, '%m/%d/%Y %h:%i %p') AS register_date_format
										, c.code AS coupon_code
										, (SELECT COUNT(*) FROM orders_comments WHERE order_id = o.id) AS comment_total
										, m.phone
										, m.email
										, o.card_expire  AS card_expiration
										, o.id AS order_id
										, DATE_FORMAT(o.register_date, '%m/%d/%Y %h:%i %p') AS order_date
                      FROM orders o
											LEFT JOIN coupons c ON c.id = o.coupon_id
											LEFT JOIN members m ON m.id = o.member_id
                     WHERE " . $where;
		$q = $this->daffny->DB->query($sql);
		$fl = true;
		$this->input["invoices"] = "";
		while ($row = $this->daffny->DB->fetch_row($q)) {
			$fl = false;
			$row["status"] = Orders::getStatusLabel($row["status"]);
			$row["card_type"] = Creditcard::$cctype_name[$row["card_type_id"]];
			if ($_SESSION['member']['group_id'] != 2) {
				$row["card_number"] = "**** **** **** " . substr($row["card_number"], -4);
				$row["card_cvv2"] = "****";
			}

			$this->daffny->tpl->data = array();
			$this->daffny->tpl->data = $row;

			//details
			$this->daffny->tpl->products = array();

			$sql_d
				= "
								SELECT
										od.*
										, p.code AS item
										, p.name AS product
								FROM orders_details od
								LEFT JOIN products p ON p.id = od.product_id
								WHERE od.`order_id` = '" . $row["id"] . "'";

			$q_d = $this->daffny->DB->query($sql_d);
			while ($row_d = $this->daffny->DB->fetch_row($q_d)) {
				$row_d["price"] = number_format($row_d["price"], 2);
				$row_d["total"] = number_format($row_d["total"], 2);
				$this->daffny->tpl->products[] = $row_d;
			}

			$this->input["invoices"] .= $this->daffny->tpl->build("orders.print", $row);
			$this->input["invoices"] .= "<p style=\"page-break-before:always\"></p>";
		}

		if ($fl) {
			$this->input["invoices"] = "No data found.";
		}
	}
}
