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
	
	class MatchCarrier extends FdObject {
		const TABLE = "app_match_carrier";
		protected $memberObjects = array();
		
		public static $attributeSentStatus = array(
			'0' => 'Mail Not Sent',
			'1' => 'Mail Sent'
		);
		
   
}
?>