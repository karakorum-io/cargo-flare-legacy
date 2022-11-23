<?php

/**
 * ******************************************************************************************************
 * Administrators Management CP Class																	*
 * 																										*
 * Client:		FreightDragon																			*
 * Version:		1.0																						*
 * Date:		2011-09-28																				*
 * Author:		C.A.W., Inc. dba INTECHCENTER															*
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076								*
 * E-mail:		techsupport@intechcenter.com															*
 * CopyRight 2011 FreightDragon. - All Rights Reserved													*
 * **************************************************************************************************** */

class CpAdmins extends CpAction {

	public $title = 'Administrators';
	private $__requiredFields = array(
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'email' => 'E-mail address',
		'confirm_email' => 'Confirm E-mail address',
		'password' => 'Password',
		'password_confirm' => 'Confirm Password',
		'address' => 'Home Street Address',
		'city' => 'City',
		'state' => 'State',
		'zip' => 'Zip Code',
		'cell_phone' => 'Cell Phone',
		'status' => 'Status',
		'IsCustomerService' => 'Is Customer Service',
		'group_id' => 'Group'
	);

	public function construct() {
		if ($_SESSION['member']['group_id'] != 2)
			redirect('/cp');
		return parent::construct();
	}

	public function idx() {
		$this->tplname = 'admins.list';

		$this->applyPager("administrators a");
		$this->applyOrder("administrators");
		$this->order->Fields[] = 'fullname';
		$this->order->Fields[] = 'groupname';

		$sql = "
			SELECT a.*
				 , CONCAT(a.first_name, ' ', a.last_name) AS fullname
				 , (CASE WHEN a.last_login IS NULL THEN 'N/A' ELSE DATE_FORMAT(a.last_login, '%m/%d/%Y %l:%i %p') END) AS last_login_show
				 , ag.name AS groupname
			  FROM administrators a
				   JOIN administrator_groups ag ON ag.id = a.group_id
				   " . $this->order->getOrder() . "
				   " . $this->pager->getLimit() . "
		";
		$this->getGridData($sql, false);
	}

	public function edit() {
		$id = (int) get_var("id");

		$this->title .= ($id > 0 ? ' - Edit' : ' - Add');
		$this->tplname = 'admins.form';

		if (!isset($_POST['submit']) && $id > 0) {
			$this->input = $this->daffny->DB->selectRow("*", "administrators", "WHERE id = " . $id);
			unset($this->input['password']);
		} else {
			$this->input = $this->SaveFormVars();
		}

		if (isset($_POST['submit'])) {
			$sql_arr = $this->getTplPostValues();

			foreach ($this->__requiredFields as $field => $name) {
				if ($id > 0 && in_array($field, array('confirm_email', 'password', 'password_confirm'))) {
					continue;
				}
				$this->isEmpty($field, $name);
			}

			$this->checkEmail("email", 'E-mail');

			if ($id == 0) {
				if ($sql_arr['email'] != $sql_arr['confirm_email']) {
					$this->err[] = 'Fields <strong>E-mail</strong> and <strong>Confirm E-mail address</strong> do not match.';
				}
			}
			if ($sql_arr['password'] != $sql_arr['password_confirm']) {
				$this->err[] = 'Fields <strong>Password</strong> and <strong>Confirm password</strong> do not match.';
			}

			if ($sql_arr['email'] != '') {
				if (!preg_match("/^[-a-zA-Z0-9._]+@freightdragon\.com$/", $sql_arr['email'])) {
					$this->err[] = 'Field <strong>E-mail</strong> is invalid<br>domain must be freightdragon.com';
				}
				$dups = $this->daffny->DB->selectValue("COUNT(*)", "administrators", "WHERE email = '".mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['email'])."' AND id <> ".$id);
				if ($dups > 0) {
					$this->err[] = 'E-mail <strong>'.$sql_arr['email'].'</strong> already exists.';
				}
			}

			if (!count($this->err)) {
				if ($sql_arr['password'] != '') {
					$sql_arr['password'] = md5($sql_arr['password']);
				} else {
					unset($sql_arr['password']);
				}

				$sql_arr = $this->daffny->DB->prepareSql("administrators", $sql_arr);
				if ($id > 0) {
					$this->daffny->DB->update("administrators", $sql_arr, "id = " . $id);
					if ($this->dbError()) {
						return;
					}
					$this->setFlashInfo("Administrator has been updated.");
				} else {
					$this->daffny->DB->insert("administrators", $sql_arr);
					if ($this->dbError()) {
						return;
					}
					$this->setFlashInfo("Administrator has been created.");
				}
				redirect(getLink("admins"));
			}
		}

