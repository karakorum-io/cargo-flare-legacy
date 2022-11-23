<?php
    /**************************************************************************************************
	* Credit Cards class
	* Class for work with credit cards
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-12-05
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/

    class CreditcardManager extends FdObjectManager {
        const TABLE = Creditcard::TABLE;

        public function getCreditcards($entity_id, $order = null, $per_page = null) {
            if (!ctype_digit($entity_id)) throw new FDException("Invalid Entity ID");
            $rows = parent::get($order, $per_page, "`entity_id` = {$entity_id} AND `deleted` = 0");
            $creditcards = array();
            foreach ($rows as $row) {
                $creditcard = new Creditcard($this->db);
                $creditcard->load($row['id']);
                $creditcards[] = $creditcard;
            }
            return $creditcards;
        }
    }
?>