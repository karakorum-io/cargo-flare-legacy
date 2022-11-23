<?php

/* * ************************************************************************************************
 * Lead Sources Manager class
 * Class for work with Lead Sources
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-04
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************* */

class LeadsourceManager extends FdObjectManager {

    const TABLE = Leadsource::TABLE;

    public function get($order = null, $per_page = 100, $where = '') {
        if (trim($where) != "") {
            $where .= " AND is_default <> 1";
        } else {
            $where .= "WHERE is_default <> 1";
        }
        $rows = parent::get($order, $per_page, $where);
        $leadsources = array();
        foreach ($rows as $row) {
            $leadsource = new Leadsource($this->db);
            $leadsource->load($row['id']);
            $leadsources[] = $leadsource;
        }
        return $leadsources;
    }

    public function getForCron($owner_id, $from) {

        $tmp = explode("@", $from);
        $domain = str_replace(">", "", @$tmp[1]);
        echo $domain;
         
        if (trim($domain) == "") {
            return null;
        } else {
            $where = "WHERE `owner_id` = '" . (int) $owner_id . "' AND `domain` = '" . mysqli_real_escape_string($this->db->connection_id, trim($domain)) . "' ";
            $rows = parent::get(null, null, $where);
            foreach ($rows as $row) {
                if (isset($row["id"])) {
                    return (int) $row["id"];
                } else {
                    return null;
                }
            }
        }
    }

    public function getLeadSourcesCombo($owner_id) {
        $wh = "WHERE `owner_id` = '" . (int) $owner_id . "' OR is_default = 1";
        $rows = parent::get(null, null, $wh);
        $leadsources = array();
        foreach ($rows as $row) {
            $leadsource = new Leadsource($this->db);
            $leadsource->load($row['id']);
            $leadsources[$leadsource->id] = $leadsource->company_name;
        }
        return $leadsources;
    }

    public function getForReport($owner_id, $ls_ids) {


        $where = "WHERE 
            (`owner_id` = '" . (int) $owner_id . "' AND `id` IN ('" . implode("','", $ls_ids) . "'))
                OR 
            (`id` IN ('" . implode("','", $ls_ids) . "') AND `is_default` = 1)";

        $rows = parent::get(null, null, $where);
        $leadsources = array();
        foreach ($rows as $row) {
            $leadsource = new Leadsource($this->db);
            $leadsource->load($row['id']);
            $leadsources[$leadsource->id] = $leadsource->company_name;
        }
        return $leadsources;
    }

}

?>