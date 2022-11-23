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


function _quickbooks_delete_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	  
	 //<!-- TxnDelType may have one of the following values: ARRefundCreditCard, Bill, BillPaymentCheck, BillPaymentCreditCard, BuildAssembly, Charge, Check, CreditCardCharge, CreditCardCredit, CreditMemo, Deposit, Estimate, InventoryAdjustment, Invoice, ItemReceipt, JournalEntry, PayrollLiabilityAdjustment [PRIVATE], PayrollPriorPayment [PRIVATE], PayrollYearToDateAdjustment [PRIVATE], PurchaseOrder, ReceivePayment, SalesOrder, SalesReceipt, SalesTaxPaymentCheck, TimeTracking, TransferInventory, VehicleMileage, VendorCredit -->
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="11.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <TxnDelRq>
      <TxnDelType>Invoice</TxnDelType>
      <TxnID>'.$arrEnt['TxnID'].'</TxnID>
    </TxnDelRq>
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
} 



/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_delete_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
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


function _quickbooks_vendor_query_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	  $vendorName = xmlentities(substr($arr['company_name'], 0, 30))." (".$ID.")";
	  
	
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="8.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <VendorQueryRq requestID="' . $requestID . '">
      <FullName>'.$vendorName.'</FullName>
      <OwnerID>0</OwnerID>
    </VendorQueryRq>  
  </QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
} 



/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_query_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
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

function _quickbooks_customer_query_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	  
	$customerName = $arr['company_name'];
	
	if($arr['company_name']=="")
	  $customerName = $arr['first_name']." ".$arr['last_name']." (".$ID.")";
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="8.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <CustomerQueryRq requestID="' . $requestID . '">
      <FullName>'.$customerName.'</FullName>
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
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
  
	
}
 
