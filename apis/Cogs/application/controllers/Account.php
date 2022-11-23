<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// controller class to deal with vehicl related operations
class Account extends CI_Controller {
    
    // default class constructor
    function __construct() {
        parent::__construct();
        $this->load->model('accounts');
    }

    public function load(){
    	// log request
    	log_request();
        
        if($this->input->post('TOKEN') == NUll){
            die("ACCESS DENIED");
        }

    	$login_status = check_login($this->input->post('TOKEN'));
        
        if($login_status){

        	$owner_id = $this->session->userdata('owner_id');
            $start_from = $this->input->post('start_from');
            $num_records = $this->input->post('num_records');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $type = $this->input->post('type');

            $res = $this->accounts->get_accounts($owner_id, $type, $start_from, $num_records, $from_date, $to_date);
                
            $synced_ids = array();

            foreach ($res as $r) {
                $synced_ids[] = $r->id;
            }

            $synced_ids = implode(",",$synced_ids);
            $resp = $this->accounts->mark_synced($synced_ids);

            // get total synced
            $all_synced = $this->accounts->get_synced_count($owner_id);

            // get total non-synced
            $all_unsynced = $this->accounts->get_unsynced_count($owner_id);

            if($type == 1 ){
                $account_type = "Shipper Account";
            } elseif($type == 2 ){
                $account_type = "Carrier Account";
            } elseif($type == 3 ){
                $account_type = "Location Account";
            } else {
                $account_type = "All Accounts";
            }

            $data = array(
            	'SUCCESS' => true,
                'ACCOUNT TYPE' => $account_type,
                'Data' => $res,
                'AllSynced' => $all_synced,
                'AllUnSynced' => $all_unsynced,
                'AllOrders' => ($all_synced + $all_unsynced)
            );
            
        } else {
            $data = array(
                'SUCCESS' => false,
                'MESSAGE' => 'ACCESS DENIED!'
            );
        }
        
    	// log response
    	log_response($data);
    	json_response($data);
    }

} 