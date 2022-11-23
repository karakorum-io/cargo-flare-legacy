<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Shipper Controller
 *
 * This is the Controller for all the operations related to the shippers. From their
 * login to user accessibility etc.
 *
 * @package	CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author      Chetu
 * @link	https://www.chetu.com/
 */

class Cron extends CI_Controller {
    
    /**
     * This is the function to load dependencies
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function __construct() {
        parent::__construct();
        $this->load->model('crons');
    }

    /**
     * This is index function to know the Introduction of the API and Credits
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function index() {
        $this->load->view('cron/dashboard');
    }
    
    /**
     * Action to terminate session for users logged in customer portal when no hit
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @return HTML Response
     */
    function terminate_no_hit_sessions(){        
        $users = $this->crons->get_logged_in_users();
                
        if($this->crons->logout_users($users)){
            $data = array(
                'status'=>'Cron Terminated Successfully!',
                'terminated_sessions'=>$users
            );
        } else {
            $data = array(
                'status'=>'Cron Terminated, No user logged out!',
                'terminated_sessions'=>array()
            );
        }
        
        $this->load->view('cron/terminate_no_hit_sessions',$data);
    }
}
