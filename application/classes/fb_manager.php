<?php
/**************************************************************************************************
 * MembersManager class
 * This class for work with Freight Board
 *
 * Client:        FreightDragon
 * Version:        1.0
 * Date:            2011-11-28
 * Author:        C.A.W., Inc. dba INTECHCENTER
 * Address:        11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:        techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 ***************************************************************************************************/

class FbManager extends FdObjectManager {
	public function search($params) {
		$grouped_results = array();
		try {
			$add_where = array();
			if (isset($params['origin_city'])) $add_where[] = "oo.`city` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['origin_city']) . "')";
			if (isset($params['origin_state'])) $add_where[] = "oo.`state` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['origin_state']) . "')";
			if (isset($params['origin_zip'])) $add_where[] = "oo.`zip` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['origin_zip']) . "')";
			if (isset($params['origin_country'])) $add_where[] = "oo.`country` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['origin_country']) . "')";
			if (isset($params['origin_region']) && is_array($params['origin_region']) && count($params['origin_region'])) {
				$regions_where = array();
				foreach ($params['origin_region'] as $region) {
					$regions_where[] = "(`region` LIKE('" . mysqli_real_escape_string($this->db->connection_id, $region) . "'))";
				}
				$add_where[] = "oo.`state` IN (SELECT `state` FROM `region_states` WHERE " . implode(" OR ", $regions_where) . ")";
			}

			if (isset($params['destination_city'])) $add_where[] = "od.`city` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['destination_city']) . "')";
			if (isset($params['destination_state'])) $add_where[] = "od.`state` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['destination_state']) . "')";
			if (isset($params['destination_zip'])) $add_where[] = "od.`zip` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['destination_zip']) . "')";
			if (isset($params['destination_country'])) $add_where[] = "od.`country` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $params['destination_country']) . "')";
			if (isset($params['destination_region']) && is_array($params['destination_region']) && count($params['destination_region'])) {
				$regions_where = array();
				foreach ($params['destination_region'] as $region) {
					$regions_where[] = "(`region` LIKE('" . mysqli_real_escape_string($this->db->connection_id, $region) . "'))";
				}
				$add_where[] = "od.`state` IN (SELECT `state` FROM `region_states` WHERE " . implode(" OR ", $regions_where) . ")";
			}
			if (isset($params['trailer_type'])) $add_where[] = "o.`ship_via` = " . (int)$params['trailer_type'];
			if (isset($params['company'])) $add_where[] = "o.`assigned_id` IN (SELECT `id` FROM " . Member::TABLE . " WHERE `parent_id` IN (SELECT `owner_id` FROM " . CompanyProfile::TABLE . " WHERE `id` IN (" . mysqli_real_escape_string($this->db->connection_id, $params['company']) . ")))";
			if (isset($params['vehicle_types'])) {
				foreach ($params['vehicle_types'] as $key => $vtype) {
					$params['vehicle_types'][$key] = "'" . mysqli_real_escape_string($this->db->connection_id, $vtype) . "'";
				}
				$add_where[] = "v.`type` IN (" . implode(", ", $params['vehicle_types']) . ")";
			}
			if (isset($params['timeframe'])) $add_where[] = "(o.`avail_pickup_date` BETWEEN CURDATE() AND CURDATE() + INTERVAL " . (int)$params['timeframe'] . " DAY)";
			if (isset($params['min_num'])) $add_where[] = "IFNULL((SELECT COUNT(*) FROM " . Vehicle::TABLE . " WHERE `entity_id` = o.`id`), 0) >= " . (int)$params['min_num'];
			if (isset($params['vehicle_condition'])) $add_where[] = "
			(SELECT COUNT(*) FROM `app_vehicles` WHERE `inop` = IF(".(int)$params['vehicle_condition']." = 2, 0, 1) AND `entity_id` = o.`id`)";
			if (isset($params['min_num'])) $add_where[] = "(SELECT COUNT(*) FROM " . Vehicle::TABLE . " WHERE `deleted` = 0 AND `entity_id` = o.`id`) >= " . (int)$params['min_num'];
			if (isset($params['trailer_type'])) $add_where[] = "o.`ship_via` = " . (int)$params['trailer_type'];
			if (isset($params['company'])) $add_where[] = "c.`id` IN (" . mysqli_real_escape_string($this->db->connection_id, $params['company']) . ")";
			if (isset($params['min_pay_type']) && isset($params['min_pay'])) {
				switch ($params['min_pay_type']) {
					case 'M':
						$add_where[] = "IF((v.`tariff` / o.`distance`) >= " . (float)$params['min_pay'] . ", 1, 0)";
						break;
					case 'L':
						$add_where[] = "IF(v.`tariff` >= " . (float)$params['min_pay'] . ", 1, 0)";
						break;
				}
			}

            if (isset($params['payment_type']) && $params['payment_type'] !="" && $params['payment_type'] !="all" ) {
                $add_where[] = "o.`balance_paid_by` IN(".(int)$params['payment_type'].")";
            }

			$add_where = implode(" AND ", $add_where);
			if (trim($add_where) != "") $add_where = " AND " . $add_where;
			$limit = "";
			if (isset($params['show'])) $limit = " LIMIT 0, " . (int)$params['show'];
			$order = array();
			if (isset($params['sort1'])) {
				switch ($params['sort1']) {
					case 'origination':
						$order[] = "origin_country";
						$order[] = "origin_state";
						$order[] = "origin_city";
						break;
					case 'origination_area':
						$order[] = "origin_region";
						break;
					case 'destination':
						$order[] = "destination_country";
						$order[] = "destination_state";
						$order[] = "destination_city";
						break;
					case 'destination_area':
						$order[] = "destination_region";
						break;
					case 'ship_date':
						$order[] = "ship_on";
						break;
					case 'company_name':
						$order[] = "company_name";
						break;
					case 'fd_id':
						$order[] = "entity_id";
						break;
					case 'post_date':
						$order[] = "posted";
						break;
					case 'price':
						$order[] = "price";
						break;
					case 'price_per_mile':
						$order[] = "(price/distance)";
						break;
				}
			}
			if (isset($params['sort2'])) {
				if (!isset($params['sort1']) || ($params['sort1'] != $params['sort2'])) {
					switch ($params['sort2']) {
						case 'origination':
							$order[] = "origin_country";
							$order[] = "origin_state";
							$order[] = "origin_city";
							break;
						case 'origination_area':
							$order[] = "origin_region";
							break;
						case 'destination':
							$order[] = "destination_country";
							$order[] = "destination_state";
							$order[] = "destination_city";
							break;
						case 'destination_area':
							$order[] = "destination_region";
							break;
						case 'ship_date':
							$order[] = "ship_on";
							break;
						case 'company_name':
							$order[] = "company_name";
							break;
						case 'fd_id':
							$order[] = "entity_id";
							break;
						case 'post_date':
							$order[] = "posted";
							break;
						case 'price':
							$order[] = "price";
							break;
						case 'price_per_mile':
							$order[] = "(price/distance)";
							break;
					}
				}
			}
			if (count($order) > 0) {
				$order = " ORDER BY " . implode(", ", $order);
			} else {
				$order = "";
			}
			$sql = "
				SELECT
					o.`id` as entity_id,
					CONCAT_WS('-', o.`prefix`,o.`number`) as load_id,
					o.`balance_paid_by` as balance_paid_by,
					o.`ship_via` as ship_via,
					(SELECT COUNT(*) as cnt FROM " . Vehicle::TABLE . " vv WHERE vv.`deleted` = 0 AND vv.`entity_id` = o.`id`) as vehicles_count,
					oo.`city` as origin_city,
					oo.`state` as origin_state,
					(SELECT `region` FROM `region_states` WHERE `state` = oo.`state` LIMIT 1) AS origin_region,
					oo.`country` as origin_country,
					od.`city` as destination_city,
					od.`state` as destination_state,
					(SELECT `region` FROM `region_states` WHERE `state` = od.`state` LIMIT 1) AS destination_region,
					od.`country` as destination_country,
					v.`id` as vehicle_id,
					v.`year` as vehicle_year,
					v.`make` as vehicle_make,
					v.`model` as vehicle_model,
					v.`type` as vehicle_type,
					v.`inop` as vehicle_inop,
					(SELECT SUM(`carrier_pay`) FROM " . Vehicle::TABLE . " WHERE `entity_id` = o.`id` AND `deleted` = 0) as price,
					o.`distance` as distance,
					(v.`tariff`/o.`distance`) as price_per_mile,
					c.`owner_id` as company_id,
					c.`companyname` as company_name,
					c.`dispatch_phone` as company_phone,
					c.`dispatch_fax` as company_fax,
					IFNULL((SELECT (100*(SUM(`type`)/2)/COUNT(*)) FROM " . Rating::TABLE . " WHERE `to_id` = c.`id` AND `status` = " . Rating::STATUS_ACTIVE . "), 0) as company_score,
					IFNULL((SELECT COUNT(*) FROM " . Rating::TABLE . " WHERE `to_id` = c.`id` AND `status` = " . Rating::STATUS_ACTIVE . "), 0) as ratings,
					o.`avail_pickup_date` as ship_on,
					o.`status_update` as posted,
					m2.`reg_date` as company_registered
				FROM
					" . Entity::TABLE . " o
					LEFT JOIN " . Vehicle::TABLE . " v ON v.entity_id = o.id,
					" . Origin::TABLE . " oo,
					" . Destination::TABLE . " od,
					" . CompanyProfile::TABLE . " c,
					" . Member::TABLE . " m1,
					" . Member::TABLE . " m2
				WHERE
					o.`assigned_id` = m1.`id`
					AND m2.`id` = m1.`parent_id`
					AND c.`owner_id` = m2.`id`
					AND o.`origin_id` = oo.`id`
					AND o.`destination_id` = od.`id`
					AND o.`status` = " . Entity::STATUS_POSTED . "
					AND o.`deleted` = 0
					AND o.`avail_pickup_date` >= CURDATE()
					AND v.`deleted` = 0
					{$add_where}
					{$order}
					{$limit}";
//				echo "<pre>$sql</pre>";
			$result = $this->db->query($sql);
			if ($this->db->isError) throw new FDException("MySQL error");
			$results = array();
			while ($row = $this->db->fetch_row($result)) {
				if (trim($row['entity_id']) == '') continue;

                $order = new Entity($this->db);
                $order->load($row["entity_id"]);
                $row["balance_paid_by"] = Entity::$balance_paid_by_string[$row["balance_paid_by"]];
				$order->getVehicles();
                $row["payment"] = $order->getCarrierPay();
                $row["ship_via"] = Entity::$ship_via_string[$row["ship_via"]];

				$results[] = $row;
			}
			foreach ($results as $result) {
				$grouped_results[$result['entity_id']][] = $result;
			}
			foreach ($grouped_results as $key => $result) {
				if (count($result) < $result[0]['vehicles_count']) unset($grouped_results[$key]);
			}
		} catch (Exception $e) {

		}
		return $grouped_results;
	}
}