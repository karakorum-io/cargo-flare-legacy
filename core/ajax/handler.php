<?php
    /**
     * The ajax handler library for handling web application functionality
     * that does not need database operations
     * 
     * @author Chetu Inc.
     * @version 1.2
     */

    /**
     * Importing required libraries 
     */
    require_once '../libs/mailer.php';
    require_once '../libs/autoQuotes.php';

    /**
     * Obtaining actions for relevant functionality redirection
     */
    $action = sanitzer('action');

    /**
     * Response array
     */
    $out = array('success'=>'false');

    switch ($action) {
        case 'sendContractUpdateEmail':            
            $mailer = new WebMailer();
            $response = $mailer->sendEmail(sanitzer('receiver'),$_POST['mailBody'],$_POST['emailSubject']);
            if($response == true){
                $out = array('success'=>'true','response'=>'mail sent');
            } else {
                $out = array('success'=>'true','response'=>'mail sending failed');
            }            
            break;
            
        case 'requestAutoQuotes':
            $auotQuotes = new AutoQuotes();
            $curlStatus = $auotQuotes->checkCURLStatus();
            if($curlStatus){
                
                /**
                 * prepare parameters array for sending to Auto quote API
                 */
                $response = $auotQuotes->getQuotes($_POST['requested']);
                
                $out= array(
                    'success'=>'true',
                    'response'=>$response
                );
                
            } else {
                
                /**
                 * when curl extension is not enabled
                 */
                $out= array(
                    'success'=>'false',
                    'response'=>array(
                        'message'=>'Curl not enabled'
                    )
                );
            }
            break;
            
        case 'requestAutoQuotesIndividual':
            $auotQuotes = new AutoQuotes();
            $curlStatus = $auotQuotes->checkCURLStatus();
            if($curlStatus){
                
                /**
                 * prepare parameters array for sending to Auto quote API
                 */
                $response = $auotQuotes->getQuotesIndividual($_POST['requested']);
                //print_r($response);die;
                $out= array(
                    'success'=>'true',
                    'response'=>$response
                );
                
            } else {
                
                /**
                 * when curl extension is not enabled
                 */
                $out= array(
                    'success'=>'false',
                    'response'=>array(
                        'message'=>'Curl not enabled'
                    )
                );
            }
            break;
            
        default:
            $out = array('success'=>'true','response'=>'no valid action');
            break;
            
    }
    
    /**
     * Sending back the JSON response
     */
    echo json_encode($out);
    die;
   
    /**
     * function to sanitize the super global post varibles
     * 
     * @author Chetu Inc.
     * @version 1.0
     */
    function sanitzer($key){
        return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING);
    }