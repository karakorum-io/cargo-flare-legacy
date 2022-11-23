<?php
/* * ************************************************************************************************
 * Cron RepostToCd
 * Client:        FreightDragon
 * Version:        1.0
 * Date:            2011-04-26
 * Author:        C.A.W., Inc. dba INTECHCENTER
 * Address:    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:        techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

@session_start();
require_once "init.php";
require_once "../libs/phpmailer/class.phpmailer.php";
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
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon�" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: ' . date("l jS \of F Y h:i:s A") . '</h3></td>
</tr>
</table>
<div><h2>Succsessful Credit Card Transaction Report on ' . date("m-d-Y") . '</h2></div>
';
$str = '


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
$totalTariff = 0;
$carrierPay = 0;

echo $sql = "SELECT
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
	WHEN INSTR(APN.text, 'CREDIT CARD PROCESSED FOR THE AMOUNT OF') > 0 then
		Trim(replace(replace(replace(APN.text,'CREDIT CARD PROCESSED FOR THE AMOUNT OF', ''),'$ ',''),'<strong>',''))
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
				(balance_paid_by = 24 ) OR
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
$total_deposite = 0;
$total_tariff = 0;
$TransactionAmount = 0;
$transactionTotal = 0;
$transactionAmt = 0;
if ($daffny->DB->num_rows() > 0) {

    $i = 1;

    while ($row = $daffny->DB->fetch_row($result)) {
        $tAmount = explode(" ", $row['TransactionAmount']);

        if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#ffffff";}

        if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>") {
            $bgcolor = "#87CEFA";
        }

        if ($row['TransType'] == "<strong>Deposit</strong>") {
            $tmp_total_carrier_pay = "0.0";
        } else {
            $tmp_total_carrier_pay = $row['total_carrier_pay'];
        }

        if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>") {
            $carrier_pay_flag = "1";
        } else {
            $carrier_pay_flag = "0";
        }

        $str .= '<tr >
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['created'] . '</td>

						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['prefix'] . '-' . $row['number'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;"> ' . $row['AssignedName'] . '</td>

						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;"><strong>' . $row['shippercompany'] . '</strong></td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['shipperlname'] . '</td>

						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['TransType'] . '</td>
						<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;"><Strong>$</strong> <Strong>' . $tAmount[1] . '</strong></td>';
        if ($carrier_pay_flag == "1") {
            $str .= '<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;font-size: 14px;"><Strong>$</strong> <Strong>' . number_format(($tmp_total_carrier_pay), 0) . '</strong></td>';
        } else {
            $str .= '<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;"><Strong>$</strong> ' . number_format(($tmp_total_carrier_pay), 0) . '</td>';
        }
        $str .= '	 <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">$ ' . number_format(($row['total_tariff']), 0) . '</td>
							 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">&nbsp;' . $row['referred_by'] . '</td>
                             <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['Type'] . '</td>
                           </tr>';

        $total_carrier_pay += $tmp_total_carrier_pay;
        $total_deposite += $row['total_deposite'];
        $total_tariff += $row['total_tariff'];
        $TransactionAmount += $tAmount[1];

        $i = $i + 1;

    }

    $str .= '<tr bgcolor="#297eaf" >
            <td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="center"  style="padding:3px;white-space: nowrap;" class="grid-body-left"><font color="white"><b>Total</b></font></td>
            <td align="right" style="padding:3px;"><font color="white"><b>$' . number_format($TransactionAmount, 2) . '</b></font></td>
            <td  align="right" style="padding:3px;"><font color="white"><b>$' . number_format($total_carrier_pay, 2) . '</b></font></td>
			 <td align="right" style="padding:3px;"><font color="white"><b>$' . number_format($total_tariff, 2) . '</b></font></td>
			<td align="right"   style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
						  </tr>';
    print $str .= '</table>';

} else {

    $str .= '<tr bgcolor="#cdcdcd" ><td align="center" colspan="11" style="padding:3px;"><h2><font color="black"><b>No Credit Card Transactions.</b></font></h2></td></tr>';
    print $str .= '</table>';
}

/******************************************/
$sub = "Succsessful Credit Card Transaction Report Till " . date("m-d-Y H:i:s");
$fname = "payreeport_" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
$path = ROOT_PATH . "cronjobs/pdf/" . $fname;
getPdfNew("F", $path, $str, $sub);

