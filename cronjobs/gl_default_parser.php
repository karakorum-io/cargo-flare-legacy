<?php

/**
 * Default email parser function
 * 
 * @version 1.0
 */
function default_parser($body, $db) {
    $body = strip_tags($body);
    $body = str_replace("Content-Type: text/plain;", "", $body);
    $body = str_replace("Content-Type: text/html;", "", $body);
    
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
        //Vehicle #2 Running Condition:
        //Does the vehicle run2?:
        ),
        "vin" => array(
            "Vehicle VIN",
            "vehicle_vin",
        ),
        "lot" => array(
            "Vehicle Lot",
            "vehicle_lot",
        ),
        "plate" => array(
            "Vehicle Plate",
            "vehicle_plate",
        ),
        "color" => array(
            "Vehicle Color",
            "vehicle_color",
        ),
    );

    $parse_vehicle2 = array(
        "year" => array(
            "Year2:",
            "Vehicle Year2:",
            "Manufactured Year2:",
            "year2:",
            "Comments / Year2:",
            "Year2 ",
            "Vehicle #2 Year:",
        ),
        "make" => array(
            "Make2:",
            "Vehicle Make2:",
            "make2:",
            "Auto Make2:",
            "Make2 ",
            "Vehicle #2 Make:",
        ),
        "model" => array(
            "Model2:",
            "Vehicle Model2:",
            "model2:",
            "Auto Model2:",
            "Model2 ",
            "Vehicle #2 Model:",
        ),
        "type" => array(
            "Type2:",
            "Vehicle Type2:",
            "vehicle_type_id2:",
            "Vehicle #2 Type:",
        ),
        "run" => array(
            "Running Condition2:",
            "Running condition2:", //YES
            "Vehicle Condition2:", //Running
            "Does the vehicle run2?:",
            "vehicle_condition2:",
            "Vehicle in Running Condition2:", //Yes
            "vehicle_runs2:",
            "Running Condition2 ",
            "Vehicle #2 Running Condition:",
        ),
        "vin" => array(
            "Vehicle VIN2:",
        ),
        "lot" => array(
            "Vehicle Lot2:",
        ),
        "plate" => array(
            "Vehicle Plate2:",
        ),
        "color" => array(
            "Vehicle Color2:",
        ),
    );

    //Lead Body parse array
    $parse_body = array(
        "source" => array(
            "Source:",
            "ID:",
        ),
        "first_name" => array(
            "Name:",
            "First Name:",
            "Customer Name:",
            "first_name:",
            "Name ",
        ),
        "last_name" => array(
            "Last Name:",
            "last_name:",
        ),
        "shipper_email" => array(
            "Customer Email:",
            "Email:",
            "Email Address:",
            "Customer E-mail:",
            "email:",
            "E-Mail Address:",
            "Email ",
        ),
        "phone1" => array(
            "Phone:",
            "Customer Phone:",
            "Phone number:",
            "Daytime Phone:",
            "Home Phone:",
            "phone:",
            "Home Phone ",
        ),
        "phone2" => array(
            "Shipper Phone 2:",
            "Evening Phone:",
            "Work Phone:",
            "Alternate Phone ",
        ),
        "fax" => array(
            "Shipper Fax:",
            "Fax:",
        ),
        "mobile" => array(
            "Customer Cell:",
            "Cell Phone:",
        ),
        "address" => array(
            "Shipper Address:",
        ),
        "address2" => array(
            "Shipper Address 2:",
        ),
        "pickup_full_address" => array(
            "Origin City/State/Zip:",
            "Origin City/State/Zip"
        ),
        "city" => array(
            "Origin City:",
            "Pickup City:",
            "Moving From:",
            "pickup_city:",
            "From City:",
            "Origin ",
            "Origin:",
        ),
        "state" => array(
            "Origin State:",
            "Pickup State:",
            "pickup_state_code:",
            "From State:",
        ),
        "zip" => array(
            "Origin Zip:",
            "Pickup Zip:",
            "Pickup Zipcode:",
            "pickup_zip:",
            "From Zip Code:",
            "Current Zip code ",
        ),
        "country" => array(
            "Shipper Country:",
            "Pickup Country:",
        ),
        "delivery_full_address" => array(
            "Destination City/State/Zip:",
            "Destination City/State/Zip"
        ),
        "delivery_city" => array(
            "Destination City:",
            "Delivery City:",
            "Dest City:",
            "Moving To:",
            "dropoff_city:",
            "New City:",
            "Destination ",
            "Destination:",
        ),
        "delivery_state" => array(
            "Destination State:",
            "Delivery State:",
            "Dest State:",
            "dropoff_state_code:",
            "New State:",
        ),
        "delivery_zip" => array(
            "Destination Zip:",
            "Delivery Zip:",
            "Dest Zip:",
            "dropoff_zip:",
        ),
        "delivery_country" => array(
            "Delivery Country:",
            "Deliver Country:",
            "Country:",
        ),
        "moving_date" => array(
            "Move date:",
            "Move Date:", //01/15/2013
            "Proposed Ship Date:",
            "Moving Date:",
            "available_date:",
            "Pickup Date:",
            "estimated_ship_date:",
            "Service Date:",
            "Move Date ",
            "Ship Date:"
        ),
        "ship_via" => array(
            "Open/enclosed:", //OPEN
            "Type Of Carrier:", //Open
            "Trailer Type:",
            "ship_via_id:",
        ),
    );

    //Get strings
    $strings = preg_split("/[\n\r]+/s", $body);
    foreach ($strings as $string) {
        foreach ($parse_body as $key => $elements) {
            foreach ($elements as $value) {
                if ($parsed[$key] == "") {
                    $a = explode($value, $string);
                    if (isset($a[1])) {
                        $parsed[$key] = strip_tags(trim($a[1]));
                    }
                }
            }
        }
        
        for ($k = 1; $k <= 10; $k++) {
            //$counter = $k - 1;
            foreach ($parse_vehicle1 as $key => $elements) {
                foreach ($elements as $value) {
                    if (!isset($parsed['vehicles'][$k][$key]) || $parsed['vehicles'][$k][$key] == "") {
                        if ($k == 1)
                            $a = explode($value . ":", $string);
                        else
                            $a = explode($value . $k . ":", $string);
                        if (isset($a[1])) {
                            $parsed['vehicles'][$k][$key] = strip_tags(trim($a[1]));
                        }
                    }
                }
            }
        }
    }

    //Moving date
    // if ($parsed['moving_date'] != "") {
    //     $d_arr = explode("/", $parsed['moving_date']);
    //     if (count($d_arr) == 3) {
    //         $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int) $d_arr[1], $d_arr[2]));
    //     } else {
    //         $d_arr = explode("-", $parsed['moving_date']);
    //         if (count($d_arr) == 3) {
    //             $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int) $d_arr[1], $d_arr[2]));
    //         } else {
    //             $parsed['moving_date'] = date("Y-m-d", strtotime($parsed['moving_date']));
    //         }
    //     }
    // }

    //Moving date
    if ($parsed['moving_date'] != "") {
        $d_arr = explode("/", $parsed['moving_date']);
        if (count($d_arr) == 3) {
            $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int) $d_arr[1], $d_arr[2]));
        } 
        else {
            $d_arr = explode("-", $parsed['moving_date']);
            if (count($d_arr) == 3) {
                $parsed['moving_date'] = (int)$d_arr[0] ."-". (int) $d_arr[1] ."-". (int)$d_arr[2];
            } else {
                $parsed['moving_date'] = date("Y-m-d", strtotime($parsed['moving_date']));
            }
        }
    }

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

    //detect state
    if (strlen($parsed["state"]) > 2) {
        $parsed['state'] = state2format($parsed['state'], $db);
    }

    if (strlen($parsed["delivery_state"]) > 2) {
        $parsed['delivery_state'] = state2format($parsed['state'], $db);
    }

    //set pickup address
    $parsed["pickup_city"] = $parsed["city"];
    $parsed["pickup_state"] = $parsed["state"];
    $parsed["pickup_zip"] = $parsed["zip"];
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

    for ($k = 1; $k <= 10; $k++) {
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
    
    if($parsed['pickup_full_address']){
        $pickupDetails = explode(',',$parsed['pickup_full_address']);
        $parsed['pickup_city'] = trim($pickupDetails[0]);
        $parsed['pickup_state'] = trim($pickupDetails[1]);
        $parsed['pickup_zip'] = trim($pickupDetails[2]);
    }

    if($parsed['delivery_full_address']){
        $deliveryDetails = explode(',',$parsed['delivery_full_address']);
        $parsed['delivery_city'] = trim($deliveryDetails[0]);
        $parsed['delivery_state'] = trim($deliveryDetails[1]);
        $parsed['delivery_zip'] = trim($deliveryDetails[2]);
    }

    return $parsed;
}

