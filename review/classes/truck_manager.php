<?php
	/***************************************************************************************************
	* TruckManager class
	* The class for work with trucks
	*
	* Client:			FreightDragon
	* Version:			1.0
	* Date:				2011-11-03
	* Author:			C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:			techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	****************************************************************************************************/
	
	class TruckManager extends FdObjectManager {
		const TABLE = Truck::TABLE;
		/**
		 * TruckManager::getTrucks()
		 * 
		 * @param int $per_page - Records per page for Pager.
		 * @param string $where - MySQL WHERE string
		 * @return array $trucks - array of Truck objects
		 * @throws FDException
		 */
		public function getTrucks($per_page, $where) {
			if (!ctype_digit((string)$per_page)) throw new FDException("TruckManager->getTrucks: invalid per_page value");
			$rows = parent::get(null, $per_page, $where." AND `deleted` = 0");
			$trucks = array();
			foreach ($rows as $row) {
				$truck = new Truck($this->db);
				$truck->load($row['id']);
				$trucks[] = $truck;
			}
			return $trucks;
		}
		
		public function search($params, $order = null) {
			// I am really ashamed of this code :(
			$add_where = array();
			if (isset($params['carrier_id'])) $add_where[] = "carr.`id` = ".$params['carrier_id'];
			if (isset($params['carrier_name'])) $add_where[] = "carr.`contactname` LIKE('".mysqil_real_escape_string($this->db->connection_id, $params['carrier_name'])."')";
			if (isset($params['date'])) {
				$date = strtotime($params['date']);
				$add_where[] = "(dep.`date` BETWEEN '".date('Y-m-d', $date - 604800)."' AND '".date('Y-m-d', $date + 604800)."')";
			}
			if (isset($params['inops'])) $add_where[] = "tr.`inops` = ".(int)$params['inops'];
			if (isset($params['spaces']) && ($params['spaces'] != "")) $add_where[] = "dep.`spaces` >= ".(int)$params['spaces'];
			if (isset($params['origin_city'])) $add_where[] = "dep.`from_city` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['origin_city'])."')";
			if (isset($params['origin_state'])) $add_where[] = "dep.`from_state` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['origin_state'])."')";
			if (isset($params['origin_coutry'])) $add_where[] = "dep.`from_country` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['origin_country'])."')";
			if (isset($params['destination_city'])) $add_where[] = "dep.`final_city` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['destination_city'])."')";
			if (isset($params['destination_state'])) $add_where[] = "dep.`final_state` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['destination_state'])."')";
			if (isset($params['destination_coutry'])) $add_where[] = "dep.`final_country` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['destination_country'])."')";
			if (isset($params['heading'])) $add_where[] = "dep.`heading` LIKE('".mysqli_real_escape_string($this->db->connection_id, $params['heading'])."')";
			$add_where = implode(" AND ", $add_where);
			if (strlen($add_where) > 0) $add_where = " AND ".$add_where;
			$match100_where = "
				AND oo.`state` = dep.`from_state`
				AND od.`state` = dep.`final_state`
				AND dep.`spaces` > (SELECT COUNT(`id`) FROM ".Vehicle::TABLE." WHERE `entity_id` = o.`id` AND `deleted` = 0)";
			$match75_where = "
				AND (oo.`state` = dep.`from_state` OR od.`state` = dep.`final_state`)
				AND dep.`spaces` > (SELECT COUNT(`id`) FROM ".Vehicle::TABLE." WHERE `entity_id` = o.`id` AND `deleted` = 0)";
			$match50_where = "
				AND oo.`state` IN (SELECT `state` FROM `region_states` WHERE `region` IN (SELECT `region` FROM `region_states` WHERE `state` = dep.`from_state`))
				AND od.`state` IN (SELECT `state` FROM `region_states` WHERE `region` IN (SELECT `region` FROM `region_states` WHERE `state` = dep.`final_state`))";
			$match25_where = "
				AND (oo.`state` IN (SELECT `state` FROM `region_states` WHERE `region` IN (SELECT `region` FROM `region_states` WHERE `state` = dep.`from_state`))
				OR od.`state` IN (SELECT `state` FROM `region_states` WHERE `region` IN (SELECT `region` FROM `region_states` WHERE `state` = dep.`final_state`)))";
			$sql = $this->getSearchSqlTemplate('100', $add_where.$match100_where);
			$sql.= " UNION " . $this->getSearchSqlTemplate('75', $add_where.$match75_where);
			$sql.= " UNION " . $this->getSearchSqlTemplate('50', $add_where.$match50_where);
			$sql.= " UNION " . $this->getSearchSqlTemplate('25', $add_where.$match25_where);
			$sql.= " UNION " . $this->getSearchZeroSqlTemplate('0', $add_where);
			if (!is_null($order)) $sql.= $order;
			$result = $this->db->query($sql);
			//echo "<pre>".$sql."</pre>";
			if ($this->db->isError) throw new FDException("MySQL error");
			$results = array();
			while ($row = $this->db->fetch_row($result)) {
				$results[] = $row;
			}
			$grouped_results = array();
			foreach ($results as $result) {
				if ($result['order_id']) {
					$order = new Entity($this->db);
					$order->load($result['order_id']);
					$result['order'] = $order;
				} else {
					$result['order'] = null;
				}
				$grouped_results[$result['departure_id'].'-'.$result['matcher']][] = $result;
			}
			return $grouped_results;
		}
		
		public static function getSearchFeilds() {
			return array(
				'matcher',
				'departure_id',
				'truck_id',
				'from_city',
				'from_state',
				'from_country',
				'final_city',
				'final_state',
				'final_country',
				'heading',
				'departure_date',
				'departure_time',
				'spaces',
				'trailer',
				'inops',
				'company',
				'phone',
				'email',
				'order_id',
				'carr_name',
				'carr_id'
			);
		}

		protected function getSearchZeroSqlTemplate($match, $add) {
			$sql_tpl = "SELECT
					{$match} as matcher,
					dep.`id` as departure_id,
					dep.`truck_id` as truck_id,
					dep.`from_city` as from_city,
					dep.`from_state` as from_state,
					dep.`from_country` as from_country,
					dep.`final_city` as final_city,
					dep.`final_state` as final_state,
					dep.`final_country` as final_country,
					dep.`heading` as heading,
					dep.`date` as departure_date,
					dep.`time` as departure_time,
					dep.`spaces` as spaces,
					tr.`trailer` as trailer,
					tr.`inops` as inops,
					comp.`companyname` as company,
					comp.`owner_id` as company_id,
					comp.`dispatch_phone` as phone,
					comp.`dispatch_email` as email,
					NULL as order_id,
					carr.`contactname` as carr_name,
					carr.`id` as carr_id
				FROM
				    ".Truck::TABLE." tr,
				    ".Departure::TABLE." dep,
				    ".Member::TABLE." as carr,
				    ".CompanyProfile::TABLE." as comp
				WHERE
					tr.`id` = dep.`truck_id`
					AND dep.`deleted` = 0
					AND carr.`id` = tr.`owner_id`
					AND (comp.`owner_id` = carr.`id` OR comp.`owner_id` = carr.`parent_id`) {$add}";
			return $sql_tpl;
		}
		
		protected function getSearchSqlTemplate($match, $add) {
			$sql_tpl = "SELECT
					{$match} as matcher,
					dep.`id` as departure_id,
					dep.`truck_id` as truck_id,
					dep.`from_city` as from_city,
					dep.`from_state` as from_state,
					dep.`from_country` as from_country,
					dep.`final_city` as final_city,
					dep.`final_state` as final_state,
					dep.`final_country` as final_country,
					dep.`heading` as heading,
					dep.`date` as departure_date,
					dep.`time` as departure_time,
					dep.`spaces` as spaces,
					tr.`trailer` as trailer,
					tr.`inops` as inops,
					comp.`companyname` as company,
					comp.`owner_id` as company_id,
					comp.`dispatch_phone` as phone,
					comp.`dispatch_email` as email,
					o.`id` as order_id,
					carr.`contactname` as carr_name,
					carr.`id` as carr_id
				FROM
				    ".Entity::TABLE." o,
				    ".Origin::TABLE." oo,
				    ".Destination::TABLE." od,
				    ".Truck::TABLE." tr,
				    ".Departure::TABLE." dep,
				    ".Member::TABLE." as carr,
				    ".CompanyProfile::TABLE." as comp
				WHERE
					tr.`id` = dep.`truck_id`
					AND dep.`deleted` = 0
					AND o.`assigned_id` = ".$_SESSION['member_id']."
					AND o.`deleted` = 0
					AND o.`type` = ".Entity::TYPE_LEAD."
					AND o.`status` = ".Entity::STATUS_ACTIVE."
					AND oo.`id` = o.`origin_id`
					AND od.`id` = o.`destination_id`
					AND carr.`id` = tr.`owner_id`
					AND (comp.`owner_id` = carr.`id` OR comp.`owner_id` = carr.`parent_id`) {$add}";
			return $sql_tpl;
		}
	}
?>