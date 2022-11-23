<?php



/* * ************************************************************************************************

 * Get Leads

 *

 * Client:		FreightDragon

 * Version:		1.0

 * Date:			2011-12-14

 * Author:		C.A.W., Inc. dba INTECHCENTER

 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

 * E-mail:	techsupport@intechcenter.com

 * CopyRight 2011 FreightDragon. - All Rights Reserved

 * ************************************************************************************************** */
 



require_once("init.php");



//require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();
include_once("gl_functions.php");

set_time_limit(0);
ini_set('memory_limit', '350000M');
ini_set('upload_max_filesize', '428M');
ini_set('post_max_size', '428M');
ini_set('max_input_time', 8000000000);

//$where = "  status=0 and id > 38901 and id < 15000 "; 
//$where = "  status=0 and `err` =  ' ' limit 0,100";
$where = "  status=0  limit 0,200 ";
print  $where;
$i=0;
$rowsExternal = $daffny->DB->selectRows('*', " external_data ", "WHERE ".$where);
$insertStr = "";
if(!empty($rowsExternal))
 {
	          
			    //print "<pre>";print_r($rowsExternal);print "</pre>";
			
                foreach ($rowsExternal as $rowExt) 
				{
					
					 try { 
				 		
						/***********************Get Assigned End *************/
		            
						$is_good = true; // Unreadable flag
						
						/*
						
					    $source_id = 88;
						if($rowExt['lead_source'] !='')
						  $source_id = $rowExt['lead_source'];
						  
					     try{
							 $lsm = new Leadsource($daffny->DB);
							 $lsm->load($source_id);
						   } catch (FDException $e) {
								print "Lead source not found-: ".$source_id;
								exit;
							}
						
						$memberID = $lsm->owner_id;
						*/
						$memberID = 159;
						$Assigned_id = $memberID;
						//print $rowExt['estimated_ship_date']." ".$rowExt['time_quoted'];
						
						$created =  $rowExt['date_quoted']." ".$rowExt['time_quoted'];
						$created =  date("Y-m-d H:i:s", strtotime($created));
						
						 $estimated_ship_date = date("Y-m-d", strtotime($rowExt['estimated_ship_date']));
						 
						 $vehicle_run = 0;
						 if (in_array(strtoupper($rowExt['vehicle_runs']), array("NOT RUNNING", "NO", "FALSE"))) 
                           	$vehicle_run = 1; //set lead as not running
					     else
						    $vehicle_run = 2;
					
						 $ship_via = 0;
						 if (in_array(strtoupper($rowExt['ship_via']), array("ENCLOSED", "CLOSED"))) 
                            	$ship_via = 2;
						 else 
							   $ship_via = 1;
						   
						//3. create Lead
						$lead_arr = array(
		
							'type' => Entity::TYPE_LEAD,
		
						   // 'status' => Entity::STATUS_UNREADABLE,
							
							'parentid' => $memberID,
							
							'creator_id' => $memberID,
		
							'created' => $created,//date("Y-m-d H:i:s"),
		
							'received' => $created,//date("Y-m-d H:i:s"),
		
							'source_id' => (!is_null($source_id) ? $source_id : NULL),
		
							'assigned_id' => $memberID,
		
							'est_ship_date' =>  $estimated_ship_date, //Estimated Date
		
		                    'vehicles_run' => $vehicle_run,
		
							'ship_via' => $ship_via, //1-Open, 2-Enclosed, 3-Driveaway
		
							'referred_by' => $rowExt['referrer'],
							'information' => $rowExt['number'],
							'buysell' => $rowExt['assigned_to'],
							'buysell_days' => $rowExt['before_quote'],
							
							
		
						);
		
						$entity = new Entity($daffny->DB);
		
						$entity->create($lead_arr);
		
		
		
					
						//5. Create Shipper
		
						$shipper = new Shipper($daffny->DB);
		
						$shipper_arr = array(
		
							'fname' => $rowExt['first_name'],
		
							'lname' => $rowExt['last_name'],
		
							'company' => $rowExt['company_name'],
		
							'email' => $rowExt['email'],
		
							'phone1' => $rowExt['phone'],
		
							'phone2' => $rowExt['phone_2'],
							'fax' => $rowExt['fax'],
		
							'mobile' => $rowExt['cell'],
		
							'address1' => $rowExt['address'],
		
							'address2' => $rowExt['address2'],
		
							'city' => $rowExt['city'],
		
							'state' => $rowExt['state'],
		
							'zip' => $rowExt['zip'],
		
							'country' => (trim($rowExt['country']) == "" ? "US" : $rowExt['country']),
		
						);
		
						$shipper->create($shipper_arr, $entity->id);
		
						
						//6. Create Origin
						
						
						 $pickup_zip = $rowExt['pickup_zip'];
		                //if($rowExt['pickup_zip'] =='')
						   //$pickup_zip =  getzip($rowExt["pickup_city"],$rowExt["pickup_state"],$daffny->DB);
						    
						   
						$origin = new Origin($daffny->DB);
		
						$origin_arr = array(
		
							'city' => $rowExt['pickup_city']
		
						, 'state' => $rowExt['pickup_state']
		
						, 'zip' => $pickup_zip
		
						, 'country' => "US"
		
						);
		
						$origin->create($origin_arr, $entity->id);
		
						
						//7. Create Destination
		                	
						$delivery_zip = $rowExt['dropoff_zip'];
		                //if($rowExt['dropoff_zip'] =='')
						   //$delivery_zip =  getzip($rowExt["dropoff_city"],$rowExt["dropoff_state"],$daffny->DB);
						
						$destination = new Destination($daffny->DB);
		
						$destination_arr = array(
		
							'city' => $rowExt["dropoff_city"]
		
						, 'state' => $rowExt["dropoff_state"]
		
						, 'zip' => $delivery_zip
		
						, 'country' => "US" //(trim($rowExt['dropoff_country']) == "" ? "US" : $rowExt['dropoff_country'])
		
						);
		
						$destination->create($destination_arr, $entity->id);
						
						//-----------------------------------
						for($k=1;$k<=10;$k++)
	                    {
							 $tariff = "tariff".$k;
							 $deposit = "deposit".$k;
							 $year = "year".$k;
							 $make = "make".$k;
							 $model = "model".$k;
							 $type = "type".$k;
							if(
							    (isset($rowExt[$tariff]) && $rowExt[$tariff] >0) &&
								(isset($rowExt[$deposit]) && $rowExt[$deposit] >0) &&
								(isset($rowExt[$year]) && $rowExt[$year] !='') 
							)
							{
								$vehicle = new Vehicle($daffny->DB);
								
								$type ='Unknown';
								$q = $daffny->DB->selectRows("name", "app_vehicles_types", "WHERE id = '" . $rowExt[$type] . "'  limit 0,1");
								if(!empty($q)){
									foreach ($q as $rowV) {
										$type = $rowV['name'];
									}
								}
								else
								{
									$type =$rowExt[$type];
								}
								
								$vehicle_arr = array(
		
								'entity_id' => $entity->id,
		
								'year' => $rowExt[$year],
		
								'make' => $rowExt[$make],
		
								'model' => $rowExt[$model],
		
								'type' =>  $type,
								
								'tariff' => $rowExt[$tariff],
		
								'carrier_pay' => ($rowExt[$tariff] - $rowExt[$deposit]),
		
								'deposit' =>  $rowExt[$deposit],
								
								
							);
		
							$vehicle->create($vehicle_arr);
								
							}  // end if
						}// end for
		
		
						$distance = RouteHelper::getRouteDistance($origin->city.",".$origin->state.",".$origin->country, $destination->city.",".$destination->state.",".$destination->country);
		
						if (!is_null($distance)) {
		
						$distance = RouteHelper::getMiles((float)$distance);
		
						} else {
		
						$distance = 'NULL';
		
						}
		
		
						
		
						//9. Update Lead
		
						$upd_arr = array(
		
						   // 'assigned_id' => $Assigned_id,//$memberID,
		
							'email_id' => $oriemail->id,
		
							'shipper_id' => $shipper->id,
		
							'origin_id' => $origin->id,
		
							'destination_id' => $destination->id,
		
							'est_ship_date' => $estimated_ship_date,
							'avail_pickup_date' => $estimated_ship_date,
		
							'distance' => $distance
		
						);
		
		
		
						if($memberID == 159)
						     $upd_arr['status'] = Entity::STATUS_LQUOTED;
						else	 
							 $upd_arr['status'] = Entity::STATUS_ACTIVE;
							 
		
						$entity->update($upd_arr);
		 
						//if ($entity->getAssigned()->getAutoQuotingSettings()->is_enabled) {
		
							//$entity->autoQuoting();
		
						//}
		
					// $entity->checkDuplicate('');
					 
		        /*
					 //----------------------Get Assigned --------------
						$Assigned_id = $memberID;
						
						$qCheck = $daffny->DB->selectRows("*", "app_defaultsettings_ass", "WHERE owner_id = '" . $memberID . "' and leadsource_id='" . $source_id . "' limit 0,1");
							
						   if(!empty($qCheck)){
							   
								 $qCheckReinitiate = $daffny->DB->selectRows("*", "app_defaultsettings_ass", "WHERE owner_id = '" . $memberID . "' and leadsource_id='" . $source_id . "' and status=0 order by ord limit 0,1");
							
								if(empty($qCheckReinitiate)){
											  // 1 print "<br>- Reinitiate------<br>";
											  $batch_count = 0;
											 
											   $arr = array( "batch_count" => $batch_count);
											
											$daffny->DB->update("app_leadsources_queue", $arr,"  leadsource_id='" . $source_id . "' ");
											
											$status =0;
											// print "member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' <br><br>";
											$arrLeadSource = array("status" => 0);
											
											$daffny->DB->update("app_defaultsettings_ass", $arrLeadSource,"leadsource_id='" . $source_id . "' ");
											  //print "----------------------------------------<br><br>";
								}
						   }
						   else{
							   $Assigned_id = $memberID;
						   }
						
							//print "WHERE owner_id = '" . $memberID . "' and leadsource_id='" . $source_id . "' and status=0 order by ord limit 0,1<br>";
							$q = $daffny->DB->selectRows("*", "app_defaultsettings_ass", "WHERE owner_id = '" . $memberID . "' and leadsource_id='" . $source_id . "' and status=0 order by ord limit 0,1");
							
						   if(!empty($q)){
							   
							//while ($row = $daffny->DB->fetch_row($q)) {
							foreach ($q as $row) {
								//print_r($row);
								//print "<br>";
								
								$StatusLeadSource = $row['status'];
								$BatchLeadSource = $row['batch'];
								
								 //print $BatchLeadSource." Status ".$StatusLeadSource;
								  $rows = $daffny->DB->selectRows('*', " app_leadsources_queue ", "WHERE member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' ");
								  if(!empty($rows))
								  {
									  foreach ($rows as $rowQueue) {
										// 1 print "<br> ---assigned to : ".$rowQueue['member_id']."<br>";
										if (!$entity->duplicate) {
											   $batch_count = $rowQueue['batch_count']+1;
											   $arr = array( "batch_count" => $batch_count);
											   $daffny->DB->update("app_leadsources_queue", $arr," member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' ");
											
										}
										else
										  $batch_count = $rowQueue['batch_count'];
										   
											$status =0;
											// 1 print "".$batch_count."- BatchLeadSource: ".$BatchLeadSource."<br>";
											if(  $batch_count >=  $BatchLeadSource)
											  $status =1;
											  //print "member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' <br><br>";
											$arrLeadSource = array("status" => $status);
											
											$daffny->DB->update("app_defaultsettings_ass", $arrLeadSource,"member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' ");
											
											$Assigned_id = $row['member_id'];
											
									  }
								  }
								  else
								  {
									  //foreach ($rows as $rowQueue) {
										 $batch_count = 1;
											 // 1 print "<br>- assigned to : ".$row['member_id']."<br>";
											   $arr = array("leadsource_id" => $source_id
												, "member_id" => (int) $row['member_id']
												, "batch_count" => $batch_count
												
											);
											
											$daffny->DB->insert("app_leadsources_queue", $arr);
											
											$status =0;
											// 1 print "BatchLeadSource: ".$BatchLeadSource."<br>";
											if( $BatchLeadSource <=1)
											  $status =1;
											 // print "member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' <br><br> ";
											$arrLeadSource = array("status" => $status);
											
											$daffny->DB->update("app_defaultsettings_ass", $arrLeadSource,"member_id='".$row['member_id']."' and leadsource_id='" . $source_id . "' ");
											
											$Assigned_id = $row['member_id'];
									//  }
									  
								   }	
			
								 //print "----------------------------------------<br><br>";
				
							}
						  }
						
						*/
					  
						
						if ($entity->duplicate) {
							//print "<br>- Duplicate Lead -<br>";
								$upd_arr_new = array(
									'assigned_id' => $Assigned_id,
									'status' => Entity::STATUS_LDUPLICATE
								 );       
								
							}
						   else{
								$upd_arr_new = array(
								  'assigned_id' => $Assigned_id
								 );
								
								
						   }
						   
						   //print_r($upd_arr_new);
						 $entity->update($upd_arr_new);
						
						 $entity->getVehicles(true);
		
						 $entity->updateHeaderTable();
						 
		                 $daffny->DB->query("COMMIT");
						 
						 $arr = array("status" => 1);
						 $daffny->DB->update("external_data", $arr,"id='".$rowExt['id']."'");
							
							echo "<br>".$rowExt['number']." Success : ".$entity->id;		
						$i++;	
					     
					  } catch (Exception $exc) {
                            echo "<br>".$rowExt['number']." id:".$rowExt['id']."<br>";
							echo "<br>".$exc->getTraceAsString()."<br>";
							echo "Lead not loaded, Please try again.";
				             
							$daffny->DB->query("ROLLBACK");
							
							$arr = array("err" => $exc->getTraceAsString());
							$daffny->DB->update("external_data", $arr,"id='".$rowExt['id']."'");
				
						}

						 
				}// data LOOP
				

		
} //validate data
else
  print "External data not found.";


require_once("done.php");


print "<br><br>Num Of Record inserted : ".$i;


function getzip($city,$state,$db)
{
	$zipcode = '';
	$zipcode = $db->selectValue("Zipcode", "fd_zipcode_database", "WHERE City = '".$city."' and State='".$state."' ORDER BY RAND( )  limit 0,1");
							
						 return $zipcode;  
}
