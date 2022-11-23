<?php
$username = json_encode($this->data[0]["username"]);
//$username = $this->data[0]["username"];
$items 	  = $this->data[0]["items"];
$sms 	  = json_encode($this->data[0]["sms"]);
$smsData 	  = json_encode($this->data[0]["smsData"]);
$chat 	  = json_encode($this->data[0]["chat"]);
$chatData 	  = json_encode($this->data[0]["chatData"]);

echo $multi_dimensional_array = '{"chat": '.$chat.',"chatData": '.$chatData.',"sms": '.$sms.',"smsData": '.$smsData.',"username": '.$username.',"items": ['.$items.']}';
?>