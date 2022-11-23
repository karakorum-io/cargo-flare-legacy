<?php
    /**************************************************************************************************
	* Payments Cards class
	* Class for work with payments cards
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-12-05
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/

    class PaymentcardManager extends FdObjectManager {
        const TABLE = Paymentcard::TABLE;

        public function getPaymentcards($entity_id, $order = null, $per_page = null) {
            if (!ctype_digit($entity_id)) throw new FDException("Invalid Entity ID");
            $rows = parent::get($order, $per_page, "`entity_id` = {$entity_id} AND `deleted` = 0");
            $paymentcards = array();
            foreach ($rows as $row) {
                $paymentcard = new Paymentcard($this->db);
                $paymentcard->load($row['id']);
                $paymentcards[] = $paymentcard;
            }
            return $paymentcards;
        }
    }
?>