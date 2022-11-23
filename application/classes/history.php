<?php
	/**************************************************************************************************
	* History class																																						*
	* This class shows lead/quote/order changes																											*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-10-26																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/
	
	class History extends FdObject {
		const TABLE = "app_history";
		
		public function __construct($param) {
			parent::__construct($param);
		}
		
		public function __get($name) {
			$value = parent::__get($name);
			if (($name == "old_value" || $name == "new_value") && (string)$value == "") {
				$value = "<i>None</i>";
			} else {
				$value = nl2br(htmlspecialchars($value));
			}
			return $value;
		}
			
		public function getChangedBy() {
		    
		    try {
			$member = new Member($this->db);
			$member->load($this->changed_by);
			$changed_by = $member->contactname . " [".$this->changed_by_ip."]";
			
		    } catch (Exception $exc) {
			$changed_by = "N/A";
		    }

		        
			return $changed_by;
		}
		
		public function getDate($format = "m/d/Y H:i:s") {
			return date($format, strtotime($this->change_date));
		}
		
		public static function add($db, $entity_id, $field_name, $old_value, $new_value) {
			if (trim($old_value) == trim($new_value)) return;
			if (!($db instanceof mysql)) throw new FDException("History->add: invalid DB helper");
			$member_id = $_SESSION['member_id'];
			$field = null;
			$data = $db->PrepareSql(self::TABLE, array('entity_id' => (int)$entity_id, 'field_name' => $field_name, 'old_value' => $old_value, 'new_value' => $new_value, 'changed_by' => $member_id, 'changed_by_ip' => $_SERVER['REMOTE_ADDR']));
			$db->insert(self::TABLE, $data);
			if ($db->isError) throw new FDException("History->add: MySQL query error");
		}
	}
?>