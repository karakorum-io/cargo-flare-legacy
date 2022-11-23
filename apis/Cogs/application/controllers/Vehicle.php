<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// controller class to deal with vehicl related operations
class Vehicle extends CI_Controller {
    
    // default class constructor
    function __construct() {
        parent::__construct();
        $this->load->model('vehicles');
        $this->load->model('response');
        $this->load->model('logger');
    }

    // api end point to get vehicle make
    public function make(){

        $request = array();
        foreach ($this->input->post() as $key => $value) {
            $request[$key] = $value;
        }

        /* Logging Hit */
        Logger::log(
            $this->uri->uri_string,
            $this->input->ip_address(),
            $request,
            Logger::REQUEST_STATE_START,
            Logger::LOG_REQUEST
        );

        $makes = $this->vehicles->get_make();

        $response = array(
            'makes' => $makes
        );

        Logger::log(
           $this->uri->uri_string,
           $this->input->ip_address() ,
           $response,
           Logger::REQUEST_STATE_ENDED,
           Logger::LOG_RESPONSE
        );

        Response::sendSuccessJSONResponse(Response::MSG200, $response);
    }

    // api end point to get vehicle model
    public function model($make_id){

        $request = array();
        foreach ($this->input->post() as $key => $value) {
            $request[$key] = $value;
        }

        /* Logging Hit */
        Logger::log(
            $this->uri->uri_string,
            $this->input->ip_address(),
            $request,
            Logger::REQUEST_STATE_START,
            Logger::LOG_REQUEST
        );

        $models = $this->vehicles->get_model($make_id);

        $response = array(
            'models' => $models
        );

        Logger::log(
           $this->uri->uri_string,
           $this->input->ip_address() ,
           $response,
           Logger::REQUEST_STATE_ENDED,
           Logger::LOG_RESPONSE
        );

        Response::sendSuccessJSONResponse(Response::MSG200, $response);
    }

    // api end point to get vehicle type
    public function type(){
        $request = array();
        foreach ($this->input->post() as $key => $value) {
            $request[$key] = $value;
        }

        /* Logging Hit */
        Logger::log(
            $this->uri->uri_string,
            $this->input->ip_address(),
            $request,
            Logger::REQUEST_STATE_START,
            Logger::LOG_REQUEST
        );

        $types = $this->vehicles->get_type();

        $response = array(
            'types' => $types
        );

        Logger::log(
           $this->uri->uri_string,
           $this->input->ip_address() ,
           $response,
           Logger::REQUEST_STATE_ENDED,
           Logger::LOG_RESPONSE
        );

        Response::sendSuccessJSONResponse(Response::MSG200, $response);
    }
}