function _quickbooks_customer_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$customerName = $arr['company_name'];
	
	if($arr['company_name']=="")
	  $customerName = $arr['first_name']." ".$arr['last_name']." (".$ID.")";
	
	//.$arr['first_name']." ".$arr['last_name']." ".$arr['city']." ".$arr['state']." ".$arr['zip_code']
	   $nodes='<Name>' . xmlentities(substr($customerName, 0, 30))." (".$ID.")" . '</Name>
						<CompanyName>' . xmlentities(substr($arr['company_name'], 0, 40)) . '</CompanyName>
						<FirstName>' . xmlentities($arr['first_name']) . '</FirstName>
						<LastName>' . xmlentities($arr['last_name']) . '</LastName>
						<BillAddress>
							<Addr1>' . xmlentities($arr['address1']) . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</BillAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . xmlentities($arr['contact_name1']) . '</Contact>
						<AltContact>' . xmlentities($arr['contact_name2']) . '</AltContact>';
						
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
	
	$customerName = $arr['company_name'];
	
	if($arr['company_name']=="")
	  $customerName = $arr['first_name']." ".$arr['last_name']." (".$ID.")";
	
	
	    $nodes='<Name>' . xmlentities(substr($customerName, 0, 30))." (".$ID.")" . '</Name>
						<CompanyName>' . xmlentities(substr($arr['company_name'], 0, 40)) . '</CompanyName>
						<FirstName>' . xmlentities($arr['first_name']) . '</FirstName>
						<LastName>' . xmlentities($arr['last_name']) . '</LastName>
						<BillAddress>
							<Addr1>' . xmlentities($arr['address1']) . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</BillAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . xmlentities($arr['contact_name1']) . '</Contact>
						<AltContact>' . xmlentities($arr['contact_name2']) . '</AltContact>
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
	
	  $vendorName = xmlentities(substr($arr['company_name'], 0, 30))." (".$ID.")";
	  
	  
	  
	  $nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . xmlentities(substr($arr['company_name'], 0, 40)) . '</CompanyName>
						<VendorAddress>
							<Addr1>' . xmlentities($arr['address1']) . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</VendorAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . xmlentities($arr['contact_name1']) . '</Contact>
						<AltContact>' . xmlentities($arr['contact_name2']) . '</AltContact>';
						
				
						
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
	/*
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 
	 $resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	 
	 $result = mysql_query("SELECT * FROM app_accounts WHERE id = " . $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	 $arr = mysql_fetch_assoc($result);
	 */
	 
	 $result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	 $arr = mysql_fetch_assoc($result);
	
	
	/* 
	$vendorName = xmlentities(substr($rowDispatch['carrier_company_name'], 0, 30))." (".$arrEnt['carrier_id'].") ";
	
	
	    $nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . xmlentities(substr($rowDispatch['carrier_company_name'], 0, 40)) . '</CompanyName>
						<VendorAddress>
							<Addr1>' . xmlentities($rowDispatch['carrier_address']) . '</Addr1>
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
		
		$vendorName = xmlentities(substr($arr['company_name'], 0, 30))." (".$ID.")";
		
		$nodes='<Name>' . $vendorName . '</Name>
						<CompanyName>' . xmlentities(substr($arr['company_name'], 0, 40)) . '</CompanyName>
						<VendorAddress>
							<Addr1>' . xmlentities($arr['address1']) . '</Addr1>
							<City>' . $arr['city'] . '</City>
							<State>' . $arr['state'] . '</State>
							<PostalCode>' . $arr['zip_code'] . '</PostalCode>
							<Country>' . $arr['country'] . '</Country>
						</VendorAddress>
						<Phone>' . $arr['phone1'] . '</Phone>
						<AltPhone>' . $arr['phone2'] . '</AltPhone>
						<Email>' . $arr['email'] . '</Email>
						<Contact>' . xmlentities($arr['contact_name1']) . '</Contact>
						<AltContact>' . xmlentities($arr['contact_name2']) . '</AltContact>';
						
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
  /*
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
 */ 
	
	 $resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	 
	 if($arrEnt['carrier_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $arrEnt['carrier_id']);
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
	  
   $amount = $arr['total_tariff'];	
   
   if(checkCodCop($arr['balance_paid_by'])==1) 
   {
	   $amount = $arr['total_deposite'];	
   }

$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="10.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <InvoiceAddRq requestID="' . $requestID . '">
      <InvoiceAdd>
        <CustomerRef>
		   <ListID>' . $arrShipper['quickbooks_listid'] . '</ListID>
		   
        </CustomerRef>
         <ARAccountRef>
		    <FullName>Accounts Receivable</FullName>
		</ARAccountRef>
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
          <Rate>'.$amount.'</Rate>
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
	  
   $amount = $arr['total_tariff_stored'];	
   
   if(checkCodCop($arr['balance_paid_by'])==1) 
   {
	   $amount = $arr['total_deposite'];	
   }
   
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
                    <Rate>'.$amount.'</Rate>
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

   $arrPayment = mysql_query("SELECT entity_id FROM app_payments WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arrPayment = mysql_fetch_assoc($arrPayment);
	
	$resultEnt = mysql_query("SELECT account_id FROM app_entities WHERE id = " . $arrPayment['entity_id']);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $arrEnt['account_id']);
	
}

/**** With entity id ****/

function _quickbooks_vendor_bill_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
  /*
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
*/	
	
	
	$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];

$xml='<?xml version="1.0" encoding="ISO-8859-1"?>
<?qbxml version="7.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
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
    /*
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 */
	 
	 $resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	// Grab the data from our MySQL database
	$rowDispatch = mysql_fetch_assoc($resultEnt);
	 if($rowDispatch['carrier_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['carrier_id']);
	 }
	 
	  mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
}


function _quickbooks_vendor_bill_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
   /*
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	*/
	
	
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
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
    <BillModRq>
      <BillMod>
	    <TxnID>'.$arrEnt['Ven_TxnID'].'</TxnID>
        <EditSequence>'.$arrEnt['Ven_EditSequence'].'</EditSequence>
        <VendorRef>
          <ListID>'.$arr['quickbooks_listid'].'</ListID>
        </VendorRef>
		<RefNumber>'.$number.'</RefNumber>
		   <ItemLineMod> 
			  <ItemRef>
				<FullName>Transport Services</FullName>
			  </ItemRef>
            <Desc>Item added for Order #'.$number.'</Desc> 
			<Quantity>1</Quantity> 
			<Cost>'.$arrEnt['total_carrier_pay'].'</Cost> 
			<Amount>'.$arrEnt['total_carrier_pay'].'</Amount>
        </ItemLineMod>
      </BillMod>
    </BillModRq>
  </QBXMLMsgsRq>
</QBXML>';

$xml = '<?xml version="1.0" ?>
<?qbxml version="10.0" ?>
<QBXML>
<QBXMLMsgsRq onError = "continueOnError" responseData= "includeNone">
<BillModRq requestID = "477-934396490">
<BillMod>
<TxnID>'.$arrEnt['Ven_TxnID'].'</TxnID>
<EditSequence>'.$arrEnt['Ven_EditSequence'].'</EditSequence>
<ItemLineMod>
        
      <Amount>229.38</Amount>
