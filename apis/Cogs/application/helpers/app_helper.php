<?php

if (!defined('BASEPATH')){
    exit('No direct script access allowed');
}

//function to check login user
function check_login($token){
    $ci=& get_instance();
    if($ci->session->userdata('token') == $token){
        return TRUE;
    } else {
        return FALSE;
    }
}

// function to log request on api hit
function log_request(){

    $ci=& get_instance();

    $params = json_encode($ci->input->post());
    $ip = $ci->input->ip_address();
    $uri = $ci->uri->uri_string;    
    
    $data = array(
        'ip' => $ip,
        'uri' => $uri,
        'params' => $params,
        'flow' => 1
    );

    $ci->db->insert('api_tai_log', $data);   
}

// function to log response on api hit
function log_response($response){

    $ci=& get_instance();

    $params = json_encode($response);
    $ip = $ci->input->ip_address();
    $uri = $ci->uri->uri_string;    
    
    $data = array(
        'ip' => $ip,
        'uri' => $uri,
        'params' => $params,
        'flow' => 2
    );

    $ci->db->insert('api_tai_log', $data);   
}

// sending JSON response
function json_response($data){
    echo json_encode($data);
    exit();
}

// function to update last hit
function update_hit() {
    return date('y-m-d h:i:s');
}

// function to validate api access key to prevent it from outside sources
function validate_access_key(){
    $ci=& get_instance();
    // validating api token
    if( !($ci->input->get_request_header("Access-Key") === get_access_key()) ){
        write_response($ci->lang->line('MSG_999'));
    }
}

// get valid active api key from database
function get_access_key(){
    // getting access key from DB
    $sql = "SELECT accessKey FROM api_access_key WHERE status = 1";
    $ci=& get_instance();
    $query = $ci->db->query($sql);
    $row = $query->result();
    return $row[0]->accessKey;
}

// funtion to write json output response
function write_response($msg, $response = null) {
    if ($response) {
        $json = array('Message' => $msg,'Response' => $response);
    } else {
        $json = array('Message' => $msg);
    }
    // response code here
    header("HTTP/1.1 200 OK");
    echo json_encode($json);
    exit;
}

// function to generate api key on login
function generate_api_key(){
    return md5(date('ymdhis'));
}