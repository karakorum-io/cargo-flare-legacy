<?php
	/**************************************************************************************************
	* MembersManager class																																		*
	* This class for work with members																															*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-10-18																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/

	class MembersManager extends FdObjectManager {
		const TABLE = Member::TABLE;

		public function getMembers($where = "") {
			$members = array();
			$where = 'is_deleted = 0'.((trim($where) != '')?' AND ': ' ').$where;
			$rows = $this->db->selectRows("`id`", self::TABLE, "WHERE {$where}");
			if (is_array($rows)) {
				foreach($rows as $row) {
					$member = new Member($this->db);
					$member->load($row['id']);
					$members[] = $member;
				}
			}
			return $members;
		}

		public function getReferrals($order, $per_page, $referer_id) {

			$rows = parent::get($order, $per_page, "referer_id = '".(int)$referer_id."'");
			$members = array();
			if (is_array($rows)) {
				foreach($rows as $row) {
					$member = new Member($this->db);
					$member->load($row['id']);
					$members[] = $member;
				}
			}
			return $members;
		}
	}
?>