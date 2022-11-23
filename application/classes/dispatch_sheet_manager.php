<?php
/**************************************************************************************************
 * DispatchSheet class
 * Class representing one Dispatch Sheet record in DB
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:		2012-01-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 ***************************************************************************************************/
class DispatchSheetManager extends FdObjectManager {
	const TABLE = DispatchSheet::TABLE;

	public function getDispatches($status, $order = '', $per_page) {
		if (!array_key_exists($status, DispatchSheet::$status_name)) throw new FDException("Invalid Dispatch Status");
		$where = "`status` = {$status} AND `deleted` = 0 AND `cancelled` IS NULL AND ".$this->getPermissionCondition(false);
		$rows = parent::get($order, $per_page, $where);
		$dispatches = array();
		foreach ($rows as $row) {
			$dispatch = new DispatchSheet($this->db);
			$dispatch->load($row['id']);
			$dispatches[] = $dispatch;
		}
		return $dispatches;
	}

	private function getPermissionCondition($edit = false, $index = '') {
		if ($index != '') $index.='.';
		$where = "carrier_id IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		return $where;
	}

	public function getDispatchSheetByOrderId($orderId = null) {
		if (is_null($orderId) || !ctype_digit((string)$orderId)) throw new FDException("Invalid Order ID");
		$where = "WHERE `entity_id` = {$orderId} AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
		$row = $this->db->selectRow("`id`", self::TABLE, $where);
		if ($this->db->isError) throw new FDException("DB query error");
		if (empty($row)) return null;
		return $row['id'];
	}

	public function getCount() {
		$where = " WHERE `deleted` = 0 AND `cancelled` IS NULL AND `carrier_id` IN (" . implode(",", Member::getCompanyMembers($this->db, $_SESSION['member']['parent_id'])) . ")";
		if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
			$where .= " AND `carrier_id` = " . (int) $_SESSION['view_id'] . " ";
		}
		$rows = $this->db->selectRows("`status`, COUNT(`id`) as cnt", self::TABLE, "{$where} GROUP BY `status`");
		$result = array(
			DispatchSheet::STATUS_ARCHIVED => 0,
			DispatchSheet::STATUS_NOTSIGNED => 0,
			DispatchSheet::STATUS_DISPATCHED => 0,
			DispatchSheet::STATUS_PICKEDUP => 0,
			DispatchSheet::STATUS_DELIVERED => 0,
			DispatchSheet::STATUS_CANCELLED => 0
		);
		foreach ($rows as $row) {
			$result[$row['status']] = $row['cnt'];
		}
		return $result;
	}
}
