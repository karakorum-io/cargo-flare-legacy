<?php

/**
 * @version        1.0
 * @since          07.08.12
 * @author         Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address        11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email          techsupport@intechcenter.com
 * @copyright      2012 Intechcenter. All Rights Reserved
 *
 * @property int    $id
 * @property string $code
 * @property string $name
 * @property float  $price
 * @property string $description
 * @property int    $period_id
 * @property int    $is_percent_discount
 * @property float  $discount
 * @property int    $is_renewal
 * @property string $renewal_code
 * @property int    $type_id
 * @property int    $is_online
 * @property int    $is_delete
 * @property string $register_date
 * @property string $period_name
 *
 */
class Product extends FdObject
{

    const TABLE = 'products';
    const TYPE_INITIAL = 1;
    const TYPE_ADDITIONAL = 2;
    const TYPE_RENEWAL = 3;
    const TYPE_STORAGE = 4;
    const TYPE_ADDON_AQ = 5;
    const PERIOD_MONTH = 1;
    const PERIOD_YEAR = 2;

    public static $period_name = array(
        self::PERIOD_MONTH => "Monthly payment",
        self::PERIOD_YEAR => "Yearly payment"
    );

    public function getSmallDescription()
    {
        return mb_substr($this->description, 0, 100);
    }

    public function getRenewalProductByCode($code)
    {

        $row = $this->db->selectRow("id", self::TABLE, "WHERE
																		`code` = '" . (int)$code . "'
																		LIMIT 0, 1
																");
        if (!empty($row)) {
            $this->load($row['id']);
            return true;
        } else {
            return false;
        }
    }

    public function getRenewalProductId()
    {

        $row = $this->db->selectRow("id", self::TABLE, "WHERE
																		`code` = '" . (int)$this->renewal_code . "'
																		LIMIT 0, 1
																");
        if (!empty($row)) {
            return $row['id'];
        } else {
            return "";
        }
    }

    public function getRestDescription()
    {
        return mb_substr($this->description, 100);
    }

    public static function getPeriods()
    {
        return array(
            self::PERIOD_MONTH => "Month",
            self::PERIOD_YEAR => "Year",
        );
    }

    public function getPeriodLabel()
    {
        $periods = self::getPeriods();
        return $periods[$this->period_id];
    }

    public static function getTypes()
    {
        return array(
            self::TYPE_INITIAL => "Initial",
            self::TYPE_RENEWAL => "Renewal",
            self::TYPE_ADDITIONAL => "Additional User",
            self::TYPE_STORAGE => "Files Storage",
            self::TYPE_ADDON_AQ => "Automate Quoting Addon",

        );
    }

    public function getTypeLabel()
    {
        $types = self::getTypes();
        return $types[$this->type_id];
    }

    public function getRenewalProducts()
    {

        $pm = new ProductManager($this->db);
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_RENEWAL . " AND is_online = 1");
        $productsArr = array();

        foreach ($products as $product) {
            $productsArr[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        return $productsArr;
    }

    public function getRenewalStoragesProducts()
    {
        $pm = new ProductManager($this->db);
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_STORAGE . " AND is_online = 1");
        $productsArr = array();

        foreach ($products as $product) {
            $productsArr[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        return $productsArr;
    }
    public function getRenewalAddonAqProducts()
    {
        $pm = new ProductManager($this->db);
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_ADDON_AQ . " AND is_online = 1");
        $productsArr = array();

        foreach ($products as $product) {
            $productsArr[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        return $productsArr;
    }

    public function getRenewalAdditionalProducts()
    {
        $additional = array();
        $pm = new ProductManager($this->db);
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_ADDITIONAL . " AND is_online = 1");
        foreach ($products as $product) {
            $additional[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        return $additional;
    }

    /**
     * If additional user is purchased in the middle of billing cycle, the purchase price must be prorated based on the number of days left in the current cycle until expiration date. Example: 1 additional license on Monthly Plan is purchased 10 days before expiration date of the license, price will be calculated as $14.99 : 30 days x 10 days = $5.00
     * @param type $price
     * @return type
     */
    final public static function calculateRestPrice($price, $expiredate, $type)
    {
        $new_price = 0;
        if ($type == self::PERIOD_MONTH) {
            $perioddays = (int)date('t');
        } else {
            $perioddays = date("L") == "1" ? 366 : 365;
        }

        $now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $exp = explode("-", $expiredate);
        $endofperiod = mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]);

        $rest = ($endofperiod - $now) / 86400;
        if ($price != 0) {
            $new_price = round(($price / $perioddays) * $rest, 2);
        }

        if ($new_price < 0) {
            $new_price = 0;
        }

        return $new_price;
    }

}
