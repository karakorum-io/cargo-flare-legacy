<?php

/* * ************************************************************************************************
 * Rating Comments Manager Calss
 * Class for work with payments cards
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-12-05
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class RatingcommentsManager extends FdObjectManager {

    const TABLE = Ratingcomments::TABLE;

    /**
     * Return comments array for build combo
     * 
     * @return array
     */
    public function getCommentsList() {
        $rows = parent::get(null, null, "");
        $comments = array();
        foreach ($rows as $row) {
            $comment = new Ratingcomments($this->db);
            $comment->load($row['id']);
            $comments[] = $comment;
        }
        return $comments;
    }

}

?>