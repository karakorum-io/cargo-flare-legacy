<?php

/**
 * Example Web Connector application
 * 
 * This is a very simple application that allows someone to enter a customer 
 * name into a web form, and then adds the customer to QuickBooks.
 * 
 * @author Keith Palmer <keith@consolibyte.com>
 * 
 * @package QuickBooks
 * @subpackage Documentation
 */
/***************************** CUSTOMERS ******************************/
/**
 * Generate a qbXML response to add a particular customer to QuickBooks
 */
 

function _quickbooks_customer_query_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	  
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="8.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <CustomerQueryRq requestID="' . $requestID . '">
      <FullName>neeraj shipper</FullName>
      <OwnerID>0</OwnerID>
    </CustomerQueryRq>  
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
} 



/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_customer_query_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	


  mysql_query("
		UPDATE 
			quickbooks_queue 
		SET 
			response = '" . mysql_real_escape_string($xml) . "'
		WHERE 
			id = " . (int) $ID);
  
	
}
 
function _quickbooks_customer_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$customerName = $arr['company_name']." (".$ID.") ";
	
	if($arr['company_name']=="")
	  $customerName = $arr['first_name']." ".$arr['last_name']." (".$ID.") ";
	
	//.$arr['first_name']." ".$arr['last_name']." ".$arr['city']." ".$arr['state']." ".$arr['zip_code']
	   $nodes='<Name>' . $customerName . '</Name>
						<CompanyName>' . $arr['company_name'] . '</CompanyName>
						<FirstName>' . $arr['first_name'] . '</FirstName>
						<LastName>' . $arr['last_name'] . '</LastName>
						<BillAddress>
							<Addr1>' . $arr['address1'] . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</BillAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . $arr['contact_name1'] . '</Contact>
						<AltContact>' . $arr['contact_name2'] . '</AltContact>';
						
	$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="2.0"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
				<CustomerAddRq requestID="' . $requestID . '">
					<CustomerAdd>
						'.$nodes.'
					</CustomerAdd>
				</CustomerAddRq>
			</QBXMLMsgsRq>
		</QBXML>';
	
	return $xml;
}


function _quickbooks_customer_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
}

/**
 * Generate a qbXML response to add a particular customer to QuickBooks
 */
function _quickbooks_customer_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$customerName = $arr['company_name']." (".$ID.") ";
	
	if($arr['company_name']=="")
	  $customerName = $arr['first_name']." ".$arr['last_name']." (".$ID.") ";
	
	
	    $nodes='<Name>' . $customerName . '</Name>
						<CompanyName>' . $arr['company_name'] . '</CompanyName>
						<FirstName>' . $arr['first_name'] . '</FirstName>
						<LastName>' . $arr['last_name'] . '</LastName>
						<BillAddress>
							<Addr1>' . $arr['address1'] . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</BillAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . $arr['contact_name1'] . '</Contact>
						<AltContact>' . $arr['contact_name2'] . '</AltContact>
						';
				if($arr['shipper_type'] =="Residential" || $arr['shipper_type'] =="Commercial")		
				{	
				/*
				$nodes.="<CustomerTypeRef> 
                              <FullName>".$arr['shipper_type']."</FullName>
                         </CustomerTypeRef>";
				*/
				//$nodes.="<Other 1>".$arr['shipper_type']."</Other 1>";
				}
						
						
		$xml='<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="2.0"?>
				<QBXML>
				  <QBXMLMsgsRq onError="stopOnError">
					<CustomerModRq requestID="'.$requestID.'">
					  <CustomerMod>
						<ListID>' . $arr['quickbooks_listid'] . '</ListID>
						<EditSequence>' . $arr['quickbooks_editsequence'] . '</EditSequence>
				 
						'.$nodes.'
				 
					  </CustomerMod>
					</CustomerModRq>
				  </QBXMLMsgsRq>
				</QBXML>';
		
	
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_customer_update_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
}




