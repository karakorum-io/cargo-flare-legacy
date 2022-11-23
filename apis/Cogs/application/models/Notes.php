<?php

/**
 * Notes Model for performing functionalities related to the orders in 
 * customer portal
 * 
 * @author Chetu Inc.
 * @version 1.0
 * @link www.chetu.com
 */

class Notes extends CI_Model {

    const TABLE = 'app_notes';
    
    const C_PORTAL_AS_MEMBER = 230;
    
    const TYPE = array(
        'ZERO' => 0,
        'ONE' => 1,
        'TWO' => 2,
        'THREE' => 3
    );
    
    const PRIORITY = array(
        'NORMAL' => 0,
        'LOW' => 1,
        'HIGH' => 2
    );

    /**
     * Constructor to load the dependencies at the time of controller call
     */
    public function __construct() {
        parent::__construct();
        {
            $this->load->helper('url');
            $this->load->library('form_validation');
            $this->load->database();
            $this->load->library('email');
            $this->load->model('payments');
        }
    }
    
    /**
     * Function / Method to fetch default setting data
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    public function add( $entity_id, $sender_customer_portal, $type ,$note, $priority ) {

        $sql = "INSERT INTO ".self::TABLE." (entity_id, sender_id ,sender_customer_portal,type,text,priority)"
                . " values ('{$entity_id}', '".self::C_PORTAL_AS_MEMBER."' ,'{$sender_customer_portal}','{$type}', '{$note}','{$priority}' )";
        $query = $this->db->query($sql);
        
        $response = array(
            'RESPONSE' => $query
        );
        
        return $response;
    }

}