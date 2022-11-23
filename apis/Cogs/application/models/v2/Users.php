<?php

// model function to deal with DB releated operations for User
class Users extends CI_Model {
    
    const TABLE = 'app_accounts';
    
    public function __construct(){
        parent::__construct();{
            $this->load->database();
        }
    }
    
    // authenticating user based on credentials
    public function authenticate($fields){
        foreach ($fields as $key => $value){
            $this->db->where($key, $value);
        }
        $query = $this->db->get(self::TABLE);
        $data = $query->result_array();
        return $data;
    }
    
    // function to get refferrers
    public function get_referers($account_id = NULL){
        if(!$account_id){
            return FALSE;
        }
        
        $this->db->where('id', $account_id);
        $query = $this->db->get(self::TABLE);
        
        foreach($query->result() as $input_type) {
            $data[] = $input_type->referred_by;
            $data[] = $input_type->referred_id;
        }
        
        return $data;
    }
}