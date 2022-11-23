<?php
/* * ************************************************************************************************
 * Cron RepostToCd
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-04-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 * ************************************************************************************************** */

@session_start();
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();

$_SESSION['iamcron'] = true; // Says I am cron for Full Access

set_time_limit(80000000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 80000000);
 
$_SESSION['iamcron'] = false;

function post_to_url($url, $data) {

        $ch = curl_init ($url);
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);  
        
        if(!response){
                die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
            }
        curl_close($ch);
	print $response;
	exit;
}


       $post = array(); 
	  /* $post['Source'] = 48;
	   $post['First Name'] = "Neeraj23456";
	   $post['Last Name'] = "Thakur2346";
	   $post['Customer Email'] = "neeraj@freightdragon.com";
	   $post['Customer Phone'] = "954-668-1278";
	   $post['Preferred Contact'] = "email";
	   $post['Move Date'] = "2015-10-30";
	   $post['Origin City'] = "Miramar";
	   $post['Origin State'] = "FL";
	   $post['Origin Zip'] = "33025";
	   $post['Destination City'] = "Irving";
	   $post['Destination State'] = "TX";
	   $post['Destination Zip'] = "75038";
	   
	   $post['Vehicle Type'] = "Midsize Sedan";
	   $post['Vehicle Year'] = 2014;
	   $post['Vehicle Make'] = "Maxima";
	   $post['Vehicle Model'] = "Nissan";
	   $post['Running Condition'] = "Yes";
	   $post['Vehicle VIN'] = "0987676477";
	   $post['Vehicle Lot'] = "lot74885";
	   $post['Vehicle Plate'] = "CT83948";
	   $post['Vehicle Color'] = "Beige";
	   */
	   
	   $post['source'] = 89;
	   $post['first_name'] = "Neeraj";
	   $post['last_name'] = "Thakur";
	   $post['shipper_email'] = "neeraj@freightdragon.com";
	   $post['phone1'] = "954-668-1278";
	   $post['moving_date'] = "2015-10-30";
	   $post['pickup_city'] = "Miramar";
	   $post['pickup_state'] = "FL";
	   $post['pickup_zip'] = "33027";
	   $post['delivery_city'] = "New York";
	   $post['delivery_state'] = "NY";
	   $post['delivery_zip'] = "10001";
	   
	   $post['pickup_country'] = "";
	   $post['ship_via'] = 1;
	   
	   
	   

	   
	   $post['type1'] = "Midsize Sedan";
	   $post['year1'] = 2016;
	   $post['make1'] = "Maxima";
	   $post['model1'] = "Nissan";
	   $post['run1'] = "Yes";
	   $post['vin1'] = "435345345435";
	   $post['lot1'] = "lot56456";
	   $post['plate1'] = "CT3345";
	   $post['color1'] = "Red";
	   
	   $post['type2'] = "Midsize Sedan";
	   $post['year2'] = 2014;
	   $post['make2'] = "Toyota";
	   $post['model2'] = "Camery";
	   $post['run2'] = "Yes";
	   $post['vin2'] = "0987676477";
	   $post['lot2'] = "lot74832";
	   $post['plate2'] = "CT83948";
	   $post['color2'] = "Blue";
	   
	   $post['type3'] = "Midsize Sedan";
	   $post['year3'] = 2010;
	   $post['make3'] = "Toyota";
	   $post['model3'] = "carola";
	   $post['run3'] = "Yes";
	   $post['vin3'] = "56456464564";
	   $post['lot3'] = "lot74832";
	   $post['plate3'] = "CT83948";
	   $post['color3'] = "Gold";
	   
	   $post['CF_uses_custom_fields'] = "Y";
	   $post['broker_id'] = "936d76f3895d2aebc0635e520df8313f";
	   $post['utm_source'] = "";
	   $post['utm_medium'] = "";
	   $post['utm_content'] = "";
	   $post['utm_term'] = "";
	   $post['utm_campaign'] = "";
	   $post['type'] = "";



post_to_url("http://freightdragon.com/cronjobs/fdposturl.php", $post);



    require_once("done.php");


exit;
?>