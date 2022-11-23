<?php

/**
 * Loading libraries and dependencies
 */
require_once('../../libs/autoQuotingSDK/nusoap.php');

/**
 * Class to send external sources curl request
 * 
 * @author Chetu Inc.
 * @version 1.0
 */
class AutoQuotes {
   
    const AUTO_QUOTE_API_URL = "https://www.transportautoquoter.com/ws/taq_quote.php?wsdl";
    const PRICE = 1;
    const QUOTE_DETAILS = 2;
    
    /**
     * default controller to load dependencies at class call
     * 
     * @return void
     * @author Chetu Inc.
     * @version 1.0
     */
    function __construct(){        
        
    }
    
    /**
     * Function to check the CURL extension status on the server
     * 
     * @return boolean
     * @author Chetu Inc.
     * @version 1.0
     */
    function checkCURLStatus(){
        /**
         * check curl extension enable
         */
        if(is_callable('curl_init')){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Function to get Quotes from external API
     * 
     * @param Array $params array for the parameters required by API
     * @return Array
     */
    function getQuotes($params) {
                
        $order_deposit = $params['Additional']['order_deposit'];
        $order_deposit_type = $params['Additional']['order_deposit_type'];
        
        $quote = array();
        
        $quote['Api_key'] = $params['Additional']['auto_quote_api_key'];
        $quote['Api_pin'] = $params['Additional']['auto_quote_api_pin'];
        
        $quote['Transport']['Carrier'] = $params['Transport']['Carrier'];    
        $quote['Transport']['Origin'] = $params['Transport']['Origin'];
        $quote['Transport']['Destination'] = $params['Transport']['Destination'];        
                
        $numberOfVehicles = count($params['Transport']['Vehicles']['Year']);
                
        for( $i=0; $i<$numberOfVehicles; $i++ ){
            $quote['Transport']['Vehicles'][$i]['v_year'] = $params['Transport']['Vehicles']['Year'][$i];
            $quote['Transport']['Vehicles'][$i]['v_make'] = $params['Transport']['Vehicles']['Make'][$i];
            $quote['Transport']['Vehicles'][$i]['v_model'] = $params['Transport']['Vehicles']['Model'][$i];
            $quote['Transport']['Vehicles'][$i]['veh_op'] = 1;
        }
 
        $quote['Transport']['only_price'] = self::PRICE;
                
        $client = new nusoap_client(self::AUTO_QUOTE_API_URL);
        $result = $client->call('GetQuote', $quote);
        
        $apiPrice = $result['Results']['price'];
        
        if($order_deposit_type == 'amount'){
            
            $order_deposit *= $numberOfVehicles;
            $tariff = $order_deposit+$apiPrice;
            $deposit = $order_deposit;
            
        } else {
            
            $order_deposit = ($apiPrice * $order_deposit)/100;
            
            $order_deposit *= $numberOfVehicles;
            
            $tariff = $apiPrice+$order_deposit;
            $deposit = $apiPrice;
        }
        
        return array(
            'Tariff' => $tariff,
            'Deposite' => $deposit
        );
    }
    
    /**
     * Function to get Quotes for individual vehicles
     * 
     * @param Array $params array required by API
     * @return Array
     */
    function getQuotesIndividual($params){
        
        $config = $params['Additional'];
        
        $order_deposit = $config['order_deposit'];
        $order_deposit_type = $config['order_deposit_type'];
        
        $quote = array();
        $quote['Api_key'] = $config['auto_quote_api_key'];
        $quote['Api_pin'] = $config['auto_quote_api_pin'];
        $quote['Transport']['Carrier'] = $params['Transport']['Carrier'];
        $quote['Transport']['Origin'] = $params['Transport']['Origin'];
        $quote['Transport']['Destination'] = $params['Transport']['Destination'];
        $quote['Transport']['Vehicles'] = $params['Transport']['Vehicles'];
        $quote['Transport']['only_price'] = self::PRICE;
        
        $client = new nusoap_client(self::AUTO_QUOTE_API_URL);
        $result = $client->call('GetQuote', $quote);
        
        $apiPrice = $result['Results']['price'];
        
        if($order_deposit_type == 'amount'){
            $tariff = $order_deposit+$apiPrice;
            $deposit = $order_deposit;
        } else {
            $order_deposit = ($apiPrice * $order_deposit)/100;
            $tariff = $apiPrice+$order_deposit;
            $deposit = $apiPrice;
        }
        
        return array(
            'Tariff' => $tariff,
            'Deposite' => $deposit,
            'API_returned'=>$apiPrice
        );
    }
    
    /**
     * Function to getQuotes for Imported Leads
     * 
     * @param $params
     * @return Array
     */
    function getAutoQuotesImportedLeads($params){
        $numberOfAmount = count($params);
        $response;
        
        for($i=0;$i<$numberOfAmount;$i++){
            /**
             * Preparing API required Array
             */
            $order_deposit = $params[$i]['Additional']['order_deposit'];
            $order_deposit_type = $params[$i]['Additional']['order_deposit_type'];
            
            $quote = array();
            $quote['Api_key'] = $params[$i]['Additional']['auto_quote_api_key'];
            $quote['Api_pin'] = $params[$i]['Additional']['auto_quote_api_pin'];
            
            $quote['Transport']['Carrier'] = $params[$i]['Transport']['Carrier'];
            $quote['Transport']['Origin'] = $params[$i]['Transport']['Origin'];
            $quote['Transport']['Destination'] = $params[$i]['Transport']['Destination'];
            
            $response[$i]['enitity_id'] = $params[$i]['Additional']['entity_id'];
            
            for($k=0;$k<count($params[$i]['Transport']['Vehicles']);$k++){
                //echo "k".$k."<br>";
                $quote['Transport']['Vehicles'] = $params[$i]['Transport']['Vehicles'];
                $quote['Transport']['only_price'] = self::PRICE;                
                $client = new nusoap_client(self::AUTO_QUOTE_API_URL);
                $result = $client->call('GetQuote', $quote);
                                
                $apiPrice = $result['Results']['price'];
                
                if($order_deposit_type == 'amount'){            
                    $order_deposit *= count($params[$i]['Transport']['Vehicles']);
                    $tariff = $order_deposit+$apiPrice;
                    $deposit = $order_deposit;
                } else {
                    $order_deposit = ($apiPrice * $order_deposit)/100;
                    $order_deposit *= count($params[$i]['Transport']['Vehicles']);
                    $tariff = $apiPrice+$order_deposit;
                    $deposit = $apiPrice;
                }
                
                $response[$i][$k] = array(
                    'vehicle_id' => $params[$i]['Transport']['Vehicles'][$k]['v_id'],
                    'tariff'=>$tariff,
                    'deposit'=>$deposit,
                    'carrirerPay' => $apiPrice
                );
            }
        }
        
        
        return $response;
        
    }
}
