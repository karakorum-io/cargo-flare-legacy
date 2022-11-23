<?php
/* * ************************************************************************************************
 * Client:  CargoFalre
 * Version: 2.0
 * Date:    2011-04-26
 * Author:  CargoFlare Team
 * Address: 7252 solandra lane tamarac fl 33321
 * E-mail:  stefano.madrigal@gmail.com
 * CopyRight 2021 Cargoflare.com - All Rights Reserved
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

//date_default_timezone_set('America/New_York');
print $str = '<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: '.date("l jS \of F Y h:i:s A").'</h3></td>
</tr>
</table>
<br><br>
<div><h2>Succsessful Credit Card Transaction Report on '.date("m-d-Y").'</h2></div>
';
$str='


<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#cdcdcd" style="font-size: 12px;">
						<tr bgcolor="#297eaf" >
                             <td width="12%" rowspan="2"  style="padding:3px;"><b><center><font color="white">Trans Time</font></center></b></td>
							<td width="8%" rowspan="2" align="left" style="padding:3px;"><b><center><font color="white">Order #</font></center></b></td>
							 <td width="9%" rowspan="2"  align="left" style="padding:3px;"><b><center><font color="white">Assigned Name</font></center></b></td>
							 
							 <td width="20%" colspan="2" align="center" bgcolor="#1E90FF" style="padding:3px;"><b><center><font color="white">Shipper</font></center></b></td>
							 <td width="8%" rowspan="2" align="left" style="padding:3px;"><b><center><font color="white">Trans Type</font></center></b></td>
							 <td width="8%" rowspan="2" align="right" style="padding:3px;"><b><center><font color="white">Trans Amount</font></center></b></td>
							 <td width="10%" rowspan="2" align="right" style="padding:3px;"><b><center><font color="white">Carrier Pay</font></center></b></td>
							 <td width="8%" rowspan="2" align="right" style="padding:3px;"><b><center><font color="white">Tariff</font></center></b></td> 
                             <td width="9%" rowspan="2" align="left"  style="padding:3px;"><b><center><font color="white">Referred By</font></center></b></td>
                             <td width="9%" align="left"  rowspan="2" style="padding:3px;"><b><center><font color="white">Trans By</font></center></b></td>
						  </tr>
                         <tr >
							 <td  width="12%" bgcolor="#1E90FF" align="left" style="padding:3px;"><b><center><font color="white">Company</font></center></b></td>
							 <td  width="8%" bgcolor="#1E90FF"  align="left" style="padding:3px;"><b><center><font color="white">Last Name</font></center></b></td>
							 
						  </tr>';
						  
						  
$TotalCount = 0;							
$totalTariff =0;
$carrierPay=0;


		$sql ="SELECT 
DATE_FORMAT(APN.created, '%m-%d-%Y %h:%i%p') as created,
case APN.system_admin
   when 2 then '<b>FreightDragon</b>'
   when 1 then M.contactname
   else M.contactname
   end as Type,
AOH.prefix ,
AOH.number, 
AOH.AssignedName,
AOH.referred_by,
AOH.shipper_type,
AOH.shippercompany,
AOH.shipperfname,
AOH.shipperlname,
AOH.total_carrier_pay,
AOH.total_deposite,
AOH.total_tariff,
CASE 
	WHEN INSTR(APN.text, 'CARD PROCESSED FOR THE AMOUNT OF') > 0 then 
		Trim(replace(replace(APN.text,'CREDIT CARD PROCESSED FOR THE AMOUNT OF', ''),'$ ',''))
	ELSE
		SUBSTRING(APN.text, INSTR(APN.text, '$')+1, INSTR(APN.text, 'by Credit Card') - INSTR(APN.text, '$') - 2)
	end	as TransactionAmount,
CASE 
		when  (( balance_paid_by = 2 ) OR 
			    (balance_paid_by = 3 ) OR 
				(balance_paid_by = 16 ) OR 
				(balance_paid_by = 17 ) OR 
				(balance_paid_by = 8 ) OR 
				(balance_paid_by = 9 ) OR 
				(balance_paid_by = 18 ) OR 
				(balance_paid_by = 19 ) ) THEN
			
			CASE 
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN '<strong>Deposit Partial Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN '<strong>Full Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN '<strong>Deposit Partial Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Deposit</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN '<strong>Deposit Full Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Deposit Partial</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN '<strong>Deposit Partial Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN '<strong>Deposit Partial Payment</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN '<strong>Deposit Partial Payment</strong>'
			
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN '<strong>Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN '<strong>Full Payment</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN '<strong>Partial</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN '<strong>Partial Payment</strong>'
			
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN '<strong>Full Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Deposit</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN '<strong>Full Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN '<strong>Partial Payment</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN '<strong>Partial Payment</strong>'
			ELSE 'COD/COP Partial Payment' END
		
		when  (( balance_paid_by = 12 ) OR 
			    (balance_paid_by = 13 ) OR 
				(balance_paid_by = 20 ) OR 
				(balance_paid_by = 21 ) ) THEN	
            
			CASE 
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN 'Billing Full Payment1'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN '<strong><font color=black>Full Payment</font></strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN '<strong><font color=black>Full Payment</font></strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Billing Partial'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN 'Billing Full Payment3'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Billing Deposit</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN 'Billing Full Payment4'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Billing Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Billing Partial Payment'
			ELSE 'Billing Partial Payment' END

		when  (( balance_paid_by = 12 ) OR 
			    (balance_paid_by = 13 ) OR 
				(balance_paid_by = 20 ) OR 
				(balance_paid_by = 21 ) ) THEN	
	
			CASE 
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN 'Invoice Full Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN 'Invoice Full Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 2) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN 'Invoice Full Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Invoice Partial'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 1) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=0) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=1) ) THEN 'Invoice Full Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 0) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=0) ) THEN '<strong>Invoice Deposit</strong>'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=1) ) THEN 'Invoice Full Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 1) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=0) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=1) ) THEN 'Invoice Partial Payment'
			WHEN ((FlagCarrier = 0) and(FlagDeposite = 2) and (FlagTarrif=2) ) THEN 'Invoice Partial Payment'
			ELSE 'Invoice Partial Payment' END
				
		END as TransType
FROM `app_notes` APN, app_order_header AOH , members M
WHERE APN.entity_id= AOH.entityid
AND   APN.sender_id = M.id
and   APN.deleted = 0
AND    (APN.`text` LIKE  '%CREDIT CARD PROCESSED%'
OR	APN.`text` LIKE  '%Shipper paid%by Credit Card%' )
AND  APN.`created` >=  DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 DAY) ,'%Y-%m-%d'),INTERVAL + 19 HOUR )
AND  APN.`created` <   DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -0 DAY) ,'%Y-%m-%d'),INTERVAL + 19 HOUR )
AND AOH.parentid = 1 
order by APN.created
";

$result = $daffny->DB->query($sql);
$total_carrier_pay = 0;							
$total_deposite =0;
$total_tariff=0;
$TransactionAmount=0;
   
if ($daffny->DB->num_rows() > 0) 
  {

      $i=1;

        while ($row = $daffny->DB->fetch_row($result)) {
			        
			 
			 if ( ($i % 2) == 0) {$bgcolor="#ffffff";} else{$bgcolor="#ffffff";}
			 
			 if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>"){
				 $bgcolor="#87CEFA";			 
			 }
			 
			 if ($row['TransType'] == "<strong>Deposit</strong>"){
				 $tmp_total_carrier_pay="0.0";			 
			 }else{				 
				$tmp_total_carrier_pay = $row['total_carrier_pay'];
			 }
			 
			  if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>"){
				 $carrier_pay_flag="1";			 
			 }else {
				 $carrier_pay_flag="0"; 
			 }
				          
			 $str .='<tr >
						 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['created'].'</td>
						 
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">' . $row['prefix'].'-'.$row['number'].'</td>
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;"> ' . $row['AssignedName'].'</td>
						 
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;"><strong>'.$row['shippercompany'].'</strong></td>
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['shipperlname'].'</td>
						 
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['TransType'].'</td>
						<td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;"><Strong>$</strong> <Strong>'.number_format(($row['TransactionAmount']), 0).'</strong></td>';	
				IF ($carrier_pay_flag=="1"){
				$str .= '<td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;font-size: 14px;"><Strong>$</strong> <Strong>'.number_format(($tmp_total_carrier_pay), 0).'</strong></td>';	
				}ELSE{	 
				$str .= '<td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;"><Strong>$</strong> '.number_format(($tmp_total_carrier_pay), 0).'</td>';
				}					 
                 $str .='	 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">$ '.number_format(($row['total_tariff']), 0).'</td>
							 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">&nbsp;'.$row['referred_by'].'</td>
                             <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['Type'].'</td>
                           </tr>';

					$total_carrier_pay += $tmp_total_carrier_pay;
					$total_deposite += $row['total_deposite'];
					$total_tariff += $row['total_tariff'];
					$TransactionAmount += $row['TransactionAmount'];
			        
					$i= $i+1;
			
			}
      
		$str .='<tr bgcolor="#297eaf" >
            <td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="center"  style="padding:3px;white-space: nowrap;" class="grid-body-left"><font color="white"><b>Total</b></font></td>
            <td align="right" style="padding:3px;"><font color="white"><b>$'. number_format($TransactionAmount, 2).'</b></font></td>
            <td  align="right" style="padding:3px;"><font color="white"><b>$'.number_format($total_carrier_pay, 2).'</b></font></td>
			 <td align="right" style="padding:3px;"><font color="white"><b>$'. number_format($total_tariff, 2).'</b></font></td>
			<td align="right"   style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
						  </tr>';
	print $str .='</table>';
 
  }
  else
  {
	  
	 $str .='<tr bgcolor="#cdcdcd" ><td align="center" colspan="11" style="padding:3px;"><h2><font color="black"><b>No Credit Card Transactions.</b></font></h2></td></tr>';
     print $str .='</table>';
  }
  
 /******************************************/
 $fname = "payreeport_".date("m-d-Y")."_".md5(mt_rand()." ".time());
 $path = ROOT_PATH . "cronjobs/pdf/" . $fname;
 getPdfNew("F", $path,$str);
 
 /***************************************/

 
