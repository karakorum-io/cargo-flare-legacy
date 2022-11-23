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
 

set_time_limit(270);

require_once("init.php");

require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();
include_once("gl_functions.php");
/*
$mm = new MembersManager($daffny->DB);

$members = $mm->getMembers("`status` = 'Active' AND chmod <> 1 AND id = parent_id AND id=1");
$memberID = $members[0]->id;
*/
             
			    /*********Take Log********/
                $arr = array("post_values" => json_encode($_REQUEST));
				
				$daffny->DB->insert("app_post_url_log", $arr);
				$logID = $daffny->DB->get_insert_id();

      try { 
	  
	         
                   //print "<pre>";
					//print_r($_REQUEST);	 
                if(isset($_REQUEST) && count($_REQUEST)>0)
				{
					
					//print "---------------------------";
						//print "<br><br>-------------------------<br><br>";
                   $parsed = post_parser($_REQUEST);
				   //print "===============";
				     // print_r($parsed ); 
						
				  
				 		
						/***********************Get Assigned End *************/
		            if(isset($parsed) && count($parsed)>0)
				    {
						$is_good = true; // Unreadable flag
						$source_id = $parsed['source'];
					
					  if(trim($source_id) !='' && $source_id>0)
					  {
						   try{
							 $lsm = new Leadsource($daffny->DB);
							 $lsm->load($source_id);
						   } catch (FDException $e) {
								print "Lead source not found-: ".$source_id;
								
								$errmsg = "Lead source not found-: ".$source_id;
								$arr = array("source_id" => $source_id,"err_msg" => $errmsg);
						        $daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
								
								exit;
							}
					  }
					  else
					  {
						        $errmsg = "Lead source not found-: ".$source_id;
								$arr = array("source_id" => $source_id,"err_msg" => $errmsg);
						        $daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
						 print "Lead source not found: ".$source_id;
								exit; 
					  }
						
						$memberID = $lsm->owner_id;
						//3. create Lead
		
						$lead_arr = array(
		
							'type' => Entity::TYPE_LEAD,
		
						   // 'status' => Entity::STATUS_UNREADABLE,
							
							'parentid' => $memberID,
							
							'creator_id' => $memberID,
		
							'created' => date("Y-m-d H:i:s"),
		
							'received' => date("Y-m-d H:i:s"),
		
							'source_id' => (!is_null($source_id) ? $source_id : NULL),
		
							'assigned_id' => $memberID,
		
							'est_ship_date' => date("Y-m-d"), //Estimated Date
		
		//                    'vehicles_run' => ($parsed['vehicle_run'] == 1 ? 1 : 2),
		
							'ship_via' => ($parsed['ship_via'] == 2 ? 2 : 1), //1-Open, 2-Enclosed, 3-Driveaway
		
							'referred_by' => NULL,
		
						);
		
						$entity = new Entity($daffny->DB);
		
						$entity->create($lead_arr);
		
		
		
						//4. insert Original Email
		
						//echo "sb".$save_body;
		
						$oriemail = new LeadEmail($daffny->DB);
		
						$oriemail_arr = array(
		
							'received' => date("Y-m-d H:i:s"),
		
							'to_address' => $to,
		
							'from_address' => $from,
		
							'subject' => $subject,
		
							'body' => $save_body,
		
						);
		
						$oriemail->create($oriemail_arr);
		
						//5. Create Shipper
		
						$shipper = new Shipper($daffny->DB);
		
						$shipper_arr = array(
		
							'fname' => $parsed['first_name'],
		
							'lname' => $parsed['last_name'],
		
							'company' => "",
		
							'email' => $parsed['shipper_email'],
		
							'phone1' => str_replace("-","",$parsed['phone1']),
		
							'phone2' => str_replace("-","",$parsed['phone2']),
		
							'fax' => $parsed['fax'],
		
							'mobile' => str_replace("-","",$parsed['mobile']),
		
							'address1' => $parsed['address'],
		
							'address2' => $parsed['address2'],
		
							'city' => $parsed['city'],
		
							'state' => $parsed['state'],
		
							'zip' => $parsed['zip'],
		
							'country' => (trim($parsed['country']) == "" ? "US" : $parsed['country']),
		
						);
		
						$shipper->create($shipper_arr, $entity->id);
		
		
		
						if ($shipper_arr['fname'] == "") {
		
							$is_good = false;
		
							echo "fname<br />";
		
						}
		
		
		
						//6. Create Origin
						if ($parsed["pickup_city"] == "" || $parsed["pickup_state"] == "") {
		
							$is_good = false;
		
							$errmsg = "Pickup city or state should not empty.";
							//print $errmsg ;
							//$arr = array("err_msg" => $errmsg);
							//$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
		
						}
						
						$pickup_zip =  getzip($parsed["pickup_city"],$parsed["pickup_state"],$daffny->DB);
						
		                if($pickup_zip !='')
						   $parsed['pickup_zip'] = $pickup_zip;
						   
						$origin = new Origin($daffny->DB);
		
						$origin_arr = array(
		
							'city' => $parsed['pickup_city']
		
						, 'state' => $parsed['pickup_state']
		
						, 'zip' => $parsed['pickup_zip']
		
						, 'country' => (trim($parsed['pickup_country']) == "" ? "US" : $parsed['pickup_country'])
		
						);
		
						$origin->create($origin_arr, $entity->id);
		
						
		
						//7. Create Destination
						if ($parsed["delivery_city"] == "" || $parsed["delivery_state"] == "") {
		
							$is_good = false;
		                    $errmsg .= "  --  Delivery city or state should not empty.";
							//print $errmsg ;
							//$arr = array("err_msg" => $errmsg);
							//$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
							//echo "dcity dstate<br />";
		
						}
		                $delivery_state =  getzip($parsed["delivery_city"],$parsed["delivery_state"],$daffny->DB);
						if($delivery_state !='')
						   $parsed['delivery_zip'] = $delivery_state;
						
						$destination = new Destination($daffny->DB);
		
						$destination_arr = array(
		
							'city' => $parsed['delivery_city']
		
						, 'state' => $parsed['delivery_state']
		
						, 'zip' => $parsed['delivery_zip']
		
						, 'country' => (trim($parsed['delivery_country']) == "" ? "US" : $parsed['delivery_country'])
		
						);
		
						$destination->create($destination_arr, $entity->id);
		
						
		
		
		
						//8. Create Vehicles
		
		                if (count($parsed['vehicles']) <= 0) {
		
							$is_good = false;
		
							$errmsg .= "  --  Vehicles not found.";
							//print $errmsg ;
							//$arr = array("err_msg" => $errmsg);
							//$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
		
						}
		
						foreach ($parsed['vehicles'] as $k => $v) {
		
							//echo "Parse vehicle...<br />";
		
							$vehicle = new Vehicle($daffny->DB);
		
		
		
							if ($v['run'] == "Yes") {
		
								$v["state"] = "Operable";
		
								$v['inop'] = 0;
		
							}
		
							if ($v['run'] == "No") {
		
								$v["state"] = "Inoperable";
		
								$v['inop'] = 1;
		
							}
		                   
						     $type ='Unknown';
						    $q = $daffny->DB->selectRows("name", "app_vehicles_types", "WHERE id = '" . $v['type'] . "'  limit 0,1");
							if(!empty($q)){
							 	foreach ($q as $row) {
									$type = $row['name'];
								}
							}
							else
							{
								$type =$v['type'];
							}
		
		
							$vehicle_arr = array(
		
								'entity_id' => $entity->id,
		
								'year' => $v['year'],
		
								'make' => $v['make'],
		
								'model' => $v['model'],
		
								'type' =>  $type,//$v['type'],
		
								'vin' => $v['vin'],
		
								'lot' => $v['lot'],
		
								'plate' => $v['plate'],
		
								'color' => $v['color'],
		
								'state' => $v['state'],
		
								'inop' => $v['inop'],
		
							);
		
							$vehicle->create($vehicle_arr);
		
						}
		
						
		
						
		
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
		
							'est_ship_date' => $parsed['moving_date'],
							'avail_pickup_date' => $parsed['moving_date'],
		
							'distance' => $distance
		
						);
		
		
		
						if ($is_good) {
		                   if($memberID == 159)
						     $upd_arr['status'] = Entity::STATUS_LQUOTED;
						   else	 
							 $upd_arr['status'] = Entity::STATUS_ACTIVE;
		
							//echo "STATUS: OK " . "<br />";
							
		                    
						} else {
		
							//echo "STATUS: UNREADABLE " .$entity->id. "<br />";
							//echo "UNREADABLE";
							//$errmsg = "UNREADABLE";
							$arr = array("source_id" => $source_id,"err_msg" => $errmsg);
							$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
							
		
						}
		
		
		
						$entity->update($upd_arr);
		/*
						if ($entity->getAssigned()->getAutoQuotingSettings()->is_enabled) {
		
							$entity->autoQuoting();
		
						}
		*/
					 $entity->checkDuplicate('');
		
					 /***********************Get Assigned *************/
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
								/*****************************************/
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
						
		
						 $entity->updateHeaderTable();
						 
		                 $daffny->DB->query("COMMIT");
						 
						 $arr = array("source_id" => $source_id,"status" => 1);
						 $daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
							echo "Success";		
							//print " before: ".date("Y-m-d H:i:s");
					        //sendMails();
						    //print " after: ".date("Y-m-d H:i:s");
							
							/****************************Mail queue update **********************************/
							
						if($memberID == 159)
						{
							$From = 'noreply@americancartransporters.com'; // Please add a from email address here
							$To = $_POST['shipper_email'];  // Add a recipient
							$Subject = 'Thank you for your recent quote request from American Car Transporters';
							$Body    = "
				<p>".$_POST["first_name"].",</p>
				<p>Thank you for you interest in <strong>American Car Transporters</strong>!</p>
				<p>We show your departure date as&nbsp;".date("m/d/Y", strtotime($_POST["moving_date"]))." and your departure city ".$_POST["pickup_city"].", ".$_POST["pickup_state"]." going to ".$_POST["delivery_city"].", ".$_POST["delivery_state"]." Please give us a call at 877-238-4718 so we can quote and schedule your transport.</p>
				<ul>
					<li>
						Personalized shipping experience by assigning a personal logistics coordinator to work with you from beginning to end.&nbsp;&nbsp;</li>
					<li>
						Our experienced staff is highly trained and has a combined<strong> 25 years of knowledge and experience</strong> in the industry.&nbsp; </li>
					<li>
						We are committed to making your shipping experience a pleasant one.</li>
				</ul>
				<p>&nbsp;</p>
				<p><strong>Our prices are ALL-INCLUSIVE and cover full insurance, taxes, tolls, and door-to-door service with no hidden fees!</strong></p>
				<p><br /><strong>Get your free quote NOW 877-238-4718</span> </strong></p>
											 <br/>
							";
							
							$mailData = array(
											'entity_id' => $entity->id,	
											'member_id' => $Assigned_id,
											'fromAddress' => $From,
											'toAddress' => $To,
											'cc' => "",
											'bcc' => "",
											'subject' => $Subject,
											'body' => $Body,
											'type' => 8,
											'sent' => 0
									);
								$daffny->DB->insert('app_mail_sent', $mailData);
						}
						 /************************************************************/
						 
						 /* 
						 if ($source_id==88 ||
							 $source_id==109 || 
							 $source_id==110 )
								{
								  $posting_url = "https://manage.callshaper.com/lead/post/0b466ddbe5ce3d9463fbfa8c20997908c9c91cab";
									$fields = array();
									$fields['DeliveryCity']     = isset($parsed['delivery_city']) ? $parsed['delivery_city'] : '';
									$fields['DeliveryState']    = isset($parsed['delivery_state']) ? $parsed['delivery_state'] : '';
									$fields['EmailAddress']     = isset($parsed['shipper_email']) ? $parsed['shipper_email'] : '';
									$fields['FirstName']        = isset($parsed['first_name']) ? $parsed['first_name'] : '';
									$fields['InquiryDate']      = date('Y-m-d');
									$fields['LastName']         = isset($parsed['last_name']) ? $parsed['last_name'] : '';
									$fields['PhoneNumber']      = isset($parsed['phone1']) ? preg_replace("/[^A-Za-z0-9 ]/", '', $parsed['phone1']) : '';
									$fields['PickUpCity']       = isset($parsed['pickup_city']) ? $parsed['pickup_city'] : '';
									$fields['PickUpState']      = isset($parsed['pickup_state']) ? $parsed['pickup_state'] : '';
									$fields['PubID']            = "WEB";
									$fields['ShipOnDate']       = isset($parsed['moving_date']) ? $parsed['moving_date'] : '';
									$fields['SubID']            = "ACT";
									$fields['VehicleMake']      = isset($parsed['vehicles'][0]['make']) ? $parsed['vehicles'][0]['make'] : '';
									$fields['VehicleModel']     = isset($parsed['vehicles'][0]['model']) ? $parsed['vehicles'][0]['model'] : '';
									$fields['VehicleYear']      = isset($parsed['vehicles'][0]['year']) ? $parsed['vehicles'][0]['year'] : '';
									$fields['ExpressConsent']   = 1;
									$fields['UTMCampaign']      = isset($parsed['utm_campaign']) ? $parsed['utm_campaign'] : '';
									$fields['UTMContent']      = isset($parsed['utm_content']) ? $parsed['utm_content'] : '';
									$fields['UTMMedium']      = isset($parsed['utm_medium']) ? $parsed['utm_medium'] : '';
									$fields['UTMSource']      = isset($parsed['utm_source']) ? $parsed['utm_source'] : '';
									$fields['UTMTerm']      = isset($parsed['utm_term']) ? $parsed['utm_term'] : '';  
							
								$fields_string = "";
								foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
								rtrim($fields_string, '&');
							
								//open connection
								$ch = curl_init();
							
								//set the url, number of POST vars, POST data
								curl_setopt($ch, CURLOPT_URL, $posting_url);
								curl_setopt($ch, CURLOPT_POST, count($fields));
								curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							
								//execute post
								$result = "CallShaper: " . curl_exec($ch);
							
								//close connection
								curl_close($ch);
							}
						  */
						 /**********************************************************/
						 
				   } // parsed data
				   else
				   {
					    $errmsg = "Parsed data not found.";
						$arr = array("err_msg" => $errmsg);
						$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
				   }
						 
				}// post data
				else
				{
				     print "Post values not found.";
				    $errmsg = "Post values not found.";
					$arr = array("err_msg" => $errmsg);
					$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");
				}

		} catch (Exception $exc) {

			//echo $exc->getTraceAsString();
			
			echo "Lead not loaded, Please try again.";

			$daffny->DB->query("ROLLBACK");
			$errmsg = $exc->getTraceAsString();
			$arr = array("err_msg" => $errmsg);
			$daffny->DB->update("app_post_url_log", $arr,"id='".$logID."'");

		}

        



