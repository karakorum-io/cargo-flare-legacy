<?php
    /**
     * CRONJOB to send user sales report
     * 
     * @author Shahrukh
     * @version 1.0
     */

    @session_start();
    require_once "init.php";
    require_once "../libs/phpmailer/class.phpmailer.php";
    ob_start();
    
    $_SESSION['iamcron'] = true; // Says I am cron for Full Access
    
    // for maximum time limit
    set_time_limit(80000000);
    ini_set('memory_limit', '3500M');
    ini_set('upload_max_filesize', '128M');
    ini_set('post_max_size', '128M');
    ini_set('max_input_time', 80000000);
    // cronjob logic starts

    //fetching all parent ids
    $query = "SELECT `parentid`, `assigned_id`,`AssignedName`, DATE_FORMAT(`created`,'%m-%d-%Y') `Createdate`, DATE_FORMAT(`created`,'%a') `day` , Count(*) `NoOfShipment`, sum(`total_tariff`) `Tariff`, sum(`total_carrier_pay`) `CarrierPay`, sum(`total_deposite`) `GP`, FORMAT(sum(`total_deposite`)* 100 / sum(`total_tariff`),2) AS `GPPercentage` FROM `app_order_header` WHERE `created` > DATE_FORMAT(NOW() ,'2014-01-01') AND `type` = 3 AND `assigned_id` IN (SELECT `id` FROM `members` WHERE `parent_id` IN (SELECT DISTINCT `parent_id` FROM `members`) AND status = 'Active' ) GROUP BY DATE_FORMAT(`created`,'%m-%d-%Y'), DATE_FORMAT(`created`,'%a')  ORDER BY `assigned_id` ASC";
    $result = $daffny->DB->query($query);

    $data = array();
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }

    $assignedFlag = $data[0]['assigned_id'];
    $pdfArray = array();
    $dataCounter = 0;
    $i = 0;
    
    $pdfArray = array();
    $j = 0;
    while($dataCounter<count($data)){
        
        if($data[$dataCounter]['assigned_id'] == $assignedFlag){
            $pdfArray[$i][$j]['assigned_id'] = $data[$dataCounter]['assigned_id'];
            $pdfArray[$i][$j]['AssignedName'] = $data[$dataCounter]['AssignedName'];
            $pdfArray[$i][$j]['parent_id'] = $data[$dataCounter]['parentid'];
            $pdfArray[$i][$j]['Createdate'] = $data[$dataCounter]['Createdate'];
            $pdfArray[$i][$j]['day'] = $data[$dataCounter]['day'];
            $pdfArray[$i][$j]['NoOfShipment'] = $data[$dataCounter]['NoOfShipment'];
            $pdfArray[$i][$j]['Tariff'] = $data[$dataCounter]['Tariff'];
            $pdfArray[$i][$j]['CarrierPay'] = $data[$dataCounter]['CarrierPay'];
            $pdfArray[$i][$j]['GP'] = $data[$dataCounter]['GP'];
            $pdfArray[$i][$j]['GPPercentage'] = $data[$dataCounter]['GPPercentage'];
            $j++;
        } else {
            $j=0;
            $i++;
            $assignedFlag = $data[$dataCounter]['assigned_id'];
            $pdfArray[$i][$j]['assigned_id'] = $data[$dataCounter]['assigned_id'];
            $pdfArray[$i][$j]['AssignedName'] = $data[$dataCounter]['AssignedName'];
            $pdfArray[$i][$j]['parent_id'] = $data[$dataCounter]['parentid'];
            $pdfArray[$i][$j]['Createdate'] = $data[$dataCounter]['Createdate'];
            $pdfArray[$i][$j]['day'] = $data[$dataCounter]['day'];
            $pdfArray[$i][$j]['NoOfShipment'] = $data[$dataCounter]['NoOfShipment'];
            $pdfArray[$i][$j]['Tariff'] = $data[$dataCounter]['Tariff'];
            $pdfArray[$i][$j]['CarrierPay'] = $data[$dataCounter]['CarrierPay'];
            $pdfArray[$i][$j]['GP'] = $data[$dataCounter]['GP'];
            $pdfArray[$i][$j]['GPPercentage'] = $data[$dataCounter]['GPPercentage'];
           
        }
        $dataCounter++;
    }

    for($outer=0;$outer<count($pdfArray);$outer++){

        $AssignedName = "";
        $AssignedId ="";
        $str_sales = '<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                        <tr bgcolor="#297eaf" >
                            <td width="15%"  style="padding:3px;"><b><center><font color="white">Create Date</font></center></b></td>
                            <td width="10%"  style="padding:3px;"><b><center><font color="white">Day</font></center></b></td>
                            <td width="15%" align="right" style="padding:3px;"><b><font color="white">Total Orders Posted</font></b></td>
                            <td width="15%" align="right" style="padding:3px;"><b><font color="white">Total Revenue</font></b></td>
                            <td width="15%"  align="right" style="padding:3px;"><b><font color="white">Carrier Pay</font></b></td>
                            <td width="15%" align="right" style="padding:3px;"><b><font color="white">GP</font></b></td>
                            <td width="15%" style="padding:3px;"><b><center><font color="white">GP%</font></center></b></td>
                        </tr>';
        
        $TotalCount = 0;
        $totalTariff = 0;
        $carrierPay = 0;
        $gpTotal = 0;
        $GPPercentage = 0;

        $i = 0;
        for($inner=0;$inner<count($pdfArray[$outer]);$inner++){
            
            if (($i % 2) == 0) {$bgcolor = "#cdcdcd";} else { $bgcolor = "#ffffff";}
            $str_sales .= '<tr>
                                <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $pdfArray[$outer][$inner]['Createdate'] . '</td>
                                <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $pdfArray[$outer][$inner]['day'] . '</td>
                                <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $pdfArray[$outer][$inner]['NoOfShipment'] . '</td>
                                <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($pdfArray[$outer][$inner]['Tariff'], 2, '.', ',') . '</td>
                                <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($pdfArray[$outer][$inner]['CarrierPay'], 2, '.', ',') . '</td>
                                <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($pdfArray[$outer][$inner]['GP'], 2, '.', ',') . '</td>
                                <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $pdfArray[$outer][$inner]['GPPercentage'] . '</td>
                            </tr>';
            $TotalCount += $pdfArray[$outer][$inner]['NoOfShipment'];
            $totalTariff += $pdfArray[$outer][$inner]['Tariff'];
            $carrierPay += $pdfArray[$outer][$inner]['CarrierPay'];
            $gpTotal += $pdfArray[$outer][$inner]['GP'];
            $GPPercentage += $pdfArray[$outer][$inner]['GPPercentage'];

            $i = $i + 1;

            if ($GPPercentage != 0) {
                $GPPercentage = $GPPercentage / $i;
            }
            
            $AssignedName = $pdfArray[$outer][$inner]['AssignedName'];
            $AssignedId = $pdfArray[$outer][$inner]['assigned_id'];
        }

        $str_sales .= '</table>';

        $sub = "Sales Activity Report  " . date("m-d-Y H:i:s");
        $fname = "salesreport" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
        $path_sales = ROOT_PATH . "cronjobs/UserReports/" . $fname;

        $GetDataQuery = "SELECT email FROM members WHERE `id` = '".$AssignedId."'";
        $MemberData = $daffny->DB->query($GetDataQuery);
        $MemberData = mysqli_fetch_assoc($MemberData);
        
        // getPdfNew("F", $path_sales, $str_sales, $sub);

        // try {
        //     $mail = new FdMailer(true);
        //     $mail->isHTML();
        //     $mail->Body = "Hey ".$AssignedName.",<br>The attached document has details about your sales, please review when possible.";
        //     $mail->Subject = $AssignedName." here are your sales for today!";            
        //     //$mail->AddAddress($MemberData['email']);
        //     $mail->AddAddress("shahrukhusmaani@gmail.com");
        //     //$mail->AddAddress("admin@ritewayautotransport.com");
        //     $mail->SetFrom("info@freightdragon.com");
        //     $mail->AddAttachment($path_sales, "Sales Activity Report " . date("m-d-y") . ".pdf");
        //     $mail->SendToCD();
            
        //     echo $outer.". Email Sent to :".$AssignedName."<br> at Email: ".$MemberData['email']."<br>";
        
        // } catch (Exception $exc) {
        //     die($exc->getTraceAsString());
        // }        
    }

    // cronjob logic ends
    echo "Cron Ended";
    $_SESSION['iamcron'] = false;
    require_once "done.php";

function getPdfNew($out = "D", $path = "DispatchSheet.pdf", $str, $sub)
{

    ob_start();
    require_once ROOT_PATH . "/libs/mpdf/mpdf.php";
    $pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);

    $pdf->SetAuthor("freight dragon");
    $pdf->SetSubject($sub);
    $pdf->SetTitle($sub);
    $pdf->SetCreator("FreightDragon.com");
    $pdf->SetAutoPageBreak(true, 30);
    $pdf->SetTopMargin(22);
    $pdf->writeHTML("<style>" . file_get_contents(ROOT_PATH . "styles/application_print_pdf.css") . "</style>", 1);

    $header = '<div style="text-align: center; font-weight: bold;height:230px;"><span class="dis_heading">' . $sub . '</span></div>';
    $pdf->SetHTMLHeader($header, 'O');

    $footer = '';

    $pdf->writeHTML($str, 2);
    ob_end_clean();
    $pdf->Output($path, $out);
    if (!is_null($signPath)) {
        unlink($signPath);
    }
}

?> 