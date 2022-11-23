<?php

class Memberapp extends AppAction {

	public function __construct() {
		parent::__construct();
	}

	protected function authorize($email, $password) {
		$this->daffny->auth->email = $email;
		$this->daffny->auth->password = md5($password);
		$this->daffny->auth->use_redirect = false;

		if (!$member = $this->daffny->auth->authorise()) {
			$this->err[] = "E-mail or password is incorrect.";
			return false;
		}

		if ($member['status'] != "Active") {
			$this->daffny->auth->unload_member();

			$this->err[] = "This account is blocked.";
			return false;
		}

		switch ($member['chmod']) {
			case 2:
				if (!$id = $this->daffny->DB->selectValue("id", "members", "WHERE is_deleted = 0 AND id = " . $member['id'])) {
					$this->daffny->auth->unload_member();

					$this->err[] = "Hmm.. Your are not Member. I can't authorize you. Sorry.";
					return false;
				}
				$_SESSION['member_id'] = $id;
				break;
		}

		return $member;
	}

	/**
	 * Authorize member
	 *
	 */
	protected function changeStatus() {
		$id = $this->checkId();
		$this->daffny->DB->query("UPDATE members SET status = (CASE WHEN status = 'Active' THEN 'Inactive' ELSE 'Active' END) WHERE id = $id");
		exit();
	}

	/**
	 * Save member
	 *
	 * @param array $sql_arr
	 * @param int   $MemberID
	 *
	 * @return int
	 */
	protected function saveMember($sql_arr, $MemberID = 0) { //use only on registration
		$ref_code = @$sql_arr['ref_code'];
		$sql_arr_br = $sql_arr;
		$sql_arr = $this->daffny->DB->PrepareSql("members", $sql_arr_br);
		unset($sql_arr['password_confirm']);
		if (trim($sql_arr_br['password']) == "") {
			unset($sql_arr['`password`']);
		} else {
			$sql_arr["`password`"] = md5($sql_arr_br['password']);
		}
		if ($MemberID <= 0) {
			// Default access settings
			$sql_arr["access_accounts"] = 1;
			$sql_arr["access_dispatch"] = 1;
			$sql_arr["access_payments"] = 1;
			$sql_arr["access_lead_sources"] = 1;
			$sql_arr["access_preferences"] = 1;
			$sql_arr["access_users"] = 1;
			$sql_arr["access_reports"] = 1;
			
			$this->daffny->DB->insert("members", $sql_arr);
			$ins_id = $this->daffny->DB->get_insert_id();
			$this->daffny->DB->update("members", array("parent_id" => $ins_id), "id='" . $ins_id . "'");

			/* Dependent tables */

			$brcarr = array();
			if (isset($sql_arr_br['type'])) {
				if ($sql_arr_br['type'] == 1) {
					$brcarr['is_carrier'] = 0;
					$brcarr['is_broker'] = 1;
				}
				if ($sql_arr_br['type'] == 2) {
					$brcarr['is_carrier'] = 1;
					$brcarr['is_broker'] = 1;
				}
				if ($sql_arr_br['type'] == 3) {
					$brcarr['is_carrier'] = 1;
					$brcarr['is_broker'] = 0;
				}
			}
			$member = new Member($this->daffny->DB);
			$member->load($ins_id);
			$brcarr["is_frozen"] = ($member->licenseIsActive())?0:1;

			$this->daffny->DB->insert("app_company_profile", $brcarr + array("owner_id" => $ins_id, "companyname" => $sql_arr['`companyname`'], "ref_code" => chr(rand(65, 90)) . chr(rand(65, 90)) . $ins_id . chr(rand(65, 90))));
			$this->daffny->DB->insert("app_defaultsettings", array("owner_id" => $ins_id));
			$this->daffny->DB->insert("app_externalforms", array("owner_id" => $ins_id, "hash" => $ins_id . md5("fright" . date("YmdHis"))));
			$this->daffny->DB->insert("app_autoquoting_settings", array("owner_id" => $ins_id));
			//default email templates with attachments & form templates
			EmailTemplate::createEmailAndFormTemplates($ins_id, $this->daffny->DB);
			//default referrers
			$qr = $this->daffny->DB->select("*", "app_referrers", "WHERE is_default = '1'");
			while ($rowr = $this->daffny->DB->fetch_row($qr)) {
				$insr = array(
					"owner_id" => $ins_id
				, "name" => $rowr["name"]
				, "description" => $rowr["description"]
				);
				$this->daffny->DB->insert("app_referrers", $insr);
			}

			//referer
			if ($ref_code != "") {

				$ref = $this->daffny->DB->select_one("owner_id", "app_company_profile", "WHERE ref_code = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $ref_code) . "'");
				if (isset($ref['owner_id'])) {
					$this->daffny->DB->update("members", array("referer_id" => $ref['owner_id']), "id='" . $ins_id . "'");
				}
			}

			//$this->daffny->DB->transaction("commit");
			return $ins_id;
		}
		$this->daffny->DB->update("members", $sql_arr, "id = {$MemberID}");
		return $MemberID;
	}

	/**
	 * Send welcome email to member
	 *
	 * @param string $toName
	 * @param string $toEmail
	 * @param array  $tplData
	 * @param string $mod
	 *
	 * @return bool
	 */
	protected function sendWelcomeEmail($toName, $toEmail, $tplData) {
		$subject = 'Welcome';
		return $this->sendEmail($toName, $toEmail, $subject, "welcome", $tplData);
	}

	/**
	 * send forgot password email
	 *
	 * @param string $subject
	 * @param array  $tplData
	 * @param string $tplName
	 *
	 * @return bool
	 */
	protected function sendForgotPasswordEmail($toName, $toEmail, $subject, $tplData, $tplName) {
		$tplData['name'] = $toName;

		return $this->sendEmail($toName, $toEmail, $subject, $tplName, $tplData);
	}

	/**
	 * Check if email already in database
	 *
	 * @param string $email
	 */
	protected function checkDuplicateEmail($email, $notId = 0) {
		$where_add = $notId > 0 ? " AND id != " . $notId : "";
		if ($notId == 0) {
			$where_add = !isGuest() ? " AND id != " . getMemberId() : "";
		}

		$row = $this->daffny->DB->select_one("id", "members", "WHERE email = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $email) . "'" . $where_add);
		if (!empty($row)) {
			return true;
		}

		return false;
	}

	protected function checkDuplicateUsername($un, $notId = 0) {
		$where_add = $notId > 0 ? " AND id != " . $notId : "";
		if ($notId == 0) {
			$where_add = !isGuest() ? " AND id != " . getMemberId() : "";
		}

		$row = $this->daffny->DB->select_one("id", "members", "WHERE username = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $un) . "'" . $where_add);
		if (!empty($row)) {
			return true;
		}

		return false;
	}

	protected function validateMember($sql_arr, $id = 0) {
		$this->checkEmail2($sql_arr["email"], "E-mail");
		if ($sql_arr['email'] != "") {
			if ($this->checkDuplicateEmail($sql_arr['email'], $id)) {
				$this->err[] = "<strong>E-mail</strong> already registered.";
			}
		}

		if ($sql_arr['email'] != "") {
			if ($this->checkDuplicateUsername($sql_arr['username'], $id)) {
				$this->err[] = "<strong>Username</strong> already registered.";
			}
		}

		if ($sql_arr['password'] != "" && $sql_arr['password'] != $sql_arr['password_confirm']) {
			$this->err[] = "<strong>Passwords does not match.</strong>";
		}
	}

}

?>