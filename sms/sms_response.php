<?php
session_start();
$memberId1 = (int)$_SESSION['member_id'];
//print $memberId1."--0";
//ob_start();
//if ($memberId1 > 0) 
{
	# Plivo AUTH ID
	$AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
	# Plivo AUTH TOKEN
	$AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
			
	
   if (isset($_GET['action'])) {
      switch ($_GET['action']) {
         case 'getSMSResponse':
		
			# SMS sender ID.
			$src = $_GET['fromPhone']; //'13309385668';
			
			# SMS destination number
			//$dst = '918957851002';  //$_GET['toPhone'];
			$dst = $_GET['toPhone']; //'19546681277';
			# SMS text
			$text = $_GET['rapp_sms'];
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
			
			 break;
			case 'getPhoneNumbers':
		
			        # SMS sender ID.
			       $country_iso = $_GET['country_iso']; //'13309385668';
			        $region = $_GET['state'];
					$pattern = $_GET['pattern'];
					//?country_iso=US&type=local&pattern=210&region=Texas            
			         $url = 'https://api.plivo.com/v1/Account/'.$AUTH_ID.'/PhoneNumber/?country_iso='.$country_iso.'&type=local&pattern='.$pattern.'&region='.$region;
					$crl = curl_init();
					
					curl_setopt($crl, CURLOPT_URL, $url);
					curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
					curl_setopt($crl, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
					//curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
					//curl_setopt($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
					$ret = curl_exec($crl);
					curl_close($crl);
			
			 break;
                default:
                    break;
            }
        }
		
}
?>