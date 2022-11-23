<?php
	/***************************************************************************************************
	* Departure class
	* This class is represent one departure
	*
	* Client:			FreightDragon
	* Version:			1.0
	* Date:				2011-11-03
	* Author:			C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:			techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	****************************************************************************************************/
	
	class Departure extends FdObject {
		const TABLE = "app_departures";
		
		public static $attributeTitles = array(
			'from_city' => 'City or Area',
			'from_state' => 'State',
			'final_state' => 'State or Region',
			'final_direction' => 'Heading Direction',
			'date' => 'Date',
			'time' => 'Time',
			'spaces' => '# Open Spaces'
		);
		
		public static $directions = array(
			'' => 'Unknown',
			'N' => 'North',
			'S' => 'South',
			'E' => 'East',
			'W' => 'West',
			'NE' => 'Northeast',
			'SE' => 'Southeast',
			'NW' => 'Northwest',
			'SW' => 'Southwest'
		);
		
		public function getDate($format = "m/d/Y") {
			return date($format, strtotime($this->date));
		}
		
		public function getFrom() {
			$from = array();
			if (trim($this->from_city) != "") $from[] = $this->from_city;
			if (trim($this->from_state) != "") $from[] = $this->from_state;
			if (trim($this->from_country) != "") $from[] = $this->from_country;
			return implode(', ', $from);
		}
		
		public function getDestnation() {
			$from = array();
			if (trim($this->final_city) != "") $from[] = $this->final_city;
			if (trim($this->final_state) != "") $from[] = $this->final_state;
			if (trim($this->final_country) != "") $from[] = $this->final_country;
			return implode(', ', $from);
		}
		
		public function getHeading() {
			if (array_key_exists($this->heading, self::$directions)) {
				return self::$directions[$this->heading];
			}
			return null;
		}
	}
?>