<?php
/**************************************************************************************************
* Source class
* This class represent lead source
*
* Client:		FreightDragon
* Version:		1.0
* Date:			2011-09-28
* Author:		C.A.W., Inc. dba INTECHCENTER
* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:		techsupport@intechcenter.com
* CopyRight 2011 FreightDragon. - All Rights Reserved
***************************************************************************************************/
class Source extends FdObject {
	const TABLE = "app_leadsources";

	public function load($id = null) {
		if (!ctype_digit((string)$id)) {
			$this->attributes['company_name'] = 'not available';
			$this->loaded = true;
		} else {
			parent::load($id);
		}
	}
}
?>