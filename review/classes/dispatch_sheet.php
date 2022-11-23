<?php
/**************************************************************************************************
 * DispatchSheet class
 * Class representing one Dispatch Sheet record in DB
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:		2012-01-26
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:	    11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved
 ***************************************************************************************************/

class DispatchSheet extends FdObject {
	const TABLE = "app_dispatch_sheets";

	protected $member_objects = array();

	/**
	 * getOrder()
	 *
	 * @param bool $reload
	 * @return Entity $order
	 * @throws FDException
	 */
	public function getOrder($reload = false) {
		if ($reload || !isset($this->member_objects['order'])) {
			$order = new Entity($this->db);
			$order->load($this->entity_id);
			$this->member_objects['order'] = $order;
		}
		return $this->member_objects['order'];
	}

	public function getCarrier($reload = false) {
		if ($reload || !isset($this->member_objects['carrier'])) {
			$carrier = new Account($this->db);
			$carrier->load($this->account_id);
			$this->member_objects['carrier'] = $carrier;
		}
		return $this->member_objects['carrier'];
	}

	public function getCreated($format = "m/d/Y") {
		return date($format, strtotime($this->created));
	}

	public function getPickupDate($format = "m/d/Y") {
		return date($format, strtotime($this->entity_load_date));
	}

	public function getDeliveryDate($format = "m/d/Y") {
		return date($format, strtotime($this->entity_delivery_date));
	}
	
	public function getAccepted($format = "m/d/Y") {
		return date($format, strtotime($this->accepted));
	}

	public function getPrice($decimal = 2) {
		return number_format($this->entity_price, $decimal);
	}

	public function getCarrierPay($decimal = 2) {
		return number_format($this->entity_carrier_pay, $decimal);
	}

	public function getOnDeliveryToCarrier($decimal = 2) {
		return number_format($this->entity_odtc, $decimal);
	}

	public function getCompanyOwesCarrier($decimal = 2) {
		return number_format($this->entity_coc, $decimal);
	}

	public function accept() {
		$entity = new Entity($this->db);
		$entity->load($this->entity_id);
		$entity->setStatus(Entity::STATUS_DISPATCHED);
		$this->update(array(
			"accepted" => date("Y-m-d H:i:s"),
			"modified_by" => $_SESSION['member_id'],
			"modified_ip" => $_SERVER['REMOTE_ADDR']
		));

       //$entity->updateHeaderTable();

        $entity = new Entity($this->db);
        $entity->load($this->entity_id);
        //save PDF under Documents
        $fname = md5(mt_rand()." ".time()." ".$this->entity_id);
        $path = ROOT_PATH . "uploads/entity/" . $fname;
        $entity->getDispatchSheet()->getPdf("F", $path);

        $ins_arr = array(
            'name_original' => "Dispatch sheet ".date("Y-m-d H-i-s").".pdf",
            'name_on_server' => $fname,
            'size' => filesize($path),
            'type' => "pdf",
            'date_uploaded' => date("Y-m-d H:i:s"),
            'owner_id' => $entity->getAssigned()->parent_id,
            'status' => 0,
        );

        $this->db->insert("app_uploads", $ins_arr);
        $ins_id = $this->db->get_insert_id();

        $this->db->insert("app_entity_uploads", array("entity_id"=>$entity->id, "upload_id"=>$ins_id));

		$mail = new FdMailer(true);
		$mail->isHTML();
		$mail->Body = 'Thank you for accepting the dispatch Sheet';
		$mail->Subject = 'Copy of Signed Dispatch Sheet';
		$mail->AddAddress($this->carrier_email, $this->carrier_contact_name);
		$mail->AddCC($entity->getAssigned()->email, $entity->getAssigned()->contactname);
		//$mail->setFrom('noreply@freightdragon.com');
		if($entity->getAssigned()->parent_id == 1)
		  //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
		  $mail->setFrom('noreply@freightdragon.com');
		else
		  $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
			  
		$mail->AddAttachment($path, 'DispatchSheet.pdf');
		$mail->send();
		
		return $ins_id;
		//return $this;
	}

	public function reject() {
		$entity = new Entity($this->db);
		$entity->load($this->entity_id);
		$entity->update(array("dispatched" => "NULL", 'carrier_id' => 'NULL'));
		$entity->setStatus(Entity::STATUS_ACTIVE);
		$this->update(array(
			"rejected" => date("Y-m-d H:i:s"),
			"modified_by" => $_SESSION['member_id'],
			"modified_ip" => $_SERVER['REMOTE_ADDR']
		));
		$entity->updateHeaderTable();
		return $this;
	}

	public function cancel() {
		$entity = new Entity($this->db);
		$entity->load($this->entity_id);
		//if ($entity->status == Entity::STATUS_PICKEDUP || $entity->status == Entity::STATUS_DELIVERED) throw new FDException("Can't cancel picked up Dispatch");
        if ($entity->status == Entity::STATUS_DELIVERED) throw new FDException("Can't cancel picked up Dispatch");
		$entity->update(array("dispatched" => "NULL", 'carrier_id' => 'NULL', 'not_signed' => 'NULL'));
		$entity->setStatus(Entity::STATUS_ACTIVE);
		$this->update(array(
			"cancelled" => date("Y-m-d H:i:s"),
			"modified_by" => $_SESSION['member_id'],
			"modified_ip" => $_SERVER['REMOTE_ADDR']
		));
		
		$entity->updateHeaderTable();
	}

	public function getVehicles() {
		$vehicleManager = new VehicleManager($this->db);
		//return $vehicleManager->getDispatchVehicles($this->id);
		return $vehicleManager->getVehicles($this->entity_id);
	}

	public function getFromLink() {
		return "http://maps.google.com/maps?q=".urlencode($this->from_city.",+".$this->from_state);
	}

	public function getToLink() {
		return "http://maps.google.com/maps?q=".urlencode($this->to_city.",+".$this->to_state);
	}

