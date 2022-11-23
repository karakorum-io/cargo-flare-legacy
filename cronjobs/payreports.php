<?php
/* * ************************************************************************************************
 * Cron RepostToCd
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-04-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

@session_start();
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);

$str='
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: '.date("m-d-Y H:i:s").'</h3></td>
</tr>
</table>
<div><h1>Pay Activity Report</h1></div>
<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#303030">
						<tr bgcolor="#297eaf" >
                             <td width="10%" rowspan="2"  style="padding:3px;"><b><center><font color="white">Date Received</font></center></b></td>
                             <td width="9%" rowspan="2" style="padding:3px;"><b><center><font color="white">Received Day</font></center></b></td>
							 <td width="45%" colspan="5" align="center" bgcolor="#1F7A1F" style="padding:3px;"><b><font color="white">Account Receivable</font></b></td>
							 <td width="27%" colspan="3"  align="center" bgcolor="##E60000"  style="padding:3px;"><b><font color="white">Account Payable</font></b></td>
                             <td width="9%" rowspan="2" align="right" style="padding:3px;"><b><font color="white">Difference</font></b></td> 
							 
						  </tr>;
                         <tr bgcolor="#297eaf" >
							 <td  width="9%" align="right" bgcolor="#1F7A1F"  style="padding:3px;"><b><font color="white">Number of Payments</font></b></td>
							 <td  width="9%" bgcolor="#1F7A1F"  style="padding:3px;"><b><center><font color="white">GP%</font></b></td>
                             <td  width="9%" bgcolor="#1F7A1F"  align="right"   style="padding:3px;"><b><font color="white">Payment(s) In</font></b></td>
							 <td  width="9%" bgcolor="#1F7A1F"  align="right" style="padding:3px;"><b><font color="white">Broker Fee(s)</font></b></td>
							 <td  width="9%" bgcolor="#1F7A1F"  style="padding:3px;"><b><center><font color="white">Carrier Fee(s)</font></b></td>
							 
                             <td  width="9%" align="right"  bgcolor="##E60000"   style="padding:3px;"><b><font color="white">Number of Payments</font></b></td>
							 <td  width="9%" align="right"  bgcolor="##E60000"   style="padding:3px;"><b><font color="white">Carrier(s) Payments</font></b></td>
							 <td  width="9%" align="right"  bgcolor="##E60000"   style="padding:3px;"><b><font color="white">Refund(s) Processed</font></b></td>
						
						  </tr>';
						  
						  
$TotalCount = 0;							
$totalTariff =0;
$carrierPay=0;


$sql = "	SELECT
		CASE isnull (A.date_received) 
				When 1 then B.date_received
				else A.date_received end  date_received,
			CASE isnull (A.date_received_day) 
				When 1 then B.date_received_day
				else A.date_received_day end  date_received_day,
		A.PayAmount as AR_Pay_In,
        A.NoOfPayment as AR_NoOfPayment,
		A.TotalVehicle as AR_Pay_InTotalVehicle,
		A.TotalCarrier_pay as AR_Pay_InCarrier_pay,
		A.TotalDeposit as AR_Pay_InTotalDeposit,
		A.TotalTariff as AR_Pay_InTotalTariff,
		B.PayAmount as AP_Pay_Out,
        B.NoOfPayment as AP_NoOfPayment,
		B.TotalVehicle as AP_Pay_OutTotalVehicle,
		B.TotalCarrier_pay as AP_Pay_OutCarrier_pay,
		B.TotalDeposit as AP_Pay_OutTotalDeposit,
		B.TotalTariff as AP_Pay_OutTotalTariff
	
	
	FROM	
		(SELECT 	DATE_FORMAT(AP.date_received,'%m-%d-%Y') date_received, 
				DATE_FORMAT(AP.date_received,'%a') date_received_day, 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentFrom,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentTo,
                count(*) as NoOfPayment,
				sum(AP.amount) as PayAmount,					
				sum(AOH.TotalVehicle) as TotalVehicle,
				sum(AOH.total_carrier_pay) as TotalCarrier_pay,
				sum(AOH.total_deposite) as TotalDeposit,
				sum(AOH.total_tariff) as TotalTariff	
		FROM  	app_payments  AP inner join app_order_header AOH 
				on AP.entity_id = AOH.entityid 
		WHERE  	AP.date_received >=  DATE_FORMAT(NOW() ,'%Y-%m-01') 
		and AP.deleted = 0
		and (AP.fromid = 2 and AP.toid = 1)
		group by DATE_FORMAT(AP.date_received,'%m-%d-%Y'),  
				DATE_FORMAT(AP.date_received,'%a'), 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END 
		) as A 
		
		LEFT OUTER JOIN		
		(
		SELECT 	DATE_FORMAT(AP.date_received,'%m-%d-%Y') date_received, 
				DATE_FORMAT(AP.date_received,'%a') date_received_day, 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentFrom,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentTo,
				sum(AP.amount) as PayAmount,	
                count(*) as NoOfPayment,				
				sum(AOH.TotalVehicle) as TotalVehicle,
				sum(AOH.total_carrier_pay) as TotalCarrier_pay,
				sum(AOH.total_deposite) as TotalDeposit,
				sum(AOH.total_tariff) as TotalTariff	
		FROM  	app_payments  AP inner join app_order_header AOH 
				on AP.entity_id = AOH.entityid 
		WHERE  	AP.date_received >=  DATE_FORMAT(NOW() ,'%Y-%m-01') 
		and AP.deleted = 0
		and (AP.fromid = 1 and AP.toid = 3)
		group by DATE_FORMAT(AP.date_received,'%m-%d-%Y'),  
				DATE_FORMAT(AP.date_received,'%a'), 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END 	
		)	as B
		
		ON A.date_received = B.date_received
		
UNION		
		
		SELECT
		CASE isnull (A.date_received) 
				When 1 then B.date_received
				else A.date_received end  date_received,
			CASE isnull (A.date_received_day) 
				When 1 then B.date_received_day
				else A.date_received_day end  date_received_day,
		A.PayAmount as AR_Pay_In,
        A.NoOfPayment as AR_NoOfPayment,
		A.TotalVehicle as AR_Pay_InTotalVehicle,
		A.TotalCarrier_pay as AR_Pay_InCarrier_pay,
		A.TotalDeposit as AR_Pay_InTotalDeposit,
		A.TotalTariff as AR_Pay_InTotalTariff,
		B.PayAmount as AP_Pay_Out,
        B.NoOfPayment as AP_NoOfPayment,
		B.TotalVehicle as AP_Pay_OutTotalVehicle,
		B.TotalCarrier_pay as AP_Pay_OutCarrier_pay,
		B.TotalDeposit as AP_Pay_OutTotalDeposit,
		B.TotalTariff as AP_Pay_OutTotalTariff
	
	
	FROM	
		(SELECT 	DATE_FORMAT(AP.date_received,'%m-%d-%Y') date_received, 
				DATE_FORMAT(AP.date_received,'%a') date_received_day, 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentFrom,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentTo,
				sum(AP.amount) as PayAmount,	
                count(*) as NoOfPayment,				
				sum(AOH.TotalVehicle) as TotalVehicle,
				sum(AOH.total_carrier_pay) as TotalCarrier_pay,
				sum(AOH.total_deposite) as TotalDeposit,
				sum(AOH.total_tariff) as TotalTariff	
		FROM  	app_payments  AP inner join app_order_header AOH 
				on AP.entity_id = AOH.entityid 
		WHERE  	AP.date_received >=  DATE_FORMAT(NOW() ,'%Y-%m-01') 
		and AP.deleted = 0
		and (AP.fromid = 2 and AP.toid = 1)
		group by DATE_FORMAT(AP.date_received,'%m-%d-%Y'),  
				DATE_FORMAT(AP.date_received,'%a'), 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END 
		) as A 
		
		RIGHT OUTER JOIN		
		(
		SELECT 	DATE_FORMAT(AP.date_received,'%m-%d-%Y') date_received, 
				DATE_FORMAT(AP.date_received,'%a') date_received_day, 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentFrom,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END PaymentTo,
				sum(AP.amount) as PayAmount,
                count(*) as NoOfPayment,					
				sum(AOH.TotalVehicle) as TotalVehicle,
				sum(AOH.total_carrier_pay) as TotalCarrier_pay,
				sum(AOH.total_deposite) as TotalDeposit,
				sum(AOH.total_tariff) as TotalTariff	
		FROM  	app_payments  AP inner join app_order_header AOH 
				on AP.entity_id = AOH.entityid 
		WHERE  	AP.date_received >=  DATE_FORMAT(NOW() ,'%Y-%m-01') 
		and AP.deleted = 0
		and (AP.fromid = 1 and AP.toid = 3)
		group by DATE_FORMAT(AP.date_received,'%m-%d-%Y'),  
				DATE_FORMAT(AP.date_received,'%a'), 
				CASE AP.fromid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END,
				CASE AP.toid 
					WHEN 1 THEN 'COMPANY' 
					WHEN 2 THEN 'SHIPPER' 
					WHEN 3 THEN 'CARRIER' 
				END 	
		)	as B
		
		ON A.date_received = B.date_received
		
		
		";					  
$result = $daffny->DB->query($sql);
$AR_Pay_In = 0;							
$AP_Pay_Out =0;
$difference=0;
if ($daffny->DB->num_rows() > 0) 
  {

      $i=1;

        while ($row = $daffny->DB->fetch_row($result)) {
			        
			 
			 if ( ($i % 2) == 0) {$bgcolor="#cdcdcd";} else{$bgcolor="#ffffff";}
			 
		     if($row['AR_Pay_InTotalTariff'] !=0 && $row['AR_Pay_InTotalDeposit'] !=0)
	         $gp_per = ($row['AR_Pay_InTotalDeposit'] / $row['AR_Pay_InTotalTariff'])*100;
			 
			          
			 $str .='<tr>
							 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['date_received'].'</td>
                             <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['date_received_day'].'</td>
							 
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;"> ' . $row['AR_NoOfPayment'].'</td>
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;"> ' . number_format($gp_per,2,'.',',').'</td>
							 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$ ' . number_format($row['AR_Pay_In'],2,'.',',').'</td>
							 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">$ '.number_format(($ls['AR_Pay_InTotalDeposit']), 2).'</td>
							 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$ ' . number_format($row['AR_Pay_InCarrier_pay'],2,'.',',').'</td>
							 
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['AP_NoOfPayment'].'</td>
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$ ' . number_format($row['AP_Pay_OutCarrier_pay'],2,'.',',').'</td>
							 
							 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$0.00</td>
							 
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$ '.number_format(($row['AR_Pay_In'] - $row['AP_Pay_Out']),2,'.',',').'</td>
							 
                           </tr>';
					$AR_Pay_In += $row['AR_Pay_In'];
					$Vehicle_In += $row['AR_NoOfPayment'];
					$Deposit_In += $row['AR_Pay_InTotalDeposit'];
					$Carrier_In += $row['AR_Pay_InCarrier_pay'];
			        $AP_Pay_Out +=$row['AP_Pay_Out'];
					$Vehicle_Out += $row['AP_NoOfPayment'];
					$Carrier_Out += $row['AP_Pay_OutCarrier_pay'];
                     $difference +=($row['AR_Pay_In'] - $ls['AP_Pay_Out']);
					$i= $i+1;
			
			}
       if($GPPercentage !=0)
		$GPPercentage = $GPPercentage/$i;
  }
  
  $str .='<tr bgcolor="#297eaf" >
							 <td align="center"  style="padding:3px;white-space: nowrap;" class="grid-body-left"><font color="white"><b>TOTALS</b></font></td>
            <td align="right" style="padding:3px;">&nbsp;</td>
			
			<td align="right" bgcolor="#1F7A1F"  style="padding:3px;"><font color="white"><b>'.$Vehicle_In.'</b></font></td>
			<td align="right" bgcolor="#1F7A1F"  style="padding:3px;">&nbsp;</td>
            <td align="right" bgcolor="#1F7A1F"  style="padding:3px;"><font color="white"><b>$'.number_format($AR_Pay_In, 2).'</b></font></td>
             <td align="right" bgcolor="#1F7A1F"  style="padding:3px;"><font color="white"><b>$'. number_format($Deposit_In, 2).'</b></font></td>
			 <td align="right" bgcolor="#1F7A1F"  style="padding:3px;"><font color="white"><b>$'. number_format($Carrier_In, 2).'</b></font></td>
			 <td align="right" bgcolor="##E60000"  style="padding:3px;"><font color="white"><b>'.$Vehicle_Out.'</b></font></td>
            <td align="right" bgcolor="##E60000"  style="padding:3px;"><font color="white"><b>$'. number_format($Carrier_Out, 2).'</b></font></td>
			<td align="right" bgcolor="##E60000"  style="padding:3px;">&nbsp;</td>
            <td align="right" style="padding:3px;"><font color="white"><b>$'. number_format($difference, 2).'</b></font></td>
							
						  </tr>';

  print $str .='</table>';
 
 

 
$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
			$mail = new FdMailer(true);
			$mail->isHTML();
			$mail->Body = $str;
			$mail->Subject = "Pay Activity Report " . date("m-d-Y H:i:s");
			$mail->AddAddress("Jeff@ritewayautotransport.com");
			$mail->AddCC("neeraj@freightdragon.com"); 
			$mail->SetFrom($daffny->cfg['info_email']);
			//$mail->Send();
			
		} catch (Exception $exc) {
			echo print "-----".$exc->getTraceAsString();
		}


    require_once("done.php");



?>