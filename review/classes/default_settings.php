<?php
/**************************************************************************************************
 * DefaultSettings clas                                                                                                                                                *
 * The class for work with default settings                                                                                                                    *
 *                                                                                                                                                                            *
 * Client:        FreightDragon                                                                                                                                    *
 * Version:        1.0                                                                                                                                                    *
 * Date:            2011-10-28                                                                                                                                        *
 * Author:        C.A.W., Inc. dba INTECHCENTER                                                                                                            *
 * Address:    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                                                                    *
 * E-mail:        techsupport@intechcenter.com                                                                                                            *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                                                                                *
 ***************************************************************************************************/

/**
 * @property int $billing_autopay;
 * @property int $billing_cc_id;
 */
class DefaultSettings extends FdObject {
	const TABLE = "app_defaultsettings";

	public function getByOwnerId($owner_id) {
		if (!ctype_digit((string)$owner_id)) throw new FDException("DefaultSettings->getByOwnerId: invalid owner ID");
		if (is_null($this->db)) throw new FDException("DefaultSettings->getByOwnerId: DB helper not set");
		$settingsId = $this->db->selectField("id", self::TABLE, "WHERE `owner_id` = " . (int)$owner_id);
		if (!$settingsId) throw new FDException("DefaultSettings->getByOwnerId: DefaultSettings not found in DB");
		$this->load($settingsId);
	}
}

?>