$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
			$mail = new FdMailer(true);
			$mail->isHTML();
			$mail->Body = "Succsessful Credit Card Transaction Report Till " . date("m-d-Y H:i:s");
			$mail->Subject = "Succsessful Credit Card Transaction Report Till " . date("m-d-Y H:i:s");
			
			$mail->AddAddress("stefano.madrigal@gmail.com");
			$mail->AddCC("stefano.madrigal@gmail.com");
			$mail->SetFrom($daffny->cfg['info_email']);
			$mail->AddAttachment($path, "Succsessful Credit Card Transaction Report 1 Till ".date("m-d-y").".pdf");
		    $mail->Send();
			
		} catch (Exception $exc) {
			echo print "-----".$exc->getTraceAsString();
		}


    require_once("done.php");

	
 function getPdfNew($out = "D", $path = "DispatchSheet.pdf",$str) {
		
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor("CargoFlare");
		$pdf->SetSubject("Succsessful Credit Card Transaction Report Till " . date("m-d-Y H:i:s")); 
		$pdf->SetTitle("Succsessful Credit Card Transaction Report Till " . date("m-d-Y H:i:s"));
		$pdf->SetCreator("CargoFlare.com");
		$pdf->SetAutoPageBreak(true, 30);
		//$pdf->setAutoTopMargin='pad';
		$pdf->SetTopMargin(22);
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print_pdf.css")."</style>", 1);
		
	$header = '<div style="text-align: center; font-weight: bold;height:230px;"><span class="dis_heading">Succsessful Credit Card Transaction Report Till ' . date("m-d-Y H:i:s").'</span></div>';
     //$pdf->SetHTMLHeader($header,'O');
	 $pdf->SetHTMLHeader($header,'O'); 
    	
      $footer = '';
 
     
	//$pdf->SetHTMLFooter($footer,$out);	
	 
	 $pdf->writeHTML($str, 2);
			
		ob_end_clean();
		$pdf->Output($path, $out);
		if (!is_null($signPath)) {
			unlink($signPath);
		}
	}

?>