require_once("done.php");

function post_parser($post = array(),$dev=0)
{
     
	if($dev==1)
	{
		
    
	   $parsed = array(); 
	   $parsed['source'] = 48;
	   $parsed['first_name'] = "Neeraj23456";
	   $parsed['last_name'] = "Thakur2346";
	   $parsed['shipper_email'] = "neeraj@freightdragon.com";
	   $parsed['phone1'] = "954-668-1278";
	   $parsed['moving_date'] = "2015-10-30";
	   $parsed['city'] = "Miramar";
	   $parsed['state'] = "FL";
	   $parsed['zip'] = "33025";
	   $parsed['delivery_city'] = "Irving";
	   $parsed['delivery_state'] = "TX";
	   $parsed['delivery_zip'] = "75038";
	   $parsed['pickup_city'] = "Miramar";
	   $parsed['pickup_state'] = "FL";
	   $parsed['pickup_zip'] = "33025";
	   $parsed['pickup_country'] = "";
	   $parsed['ship_via'] = 1;
	   
	   $vehicle = array();
	   
	   $vehicleTemp = array();
	   $vehicleTemp['type'] = "Midsize Sedan";
	   $vehicleTemp['year'] = 2014;
	   $vehicleTemp['make'] = "Maxima";
	   $vehicleTemp['model'] = "Nissan";
	   $vehicleTemp['run'] = "Yes";
	   $vehicleTemp['vin'] = "0987676477";
	   $vehicleTemp['lot'] = "lot74885";
	   $vehicleTemp['plate'] = "CT83948";
	   $vehicleTemp['color'] = "Beige";
	   
	   $vehicle[0] = $vehicleTemp;
	   
	   $vehicleTemp = array();
	   $vehicleTemp['type'] = "Pickup Small";
	   $vehicleTemp['year'] = 2016;
	   $vehicleTemp['make'] = "Subaru";
	   $vehicleTemp['model'] = "Sanbar";
	   $vehicleTemp['run'] = "Yes";
	   $vehicleTemp['vin'] = "3489328489";
	   $vehicleTemp['lot'] = "lot7673";
	   $vehicleTemp['plate'] = "NJ8633";
	   $vehicleTemp['color'] = "red";
	   
	   $vehicle[1] = $vehicleTemp;

            
	   $parsed['vehicles'] = $vehicle;
	   
	}
	else
	{
		
		//------------------------------------------------------------------
		
		$parsed = array();
	
		$parsed['vehicles'] = array();
	
	
	
		$parse_vehicle1 = array(
	
			"year" => array(
	
				"Year",
	
				"Vehicle Year",
	
				"Manufactured Year",
	
				"year",
	
				"Comments / Year",
	
				"Year ",
				"vehicle_year",
				//Vehicle #2 Year:
	
				//Year2:
	
			),
	
			"make" => array(
	
				"Make",
	
				"Vehicle Make",
	
				"make",
	
				"Auto Make",
	
				"Make ",
				"vehicle_make",
				//Vehicle #2 Make:
	
				//Make2:
	
			),
	
			"model" => array(
	
				"Model",
	
				"Vehicle Model",
	
				"model",
	
				"Auto Model",
	
				"Model ",
				"vehicle_model",
				//Vehicle #2 Model:
	
				//Model2:
	
			),
	
			"type" => array(
	
				"Type",
	
				"Vehicle Type",
	
				"vehicle_type_id",
				"vehicle_type",
				"type",
				//Vehicle #2 Type:
	
				//Vehicle Type2:
	
			),
	
			"run" => array(
	
				"Running Condition",
	
				"Running condition", //YES
	
				"Vehicle Condition", //Running
	
				"Does the vehicle run?",
	
				"vehicle_condition",
	
				"Vehicle in Running Condition", //Yes
	
				"vehicle_runs",
	
				"Running Condition ",
				"run",
	
				//Vehicle #2 Running Condition:
	
				//Does the vehicle run2?:
	
			),
	
			"vin" => array(
	
				"Vehicle VIN",
				"vehicle_vin",
				"vin",
			),
	
			"lot" => array(
	
				"Vehicle Lot",
				"vehicle_lot",
				"lot",
			),
	
			"plate" => array(
	
				"Vehicle Plate",
				"vehicle_plate",
				"plate",
			),
	
			"color" => array(
	
				"Vehicle Color",
				"vehicle_color",
				"color",
			),
	
		);
		
		
		
    //Lead Body parse array

    $parse_body = array(
        "source" => array(

            "Source",

            "ID",
			"source",

        ),
        "first_name" => array(

            "Name",

            "First Name",

            "Customer Name",

            "first_name",

            "Name ",
			"First_Name",

        ),

        "last_name" => array(

            "Last Name",

            "last_name",
			"Last_Name",

        ),

        "shipper_email" => array(

            "Customer Email",

            "Email",

            "Email Address",

            "Customer E-mail",

            "email",

            "E-Mail Address",

            "Email ",
			"shipper_email",

        ),

        "phone1" => array(

            "Phone",

            "Customer Phone",

            "Phone number",

            "Daytime Phone",

            "Home Phone",

            "phone",
			"phone1",

            "Home Phone ",

        ),

        "phone2" => array(

            "Shipper Phone 2",

            "Evening Phone",

            "Work Phone",
			"phone2",

            "Alternate Phone ",

        ),

        "fax" => array(

            "Shipper Fax",

            "Fax",

        ),

        "mobile" => array(

            "Customer Cell",

            "Cell Phone",

        ),

        "address" => array(

            "Shipper Address",

        ),

        "address2" => array(

            "Shipper Address 2",

        ),

        "city" => array(

            "Origin City",

            "Pickup City",

            "Moving From",

            "pickup_city",

            "From City",

            "Origin ",

            "Origin",
			"city",

        ),

        "state" => array(

            "Origin State",

            "Pickup State",

            "pickup_state_code",

            "From State",
			"state",
			"pickup_state",

        ),

        "zip" => array(

            "Origin Zip",

            "Pickup Zip",

            "Pickup Zipcode",

            "pickup_zip",

            "From Zip Code",

            "Current Zip code ",
			"zip",

        ),

        "country" => array(

            "Shipper Country",

            "Pickup Country",
			
			"country",
			"pickup_country",

        ),

        "delivery_city" => array(

            "Destination City",

            "Delivery City",

            "Dest City",

            "Moving To",

            "dropoff_city",

            "New City",

            "Destination ",

            "Destination",
			"delivery_city",

        ),

        "delivery_state" => array(

            "Destination State",

            "Delivery State",

            "Dest State",

            "dropoff_state_code",

            "New State",
			"delivery_state",

        ),

        "delivery_zip" => array(

            "Destination Zip",

            "Delivery Zip",

            "Dest Zip",

            "dropoff_zip",
			"delivery_zip",

        ),

        "delivery_country" => array(

            "Delivery Country",

            "Deliver Country",

            "Country",
			"delivery_country",

        ),

        "moving_date" => array(

            "Move date",

            "Move Date", //01/15/2013

            "Proposed Ship Date",

            "Moving Date",

            "available_date",

            "Pickup Date",

            "estimated_ship_date",

            "Service Date",

            "Move Date ",
			"moving_date",

        ),

        "ship_via" => array(

            "Open/enclosed", //OPEN

            "Type Of Carrier", //Open

            "Trailer Type",

            "ship_via_id",

        ),
		"CF_uses_custom_fields" => array(
            "CF_uses_custom_fields",
        ),
        "broker_id" => array(
            "broker_id",
        ),
		"utm_source" => array(
            "utm_source",
        ),
		"utm_medium" => array(
            "utm_medium",
        ),
		"utm_content" => array(
            "utm_content",
        ),
		"utm_term" => array(
            "utm_term",
        ),
        "utm_campaign" => array(
            "utm_campaign",
        ),
		"type" => array(
            "type",
        ),
    );
	
	
	         $strings = $post;
			 
			//foreach ($strings as $string) {
		
		
		
				foreach ($parse_body as $key => $elements) {
		
					foreach ($elements as $value) {
		
						if ($parsed[$key] == "") {
		
							//$a = explode($value, $string);
		                    //$a = $strings[$value];
							//print "<br>--".$value;
							if (array_key_exists($value,$strings)) {
		
								$parsed[$key] = strip_tags(trim($strings[$value]));
		                        //print "<br>=====".$strings[$value];
							}
		
						}
		
					}
		
				}
		
		
				for($k=1;$k<=10;$k++)
				{
					//$counter = $k - 1;
					foreach ($parse_vehicle1 as $key => $elements) {
			
						foreach ($elements as $value) {
							
							if (!isset($parsed['vehicles'][$k][$key]) || $parsed['vehicles'][$k][$key] == "") {
								
								/*if($k==1)
								  $a = explode($value."", $string);
								else
								  $a = explode($value.$k."", $string);
								  
								  if (isset($a[1])) {
			
									$parsed['vehicles'][$k][$key] = strip_tags(trim($a[1]));
			
								}
								  */
								  //print "<br>--".$value;
								if (array_key_exists($value.$k."",$strings)) {
		
									//$parsed[$key] = strip_tags(trim($strings[$value]));
									//print "<br>=====".$strings[$value];
									$parsed['vehicles'][$k][$key] = strip_tags(trim($strings[$value.$k]));
									//print "<br>=====".$strings[$value.$k];
							    }
								
			
							}
			
						}
			
					}
					
				}
		
	//	  }  // for
		  
		  
		  /********************************************************/
	
	//print "--".$parsed['moving_date'];
		  
    //Moving date

    if ($parsed['moving_date'] != "") {

        $d_arr = explode("/", $parsed['moving_date']);

        if (count($d_arr) == 3) {

            $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int)$d_arr[1], $d_arr[2]));

        } else {
    /*
            $d_arr = explode("-", $parsed['moving_date']);

            if (count($d_arr) == 3) {

                $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int)$d_arr[1], $d_arr[2]));

            } else {

                $parsed['moving_date'] = date("Y-m-d", strtotime($parsed['moving_date']));

            }
*/
        }

    }

