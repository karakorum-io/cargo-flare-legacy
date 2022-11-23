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

//date_default_timezone_set('America/New_York');
print $str = '<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon™" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: '.date("l jS \of F Y h:i:s A").'</h3></td>
</tr>
</table>
<div><h2>Status update Report on '.date("m-d-Y").'</h2></div>';

$str='<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style="font-size: 12px;">
						<tr bgcolor="#297eaf" >
                             <td width="5%" style="padding:3px;"><b><center><font color="white">Seq No</font></center></b></td>
							 <td width="15%" align="left" style="padding:3px;"><b><center><font color="white">Status Change Date</font></center></b></td>
							 <td width="10%" align="left" style="padding:3px;"><b><center><font color="white">Order #</font></center></b></td>
							 <td width="15%" align="left" style="padding:3px;"><b><center><font color="white">Order created</font></center></b></td>
							 <td width="15%" align="left" style="padding:3px;"><b><font color="white">Assigned Name</font></b></td>	
							 <td width="10%" align="left" style="padding:3px;"><b><font color="white">Updated By</font></b></td>								 
							 <td width="15%" align="left" style="padding:3px;"><b><font color="white">Update Date</font></b></td>
							 <td width="15%" align="left" style="padding:3px;"><b><font color="white">Carrier Company</font></b></td>
						  </tr>';

		$sql ="SELECT 
 A.`created` as UpdateTime,
concat(B.`prefix`,'-',B.`number`) as OrderNumber,
B.`created` as ordCreated,
ASG.`contactname` as Assigned,
 case
when M.`id` = 1 then 'Carrier' 
else M.`contactname` end UpdatedBy,
case 
when INSTR(A.`text`, 'PICKED UP') > 0 then concat('Picked Up',' ', B.`actual_pickup_date`)
when INSTR(A.`text`, 'DELIVERED') > 0 then concat('Delivered',' ',B.`delivery_date`)
else '' end RecordDate,
B.`carrier_id` as carrierID,
C.`company_name` as CarrierCompany
FROM  `app_notes` A inner join `app_entities` B
ON 	A.entity_id = B.id inner join `app_accounts` C
on  B.`carrier_id` = C.id inner join `members` M
on  A.`sender_id` = M.`id` inner join `members` ASG
on 	B.`assigned_id` = ASG.`id`
and B.parentid = 1
WHERE B.`created` >=  DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 2 DAY) ,'%Y-%m-%d'),INTERVAL + 0 HOUR )
and  B.`created`  <    DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d'),INTERVAL + 24 HOUR )
and (A.`text` LIKE  '%has marked this order as DELIVERED on%'
OR   A.`text` LIKE  '%has marked this order as PICKED UP on %'
OR   A.`text` LIKE  '%marked this order as PICKED UP.%'
OR   A.`text` LIKE  '%marked this order as DELIVERED.%'
OR   A.`text` LIKE  '%has marked this order(%) as DELIVERED on%'
OR   A.`text` LIKE  '%has marked this order(%) as PICKED UP on%')
order by A.`created`
";

$result = $daffny->DB->query($sql);
   
if ($daffny->DB->num_rows() > 0) 
  {

      $i=1;

        while ($row = $daffny->DB->fetch_row($result)) {
			        
			 
			 if ( ($i % 2) == 0) {$bgcolor="#ffffff";} else{$bgcolor="#cfcfcf";}
			 
			   
			 $str .='<tr >
						 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$i.'</td>
						 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['UpdateTime'].'</td>	
						 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['OrderNumber'].'</td>	 
						 <td align="center" bgcolor="'.$bgcolor.'" style="padding:3px;background-color:'.$bgcolor.'">'.$row['ordCreated'].'</td>							 					 
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">' . $row['Assigned'].'</td>
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;"> ' . $row['UpdatedBy'].'</td>
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['RecordDate'].'</td>
						 <td align="Left" bgcolor="'.$bgcolor.'" style="padding:3px;">'.$row['CarrierCompany'].'</td>
                         </tr>';

					$i= $i+1;
			
			}
      
		print $str .='</table>';
 
  }
  else
  {
	  
	 $str .='<tr bgcolor="#cdcdcd" ><td align="center" colspan="11" style="padding:3px;"><h2><font color="black"><b>No Status update done.</b></font></h2></td></tr>';
     print $str .='</table>';
  }
  
 /******************************************/
 $sub = "Status update Report on " . date("m-d-Y H:i:s");
 $fname = "payreeport_".date("m-d-Y")."_".md5(mt_rand()." ".time());
 $path = ROOT_PATH . "cronjobs/pdf/" . $fname;
 getPdfNew("F", $path,$str,$sub);
 
 /***************************************/
  
$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
			$mail = new FdMailer(true);
			/*$mail->isHTML();
			$mail->Body = "End of the Day reports on  " . date("m-d-Y H:i");
			$mail->Subject = "End of the Day reports on   " . date("m-d-Y H:i:s");
			
			$mail->AddAddress("neeraj@freightdragon.com");
			//$mail->AddCC("neeraj@freightdragon.com");
			$mail->SetFrom($daffny->cfg['info_email']);
			$mail->AddAttachment($path, "Succsessful Credit Card Transaction Report Till ".date("m-d-y").".pdf");
			$mail->AddAttachment($path_dispatch, "Dispatch Activity Report ".date("m-d-y").".pdf");
			$mail->AddAttachment($path_sales, "Sales Activity Report ".date("m-d-y").".pdf");
			$mail->AddAttachment($path_check, "Payment Recieved Report Till ".date("m-d-y").".pdf");
		    //$mail->Send();
			*/
		} catch (Exception $exc) {
			echo print "-----".$exc->getTraceAsString();
		}


    require_once("done.php");

	
 function getPdfNew($out = "D", $path = "DispatchSheet.pdf",$str,$sub) {
		
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor("freight dragon");
		$pdf->SetSubject($sub); 
		$pdf->SetTitle($sub);
		$pdf->SetCreator("FreightDragon.com");
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