/***************************************/

$str_dispatch = '<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#C4C4C4">
                         <tr bgcolor="#297eaf" >
                             <td  style="padding:3px;"><b><center><font color="white">Dispatch Date</font></center></b></td>
                             <td  style="padding:3px;"><b><center><font color="white">Day</font></center></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">Total Loads Dispatched</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">Total Revenue</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">COD/COP</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">Net Revenue</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">Carrier Pay</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">GP</font></b></td>
							 <td  style="padding:3px;"><b><center><font color="white">GP%</font></center></b></td>
						  </tr>';

$TotalCount = 0;
$totalTariff = 0;
$carrierPay = 0;
$gpTotal = 0;
$GPPercentage = 0;
$Totalcod_cop = 0;
$TotalNetRevenue = 0;

$where = "dispatched > DATE_FORMAT(NOW() ,'%Y-%m-01')
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
sum(
	Case
	When `balance_paid_by` = 2 then  	`total_carrier_pay`
	When `balance_paid_by` = 3 then  	`total_carrier_pay`
	When `balance_paid_by` = 16 then    `total_carrier_pay`
	When `balance_paid_by` = 17 then 	`total_carrier_pay`
	When `balance_paid_by` = 8 then 	`total_carrier_pay`
	When `balance_paid_by` = 9 then 	`total_carrier_pay`
	When `balance_paid_by` = 18 then 	`total_carrier_pay`
	When `balance_paid_by` = 19 then 	`total_carrier_pay`
	else 0
	end ) as cod_cop,
	sum(`total_tariff`) - sum(
	Case
	When `balance_paid_by` = 2 then  	`total_carrier_pay`
	When `balance_paid_by` = 3 then  	`total_carrier_pay`
	When `balance_paid_by` = 16 then    `total_carrier_pay`
	When `balance_paid_by` = 17 then 	`total_carrier_pay`
	When `balance_paid_by` = 8 then 	`total_carrier_pay`
	When `balance_paid_by` = 9 then 	`total_carrier_pay`
	When `balance_paid_by` = 18 then 	`total_carrier_pay`
	When `balance_paid_by` = 19 then 	`total_carrier_pay`
	else 0
	end  ) as NetRevenue,
FORMAT(sum(`total_deposite`)* 100 /
sum(`total_tariff`),2) as GPPercentage", " app_order_header ", "WHERE " . $where);

if (!empty($rows)) {

    $i = 0;

    foreach ($rows as $row) {

        if (($i % 2) == 0) {$bgcolor = "#cdcdcd";} else { $bgcolor = "#ffffff";}

        $str_dispatch .= '<tr>
							 <td width="15%" align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['dispatched'] . '</td>
                             <td width="10%" align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['day'] . '</td>
							 <td width="10%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['NoOfShipment'] . '</td>
							 <td width="15%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['Tariff'], 2, '.', ',') . '</td>
							 <td width="10%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['cod_cop'], 2, '.', ',') . '</td>
							 <td width="10%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['NetRevenue'], 2, '.', ',') . '</td>
                             <td width="10%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['CarrierPay'], 2, '.', ',') . '</td>
                             <td width="10%" align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['GP'], 2, '.', ',') . '</td>
							 <td width="10%" align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['GPPercentage'], 2, '.', ',') . '</td>

                           </tr>';

        $Totalcod_cop += $row['cod_cop'];
        $TotalNetRevenue += $row['NetRevenue'];
        $TotalCount += $row['NoOfShipment'];
        $totalTariff += $row['Tariff'];
        $carrierPay += $row['CarrierPay'];
        $gpTotal += $row['GP'];
        $GPPercentage += $row['GPPercentage'];

        $i = $i + 1;

    }
    if ($GPPercentage != 0) {
        $GPPercentage = $GPPercentage / $i;
    }

}

