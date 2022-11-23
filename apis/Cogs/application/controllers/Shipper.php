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

class Shipper extends CI_Controller {
    
    /**
     * This is the function to load dependencies
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function __construct() {
        parent::__construct();
        $this->load->model('shippers');
        $this->load->model('quotes');
        $this->load->model('response');
        $this->load->model('logger');
        $this->load->model('orders');
        $this->load->model('v2/users');
    }

    /**
     * This is index function to know the Introduction of the API and Credits
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function index() {
        
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        /* Logging Hit */
        Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);
              
        /* Validate API User */
        if ($this->input->post('accessKey') != $this->shippers->accessKey()) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::ACCESS_KEY
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            /*  Sending JSON Response*/
            Response::sendErrorJSONResponse($response);
        }
        
        $response = array(
            'title' => 'FreightDragon-API-1.0.0',
            'author' => 'Chetu Inc.',
            'link' => 'https://www.chetu.com/'
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

    /**
     * This function / action is for shippers to login.
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function login() {
        
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        /* Logging Hit */
        Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);
        
        /* Validate API User */
        if ($this->input->post('accessKey') != $this->shippers->accessKey()) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::ACCESS_KEY
            );
            Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address() ,
                $response,
                Logger::REQUEST_STATE_ENDED,
                Logger::LOG_RESPONSE
            );
            
            /* Sending JSON Response*/
            Response::sendErrorJSONResponse($response);           
        }
        
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $accountId = $this->input->post('id');
        
        if (empty($email) || empty($password) || empty($accountId)) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ONGOING,
                    Logger::LOG_RESPONSE
            );
            
            /* sending JSON Response*/
            Response::sendErrorJSONResponse($response);            
        }
        
        /* Not trusting User inputs */
        $email = addslashes($email);
        $password = addslashes($password);
        $password = md5($password);

        /* checking in database for existance */
        $result = $this->shippers->select(
                "app_accounts", array(
                'email' => $email,
                "password" => $password,
                "id" => $accountId
                )
        );

        if (count($result) > 0) {

            /* destroy previous session if any */
            
            $loginStatusData = $this->shippers->select(
                "api_keys", array(
                "user_id" => $result[0]['id']
                )
            );
            
            if (count($loginStatusData) > 0) {
                $this->logout($loginStatusData[0]['key']);
            }
            
            /* Successfull Login */
            $userData = array(
                'id' => $result[0]['id']
            );

            $id = $result[0]['id'];
            $email = $result[0]['email'];

            /* Generating API Key / Token */
            $apikey = md5($id . $email . time());

            $data = array(
                'key' => $apikey,
                'user_id' => $id,
            );
            
            /* maintaining keys in database */
            $this->shippers->insert('api_keys', $data);

            /* Login log report */
            $this->loginReport($userData);
            
            $response = array(
                'apikey' => $apikey,
                'userData' => $result
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);
        } else {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            /* Login Failure */
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * This function / action is for shippers to logout.
     * 
     * @param String $key after login access token
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 10112017
     */
    function logout($key=NULL) {
       
        if($key == NULL){
            $apiKey = $this->input->post('apikey');            
            $request = array();
            foreach($this->input->post() as $key => $value){
                $request[$key] = $value;
            }
            /* Logging Hit */
            Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);
            
        } else {
            $apiKey = $key;
        }
        
        if (empty($apiKey)) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            /* Maintaing Logout Log */
            $this->logoutReport($apiKey);

            /* Destroying APi Key */
            if (!empty($apiKey)) {
                $this->shippers->delete("api_keys", $apiKey);
            }
            
            /* Returning response */
            if(empty($key)){
                
            } else {
                
                $response = array(
                    'response' => "USER LOGGED OUT"
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
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * This function / action is for shippers to change password
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 08122017
     */
    function changePassword() {
        
        $apiKey = $this->input->post('apikey');
        $request = array();
        foreach ($this->input->post() as $key => $value) {
            $request[$key] = $value;
        }
        /* Logging Hit */
        Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);
        
        $apiKey = $this->input->post('apikey');

        if (empty($apiKey)) {
             /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string, $this->input->ip_address(), $response, Logger::REQUEST_STATE_ENDED, Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            $old = md5($this->input->post('old_password'));
            $new = md5($this->input->post('new_password'));

            $shipperId = $this->shippers->getUserId($apiKey);

            /* check old password */
            $result = $this->shippers->select(
                    "app_accounts", array(
                    'id' => $shipperId
                    )
            );

            if ($result[0]['password'] == $old) {

                $this->shippers->update(
                        'app_accounts', 'id', $shipperId, array('password' => $new
                ));

                $response = array(
                    'response' => "USER PASSWORD CHANGED"
                );
                Logger::log(
                        $this->uri->uri_string, $this->input->ip_address(), $response, Logger::REQUEST_STATE_ENDED, Logger::LOG_RESPONSE
                );
                Response::sendSuccessJSONResponse(Response::MSG200, $response);
                
            } else {
                /* Logging */
                $response = array(
                    "status" => Response::STATUS_FAILURE,
                    "error" => Response::MSG400
                );

                Logger::log(
                        $this->uri->uri_string, $this->input->ip_address(), $response, Logger::REQUEST_STATE_ENDED, Logger::LOG_RESPONSE
                );

                Response::sendErrorJSONResponse($response);
            }
        } else {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string, $this->input->ip_address(), $response, Logger::REQUEST_STATE_ENDED, Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Function / Action to provide API user profile information
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @release 08052018
     */
    function user_profile(){

        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        
        $apiKey = $this->input->post('apikey');
         
        /* Logging Hit */
        Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address(),
                $request,
                Logger::REQUEST_STATE_START,
                Logger::LOG_REQUEST
        );

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            /* getting account id for this user*/
            $account_id = $this->shippers->getUserId($apiKey);
            
            $user_profile = $this->shippers->UserProfile($account_id);
            
            /* Gathering dashboard information*/
            $response = $user_profile;
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
             /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API to edit customer data from customer portal
     * 
     * @author Shahrukh
     * @version 1.0
     * @return JSON Response
     */
    function EditProfile(){

        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        
        $apiKey = $this->input->post('apikey');

        $company_name = $this->input->post('company_name');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $phone1 = $this->input->post('phone1');
        $phone2 = $this->input->post('phone2');
        $cell = $this->input->post('cell');
        $fax = $this->input->post('fax');
        $tax_id_num = $this->input->post('tax_id_num');

        $Address1 = $this->input->post('address1');
        $Address2 = $this->input->post('address2');
        $City = $this->input->post('city');
        $State = $this->input->post('state');
        $Zip = $this->input->post('zip_code');
        $Country = $this->input->post('country');

        /* Logging Hit */
        Logger::log(
            $this->uri->uri_string,
            $this->input->ip_address(),
            $request,
            Logger::REQUEST_STATE_START,
            Logger::LOG_REQUEST
        );

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            /* getting account id for this user*/
            $account_id = $this->shippers->getUserId($apiKey);
            
            $Params = array(
                "id" => $account_id,
                "company_name" => $company_name,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "phone1" => $phone1,
                "phone2" => $phone2,
                "cell" => $cell,
                "fax" => $fax,
                "tax_id_num" => $tax_id_num,
                "address1" => $Address1,
                "address2" => $Address2,
                "city" => $City,
                "state" => $State,
                "zip_code" => $Zip,
                "country" => $Country
            );

            $user_profile = $this->shippers->ModifyProfile($Params);
            
            /* Gathering dashboard information*/
            $response = $user_profile;
            
            Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address(),
                $response,
                Logger::REQUEST_STATE_ENDED,
                Logger::LOG_RESPONSE
            );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Profile Edit History
     * 
     * @author Shahrukh
     * @version 1.0
     */
    function ProfileUpdateHistory(){
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        
        $apiKey = $this->input->post('apikey');
        $AccountID = $this->input->post('AccountID');

        /* Logging Hit */
        Logger::log(
            $this->uri->uri_string,
            $this->input->ip_address(),
            $request,
            Logger::REQUEST_STATE_START,
            Logger::LOG_REQUEST
        );

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            /* getting account id for this user*/
            $account_id = $this->shippers->getUserId($apiKey);

            $History = $this->shippers->GetHistory($account_id);
            
            /* Gathering dashboard information*/
            $response = $History;
            
            Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address(),
                $response,
                Logger::REQUEST_STATE_ENDED,
                Logger::LOG_RESPONSE
            );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Function / action to fetch order history related data and return to API
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @returns JSON response
     */
    function order_history(){
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');
        
        /* Logging Hit */
        Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address(),
                $request,
                Logger::REQUEST_STATE_START,
                Logger::LOG_REQUEST
        );
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            /* Getting History Data */
            $response = $this->orders->get_history($entity_id);
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);
        } else {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }
    
    /**
     * This function / action to Send the Dashboard Data to the user after login
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 16112017
     */
    function dashboard() {

        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        
         $apiKey = $this->input->post('apikey');
        
        /* Logging Hit */
        Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address(),
                $request,
                Logger::REQUEST_STATE_START,
                Logger::LOG_REQUEST
        );
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
           
            /* getting account id for this user*/
            $account_id = $this->shippers->getUserId($apiKey);
            
            $dashboard_data = $this->shippers->get_dashboard($account_id);
            
            /* Gathering dashboard information*/
            $response = $dashboard_data;
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address(),
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * This function / action for requesting a quote from customer portal
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 29112017
     */
    function requestQuotes() {

        /* obtaining api key */
        $apiKey = $this->input->post('apikey');

        if ($this->shippers->checkLogin($apiKey)) {

            /* Update Last Hit for User */
            $this->updateLastHit($apiKey);

            /* getting request parameters */
            $origin_city = $this->input->post('origin_city');
            $origin_state = $this->input->post('origin_state');
            $origin_zip = $this->input->post('origin_zip');
            $origin_country = $this->input->post('origin_country');
            $destination_city = $this->input->post('destination_city');
            $destination_state = $this->input->post('destination_state');
            $destination_zip = $this->input->post('destination_zip');
            $destination_country = $this->input->post('destination_country');
            $numVehicles = $this->input->post('vehicles');
            $shipping_est_date = $this->input->post('shipping_est_date');
            $shipping_ship_via = $this->input->post('shipping_ship_via');
            $shipping_notes = $this->input->post('shipping_notes');
            $send_mail = $this->input->post('send_mail');

            /* fetching shipper data */
            $shipperId = $this->shippers->getUserId($apiKey);
            $result = $this->shippers->select(
                "app_accounts", array(
                    'id' => $shipperId
                )
            );

            /* getting shipper data */
            $shipperOwnerId = $result[0]['owner_id'];
            $shipperMemberId = $result[0]['member_id'];

            /* getting membes id */
            //$this->members->getMemberId();

            $refferer_array = $this->users->get_referers($shipperId);
            
            $quote = array(
                'type' => '1',
                'number' => $this->quotes->get_next_number($shipperId),
                'received' => date('Y-m-d H:i:s'),
                'creator_id' => $this->shippers->get_assigned($shipperId),
                'account_id' => $shipperId,
                'assigned_id' => $this->shippers->get_assigned($shipperId),
                'parentid' => $shipperOwnerId,
                'salesrepid' => '',
                'est_ship_date' => date("Y-m-d", strtotime($shipping_est_date)),
                'status' => Quotes::STATUS,
                'ship_via' => $shipping_ship_via,
                'information' => $shipping_notes,
                'referred_by' => $refferer_array[0],
                'referred_id' => $refferer_array[1]
            );

            /* starting database transaction */
            $this->db->trans_start();

            /* creating new quotes */
            $entity_id = $this->quotes->createQuote($quote);
           
            /* gathering origin */
            $origin = array(
                'city' => $origin_city,
                'state' => $origin_state,
                'zip' => $origin_zip,
                'country' => $origin_country
            );

            /* gathering destination */
            $destination = array(
                'city' => $destination_city,
                'state' => $destination_state,
                'zip' => $destination_zip,
                'country' => $destination_country
            );

            /* getting vehicle data */
            $vehicles = array();

            for ($i = 0; $i < $numVehicles; $i++) {
                $vehicles[$i]['entity_id'] = $entity_id;
                $vehicles[$i]['year'] = $this->input->post('year' . $i);
                $vehicles[$i]['make'] = $this->input->post('make' . $i);
                $vehicles[$i]['model'] = $this->input->post('model' . $i);
                $vehicles[$i]['type'] = $this->input->post('type' . $i);
                $vehicles[$i]['inop'] = $this->input->post('inop' . $i);
            }

            
            
            $shipper = array(
                'fname' => $result[0]['first_name'],
                'lname' => $result[0]['last_name'],
                'email' => $result[0]['email'],
                'company' => $result[0]['company_name'],
                'phone1' => str_replace("-", "", $result[0]['phone1']),
                'phone2' => str_replace("-", "", $result[0]['phone2']),
                'mobile' => str_replace("-", "", $result[0]['cell']),
                'fax' => $result[0]['fax'],
                'address1' => $result[0]['address1'],
                'address2' => $result[0]['address2'],
                'city' => $result[0]['city'],
                'state' => $result[0]['state'],
                'zip' => $result[0]['zip_code'],
                'country' => $result[0]['country'],
                'shipper_type' => $result[0]['shipper_type'],
                'shipper_hours' => $result[0]['hours_of_operation']
            );

            /* gathering follow up informartion */
            $days = $this->quotes->getFirstFollowUp($shipperOwnerId);

            $followUp = array(
                'type' => 0,
                'created' => date('Y-m-d h:i:s'),
                'followup' => date("Y-m-d", mktime(0, 0, 0, (int) date("m"), (int) date("d") + $days, (int) date("Y"))),
                'entity_id' => $entity_id,
                'sender_id' => $shipperMemberId ? "" : 0
            );

            /* gathering notes */
            $notes = array(
                'entity_id' => $entity_id,
                'type' => 2,
                'text' => $shipping_notes
            );

            try {

                /* inserting shipper in app_ahippers */
                $insertedShipperId = $this->quotes->createShipper($shipper);
                /* inserting shipper in origin */
                $insertedOriginId = $this->quotes->createOrigin($origin);
                /* inserting shipper in destination */
                $insertedDestinationId = $this->quotes->createDestination($destination);
                /* inserting shipper in vehicles */
                $insertedVehicleId = $this->quotes->createVehicle($vehicles);

                /* updating entities table */
                $hash = $this->quotes->findFreeHash();

                $update_entity = array(
                    'shipper_id' => $insertedShipperId,
                    'origin_id' => $insertedOriginId,
                    'destination_id' => $insertedDestinationId,
                    'distance' => 'NULL',
                    'hash' => $hash
                );

                $result = $this->quotes->updateEntity($update_entity, $entity_id);

                /* inserting follow up data */
                $insertedFollowUpId = $this->quotes->craeteFollowUp($followUp);

                /* inserting notes */
                $this->quotes->createNotes($notes);

                /* insert into app_order_header */
                $this->quotes->copyInOrderHeader($entity_id);

                /* commiting dataabse transaction */
                 if ($this->db->trans_status() === FALSE) {
                     $this->db->trans_rollback();
                 } else {
                     $this->db->trans_commit();
                 }

                $url = $this->config->item('sendEmailQuotesRequest');
                /* send email trigger */
                if ( $send_mail == 1 ) {
                    $email_response = $this->sendEmail(
                        array(
                            'account_id' => $shipperId,
                            'entity_id' => $entity_id,
                            'vehicle_id' => $insertedVehicleId[0]
                        ),
                        $url
                    );
                }

                $response = array(
                    'apikey' => $apiKey,
                    'email' => $email_response,
                    'message' => 'Quote Request Sent Successfully!'
                );
                
                Logger::log(
                   $this->uri->uri_string,
                   $this->input->ip_address() ,
                   $response,
                   Logger::REQUEST_STATE_ENDED,
                   Logger::LOG_RESPONSE
                );

                Response::sendSuccessJSONResponse(Response::MSG200, $response);

            } catch (Exception $e) {
                /* log exception */
                print_r($e);
            }
        } else {
            Response::sendErrorJSONResponse(Response::MSG400);
        }
    }

    /**
     * functionality and to send email via curl request
     * 
     * @author Shahrukh
     * @version 1.0
     */
    private function sendEmail($params,$url){

        $postData = '';
        //create name value pairs seperated by &

        foreach($params as $k => $v) { 
            $postData .= $k . '='.$v.'&'; 
        }
        $postData = rtrim($postData, '&');
        
        $ch = curl_init();  
    
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
    
        $output=curl_exec($ch);
    
        curl_close($ch);
        return $output;
    }

    /**
     * This function / action login log when User logs in Successfully
     * 
     * @param Array $userData User information array
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 16112017
     */
    function loginReport($userData) {

        /* Adding Log for Logged In User */
        $log = array(
            'user_id' => $userData['id'],
            'ip'=> $_SERVER['REMOTE_ADDR']
        );
        $this->shippers->insert('api_login_logout_report', $log);
    }

    /**
     * This function / action login log when User logs in Successfully
     * 
     * @param String $apiKey The aPI key which is confidential in development teams
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 16112017
     */
    function logoutReport($apiKey) {

        $userId = $this->shippers->getUserId($apiKey);
        $this->shippers->update(
                'api_login_logout_report', 'user_id', $userId, array('logout_at' => date('Y-m-d H:i:s'))
        );
    }

    /**
     * This function updates last hit in the database on the basis of user id
     * 
     * @param string $apiKey The aPI key which is confidential in development teams
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 16112017
     */    
    function updateLastHit($apiKey) {

        $userId = $this->shippers->getUserId($apiKey);
        $this->shippers->update(
                'api_login_logout_report', 'user_id', $userId, array('last_hit_at' => date('Y-m-d H:i:s'))
        );
    }

    /**
     * This function fetches 
     * 
     * @author      Chetu
     * @output      JSON response
     * @lastUpdated 16112017
     */    
    function getLoginLogouReports() {

        $limit1 = $this->input->post('limit1');
        $limit2 = $this->input->post('limit2');
        
        if(!empty($limit1) || !empty($limit2)){            
            $result = $this->shippers->selectAllWithLimits('api_login_logout_report', $limit1, $limit2);
            Response::sendSuccessJSONResponse(Response::MSG200, $result);
        } else {
            Response::sendErrorJSONResponse(Response::MSG400);
        }
    }
    
    /**
     * API to get Orders DATA
     * 
     * @author shahrukh
     * @version 1.0
     */
    function getOrders(){
        
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        /* Logging Hit */
        Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);
        
        /* Listing Limits */
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $apiKey = $this->input->post('apikey');
        $status = $this->input->post('status');
        
        /* Vallidate mandatory parameters */
        $this->act_on_empty($to);
        $this->act_on_empty($apiKey);
        $this->act_on_empty($status);
        
        /* Validating Limits*/
        if( ( $from < 0 ) || ( $to > $this->config->item('paginated_page_max_list_item') ) || ( $from > $to ) ){
                /* Loggin Error*/
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_PAGINATION
                );
                /* Sending error response*/
                Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
                );            
                Response::sendErrorJSONResponse($response);
        }
        
       if ($this->shippers->checkLogin($apiKey)) {

            /* Update Last Hit for User */
            $this->updateLastHit($apiKey);
            
            /* validate order status */
            if( !in_array( $status, Orders::STATUS ) ) {
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::MSG400
                );
                Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
                );            
                Response::sendErrorJSONResponse($response);
            } else {
                /* Gathering order listing */            
                $orderList = $this->orders->getOrderList(
                    $this->shippers->getUserId($apiKey),
                    $status,
                    $from,
                    $to
                );
                
                $response = array(
                    'apikey' => $apiKey,
                    'orderList' => $orderList,
                    'all_records' => $this->orders->get_orders_count(
                                $this->shippers->getUserId($apiKey),
                                $status
                    )
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
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );            
            Response::sendErrorJSONResponse($response);
        }
        
    }
    
    /**
     * API index to return the Quotes listing on the basis of Status sent
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @return JSON API Response
     */
    function getQuotes(){
        $request = array();
        foreach($this->input->post() as $key => $value){
            $request[$key] = $value;
        }
        /* Logging Hit */
        Logger::log($this->uri->uri_string, $this->input->ip_address() , $request, Logger::REQUEST_STATE_START, Logger::LOG_REQUEST);

        /* Listing Limits */
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $apiKey = $this->input->post('apikey');
        $status = $this->input->post('status');
        
        /* Vallidate mandatory parameters */
        $this->act_on_empty($to);
        $this->act_on_empty($apiKey);
        $this->act_on_empty($status);
        
        /* Validating Limits*/
        if( ( $from < 0 ) || ( $to > $this->config->item('paginated_page_max_list_item') ) || ( $from > $to ) ){
                /* Loggin Error*/
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_PAGINATION
                );
                /* Sending error response*/
                Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
                );            
                Response::sendErrorJSONResponse($response);
        }
        
        if ($this->shippers->checkLogin($apiKey)) {

            /* Update Last Hit for User */
            $this->updateLastHit($apiKey);
            
            
            /* Gathering quotes listing */            
            $quotesList = $this->quotes->getQuotesList(
                $this->shippers->getUserId($apiKey),
                $status,
                $from,
                $to
            );

            $response = array(
                'apikey' => $apiKey,
                'quotesList' => $quotesList,
                'all_records' => $this->quotes->get_quotes_count(
                        $this->shippers->getUserId($apiKey),
                        $status
                )
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            Response::sendErrorJSONResponse($response);
        }
    }
    
    /**
     * This function / action to get the order details Data to the user 
     * 
     * @author      Chetu
     * @version 1.0
     * @return JSON API response
     */
    function orderDetail() {
        
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');
        
        /* Update Last Hit for User */
        $this->updateLastHit($apiKey);
        
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
        
        /* Checking empty API Key  and Entity Id*/
        if (empty($apiKey) || empty($entity_id)) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);
            $order_details = $this->orders->getOrderData($entity_id,$account_id);
            
            $response = array(
                'apikey' => $apiKey,
                'order_details' => $order_details
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
    }
    
    /**
     * Function / action to get the quotes details data to the user
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @return JSON API response
     */
    function quoteDetail(){
        
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');
        
        /* Update Last Hit for User */
        $this->updateLastHit($apiKey);
        
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
        
        /* Checking empty API Key  and Entity Id*/
        if (empty($apiKey) || empty($entity_id)) {
            
            /* Logging */
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::MSG400
            );
            
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);
            $quotes_details = $this->quotes->getQuoteData($entity_id,$account_id);
            
            $response = array(
                'apikey' => $apiKey,
                'quotes_details' => $quotes_details
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
    }
    
    /**
     * Function to check empty and non set variables and return corresponding JSON response.
     * 
     * @param ANY $variable
     * @return void
     */
    private function act_on_empty($variable){
        if( (!isset($variable)) || empty($variable) ){
            /* Loggin Error*/
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::MANDATORY_FIELDS_EMPTY
                );
                /* Sending error response*/
                Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
                );
                Response::sendErrorJSONResponse($response);
        }
    }
    
    /**
     * Function / Action to return track and trace data on the basis of account_id and entity_id
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    public function track_and_trace(){
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');
        
        /* Update Last Hit for User */
        $this->updateLastHit($apiKey);
        
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
        
        /* Checking empty API Key  and Entity Id*/
        if (empty($apiKey) || empty($entity_id)) {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string, $this->input->ip_address(), $response, Logger::REQUEST_STATE_ENDED, Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
        
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);            
            $track_data = $this->orders->get_track_trace($entity_id,$account_id);
            
            $response = array(
                'apikey' => $apiKey,
                'track_and_trace_data' => $track_data
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
    }

    /**
     * Function / Action to add notes under entity id
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    public function add_note() {
        
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');
        $internal_note = $this->input->post('internal_note');
        $priority_notes = $this->input->post('priority');
        
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
        
         /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($entity_id) || empty($internal_note) || empty($priority_notes) ) {
            
            /* Update Last Hit for User */
            $this->updateLastHit($apiKey);
        
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string, 
                    $this->input->ip_address(),
                    $response, 
                    Logger::REQUEST_STATE_ENDED, 
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);
            
            /**
             * Notes notes Model
             */
            $this->load->model('notes');            
                        
            $notes_response = $this->notes->add(
                    $entity_id,
                    $account_id,
                    Notes::TYPE['THREE'],
                    $internal_note, 
                    $priority_notes 
            );
            
            $response = array(
                'apikey' => $apiKey,
                'NOTES_RESPONSE' => $notes_response
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Functionality to get payment gateway information based on default settings
     * 
     * @author Shahrukh Charlie
     * @version 1.1
     */
    public function GetPaymentGateWay(){
        
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');

        /* Update Last Hit for User */
        $this->updateLastHit($apiKey);

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
        
         /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($entity_id)) {
            
            /* Update Last Hit for User */
            $this->updateLastHit($apiKey);
        
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                    $this->uri->uri_string, 
                    $this->input->ip_address(),
                    $response, 
                    Logger::REQUEST_STATE_ENDED, 
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
        
        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);
            
            /**
             * Notes notes Model
             */
            $this->load->model('payments');            
                        
            $GatewayInformation = $this->payments->GetGateWay(
                $entity_id,
                $account_id
            );
            
            $response = array(
                'apikey' => $apiKey,
                'GateWayInformation' => $GatewayInformation
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
           );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {

            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Functionality to update payment related query in database after processing payments
     * 
     * @author Shahrukh Charlie
     * @version 1.0
     */
    public function ProcessPaymentUpdates(){

        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');

        /* Update Last Hit for User */
        $this->updateLastHit($apiKey);

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

        /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($entity_id)) {
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $account_id = $this->shippers->getUserId($apiKey);
            
            $date_received = $this->input->post('date_received');
            $method = $this->input->post('method');
            $from_to = $this->input->post('from_to');
            $transaction_id = $this->input->post('transaction_id');
            $amount = $this->input->post('amount');
            $cc_number = $this->input->post('cc_number');
            $cc_type = $this->input->post('cc_type');
            $cc_exp_year = $this->input->post('cc_exp_year');
            $cc_exp_month = $this->input->post('cc_exp_month');
            $cc_auth = $this->input->post('cc_auth');
            $payment_type = $this->input->post('payment_type');
            
            $params = array(
                'entity_id' => $entity_id,
                'date_received' => $date_received,
                'method' => $method,
                'transaction_id' => $transaction_id,
                'entered_by' => $account_id,
                'amount' => $amount,
                'cc_number' => $cc_number,
                'cc_type' => $cc_type,
                'cc_exp_year' => $cc_exp_year,
                'cc_exp_month' => $cc_exp_month,
                'cc_auth' => $cc_auth,
                'payment_type' => $payment_type
            );
            
            $payment_response = $this->payments->RecordProcessPayment( $params );

            $response = array(
                'apikey' => $apiKey,
                'payment_processing_response' => $payment_response
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );
           Response::sendSuccessJSONResponse(Response::MSG200, $response);
            
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Order count API for showing order counts on customrt portal
     * 
     * @author shahrukh charlie
     * @version 1.0
     */
    public function EntityCounts(){

        $apiKey = $this->input->post('apikey');
        $entity_type = $this->input->post('entity_type');

        /* Update Last Hit for User */
        //$this->updateLastHit($apiKey);

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

        /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($entity_type)) {

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            $AccountID = $this->shippers->getUserId($apiKey);

            $Params =array(
                "type" => $entity_type,
                "account_id" => $AccountID
            );

            $Counts = $this->shippers->FetchEntityCounts($Params);

            $response = array(
                'apikey' => $apiKey,
                'Counts' => $Counts
            );

            Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address() ,
                $response,
                Logger::REQUEST_STATE_ENDED,
                Logger::LOG_RESPONSE
             );
             
            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API end point to get quote count
     * 
     * @author charlie
     * @version 1.0
     */
    public function quotesCount(){

        $apiKey = $this->input->post('apikey');
        $entity_type = $this->input->post('entity_type');

        /* Update Last Hit for User */
        //$this->updateLastHit($apiKey);

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

        /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($entity_type)) {

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            $AccountID = $this->shippers->getUserId($apiKey);

            $Params =array(
                "type" => $entity_type,
                "account_id" => $AccountID
            );

            $Counts = $this->shippers->quotesCount($Params);

            $response = array(
                'apikey' => $apiKey,
                'Counts' => $Counts
            );

            Logger::log(
                $this->uri->uri_string,
                $this->input->ip_address() ,
                $response,
                Logger::REQUEST_STATE_ENDED,
                Logger::LOG_RESPONSE
             );
             
            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );
            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API to get owe amount based on account id
     * 
     * @author shahrukh
     * @version 1.0
     * @return JSON response
     */
    public function CustomerOweAmount(){
        $apiKey = $this->input->post('apikey');
        $EntityID = $this->input->post('entity_id');

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

        /* Checking empty parameters for mandatory values*/
        if (empty($apiKey) || empty($EntityID)) {

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);
            $Response = $this->orders->CustomerOweAmount( $AccountID, $EntityID );
            
            $response = array(
                'apikey' => $apiKey,
                'Response' => $Response
            );
            
            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {

            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Functionality to test development
     * 
     * @author  Shahrukh
     * @version 1.0
     * @usage   Development
     */
    public function InitiatePaymentProcessing(){
                
        $apiKey = $this->input->post('apikey');
        $EntityID = $this->input->post('EntityID');

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

        /* cheking empty entity id */
        if (empty($apiKey) || empty($EntityID)){
            
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            // validating api parameters
            $gt_pt_type = $this->input->post("gw_pt_type");
            if(empty($gt_pt_type)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_GATEWAY
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $tariff_pay = (float)$this->input->post("tariff_pay");
            $deposit_pay = (float)$this->input->post("deposit_pay");
            $other_amount = (float)$this->input->post("other_amount");

            if( $gt_pt_type === "balance" ){
                if(empty($tariff_pay)){
                    $response = array(
                        "status"=> Response::STATUS_FAILURE,
                        "error"=> Response::INVALID_TARIFF
                    );
                    Response::sendErrorJSONResponse($response);
                }
            } else if ( $gt_pt_type == "deposit" ) {
                if(empty($deposit_pay)){
                    $response = array(
                        "status"=> Response::STATUS_FAILURE,
                        "error"=> Response::INVALID_DEPOSIT
                    ); 
                    Response::sendErrorJSONResponse($response);
                }
            } else {
                if(empty($other_amount)){
                    $response = array(
                        "status"=> Response::STATUS_FAILURE,
                        "error"=> Response::INVALID_AMOUNT
                    ); 
                    Response::sendErrorJSONResponse($response);
                }
            }
            

            $cc_fname = $this->input->post('cc_fname');
            if(empty($cc_fname)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC_FNAME
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $cc_lname = $this->input->post('cc_lname');
            if(empty($cc_lname)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC_LNAME
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $cc_cvv2 = $this->input->post('cc_cvv2');
            if(empty($cc_cvv2)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CVV
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $cc_number = $this->input->post('cc_number');
            if(empty($cc_number)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC
                ); 
                Response::sendErrorJSONResponse($response);
            }
            
            $cc_type = $this->input->post('cc_type');
            if(empty($cc_type)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $cc_month = $this->input->post('cc_month');
            if(empty($cc_month)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC_MONTH
                ); 
                Response::sendErrorJSONResponse($response);
            }

            $cc_year = $this->input->post('cc_year');
            if(empty($cc_year)){
                $response = array(
                    "status"=> Response::STATUS_FAILURE,
                    "error"=> Response::INVALID_CC_YEAR
                ); 
                Response::sendErrorJSONResponse($response);
            }

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);

            $params = array(
                "AccountID" => $AccountID,
                "gw_pt_type" => $gt_pt_type,
                "tariff_pay" => $tariff_pay,
                "deposit_pay" => $deposit_pay,
                "other_amount" => $other_amount,
                "cc_fname" => $cc_fname,
                "cc_lname" => $cc_lname,
                "cc_address" => $this->input->post('cc_address'),
                "cc_city" => $this->input->post('cc_city'),
                "cc_state" => $this->input->post('cc_state'),
                "cc_zip" => $this->input->post('cc_zip'),
                "cc_cvv2" => $cc_cvv2,
                "cc_number" => $cc_number,
                "cc_type" => $cc_type,
                "cc_month" => $cc_month,
                "cc_year" => $cc_year,
    
            );
    
            // processing payments
            $Response = $this->ProcessPayment($this->config->item('payment_processing_url')."?EntityID=".$EntityID,$params);
    
            $response = array(
                'apikey' => $apiKey,
                'Response' => json_decode($Response)
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * Functionality to hit the payment processing AJAX file in freightdragon application
     * 
     * @author Shahrukh
     * @version 2.0
     */
    private function ProcessPayment($url,$params) {

        $postData = '';
        //create name value pairs seperated by &

        foreach($params as $k => $v) { 
            $postData .= $k . '='.$v.'&'; 
        }
        $postData = rtrim($postData, '&');
        
        $ch = curl_init();  
    
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
    
        $output=curl_exec($ch);
    
        curl_close($ch);
        return $output;
        
    }

    /**
     * API  call to Agent Contact Information
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function AgentInformation(){
        $apiKey = $this->input->post('apikey');
        $EntityID = $this->input->post('EntityID');

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

        /* cheking empty entity id */
        if (empty($apiKey) || empty($EntityID)){
            
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);

            $Response = $this->shippers->GetAgentInformation($AccountID, $EntityID);

            $response = array(
                'apikey' => $apiKey,
                'Response' => $Response
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);
        } else {

            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API to upload Agent Documents
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function UploadDocuments(){
        $apiKey = $this->input->post('apikey');
        $EntityID = $this->input->post('EntityID');

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

        /* cheking empty entity id */
        if (empty($apiKey)){
            
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        /* Validating API Key */
        if ($this->shippers->checkLogin($apiKey)) {
            
            $config['upload_path']   = './uploads/'; 
            $config['allowed_types'] = 'gif|jpg|png'; 
            $config['max_size']      = 1024; 
            $config['max_width']     = 1024; 
            $config['max_height']    = 768;  
            

            $localFile = $_FILES['document']['tmp_name']; 

            $fp = fopen($localFile, 'r');

            // Connecting to website.
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_USERPWD, "email@email.org:password");
            curl_setopt($ch, CURLOPT_URL, 'ftp://@ftp.freightdragon.info/application/ajax/CPDocumentManager.php' . $_FILES['document']['name']);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 86400); // 1 Day Timeout
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, 'CURL_callback');
            curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFile));
            curl_exec ($ch);

            if (curl_errno($ch)) {

                $msg = curl_error($ch);
            }
            else {

                $msg = 'File uploaded successfully.';
            }

            curl_close ($ch);

            $return = array('msg' => $msg);

            echo json_encode($return);die;

            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('document')) {
                $error = array('error' => $this->upload->display_errors()); 
                echo json_encode($error);die;
            } else {
                $data = array('upload_data' => $this->upload->data()); 
                echo json_encode($data);die;
            }

        } else {

            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API endpoint to get credit card details
     * 
     * @author shahrukh
     * @version 1.0
     */
    public function MyCardInformation(){

        $apiKey = $this->input->post('apikey');

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

        /* cheking empty entity id */
        if (empty($apiKey)){
            
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        if ($this->shippers->checkLogin($apiKey)) {

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);

            $Response = $this->shippers->GetAccountCreditCards($AccountID);

            $response = array(
                'apikey' => $apiKey,
                'Response' => $Response
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {

            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API endpoint to get search results
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function Search(){

        $apiKey = $this->input->post('apikey');
        $search = $this->input->post('search');

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

        /* checking empty entity id */
        if (empty($apiKey)){

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        /* checking empty search */
        if (empty($search)){

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::SEARCH_EMPTY
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        /* checking search string length */
        if(strlen($search) < 3){
            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::SEARCH_STRING
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }
        
        if ($this->shippers->checkLogin($apiKey)) {

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);

            $Response = $this->shippers->search($search,$AccountID);

            $response = array(
                'apikey' => $apiKey,
                'SearchResults' => $Response
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    /**
     * API endpoint to get Company Image
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function getLogo(){
        $apiKey = $this->input->post('apikey');

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

        /* checking empty entity id */
        if (empty($apiKey)){

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        if ($this->shippers->checkLogin($apiKey)) {

            /* obatning account id based on apikey */
            $AccountID = $this->shippers->getUserId($apiKey);

            $Response = $this->shippers->getLogo($AccountID);

            $response = array(
                'apikey' => $apiKey,
                'company_logo' => $Response
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);
        } else {
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }

    // api end to get vehiles based on entiy id
    public function get_vehicles(){
        $apiKey = $this->input->post('apikey');
        $entity_id = $this->input->post('entity_id');

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

        /* checking empty entity id */
        if (empty($apiKey)){

            /* Logging */
            $response = array(
                "status" => Response::STATUS_FAILURE,
                "error" => Response::MSG400
            );

            Logger::log(
                $this->uri->uri_string, 
                $this->input->ip_address(),
                $response, 
                Logger::REQUEST_STATE_ENDED, 
                Logger::LOG_RESPONSE
            );

            Response::sendErrorJSONResponse($response);
        }

        if ($this->shippers->checkLogin($apiKey)) {
            // getting vehicles data
            $vehicles = $this->orders->get_vehicles($entity_id);

            // sending success response
            $response = array(
                'apikey' => $apiKey,
                'vehciles' => $vehicles
            );

            Logger::log(
               $this->uri->uri_string,
               $this->input->ip_address() ,
               $response,
               Logger::REQUEST_STATE_ENDED,
               Logger::LOG_RESPONSE
            );

            Response::sendSuccessJSONResponse(Response::MSG200, $response);

        } else {
            
            $response = array(
                "status"=> Response::STATUS_FAILURE,
                "error"=> Response::TOKEN_ERROR
            );

            Logger::log(
                    $this->uri->uri_string,
                    $this->input->ip_address() ,
                    $response,
                    Logger::REQUEST_STATE_ENDED,
                    Logger::LOG_RESPONSE
            );
            
            Response::sendErrorJSONResponse($response);
        }
    }
}
