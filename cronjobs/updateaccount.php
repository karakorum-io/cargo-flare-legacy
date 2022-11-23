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

require_once "init.php";

require_once "../libs/phpmailer/class.phpmailer.php";

ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);

require_once "init.php";

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);

$em = new EntityManager($daffny->DB);

$where = " A.shipper_id = B.id
and type = 3 and ( account_id  is null
or account_id = 0)
group by A.shipper_id,
B.fname,
B.lname,
B.company,
B.email
order by count(*) desc limit 0,300
";

//print  $where;

$rows = $daffny->DB->selectRows('A.id as id', " app_entities A,
                app_shippers B ", "WHERE " . $where);
$k = 1;
if (!empty($rows)) {
    $messages = "<p>Order ID/Entity Id resposted</p><br>";
    $entities = array();

    foreach ($rows as $row) {

        $entity = new Entity($daffny->DB);
        $entity->load($row['id']);

        print "<br>-ID:" . $entity->id . "--Number:" . $entity->number;
        //$messages .= "<p>".$entity->id."  : ".$entity->number." : ".$entity->posted."</p><br>";

        $shipperNew = $entity->getShipper();

        $shipper_type = "";
        if ($shipperNew->shipper_type == "") {
            if ($shipperNew->shipper_company == "") {
                $shipper_type = "Residential";
            } else {
                $shipper_type = "Commercial";
            }
        } else {
            $shipper_type = $shipperNew->shipper_type;
        }

        $shipper = new Account($daffny->DB);
        $shipperArr = array(
            'owner_id' => $entity->assigned_id,
            'company_name' => $shipperNew->company,
            'status' => Account::STATUS_ACTIVE,
            'is_carrier' => 0,
            'is_shipper' => 1,
            'is_location' => 0,
            'first_name' => $shipperNew->fname,
            'last_name' => $shipperNew->lname,
            'email' => $shipperNew->email,
            'phone1' => $shipperNew->phone1,
            'phone2' => $shipperNew->phone2,
            'cell' => $shipperNew->mobile,
            'fax' => $shipperNew->fax,
            'address1' => $shipperNew->address1,
            'address2' => $shipperNew->address2,
            'city' => $shipperNew->city,
            'state' => $shipperNew->state,
            'state_other' => $shipperNew->state,
            'zip_code' => $shipperNew->zip,
            'country' => $shipperNew->country,
            'shipper_type' => $shipper_type,
            'hours_of_operation' => $shipperNew->shipper_hours,
            //'referred_by' => $shipperNew->referred_by
            'referred_by' => $entity->referred_by,
            'referred_id' => $entity->referred_id,

        );

        if ($shipperNew->company) {
            $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->company) . "' AND state='" . $shipperNew->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->lname) . "' AND `is_shipper` = 1)");

        } else {

            $rowShipper = $daffny->DB->selectRow("id", "app_accounts", "WHERE
							(`company_name` ='' AND state='" . $shipperNew->state . "' AND city='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->city) . "' AND first_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->fname) . "' AND last_name='" . mysqli_real_escape_string($daffny->DB->connection_id, $shipperNew->lname) . "' AND `is_shipper` = 1)");

        }

        if (empty($rowShipper)) {
            $shipper->create($shipperArr);

            // Update Entity
            $update_account_id_arr = array(
                'account_id' => $shipper->id,
            );
            $entity->update($update_account_id_arr);

            print "--$k - account id: $shipper->id<br><pre>";
            print_r($shipperArr);
            print "</pre><br><br>";
            $k++;

        }

        print "<br>==============================<br>";

        //fflush();

    }

    $numRows = sizeof($rows);
    print "numRows : " . $numRows;

    //$body = ob_get_clean();
    $body = "<p>RePosted date : " . date("Y-m-d H:i:s") . "</p><br>";
    $body .= "<p>How many reposted : " . $numRows . "</p><br>";
    $body .= $messages;

    //print "---".$daffny->cfg['suadminemail."-----";

}

$_SESSION['iamcron'] = false;

//send mail to Super Admin

require_once "done.php";
