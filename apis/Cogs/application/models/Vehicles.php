<?php

// model to deal with vehicle operation
class Vehicles extends CI_Model {
    
    const VEHICLE_MAKE = 'vehicle_makes';
    const VEHICLE_MODELS = 'vehicle_models';
    const VEHICLE_TYPES = 'app_vehicles_types';

    // default mdel constructor
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    // function to get vehicle make
    function get_make(){
        $sql = "SELECT * FROM ".self::VEHICLE_MAKE;
        $query = $this->db->query($sql);
        $makes = $query->result();
        return $makes;
    }

    // function to get vehicle model
    function get_model($make_id){
        $sql = "SELECT * FROM ".self::VEHICLE_MODELS." WHERE make_id = ".$make_id;
        $query = $this->db->query($sql);
        $models = $query->result();
        return $models;
    }

    // function to get vehicle type
    function get_type(){
        $sql = "SELECT * FROM ".self::VEHICLE_TYPES;
        $query = $this->db->query($sql);
        $types = $query->result();
        return $types;
    }
}