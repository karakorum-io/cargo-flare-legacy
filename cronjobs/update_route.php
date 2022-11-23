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

//require_once("../libs/phpmailer/class.phpmailer.php");

ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);

require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);



//$em = new EntityManager($daffny->DB);

//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB(now(),INTERVAL 1 DAY),'%Y-%m-%d') ";
//$where = "e.`type` = 3 AND e.`deleted` = 0 AND e.`status` = 4 and date_format(e.posted,'%Y-%m-%d') = date_format(DATE_SUB('2014-10-05',INTERVAL 1 DAY),'%Y-%m-%d') order by e.id desc limit 60,90"; 

//$where =  "e.id = 4348";

$where = " type = 3 and status >5 and status !=3  and route=0  limit 0,500";
print  $where;

$rows = $daffny->DB->selectRows('id', Entity::TABLE , "WHERE " . $where);

  if(!empty($rows))
  {

        foreach ($rows as $row) {
         print "<br>----------".$row['id']."---------<br>";
            $entity = new Entity($daffny->DB);
            $entity->load($row['id']);
			
			$update_arr=array("route"=>1);
			$entity->update($update_arr);
	//print "========";
			if ($ds = $entity->getDispatchSheet()) {
				if($ds->account_id !=0 )
				{
					$origin = $entity->getOrigin();
					//print "----1";
					$destination = $entity->getDestination();
				   //print "---2";
					print "<br>".$i."--".$entity->id."--".$entity->status."--".$origin->city.",".$origin->state.",".$origin->zip."|".$destination->city.",".$destination->state.",".$destination->zip."<br>";
					
							   
								$radius = 30;
							   
								$AccountRouteObj = new AccountRoute($daffny->DB);
								//print "-----------";
								$AccountRouteArr = array();
								$AccountRouteArr['type'] = "ORG";
								$AccountRouteArr['account_id'] = $ds->account_id;
								$AccountRouteArr['origin'] = $origin->city.",".$origin->state.",".$origin->zip;
								$AccountRouteArr['destination'] = $destination->city.",".$destination->state.",".$destination->zip;
								$AccountRouteArr['ocity'] = $origin->city;
								$AccountRouteArr['ostate'] = $origin->state;
								$AccountRouteArr['ozip'] = $origin->zip;
								$AccountRouteArr['dcity'] = $destination->city;
								$AccountRouteArr['dstate'] = $destination->state;
								$AccountRouteArr['dzip'] = $destination->zip;
								$AccountRouteArr['radius'] = $radius;
								
								$AccountRouteObj->create($AccountRouteArr);
								$AccountRouteID = $AccountRouteObj->id;
								
								print $ds->account_id." account id : ".$AccountRouteID;
								print "<br>==============================<br>";
								
								//$RouteObj = new Route($daffny->DB);
								
								//$RouteObj->routeMapping($AccountRouteID,"ORG",$origin->city, $origin->state, $origin->zip, $radius);
								//$RouteObj->routeMapping($AccountRouteID,"DES",$destination->city, $destination->state, $destination->zip, $radius);
							
							  $rows_origin = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
								
								lat + ('.$radius.' / 69.1) as origin_lat_front,
								
								lat - ('.$radius.' / 69.1) as origin_lat_back,
								
								vLong + ('.$radius.' / (69.1 * cos(lat/57.3)) ) as origin_long_front,
								
								vLong - ('.$radius.' / (69.1 * cos(lat/57.3)) ) as origin_long_back', " fd_zipcode_database ", " WHERE Zipcode ='".$origin->zip."'");
					
								  if(!empty($rows_origin))
								  {
										 $sql = "INSERT INTO app_route (route_id,type,city,	state, zip, `long`, lati)
												 SELECT 
														".$AccountRouteID.",'ORG',City,State,Zipcode,vLong,Lat
												 FROM 	fd_zipcode_database
												 WHERE 	lat <= ".$rows_origin[0]['origin_lat_front']."
													and lat >= ".$rows_origin[0]['origin_lat_back']."
													and vLong <= ".$rows_origin[0]['origin_long_front']."
													and vlong >= ".$rows_origin[0]['origin_long_back']."";
													
										$result = $daffny->DB->query($sql);
									
								  }
								  
								  
						$rows_destination = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
								
								lat + ('.$radius.' / 69.1) as destination_lat_front,
								
								lat - ('.$radius.' / 69.1) as destination_lat_back,
								
								vLong + ('.$radius.' / (69.1 * cos(lat/57.3)) ) as destination_long_front,
								
								vLong - ('.$radius.' / (69.1 * cos(lat/57.3)) ) as destination_long_back', " fd_zipcode_database ", " WHERE Zipcode ='".$destination->zip."'");
					
								  if(!empty($rows_destination))
								  {
										$sql = "INSERT INTO app_route (route_id,type,city,	state, zip, `long`, lati)
												 SELECT 
														".$AccountRouteID.",'DES',City,State,Zipcode,vLong,Lat
												 FROM 	fd_zipcode_database
												 WHERE 	lat <= ".$rows_destination[0]['destination_lat_front']."
													and lat >= ".$rows_destination[0]['destination_lat_back']."
													and vLong <= ".$rows_destination[0]['destination_long_front']."
													and vlong >= ".$rows_destination[0]['destination_long_back']."";
													
										$result = $daffny->DB->query($sql);
									
								  }
				} //$ds->account_id !=0
			}
			
			       
        }


    
		
		$numRows = sizeof($rows);
		print "numRows : ".$numRows;

    
  }
  
  

$_SESSION['iamcron'] = false;



//send mail to Super Admin

    require_once("done.php");

?>