<?php

class Account extends FdObject
{

    const TABLE = "app_accounts";
    const STATUS_ACTIVE = 1;
    const STATUS_ACTIVE_NEW = 2;
    const STATUS_INACTIVE = 0;
    const TYPE_CARRIER = 1;
    const TYPE_SHIPPER = 2;
    const TYPE_TERMINAL = 3;
    const INSURANCE_TYPE_CHARGO = 1;
    const INSURANCE_TYPE_LIABILITY = 2;
    const INSURANCE_TYPE_CHARGO_LIABILITY = 3;

    public static $status_name = array(

        self::STATUS_ACTIVE => 'Active',
        self::STATUS_ACTIVE_NEW => 'Active',
        self::STATUS_INACTIVE => 'Inactive',

    );

    public static $ins_tupe_name = array(

        self::INSURANCE_TYPE_CHARGO => 'Insurance Cargo',
        self::INSURANCE_TYPE_LIABILITY => 'Insurance Liability',
        self::INSURANCE_TYPE_CHARGO_LIABILITY => 'Cargo & Liability',

    );

    public static $type_name = array(

        self::TYPE_CARRIER => "Carrier",
        self::TYPE_SHIPPER => "Shipper",
        self::TYPE_TERMINAL => "Terminal",

    );

    public static $shipper_types = array(
        'Residential' => 'Residential',
        'Commercial' => 'Commercial',
    );

    public static $carrier_types = array(
        'Auto' => 'Auto',
        'Domestic LTL' => 'Domestic LTL',
        'Household Goods' => 'Household Goods',
        'Local Towing' => 'Local Towing',
        'Port Dravage' => 'Port Dravage',
        'Heavy Equipment' => 'Heavy Equipment',
        'Flatbed' => 'Flatbed',
        'Lowboy' => 'Lowboy',
        'Partial Truck Load' => 'Partial Truck Load',
        'Domestic TL' => 'Domestic TL',
        'Air Freight' => 'Air Freight',
        'Van Move' => 'Van Move',
        'Ocean Freight' => 'Ocean Freight',
        'Rail Freight' => 'Rail Freight',
        'Step Deck' => 'Step Deck',
        'Dry Vans' => 'Dry Vans',
        'Refrigerated' => 'Refrigerated',
        'Tankers' => 'Tankers',
    );

    public function update($data, $id = null)
    {
        if (!is_null($id)) {
            $this->load($id);
        }

        parent::update($data);
    }

    public function load($id)
    {

        parent::load($id);

        $type = "";
        if ($this->attributes['is_carrier']) {
            $type = "Carrier";
        }

        if ($this->attributes['is_shipper']) {
            $type .= " Shipper";
        }

        if ($this->attributes['is_location']) {
            $type .= " Location";
        }

        $this->attributes['type'] = trim($type);
        $this->attributes['status_name'] = Account::$status_name[$this->status];

    }

    public static function getAccounts($db, $where = "")
    {

        if (!($db instanceof mysql)) {
            throw new FDException("Invalid DB Helper");
        }

        $account_ids = $db->selectRows('`id`', self::TABLE, "WHERE " . $where);
        $accounts = array();

        foreach ($account_ids as $value) {
            $accounts[] = $value["id"];
        }

        return $accounts;
    }

    /**
     * @param string $hash
     * @return \FdObject|void
     */
    public function loadByHash($hash)
    {

        $id = $this->db->selectField('id', self::TABLE, "WHERE md5(id) = '" . mysqli_real_escape_string($this->db->connection_id, $hash) . "'");
        return $this->load($id);
    }

}
