<?php

echo "Address API Test";
  
// From URL to get webpage contents.
$url = "https://us-street.api.smartystreets.com/street-address?auth-id=0f8f8174-af40-c373-fb17-363949e84b1e&
auth-token=qp375l7X3YkTSxrCd2Cd&
street=1600+amphitheatre+pkwy&
city=mountain+view&
state=CA&
candidates=10&
license=us-rooftop-geocoding-cloud";
  
// Initialize a CURL session.
$ch = curl_init(); 
  
// Return Page contents.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
//grab URL and pass it to the variable.
curl_setopt($ch, CURLOPT_URL, $url);
  
$result = curl_exec($ch);
  
echo $result;