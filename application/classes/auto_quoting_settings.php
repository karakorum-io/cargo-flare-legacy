<?php

class AutoQuotingSettings extends FdObject
{
    const TABLE = "app_autoquoting_settings";

    public function loadByOwnerId($owner_id)
    {
        if (!ctype_digit((string) $owner_id)) {
            throw new FDException("Invalid Owner ID");
        }

        if (!($this->db instanceof mysql)) {
            throw new FDException("DB helper not set");
        }

        $id = $this->db->selectField("id", self::TABLE, "WHERE `owner_id` = {$owner_id}");
        if ($this->db->isError) {
            throw new FDException("MySQL query error");
        }
        $this->load($id);
        return $this;
    }
}
