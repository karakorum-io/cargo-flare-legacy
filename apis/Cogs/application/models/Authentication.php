<?php

// model to deal with Authentication operations
class Authentication extends CI_Model {

	// default mdel constructor
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    // for authentication and returning token
    public function brute_force($parent_id, $user, $password){

    	$this->db->where('owner_id',$parent_id);
    	$this->db->where('tai_user',$user);
    	$this->db->where('tai_password',$password);

	    $query = $this->db->get('app_defaultsettings');
	    $result = $query->result();

	    if(empty($result)){
	    	return FALSE;
	    } else {
	    	return $result;
	    }
    }

} 