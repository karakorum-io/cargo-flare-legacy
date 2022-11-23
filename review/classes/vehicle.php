<?php
	/**************************************************************************************************
	* Vehicle class																																						*
	* Class for working with vehicle																																*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-09-28																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/

/**
 * @property int $id
 * @property int $entity_id
 * @property int $dispatch_id
 * @property int $year
 * @property string $make
 * @property string $model
 * @property string $type
 * @property string $color
 * @property string $plate
 * @property string $state
 * @property string $vin
 * @property string $lot
 * @property float $tariff
 * @property float $carrier_pay
 * @property float deposit
 * @property string $created
 * @property int $deleted
 * @property int $inop
 */

	class Vehicle extends FdObject{

		const TABLE = "app_vehicles";
		
		public function getTariff($format = true) {
			return (($format)?"$ ":"").number_format($this->tariff, 2, ".", ",");
		}
		
		public function getDeposit($format = true) {
			return (($format)?"$ ":"").number_format($this->deposit, 2, ".", ",");
		}

		public function getCarrierPay($format = true) {
			return (($format)?"$ ":"").number_format($this->carrier_pay, 2, ".", ",");
		}
		
		public function update($data = null) {
			if (isset($data['vin'])) {
				$data['vin'] = strtoupper($data['vin']);
			}
			$old_values = $this->attributes;
			parent::update($data);
			$new_values = $this->attributes;
			if (count(array_diff($old_values, $new_values)) == 0) return;
			if (($old_values['entity_id'] != $new_values['entity_id'])) {
				$old = "";
			} else {
				$old = "Year: {$old_values['year']}\n Make: {$old_values['make']}\n Model: {$old_values['model']}\nType: {$old_values['type']}";
				$old.= "\nTariff: {$old_values['tariff']}\nDeposit: {$old_values['deposit']}\nCarrier Pay: {$old_values['deposit']}";
			}
			$new = "Year: {$new_values['year']}\n Make: {$new_values['make']}\n Model: {$new_values['model']}\nType: {$new_values['type']}";
			$new.= "\nTariff: {$new_values['tariff']}\nDeposit: {$new_values['deposit']}\nCarrier Pay: {$new_values['deposit']}";
			History::add($this->db, $new_values['entity_id'], "Vehicle", $old, $new);
		}
		
		public function create($data = null) {
			if (isset($data['vin'])) {
				$data['vin'] = strtoupper($data['vin']);
			}
			parent::create($data);
			$new_values = $this->attributes;
			$new = "Year: {$new_values['year']}\n Make: {$new_values['make']}\n Model: {$new_values['model']}\nType: {$new_values['type']}";
			$new.= "\nTariff: {$new_values['tariff']}\nDeposit: {$new_values['deposit']}\nCarrier Pay: {$new_values['deposit']}";
			History::add($this->db, $new_values['entity_id'], "Vehicle", '', $new);
		}

		public function cloneForDispatch($dispatch_id = null) {
			if (is_null($dispatch_id) || !ctype_digit((string)$dispatch_id)) throw new FDException("Invalid Dispatch Sheet ID");
			$entity_id = $this->entity_id;
			$this->attributes['entity_id'] = "NULL";
			$this->attributes['dispatch_id'] = (int)$dispatch_id;
			$new = $this->selfclone();
			$this->attributes['entity_id'] = $entity_id;
			$this->attributes['dispatch_id'] = null;
			return $new;
		}
	}
?>