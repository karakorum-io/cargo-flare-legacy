<?php

class AutoQuotingSeasonManager extends FdObjectManager
{
    const TABLE = AutoQuotingSeason::TABLE;

    public function getSeasons($order, $per_page, $owner_id)
    {
        if (!ctype_digit((string) $owner_id)) {
            throw new FDException("Invalid Owner ID");
        }

        $rows = parent::get($order, $per_page, "`owner_id` = " . $owner_id);
        $seasons = array();
        foreach ($rows as $row) {
            $season = new AutoQuotingSeason($this->db);
            $season->load($row['id']);
            $seasons[] = $season;
        }
        return $seasons;
    }
}
