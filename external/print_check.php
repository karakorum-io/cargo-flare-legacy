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
        $row = $daffny->DB->select_one("id", "app_entities", "WHERE md5(id) = '" . $ID . "' ");
        if (!empty($row)) {
               //print "----".$row["id"];
            
               $entity = new Entity($daffny->DB);
               $entity->load($row['id']);
			   
			   $carrier = $entity->getCarrier();
			   if(!is_null($carrier))
			   {
			   
			    $amount = $entity->getCarrierPay(false);
				$amountFormat = number_format((float)$entity->carrier_pay_stored, 2, ".", ",");
			    $obj = new toWords( number_format((float)$entity->carrier_pay_stored, 2, ".", "") , 'dollars','c');
				$amountWords  = $obj->words;      // gives Twelve thousand three hundred forty five dollars 67c
				//echo  $obj->number; 
                $printName = ucfirst($carrier->company_name);
				if($carrier->print_name!='')
				{
				  $printName = ucfirst($carrier->print_name);
				  $company_name = $printName;
				  
				} else 
				{
				  $company_name = ucfirst($carrier->company_name);
				}   
				  
				  $address1 = $carrier->address1;
				  $address2 = $carrier->address2;
				  $city = $carrier->city;
				  $state = $carrier->state;
				  $zip_code = $carrier->zip_code;
				  
				  if($carrier->print_check == 1)
				  {
					  $printName = ucfirst($carrier->print_name);
					  $company_name = $printName;
					  $address1 = $carrier->print_address1;
					  $address2 = $carrier->print_address2;
					  $city = $carrier->print_city;
					  $state = $carrier->print_state;
					  $zip_code = $carrier->print_zip_code;
				  }
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
    &nbsp;&nbsp;&nbsp;&nbsp;<?php print $amountFormat;?>
  </div>
</div>
<div class="row" style="margin-top:10px;">
  <div style="float:left;width:475px; padding-left:10px;">
    <?php print ucwords($amountWords);?>
  </div>
</div>

<div class="row" style="margin-top:20px;">
  <div style="float:left; padding-left:50px;width:310px;">
    <?php print $company_name;?> 
  </div>
</div>
<div class="row" style="">
  <div style="float:left; padding-left:50px;width:310px;">
    <?php print $address1;?> <?php print $address2;?>
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
    <?php print $company_name;?> 
  </div>
  <div style="float:right; padding-right:5px;width:120px;">
     <?php print date('m/d/Y');?>
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
    <?php print $company_name;?> 
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
			   else
			   print "Carrier not found, Check not created.";
        } 
       
$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    //require_once("done.php");

?>
</body>
</html>

<?php
define("MAJOR", 'pounds');
define("MINOR", 'p');
class toWords  {
           var $pounds;
           var $pence;
           var $major;
           var $minor;
           var $words = '';
           var $number;
           var $magind;
           var $units = array('','one','two','three','four','five','six','seven','eight','nine');
           var $teens = array('ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen');
           var $tens = array('','ten','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety');
           var $mag = array('','thousand','million','billion','trillion');
    function toWords($amount, $major=MAJOR, $minor=MINOR) {
             $this->major = $major;
             $this->minor = $minor;
             $this->number = number_format($amount,2);
             list($this->pounds,$this->pence) = explode('.',$this->number);
             $this->words = " $this->major $this->pence$this->minor";
             if ($this->pounds==0)
                 $this->words = "Zero $this->words";
             else {
                 $groups = explode(',',$this->pounds);
                 $groups = array_reverse($groups);
                 for ($this->magind=0; $this->magind<count($groups); $this->magind++) {
                      if (($this->magind==1)&&(strpos($this->words,'hundred') === false)&&($groups[0]!='000'))
                           $this->words = ' and ' . $this->words;
                      $this->words = $this->_build($groups[$this->magind]).$this->words;
                 }
             }
    }
    function _build($n) {
             $res = '';
             $na = str_pad("$n",3,"0",STR_PAD_LEFT);
             if ($na == '000') return '';
             if ($na{0} != 0)
                 $res = ' '.$this->units[$na{0}] . ' hundred';
             if (($na{1}=='0')&&($na{2}=='0'))
                  return $res . ' ' . $this->mag[$this->magind];
             $res .= $res==''? '' : ' and';
             $t = (int)$na{1}; $u = (int)$na{2};
             switch ($t) {
                     case 0: $res .= ' ' . $this->units[$u]; break;
                     case 1: $res .= ' ' . $this->teens[$u]; break;
                     default:$res .= ' ' . $this->tens[$t] . ' ' . $this->units[$u] ; break;
             }
             $res .= ' ' . $this->mag[$this->magind];
             return $res;
    }
}
?>
