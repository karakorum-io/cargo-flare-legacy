<?php

/* * ************************************************************************************************
 * Member class																																						*
 * This class represent one member																															*
 * 																																											*
 * Client:		FreightDragon																																	*
 * Version:		1.0																																					*
 * Date:			2011-10-06																																		*
 * Author:		C.A.W., Inc. dba INTECHCENTER																											*
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
 * E-mail:		techsupport@intechcenter.com																											*
 * CopyRight 2011 FreightDragon. - All Rights Reserved																								*
 * ************************************************************************************************* */

/**
 * @property int $id
 * @property int $parent_id
 * @property int $group_id
 * @property string $email
 * @property string $password
 * @property string $contactname
 * @property string $companyname
 * @property string $username
 */
class Member extends FdObject {

	const TABLE = "members";

	protected $memberObjects = array();

	public function setRecordsPerPage($per_page = 100) {
		$this->update(array('records_per_page' => (int)$per_page));
	}

	public function getDefaultSettings() {
		if (!$this->loaded)
			throw new FDException("Member->getDefaultSettings: Object not loaded");
		$defaultSettings = new DefaultSettings($this->db);
		$ownerId = ($this->parent_id == 0) ? $this->id : $this->parent_id;
		$defaultSettings->getByOwnerId($ownerId);
		return $defaultSettings;
	}

	public function getCompanyProfile() {
		if (!$this->loaded)
			throw new FDException("Member->getCompanyProfile: Object not loaded");
		$companyProfile = new CompanyProfile($this->db);
		$ownerId = ($this->parent_id == 0) ? $this->id : $this->parent_id;
		$companyProfile->getByOwnerId($ownerId);
		return $companyProfile;
	}

	public function getCompanyProfileById($ownerId = null) {
		if ((int)$ownerId <= 0) {
			throw new FDException("Member->getCompanyProfile: Object not loaded");
		}
		$companyProfile = new CompanyProfile($this->db);
		$companyProfile->getByOwnerId($ownerId);
		return $companyProfile;
	}

	public function getOwnerId() {
		return ($this->parent_id == 0) ? $this->id : $this->parent_id;
	}

	public function getParent() {
		if (($this->parent_id == $this->id) || !ctype_digit((string)$this->parent_id))
			return $this;
		$member = new Member($this->db);
		$member->load($this->parent_id);
	}

	/**
	 * @param bool $reload
	 * @return AutoQuotingSettings
	 */
	public function getAutoQuotingSettings($reload = false) {
		if ($reload || !isset($this->memberObjects['auto_quoting_settings'])) {
			$aqs = new AutoQuotingSettings($this->db);
			$aqs->loadByOwnerId($this->id);
			$this->memberObjects['auto_quoting_settings'] = $aqs;
		}
		return $this->memberObjects['auto_quoting_settings'];
	}

	public function getRegDate($format = "M,d Y") {
		return date($format, strtotime($this->reg_date));
	}

	public static function getCompanyMembers($db, $parent_id, $where = "", $with_name = false) {
		if (!($db instanceof mysql))
			throw new FDException("Invalid DB Helper");
		if (!ctype_digit((string)$parent_id))
			throw new FDException("Invalid Member ID");
		if (trim($where != "")) {
			$where = " AND " . $where;
		}
		$member_ids = $db->selectRows('`id`, `contactname`', self::TABLE, "WHERE `parent_id` = '" . (int)$parent_id . "' " . $where);
		$result = array();
		foreach ($member_ids as $member_id) {
			if ($with_name) {
				$result[$member_id['id']] = $member_id['contactname'];
			} else {
				$result[] = $member_id['id'];
			}
		}
		return $result;
	}