	/**
	 * @param template $tpl
	 * @param bool $showSignature
	 * @throws FDException
	 * @return string Dispatch Sheet HTML
	 */
	public function getHtml($tpl,$type='html', $showSignature = true) {
		if (!($tpl instanceof template)) throw new FDException("Invalid template object");
		
		$entity = new Entity($this->db);
        $entity->load($this->entity_id);
		
		$origin = $entity->getOrigin();
        $destination = $entity->getDestination();
		
		 $entity_odtc = $this->getOnDeliveryToCarrier();
		 $payments_terms_dispatch = $this->payments_terms;
		 if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17 , 8,9,18,19))){   
			//$payments_terms_dispatch = "COD / COP";
			$entity_odtc = $this->getCarrierPay();
		 }	
		 
		$values = array (
			'order_number'			=> $this->order_number,
			'c_companyname'			=> $this->c_companyname,
			'c_address1'			=> $this->c_address1,
			'c_address2'			=> $this->c_address2,
			'c_city'				=> $this->c_city,
			'c_state'				=> $this->c_state,
			'c_zip_code'			=> $this->c_zip_code,
			'c_phone'				=> $this->c_phone,
			'c_dispatch_contact'	=> $this->c_dispatch_contact,
			'c_dispatch_phone'		=> $this->c_dispatch_phone,
			'c_dispatch_fax'		=> $this->c_dispatch_fax,
			'c_dispatch_accounting_fax'		=> $this->c_dispatch_accounting_fax,
			'c_icc_mc_number'		=> $this->c_icc_mc_number,
			'carrier_company_name'	=> $this->carrier_company_name,
			'carrier_address'		=> $this->carrier_address,
            'carrier_print_name'	=> $this->carrier_print_name,
            'carrier_insurance_iccmcnumber' => $this->carrier_insurance_iccmcnumber,
			'carrier_city'			=> $this->carrier_city,
			'carrier_state'			=> $this->carrier_state,
			'carrier_zip'			=> $this->carrier_zip,
			'carrier_email'			=> $this->carrier_email,
			'carrier_contact_name'	=> $this->carrier_contact_name,
			'carrier_phone_1'		=> formatPhone($this->carrier_phone_1),
			'carrier_phone_2'		=> formatPhone($this->carrier_phone_2),
            'carrier_phone_1_ext'		=> ($this->carrier_phone1_ext=='')?'':' X '.$this->carrier_phone1_ext,
			'carrier_phone_2_ext'		=> ($this->carrier_phone1_ext=='')?'':' X '.$this->carrier_phone2_ext,
			'carrier_fax'			=> $this->carrier_fax,
			'carrier_phone_cell'	=> formatPhone($this->carrier_phone_cell),
			'created'				=> $this->getCreated("m/d/Y"),
			'load_date'				=> $this->getPickupDate("m/d/Y"),
			'load_date_type'		=> Entity::$date_type_string[$this->entity_load_date_type],
			'delivery_date'			=> $this->getDeliveryDate("m/d/Y"),
			'delivery_date_type'	=> Entity::$date_type_string[$this->entity_delivery_date_type],
			'ship_via'				=> Entity::$ship_via_string[$this->entity_ship_via],
//			'vehicles_run'			=> Entity::$vehicles_run_string[$this->entity_vehicles_run],
			'entity_price'			=> $this->getPrice(),
			'entity_carrier_pay'	=> ($this->entity_coc > 0) ? $this->getCompanyOwesCarrier() : $this->getCarrierPay(),
			'entity_carrier_pay_c'	=> $this->entity_carrier_pay_c,
			'entity_odtc'			=> $entity_odtc,
			'entity_coc'			=> $this->getCompanyOwesCarrier(),
			'entity_coc_c'			=> $this->entity_coc_c,
            'entity_booking_number'	=> $this->entity_booking_number,
            'entity_buyer_number'	=> $this->entity_buyer_number,
            'entity_pickup_terminal_fee'	=> $this->entity_pickup_terminal_fee,
            'entity_dropoff_terminal_fee'	=> $this->entity_dropoff_terminal_fee,
			//'company_or_carrier'	=> ($this->entity_coc > 0) ? 'Company owes Carrier' : 'Carrier owes Company',
			'company_or_carrier'	=> (in_array($this->entity_balance_paid_by, array(12, 13 , 20 , 21))) ? 'Company owes Carrier' : 'Carrier owes Company',
			'paid_by_company'	=> (in_array($this->entity_balance_paid_by, array(12, 13 , 20 , 21))) ? '(Paid by '.$this->c_companyname.')' : '',
			//'origin_auction_name'	=> $this->origin_auction_name,
			
			/*
			'from_name'				=> $this->from_name,
			'from_company'			=> $this->from_company,						
			'from_booking_number'	=> $this->from_booking_number,
			'from_buyer_number'	=> $this->from_buyer_number,		
			'from_address'			=> $this->from_address,
			'from_address2'			=> $this->from_address2,
			'from_city'				=> $this->from_city,
			'from_state'			=> $this->from_state,
			'from_zip'				=> $this->from_zip,
			'from_country'			=> $this->from_country,
			'from_phone_1'			=> $this->from_phone_1,
			'from_phone_2'			=> $this->from_phone_2,
			'from_phone_cell'		=> $this->from_phone_cell,
			'to_name'				=> $this->to_name,
			'to_company'			=> $this->to_company,
			'to_auction_name'	=> $this->to_auction_name,
			'to_booking_number'	=> $this->to_booking_number,
			'to_buyer_number'	=> $this->to_buyer_number,
			'to_address'			=> $this->to_address,
			'to_address2'			=> $this->to_address2,
			'to_city'				=> $this->to_city,
			'to_state'				=> $this->to_state,
			'to_zip'				=> $this->to_zip,
			'to_country'			=> $this->to_country,
			'to_phone_1'			=> $this->to_phone_1,
			'to_phone_2'			=> $this->to_phone_2,
			'to_phone_cell'			=> $this->to_phone_cell,
			*/
			'instructions'			=> $this->instructions,
			'information'			=> $entity->information,
			'dispatch_terms'		=> $this->dispatch_terms,
			'payments_terms'		=> $payments_terms_dispatch
			
		);
		
		//print $this->entity_ship_via."--".Entity::$ship_via_string[$this->entity_ship_via]."--".$this->id;
		 
			
			if(in_array($entity->status, array(2,3,6, 7 , 8 , 9))){
			
				$values['from_name'] 			= $this->from_name;
				$values['from_name2'] 			= $this->from_name2;
				$values['from_company'] 		= $this->from_company;
				$values['from_address'] 		= $this->from_address;
				$values['from_address2'] 		= $this->from_address2;
				$values['from_city'] 			= $this->from_city;
				$values['from_state'] 			= $this->from_state;
				$values['from_zip'] 			= $this->from_zip;
				$values['from_country'] 		= $this->from_country;
				$values['from_phone_1'] 		= formatPhone($this->from_phone_1);
				$values['from_phone_2'] 		= formatPhone($this->from_phone_2);
				$values['from_phone_3'] 		= formatPhone($this->from_phone_3);
                $values['from_phone_4'] 		= formatPhone($this->from_phone_4);
                $values['from_phone_1_ext'] 		= ($this->from_phone1_ext=='')?'':' X '.$this->from_phone1_ext;
				$values['from_phone_2_ext'] 		= ($this->from_phone2_ext=='')?'':' X '.$this->from_phone2_ext;
				$values['from_phone_3_ext'] 		= ($this->from_phone3_ext=='')?'':' X '.$this->from_phone3_ext;
                $values['from_phone_4_ext'] 		= ($this->from_phone4_ext=='')?'':' X '.$this->from_phone4_ext;			
				$values['from_phone_cell'] 		= formatPhone($this->from_phone_cell);
				$values['from_booking_number'] 	= $this->from_booking_number;
				$values['from_buyer_number'] 	= $this->from_buyer_number;
				$values['from_hours'] 	        = $origin->hours;
				$values['avail_pickup_date'] 	= $this->avail_pickup_date;
				
				$values['to_name'] 				= $this->to_name;
				$values['to_name2'] 			= $this->to_name2;
				$values['to_company'] 			= $this->to_company;
				$values['to_address'] 			= $this->to_address;
				$values['to_address2'] 			= $this->to_address2;
				$values['to_city'] 				= $this->to_city;
				$values['to_state'] 			= $this->to_state;
				$values['to_zip'] 				= $this->to_zip;
				$values['to_country'] 			= $this->to_country;
				$values['to_phone_1'] 			= formatPhone($this->to_phone_1);
				$values['to_phone_2'] 			= formatPhone($this->to_phone_2);
				$values['to_phone_3'] 			= formatPhone($this->to_phone_3);
				$values['to_phone_4'] 			= formatPhone($this->to_phone_4);
				$values['to_phone_1_ext'] 			= ($this->to_phone1_ext=='')?'':' X '.$this->to_phone1_ext;
				$values['to_phone_2_ext'] 			= ($this->to_phone2_ext=='')?'':' X '.$this->to_phone2_ext;
				$values['to_phone_3_ext'] 			= ($this->to_phone3_ext=='')?'':' X '.$this->to_phone3_ext;
                $values['to_phone_4_ext'] 			= ($this->to_phone4_ext=='')?'':' X '.$this->to_phone4_ext;
				$values['to_phone_cell'] 		= formatPhone($this->to_phone_cell);
				//$values['to_auction_name'] 		= $this->to_auction_name;
				$values['to_booking_number'] 	= $this->to_booking_number;
				$values['to_buyer_number'] 	    = $this->to_buyer_number;
				$values['to_hours'] 	        =  $destination->hours;
				
			
		}
		else{
		
		
			$values['from_name'] 			= "";
			$values['from_name2'] 			= "";
			$values['from_company'] 		= "";
			$values['from_address'] 		= "";
			$values['from_address2'] 		= "";
			$values['from_city'] 			= "";
			$values['from_state'] 			= "";
			$values['from_zip'] 			= "";
			$values['from_country'] 		= "";
			$values['from_phone_1'] 		= "";
			$values['from_phone_2'] 		= "";
			$values['from_phone_3'] 		= "";
            $values['from_phone_4'] 		= "";
			$values['from_phone_1_ext'] 		= "";
			$values['from_phone_2_ext'] 		= "";
			$values['from_phone_3_ext'] 		= "";
            $values['from_phone_4_ext'] 		= "";
			$values['from_phone_cell'] 		= "";
			$values['from_booking_number'] 	= "";
			$values['from_buyer_number'] 	= "";
			$values['from_hours'] 	        = "";
			
			$values['to_name'] 				= "";
			$values['to_name2'] 			= "";
			$values['to_company'] 			= "";
			$values['to_address'] 			= "";
			$values['to_address2'] 			= "";
			$values['to_city'] 				= "";
			$values['to_state'] 			= "";
			$values['to_zip'] 				= "";
			$values['to_country'] 			= "";
			$values['to_phone_1'] 			= "";
			$values['to_phone_2'] 			= "";
			$values['to_phone_3'] 			= "";
            $values['to_phone_4'] 			= "";
			$values['to_phone_1_ext'] 			= "";
			$values['to_phone_2_ext'] 			= "";
			$values['to_phone_3_ext'] 			= "";
			$values['to_phone_4_ext'] 			= "";
			$values['to_phone_cell'] 		= "";
			//$values['to_auction_name'] 		= "";
			$values['to_booking_number'] 	= "";
			$values['to_buyer_number'] 	    = "";
			$values['to_hours'] 	        =  "";

		}
		 
		
		if (trim($this->signature) == '' || !$showSignature) {
			$tpl->signature = '';
		} else {
			$tpl->signature = '<img src="'.BASE_PATH.'application/dispatches/signature/id/'.$this->id.'/sign.jpg"  width="300" height="75"/>';
		}
		foreach ($values as $k => $v) {
			if (!in_array($k, array('dispatch_terms'))) {
				$values[$k] = htmlspecialchars($v);
			}
		}
		
		
		$values['vehicles'] = $this->getVehiclesHtml();
		//return $tpl->build("dispatches.sheet", $values);
		//return $tpl->build("dispatches.sheet_new", $values);
		
		if($type == 'pdf'){
			//$values['vehicles'] = $this->getVehiclesHtmlNew();//$this->getVehiclesHtml();
		  return $tpl->build("dispatches.sheet", $values);
		}
		else{
			//$values['vehicles'] = $this->getVehiclesHtml();
		  return $tpl->build("dispatches.sheet_new", $values);
		}
	}
	
	
		/**
	 * @param template $tpl
	 * @param bool $showSignature
	 * @throws FDException
	 * @return string Dispatch Sheet HTML
	 */
	public function getHtmlNew($tpl,$type='html', $showSignature = true) {
		if (!($tpl instanceof template)) throw new FDException("Invalid template object");
		
		$entity = new Entity($this->db);
        $entity->load($this->entity_id);
		
		$origin = $entity->getOrigin();
        $destination = $entity->getDestination();
		
		$paid_by = Entity::$balance_paid_by_string[$entity->balance_paid_by];
		
		$TotalTariff = '';
		$CarrierCheck = "";
		$BalancePaidBy = "";
	    $AmountSectionText = "Freight charges are to be paid by ".$this->c_companyname." unless marked CARRIER COLLECT";
		
		 $entity_odtc = $this->getOnDeliveryToCarrier();
		 $payments_terms_dispatch = $this->payments_terms;
		 
		$entity_carrier_pay = ($this->entity_coc > 0) ? $this->getCompanyOwesCarrier() : $this->getCarrierPay();
		$customer_balance_paid_by =  $entity->getPaymentOption($entity->customer_balance_paid_by);
		 
		 $text_paid_by = "";
		 $text_paid_by_next = "";
		 if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17))){   
			//$payments_terms_dispatch = "COD / COP";
			$entity_odtc = $this->getCarrierPay();
			$BalancePaidBy = "*COD";
			$CarrierCheck = "[X]";
		    $shipper_check  = "[X]";
		    $consignee_check = "[ ]";
			
			if($entity->balance_paid_by == 2)
			  $funds_check = "[X]";
			else
			  $funds_check = "[ ]";
			  
			if($entity->balance_paid_by == 3) 
		      $check_check = "[X]";
			else
			  $check_check = "[ ]"; 
			  
			  $text_paid_by = "*COD";
			  //$text_paid_by_next = "COLLECT ".$entity_odtc." by ".$customer_balance_paid_by." upon delivery";
			  $text_paid_by_next = "COLLECT ".$entity_odtc." by ".$paid_by." upon delivery";
			  
		 }
		 elseif(in_array($entity->balance_paid_by, array(8,9,18,19))){   
			//$payments_terms_dispatch = "COD / COP";
			$entity_odtc = $this->getCarrierPay();
			$BalancePaidBy = "*COP";
			$CarrierCheck = "[X]";
		    $shipper_check  = "[X]";
		    $consignee_check = "[ ]";
			
			if($entity->balance_paid_by == 8)
			  $funds_check = "[X]";
			else
			  $funds_check = "[ ]";
			  
			if($entity->balance_paid_by == 9) 
		      $check_check = "[X]";
			else
			  $check_check = "[ ]"; 
			
			$text_paid_by = "*COP";
			$text_paid_by_next = "COLLECT ".$entity_odtc." by ".$paid_by." upon pickup";
		 }
		 elseif(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21,24))){ 
		   $CarrierCheck = "[ ]";
		   $shipper_check  = "[X]";
		   $consignee_check = "[ ]";
		   $BalancePaidBy = "*COC";
		   $TotalTariff = $entity->getTotalDeposit();
		   $entity_carrier_pay = $entity->getCarrierPay();//$this->getCarrierPay();
		   
		   
			if($entity->balance_paid_by == 12)
			  $funds_check = "[X]";
			else
			  $funds_check = "[ ]";
			  
			if($entity->balance_paid_by == 13) 
		      $check_check = "[X]";
			else
			  $check_check = "[ ]"; 
			 
			 if($entity->balance_paid_by == 12)
			  $paid_by = "Cash/Certified Funds";
			 elseif($entity->balance_paid_by == 13)
			  $paid_by = "Check";
			 elseif($entity->balance_paid_by == 20) 
		      $paid_by = "Comcheck";
			 elseif($entity->balance_paid_by == 21) 
			  $paid_by = "QuickPay";
			 elseif($entity->balance_paid_by == 24) 
			  $paid_by = "Wire-transfer";
			 
			 $text_paid_by = "Broker is responsible for paying ".$entity_carrier_pay." in the form of ".$paid_by." to Carrier.";
			//$text_paid_by = $this->c_companyname." is responsible for paying ".$entity_carrier_pay." in the form of ".$paid_by." to ".$this->carrier_print_name;
			///$text_paid_by = $this->c_companyname." is responsible for paying ".$entity_carrier_pay." in the form of wire-transfer to ".$this->carrier_print_name;
			$text_paid_by_next = "if you have any question regarding your payment, please call ".$this->c_companyname." at ".$this->c_dispatch_phone;
		 }
		 elseif(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23))){ 
		   $TotalDeposit = $entity->getTotalDeposit();
		   $CarrierCheck = "[ ]";
		   $shipper_check  = "[ ]";
		   $consignee_check = "[X]";
		   $BalancePaidBy = "*CAC";
			$AmountSectionText = $this->carrier_print_name." is required to send ".$this->c_companyname." the amount below after collecting freight charges from customer.";
			
			if($entity->balance_paid_by == 14)
			  $funds_check = "[X]";
			else
			  $funds_check = "[ ]";
			if($entity->balance_paid_by == 15) 
		      $check_check = "[X]";
			else
			  $check_check = "[ ]";
			  
			  $paid_by = '';
			  
			 if($entity->balance_paid_by == 14)
			  $paid_by = "Cash/Certified Funds";
			 elseif($entity->balance_paid_by == 15)
			  $paid_by = "Check";
			 elseif($entity->balance_paid_by == 22) 
		      $paid_by = "Comcheck";
			 elseif($entity->balance_paid_by == 23) 
			  $paid_by = "QuickPay";
			  
			 //$text_paid_by = $this->carrier_print_name." is responsible for collecting ".$entity->getTotalTariff()." in the form of ".$paid_by." from the customer. ".$this->c_companyname." will issue ".$this->carrier_print_name." an invoice for the amount of ".$TotalDeposit." and due upon receipt of invoice. "; 	   
			//$text_paid_by = $this->c_companyname." is responsible for paying @carrier_pay@ in the form of ".$customer_balance_paid_by." to ".$this->carrier_print_name;
			//$text_paid_by_next = "if you have any question regarding your payment, please call ".$this->c_companyname." at ".$this->c_dispatch_phone;
			
			$CarrierPay = $entity->getCarrierPay(false);
			$paymentManager = new PaymentManager($this->db);
			$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
			 
			  //$Ammount = $CarrierPay - $shipperPaid;
			  $Ammount = $entity->getTotalTariff(false) - $shipperPaid;
			  $text_paid_by = "Carrier is responsible for collecting $ ".$Ammount." and issuing Broker a ".$paid_by." in the amount of $ ".($entity->getTotalDeposit(false)-$shipperPaid)." for this shipment.";
			  /*$text_paid_by = $this->carrier_print_name." is responsible for collecting ".$entity->getTotalTariff()." and issuing
".$this->c_companyname." a ".$paid_by." in the amount of $ ".$Ammount." for this shipment.";*/
           //$text_paid_by = $this->carrier_print_name." is responsible for collecting $ ".$Ammount."."; 

			$text_paid_by_next = "";
		 }
		 else{
		    $TotalTariff = $entity->getTotalDeposit();
			$CarrierCheck = "[ ]";
			$shipper_check  = "[ ]";
		    $consignee_check = "[ ]";
			$funds_check = "[ ]";
			$check_check = "[ ]";
			
		      
		 }
			
		 
		 
		 
		  $member = $entity->getAssigned();
		  $filePath = UPLOADS_PATH."company".DIRECTORY_SEPARATOR.$member->getOwnerId()."_small.jpg";
		  $companyLogo = "";

                        if (file_exists($filePath)){

                            $encoded_data = base64_encode(file_get_contents($filePath));

                            $companyLogo = "data:image/jpg;base64,".$encoded_data;

                        }else{

                            $companyLogo = "data:image/jpg;base64,iVBORw0KGgoAAAANSUhEUgAAAIsAAAAyCAYAAABs3ChCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA01pVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoMTMuMCAyMDEyMDMwNS5tLjQxNSAyMDEyLzAzLzA1OjIxOjAwOjAwKSAgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ3NDM1REYzQUIwODExRTE4MTZBRjFGREJDMDU4MEYwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ3NDM1REY0QUIwODExRTE4MTZBRjFGREJDMDU4MEYwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RDc0MzVERjFBQjA4MTFFMTgxNkFGMUZEQkMwNTgwRjAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RDc0MzVERjJBQjA4MTFFMTgxNkFGMUZEQkMwNTgwRjAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1l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";

                        }
		 
	
	
		$values = array (
			'order_number'			=> $this->order_number,
			'c_companyname'			=> $this->c_companyname,
			'c_address1'			=> $this->c_address1,
			'c_address2'			=> $this->c_address2,
			'c_city'				=> $this->c_city,
			'c_state'				=> $this->c_state,
			'c_zip_code'			=> $this->c_zip_code,
			'c_phone'				=> $this->c_phone,
			'c_dispatch_contact'	=> $this->c_dispatch_contact,
			'c_dispatch_phone'		=> $this->c_dispatch_phone,
			'c_dispatch_fax'		=> $this->c_dispatch_fax,
			'c_dispatch_accounting_fax'		=> $this->c_dispatch_accounting_fax,
			'c_icc_mc_number'		=> $this->c_icc_mc_number,
			'c_logo'		        => $companyLogo,
			'c_logo_path'		        => $filePath,
			'carrier_company_name'	=> $this->carrier_company_name,
			'carrier_address'		=> $this->carrier_address,
            'carrier_print_name'	=> $this->carrier_print_name,
            'carrier_insurance_iccmcnumber' => $this->carrier_insurance_iccmcnumber,
			'carrier_city'			=> $this->carrier_city,
			'carrier_state'			=> $this->carrier_state,
			'carrier_zip'			=> $this->carrier_zip,
			'carrier_email'			=> $this->carrier_email,
			'carrier_contact_name'	=> $this->carrier_contact_name,
			'carrier_phone_1'		=> formatPhone($this->carrier_phone_1),
			'carrier_phone_2'		=> formatPhone($this->carrier_phone_2),
			'carrier_phone_1_ext'	=> ($this->carrier_phone1_ext=='')?'':' X '.$this->carrier_phone1_ext,
            'carrier_phone_2_ext'	=> ($this->carrier_phone2_ext=='')?'':' X '.$this->carrier_phone2_ext,
			'carrier_fax'			=> $this->carrier_fax,
			'carrier_phone_cell'	=> formatPhone($this->carrier_phone_cell),
			'created'				=> $this->getCreated("m/d/Y"),
			'load_date'				=> $this->getPickupDate("m/d/Y"),
			'load_date_type'		=> Entity::$date_type_string[$this->entity_load_date_type],
			'delivery_date'			=> $this->getDeliveryDate("m/d/Y"),
			'delivery_date_type'	=> Entity::$date_type_string[$this->entity_delivery_date_type],
			'ship_via'				=> Entity::$ship_via_string[$this->entity_ship_via],
//			'vehicles_run'			=> Entity::$vehicles_run_string[$this->entity_vehicles_run],
			'entity_price'			=> $this->getPrice(),
			'entity_carrier_pay'	=> $entity_carrier_pay,
			'entity_carrier_pay_c'	=> $this->entity_carrier_pay_c,
			'entity_odtc'			=> $entity_odtc,
			'entity_coc'			=> $this->getCompanyOwesCarrier(),
			'entity_coc_c'			=> $this->entity_coc_c,
            'entity_booking_number'	=> $this->entity_booking_number,
            'entity_buyer_number'	=> $this->entity_buyer_number,
            'entity_pickup_terminal_fee'	=> $this->entity_pickup_terminal_fee,
            'entity_dropoff_terminal_fee'	=> $this->entity_dropoff_terminal_fee,
			//'company_or_carrier'	=> ($this->entity_coc > 0) ? 'Company owes Carrier' : 'Carrier owes Company',
			'company_or_carrier'	=> (in_array($this->entity_balance_paid_by, array(12, 13 , 20 , 21))) ? 'Company owes Carrier' : 'Carrier owes Company',
			'paid_by_company'	=> (in_array($this->entity_balance_paid_by, array(12, 13 , 20 , 21))) ? '(Paid by '.$this->c_companyname.')' : '',
			//'origin_auction_name'	=> $this->origin_auction_name,
			
			/*
			'from_name'				=> $this->from_name,
			'from_company'			=> $this->from_company,						
			'from_booking_number'	=> $this->from_booking_number,
			'from_buyer_number'	=> $this->from_buyer_number,		
			'from_address'			=> $this->from_address,
			'from_address2'			=> $this->from_address2,
			'from_city'				=> $this->from_city,
			'from_state'			=> $this->from_state,
			'from_zip'				=> $this->from_zip,
			'from_country'			=> $this->from_country,
			'from_phone_1'			=> $this->from_phone_1,
			'from_phone_2'			=> $this->from_phone_2,
			'from_phone_cell'		=> $this->from_phone_cell,
			'to_name'				=> $this->to_name,
			'to_company'			=> $this->to_company,
			'to_auction_name'	=> $this->to_auction_name,
			'to_booking_number'	=> $this->to_booking_number,
			'to_buyer_number'	=> $this->to_buyer_number,
			'to_address'			=> $this->to_address,
			'to_address2'			=> $this->to_address2,
			'to_city'				=> $this->to_city,
			'to_state'				=> $this->to_state,
			'to_zip'				=> $this->to_zip,
			'to_country'			=> $this->to_country,
			'to_phone_1'			=> $this->to_phone_1,
			'to_phone_2'			=> $this->to_phone_2,
			'to_phone_cell'			=> $this->to_phone_cell,
			*/
			//'to_auction_name'	=> $this->to_auction_name,
			'instructions'			=>  $this->instructions,//substr($this->instructions, 0, 100),
			'information'			=> $entity->information,
			'pickup_date'			=> $entity->getFirstAvail("m/d/Y"),
			'delivery_date'			=> $entity->getDeliveryDate("m/d/Y"),
			'entity_total_price'	=> $TotalTariff,
			'dispatch_terms'		=> $this->dispatch_terms,
			'carrier_check'		    => $CarrierCheck,
			'funds_check'		    => $funds_check,
			'check_check'		    => $check_check,
			'shipper_check'		    => $shipper_check,
			'consignee_check'		=> $consignee_check,
			'payments_terms'		=> $this->payments_terms,//substr($this->payments_terms, 0, 100),
			'origin_hours'		    => $origin->hours,
			'destination_hours'		=> $destination->hours,
			'assigned_name'		    => $member->contactname,
			'accepted'		        => $this->getAccepted("m/d/Y"),
			'modified_ip'		    => $this->modified_ip,
			'amount_section_text'   => $AmountSectionText,
			'text_paid_by'          => $text_paid_by,
			'text_paid_by_next'     => $text_paid_by_next,
			'carrier_driver_name'  => $this->carrier_driver_name,
			'carrier_driver_phone'  => formatPhone($this->carrier_driver_phone),
			'balance_paid_by'       => $BalancePaidBy
			
			
			
		);
		
		 
			
			//if(in_array($entity->status, array(2,3,6, 7 , 8 , 9))){
			
				$values['from_name'] 			= $this->from_name;
				$values['from_name2'] 			= $this->from_name2;
				$values['from_company'] 		= $this->from_company;
				$values['from_address'] 		= $this->from_address;
				$values['from_address2'] 		= $this->from_address2;
				$values['from_city'] 			= $this->from_city;
				$values['from_state'] 			= $this->from_state;
				$values['from_zip'] 			= $this->from_zip;
				$values['from_country'] 		= $this->from_country;
				$values['from_phone_1'] 		= formatPhone($this->from_phone_1);
				$values['from_phone_2'] 		= formatPhone($this->from_phone_2);
				$values['from_phone_3'] 		= formatPhone($this->from_phone_3);
                $values['from_phone_4'] 		= formatPhone($this->from_phone_4);
				$values['from_phone_1_ext'] 		= ($this->from_phone1_ext=='')?'':' X '.$this->from_phone1_ext;
				$values['from_phone_2_ext'] 		= ($this->from_phone2_ext=='')?'':' X '.$this->from_phone2_ext;
				$values['from_phone_3_ext'] 		= ($this->from_phone3_ext=='')?'':' X '.$this->from_phone3_ext;
				$values['from_phone_4_ext'] 		= ($this->from_phone4_ext=='')?'':' X '.$this->from_phone4_ext;
				$values['from_phone_cell'] 		= formatPhone($this->from_phone_cell);
				$values['from_auction_name'] 	= $origin->auction_name;
				$values['from_booking_number'] 	= $this->from_booking_number;
				$values['from_buyer_number'] 	= $this->from_buyer_number;
				
				//$values['from_phone_4'] 		= $this->from_phone_4;
				$values['from_phone_cell2'] 	= formatPhone($this->from_phone_cell2);
				$values['from_fax'] 			= $this->from_fax;
				$values['from_fax2'] 			= $this->from_fax2;
				
				$values['to_name'] 				= $this->to_name;
				$values['to_name2'] 			= $this->to_name2;
				$values['to_company'] 			= $this->to_company;
				$values['to_address'] 			= $this->to_address;
				$values['to_address2'] 			= $this->to_address2;
				$values['to_city'] 				= $this->to_city;
				$values['to_state'] 			= $this->to_state;
				$values['to_zip'] 				= $this->to_zip;
				$values['to_country'] 			= $this->to_country;
				$values['to_phone_1'] 			= formatPhone($this->to_phone_1);
				$values['to_phone_2'] 			= formatPhone($this->to_phone_2);
				$values['to_phone_3'] 			= formatPhone($this->to_phone_3);
				$values['to_phone_4'] 			= formatPhone($this->to_phone_4);
				$values['to_phone_1_ext'] 			= ($this->to_phone1_ext=='')?'':' X '.$this->to_phone1_ext;
				$values['to_phone_2_ext'] 			= ($this->to_phone2_ext=='')?'':' X '.$this->to_phone2_ext;
				$values['to_phone_3_ext'] 			= ($this->to_phone3_ext=='')?'':' X '.$this->to_phone3_ext;
                $values['to_phone_4_ext'] 			= ($this->to_phone4_ext=='')?'':' X '.$this->to_phone4_ext;
				$values['to_phone_cell'] 		= formatPhone($this->to_phone_cell);
				$values['to_auction_name'] 		= $destination->auction_name;
				$values['to_booking_number'] 	= $this->to_booking_number;
				$values['to_buyer_number'] 	    = $this->to_buyer_number;
			    
				//$values['to_phone_4'] 		= $this->to_phone_4;
				$values['to_phone_cell2'] 	= formatPhone($this->to_phone_cell2);
				$values['to_fax'] 			= $this->to_fax;
				$values['to_fax2'] 			= $this->to_fax2;
	/*	}
		else{
		
		
			$values['from_name'] 			= "";
			$values['from_company'] 		= "";
			$values['from_address'] 		= "";
			$values['from_address2'] 		= "";
			$values['from_city'] 			= "";
			$values['from_state'] 			= "";
			$values['from_zip'] 			= "";
			$values['from_country'] 		= "";
			$values['from_phone_1'] 		= "";
			$values['from_phone_2'] 		= "";
			$values['from_phone_cell'] 		= "";
			$values['from_booking_number'] 	= "";
			$values['from_buyer_number'] 	= "";
			
			$values['to_name'] 				= "";
			$values['to_company'] 			= "";
			$values['to_address'] 			= "";
			$values['to_address2'] 			= "";
			$values['to_city'] 				= "";
			$values['to_state'] 			= "";
			$values['to_zip'] 				= "";
			$values['to_country'] 			= "";
			$values['to_phone_1'] 			= "";
			$values['to_phone_2'] 			= "";
			$values['to_phone_cell'] 		= "";
			//$values['to_auction_name'] 		= "";
			$values['to_booking_number'] 	= "";
			$values['to_buyer_number'] 	    = "";
		}
		*/ 
		
		if (trim($this->signature) == '' || !$showSignature) {
			$tpl->signature = '';
		} else {
			$tpl->signature = '<img src="'.BASE_PATH.'application/dispatches/signature/id/'.$this->id.'/sign.jpg"  width="300" height="75"/>';
		}
		
		$filePath = BASE_PATH."uploads".DIRECTORY_SEPARATOR."company".DIRECTORY_SEPARATOR.$member->getOwnerId()."_small.jpg";
		
		if (trim($member->getOwnerId()) == '') {
			$tpl->c_logo_path = '';
		} else {
			$tpl->c_logo_path = '<img src="'.$filePath.'"  width="300" height="75"/>';
		}
		
		
		
		foreach ($values as $k => $v) {
			if (!in_array($k, array('dispatch_terms'))) {
				$values[$k] = htmlspecialchars($v);
			}
		}
		
		
		//$values['vehicles'] = $this->getVehiclesHtml();
		
		if($type == 'pdf'){
			$values['vehicles'] = $this->getVehiclesHtmlNew();//$this->getVehiclesHtml();
		  return $tpl->build("dispatches.sheet_pdf", $values);
		}
		else{
			$values['vehicles'] = $this->getVehiclesHtml();
		  return $tpl->build("dispatches.sheet_new", $values);
		}
	}

	public function getVehiclesHtml() {
		$vehicles = $this->getVehicles();
		$vehicles_html = "";
		if (count($vehicles) > 0) {
			$vehicles_html = "<table cellpadding='0' cellspacing='1' border='0' class='vehicles-table'>";
			$vehicles_html.= "<tr><th>Make</th><th>Model</th><th>Year</th><th>Type</th><th>Vin #</th><th>Lot #</th><th>Plate #</th><th>State</th><th>Color</th><th>Inop</th></tr>";
			foreach ($vehicles as $vehicle) {
				$vehicles_html.= "<tr><td>" . htmlspecialchars($vehicle->make) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->model) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->year) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->type) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->vin) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->lot) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->plate) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->state) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->color) . "</td>";
				$vehicles_html.= "<td>" . ($vehicle->inop?'Yes':'No') . "</td></tr>";
			}
			$vehicles_html.= "</table>";
		}
		return $vehicles_html;
	}
	
	public function getVehiclesHtmlNew() {
		$vehicles = $this->getVehicles();
		$vehicles_html = "";
		if (count($vehicles) > 0) {
			$vehicles_html = "<table cellpadding='0' cellspacing='0' border='0' class='vehicles-table' width='100%'>";
			$vehicles_html.= "<tr><th>Make</th><th>Model</th><th>Year</th><th>Type</th><th>Vin #</th><th>Lot #</th><th>Plate #</th><th>State</th><th>Color</th><th>Inop</th></tr>";
			foreach ($vehicles as $vehicle) {
				$vehicles_html.= "<tr><td  width='100' height='22'>" . htmlspecialchars($vehicle->make) . "</td>";
				$vehicles_html.= "<td   width='100'>" . htmlspecialchars($vehicle->model) . "</td>";
				$vehicles_html.= "<td  width='100'>" . htmlspecialchars($vehicle->year) . "</td>";
				$vehicles_html.= "<td width='150'>" . htmlspecialchars($vehicle->type) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->vin) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->lot) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->plate) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->state) . "</td>";
				$vehicles_html.= "<td>" . htmlspecialchars($vehicle->color) . "</td>";
				$vehicles_html.= "<td>" . ($vehicle->inop?'Yes':'No') . "</td></tr>";
			}
			
			$NumOfVehicles = 10-count($vehicles);
			for($i=0;$i<$NumOfVehicles;$i++)
			{
				 $vehicles_html.= "<tr><td  height='22'>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td>";
				$vehicles_html.= "<td>&nbsp;</td></tr>";
			}
			$vehicles_html.= "</table>";
		}
		return $vehicles_html;
	}




	public function getPdf($out = "D", $path = "DispatchSheet.pdf") {
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		$pdf->SetAuthor($this->getOrder()->getAssigned()->getCompanyProfile()->companyname);
		$pdf->SetSubject("Dispatch Sheet");
		$pdf->SetTitle("Dispatch Sheet");
		$pdf->SetCreator("FreightDragon.com");
		$pdf->SetAutoPageBreak(true, 30);
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print.css")."</style>", 1);
		$pdf->writeHTML($this->getHtml(new template(),'pdf', false), 2);
		$signPath = null;
		if (trim($this->signature) != '') {
			$signPath = ROOT_PATH.'uploads'.DIRECTORY_SEPARATOR.'sign_tmp';
			if (!file_exists($signPath)) {
				mkdir($signPath, 0755);
			}
			$signPath .= DIRECTORY_SEPARATOR.md5(mt_rand().time()).'.jpg';
			file_put_contents($signPath, $this->signature);

			$pdf->showWatermarkImage = true;
			$pdf->SetWatermarkImage($signPath, 1, 'D', array(10, 250));
		}
		ob_end_clean();
		$pdf->Output($path, $out);
		if (!is_null($signPath)) {
			unlink($signPath);
		}
	}

    public function loadByHash($hash)
    {
        $id = $this->db->selectField('id', self::TABLE, "WHERE `hash_link` LIKE '" . mysqli_real_escape_string($this->db->connection_id, $hash) . "'");
        return $this->load($id);
    }
	
	
	public function acceptNew() {
		$entity = new Entity($this->db);
		$entity->load($this->entity_id);
	
	if (!empty($_SERVER["HTTP_CLIENT_IP"]))
	{
	 //check for ip from share internet
	 $ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
	 // Check for the Proxy User
	 $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	else
	{
	 $ip = $_SERVER["REMOTE_ADDR"];
	}
	

		$entity->setStatus(Entity::STATUS_DISPATCHED);
		
		$this->update(array(
			"accepted" => date("Y-m-d H:i:s"),
			"modified_by" => $_SESSION['member_id'],
			"modified_ip" => $ip
		));
	
        //$entity = new Entity($this->db);
        //$entity->load($this->entity_id);
        //save PDF under Documents
        $fname = md5(mt_rand()." ".time()." ".$this->entity_id);
        $path = ROOT_PATH . "uploads/entity/" . $fname;
        $entity->getDispatchSheet()->getPdfNew("F", $path);

        $ins_arr = array(
            'name_original' => "Dispatch sheet ".date("Y-m-d H-i-s").".pdf",
            'name_on_server' => $fname,
            'size' => filesize($path),
            'type' => "pdf",
            'date_uploaded' => date("Y-m-d H:i:s"),
            'owner_id' => $entity->getAssigned()->parent_id,
            'status' => 0,
        );

        $this->db->insert("app_uploads", $ins_arr);
        $ins_id = $this->db->get_insert_id();

        $this->db->insert("app_entity_uploads", array("entity_id"=>$entity->id, "upload_id"=>$ins_id));

		$mail = new FdMailer(true);
		$mail->isHTML();
		$mail->Body = "Thank you <strong>$this->sign_by</strong> for accepting <strong>$this->c_companyname's </strong> Dispatch Sheet on behalf of <strong>$this->carrier_company_name</strong>. For your convenience we have attached a copy of your dispatch sheet. If this was sent to you by error please contact <strong>$this->c_companyname</strong> at <strong>$this->c_dispatch_phone</strong><br><br>Sincerely,<br>$this->c_companyname<br><br><br>This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager. This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender <strong>$this->c_companyname</strong> immediately by phone <strong>$this->c_dispatch_phone</strong> if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. <br><br><br>FreightDragon&trade;&reg; Software Solutions<br>";
		
		$mail->Subject = "$this->carrier_company_name thank you for accepting dispatch ID $this->order_number";
		$mail->AddAddress($this->carrier_email, $this->carrier_contact_name);
		//$mail->setFrom('noreply@freightdragon.com');
		
		//if($entity->getAssigned()->parent_id == 1)
		  //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
		  //$mail->setFrom('noreply@freightdragon.com');
		//else
		  $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
			  
		
		$mail->AddAttachment($path, "Dispatch Sheet ID $this->order_number accepted on ".date("m-d-y").".pdf");
		$mail->send();
		//$mail = new FdMailer(true);
		//$mail->isHTML();
		
		$mail = new FdMailer(true);
		$mail->isHTML();
		$mail->Body = "This is an automated message to inform you that load <strong>$this->order_number</strong> has been successfully dispatched to <strong>$this->carrier_company_name</strong> for pickup. Included in this notification is a copy of the signed dispatch sheet. <br><br>Please contact your dispatch department if you need to change anything or have any questions at <strong>$this->c_dispatch_phone</strong>.<br><br>Sincerely,<br>$this->c_companyname<br><br><br>This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager. This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender <strong>$this->c_companyname</strong> immediately by phone <strong>$this->c_dispatch_phone</strong> if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.<br><br><br>FreightDragon&trade;&reg; Software Solutions<br>";
		
		$mail->Subject = "$this->carrier_company_name has accepted to transport Order ID: $this->order_number";
		$mail->AddAddress($entity->getAssigned()->email, $entity->getAssigned()->contactname);
		//$mail->setFrom('noreply@freightdragon.com');
		//if($entity->getAssigned()->parent_id == 1)
		  //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
		  //$mail->setFrom('noreply@freightdragon.com');
		//else
		  $mail->setFrom($entity->getAssigned()->getDefaultSettings()->smtp_from_email);
		  
		$mail->AddAttachment($path, "Dispatch Sheet for Order ID $this->order_number accepted on ".date("m-d-y").".pdf");
		$mail->send();
		$mail = new FdMailer(true);
		$mail->isHTML();
		
		
		//return $this;
		return $ins_id;
	}
	

	
public function getPdfNew($out = "D", $path = "DispatchSheet.pdf") {
		$entity = new Entity($this->db);
        $entity->load($this->entity_id);

         $member = $entity->getAssigned();
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor($this->getOrder()->getAssigned()->getCompanyProfile()->companyname);
		$pdf->SetSubject("$this->sign_by from $this->carrier_company_name accepted to transport $this->order_number"); 
		$pdf->SetTitle("Dispatch Sheet $this->order_number ".date("m-d-y")."");
		$pdf->SetCreator("FreightDragon.com");
		$pdf->SetAutoPageBreak(true, 30);
		//$pdf->setAutoTopMargin='pad';
		$pdf->SetTopMargin(22);
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_print_pdf.css")."</style>", 1);
		
	$header = '<table cellpadding="0" cellspacing="0" border="0" class="dispatch_table" width="100%" style="border:1px solid #000000;">
 <tr>
	<td class="group"  align="center">
      <span class="dis_heading">TRANSPORT DISPATCH SHEET -- NOT TO BE USED AS A BOL</span>
    </td>
 </tr> 
 <tr>
	<td class="group" align="center">
      <span class="dis_heading_small">SUBJECT TO THE TERMS AND CONDITIONS--QUESTIONS?</span> <span class="dis_heading_vsmall">@c_phone@</span>
    </td>
 </tr>  
</table>
 ';
     //$pdf->SetHTMLHeader($header,'O');
	 $pdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold;border-left:1px solid #2C87B9;border-right:1px solid #2C87B9;border-top:1px solid #2C87B9;height:230px;"><span class="dis_heading">TRANSPORT DISPATCH SHEET -- NOT TO BE USED AS A BOL</span><br><span class="dis_heading_small1">SUBJECT TO THE TERMS AND CONDITIONS--QUESTIONS?</span> <span class="dis_heading_vsmall">'.$this->c_dispatch_phone.'</span></div>','O'); 
     $pdf->SetHTMLHeader('<div style="text-align: center; font-weight: bold;border-left:1px solid #2C87B9;border-right:1px solid #2C87B9;border-top:1px solid #2C87B9;height:230px;"><span class="dis_heading">TRANSPORT DISPATCH SHEET -- NOT TO BE USED AS A BOL</span><br><span class="dis_heading_small1">SUBJECT TO THE TERMS AND CONDITIONS--QUESTIONS?</span> <span class="dis_heading_vsmall">'.$this->c_dispatch_phone.'</span></div>','E');
		
      $footer = ' <table cellpadding="11" cellspacing="11"   width="800" height="400"   style="background-color:#ffffff; border:1px solid #2C87B9;">
               <tr>
                 <td>
                   <b>ACCEPTED BY:<span style="font-family:franklingothicmedium,quillscript;"> '.$this->sign_by.'</span></b>
                 </td>
                 <td width="30%">
                   <b>DATE ACCEPTED: '.$this->getAccepted("m/d/Y").'</b>
                 </td>
                 <td width="33%">
                   <b>IP ADDRESS: '.$this->modified_ip.'</b>
                 </td>
                </tr> 
				<tr>
				 <td>
				   <span class="dis_heading_small">Powered by FreightDragon&trade;&reg;</span> 
				 </td>
				<td>
				   <span class="dis_heading_small">Dispatch ID '.$this->order_number.'</span> 
				 </td>$entity->
				 <td  align="right">
				 <span class="dis_heading_small">Page {PAGENO} of {nb}</span>
</td>
				</tr> 
              </table>';
 
     
	$pdf->SetHTMLFooter($footer,$out);	
	 
	 $pdf->writeHTML($this->getHtmlNew(new template(), 'pdf'), 2);
	 
	
		/*
		$signPath = null;
		if (trim($this->signature) != '') {
			$signPath = ROOT_PATH.'uploads'.DIRECTORY_SEPARATOR.'sign_tmp';
			if (!file_exists($signPath)) {
				mkdir($signPath, 0755);
			}
			$signPath .= DIRECTORY_SEPARATOR.md5(mt_rand().time()).'.jpg';
			file_put_contents($signPath, $this->signature);

			$pdf->showWatermarkImage = true;
			$pdf->SetWatermarkImage($signPath, 1, 'D', array(10, 250));
		}
		*/
		ob_end_clean();
		$pdf->Output($path, $out);
		if (!is_null($signPath)) {
			unlink($signPath);
		}
	}
	

	
}