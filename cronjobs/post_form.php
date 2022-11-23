<?php



/* * ************************************************************************************************

 * Cron RepostToCd

 *

 * Client:		FreightDragon

 * Version:		1.0

 * Date:			2011-04-26

 * Author:		C.A.W., Inc. dba INTECHCENTER

 * Address:	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076

 * E-mail:		techsupport@intechcenter.com

 * CopyRight 2011 FreightDragon. - All Rights Reserved

 * ************************************************************************************************** */

@session_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);
//print "------";
require_once("init.php");
require_once("../libs/phpmailer/class.phpmailer.php");
ob_start();
//print "-------";
$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);



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
	return $response;
	
}

$response = '';
if(isset($_POST) && $_POST['frmsubmit'] == "Test Now")
{
	
       $post = array(); 
	   $post['source'] = $_POST['source'];
	   $post['first_name'] = $_POST['first_name'];
	   $post['last_name'] = $_POST['last_name'];
	   $post['shipper_email'] = $_POST['shipper_email'];
	   $post['phone1'] = $_POST['phone1'];
	   $post['moving_date'] = $_POST['moving_date'];
	   $post['city'] = $_POST['city'];
	   $post['state'] = $_POST['state'];
	   $post['zip'] = $_POST['zip'];
	   $post['delivery_city'] = $_POST['delivery_city'];
	   $post['delivery_state'] = $_POST['delivery_state'];
	   $post['delivery_zip'] = $_POST['delivery_zip'];
	   $post['pickup_city'] = $_POST['pickup_city'];
	   $post['pickup_state'] = $_POST['pickup_state'];
	   $post['pickup_zip'] = $_POST['pickup_zip'];
	   $post['pickup_country'] = $_POST['pickup_country'];
	   $post['ship_via'] = $_POST['ship_via'];
	   
					   
	   
	   $post['type1'] = $_POST['type1'];
	   $post['year1'] = $_POST['year1'];
	   $post['make1'] = $_POST['make1'];
	   $post['model1'] = $_POST['model1'];
	   $post['run1'] = $_POST['run1'];
	   $post['vin1'] = $_POST['vin1'];
	   $post['lot1'] = $_POST['lot1'];
	   $post['plate1'] = $_POST['plate1'];
	   $post['color1'] = $_POST['color1'];
	   
	  
	   $post['CF_uses_custom_fields'] = $_POST['CF_uses_custom_fields'];
	   $post['broker_id'] = $_POST['broker_id'];
	   $post['utm_source'] = $_POST['utm_source'];
	   $post['utm_medium'] = $_POST['utm_medium'];
	   $post['utm_content'] = $_POST['utm_content'];
	   $post['utm_term'] = $_POST['utm_term'];
	   $post['utm_campaign'] = $_POST['utm_campaign'];
	   $post['type'] = $_POST['type'];



//print "http://freightdragondb.com/cronjobs/fdposturl.php";
//$response = post_to_url("http://freightdragon.com/cronjobs/fdposturl.php", $post);
$response = post_to_url("http://freightdragon.com/cronjobs/fdposturl.php", $post);

                  $source_id = $post['source'];
                  if(trim($source_id) !='' && $source_id>0)
					  {
						   try{
							 $lsm = new Leadsource($daffny->DB);
							 $lsm->load($source_id);
						   } catch (FDException $e) {
								//print "Lead source not found-: ".$source_id;
								//exit;
							}
					  }
					  else
					  {
						 //print "Lead source not found: ".$source_id;
								//exit; 
					  }
						
						$memberID = $lsm->owner_id;
	if($response == "Success" && $memberID==159)
	{
	   	
		sendMails();
		//header("Location: http://www.americancartransporters.com/thank-you/");
			   //exit;
		 
	}
}

