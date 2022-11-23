<?php

/**
 * Member Messages
 *
 * This is the Model for all the operations related to the Messages.
 *
 * @category	Controller
 * @author      Chetu
 * @link	https://www.chetu.com/
 * @reference   http://www.restapitutorial.com/httpstatuscodes.html
 */

class Response extends CI_Model {
    
    const MSG200 = "OK";
    const MSG201 = "CREATED";
    const MSG202 = "ACCEPTED";
    const MSG204 = "NO CONTENT";
    const MSG304 = "NOT MODIFIED";
    const MSG400 = "BAD REQUEST";
    const MSG401 = "UN-AUTHORIZED";
    const MSG403 = "FORBIDDEN";
    const MSG404 = "NOT FOUND";
    const MSG409 = "CONFLICT";
    const MSG500 = "INTERNAL SERVER ERROR";
    
    const LOGIN = "USER LOGGED IN SUCCESSFULY";
    const LOGOUT = "USER LOGGED OUT SUCCESSFULY";
    const QUOTE_REQUEST = "QUOTE REQUEST SENT SUCCESSFULLY";
    const LOGIN_LOGOUT_REPORT = "LOGIN LOGOUT REPORT FETCHED SUCCESSFULLY";
    const TOKEN_ERROR = "INVALID TOKEN KEY";
    const ACCESS_KEY = "INVALID ACCESS KEY";
    const NON_INTEGER_VALUE = "NON INTEGER PASSED IN INTEGER TYPE";
    const INVALID_PAGINATION = "INVALID PAGINATION PARAMETERS PASSED";
    const MANDATORY_FIELDS_EMPTY = "MANDATORY FIELDS ARE MISSING";

    const INVALID_GATEWAY = "INVALID OR NO PAYMENT GATEWAY SELECTED";
    const INVALID_TARIFF = "INVALID OR NO TARIFF";
    const INVALID_DEPOSIT  = "INVALID OR NO DEPOSIT";
    const INVALID_AMOUNT  = "INVALID OR NO AMOUNT";
    const INVALID_CC_FNAME = "INVALID CC FIRST NAME";
    const INVALID_CC_LNAME = "INVALID CC LAST NAME";
    const INVALID_CVV = "INVALID OR NO CVV";
    const INVALID_CC = "INVALID OR NO CREDIT CARD NUMBER";
    const INVALID_CC_MONTH = "INVALID OR NO CREDIT CARD EXPIRY MONTH";
    const INVALID_CC_YEAR = "INVALID OR NO CREDIT CARD EXPIRY YEAR";

    const SEARCH_STRING = "STRING CANNOT BE LESS THAN 3 CHARACTERS";
    const SEARCH_EMPTY = "SEARCH CANNOT BE EMPTY";
    
    const STATUS_SUCCESS = 'Success';
    const STATUS_FAILURE = 'Failure';
    
    /*
     * This function send response to in json format if there is error
     * 
     * @author          Chetu
     * @lastUpdateDate  1212017
     * @params          $error error code and message
     * @return          JSON response
     */
    public static function sendErrorJSONResponse($response){        
        header("HTTP/1.1 200 OK");
        echo json_encode(
                $response
        );
        exit;
    }
    
    /*
     * This function send response to in json format if there is error
     * 
     * @author          Chetu
     * @lastUpdateDate  1212017
     * @params          $message message code and message
     * @params          $response response data
     * @return          JSON response
     */
    public static function sendSuccessJSONResponse($message,$response = NULL){
        header("HTTP/1.1 200 OK");
        
        if($response == NULL){
            $responseData = array(
                'status' => 'SUCCESS',
                'message' => $message
            );
        } else {
            $responseData = array(
                'status' => 'SUCCESS',
                'message' => $message,
                'response'=> $response
            );
        }
        echo json_encode($responseData);
        exit;
    }
    
}