<?php

/**
 * Crons model for performing functionalities related to the crons in 
 * customer portal
 * 
 * @author Chetu Inc.
 * @version 1.0
 * @link www.chetu.com
 */
class Crons extends CI_Model {

    const THRESHOLD_TIME = 1;
    
    /**
     * Constructor to load the dependencies at the time of controller call
     */
    public function __construct() {
        parent::__construct();
        {
            $this->load->helper('url');
            $this->load->database();
        }
    }
    
    /**
     * Function to fetch out all the logged in users not using API
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    public function get_logged_in_users(){
         $sql = 'SELECT `user_id` FROM `api_login_logout_report` WHERE (TIMESTAMPDIFF(MINUTE, `last_hit_at`,NOW()) > '.self::THRESHOLD_TIME.') AND logout_at = "0000-00-00 00-00-00"';
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $users = array();
        foreach($result as $user_data){
            $users[] = $user_data;           
        }
        
        return $users;
    }
    
    /**
     * Function to terminate session for user logged in and not using API
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @param Array $user_id
     */
    public function logout_users($user_ids){        
        $user_ids_array = array();
        
        foreach($user_ids as $id){
            $user_ids_array[] = $id['user_id'];
        }
                
        $user_ids_comma_seperated = implode(",",$user_ids_array);
        
        if(!empty($user_ids_comma_seperated)){
            
            $sql = "DELETE FROM `api_keys` WHERE user_id IN (".$user_ids_comma_seperated.")";
            $this->db->query($sql);
            
            $sql = "UPDATE `api_login_logout_report` SET `logout_at` = '".date('Y-m-d h:i:s')."' "
            . "WHERE user_id IN (".$user_ids_comma_seperated.")";
            
            $this->db->query($sql);
            return 1;
        } else {            
            return 0;
        }
    }    
}