/**
 * Function to parse email from IMAP
 * 
 * @author Chetu Inc.
 * @version 1.0
 */
function custom_parser($body, $db) {
    $body = strip_tags($body);
    $body = str_replace("Content-Type: text/plain;", "", $body);
    $body = str_replace("Content-Type: text/html;", "", $body);
    
    $parsed = array();
    $parsed['vehicles'] = array();

    // helps in parsing vehicle count from email
    $vehicle_count = array(
        "count" => array(
            "count:",
            "count",
            "Count:",
            "Count"
        )
    );

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
        //Vehicle #2 Running Condition:
        //Does the vehicle run2?:
        ),
        "vin" => array(
            "Vehicle VIN",
            "vehicle_vin",
        ),
        "lot" => array(
            "Vehicle Lot",
            "vehicle_lot",
        ),
        "plate" => array(
            "Vehicle Plate",
            "vehicle_plate",
        ),
        "color" => array(
            "Vehicle Color",
            "vehicle_color",
        ),
    );

    $parse_vehicle2 = array(
        "year" => array(
            "Year2:",
            "Vehicle Year2:",
            "Manufactured Year2:",
            "year2:",
            "Comments / Year2:",
            "Year2 ",
            "Vehicle #2 Year:",
        ),
        "make" => array(
            "Make2:",
            "Vehicle Make2:",
            "make2:",
            "Auto Make2:",
            "Make2 ",
            "Vehicle #2 Make:",
        ),
        "model" => array(
            "Model2:",
            "Vehicle Model2:",
            "model2:",
            "Auto Model2:",
            "Model2 ",
            "Vehicle #2 Model:",
        ),
        "type" => array(
            "Type2:",
            "Vehicle Type2:",
            "vehicle_type_id2:",
            "Vehicle #2 Type:",
        ),
        "run" => array(
            "Running Condition2:",
            "Running condition2:", //YES
            "Vehicle Condition2:", //Running
            "Does the vehicle run2?:",
            "vehicle_condition2:",
            "Vehicle in Running Condition2:", //Yes
            "vehicle_runs2:",
            "Running Condition2 ",
            "Vehicle #2 Running Condition:",
        ),
        "vin" => array(
            "Vehicle VIN2:",
        ),
        "lot" => array(
            "Vehicle Lot2:",
        ),
        "plate" => array(
            "Vehicle Plate2:",
        ),
        "color" => array(
            "Vehicle Color2:",
        ),
    );

    //Lead Body parse array
    $parse_body = array(
        "source" => array(
            "Source:",
            "ID:",
        ),
        "first_name" => array(
            "Name:",
            "First Name:",
            "Customer Name:",
            "first_name:",
            "Name ",
        ),
        "last_name" => array(
            "Last Name:",
            "last_name:",
        ),
        "shipper_email" => array(
            "Customer Email:",
            "Email:",
            "Email Address:",
            "Customer E-mail:",
            "email:",
            "E-Mail Address:",
            "Email ",
        ),
        "phone1" => array(
            "Phone:",
            "Customer Phone:",
            "Phone number:",
            "Daytime Phone:",
            "Home Phone:",
            "phone:",
            "Home Phone ",
        ),
        "phone2" => array(
            "Shipper Phone 2:",
            "Evening Phone:",
            "Work Phone:",
            "Alternate Phone ",
        ),
        "fax" => array(
            "Shipper Fax:",
            "Fax:",
        ),
        "mobile" => array(
            "Customer Cell:",
            "Cell Phone:",
        ),
        "address" => array(
            "Shipper Address:",
        ),
        "address2" => array(
            "Shipper Address 2:",
        ),
        "city" => array(
            "Origin City:",
            "Pickup City:",
            "Moving From:",
            "pickup_city:",
            "From City:",
            "Origin ",
            "Origin:",
        ),
        "state" => array(
            "Origin State:",
            "Pickup State:",
            "pickup_state_code:",
            "From State:",
        ),
        "zip" => array(
            "Origin Zip:",
            "Pickup Zip:",
            "Pickup Zipcode:",
            "pickup_zip:",
            "From Zip Code:",
            "Current Zip code ",
        ),
        "country" => array(
            "Shipper Country:",
            "Pickup Country:",
        ),
        "delivery_city" => array(
            "Destination City:",
            "Delivery City:",
            "Dest City:",
            "Moving To:",
            "dropoff_city:",
            "New City:",
            "Destination ",
            "Destination:",
        ),
        "delivery_state" => array(
            "Destination State:",
            "Delivery State:",
            "Dest State:",
            "dropoff_state_code:",
            "New State:",
        ),
        "delivery_zip" => array(
            "Destination Zip:",
            "Delivery Zip:",
            "Dest Zip:",
            "dropoff_zip:",
            "Delivery Zipcode:"
        ),
        "delivery_country" => array(
            "Delivery Country:",
            "Deliver Country:",
            "Country:",
        ),
        "moving_date" => array(
            "Move date:",
            "Move Date:", //01/15/2013
            "Proposed Ship Date:",
            "Moving Date:",
            "available_date:",
            "Pickup Date:",
            "estimated_ship_date:",
            "Service Date:",
            "Move Date ",
            "Ship Date:"
        ),
        "ship_via" => array(
            "Open/enclosed:", //OPEN
            "Type Of Carrier:", //Open
            "Trailer Type:",
            "ship_via_id:",
        ),
    );

    //Get strings
    $strings = preg_split("/[\n\r]+/s", $body);
    foreach ($strings as $string) {
        $string = base64_decode($string);

        //for obtaining vehicle count
        foreach ($vehicle_count as $key => $elements) {
            foreach ($elements as $value) {
                if ($parsed[$key] == "") {
                    $a = explode($value, $string);
                    if (isset($a[1])) {
                        $parsed[$key] = strip_tags(trim($a[1]));
                    }
                }
            }
        }  

        foreach ($parse_body as $key => $elements) {
            foreach ($elements as $value) {
                if ($parsed[$key] == "") {
                    $a = explode($value, $string);
                    if (isset($a[1])) {
                        $parsed[$key] = strip_tags(trim($a[1]));
                    }
                }
            }
        }

        // getting total vehicle count
        $total_vehicle = (int)$parsed['count'];

       if($total_vehicle === 0){
        $total_vehicle = 1;
       }
        for ($k = 1; $k <= $total_vehicle; $k++) {            
            foreach ($parse_vehicle1 as $key => $elements) {
                foreach ($elements as $value) {                   
                    if (!isset($parsed['vehicles'][$k][$key]) || $parsed['vehicles'][$k][$key] == "") {
                        
                        if ($k == 1){ 
                            $a = explode($value, $string);
                            $a = explode(":", $a[1]);                          
                        } else {
                            $a = explode($value . $k . ":", $string);                            
                        }                        

                        if (isset($a[1])) {
                            $a[1] = str_replace("=","",$a[1]);
                            $parsed['vehicles'][$k][$key] = strip_tags(trim( $a[1] ));
                        } 
                    } 
                }
            }
        }
    }    

    //Moving date
    if ($parsed['moving_date'] != "") {
        $d_arr = explode("/", $parsed['moving_date']);
        if (count($d_arr) == 3) {
            $parsed['moving_date'] = date("Y-m-d", mktime(0, 0, 0, $d_arr[0], (int) $d_arr[1], $d_arr[2]));
        } 
        else {
            $d_arr = explode("-", $parsed['moving_date']);
            if (count($d_arr) == 3) {
                $parsed['moving_date'] = (int)$d_arr[0] ."-". (int) $d_arr[1] ."-". (int)$d_arr[2];
            } else {
                $parsed['moving_date'] = date("Y-m-d", strtotime($parsed['moving_date']));
            }
        }
    }

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

    //detect state
    if (strlen($parsed["state"]) > 2) {
        $parsed['state'] = state2format($parsed['state'], $db);
    }

    if (strlen($parsed["delivery_state"]) > 2) {
        $parsed['delivery_state'] = state2format($parsed['state'], $db);
    }

    //set pickup address
    $parsed["pickup_city"] = $parsed["city"];
    $parsed["pickup_state"] = $parsed["state"];
    $parsed["pickup_zip"] = $parsed["zip"];
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

    for ($k = 1; $k <= 10; $k++) {
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
    
    return $parsed;
}