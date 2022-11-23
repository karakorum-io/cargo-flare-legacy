<?php

class CompanyProfile extends FdObject
{

    const TABLE = "app_company_profile";
    const FROZEN_YES = 1;
    const FROZEN_NO = 0;

    const LICENSE_BROKER = 1;
    const LICENSE_CARRIER = 2;
    const LICENSE_BROKER_CARRIER = 3;

    public static $type_name = array(
        self::FROZEN_YES => "Yes",
        self::FROZEN_NO => "No",
    );

    public static $type_full_name = array(
        self::FROZEN_YES => "Frozen",
        self::FROZEN_NO => "Active",
    );

    public static $license_name = array(
        self::LICENSE_BROKER => "Broker",
        self::LICENSE_CARRIER => "Carrier",
        self::LICENSE_BROKER_CARRIER => "Broker & Carrier",
    );

    protected $memberObjects = array();

    public function getByOwnerId($owner_id)
    {
        if (!ctype_digit((string) $owner_id)) {
            throw new FDException("CompanyProfile->getByOwnerId: invalid owner ID");
        }

        if (is_null($this->db)) {
            throw new FDException("CompanyProfile->getByOwnerId: DB helper not set");
        }

        $profileId = $this->db->selectField("id", self::TABLE, "WHERE `owner_id` = " . (int) $owner_id);
        if (!$profileId) {
            throw new FDException("CompanyProfile->getByOwnerId: CompanyProfile not found in DB");
        }

        $this->load($profileId);
    }

    /**
     * @param bool $reload
     * @return DefaultSettings $settings
     */
    public function getDefaultSettings($reload = false)
    {
        if ($reload || !isset($this->memberObjects['default_settings'])) {
            $defaultSettings = new DefaultSettings($this->db);
            $defaultSettings->getByOwnerId($this->owner_id);
            $this->memberObjects['default_settings'] = $defaultSettings;
        }
        return $this->memberObjects['default_settings'];
    }

    public function getNextNumber()
    {
        $nextNumber = $this->max_lead_number + $this->getDefaultSettings()->lead_start_number + 1;
        $this->update(array('max_lead_number' => ($this->max_lead_number + 1)));
        return $nextNumber;
    }

    public static function searchByName($db, $companyName)
    {
        if (!($db instanceof mysql)) {
            throw new FDException("CompanyProfile::searchByName: invalid DB Helper");
        }

        $companyName = trim($companyName);
        if (empty($companyName)) {
            return array();
        }

        $rows = $db->selectRows("`id`, `companyname` as name", self::TABLE, "WHERE `companyname` LIKE('%" . mysqli_real_escape_string($db->connection_id, $companyName) . "%')");
        if ($db->isError) {
            throw new FDException("CompanyProfile::searchByName: MySQL query error");
        }

        return $rows;
    }

    public static function getCompanies($db, $companyIds)
    {
        if (!($db instanceof mysql)) {
            throw new FDException("CompanyProfile::getCompanies: invalid DB Helper");
        }

        if (!is_array($companyIds)) {
            throw new FDException("CompanyProfile::GetCompanies: invalid company IDs");
        }

        $rows = $db->selectRows("`id`, `companyname` as name", self::TABLE, "WHERE `id` IN (" . mysqli_real_escape_string($db->connection_id, implode(", ", $companyIds)) . ")");
        if ($db->isError) {
            throw new FDException("CompanyProfile::getCompanies: MySQL query error");
        }

        return $rows;
    }

    public function searchMembers($contactName = '')
    {
        if ($contactName == '') {
            return array();
        }

        $mm = new MembersManager($this->db);
        $result = $mm->getMembers("`parent_id` = {$this->owner_id} AND `contactname` LIKE ('" . mysqli_real_escape_string($this->db->connection_id, $contactName) . "%')");
        return $result;
    }

