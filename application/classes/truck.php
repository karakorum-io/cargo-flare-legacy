<?php
	/***************************************************************************************************
	* Truck class
	* This class is represent one truck
	*
	* Client:			FreightDragon
	* Version:			1.0
	* Date:				2011-11-03
	* Author:			C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:			techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	****************************************************************************************************/
	
	class Truck extends FdObject {
		const TABLE = "app_trucks";
		protected $memberObjects = array();
		
		public static $attributeTitles = array(
			'name' => 'Name',
			'trailer' => 'Trailer Type',
			'inops' => 'Inops OK'
		);
		
		public static $trailer_string = array(1 => "Open", 2 => "Enclosed");
		
		public function getDepartures($reload = false) {
			if ($reload || !isset($this->memberObjects['departures'])) {
				$rows = $this->db->selectRows("`id`", Departure::TABLE, "WHERE `truck_id` = ".$this->id." AND `deleted` = 0");
				if ($this->db->isError) throw new FDException("Truck->getDepartures: MySQL query error");
				$departures = array();
				foreach ($rows as $row) {
					$departure = new Departure($this->db);
					$departure->load($row['id']);
					$departures[] = $departure;
				}
				$this->memberObjects['departures'] = $departures;
			}
			return $this->memberObjects['departures'];
		}
		
		public static function getTrailerType($type) {
			if (array_key_exists($type, self::$trailer_string)) {
				return self::$trailer_string[$type];
			}
			return null;
		}
	}
?>