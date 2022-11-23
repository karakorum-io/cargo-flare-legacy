<?php
/**************************************************************************************************
* VehicleManager class
* Class for working with vehicles
*
* Client:		FreightDragon
* Version:		1.0
* Date:			2011-09-28
* Author:		C.A.W., Inc. dba INTECHCENTER
* Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:		techsupport@intechcenter.com
* CopyRight 2011 FreightDragon. - All Rights Reserved
***************************************************************************************************/

class VehicleManager extends FdObjectManager {
	const TABLE = Vehicle::TABLE;

	public $total_tariff = null;
	public $total_deposit = null;
	public $total_carrier_pay = null;

	/* GETTERS */

	/**
	 * @param int $entity_id
	 * @return array[Vehicle]
	 * @throws FDException
	 */
	public function getVehicles($entity_id = null) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid Entity ID");
		$vehicles = array();
		$this->total_tariff = 0;
		$this->total_deposit = 0;
		$this->total_carrier_pay = 0;
		$rows = parent::get(null, null, "`entity_id` = ".(int)$entity_id." AND `deleted` = 0");
		foreach($rows as $row) {
			$vehicle = new Vehicle($this->db);
			$vehicle->load($row['id']);
			$this->total_tariff += (float)$vehicle->tariff;
			$this->total_deposit += (float)$vehicle->deposit;
			$this->total_carrier_pay += (float)$vehicle->carrier_pay;
			$vehicles[] = $vehicle;
		}
		return $vehicles;
	}

public function getVehiclesArrData($entity_id = null) {
		
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid Entity ID");
		$vehicles = array();
		
		$rows = parent::getAll(null, null, "`entity_id` = ".(int)$entity_id." AND `deleted` = 0");
		return $rows;
	}
	
	public function getDispatchVehicles($dispatch_id = null) {
		if(!ctype_digit((string)$dispatch_id)) throw new FDException("Invalid Dispatch Sheet ID");
		$vehicles = array();
		$rows = parent::get(null, null, "`dispatch_id` = ".(int)$dispatch_id." AND `deleted` = 0");
		foreach($rows as $row) {
			$vehicle = new Vehicle($this->db);
			$vehicle->load($row['id']);
			$vehicles[] = $vehicle;
		}
		return $vehicles;
	}
}