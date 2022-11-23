<?php
# Plivo AUTH ID
$AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
# Plivo AUTH TOKEN
$AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';

//print "<pre>";
$url = 'https://api.plivo.com/v1/Account/'.$AUTH_ID.'/PhoneNumber/?country_iso=US&type=local&pattern=210&region=Texas';
$crl = curl_init();
        $timeout = 5;
		
        curl_setopt($crl, CURLOPT_URL, $url);
		curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($crl, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
        //curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        //print_r(  $ret);
		//$response = json_encode($getdata);

//print_r($response->api_id);


/*
$url = 'https://api.plivo.com/v1/Account/'.$AUTH_ID.'/PhoneNumber/'; 
$data = array("country_iso" => "US",'type' => 'local','pattern' => '210','region' => 'Texas');
$data_string = json_encode($data);
$ch=curl_init($url);
//curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$response = curl_exec( $ch );
curl_close($ch);
*/
/*
# Plivo AUTH ID
$AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
# Plivo AUTH TOKEN
$AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
# SMS sender ID.
$src = '13309385668';
# SMS destination number
$dst = '918957851002';
# SMS text
$text = 'Hi, Message from Plivo';
$url = 'https://api.plivo.com/v1/Account/'.$AUTH_ID.'/Message/'; 
$data = array("src" => "$src", "dst" => "$dst", "text" => "$text");
$data_string = json_encode($data);
$ch=curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$response = curl_exec( $ch );



curl_close($ch);
*/

/*
print "<pre>---";
//$response = curl_getinfo( $ch );
print $response;

//$error = curl_error( $ch );

//print "====".$error;
//$response = json_encode($response);
print "<br>=================<br>";

print_r($response);
print "<br>=================<br>";
$r=explode("\r",$response);
print_r($r);
print "</pre>";
*/

/*
$url = "http://www.agilewin.com/StoneEagle/StartRateQuote.aspx?VIP=KMHCG45C94U565554&Odometer=30000";
$crl = curl_init();
        $timeout = 5;
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        print  $ret;
	     */
		/*	$fields = array(
           
        );
		 $url = "http://www.agilewin.com/StoneEagle/createContract.aspx";
		
		$ch = curl_init ($url);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
print $returndata = curl_exec ($ch);*/
		
		/*
		$fields_string = "";	
	   $url = "http://www.agilewin.com/StoneEagle/createContract.aspx";
		//url-ify the data for the POST
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');
		$fields_string = substr($fields_string,0,-1);
		
		
		//open connection
		$ch = curl_init();
		
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		$result = curl_exec($ch);
		curl_close($ch);	
		
		    print "<br>$fields_string<pre>";
			print_r($fields);
			print "</pre>";
			print "=============".$result;*/
/*

	$fields = array(
           'src' => '13309385668 ', // Sender's phone number with country code
        'dst' => '918957851002', // Receiver's phone number with country code
        'text' => 'Hi, Message from Plivo' // Your SMS text message
        );
		 $url = "https://api.plivo.com/v1/Account/MAZMU2NTAXNDKWM2Q1M2/Message/";
		
		$ch = curl_init ($url);
curl_setopt ($ch, CURLOPT_POST, true);
curl_setopt ($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
$returndata = curl_exec ($ch);
print "-----------";
print_r($returndata);
*/
/*
    require_once 'plivo.php';
    $auth_id = "MAZMU2NTAXNDKWM2Q1M2";
    $auth_token = "NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm";

    $p = new RestAPI($auth_id, $auth_token);

    // Set message parameters
    $params = array(
        'src' => '13309385668 ', // Sender's phone number with country code
        'dst' => '918957851002', // Receiver's phone number with country code
        'text' => 'Hi, Message from Plivo', // Your SMS text message
        // To send Unicode text
        //'text' => 'こんにちは、元気ですか？' # Your SMS Text Message - Japanese
        //'text' => 'Ce est texte généré aléatoirement' # Your SMS Text Message - French
        'url' => 'http://example.com/report/', // The URL to which with the status of the message is sent
        'method' => 'POST' // The method used to call the url
    );
    // Send message
    $response = $p->send_message($params);

    // Print the response
    echo "Response : ";
    print_r ($response['response']);

    // Print the Api ID
    echo "<br> Api ID : {$response['response']['api_id']} <br>";

    // Print the Message UUID
    echo "Message UUID : {$response['response']['message_uuid'][0]} <br>";
	*/

?>

