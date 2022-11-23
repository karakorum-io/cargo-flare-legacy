<?php

/* * *************************************************************************************************
 * Manage accounts
 *
 *
 * Client: 	FreightDragon
 * Version: 	1.0
 * Date:    	2011-10-28
 * Author:  	C.A.W., Inc. dba INTECHCENTER
 * Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:	techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

require_once(DAFFNY_PATH . "libs/upload.php");

class Applicationaccounts extends ApplicationAction {
	public $title = "Accounts";
	public $section = "Accounts";
	public $tplname = "accounts.accounts.list";

	public function construct() {
		if (!$this->check_access('accounts')) {
			$this->setFlashError('Access Denied.');
			redirect(getLink());
		}
		return parent::construct();
	}

	/**
	 * List all

	 */
	public function idx() {
		$this->loadAccountPage('all');
	}

	public function import() {
		try {
			$this->breadcrumbs = $this->getBreadCrumbs(array(
				getLink('accounts') => "Accounts",
				getLink('accounts/carriers') => 'Carriers',
				'Import'
			));
			$this->tplname = "accounts.accounts.import";
			$this->title .= " - Import Account";
			if (count($_POST) > 0) {
				$this->isEmpty("company_name", "Company Name");
				$this->isEmpty("status", "Status");
				$this->isEmpty("member_id", "Carrier Account");
				if (post_var('email') != "") {
					$this->checkEmail('email', "Email");
				}
				if (!count($this->err)) {
					$insert_arr = $this->getInsertArray();
					$insert_arr['is_carrier'] = 1;
					$insert_arr['owner_id'] = getParentId();
					$insert_arr['member_id'] = post_var('member_id');
					$account = new Account($this->daffny->DB);
					$account->create($insert_arr);
					if (!$this->daffny->DB->isError) {
						$this->setFlashInfo("Account has been added.");
					} else {
						$this->setFlashError("Can't create account. Try again later, please.");
					}
				}
			}
			$this->form->TextField("company_name", 255, array("readonly" => "readonly"), "Company Name", "</td><td>");
			$this->form->TextField("contact_name", 255, array("readonly" => "readonly"), "Contact Name", "</td><td>");
			$this->form->ComboBox("status", Account::$status_name, array('style' => "width: 100px;"), $this->requiredTxt . "Status", "</td><td>");
			$this->form->TextArea("notes", 15, 10, array("style" => "height:100px; width:470px;"), "Notes", "</td><td>");
			$this->form->TextField("address1", 255, array("readonly" => "readonly"), "Address 1", "</td><td>");
			$this->form->TextField("address2", 255, array("readonly" => "readonly"), "Address 2", "</td><td>");
			$this->form->TextField("city", 255, array("readonly" => "readonly"), "City", "</td><td>");
			$this->form->TextField("state", 50, array("readonly" => "readonly"), "State", "</td><td>");
			$this->form->TextField("zip", 10, array("readonly" => "readonly"), "Zip/Postal Code", "</td><td>");
			$this->form->TextField("country", 255, array("readonly" => "readonly"), "Country", "</td><td>");
			$this->form->TextField("phone1", 100, array("readonly" => "readonly"), "Phone 1", "</td><td>");
			$this->form->TextField("phone2", 100, array("readonly" => "readonly"), "Phone 2", "</td><td>");
			$this->form->TextField("cell", 100, array("readonly" => "readonly"), "Cell Phone", "</td><td>");
			$this->form->TextField("fax", 100, array("readonly" => "readonly"), "Fax", "</td><td>");
			$this->form->TextField("email", 255, array("readonly" => "readonly"), "Email", "</td><td>");
			$this->form->CheckBox("donot_dispatch", array(), "Ban Carrier/Shipper/Location", "</td><td>");

			$this->form->TextField("insurance_companyname", 255, array(), "Name", "</td><td>");
			$this->form->TextField("insurance_address", 255, array(), "Address", "</td><td>");
			$this->form->TextField("insurance_phone", 255, array(), "Company Phone", "</td><td>");
			$this->form->ComboBox("insurance_holder", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Certificate Holder", "</td><td>");
			$this->form->ComboBox("insurance_insured", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Additionally Insured", "</td><td>");
			$this->form->TextField("insurance_agentname", 255, array(), "Agent Name", "</td><td>");
			$this->form->TextField("insurance_agentphone", 255, array(), "Agent Phone", "</td><td>");
			$this->form->TextField("insurance_policynumber", 30, array(), "Policy Number", "</td><td>");
			$this->form->TextField("insurance_expirationdate", 255, array("style" => "width:100px;"), "Expiration Date", "</td><td>");
			$this->form->ComboBox("insurance_contract", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Broker/Carrier Contract", "</td><td>");
			$this->form->TextField("insurance_iccmcnumber", 30, array(), $this->requiredTxt . "ICC MC Number", "</td><td>");
		} catch (FDException $e) {
			redirect(getLink('accounts'));
		}
	}

	public function carriers() {
		$this->importAccounts('carrier');
		$this->loadAccountPage('carrier');
	}

	public function locations() {
		$this->importAccounts('location');
		$this->loadAccountPage('location');
	}

	public function shippers() {
		$this->importAccounts('shipper');
		$this->loadAccountPage('shipper');
	}

	protected function importAccounts($type) {
		if (count($_FILES)) {
			$upload = new upload();
			$upload->out_file_dir = UPLOADS_PATH . "temp/";
			$upload->max_file_size = 50 * 1024 * 1024;
			$upload->form_field = "import";
			$upload->make_script_safe = 1;
			$upload->allowed_file_ext = array("xls", "xlsx", "csv");
			$upload->save_as_file_name = md5(time() . mt_rand()) . '_import';
			$upload->upload_process();
			try {
				switch ($upload->error_no) {
					case 0:
						$import = new Import();
						switch ($type) {
							case 'carrier':
								$result = $import->importCarriers($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
								break;
							case 'location':
								$result = $import->importLocations($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
								break;
							case 'shipper':
								$result = $import->importShippers($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
								break;
							default:
								die('ERROR: Invalid import type');
								break;
						}
						$this->input['success'] = $result['success'];
						$this->input['failed'] = $result['failed'];
						break;
					case 1:
						throw new RuntimeException('File not selected or empty.');
					case 2:
					case 5:
						throw new RuntimeException('Invalid File Extension');
					case 3:
						throw new RuntimeException('File too big');
					case 4:
						throw new RuntimeException('Cannot move uploaded file');
					default:
				}
			} catch (RuntimeException $e) {
				if (file_exists($upload->saved_upload_name)) {
					unlink($upload->saved_upload_name);
				}
				die('ERROR: ' . $e->getMessage());
			}
		}
	}

	public function inactive() {
		$this->loadAccountPage('inactive');
	}

	private function loadAccountPage($type) {
	
		try {
			$this->tplname = "accounts.accounts.list";
			
			$accountManager = new AccountManager($this->daffny->DB);
			
			$this->applyOrder("app_accounts");
			$where = "";
			switch ($type) {
				case 'all':
					$this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts"));
					break;
				case 'carrier':
					$this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Carriers'));
					$where .= " AND is_carrier=1 ";
					break;
				case 'location':
					$this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Locations'));
					$where .= " AND is_location=1 ";
					break;
				case 'shipper':
					$this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Shippers'));
					$where .= " AND is_shipper=1 ";
					break;
				case 'inactive':
					$this->breadcrumbs = $this->getBreadCrumbs(array(getLink('accounts') => "Accounts", 'Inactive'));
					$where .= " AND status=".Account::STATUS_INACTIVE;
					break;

			}
			if (isset($_POST['searchval'])) {
				$searchVal = trim(post_var('searchval'));
				$where .= " AND (company_name LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal)."%' ";
				$where .= " OR first_name LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal)."%' ";
				$where .= " OR last_name LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, $searchVal)."%') ";
				$this->input['searchval'] = $searchVal;
			}
			$this->form->TextField("searchval", 50);
			
			
			$accounts = $accountManager->get($this->order->getOrder(), $_SESSION['per_page'], "`owner_id` = " . getParentId() . " " . $where);
			$this->setPager($accountManager->getPager());
			$this->daffny->tpl->accounts = $accounts;
			$this->daffny->tpl->accountType = $type;
		} catch (FDException $e) {
		   
			redirect(SITE_IN);
		}
	}

	public function edit() {
		try {
			$ID = (int)get_var("id");
			$account = new Account($this->daffny->DB);
			$this->tplname = "accounts.accounts.form";
			$this->title .= ($ID > 0 ? " - Edit Account" : " - Add New Account");
			$this->check_access("accounts", "edit", array("id" => $ID));
            $account->load($ID);
			$sql_arr = $this->getInsertArray();
    

			if ($sql_arr['country'] == "US") {
				$sql_arr['state_other'] = "";
			} else {
				$sql_arr['state'] = "";
			}

			if ($sql_arr['insurance_expirationdate'] != "") {
				$sql_arr['insurance_expirationdate'] = $this->validateDate($sql_arr['insurance_expirationdate'], "Expiration Date");
			}

			$this->input = $sql_arr;
			if (isset($_POST['submit'])) {

				if ($sql_arr['is_shipper'] == 0) {
					$this->isEmpty("company_name", "Company Name");
				}

				if ($sql_arr['is_shipper'] == 1) {
					$this->isEmpty("shipper_type", "Shipper Type");
				}

				if ($sql_arr['is_carrier'] == 1) {
					$this->isEmpty("carrier_type", "Carrier Type");
					$this->isEmpty("insurance_iccmcnumber", "ICC MC Number and DLT");
				}

				$this->isEmpty("status", "Status");

				if ($sql_arr['is_carrier'] == 0 && $sql_arr['is_shipper'] == 0 && $sql_arr['is_location'] == 0) {
					$this->err[] = "Please choose account <strong>'Type'</strong>";
				}

				$this->checkEmail("email", "Email");
				if ($sql_arr['is_location'] == 1) {
					$this->isEmpty("city", "City");
					$this->isEmpty("location_type", "Location Type");

					if (post_var('country') == "US") {
						$this->isEmpty("state", "State");
					} else {
						$this->isEmpty("state_other", "State/Province");
					}
					$this->isEmpty("zip_code", "Zip/Postal Code");
					$this->isEmpty("country", "Country");
				}



								  
				if (!count($this->err)) {
					if ($ID > 0) {
						$this->updateHistory($sql_arr, $ID);
						/*******************************************/
						
						if($sql_arr['referred_id']!=$account->referred_id) 
						{
						 
									$member = new Member($this->daffny->DB);
									$member->load($account->owner_id);
									$where .= " AND ae.`assigned_id` IN (" . implode(', ', $member->getCompanyMembers($this->daffny->DB, $member->getParent()->id)) . ")";
									
									$query = " SELECT ac.id AS account_id, ae.id AS entity_id, ash.id AS shipper_id, ac.referred_id, ac.referred_by
												FROM  `app_accounts` AS ac
												INNER JOIN app_shippers AS ash 
												ON ash.company = ac.company_name 
												INNER JOIN app_entities AS ae 
												ON ae.shipper_id=ash.id AND ae.type=3 ".$where."
												WHERE ac.`is_shipper` =1
												AND ac.`referred_id` !=0
												AND ac.id='".$account->id."'";
												
												//AND ash.state=ac.state
							  
							        //print $query."<br>";
							  
									$result = $this->daffny->DB->query($query);
									if ($result) {
										while ($row = $this->daffny->DB->fetch_row($result)) {
											
											$entity = new Entity($this->daffny->DB);
											$entity->load($row['entity_id']);
											
											$update_arr = array(
												'referred_by' => $row['referred_by'],
												'referred_id' => $row['referred_id']
											);
											$entity->update($update_arr);
											
											print_r($update_arr);
											
										}
									}
									print "----";
									exit;
							 }
						   
						   $account->update($sql_arr, $ID);
						   
						/******************************************/
						//$account->update($sql_arr, $ID);
						$this->setFlashInfo("Account has been updated.");
					} else {
						$sql_arr['owner_id'] = getParentId();
						$account->create($sql_arr);
						$this->setFlashInfo("Account has been added.");
						$ID = $account->id;
					}
					if ($this->dbError()) {
						return;
					}
					redirect(getLink("accounts", "details", "id", $ID));
				}
			} else {
				if ($ID > 0) {
					
					if ($account->owner_id != getParentId()) {
						$this->setFlashError("Access denied.");
						redirect(getLink('accounts'));
					}
					$this->input = $account->getAttributes();
				}
			}
			$this->breadcrumbs = $this->getBreadCrumbs(array(
				getLink("accounts") => "Accounts",
				'' => ($ID > 0 ? htmlspecialchars($this->input['company_name']) : "Add New Account")
			));
			foreach ($this->input as $key => $value) {
				$this->input[$key] = htmlspecialchars($value);
			}
			$this->daffny->tpl->accountType = $this->input['type'];
			
			/*******************************************/
			$sql = "SELECT ac.id as id,
					               ac.reffered_by as shipper_id,
								   ac.reffered_by as members_id,
					               r.name as reffered_by,
								   ac.commision as commision,
								   ac.create_date as create_date,
								   m.contactname as contactname,
								   aa.company_name as company_name
                            FROM app_commision ac
                            LEFT JOIN app_accounts aa ON ac.shipper_id = aa.id 
							LEFT JOIN members m ON ac.members_id = m.id
							LEFT JOIN app_referrers as r ON r.id = ac.reffered_by
                            WHERE ac.is_deleted=0 and shipper_id='".$ID."'
                            ORDER BY ac.id desc";
					
					$rows = $this->daffny->DB->selectRows($sql);
					if (!is_array($rows) || $this->daffny->DB->isError) throw new FDException("DB query error");
					if (count($rows) > 0) {
						$commissionData = array();
						foreach ($rows as $row) {
							$tempArr  = array();
							$tempArr['id'] = $row['id'];
							$tempArr['shipper_id'] = $row['shipper_id'];
							$tempArr['members_id'] = $row['members_id'];
							$tempArr['reffered_by'] = $row['reffered_by'];
							$tempArr['commision'] = $row['commision'];
							$tempArr['create_date'] = $row['create_date'];
							$tempArr['contactname'] = $row['contactname'];
							$tempArr['company_name'] = $row['company_name'];
							$commissionData[] = $tempArr;
						}
						$this->daffny->tpl->commissionData = $commissionData;
					}
			
			$this->input['insurance_expirationdate'] = $this->getFormattedDate($this->input['insurance_expirationdate']);
			//1)
			$this->form->TextField("company_name", 255, array(), $this->requiredTxtCompany . "Company Name", "</td><td>");
			$this->form->TextArea("notes", 15, 10, array("style" => "height:100px; width:400px;"), "Notes", "</td><td>");
			$this->form->ComboBox("status", Account::$status_name, array('style' => "width: 100px;"), $this->requiredTxt . "Status", "</td><td>");
			$this->form->CheckBox("is_carrier", array(), "Carrier", "&nbsp;");
			$this->form->CheckBox("is_shipper", array(), "Shipper", "&nbsp;");
			$this->form->CheckBox("is_location", array(), "Location", "&nbsp;");
			$this->form->CheckBox("donot_dispatch", array(), "Ban Carrier/Shipper/Location", "&nbsp;");
			$this->form->TextField("rating", 1, array("style" => "width:25px;"), "Rating", "</td><td>");
			$this->form->TextField("first_name", 50, array(), "First Name", "</td><td>");
			$this->form->TextField("last_name", 50, array(), "Last Name", "</td><td>");
			$this->form->TextField("tax_id_num", 30, array(), "Tax ID", "</td><td>");
			//2)
			$this->form->TextField("contact_name1", 100, array(), "Contact 1", "</td><td>");
			$this->form->TextField("contact_name2", 100, array(), "Contact 2", "</td><td>");
			$this->form->TextField("phone1", 100, array("class" => "phone"), "Phone 1", "</td><td>");
			$this->form->TextField("phone2", 100, array("class" => "phone"), "Phone 2", "</td><td>");
			$this->form->TextField("cell", 100, array("class" => "phone"), "Cell Phone", "</td><td>");
			$this->form->TextField("fax", 100, array("class" => "phone"), "Fax", "</td><td>");
			$this->form->TextField("email", 255, array(), "Email", "</td><td>");
			$this->form->TextField("address1", 255, array(), "Address 1", "</td><td>");
			$this->form->TextField("address2", 255, array(), "Address 2", "</td><td>");
			
			// Additional
        $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
        $referrers_arr = array();
        foreach ($referrers as $referrer) {
            $referrers_arr[$referrer->id] = $referrer->name;
        }
		
		$this->form->ComboBox("referred_id", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55), "Referred By", "</td><td>");


			$this->form->TextField("city", 255, array("class" => "geo-city"), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "City", "</td><td>");
			$this->form->ComboBox("state", array("" => "Select State") + $this->getStates(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State", "</td><td>");
			$this->form->TextField("state_other", 50, array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "State/Province", "</td><td>");
			$this->form->TextField("zip_code", 10, array('style' => 'width:100px'), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Zip/Postal Code", "</td><td>");
			$this->form->ComboBox("country", $this->getCountries(), array(), ($sql_arr['is_location'] == 1 ? $this->requiredTxt : "") . "Country", "</td><td>");

			$this->form->TextField("print_name", 255, array(), "Print on check As", "</td><td>");
			//3)
			$this->form->TextField("insurance_companyname", 255, array(), "Name", "</td><td>");
			$this->form->TextField("insurance_address", 255, array(), "Address", "</td><td>");
			$this->form->TextField("insurance_phone", 255, array(), "Company Phone", "</td><td>");
			$this->form->ComboBox("insurance_holder", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Certificate Holder", "</td><td>");
			$this->form->ComboBox("insurance_insured", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Additionally Insured", "</td><td>");
			$this->form->TextField("insurance_agentname", 255, array(), "Agent Name", "</td><td>");
			$this->form->TextField("insurance_agentphone", 255, array(), "Agent Phone", "</td><td>");
			$this->form->TextField("insurance_policynumber", 30, array(), "Policy Number", "</td><td>");
			$this->form->TextField("insurance_expirationdate", 255, array("style" => "width:100px;"), "Expiration Date", "</td><td>");
			$this->form->ComboBox("insurance_contract", array(
				"" => "--Select one--",
				"1" => "Yes",
				"0" => "No"
			), array("style" => "width:80px;"), "Broker/Carrier Contract", "</td><td>");
			$this->form->TextField("insurance_iccmcnumber", 30, array(), $this->requiredTxt . "ICC MC Number", "</td><td>");

			//4)

			$this->form->TextField("hours_of_operation", 255, array(), "Hours of operation", "</td><td>");
			$this->form->TextField("location_type", 255, array(), $this->requiredTxt . "Location Type", "</td><td>");
			$this->form->ComboBox("carrier_type", array("" => "--Select one--") + Account::$carrier_types, array("style" => "width:221px;"), $this->requiredTxt . "Carrier Type", "</td><td>");
			$this->form->ComboBox("shipper_type", array("" => "--Select one--") + Account::$shipper_types, array("style" => "width:221px;"), $this->requiredTxt . "Shipper Type", "</td><td>");

            $this->form->MoneyField("insurance_liability_amount", 15, array(), "Liability Amount $", "</td><td>");
            $this->form->MoneyField("insurance_coverage", 15, array(), "Insurance Coverage $", "</td><td>");
            $this->form->MoneyField("insurance_cargo_deductible", 15, array(), "Cargo Deductible $", "</td><td>");
		} catch (FDException $e) {
			redirect(getLink('accounts'));
		}
	}

	public function details() {
		try {
			$ID = (int)get_var("id");
			$this->tplname = "accounts.accounts.details";
			$this->title = "Account Details";
			$this->check_access("accounts", "edit", array("id" => $ID));

			$account = new Account($this->daffny->DB);
			$account->load($ID);
			if ($account->owner_id != getParentId()) {
				$this->setFlashError("Access denied.");
				redirect(getLink('accounts'));
			}
			//$this->input = $account->getAttributes();
			$attributeArr = $account->getAttributes();
			$this->input = $attributeArr;
			$this->daffny->tpl->accountType = $attributeArr['type']; 
			$this->daffny->tpl->isShipper = $attributeArr['is_shipper'];
			
			$this->breadcrumbs = $this->getBreadCrumbs(array(
				getLink("accounts") => "Accounts",
				getLink("accounts", "edit", "id", $ID) => htmlspecialchars($account->company_name),
				'' => "Account Details"
			));
			$this->input['country_name'] = $this->getCountryByCode($this->input['country']);
			foreach ($this->input as $key => $value) {
				$this->input[$key] = htmlspecialchars($value);
			}
			$this->input['insurance_expirationdate'] = $this->getFormattedDate($this->input['insurance_expirationdate']);
			$this->input['insurance_holder'] = $this->getYesNo($this->input['insurance_holder']);
			$this->input['insurance_insured'] = $this->getYesNo($this->input['insurance_insured']);
			$this->input['insurance_contract'] = $this->getYesNo($this->input['insurance_contract']);

			if (trim($this->input['email']) != "") {
				$this->input['email'] = "<a href=\"mailto:" . $this->input['email'] . "\">" . $this->input['email'] . "</a>";
			}
		} catch (FDException $e) {
			redirect(getLink('accounts'));
		}
	}

	public function accounthistory() {
		$ID = (int)get_var("id");
		$this->tplname = "accounts.accounts.accounthistory";
		$this->title .= " - Account History";
		$this->check_access("accounts", "show", array('id' => $ID));

		$account = new Account($this->daffny->DB);
		$account->load($ID);
		if ($account->owner_id != getParentId()) {
			$this->setFlashError("Access denied.");
			redirect(getLink('accounts'));
		}
		$this->input = $account->getAttributes();
		$this->breadcrumbs = $this->getBreadCrumbs(array(
			getLink("accounts") => "Accounts",
			getLink("accounts", "details", "id", $ID) => htmlspecialchars($this->input['company_name']),
			'' => "Account History"
		));

		$this->applyPager("app_accounts_history h", "", "WHERE h.account_id='" . $ID . "'");
		$this->applyOrder("app_accounts_history");

		$sql
			= "SELECT h.*
					 , m.contactname AS changed_by_name
					 , f.commonname AS field_name
                     , DATE_FORMAT(h.change_date, '%m/%d/%Y %H:%i:%s') change_date
                  FROM app_accounts_history h
                  LEFT JOIN members m ON h.changed_by = m.id
                  LEFT JOIN app_fields f ON f.name = h.field_name AND f.table_name = 'app_accounts'
                 WHERE h.account_id='" . $ID . "' " . $this->order->getOrder() . $this->pager->getLimit();
		$this->getGridData($sql, false);
	}

	public function delete() {
		$out = array('success' => false);
		try {
			$ID = $this->checkId();
			$this->check_access("accounts", "delete", array("id" => $ID));
			$account = new Account($this->daffny->DB);
			$account->delete($ID, true);
			$out = array('success' => true);
		} catch (FDException $e) {
		}
		die(json_encode($out));
	}

	public function status() {
		$out = array('success' => false);
		try {
			$id = $this->checkId();
			$this->check_access("accounts", "update", array("id" => $id));
			$account = new Account($this->daffny->DB);
			$account->load($id);
			$this->updateHistory(array('status' => ($account->status == 1) ? 0 : 1), $id);
			$account->update(array('status' => ($account->status == 1) ? 0 : 1), $id);
			$out = array('success' => true);
		} catch (FDException $e) {
		}
		die(json_encode($out));
	}

	protected function updateHistory($new_arr, $ID) {
		$ins_arr = array();
		$old_arr = $this->daffny->DB->select_one("*", "app_accounts", "WHERE id = '" . $ID . "'");
		$change_date = date("Y-m-d H:i:s");
		$changed_by = $_SESSION['member_id'];
		foreach ($old_arr AS $key => $value) {
			if (isset($new_arr[$key])) {
				if ($new_arr[$key] != $old_arr[$key]) {
					$ins_arr[] = array(
						"account_id" => $ID,
						"field_name" => $key,
						"old_value" => $old_arr[$key],
						"new_value" => $new_arr[$key],
						"change_date" => $change_date,
						"changed_by" => $changed_by
					);
				}
			}
		}

		if (!empty($ins_arr)) {
			foreach ($ins_arr AS $arr) {
				$this->daffny->DB->insert("app_accounts_history", $arr);
			}
		}
	}

	protected function getInsertArray() {
		
		         $referrer_name_value = "";
				 
				
				if(post_var("referred_id")!=""){
					$row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . post_var("referred_id") . "'");
				   if (!empty($row_referrer)) 
					{
						 $referrer_name_value = $row_referrer['name']; 
						 
					}
				}
		
		$insert_arr = array(
			"company_name" => post_var("company_name"),
			"status" => (int)post_var("status"),
			"is_carrier" => (post_var("is_carrier") == "1" ? 1 : 0),
			"is_shipper" => (post_var("is_shipper") == "1" ? 1 : 0),
			"is_location" => (post_var("is_location") == "1" ? 1 : 0),
			"first_name" => post_var("first_name"),
			"last_name" => post_var("last_name"),
			"tax_id_num" => post_var("tax_id_num"),
			"notes" => post_var("notes"),
			"contact_name1" => post_var("contact_name1"),
			"contact_name2" => post_var("contact_name2"),
			"phone1" => post_var("phone1"),
			"phone2" => post_var("phone2"),
			"cell" => post_var("cell"),
			"fax" => post_var("fax"),
			"email" => post_var("email"),
			"address1" => post_var("address1"),
			"address2" => post_var("address2"),
			"city" => post_var("city"),
			"state" => post_var("state"),
			"state_other" => post_var("state_other"),
			"zip_code" => post_var("zip_code"),
			"country" => post_var("country"),
			"print_name" => post_var("print_name"),
			"insurance_companyname" => post_var("insurance_companyname"),
			"insurance_address" => post_var("insurance_address"),
			"insurance_phone" => post_var("insurance_phone"),
			"insurance_holder" => post_var("insurance_holder"),
			"insurance_insured" => post_var("insurance_insured"),
			"insurance_agentname" => post_var("insurance_agentname"),
			"insurance_agentphone" => post_var("insurance_agentphone"),
			"insurance_policynumber" => post_var("insurance_policynumber"),
			"insurance_expirationdate" => post_var("insurance_expirationdate"),
			"insurance_contract" => post_var("insurance_contract"),
			"insurance_iccmcnumber" => post_var("insurance_iccmcnumber"),
			"rating" => (int)post_var("rating"),
			"donot_dispatch" => (post_var("donot_dispatch") == "1" ? 1 : 0),
			"carrier_type" => post_var("carrier_type"),
			"shipper_type" => post_var("shipper_type"),
			"location_type" => post_var("location_type"),
			"hours_of_operation" => post_var("hours_of_operation"),
			"insurance_liability_amount" => post_var("insurance_liability_amount"),
			"insurance_coverage" => post_var("insurance_coverage"),
			"insurance_cargo_deductible" => post_var("insurance_cargo_deductible"),
			"referred_by" => $referrer_name_value,
			'referred_id' => post_var("referred_id"),
		);
		
		
        return $insert_arr;
	}

	public function uploads() {
		$ID = (int)get_var("id");
		$this->tplname = "accounts.accounts.uploads";
		$this->title .= " - Account Documents";
		$this->check_access("accounts", "show", array('id' => $ID));

		$account = new Account($this->daffny->DB);
		$account->load($ID);
		if ($account->owner_id != getParentId()) {
			$this->setFlashError("Access denied.");
			redirect(getLink('accounts'));
		}
		$this->breadcrumbs = $this->getBreadCrumbs(array(
			getLink("accounts") => "Accounts",
			getLink("accounts", "details", "id", $ID) => htmlspecialchars($this->input['company_name']),
			'' => "Account Documents"
		));

		$this->daffny->tpl->files = $this->getFiles($ID);
		$this->form->FileFiled("files_upload", array(), "Upload", "</td><td>");

		$this->form->TextField("mail_to", 255, array(), $this->requiredTxt . "Email", "</td><td>");
		$this->form->TextField("mail_subject", 255, array(), $this->requiredTxt . "Subject", "</td><td>");
		$this->form->TextArea("mail_body", 15, 10, array(), $this->requiredTxt . "Body", "</td><td>");
	}

	/* Upload documents */
	public function upload_file() {

		$id = (int)get_var("id");
		$upload = new upload();
		$upload->out_file_dir = UPLOADS_PATH . "accounts/";
		$upload->max_file_size = 3 * 1024 * 1024;
		$upload->form_field = "file";
		$upload->make_script_safe = 1;
		$upload->allowed_file_ext = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "tiff", "wpd");
		$upload->save_as_file_name = md5(time() . "-" . rand()) . time();
		$upload->upload_process();

		switch ($upload->error_no) {
			case 0:
			{
				//check storage space
				$license = new License($this->daffny->DB);
				$license->loadCurrentLicenseByMemberId(getParentId());
				$space = $license->getCurrentStorageSpace();
				$used = $license->getUsedStorageSpace();

				if ($used > $space) {
					die ("ERROR:Storage space exceeded.");
				} else {

					$sql_arr = array(
						'name_original' => $_FILES[$upload->form_field]['name'],
						'name_on_server' => $upload->save_as_file_name,
						'size' => $_FILES[$upload->form_field]['size'],
						'type' => $upload->file_extension,
						'date_uploaded' => "now()",
						'owner_id' => getParentId(),
						'status' => 0,
					);
					$ins_arr = $this->daffny->DB->PrepareSql("app_uploads", $sql_arr);
					$this->daffny->DB->insert("app_uploads", $ins_arr);
					$insid = $this->daffny->DB->get_insert_id();

					$this->daffny->DB->insert("app_accounts_uploads", array(
						"account_id" => $id,
						"upload_id" => $insid
					));

					$out = getFileImageByType($upload->file_extension) . " ";
					$out .= '<a href="' . getLink("accounts", "getdocs", "id", $insid) . '">' . $_FILES[$upload->form_field]['name'] . '</a>';
					$out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") ";
					$out .= '&nbsp;&nbsp;<a href="#" onclick="sendFile(\'' . $insid . '\', \'' . $sql_arr['name_original'] . '\')">Email</a>';
					$out .= "&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"return deleteFile('" . getLink("accounts", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
					die ("<li id=\"file-" . $insid . "\">" . $out . "</li>");
				}
			}
			case 1:
				die ("ERROR:File not selected or empty.");
			case 2:
			case 5:
				die ("ERROR:Invalid File Extension");
			case 3:
				die ("ERROR:File too big");
			case 4:
				die ("ERROR:Cannot move uploaded file");
		}
		exit;
	}

	public function delete_file() {
		$out = array('success' => false);
		$id = (int)get_var('id');
		try {
			if ($row = $this->daffny->DB->selectRow('*', "app_uploads", "WHERE id = '$id' AND owner_id = '" . getParentId() . "'")) {
				if ($this->daffny->DB->isError) {
					throw new Exception($this->getDBErrorMessage());
				} else {
					$file_path = UPLOADS_PATH . "accounts/" . $row["name_on_server"];
					$this->daffny->DB->delete('app_uploads', "id = '" . $id . "'");
					$this->daffny->DB->delete('app_accounts_uploads', "upload_id = '" . $id . "'");
					$out = array('success' => true);
					@unlink($file_path);
				}
			}
		} catch (FDException $e) {
		}
		die(json_encode($out));
	}

	protected function getFiles($id) {
		$sql
			= "SELECT u.*
                  FROM app_accounts_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.account_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
                 ORDER BY u.date_uploaded";
		$FilesList = $this->daffny->DB->selectRows($sql);
		$files = array();
		foreach ($FilesList as $i => $file) {
			$files[$i] = $file;
			$files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
			$files[$i]['size_formated'] = size_format($file['size']);
		}
		return $files;
	}

	public function getdocs() {
		$ID = (int)get_var("id");
		$file = $this->daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
		if (!empty($file)) {

			$file_path = UPLOADS_PATH . "accounts/" . $file["name_on_server"];
			$file_name = $file["name_original"];
			$file_size = $file["size"];
			if (file_exists($file_path)) {
				header("Content-Type: application; filename=\"" . $file_name . "\"");
				header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
				header("Content-Description: \"" . $file_name . "\"");
				header("Content-length: " . $file_size);
				header("Expires: 0");
				header("Cache-Control: private");
				header("Pragma: cache");
				$fptr = @fopen($file_path, "r");
				$buffer = @fread($fptr, filesize($file_path));
				@fclose($fptr);
				echo $buffer;
				exit(0);
			}
		}
		header("HTTP/1.0 404 Not Found");
		exit(0);
	}
	
	
	
	public function shippersComm() {
		$this->commision('shipper');
	}
	
	public function shippersCommEdit() {
		$this->commisionEdit('shipper');
	}
	
	function commision($type)
	{
		 	 try {
					$this->tplname = "accounts.accounts.commision";
					$this->title = "Account Commission";
					
					$ID = (int)get_var("id");
					if($ID=="")
					  $ID = get_var("shipper");
					
					/*
			        $account = new Account($this->daffny->DB);
					if ($ID > 0) {
						$account->load($ID);
						if ($account->owner_id != getParentId()) {
							$this->setFlashError("Access denied.");
							redirect(getLink('accounts'));
						}
					$this->input = $account->getAttributes();
					*/
					//
					$accountManager = new AccountManager($this->daffny->DB);
					$where .= " AND is_shipper=1 ";
					$accounts = $accountManager->get(" order by id desc", 10000, "`owner_id` = " . getParentId() . " " . $where);
					$this->daffny->tpl->accounts = $accounts;
					
					// Additional
					$referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
					$referrers_arr = array();
					foreach ($referrers as $referrer) {
						$referrers_arr[$referrer->id] = $referrer->name;
					}
					
					$commissionArr = array();
					for($i=1; $i<=100;$i++)
					  $commissionArr[$i] = $i." %";
					  
					  
					  	$sql_arr = array(
								"shipper_id" => (int)post_var("account"),
								"members_id" => (int)post_var("salesman"),
								"reffered_by" => post_var("referred_by"),
								"commision" => (int)post_var("commission"),
								"create_date" => date("Y-m-d H:i:s"),
							);
						
		              if (isset($_POST['submit'])) {

							if ($sql_arr['account'] == "") {
								$this->isEmpty("account", "Shipper");
							}
			
							if ($sql_arr['salesman'] == "") {
								$this->isEmpty("salesman", "User/Salesman");
							}
			          /*
							if ($sql_arr['commission'] == "") {
								$this->isEmpty("commission", "Commission");
								
							}
			   */
							if (!count($this->err)) {
								 $rows = $this->daffny->DB->selectRow("`id`", "`app_commision`", " where shipper_id='".$sql_arr['shipper_id']."' AND members_id='".$sql_arr['members_id']."'");
							
								if (!count($rows) > 0) {
									
										$this->daffny->DB->insert("app_commision", $sql_arr);
										//$insid = $this->daffny->DB->get_insert_id();
										
										if ($this->dbError()) {
											  return;
										 }
										 
										$this->setFlashInfo("Commission setting saved.");
										redirect(getLink("accounts", "edit","id",$ID));
								}
								else
								{
								    $this->setFlashInfo("Record already exist.");	
								}
                               
							}
							
					  }
					
					/*
					$sql = "SELECT ac.id as id,
					               ac.reffered_by as shipper_id,
								   ac.reffered_by as members_id,
					               ac.reffered_by as reffered_by,
								   ac.commision as commision,
								   ac.create_date as create_date,
								   m.contactname as contactname,
								   aa.company_name as company_name
                            FROM app_commision ac
                            LEFT JOIN app_accounts aa ON ac.shipper_id = aa.id 
							LEFT JOIN members m ON ac.members_id = m.id
                            WHERE ac.is_deleted=0
                            ORDER BY ac.id desc";
					
					$rows = $this->daffny->DB->selectRows($sql);
					if (!is_array($rows) || $this->daffny->DB->isError) throw new FDException("DB query error");
					if (count($rows) > 0) {
						$commissionData = array();
						foreach ($rows as $row) {
							$tempArr  = array();
							$tempArr['id'] = $row['id'];
							$tempArr['shipper_id'] = $row['shipper_id'];
							$tempArr['members_id'] = $row['members_id'];
							$tempArr['reffered_by'] = $row['reffered_by'];
							$tempArr['commision'] = $row['commision'];
							$tempArr['create_date'] = $row['create_date'];
							$tempArr['contactname'] = $row['contactname'];
							$tempArr['company_name'] = $row['company_name'];
							$commissionData[] = $tempArr;
						}
						$this->daffny->tpl->commissionData = $commissionData;
					}
					*/
					
					if (count($_POST)) {
							 $this->input['referred_by'] = $_POST['referred_by'];
							 $this->input['commission'] = $_POST['commission'];
					   }
					  
					
					$this->form->ComboBox("referred_by", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55,"onchange"=>"selectReferred();"), "Referred By", "</td><td>");
					//$this->form->ComboBox("commission", array("" => "Select Commission")+$commissionArr , array("style" => "width:150px;"), "Commission", "</td><td>");
			
		     } catch (FDException $e) {
		       print $e;
			 }
    }
	
	
	function commisionEdit($type)
	{
		 	 try {
					$this->tplname = "accounts.accounts.commisionedit";
					$this->title = "Account Commission Edit";
					
					$shipperID = get_var("shipper");
					
					//$account = new Account($this->daffny->DB);
					$accountManager = new AccountManager($this->daffny->DB);
					$where .= " AND is_shipper=1 ";
					$accounts = $accountManager->get(" order by id desc", 10000, "`owner_id` = " . getParentId() . " " . $where);
					$this->daffny->tpl->accounts = $accounts;
					
					// Additional
					$referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
					$referrers_arr = array();
					foreach ($referrers as $referrer) {
						$referrers_arr[$referrer->id] = $referrer->name;
					}
					
					$commissionArr = array();
					for($i=1; $i<=100;$i++)
					  $commissionArr[$i] = $i." %";
					  
					  
					  	$sql_arr = array(
								"shipper_id" => (int)post_var("account"),
								"members_id" => (int)post_var("salesman"),
								"reffered_by" => post_var("referred_by"),
								//"commision" => (int)post_var("commission"),
								"create_date" => date("Y-m-d H:i:s"),
							);
						
		              if (isset($_POST['submit'])) {

							if ($sql_arr['account'] == "") {
								$this->isEmpty("account", "Shipper");
							}
			
							if ($sql_arr['salesman'] == "") {
								$this->isEmpty("salesman", "User/Salesman");
							}
			/*
							if ($sql_arr['commission'] == "") {
								$this->isEmpty("commission", "Commission");
								
							}
			*/
							if (!count($this->err)) {
								
								$this->daffny->DB->update("app_commision", $sql_arr, "id='" .  (int)$_GET['id']."'");
				                //$insid = $this->daffny->DB->get_insert_id();
								
								if ($this->dbError()) {
						              return;
					             }
								 
			                    $this->setFlashInfo("Commission setting updated.");
                                redirect(getLink("accounts", "edit","id",$shipperID));
							}
							
					  }
					 
					  
					   if (count($_POST) == 0) {
						    $rows = $this->daffny->DB->selectRow("`id`, `shipper_id`, `members_id`, `reffered_by`, `commision`,`create_date`", "`app_commision`", " where id=".$_GET['id']);
							
							if (!is_array($rows) || $this->daffny->DB->isError) throw new FDException("DB query error");
							if (count($rows) > 0) {
							   
							  $this->input['referred_by'] = $rows['reffered_by'];
							  $this->input['commission'] = $rows['commision'];
							  $this->daffny->tpl->shipper_id = $rows['shipper_id'];
							  $this->daffny->tpl->members_id = $rows['members_id'];
							}
					   }
					   else
					   {
						 //  print_r($_POST);
						  $this->input['referred_by'] = $_POST['referred_by'];
						  $this->input['commission'] = $_POST['commission']; 
						  $this->daffny->tpl->shipper_id = $_POST['account'];
						  $this->daffny->tpl->members_id = $_POST['salesman'];
					   }
					
					$this->form->ComboBox("referred_by", array('' => 'Select One') + $referrers_arr, array('tabindex' => 55,"onchange"=>"selectReferred();"), "Referred By", "</td><td>");
					//$this->form->ComboBox("commission", array("" => "Select Commission")+$commissionArr , array("style" => "width:150px;"), "Commission", "</td><td>");
			
		     } catch (FDException $e) {
		       //print $e;
			 }
    }
	
   public function shippersCommDelete() {
	     $out = array('success' => false);
		try {
			$ID = $this->checkId();
			$this->check_access("accounts", "delete", array("id" => $ID));
			
			$this->daffny->DB->update("app_commision", array("is_deleted" => 1), "id = $ID");
            if ($this->daffny->DB->isError) {
                throw new Exception($this->getDBErrorMessage());
            }
			$out = array('success' => true);
		    die(json_encode($out));
			 
			                   // $this->setFlashInfo("Commission setting updated.");
                                //redirect(getLink("accounts", "shippersComm"));
		} catch (FDException $e) {
			print $e;
		}
		
	}
}