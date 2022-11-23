<?php

	/**************************************************************************************************

	* Task class

	* This class represent one task

	*

	* Client:		FreightDragon

	* Version:		1.0

	* Date:			2011-11-02

	* Author:		C.A.W., Inc. dba INTECHCENTER

	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

	* E-mail:		techsupport@intechcenter.com

	* CopyRight 2011 FreightDragon. - All Rights Reserved

	***************************************************************************************************/

	

	class Task extends FdObject {

		const TABLE = "app_tasks";

		const LINK_TABLE = "app_task_member";

		

		public static $status_name = array(0 => "Active", 1 => "Completed");

		

		const TYPE_TODAY = 1;

		const TYPE_WEEK = 2;
   
        const TYPE_REMINDER = 3;
		

		public function create($data = null, $receivers = null) {

			if (!is_array($receivers)) throw new FDException("Task->create: invalid receivers array");

			parent::create($data);

			foreach ($receivers as $member_id) {

				$this->db->insert(self::LINK_TABLE, array('task_id' => $this->id, 'member_id' => $member_id));

				if ($this->db->isError) throw new FDException("Task->create: MySQL query error");

			}

		}

		

		public function getDate($format = "m/d/Y") {

			return date($format, strtotime($this->date));
		}

		

		public function getSender() {

			$sender = new Member($this->db);

			$sender->load($this->sender_id);

			return $sender;

		}

		

		public function getMembers() {

			$members = array();

			$rows = $this->db->selectRows("`member_id`", self::LINK_TABLE, "WHERE `task_id` = ".$this->id);

			if ($this->db->isError) throw new FDException("Task->getMembers: MySQL query error");

			foreach ($rows as $row) {

				$member = new Member($this->db);

				$member->load($row['member_id']);

				$members[] = $member;

			}

			return $members;

		}

		

		public function update($data = null, $assigned = null) {

			try {

				$this->db->transaction();

				parent::update($data);

				$this->db->query("DELETE FROM ".self::LINK_TABLE." WHERE `task_id` = {$this->id}");

				if ($this->db->isError) throw new FDException("Task->update: MySQL query error");

				if (is_array($assigned)) {

					foreach ($assigned as $member_id) {

						if (!ctype_digit((string)$member_id)) throw new FDException("Task->update: invalid Member ID");

						$this->db->insert(self::LINK_TABLE, array('task_id' => $this->id, 'member_id' => $member_id));

					}

				}

				$this->db->transaction('commit');

			} catch (FDException $e) {

				$this->db->transaction('rollback');

			}

		}

	}

?>