<?php

/**
 * ******************************************************************************************************
 * Logout CP Class																						*
 * 																										*
 * Client:		FreightDragon																			*
 * Version:		1.0																						*
 * Date:		2011-09-28																				*
 * Author:		C.A.W., Inc. dba INTECHCENTER															*
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076								*
 * E-mail:		techsupport@intechcenter.com															*
 * CopyRight 2011 FreightDragon. - All Rights Reserved													*
 * **************************************************************************************************** */
class CpLogout extends CpAction {

	public function idx() {
		$this->daffny->auth->logout();
		redirect(getLink());
	}

}
