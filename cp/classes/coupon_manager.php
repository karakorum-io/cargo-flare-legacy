<?php
/**
 * @version		1.0
 * @since		08.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 */ 
class CouponManager extends FdObjectManager {
	const TABLE = Coupon::TABLE;

	public function get($order = null, $per_page = 50, $where = null) {
		$rows = parent::get($order, $per_page, $where);
		$coupons = array();
		foreach ($rows as $row) {
			$coupon = new Coupon($this->db);
			$coupon->load($row['id']);
			$coupons[] = $coupon;
		}
		return $coupons;
	}
}
