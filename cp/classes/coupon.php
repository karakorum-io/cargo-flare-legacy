<?php
/**
 * @version		1.0
 * @since		08.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 *
 * @property int $id
 * @property string $code
 * @property int $time_to_use
 * @property int $is_per_customer
 * @property string $expire_date
 * @property int $is_never_expire
 * @property string $company
 * @property string $status
 * @property int $is_delete
 * @property string $register_date
 */ 
class Coupon extends FdObject {
	const TABLE = 'coupons';

	public function generateCoupon() {
		$validCharacters = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
		$validCharNumber = strlen($validCharacters);
		do {
			$result = "";
			for ($i = 0; $i < 6; $i++) {
				$index = mt_rand(0, $validCharNumber - 1);
				$result .= $validCharacters[$index];
			}
			$row = $this->db->selectRow('COUNT(*) as cnt', self::TABLE, "WHERE `code` LIKE('".$result."')");
		} while ($row['cnt'] != 0);
		return $result;
	}

	public function isExpired() {
		return (($this->is_never_expire == 0) && (strtotime($this->expire_date) < time()));
	}
}
