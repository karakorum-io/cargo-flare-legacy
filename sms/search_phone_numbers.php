<?php
# Plivo AUTH ID
	$AUTH_ID = 'MAZMU2NTAXNDKWM2Q1M2';
	# Plivo AUTH TOKEN
	$AUTH_TOKEN = 'NzFiOTM3MzM5YzQ5NDQ3MjEyNTk5Njc0N2NjOGJm';
	
	/*
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
	*/	


//=============================================
//print "----------0";
/*
               $url = "https://api.plivo.com/v1/Account/".$AUTH_ID."/PhoneNumber/19547156943/"; 
            
               $ch=curl_init($url);
               curl_setopt($ch, CURLOPT_POST, true);
               //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
               curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
               curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                      
                 $response = curl_exec( $ch );
				 $purchase_response = json_decode($response);
				 
				 
            if(isset($purchase_response->error) ){
                die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
            }
			else
			{
				print "------------";
			    print_r($purchase_response);	
			}
             
            curl_close($ch); 
			
*/

               $url = "https://api.plivo.com/v1/Account/".$AUTH_ID."/Number/1111111111/";  // 19547156943 
            
               $ch=curl_init($url);
               //curl_setopt($ch, CURLOPT_POST, true);
               //curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
               curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
               curl_setopt($ch, CURLOPT_USERPWD, $AUTH_ID . ":" . $AUTH_TOKEN);
               curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
               curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			   
                 $response = curl_exec( $ch );
				 $purchase_response = json_decode($response);
				 
				 
            if(isset($purchase_response->error) ){
                print $purchase_response->error;
            }
			else
			{
				print "------------";
			    print_r($purchase_response);	
			}
             
            curl_close($ch); 
        
?>