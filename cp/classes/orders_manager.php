<?php
/**
 * @version		1.0
 * @since		09.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */ 
class OrdersManager extends FdObjectManager {
	const TABLE = Orders::TABLE;

	public function get($ord = null, $per_page = 50, $where = null) {
		$rows = parent::get($ord, $per_page, $where);
		$orders = array();
		foreach ($rows as $row) {
			$order = new Orders($this->db);
			$order->load($row['id']);
			$orders[] = $order;
		}
		return $orders;
	}
}
