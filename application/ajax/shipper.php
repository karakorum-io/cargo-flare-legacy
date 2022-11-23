<?php

/**
 * shipper.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 * 
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once("init.php");
require_once("../../libs/QuickBooks.php");

$memberId = (int) $_SESSION['member_id'];

$out = array('success' => false);

if ($memberId > 0) {
    try {
        switch (filter_var($_POST['action'])) {
            case 'validateUniqueShipper':
                
                if(isset($_POST['key']) && isset($_POST['value'])){
                    $query = "SELECT count(*) as `exists`,id,phone1_ext,phone2_ext,phone2,cell,referred_by,referred_id,fax,shipper_type,hours_of_operation, first_name, last_name, company_name, email, phone1, referred_by, address1, address2, city, state, zip_code, country FROM `app_accounts` WHERE `is_shipper`=1 AND `".$_POST['key']."` = '".$_POST['value']."' ";
                    $result = $daffny->DB->hardQuery($query);
                    $exists = mysqli_fetch_assoc($result);                    
                }                
                $out = array(
                    'success' => true,
                    'id'=>$exists['id'],
                    'shipper_type'=>$exists['shipper_type'],
                    'hours_of_operation'=>$exists['hours_of_operation'],
                    'exists'=>$exists['exists'],
                    'first_name'=>$exists['first_name'],
                    'last_name'=>$exists['last_name'],
                    'company_name'=>$exists['company_name'],
                    'email'=>$exists['email'],
                    'phone1'=>$exists['phone1'],
                    'phone2'=>$exists['phone2'],
                    'phoneExt1'=>$exists['phone1_ext'],
                    'phoneExt2'=>$exists['phone2_ext'],
                    'cell'=>$exists['cell'],
                    'referred_by'=>$exists['referred_by'],
                    'referred_id'=>$exists['referred_id'],
                    'address1'=>$exists['address1'],
                    'address2'=>$exists['address2'],
                    'city'=>$exists['city'],
                    'state'=>$exists['state'],
                    'country'=>$exists['country'],
                    'zip_code'=>$exists['zip_code']                    
                );
            break;
            case 'getShipperOrders':
                $html = '<table class="table table-bordered">
                    <thead>
                        <tr >
                            <th>Order ID</th>
                            <th>Created Date</th>
                            <th>Vehicles</th>
                            <th>Route</th>
                            <th colspan="2" align="center">Dates</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>';
                if(isset($_POST['shipperId'])){
                    $shipperId =  $_POST['shipperId'];
                    $query = "SELECT * FROM `app_order_header` WHERE `account_id`='".$shipperId."'  AND  type = 3";
                    $result = $daffny->DB->hardQuery($query);

                    while($row = mysqli_fetch_assoc($result)){

                        if($row['status'] ==1){
                            $status = "Active";
                        } elseif($row['status'] == 2) {
                            $status = "On Hold";
                        } elseif($row['status'] == 3) {
                            $status = "Cancelled";
                        } elseif($row['status'] == 4) {
                            $status = "Posted";
                        } elseif($row['status'] == 5) {
                            $status = "Not Signed";
                        } elseif($row['status'] == 6) {
                            $status = "Dispatched";
                        } elseif($row['status'] == 7) {
                            $status = "Issues";
                        } elseif($row['status'] == 8) {
                            $status = "Picked Up";
                        } elseif($row['status'] == 9) {
                            $status = "Delivered";
                        } else {
                            $status = "Invalid";
                        }

                        /* dated settings */
                        $date_type_string = array(
                            1 => "Estimated",
                            2 => "Exactly",
                            3 => "Not Earlier Than",
                            4 => "Not Later Than"
                        );

                        $Date1 = "";
                        $Date2 = "";

                        /*
                        *  Dates two Column data assembling in innerPopup of the shipper popup
                        */
                        if ($row['status'] == 4 || $row['status'] == 1) {
                            if (strtotime($row['avail_pickup_date']) > 0) {
                                $Date1 = "<b>1st avil:</b><br>".date("m/d/y",strtotime($row['avail_pickup_date']))."<br>";
                            }
                            if (strtotime($row['posted']) > 0) {
                                $Date2 = "<b>Posted:</b><br>" . date("m/d/y", strtotime($row['posted']));
                            }
                        } elseif ($row['status'] == 3) {

                            if (strtotime($row['avail_pickup_date']) > 0){
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($row['avail_pickup_date']));
                            }

                            if ($row['archived'] != ""){
                                $Date2 = "<b>Cancelled:</b><br>" . date("m/d/y", strtotime($row['archived']));
                            }

                        } elseif ($row['status'] == 7 || $row['status'] == 9) {
                            if (strtotime($row['load_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['load_date_type'] > 0 ? $date_type_string[(int) $row['load_date_type']] : "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['load_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] :
                                        "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 5 || $row['status'] == 6) {

                            if (strtotime($row['load_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['load_date_type'] > 0 ? $date_type_string[(int) $row['load_date_type']] :
                                        "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['load_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] :
                                        "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 8) {

                            if (strtotime($row['actual_pickup_date']) > 0){
                                $Date1 = "<b>Pickup:</b><br>" . date("m/d/y", strtotime($row['actual_pickup_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] : "";
                                $Date2 = $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 2) {
                            if (strtotime($row['avail_pickup_date']) > 0){
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($row['avail_pickup_date']));
                            }

                            if ($row['hold_date'] != ""){
                                $Date2 = "<b>Hold:</b><br>" . date("m/d/y", strtotime($row['hold_date']));
                            }

                        } else {
                            $Date1 = "N/A";
                            $Date2 = "N/A";
                        }

                        $entity = new Entity( $daffny->DB);

                        try {
                            $entity->load($row['entityid']);
                            $color = $entity->isPaidOffColor();

                            $tariffColor= $colorCode[$color['total']];
                            $depositeColor = $colorCode[$color['deposit']];
                            $carrierPayColor = $colorCode[$color['carrier']];
                            
                            $colorCode = array(
                                0 => 'black',
                                1 => 'green',
                                2 => 'red'
                            );

                            $html .= '<tr>
                                        <td align="center" width="6%">
                                            <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/show/id/'.$row['entityid'].'">
                                                <div class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill order_id" style="margin:9px 3px">'.$row['prefix'].'-'.$row['number'].'</div>
                                            </a>
                                            <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/history/id/'.$row['entityid'].'">History</a><br/>
                                            <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/show/id/'.$row['entityid'].'"><b>'.$status.'</b></a>
                                        </td>
                                        <td valign="top" width="10%" style="">
                                            '.$row['ordered'].'<br>
                                            <br>Assigned to:<br> <strong class="kt-font-success">'.$row['AssignedName'].'</strong><br>
                                        </td>
                                        <td width="13%">
                                            <a target="_blank" class="t-badge  kt-badge--warning kt-badge--inline" style="color:#008ec2; cursor: pointer;" onclick="">'.$row['Vehicleyear'].' '.$row['Vehiclemake'].' '.$row['Vehiclemodel'].'<br>'.$row['Vehicletype'].'&nbsp;</a>
                                            <a target="_blank" href="http://www.google.com/search?tbm=isch&amp;hl=en&amp;q=+'.$row['Vehiclemake'].'+'.$row['Vehiclemodel'].'" onclick="window.open(this.href); return false;" title="Show It">[Show It]</a>
                                            <br>
                                            <span style="color:red;weight:bold;"></span><br>
                                        </td>
                                        <td width="13%">
                                            <span class="kt-font-bold" onclick="https://maps.google.com/maps?q='.$row['Origincity'].'+'.$row['Originstate'].'+'.$row['Originzip'].'">'.$row['Origincity'].', '.$row['Originstate'].' '.$row['Originzip'].'</span>/<br>
                                            <span class="kt-font-bold" onclick="https://maps.google.com/maps?q='.$row['Destinationcity'].'+'.$row['Destinationstate'].'+'.$row['Destinationzip'].'">'.$row['Destinationcity'].', '.$row['Destinationstate'].' '.$row['Destinationzip'].'</span>
                                        </td>
                                        <td valign="top" align="center" width="7%">
                                            <span class="">'.$Date1.'</span>
                                            <span class="">'.$Date2.'</span>
                                        </td>
                                        <td valign="top" align="center" width="7%"></td>
                                        <td width="10%" align="right">
                                            <img src="https://cargoflare.dev/images/icons/dollar.png" width="16" height="16">
                                            <span style="color: '.$tariffColor.';" align="right"> $'.$row['total_tariff'].'</span>
                                            <br/>
                                            <img src="https://cargoflare.dev/images/icons/truck.png" width="16" height="16">
                                            <span style="color: '.$carrierPayColor.';" align="right"> $'.$row['total_carrier_pay'].'</span>
                                            <br/>
                                            <img src="https://cargoflare.dev/images/icons/person.png" width="16" height="16">
                                            <span style="color: '.$depositeColor.';" align="right"> $'.$row['total_deposite'].'</span>
                                        </td>
                                    </tr>';
                        } catch(Exception $e){
                            // do nothing
                        }
                    }

                    $html .= '</tbody></table>';

                    $out = array(
                        'html'=>$html
                    );
                }
            break;
            case 'getShipperQuotes':
                $html = '<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Created Date</th>
                            <th>Vehicles</th>
                            <th>Route</th>
                            <th colspan="2" align="center">Dates</th>
                            <th>Cost</th>
                        </tr>
                    </thead>
                    <tbody>';
                /* validating the variables*/
                if(isset($_POST['shipperId'])){
                    $shipperId =  $_POST['shipperId'];
                    $query = "SELECT * FROM `app_order_header` WHERE `account_id`='".$shipperId."' AND (type = 1 OR type=4)  ";
                    $result = $daffny->DB->hardQuery($query);

                    while($row = mysqli_fetch_assoc($result)){

                        if($row['status'] ==1){
                            $status = "Active";
                        } elseif($row['status'] == 2) {
                            $status = "On Hold";
                        } elseif($row['status'] == 3) {
                            $status = "Cancelled";
                        } elseif($row['status'] == 4) {
                            $status = "Posted";
                        } elseif($row['status'] == 5) {
                            $status = "Not Signed";
                        } elseif($row['status'] == 6) {
                            $status = "Dispatched";
                        } elseif($row['status'] == 7) {
                            $status = "Issues";
                        } elseif($row['status'] == 8) {
                            $status = "Picked Up";
                        } elseif($row['status'] == 9) {
                            $status = "Delivered";
                        } else {
                            $status = "Invalid";
                        }

                        /* dated settings */
                        $date_type_string = array(
                            1 => "Estimated",
                            2 => "Exactly",
                            3 => "Not Earlier Than",
                            4 => "Not Later Than"
                        );

                        $Date1 = "";
                        $Date2 = "";

                        /*
                        *  Dates two Column data assembling in innerPopup of the shipper popup
                        */
                        if ($row['status'] == 4 || $row['status'] == 1) {
                            if (strtotime($row['avail_pickup_date']) > 0) {
                                $Date1 = "<b>1st avil:</b><br>".date("m/d/y",strtotime($row['avail_pickup_date']))."<br>";
                            }
                            if (strtotime($row['posted']) > 0) {
                                $Date2 = "<b>Posted:</b><br>" . date("m/d/y", strtotime($row['posted']));
                            }
                        } elseif ($row['status'] == 3) {

                            if (strtotime($row['avail_pickup_date']) > 0){
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($row['avail_pickup_date']));
                            }

                            if ($row['archived'] != ""){
                                $Date2 = "<b>Cancelled:</b><br>" . date("m/d/y", strtotime($row['archived']));
                            }

                        } elseif ($row['status'] == 7 || $row['status'] == 9) {
                            if (strtotime($row['load_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['load_date_type'] > 0 ? $date_type_string[(int) $row['load_date_type']] : "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['load_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] :
                                        "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 5 || $row['status'] == 6) {

                            if (strtotime($row['load_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['load_date_type'] > 0 ? $date_type_string[(int) $row['load_date_type']] :
                                        "";
                                $Date1 = "<b>ETA Pickup:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['load_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] :
                                        "";
                                $Date2 = "<b>ETA Delivery:</b><br />" . $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 8) {

                            if (strtotime($row['actual_pickup_date']) > 0){
                                $Date1 = "<b>Pickup:</b><br>" . date("m/d/y", strtotime($row['actual_pickup_date']));
                            }

                            if (strtotime($row['delivery_date']) == 0){
                                $abbr = "N/A";
                            } else {
                                $abbr = $row['delivery_date_type'] > 0 ? $date_type_string[(int) $row['delivery_date_type']] : "";
                                $Date2 = $abbr . "<br />" . date("m/d/y", strtotime($row['delivery_date']));
                            }

                        } elseif ($row['status'] == 2) {
                            if (strtotime($row['avail_pickup_date']) > 0){
                                $Date1 = "<b>1st avil:</b><br>" . date("m/d/y", strtotime($row['avail_pickup_date']));
                            }

                            if ($row['hold_date'] != ""){
                                $Date2 = "<b>Hold:</b><br>" . date("m/d/y", strtotime($row['hold_date']));
                            }

                        } else {
                            $Date1 = "N/A";
                            $Date2 = "N/A";
                        }
                        $entity = new Entity( $daffny->DB);
                        $entity->load($row['entityid']);
                        $color = $entity->isPaidOffColor();

                        $tariffColor= $colorCode[$color['total']];
                        $depositeColor = $colorCode[$color['deposit']];
                        $carrierPayColor = $colorCode[$color['carrier']];

                        $colorCode = array(
                            0 => 'black',
                            1 => 'green',
                            2 => 'red'
                        );

                        $html .= '<tr>
                            <td align="center" width="6%">
                                <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/show/id/'.$row['entityid'].'">
                                    <div class="kt-badge kt-badge--info kt-badge--inline kt-badge--pill order_id" style="margin:9px 3px">'.$row['prefix'].'-'.$row['number'].'</div>
                                </a>
                                <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/history/id/'.$row['entityid'].'">History</a><br/>
                                <a target="_blank" href="'.$_SERVER['HTTP_ORIGIN'].'/application/orders/show/id/'.$row['entityid'].'"><b>'.$status.'</b></a>
                            </td>
                            <td valign="top" width="10%" style="">
                                '.$row['ordered'].'<br>
                                <br>Assigned to:<br> <strong class="kt-font-success">'.$row['AssignedName'].'</strong><br>
                            </td>
                            <td width="13%">
                                <a target="_blank" class="t-badge  kt-badge--warning kt-badge--inline" style="color:#008ec2; cursor: pointer;" onclick="">'.$row['Vehicleyear'].' '.$row['Vehiclemake'].' '.$row['Vehiclemodel'].'<br>'.$row['Vehicletype'].'&nbsp;</a>
                                <a target="_blank" href="http://www.google.com/search?tbm=isch&amp;hl=en&amp;q=+'.$row['Vehiclemake'].'+'.$row['Vehiclemodel'].'" onclick="window.open(this.href); return false;" title="Show It">[Show It]</a>
                                <br>
                                <span style="color:red;weight:bold;"></span><br>
                            </td>
                            <td width="13%">
                                <span class="kt-font-bold" onclick="https://maps.google.com/maps?q='.$row['Origincity'].'+'.$row['Originstate'].'+'.$row['Originzip'].'">'.$row['Origincity'].', '.$row['Originstate'].' '.$row['Originzip'].'</span>/<br>
                                <span class="kt-font-bold" onclick="https://maps.google.com/maps?q='.$row['Destinationcity'].'+'.$row['Destinationstate'].'+'.$row['Destinationzip'].'">'.$row['Destinationcity'].', '.$row['Destinationstate'].' '.$row['Destinationzip'].'</span>
                            </td>
                            <td valign="top" align="center" width="7%">
                                <span class="">'.$Date1.'</span>
                                <span class="">'.$Date2.'</span>
                            </td>
                            <td valign="top" align="center" width="7%"></td>
                            <td width="10%" align="right">
                                <img src="https://cargoflare.dev/images/icons/dollar.png" width="16" height="16">
                                <span style="color: '.$tariffColor.';" align="right"> $'.$row['total_tariff'].'</span>
                                <br/>
                                <img src="https://cargoflare.dev/images/icons/truck.png" width="16" height="16">
                                <span style="color: '.$carrierPayColor.';" align="right"> $'.$row['total_carrier_pay'].'</span>
                                <br/>
                                <img src="https://cargoflare.dev/images/icons/person.png" width="16" height="16">
                                <span style="color: '.$depositeColor.';" align="right"> $'.$row['total_deposite'].'</span>
                            </td>
                        </tr>';
                    }

                    $html .= '</tbody></table>';

                    $out = array(
                        'html'=>$html
                    );
                }
            break;
            case 'resetPassword':
                
                $shipperId = $_POST['shipperId'];
                $token = md5($shipperId.$_SESSION['member']['email'].rand(1000,9999));
                $query = "UPDATE `app_accounts` SET `password_token` = '".$token."', `password_flag` = '1' WHERE `id` = '".$shipperId."' ";
                $result = $daffny->DB->hardQuery($query);
                
                $protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
                $domain    = $_SERVER['SERVER_NAME'];
                $disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : "";
                $base_url  = preg_replace("!^${doc_root}!", '', $base_dir);
                $changePasswordURL  = "${protocol}://${domain}${disp_port}${base_url}"."/reset/id/".$shipperId."/token/".$token;                
                
                $out = array(
                        'message'=>'Email sent to the shipper with reset link',
                        'url'=>$changePasswordURL
                );
                
            break;
        }
    } catch (FDException $e) {
        echo $e->getMessage();
    }
}

echo $json->encode($out);
require_once("done.php");
