<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// controller class to deal with vehicl related operations
class Order extends CI_Controller {
    
    // default class constructor
    function __construct() {
        parent::__construct();
        $this->load->model('accounts');
        $this->load->model('orders');
    }

    public function reset_synced(){
        // log request
        log_request();
        
        if($this->input->post('TOKEN') == NUll){
            die("ACCESS DENIED");
        }

        $login_status = check_login($this->input->post('TOKEN'));

        if($login_status){
            $owner_id = $this->session->userdata('owner_id');

            $this->orders->reset_synced($owner_id);

            $data = array(
                'SUCCESS' => true,
                'MESSAGE' => 'Syncing resetting completed'
            );
        }

        // log response
    	log_response($data);
    	json_response($data);
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
            $synced = $this->input->post('synced');
            $num_records = $this->input->post('num_records');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');

            $res = $this->orders->get_orders($owner_id, $synced, $num_records, $from_date, $to_date);

            // get total synced
            $all_synced = $this->orders->get_synced_count($owner_id);

            // get total non-synced
            $all_unsynced = $this->orders->get_unsynced_count($owner_id);

            if(count($res) > 0){
            	$synced_ids = array();

	            $response = array();

	            $counter = 0;
	            foreach ($res as $r) {

	                $response[$counter]['OrderData'] = $r;

	                $synced_ids[] = $r['entityid'];
	                
	                // get vehicles
	                $vehicles = $this->orders->get_vehicles($r['entityid']);
	                $response[$counter]['Vehicles'] = $vehicles;

	                // get notes
	                $notes = $this->orders->get_notes($r['entityid']);
	                $response[$counter]['Notes'] = $notes;

	                $counter++;
	            }

	            $synced_ids = implode(",",$synced_ids);
	            $resp = $this->orders->mark_synced($synced_ids);
	            
	            $data = array(
	            	'SUCCESS' => true,
	            	'Data' => $response,
                    'AllSynced' => $all_synced,
                    'AllUnSynced' => $all_unsynced,
                    'AllOrders' => ($all_synced + $all_unsynced)
	            );
            } else {
            	$data = array(
	            	'SUCCESS' => true,
	            	'MESSAGE' => 'NO MORE DATA'
	            );
            }
            
            
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

    // API end point 
    public function create(){

    }

} 