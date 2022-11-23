<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// controller class to deal with vehicl related operations
class Auth extends CI_Controller {
    
    // default class constructor
    function __construct() {
        parent::__construct();
        $this->load->model('authentication');
    }

    public function attempt(){
    	// log request
    	log_request();

    	$parent_id = $this->input->post('parent_id');
    	$username = $this->input->post('username');
    	$password = $this->input->post('password');

    	if($username == NUll){
    		die("ACCESS DENIED");
    	}

    	$res = $this->authentication->brute_force($parent_id, $username, $password);
        
        if($res){

    		$token = md5(rand(10000,99999));
    		$response = array(
    			'SUCCESS' => TRUE,
    			'TOKEN' => $token
    		);
    		$this->session->set_userdata(
                    array(
                        'token'=>$token,
                        'owner_id'=>$res[0]->owner_id
                    )
                );

    	} else {
    		$response = array(
    			'SUCCESS' => FALSE,
    			'MESSAGE' => 'Access Denied!'
    		);
    	}

    	// log response
    	log_response($response);
    	json_response($response);
    }

}