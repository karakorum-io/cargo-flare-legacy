<?php
	/**************************************************************************************************
	* UserException class
	* This class for exceptions with message to user
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-10-28
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
	
	class UserException extends Exception {
		protected $redirectUrl = null;
		
		public function __construct($reason, $redirectUrl = null) {
			parent::__construct($reason);
			$this->redirectUrl = $redirectUrl;			
		}
		
		public function getRedirectUrl() {
			return $this->redirectUrl;
		}
	}
?>