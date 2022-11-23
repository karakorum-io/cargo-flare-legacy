<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
 * ************************************************************************************************** */
 
@session_start();

/**
 * including dependencies
 */
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once('../libs/autoQuotingSDK/nusoap.php');
require_once ('../config.php');

ob_start();
ini_set('max_execution_time', 300);
error_reporting(E_ALL | E_NOTICE);
require_once("init.php");

$_SESSION['iamcron'] = true;

echo "<h3>CRON STARTED</h3>";
echo "Maximum Leads to be fetched: <b>".$CONF['MAX_AUTO_QUOTE']."</b><br><br><br>";

$sql = "SELECT `parentid`,`entityid`,`ship_via`,`vehicleid` FROM `app_order_header` where type = 1 AND quoted IS NULL ORDER BY `id` DESC limit ".$CONF['MAX_AUTO_QUOTE']."";

$result = $daffny->DB->query($sql);

$i = 0;

$entities = [];
/**
 * initializing classes
 */
$entity = new Entity($daffny->DB);
$destination = new Destination($daffny->DB);
$origin = new Origin($daffny->DB);
$shipper = new Shipper($daffny->DB);
$emailTemplate = new EmailTemplate($daffny->DB);
$client = new nusoap_client('https://www.transportautoquoter.com/ws/taq_quote.php?wsdl');

