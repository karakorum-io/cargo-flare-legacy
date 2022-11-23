<?php

class Notification extends FdObject
{
    const TABLE = "app_notification";
    
    /**
     * Function to add record for notification and notification related data.
     * 
     * @return Void
     * @author Shahrukh
     * @version 1.0.0
     */
    public function add($for=null, $data = null) 
    {
        $ins_arr = array(
            'user_type' => 'member',
            'for_id' => $for == null  ? $_SESSION['member']['id'] : $for,
            'title' => $data['title'],
            'message' => $data['message'],
            'link' => $data['link']
        );

        $data = $this->db->PrepareSql(self::TABLE, $ins_arr);
        $this->db->insert(self::TABLE, $data);
    }
}