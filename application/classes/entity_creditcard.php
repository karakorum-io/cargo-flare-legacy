<?php

/* * ************************************************************************************************
 * entity_creditcard.php
 *
 * Version:		1.0
 * Date:		2012-05-30
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2012 Intechcenter. - All Rights Reserved
 * ************************************************************************************************* */

/**
 * @property int $id
 * @property int $entity_id
 * @property string $fname
 * @property string $lname
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $number
 * @property string $cvv2
 * @property int $type
 * @property string $month
 * @property string $year
 */
class EntityCreditcard extends FdObject {

		private $key = "";

		const TABLE = "app_entity_creditcards";
		const CC_VISA = 1;
		const CC_MASTERCARD = 2;
		const CC_AMEX = 3;
		const CC_DISCOVER = 4;
		const CC_OTHER = 5;

		public function __construct($db) {
				parent::__construct($db);
				$this->setKey($GLOBALS['CONF']['security_salt']);
		}

		public static $type_name = array(
				self::CC_VISA => 'Visa',
				self::CC_MASTERCARD => 'MasterCard',
				self::CC_AMEX => 'Amex',
				self::CC_DISCOVER => 'Discover',
				self::CC_OTHER => 'Other',
		);

		public function getType() {
				return self::$type_name[$this->type];
		}

		public function setKey($key) {
				$this->key = $key;
		}

		public function create($data) {
				if (isset($data['number'])) {
						if ($GLOBALS['CONF']['DES_ENCRYPT']) {
								$data['number'] = "DES_ENCRYPT('" . mysqli_real_escape_string($this->db->connection_id, $data['number']) . "', '" . $this->key . "')";
						}
				}
				parent::create($data);
		}

		public function update($data) {
				if (isset($data['number'])) {
						if ($GLOBALS['CONF']['DES_ENCRYPT']) {
								$data['number'] = "DES_ENCRYPT('" . mysqli_real_escape_string($this->db->connection_id, $data['number']) . "', '" . $this->key . "')";
						}
				}
				parent::update($data);
		}

		public function load($id = null) {
				if (is_null($this->db))
						throw new FDException("DB helper not set");
				if (!ctype_digit((string) $id))
						throw new FDException("invalid ID");
				if ($GLOBALS['CONF']['DES_ENCRYPT']) {
						$row = $this->db->selectRow("*,  DES_DECRYPT(`number`, '" . $this->key . "') AS `number`", static::TABLE, "WHERE `id` = " . (int) $id);
				} else {
						$row = $this->db->selectRow("*,  `number` AS `number`", static::TABLE, "WHERE `id` = " . (int) $id);
				}
				if (!is_array($row))
						throw new FDException("ID({$id}) not found in DB");
				foreach ($row as $key => $val) {
						$this->attributes[$key] = $val;
				}
				$this->loaded = true;
		}

		public function loadByEntityId($entity_id) {
				if (is_null($this->db))
						throw new FDException("DB helper not set");
				if (!ctype_digit((string) $entity_id))
						throw new FDException("invalid Entity ID");
				if ($GLOBALS['CONF']['DES_ENCRYPT']) {
						$row = $this->db->selectRow("*,  DES_DECRYPT(`number`, '" . $this->key . "') AS `number`", static::TABLE, "WHERE `entity_id` = " . (int) $entity_id);
				} else {
						$row = $this->db->selectRow("*,  `number` AS `number`", static::TABLE, "WHERE `entity_id` = " . (int) $entity_id);
				}
				if (!is_array($row) || empty($row)) {
						$this->create(array(
								'entity_id' => $entity_id,
						));
						if ($this->db->isError)
								throw new FDException("MySQL query error");
						return $this->loadByEntityId($entity_id);
				}
				foreach ($row as $key => $val) {
						$this->attributes[$key] = $val;
				}
				$this->loaded = true;
				return $this;
		}

}