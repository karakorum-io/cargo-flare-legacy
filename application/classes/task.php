<?php
/**
 * Model Task Class to deal with task related database operations
 * 
 * @author Shahrukh
 * @version 2.0
 */
class Task extends FdObject {

    const TABLE = "app_tasks";
    const LINK_TABLE = "app_task_member";

    public static $status_name = array(0 => "Active", 1 => "Completed");

    const TYPE_TODAY = 1;
    const TYPE_WEEK = 2;
    const TYPE_REMINDER = 3;

    public function create($data = null, $receivers = null) {

        if (!is_array($receivers))
            throw new FDException("Task->create: invalid receivers array");
        parent::create($data);
        foreach ($receivers as $member_id) {
            $this->db->insert(
                self::LINK_TABLE, 
                array(
                    'task_id' => $this->id,
                    'member_id' => $member_id,
                    'reminder_date' => $data['reminder_date'],
                    'reminder_time' => $data['reminder_time']
                )
            );
            $this->db->insert('app_task_emails', array('task_id' => $this->id, 'member_id' => $member_id));
            if ($this->db->isError)
                throw new FDException("Task->create: MySQL query error");
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
    
    public function get_tast_completer(){
        if($this->completed_by == 0 || $this->completed_by == NULL){
            return 0;
        } else {
            $sender = new Member($this->db);
            $sender->load($this->completed_by);
            return $sender;
        }
    }
    
    public function get_tast_deleter(){
        if($this->deleted_by == 0 || $this->deleted_by == NULL){
            return 0;
        } else {
            $sender = new Member($this->db);
            $sender->load($this->deleted_by);
            return $sender;
        }
    }
    
    public function get_completed_date($format = "m/d/Y"){
        if($this->completed_date == 0 || $this->completed_date == NULL){
            return 0;
        } else {
            return date($format, strtotime($this->completed_date));
        }
    }
    
    public function get_deleted_date($format = "m/d/Y"){
        if($this->deleted_date == 0 || $this->deleted_date == NULL){
            return 0;
        } else {
            return date($format, strtotime($this->deleted_date));
        }
    }

    public function getMembers() {
        $members = array();
        $rows = $this->db->selectRows("`member_id`", self::LINK_TABLE, "WHERE `task_id` = " . $this->id);
        if ($this->db->isError)
            throw new FDException("Task->getMembers: MySQL query error");
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
            $this->db->query("DELETE FROM " . self::LINK_TABLE . " WHERE `task_id` = {$this->id}");
            $this->db->query("DELETE FROM app_task_emails WHERE `task_id` = {$this->id}");
            if ($this->db->isError)
                throw new FDException("Task->update: MySQL query error");
            if (is_array($assigned)) {
                foreach ($assigned as $member_id) {
                    if (!ctype_digit((string) $member_id))
                        throw new FDException("Task->update: invalid Member ID");
                    $this->db->insert('app_task_emails', array('task_id' => $this->id, 'member_id' => $member_id));
                    $this->db->insert(
                        self::LINK_TABLE, 
                        array(
                            'task_id' => $this->id,
                            'member_id' => $member_id,
                            'reminder_date' => $data['reminder_date'],
                            'reminder_time' => $data['reminder_time']
                        )
                    );
                }
            }
            $this->db->transaction('commit');
        } catch (FDException $e) {
            $this->db->transaction('rollback');
        }
    }
    
    public function update_custom($data = null, $assigned = null) {
        try {
            $this->db->transaction();
            parent::update($data);
            $this->db->transaction('commit');
            $this->send_notification_email($assigned,$this->id);
        } catch (FDException $e) {
            $this->db->transaction('rollback');
        } 
    }

    private function send_notification_email($assigned, $task_id){
        foreach ($assigned as $member_id) {
            $this->db->insert('app_task_emails', array('task_id' => $task_id, 'member_id' => $member_id));   
        }
    }
}
?>