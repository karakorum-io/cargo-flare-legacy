<?php

	/**************************************************************************************************

	* TaskManager class

	* This is the class for work with tasks

	*

	* Client:		FreightDragon

	* Version:		1.0

	* Date:			2011-11-02

	* Author:		C.A.W., Inc. dba INTECHCENTER

	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

	* E-mail:		techsupport@intechcenter.com

	* CopyRight 2011 FreightDragon. - All Rights Reserved

	***************************************************************************************************/



	class TaskManager extends FdObjectManager {

		const TABLE = Task::TABLE;

		const LINK_TABLE = Task::LINK_TABLE;



		public function getByMemberId($member_id, $date_type, $completed = 0) {

      
			switch ($date_type) {

				case Task::TYPE_TODAY:

					$add_where = " CURDATE() = `date` ";

					break;

				case Task::TYPE_WEEK:

					$add_where = " WEEK(CURDATE()) = WEEK(`date`) ";

					break;
                case Task::TYPE_REMINDER:

					  $add_where = " CURDATE() = `date` OR  now() >= `reminder_date` ";
					  //print "<br>".$add_where;
                      
					break;
				default:

					throw new FDException("Taskmanager->getByMemberId: invalid Date type");

					break;

			}
			

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getByMemberId: invalid Member ID");

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getByMemberId: invalid Completion flag");

			$ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = ".(int)$member_id);

			$ids_s = array();

			foreach ($ids as $id_s) {

				$ids_s[] = $id_s['task_id'];

			}

			if (count($ids_s)>0){

				$rows = parent::get(null, null, "`id` IN (".implode(",", $ids_s).") AND {$add_where} AND `completed` = {$completed} AND `deleted` = 0");

			}else{

				return array();

			}

			$tasks = array();

			foreach($rows as $row) {

				$task = new Task($this->db);

				$task->load($row['id']);

				$tasks[] = $task;

			}

			return $tasks;

		}



		public function getBySenderId($sender_id, $date_type) {

			switch ($date_type) {

				case Task::TYPE_TODAY:

					$add_where = " CURDATE() = `date` ";

					break;

				case Task::TYPE_WEEK:

					$add_where = " WEEK(CURDATE()) = WEEK(`date`) ";

					break;

				default:

					throw new FDException("Taskmanager->getByMemberId: invalid Date type");

					break;

			}

			if (!ctype_digit((string)$sender_id)) throw new FDException("TaskManager->getByMemberId: invalid Member ID");

			

			$rows = parent::get(null, null, "`sender_id` = ".(int)$sender_id." AND {$add_where}");

			$tasks = array();

			foreach($rows as $row) {

				$task = new Task($this->db);

				$task->load($row['id']);

				$tasks[] = $task;

			}

			return $tasks;

		}

		

		public function getAllVisible($member_id, $order, $per_page) {

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getAllVisible: invalid member ID");

			$ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = ".(int)$member_id);

			$ids_s = array();

			$add_where = "";

			foreach ($ids as $id_s) {

				$ids_s[] = $id_s['task_id'];

			}

			if (count($ids_s) > 0) {

				$add_where = " OR `id` IN (".implode(',', $ids_s).") ";

			}

			$rows = parent::get($order, $per_page, "`sender_id` = {$member_id} {$add_where}");

			$tasks = array();

			foreach ($rows as $row) {

				$task = new Task($this->db);

				$task->load($row['id']);

				$tasks[] = $task;

			}

			return $tasks;

		}
		
		public function getConditionalTask($member_id, $order, $per_page, $where = "") {

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getAllVisible: invalid member ID");

			$ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = ".(int)$member_id);

			$ids_s = array();

			$add_where = "";

			foreach ($ids as $id_s) {

				$ids_s[] = $id_s['task_id'];

			}

			if (count($ids_s) > 0) {

				$add_where = " OR `id` IN (".implode(',', $ids_s).") ";

			}
            $add_where = " ".$where;
			$rows = parent::get($order, $per_page, "`sender_id` = {$member_id} {$add_where}");

			$tasks = array();

			foreach ($rows as $row) {

				$task = new Task($this->db);

				$task->load($row['id']);

				$tasks[] = $task;

			}

			return $tasks;

		}

		

		public function getTodaysCount($member_id) {

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getTodaysCount: invalid Member ID");

			$count = $this->db->selectRow("COUNT(a.`id`) as cnt", self::TABLE." a, ".self::LINK_TABLE." b", "WHERE a.`id` = b.`task_id` AND b.`member_id` = {$member_id} AND a.`completed` = 0 AND a.`date` = '".date('Y-m-d')."'");

			return $count['cnt'];

		}

		

		public function getAllCount($member_id) {

			if (!ctype_digit((string)$member_id)) throw new FDException("TaskManager->getTodaysCount: invalid Member ID");

			$assigned = array();

			$rows = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = ".$member_id);

			if ($this->db->isError) throw new FDException("TaskManager->getTodaysCount: MySQL query error");

			foreach($rows as $row) {

				$assigned[] = $row['task_id'];

			}

			$where = "`sender_id` = ".$member_id;

			if (count($assigned) > 0) $where.= " OR `id` IN (".implode(",", $assigned).")";

			$count = $this->db->selectRow('COUNT(`id`) as `cnt`', self::TABLE, "WHERE {$where}");

			return $count['cnt'];

		}

	}

?>