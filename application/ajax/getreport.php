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

function validateDate($val, $fieldName = "date")
{
    if ($val != "") {
        $date_tmp = explode("/", $val);
        if (count($date_tmp) != 3 || !checkdate($date_tmp[0], $date_tmp[1], $date_tmp[2])) {
            $err[] = "<strong>$fieldName</strong> is incorrect. (Use format: dd/mm/yyyy)";
        } else {
            return $date_tmp[2] . "-" . $date_tmp[0] . "-" . $date_tmp[1];
        }
    }
    return "NULL";
}

function getTimePeriod($type)
{

    $d1 = date("Y-m-d 00:00:00");
    $d2 = date("Y-m-d 23:59:59");

    switch ($type) {
        case "1":
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
            $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, date("Y")));
            break;
        case "2":
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
            $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y")));
            break;
        case "3":
            $startmth = date("m") - 3 - ((date("m") - 1) % 3);
            $year = date("Y");
            if ($startmth == -2) {
                $startmth += 12;
                $year -= 1;
            }
            $endmth = $startmth + 2;
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, $startmth, 1, $year));
            $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, $endmth, date("t", mktime(0, 0, 0, $endmth, 1, $year)), $year));
            break;
        case "4":
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y")));
            $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
            break;
        case "5":
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, 2011));
            $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
            break;
        default:
            $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, 2011));
            $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
    }

    return array(
        "0" => $d1
        , "1" => $d2,
    );
}

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);