$str_dispatch .= '<tr bgcolor="#297eaf" >
							 <td  style="padding:3px;"><b><center><font color="white">Total</font></center></b></td>
                             <td  style="padding:3px;">&nbsp;</td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($TotalCount, 0, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($totalTariff, 2, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($Totalcod_cop, 2, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($TotalNetRevenue, 2, '.', ',') . '</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($carrierPay, 2, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($gpTotal, 2, '.', ',') . '</font></b></td>
							 <td  align="center" style="padding:3px;"><b><font color="white">' . number_format($GPPercentage, 2, '.', ',') . '</font></b></td>
						  </tr>';

print $str_dispatch .= '</table>';

/******************************************/
$sub = "Dispatch Activity Report  " . date("m-d-Y H:i:s");
$fname = "dispatchreport" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
$path_dispatch = ROOT_PATH . "cronjobs/pdf/report" . $fname;
getPdfNew("F", $path_dispatch, $str_dispatch, $sub);

/***************************************/

$TotalCount = 0;
$totalTariff = 0;
$carrierPay = 0;
$gpTotal = 0;
$GPPercentage = 0;
$str_sales = '

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
and parentid = 1
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

if (!empty($rows)) {

    $i = 0;

    foreach ($rows as $row) {

        if (($i % 2) == 0) {$bgcolor = "#cdcdcd";} else { $bgcolor = "#ffffff";}

        $str_sales .= '<tr>
							 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['Createdate'] . '</td>
                             <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['day'] . '</td>
							 <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['NoOfShipment'] . '</td>
							 <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['Tariff'], 2, '.', ',') . '</td>
                             <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['CarrierPay'], 2, '.', ',') . '</td>
                             <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">' . number_format($row['GP'], 2, '.', ',') . '</td>
							 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['GPPercentage'] . '</td>

                           </tr>';

        $TotalCount += $row['NoOfShipment'];
        $totalTariff += $row['Tariff'];
        $carrierPay += $row['CarrierPay'];
        $gpTotal += $row['GP'];
        $GPPercentage += $row['GPPercentage'];

        $i = $i + 1;
    }
    if ($GPPercentage != 0) {
        $GPPercentage = $GPPercentage / $i;
    }

}
$str_sales .= '<tr bgcolor="#297eaf" >
                             <td  style="padding:3px;"><center><font color="white">Total</font></center></td>
                             <td  style="padding:3px;">&nbsp;</td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($TotalCount, 0, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($totalTariff, 2, '.', ',') . '</font></b></td>
                             <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($carrierPay, 2, '.', ',') . '</font></b></td>
							 <td  align="right" style="padding:3px;"><b><font color="white">' . number_format($gpTotal, 2, '.', ',') . '</font></b></td>
							 <td  align="center" style="padding:3px;"><b><font color="white">' . number_format($GPPercentage, 2, '.', ',') . '</font></b></td>
						  </tr>';
print $str_sales .= '</table>';

/******************************************/
$sub = "Sales Activity Report  " . date("m-d-Y H:i:s");
$fname = "salesreport" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
$path_sales = ROOT_PATH . "cronjobs/pdf/report" . $fname;
getPdfNew("F", $path_sales, $str_sales, $sub);

/***************************************/

/**********************************************************************************************************************/
//date_default_timezone_set('America/New_York');
print $str = '<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
<tr>
		 <td><div><img src="http://freightdragon.com/images/logo_cp.png" alt="Freight Dragon�" width="210" height="75"></div></td>
		 <td align="right"><h3>Run Date: ' . date("l jS \of F Y h:i:s A") . '</h3></td>
</tr>
</table>
<div><h2>Payment Recieved Report on ' . date("m-d-Y") . '</h2></div>
';
$str = '


<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#cdcdcd" style="font-size: 11px;">
						<tr bgcolor="#297eaf" >
                             <td width="12%" rowspan="2"  style="padding:3px;"><b><center><font color="white">Trans Date /<br>Trans By</font></center></b></td>
							<td width="10%" rowspan="2" align="left" style="padding:3px;"><b><center><font color="white">Order# /<br> Assigned To</font></center></b></td>


							 <td width="20%" colspan="2" align="center" bgcolor="#1E90FF" style="padding:3px;"><b><center><font color="white">Shipper</font></center></b></td>
							 <td width="8%" rowspan="2" align="left" style="padding:3px;"><b><font color="white">Trans Type</font></b></td>
                             <td width="9%" align="left"  rowspan="2" style="padding:3px;"><b><font color="white">Payment Via</font></b></td>
							 <td width="7%" rowspan="2"  align="right" style="padding:3px;"><b><font color="white">Deposit</font></b></td>
							 <td width="8%" rowspan="2" align="right" style="padding:3px;"><b><font color="white">Trans Amount</font></b></td>
							 <td width="10%" rowspan="2" align="right" style="padding:3px;"><b><font color="white">Carrier Pay</font></b></td>
							 <td width="7%" rowspan="2" align="right" style="padding:3px;"><b><font color="white">Tariff</font></b></td>
                             <td width="9%" rowspan="2" align="left"  style="padding:3px;"><b><font color="white">Source</font></b></td>
						  </tr>
                         <tr >
							 <td  width="12%" bgcolor="#1E90FF" align="left" style="padding:3px;"><b><center><font color="white">Company</font></center></b></td>
							 <td  width="8%" bgcolor="#1E90FF"  align="left" style="padding:3px;"><b><center><font color="white">Last Name</font></center></b></td>

						  </tr>';

$TotalCount = 0;
$totalTariff = 0;
$carrierPay = 0;

$sql = "SELECT
A.`entity_id`,
DATE_FORMAT(A.`created` ,'%m/%d/%Y %h:%i %p') as created,
A.`amount` as TransactionAmount,
A.`method`,
case A.`method`
when 1 then 'ACH'
when 2 then 'Company Check'
when 3 then 'credit card'
when 4 then 'Money Order'
when 5 then 'Personal Check'
when 6 then 'Wire Transfer'
else 'Company Check' end as methodType,
CONCAT(B.`prefix` , '-', B.`number`) as OrderNumber,
B.`AssignedName`,
B.`total_carrier_pay`,
B.`total_deposite`,
B.`total_tariff`,
C.`contactname` as TransBy,
Case
When (length(A.`transaction_id`) = 0) then
		Case
			when A.`check` IS NOT NULL  then A.`check`
			when A.`check` IS NULL  then ''
			else A.`check`
		end
When (A.`transaction_id` IS NOT NULL) then A.`transaction_id`
When (A.`transaction_id` IS NULL ) then
		Case
			when A.`check` IS NOT NULL  then A.`check`
			when A.`check` IS NULL  then ''
			else A.`check`
		end
 end as CheckNo,
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

		END as TransType,
    B.`shippercompany`,
    B.`shipperfname`,
    B.`shipperlname`,
	B.`referred_by`

FROM  `app_payments` A inner join `app_order_header` B
on A.`entity_id` = B.`entityid` inner join `members`  C
on A.`entered_by` = C.`id`
WHERE  A.`fromid` = 2
AND  A.`toid` =1
AND  A.`created` >=  DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -1 DAY) ,'%Y-%m-%d'),INTERVAL + 19 HOUR )
AND  A.`created` <   DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL -0 DAY) ,'%Y-%m-%d'),INTERVAL + 19 HOUR )
and  A.`deleted` = 0
AND  A.`method` not in (9,3)
AND  B.`parentid` = 1
order by A.`created`
";

