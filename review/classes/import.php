<?php

require_once ROOT_PATH . 'libs/excel/PHPExcel.php';

class Import {


	protected static $quote_fields = array(
        'quote_id',
		'Assigned_To',
		'status',

		'shipper_first_name', 'shipper_last_name', 'shipper_company', 'shipper_email', 'shipper_phone', 'shipper_phone2', 'shipper_mobile', 'shipper_fax', 'shipper_address1', 'shipper_address2', 'shipper_city', 'shipper_state', 'shipper_zip', 'shipper_country',

		'origin_city', 'origin_state', 'origin_zip', 'origin_country',

		'destination_city', 'destination_state', 'destination_zip', 'destination_country',

		'estimated_ship_date', 'ship_via', 'shipper_notes',

		'vehicle1_year', 'vehicle1_make', 'vehicle1_model', 'vehicle1_type', 'vehicle1_vin', 'vehicle1_color', 'vehicle1_plate', 'vehicle1_state', 'vehicle1_lot', 'vehicle1_inop', 'vehicle1_carrier_pay', 'vehicle1_deposit',

		'vehicle2_year', 'vehicle2_make', 'vehicle2_model', 'vehicle2_type', 'vehicle2_vin', 'vehicle2_color', 'vehicle2_plate', 'vehicle2_state', 'vehicle2_lot', 'vehicle2_inop', 'vehicle2_carrier_pay', 'vehicle2_deposit',

		'vehicle3_year', 'vehicle3_make', 'vehicle3_model', 'vehicle3_type', 'vehicle3_vin', 'vehicle3_color', 'vehicle3_plate', 'vehicle3_state', 'vehicle3_lot', 'vehicle3_inop', 'vehicle3_carrier_pay', 'vehicle3_deposit',

		'vehicle4_year', 'vehicle4_make', 'vehicle4_model', 'vehicle4_type', 'vehicle4_vin', 'vehicle4_color', 'vehicle4_plate', 'vehicle4_state', 'vehicle4_lot', 'vehicle4_inop', 'vehicle4_carrier_pay', 'vehicle4_deposit',

		'vehicle5_year', 'vehicle5_make', 'vehicle5_model', 'vehicle5_type', 'vehicle5_vin', 'vehicle5_color', 'vehicle5_plate', 'vehicle5_state', 'vehicle5_lot', 'vehicle5_inop', 'vehicle5_carrier_pay', 'vehicle5_deposit',

		'vehicle6_year', 'vehicle6_make', 'vehicle6_model', 'vehicle6_type', 'vehicle6_vin', 'vehicle6_color', 'vehicle6_plate', 'vehicle6_state', 'vehicle6_lot', 'vehicle6_inop', 'vehicle6_carrier_pay', 'vehicle6_deposit',

		'vehicle7_year', 'vehicle7_make', 'vehicle7_model', 'vehicle7_type', 'vehicle7_vin', 'vehicle7_color', 'vehicle7_plate', 'vehicle7_state', 'vehicle7_lot', 'vehicle7_inop', 'vehicle7_carrier_pay', 'vehicle7_deposit',

		'vehicle8_year', 'vehicle8_make', 'vehicle8_model', 'vehicle8_type', 'vehicle8_vin', 'vehicle8_color', 'vehicle8_plate', 'vehicle8_state', 'vehicle8_lot', 'vehicle8_inop', 'vehicle8_carrier_pay', 'vehicle8_deposit',

		'vehicle9_year', 'vehicle9_make', 'vehicle9_model', 'vehicle9_type', 'vehicle9_vin', 'vehicle9_color', 'vehicle9_plate', 'vehicle9_state', 'vehicle9_lot', 'vehicle9_inop', 'vehicle9_carrier_pay', 'vehicle9_deposit',

		'vehicle10_year', 'vehicle10_make', 'vehicle10_model', 'vehicle10_type', 'vehicle10_vin', 'vehicle10_color', 'vehicle10_plate', 'vehicle10_state', 'vehicle10_lot', 'vehicle10_inop', 'vehicle10_carrier_pay', 'vehicle10_deposit',

	);



