<?php

set_time_limit(0);
@session_start();
error_reporting(E_ALL|E_NOTICE);
require_once("init.php");

ob_start();
$_SESSION['iamcron'] = true;

// code to send email here

$sql = "SELECT first_name, last_name, company_name FROM `app_accounts` WHERE id = ".$_POST['account_id'];
$result = $daffny->DB->query($sql);
$accountData = mysqli_fetch_assoc($result);

$sql = "SELECT account_id,Origincity,Originstate,Originzip,Destinationcity,Destinationstate,Destinationzip FROM `app_order_header` WHERE entityid = ".$_POST['entity_id'];
$result = $daffny->DB->query($sql);
$Route = mysqli_fetch_assoc($result);

$sql = "SELECT email FROM app_accounts WHERE id = ".$Route['account_id'];
$result = $daffny->DB->query($sql);
$email = mysqli_fetch_assoc($result);

$sql = "SELECT * FROM `app_vehicles` WHERE entity_id = ".$_POST['entity_id'];
$result = $daffny->DB->query($sql);

$Vehicles;
while($row = mysqli_fetch_assoc($result)){
    $Vehicles[] = $row;
}

$html = "New Leads Information<br> <table border='1'>";
$html .= "<tr> <th>ID</th> <th>CUSTOMER</th> <th>COMPANY</th> <th>ORIGIN</th> <th>DESTINATION</th> </tr>";
$html .= "<tr> <td>".$_POST['account_id']."</td> <td>".$accountData['first_name']."".$accountData['last_name']."</td> <td>".$accountData['company_name']."</td>";
$html .= "<td>".$Route['Origincity'].",".$Route['Originstate'].",".$Route['Originzip']."</td><td>".$Route['Destinationcity'].",".$Route['Destinationstate'].",".$Route['Destinationzip']."</td> </tr>";
$html .= "</table>";

$html .= "<br>Vehicle Information<br> <table border='1'>";

for( $i=0; $i<count($Vehicles); $i++ ){
    $html .= "<tr> <td>".$Vehicles[$i]['year']."</td> <td>".$Vehicles[$i]['make']."</td> <td>".$Vehicles[$i]['model']."</td> <td>".$Vehicles[$i]['type']."</td> </tr>";
}

$html .= "</table>";

//sending email
$mail = new FdMailer(true);
$mail->isHTML();
$mail->Body = $html;

$mail->SetFrom("info@freightdragon.com");
$mail->Subject = "New Quote Request";
$mail->AddAddress('shahrukhusmaani@gmail.com');
//$mail->AddAddress($email['email']);
$ret = $mail->SendToCD();

if($ret == "true"){
	die("<br>Under development!");
} else {
	echo json_encode(array('Success'=>true, 'Email Sent'));	
}

// code to send email finishes here

$_SESSION['iamcron'] = false;
require_once("done.php");
?>