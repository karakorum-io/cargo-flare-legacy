<?php
    /**************************************************************************************************
	* AutoQuotingSeasonManager class
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-30
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
    
    class AutoQuotingSeasonManager extends FdObjectManager {
        const TABLE = AutoQuotingSeason::TABLE;
        
        public function getSeasons($order, $per_page, $owner_id) {
            if (!ctype_digit((string)$owner_id)) throw new FDException("Invalid Owner ID");
            $rows = parent::get($order, $per_page, "`owner_id` = ".$owner_id);
            $seasons = array();
            foreach ($rows as $row) {
                $season = new AutoQuotingSeason($this->db);
                $season->load($row['id']);
                $seasons[] = $season;
            }
            return $seasons;
        }
    }
?>