	protected static $order_fields = array(
        'order_id',
		'status',

		'shipper_first_name', 'shipper_last_name', 'shipper_company', 'shipper_email', 'shipper_phone', 'shipper_phone2', 'shipper_mobile', 'shipper_fax', 'shipper_address1', 'shipper_address2', 'shipper_city', 'shipper_state', 'shipper_zip', 'shipper_country',

		'origin_address1', 'origin_address2', 'origin_city', 'origin_state', 'origin_zip', 'origin_country', 'origin_contact_name', 'origin_company_name', 'origin_auction_name', 'origin_booking_number', 'origin_buyer_number', 'origin_phone1', 'origin_phone2', 'origin_phone3', 'origin_phone_mobile',

		'destination_address1', 'destination_address2', 'destination_city', 'destination_state', 'destination_zip', 'destination_country', 'destination_contact_name', 'destination_company_name', 'destination_phone1', 'destination_phone2', 'destination_phone3', 'destination_phone_mobile',

		'avail_pickup_date', 'ship_via', 'load_date_type', 'load_date', 'delivery_date_type', 'delivery_date', 'shipper_notes', 'notes_to_shipper',

		'balance_paid_by', 'pickup_terminal_fee', 'delivery_terminal_fee',

		'vehicle1_year', 'vehicle1_make', 'vehicle1_model', 'vehicle1_type', 'vehicle1_vin', 'vehicle1_color', 'vehicle1_plate', 'vehicle1_state', 'vehicle1_lot', 'vehicle1_inop', 'vehicle1_carrier_pay', 'vehicle1_deposit',

		'vehicle2_year', 'vehicle2_make', 'vehicle2_model', 'vehicle2_type', 'vehicle2_vin', 'vehicle2_color', 'vehicle2_plate', 'vehicle2_state', 'vehicle2_lot', 'vehicle2_inop', 'vehicle2_carrier_pay', 'vehicle2_deposit',

		'vehicle3_year', 'vehicle3_make', 'vehicle3_model', 'vehicle3_type', 'vehicle3_vin', 'vehicle3_color', 'vehicle3_plate', 'vehicle3_state', 'vehicle3_lot', 'vehicle3_inop', 'vehicle3_carrier_pay', 'vehicle3_deposit',

		'vehicle4_year', 'vehicle4_make', 'vehicle4_model', 'vehicle4_type', 'vehicle4_vin', 'vehicle4_color', 'vehicle4_plate', 'vehicle4_state', 'vehicle4_lot', 'vehicle4_inop', 'vehicle4_carrier_pay', 'vehicle4_deposit',

		'vehicle5_year', 'vehicle5_make', 'vehicle5_model', 'vehicle5_type', 'vehicle5_vin', 'vehicle5_color', 'vehicle5_plate', 'vehicle5_state', 'vehicle5_lot', 'vehicle5_inop', 'vehicle5_carrier_pay', 'vehicle5_deposit',

		'vehicle6_year', 'vehicle6_make', 'vehicle6_model', 'vehicle6_type', 'vehicle6_vin', 'vehicle6_color', 'vehicle6_plate', 'vehicle6_state', 'vehicle6_lot', 'vehicle6_inop', 'vehicle6_carrier_pay', 'vehicle6_deposit',

		'vehicle7_year', 'vehicle7_make', 'vehicle7_model', 'vehicle7_type', 'vehicle7_vin', 'vehicle7_color', 'vehicle7_plate', 'vehicle7_state', 'vehicle7_lot', 'vehicle7_inop', 'vehicle7_carrier_pay', 'vehicle7_deposit',

		'vehicle8_year', 'vehicle8_make', 'vehicle8_model', 'vehicle8_type', 'vehicle8_vin', 'vehicle8_color', 'vehicle8_plate', 'vehicle8_state', 'vehicle8_lot', 'vehicle8_inop', 'vehicle8_carrier_pay', 'vehicle8_deposit',

		'vehicle9_year', 'vehicle9_make', 'vehicle9_model', 'vehicle9_type', 'vehicle9_vin', 'vehicle9_color', 'vehicle9_plate', 'vehicle9_state', 'vehicle9_lot', 'vehicle9_inop', 'vehicle9_carrier_pay', 'vehicle9_deposit',

		'vehicle10_year', 'vehicle10_make', 'vehicle10_model', 'vehicle10_type', 'vehicle10_vin', 'vehicle10_color', 'vehicle10_plate', 'vehicle10_state', 'vehicle10_lot', 'vehicle10_inop', 'vehicle10_carrier_pay', 'vehicle10_deposit',

	);



