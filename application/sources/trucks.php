<?php

class ApplicationTrucks extends ApplicationAction {
	public static $open_spaces
		= array(
			1  => 1,
			2  => 2,
			3  => 3,
			4  => 4,
			5  => 5,
			6  => 6,
			7  => 7,
			8  => 8,
			9  => 9,
			10 => 10,
			11 => 11,
			12 => 12
		);
	public static $inops
		= array(
			1 => 'Yes',
			2 => 'No'
		);

	public function idx() {
		try {
			if ($_SESSION['is_carrier'] != "1") {
				$this->search();
				return;
			}
			$this->tplname = "trucks.main";
			$this->title = "My Trucks";
			$this->section = "Trucks";
			$this->breadcrumbs = $this->getBreadCrumbs(array(getLink("trucks") => "Trucks"));
			$this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
			$truckManager = new TruckManager($this->daffny->DB);
			$this->daffny->tpl->trucks = $truckManager->getTrucks($_SESSION['per_page'],
				"`owner_id` = " . getParentId());
			$this->pager = $truckManager->getPager();
			$tpl_arr = array(
				'navigation'    => $this->pager->getNavigation(),
				'current_page'  => $this->pager->CurrentPage,
				'pages_total'   => $this->pager->PagesTotal,
				'records_total' => $this->pager->RecordsTotal
			);
			$this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
		} catch (FDException $e) {
			redirect(getLink(''));
		}
	}

	public function edit() {
		try {
			if (isset($_GET['id']) && !ctype_digit((string)$_GET['id'])) {
				throw new UserException("Invalid Truck ID", getLink('trucks'));
			}
			$this->tplname = "trucks.edit";
			if (isset($_GET['id'])) {
				$this->title = "Edit Truck";
			} else {
				$this->title = "Add a New Truck";
			}
			$this->section = "Trucks";
			$this->breadcrumbs = $this->getBreadCrumbs(array(
				getLink('trucks') => 'Trucks',
				''                => (isset($_GET['id'])) ? 'Edit' : 'Create'
			));
			$this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
			if (isset($_POST['submit']) && $sql_arr = $this->checkForm()) {
				$truck = new Truck($this->daffny->DB);
				if (isset($_POST['id']) && ctype_digit((string)$_POST['id'])) {
					$truck->load($_POST['id']);
					$truck->update(array(
						'name'    => $sql_arr['name'],
						'trailer' => $sql_arr['trailer'],
						'inops'   => $sql_arr['inops'],
						'phone'   => $sql_arr['phone']
					));
				} else {
					$truck->create(array(
						'owner_id' => getParentId(),
						'name'     => $sql_arr['name'],
						'trailer'  => $sql_arr['trailer'],
						'inops'    => $sql_arr['inops'],
						'phone'    => $sql_arr['phone']
					));
				}
				//save documents form tmp;

				$this->setFlashInfo("Track information successfully saved.");
				redirect(getLink('trucks'));
			} else {
				foreach ($_POST as $key => $val) {
					if (!is_array($val)) {
						$this->input[$key] = htmlspecialchars($val);
					} else {
						foreach ($val as $key2 => $val2) {
							$this->input[$key][$key2] = htmlspecialchars($val2);
						}
					}
				}
				if (count($this->err)) {
					$this->input['error'] = "<div class='form-errors'>";
					foreach ($this->err as $err) {
						$this->input['error'] .= "<p>" . $err . "</p>";
					}
					$this->input['error'] .= "</div>";
				}
			}
			if (isset($_GET['id']) && (count($_POST) == 0)) {
				$truck = new Truck($this->daffny->DB);
				$truck->load($_GET['id']);
				$this->input = $truck->getAttributes();
			}
			$this->form->TextField('name', 255, array(), $this->requiredTxt . "Truck Name", "</td><td>", true, true);
			$this->form->ComboBox('trailer', array('' => 'Select One', 1 => 'Open', 2 => 'Enclosed'), array(),
				$this->requiredTxt . "Trailer Type", "</td><td>");
			$this->form->ComboBox('inops', array('' => 'Select One', 1 => 'Yes', 2 => 'No'), array(),
				$this->requiredTxt . "Inops OK", "</td><td>");
			$this->form->TextField('phone', 255, array('class' => 'phone'),
				$this->requiredTxt . "Dispatch Phone #", "</td><td>", true, true);

			$this->daffny->tpl->files = $this->getFiles((int)get_var("id"));
			$this->form->FileFiled("files_upload", array(), "Upload documents", "</td><td>");
		} catch (FDException $e) {
			redirect(getLink('trucks'));
		} catch (UserException $e) {
			$this->setFlashError($e->getMessage());
			redirect($e->getRedirectUrl());
		}
	}

