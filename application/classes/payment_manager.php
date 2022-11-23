<?php

/* * ************************************************************************************************
 * PaymentManager class
 * Class for work with payments
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-22
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class PaymentManager extends FdObjectManager {

    const TABLE = Payment::TABLE;

    public function getPayments($entity_id, $order = null, $per_page = null) {
        if (!ctype_digit((string)$entity_id))
            throw new FDException("Invalid Entity ID");
        $rows = parent::get($order, $per_page, "`entity_id` = {$entity_id} AND `deleted` = 0");
        $payments = array();
        foreach ($rows as $row) {
            $payment = new Payment($this->db);
            $payment->load($row['id']);
            $payments[] = $payment;
        }
        return $payments;
    }

	public function getDepositPaid($entity_id) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid Entity ID");
		$row = $this->db->selectRow("SUM(`amount`) as `value`", self::TABLE, "WHERE `fromid` = ".Payment::SBJ_SHIPPER." AND `toid` = ".Payment::SBJ_COMPANY." AND `deleted` = 0 AND `entity_id` = " . (int)$entity_id);
		if ($this->db->isError || !is_array($row)) throw new FDException("DB query error");
		return (float)$row['value'];
	}

    public function getFilteredPayments($entity_id, $from = null, $to = null, $order = null, $per_page = null) {
        if (!ctype_digit($entity_id))
            throw new FDException("Invalid Entity ID");

        $where = "`entity_id` = '" . (int) $entity_id . "' AND `deleted` = 0";

        if (!is_null($from)) {
			$where .= " AND `fromid` = ".(int)$from;
		}
		if (!is_null($to)) {
			$where .= " AND `toid` = ".(int)$to;
		}
        $rows = parent::get($order, $per_page, $where);
        $payments = array();
        foreach ($rows as $row) {
            $payment = new Payment($this->db);
            $payment->load($row['id']);
            $payments[] = $payment;
        }
        return $payments;
    }
    
    
    public function getFilteredPaymentsTotals($entity_id, $from = null, $to = null, $format = true) {
        
        if (!ctype_digit($entity_id))
            throw new FDException("Invalid Entity ID");
        
        $where = " `entity_id` = '" . (int) $entity_id . "' AND `deleted` = 0";

        if (!is_null($from)) {
			$where .= " AND `fromid` = ".(int)$from;
        }
        if (!is_null($to)) {
			$where .= " AND `toid` = ".(int)$to;
	}
        
        $rows = parent::get(null, null, $where);
        $totals = 0;
        foreach ($rows as $row) {
            $payment = new Payment($this->db);
            $payment->load($row['id']);
            $totals = $totals +$payment->amount;
        }
        if ($format){
            return "$".number_format($totals, 2);
        }else{
            return (float)$totals;
        }
        
    }

    
    /**
     * Payments received Report
     * Slide 34
     * Date, Order ID, Shipper, Amount, Payment Method, Entered By, Reference #, Notes, Check #, Last 4 digits of CC, CC Type, CC Expiration, Authorization Code, Transaction ID). 
     * This report can be additional filtered by Order ID, Shipper, Reference # and Transaction ID.  
     * @param type $order
     * @param type $per_page
     * @param array $report_arr
     * @param int $owner_id
     * @return payments data array
     */
    final public function getPaymentsReport($order = null, $per_page = 100, $report_arr = array(), $owner_id = null) {
        
        $users = array();
        $members = Member::getCompanyMembers($this->db, $owner_id);
        if (count($report_arr["users_ids"]) > 0) {
            foreach ($report_arr["users_ids"] as $value) {
                if (in_array($value, $members)) {
                    $users[] = $value;
                }
            }
        }
        //where for payments    
        $where = "`entered_by` IN(" . implode(",", $users) . ")
			AND `deleted` = 0
			AND `date_received` >= '" . $report_arr["start_date"] . "'
			AND `date_received` <= '" . $report_arr["end_date"] . "'";
        // Reference #
        if (trim($report_arr["reference_no"]) != "") {
            $where .= " AND `number` LIKE '%" . mysqli_real_escape_string($this->db->connection_id, $report_arr["reference_no"]) . "%'";
        }
        
        // Transaction ID
        if (trim($report_arr["transaction_id"]) != "") {
            $where .= " AND `transaction_id` LIKE '%" . mysqli_real_escape_string($this->db->connection_id, $report_arr["transaction_id"]) . "%'";
        }

        //Order ID
        $order_where = array();
        if (trim($report_arr["order_id"]) != "") {
            $order_where[] = " `id` LIKE '%" . mysqli_real_escape_string($this->db->connection_id, $report_arr["order_id"]) . "%'";
        }
        
        $shippers = array();
        $shipper_where = array();
        //shippers where
        if (isset($report_arr["ship_via"]) && trim($report_arr["ship_via"]) != "") {
            $ship_via = mysqli_real_escape_string($this->db->connection_id, trim($report_arr["ship_via"]));
            $shipper_where[] = " 
                (fname LIKE '%" . $ship_via . "%' OR 
                lname LIKE '%" . $ship_via . "%' OR
                CONCAT_WS(' ', 'fname', 'lname') LIKE '%" . $ship_via . "%' OR
                company LIKE '%" . $ship_via . "%')";
        }
        //get shippers ids
        if (count($shipper_where) > 0) {
            $swhere = implode(" AND ", $shipper_where);
            $shippers = Shipper::getShippers($this->db, $swhere);
            $order_where[] = " `shipper_id` IN('" . implode(",", $shippers) . "') ";
        }
        
        if (count($order_where) > 0) {
            $owhere = implode(" AND ", $order_where);
            $orders = Entity::getEntities($this->db, $owhere);
            $where .= " AND `entity_id` IN('" . implode(",", $orders) . "') ";
        }
        $rows = parent::get($order, $per_page, $where);
        $data = array();
        foreach ($rows as $row) {
            $payment = new Payment($this->db);
            $payment->load($row['id']);
            $data[$row['id']] = $payment;
        }
        return $data;
    }
}