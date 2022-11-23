<?php

/* * ************************************************************************************************
 * Billing class
 * This class represent Billing Info
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-12-06
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class Billing extends FdObject {

    const TABLE = "app_billing";
    const TYPE_PAYMENT = 1;
    const TYPE_CHARGE = 2;
    
    protected $memberObjects = array();

    public static $type_name = array(
        self::TYPE_PAYMENT => "Payment",
        self::TYPE_CHARGE => "Charge"
    );
    
    public function getCompany($reload = false) {
        if ($reload || !isset($this->memberObjects['company'])) {
            $company = new CompanyProfile($this->db);
            $company->getByOwnerId($this->owner_id);
            $this->memberObjects['company'] = $company;
        }
        return $this->memberObjects['company'];
    }
    
    
}