<?php

// model to deal with vehicle operation
class Accounts extends CI_Model {

    // default mdel constructor
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    public function get_accounts($owner_id, $type, $start_from, $num_records, $from_date, $to_date){

        if($type == 1 ){
        	$type_where = " AND is_shipper = 1 ";
        } elseif($type == 2 ){
        	$type_where = " AND is_carrier = 1 ";
        } elseif($type == 3 ){
        	$type_where = " AND is_location = 1 ";
        } else {
        	$type_where = "";
        }

        if($start_from == NULL){
        	$start_from = 0;
        }

        if($num_records == NULL){
        	$num_records = 10;
        }

        if($num_records > 500){
        	$num_records = 500;
        }

        if($from_date == "" || $to_date == ""){
        	$where_date_range = "";
        } else {
        	$where_date_range = "AND ( create_date >= ".$from_date." OR create_date <= ".$to_date.") ";
        }

        $sql = "SELECT `id` FROM members WHERE parent_id = {$owner_id}";
        $res = $this->db->query($sql)->result();

        $members = array();
        foreach ($res as $key => $value) {
            $members[] = $value->id;
        }

        $members = implode(",",$members);      
        
        $sql = "SELECT * FROM app_accounts WHERE owner_id IN ({$members}) {$type_where} {$where_date_range} AND synced = 0 ORDER BY id desc LIMIT ".$start_from.",".$num_records;
        $res = $this->db->query($sql)->result();
        
        return $res;
    }

    public function mark_synced($ids){
    	$sql = "UPDATE app_accounts SET synced = 1 WHERE id IN ($ids)";
    	$res = $this->db->query($sql);
    	return $res;
    }

    // Function to pull all synced orders
    public function get_synced_count($owner_id)
    {
        $sql = "SELECT `id` FROM members WHERE parent_id = {$owner_id}";
        $res = $this->db->query($sql)->result();

        $members = array();
        foreach ($res as $key => $value) {
            $members[] = $value->id;
        }

        $members = implode(",",$members);  

        $query = "SELECT count(*) as `AllSynced` FROM app_accounts WHERE owner_id IN ({$members}) AND synced = 1 ";
        $query = $this->db->query($query);
        $result = $query->result_array();

        return $result[0]['AllSynced'];
    }

    // Function to pull all un-synced orders
    public function get_unsynced_count($owner_id)
    {
        $sql = "SELECT `id` FROM members WHERE parent_id = {$owner_id}";
        $res = $this->db->query($sql)->result();

        $members = array();
        foreach ($res as $key => $value) {
            $members[] = $value->id;
        }

        $members = implode(",",$members);  

        $query = "SELECT count(*) as `AllUnSynced` FROM app_accounts WHERE owner_id IN ({$members}) AND synced = 0 ";
        $query = $this->db->query($query);
        $result = $query->result_array();

        return $result[0]['AllUnSynced'];
    }
} 	