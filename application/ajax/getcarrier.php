<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
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
    try {

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'getroute':

                    if (!isset($_POST['route_id']) || !ctype_digit((string) $_POST['route_id'])) {
                        throw new RuntimeException("Invalid Route ID");
                    }

                    $data = '<table class="table-bordered table"><tr class=""><td><b><p>City</p></b></td><td><b><p>State</p></b></td><td><b><p>Zip</p></b></td><td><b><p>Longitude</p></b></td><td><b><p>Latitude</p></b></td><td><b><p>Type</p></b></td></tr>';
                    echo $sql = "select  * from app_route WHERE route_id='" . $_POST['route_id'] . "'";
                    $result = $daffny->DB->query($sql);

                    if ($daffny->DB->num_rows() > 0) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $typeString = "DEST";

                            if ($row['type'] == 'ORG') {
                                $typeString = "ORG";
                            }

                            $data .= ' <tr><td bgcolor="#ffffff" style="padding:3px;">' . $row['city'] . '</td><td bgcolor="#ffffff" style="padding:3px;">' . $row['state'] . ' </td><td bgcolor="#ffffff" style="padding:3px;">' . $row['zip'] . '</td><td bgcolor="#ffffff" style="padding:3px;"> ' . $row['long'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;"> ' . $row['lati'] . '</td><td bgcolor="#ffffff" style="padding-left:5px;"> ' . $typeString . '</td></tr>';
                        }
                    } else {
                        $data = ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="6" align="center">Route not found.</td></tr>';
                    }

                    $out = array('success' => true, 'carrierData' => $data);
                    break;
                case 'getcarrierData':

                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $entity_id = $_POST['entity_id'];
                    $radius = $_POST['radius'];

                    if ($radius == '') {
                        $radius = 100;
                    }

                    $data2 .= '<table width="100%">
                                <tr><td>&nbsp;</td></tr>
                                        <tr><td align="left"><h2>Possible Carriers In Radius</h2></td></tr>
                                </table>
                                <table class="table table-bordered">
                                 <tr >
                                    <td><b><p>Company</p></b></td>
                                    <td><b><p>Name</p></b></td>
                                    <td><b><p>Email</p></b></td>
                                    <td><b><p>Phone1</p></b></td>
                                    <td><b><p>Phone2</p></b></td>
                                    <td><b><p>Orders</p></b></td>
                                    <td><b><p>Order Date</p></b></td>
                                  </tr>';

                    ini_set('max_execution_time', 300);

                    $result = $daffny->DB->query("CALL fd_matching_carrier('" . $_POST['ozip'] . "',  '" . $_POST['dzip'] . "', " . $radius . ")");

                    if ($daffny->DB->num_rows() > 0) {
                        while ($row = $daffny->DB->fetch_row($result)) {
                            $data2 .= ' <tr class="grid-body">
                                             <td bgcolor="#ffffff" style="padding:3px;" class="grid-body-left">' . $row['company_name'] . '</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">' . $row['contact_name1'] . ' ' . $row['contact_name2'] . '</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">' . $row['email'] . '</td>
                                             <td bgcolor="#ffffff" style="padding:3px;"> ' . formatPhone($row['phone1']) . '</td>
                                             <td bgcolor="#ffffff" style="padding:3px;">' . formatPhone($row['phone2']) . '</td>
                                             <td bgcolor="#ffffff" style="padding-left:5px;" > ' . $row['number_of_orders'] . '</td>
											 <td bgcolor="#ffffff" style="padding-left:5px;" class="grid-body-right"> ' . date("m/d/Y", strtotime($row['followupdate'])) . '</td>
                                           </tr>';
                        }
                    } else {
                        $data2 .= ' <tr><td bgcolor="#ffffff" style="padding:3px;" colspan="8" align="center">Assigned Carrier not found.</td></tr>';
                    }

                    $data2 .= '</table>';

                    $data = $data1 . $data . $data2;
                    $out = array('success' => true, 'carrierData' => $data);
                    break;

                case 'getcarrier':

                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $entity_id = $_POST['entity_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $entity_id);
                    $defaultCarrier = 0;
                    $data1 .= '<table class="table-bordered table">
                                    <tr>
                                        <td style="vertical-align:top;" valign="top" width="50%">';

                    if ($ds = $entity->getDispatchSheet()) {
                        $defaultCarrier = 1;

                        $carrier = $entity->getCarrier();

                        $data1 .= '
                                                <table width="100%" cellpadding="1" cellpadding="1">
                                                    <tr><td width="33%"><strong>MC Number</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $carrier->insurance_iccmcnumber . '</td></tr>
                                                    <tr><td width="33%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_company_name . '</td></tr>
                                                    <tr><td ><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_address . '</td></tr>
                                                    <tr><td ><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_city . '</td></tr>
                                                    <tr><td ><strong>State/Zip</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"> ' . $ds->carrier_state . ' , ' . $ds->carrier_zip . '</td></tr>
                                                    <tr><td ><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_contact_name . '</td></tr>
                                                    <tr><td ><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_phone_1) . '</td></tr>
                                                    <tr><td ><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_phone_2) . '</td></tr>
                                                    <tr><td ><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_fax) . '</td></tr>
                                                    <tr><td ><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:' . $ds->carrier_email . '">' . $ds->carrier_email . '</a></td></tr>';

                        $carrier = $entity->getCarrier();

                        if ($carrier instanceof Account) {
                            $data1 .= '<tr><td ><strong>Hours</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $carrier->hours_of_operation . '</td></tr>';
                        }

                        $data1 .= '<tr><td ><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_driver_name . '</td></tr>
                                                    <tr><td ><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_driver_phone) . '</td></tr>
                                                    </table>';
                    } else {
                        $data1 .= ' No carrier is Assigned.';
                    }

                    $data1 .= '</td><td style="vertical-align:top;">
                                            <table width="100%" cellpadding="1" cellpadding="1">
                                                <tr><td ><h3>Order ID<b>:</b>&nbsp;' . $entity->getNumber() . '</h3></td></tr>
                                                <tr><td ><strong>Carrier Pay</strong><b>:</b>&nbsp;<strong><font color="red">$ ' . number_format((float) $entity->carrier_pay_stored, 2, ".", ",") . '</font></strong></td></tr>
                                                <tr><td>
                                        ';
                    $payments_terms = $entity->payments_terms;

                    if (in_array($entity->balance_paid_by, array(2, 3, 16, 17, 8, 9, 18, 19))) {
                        $payments_terms = "COD / COP";
                    }

                    if ($payments_terms != "") {
                        $data1 .= '<b>Payment Terms:</b> ' . $payments_terms;
                    }

                    $data1 .= '</td>
                                                </tr>
                                                    <tr><td>
                                                    <div class="attention-box import-hidden" style="width: 340px; display: block;">
                                            <span style="color: #f00">ATTENTION:</span> If you would like to view other carrier(s) that match this route please click the button below.
                                            <div style="text-align:center; margin:10px;">
                                            <div style="float-left;width:30%;"><b>Radius: </b>
                                                <select  class="form-control" id="radius" style="margin-top:5px">
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
                                                    <input type="submit" id="submit_button" class="btn-sm btn btn_dark_blue " value="View Carriers" onclick="getCarrierDataRoute(' . $_POST['entity_id'] . ',\'' . $_POST['ocity'] . '\',\'' . $_POST['ostate'] . '\',\'' . $_POST['ozip'] . '\',\'' . $_POST['dcity'] . '\',\'' . $_POST['dstate'] . '\',\'' . $_POST['dzip'] . '\');" style="-webkit-user-select: none;">
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                        </td></tr>
                                    </table>
                                    </td>
                                </tr>
                                <tr><td colspan="2" align="center" >
                                    <div id="routeCarrierDataDiv" ></div>
                                </td></tr>
                                </table>';
                    $data = $data1;
                    $out = array('success' => true, 'carrierData' => $data);
                    break;

                case 'getcarrierForReview':

                    if (!isset($_POST['entity_id']) || !ctype_digit((string) $_POST['entity_id'])) {
                        throw new RuntimeException("Invalid Entity ID");
                    }

                    $entity_id = $_POST['entity_id'];
                    $entity = new Entity($daffny->DB);
                    $entity->load((int) $entity_id);
                    $defaultCarrier = 0;
                    $data1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="vertical-align:top;" valign="top" width="50%">';

                    if ($ds = $entity->getDispatchSheet()) {
                        $defaultCarrier = 1;
                        $data1 .= '
                                            <table width="100%" cellpadding="1" cellpadding="1" >
                                                <tr><td width="33%"><strong>Company Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_company_name . '</td></tr>
                                                <tr><td ><strong>Address</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_address . '</td></tr>
                                                <tr><td ><strong>City</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_city . '</td></tr>
                                                <tr><td ><strong>State/Zip</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"> ' . $ds->carrier_state . ' , ' . $ds->carrier_zip . '</td></tr>
                                                <tr><td ><strong>Contact Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_contact_name . '</td></tr>
                                                <tr><td ><strong>Phone 1</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_phone_1) . '</td></tr>
                                                <tr><td ><strong>Phone 2</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_phone_2) . '</td></tr>
                                                <tr><td ><strong>Fax</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_fax) . '</td></tr>
                                                <tr><td ><strong>Email</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;"><a href="mailto:' . $ds->carrier_email . '">' . $ds->carrier_email . '</a></td></tr>';

                        $carrier = $entity->getCarrier();

                        if ($carrier instanceof Account) {
                            $data1 .= '<tr><td ><strong>Hours</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $carrier->hours_of_operation . '</td></tr>';
                        }

                        $data1 .= '<tr><td ><strong>Driver Name</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . $ds->carrier_driver_name . '</td></tr>
                                                <tr><td ><strong>Driver Phone</strong></td><td width="4%" align="center"  style="line-height:15px;"><b>:</b></td><td align="left"  style="line-height:15px;">' . formatPhone($ds->carrier_driver_phone) . '</td></tr>
                                                </table>
                                    ';
                    } else {
                        $data1 .= ' No carrier is Assigned.';
                    }
                    $data = $data1;
                    $out = array('success' => true, 'carrierData' => $data);
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

/*ob_clean();*/
echo $json->encode($out);
require_once "done.php";
