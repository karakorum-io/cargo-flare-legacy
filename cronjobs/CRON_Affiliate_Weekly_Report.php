<?php
/**
 * CRONJOB to send weekly report to lead sources on email
 * 
 * @author Shahrukh
 * @version 1.0
 */
@session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Including the involved core functionality libraries
 */
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
require_once("core/template.php");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
$_SESSION['iamcron'] = true;
echo "Affiliate Portal Weekly Report CRON Started<br>";

// end date
//$to = date('Y-m-d');
$to = '2014-04-23';
// start date
//$from = date('Y-m-d', strtotime('-7 days', strtotime($to)));
$from = '2014-04-01';

// fetching all entity ids
$where = " `weekly_report` = 1 AND `status` = 1 ";
$rows = $daffny->DB->selectRows('id, email', "app_leadsources" ,"WHERE " . $where);
$status = array(
    "1" => "Active",
    "2" => "On Hold",
    "3" => "Cancelled",
    "4" => "Posted",
    "5" => "Not Signed",
    "6" => "Dispatched",
    "7" => "Issues",
    "8" => "Picked Up",
    "9" => "Delivered"
);

$html = "";
for ($i=0 ;$i<count($rows);$i++){
    //$id = $rows[$i]['id'];
    //$email = $rows[$i]['email'];
    $id = 11;
    $email = "shahrukhusmaani@gmail.com";

    $query = "select `id`,`entityid`,`source_id`, `type`, `prefix`, `number`, `quoted`, `Origincity`, `Originstate`, `Originzip`, `Destinationcity`, `Destinationstate`, `Destinationzip`, `created`, `status` from app_order_header where source_id = '".$id."' and `created` between '".$from."' and '".$to."' ORDER BY entityid DESC ";
    
    $html = "<center>
            <h2>Freightdragon</h2>
            <p>Weekly Report</p>
            <table border='1'>
                <tr>
                <th>ID</th> <th>Type</th> <th>Quoted</th> <th>Processed</th> <th>Route</th> <th>Vehicles</th> <th>Captured</th> <th>Status</th>
                </tr>";
    $rows = $daffny->DB->query($query);
    while($r = mysqli_fetch_assoc($rows)){
        
        if($r['type'] == 1){
            $type = "Leads";
        } else if($r['type'] == 2){
            $type = "Quotes";
        } else {
            $type = "Order";
        }
        
        $vehicles = getVehicles($daffny->DB,$r['entityid']);
        $html .= "<tr>";
        $html .= "<td>".$r['prefix']."-".$r['number']."</td>";
        $html .= "<td>".$type."</td>";
        $html .= "<td>".$r['quoted']."</td>";
        $html .= "<td align='center'>".checkProcessed($daffny->DB,$r['entityid'])."</td>";
        $html .= "<td>".$r['Origincity'].",".$r['Originstate'].",".$r['Originzip']." / ".$r['Destinationcity'].",".$r['Destinationstate'].",".$r['Destinationzip']."</td>";
        
        if(count($vehicles) > 1){
            for($i=0;$i<count($vehicles);$i++){
                $html .= "<td>".$vehicles[$i]['year']." ".$vehicles[$i]['make']." ".$vehicles[$i]['model']." ".$vehicles[$i]['type']."</td>";
            }
        } else {
            $html .= "<td>".$vehicles[0]['year']." ".$vehicles[0]['make']." ".$vehicles[0]['model']." ".$vehicles[0]['type']."</td>";
        }
        
        $html .= "<td >".$r['created']."</td>";
        $html .= "<td align='center'>".$status[$r['status']]."</td>";
        $html .= "</tr>";
        $html .= "</table></center>";

        //sending email
        $mail = new FdMailer(true);
        $mail->isHTML();
        $mail->Body = $html;

        $mail->SetFrom("info@freightdragon.com");
        $mail->Subject = "Freightfragon Affiliate Weekly Report";
        $mail->AddAddress($email);
        $ret = $mail->SendToCD();
    }
}

echo "CRON Ends Here";
$_SESSION['iamcron'] = false;
require_once("done.php");

function getVehicles($db, $entity_id){
    $query = "SELECT year, make, model, type FROM app_vehicles WHERE entity_id = ".$entity_id."";
    $vehicles = $db->query($query);

    $data = array();
    while($row = mysqli_fetch_assoc($vehicles)){
        $data[] = $row;
    }
    return $data;
}

function checkProcessed($db, $entity_id){
    $query = "SELECT count(*) as records FROM app_payments WHERE entity_id = {$entity_id} AND deleted = 0 ";
    $rows = $db->query($query);
    $records = mysqli_fetch_assoc($rows);
    if( $records['records'] ==0 ){
        return "NO";
    } else {
        return "YES";
    }
}