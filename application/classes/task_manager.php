<?php

/**
 * Task manager model to deal with database related minor operation in task module
 *
 * @author Shahrukh
 * @version 2.0
 */
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
                break;
           
            default:
                throw new FDException("Taskmanager->getByMemberId: invalid Date type");
                break;
        }

        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getByMemberId: invalid Member ID");

        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getByMemberId: invalid Completion flag");

        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . (int) $member_id);
        $ids_s = array();

        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $rows = parent::get(null, null, "`id` IN (" . implode(",", $ids_s) . ") AND {$add_where} AND `completed` = {$completed} AND `deleted` = 0");
        } else {
            return array();
        }

        $tasks = array();

        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }

        return $tasks;
    }

    public function get_notification_list_task($member_id ,$search) {

        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getAllVisible: invalid member ID");
       
        $search_string = "";
        if($search != ""){
            $search_string = " AND (`message` LIKE '%".$search."%' OR `taskdata` LIKE '%".$search."%')";
        }
        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . (int) $member_id);
        $ids_s = array();
        $add_where = "";
        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $add_where = " `id` IN (" . implode(',', $ids_s) . ") ";
        }

        $add_where .= " " . $where;
        $rows = parent::get(null, null, "{$add_where} AND completed = 0 AND deleted = 0 AND reminder = 1".$search_string);

        $tasks = array();
        
        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }
    
    public function get_notification_task($member_id) {

        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `reminder_date` <= CURDATE() AND ( CURRENT_TIME() > `reminder_time` AND CURRENT_TIME() < AddTime(`reminder_time`,'00:00:30') ) AND `member_id` = " . (int) $member_id);
        $ids_s = array();

        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $sql = "SELECT `id` FROM app_tasks WHERE `id` IN (" . implode(",", $ids_s) . ") AND `completed` = 0 AND `deleted` = 0";
            $rows = $this->db->query($sql);
        } else {
            return array();
        }

        $tasks = array();

        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }

    public function snooze($task_id,$time){
        $sql = "UPDATE app_tasks SET reminder_time = AddTime( CURRENT_TIME(), '".$_POST['snooze_time']."') WHERE `id` = ".$task_id;
        $res = $this->db->selectRows($sql);

        $sql = "UPDATE app_task_member SET reminder_time = AddTime( CURRENT_TIME(), '".$_POST['snooze_time']."') WHERE `task_id` = ".$task_id." AND `member_id` = ". $_SESSION['member_id'];
        $res = $this->db->selectRows($sql);
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

        if (!ctype_digit((string) $sender_id))
            throw new FDException("TaskManager->getByMemberId: invalid Member ID");

        $rows = parent::get(null, null, "`sender_id` = " . (int) $sender_id . " AND {$add_where}");
        $tasks = array();

        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }

    public function getAllVisible($member_id, $order, $per_page) {
        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getAllVisible: invalid member ID");

        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . (int) $member_id);
        $ids_s = array();
        $add_where = "";

        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }
       
        if (count($ids_s) > 0) {
            $add_where = " OR `id` IN (" . implode(',', $ids_s) . ") ";
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

    public function get_list_todays_task($member_id,$search) {
        
        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getAllVisible: invalid member ID");
       
        $search_string = "";
        if($search != ""){
            $search_string = " AND (`message` LIKE '%".$search."%' OR `taskdata` LIKE '%".$search."%')";
        }
        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . (int) $member_id);
        $ids_s = array();
        $add_where = "";
        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $add_where = " `id` IN (" . implode(',', $ids_s) . ") ";
        }

        $add_where .= " " . $where;
        $rows = parent::get(null, null, "{$add_where} AND completed = 0 AND deleted = 0 ".$search_string);

        $tasks = array();
        
        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }

    public function getConditionalTask($member_id, $order, $per_page, $where = "") {
        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getAllVisible: invalid member ID");
       
        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . (int) $member_id);
        $ids_s = array();
        $add_where = "";
        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $add_where = " ( `sender_id` = {$member_id} OR `id` IN (" . implode(',', $ids_s) . ") ) ";
        }

        $add_where .= " " . $where;

        //echo $add_where;die("<br>Under progress!");
        $rows = parent::get($order, $per_page, " {$add_where}");

        $tasks = array();

        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }

    public function getConditionalTaskByEntity($entity_id, $order, $per_page, $where = "") {
        // if (!ctype_digit((string) $member_id))
        //     throw new FDException("TaskManager->getAllVisible: invalid member ID");
       
        $ids = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `entity_id` = " . (int) $entity_id);
        $ids_s = array();
        $add_where = "";
        foreach ($ids as $id_s) {
            $ids_s[] = $id_s['task_id'];
        }

        if (count($ids_s) > 0) {
            $add_where = " OR `id` IN (" . implode(',', $ids_s) . ") ";
        }
       
        $add_where = " " . $where;
        $rows = parent::get($order, $per_page, "`sender_id` = {$member_id} {$add_where}");

        $tasks = array();

        foreach ($rows as $row) {
            $task = new Task($this->db);
            $task->load($row['id']);
            $tasks[] = $task;
        }
        return $tasks;
    }

    /**
     * Function to show tasks data on entity details page
     * @param type $entity_id
     * @param type $member_id
     * @param type $where
     * @return Array
     */
    public function get_user_entity_task($entity_id, $member_id, $where){
       
        $sql = "SELECT * FROM app_tasks WHERE `entity_id` = ".$entity_id." ".$where;
        $tasks = $this->db->selectRows($sql);
       
        $task_data = array();
        $i=0;
        foreach($tasks as $t){
            $task_data[$i]['data'] = $t;
            $sql = "SELECT member_id FROM app_task_member WHERE `task_id` = ".$t['id'];
            $assigned_ids = $this->db->selectRows($sql);
           
            $assigned_user = array();
            foreach($assigned_ids as $ass){
                $sql = "SELECT contactname FROM members WHERE `id` = ".$ass['member_id'];
                $members = $this->db->selectRows($sql);
                $assigned_user[] = $members[0]['contactname'];
            }
            $task_data[$i]['assigned_user'] = $assigned_user;
            $i++;
        }
        return $task_data;
    }
   
    public function getTodaysCount($member_id) {
        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getTodaysCount: invalid Member ID");
        $count = $this->db->selectRow("COUNT(a.`id`) as cnt", self::TABLE . " a, " . self::LINK_TABLE . " b", "WHERE a.`id` = b.`task_id` AND b.`member_id` = {$member_id} AND a.`completed` = 0 AND a.`date` = '" . date('Y-m-d') . "'");
        return $count['cnt'];
    }

    public function getAllCount($member_id) {
        if (!ctype_digit((string) $member_id))
            throw new FDException("TaskManager->getTodaysCount: invalid Member ID");
        $assigned = array();
        $rows = $this->db->selectRows("`task_id`", self::LINK_TABLE, "WHERE `member_id` = " . $member_id);
        if ($this->db->isError)
            throw new FDException("TaskManager->getTodaysCount: MySQL query error");
        foreach ($rows as $row) {
            $assigned[] = $row['task_id'];
        }
        $where = "`sender_id` = " . $member_id;
        if (count($assigned) > 0)
            $where .= " OR `id` IN (" . implode(",", $assigned) . ")";
        $count = $this->db->selectRow('COUNT(`id`) as `cnt`', self::TABLE, "WHERE {$where}");
        return $count['cnt'];
    }
}
?>