$result = $daffny->DB->query($sql);
$total_carrier_pay = 0;
$total_deposite = 0;
$total_tariff = 0;
$TransactionAmount = 0;

if ($daffny->DB->num_rows() > 0) {

    $i = 1;

    while ($row = $daffny->DB->fetch_row($result)) {

        if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#ffffff";}

        if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>") {
            $bgcolor = "#87CEFA";
        }

        if ($row['TransType'] == "<strong>Deposit</strong>") {
            $tmp_total_carrier_pay = "0.0";
        } else {
            $tmp_total_carrier_pay = $row['total_carrier_pay'];
        }

        if ($row['TransType'] == "<strong><font color=black>Full Payment</font></strong>") {
            $carrier_pay_flag = "1";
        } else {
            $carrier_pay_flag = "0";
        }

        $str .= '<tr >
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['created'] . '/<br>' . $row['TransBy'] . '</td>

						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['OrderNumber'] . ' /<br>' . $row['AssignedName'] . '</td>


						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;"><strong>' . $row['shippercompany'] . '</strong></td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['shipperlname'] . '</td>

						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['TransType'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['methodType'] . '</td>
						<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;font-size: 14px;"><strong>$ ' . number_format(($row['total_deposite']), 0) . '</strong></td>
						<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;"><Strong>$</strong> <Strong>' . number_format(($row['TransactionAmount']), 0) . '</strong></td>';
        if ($carrier_pay_flag == "1") {
            $str .= '<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">$ ' . number_format(($tmp_total_carrier_pay), 0) . '</td>';
        } else {
            $str .= '<td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">$ ' . number_format(($tmp_total_carrier_pay), 0) . '</td>';
        }
        $str .= '	 <td align="right" bgcolor="' . $bgcolor . '" style="padding:3px;">$ ' . number_format(($row['total_tariff']), 0) . '</td>
							 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">&nbsp;' . $row['referred_by'] . '</td>
                           </tr>';

        $total_carrier_pay += $tmp_total_carrier_pay;
        $total_deposite += $row['total_deposite'];
        $total_tariff += $row['total_tariff'];
        $TransactionAmount += $row['TransactionAmount'];

        $i = $i + 1;

    }

    $str .= '<tr bgcolor="#297eaf" >
            <td align="right" style="padding:3px;">&nbsp;</td>

			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="right" style="padding:3px;">&nbsp;</td>
			<td align="center"  style="padding:3px;white-space: nowrap;" class="grid-body-left"><font color="white"><b>Total</b></font></td>
			<td align="right" style="padding:3px;font-size: 14px;"><font color="white"><b>$' . number_format($total_deposite, 2) . '</b></font></td>
            <td align="right" style="padding:3px;"><font color="white"><b>$' . number_format($TransactionAmount, 2) . '</b></font></td>
            <td  align="right" style="padding:3px;"><font color="white"><b>$' . number_format($total_carrier_pay, 2) . '</b></font></td>
			 <td align="right" style="padding:3px;"><font color="white"><b>$' . number_format($total_tariff, 2) . '</b></font></td>
			<td align="right"   style="padding:3px;">&nbsp;</td>
			</tr>';
    print $str .= '</table>';

} else {

    $str .= '<tr bgcolor="#cdcdcd" ><td align="center" colspan="11" style="padding:3px;"><h2><font color="black"><b>No Check Transactions.</b></font></h2></td></tr>';
    print $str .= '</table>';
}

