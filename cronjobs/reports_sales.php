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

print $str='<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: '.date("m-d-Y H:i:s").'</h3></td>
</tr>
</table>
';
$TotalCount=0;
$totalTariff =0;
$carrierPay=0;
$gpTotal=0;
$GPPercentage =0;
$str ='
<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                         <tr bgcolor="#297eaf" >
                             <td width="15%"  style="padding:3px;"><b><center><font color="white">Create Date</font></center></b></td>
                             <td width="10%"  style="padding:3px;"><b><center><font color="white">Day</font></center></b></td>
							 <td width="15%" align="right" style="padding:3px;"><b><font color="white">Total Orders Posted</font></b></td>
							 <td width="15%" align="right" style="padding:3px;"><b><font color="white">Total Revenue</font></b></td>
                             <td width="15%"  align="right" style="padding:3px;"><b><font color="white">Carrier Pay</font></b></td> 
							 <td width="15%" align="right" style="padding:3px;"><b><font color="white">GP</font></b></td>
							 <td width="15%" style="padding:3px;"><b><center><font color="white">GP%</font></center></b></td>
						  </tr>';
						

$where = "`created` > DATE_FORMAT(NOW() ,'%Y-%m-01')
and type = 3
group by DATE_FORMAT(`created`,'%m-%d-%Y'),
DATE_FORMAT(`created`,'%a')
LIMIT 0 , 100 ";

$rows = $daffny->DB->selectRows("DATE_FORMAT(`created`,'%m-%d-%Y') Createdate ,

DATE_FORMAT(`created`,'%a') day ,

Count(*) NoOfShipment,

sum(`total_tariff`) Tariff,

sum(`total_carrier_pay`) CarrierPay,

sum(`total_deposite`) GP,

FORMAT(sum(`total_deposite`)* 100 /
sum(`total_tariff`),2) as GPPercentage", " app_order_header ", "WHERE " . $where);

 if(!empty($rows))
  {

      $i=1;

        foreach ($rows as $row) {
			        
			 
			 if ( ($i % 2) == 0) {$bgcolor="#cdcdcd";} else{$bgcolor="#ffffff";}
		
			 $str .='<tr>
							 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['Createdate'].'</td>
                             <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['day'].'</td>
							 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['NoOfShipment'].'</td> 
							 <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['Tariff'],2,'.',',').'</td>
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['CarrierPay'],2,'.',',').'</td>
                             <td align="right" bgcolor="'.$bgcolor.'" style="padding:3px;">'.number_format($row['GP'],2,'.',',').'</td>
							 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['GPPercentage'].'</td>
							
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
   $str .='<tr bgcolor="#297eaf" >
                             <td  style="padding:3px;"><center><font color="white">Total</font></center></td>
                             <td  style="padding:3px;">&nbsp;</td>
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($TotalCount,0,'.',',').'</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($totalTariff,2,'.',',').'</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($carrierPay,2,'.',',').'</font></b></td> 
							 <td  align="right" style="padding:3px;"><b><font color="white">'.number_format($gpTotal,2,'.',',').'</font></b></td>
							 <td  align="center" style="padding:3px;"><b><font color="white">'.number_format($GPPercentage,2,'.',',').'</font></b></td>
						  </tr>';
 print $str .='</table>';
 
   
 /******************************************/
 $fname = "salesreport".date("m-d-Y")."_".md5(mt_rand()." ".time());
 $path = ROOT_PATH . "cronjobs/pdf/report" . $fname;
 getPdfNew("F", $path,$str);
 
 /***************************************/
 
$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
			$mail = new FdMailer(true);
			$mail->isHTML();
			$mail->Body = "Sales Activity Report " . date("m-d-Y H:i:s");//$str;
			$mail->Subject = "Sales Activity Report " . date("m-d-Y H:i:s");
			$mail->AddAddress("Jeff@ritewayautotransport.com");
			$mail->AddCC("nkumar@agilesoftsolutions.com");
			$mail->SetFrom($daffny->cfg['info_email']);
			$mail->AddAttachment($path, "Sales Activity Report ".date("m-d-y").".pdf");
			$mail->Send();
			
		} catch (Exception $exc) {
			echo print "-----".$exc->getTraceAsString();
		}


    require_once("done.php");

	
 function getPdfNew($out = "D", $path = "DispatchSheet.pdf",$str) {
		
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor("freight dragon");
		$pdf->SetSubject("Sales Activity Report " . date("m-d-Y H:i:s")); 
		$pdf->SetTitle("Sales Activity Report " . date("m-d-Y H:i:s"));
		$pdf->SetCreator("FreightDragon.com");
		$pdf->SetAutoPageBreak(true, 30);
		//$pdf->setAutoTopMargin='pad';
		$pdf->SetTopMargin(22);
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print_pdf.css")."</style>", 1);
		
	$header = '<div style="text-align: center; font-weight: bold;height:230px;"><span class="dis_heading">Sales Activity Report ' . date("m-d-Y H:i:s").'</span></div>';
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