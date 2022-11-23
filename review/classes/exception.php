<?php
	/**************************************************************************************************
	* FDException class																																							*
	* This class for runtime Freight Dragon exceptions																																*
	*																																											*
	* Client:		FreightDragon																																	*
	* Version:		1.0																																					*
	* Date:			2011-10-28																																		*
	* Author:		C.A.W., Inc. dba INTECHCENTER																											*
	* Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
	* E-mail:		techsupport@intechcenter.com																											*
	* CopyRight 2011 FreightDragon. - All Rights Reserved																								*
	***************************************************************************************************/
	class FDException extends Exception {
		const FILE_NAME = "fd_exceptions.log";
		public function __construct($reason, $code = 0) {
			parent::__construct($reason);
			$trace = $this->getTrace();
			$this->message = $trace[0]["class"]."->".$trace[0]["function"].": ".$this->message;
			$this->saveToFile();
		}
		
		public function saveToFile() {
			$string = date("Y-m-d H:i:s")."\t".parent::getFile()." : ".parent::getLine()."\n\t\t\t".parent::getMessage()."\nTrace:".parent::getTraceAsString()."\n\n";
			file_put_contents(ROOT_PATH.self::FILE_NAME, $string, FILE_APPEND);
		}
	}
?>