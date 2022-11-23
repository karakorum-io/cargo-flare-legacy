<?php

/**
 * Logger Model
 *
 * This is the Model for all the operations related to the Logging.
 *
 * @category	Controller
 * @author      Chetu
 * @link	https://www.chetu.com/
 * @reference   http://www.restapitutorial.com/httpstatuscodes.html
 */

class Logger extends CI_Model {
    
    const SYSTEM_LOG_FILE = 'application/logs/SYSTEM.LOG';    
    
    const API_HIT_START = ':: TERMINAL API :: CALL START :: ';
    const API_HIT_INPROGRESS = ':: TERMINAL API :: CALL IN_PROGRESS :: ';
    const API_HIT_ENDED = ':: TERMINAL API :: CALL ENDED :: ';
    
    const LOG_SECTION_HEADER_LINE = '::::::::::::::::::::::::::::::::::::::::::::::LOG ENTRY STARTS:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::';
    const LOG_SECTION_FOOTER_LINE = '::::::::::::::::::::::::::::::::::::::::::::::LOG ENTRY ENDS::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::';
    
    const REQUEST_STATE_START = 1;
    const REQUEST_STATE_ONGOING = 2;
    const REQUEST_STATE_ENDED = 3;
    
    const LOG_REQUEST = 1;
    const LOG_RESPONSE = 2;
    const LOG_DATA =3;
    /*
     * This error log data in the file
     * 
     * @author          Chetu
     * @lastUpdateDate  1122017
     */
    public static function log($api, $ip, $data, $state, $flow){        
        $footing="";
        $log="";
        
        if($state == 1){
            $heading = Logger::API_HIT_START;
            $log = Logger::LOG_SECTION_HEADER_LINE.PHP_EOL;
            $footing = "";
        } elseif ( $state == 2 ){
            $heading = Logger::API_HIT_INPROGRESS;
            $footing = Logger::LOG_SECTION_FOOTER_LINE.PHP_EOL.PHP_EOL;
        } else {
            $heading = Logger::API_HIT_ENDED;
            $footing = Logger::LOG_SECTION_FOOTER_LINE.PHP_EOL.PHP_EOL;
        }
        
        $log .= '# '.date('Y-m-d H:i:s');
        $log .= $heading.$api.PHP_EOL;        
        if($ip != NULL){
            $log .="IP :: ".$ip.PHP_EOL;
        }
                
        if($flow == 1){
            $log .= "REQUEST ARRAY :: ";
        } elseif ($flow == 2) {
            $log .= "RESPONSE ARRAY :: ";
        } else {
            $log .= "DATA LOG ARRAY :: ";
        }
       
        file_put_contents(Logger::SYSTEM_LOG_FILE, $log.PHP_EOL, FILE_APPEND | LOCK_EX );
        file_put_contents(Logger::SYSTEM_LOG_FILE, print_r($data,true), FILE_APPEND | LOCK_EX );
        file_put_contents(Logger::SYSTEM_LOG_FILE, $footing.PHP_EOL, FILE_APPEND | LOCK_EX );
    }
    
}