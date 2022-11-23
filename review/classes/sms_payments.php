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

	class SmsPayments extends FdObject {
		const TABLE = "app_sms_account_payments";

		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

        
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
     
	 public function create($data = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->create: DB helper not set");
				if (!is_array($data))
						throw new FDException(get_class($this) . "->create: invalid input data");
				
				$data = $this->db->PrepareSql(self::TABLE, $data);
				//printt($data);
				$this->db->insert(self::TABLE, $data);
				if ($this->db->isError)
						throw new FDException(get_class($this) . "->create: MySQL query error");
				
				return $this->db->get_insert_id();
		}

		
	}
?>