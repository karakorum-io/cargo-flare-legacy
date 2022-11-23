<?php
/**
 * @version		1.0
 * @since		26.09.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 *
 * @property int $id;
 * @property int $entity_id
 * @property string $name
 * @property string $filename
 * @property string $signature
 */ 
class EntityDoc extends FdObject {
	const TABLE = "app_entity_docs";

	/**
	 * @param mysql $db
	 * @param int $entity_id
	 * @return EntityDoc[]
	 */
	public static function getEntityDocs($db, $entity_id) {
		$docs = array();
		$docIds = $db->selectRows('id', self::TABLE, "WHERE `entity_id` = ".(int)$entity_id);
		foreach ($docIds as $docId) {
			$doc = new EntityDoc($db);
			$doc->load($docId['id']);
			$docs[] = $doc;
		}
		return $docs;
	}
}
