<?php

class Applicationautoquoting extends ApplicationAction {

    public $title = "Automated Quoting";
    public $section = "Automated Quoting";
    public $tplname = "settings.autoquoting.list";

	public function construct() {


        
		if (!$this->check_access('preferences')) {
			$this->setFlashError('Access Denied.');
			redirect(getLink());
		}

        //Check if user have Automate Quoting Addon...
        $license = new License($this->daffny->DB);
        $license->loadCurrentLicenseByMemberId(getParentId());
        if ($license->addon_aq_id > 0 ) {

        } else {
            $this->setFlashError('Access Denied. Please purchase Automate Quoting Addon under Profile->Billing');
            redirect(getLink("companyprofile"));
        }

		return parent::construct();
	}

    /**
     * List all
     *
     */
    public function idx() {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", '' => "Automated Quoting"));
        $this->tplname = "settings.autoquoting.list";
        $aqsm = new AutoQuotingSeasonManager($this->daffny->DB);
        $this->applyOrder(AutoQuotingSeason::TABLE);
        $seasons = $aqsm->getSeasons($this->order->getOrder(), $_SESSION['per_page'], getParentId());
        $this->setPager($aqsm->getPager());
        $this->daffny->tpl->data = $seasons;
    }

    /**
     * Edit
     *
     */
    public function editseason() {
        try {
            $ID = (int) get_var("id");
            $this->tplname = "settings.autoquoting.form";
            $this->title .= ($ID > 0 ? " - Edit Season" : " - Add New Season");
            $season = new AutoQuotingSeason($this->daffny->DB);
            if ($ID > 0) {
                $season->load($ID);
                $season->checkAccess(getParentId());
            }
            $sql_arr = array(
                'name' => post_var("name")
                , 'start_date' => post_var("start_date")
                , 'end_date' => post_var("end_date")
                , 'status' => post_var("status")
            );
            $this->input = $sql_arr;
            if (isset($_POST['submit'])) {
                $this->isEmpty("name", "Name");
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $this->isEmpty("status", "Status");

                $sql_arr['start_date'] = $this->validateDate(post_var("start_date"), "Start Date");
                $sql_arr['end_date'] = $this->validateDate(post_var("end_date"), "End Date");

                if (!count($this->err)) {
                    if ($ID > 0) {
                        $season->update($sql_arr);
                        $this->setFlashInfo("Season has been updated.");
                    } else {
                        $sql_arr['owner_id'] = getParentId();
                        $season->create($sql_arr);
                        $this->setFlashInfo("Season has been added.");
                        $ID = $season->id;
                    }
                    redirect(getLink("autoquoting"));
                }
            } else {
                if ($ID > 0) {
                    $this->input = array(
                        'name' => $season->name,
                        'start_date' => $season->getStartDate(),
                        'end_date' => $season->getEndDate(),
                        'status' => $season->status
                    );
                }
            }
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("autoquoting") => "Automated Quoting", '' => ($ID > 0 ? $this->input['name'] : "Add New Season")));
            foreach ($this->input as $key => $value) {
                $this->input[$key] = htmlspecialchars($value);
            }
            $this->form->TextField("name", 255, array(), $this->requiredTxt . "Name", "</td><td>");
            $this->form->TextField("start_date", 10, array('style' => ""), $this->requiredTxt . "Start Date", "</td><td>");
            $this->form->TextField("end_date", 10, array('style' => ""), $this->requiredTxt . "End Date", "</td><td>");
            $this->form->ComboBox("status", array("Active" => "Active", "Inactive" => "Inactive"), array('style' => ""), $this->requiredTxt . "Status", "</td><td>");
        } catch (FDException $e) {
            redirect(getLink('autoquoting'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function copyseason() {
        try {
            $ID = $this->checkId();
            $season = new AutoQuotingSeason($this->daffny->DB);
            $season->load($ID);
            $season->checkAccess(getParentId());
            $season->duplicate();
            redirect(getLink('autoquoting'));
        } catch (FDException $e) {
            redirect(getLink('autoquoting'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    /*
     * Delete auto-quoting season
     * AJAX use only!
     */

    public function deleteseason() {
        try {
            $ID = $this->checkId();
            $season = new AutoQuotingSeason($this->daffny->DB);
            $season->load($ID);
            if ($season->owner_id != getParentId())
                throw new FDException("Access Denied");
            $season->delete(null, true);
            $out = array('success' => true);
        } catch (FdException $e) {
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

    /*
     * Delete auto-quoting season lane
     * AJAX use only!
     */

    public function deletelane() {
        try {
            $ID = $this->checkId();
            $lane = new AutoQuotingLane($this->daffny->DB);
            $lane->load($ID);
            $lane->getSeason()->checkAccess(getParentId());
            $lane->delete(null, true);
            $out = array('success' => true);
        } catch (FdException $e) {
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

    /*
     * Switch auto-quoting season status
     * AJAX use only!
     */

    public function statusseason() {
        try {
            $ID = $this->checkId();
            $season = new AutoQuotingSeason($this->daffny->DB);
            $season->load($ID);
            $season->checkAccess(getParentId());
            $status = ($season->status == 'Active') ? 'Inactive' : 'Active';
            $season->update(array('status' => $status));
            $out = array('success' => true);
        } catch (FDException $e) {
            $out = array('success' => false);
        }
        die(json_encode($out));
    }

    /* Lanes */

    public function lanes() {
        try {
            if (!isset($_GET['sid']) || !ctype_digit((string) $_GET['sid']))
                throw new UserException("Invalid Season ID", getLink('autoquoting'));
            $season = new AutoQuotingSeason($this->daffny->DB);
            $season->load($_GET['sid']);
            $season->checkAccess(getParentId());
            $this->input['season_name'] = htmlspecialchars($season->name);
            $this->input['season_start_date'] = $season->getStartDate();
            $this->input['season_end_date'] = $season->getEndDate();
            $lm = new AutoQuotingLaneManager($this->daffny->DB);
            $this->applyOrder(AutoQuotingSeason::TABLE);
            $this->daffny->tpl->data = $lm->getLanes($this->order->getOrder(), $_SESSION['per_page'], "`season_id` = {$season->id}");
            $this->setPager($lm->getPager());
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("autoquoting") => "Automated Quoting", getLink("autoquoting", "edit", "id", $_GET['sid']) => htmlspecialchars($season->name), '' => "Lanes"));
            $this->tplname = "settings.autoquoting.lanes";
        } catch (FDException $e) {
            redirect(getLink('autoquoting'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function editlane() {

        try {
            $ID = (int) get_var("id");
            if (!isset($_GET['sid']) || !ctype_digit((string) $_GET['sid']))
                throw new UserException("Invalid Season ID", getLink('autoquoting'));
            $season = new AutoQuotingSeason($this->daffny->DB);
            $season->load($_GET['sid']);
            $season->checkAccess(getParentId());
            $lane = new AutoQuotingLane($this->daffny->DB);

            if ($ID > 0) {
                $lane->load($ID);
                $this->input = $lane->getAttributes();
                $this->input["origin_new"] = $this->input["origin"];
                $this->input["destination_new"] = $this->input["destination"];
            } else {
                $this->input = $_POST;

            }

            $this->tplname = "settings.autoquoting.laneform";
            $this->title .= ($ID > 0 ? " - Edit Lane" : " - Add New Lane");
            foreach ($this->input as $key => $value) {
                if (!is_array($value)){
                    $this->input[$key] = htmlspecialchars($value);
                }
            }

            if (isset($_POST['submit'])) {



                $this->isEmpty('name', "Name");
                if (count($this->err) == 0) {
                    try {
                        $this->daffny->DB->transaction("start");
                        $sql_arr = $_POST;

                        unset($sql_arr['v_surcharge']);
                        unset($sql_arr['origin']);
                        unset($sql_arr['o_surcharge']);
                        unset($sql_arr['destination']);
                        unset($sql_arr['d_surcharge']);
                        unset($sql_arr['submit']);
                        unset($sql_arr['addPrice']);
                        unset($sql_arr['additional_origins']);
                        unset($sql_arr['multiselect_additional_origins']);
	                    unset($sql_arr['additional_destinations']);
	                    unset($sql_arr['multiselect_additional_destinations']);
	                    unset($sql_arr['additional_seasons']);
	                    unset($sql_arr['additional_season_switcher']);

                        $sql_arr['origin'] = $sql_arr['origin_new'];
                        $sql_arr['destination'] = $sql_arr['destination_new'];

                        unset($sql_arr['origin_new']);
                        unset($sql_arr['destination_new']);

                        $sql_arr['modified'] = date("Y-m-d H:i:s");

	                    if (!isset($sql_arr['round_total_to'])){
		                    $sql_arr['round_total_to'] = 0;
	                    }

                        if ($ID > 0) {
                            $lane->update($sql_arr);
                        } else {
                            $sql_arr['season_id'] = (int)$_GET['sid'];
                            $lane->create($sql_arr);
	                        if (isset($_POST['addPrice']) && is_array($_POST['addPrice'])) {
		                        foreach ($_POST['addPrice'] as $season_id => $addPrice) {
			                        $sql_arr['season_id'] = (int)$season_id;
			                        foreach ($addPrice as $laneCode => $price) {
				                        if ($sql_arr['price_type'] == 'base') {
					                        $sql_arr['base_price'] = number_format((float)$price, 2, '.', '');
				                        } else {
					                        $sql_arr['cpm_price'] = number_format((float)$price, 2, '.', '');
				                        }
				                        $laneCodeParams = explode('_', $laneCode);
				                        $sql_arr['origin'] = $laneCodeParams[0];
				                        $sql_arr['destination'] = $laneCodeParams[1];
				                        $sql_arr['name'] = str_replace('_', ' - ', $laneCode);
				                        $addLane = new AutoQuotingLane($this->daffny->DB);
				                        $addLane->create($sql_arr);
				                        $addLane->updateVehicleSurcharges($_POST['v_surcharge']);
				                        $addLane->updateOriginSurcharges((isset($_POST['o_surcharge'])) ? $_POST['o_surcharge'] : array(), (isset($_POST['origin'])) ? $_POST['origin'] : array());
				                        $addLane->updateDestinationSurcharges((isset($_POST['d_surcharge'])) ? $_POST['d_surcharge'] : array(), (isset($_POST['destination'])) ? $_POST['destination'] : array());
			                        }
		                        }
	                        }
                        }
                        
                        $lane->updateVehicleSurcharges($_POST['v_surcharge']);
                        $lane->updateOriginSurcharges((isset($_POST['o_surcharge'])) ? $_POST['o_surcharge'] : array(), (isset($_POST['origin'])) ? $_POST['origin'] : array());
                        $lane->updateDestinationSurcharges((isset($_POST['d_surcharge'])) ? $_POST['d_surcharge'] : array(), (isset($_POST['destination'])) ? $_POST['destination'] : array());
                        
                        $this->daffny->DB->transaction("commit");
                        $this->setFlashInfo("Lane has been saved");
                        redirect(getLink("autoquoting", "editlane","id", $lane->id, "sid", $lane->season_id));
                    } catch (FDException $e) {
                        $this->daffny->DB->transaction("rollback");
                        throw $e;
                    }
                }
            } elseif ($ID > 0) {
                $this->lane = $lane;
            }

	        if (!isset($this->input['price_type'])) {
		        $this->input['price_type'] = 'base';
	        }

            $this->tplname = "settings.autoquoting.laneform";
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("autoquoting") => "Automated Quoting", getLink("autoquoting", "lanes", "sid", $_GET['sid']) => htmlspecialchars($season->name), '' => ($ID > 0 ? htmlspecialchars($lane->name) : "Add New Lane")));
            $this->form->TextField("name", 255, array(), $this->requiredTxt . "Name", "</td><td>");
            $this->form->ComboBox("status", array("Active" => "Active", "Inactive" => "Inactive"), array('style' => "width: 100px;"), "Status", "</td><td>");


            $this->form->ComboBox("origin_new", $this->getAllStates(), array('style' => 'width: 100px;'), 'Origin', '</td><td>');
            $this->form->ComboBox("destination_new", $this->getAllStates(), array('style' => 'width: 100px;'), 'Destination', '</td><td>');

	        $this->form->ComboBox("additional_origins", $this->getAllStates(), array('style' => 'width: 100px;', 'multiple' => 'multiple'), 'Origin', '</td><td>');
	        $this->form->ComboBox("additional_destinations", $this->getAllStates(), array('style' => 'width: 100px;', 'multiple' => 'multiple'), 'Destination', '</td><td>');
	        $this->form->CheckBox("additional_season_switcher");

	        $i = 0;
	        foreach ($this->getSeasons($season->id) as $season_id => $season_name) {
		        $i++;
		        $this->form->CheckBox('additional_seasons[]', array('value' => $season_id, 'id' => 'additional_seasons_'.$i), $season_name, '</td><td>', true, 'additional_seasons_'.$i);
		        $this->form->MoneyField('additional_seasons_price['.$season_id.']', 10, array('id' => 'additional_seasons_price_'.$i), '', '', true, false, 'additional_seasons_price_'.$i);
	        }
	        $this->daffny->tpl->seasons_count = $i;
	        $price_type = $this->form->Radio('price_type', array('id' => 'price_type_base', 'value' => 'base'), 'Base Price', '', false);
	        $price_type .= '</td><td>'.$this->form->Radio('price_type', array('id' => 'price_type_cpm', 'value' => 'cpm'), 'CPM Price', '', false);
	        $this->input['price_type'] = $price_type;
	        $this->form->MoneyField("cpm_price", 15, array(), "CPM Price $", "</td><td>");
            $this->form->MoneyField("base_price", 15, array(), "Base Price $", "</td><td>");
            $this->form->MoneyField("inop_surcharge", 15, array(), "Inop. Surcharge $", "</td><td>");
            $this->form->MoneyField("encl_surcharge", 15, array(), "Encl. Surcharge $", "</td><td>");
            $this->form->TextField("origin_radius", 2, array("style" => "width:75px; text-align:right;"), "Origin Radius", "</td><td>");
            $this->form->TextField("destination_radius", 2, array("style" => "width:75px; text-align:right;"), "Destination Radius", "</td><td>");
            $this->form->MoneyField("calculate_price", 15, array(), "Calculate Price", "</td><td>");
            $roundTotal = (($ID > 0) && ($lane->round_total_to == 1)) ? array("checked" => "checked") : array();
            $this->form->CheckBox("round_total_to", $roundTotal, "Round Total to $5", "</td><td>");

            $vehiclesData = array();
            if ($ID > 0) {
                $vehiclesData = $lane->getVehiclesArray();
            }

            $vtd = $this->daffny->DB->selectRows("*", "app_vehicles_types", "");
            foreach ($vtd as $vtr) {
                if (isset($vehiclesData[$vtr['id']])) {
                    $this->form->MoneyField("v_surcharge[" . $vtr['id'] . "]", 15, array('value' => $vehiclesData[$vtr['id']]), $vtr['name'], "</td><td>$ ");
                } else {
                    $this->form->MoneyField("v_surcharge[" . $vtr['id'] . "]", 15, array('value' => '0.00'), $vtr['name'], "</td><td>$ ");
                }
            }

            $this->daffny->tpl->origin = array();
            $this->daffny->tpl->destination = array();
            $states_o = array();
            $states_d = array();
            if ($ID > 0) {
                foreach ($lane->getCitiesArray() as $city) {
                    $city['checked'] = ($city['is_active'] == 1) ? ' checked="checked"' : '';
                    if ($city['type'] == 0) {
                        $this->daffny->tpl->origin[] = $city;
                        if (!in_array($city['state'], $states_o))
                            array_push($states_o, $city['state']);
                    } else {
                        $this->daffny->tpl->destination[] = $city;
                        if (!in_array($city['state'], $states_d))
                            array_push($states_d, $city['state']);
                    }
                }
            }
            $this->form->ComboBox("o_state", $this->getStates(), array(), "State", "</td><td>");
            $this->form->ComboBox("d_state", $this->getStates(), array(), "State", "</td><td>");

            $this->daffny->tpl->states_o = "var states_o = [];";
            $this->daffny->tpl->states_d = "var states_d = [];";
            if (count($states_o) > 0) {
                $this->daffny->tpl->states_o = "var states_o = ['" . implode("','", $states_o) . "'];";
            }
            if (count($states_d) > 0) {
                $this->daffny->tpl->states_d = "var states_d = ['" . implode("','", $states_d) . "'];";
            }
        } catch (FDException $e) {
            $e->getTraceAsString();
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function settings() {
        $this->tplname = "settings.autoquoting.settings";
        $this->title = "Automated Quoting Settings";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("autoquoting") => "Automated Quoting", '' => "Settings"));
        $settings = new AutoQuotingSettings($this->daffny->DB);
        $settings->loadByOwnerId(getParentId());
        if (isset($_POST['submit'])) {
            $sql_arr = array(
                "is_enabled" => (post_var("is_enabled") == "1" ? 1 : 0)
                , "email_type" => (int) post_var("email_type")
                , "surcharge_type" => (int) post_var("surcharge_type")
                , "is_autoquote_unknown" => (post_var("is_autoquote_unknown") == "1" ? 1 : 0)
            );

            if (!count($this->err)) {
                $settings->update($sql_arr);
                $this->input = $settings->getAttributes();
                $this->setFlashInfo("Settings successfully saved.");
            } else {
                $this->input = $sql_arr;
            }
        } else {
            $this->input = $settings->getAttributes();
        }

		$data = $this->daffny->DB->selectRow("COUNT(*) as `cnt`", "`app_autoquoting_quotes`", "WHERE `owner_id` = ".$_SESSION['member']['parent_id']." AND `date` = CURDATE()");
		$this->input['today'] = $data['cnt'];
		$data = $this->daffny->DB->selectRow("COUNT(*) as `cnt`", "`app_autoquoting_quotes`", "WHERE `owner_id` = ".$_SESSION['member']['parent_id']." AND MONTH(`date`) = MONTH(NOW()) ");
		$this->input['this_month'] = $data['cnt'];
		$data = $this->daffny->DB->selectRow("COUNT(*) as `cnt`", "`app_autoquoting_quotes`", "WHERE `owner_id` = ".$_SESSION['member']['parent_id']." AND MONTH(`date`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))");
		$this->input['last_month'] = $data['cnt'];

        $this->form->CheckBox("is_enabled", array(), "<strong>Enable automated quoting</strong>", "&nbsp;");
        $this->form->CheckBox("is_autoquote_unknown", array(), "<strong>Auto-quote unknown vehicle type</strong>", "&nbsp;");

        $this->form->helperEmailType("email_type");
        $this->form->helperSurchargeType("surcharge_type");
    }

    public function import() {
        $this->tplname = "settings.autoquoting.import";
        $this->title = "Import Lanes";
        $season = new AutoQuotingSeason($this->daffny->DB);
        if (isset($_GET['sid'])) {
            $SID = (int) get_var("sid");
            $season->load($SID);
            $season->checkAccess(getParentId());
        } else
            throw new UserException("Access Denied", getLink('autoquoting'));
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("companyprofile") => "Settings", getLink("autoquoting") => "Automated Quoting", getLink("autoquoting", "lanes", "sid", $SID) => htmlspecialchars($season->name), '' => "Import Lanes"));

        if (isset($_FILES['csv'])) {
            $i = 0;
            $uploaddir = UPLOADS_PATH . "lanes/";
            if (!file_exists($uploaddir)) {
                mkdir($uploaddir, 0777);
                $index_file = $uploaddir . "index.html";
                $handle = fopen($index_file, "w");
                fclose($handle);
                @chmod($index_file, 0777);
            }

            $file_name = basename($_FILES['csv']['name']);
            $ext = strtolower(substr($file_name, strrpos($file_name, ".") + 1));
            $id = rand(10000000, 99999999);
            $uploadfile = $uploaddir . $id . ".csv";
            switch ($ext) {
                case "csv":
                    if (move_uploaded_file($_FILES['csv']['tmp_name'], $uploadfile)) {
                        @chmod($uploadfile, 0777);
                        $lines = file($uploadfile, FILE_SKIP_EMPTY_LINES);
                        if (!empty($lines)) {
                            $this->daffny->DB->transaction("start");
                            try {
                                foreach ($lines as $line) {
                                    $rec = explode(";", $line);
	                                $lane = new AutoQuotingLane($this->daffny->DB);
                                    if ($rec[0] == 1) {
                                        $i++;
                                        $ins_arr = array(
                                            'season_id' => $SID,
                                            'name' => $rec[2],
                                            'base_price' => number_format($rec[3], 2, ".", ""),
                                            'inop_surcharge' => number_format($rec[4], 2, ".", ""),
                                            'encl_surcharge' => number_format($rec[5], 2, ".", ""),
                                            'origin_radios' => (int) $rec[6],
                                            'destination_radois' => (int) $rec[7],
                                            'calculate_price' => (int) $rec[8],
                                            'round_total_to' => (int) $rec[9],
                                            'modified' => date("Y-m-d H:i:s")
                                        );
                                        $lane->create($ins_arr);
                                        $ins_arr = array(
                                            1 => $rec[10], 2 => $rec[11], 3 => $rec[12],
                                            4 => $rec[13], 5 => $rec[14], 6 => $rec[15],
                                            7 => $rec[16], 8 => $rec[17], 9 => $rec[18],
                                            10 => $rec[19], 11 => $rec[20], 12 => $rec[21],
                                            13 => $rec[22], 14 => $rec[23], 15 => $rec[24],
                                            16 => $rec[25], 17 => $rec[26], 18 => $rec[27],
                                            19 => $rec[28], 20 => $rec[29], 21 => $rec[30],
                                            0 => $rec[31], -1 => $rec[32]
                                        );
                                        $lane->updateVehicleSurcharges($ins_arr);
                                    }

                                    if ($rec[0] == 2 || $rec[0] == 3) {
                                        $city_id = (int) $this->getCityId(trim($rec[2]), trim($rec[3]));
                                        if ($city_id > 0) {
                                            if ($rec[0] == 2) {
                                                $lane->updateOriginSurcharges(array((int) $city_id => number_format((float) $rec[4], 2, ".", "")), array((int) $city_id => 1));
                                            } else {
                                                $lane->updateDestinationSurcharges(array((int) $city_id => number_format((float) $rec[4], 2, ".", "")), array((int) $city_id => 1));
                                            }
                                        }
                                    }
                                }
                                $this->daffny->DB->transaction("commit");
                            } catch (FDException $e) {
                                $this->daffny->DB->transaction("rollback");
                                throw new UserException("Bad file format", getLInk('autoquoting', 'import', 'sid', $SID));
                            }
                        }
                        @unlink($uploadfile);
                        $this->setFlashInfo($i . " Lanes has been uploaded");
                        redirect(getLink("autoquoting", "lanes", "sid", $SID));
                    }
                    break;
                default:
                    $this->setFlashError("Bad extention. Only (*.csv)");
                    redirect(getLink("auto"."quoting", "import", "sid", $SID));
            }
        }

        $this->form->FileFiled("csv", array(), "Import Lanes (*.csv)", "</td><td>");
    }

    private function getCityId($city, $state) {
        if ($city != "" && $state != "") {
            $rc = $this->daffny->DB->select_one("id", "cities", "WHERE city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $city) . "' AND state='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $state) . "'");
            if (isset($rc['id']) && $rc['id'] > 0) {
                return $rc['id'];
            }
        }
        return 0;
    }

	private function getSeasons($except_id = null) {
		$where = 'WHERE `owner_id` = '.(int)getParentId();
		if (!is_null($except_id) && is_numeric($except_id)) {
			$where .= ' AND `id` != '.(int)$except_id;
		}
		$seasonsData = $this->daffny->DB->selectRows('id, name', AutoQuotingSeason::TABLE, $where);
		$seasons = array();
		foreach ($seasonsData as $row) {
			$seasons[$row['id']] = $row['name'];
		}
		return $seasons;
	}

}
