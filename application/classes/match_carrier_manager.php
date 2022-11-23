<?php
	/***************************************************************************************************
	* WebMenuManager class
	* The class for work with menus
	*
	* Client:			FreightDragon
	* Version:			1.0
	* Date:				2011-11-03
	* Author:			C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:			techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	****************************************************************************************************/
	
	class MatchCarrierManager extends FdObjectManager {
		const TABLE = MatchCarrier::TABLE;
		/**
		 * WebMenuManager::getMenu()
		 * 
		 * @param int $per_page - Records per page for Pager.
		 * @param string $where - MySQL WHERE string
		 * @return array $trucks - array of WebMenu objects
		 * @throws FDException
		 */
		public function getMatchCarrier($order = null, $per_page = 100, $where = '') {
		//echo $where;
			if (!ctype_digit((string)$per_page)) throw new FDException("MatchCarrierManager->getMatchCarrier: invalid per_page value");
			
			$rows = parent::get($order, $per_page, $where);
			$carrierArr = array();
			
			foreach ($rows as $row) {
				$carrier = new MatchCarrier($this->db);
				$carrier->load($row['id']);
				$carrierArr[] = $carrier;
			}
			return $carrierArr;
		}		
	}
?>