/******************************************/
$sub = "Payment Recieved Report Till " . date("m-d-Y H:i:s");
$fname = "checkpayreport_" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
$path_check = ROOT_PATH . "cronjobs/pdf/" . $fname;
getPdfNew("F", $path_check, $str, $sub);

/***************************************/

$str = '<div><h2>Status update Report on ' . date("m-d-Y") . '</h2></div>';

$str .= '<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#000000" style="font-size: 12px;">
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

$sql = "SELECT
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
WHERE A.`created` >=  DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d'),INTERVAL + 0 HOUR )
and  A.`created`  <    DATE_ADD(DATE_FORMAT(DATE_ADD(NOW(), INTERVAL - 0 DAY) ,'%Y-%m-%d'),INTERVAL + 24 HOUR )
and (A.`text` LIKE  '%has marked this order as DELIVERED on%'
OR   A.`text` LIKE  '%has marked this order as PICKED UP on %'
OR   A.`text` LIKE  '%marked this order as PICKED UP.%'
OR   A.`text` LIKE  '%marked this order as DELIVERED.%'
OR   A.`text` LIKE  '%has marked this order(%) as DELIVERED on%'
OR   A.`text` LIKE  '%has marked this order(%) as PICKED UP on%')
order by A.`created`
";

$result = $daffny->DB->query($sql);

