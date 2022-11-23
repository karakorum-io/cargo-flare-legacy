<?php

/* * ************************************************************************************************
 * Payment class
 * Class representing one payment record in DB
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-22
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class Payment extends FdObject {
    
    protected $memberObjects = array();

    const TABLE = "app_payments";
    const SBJ_COMPANY = 1;
    const SBJ_SHIPPER = 2;
    const SBJ_CARRIER = 3;
    const SBJ_TERMINAL_P = 4;
    const SBJ_TERMINAL_D = 5;

    public static $subject_name = array(
        self::SBJ_COMPANY => 'Company',
        self::SBJ_SHIPPER => 'Shipper',
        self::SBJ_CARRIER => 'Carrier',
        self::SBJ_TERMINAL_P => 'Pickup Terminal',
        self::SBJ_TERMINAL_D => 'Delivery Terminal'
    );

    const M_PE_CHECK = 1;
    const M_CO_CHECK = 2;
    const M_CA_CHECK = 3;
    const M_COMCHEK = 4;
    const M_CASH = 5;
    const M_ETRANSFER = 6;
    const M_OTHER = 7;
    const M_MORDER = 8;
    const M_CC = 9;

    public static $method_name = array(
        self::M_PE_CHECK => 'Personal Check',
        self::M_CO_CHECK => 'Company Check',
        self::M_CA_CHECK => 'Cashiers Check',
        self::M_COMCHEK => 'Comchek',
        self::M_CASH => 'Cash',
        self::M_ETRANSFER => 'Electronic transfer',
        self::M_OTHER => 'Other',
        self::M_MORDER => 'Money Order',
        self::M_CC => 'Credit Card'
    );

    const CC_VISA = 1;
    const CC_MASTERCARD = 2;
    const CC_AMEX = 3;
    const CC_DISCOVER = 4;
    const CC_OTHER = 0;

    public static $cctype_name = array(
        self::CC_VISA => 'Visa',
        self::CC_MASTERCARD => 'MasterCard',
        self::CC_AMEX => 'Amex',
        self::CC_DISCOVER => 'Discover',
        self::CC_OTHER => 'Other'
    );

    public function getDate($format = "m/d/Y") {
        return date($format, strtotime($this->date_received));
    }

    public function getCCType() {
        if (array_key_exists($this->cc_type, self::$cctype_name)) {
            return self::$cctype_name[$this->cc_type];
        }
        return $this->cc_type;
    }

    public static function getCCTypeById($id) {
        if (array_key_exists($id, self::$cctype_name)) {
            return self::$cctype_name[$id];
        }
        return "";
    }

    public function getFrom() {
        if (array_key_exists($this->fromid, self::$subject_name)) {
            return self::$subject_name[$this->fromid];
        }
        return null;
    }

    public function getTo() {
        if (array_key_exists($this->toid, self::$subject_name)) {
            return self::$subject_name[$this->toid];
        }
        return null;
    }

    public function getAmount() {
        return "$ " . number_format($this->amount, 2, '.', ',');
    }

    public function getMethod() {
        if (array_key_exists($this->method, self::$method_name)) {
            return self::$method_name[$this->method];
        }
        return null;
    }

    public function getEnteredBy() {
        if (!ctype_digit((string) $this->entered_by))
            throw new FDException("Invalid Entered By value");
        if ($this->entered_by == 0)
            return 'System';
        $member = new Member($this->db);
        $member->load($this->entered_by);
        return $member->contactname;
    }

    public function getNumber() {
        $entity = new Entity($this->db);
        $entity->load($this->entity_id);
        return $entity->getNumber() . '-P' . $this->number;
    }
    
    /**
     * getCCExp()
     * Return formatted date
     * @param string $format
     * @return string
     */
    
    public function getCCExp($format = "m/d/Y") {
        if (trim($this->cc_exp) != ""){
            return date($format, strtotime($this->cc_exp));
        }else{
            return "";
        }
        
    }
    
    /**
     * Payment::getEntity()
     * 
     * @param type $reload
     * @return entity object 
     */
    public function getEntity($reload = false) {
        if ($reload || !isset($this->memberObjects['entity'])) {
            $entity = new Entity($this->db);
            $entity->load($this->entity_id);
            $this->memberObjects['entity'] = $entity;
        }
        return $this->memberObjects['entity'];
    }
    
    
    

    public static function getNextNumber($entity_id, $db) {
        if (!($db instanceof mysql))
            throw new FDException('Invalid DB Helper instance');
        if (!ctype_digit((string) $entity_id))
            throw new FDException('Invalid Entity ID');
        $row = $db->selectRow('MAX(number) as mx', self::TABLE, "WHERE `entity_id` = {$entity_id}");
        if (ctype_digit((string) $row['mx'])) {
            return (int) $row['mx'] + 1;
        }
        return 1;
    }


	public function create($data) {
		$ret = parent::create($data);
		if ($data['fromid'] == self::SBJ_SHIPPER) {
			$this->getEntity()->sendPaymentReceived($data['amount']);
		}
		return $ret;
	}
}

?>