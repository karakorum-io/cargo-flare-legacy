<?php
	class AppQuote extends AppAction {
		public function idx() {
			try {
				if (!isset($_GET['hash'])) redirect(getLink(''));
				$this->renderParentLayout = false;
				$this->tplname = "quote.new";
				$this->input['title'] = "Quote Request";
				$row = $this->daffny->DB->selectRow("*", "app_externalforms", "WHERE `hash` = '".mysqli_real_escape_string($this->daffny->DB->connection_id, $_GET['hash'])."'");
				if (!$row) redirect(getLink(''));
				$member = new Member($this->daffny->DB);
				$member->load($row['owner_id']);
				$companyProfile = $member->getCompanyProfile();
				if ((count($_POST) > 0) && $sql_arr = $this->checkEditForm()) {
					$this->daffny->DB->transaction('start');
					/* Create Lead */
					$insert_arr = array(
						'type' => Entity::TYPE_QUOTE,
						'created' => date("Y-m-d H:i:s"),
                        'quoted' => date("Y-m-d H:i:s"),
						'received' => date("Y-m-d H:i:s"),
						'number' => $companyProfile->getNextNumber(),
						'source_id' => Leadsource::ID_EXTERNAL_FORM,
						'assigned_id' => $member->id,
						'est_ship_date' => date("Y-m-d", strtotime($sql_arr['shipping_est_date'])),
						'status' => Entity::STATUS_ACTIVE,
						'ship_via' => $sql_arr['shipping_ship_via'],
						'referred_by' => (isset($sql_arr['referred_by']))?$sql_arr['referred_by']:""
					);

                    if (isset($_POST["CUSTOM_EXTERNAL_FORM"])){
                        $insert_arr['source_id']  = Leadsource::ID_CUSTOM_EXTERNAL_FORM;
                    }

					$entity = new Entity($this->daffny->DB);
					$entity->create($insert_arr);
					/* Create Shipper */
					$shipper = new Shipper($this->daffny->DB);
					$insert_arr = array(
						'fname' => $sql_arr['shipper_fname'],
						'lname' => $sql_arr['shipper_lname'],
						'email' => $sql_arr['shipper_email'],
						'company' => (isset($sql_arr['shipper_company']))?$sql_arr['shipper_company']:"",
						'phone1' => $sql_arr['shipper_phone1'],
						'phone2' => (isset($sql_arr['shipper_phone2']))?$sql_arr['shipper_phone2']:"",
						'mobile' => (isset($sql_arr['shipper_mobile']))?$sql_arr['shipper_mobile']:"",
						'fax' => (isset($sql_arr['shipper_fax']))?$sql_arr['shipper_fax']:"",
						'address1' => (isset($sql_arr['shipper_address1']))?$sql_arr['shipper_address1']:"",
						'address2' => (isset($sql_arr['shipper_address2']))?$sql_arr['shipper_address2']:"",
						'city' => (isset($sql_arr['shipper_city']))?$sql_arr['shipper_city']:"",
						'state' => (isset($sql_arr['shipper_state']))?$sql_arr['shipper_state']:"",
						'zip' => (isset($sql_arr['shipper_zip']))?$sql_arr['shipper_zip']:"",
						'country' => (isset($sql_arr['shipper_country']))?$sql_arr['shipper_country']:""
					);
					$shipper->create($insert_arr, $entity->id);
					/* Create Origin */
					$origin = new Origin($this->daffny->DB);
					$insert_arr = array(
						'city' => $sql_arr['origin_city'],
						'state' => $sql_arr['origin_state'],
						'zip' => (isset($sql_arr['origin_zip']))?$sql_arr['origin_zip']:"",
						'country' => $sql_arr['origin_country']
					);
					$origin->create($insert_arr, $entity->id);
					/* Create Destination */
					$destination = new Destination($this->daffny->DB);
					$insert_arr = array(
						'city' => $sql_arr['destination_city'],
						'state' => $sql_arr['destination_state'],
						'zip' => (isset($sql_arr['destination_zip']))?$sql_arr['destination_zip']:"",
						'country' => $sql_arr['destination_country']
					);
					$destination->create($insert_arr, $entity->id);
					/* Update Quote */
					$update_arr = array(
						'shipper_id' => $shipper->id,
						'origin_id' => $origin->id,
						'destination_id' => $destination->id
					);
					$entity->update($update_arr);
					/* Create Vehicles */
					foreach ($sql_arr['year'] as $i => $year) {
						$vehicle = new Vehicle($this->daffny->DB);
						$insert_arr = array(
							'entity_id' => $entity->id,
							'year' => $sql_arr['year'][$i],
							'make' => $sql_arr['make'][$i],
							'model' => $sql_arr['model'][$i],
							'type' => $sql_arr['type'][$i],
							'lot' => $sql_arr['lot'][$i],
							'vin' => $sql_arr['vin'][$i],
							'plate' => $sql_arr['plate'][$i],
							'state' => $sql_arr['state'][$i],
							'color' => $sql_arr['color'][$i],
						);
						$vehicle->create($insert_arr);
					}
					/* Create Note */
					if (isset($sql_arr['shipping_notes']) && trim($sql_arr['shipping_notes']) != "") {
						$note = new Note($this->daffny->DB);
						$insert_arr = array(
							'entity_id' => $entity->id,
							'type' => Note::TYPE_FROM,
							'text' => $sql_arr['shipping_notes']
						);
						$note->create($insert_arr);
					}
					if ($entity->getAssigned()->getAutoQuotingSettings()->is_enabled) {
						$entity->autoQuoting();
					}
					$this->daffny->DB->transaction("commit");
					if (isset($_POST['post_back'])) {
						redirect($_POST['post_back']);
					} elseif (trim($row['redirect_url'])) {
						redirect($row['redirect_url']);
					} else {
						redirect(getLink('thankyou'));
					}
				} else {
					foreach($_POST as $key => $val) {
						if (!is_array($val)) {
							$this->input[$key] = htmlspecialchars($val);
						} else {
							foreach($val as $key2 => $val2) {
								$this->input[$key][$key2] = htmlspecialchars($val2);
							}
						}
					}
					if (count($this->err)) {
						$this->input['error'] = "<div class='form-errors'>";
						foreach($this->err as $err) {
							$this->input['error'] .= "<p>".$err."</p>";
						}
						$this->input['error'] .= "</div>";
					}
					if (isset($_POST['post_back'])) {
						if (isset($_POST['return_errors'])) {
							$s = (stripos($_POST['post_back'], "?") === false)?"?":"&";
							redirect($_POST['post_back'].$s."errors=".urlencode(json_encode($this->err)));
						} else {
							$this->tplname = "quote.errors";
							$this->daffny->tpl->post_back = $_POST['post_back'];
							$this->renderParentLayout = true;
							return;
						}
					}
				}
				$company = $member->getCompanyProfile();
				$tpl_vars = array(
					'company_name' => $company->companyname,
					'company_logo' => "<img src=\"" . SITE_IN . "uploads/company/" . $member->id . "_small.jpg?" . mt_rand() . "\" alt=\"{$company->companyname} Logo\" />",
					'year' => date("Y")
				);
				$this->input['header'] = $this->daffny->tpl->get_parsed_from_array($row['header'], $tpl_vars);
				$this->input['footer'] = $this->daffny->tpl->get_parsed_from_array($row['footer'], $tpl_vars);
				$this->getEditForm();
			} catch (FDException $e) {
				$this->daffny->DB->transaction("rollback");
				redirect(getLink(''));
			}
		}
		
		protected function getEditForm() {
			/* SHIPPER */
			$this->form->TextField("shipper", 32, array("tabindex"=>1), "Shipper", "</td><td>");
			$this->form->TextField("shipper_fname", 32, array("tabindex"=>2), $this->requiredTxt."First Name", "</td><td>");
			$this->form->TextField("shipper_lname", 32, array("tabindex"=>3), $this->requiredTxt."Last Name", "</td><td>");
			$this->form->TextField("shipper_email", 32, array("tabindex"=>5, 'class' => 'email'), $this->requiredTxt."Email", "</td><td>");
			$this->form->TextField("shipper_company", 64, array("tabindex"=>4), "Company", "</td><td>");
			$this->form->TextField("shipper_phone1", 32, array("tabindex"=>6, 'class' => 'phone'), $this->requiredTxt."Phone", "</td><td>");
			$this->form->TextField("shipper_phone2", 32, array("tabindex"=>7, 'class' => 'phone'), "Phone 2", "</td><td>");
			$this->form->TextField("shipper_mobile", 32, array("tabindex"=>8, 'class' => 'phone'), "Mobile", "</td><td>");
			$this->form->TextField("shipper_fax", 32, array("tabindex"=>9, 'class' => 'phone'), "Fax", "</td><td>");
			$this->form->TextField("shipper_address1", 64, array("tabindex"=>10 ), "Address", "</td><td>");
			$this->form->TextField("shipper_address2", 64, array("tabindex"=>11), "Address 2", "</td><td>");
			$this->form->TextField("shipper_city", 32, array("tabindex"=>12, 'class' => 'geo-city'), "City", "</td><td>");
			$this->form->ComboBox("shipper_state", array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array("tabindex"=>13, 'style' => 'width:150px;'), "State/Zip", "</td><td>");
			$this->form->TextField("shipper_zip", 8, array("tabindex"=>14, 'style' => 'width:50px;margin-left:7px;', 'class' => 'zip'), "", "");
			$this->form->ComboBox("shipper_country", $this->getCountries(), array("tabindex"=>15), "Country", "</td><td>");
			/* ORIGIN */
			$this->form->TextField("origin_city", 32, array("tabindex"=>16, 'class' => 'geo-city'), $this->requiredTxt."City", "</td><td>");
			$this->form->ComboBox("origin_state", array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:160px;',"tabindex"=>17 ), $this->requiredTxt."State/Zip", "</td><td>");
			$this->form->TextField("origin_zip", 64, array("tabindex"=>18, 'style' => 'width:50px;margin-left:5px;', 'class' => 'zip'), "", "");
			$this->form->ComboBox("origin_country", $this->getCountries(), array("tabindex"=>19), "Country", "</td><td>");
			/* DESTINATION */
			$this->form->TextField("destination_city", 32, array("tabindex"=>20, 'class' => 'geo-city'), $this->requiredTxt."City", "</td><td>");
			$this->form->ComboBox("destination_state", array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:160px;',"tabindex"=>21 ), $this->requiredTxt."State/Zip", "</td><td>");
			$this->form->TextField("destination_zip", 64, array("tabindex"=>22, 'style' => 'width:50px;margin-left:5px;', 'class' => 'zip'), "", "");
			$this->form->ComboBox("destination_country", $this->getCountries(), array("tabindex"=>23, ), "Country", "</td><td>");
			/* SHIPPING INFORMATION */
			$this->form->TextField("shipping_est_date", 8, array("tabindex"=>24, 'class' => 'datepicker'), $this->requiredTxt."Estimated Ship Date", "</td><td>");
			$this->form->ComboBox("shipping_ship_via", array('' => "Select One") + Entity::$ship_via_string, array("tabindex"=>26, ), $this->requiredTxt."Ship Via", "</td><td>");
			$this->form->TextArea("shipping_notes", 4, 10, array("tabindex"=>27, 'style' => 'height:80px;'), "Notes", "</td><td rowspan=\"3\">");
		}
		
		protected function checkEditForm() {
			$sql_arr = $_POST;
			$checkEmpty = array(
				'shipper_fname' => "Shipper First Name",
				'shipper_lname' => "Shipper Last Name",
				'shipper_email' => "Shipper Email",
				'shipper_phone1' => "Shipper Phone",
				'origin_city' => "Origin City",
				'origin_country' => 'Origin Country',
				'origin_state' => 'Origin State',
				'destination_city' => "Destination City",
				'destination_country' => 'Destination Country',
				'destination_state' => 'Destination State',
				'shipping_est_date' => 'Estimate Ship Date',
				'shipping_ship_via' => 'Ship Via'
			);
			foreach ($checkEmpty as $field => $label) {
				$this->isEmpty($field, $label);
			}
			if (!isset($_POST['year'])) {
				$this->err[] = "You must add at least one vehicle";
			} else {
				foreach ($_POST['year'] as $k => $v) {
					if ((!isset($_POST['make'][$k]) || trim($_POST['make'][$k]) == "")
						|| (!isset($_POST['make'][$k]) || trim($_POST['make'][$k]) == "")
						|| (!isset($_POST['make'][$k]) || trim($_POST['make'][$k]) == "")
						|| (trim($_POST['year'][$k]) == "")) {
						unset($_POST['year'][$k], $_POST['make'][$k], $_POST['model'][$k], $_POST['type'][$k]);
					}
				}
			}

			$this->checkEmail('shipper_email', "Shipper E-mail");
			if (count($this->err)) {
				foreach($sql_arr as $key => $value) {
					$this->input[$key] = $value;
				}
				return false;
			}
			return $sql_arr;
		}

		public function convert_to_order() 
		{
			$id = $_GET['id'];

			if(!empty($id)){
				// no parent layout
				$this->renderParentLayout = false;

				$this->tplname = "quote.convert";

				$entity = new Entity($this->daffny->DB);
				$entity->load($id);
				$this->daffny->tpl->entity = $entity;

				$origin = new Origin($this->daffny->DB);
				$origin->load($entity->origin_id);
				$this->daffny->tpl->origin = $origin;

				$destination = new Destination($this->daffny->DB);
				$destination->load($entity->destination_id);
				$this->daffny->tpl->dest = $destination;

				$this->daffny->tpl->vehicles = $entity->getVehicles();

			} else {
				die("Invalid Quote ID");
			}
		}

		public function make_order() 
		{
			$price = $_POST['price'];
			$entity = new Entity($this->daffny->DB);
			$entity->load($_POST['entity_id']);
			
			// if($entity->type == 2){
				$entity->update([
					'avail_pickup_date' => $_POST['avail_pickup_date']
				]);

				$entity->convertToPendingOrder();
				$entity->updateHeaderTable();

				$out = array('success' => true,'message'=>'Quote converted to order. Status: Pending!');
				echo json_encode($out);
			// } else {
			// 	$out = array('success' => false,'message'=>'Invalid quote!');
			// 	echo json_encode($out);
			// }
			die;
		}

		public function convert_to_order_thanks()
		{
			$this->renderParentLayout = false;
			$this->tplname = "quote.thanks";
		}
	}
?>