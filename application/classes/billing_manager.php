<?php

class BillingManager extends FdObjectManager
{
    const TABLE = Billing::TABLE;
    public $owner_id;
    public $key;

    public function get($order = null, $per_page = 100, $where = "")
    {
        $rows = parent::get($order, $per_page, $where);
        $billings = array();foreach ($rows as $row) {$billing = new Billing($this->db);
            $billing->load($row['id']);
            $billings[] = $billing;}return $billings;
    }

    public function getLast()
    {
        $rows = parent::get(' ORDER BY id DESC ', 100, '');
        $billings = array();

        foreach ($rows as $row) {
            $billing = new Billing($this->db);
            $billing->load($row['id']);

            if ($billing->owner_id == $this->owner_id) {
                $billings[] = $billing;
            }
        }

        return $billings;
    }

    public function getLastPaymentDate()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $row = $this->db->selectRow(" DATE_FORMAT(added, '%m/%d/%Y') as added ", static::TABLE, "WHERE owner_id='" . (int) $this->owner_id . "' AND owner_id='" . (int) $this->owner_id . "' AND type='" . Billing::TYPE_PAYMENT . "' ORDER BY id DESC LIMIT 0,1");

        if (is_array($row) && isset($row['added'])) {
            return $row['added'];
        }

        return "N/A";
    }

    public function getLastPaymentAmount()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $row = $this->db->selectRow("amount", static::TABLE, "WHERE owner_id='" . (int) $this->owner_id . "' AND owner_id='" . (int) $this->owner_id . "' ORDER BY id DESC LIMIT 0,1");

        if (is_array($row) && isset($row['amount'])) {
            return $row['amount'];
        }

        return "N/A";
    }

    public function getCurrentBalance()
    {
        $balance = 0.00;

        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $sql = "SELECT SUM( IF(type = 1, amount,  amount*-1 ) ) as amt FROM " . self::TABLE . " WHERE owner_id='" . (int) $this->owner_id . "' GROUP BY owner_id LIMIT 0,1 ";
        $q = $this->db->query($sql);
        $r = $this->db->fetch_row($q);

        if (isset($r['amt']) && $r['amt'] !== "") {
            $balance = $r['amt'];
        }
        return $balance;
    }

    public function getCards()
    {
        if (is_null($this->db)) {
            throw new FDException(get_class($this) . "->load: DB helper not set");
        }

        if (!ctype_digit((string) $this->owner_id)) {
            throw new FDException(get_class($this) . "->load: invalid owner_id");
        }

        $r = array();

        if ($GLOBALS['CONF']['DES_ENCRYPT']) {
            $r = $this->db->selectRows("*, DES_DECRYPT(cc_number, '" . $this->key . "') AS cc_number", "app_creditcards", "WHERE owner_id='" . (int) $this->owner_id . "'");
        } else {
            $r = $this->db->selectRows("*, cc_number AS cc_number", "app_creditcards", "WHERE owner_id='" . (int) $this->owner_id . "'");
        }

        $cards = array("" => "--Select one--");

        if (!empty($r)) {
            foreach ($r as $k => $v) {
                $v['cc_number'] = $this->hideCCNumber($v['cc_number']);
                $cards[$v['id']] = $v['cc_number'];
            }
        }
        return $cards;
    }

    private function hideCCNumber($number)
    {
        return "**** **** **** " . @substr($number, -4);
    }
}
