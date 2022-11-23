<?php

/**

 * @version		1.0

 * @since		27.09.12

 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER

 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

 * @email		techsupport@intechcenter.com

 * @copyright	2012 Intechcenter. All Rights Reserved

 *

 * @property int $id

 * @property int $is_default

 * @property int|null $owner_id

 * @property string $name

 * @property string $description

 * @property string $body

 * @property int $is_system

 * @property int $sys_id

 */ 

class FormTemplate extends FdObject {

	const TABLE = "app_formtemplates";



	const SYS_QUOTE = 1;

	const SYS_ORDER = 2;

	const SYS_INVOICE = 3;

    const SYS_ORDER_COMMERCIAL = 4;
	
	const SYS_ORDER_ESIGN_TOTAL = 5;

	/**

	 * @param int $sysId

	 * @param int $ownerId

	 * @return \FdObject

	 */

	public function loadByOwnerId($sysId, $ownerId) {

		return $this->load($this->db->selectValue('id', self::TABLE, "WHERE `sys_id` = ".(int)$sysId." AND `owner_id` = ".(int)$ownerId));

	}

}

