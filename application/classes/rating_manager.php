<?php

/* * ************************************************************************************************
 * RatingManager class
 * The class for work with ratings
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-28
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class RatingManager extends FdObjectManager {

    const TABLE = Rating::TABLE;

    public function getCompanyScore($company_id, $period = null) {
        if (!ctype_digit((string) $company_id))
            throw new FDException("Invalid Company ID");
        if (!is_null($period) && !ctype_digit((string) $period))
            throw new FDException("Invalid period");
        $row = $this->db->selectRow("(100*(SUM(`type`)/2)/COUNT(*)) as score", self::TABLE, "WHERE `to_id` = {$company_id} AND status = '".Rating::STATUS_ACTIVE."'");
        if ($this->db->num_rows() == 1) {
            return $row['score'];
        } else {
            return 0;
        }
    }
    
    public function getRatingsHistory($id){
        /*
        $ratings_score = 0;
        $ratings_score_p = 0;
        $ratings_score_t = 0;
        $ratings_score_n = 0;
        $ratings_score1 = 0;
        $ratings_score_p1 = 0;
        $ratings_score_t1 = 0;
        $ratings_score_n1 = 0;
        $ratings_score6 = 0;
        $ratings_score_p6 = 0;
        $ratings_score_t6 = 0;
        $ratings_score_n6 = 0;
        $ratings_received = 0;

        $sql = "SELECT  'all' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1
					GROUP BY type
			UNION
				SELECT  'month' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1 AND added >= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("n") - 1, date("d"), date("Y"))) . "'
					GROUP BY type
			UNION
				SELECT  'six' as period
					   , type
					   , COUNT(id) as cnt
					FROM app_ratings
				WHERE to_id = '" . $id . "' AND status = 1 AND added >= '" . date("Y-m-d H:i:s", mktime(0, 0, 0, date("n") - 6, date("d"), date("Y"))) . "'
					GROUP BY type";
        $q = $this->daffny->DB->query($sql);
        while ($r = $this->daffny->DB->fetch_row($q)) {
            if ($r['period'] == 'all') {
                if ($r['type'] == 0) {
                    $ratings_score_p = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_n = $r['cnt'];
                }
            }
            if ($r['period'] == 'month') {
                if ($r['type'] == 0) {
                    $ratings_score_p1 = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t1 = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_n1 = $r['cnt'];
                }
            }
            if ($r['period'] == 'six') {
                if ($r['type'] == 0) {
                    $ratings_score_p6 = $r['cnt'];
                }
                if ($r['type'] == 1) {
                    $ratings_score_t6 = $r['cnt'];
                }
                if ($r['type'] == 2) {
                    $ratings_score_n6 = $r['cnt'];
                }
            }
        }

        $rating_received = $this->daffny->DB->selectValue("COUNT(*)", "app_ratings", "WHERE to_id = '" . $id . "' AND status = 1");

        if (($ratings_score_p + $ratings_score_t + $ratings_score_n) != 0) { //zero division
            $ratings_score = ($ratings_score_p * 1 + $ratings_score_t * 0.5 + $ratings_score_n * 0) * 100 / ($ratings_score_p + $ratings_score_t + $ratings_score_n);
        } else {
            $ratings_score = 0;
        }
        if (($ratings_score_p1 + $ratings_score_t1 + $ratings_score_n1) != 0) { //zero division
            $ratings_score1 = ($ratings_score_p1 * 1 + $ratings_score_t1 * 0.5 + $ratings_score_n1 * 0) * 100 / ($ratings_score_p1 + $ratings_score_t1 + $ratings_score_n1);
        } else {
            $ratings_score1 = 0;
        }
        if (($ratings_score_p6 + $ratings_score_t6 + $ratings_score_n6) != 0) { //zero division
            $ratings_score6 = ($ratings_score_p6 * 1 + $ratings_score_t6 * 0.5 + $ratings_score_n6 * 0) * 100 / ($ratings_score_p6 + $ratings_score_t6 + $ratings_score_n6);
        } else {
            $ratings_score6 = 0;
        }

        return array("rating_score" => number_format($ratings_score, 2, ".", ",")
            , "rating_score_p" => $ratings_score_p
            , "rating_score_t" => $ratings_score_t
            , "rating_score_n" => $ratings_score_n
            , "rating_score1" => number_format($ratings_score1, 2, ".", ",")
            , "rating_score_p1" => $ratings_score_p1
            , "rating_score_t1" => $ratings_score_t1
            , "rating_score_n1" => $ratings_score_n1
            , "rating_score6" => number_format($ratings_score6, 2, ".", ",")
            , "rating_score_p6" => $ratings_score_p6
            , "rating_score_t6" => $ratings_score_t6
            , "rating_score_n6" => $ratings_score_n6
            , "rating_received" => $rating_received
        );
         */
    }

    public function get($order = null, $per_page = 100, $where = "") {
        $rows = parent::get($order, $per_page, $where);
        $ratings = array();
        foreach ($rows as $row) {
            $rating = new Rating($this->db);
            $rating->load($row["id"]);
            $ratings[] = $rating;
        }
        return $ratings;
    }
}

?>