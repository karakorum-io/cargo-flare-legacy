<style type="text/css">
	th {
    font-size: 13px;
}
</style>

<?php
require_once "init.php";
require_once "../../libs/anet/AuthorizeNet.php";

$memberId = (int) $_SESSION['member_id'];
$out = array('success' => false);
if ($memberId > 0) {

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'matching-carrier':

                $radius = $_POST['radius'];

                if ($radius == '') {
                    $radius = 100;
                }

                $data2 .= '<table width="100%">
								<tr><td>&nbsp;</td></tr>
								<tr><td align="left"><h3>Possible Carriers In Radius</h3></td></tr>
							</table>
							<div style="max-height:300px;overflow:auto">
							<table class="table table-bordered">
								<tr>
									<th><b><p>Company</p></b></th>
									<th><b><p>Name</p></b></th>
									<th><b><p>Email</p></b></th>
									<th><b><p>Phone1</p></b></th>
									<th><b><p>Phone2</p></b></th>
									<th><b><p>Orders</p></b></th>
									<th><b><p>Order Date</p></b></th>
								</tr>';

                $result = $daffny->DB->query("CALL fd_matching_carrier('" . $_POST['ozip'] . "',  '" . $_POST['dzip'] . "', " . $radius . ")");

                if ($daffny->DB->num_rows() > 0) {
                    while ($row = $daffny->DB->fetch_row($result)) {
                        $data2 .= ' <tr >
											<td bgcolor="#ffffff" style="padding:3px;" class="grid-body-left">' . $row['company_name'] . '</td>
											<td bgcolor="#ffffff" style="padding:3px;">' . $row['contact_name1'] . ' ' . $row['contact_name2'] . '</td>
											<td bgcolor="#ffffff" style="padding:3px;">' . $row['email'] . '</td>
											<td bgcolor="#ffffff" style="padding:3px;"> ' . $row['phone1'] . '</td>
											<td bgcolor="#ffffff" style="padding:3px;">' . $row['phone2'] . '</td>
											<td bgcolor="#ffffff" style="padding-left:5px;"> ' . $row['number_of_orders'] . '</td>
											<td bgcolor="#ffffff" style="padding-left:5px;" class="grid-body-right"> ' . date("m/d/Y", strtotime($row['followupdate'])) . '</td>
										</tr>';
                    }
                } else {
                    $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="7" align="center">Assigned Carrier not found.</td></tr>';
                }

                $data2 .= '</table></div>';
                $data = $data2 . $data;
                $out = array('success' => true, 'matching_carrier_data' => $data);
                break;
            case 'getcarrierprice':

                // stored procedure code added by chetu started here
                $sqlpricedata = "CALL sp_matching_carrier_price('$_POST[ozip]','$_POST[dzip]','$_POST[shipping_Radius]','$_POST[carrierloadsize]','$_POST[shipping_ship_via]')";
                $result = $daffny->DB->query($sqlpricedata);
                $data_all = [];
                $total_rec = $daffny->DB->num_rows();
                if ($total_rec > 0) {
                    while ($row = $daffny->DB->fetch_row($result)) {
                        $data_all[] = $row;
                        $DistanceMileage = number_format($row['distance'], 2);
                    }
                }
				
                $data .= '<table style="width: 100%;padding-right:10px; padding-left:10px;" ';
                if ($total_rec > 0) {
                    $data .= '<tr><td style="font-weight: 600;">Distance: </td><td style="color: #36c007;" colspan=6>' . $DistanceMileage . ' miles<td></tr><tr><td style="font-weight: 600;">Vehicle Type </td><td style="font-weight: 600;">Count</td><td style="font-weight: 600;">CPM</td><td style="font-weight: 600;">Avg. Price</td><td style="font-weight: 600;">Min Price</td><td style="font-weight: 600;">Max Price</td></tr> ';
                    foreach ($data_all as $row) {
                        "$ " . number_format($amount, 2);
                        $data .= '<tr><td>' . $row['type'] . '</td><td style="color: #36c007;"  align="center">' . number_format($row['counts'], 0) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['UnitPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['AvgPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['MinPrice'], 2) . '</td><td style="color: #36c007;">' . "$ " . number_format($row['MaxPrice'], 2) . '</td></tr> ';
                    }
                    $data .= '</table>';
                    $out = array('success' => true, 'getprice_carrier_data' => $data);
                } else {
                    $data .= '<tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Price detail not found.</td></tr></table>';
                    $out = array('success' => true, 'getprice_carrier_data' => $data);
                }
                break;
            default:
                $out = array("success" => false, "message" => "Invaid API Action");
                break;
        }
    }
}

ob_clean();
echo $json->encode($out);
require_once "done.php";