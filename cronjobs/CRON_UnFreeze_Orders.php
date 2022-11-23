<?php

/**
 * Cronjob to run in the bckground to achieve the unlocking of the orders, that have exceeded the maximum blocking limit.
 * 
 * @author Shahrukh
 * @version 1.0.0
 */

@session_start();
require_once "init.php";
ob_start();
$_SESSION['iamcron'] = true;

echo "Fetching Records that are currently blocked!<br/>";

$where_clause = "blocked_by != 0 AND blocked_time !=0 OR blocked_by_carrier != 0 AND blocked_by_carrier_time !=0";
$query = "SELECT id, prefix, number, blocked_by, blocked_time, blocked_by_carrier, blocked_by_carrier_time FROM app_entities WHERE ".$where_clause." ORDER BY id DESC";

$result = $daffny->DB->query($query);

$toFreeEntities= [];
while($r = mysqli_fetch_assoc($result)){

    $date1;
    if($r['blocked_time']){
        $date2 = date('Y-m-d h:i:s',$r['blocked_time']);
    } else{
        $date2 = date('Y-m-d h:i:s',$r['blocked_by_carrier_time']);
    }

    $startdate = new DateTime($date2);
    $endDate   = new DateTime('now');
    $interval  = $endDate->diff($startdate);
    $minutes = $interval->format('%i');
    $days = $interval->format('%d');
    $months = $interval->format('%m');
    $years = $interval->format('%y');

    $free = false;
    if($years > 0) {
        $free = true;
    }
    
    if($month > 0) {
        $free = true;
    }
    
    if($days > 0) {
        $free = true;
    }

    if($minutes > 4) {
        echo "Entity ID: ".$r['id']."-";
        echo $minutes."Mins<br/>";
        $free = true;
    }

    if($free){
        $toFreeEntities[] = $r['id'];
    }
}

if(count($toFreeEntities) > 0 ){
    $where_in_entities = implode(',',$toFreeEntities);
    $sql = "UPDATE app_entities SET blocked_by = NULL, blocked_time = NULL, blocked_by_carrier = NULL, blocked_by_carrier_time = NULL WHERE id IN (".$where_in_entities.")";
    $daffny->DB->query($sql);
    echo "<br/>All Records freed, that were blocked for more that maximum block time.<br/>";
} else {
    echo "<br/>No Entity to free at the moment.<br/>";
}

$_SESSION['iamcron'] = false;
require_once "done.php";