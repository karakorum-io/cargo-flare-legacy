<?php

/* * ************************************************************************************************
 * Shipper class																																						*
 * Class for working with shipper																																*
 * 																																											*
 * Client:		FreightDragon																																	*
 * Version:		1.0																																					*
 * Date:			2011-09-28																																		*
 * Author:		C.A.W., Inc. dba INTECHCENTER																											*
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
 * E-mail:		techsupport@intechcenter.com																											*
 * CopyRight 2011 FreightDragon. - All Rights Reserved																								*
 * ************************************************************************************************* */

class Shipper extends FdObject {

    const TABLE = "app_shippers";

    public static $attributeTitles = array(
        'id' => 'ID',
        'fname' => 'First Name',
        'lname' => 'Last Name',
        'company' => 'Company',
        'email' => 'Email',
        'phone1' => 'Phone',
        'phone2' => 'Phone 2',
        'mobile' => 'Mobile',
        'fax' => 'Fax',
        'address1' => 'Address',
        'address2' => 'Address 2',
        'city' => 'City',
        'state' => 'State',
        'zip' => 'Zip',
        'country' => 'Country',
        'created' => 'Created'
    );

    public function update($data = null) {
        $old_values = $this->attributes;
        parent::update($data);
        $new_values = $this->attributes;
        foreach ($new_values as $key => $value) {
            if ($old_values[$key] != $value) {
                $rows = $this->db->selectRows("`id`", Entity::TABLE, "WHERE `shipper_id` = " . $this->id . " AND `deleted` = 0");
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        History::add($this->db, $row['id'], 'Shipper ' . self::$attributeTitles[$key], $old_values[$key], $value);
                    }
                }
            }
        }
    }

    public function create($data, $entity_id = null) {
        $id = parent::create($data);
        if (!is_null($entity_id)) {
            $new_values = $this->attributes;
            foreach ($new_values as $key => $value) {
                if (in_array($key, array('created', 'id')))
                    continue;
                History::add($this->db, $entity_id, 'Shipper ' . self::$attributeTitles[$key], '', $value);
            }
        }
	    return $id;
    }

    public static function getShippers($db, $where = "") {
        if (!($db instanceof mysql))
            throw new FDException("Invalid DB Helper");
        $shipper_ids = array();
        $shipper_ids = $db->selectRows('`id`', self::TABLE, "WHERE ". $where);
        $shippers = array();
        foreach ($shipper_ids as $key=>$value){
            $shippers[] = $value["id"];
        }
        return $shippers;
    }

}

?>