<?php

/**
 * ajax.php Ajax handler file for handling all ajax related operations
 * from CargoFlare.com
 *
 * @author Shahrukh
 * @copyright CargoFlare
 */

require_once "init.php";

$out = array('success' => false);

if (isset($_GET['action'])) {
    $items = array();
    switch ($_GET['action']) {
        case "getVehicleMake":
            $makes = $daffny->DB->selectRows("`make`", 'vehicle_makes', "WHERE `make` LIKE('" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "%')");
            foreach ($makes as $make) {
                $items[] = $make['make'];
            }
            $out = $items;
            break;
        case "getVehicleModel":
            $sql = "SELECT a.`model`
			FROM `vehicle_models` a
			JOIN `vehicle_makes` b ON b.`id` = a.`make_id`
			WHERE b.`make` LIKE '" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['make']) . "' AND a.`model` LIKE '" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "%'
			";
            $res = $daffny->DB->query($sql);
            while ($row = $daffny->DB->fetch_row($res)) {
                $items[] = $row['model'];
            }
            $out = $items;
            break;
        case "getCompany":
            $makes = $daffny->DB->selectRows("`company_name`,id", 'app_accounts', "WHERE `company_name` LIKE('" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "%') AND is_shipper=1 AND `owner_id` IN (" . implode(', ', Member::getCompanyMembers($daffny->DB, $_SESSION['member']['parent_id'])) . ")");
            foreach ($makes as $make) {
                $items[] = $make['company_name'];
            }
            $out = $items;
            break;
        case "getCompanyData":
            $makes = $daffny->DB->selectRows("id,`company_name`,city,state,first_name,last_name,phone1,email,address1,zip_code,country,shipper_type,referred_id,referred_by", 'app_accounts', "WHERE `company_name` LIKE('" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "%') AND is_shipper=1  AND `owner_id` IN (" . implode(', ', Member::getCompanyMembers($daffny->DB, $_SESSION['member']['parent_id'])) . ")");

            $projects = array();
            foreach ($makes as $make) {
                $items = array();
                $first_name = "";
                $last_name = "";

                if ($make['first_name'] != null && $make['first_name'] != "N/A") {
                    $first_name = $make['first_name'];
                }

                if ($make['last_name'] != null && $make['last_name'] != "N/A") {
                    $last_name = $make['last_name'];
                }

                $items['value'] = $make['id'];
                $items['label'] = $make['company_name'] . " | " . $make['city'] . " | " . $make['state'] . " | " . $first_name . " " . $last_name;
                $items['company_name'] = $make['company_name'];
                $items['first_name'] = $make['first_name'];
                $items['last_name'] = $make['last_name'];
                $items['phone1'] = $make['phone1'];
                $items['email'] = $make['email'];
                $items['phone2'] = $make['phone2'];
                $items['cell'] = $make['cell'];
                $items['fax'] = $make['fax'];
                $items['address1'] = $make['address1'];
                $items['address2'] = $make['address2'];
                $items['city'] = $make['city'];
                $items['state'] = $make['state'];
                $items['zip_code'] = $make['zip_code'];
                $items['country'] = $make['country'];
                $items['shipper_type'] = $make['shipper_type'];
                $items['referred_id'] = $make['referred_id'];
                $items['referred_by'] = $make['referred_by'];
                $projects[] = $items;

            }
            $out = $projects;
            break;
        case "getCompanyDataBatchPayment":
            $makes = $daffny->DB->selectRows("distinct(`company_name`) as company_name", 'app_accounts', "WHERE `company_name` LIKE('" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "%') AND is_shipper=1 ");
            $projects = array();
            foreach ($makes as $make) {
                $items = array();

                $items['label'] = $make['company_name'];
                $items['company_name'] = $make['company_name'];

                $projects[] = $items;

            }
            $out = $projects;
            break;
        case "getCarrier":
            $accountManager = new AccountManager($daffny->DB);
            $accounts = $accountManager->searchAccount($_GET['term'], getParentId(), 1);

            $projects = array();
            foreach ($accounts as $account) {

                $projects[] = array(
                    'id' => $account->id,
                    'label' => $account->company_name . "|" . $account->city . "|" . $account->state . "|" . $account->contact_name1 . " " . $account->contact_name2,
                    'member_id' => $account->member_id,
                    'company_name' => $account->company_name,
                    'carrier_type' => $account->carrier_type,
                    'contact_name1' => $account->contact_name1,
                    'contact_name2' => $account->contact_name2,
                    'first_name' => $account->first_name,
                    'last_name' => $account->last_name,
                    'phone1' => $account->phone1,
                    'phone2' => $account->phone2,
                    'cell' => $account->cell,
                    'fax' => $account->fax,
                    'email' => $account->email,
                    'address1' => $account->address1,
                    'address2' => $account->address2,
                    'city' => $account->city,
                    'state' => ($account->country == "US") ? $account->state : $account->state_other,
                    'zip' => $account->zip_code,
                    'country' => $account->country,
                    'print_name' => $account->print_name,
                    'insurance_iccmcnumber' => $account->insurance_iccmcnumber,
                    'shipper_type' => $account->shipper_type,
                    'location_type' => $account->location_type,
                    'carrier_type' => $account->carrier_type,
                    'hours_of_operation' => $account->hours_of_operation,
                    'expired' => !is_null($account->insurance_expirationdate) && (strtotime($account->insurance_expirationdate) < time()),
                );
            }
            $out = $projects;
            break;
        case "getCompanyValues":
            $sql = "SELECT first_name,last_name,phone1,email,address1,city,state,zip_code,country,shipper_type,referred_id,referred_by
                  FROM app_accounts
                  WHERE `company_name` ='" . mysqli_real_escape_string($daffny->DB->connection_id, $_GET['term']) . "' AND is_shipper=1
                 LIMIT 0, 1";

            $makes = $daffny->DB->selectRows($sql);

            foreach ($makes as $make) {
                $items['first_name'] = $make['first_name'];
                $items['last_name'] = $make['last_name'];
                $items['phone1'] = $make['phone1'];
                $items['email'] = $make['email'];
                $items['phone2'] = $make['phone2'];
                $items['cell'] = $make['cell'];
                $items['fax'] = $make['fax'];
                $items['address1'] = $make['address1'];
                $items['address2'] = $make['address2'];
                $items['city'] = $make['city'];
                $items['state'] = $make['state'];
                $items['zip_code'] = $make['zip_code'];
                $items['country'] = $make['country'];
                $items['shipper_type'] = $make['shipper_type'];
                $items['referred_id'] = $make['referred_id'];
                $items['referred_by'] = $make['referred_by'];
            }
            $out = $items;
            break;
    }
}
echo $json->encode($out);
require_once "done.php";
