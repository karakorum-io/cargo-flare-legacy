<?php
	/**************************************************************************************************
	* Referrer class																																						*
	* This class represent one referrer																															*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-11-01																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/

	class Referrer extends FdObject {
		const TABLE = "app_referrers";

		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 0;

		public static $status_name = array(
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive'
		);

		public function update($data, $id) {
			$this->load($id);
			parent::update($data);
		}
	}
?>