	protected static $truck_fields = array(

		'name', 'trailer_type', 'inops', 'dispatch_phone',

	);



	protected static $carrier_fields = array(

		'company_name', 'print_name', 'status', 'type', 'hours_of_operation',

		'contact_name1', 'contact_name2', 'contact_phone1', 'contact_phone2', 'contact_cell_phone', 'contact_fax', 'contact_email', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country',

		'insurance_company_name', 'insurance_company_address', 'insurance_company_phone', 'insurance_certificate_holder', 'insurance_additionally_insured', 'insurance_agent_name', 'insurance_agent_phone', 'insurance_policy_number', 'insurance_expiration_date', 'insurance_broker_carrier_contract', 'insurance_icc_mc_number',

		'banned', 'notes',

	);



	protected static $location_fields = array(

		'company_name', 'print_name', 'status', 'type', 'hours_of_operation',

		'contact_name1', 'contact_name2', 'contact_phone1', 'contact_phone2', 'contact_cell_phone', 'contact_fax', 'contact_email', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country',

		'insurance_company_name', 'insurance_company_address', 'insurance_company_phone', 'insurance_agent_name', 'insurance_agent_phone', 'insurance_policy_number', 'insurance_expiration_date',

	);



	protected static $shipper_fields = array(

		'company_name', 'first_name', 'last_name', 'tax_id', 'print_name', 'status', 'type',

		'contact_name1', 'contact_name2', 'contact_phone1', 'contact_phone2', 'contact_cell_phone', 'contact_fax', 'contact_email', 'contact_address1', 'contact_address2', 'contact_city', 'contact_state', 'contact_zip', 'contact_country',

		'notes',

	);

	

	



	/**

	 * @param string $filePath

	 * @param int    $assign_id

	 * @param mysql  $DB

	 *

	 * @throws RuntimeException

	 * @return array

	 */

	public function importQuotes($filePath, $assign_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}



		$sheet = $objPHPExcel->getSheet(0);