/***************************** VENDORS ******************************/

function _quickbooks_vendor_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	/*
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	
	    $vendorName = $rowDispatch['carrier_company_name']." (".$rowDispatch['account_id'].") ";
	
	   $nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . $rowDispatch['carrier_company_name'] . '</CompanyName>
						<VendorAddress>
							<Addr1>' . $rowDispatch['carrier_address'] . '</Addr1>
							<City>' . $rowDispatch['carrier_city'] . '</City>
							<State>' . $rowDispatch['carrier_state'] . '</State>
							<PostalCode>' . $rowDispatch['carrier_zip'] . '</PostalCode>
							<Country>' . $rowDispatch['carrier_country'] . '</Country>
						</VendorAddress>
						<Phone>' . $rowDispatch['carrier_phone_1'] . '</Phone>
						<AltPhone>' . $rowDispatch['carrier_phone_2'] . '</AltPhone>
						<Email>' . $rowDispatch['carrier_email'] . '</Email>
						<Contact>' . $rowDispatch['carrier_contact_name'] . '</Contact>
						<AltContact>' . $rowDispatch['carrier_print_name'] . '</AltContact>';
		
	*/
	/******************** Used for cron  ******************/
	
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	  $vendorName = $arr['company_name']." (".$ID.") ";
	  $nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . $arr['company_name'] . '</CompanyName>
						<VendorAddress>
							<Addr1>' . $arr['address1'] . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</VendorAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . $arr['contact_name1'] . '</Contact>
						<AltContact>' . $arr['contact_name2'] . '</AltContact>';
						
				
						
	$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="2.0"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
				<VendorAddRq requestID="' . $requestID . '">
					<VendorAdd>
						'.$nodes.'
					</VendorAdd>
				</VendorAddRq>
			</QBXMLMsgsRq>
		</QBXML>';
	
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

  /*
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 if($rowDispatch['account_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['account_id']);
	 }
	 */
	 
	 mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $ID);
}

/**
 * Generate a qbXML response to add a particular customer to QuickBooks
 */
function _quickbooks_vendor_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 $result = mysql_query("SELECT * FROM app_accounts WHERE id = " . $rowDispatch['account_id']);
	// Grab the data from our MySQL database
	 $arr = mysql_fetch_assoc($result);
	 
	$vendorName = $rowDispatch['carrier_company_name']." (".$rowDispatch['account_id'].") ";
	
	    $nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . $rowDispatch['carrier_company_name'] . '</CompanyName>
						<VendorAddress>
							<Addr1>' . $rowDispatch['carrier_address'] . '</Addr1>
							<City>' . $rowDispatch['carrier_city'] . '</City>
							<State>' . $rowDispatch['carrier_state'] . '</State>
							<PostalCode>' . $rowDispatch['carrier_zip'] . '</PostalCode>
							<Country>' . $rowDispatch['carrier_country'] . '</Country>
						</VendorAddress>
						<Phone>' . $rowDispatch['carrier_phone_1'] . '</Phone>
						<AltPhone>' . $rowDispatch['carrier_phone_2'] . '</AltPhone>
						<Email>' . $rowDispatch['carrier_email'] . '</Email>
						<Contact>' . $rowDispatch['carrier_contact_name'] . '</Contact>
						<AltContact>' . $rowDispatch['carrier_print_name'] . '</AltContact>';
						
		$xml='<?xml version="1.0" encoding="utf-8"?>
				<?qbxml version="2.0"?>
				<QBXML>
				  <QBXMLMsgsRq onError="stopOnError">
					<VendorModRq requestID="'.$requestID.'">
					  <VendorMod>
						<ListID>' . $arr['quickbooks_listid'] . '</ListID>
						<EditSequence>' . $arr['quickbooks_editsequence'] . '</EditSequence>
				 
						'.$nodes.'
				 
					  </VendorMod>
					</VendorModRq>
				  </QBXMLMsgsRq>
				</QBXML>';
		
	
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_update_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

   $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 if($rowDispatch['account_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['account_id']);
	 }
}