//print "==".$parsed['moving_date'];

    //Address

    if ($parsed['state'] == "") {

        if ($parsed['city'] != "") {

            $addr = split_address($parsed['city']);

            $parsed['city'] = $addr["city"];

            $parsed['state'] = $addr["state"];

            if ($parsed['zip'] == "") {

                $parsed['zip'] = $addr["zip"];

            }

        }

    }


    if ($parsed['delivery_state'] == "") {

        if ($parsed['delivery_city'] != "") {

            $addr = split_address($parsed['delivery_city']);

            $parsed['delivery_city'] = $addr["city"];

            $parsed['delivery_state'] = $addr["state"];

            $parsed['delivery_zip'] = $addr["zip"];

            if ($parsed['delivery_zip'] == "") {

                $parsed['delivery_zip'] = $addr["zip"];

            }

        }

    }

     //$parsed["delivery_zip"] =  getzip($parsed["delivery_city"],$parsed["delivery_state"],$db);


    //detect state

    if (strlen($parsed["state"]) > 2) {

        $parsed['state'] = state2format($parsed['state'], $db);

    }

    if (strlen($parsed["delivery_state"]) > 2) {

        $parsed['delivery_state'] = state2format($parsed['state'], $db);

    }

/***************************** GET ZIPCODE ********************************/
              
    //set pickup address

    $parsed["pickup_city"] = $parsed["city"];

    $parsed["pickup_state"] = $parsed["state"];
    $parsed["pickup_country"] = $parsed["country"];



    //Last Name

    if ($parsed['last_name'] == "") {

        if ($parsed['first_name'] != "") {

            $name = split_name($parsed['first_name']);

            $parsed['first_name'] = $name["fn"];

            $parsed['last_name'] = $name["ln"];

        }

    }



    //Ship Via

    if (in_array(strtoupper($parsed['ship_via']), array("ENCLOSED", "CLOSED"))) {

        $parsed['ship_via'] = 2;

    } else {

        $parsed['ship_via'] = 1;

    }

    for($k=1;$k<=10;$k++)
	{
		if (in_array(strtoupper($parsed['vehicles'][$k]["run"]), array("NOT RUNNING", "NO", "FALSE"))) {

			$parsed['vehicles'][$k]["run"] = "No";
	
			$parsed['vehicle_run'] = 1; //set lead as not running
	
		} else {
	
			$parsed['vehicles'][$k]["run"] = "Yes";
	
		}
		
		
		  //Strip empty vehicles

		if (isset($parsed['vehicles'][$k])) {
	
			if ($parsed['vehicles'][$k]["make"] == "" && $parsed['vehicles'][$k]["model"] == "" && $parsed['vehicles'][$k]["year"] == "") {
	
				unset($parsed['vehicles'][$k]);
	
			}
	
		}

		
	}
    
		
	}// else
	return $parsed;
}

