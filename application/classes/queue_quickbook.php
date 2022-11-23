<?php
	/***************************************************************************************************
	* Truck class
	* This class is represent one truck
	*
	* Client:			FreightDragon
	* Version:			1.0
	* Date:				2011-11-03
	* Author:			C.A.W., Inc. dba INTECHCENTER
	* Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
	* E-mail:			techsupport@intechcenter.com
	* CopyRight 2011 FreightDragon. - All Rights Reserved
	****************************************************************************************************/
	require_once(ROOT_PATH . "libs/QuickBooks.php");
	class QueueQuickbook extends FdObject {
		
		public function queueCustomerQuery($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_QUERY_CUSTOMER, $id,30);
			
		}
		
		public function queueCustomer($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, $id,29);
			
		}
		
		public function queueUpdateCustomer($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 $Queue->enqueue(QUICKBOOKS_QUERY_CUSTOMER, $id,30);
	         $Queue->enqueue(QUICKBOOKS_MOD_CUSTOMER, $id,28);
			
		}
		
		public function queueDeleteCustomer($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 $Queue->enqueue(QUICKBOOKS_QUERY_CUSTOMER, $id,30);
	         $Queue->enqueue(QUICKBOOKS_DELETE_LIST, $id,28);
			
		}
		
		
		public function queueVendorQuery($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_QUERY_VENDOR, $id,27);
			
		}
		public function queueVendor($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_VENDOR, $id,26);
			
		}
		
		public function queueUpdateVendor($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 // $Queue->enqueue(QUICKBOOKS_QUERY_VENDOR, $id,27);
	         $Queue->enqueue(QUICKBOOKS_MOD_VENDOR, $id,25);
			
		}
				
		
		public function queueInvoice($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_INVOICE, $id,24);
			
		}
		public function queueUpdateInvoice($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 //************* Need to add Invoice Query request here to get latest edit sequence *************************
	         $Queue->enqueue(QUICKBOOKS_QUERY_INVOICE, $id,23);
			 $Queue->enqueue(QUICKBOOKS_MOD_INVOICE, $id,22);
			
		}
		
		public function queueReceivedPayment($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_RECEIVEPAYMENT, $id, 21);
			
		}
		public function queueReceivedPaymentUpdate($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_MOD_RECEIVEPAYMENT, $id, 21);
			
		}   
		public function queueCreditMemo($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_CREDITMEMO, $id, 21);
			
		}
		public function queueVendorBill($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_BILL, $id , 20);
			
		}
		
		public function queueVendorBillUpdate($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 //************* Need to add BIll Query request here to get latest edit sequence 
	         $Queue->enqueue(QUICKBOOKS_IMPORT_BILL, $id, 19);
	         $Queue->enqueue(QUICKBOOKS_MOD_BILL, $id, 18);
			
		}
		
		public function queueVendorCredit($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_VENDORCREDIT, $id, 17);
			
		}
		public function queueVendorCreditUpdate($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
			 //************* Need to add VendorCredit Query request here to get latest edit sequence 
	         $Queue->enqueue(QUICKBOOKS_QUERY_VENDORCREDIT, $id, 16);
	         $Queue->enqueue(QUICKBOOKS_MOD_VENDORCREDIT, $id,15);
			
		}
		public function queueVendorBillCheck($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_ADD_BILLPAYMENTCHECK, $id,14);
			
		}
		
		/*public function queueDeleteTransaction($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_DEL_TRANSACTION, $id,13);
			
		}*/
		
		public function queueDeleteInvoice($dsn = '', $id = '') {

			// Queue up the customer add 
			$Queue = new QuickBooks_WebConnector_Queue($dsn);
			$Queue->enqueue(QUICKBOOKS_DEL_TRANSACTION, $id, 13);
		}
		
		public function queueDeleteTransaction($dsn = '', $id = '') {

        		// Queue up the customer add 
        		$Queue = new QuickBooks_WebConnector_Queue($dsn);
        
        		$Queue->enqueue(QUICKBOOKS_DEL_TRANSACTION, $id, 13);
    		}
		
		public function queueDeletePayment($dsn = '', $id = '')
		{
			// Queue up the customer add 
	         $Queue = new QuickBooks_WebConnector_Queue($dsn);
	         $Queue->enqueue(QUICKBOOKS_DEL_TXN_PAY, $id,13);
			
		}

	}
?>