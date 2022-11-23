<?php
	/**************************************************************************************************
	* SavedSearchManager
	* Class for work with saved Search forms
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-08
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
	
	class SavedSearchManager extends FdObjectManager {
		const TABLE = SavedSearch::TABLE;
		
		public function getByMemberId($member_id = null) {
			if (!ctype_digit((string)$member_id)) throw new FDException("SavedSearchManager->getByMemberId: invalid Member ID");
			$query = $this->db->hardQuery("SELECT id FROM ".self::TABLE." WHERe member_id = ". (int)$member_id);

			$ids = [];
			$i = 0;
			while($r = mysqli_fetch_assoc($query)){
				$ids[$i]['id'] = $r['id'];
				$i++;
			}
			//$rows = $this->db->selectRows("`id`", self::TABLE, "WHERE `member_id` = ".(int)$member_id);
			//if ($this->db->isError) throw new FDException("SavedSearchManager->getByMemberId: MySQL query error");
			$searches = array();
			foreach ($ids as $row) {
				$search = new SavedSearch($this->db);
				$search->load($row['id']);
				$searches[] = $search;
			}
			return $searches;
		}
	}
?>