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

	class SmsUsers extends FdObject {
		const TABLE = "app_sms_account_users";

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

		
	}
?>