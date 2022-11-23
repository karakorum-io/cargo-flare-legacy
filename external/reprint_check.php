<?php

@session_start();



ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);
//print "------";
require_once("init.php");
//print "-------";
$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style>
*{
	box-sizing:border-box;
}
.container {
	width: 670px;
	margin: auto;
	padding: 3px;
}
.row {
	clear: left;
	width: 100%;
	display:inline-block;
}
input {
	border: none;
	height: 20px;
	width: 100%;
}
</style>
</head>
<body onload="window.print()">
<?php

$ID = $_GET["ent"];

//$ID = md5(36350);

//print "--".$ID;
        $row = $daffny->DB->select_one("*", "app_payments_check", "WHERE md5(id) = '" . $ID . "' ");
        if (!empty($row)) {
               //print "----".$row["id"];
            
               $entity = new Entity($daffny->DB);
               $entity->load($row['entity_id']);
			   
			   
			    $amount = $row['amount'];
				$amountFormat = $row['amount_format'];
			    $amountWords  = $row['amount_words'];      // gives Twelve thousand three hundred forty five dollars 67c
				//echo  $obj->number; 
				$printName = $row['print_name'];
				
				
				  
				  $company_name = $row['company_name'];
				  $address1 = $row['address1'];
				  $city = $row['city'];
				  $state = $row['state'];
				  $zip_code = $row['zip_code'];
				  
				  
		?>

        
<div class="container">
<div class="row" style="margin-top:70px;">
  <div style="float:right; padding-right:2px;">
    <?php print date('m/d/Y');?>
  </div>
</div>
<div class="row" style="margin-top:25px;">
  <div style="float:left; padding-left:35px;width:530px;">
      <?php print $printName;?> 
  </div>
  <div style="float:right;padding-right:30px;">
    &nbsp&nbsp<?php print $amountFormat;?>
  </div>
</div>
<div class="row" style="margin-top:10px;">
  <div style="float:left;width:475px; padding-left:10px;">
    <?php print ucwords($amountWords);?>
  </div>
</div>

<div class="row" style="margin-top:20px;">
  <div style="float:left; padding-left:50px;width:310px;">
    <?php print $printName;?> 
  </div>
</div>
<div class="row" style="">
  <div style="float:left; padding-left:50px;width:310px;">
    <?php print $address1;?>
  </div>
</div>
<div class="row" style="">
  <div style="float:left; padding-left:50px;width:310px;">
    <?php print $city;?>, <?php print $state;?> <?php print $zip_code;?>
  </div>
</div>
<div class="row" style="margin-top:10px;">
  <div style="float:left; padding-left:30px;width:260px;">
   Dispatch ID <?php print $entity->getNumber();?>
  </div>
  
</div>

<div class="row" style="margin-top:100px;">
  <div style="float:left; padding-left:5px;width:380px;">
    <?php print $printName;?> 
  </div>
  <div style="float:right; padding-right:5px;width:120px;">
     <?php print date('m/d/Y',strtotime($row['created']));?>
  </div>
</div>
<div class="row" style="margin-top:10px;">

  <table width="100%" cellpadding="1" cellspacing="1">
    <tr>
      <td width="33%">Date</td>
      <td  width="33%" align="center">Reference</td>
      <td  width="34%"  align="right">Payment</td>
    </tr>
    <tr>
      <td><?php print date('m/d/Y');?></td>
     
      <td align="center">#<?php print $entity->getNumber();?></td>
      
      <td  align="right"><?php print $amountFormat;?></td>
    </tr>
    <tr><td colspan="7">&nbsp;</td></tr>
   
  </table>
 
  
</div>


<div class="row" style="margin-top:100px;">
  
</div>


<div class="row" style="margin-top:155px;">
  <div style="float:left; padding-left:5px;width:295px;">
    <?php print $printName;?> 
  </div>
  <div style="float:right; padding-right:5px;width:120px;">
     <?php print date('m/d/Y');?>
  </div>
</div>
<div class="row" style="margin-top:10px;">

  <table width="100%" cellpadding="1" cellspacing="1">
    <tr>
      <td  width="33%">Date</td>
      <td  width="33%" align="center">Reference</td>
      
      <td  width="34%"  align="right">Payment</td>
    </tr>
    <tr>
      <td><?php print date('m/d/Y');?></td>
      
      <td align="center">#<?php print $entity->getNumber();?></td>
     
      <td align="right"><?php print $amountFormat;?></td>
    </tr>
    <tr><td colspan="7">&nbsp;</td></tr>
      </table>
 
  
</div>


<div class="row" style="margin-top:110px;">
</div>



               <?php
			  
        } 
       
$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    //require_once("done.php");

?>
</body>
</html>