/*******************INOICES*************/

function _quickbooks_invoice_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	
	$result = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	
	$resultShipper = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $arr['account_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	  
	   $number = "";
        if (trim($arr['prefix']) != "") {
            $number .= $arr['prefix'] . "-";
        }
        $number .= $arr['number'];
//<FullName>'.$arrShipper['company_name'].'</FullName>		
    $Desc='';
	if($arrShipper['shipper_type']=="Commercial")
	  $Desc='Order # '.$number.'  of '.$arrShipper['company_name'];
	elseif($arrShipper['shipper_type']=="Residential")
	  $Desc='Order # '.$number.'  of '.$arrShipper['first_name']." ".$arrShipper['last_name'];

$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="10.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <InvoiceAddRq requestID="' . $requestID . '">
      <InvoiceAdd>
        <CustomerRef>
		   <ListID>' . $arrShipper['quickbooks_listid'] . '</ListID>
		   
        </CustomerRef>
         
        <TxnDate>'.date('Y-m-d').'</TxnDate>
        <RefNumber>'.$number.'</RefNumber>
        <BillAddress>
          <Addr1>'.$arrShipper['address1'].'</Addr1>
          <City>'.$arrShipper['city'].'</City>
          <State>'.$arrShipper['state'].'</State>
          <PostalCode>'.$arrShipper['zip_code'].'</PostalCode>
          <Country>US</Country>
        </BillAddress>
        <PONumber>'.$arr['entityid'].'</PONumber>
        
        <Memo>Order id #'.$number.'</Memo>
 
        <InvoiceLineAdd>
          <ItemRef>
            <FullName>Transport Services</FullName>
          </ItemRef>
          <Desc>'.$Desc.'</Desc>
          <Quantity>1</Quantity>
          <Rate>'.$arr['total_tariff'].'</Rate>
        </InvoiceLineAdd>
 
        
      </InvoiceAdd>
    </InvoiceAddRq>
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_invoice_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

   mysql_query("
		UPDATE 
			app_entities
		SET 
			TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
	
}



function _quickbooks_invoice_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$resultShipper = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $arr['account_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);

         $number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];

    $Desc='';
	if($arrShipper['shipper_type']=="Commercial")
	  $Desc='Order # '.$number.'  of '.$arrShipper['company_name'];
	elseif($arrShipper['shipper_type']=="Residential")
	  $Desc='Order # '.$number.'  of '.$arrShipper['first_name']." ".$arrShipper['last_name'];
//<FullName>'.$arrShipper['company_name'].'</FullName>
$xml='<?xml version="1.0" ?>
<?qbxml version="6.0"?>
<QBXML>
    <QBXMLMsgsRq onError="stopOnError">
        <InvoiceModRq requestID="'.$requestID.'">
            <InvoiceMod>
			    
                <TxnID>'.$arrEnt['TxnID'].'</TxnID>
                <EditSequence>'.$arrEnt['EditSequence'].'</EditSequence>
                <CustomerRef>
                    <ListID>'.$arrShipper['quickbooks_listid'].'</ListID>
                    
                </CustomerRef>
                
                <InvoiceLineMod>
				   <TxnLineID>-1</TxnLineID>
                    <ItemRef>
                        
                        <FullName>Transport Services</FullName>
                    </ItemRef>
					<Desc>'.$Desc.'</Desc>
                    <Quantity>1</Quantity>
                    <Rate>'.$arr['total_tariff_stored'].'</Rate>
                </InvoiceLineMod>
            </InvoiceMod>
        </InvoiceModRq>
    </QBXMLMsgsRq>
</QBXML>';
	/*
	<CustomerRef>
          <FullName>'.$arrShipper['company_name'].'</FullName>
        </CustomerRef>
	*/
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_invoice_update_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

  mysql_query("
		UPDATE 
			app_entities
		SET 
			TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
}



/*******************INOICES*************/

function _quickbooks_recieve_payment_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$arrPayment = mysql_query("SELECT * FROM app_payments WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrPayment = mysql_fetch_assoc($arrPayment);
	
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $arrPayment['entity_id']);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $arrPayment['entity_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	
	
	$resultShipper = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $arr['account_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	
    $RefNumber=$arrPayment['transaction_id'];
    if($arrPayment['transaction_id']=='')
	   $RefNumber=$arr['entityid'];
	
	 $number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];
	
  $paymentType='';
switch ($arrPayment['method']) {
				case "9":
				  /*
					$insert_arr['cc_number'] = $_POST['cc_numb'];
					if ($_POST['cc_type'] != 0) {
						$insert_arr['cc_type'] = $_POST['cc_type'];
					} else {
						$insert_arr['cc_type'] = $_POST['cc_type_other'];
					}
					$insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
					$insert_arr['cc_auth'] = $_POST['cc_auth'];
					*/
					$paymentType='CHECK';
					break;
					
				case "1":
				case "2":
				case "3":
				case "4":
					$paymentType='CHECK';
					break;
			     case "5":
					$paymentType='Cash';
					break;
				 	
                   }
				   
$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="2.1"?>
<QBXML>
	<QBXMLMsgsRq onError="stopOnError">
		<ReceivePaymentAddRq>
			<ReceivePaymentAdd>
				<CustomerRef>
					<ListID>' . $arrShipper['quickbooks_listid'] . '</ListID>
					
				</CustomerRef>
				
				<TxnDate>'.date('Y-m-d').'</TxnDate>
				<RefNumber>' . $RefNumber . '</RefNumber>
				<TotalAmount>' . $arrPayment['amount'] . '</TotalAmount>
				<PaymentMethodRef>
					<FullName>'.$paymentType.'</FullName>
				</PaymentMethodRef>
				<Memo>Payment for order # '.$number.'</Memo>
				<AppliedToTxnAdd>
					<TxnID>'.$arrEnt['TxnID'].'</TxnID>
					<PaymentAmount>' . $arrPayment['amount'] . '</PaymentAmount>
				</AppliedToTxnAdd>
				
			
			</ReceivePaymentAdd>
		</ReceivePaymentAddRq>
	</QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
}

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_recieve_payment_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

   /*
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
	*/
}

