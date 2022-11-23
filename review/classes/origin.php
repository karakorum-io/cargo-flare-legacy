<?php
	/**************************************************************************************************
	* Origin class																																						*
	* Class for working with origin																																*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-10-26																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/
	
	class Origin extends Location {
		public function update($data = null) {
			$old_values = $this->attributes;
			parent::update($data);
			$new_values = $this->attributes;
			foreach($new_values as $key => $value) {
				if ($old_values[$key] != $value) {
					$rows = $this->db->selectRows("`id`", Entity::TABLE, "WHERE `origin_id` = ".$this->id." AND `deleted` = 0");
					if (is_array($rows)) {
						foreach($rows as $row) {
							History::add($this->db, $row['id'], 'Origin '.self::$attributeTitles[$key], $old_values[$key], $value);
						}
					}
				}
			}
		}
		
		public function create($data, $entity_id = null) {
			$id = parent::create($data);
			if (!is_null($entity_id)) {
				$new_values = $this->attributes;
				foreach($new_values as $key => $value) {
					if (in_array($key, array('created', 'id'))) continue;
					History::add($this->db, $entity_id, 'Origin '.self::$attributeTitles[$key], '', $value);
				}
			}
			return $id;
		}
	}