</ItemLineMod>

</BillMod>
</BillModRq>
</QBXMLMsgsRq>
</QBXML>';
*/
$xml='<?xml version="1.0" ?>
<?qbxml version="10.0" ?>
<QBXML>
<QBXMLMsgsRq onError = "stopOnError" >
<BillModRq requestID="'.$requestID.'">
<BillMod>
        <TxnID>'.$arrEnt['Ven_TxnID'].'</TxnID>
        <EditSequence>'.$arrEnt['Ven_EditSequence'].'</EditSequence>
        <VendorRef>
          <ListID>'.$arr['quickbooks_listid'].'</ListID>
        </VendorRef>
		<ItemLineMod>
			<TxnLineID>-1</TxnLineID>
			<ItemRef>
				<FullName>Transport Services</FullName>
			  </ItemRef>
			<Amount>'.$arrEnt['carrier_pay_stored'].'</Amount>
		</ItemLineMod>


</BillMod>
</BillModRq>
</QBXMLMsgsRq>
</QBXML>';
	
	return $xml;
}

function _quickbooks_vendor_bill_update_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	
/*
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	$rowDispatch = mysql_fetch_assoc($resultDispatch);
	*/
	
	$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	// Grab the data from our MySQL database
	$rowDispatch = mysql_fetch_assoc($resultEnt);
	 
	 
	 if($rowDispatch['carrier_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['carrier_id']);
	 }
	 
	  mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
	
}


function _quickbooks_vendor_credit_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
    $IDArr = explode("_",$ID);
	if(sizeof($IDArr)>1){
		$ID  = $IDArr[0];
		$Type  = $IDArr[1];
	}
	else
	  $Type = 0;
	/*   
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	*/
	
	
	//$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];
/*
$xml='
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
	
	//$total_carrier_pay = $arrEnt['total_carrier_pay'];
	
	if($Type ==1){
	  if($arrEnt['delivery_credit'] == 1)
	   $total_carrier_pay = (($arrEnt['carrier_pay_stored'] * 5)/ 100) + 12;   
	  elseif($arrEnt['delivery_credit'] == 2)
	   $total_carrier_pay = (($arrEnt['carrier_pay_stored'] * 3)/ 100) + 12; 
	  else
	    $total_carrier_pay = $arrEnt['carrier_pay_stored'];
		
	   $total_carrier_pay = number_format($total_carrier_pay, 2);
	}
	else
	  $total_carrier_pay = $arrEnt['carrier_pay_stored'];
	  
	
$xml='<?xml version="1.0" encoding="UTF-8"?>
<?qbxml version="8.0"?>
<QBXML>
    <QBXMLMsgsRq onError="stopOnError">
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
					<Cost>'.$total_carrier_pay.'</Cost> 
                    <Amount>'.$total_carrier_pay.'</Amount>
                    
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

     $IDArr = explode("_",$ID);
	if(sizeof($IDArr)>1){
		$ID  = $IDArr[0];
		$Type  = $IDArr[1];
	}
	else
	  $Type = 0;
	/*  
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	 */
	 $resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $ID);
	// Grab the data from our MySQL database
	$rowDispatch = mysql_fetch_assoc($resultEnt);
	
	 if($rowDispatch['carrier_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['carrier_id']);
	 }
	 
	if($Type ==1){
	   mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_Credit_Extra_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_Extra_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_Credit_Extra_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_Extra_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
	}
	else
	{
		 mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_Credit_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_Credit_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);  
		  
	}
	
}



