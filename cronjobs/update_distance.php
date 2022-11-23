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
//require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();

set_time_limit(0);
ini_set('memory_limit', '350000M');
ini_set('upload_max_filesize', '428M');
ini_set('post_max_size', '428M');
ini_set('max_input_time', 8000000000);

//$where = "  status=0 and id > 38901 and id < 15000 "; 
//$where = "  status=0 and `err` =  ' ' limit 0,100";
$where = "  distance=0 and type=1 and tmp_status=0 and origin_id is not null and destination_id is not null order by id desc limit 0,120";
//$where = "  id=39305 ";

print  $where;
$i=0;
$rowsExternal = $daffny->DB->selectRows('id', " app_entities ", "WHERE ".$where);
$insertStr = "";
if(!empty($rowsExternal))
 {
	          
			    //print "<pre>";print_r($rowsExternal);print "</pre>";
			
                foreach ($rowsExternal as $rowExt) 
				{
					
					 try { 
				 		
						/***********************Get Assigned End *************/
						$entity = new Entity($daffny->DB);
                        $entity->load($rowExt['id']);
		                $origin = $entity->getOrigin();
	                    $destination = $entity->getDestination();
						
						if($origin->city!='' && $destination->city!='')
						{
							print "<br>".$origin->city.",".$origin->state.",".$origin->country."|". $destination->city.",".$destination->state.",".$destination->country;
			
							$distance = RouteHelper::getRouteDistance($origin->city.",".$origin->state.",".$origin->country, $destination->city.",".$destination->state.",".$destination->country);
							 //OVER_QUERY_LIMIT You have exceeded your daily request quota for this API. We recommend registering for a key at the Google Developers Console: https://console.developers.google.com/apis/credentials?project=_ 
							
							if (!is_null($distance)) {
				
							$distance = RouteHelper::getMiles((float)$distance);
			
							} else {
			
							$distance = 'NULL';
			
							}
							//echo "<br> dis : ".$distance;
			
			
							 $upd_arr_new = array(
									  'distance' => $distance,
									  'tmp_status' => 1
									 );
									
							   //print_r($upd_arr_new);
							 $entity->update($upd_arr_new);
							
							 $entity->updateHeaderTable();
							 
								
								echo "<br>".$rowExt['id']." Success : ".$distance."<br>------------<br>";	
								
						$i++;	
						
						}
					     
					  } catch (Exception $exc) {

							echo $exc->getTraceAsString();
							echo "<br>".$rowExt['id']." Distance Not Updated.<br>===================<br>";
							
						}

						 
				}// data LOOP
				

		
} //validate data
else
  print "Data not found.";


require_once("done.php");


print "<br><br>Num Of Record updated : ".$i;


function getzip($city,$state,$db)
{
	$zipcode = '';
	$zipcode = $db->selectValue("Zipcode", "fd_zipcode_database", "WHERE City = '".$city."' and State='".$state."' ORDER BY RAND( )  limit 0,1");
							
						 return $zipcode;  
}
