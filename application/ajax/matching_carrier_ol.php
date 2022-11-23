<?php

/**
 * Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";
require_once "../../libs/anet/AuthorizeNet.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'matching-carrier':

                $sql = "SELECT ac.*  from (
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
						and zip = '" . $_POST['ozip'] . "') as ORG INNER JOIN
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
						and ARD.zip = '" . $_POST['dzip'] . "' ) AS DST
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
						and zip = '" . $_POST['ozip'] . "') as ORG INNER JOIN
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
						and ARD.zip = '" . $_POST['dzip'] . "' ) AS DST
					ON ORG.RoutingID = DST.RoutingID
					) as Z INNER JOIN app_accounts as ac
				ON Z.carrierID = ac.id";

                $result = $daffny->DB->query($sql);
                if ($daffny->DB->num_rows() > 0) {
                    while ($row = $daffny->DB->fetch_row($result)) {
                        $data1 .= '<tr>
									<td bgcolor="#ffffff" style="padding:3px;">' . $row['company_name'] . '</td>
									<td bgcolor="#ffffff" style="padding:3px;">' . $row['first_name'] . ' ' . $row['last_name'] . '</td>
									<td bgcolor="#ffffff" style="padding:3px;">' . $row['email'] . '</td>
									<td bgcolor="#ffffff" style="padding:3px;"> ' . $row['phone1'] . '</td>
									<td bgcolor="#ffffff" style="padding-left:5px;"> ' . $row['phone2'] . '</td>
								</tr>';
                    }
                } else {
                    $data1 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Carrier not found.</td></tr>';
                }

                $data .= '<table width="100%">
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
                $data .= $data1;
                $data .= '</table>';

                //print  orgin;
                $where = " `Zipcode` = " . $_POST['ozip'] . "";
                $rows_origin = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
						lat + (40 / 69.1) as origin_lat_front,
						lat - (40 / 69.1) as origin_lat_back,
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as origin_long_front,
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as origin_long_back', " fd_zipcode_database ", " WHERE " . $where);

                //destination_lat
                $where = " `Zipcode` = " . $_POST['dzip'] . "";
                $rows_destination = $daffny->DB->selectRows('distinct `Lat`, `vLong`,
						lat + (40 / 69.1) as destination_lat_front,
						lat - (40 / 69.1) as destination_lat_back,
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as destination_long_front,
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as destination_long_back', " fd_zipcode_database ", " WHERE " . $where);

                $data2 .= '<table width="100%">
								<tr><td>&nbsp;</td></tr>
								<tr><td align="left"><h3>Other Possible Carrier(s) who transport on this route.</h3></td></tr>
							</table>
							<table width="100%" cellpadding="1" cellspacing="1" class="grid">
								<tr class="grid-head">
									<td><b><p>Company</p></b></td>
									<td><b><p>Name</p></b></td>
									<td><b><p>Email</p></b></td>
									<td><b><p>Phone1</p></b></td>
									<td><b><p>Phone2</p></b></td>
									<td><b><p>Orders</p></b></td>
								</tr>';
                if (count($rows_origin) > 0 && count($rows_destination) > 0) {

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
													FROM `fd_zipcode_database` WHERE lat <= " . $rows_origin[0]['origin_lat_front'] . "

																						and lat >= " . $rows_origin[0]['origin_lat_back'] . "

																						and vLong <= " . $rows_origin[0]['origin_long_front'] . "

																						and vlong >= " . $rows_origin[0]['origin_long_back'] . ") as z
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
												FROM `fd_zipcode_database` WHERE lat <= " . $rows_destination[0]['destination_lat_front'] . "

																					and lat >= " . $rows_destination[0]['destination_lat_back'] . "

																					and vLong <= " . $rows_destination[0]['destination_long_front'] . "

																					and vlong >= " . $rows_destination[0]['destination_long_back'] . ") as z
												on o.zip = z.Zipcode
												where e.status = 9 OR e.status = 6 OR e.status = 8
												AND e.dispatched IS NOT NULL
												AND e.delivered IS NOT NULL
										) as destination

									ON origin.id = destination.id
							) as z	on en.id = z.id
							Where acc.company_name !=''
							group by acc.id,acc.company_name";

                    $result = $daffny->DB->query($sql);
                    if ($daffny->DB->num_rows() > 0) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;">' . $row['company_name'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $row['first_name'] . ' ' . $row['last_name'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $row['email'] . '</td><td bgcolor="#ffffff" style="padding:3px;"> ' . $row['phone1'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;"> ' . $row['phone2'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;"> ' . $row['number_of_orders'] . '</td></tr>';
                        }

                    } else {
                        if ($defaultCarrier == 0) {
                            $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Carrier not found.</td></tr>';
                        }
                    }
                } else {
                    if ($defaultCarrier == 0) {
                        $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Carrier not found.</td></tr>';
                    }
                }

                $data2 .= '</table>';
                $data = $data2 . $data;
                $out = array('success' => true, 'matching_carrier_data' => $data);
                break;
            case 'getcarrierprice':

                $sqlOrigin = $daffny->DB->selectRows("SELECT distinct `Lat`, `vLong`,
							lat + (30 / 69.1) as maxLati,
							lat - (30 / 69.1) as minLati,
							vLong + (30 / (69.1 * cos(lat/57.3)) ) as maxLong,
							vLong - (30 / (69.1 * cos(lat/57.3)) ) as minLong
							FROM `fd_zipcode_database` WHERE `Zipcode` = '" . $_POST['ozip'] . "'");

                if (count($sqlOrigin) > 0) {
                    $minoLati = $sqlOrigin[0]['minLati'];
                    $maxoLati = $sqlOrigin[0]['maxLati'];
                    $minoLong = $sqlOrigin[0]['minLong'];
                    $maxoLong = $sqlOrigin[0]['maxLong'];
                }

                $sqlDestination = $daffny->DB->selectRows("SELECT distinct `Lat`, `vLong`,
								lat + (30 / 69.1) as maxLati,
								lat - (30 / 69.1) as minLati,
								vLong + (30 / (69.1 * cos(lat/57.3)) ) as maxLong,
								vLong - (30 / (69.1 * cos(lat/57.3)) ) as minLong
								FROM `fd_zipcode_database` WHERE `Zipcode` = '" . $_POST['dzip'] . "'");

                if (count($sqlDestination) > 0) {
                    $mindLati = $sqlDestination[0]['minLati'];
                    $maxdLati = $sqlDestination[0]['maxLati'];
                    $mindLong = $sqlDestination[0]['minLong'];
                    $maxdLong = $sqlDestination[0]['maxLong'];
                }

                if ($_POST['carrierloadsize'] == 1) {
                    $loadsize = 'COUNT( AVT.entity_id ) ="' . $_POST['carrierloadsize'] . '"';
                } else {
                    $loadsize = 'COUNT( AVT.entity_id ) >="' . $_POST['carrierloadsize'] . '"';
                }

                $sqlpricedata = "SELECT AVP.id,
									AV.type,
									AVC.Loadsize,
									sum(AV.carrier_pay)/sum(en.distance) as UnitPrice,
									sum(en.distance) /count(*) as distance,
									sum(AV.carrier_pay)/count(*) as AvgPrice,
									min(AV.carrier_pay) as MinPrice,
									max(AV.carrier_pay) as MaxPrice
								FROM
									app_entities en inner Join
									(SELECT AVT.entity_id,
											SUM( AVT.carrier_pay ) as carrier_pay ,
											COUNT( AVT.entity_id ) as Loadsize
									FROM app_vehicles AVT
									WHERE AVT.Deleted =0
									GROUP BY AVT.entity_id
									having " . $loadsize . ") as AVC
									ON en.id = AVC.entity_id inner Join app_vehicles AV
									ON en.id = AV.entity_id
									and en.type = 3 Left Outer Join app_vehicles_types AVP
									on AV.type = AVP.name
									INNER JOIN
									(
										SELECT origin.id
													from (
															SELECT  e.id,o.zip,z.Zipcode
															FROM  app_entities e
															Left Outer join app_locations o
															ON o.id = e.origin_id
															inner join (SELECT distinct Zipcode
															FROM `fd_zipcode_database` WHERE lat <= " . $maxoLati . "
																						and lat >= " . $minoLati . "
																						and vLong <= " . $maxoLong . "
																						and vlong >= " . $minoLong . ") as z
															on o.zip = z.Zipcode
															where (e.status = 9 OR e.status = 6 OR e.status = 8)
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
														FROM `fd_zipcode_database` WHERE lat <= " . $maxdLati . "
																						and lat >= " . $mindLati . "
																						and vLong <= " . $maxdLong . "
																						and vlong >= " . $mindLong . ") as z
														on o.zip = z.Zipcode
														where (e.status = 9 OR e.status = 6 OR e.status = 8)
														AND e.dispatched IS NOT NULL
														AND e.delivered IS NOT NULL
												) as destination

											ON origin.id = destination.id
									) as z	on en.id = z.id
								where 	en.distance > 0 group by AVP.id,AV.type order by AVP.id";

                $result = $daffny->DB->query($sqlpricedata);
                $data .= '<table style="width: 100%;padding-right:10px; padding-left:10px;"><tr><td style="font-weight: 600;">Vehicle Type </td><td style="font-weight: 600;">Unit Price</td><td style="font-weight: 600;">Avg. Price</td><td style="font-weight: 600;">Min Price</td><td style="font-weight: 600;">Max Price</td></tr> ';
                if ($daffny->DB->num_rows() > 0) {
                    while ($row = $daffny->DB->fetch_row($result)) {"$ " . number_format($amount, 2);
                        $data .= '<tr><td>' . $row['type'] . '</td><td style="color: #36c007;">' . "$ " . number_format($row['UnitPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['AvgPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['MinPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['MaxPrice'], 2) . '</td></tr> ';
                    }
                    $data .= '</table>';
                    $out = array('success' => true, 'getprice_carrier_data' => $data);
                } else {
                    $data .= '<tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Price detail not found.</td></tr></table>';
                    $out = array('success' => true, 'getprice_carrier_data' => $data);
                }
                break;
            default:
			break;
        }
    }
}

ob_clean();
echo $json->encode($out);
require_once "done.php";
