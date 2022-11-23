<?php

/* * ************************************************************************************************
 * Rating class
 * This class represent rating record in DB
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-28
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class Rating extends FdObject {

    const TABLE = "app_ratings";
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_PENDING = 2;
    const TYPE_POSITIVE = 2;
    const TYPE_NEUTRAL = 1;
    const TYPE_NEGATIVE = 0;

    protected $memberObjects = array();
    public static $type_name = array(
        self::TYPE_POSITIVE => 'Positive',
        self::TYPE_NEUTRAL => 'Neutral',
        self::TYPE_NEGATIVE => 'Negative'
    );
    
    public static $image_name = array(
        self::TYPE_POSITIVE => 'ratepositive',
        self::TYPE_NEUTRAL => 'rateneutral',
        self::TYPE_NEGATIVE => 'ratenegative'
    );
    
    public static $status_name = array(
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_PENDING => 'Pending'
    );

    /**
     * Return formatted Date
     * @param type $format
     * @return string 
     */
    public function getDate($format = "m/d/Y") {
        return date($format, strtotime($this->added));
    }

    /**
     * Get Type
     * @return string 
     */
    public function getType() {
        if (array_key_exists($this->type, self::$type_name)) {
            return self::$type_name[$this->type];
        }
        return $this->type;
    }
    
    /**
     * Return Image
     * 
     * @return string 
     */
    public function getTypeImage() {
        if (array_key_exists((int)$this->type, self::$image_name)) {
            return "<img src=\"".SITE_IN."images/icons/".self::$image_name[$this->type].".png\" width=\"16\" height=\"16\" alt=\"Rating\" />";
        }
        return $this->type;
    }
    
    /**
     * Get Status
     * 
     * @return String
     */
    public function getStatus() {
        if (array_key_exists($this->status, self::$status_name)) {
            return self::$status_name[$this->status];
        }
        return $this->status;
    }

    /**
     * Get From
     * 
     * @param type $reload
     * @return CompanyProfile Object 
     */
    public function getFrom($reload = false) {
        if ($reload || !isset($this->memberObjects['from'])) {
            $from = new CompanyProfile($this->db);
            $from->getByOwnerId($this->from_id);
            $this->memberObjects['from'] = $from;
        }
        return $this->memberObjects['from'];
    }

    /**
     * Get To
     * 
     * @param type $reload
     * @return CompanyProfile Object 
     */
    public function getTo($reload = false) {
        if ($reload || !isset($this->memberObjects['to'])) {
            $to = new CompanyProfile($this->db);
            $to->getByOwnerId($this->to_id);
            $this->memberObjects['to'] = $to;
        }
        return $this->memberObjects['to'];
    }

}

?>