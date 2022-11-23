<?php
	/**************************************************************************************************
	* Lead Sources class
	* This class represent one Carrier, Terminal, Shipper
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-04
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/

	class Leadsource extends FdObject {
		const TABLE = "app_leadsources";

		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

        const ID_EXTERNAL_FORM = 1; //DB ID 1
        const ID_CUSTOM_EXTERNAL_FORM = 2; //DB ID 2

		public $type;

		public static $status_name = array(
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive'
		);

		public function update($data, $id = "") {
			if ($id != ""){
				$this->load($id);
			}
			parent::update($data);
		}

		public function load($id = null) {
			if (!ctype_digit((string)$id)) {
				$this->attributes['company_name'] = 'not available';
				$this->loaded = true;
			} else {
				parent::load($id);
				$this->attributes['status_name'] = Leadsource::$status_name[$this->status];
			}
		}
	}
?>