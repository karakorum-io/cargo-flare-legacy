<?php
print "<pre>";

//$getdata = file_get_contents('https://freightdragondb.com/sms/ctest.php');
$getdata = file_get_contents('https://freightdragondb.com/sms/ctest.php');
//print $getdata = file_get_contents('https://freightdragondb.com/application/ajax/sms_response.php?action=getSMSResponse&phone=3524326018&entity_id=37437&app_sms=hi');

//echo $getdata."<br>";
$response = json_decode($getdata);

print_r($response->objects);

print $response->error."--".$response->api_id;
?>