function sendMails(){
	
		    $to = $_POST['shipper_email'];
		   
		    $mail = new FdMailer(true);
			$mail->isHTML();
			//$mail->From = 'admin@ritewayautotransport.com';
			//$mail->Subject = "A New Lead Was Created From www.americancartransporters.com";
			//$mail->AddAddress('mitchamericancartransporters.americancartransporters@freightdragon.com');  // Add a recipient
			//$mail->AddAddress('noreply@americancartransporters.com');               // Name is optional
			//$mail->AddAddress('neeraj@freightdragon.com');
			//$mail->AddCC("nkumar@agilesoftsolutions.com"); 
			//$mail->SetFrom($daffny->cfg['info_email']);
			
				//print "----sendmail";
			$mail->Body    = "
			
			Source: 88 
			<br>
			Ship Via: Open 
			<br>
			Move Date: ".date("m/d/Y", strtotime($_POST["moving_date"]))."
			<br>
			First Name: ".$_POST["first_name"]."
			<br>
			Last Name: ".$_POST["last_name"]."
			<br>
			Customer Email: ".$_POST["shipper_email"]."
			<br>
			Customer Phone: ".$_POST["phone1"]."
			<br>
			Origin City: ".$_POST["pickup_city"]."
			<br>
			Origin State: ".$_POST["pickup_state"]."
			<br>
			Origin Zip: 
			<br>
			Destination City: ".$_POST["delivery_city"]."
			<br>
			Destination State: ".$_POST["delivery_state"]."
			<br>
			Destination Zip: 
			<br>
			Vehicle Type: other
			<br>
			Vehicle Year: ".$_POST["year1"]."
			<br>
			Vehicle Make: ".$_POST["make1"]."
			<br>
			Vehicle Model: ".$_POST["model1"]."
			<br>
			Running Condition: Yes
			<br>
			<br>
			THIS IS AN AUTOMATED MESSAGE
			
							";
			
			//$mail->Send();
				
			// Email 2 - going to client
			//$mail->ClearAddresses();  
			$mail->From = 'admin@ritewayautotransport.com'; // Please add a from email address here
			$mail->AddAddress($_POST['shipper_email']);  // Add a recipient
			$mail->Subject = 'Thank you for your recent quote request from American Car Transporters';
			$mail->Body    = "
							<p><span style='font-size:12px; font-family:Arial;'>Hi  ".$_POST["first_name"]." ".$_POST["last_name"]." </span>,<br /><br /></p>
							<p><span style='font-size:12px; font-family:Arial;'><b>Welcome to American Car Transporters.</b><br/><br/> 
							Thank you for your interest in American Car Transporters.<br/><br>We have received your information and a representative from our company will be contacting you within 48 hours.<br /><br>If you require immediate assistance, please call <b>1-877-238-4718</b>.<br><br>We look forward to speaking with you.<br/><br/> Thanking you,<br/><b>American Car Transpoters</b></span></b><br /><br /></p>
							 <br/>
			";
			
			// END OF EMAILS
				
			if(!$mail->Send()) {
				header("Location: http://www.americancartransporters.com/thank-you/");
			   exit;
			   
			}
			else{
			  header("Location: http://www.americancartransporters.com/thank-you/");
			   exit;
			}
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
.main-container {
    margin: 0 auto;
    display: block;
    max-width: 1200px;
    height: 1015px;
}
#form-container {
	max-width: 500px;
  height: 100%;
  padding: 2%;
  background-color: #f1f1f1;
  border: 9px #FFFDFD solid;
  margin: 0 auto;
  overflow: auto;
  overflow-x: hidden;
  box-shadow: 0px 6px 12px 0px rgba(0, 0, 0, 0.5);
  z-index: 999;
}
#form-head {
    background-color: #f1f1f1;
    padding-top: 0px;
}

#form-head h4 {
    text-align: center;
    color: #717171;
    font-size: 1.2em;
    margin-bottom: 2px !important;
}

.act-form {
    height: auto;
    min-width: 350px;
    margin: 0 auto;
    width: 100%;
}
.half {
    width: 46%;
    height: 34px;
    float: left;
    margin: 20px 10px;
}
input, select {
	width: 100%;
	height: 100%;
	padding: 8px 12px !important;
	font-size: 13px;
	color: #767676;
	background-color: #fdfdfd;
	border: 1px solid #e3e3e3;
	outline: 0;
	box-sizing: border-box;
}

textarea {
	width: 100%;
	height: 200px;
	display: block;
	margin: 23px auto;
	padding: 0 20px;
	box-sizing: border-box;
}
#submitfrm {
    padding: 20px 15px;
    font-size: 24px;
    line-height: 100%;
    min-width: 96%;
    border: none !important;
    margin-left: 0px !important;
    margin-right: 0px !important;
    margin-bottom: 10px !important;
    letter-spacing: 0px;
    background-color: #FFD400;
    color: #1A1A1A!important;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    text-shadow: 0 1px rgba(0,0,0,.4);
    background-image: -webkit-gradient(linear,left top,left bottom,from(rgba(0,0,0,.07)),to(rgba(0,0,0,.15)));
    background-image: -webkit-linear-gradient(top,rgba(0,0,0,.07),rgba(0,0,0,.15));
    background-image: -moz-linear-gradient(top,rgba(0,0,0,.07),rgba(0,0,0,.15));
    background-image: -ms-linear-gradient(top,rgba(0,0,0,.07),rgba(0,0,0,.15));
    background-image: -o-linear-gradient(top,rgba(0,0,0,.07),rgba(0,0,0,.15));
    box-shadow: inset 0 0 1px rgba(0,0,0,.8);
}
</style>
</head>