function getzip($city,$state,$db)
{
	$zipcode = '';
	$zipcode = $db->selectValue("Zipcode", "fd_zipcode_database", "WHERE City = '".$city."' and State='".$state."' ORDER BY RAND( )  limit 0,1");
							
						 return $zipcode;  
}

function sendMails(){
	
		    $to = $_POST['shipper_email'];
		   
		    $mail = new FdMailer(true);
			$mail->isHTML();
			//$mail->From = 'admin@ritewayautotransport.com';
			//$mail->Subject = "A New Lead Was Created From www.americancartransporters.com";
			//$mail->AddAddress('mitchamericancartransporters.americancartransporters@freightdragon.com');  // Add a recipient
			//$mail->AddAddress('noreply@americancartransporters.com');               // Name is optional
			//$mail->AddAddress('neeraj@freightdragon.com');
			//$mail->AddCC("nkumar@agilesoftsolutions.com"); 
			//$mail->SetFrom($daffny->cfg['info_email']);
			
				//print "----sendmail";
			$mail->Body    = "
			
			Source: 88 
			<br>
			Ship Via: Open 
			<br>
			Move Date: ".date("m/d/Y", strtotime($_POST["moving_date"]))."
			<br>
			First Name: ".$_POST["first_name"]."
			<br>
			Last Name: ".$_POST["last_name"]."
			<br>
			Customer Email: ".$_POST["shipper_email"]."
			<br>
			Customer Phone: ".$_POST["phone1"]."
			<br>
			Origin City: ".$_POST["pickup_city"]."
			<br>
			Origin State: ".$_POST["pickup_state"]."
			<br>
			Origin Zip: 
			<br>
			Destination City: ".$_POST["delivery_city"]."
			<br>
			Destination State: ".$_POST["delivery_state"]."
			<br>
			Destination Zip: 
			<br>
			Vehicle Type: other
			<br>
			Vehicle Year: ".$_POST["year1"]."
			<br>
			Vehicle Make: ".$_POST["make1"]."
			<br>
			Vehicle Model: ".$_POST["model1"]."
			<br>
			Running Condition: Yes
			<br>
			<br>
			THIS IS AN AUTOMATED MESSAGE
			
							";
			
			//$mail->Send();
				
			// Email 2 - going to client
			//$mail->ClearAddresses(); 

            $mail->Host = 'outlook.office365.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'noreply@americancartransporters.com';                 // SMTP username
            $mail->Password = 'Password1';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to			
			$mail->SetFrom('noreply@americancartransporters.com', 'American Car Transporters'); // Please add a from email address here
			$mail->AddAddress($_POST['shipper_email']);  // Add a recipient
			$mail->Subject = 'Thank you for your recent quote request from American Car Transporters';
			$mail->Body    = "
<p>".$_POST["first_name"].",</p>
<p>Thank you for you interest in <strong>American Car Transporters</strong>!</p>
<p>We show your departure date as&nbsp;".date("m/d/Y", strtotime($_POST["moving_date"]))." and your departure city ".$_POST["pickup_city"].", ".$_POST["pickup_state"]." going to ".$_POST["delivery_city"].", ".$_POST["delivery_state"]." Please give us a call at 877-238-4718 so we can quote and schedule your transport.</p>
<ul>
	<li>
		Personalized shipping experience by assigning a personal logistics coordinator to work with you from beginning to end.&nbsp;&nbsp;</li>
	<li>
		Our experienced staff is highly trained and has a combined<strong> 25 years of knowledge and experience</strong> in the industry.&nbsp; </li>
	<li>
		We are committed to making your shipping experience a pleasant one.</li>
</ul>
<p>&nbsp;</p>
<p><strong>Our prices are ALL-INCLUSIVE and cover full insurance, taxes, tolls, and door-to-door service with no hidden fees!</strong></p>
<p><br /><strong>Get your free quote NOW 877-238-4718</span> and ask about the $100 gift card!&nbsp; </strong></p>
							 <br/>
			";
			
			// END OF EMAILS
				
			if(!$mail->Send()) {
				//header("Location: http://www.americancartransporters.com/thank-you/");
			   //exit;
			   
			}
			else{
			  //header("Location: http://www.americancartransporters.com/thank-you/");
			   //exit;
			}
	}