		$highestRow = $sheet->getHighestDataRow();

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {
				
				if ($rowData[0] != self::$quote_fields) {
                  
					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$quote_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $quoteData) {

			try {

			
				$insert_arr = array(

					'fname' => $quoteData['shipper_first_name'],

					'lname' => $quoteData['shipper_last_name'],

					'company' => $quoteData['shipper_company'],

					'email' => $quoteData['shipper_email'],

					'phone1' => $quoteData['shipper_phone'],

					'phone2' => $quoteData['shipper_phone2'],

					'mobile' => $quoteData['shipper_mobile'],

					'fax' => $quoteData['shipper_fax'],

					'address1' => $quoteData['shipper_address1'],

					'address2' => $quoteData['shipper_address2'],

					'city' => $quoteData['shipper_city'],

					'state' => $quoteData['shipper_state'],

					'zip' => $quoteData['shipper_zip'],

					'country' => $quoteData['shipper_country'],

					'created' => date('Y-m-d H:i:s'),

				);

				$shipper = new Shipper($DB);

				$shipper_id = $shipper->create($insert_arr);

				$insert_arr = array(

					'city' => $quoteData['origin_city'],

					'state' => $quoteData['origin_state'],

					'zip' => $quoteData['origin_zip'],

					'country' => $quoteData['origin_country'],

				);

				$origin = new Origin($DB);

				$origin_id = $origin->create($insert_arr);

				$insert_arr = array(

					'city' => $quoteData['destination_city'],

					'state' => $quoteData['destination_state'],

					'zip' => $quoteData['destination_zip'],

					'country' => $quoteData['destination_country'],

				);

				$destination = new Destination($DB);

				$destination_id = $destination->create($insert_arr);
                 
				 $quote_idArr = array();
                $quote_idArr = explode("-",$quoteData['quote_id']);
				if(is_array($quote_idArr)){
				  $number = $quote_idArr[0];
				   if(sizeof($quote_idArr)>1)
				     $prefix = $quote_idArr[1];
				}
				
				/*
				$tempArrDate = array();
                $tempArrDate = explode("-",$quoteData['estimated_ship_date']);
                $dateStr = "20".trim($tempArrDate[2])."-".trim($tempArrDate[0])."-".trim($tempArrDate[1]);
                */
				$dateStr =$quoteData['estimated_ship_date'];
				
				$insert_arr = array(

					'type' => Entity::TYPE_QUOTE,
					
					'number' => $number,
					'prefix' => $prefix,

					'assigned_id' => $quoteData['Assigned_To'],//$assign_id,

					'received' => date('Y-m-d H:i:s'),

					'shipper_id' => $shipper_id,

					'origin_id' => $origin_id,

					'destination_id' => $destination_id,

					'est_ship_date' => date('Y-m-d H:i:s', strtotime(trim($dateStr))),//date('Y-m-d H:i:s', strtotime(trim($quoteData['estimated_ship_date']))),

					'ship_via' => self::getShipViaValue($quoteData['ship_via']),

					'status' => self::getStatusValue($quoteData['status']),

					'created' =>date('Y-m-d H:i:s'),

					'quoted' =>date('Y-m-d H:i:s'),

				);

				$quote = new Entity($DB);

				$quote->create($insert_arr);

              if($prefix==""){
				  $quote->update(array(

					'prefix' => $quote->getNewPrefix(),

				  ));
			  }

				if (!empty($quoteData['shipper_notes'])) {

					$note = new Note($DB);

					$insert_arr = array(

						'entity_id' => $quote->id,

						'type' => Note::TYPE_FROM,

						'text' => $quoteData['shipper_notes']

					);

					$note->create($insert_arr);

				}

				for ($i = 1; $i <= 10; $i++) {

					if (trim($quoteData['vehicle'.$i.'_year']) != '') {

						$insert_arr = array(

							'entity_id' => $quote->id,

							'year' => $quoteData['vehicle'.$i.'_year'],

							'make' => $quoteData['vehicle'.$i.'_make'],

							'model' => $quoteData['vehicle'.$i.'_model'],

							'type' => $quoteData['vehicle'.$i.'_type'],

							'color' => $quoteData['vehicle'.$i.'_color'],

							'plate' => $quoteData['vehicle'.$i.'_plate'],

							'state' => $quoteData['vehicle'.$i.'_state'],

							'vin' => $quoteData['vehicle'.$i.'_vin'],

							'lot' => $quoteData['vehicle'.$i.'_lot'],

							'tariff' => (float)$quoteData['vehicle'.$i.'_carrier_pay'] + (float)$quoteData['vehicle'.$i.'_deposit'],

							'carrier_pay' => $quoteData['vehicle'.$i.'_carrier_pay'],

							'deposit' => $quoteData['vehicle'.$i.'_deposit'],

							'created' => date('Y-m-d H:i:s'),

							'inop' => self::getInopValue($quoteData['vehicle'.$i.'_inop']),

						);

						$vehicle = new Vehicle($DB);

						$vehicle->create($insert_arr);

					}

				}

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	/**

	 * @param string $filePath

	 * @param int    $assign_id

	 * @param mysql  $DB

	 *

	 * @throws RuntimeException

	 * @return array

	 */

	public function importOrders($filePath, $assign_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}



		$sheet = $objPHPExcel->getSheet(0);

		$highestRow = $sheet->getHighestDataRow();

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {

				if ($rowData[0] != self::$order_fields) {

					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$order_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $orderData) {

			try {

				$insert_arr = array(

					'fname' => $orderData['shipper_first_name'],

					'lname' => $orderData['shipper_last_name'],

					'company' => $orderData['shipper_company'],

					'email' => $orderData['shipper_email'],

					'phone1' => $orderData['shipper_phone'],

					'phone2' => $orderData['shipper_phone2'],

					'mobile' => $orderData['shipper_mobile'],

					'fax' => $orderData['shipper_fax'],

					'address1' => $orderData['shipper_address1'],

					'address2' => $orderData['shipper_address2'],

					'city' => $orderData['shipper_city'],

					'state' => $orderData['shipper_state'],

					'zip' => $orderData['shipper_zip'],

					'country' => $orderData['shipper_country'],

					'created' => date('Y-m-d H:i:s'),

				);

				$shipper = new Shipper($DB);

				$shipper_id = $shipper->create($insert_arr);

				$insert_arr = array(

					'name' => $orderData['origin_contact_name'],

					'company' => $orderData['origin_company_name'],

					'address1' => $orderData['origin_address1'],

					'address2' => $orderData['origin_address2'],

					'city' => $orderData['origin_city'],

					'state' => $orderData['origin_state'],

					'zip' => $orderData['origin_zip'],

					'country' => $orderData['origin_country'],

					'auction_name' => $orderData['origin_auction_name'],

					'phone1' => $orderData['origin_phone1'],

					'phone2' => $orderData['origin_phone2'],

					'phone3' => $orderData['origin_phone3'],

					'phone_cell' => $orderData['origin_phone_mobile'],

				);

				$origin = new Origin($DB);

				$origin_id = $origin->create($insert_arr);

				$insert_arr = array(

					'name' => $orderData['destination_contact_name'],

					'company' => $orderData['destination_contact_company'],

					'address1' => $orderData['destination_address1'],

					'address2' => $orderData['destination_address2'],

					'city' => $orderData['destination_city'],

					'state' => $orderData['destination_state'],

					'zip' => $orderData['destination_zip'],

					'country' => $orderData['destination_country'],

					'phone1' => $orderData['destination_phone1'],

					'phone2' => $orderData['destination_phone2'],

					'phone3' => $orderData['destination_phone3'],

					'phone_cell' => $orderData['destination_phone_mobile'],

				);

				$destination = new Destination($DB);

				$destination_id = $destination->create($insert_arr);

				$insert_arr = array(

					'type' => Entity::TYPE_ORDER,
					
					'number' => $orderData['order_id'],

					'assigned_id' => $assign_id,

					'received' => date('Y-m-d H:i:s'),

					'shipper_id' => $shipper_id,

					'origin_id' => $origin_id,

					'destination_id' => $destination_id,

					'avail_pickup_date' => date('Y-m-d H:i:s', strtotime(trim($orderData['avail_pickup_date']))),

					'load_date_type' => self::getDateTypeValue($orderData['load_date_type']),

					'load_date' => date('Y-m-d H:i:s', strtotime(trim($orderData['load_date']))),

					'delivery_date_type' => self::getDateTypeValue($orderData['delivery_date_type']),

					'delivery_date' => date('Y-m-d H:i:s', strtotime(trim($orderData['delivery_date']))),

					'ship_via' => self::getShipViaValue($orderData['ship_via']),

					'status' => self::getStatusValue($orderData['status']),

					'created' =>date('Y-m-d H:i:s'),

					'ordered' =>date('Y-m-d H:i:s'),

					'information' => $orderData['notes_to_shipper'],

					'balance_paid_by' => self::getBalancePaidByValue($orderData['balance_paid_by']),

					'pickup_terminal_fee' => $orderData['pickup_terminal_fee'],

					'dropoff_terminal_fee' => $orderData['delivery_terminnal_fee'],

					'buyer_number' => $orderData['origin_buyer_number'],

					'booking_number' => $orderData['origin_booking_number'],

				);

				$order = new Entity($DB);

				$order->create($insert_arr);
	/*		
 print "<br><br><pre>".$orderData['order_id'];
			print_r($insert_arr);
			print "</pre>";
	*/		
           if(empty($orderData['order_id'])){
				$order->update(array(

					'prefix' => $order->getNewPrefix(),

				));
             }
				if (!empty($orderData['shipper_notes'])) {

					$note = new Note($DB);

					$insert_arr = array(

						'entity_id' => $order->id,

						'type' => Note::TYPE_FROM,

						'text' => $orderData['shipper_notes']

					);

					$note->create($insert_arr);

				}

				for ($i = 1; $i <= 10; $i++) {

					if (trim($orderData['vehicle'.$i.'_year']) != '') {

						$insert_arr = array(

							'entity_id' => $order->id,

							'year' => $orderData['vehicle'.$i.'_year'],

							'make' => $orderData['vehicle'.$i.'_make'],

							'model' => $orderData['vehicle'.$i.'_model'],

							'type' => $orderData['vehicle'.$i.'_type'],

							'color' => $orderData['vehicle'.$i.'_color'],

							'plate' => $orderData['vehicle'.$i.'_plate'],

							'state' => $orderData['vehicle'.$i.'_state'],

							'vin' => $orderData['vehicle'.$i.'_vin'],

							'lot' => $orderData['vehicle'.$i.'_lot'],

							'tariff' => (float)$orderData['vehicle'.$i.'_carrier_pay'] + (float)$orderData['vehicle'.$i.'_deposit'],

							'carrier_pay' => $orderData['vehicle'.$i.'_carrier_pay'],

							'deposit' => $orderData['vehicle'.$i.'_deposit'],

							'created' => date('Y-m-d H:i:s'),

							'inop' => self::getInopValue($orderData['vehicle'.$i.'_inop']),

						);

						$vehicle = new Vehicle($DB);

						$vehicle->create($insert_arr);

					}

				}

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	public function importTrucks($filePath, $owner_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}



		$sheet = $objPHPExcel->getSheet(0);

		$highestRow = $sheet->getHighestDataRow();

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {

				if ($rowData[0] != self::$truck_fields) {

					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$truck_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $truckData) {

			try {

				$insert_arr = array(

					'owner_id' => $owner_id,

					'name' => $truckData['name'],

					'trailer' => self::getTrailerTypeValue($truckData['trailer_type']),

					'inops' => self::getInopsOkValue($truckData['inops']),

					'phone' => $truckData['dispatch_phone'],

					'created' => date('Y-m-d H:i:s'),

				);

				$truck = new Truck($DB);

				$truck->create($insert_arr);

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	

	

	

	public function importShippers($filePath, $owner_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}



		$sheet = $objPHPExcel->getSheet(0);

		$highestRow = $sheet->getHighestDataRow();

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {

				if ($rowData[0] != self::$shipper_fields) {

					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$shipper_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $shipperData) {

			try {

				$insert_arr = array(

					'owner_id' => $owner_id,

					'is_shipper' => 1,

					'company_name' => $shipperData['company_name'],

					'first_name' => $shipperData['first_name'],

					'last_name' => $shipperData['last_name'],

					'tax_id_num' => $shipperData['tax_id'],

					'print_name' => $shipperData['print_name'],

					'status' => (strtolower(trim($shipperData['status'])) == 'active')?1:0,

					'shipper_type' => substr($shipperData['type'], 0, 12),

					'contact_name1' => $shipperData['contact_name1'],

					'contact_name2' => $shipperData['contact_name2'],

					'phone1' => $shipperData['contact_phone1'],

					'phone2' => $shipperData['contact_phone2'],

					'cell' => $shipperData['contact_cell_phone'],

					'fax' => $shipperData['contact_fax'],

					'email' => $shipperData['contact_email'],

					'address1' => $shipperData['contact_address1'],

					'address2' => $shipperData['contact_address2'],

					'city' => $shipperData['contact_city'],

					'state' => $shipperData['contact_state'],

					'zip_code' => $shipperData['contact_zip'],

					'country' => $shipperData['contact_country'],

					'notes' => $shipperData['notes'],

				);

				$account = new Account($DB);

				$account->create($insert_arr);

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	public function importCarriers($filePath, $owner_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}





		$sheet = $objPHPExcel->getSheet(0);

        $highestRow = $sheet->getHighestDataRow();

        $sheet->getStyle('AA1:AA'.$highestRow)

            ->getNumberFormat()

            ->setFormatCode('yyyy-m-d');

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {

				if ($rowData[0] != self::$carrier_fields) {

					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$carrier_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $carrierData) {

			try {

				$insert_arr = array(

					'owner_id' => $owner_id,

					'is_carrier' => 1,

					'company_name' => $carrierData['company_name'],

                    'print_name' => $carrierData['print_name'],

					'status' => (strtolower(trim($carrierData['status'])) == 'active')?1:0,

					'carrier_type' => $carrierData['type'],

					'contact_name1' => $carrierData['contact_name1'],

					'contact_name2' => $carrierData['contact_name2'],

					'phone1' => $carrierData['contact_phone1'],

					'phone2' => $carrierData['contact_phone2'],

					'cell' => $carrierData['contact_cell_phone'],

					'fax' => $carrierData['contact_fax'],

					'email' => $carrierData['contact_email'],

					'address1' => $carrierData['contact_address1'],

					'address2' => $carrierData['contact_address2'],

					'city' => $carrierData['contact_city'],

					'state' => $carrierData['contact_state'],

					'zip_code' => $carrierData['contact_zip'],

					'country' => $carrierData['contact_country'],

					'insurance_companyname' => $carrierData['insurance_company_name'],

					'insurance_address' => $carrierData['insurance_company_address'],

					'insurance_phone' => $carrierData['insurance_company_phone'],

					'insurance_agentname' => $carrierData['insurance_agent_name'],

					'insurance_agentphone' => $carrierData['insurance_agent_phone'],

					'insurance_polictynumber' => $carrierData['insurance_policy_number'],

					'insurance_expirationdate' => $carrierData['insurance_expiration_date'],

					'insurance_contract' => $carrierData['insurance_broker_carrier_contract'],

					'insurance_iccmcnumber' => $carrierData['insurance_icc_mc_number'],

					'notes' => $carrierData['notes'],

					'donot_dispatch' => (strtolower(trim($carrierData['banned'])) == 'yes')?1:0,

                    'hours_of_operation' => $carrierData['hours_of_operation'],

                    'insurance_holder' => (strtolower(trim($carrierData['insurance_certificate_holder'])) == 'yes')?1:0,

                    'insurance_insured' => (strtolower(trim($carrierData['insurance_additionally_insured'])) == 'yes')?1:0,

                    'insurance_policynumber' => $carrierData['insurance_policy_number'],

                );



				$account = new Account($DB);



				$account->createOrUpdate($insert_arr);

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	public function importLocations($filePath, $owner_id, $DB) {

		$success = 0;

		$failed = 0;

		try {

			$inputFileType = PHPExcel_IOFactory::identify($filePath);

			$objReader = PHPExcel_IOFactory::createReader($inputFileType);

			/** @var PHPExcel $objPHPExcel */

			$objPHPExcel = $objReader->load($filePath);

		} catch(Exception $e) {

			throw new RuntimeException($e->getMessage());

		}



		$sheet = $objPHPExcel->getSheet(0);

		$highestRow = $sheet->getHighestDataRow();

		$highestColumn = $sheet->getHighestDataColumn();

		$importData = array();

		for ($row = 1; $row <= $highestRow; $row++) {

			$rowData = $sheet->rangeToArray('A'.$row.':'.$highestColumn.$row, null, false, true);

			if ($row == 1) {

				if ($rowData[0] != self::$location_fields) {

					throw new RuntimeException('Invalid data format');

				}

			} else {

				$importData[$row] = array();

				foreach ($rowData[0] as $k => $column) {

					$importData[$row][self::$location_fields[$k]] = $column;

				}

			}

		}

		foreach ($importData as $locationData) {

			try {

				$insert_arr = array(

					'owner_id' => $owner_id,

					'is_location' => 1,

					'company_name' => $locationData['company_name'],

					'status' => (strtolower(trim($locationData['status'])) == 'active')?1:0,

					'location_type' => $locationData['type'],

					'hours_of_operation' => $locationData['hours_of_operation'],

					'contact_name1' => $locationData['contact_name1'],

					'contact_name2' => $locationData['contact_name2'],

					'phone1' => $locationData['contact_phone1'],

					'phone2' => $locationData['contact_phone2'],

					'cell' => $locationData['contact_cell_phone'],

					'fax' => $locationData['contact_fax'],

					'email' => $locationData['contact_email'],

					'address1' => $locationData['contact_address1'],

					'address2' => $locationData['contact_address2'],

					'city' => $locationData['contact_city'],

					'state' => $locationData['contact_state'],

					'zip_code' => $locationData['contact_zip'],

					'country' => $locationData['contact_country'],

					'insurance_companyname' => $locationData['insurance_company_name'],

					'insurance_address' => $locationData['insurance_company_address'],

					'insurance_agentname' => $locationData['insurance_agent_name'],

					'insurance_agentphone' => $locationData['insurance_agent_phone'],

					'insurance_polictynumber' => $locationData['insurance_policy_number'],

					'insurance_expirationdate' => $locationData['insurance_expiration_date'],

				);

				$account = new Account($DB);

				$account->createOrUpdate($insert_arr);

				$success++;

			} catch (Exception $e) {

				$failed++;

			}

		}

		return array(

			'success' => $success,

			'failed' => $failed,

		);

	}



	protected static function getInopsOkValue($value) {

		switch (strtolower(trim($value))) {

			case 'yes':

				return 1;

			case 'no':

				return 2;

			default:

				return null;

		}

	}



	protected static function getTrailerTypeValue($value) {

		switch (strtolower(trim($value))) {

			case 'open':

				return 1;

			case 'enclosed':

				return 2;

			default:

				return null;

		}

	}



	protected static function getBalancePaidByValue($value) {

		switch (strtolower(trim($value))) {

			case 'cod_cash_credit':

				return Entity::BALANCE_COD_TO_CARRIER_CASH;

			case 'cod_check':

				return Entity::BALANCE_COD_TO_CARRIER_CHECK;

			case 'cod_comcheck':

				return Entity::BALANCE_COD_TO_CARRIER_COMCHECK;

			case 'cod_quickpay':

				return Entity::BALANCE_COD_TO_CARRIER_QUICKPAY;

			case 'cop_cash_credit':

				return Entity::BALANCE_COP_TO_CARRIER_CASH;

			case 'cop_check':

				return Entity::BALANCE_COP_TO_CARRIER_CHECK;

			case 'cop_comcheck':

				return Entity::BALANCE_COP_TO_CARRIER_COMCHECK;

			case 'cop_quickpay':

				return Entity::BALANCE_COP_TO_CARRIER_QUICKPAY;

			case 'company_owes_cash_credit':

				return Entity::BALANCE_COMPANY_OWES_CARRIER_CASH;

			case 'company_owes_check':

				return Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK;

			case 'company_owes_comcheck':

				return Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK;

			case 'company_owes_quickpay':

				return Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY;

			case 'carrier_owes_cash_credit':

				return Entity::BALANCE_CARRIER_OWES_COMPANY_CASH;

			case 'carrier_owes_check':

				return Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK;

			case 'carrier_owes_comcheck':

				return Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK;

			case 'carrier_owes_quickpay':

				return Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY;

			default:

				return null;

		}

	}



	protected static function getDateTypeValue($value) {

		switch (strtolower(trim($value))) {

			case 'estimated':

				return Entity::DATE_TYPE_ESTIMATED;

			case 'exactly':

				return Entity::DATE_TYPE_EXACTLY;

			case 'not_earlier_than':

				return Entity::DATE_TYPE_NO_EARLY;

			case 'not_later_than':

				return Entity::DATE_TYPE_NO_LATER;

			default:

				return null;

		}

	}



	protected static function getInopValue($value) {

		switch (strtolower(trim($value))) {

			case 'yes':

				return 1;

				break;

			default:

				return 0;

				break;

		}

	}



	protected static function getStatusValue($value) {

		switch (strtolower(trim($value))) {

			case 'new':

				return Entity::STATUS_ACTIVE;

				break;

			case 'hold':

				return Entity::STATUS_ONHOLD;

				break;

			case 'archived':

				return Entity::STATUS_ARCHIVED;

				break;

			default:

				return Entity::STATUS_ACTIVE;

				break;

		}

	}



	protected static function getShipViaValue($value) {

		switch (strtolower(trim($value))) {

			case 'open':

				return 1;

				break;

			case 'enclosed':

				return 2;

				break;

			case 'driveaway':

				return 3;

				break;

			default:

				return null;

				break;

		}

	}

}

 