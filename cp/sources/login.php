<?php

/* * *************************************************************************************************
 * Authorize CP Class                                                                 					*
 *                                                                              					   *
 *                                                                                                  *
 * Client: 	FreightDragon                                                                          *
 * Version: 	1.0                                                                                    *
 * Date:    	2011-09-29                                                                             *
 * Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
 * E-mail:	techsupport@intechcenter.com                                                           *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                              *
 * ************************************************************************************************** */
class CpLogin extends CpAction {


	public function idx() {

		if (isset($_SESSION["admin_here"]) && $_SESSION["admin_here"] === true) {
			redirect(getLink());
		}

		$this->tplname = "login";
		$this->input = $this->SaveFormVars();
		$this->form->TextField("email", 50, array('style' => "width: 180px;", 'tabindex' => 1), "E-mail", "</td><td>");
		$this->form->PasswordField("password", 20, array('style' => "width: 180px;", 'tabindex' => 2), "Password", "</td><td>");
		$this->input['error'] = "";

		if (!isset($_POST['submit'])) {
			return;
		}

		$email = post_var("email");
		if (!validate_email($email)) {
			$this->input['error'] = "Email is incorrect.";
			return;
		}

		$this->daffny->auth->use_redirect = false;
		$this->daffny->auth->tableName = 'administrators';
		$this->daffny->auth->email = mysqli_real_escape_string($this->daffny->DB->connection_id, $email);
		$this->daffny->auth->password = md5(post_var("password"));

		if (!$member = $this->daffny->auth->authorise()) {
			$this->input['error'] = "E-mail or password are incorrect.";
			return;
		}

		if ($member['status'] != "Active") {
			$this->daffny->auth->unload_member();
			$this->input['error'] = "Account is blocked.";
			return;
		}
		$_SESSION["admin_here"] = true;
		redirect(getLink());
	}

}
