<?php

class Billing extends FdObject
{

    const TABLE = "app_billing";
    const TYPE_PAYMENT = 1;
    const TYPE_CHARGE = 2;

    protected $memberObjects = array();

    public static $type_name = array(
        self::TYPE_PAYMENT => "Payment",
        self::TYPE_CHARGE => "Charge",
    );

    public function getCompany($reload = false)
    {
        if ($reload || !isset($this->memberObjects['company'])) {
            $company = new CompanyProfile($this->db);
            $company->getByOwnerId($this->owner_id);
            $this->memberObjects['company'] = $company;
        }
        return $this->memberObjects['company'];
    }

}
