<?php

/* * ************************************************************************************************
 * FollowUpManager class																																		*
 * This is the class for work with follow-ups																												*
 * 																																											*
 * Client:		FreightDragon																																	*
 * Version:		1.0																																					*
 * Date:			2011-10-28																																		*
 * Author:		C.A.W., Inc. dba INTECHCENTER																											*
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076																	*
 * E-mail:		techsupport@intechcenter.com																											*
 * CopyRight 2011 FreightDragon. - All Rights Reserved																								*
 * ************************************************************************************************* */

class FollowUpManager extends FdObjectManager {

    const TABLE = FollowUp::TABLE;

    public function getFollowUps($where = "") {
        $followUps = array();
        //$where = "`followup` = '" . date("Y-m-d") . "'";
        $rows = $this->get(null, null, $where);
        foreach ($rows as $row) {
            $followup = new FollowUp($this->db);
            $followup->load($row['id']);
            $followUps[] = $followup;
        }
        return $followUps;
    }

}

?>