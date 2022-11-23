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

require_once("init.php");

require_once("../libs/phpmailer/class.phpmailer.php");

ob_start();

set_time_limit(1000);

error_reporting(E_ALL | E_NOTICE);

require_once("init.php");


$query = "SELECT 
X.type,X.entity_id, X.number, X.prefix, X.created,X.status, X.total_tariff_stored,X.carrier_pay_stored, X.reffered_id, X.reffered_by,X.account_id,X.company_name, X.creator_id, X.contactname as creator_name,X.assigned_id,m.contactname as assign_name,X.commission,X.intial_percentage,X.residual_percentage

FROM
(
SELECT
ae.type as type,
ae.id as entity_id, ae.number as number, ae.prefix as prefix, ae.created as created,ae.status, ae.total_tariff_stored as total_tariff_stored,ae.carrier_pay_stored, ac.reffered_by as reffered_id,r.name as reffered_by,ae.account_id, aa.company_name as company_name, ae.creator_id as creator_id, m.contactname as contactname , ae.assigned_id as assigned_id,r.commission,r.intial_percentage,r.residual_percentage
FROM app_entities as ae 
INNER JOIN app_commision ac ON ae.account_id = ac.shipper_id 
LEFT JOIN app_accounts aa ON ac.shipper_id = aa.id 
LEFT JOIN members m ON ae.creator_id = m.id 
LEFT JOIN app_referrers as r ON r.id = ac.reffered_by

) as X LEFT JOIN members m ON X.assigned_id = m.id ";

$rows = $daffny->DB->query($query);
$numRows = count($rows);
//print "<br><br>numRows : ".$numRows."<br><br>";
  if(!empty($rows))
  {
	   
        $entities = array();

        $status = 3;
		
		foreach ($rows as $row) {
         print "<br><br>-".$row['entity_id']."--".$row['creator_name']."--".$row['assign_name']."--".$row['reffered_by'];
          if($row['creator_id']!=0 && $row['account_id']!=0)
		  {
			
			
			$commissionArr = array(
                        'entity_id' => $row['entity_id'],
                        'type' => $row['type'],
                        'number' => $row['prefix']."-".$row['number'],
						'status' => $row['status'],
						'created' => $row['created'],
                        'account_id' => $row['account_id'],
                        'company_name' => $row['company_name'],
                        'creator_id' => $row['creator_id'],
                        'creator_name' => $row['creator_name'],
                        'assigned_id' => $row['assigned_id'],
                        'assign_name' => $row['assign_name'],
                        'total_tariff_stored' => $row['total_tariff_stored'],
						'carrier_pay_stored' => $row['carrier_pay_stored'],
                        'reffered_id' => $row['reffered_id'],
                        'reffered_by' => $row['reffered_by'],
                        'commission' => $row['commission'],
                        'intial_percentage' => $row['intial_percentage'],
                        'residual_percentage' => $row['residual_percentage']
						
                    );
			
			$rowCommission = $daffny->DB->selectRow("id,commission_got,commission_type", "app_entity_commission", "WHERE entity_id=".$row['entity_id']." AND account_id=".$row['account_id']." AND creator_id=".$row['creator_id']." AND commission_payed=0");
						
				if (empty($rowCommission)) {
					print "---Screate----<br>";
					$rowCommissionCheck = $daffny->DB->selectRow("id", "app_entity_commission", "WHERE  account_id=".$row['account_id']." AND creator_id=".$row['creator_id']."");
					if (empty($rowCommissionCheck)) {
						
						$commissionArr['commission_got'] = $row['intial_percentage'];
						$commissionArr['commission_type'] = 0;
					}
					else
					{
						$commissionArr['commission_got'] = $row['residual_percentage'];
						$commissionArr['commission_type'] = 1;
					}
					/*print "<pre>";
					print_r($commissionArr);
					print "</pre>";
					*/
					//$commissionArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $commissionArr['commission_got'])/100;
					$daffny->DB->insert("app_entity_commission", $commissionArr);
				}
				else
				{
					   if ($rowCommission['commission_type']==0)
					      $commissionArr['commission_got'] = $row['intial_percentage'];
					   elseif($rowCommission['commission_type']==1)
					      $commissionArr['commission_got'] = $row['residual_percentage'];
						
					  //$commissionArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $rowCommission['commission_got'])/100;
					  print "---SUpdate----<br>";
					  $upd_commission_arr = $daffny->DB->PrepareSql("app_entity_commission", $commissionArr);
					  $daffny->DB->update("app_entity_commission", $upd_commission_arr, "id = '" . $rowCommission["id"] . "' AND commission_payed=0");
				}
			
            print "<br>==============================<br>";
          }

		}
  }
  
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>