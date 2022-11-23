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
	
	
	   $nodes='<Name>' . $arr['company_name'] . '</Name>
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

/**
 * Receive a response from QuickBooks 
 */
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
	    $nodes='<Name>' . $arr['company_name'] . '</Name>
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
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	
	   $nodes='<Name>' . $arr['company_name'] . '</Name>
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
function _quickbooks_vendor_update_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	$result = mysql_query("SELECT * FROM app_accounts WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	    $nodes='<Name>' . $arr['company_name'] . '</Name>
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
	mysql_query("
		UPDATE 
			app_accounts 
		SET 
			quickbooks_listid = '" . mysql_real_escape_string($idents['ListID']) . "', 
			quickbooks_editsequence = '" . mysql_real_escape_string($idents['EditSequence']) . "'
		WHERE 
			id = " . (int) $ID);
}





/*******************INOICES*************/

function _quickbooks_invoice_add_request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale)
{
	
	$result = mysql_query("SELECT * FROM app_order_header WHERE id = " . (int) $ID);
	// Grab the data from our MySQL database
	$arr = mysql_fetch_assoc($result);
	
	$resultShipper = mysql_query("SELECT company FROM app_shippers WHERE id = " . (int) $arr['shipper_id']);
	// Grab the data from our MySQL database
	$arrShipper = mysql_fetch_assoc($resultShipper);
	
	/*
	$entity = new Entity($daffny->DB);
	$entity->load((int) $ID);
	
	$account = new Account($entity->account_id);
	
    $vehicles = $entity->getVehicles();
	$num_vehicles = count($vehicles);
	 */
$xml = '<?xml version="1.0" encoding="utf-8"?>
<?qbxml version="10.0"?>
<QBXML>
  <QBXMLMsgsRq onError="stopOnError">
    <InvoiceAddRq requestID="' . $requestID . '">
      <InvoiceAdd>
        <CustomerRef>
          <FullName>'.$account->company_name.'</FullName>
        </CustomerRef>
         
        <TxnDate>'.date('Y-m-d').'</TxnDate>
        <RefNumber>'.$entity->id.'</RefNumber>
        <BillAddress>
          <Addr1>'.$account->address1.'</Addr1>
          <City>'.$account->address1.'</City>
          <State>'.$account->address1.'</State>
          <PostalCode>'.$account->address1.'</PostalCode>
          <Country>'.$account->address1.'</Country>
        </BillAddress>
        <PONumber>'.$entity->id.'</PONumber>
        
        <Memo>test order #'.$entity->number.'</Memo>
 
        <InvoiceLineAdd>
          <ItemRef>
            <FullName>Transport Services</FullName>
          </ItemRef>
          <Desc>Invoice for '.$num_vehicles.' vehicles.</Desc>
          <Quantity>'.$num_vehicles.'</Quantity>
          <Rate>'.$entity->total_tariff_stored.'</Rate>
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