	/**
	 * Get list of inactive users when license renew
	 * @param type $parent_id
	 * @param type $users
	 * @param type $renewal_users
	 * @return type
	 * @throws FDException
	 */
	final public function getNextInactiveUsers($parent_id, $renewal_users, $remove = false) {
		if (!($this->db instanceof mysql))
			throw new FDException("Invalid DB Helper");
		if (!ctype_digit((string)$parent_id))
			throw new FDException("Invalid Member ID");

		$limit = " LIMIT $renewal_users, 100";

		$member_ids = $this->db->selectRows('`id`, `contactname`', self::TABLE, "WHERE `id` <> '" . (int)$parent_id . "' AND `parent_id` = '" . (int)$parent_id . "' AND is_deleted <> 1 AND status='Active' ORDER BY id DESC " . $limit);

		if ($remove) {
			$result = array();
			foreach ($member_ids as $member) {
				$inactm = new Member($this->db);
				$inactm->load($member["id"]);
				$inactm->update(array("status" => "Inactive"));
				$result[] = $member['id'];
			}
			return $result;
		} else {
			$result = "";
			foreach ($member_ids as $member) {
				$result .= $member['contactname'] . "<br />";
			}
			if ($result == "") {
				return "No one";
			} else {
				return $result;
			}
		}
	}

    /**
     * Reload member session from DB
     */

    public function reloadMemberSession(){
        $_SESSION["member"] = $this->attributes;
    }

	/**
	 * Check Login Restrictions
	 *
	 * @return bool
	 */
	public function checkLoginRestrictions() {
		$allowed = false;
		if (is_null($this->db))
			throw new FDException(get_class($this) . "->load: DB helper not set");
		if (!$this->loaded)
			throw new FDException("Member->checkLoginRestrictions: Object not loaded");
		if ($this->is_deleted) {
			return false;
		}
		if ($this->id == $this->parent_id) { //if admin of license
			$allowed = true;
		}
		if ($this->loginr_enable != 1) {
			$allowed = true;
		} else { //Enable login restrictions
			$ld = "loginr_day" . (int)date("N");
			$tm = (int)strtotime(date("H:i"));
			$tm_from = (int)strtotime($this->loginr_time_from);
			$tm_to = (int)strtotime($this->loginr_time_to);

			if ($this->{$ld} == "1") { //check days
				if ($tm_from <= $tm && $tm_to >= $tm) { //check hours
					$allowed = true;
				}
			}
		}
		return $allowed;
	}

	public function licenseIsActive() {
		$row = $this->db->selectRow("COUNT(*) as cnt", "" . License::TABLE . " l INNER JOIN " . self::TABLE . " m ON (l.owner_id = m.parent_id)", "WHERE l.expire >= NOW()");
		return ($row['cnt'] > 0);
	}

	public function getPasswordHint($email) {
		$row = $this->db->selectRow("`password_hint`", "" . self::TABLE . "", "WHERE `email` = '" . mysqli_real_escape_string($this->db->connection_id, $email) . "' ");
		if (isset($row["password_hint"])) {
			return $row["password_hint"];
		} else {
			return "";
		}
	}

	public function isAutoQuoteAllowed() {
		$settings = new AutoQuotingSettings($this->db);
		$settings->loadByOwnerId($this->parent_id);
		$license = new License($this->db);
		$license->loadCurrentLicenseByMemberId($this->parent_id);
		return ($settings->is_enabled == 1 && $license->addon_aq_id > 0 );
	}
	
  public static function getCompanyMembersByStatus($db, $parent_id, $where = "", $with_name = false) {
		if (!($db instanceof mysql))
			throw new FDException("Invalid DB Helper");
		if (!ctype_digit((string)$parent_id))
			throw new FDException("Invalid Member ID");
		if (trim($where != "")) {
			$where = " AND " . $where;
		}
		
		$member_ids = $db->selectRows('`id`, `contactname`', self::TABLE, "WHERE `parent_id` = '" . (int)$parent_id . "' " . $where."ORDER BY status,contactname ASC");
		$result = array();
		foreach ($member_ids as $member_id) {
			if ($with_name) {
				$result[$member_id['id']] = $member_id['contactname'];
			} else {
				$result[] = $member_id['id'];
			}
		}
		return $result;
	}
}