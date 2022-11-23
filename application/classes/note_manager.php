<?php
/**************************************************************************************************
 * NoteManager class                                                                                                                                                *
 * This class for work with notes                                                                                                                                *
 *                                                                                                                                                                            *
 * Client:        FreightDragon                                                                                                                                    *
 * Version:        1.0                                                                                                                                                    *
 * Date:            2011-09-28                                                                                                                                        *
 * Author:        C.A.W., Inc. dba INTECHCENTER                                                                                                            *
 * Address:    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                                                                    *
 * E-mail:        techsupport@intechcenter.com                                                                                                            *
 * CopyRight 2011 FreightDragon. - All Rights Reserved                                                                                                *
 ***************************************************************************************************/
class NoteManager extends FdObjectManager {
	const TABLE = Note::TABLE;

	/* GETTERS */
	public function getNotes($entity_id = null, $order = null) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid input data");
		$notes = array(Note::TYPE_TO => array(), Note::TYPE_FROM => array(), Note::TYPE_INTERNAL => array());
		$rows = parent::get($order, null, "`entity_id` = " . (int)$entity_id . " AND `deleted` = 0");
		if (!is_array($rows)) return $notes;
		foreach ($rows AS $row) {
			$note = new Note($this->db);
			$note->load($row['id']);
			if (($_SESSION['member']['access_notes'] == 0 ) ||
					  $_SESSION['member']['access_notes'] == 1
					  || $_SESSION['member']['access_notes'] == 2
					)
					{
						$notes[$note->type][] = $note;
					}
			//$notes[$note->type][] = $note;
		}
		return $notes;
	}
	
	/* GETTERS */
	public function getNotesArrData($entity_id = null, $order = null) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid input data");
		$notes = array(Note::TYPE_TO => array(), Note::TYPE_FROM => array(), Note::TYPE_INTERNAL => array());
		
		$where_str = " WHERE `entity_id` = " . (int)$entity_id . " AND `deleted` = 0";
		
		$rows = $this->db->selectRows("`id`,type,status,sender_id", "app_notes", $where_str);
		
		if (!is_array($rows)) return $notes;
		foreach ($rows AS $row) {
			//print Note::TYPE_INTERNALNEW."---".$row['status'];
			$type = $row['type'];
			if(trim($row['status'])=="1")
			{
				
				$notes[Note::TYPE_INTERNALNEW][] = Note::TYPE_INTERNALNEW;
			}
			elseif($type == Note::TYPE_INTERNAL){
				if (($_SESSION['member']['access_notes'] == 0 && ($row['sender_id'] == (int)$_SESSION['member_id'])) ||
					  ($_SESSION['member']['access_notes'] == 1 && ($row['sender_id'] == (int)$_SESSION['member_id']))
					  || $_SESSION['member']['access_notes'] == 2
					)
					{
						$notes[$type][] = $type;
					}
			}
			else
			  $notes[$type][] = $type;
		}
		
		return $notes;
	}
	
	/* GETTERS */
	public function getNewNotes($entity_id = null, $order = null) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid input data");
		$notes = array(Note::TYPE_TO => array(), Note::TYPE_FROM => array(), Note::TYPE_INTERNAL => array());
		$rows = parent::get($order, null, "`entity_id` = " . (int)$entity_id . " AND `deleted` = 0 and status = 1");
		if (!is_array($rows)) return $notes;
		foreach ($rows AS $row) {
			$note = new Note($this->db);
			$note->load($row['id']);
			$notes[Note::TYPE_INTERNALNEW][] = $note;
		}
		return $notes;
	}


	public function getLastInternalNote($entity_id = null) {
		if (!ctype_digit((string)$entity_id)) throw new FDException("Invalid input data");
		$row = parent::get(null, null, "`entity_id` = " . (int)$entity_id . " AND `deleted` = 0 AND `type` = '" . (Note::TYPE_INTERNAL) . "' ORDER BY `created` DESC LIMIT 0,1 ");
		if (!is_array($row) || count($row) <= 0) {
			return "";
		}
		$note = new Note($this->db);
		$note->load($row[0]['id']);
		return $note->text;
	}
}

?>