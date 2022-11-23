<?php

/* * ************************************************************************************************
 * Credit Cards class
 * Class representing one Credit Card record in DB
 *
 * Client:        FreightDragon
 * Version:        1.0
 * Date:            2011-12-07
 * Author:        C.A.W., Inc. dba INTECHCENTER
 * Address:        11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:        techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

/**
 * @property int $id
 * @property int $owner_id
 * @property string $cc_fname
 * @property string $cc_lname
 * @property string $cc_address
 * @property string $cc_city
 * @property string $cc_state
 * @property string $cc_zip
 * @property string $cc_number
 * @property string $cc_cvv2
 * @property int $cc_type
 * @property string $cc_month
 * @property string $cc_year
 */
class Creditcard extends FdObject {

		public $key = "";

		const TABLE = "app_creditcards";
		const CC_VISA = 1;
		const CC_MASTERCARD = 2;
		const CC_AMEX = 3;
		const CC_DISCOVER = 4;
		const CC_OTHER = 0;

		public function __construct($param = null) {
				parent::__construct($param);
				$this->key = (isset($GLOBALS['CONF']['security_salt'])) ? $GLOBALS['CONF']['security_salt'] : $_SERVER['HTTP_HOST'];
		}

		public static $cctype_name = array(
				self::CC_VISA => 'Visa',
				self::CC_MASTERCARD => 'MasterCard',
				self::CC_AMEX => 'Amex',
				self::CC_DISCOVER => 'Discover',
				self::CC_OTHER => 'Other'
		);

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

		public function create($data = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->create: DB helper not set");
				if (!is_array($data))
						throw new FDException(get_class($this) . "->create: invalid input data");
				$owner_id = $data['owner_id'];
				if ($GLOBALS['CONF']['DES_ENCRYPT']){
						$data['cc_number'] = "DES_ENCRYPT('" . $data['cc_number'] . "', '" . $this->key . "')";
				}
				$data = $this->db->PrepareSql(self::TABLE, $data);
				//printt($data);
				$this->db->insert(self::TABLE, $data);
				if ($this->db->isError)
						throw new FDException(get_class($this) . "->create: MySQL query error");
				$this->load($this->db->get_insert_id(), $owner_id);
				return $this->db->get_insert_id();
		}

		public function load($id = null, $owner_id = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->load: DB helper not set");
				if (!ctype_digit((string) $id) || !ctype_digit((string) $owner_id))
						throw new FDException(get_class($this) . "->load: invalid ID");
				if ($GLOBALS['CONF']['DES_ENCRYPT']){
						$row = $this->db->selectRow("*,  DES_DECRYPT(cc_number, '" . $this->key . "') AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND owner_id='" . $owner_id . "'");
				}else{
						$row = $this->db->selectRow("*,  cc_number AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND owner_id='" . $owner_id . "'");
				}
				if (!is_array($row))
						throw new FDException(get_class($this) . "->load: ID({$id}) not found in DB");
				foreach ($row as $key => $val) {
						$this->attributes[$key] = $val;
				}
				$this->loaded = true;
		}

		public function getCurrentAutopayCC($id = null, $owner_id = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->load: DB helper not set");
				if (!ctype_digit((string) $id) || !ctype_digit((string) $owner_id))
						throw new FDException(get_class($this) . "->load: invalid ID");
				if ($GLOBALS['CONF']['DES_ENCRYPT']){
						$row = $this->db->selectRow("*,  DES_DECRYPT(cc_number, '" . $this->key . "') AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND owner_id='" . $owner_id . "'");
				}else{
						$row = $this->db->selectRow("*,  cc_number AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND owner_id='" . $owner_id . "'");
				}
				if (!is_array($row)) {
						return false;
				}
				foreach ($row as $key => $val) {
						$this->attributes[$key] = $val;
				}
				$this->loaded = true;
				return true;
		}
}

?>