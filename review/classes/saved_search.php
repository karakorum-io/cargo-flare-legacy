<?php
	/**************************************************************************************************
	* SavedSearch
	* This class represend one saved Search form
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-08
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
	/**
	 * @property int $member_id
	 * @property string $data
	 */
	class SavedSearch extends FdObject {
		const TABLE = "app_saved_searches";
		protected $formData = null;
		
		public function load($id) {
			parent::load($id);
			$this->formData = unserialize($this->data);
		}
		
		public function get($field) {
			if (is_null($this->formData)) throw new FDException("SavedSearch->get: data not loaded.");
			if (!isset($this->formData[$field])) return null;
			return $this->formData[$field];
		}
	}
?>