/*
function sendMails(){
	
		    $to = $_POST['shipper_email'];
		   
		    $mail = new FdMailer(true);
			$mail->isHTML();
			$mail->From = 'admin@ritewayautotransport.com';
			$mail->Subject = "A New Lead Was Created From www.americancartransporters.com";
			//$mail->AddAddress('mitchamericancartransporters.americancartransporters@freightdragon.com');  // Add a recipient
			//$mail->AddAddress('noreply@americancartransporters.com');               // Name is optional
			$mail->AddAddress('neeraj@freightdragon.com');
			//$mail->AddCC("nkumar@agilesoftsolutions.com"); 
			//$mail->SetFrom($daffny->cfg['info_email']);
			
				//print "----sendmail";
			$mail->Body    = "
			
			Source: 88 
			<br>
			Ship Via: Open 
			<br>
			Move Date: ".$_POST["moving_date"]."
			<br>
			First Name: ".$_POST["first_name"]."
			<br>
			Last Name: ".$_POST["last_name"]."
			<br>
			Customer Email: ".$_POST["shipper_email"]."
			<br>
			Customer Phone: ".$_POST["phone1"]."
			<br>
			Origin City: ".$_POST["pickup_city"]."
			<br>
			Origin State: ".$_POST["pickup_state"]."
			<br>
			Origin Zip: 
			<br>
			Destination City: ".$_POST["delivery_city"]."
			<br>
			Destination State: ".$_POST["delivery_state"]."
			<br>
			Destination Zip: 
			<br>
			Vehicle Type: other
			<br>
			Vehicle Year: ".$_POST["year1"]."
			<br>
			Vehicle Make: ".$_POST["make1"]."
			<br>
			Vehicle Model: ".$_POST["model1"]."
			<br>
			Running Condition: Yes
			<br>
			<br>
			THIS IS AN AUTOMATED MESSAGE
			
							";
			
			$mail->Send();
				
			// Email 2 - going to client
			$mail->ClearAddresses();  
			$mail->From = 'admin@ritewayautotransport.com'; // Please add a from email address here
			$mail->AddAddress($_POST['shipper_email']);  // Add a recipient
			$mail->Subject = 'Thank you for your recent quote request from American Car Transporters';
			$mail->Body    = "
							<p><span style='font-size:12px; font-family:Arial;'>Hi  ".$_POST["first_name"]." ".$_POST["last_name"]." </span>,<br /><br /></p>
							<p><span style='font-size:12px; font-family:Arial;'><b>Welcome to American Car Transporters.</b><br/><br/> 
							Thank you for your interest in American Car Transporters.<br/><br>We have received your information and a representative from our company will be contacting you within 48 hours.<br /><br>If you require immediate assistance, please call <b>1-877-238-4718</b>.<br><br>We look forward to speaking with you.<br/><br/> Thanking you,<br/><b>American Car Transpoters</b></span></b><br /><br /></p>
							 <br/>
			";
			
			// END OF EMAILS
				
			if(!$mail->Send()) {
				header("Location: http://www.americancartransporters.com/thank-you/");
			   exit;
			   
			}
			else{
			  header("Location: http://www.americancartransporters.com/thank-you/");
			   exit;
			}
	}*/