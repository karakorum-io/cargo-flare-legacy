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

$PrevMonths = -0;
$NextMonths = $PrevMonths + 1;


print $str =  '<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: '.date("l jS \of F Y h:i:s A").'</h3></td>
</tr>
</table>
<div><h2>Dispatch Activity Report '.date("m-d-Y").'</h2></div>
';
 
 $str_dispatch=' 
<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                         <tr bgcolor="#297eaf" >
                             <td  style="padding:3px;"><b><center><font color="white">Dispatch Date</font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white">Day</font></center></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">Total Loads Dispatched</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">Total Revenue</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">Carrier Pay</font></b></td> 
							 <td  align="right" style="padding:3px;"><b><font color="white">GP</font></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">GP%</font></center></b></td>
						  </tr>';
						  
$TotalCount = 0;							
$totalTariff =0;
$carrierPay=0;
$gpTotal=0;
$GPPercentage =0;


$where = "dispatched >= DATE_ADD( DATE_FORMAT(NOW() ,'%Y-%m-01') , INTERVAL ".$PrevMonths." MONTH )
and dispatched < DATE_ADD( DATE_FORMAT(NOW() ,'%Y-%m-01') , INTERVAL ".$NextMonths." MONTH )
and type = 3
and parentid = 1
group by DATE_FORMAT(`dispatched`,'%m-%d-%Y'),
DATE_FORMAT(`dispatched`,'%a') 
LIMIT 0 , 100 ";

$rows = $daffny->DB->selectRows("DATE_FORMAT(`dispatched`,'%m-%d-%Y') dispatched ,

DATE_FORMAT(`dispatched`,'%a') day ,

Count(*) NoOfShipment,

sum(`total_tariff`) Tariff,

sum(`total_carrier_pay`) CarrierPay,

sum(`total_deposite`) GP,

FORMAT(sum(`total_deposite`)* 100 /
sum(`total_tariff`),2) as GPPercentage", " app_order_header ", "WHERE " . $where);

 if(!empty($rows))
  {

      $i=0;

        foreach ($rows as $row) {
			        
			 
			 if ( ($i % 2) == 0) {$bgcolor="#cdcdcd";} else{$bgcolor="#ffffff";}
		
			 $str_dispatch .='<tr>
							 <td width="15%" align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['dispatched'].'</td>
                             <td width="10%" align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['day'].'</td>
							 <td width="15%" align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['NoOfShipment'].'</td> 
							 <td width="15%" align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">' . number_format($row['Tariff'],2,'.',',').'</td>
                             <td width="15%" align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['CarrierPay'],2,'.',',').'</td>
                             <td width="15%" align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['GP'],2,'.',',').'</td>
							 <td width="15%" align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['GPPercentage'],2,'.',',').'</td>
							
                           </tr>';
					$TotalCount += $row['NoOfShipment'];
			        $totalTariff +=$row['Tariff'];
                    $carrierPay +=$row['CarrierPay'];
					$gpTotal +=$row['GP'];
					$GPPercentage +=$row['GPPercentage'];
					
					$i= $i+1;
			
			}
       if($GPPercentage !=0)
		$GPPercentage = $GPPercentage/$i;
  }
  
  $str_dispatch .='<tr bgcolor="#297eaf" >
							 <td  style="padding:3px;"><b><center><font color="white">Total</font></center></b></td>
                             <td  style="padding:3px;">&nbsp;</td>
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($TotalCount,0,'.',',').'</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($totalTariff,2,'.',',').'</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($carrierPay,2,'.',',').'</font></b></td> 
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($gpTotal,2,'.',',').'</font></b></td>
							 <td  align="center" style="padding:3px;"><b><font color="white">'.number_format($GPPercentage,2,'.',',').'</font></b></td>
						  </tr>';
						

print  $str_dispatch .='</table>';
 
 /******************************************/
 $sub = "Monthly Dispatch Activity Report  " . date("m-d-Y H:i:s");
 $fname = "monthlydispatchreport".date("m-d-Y")."_".md5(mt_rand()." ".time());
 $path_dispatch = ROOT_PATH . "cronjobs/pdf/report" . $fname;
 getPdfNew("F", $path_dispatch,$str_dispatch,$sub);
 
 /***************************************/
  
 
 
$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
			$mail = new FdMailer(true);
			$mail->isHTML();
			$mail->Body = "End of the Day reports on  " . date("m-d-Y H:i");
			$mail->Subject = "End of the Day reports on   " . date("m-d-Y H:i:s");
			
			
			$mail->AddAddress("stefano.madrigal@gmail.com");
			//$mail->AddCC("stefano.madrigal@gmail.com");
			$mail->SetFrom($daffny->cfg['info_email']);
			$mail->AddAttachment($path_dispatch, "Monthly Dispatch Activity Report ".date("m-d-y").".pdf");
		    $mail->Send();
			
		} catch (Exception $exc) {
			echo print "-----".$exc->getTraceAsString();
		}


    require_once("done.php");

	
 function getPdfNew($out = "D", $path = "DispatchSheet.pdf",$str,$sub) {
		
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor("CargoFlare");
		$pdf->SetSubject($sub); 
		$pdf->SetTitle($sub);
		$pdf->SetCreator("cargoflare.com");
		$pdf->SetAutoPageBreak(true, 30);
		//$pdf->setAutoTopMargin='pad';
		$pdf->SetTopMargin(22);
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print_pdf.css")."</style>", 1);
		
	$header = '<div style="text-align: center; font-weight: bold;height:230px;"><span class="dis_heading">' . $sub.'</span></div>';
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