<?php

/* * ************************************************************************************************
 * Payments Cards class
 * Class representing one Card record in DB
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-12-05
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class Paymentcard extends FdObject {

		public $key = "";

		const TABLE = "app_paymentcards";
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

		public function getCCType() {
				if (array_key_exists($this->cc_type, self::$cctype_name)) {
						return self::$cctype_name[$this->cc_type];
				}
				return $this->cc_type;
		}

		public function getCCTypeById($id) {
				if (array_key_exists($id, self::$cctype_name)) {
						return self::$cctype_name[$id];
				}
				return "";
		}

		public function create($data) {
				if ($GLOBALS['CONF']['DES_ENCRYPT']) {
						$data['cc_number'] = "DES_ENCRYPT('" . $data['cc_number'] . "', '" . $this->key . "')";
				}
				if (is_null($this->db)) throw new FDException(get_class($this) . "->create: DB helper not set");
				if (!is_array($data)) throw new FDException(get_class($this) . "->create: invalid input data");
				$data = $this->db->PrepareSql(static::TABLE, $data);
				$this->db->insert(static::TABLE, $data);
				if ($this->db->isError) throw new FDException(get_class($this) . "->create: MySQL query error");
				//parent::create($data);
				parent::load($this->db->get_insert_id());
				return $this->id;
		}

		public function load($id = null, $entity_id = null, $owner_id = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->load: DB helper not set");
				if (!ctype_digit((string) $id) || !ctype_digit((string) $entity_id) || !ctype_digit((string) $owner_id))
						throw new FDException(get_class($this) . "->load: invalid ID");
				if ($GLOBALS['CONF']['DES_ENCRYPT']) {
						$row = $this->db->selectRow("*,  DES_DECRYPT(cc_number, '" . $this->key . "') AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND entity_id='" . $entity_id . "' AND owner_id='" . $owner_id . "'");
				} else {
						$row = $this->db->selectRow("*,  cc_number AS cc_number", static::TABLE, "WHERE `id` = " . (int) $id . " AND entity_id='" . $entity_id . "' AND owner_id='" . $owner_id . "'");
				}
				if (!is_array($row))
						throw new FDException(get_class($this) . "->load: ID({$id}) not found in DB");
				foreach ($row as $key => $val) {
						$this->attributes[$key] = $val;
				}
				$this->loaded = true;
		}

		public function loadLastCC($entity_id = null, $owner_id = null) {
				if (is_null($this->db))
						throw new FDException(get_class($this) . "->load: DB helper not set");
				if (!ctype_digit((string) $entity_id) || !ctype_digit((string) $owner_id))
						throw new FDException(get_class($this) . "->load: invalid entity_id and owner_id");
				if ($GLOBALS['CONF']['DES_ENCRYPT']) {
						$row = $this->db->selectRow("*,  DES_DECRYPT(cc_number, '" . $this->key . "') AS cc_number", static::TABLE, "WHERE entity_id='" . (int) $entity_id . "' AND owner_id='" . (int) $owner_id . "' ORDER BY id DESC LIMIT 0,1");
				} else {
						$row = $this->db->selectRow("*,  cc_number AS cc_number", static::TABLE, "WHERE entity_id='" . (int) $entity_id . "' AND owner_id='" . (int) $owner_id . "' ORDER BY id DESC LIMIT 0,1");
				}
				if (!is_array($row)) {
						$this->loaded = false;
				} else {
						foreach ($row as $key => $val) {
								$this->attributes[$key] = $val;
						}
						$this->loaded = true;
				}
		}

}

?>