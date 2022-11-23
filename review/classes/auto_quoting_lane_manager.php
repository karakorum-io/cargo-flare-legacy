<?php
    /**************************************************************************************************
     * AutoQuotingSeason class
     *
     * Client:		FreightDragon
     * Version:		1.0
     * Date:        2011-11-30
     * Author:		C.A.W., Inc. dba INTECHCENTER
     * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
     * E-mail:		techsupport@intechcenter.com
     * CopyRight 2011 FreightDragon. - All Rights Reserved
     ***************************************************************************************************/

    class AutoQuotingLaneManager extends FdObjectManager {
        const TABLE = AutoQuotingLane::TABLE;

        public function getLanes($order = null, $per_page = 50, $add = "") {
            $rows = parent::get($order, $per_page, $add);
            $lanes = array();
            foreach ($rows as $row) {
                $lane = new AutoQuotingLane($this->db);
                $lane->load($row['id']);
                $lanes[] = $lane;
            }
            return $lanes;
        }
    }
?>