if ($daffny->DB->num_rows() > 0) {

    $i = 1;

    while ($row = $daffny->DB->fetch_row($result)) {

        if (($i % 2) == 0) {$bgcolor = "#ffffff";} else { $bgcolor = "#cfcfcf";}

        $str .= '<tr >
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $i . '</td>
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['UpdateTime'] . '</td>
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['OrderNumber'] . '</td>
						 <td align="center" bgcolor="' . $bgcolor . '" style="padding:3px;background-color:' . $bgcolor . '">' . $row['ordCreated'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['Assigned'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;"> ' . $row['UpdatedBy'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['RecordDate'] . '</td>
						 <td align="Left" bgcolor="' . $bgcolor . '" style="padding:3px;">' . $row['CarrierCompany'] . '</td>
                         </tr>';

        $i = $i + 1;

    }

    print $str .= '</table>';

} else {

    $str .= '<tr bgcolor="#cdcdcd" ><td align="center" colspan="11" style="padding:3px;"><h2><font color="black"><b>No Status update done.</b></font></h2></td></tr>';
    print $str .= '</table>';
}

/******************************************/
$sub = "Status update Report on " . date("m-d-Y H:i:s");
$fname = "status_update_report_" . date("m-d-Y") . "_" . md5(mt_rand() . " " . time());
$path_status_update = ROOT_PATH . "cronjobs/pdf/" . $fname;
getPdfNew("F", $path_status_update, $str, $sub);

/***************************************/
/*********************************************************************************************************************/

$_SESSION['iamcron'] = false;

//send mail to Super Admin

try {
    $mail = new FdMailer(true);
    $mail->isHTML();
    $mail->Body = "End of the Day reports on  " . date("m-d-Y H:i");
    $mail->Subject = "End of the Day reports on   " . date("m-d-Y H:i:s");

    $mail->AddAddress("shahrukhusmaani@gmail.com");
    //$mail->AddCC("admin@ritewayautotransport.com");
    $mail->SetFrom($daffny->cfg['info_email']);
    $mail->AddAttachment($path, "Succsessful Credit Card Transaction Report Till " . date("m-d-y") . ".pdf");
    $mail->AddAttachment($path_dispatch, "Dispatch Activity Report " . date("m-d-y") . ".pdf");
    $mail->AddAttachment($path_sales, "Sales Activity Report " . date("m-d-y") . ".pdf");
    $mail->AddAttachment($path_check, "Payment Recieved Report Till " . date("m-d-y") . ".pdf");
    $mail->AddAttachment($path_status_update, "Status update Report on " . date("m-d-y") . ".pdf");
    //$mail->Send();

} catch (Exception $exc) {
    echo print "-----" . $exc->getTraceAsString();
}

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
    //$pdf->setAutoTopMargin='pad';
    $pdf->SetTopMargin(22);
    $pdf->writeHTML("<style>" . file_get_contents(ROOT_PATH . "styles/application_print_pdf.css") . "</style>", 1);

    $header = '<div style="text-align: center; font-weight: bold;height:230px;"><span class="dis_heading">' . $sub . '</span></div>';
    //$pdf->SetHTMLHeader($header,'O');
    $pdf->SetHTMLHeader($header, 'O');

    $footer = '';

    //$pdf->SetHTMLFooter($footer,$out);

    $pdf->writeHTML($str, 2);

    ob_end_clean();
    $pdf->Output($path, $out);
    if (!is_null($signPath)) {
        unlink($signPath);
    }
}
