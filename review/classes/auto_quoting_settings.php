<?php
    /**************************************************************************************************
	* AutoQuotingSettings class
	* This class represent company's auto quoting options
	*
	* Client:		FreightDragon
	* Version:		1.0
	* Date:			2011-11-29
	* Author:		C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:		techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	***************************************************************************************************/

/**
 * Class AutoQuotingSettings
 * @property int $id
 * @property int $owner_id
 * @property int $is_enabled
 * @property int $email_type
 * @property int $surcharge_type
 * @property int $is_autoquote_unknown
 */
class AutoQuotingSettings extends FdObject {
    const TABLE = "app_autoquoting_settings";

    public function loadByOwnerId($owner_id) {
        if (!ctype_digit((string)$owner_id)) throw new FDException("Invalid Owner ID");
        if (!($this->db instanceof mysql)) throw new FDException("DB helper not set");
        $id = $this->db->selectField("id", self::TABLE, "WHERE `owner_id` = {$owner_id}");
        if ($this->db->isError){
            throw new FDException("MySQL query error");
        }
        $this->load($id);
        return $this;
    }
}