<body>
<section class="main-container">
                <div id="form-container">
                                <div id="form-head">
                                                <h4>Freight Dragon Testing Form<br />
                                                <a>Post Method using URL</a></h4>
                                </div><!-- Form Top Ends-->
                  <?php
				    if($response!='')
					 {
					   print "<font color='red'>".$response."</font>";	
					}
				  ?>

                <form id="actform" action="" method="post" class="act-form" >

                                <!-- ------------------ Hidden Inputs ------------------88 -->
                                <div style="display: none;">
                                                <input type="hidden" name="utm_source" value="" />
                                                <input type="hidden" name="utm_medium" value="" />
                                                <input type="hidden" name="utm_term" value="" />
                                                <input type="hidden" name="utm_content" value="" />
                                                <input type="hidden" name="utm_campaign" value="" />
                                                <input type="hidden" name="type" value="" />

                                                <input type="hidden" name="source" value="117" title="source"/>
                                                <input type="hidden" name="pickup_zip" value="00000" title="pickup_zip"/>
                                                <input type="hidden" name="delivery_zip" value="00000" title="delivery_zip"/>
                                                <input type="hidden" name="pickup_country" value="US" title="pickup_country"/>
                                                <input type="hidden" name="ship_via" value="1" title="ship_via"/>
                                                <input type="hidden" name="type1" value="-1" title="type1"/>
                                                <input type="hidden" name="run1" value="Yes" title="run1"/>

                                </div><!-- Input Field Ends-->

                                <!-- ------------------ Shipper Information Inputs ------------------ -->
                                <span class="half">
                                                <input type="text" name="first_name" value=""   aria-required="true"  placeholder="First Name" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input type="text" name="last_name" value=""   aria-required="true"  placeholder="Last Name" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input type="email" name="shipper_email" value=""   aria-required="true"  placeholder="Email" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input type="tel" name="phone1" value=""  aria-required="true"  maxlength="15"  placeholder="Phone" pattern="\d{3}[\-]\d{3}[\-]\d{4}" required/>
                                </span><!-- Input Field Ends-->

                                <!-- ------------------ Vehichle Information Inputs ------------------ -->
                                <span class="half">
                                                <input id="make1" type="text" name="make1" value=""  aria-required="true"  placeholder="Vehicle Make" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input id="model1" type="text" name="model1" value=""  aria-required="true"   placeholder="Vehicle Model" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input id="VehicleYear" type="text" name="year1" value=""  aria-required="true"   placeholder="Vehicle Year" required/>
                                </span><!-- Input Field Ends-->

                                <!-- ------------------ Ship On Date Inputs ------------------ -->
                                <span class="half">
                                                <input type="date" name="moving_date" value="" aria-required="true" title="MM/DD/YYYY" placeholder="Ship on Date" required/>
                                </span><!-- Input Field Ends-->

                                <!-- ------------------ Pickup Inputs ------------------ -->
                                <span class="half">
                                                <input type="text" name="pickup_city" value=""  aria-required="true"  placeholder="Pick Up City" required/>
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <select class="pck" name="pickup_state" >
                                                <option value="" required>Select Pick Up State</option>
                                                               <option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
                                                                
                                                                
                                                </select>
                                                
                                                
                                </span><!-- Input Field Ends-->

                                <span class="half">
                                                <input type="text" name="delivery_city" value=""  aria-required="true"  placeholder="Delivery City" required/>
                                </span><!-- Input Field Ends-->




                                <!-- ------------------ Delivery Inputs ------------------ -->
                                <span class="half">
                                                <select class="pck" name="delivery_state" >
                                                                <option value="" required>Select Delivery State</option>
                                                                <option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option>
                                                </select>
                                </span><!-- Input Field Ends-->

                                                <!-- <textarea name="comments" class=""  placeholder="Additional Information">
                                                </textarea><!-- Input Field Ends-->

                                <input name="frmsubmit" type="submit" class="mk-button custom button-54c65e1834fc4 light-color  two-dimension xx-large rounded" id="submitfrm" value="Test Now"><!-- Input Btn Ends-->

                </form>
                </div>
</section>
<?php
       
$_SESSION['iamcron'] = false;
require_once("done.php");
?>
</body>
</html>