/**** With entity id ****/

function _quickbooks_vendor_bill_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{

	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $rowDispatch['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];

$xml='<?xml version="1.0" encoding="ISO-8859-1"?>
<?qbxml version="7.0"?>
<QBXML>
  <QBXMLMsgsRq onError="continueOnError">
    <BillAddRq>
      <BillAdd>
        <VendorRef>
          <ListID>'.$arr['quickbooks_listid'].'</ListID>
        </VendorRef>
		<RefNumber>'.$number.'</RefNumber>
		   <ItemLineAdd> 
			  <ItemRef>
				<FullName>Transport Services</FullName>
			  </ItemRef>
            <Desc>Item added for Order #'.$number.'</Desc> 
			<Quantity>1</Quantity> 
			<Cost>'.$arrEnt['total_carrier_pay'].'</Cost> 
			<Amount>'.$arrEnt['total_carrier_pay'].'</Amount>
        </ItemLineAdd>
      </BillAdd>
    </BillAddRq>
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
}


/**** With payment id ****/
/*
function _quickbooks_vendor_bill_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	
	$arrPayment = mysql_query("SELECT * FROM app_payments WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrPayment = mysql_fetch_assoc($arrPayment);
	
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $arrPayment['entity_id']);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	
	
	
	$resultShipper = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . (int) $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];

$xml='<?xml version="1.0" encoding="ISO-8859-1"?>
<?qbxml version="7.0"?>
<QBXML>
  <QBXMLMsgsRq onError="continueOnError">
    <BillAddRq>
      <BillAdd>
        <VendorRef>
          <ListID>'.$arrShipper['quickbooks_listid'].'</ListID>
        </VendorRef>
		<RefNumber>'.$number.'</RefNumber>
		   <ItemLineAdd> 
			  <ItemRef>
				<FullName>Transport Services</FullName>
			  </ItemRef>
            <Desc>Item added for Order #'.$number.'</Desc> 
			<Quantity>1</Quantity> 
			<Cost>'.$arrPayment['amount'].'</Cost> 
			<Amount>'.$arrPayment['amount'].'</Amount>
        </ItemLineAdd>
      </BillAdd>
    </BillAddRq>
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
}
*/

/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_bill_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 if($rowDispatch['account_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['account_id']);
	 }
}


function _quickbooks_vendor_credit_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{

	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $rowDispatch['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];
/*
$xml='<?xml version="1.0" encoding="ISO-8859-1"?>
<?qbxml version="7.0"?>
<QBXML>
  <QBXMLMsgsRq onError="continueOnError">
    <BillAddRq>
      <BillAdd>
        <VendorRef>
          <ListID>'.$arr['quickbooks_listid'].'</ListID>
        </VendorRef>
		<RefNumber>'.$number.'</RefNumber>
		   <ItemLineAdd> 
			  <ItemRef>
				<FullName>Transport Services</FullName>
			  </ItemRef>
            <Desc>Item added for Order #'.$number.'</Desc> 
			<Quantity>1</Quantity> 
			<Cost>'.$arrEnt['total_carrier_pay'].'</Cost> 
			<Amount>'.$arrEnt['total_carrier_pay'].'</Amount>
        </ItemLineAdd>
      </BillAdd>
    </BillAddRq>
  </QBXMLMsgsRq>
</QBXML>';

<APAccountRef>
                    <FullName>Accounts Payable</FullName>
                </APAccountRef>

<IsTaxIncluded>false</IsTaxIncluded>
                <SalesTaxCodeRef>
                    <FullName>S</FullName>
                </SalesTaxCodeRef>
				
				<SalesTaxCodeRef>
                        <ListID>80000003-1452248465</ListID>
                        <FullName>Z</FullName>
                    </SalesTaxCodeRef>
                    <OverrideItemAccountRef>
                        <FullName>cost of goods sold</FullName>
                    </OverrideItemAccountRef>
					
					<SalesTaxCodeRef>
                        <ListID>80000001-1452248465</ListID>
                        <FullName>S</FullName>
                    </SalesTaxCodeRef>
                    <OverrideItemAccountRef>
                        <FullName>cost of goods sold</FullName>
                    </OverrideItemAccountRef>
	*/
$xml='<?xml version="1.0" encoding="UTF-8"?>
<?qbxml version="8.0"?>
<QBXML>
    <QBXMLMsgsRq onError="continueOnError">
        <VendorCreditAddRq requestID="'.$requestID.'">
            <VendorCreditAdd>
                <VendorRef>
                    <ListID>'.$arr['quickbooks_listid'].'</ListID>
                </VendorRef>
                
                <TxnDate>'.date('Y-m-d').'</TxnDate>
                <RefNumber>'.$number.'</RefNumber>
                <Memo>Credit for #order '.$number.'</Memo>
                
                <ItemLineAdd>
                    <ItemRef>
                        <FullName>speed pay discount</FullName>
                    </ItemRef>
                    <Desc>Credit for #order '.$number.'</Desc>
					<Cost>'.$arrEnt['total_carrier_pay'].'</Cost> 
                    <Amount>'.$arrEnt['total_carrier_pay'].'</Amount>
                    
                </ItemLineAdd>
               
            </VendorCreditAdd>
        </VendorCreditAddRq>
    </QBXMLMsgsRq>
</QBXML>';	
	
	return $xml;
}



/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_credit_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 if($rowDispatch['account_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['account_id']);
	 }
}



/**
 * Catch and handle an error from QuickBooks
 */
function _quickbooks_error_catchall($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg)
{
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_errnum = '" . mysql_real_escape_string($errnum) . "', 
			quickbooks_errmsg = '" . mysql_real_escape_string($errmsg) . "'
		WHERE 
			id = " . (int) $ID);
}



/*

function _quickbooks_customer_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
		
		
	$result = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	
	$resultShipper = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $arr['account_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	  
	   $number = "";
        if (trim($arr['prefix']) != "") {
            $number .= $arr['prefix'] . "-";
        }
        $number .= $arr['number'];
		
    $Desc='';
	if($arrShipper['shipper_type']=="Commercial")
	  $Desc='Order # '.$number.'  of '.$arrShipper['company_name'];
	elseif($arrShipper['shipper_type']=="Residential")
	  $Desc='Order # '.$number.'  of '.$arrShipper['first_name']." ".$arrShipper['last_name'];
	  

						
	$xmlCustomer = '<CustomerAddRq requestID="' . $requestID . '">
					<CustomerAdd>
						<Name>' . $arrShipper['company_name'] . '</Name>
						<CompanyName>' . $arrShipper['company_name'] . '</CompanyName>
						<FirstName>' . $arrShipper['first_name'] . '</FirstName>
						<LastName>' . $arrShipper['last_name'] . '</LastName>
						<BillAddress>
							<Addr1>' . $arrShipper['address1'] . '</Addr1>
							<City>' . $arrShipper['city'] . '</City>
							<State>' . $arrShipper['state'] . '</State>
							<PostalCode>' . $arrShipper['zip_code'] . '</PostalCode>
							<Country>' . $arrShipper['country'] . '</Country>
						</BillAddress>
						<Phone>' . $arrShipper['phone1'] . '</Phone>
						<AltPhone>' . $arrShipper['phone2'] . '</AltPhone>
						<Email>' . $arrShipper['email'] . '</Email>
						<Contact>' . $arrShipper['contact_name1'] . '</Contact>
						<AltContact>' . $arrShipper['contact_name2'] . '</AltContact>
					</CustomerAdd>
				</CustomerAddRq>';	  

$xmlInvoice = '
       <InvoiceAdd>
        <CustomerRef>
          <FullName>'.$arrShipper['company_name'].'</FullName>
        </CustomerRef>
         
        <TxnDate>'.date('Y-m-d').'</TxnDate>
        <RefNumber>'.$number.'</RefNumber>
        <BillAddress>
          <Addr1>'.$arrShipper['address1'].'</Addr1>
          <City>'.$arrShipper['city'].'</City>
          <State>'.$arrShipper['state'].'</State>
          <PostalCode>'.$arrShipper['zip_code'].'</PostalCode>
          <Country>US</Country>
        </BillAddress>
        <PONumber>'.$arr['entityid'].'</PONumber>
        
        <Memo>Order id #'.$number.'</Memo>
 
        <InvoiceLineAdd>
          <ItemRef>
            <FullName>Transport Services</FullName>
          </ItemRef>
          <Desc>'.$Desc.'</Desc>
          <Quantity>'.$arr['TotalVehicle'].'</Quantity>
          <Rate>'.$arr['total_tariff_stored'].'</Rate>
        </InvoiceLineAdd>
 
        
      </InvoiceAdd>
    
  ';		
  
  $xmlCustomer = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="2.0"?>
		<QBXML>
			<QBXMLMsgsRq onError="stopOnError">
				'.$xmlCustomer.'
				'.$xmlInvoice.'
			</QBXMLMsgsRq>
		</QBXML>';
	
	return $xml;
}


function _quickbooks_customer_add_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
}
*/

