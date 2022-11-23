<?php
    /**************************************************************************************************
	* AutoQuotingManager class
	* This class for work with auto quotes
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-29
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
    
    class AutoQuotingManager extends FdObjectManager {
        const SEASONS_TABLE = "app_autoquoting_seasons";
        const LANES_TABLE = "app_autoquoting_lanes";
        const CITIES_TABLE = "app_autoquoting_cities";
        const VEHICLES_TABLE = "app_autoquoting_vehicles";

	    /**
	     * @param Origin $origin
	     * @param Destination $destination
	     * @param Vehicle $vehicle
	     * @param int $owner_id
	     * @param string $date
	     * @param bool $encl
	     * @return float|int
	     */
	    public function getChargeAmount($origin, $destination, $vehicle, $owner_id, $date, $encl = false) {
			$amount = 0;
			try {
				if (!ctype_digit((string)$owner_id)) throw new FDException("Invalid Owner ID");
				if (!($origin instanceof Origin)) throw new FDException("Invalid Origin object");
				if (!($destination instanceof Destination)) throw new FDException("Invalid Destination object");
				if (!($vehicle instanceof Vehicle)) throw new FDException("Invalid Vehicle object");


				$origin_geo = $this->db->selectRow("`latitude` as lat, `longitude` as lng", "`zip_codes`", "WHERE `zip` LIKE '{$origin->zip}' AND `latitude` IS NOT NULL AND `longitude` IS NOT NULL");
				if (!$origin_geo) {
					$origin_geo = $this->db->selectRow("`lat`, `lng`", "`cities`", "WHERE `city` LIKE('{$origin->city}') AND `state` LIKE('{$origin->state}') AND `lat`  IS NOT NULL AND `lng` IS NOT NULL AND `country` LIKE('{$origin->country}')");
					if (!$origin_geo){
	                    throw new FDException("Origin location not found");
	                }
				}
				$destination_geo = $this->db->selectRow("`latitude` as lat, `longitude` as lng", "`zip_codes`", "WHERE `zip` LIKE '{$destination->zip}' AND `latitude` IS NOT NULL AND `longitude` IS NOT NULL");
				if (!$destination_geo) {
					$destination_geo = $this->db->selectRow("`lat`, `lng`", "`cities`", "WHERE `city` LIKE('{$destination->city}') AND `state` LIKE('{$destination->state}') AND `lat`  IS NOT NULL AND `lng`  IS NOT NULL AND `country` LIKE('{$destination->country}')");
					if (!$destination_geo){
	                    throw new FDException("Destination location not found");
	                }
				}

				// SELECT LANE
				$sql = "
				SELECT
					xl.`id`,
					xl.`name`,
					xl.`price_type`,
					xl.`cpm_price`,
					xl.`origin`,
				    xl.`destination`,
					xl.`base_price`,
					xl.`inop_surcharge`,
					xl.`encl_surcharge`,
					xl.`origin_radius`,
					xl.`destination_radius`,
					xl.`calculate_price`,
					xl.`round_total_to`
				FROM
					".AutoQuotingLane::TABLE." xl
				WHERE
					`season_id` IN (
						SELECT
							`id`
						FROM
							".self::SEASONS_TABLE."
						WHERE
							'".mysqli_real_escape_string($this->db->connection_id, $date)."' BETWEEN `start_date` AND `end_date`
							AND `status` = 'Active'
							AND `owner_id` = {$owner_id}
					)
					AND `status` = 'Active'
					AND xl.`origin` = '".$origin->state."' AND xl.`destination` = '".$destination->state."'
				LIMIT 0,1
				";

				$res = $this->db->query($sql);
				if ($res && $lane = $this->db->fetch_row($res)) {
					// SELECT SURCHARGES
					$sql = "
					SELECT
						v.`surcharge` AS v_surcharge,
						oc.`city_id`,
						dc.`city_id`,
						oc.`surcharge` AS oc_surcharge,
						dc.`surcharge` AS dc_surcharge,
						distance(occ.`lat`, occ.`lng`, {$origin_geo['lat']}, {$origin_geo['lng']}) as oc_distance,
						distance(dcc.`lat`, dcc.`lng`, {$destination_geo['lat']}, {$destination_geo['lng']}) as dc_distance
					FROM
						".AutoQuotingLane::TABLE." l
						LEFT JOIN ".AutoQuotingLane::CITIES_TABLE." oc ON oc.`id` IN (
							SELECT
								xoc.`id`
							FROM
								".AutoQuotingLane::CITIES_TABLE." xoc,
								`cities` xocc
							WHERE
								xocc.`id` = xoc.`city_id`
								AND xocc.`lat` BETWEEN ({$origin_geo['lat']} - geo_distance(l.`origin_radius`, 0)) AND ({$origin_geo['lat']} + geo_distance(l.`origin_radius`, 0))
								AND xocc.`lng` BETWEEN ({$origin_geo['lng']} - geo_distance(l.`origin_radius`, {$origin_geo['lat']})) AND ({$origin_geo['lng']} + geo_distance(l.`origin_radius`, {$origin_geo['lat']}))
								AND xoc.`type` = 0
								AND xoc.`is_active` = 1
						) AND oc.`surcharge` > 0
						LEFT JOIN ".AutoQuotingLane::CITIES_TABLE." dc ON dc.`id` IN (
							SELECT
								xdc.`id`
							FROM
								".AutoQuotingLane::CITIES_TABLE." xdc,
								`cities` xdcc
							WHERE
								xdcc.`id` = xdc.`city_id`
								AND xdcc.`lat` BETWEEN ({$destination_geo['lat']} - geo_distance(l.`destination_radius`, 0)) AND ({$destination_geo['lat']} + geo_distance(l.`destination_radius`, 0))
								AND xdcc.`lng` BETWEEN ({$destination_geo['lng']} - geo_distance(l.`destination_radius`, {$destination_geo['lat']})) AND ({$destination_geo['lng']} + geo_distance(l.`destination_radius`, {$destination_geo['lat']}))
								AND xdc.`type` = 1
								AND xdc.`is_active` = 1
						) AND dc.`surcharge` > 0
						LEFT JOIN `cities` occ ON  occ.`id` = oc.`city_id`
						LEFT JOIN `cities` dcc ON  dcc.`id` = dc.`city_id`
						LEFT JOIN ".AutoQuotingLane::VEHICLES_TABLE." v ON (v.`lane_id` = l.`id` AND v.`vehicle_type_id` = IFNULL((SELECT `id` FROM `app_vehicles_types` WHERE `name` LIKE('{$vehicle->type}')), -1))
					WHERE l.`id` = {$lane['id']}
					ORDER BY oc_distance, dc_distance ASC
					";
					$res = $this->db->query($sql);
					if ($res && $surcharges = $this->db->fetch_row($res)) {
						if ($lane['price_type'] == 'base') {
							$amount = (float)$lane['base_price'];
						} elseif ($lane['price_type'] == 'cpm') {
							$distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
							if (!is_null($distance)) {
								$distance = RouteHelper::getMiles((float)$distance, false);
								$amount = (float)$distance * (float)$lane['cpm_price'];
							}
						}
						$amount += (float)$surcharges['v_surcharge'];
						if ($vehicle->inop) {
							$amount += (float)$lane['inop_surcharge'];
						}
						if ($encl) {
							$amount += (float)$lane['encl_surcharge'];
						}
						if ((float)$surcharges['oc_surcharge'] >= (float)$surcharges['dc_surcharge']) {
							$amount += (float)$surcharges['oc_surcharge'] + ((float)$surcharges['dc_surcharge'] * (int)$lane['calculate_price'] / 100);
						} else {
							$amount += (float)$surcharges['dc_surcharge'] + ((float)$surcharges['oc_surcharge'] * (int)$lane['calculate_price'] / 100);
						}
						if ($lane['round_total_to'] == '1') {
							$round = round($amount / 5);
							$amount = $round * 5;
						}
					}
				}
			} catch (FDException $e) {}
			return $amount;
        }
    }