if ($memberId > 0) {
    try {

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {

                case 'getcarrierTariff':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid member ID");
                    }

                    $id = $_POST['id'];

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3

                    ";

                    $sql = "SELECT  entityid FROM `app_order_header` WHERE `deleted` = 0 AND `carrier_id` =" . $id . " " . $where;
                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table >
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';

                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " <br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>


											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }

                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getcarrierDispatched':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
                        AND `status` <> 3
                    ";

                    $sql = "SELECT entityid FROM `app_order_header` WHERE `deleted` =0 AND `carrier_id` =" . $id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);

                    $data = '<table >
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "
					      <br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
					             <td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
					             <td style="padding:3px;"></td>
								 <td style="padding:3px;"></td>
					             <td style="padding:3px;"></td>
					             <td style="padding:3px;"></td>
								 <td style="padding:3px;">
								 <table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
								<tr>
									<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
									<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
								</tr>
                                </table></td>
								<td style="padding:3px;">
								</td>
					   		</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;

                case 'getshipperorders':
                    $source = '';
                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $assigned_id = $_POST['assigned_id'];
                    $source = $_POST['source'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $where = " AND  A.`parentid` =" . getParentID() . " AND A.STATUS <>3 AND A.`type` =3 AND A.account_id <>0 AND `created` >= '" . $start_date . "' AND `created` <= '" . $end_date . "'";
                    $sql = "Select A.account_id,
						A.assigned_id,
						A.entityid,
						B.first_name as shipperfname,
						B.last_name as shipperlname,
						B.company_name as shippercompany,
						B.email as shipperemail,
						B.phone1 as shipperphone1,
						B.phone1_ext as shipperphone1_ext,
						B.phone2 as shipperphone2,
						B.phone2_ext as shipperphone2_ext,
						case
						when A.source_name is null then
						A.referred_by
						else A.source_name end as `source_name`,
						A.AssignedName
						FROM app_order_header A inner join app_accounts B
						on A.account_id = B.id
						WHERE A.`account_id` =" . $id . " and case when A.source_name is null then A.referred_by else A.source_name end LIKE '" . $source . "' and A.assigned_id=" . $assigned_id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';

                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " <br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td><b>' . $row['shippercompany'] . '</b><br/>' . $row['shipperfname'] . ' ' . $row['shipperlname'] . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
					   				</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);
                    break;
                case 'getcarrierinvoices':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
                        AND `FlagTarrif` =2

                    ";

                    $sql = "SELECT  entityid FROM `app_order_header` WHERE `deleted` =0 AND `carrier_id` =" . $id . " " . $where;
                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table >
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "
					      <br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span>

						<br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
												</tr>
											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';
                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getopeninvoices':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $assigned_id = $_POST['assigned_id'];
                    $source = $_POST['source'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $where = "
                        AND  A.`parentid` =" . getParentID() . " AND A.STATUS <>3 AND A.`type` =3 AND A.account_id <>0
                        AND `FlagTarrif` =2	AND `created` >= '" . $start_date . "' AND `created` <= '" . $end_date . "'

                    ";

                    $sql = "Select A.account_id,
						A.assigned_id,
						A.entityid,
						B.first_name as shipperfname,
						B.last_name as shipperlname,
						B.company_name as shippercompany,
						B.email as shipperemail,
						B.phone1 as shipperphone1,
						B.phone1_ext as shipperphone1_ext,
						B.phone2 as shipperphone2,
						B.phone2_ext as shipperphone2_ext,
						case
						when A.source_name is null then
						A.referred_by
						else A.source_name end as `source_name`,
						A.AssignedName
						FROM app_order_header A inner join app_accounts B
						on A.account_id = B.id
						WHERE A.`account_id` =" . $id . " and case when A.source_name is null then A.referred_by else A.source_name end LIKE '" . $source . "' and A.assigned_id=" . $assigned_id . " " . $where;
                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td><b>' . $row['shippercompany'] . '</b><br/>' . $row['shipperfname'] . ' ' . $row['shipperlname'] . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getpayments':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $assigned_id = $_POST['assigned_id'];
                    $source = $_POST['source'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $where = "
                        AND  A.`parentid` =" . getParentID() . " AND A.STATUS <>3 AND A.`type` =3 AND A.account_id <>0
                        AND `FlagTarrif` !=2 AND `created` >= '" . $start_date . "' AND `created` <= '" . $end_date . "'

                    ";

                    $sql = "Select A.account_id,
						A.assigned_id,
						A.entityid,
						B.first_name as shipperfname,
						B.last_name as shipperlname,
						B.company_name as shippercompany,
						B.email as shipperemail,
						B.phone1 as shipperphone1,
						B.phone1_ext as shipperphone1_ext,
						B.phone2 as shipperphone2,
						B.phone2_ext as shipperphone2_ext,
						case
						when A.source_name is null then
						A.referred_by
						else A.source_name end as `source_name`,
						A.AssignedName
						FROM app_order_header A inner join app_accounts B
						on A.account_id = B.id
						WHERE A.`account_id` =" . $id . " and case when A.source_name is null then A.referred_by else A.source_name end LIKE '" . $source . "' and A.assigned_id=" . $assigned_id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';

                    if (!empty($rows)) {
                        $i = 1;
                        $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td><b>' . $row['shippercompany'] . '</b><br/>' . $row['shipperfname'] . ' ' . $row['shipperlname'] . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';
                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getcarrierpayments':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
                        AND `FlagTarrif` !=2

                    ";

                    $sql = "SELECT  entityid FROM `app_order_header` WHERE `deleted` =0 AND `carrier_id` =" . $id . " " . $where;
                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['entityid']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																	title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';
                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getsales':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $sd = explode("-", substr($start_date, 0, 10));

                    $ed = explode("-", substr($end_date, 0, 10));

                    $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);

                    $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
                        AND `created` >= '" . $start_date . "'
                        AND `created` <= '" . $end_date . "'
                    ";

                    $sql = "SELECT  id
					FROM `app_entities`
					WHERE `deleted` =0
					AND `assigned_id` =" . $id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);

                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        (float) $tariff = 0.00;
                        $i = 1;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['id']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $tariff = $tariff + $entity->getTotalTariff(false);
                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }

                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
									<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
									<tr>
										<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
															title="Total Tariff" width="16" height="16"/></td>
										<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
									</tr>
									</table></td>
									<td style="padding:3px;">
									</td>
								</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getsalesTariff':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid member ID");
                    }

                    $id = $_POST['id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $sd = explode("-", substr($start_date, 0, 10));

                    $ed = explode("-", substr($end_date, 0, 10));

                    $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);

                    $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
                        AND `created` >= '" . $start_date . "'
                        AND `created` <= '" . $end_date . "'
                    ";

                    $sql = "SELECT  id
							FROM `app_entities`
							WHERE `deleted` =0
							AND `assigned_id` =" . $id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);
                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        (float) $tariff = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['id']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $tariff = $tariff + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }

                        $tariff1 = number_format($tariff, 2);
                        $data .= '<tr>
									<td style="white-space: nowrap;" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
										<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
											<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $tariff1 . '</b></td>
											</tr>
										</table>
									</td>
									<td style="padding:3px;"></td>
					   			</tr>';
                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);
                    break;

                case 'getsalesCarrier':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $sd = explode("-", substr($start_date, 0, 10));

                    $ed = explode("-", substr($end_date, 0, 10));

                    $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);

                    $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
                        AND `created` >= '" . $start_date . "'

                        AND `created` <= '" . $end_date . "'

                    ";

                    $sql = "SELECT  id
							FROM `app_entities`
							WHERE `deleted` =0
							AND `assigned_id` =" . $id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);

                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        (float) $carrier = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['id']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $carrier = $carrier + $entity->getCarrierPay(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $carrier1 = number_format($carrier, 2);
                        $data .= '<tr>
									<td style="" class="grid-body-left"><b>TOTALS</b></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
										<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
											<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><b>' . $carrier1 . '</b></td>
											</tr>
										</table>
									</td>
									<td style="padding:3px;">
									</td>
					   			</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;

                case 'getsalesDispatched':

                    if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $id = $_POST['id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    $sd = explode("-", substr($start_date, 0, 10));

                    $ed = explode("-", substr($end_date, 0, 10));

                    $start_date_t = mktime(0, 0, 0, $sd[1], $sd[2], $sd[0]);

                    $end_date_t = mktime(23, 59, 59, $ed[1], $ed[2], $ed[0]);

                    $where = "
                        AND `type` = '" . Entity::TYPE_ORDER . "'
						AND `status` <> 3
						AND `status` = " . Entity::STATUS_DISPATCHED . "
                        AND `created` >= '" . $start_date . "'
                        AND `created` <= '" . $end_date . "'
                    ";

                    $sql = "SELECT  id
							FROM `app_entities`
							WHERE `deleted` =0
							AND `assigned_id` =" . $id . " " . $where;

                    $rows = $daffny->DB->selectRows($sql);

                    $data = '<table class="table table-responsive table-striped table-bordered">
								<thead>
									<tr>
										<th width="2%"  align="center" class="grid-head-left">#</th>
										<th width="8%"  align="center" class="grid-head-left">ID</th>
										<th width="10%"  align="Left" class="grid-head-left">Created</th>
										<th width="16%"  align="Left" class="grid-head-left">Shipper</th>
										<th width="16%"  align="Left" class="grid-head-left">Vehicles</th>
										<th width="8%"  align="Left" class="grid-head-left">Total</th>
										<th width="8%"  align="Left" class="grid-head-left">status</th>
									</tr>
								</thead>';
                    if (!empty($rows)) {
                        $i = 1;
                        (float) $dispatch = 0.00;
                        foreach ($rows as $row) {
                            $entity = new Entity($daffny->DB);
                            $entity->load($row['id']);

                            $status = '';
                            if ($entity->status == Entity::STATUS_ACTIVE) {
                                $status = "Active";
                            } elseif ($entity->status == Entity::STATUS_ONHOLD) {
                                $status = "OnHold";
                            } elseif ($entity->status == Entity::STATUS_ARCHIVED) {
                                $status = "Cancelled";
                            } elseif ($entity->status == Entity::STATUS_POSTED) {
                                $status = "Posted To FB";
                            } elseif ($entity->status == Entity::STATUS_NOTSIGNED) {
                                $status = "Not Signed";
                            } elseif ($entity->status == Entity::STATUS_DISPATCHED) {
                                $status = "Dispatched";
                            } elseif ($entity->status == Entity::STATUS_ISSUES) {
                                $status = "Issues";
                            } elseif ($entity->status == Entity::STATUS_PICKEDUP) {
                                $status = "Picked Up";
                            } elseif ($entity->status == Entity::STATUS_DELIVERED) {
                                $status = "Delivered";
                            }

                            $shipper = $entity->getShipper();
                            $vehicles = $entity->getVehicles();
                            $dispatch = $dispatch + $entity->getTotalTariff(false);

                            $vehiclesStr = '';
                            if (count($vehicles) == 0) {
                            } elseif (count($vehicles) == 1) {
                                $vehicle = $vehicles[0];
                                $vehiclesStr = $vehicle->year . " " . $vehicle->make . " " . $vehicle->model . "<br><span style='color:black;weight:bold;'>VIN: " . $vehicle->vin . "</span>";
                            } else {
                                $vehiclesStr = '<span class="like-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(' . count($vehicles) . ')</span></b></span><br/>';
                            }

                            $data .= '<tr>
										<td>' . $i . '</td>
										<td>' . $entity->getNumber() . '</td>
										<td>' . date("m/d/y h:i a", strtotime($entity->created)) . '</td>
										<td>' . $shipper->fname . ' ' . $shipper->lname . '</td>
										<td>' . $vehiclesStr . '</td>
										<td>
											<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff"
																		title="Total Tariff" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalTariff() . '</td>
												</tr>
												<tr>
													<td width="10">
														<img src="' . SITE_IN . 'images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>

													</td>
													<td style="white-space: nowrap;">' . $entity->getCarrierPay() . '<br/></td>
												</tr>
												<tr>
													<td width="10"><img src="' . SITE_IN . 'images/icons/person.png" alt="Deposit    "
																		title="Deposit" width="16" height="16"/></td>
													<td style="white-space: nowrap;">' . $entity->getTotalDeposit() . '</td>
											</tr>

											</table>
										</td>
										<td bgcolor="#ffffff"><b>' . $status . '</b></td>
									</tr>';
                            $i++;
                        }
                        $dispatch1 = number_format($dispatch, 2);
                        $data .= '<tr>
									<td style="" class="grid-body-left"><strong>TOTALS</strong></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;"></td>
									<td style="padding:3px;">
										<table cellspacing="0" cellpadding="0" border="0" width="100%" class="tariff-grid">
											<tr>
												<td width="10"><img src="' . SITE_IN . 'images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/></td>
												<td style="white-space: nowrap;"><strong>' . $dispatch1 . '</strong></td>
											</tr>
										</table>
									</td>
									<td style="padding:3px;">
									</td>
								</tr>';

                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;

                case 'getvehicles':

                    $start_date = "";
                    $end_date = "";
                    $users_ids = '';
                    $vehicles = post_var("vehicles");
                    // Generate report

                    $data = '<table width="100%"   cellpadding="1" cellspacing="1" class="grid"><tr><td><b><p>Year </p></b></td><td><b><p>Make</p></b></td><td><b><p>Model</p></b></td><td><b><p>Type</p></b></td><td><b><p>Vin#</p></b></td><td><b><p>Inop</p></b></td></tr>';

                    if (!empty($vehicles)) {

                        foreach ($vehicles as $row) {
                            $data .= '<tr><td>' . $row['year'] . '</td><td>' . $row['vehicles']['make'] . ' </td><td>' . $row['model'] . '</td><td>' . $row['type'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;">' . $row['vin'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;">$' . $row['inop'] . '</td></tr>';
                        }
                    } else {
                        $data = ' <tr><td colspan="7" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    $out = array('success' => true, 'detailData' => $data);

                    break;
                case 'getpay':

                    $start_date = "";
                    $end_date = "";
                    $users_ids = '';

                    $users_ids_data = post_var("users_ids");

                    $start_date = post_var("start_date") . " 00:00:00";
                    // Generate report
                    $sales = array();
                    $totals = array();
                    $sales = array();

                    if (post_var("user_ids") != '') {
                        $users_ids = explode(',', post_var("user_ids"));
                    }

                    if (!is_array($users_ids)) {
                        $users_ids = array();
                    }

                    //Get Users

                    $where = " id IN ('" . implode("','", $users_ids) . "')";

                    $user = new Member();
                    $users = $user->getCompanyMembers($daffny->DB, getParentId(), $where, true);

                    $entityManager = new EntityManager($daffny->DB);
                    // For each users get sales
                    // foreach ($users as $key => $value) {
                    $rows = $entityManager->getPayReportByDate(getParentId(), $start_date, post_var("ptype"), $users, "assigned_id", $arr['define_as']);

                    $data = '<table class="table table-responsive table-striped table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" width="8%"  align="center" class="grid-head-left">Order #</th>
                                <th rowspan="2" width="10%"  align="Left" class="grid-head-left">Assigned Name</th>
                                <th rowspan="2" width="16%"  align="Left" class="grid-head-left">Shipper company</th>
                                <th rowspan="2" width="16%"  align="Left" class="grid-head-left">Date Recieved</th>
                                <th rowspan="2" width="8%"  align="Left" class="grid-head-left">Payment From</th>
                                <th rowspan="2" width="8%"  align="Left" class="grid-head-left">Payment To</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Amount</th>
                                <th rowspan="2" width="10%"  align="Left" class="grid-head-left">Method</th>
                                <th rowspan="2" width="10%"  align="Left" class="grid-head-left">check# <br> Transaction ID</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Carrier Pay</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Total Deposit</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Total Tariff</th>
                                <th rowspan="2" width="16%"  align="Left" class="grid-head-left">Carrier company</th>
                                <th colspan="3" width="20%"  align="center" class="grid-head-left">Origin</th>
                                <th colspan="3" width="20%"  align="center" class="grid-head-left">Destination</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Distance</th>
                                <th rowspan="2" width="8%"  align="right" class="grid-head-left">Vehicle<br>Count</th>
                                <th rowspan="2" width="8%"  align="Left" class="grid-head-left">Referred By</th>
                            </tr>
                            <tr>
                                <th width="8%"  align="Left" class="grid-head-left">City</th>
                                <th width="6%"  align="Left" class="grid-head-left">State</th>
                                <th width="6%"  align="Left" class="grid-head-left">Zip</th>
                                <th width="8%"  align="Left" class="grid-head-left">City</th>
                                <th width="6%"  align="Left" class="grid-head-left">State</th>
                                <th width="6%"  align="Left" class="grid-head-left">Zip</th>
                            </tr>
                    </thead>';

                    if (!empty($rows)) {
                        $amount = 0;
                        $TotalVehicle = 0;
                        $total_carrier_pay = 0;
                        $total_deposite = 0;
                        $total_tariff = 0;

                        foreach ($rows as $row) {
                            $cPay = 0;
                            if( in_array($row['PaidBy'], [2,3,8,9]) ){
                                // nothing to do
                            } else {
                                if(str_replace("$","",$row['amount']) < $row['total_tariff']){
                                    $cPay = $row['total_tariff'] - $row['total_deposite'] - str_replace("$","",$row['amount']);
                                    $row['total_carrier_pay'] = $cPay;
                                } else {
                                    // nothing to do
                                }
                            }

                            $data .= '<tr><td>' . $row['prefix'] . '-' . $row['Number'] . '</td><td>' . $row['AssignedName'] . '</td>
										<td>' . $row['shippercompany'] . '</td>
										<td>' . $row['date_received'] . '</td><td>' . $row['PaymentFrom'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;">' . $row['PaymentTo'] . '</td>
                                        <td align="right" bgcolor="#ffffff" style="padding-left:5px;">$' . number_format($row['amount'], 2, '.', ',') . '</td><td bgcolor="#ffffff" style="padding-left:5px;">' . $row['method'] . '</td><td><p>' . $row['check_trans'] . '</p></td>
										<td align="right"><p>$' . number_format($row['total_carrier_pay'], 2, '.', ',') . '</p></td>
										<td align="right"><p>$' . number_format($row['total_deposite'], 2, '.', ',') . '</p></td>
										<td align="right"><p>$' . number_format($row['total_tariff'], 2, '.', ',') . '</p></td>
										<td>' . $row['CarrierCompany'] . '</td>
										<td>' . $row['Origincity'] . '</td><td>' . $row['Originstate'] . '</td><td>' . $row['Originzip'] . '</td>
										<td>' . $row['Destinationcity'] . '</td><td>' . $row['Destinationstate'] . '</td><td>' . $row['Destinationzip'] . '</td>
										<td align="right"><p>' . $row['distance'] . '</p></td><td align="right"><p>' . $row['TotalVehicle'] . '</p></td><td><p>' . $row['referred_by'] . '</p></td>
									</tr>';

                            $amount += $row['amount'];
                            $TotalVehicle += $row['TotalVehicle'];
                            $total_carrier_pay += $row['total_carrier_pay'];
                            $total_deposite += $row['total_deposite'];
                            $total_tariff += $row['total_tariff'];

                        }

                        $data .= '<tr><td style="padding:3px;">Total</td>
									<td style="padding:3px;">&nbsp;</td>
									<td style="padding:3px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td align="right" style="padding-left:5px;">$' . number_format($amount, 2, '.', ',') . '</td><td style="padding-left:5px;">&nbsp;</td>
									<td style="padding:3px;">&nbsp;</td>
									<td align="right" style="padding-left:5px;">$' . number_format($total_carrier_pay, 2, '.', ',') . '</td>
									<td align="right" style="padding-left:5px;">$' . number_format($total_deposite, 2, '.', ',') . '</td>
									<td align="right" style="padding-left:5px;">$' . number_format($total_tariff, 2, '.', ',') . '</td>
									<td align="right" style="padding:3px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td style="padding-left:5px;">&nbsp;</td>
									<td align="right" style="padding-left:5px;">' . $TotalVehicle . '</td>
									<td style="padding:3px;">&nbsp;</td>
								</tr>';
                    } else {
                        $data = ' <tr><td colspan="15" align="center">Records not found.</td></tr>';
                    }

                    $data .= '</table>';
                    
                    $out = array('success' => true, 'detailData' => $data);
                    echo $json->encode($out);
                    die;

                    break;

                default:
                    break;
            }
        } elseif (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'getDoc':

                    break;
                default:
                    break;
            }
        }
    } catch (Exception $e) {
        if ($daffny->DB->isTransaction) {
            $daffny->DB->transaction('rollback');
        }
        $out['message'] = $e->getMessage();
    }
}

function getAlmostUniqueHash($id, $number)
{
    return md5($id . "_" . $number . "_" . rand(100000000, 9999999999)) . uniqid() . time() . sha1(time());
}

ob_clean();
echo $json->encode($out);
require_once "done.php";