while ($row = mysqli_fetch_assoc($result)) {
           
    $sql = "SELECT `on_off_auto_quoting`,`order_deposit`,`order_deposit_type`,`auto_quote_api_pin`,`auto_quote_api_key` FROM `app_defaultsettings` WHERE `owner_id` = (" . $row['parentid'] . ")";
    $settingsData = $daffny->DB->query($sql);
    $settings = mysqli_fetch_assoc($settingsData);

    if( $settings['on_off_auto_quoting'] == 1 ){

        echo "***************************************<br>";
        echo "Iteration: ".$i."<br><br>";
        echo "1. Quoting entity id ".$row['entityid']."<br>";

        /**
         * Auto Quoting code here
         */
        $sql = "SELECT `companyname`,`username`,`email`,`phone` FROM `members` WHERE `id` = (" . $row['parentid'] . ")";
        $memberData = $daffny->DB->query($sql);
        $members = mysqli_fetch_assoc($memberData);
        
        $entity->load($row['entityid']);        
        $destination->load($entity->destination_id);        
        $origin->load($entity->origin_id);        
        $shipper->load($entity->shipper_id);
        
        $sql = "SELECT `id`,`year`,`make`,`model` FROM `app_vehicles` WHERE `entity_id` = (" . $row['entityid'] . ")";
        $resultVehicles = $daffny->DB->query($sql);

        echo "2. Fetched all the neccesary detail for quoting and<br> sending email <br>";

        $vehiclesData = [];
        $params = [];
        $j = 0;

        while ($vehicles = mysqli_fetch_assoc($resultVehicles)) {
            $vehiclesData[$j] = array(
                'v_id' => $vehicles['id'],
                'v_year' => $vehicles['year'],
                'v_make' => $vehicles['make'],
                'v_model' => $vehicles['model'],
                'veh_op' => 1
            );
            $j++;
        }

        if ($row['ship_via'] == 1) {
            $carrier = 'Open';
        } elseif ($row['ship_via'] == 2) {
            $carrier = 'Close';
        } else {
            $carrier = 'Drive Away';
        }

        $params[$i]['Transport'] = array(
            'Carrier' => $carrier,
            'Origin' => array(
                "City" => $origin->city,
                "State" => $origin->state,
                "Zipcode" => $origin->zip
            ),
            'Destination' => array(
                "City" => $destination->city,
                "State" => $destination->state,
                "Zipcode" => $destination->zip
            ),
            'Vehicles' => $vehiclesData
        );

        $params[$i]['Additional'] = array(
            "order_deposit" => $settings['order_deposit'],
            "order_deposit_type" => $settings['order_deposit_type'],
            "auto_quote_api_pin" => $settings['auto_quote_api_pin'],
            "auto_quote_api_key" => $settings['auto_quote_api_key'],
            "entity_id" => $row['entityid']
        );
        

        echo "3. Preparing array required by TAQ API for auto<br> quoting<br>";
        
        /**
         * Gathering settings
         */
        $order_deposit = $params[$i]['Additional']['order_deposit'];
        $order_deposit_type = $params[$i]['Additional']['order_deposit_type'];

        /**
         * Calling API connected class and 
         */
        $quote = [];        

        $quote['Api_key'] = $params[$i]['Additional']['auto_quote_api_key'];
        $quote['Api_pin'] = $params[$i]['Additional']['auto_quote_api_pin'];
        $quote['Transport']['Carrier'] = $params[$i]['Transport']['Carrier'];
        $quote['Transport']['Origin'] = $params[$i]['Transport']['Origin'];
        $quote['Transport']['Destination'] = $params[$i]['Transport']['Destination'];

        $netTariff=0;
        $netCarrierPay=0;
        $netDeposit=0;

        for ($k = 0; $k < count($params[$i]['Transport']['Vehicles']); $k++) {
            
            $quote['Transport']['Vehicles'] = $params[$i]['Transport']['Vehicles'];
            $quote['Transport']['only_price'] = 1;            
            
            $resp = getQuotation( $client, $quote );
            $apiPrice = $resp['Results']['price'];

            echo "4. ".$i." Api returned amoount for entity id ".$row['entityid']." and<br> Vehicle number".$k." is : ".$apiPrice."<br>";
            
            $tariff = "";
            $deposit ="";
            $carrier_pay = $apiPrice;

            if ($order_deposit_type == 'amount') {
                $order_deposit *= count($params[$i]['Transport']['Vehicles']);
                
                $tariff = $order_deposit + $apiPrice;                
                $deposit = $order_deposit;

                $netTariff += $tariff;
                $netCarrierPay += $apiPrice;
                $netDeposit += $deposit;
            } else {
                $order_deposit = ($apiPrice * $order_deposit) / 100;
                $order_deposit *= count($params[$i]['Transport']['Vehicles']);
                
                $tariff = $apiPrice + $order_deposit;
                $deposit = $apiPrice;

                $netTariff += $tariff;
                $netCarrierPay += $apiPrice;
                $netDeposit += $deposit;
            }

            $sql = "UPDATE `app_vehicles` SET "
            . "`tariff` = '" . $tariff . "',"
            . " `carrier_pay` = '" . $carrier_pay . "',"
            . "`deposit` = '" . $deposit . "' WHERE `id` = '" . $params[$i]['Transport']['Vehicles'][$k]['v_id'] . "' " . "";            
            $resultVehicles = $daffny->DB->query($sql);
            if($resultVehicles){
                echo "5. Vehicle price updated in the database<br>";
            } else {
                echo "5. Unable to update vehicle price in database<br>";
            }
        }

        /**
         * Change Entity status from app_order_header and app_entities
         * add total deposit and total tariff
         */
        $sql = "UPDATE `app_order_header` SET "
            . "carrier_pay_stored='".$netCarrierPay."',"
            . "total_tariff_stored='".$netTariff."',"
            . "status=21,"
            . "`quoted` = '" . date('Y/m/d h:i:s') . "' WHERE entityid = '" . $row['entityid'] . "' ";
        $orderHeaderResponse = $daffny->DB->query($sql);

        $sql = "UPDATE `app_entities` SET `status` =21, 
            `quoted` = '" . date('Y/m/d h:i:s') . "' WHERE id = '" . $row['entityid'] . "'";
        $entitesResponse = $daffny->DB->query($sql);

        if(($entitesResponse == 1) && ($orderHeaderResponse == 1)){
            echo "6. Entity status update to 21";
        } else {
            echo "6. Unable to update leads Status to 21 after quotation and updating app_vehicles";
        }

        /**
         * Email dependent variables
         */
        $tpl = new template();
        $emailTemplate->setTemplateBuilder($tpl);
        $emailTemplate->loadTemplate(EmailTemplate::SYS_INIT_QUOTE, $entity->getAssigned()->parent_id, $entity, array(), true);
        
        if ($entity->getAssigned()->parent_id == 1){
            $fromName = $emailTemplate->getFromName();
            $from = $emailTemplate->getFromAddress();
        } else {
            $fromName = $emailTemplate->getFromName();
            $from = $entity->getAssigned()->getDefaultSettings()->smtp_from_email;            
        }       
        
        
        $to = $emailTemplate->getToAddress();
        $from = "admin@americancartransporters.com";        
        $subject = $emailTemplate->getSubject();
        $body = $emailTemplate->getBody();

        $response = sendInitQuoteEmail( $CONF['MAIL_HOST'], $CONF['MAIL_PORT'], $to, $from, $fromName, $subject, $body);    
        echo "Mail Sent to Shipper<br>";        

        echo "8. Quoting for entity id ".$row['entityid']." id over <br>";
        echo "***************************************<br><br>";
    } else {
        echo "***************************************<br>";
        echo "Iteration: ".$i."<br><br>";
        echo "1. TAQ for entity id ".$row['entityid']." is turned off!<br>";
        echo "***************************************<br><br>";
    }
    $i++;
}

die("CRON ENDED");

$_SESSION['iamcron'] = false;
require_once("done.php");

/**
 * Function to send email
 * 
 * @param $host Email host
 * @param $port Email port
 * @param $auth Email auth
 * @param $to Reciever email address
 * @param $from Sender email address
 * @param $fromName Sender Name
 * @param $subject Email subject
 * @param $body Email body
 * @author Chetu Inc.
 */
function sendInitQuoteEmail($host,$port,$to,$from,$fromname,$subject,$body){
    /**
     * Sending Email to shipper
     */
    $mail = new PHPMailer;            
    $mail->IsSMTP();
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->SMTPAuth = false;    
    $mail->SetFrom("$from ", "".$fromname);
    $mail->AddAddress($to);
    $mail->IsHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    try{
        $mail->Send();
        echo "<br>";
        return TRUE;
    } catch (Exception $e){
        echo "<pre>";
        print_r($e);
        echo "</pre>";
        return FALSE;
    }    
}

function getQuotation($client,$quote){    
    $result = $client->call('GetQuote', $quote);
    return $result;
}