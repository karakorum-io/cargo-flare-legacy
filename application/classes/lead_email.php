<?php
	/**************************************************************************************************
	* LeadEmail class																																					*
	* This class represent lead original email																													*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-10-17																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/
	
	class LeadEmail extends FdObject {
		const TABLE = "app_leads_email";
		
		public function getReceived($format = "m/d/Y h:i:s") {
			return date($format, strtotime($this->received));
		}
	}
?>