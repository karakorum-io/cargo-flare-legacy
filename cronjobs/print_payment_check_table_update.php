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

require_once("init.php");

ob_start();

//set_time_limit(800);

//error_reporting(E_ALL | E_NOTICE);

require_once("init.php");

$_SESSION['iamcron'] = true; // Says I am cron for Full Access



set_time_limit(800000);
ini_set('memory_limit', '3500M');
ini_set('upload_max_filesize', '128M');
ini_set('post_max_size', '128M');
ini_set('max_input_time', 800000);

$where = " deleted =0 order by id desc ";

print  $where;
$i=0;
$rows = $daffny->DB->selectRows('id,entity_id', " app_payments_check ", "WHERE ".$where);
$insertStr = "";
  if(!empty($rows))
  {
	     $objQuickbook = new QueueQuickbook();
	     foreach ($rows as $row) {
			       print "<br>---------id: ".$row['id']."---".$row['entity_id']."---<br>";
				    $entity = new Entity($daffny->DB);
					$entity->load($row['entity_id']);
					
					$carrier = $entity->getCarrier();
			            if(!is_null($carrier))
						   {
						   print "--inside--";
								$amount = (float)$entity->carrier_pay_stored;
								$amountFormat = number_format((float)$entity->carrier_pay_stored, 2, ".", ",");
								$obj = new toWords( number_format((float)$entity->carrier_pay_stored, 2, ".", "") , 'dollars','c');
								$amountWords  = $obj->words;      // gives Twelve thousand three hundred forty five dollars 67c
								//echo  $obj->number; 
								$printName = ucfirst($carrier->company_name);
								if($carrier->print_name!='')
								  $printName = ucfirst($carrier->print_name);
								  
								  $company_name = ucfirst($carrier->company_name);
								  $address1 = $carrier->address1;
								  $city = $carrier->city;
								  $state = $carrier->state;
								  $zip_code = $carrier->zip_code;
								  
								  if($carrier->print_check == 1)
								  {
									  $address1 = $carrier->print_address1;
									  $city = $carrier->print_city;
									  $state = $carrier->print_state;
									  $zip_code = $carrier->print_zip_code;
								  }
						   }
						   
						   $upd_arr = array(
													//'amount' => $entity->carrier_pay_stored,
													'amount_format' => $amountFormat,
													'amount_words' => $amountWords,
													'print_name' => $printName,
													'company_name' => $company_name,
													'address1' => $address1,
													'city' => $city,
													'state' => $state,
													'zip_code' => $zip_code
													
												);
						   
						  // $daffny->DB->update("app_payments_check",$upd_arr, "id = '" . $row['id'] . "' ");
						   
						   print $carrier->id."----Updated<br>";
						   
						   $i++;
			 }
		
  }
 
	$numRows = count($rows);
		print "<br> $i: numRows : ".$numRows;						  

$_SESSION['iamcron'] = false;


		
//send mail to Super Admin

    require_once("done.php");
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