    /**
     * Get license type for Company
     *
     * @return int
     */
    public function getCompanyType()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $type = 1;
        if ($this->is_broker == "1") {
            $type = self::LICENSE_BROKER;
        }
        if ($this->is_carrier == "1") {
            $type = self::LICENSE_CARRIER;
        }
        if ($this->is_carrier == "1" && $this->is_broker == "1") {
            $type = self::LICENSE_BROKER_CARRIER;
        }
        return self::$license_name[$type];
    }

    /**
     * Get Status of Company Account (Active, Frozen)
     *
     * @return string
     */
    public function getAccountStatus()
    {

        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $st = self::$type_full_name[$this->is_frozen];
        if ($this->is_frozen == self::FROZEN_NO) {
            $st = "<span style=\"color:green\">" . $st . "</span>";
        }

        return $st;
    }

    /**
     * Return Monthly / Year Charge Amount
     *
     * @param $cfg array $this->daffny->cfg
     * @return float
     */
    public function getMonthlyYearlyPayment($cfg = array())
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $users = (int) $this->getAdditionalUsers();
        $lt = $this->getLicenseType();
        switch ($lt) {
            case self::LICENSE_BROKER:
                $payment = (float) $cfg["license_broker"];
                $payment += $users * $cfg["license_broker_add"];
                break;
            case self::LICENSE_CARRIER:
                $payment = (float) $cfg["license_carrier"];
                $payment += $users * $cfg["license_carrier_add"];
                break;
            case self::LICENSE_BROKER_CARRIER:
                $payment = (float) $cfg["license_brcarr"];
                $payment += $users * $cfg["license_brcarr_add"];
                break;

            default:
                $payment = (float) $cfg["license_broker"];
                $payment += $users * $cfg["license_broker_add"];
                break;
        }
        return $payment;
    }

    /**
     * Return Charge Type
     *
     * @param $cfg array $this->daffny->cfg
     * @return float
     */

    public function getMonthlyYearlyPaymentType($cfg = array())
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $users = (int) $this->getAdditionalUsers();
        $lt = $this->getLicenseType();

        //return "Monthly Payment";
        return "Yearly Payment";
        /*
        switch ($lt) {
        case self::LICENSE_BROKER:
        $payment = (float)$cfg["license_broker"];
        $payment += $users*$cfg["license_broker_add"];
        break;
        case self::LICENSE_CARRIER:
        $payment = (float)$cfg["license_carrier"];
        $payment += $users*$cfg["license_carrier_add"];
        break;
        case self::LICENSE_BROKER_CARRIER:
        $payment = (float)$cfg["license_brcarr"];
        $payment += $users*$cfg["license_brcarr_add"];
        break;

        default:
        $payment = (float)$cfg["license_broker"];
        $payment += $users*$cfg["license_broker_add"];
        break;
        }
         *
         */
        return $payment;
    }

    /**
     * Count additional users per license
     *
     * @return int
     * @throws FDException
     */
    public function getAdditionalUsers()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $users = 0;
        $r = $this->db->selectRow("COUNT(id) AS cnt", "members", "WHERE parent_id='" . (int) $this->owner_id . "' AND is_deleted <> 1 AND status = 'Active'");

        if (!empty($r)) {
            $users = $r["cnt"] - 1; // minus self owner
        }
        return $users;
    }

    public function AdditionalUsersAllowed()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $license = new License($this->db);
        $allowed_users = $license->getAdditionalLicenseUsersByMemberId($this->owner_id);
        $used_users = $this->getAdditionalUsers();

        if ($allowed_users > $used_users) {
            return true;
        } else {
            return false;
        }
    }

    public function getMemberSince($format = "M Y")
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $m = new Member($this->db);
        $m->load($this->owner_id);
        return date($format, strtotime($m->reg_date));
    }

    public function setFrozen()
    {
        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $this->update(array('is_frozen' => 1));
    }

    public function getCompanyLogo()
    {
        $id = getParentId();
        $image = UPLOADS_PATH . "company/" . $id . ".jpg";
        if (file_exists($image)) {
            return SITE_IN . "uploads/company/" . $id . "_small.jpg";
        } else {
            return SITE_IN."/styles/cargo_flare/logo.png";
        }
    }
}
