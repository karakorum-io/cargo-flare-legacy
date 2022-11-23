<?php
/**************************************************************************************************
 * FdObject class                                                                                                                                                        *
 * Abstract Class with most common functions                                                                                                            *
 *                                                                                                                                                                            *
 * Client:        FreightDragon                                                                                                                                    *
 * Version:        1.0                                                                                                                                                    *
 * Date:            2011-09-13                                                                                                                                        *
 * Author:        C.A.W., Inc. dba INTECHCENTER                                                                                                            *
 * Address:    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                                                                    *
 * E-mail:        techsupport@intechcenter.com                                                                                                            *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                                                                                *
 ***************************************************************************************************/

/**
 * @property int $id
 */

abstract class FdObject {
	/**
	 * @var mysql DB
	 */
	protected $db = null;
	/* Object attributes */
	public $attributes = array();
	/* Indicate if object data is loaded from DB */
	public  $loaded = false;

	const TABLE = null;

	public function __construct($param = null) {
		/* If param is instance of Daffny MySQL library - set it to Object */
		if ($param instanceof mysql) {
			$this->setDbHelper($param);
		}
	}

	public function isLoaded() {
		return $this->loaded;
	}

	/**
	 * Sets DB helper for Object
	 */
	public function setDbHelper($db = null) {
		if (!($db instanceof mysql)) throw new FDException(get_class($this) . "->setDbHelper: invalid DB helper");
		$this->db = $db;
	}

	/**
	 * Common Object getter.
	 * Returns object attributes or members.
	 * Throws exception if Object not loaded
	 */
	public function __get($name) {
		if (!$this->loaded) throw new FDException(get_class($this) . "->__get({$name}): object not loaded");
		if (!in_array($name, get_class_vars(get_class($this)))) {
			return $this->attributes[$name];
		} else {
			return $this->$name;
		}
	}

	/**
	 * Get object attributes
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}

	/**
	 * Common Object setter.
	 * Sets Object members (not attributes)
	 * Throws exception if Object don't have such member
	 */
	public function __set($name, $value) {
		if (!in_array($name, get_class_vars(get_class($this)))) throw new FDException(get_class($this) . "->__set({$name}, {$value}): member not found");
		$this->$name = $value;
	}

	/**
	 * Load Object data from DB
	 * Throws exception if DB helper not set, invalid ID or ID not found in DB
	 */
	public function load($id = null) {
            	
	if (is_null($this->db)) throw new FDException(get_class($this) . "->load: DB helper not set");
		if (!ctype_digit((string)$id)) throw new FDException(get_class($this) . "->load: invalid ID");
		$row = $this->db->selectRow("*", static::TABLE, "WHERE `id` = " . (int)$id);
		if (!is_array($row)) throw new FDException(get_class($this) . "->load: ID({$id}) not found in DB");
		foreach ($row as $key => $val) {
			$this->attributes[$key] = $val;
		}
		$this->loaded = true;
		return $this;
	}

	/**
	 * Update Object data in DB
	 */
	public function update($data = null) {
		if (is_null($this->db)) throw new FDException(get_class($this) . "->update: DB helper not set");
		if (!ctype_digit((string)$this->id)) throw new FDException(get_class($this) . "->update: invalid ID");
		if (!is_array($data)) throw new FDException(get_class($this) . "->update: invalid input data");
		$data = $this->db->PrepareSql(static::TABLE, $data);
		$this->db->update(static::TABLE, $data, "`id` = " . (int)$this->id);
		if ($this->db->isError) throw new FDException(get_class($this) . "->update: MySQL query error");
		$this->load($this->id);
	}

	/**
	 * Creates required records in DB and loads crated object
	 */
	public function create($data = null) {
		if (is_null($this->db)) throw new FDException(get_class($this) . "->create: DB helper not set");
		if (!is_array($data)) throw new FDException(get_class($this) . "->create: invalid input data");
		$data = $this->db->PrepareSql(static::TABLE, $data);
		$this->db->insert(static::TABLE, $data);
		if ($this->db->isError) throw new FDException(get_class($this) . "->create: MySQL query error");
		$this->load($this->db->get_insert_id());
		return $this->id;
	}

    public function createOrUpdate($data = null) {
        if (is_null($this->db)) throw new FDException(get_class($this) . "->create: DB helper not set");
        if (!is_array($data)) throw new FDException(get_class($this) . "->create: invalid input data");
        $data = $this->db->PrepareSql(static::TABLE, $data);
        $this->db->insertOrUpdate(static::TABLE, $data);
        if ($this->db->isError) throw new FDException(get_class($this) . "->create: MySQL query error");
    }

	/**
	 * Delete record in DB
	 */
	public function delete($id = null, $force = false) {
		if (is_null($this->db)) throw new FDException(get_class($this) . "->delete: DB helper not set");
		if (!ctype_digit((string)$id) && !ctype_digit((string)$this->attributes['id'])) throw new FDException(get_class($this) . "->delete: invalid ID");
		if (!isset($this->attributes['id'])) $this->attributes['id'] = (int)$id;
		if ($force) {
			$this->db->delete(static::TABLE, "`id` = " . (int)$this->attributes['id']);
		} else {
			$this->db->update(static::TABLE, array('deleted' => 1), "`id` = " . (int)$this->attributes['id']);
		}
		if ($this->db->isError) throw new FDException(get_class($this) . "->delete: MySQL query error");
	}

	/**
	 * @return FdObject
	 * @throws FDException
	 */
	public function selfclone() {
		if (!$this->loaded) throw new FDException(get_class($this) . "->clone: You must load Object before call clone function");
		$class_name = get_class($this);
		/* @var FdObject $new */
		$new = new $class_name($this->db);
		$insert_arr = $this->getAttributes();
		unset($insert_arr['id']);
		$new->create($insert_arr);
		return $new;
	}
}