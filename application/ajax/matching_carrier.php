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
				<tr><td align="left"><h3>Possible Carrier(s) who transport on this route.</h3></td></tr>
			</table>
			<table width="100%" cellpadding="1" cellspacing="1" class="grid">
				<tr class="grid-head">
					<td><b><p>Company</p></b></td>
					<td><b><p>Name</p></b></td>
					<td><b><p>Email</p></b></td>
					<td><b><p>Phone1</p></b></td>
					<td><b><p>Phone2</p></b></td>
				</tr>';
    $data .= $data1;
    $data .= '</table>';
    $out = array('success' => true, 'matching_carrier_data' => $data);

}

ob_clean();
echo $json->encode($out);
require_once "done.php";