function _quickbooks_vendor_credit_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
     $IDArr = explode("_",$ID);
	if(sizeof($IDArr)>1){
		$ID  = $IDArr[0];
		$Type  = $IDArr[1];
	}
	else
	  $Type = 0;
	 /* 
	 $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	*/
	
	
	//$resultEnt = mysql_query("SELECT * FROM app_order_header WHERE entityid = " . $ID);
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $ID);
	// Grab the data from our MySQL database
	$arrEnt = mysql_fetch_assoc($resultEnt);
	
	$result = mysql_query("SELECT quickbooks_listid FROM app_accounts WHERE id = " . $arrEnt['carrier_id']);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$number = "";
        if (trim($arrEnt['prefix']) != "") {
            $number .= $arrEnt['prefix'] . "-";
        }
        $number .= $arrEnt['number'];

   if($Type ==1){
	  if($arrEnt['delivery_credit'] == 1)
	   $total_carrier_pay = (($arrEnt['carrier_pay_stored'] * 5)/ 100) + 12;   
	  elseif($arrEnt['delivery_credit'] == 2)
	   $total_carrier_pay = (($arrEnt['carrier_pay_stored'] * 3)/ 100) + 12; 
	  else
	    $total_carrier_pay = $arrEnt['carrier_pay_stored'];
		
	   $total_carrier_pay = number_format($total_carrier_pay, 2);
	   
	   $TxnID = $arrEnt['Ven_Credit_Extra_TxnID'];
	   $EditSequence = $arrEnt['Ven_Credit_Extra_EditSequence'];
	}
	else{
	  $total_carrier_pay = $arrEnt['carrier_pay_stored'];
	  $TxnID = $arrEnt['Ven_Credit_TxnID'];
	  $EditSequence = $arrEnt['Ven_Credit_EditSequence'];
	}

$xml='<?xml version="1.0" encoding="UTF-8"?>
<?qbxml version="8.0"?>
<QBXML>
    <QBXMLMsgsRq onError="stopOnError">
        <VendorCreditModRq requestID="'.$requestID.'">
            <VendorCreditMod>
			    <TxnID>'.$TxnID.'</TxnID>
                <EditSequence>'.$EditSequence.'</EditSequence>
                <VendorRef>
                    <ListID>'.$arr['quickbooks_listid'].'</ListID>
                </VendorRef>
                
                <TxnDate>'.date('Y-m-d').'</TxnDate>
                <RefNumber>'.$number.'</RefNumber>
                <Memo>Credit for #order '.$number.'</Memo>
                
                <ItemLineMod>
				    <TxnLineID>-1</TxnLineID>
                    <ItemRef>
                        <FullName>speed pay discount</FullName>
                    </ItemRef>
                    <Desc>Credit for #order '.$number.'</Desc>
					<Cost>'.$total_carrier_pay.'</Cost> 
                    <Amount>'.$total_carrier_pay.'</Amount>
                    
                </ItemLineMod>
               
            </VendorCreditMod>
        </VendorCreditModRq>
    </QBXMLMsgsRq>
</QBXML>';	
	
	return $xml;
}



/**
 * Receive a response from QuickBooks 
 */
