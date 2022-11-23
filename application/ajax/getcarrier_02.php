<?php
	require_once("init.php");
	require_once("../../libs/anet/AuthorizeNet.php");
	$memberId = (int)$_SESSION['member_id'];
	$out = array('success' => false);
	//$out = array('success' => true,'carrierData' => "test");

	if ($memberId > 0) {
		try {

			if (isset($_POST['action'])) {
				switch ($_POST['action']) {
					case 'getroute':

						if (!isset($_POST['route_id']) || !ctype_digit((string)$_POST['route_id']) ) throw new RuntimeException("Invalid Route ID");
						$data = '<table width="100%"   cellpadding="1" cellspacing="1" class="grid"><tr class="grid-head"><td><b><p>City</p></b></td><td><b><p>State</p></b></td><td><b><p>Zip</p></b></td><td><b><p>Longitude</p></b></td><td><b><p>Latitude</p></b></td><td><b><p>Type</p></b></td></tr>';
						$sql = "select  * from app_route WHERE route_id='".$_POST['route_id']."'";
						$result = $daffny->DB->query($sql);

						if ($daffny->DB->num_rows() > 0) {
							while ($row = $daffny->DB->fetch_row($result)) {
								$typeString = "DEST";

								if($row['type'] == 'ORG')   $typeString = "ORG";
								//$defaultCarrier = 1;
								$data .= ' <tr><td bgcolor="#ffffff" style="padding:3px;">'.$row['city'].'</td><td bgcolor="#ffffff" style="padding:3px;">'.$row['state'].' </td><td bgcolor="#ffffff" style="padding:3px;">'.$row['zip'] .'</td><td bgcolor="#ffffff" style="padding:3px;"> '.$row['long'].'</td><td bgcolor="#ffffff" style="padding-left:5px;"> '.$row['lati'].'</td><td bgcolor="#ffffff" style="padding-left:5px;"> '.$typeString.'</td></tr>';
							}

						} else       $data = ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Route not found.</td></tr>';
						$out = array('success' => true,'carrierData' => $data);
						break;
					case 'getcarrierData':

						if (!isset($_POST['entity_id']) || !ctype_digit((string)$_POST['entity_id']) ) throw new RuntimeException("Invalid Entity ID");
						$entity_id = $_POST['entity_id'];
						$radius = $_POST['radius'];

						if($radius=='')  $radius=100;
						$data2 .= '<table width="100%">
			               <tr><td>&nbsp;</td></tr>
					       <tr><td align="left"><h3>Possible Carriers In Radius</h3></td></tr>
					</table>
					<table width="100%"   cellpadding="1" cellspacing="1" class="grid">
                                         <tr class="grid-head">
                                             <td><b><p>Company</p></b></td>
                                             <!----<td><b><p>Name</p></b></td>----->
                                             <td><b><p>Email</p></b></td>
                                             <td><b><p>Phone1</p></b></td>
                                             <td><b><p>Phone2</p></b></td>
                                             <td><b><p>Orders</p></b></td>
											 <td><b><p>Order Date</p></b></td>

                                          </tr>';
										  
					ini_set('max_execution_time', 300);
						$result = $daffny->DB->query("CALL fd_matching_carrier('".$_POST['ozip']."',  '".$_POST['dzip']."', ".$radius.")");

						if ($daffny->DB->num_rows() > 0) {
							while ($row = $daffny->DB->fetch_row($result)) {
								//print_r($row);
								$data2 .= ' <tr class="grid-body">
                                             <td bgcolor="#ffffff" style="padding:3px;" class="grid-body-left">'.$row['company_name'].'</td>
                                             <!---<td bgcolor="#ffffff" style="padding:3px;">'.$row['contact_name1'].' '.$row['contact_name2'].'</td>----->
                                             <td bgcolor="#ffffff" style="padding:3px;">'.$row['email'] .'</td>
                                             <td bgcolor="#ffffff" style="padding:3px;"> '.formatPhone($row['phone1']).'</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">'.formatPhone($row['phone2']) .'</td>
                                             <td bgcolor="#ffffff" style="padding-left:5px;" > '.$row['number_of_orders'].'</td>
											 <td bgcolor="#ffffff" style="padding-left:5px;" class="grid-body-right"> '.date("m/d/Y", strtotime($row['followupdate'])).'</td>
                                           </tr>';
							}

						} else       $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Currently no carrier(S)can be found at this time</td></tr>';
						$data2 .= '</table>';
						/*
			//----------------------------------------------------------------------------------
			$data2 .= '<table width="100%">
			               <tr><td>&nbsp;</td></tr>
					       <tr><td align="left"><h3>Other Possible Carriers In Radius</h3></td></tr>
					</table>
					<table width="100%"   cellpadding="1" cellspacing="1" class="grid">
                                         <tr class="grid-head">
                                             <td><b><p>Company</p></b></td>
                                             <td><b><p>Name</p></b></td>
                                             <td><b><p>Email</p></b></td>
                                             <td><b><p>Phone1</p></b></td>
                                             <td><b><p>Phone2</p></b></td>
                                          </tr>';
$sql="SELECT ac.*  from (
SELECT
	distinct ORG.carrierID
FROM
	(SELECT
		AAR.account_id as carrierID,
		AAR.id as RoutingID,
		AR.city,
		AR.id,
		AR.lati,
		AR.long,
		AR.state,
		AR.type,
		AR.zip
	FROM app_account_route as AAR inner join app_route AR
	ON AAR.id = AR.route_id
	and type = 'ORG'
	and zip = '".$_POST['ozip']."') as ORG INNER JOIN
	(SELECT
		AARD.account_id as carrierID,
		AARD.id as RoutingID,
		ARD.city,
		ARD.id,
		ARD.lati,
		ARD.long,
		ARD.state,
		ARD.type,
		ARD.zip
	FROM app_account_route as AARD inner join app_route ARD
	ON AARD.id = ARD.route_id
	and ARD.type = 'DES'
	and ARD.zip = '".$_POST['dzip']."' ) AS DST
ON ORG.RoutingID = DST.RoutingID
union
SELECT
	distinct ORG.carrierID
FROM
	(SELECT
		AAR.account_id as carrierID,
		AAR.id as RoutingID,
		AR.city,
		AR.id,
		AR.lati,
		AR.long,
		AR.state,
		AR.type,
		AR.zip
	FROM app_account_route as AAR inner join app_route AR
	ON AAR.id = AR.route_id
	and type = 'DES'
	and zip = '".$_POST['ozip']."') as ORG INNER JOIN
	(SELECT
		AARD.account_id as carrierID,
		AARD.id as RoutingID,
		ARD.city,
		ARD.id,
		ARD.lati,
		ARD.long,
		ARD.state,
		ARD.type,
		ARD.zip
	FROM app_account_route as AARD inner join app_route ARD
	ON AARD.id = ARD.route_id
	and ARD.type = 'ORG'
	and ARD.zip = '".$_POST['dzip']."' ) AS DST
ON ORG.RoutingID = DST.RoutingID
) as Z INNER JOIN app_accounts as ac
ON Z.carrierID = ac.id
";
				   $result = $daffny->DB->query($sql);
					if ($daffny->DB->num_rows() > 0) {
                     	while ($row = $daffny->DB->fetch_row($result)) {
							//$defaultCarrier = 1;
							$data2 .= ' <tr>
                                             <td bgcolor="#ffffff" style="padding:3px;">'.$row['company_name'].'</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">'.$row['first_name'].' '.$row['last_name'].'</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">'.$row['email'] .'</td>
                                             <td bgcolor="#ffffff" style="padding:3px;"> '.$row['phone1'].'</td>
                                             <td bgcolor="#ffffff" style="padding-left:5px;"> '.$row['phone2'].'</td>
                                           </tr>';
						   }
						}
						else
					       $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Assigned Carrier not found.</td></tr>';
             //-------------------------------------------------------------------------------------------------------
			$where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o
							ON o.id = e.origin_id where e.id = ".$entity_id.")";
			//print  $where;
			$rows_origin = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
						lat + (40 / 69.1) as origin_lat_front,
						lat - (40 / 69.1) as origin_lat_back,
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as origin_long_front,
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as origin_long_back', " fd_zipcode_database ", " WHERE " . $where);
			  if(!empty($rows_origin))
			  {
					///$messages = "<p>Order ID/Entity Id resposted</p><br>";
					//$entities = array();
					//print "<pre>";
					//print_r($rows_origin);
			  }
			 // print "Get Destination zip codes";
			  $where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o
							ON o.id = e.destination_id where e.id = ".$entity_id.")";
			//print  $where;
			$rows_destination = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
						lat + (40 / 69.1) as destination_lat_front,
						lat - (40 / 69.1) as destination_lat_back,
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as destination_long_front,
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as destination_long_back', " fd_zipcode_database ", " WHERE " . $where);
			  if(!empty($rows_destination))
			  {
					//print "<br><br><pre>";
					//print_r($rows_destination);
			  }
			 //print count($rows_origin)."=================".count($rows_destination);
$data .= '<table width="100%">
                           <tr><td>&nbsp;</td></tr>
					       <tr><td align="left"><h3>Other Possible Carrier(s) who transport on this route.</h3></td></tr>
					</table>
					<table width="100%"   cellpadding="1" cellspacing="1" class="grid">
                                         <tr class="grid-head">
                                             <td><b><p>Company</p></b></td>
                                             <td><b><p>Name</p></b></td>
                                             <td><b><p>Email</p></b></td>
                                             <td><b><p>Phone1</p></b></td>
                                             <td><b><p>Phone2</p></b></td>
                                             <td><b><p>Orders</p></b></td>
                                          </tr>';
			 if(count($rows_origin)>0 && count($rows_destination)>0)
			 {
				 $sql = "
				 SELECT acc.id, acc.company_name,acc.first_name,acc.last_name,acc.email,acc.phone1,acc.phone2,count(*) as number_of_orders FROM
					  app_entities en
					Left Outer Join  app_dispatch_sheets    ad
					ON en.id = ad.entity_id
					Left Outer Join app_accounts acc
					ON ad.account_id = acc.id
					INNER JOIN
					 (
						 SELECT origin.id
									from (
												  SELECT  e.id,o.zip,z.Zipcode
											FROM  app_entities e
											Left Outer join app_locations o
											ON o.id = e.origin_id
											inner join (SELECT distinct Zipcode
											FROM `fd_zipcode_database` WHERE lat <= ".$rows_origin[0]['origin_lat_front']."
																				and lat >= ".$rows_origin[0]['origin_lat_back']."
																				and vLong <= ".$rows_origin[0]['origin_long_front']."
																				and vlong >= ".$rows_origin[0]['origin_long_back'].") as z
											on o.zip = z.Zipcode
											where e.status = 9 OR e.status = 6 OR e.status = 8
											AND e.dispatched IS NOT NULL
											AND e.delivered IS NOT NULL
								) as origin
							INNER JOIN
								(
										 SELECT  e.id,o.zip,z.Zipcode
										FROM  app_entities e
										Left Outer join app_locations o
										ON o.id = e.destination_id
										inner join (SELECT distinct Zipcode
										FROM `fd_zipcode_database` WHERE lat <= ".$rows_destination[0]['destination_lat_front']."
																			and lat >= ".$rows_destination[0]['destination_lat_back']."
																			and vLong <= ".$rows_destination[0]['destination_long_front']."
																			and vlong >= ".$rows_destination[0]['destination_long_back'].") as z
										on o.zip = z.Zipcode
										where e.status = 9 OR e.status = 6 OR e.status = 8
										AND e.dispatched IS NOT NULL
										AND e.delivered IS NOT NULL
								) as destination
							   ON origin.id = destination.id
					   ) as z	on en.id = z.id
					 Where acc.company_name !=''
					 group by acc.id,acc.company_name
			";
				 //print $sql;
				// print "<br><br>-----------------Output-----------------<br><br>";
					$result = $daffny->DB->query($sql);
					//print "--------".$daffny->DB->num_rows();
					if ($daffny->DB->num_rows() > 0) {
						//$carrierData = array();
						while ($row = $daffny->DB->fetch_row($result)) {
						  		$data .= ' <tr><td bgcolor="#ffffff" style="padding:3px;">'.$row['company_name'].'</td><td bgcolor="#ffffff" style="padding:3px;">'.$row['first_name'].' '.$row['last_name'].'</td><td bgcolor="#ffffff" style="padding:3px;">'.$row['email'] .'</td><td bgcolor="#ffffff" style="padding:3px;"> '.$row['phone1'].'</td><td bgcolor="#ffffff" style="padding-left:5px;"> '.$row['phone2'].'</td><td bgcolor="#ffffff" style="padding-left:5px;"> '.$row['number_of_orders'].'</td></tr>';
						}
					}
					else
				      {
						if($defaultCarrier==0)
						$data .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Carrier not found.</td></tr>';
                      }
				}
				else
				  {
					if($defaultCarrier==0)
						$data .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Carrier not found.</td></tr>';
				  }
				$data .= '</table>';
				*/
						$data = $data1.$data.$data2;
						$out = array('success' => true,'carrierData' => $data);
						break;
					case 'getcarrier':

						if (!isset($_POST['entity_id']) || !ctype_digit((string)$_POST['entity_id']) ) throw new RuntimeException("Invalid Entity ID");
						$entity_id = $_POST['entity_id'];
						$entity = new Entity($daffny->DB);
						$entity->load((int)$entity_id);
						$defaultCarrier = 0;
						$data1 .= ' 		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td style="vertical-align:top;" valign="top" width="50%">';

						if ($ds = $entity->getDispatchSheet()) {
							$defaultCarrier = 1;
							$data1 .= '
                        <table width="100%" cellpadding="1" cellpadding="1">
                            <tr><td width="33%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. $ds->carrier_company_name .'</td></tr>
                            <tr><td ><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. $ds->carrier_address .'</td></tr>
							<tr><td ><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. $ds->carrier_city .'</td></tr>
							<tr><td ><strong>State/Zip</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"> '. $ds->carrier_state .' , '. $ds->carrier_zip .'</td></tr>
							 <tr><td ><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. $ds->carrier_contact_name .'</td></tr>
                            <tr><td ><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. formatPhone($ds->carrier_phone_1) .'</td></tr>
                            <tr><td ><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. formatPhone($ds->carrier_phone_2) .'</td></tr>
                            <tr><td ><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. formatPhone($ds->carrier_fax) .'</td></tr>
                            <tr><td ><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:' . $ds->carrier_email . '">'. $ds->carrier_email .'</a></td></tr>';
							$carrier = $entity->getCarrier();

							if ($carrier instanceof Account) {
								$data1 .= '<tr><td ><strong>Hours</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'.$carrier->hours_of_operation.'</td></tr>';
							}

							$data1 .= '<tr><td ><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'.$ds->carrier_driver_name .'</td></tr>
                            <tr><td ><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">'. formatPhone($ds->carrier_driver_phone) .'</td></tr>
                            </table>
                   ';
						} else       $data1 .= ' No carrier is assigned at this time.';
						$data1 .= '</td><td style="vertical-align:top;">
				      <table width="100%" cellpadding="1" cellpadding="1">
					    <tr><td ><h3>Order ID<b>:</b>&nbsp;'.$entity->getNumber().'</h3></td></tr>
					    <tr><td ><strong>Carrier Pay</strong><b>:</b>&nbsp;<strong><font color="red">$ '.number_format((float)$entity->carrier_pay_stored, 2, ".", ",") .'</font></strong></td></tr>
						<tr><td>
				   ';
						$payments_terms = $entity->payments_terms;

						if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){
							$payments_terms = "COD / COP";
						}


						if($payments_terms!=""){
							$data1 .= '<b>Payment Terms:</b> '. $payments_terms;
						}

						$data1 .= '</td>
						   </tr>
							<tr><td>
							<div class="attention-box import-hidden" style="width: 340px; display: block;">
				     <span style="color: #f00">ATTENTION:</span> If you would like to view other carrier(s) that match this route please click the button below.
					 <div style="text-align:center; margin:10px;">
					  <div style="float-left;width:30%;"><b>Radius: </b>
					      <select style="width:50px;" id="radius">
					        <option value="30">30</option>
							<option value="40">40</option>
							<option value="50">50</option>
							<option value="60">60</option>
							<option value="70">70</option>
							<option value="80">80</option>
							<option value="90">90</option>
							<option value="100" selected>100</option>
						</select></div>
					  <div class="form-box-buttons" style="float-left;">
						<span id="submit_button-submit-btn" style="-webkit-user-select: none;">
							<input type="submit" id="submit_button" value="View Carriers" onclick="getCarrierDataRoute('.$_POST['entity_id'].',\''.$_POST['ocity'].'\',\''.$_POST['ostate'].'\',\''.$_POST['ozip'].'\',\''.$_POST['dcity'].'\',\''.$_POST['dstate'].'\',\''.$_POST['dzip'].'\');" style="-webkit-user-select: none;">
						</span>
					</div>
				  </div>
			</div>
							</td></tr>
					   </table>
			         </td>
                   </tr>
				   <tr><td colspan="2" align="center" >
				      <div id="routeCarrierDataDiv" style="height:100px;"></div>
				   </td></tr>
                </table>';
						//----------------------------------------------------------------------------------
						$data = $data1;
						$out = array('success' => true,'carrierData' => $data);
						break;
					default:
						break;
				}

		}

		elseif (isset($_GET['action'])) {
			switch ($_GET['action']) {
				case 'getDoc':
					break;
				default:
					break;
			}

	}

}

catch (Exception $e) {

	if ($daffny->DB->isTransaction) {
		$daffny->DB->transaction('rollback');
	}

	$out['message'] = $e->getMessage();
}

}


function getAlmostUniqueHash($id, $number){
	return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

ob_clean();
echo $json->encode($out);
require_once("done.php");