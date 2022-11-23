<?php

/* * ************************************************************************************************
 * AccountsShippers class
 * This class represent one Carrier, Terminal, Shipper
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-01
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class AccountRoute extends FdObject {

    const TABLE = "app_account_route";
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    public static $status_name = array(
       self::STATUS_ACTIVE => 'Active',
       self::STATUS_INACTIVE => 'Inactive'
    );



    public function update($data, $id = null) {
      if (!is_null($id)) {

           $this->load($id);
       }
       parent::update($data);

    }

    public function load($id) {

        parent::load($id);

    }
	
    public static function getAccountRoute($db, $where = "") {
       if (!($db instanceof mysql))
            throw new FDException("Invalid DB Helper");
       $account_ids = $db->selectRows('`id`', self::TABLE, "WHERE ". $where);
       $accounts = array();
       foreach ($account_ids as $value){
           $accounts[] = $value["id"];

        }

        return $accounts;

    }

}

?>