	// AJAX use only!
	public function delete() {
		try {
			if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
				throw new UserException("Invalid Truck ID", getLink('trucks'));
			}
			$truck = new Truck($this->daffny->DB);
			$truck->delete($_GET['id']);
			die(json_encode(array('success' => true)));
		} catch (FDException $e) {
		} catch (UserException $e) {
		}
		die(json_encode(array('success' => false)));
	}

	public function departure() {
		try {
			if (isset($_GET['id']) && !ctype_digit((string)$_GET['id'])) {
				throw new UserException("Invalid Departure ID", getLink('trucks'));
			}
			if (isset($_GET['delete'])) {
				$departure = new Departure($this->daffny->DB);
				$departure->delete($_GET['id']);
				die(json_encode(array('success' => true)));
			}
			if (!isset($_GET['truck_id']) || !ctype_digit((string)$_GET['truck_id'])) {
				throw new UserException("Invalid Truck ID", getLink('trucks'));
			}
			$truck = new Truck($this->daffny->DB);
			$truck->load($_GET['truck_id']);
			$this->daffny->tpl->truck = $truck;
			$this->tplname = "trucks.departure";
			if (isset($_GET['id'])) {
				$this->title = "Edit Departure";
			} else {
				$this->title = "Add a New Departure";
			}
			$this->section = "Trucks";
			$this->breadcrumbs = $this->getBreadCrumbs(array(
				getLink('trucks') => 'Trucks',
				''                => (isset($_GET['id'])) ? 'Edit Departure' : 'Create Departure'
			));
			$this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
			if (isset($_POST['submit']) && $sql_arr = $this->checkDepartureForm($truck)) {
				$departure = new Departure($this->daffny->DB);
				if (isset($_POST['id']) && ctype_digit((string)$_POST['id'])) {
					$departure->load($_POST['id']);
					$departure->update(array(
						'from_city'     => $sql_arr['from_city'],
						'from_state'    => $sql_arr['from_state'],
						'from_country'  => $sql_arr['from_country'],
						'final_city'    => $sql_arr['final_city'],
						'final_state'   => $sql_arr['final_state'],
						'final_country' => $sql_arr['final_country'],
						'heading'       => $sql_arr['heading'],
						'date'          => date('Y-m-d', strtotime($sql_arr['date'])),
						'time'          => $sql_arr['time'],
						'spaces'        => $sql_arr['spaces']
					));
				} else {
					$departure->create(array(
						'truck_id'      => $sql_arr['truck_id'],
						'from_city'     => $sql_arr['from_city'],
						'from_state'    => $sql_arr['from_state'],
						'from_country'  => $sql_arr['from_country'],
						'final_city'    => $sql_arr['final_city'],
						'final_state'   => $sql_arr['final_state'],
						'final_country' => $sql_arr['final_country'],
						'heading'       => $sql_arr['heading'],
						'date'          => date('Y-m-d', strtotime($sql_arr['date'])),
						'time'          => $sql_arr['time'],
						'spaces'        => $sql_arr['spaces']
					));
				}
				$this->setFlashInfo("Deperture information successfully saved.");
				redirect(getLink('trucks'));
			} else {
				foreach ($_POST as $key => $val) {
					if (!is_array($val)) {
						$this->input[$key] = htmlspecialchars($val);
					} else {
						foreach ($val as $key2 => $val2) {
							$this->input[$key][$key2] = htmlspecialchars($val2);
						}
					}
				}
				if (count($this->err)) {
					$this->input['error'] = "<div class='form-errors'>";
					foreach ($this->err as $err) {
						$this->input['error'] .= "<p>" . $err . "</p>";
					}
					$this->input['error'] .= "</div>";
				}
			}
			if (isset($_GET['id']) && (count($_POST) == 0)) {
				$departure = new Departure($this->daffny->DB);
				$departure->load($_GET['id']);
				$this->input = $departure->getAttributes();
			}

			$this->form->TextField('from_city', 255, array('class' => 'geo-city'),
				$this->requiredTxt . "City", "</td><td>", true, true);
			$this->form->ComboBox('from_state', $this->getAllStates(), array(),
				$this->requiredTxt . "State", "</td><td>", true);
			$this->form->ComboBox('from_country', $this->getCountries(), array(),
				$this->requiredTxt . "Country", "</td><td>");

			$this->form->TextField('final_city', 255, array('class' => 'geo-city'),
				$this->requiredTxt . "City", "</td><td>", true, true);
			$this->form->ComboBox('final_state', array(
					'United States' => $this->getStates(),
					'Canada'        => $this->getCanadaStates()
				), array(),
				$this->requiredTxt
				. "State", "</td><td>", true);
			$this->form->ComboBox('final_country', $this->getCountries(), array(),
				$this->requiredTxt . "Country", "</td><td>");

			$this->form->ComboBox('heading', Departure::$directions, array(),
				$this->requiredTxt . "Heading Direction", "</td><td>");

			$this->form->TextField('date', 255, array(), $this->requiredTxt . "Date", "</td><td>", true, true);
			$this->form->ComboBox('time', array(
					'Morning'   => 'Morning',
					'Afternoon' => 'Afternoon',
					'Evening'   => 'Evening'
				), array(),
				$this->requiredTxt
				. "Time", "</td><td>");
			$this->form->ComboBox('spaces',
				array('' => 'Select One') + self::$open_spaces, array(),
				$this->requiredTxt . "# Open Spaces", "</td><td>");
		} catch (FDException $e) {
			redirect(getLink('trucks'));
		} catch (UserException $e) {
			$this->setFlashError($e->getMessage());
			redirect($e->getRedirectUrl());
		}
	}

	public function checkForm() {
		$sql_arr = $_POST;
		$checkEmpty = array(
			'name'    => 'Truck Name',
			'trailer' => 'Trailer Type',
			'inops'   => 'Inops OK',
			'phone'   => 'Dispatch Phone #'
		);
		foreach ($checkEmpty as $field => $label) {
			$this->isEmpty($field, $label);
		}
		if (count($this->err)) {
			return false;
		}
		return $sql_arr;
	}

	public function checkDepartureForm($truck) {
		if (!($truck instanceof Truck)) {
			throw new FDException("Invalid Truck instance");
		}
		$sql_arr = $_POST;
		$checkEmpty = array(
			'from_city'   => 'From City',
			'from_state'  => 'From State',
			'from_state'  => 'From Country',
			'final_city'  => 'Destination City',
			'final_state' => 'Destination State',
			'final_state' => 'Destination Country',
			'date'        => 'Date',
			'time'        => 'Time',
			'spaces'      => '# Open Spaces',
		);
		foreach ($checkEmpty as $field => $label) {
			$this->isEmpty($field, $label);
		}
		$departures = $truck->getDepartures();
		foreach ($departures as $departure) {
			if (($departure->date == date('Y-m-d', strtotime($sql_arr['date'])))
				&& (!isset($_GET['id'])
					|| ($departure->id != $_GET['id']))
			) {
				$this->err[] = "You already have departure for that date";
			}
		}
		if (count($this->err)) {
			return false;
		}
		return $sql_arr;
	}

	public function search() {
		if (!isset($_GET['order']) && isset($_SESSION['sts_post'])) {
			unset($_SESSION['sts_post']);
		}
		if ((count($_POST) == 0) && isset($_SESSION['sts_post'])) {
			$_POST = $_SESSION['sts_post'];
		}
		$this->daffny->tpl->results = array();
		$this->tplname = "trucks.search";
		$this->title = "Search Truck Space";
		$this->section = "Trucks";
		$this->breadcrumbs = $this->getBreadCrumbs(array(getLink("trucks") => "Trucks", '' => 'Search Truck Space'));
		$this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
		$this->order = new OrderRewrite($this->daffny->DB);
		$this->order->UrlStart = getLink();
		$this->order->Fields = TruckManager::getSearchFeilds();
		$this->order->setDefault('matcher', 'desc');
		$this->order->init();
		$this->daffny->tpl->order = $this->order;
		if (isset($_POST['submit'])) {
			$_SESSION['sts_post'] = $_POST;
			$truckManager = new TruckManager($this->daffny->DB);
			$params = $_POST;
			$req_params = array(
				'carrier_name',
				'carrier_id',
				'origin_city',
				'origin_state',
				'origin_country',
				'destination_city',
				'destination_state',
				'destination_country',
				'date',
				'heading',
				'spaces',
				'inops'
			);
			foreach ($params as $key => $value) {
				$params[$key] = trim($value);
				if ($params[$key] == "") {
					unset($params[$key]);
				}
				if (!in_array($key, $req_params)) {
					unset($params[$key]);
				}
			}
			$this->daffny->tpl->results = $truckManager->search($params, $this->order->getOrder());
			$this->input += $_POST;
		}
		$this->form->TextField('carrier_name', 255, array(), 'Carrier Name', '</td><td>', true, true);
		$this->form->TextField('carrier_id', 11, array('class' => 'digit-only'), 'Carrier ID', '</td><td>', true, true);
		$this->form->TextField('origin_city', 255, array(), 'City', '</td><td>', true, true);
		$this->form->ComboBox('origin_state',
			array('' => 'Select One') + $this->getAllStates(), array(), 'State', '</td><td>');
		$this->form->ComboBox('origin_country', $this->getCountries(), array(), 'Country', '</td><td>');
		$this->form->TextField('destination_city', 255, array(), 'City', '</td><td>', true, true);
		$this->form->ComboBox('destination_state',
			array('' => 'Select One') + $this->getAllStates(), array(), 'State', '</td><td>');
		$this->form->ComboBox('destination_country', $this->getCountries(), array(), 'Country', '</td><td>');
		$this->form->ComboBox('heading', Departure::$directions, array(), "Heading Direction", "</td><td>");
		$this->form->TextField('date', 255, array(), 'Departure Date (+/- 7 days)', '</td><td>', true, true);
		$this->form->ComboBox('spaces',
			array('' => 'Select One') + self::$open_spaces, array(), 'Open Spaces', '</td><td>');
		$this->form->ComboBox('inops', array('' => 'Select One') + self::$inops, array(), 'Inops', '</td><td>');
	}

	/* Upload documents */

	public function upload_file() {
		$track_id = (int)post_var('track_id');
		$upload = new upload();
		$upload->out_file_dir = UPLOADS_PATH . "trucks/";
		$upload->max_file_size = 10000000;
		$upload->form_field = "file";
		$upload->make_script_safe = 1;
		$upload->allowed_file_ext = array("pdf", "doc", "docx");
		$upload->save_as_file_name = md5(time() . "-" . rand()) . time();
		$upload->upload_process();
		switch ($upload->error_no) {
			case 0:
			{
				$sql_arr = array(
					'name_original'  => $_FILES[$upload->form_field]['name'],
					'name_on_server' => $upload->save_as_file_name,
					'size'           => $_FILES[$upload->form_field]['size'],
					'type'           => $upload->file_extension,
					'date_uploaded'  => "now()",
					'track_id'       => $track_id,
					'member_id'      => $_SESSION['member_id'],
					'owner_id'       => getParentId()
				);

				if ($track_id == 0) {
					$sql_arr['is_tmp'] = 1;
				}

				$ins_arr = $this->daffny->DB->PrepareSql("app_truck_documents", $sql_arr);
				$this->daffny->DB->insert("app_truck_documents", $ins_arr);
				$insid = $this->daffny->DB->get_insert_id();
				$out = getFileImageByType($upload->file_extension) . " ";
				$out .= $_FILES[$upload->form_field]['name'];
				$out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") ";
				$out
					.= "<a href=\"#\" onclick=\"return deleteFile('" . getLink("trucks", "delete-file") . "','" . $insid
					. "');\"><img src=\""
					. SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
				die("<li id=\"file-" . $insid . "\">" . $out . "</li>");
			}

			case 1:
				die("ERROR:File not selected or empty.");
			case 2:
			case 5:
				die("ERROR:Invalid File Extension");
			case 3:
				die("ERROR:File too big");
			case 4:
				die("ERROR:Cannot move uploaded file");
		}
		exit;
	}

	public function delete_file() {
		$out = array('success' => false);
		$id = (int)get_var('id');
		try {
			if ($row = $this->daffny->DB->selectRow('*', "app_truck_documents",
				"WHERE id = '$id' AND owner_id = '" . getParentId() . "'")
			) {
				if ($this->daffny->DB->isError) {
					throw new Exception($this->getDBErrorMessage());
				} else {
					$file_path = UPLOADS_PATH . "trucks/" . $row["name_on_server"];
					$this->daffny->DB->delete('app_truck_documents', "id = '" . quote($id) . "'");
					$out = array('success' => true);
					@unlink($file_path);
				}
			}
		} catch (FDException $e) {
		}
		die(json_encode($out));
	}

	protected function getFiles($id) {
		try {
			$sql
				= "SELECT *
		                  FROM app_truck_documents
		                 WHERE (track_id = '" . $id . "' OR (is_tmp = 1 AND member_id='" . $_SESSION['member_id'] . "'))
		                 	AND owner_id = '" . getParentId() . "'
		                 ORDER BY date_uploaded";
			$FilesList = $this->daffny->DB->selectRows($sql);
			$files = array();
			foreach ($FilesList as $i => $file) {
				$files[$i] = $file;
				$files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
				$files[$i]['size_formated'] = size_format($file['size']);
			}
			return $files;
		} catch (FDException $e) {
			redirect(getLink('trucks'));
		}
	}

	public function import() {
		$this->tplname = "trucks.import";
		$this->title = "Import Trucks";
		$this->breadcrumbs = $this->getBreadCrumbs(array(getLink("trucks") => "Trucks", '' => 'Import'));
		if (count($_FILES)) {
			$upload = new upload();
			$upload->out_file_dir = UPLOADS_PATH . "entity/";
			$upload->max_file_size = 50 * 1024 * 1024;
			$upload->form_field = "import";
			$upload->make_script_safe = 1;
			$upload->allowed_file_ext = array("xls", "xlsx", "csv");
			$upload->save_as_file_name = md5(time().mt_rand()).'_import';
			$upload->upload_process();
			try {
				switch ($upload->error_no) {
					case 0:
						$import = new Import();
						$result = $import->importTrucks($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
						$this->input['success'] = $result['success'];
						$this->input['failed'] = $result['failed'];
						break;
					case 1:
						throw new RuntimeException('File not selected or empty.');
					case 2:
					case 5:
						throw new RuntimeException('Invalid File Extension');
					case 3:
						throw new RuntimeException('File too big');
					case 4:
						throw new RuntimeException('Cannot move uploaded file');
					default:

				}
			} catch (RuntimeException $e) {
				if (file_exists($upload->saved_upload_name)) {
					unlink($upload->saved_upload_name);
				}
				die('ERROR: '.$e->getMessage());
			}
		}
	}
}
