<?php
    /**************************************************************************************************
	* AutoQuotingSeason class
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-30
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/
    
    class AutoQuotingSeason extends FdObject {
        const TABLE = "`app_autoquoting_seasons`";
        
        protected $memberObjects = array();
        
        public function getStartDate($format = "m/d/Y") {
            return date($format, strtotime($this->start_date));
        }
        
        public function getEndDate($format = "m/d/Y") {
            return date($format, strtotime($this->end_date));
        }
        
        public function getLanesCount($reload = false) {
            if ($reload || !in_array('lanes_count', $this->memberObjects)) {
                $count = $this->db->selectRow("COUNT(*) as cnt", AutoQuotingLane::TABLE, "WHERE `status` = 'Active' AND `season_id` = {$this->id}");
                if ($this->db->isError) throw new FDException("MySQL query error");
                $this->memberObjects['lanes_count'] = (int)$count['cnt']; 
            }
            return $this->memberObjects['lanes_count'];
        }

        public function getLanes($reload = false) {
            if ($reload || !in_array('lanes', $this->memberObjects)) {
                $lm = new AutoQuotingLaneManager($this->db);
                $this->memberObjects['lanes'] = $lm->getLanes(null, null, "`season_id` = {$this->id}");
            }
            return $this->memberObjects['lanes'];
        }

        public function duplicate() {
            try {
                $this->db->transaction("start");
                $lanes = $this->getLanes();
                $season = $this->selfclone();
                foreach ($lanes as $lane) {
                    $lane->duplicate($season->id);
                }
                $this->db->transaction("commit");
            } catch (FDException $e) {
                $this->db->transaction("rollback");
                throw $e;
            }
        }

        public function checkAccess($owner_id) {
            if ($owner_id != $this->owner_id) throw new FDException("Access Denied");
        }
    }
?>