		$add = array('autocomplete' => 'off');
		$states = $this->getStates();
		if ($id <= 0) {
			if (!isset($_POST['submit'])) {
				$add['autofocus'] = true;
			}
			$states = array_merge(array('' => 'Select One'), $states);
		}

		$this->form->TextField("first_name", 50, $add, $this->__getLabel('first_name'), "<br />");
		$this->form->TextField("last_name", 50, array('autocomplete' => 'off'), $this->__getLabel('last_name'), "<br />");
		$this->form->TextField("email", 255, array('autocomplete' => 'off'), $this->__getLabel('email'), "<br />");
		if ($id == 0) {
			$this->form->TextField("confirm_email", 255, array('autocomplete' => 'off'), $this->__getLabel('confirm_email'), "<br />");
		} else {
			$this->input['confirm_email'] = '';
		}
		$this->form->TextField("address", 255, array('autocomplete' => 'off', 'style' => 'width: 451px'), $this->__getLabel('address'), "<br />");
		$this->form->TextField("city", 50, array('autocomplete' => 'off'), $this->__getLabel('city'), "<br />");
		$this->form->ComboBox("state", $states, array(), $this->__getLabel('state'), "<br>");
		$this->form->TextField("zip", 10, array('autocomplete' => 'off', 'class'=>'zip'), $this->__getLabel('zip'), "<br />");
		$this->form->TextField("cell_phone", 15, array('autocomplete' => 'off', 'class'=>'phone'), $this->__getLabel('cell_phone'), "<br />");
		$this->form->ComboBox("group_id", $this->__getGroups(), array(), $this->__getLabel('group_id'), "<br>");
		$this->form->ComboBox("status", array('Active' => 'Active', 'Inactive' => 'Inactive'), array(), $this->__getLabel('status'), "<br>");
		$this->form->ComboBox("IsCustomerService", array('Yes' => 'Yes', 'No' => 'No'), array(), $this->__getLabel('IsCustomerService'), "<br>");
		$this->form->CheckBox("email_notify", array(), "Email notify", " &nbsp;");

		if ($id > 0) {
			$pass1_label = "Change password";
			$pass2_label = "Confirm password";
		} else {
			$pass1_label = $this->requiredTxt . "Password";
			$pass2_label = $this->requiredTxt . "Confirm password";
		}

		$this->form->PasswordField("password", 15, array(), $pass1_label, "<br />");
		$this->form->PasswordField("password_confirm", 15, array(), $pass2_label, "<br />");
	}

	public function delete() {
		$ID = $this->checkId();
		$out = array('success' => false);
		try {
			$this->daffny->DB->delete("members", "id = $ID");
			if ($this->daffny->DB->isError) {
				throw new Exception($this->getDBErrorMessage());
			} else {
				$out = array('success' => true);
			}
		} catch (FDException $e) {
			
		}
		die(json_encode($out));
	}

	public function status() {
		$id = (int) get_var("id");
		$this->daffny->DB->query("UPDATE administrators SET status = (CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END) WHERE id = " . $id);
		die(json_encode(array('success' => true)));
	}

	private function __getLabel($name) {
		$out = '';
		if (array_key_exists($name, $this->__requiredFields)) {
			$out .= $this->requiredTxt . $this->__requiredFields[$name];
		}
		return $out;
	}

	private function __getGroups() {
		$groups = array();
		$result = $this->daffny->DB->selectRows("id, name", "administrator_groups", "ORDER BY id", "id");
		foreach ($result as $key => $values) {
			$groups[$key] = htmlspecialchars($values['name']);
		}
		return $groups;
	}

}
