<?php

class AutoQuotingLaneManager extends FdObjectManager
{
    const TABLE = AutoQuotingLane::TABLE;

    public function getLanes($order = null, $per_page = 50, $add = "")
    {
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
