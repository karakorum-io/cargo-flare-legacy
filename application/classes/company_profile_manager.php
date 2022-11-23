<?php
/**************************************************************************************************
 * CompanyProfileManager class
 * The class for work with CompanyProfiles
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:		2011-12-08
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 ***************************************************************************************************/
class CompanyProfileManager extends FdObjectManager {
    const TABLE = CompanyProfile::TABLE;

    public function searchByName($name = '', $add = null) {
        $result = array();
        if (strlen($name) == 0) return $result;
        $where = "`companyname` LIKE('".mysqli_real_escape_string($this->db->connection_id, $name)."%')";
        if (!is_null($add)) {
            $where .= " AND {$add}";
        }
        $rows = parent::get(null, null, $where);
        foreach ($rows as $row) {
            $cp = new CompanyProfile($this->db);
            $cp->load($row['id']);
            $result[] = $cp;
        }
        return $result;
    }
}