function _quickbooks_vendor_credit_update_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	

     $IDArr = explode("_",$ID);
	if(sizeof($IDArr)>1){
		$ID  = $IDArr[0];
		$Type  = $IDArr[1];
	}
	else
	  $Type = 0;
	 /* 
     $query = "select * from app_dispatch_sheets WHERE `entity_id` = ".$ID." AND `deleted` = 0 AND `cancelled` IS NULL AND `rejected` IS NULL";
	 $resultDispatch = mysql_query($query);
	// Grab the data from our MySQL database
	 $rowDispatch = mysql_fetch_assoc($resultDispatch);
	*/
	
	$resultEnt = mysql_query("SELECT * FROM app_entities WHERE id = " . $ID);
	// Grab the data from our MySQL database
	$rowDispatch = mysql_fetch_assoc($resultEnt);
	 if($rowDispatch['carrier_id'] > 0)
	 {
		mysql_query("
			UPDATE 
				app_accounts 
			SET 
				quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
				quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
			WHERE 
				id = " . $rowDispatch['carrier_id']);
	 }
	 
	 if($Type ==1){
		 
	   mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_Credit_Extra_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_Extra_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_Credit_Extra_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_Extra_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
	}
	else
	{
		 mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_Credit_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_Credit_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_Credit_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);  
		  
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



function _quickbooks_billpayment_check_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
		
		
	$result = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	
	$resultShipper = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $arr['carrier_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	
	
		  $sql = "SELECT *
                  FROM app_payments_check pc
				  WHERE pc.entity_id = '" . (int) $ID . "'
                  ORDER BY id desc limit 0,1";
	  $resultCheck = mysql_query($sql);
	// Grab the data from our MySQL database
	 $arrCheck = mysql_fetch_assoc($resultCheck);
	 
	 $number = "";
        if (trim($arr['prefix']) != "") {
            $number .= $arr['prefix'] . "-";
        }
        $number .= $arr['number'];
/*	
  $xml = '<?xml version="1.0" encoding="utf-8" ?>
  <?qbxml version="13.0"?>
     <QBXML>
         <QBXMLMsgsRq onError="continueOnError">
             <BillPaymentCheckAddRq>
                 <BillPaymentCheckAdd>
                     <PayeeEntityRef>
                        <ListID>800064FF-1456467432</ListID>
                      </PayeeEntityRef>
                      <BankAccountRef>
                        <FullName>Rite way Account</FullName>
                      </BankAccountRef>
					 <RefNumber>22363464</RefNumber>
                     <Memo>Test</Memo>
                     <AppliedToTxnAdd>
					    <PaymentAmount>10.00</PaymentAmount>
                      </AppliedToTxnAdd>
                  </BillPaymentCheckAdd>
              </BillPaymentCheckAddRq>
          </QBXMLMsgsRq>
      </QBXML>';
	
	$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="13.0"?>
<QBXML>
    <QBXMLMsgsRq onError = "continueOnError">
        <BillPaymentCheckAddRq requestID = "0">
            <BillPaymentCheckAdd>
                <PayeeEntityRef>
                    <ListID>800064FF-1456467432</ListID>
                </PayeeEntityRef>
                <TxnDate>2017-01-21</TxnDate>
                <BankAccountRef>
                    <FullName>Rite way Account</FullName>
                </BankAccountRef>
				<IsToBePrinted>false</IsToBePrinted>
                <RefNumber>11000</RefNumber>
                <Memo>786-35 Sample</Memo>
                <ExchangeRate>1.000000</ExchangeRate>
                <AppliedToTxnAdd>
                    <TxnID>3E42-1071498278</TxnID>
                    <PaymentAmount>20.00</PaymentAmount>
                </AppliedToTxnAdd>
            </BillPaymentCheckAdd>
        </BillPaymentCheckAddRq>
    </QBXMLMsgsRq>
</QBXML>';
Rite way Account Carrier Pay
*/
$xml='<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="9.0"?>
<QBXML>
    <QBXMLMsgsRq onError="stopOnError">
<BillPaymentCheckAddRq requestID="' . $requestID . '">
      <BillPaymentCheckAdd>
        <PayeeEntityRef>
          <ListID>'.$arrShipper['quickbooks_listid'].'</ListID>
        </PayeeEntityRef>
       
        <TxnDate>'.date('Y-m-d').'</TxnDate>
        <BankAccountRef> 
          <FullName>1200.Chase-Carrier Pay*8440</FullName>
        </BankAccountRef>
        <RefNumber>'.$arrCheck['check_number'].'</RefNumber>
        <Memo>'.$number.'</Memo>
        <AppliedToTxnAdd>
          <TxnID>'.$arr['Ven_TxnID'].'</TxnID>
          <PaymentAmount>'.$arr['carrier_pay_stored'].'</PaymentAmount>
        </AppliedToTxnAdd>
     </BillPaymentCheckAdd>
    </BillPaymentCheckAddRq></QBXMLMsgsRq>
</QBXML>';
	return $xml;
}


function _quickbooks_billpayment_check_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents)
{	
/*
   $result = mysql_query("SELECT * FROM app_entities WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);

	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $arr['carrier_id']);
	
	*/
	
	 mysql_query("
		UPDATE 
			app_entities
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
   
	mysql_query("
		UPDATE 
			app_order_header
		SET 
			Ven_TxnID = '" . mysql_real_escape_string($idents['TxnID']) . "', 
			Ven_EditSequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			entityid = " . (int) $ID);
	
}

function xmlentities($text)
  {
    $search = array('&','<','>','"','\'');
    $replace = array('&amp;','&lt;','&gt;','&quot;','&apos;');
    return str_replace($search,$replace,$text);  
  }
  
function checkCodCop($balance_paid_by){
	
	
		$flag = 0;
		switch ($balance_paid_by) {
						//case Entity::BALANCE_INVOICE_CARRIER:
						case 8:
						case 9:
						case 18:
						case 19:
						case 2:
						case 3:
						case 16:
						case 17:
							$flag = 1;
							break;
						//--
						case 12:
						case 13:
						case 20:
						case 21:
						
							$flag = 2;
							
							break;
						case 14:
						case 15:
						case 22:
						case 23:
						
							$flag = 3;
							
							break;
						default:
							break;
					}
					return $flag;
		}  