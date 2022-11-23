<?php

class ApplicationHome extends ApplicationAction
{

    public $tplname = "home";

    // page manager and HTML renderer function
    public function idx($taskType = "")
    {
        $this->title = "Activity Summary";
        $this->section = "Activity Summary";

        $this->input = $this->get_summary();
        $taskType = str_replace("/", "", $_REQUEST['url']);

        $AM_PM = 'AM';
        $TimeArr = array();
        for ($i = 0.15, $j = 0.15, $m = 0.15; $i <= 23; $i += 0.15, $j += 0.15, $m += 0.15) {
            if ($j == 0.60) {
                $i = (int) $i + 1;
                $j = 0.0;
            }
            if ($i > 12) {
                $AM_PM = 'PM';
            }

            $k = number_format((float) $i, 2, '.', '');
            $k = str_replace(".", ":", $k);
            $i = number_format((float) $i, 2, '.', '');
            $TimeArr[$i . "_" . $AM_PM] = $k . ' ' . $AM_PM;
        }

        $statusArr = array(
            "Not Started" => "Not Started",
            "In Progress" => "In Progress",
            "Completed" => "Completed",
            "Waiting on someone else" => "Waiting on someone else",
            "Deferred" => "Deferred",
            "High" => "High",
        );

        $priorityArr = array(
            "Low" => "Low",
            "Normal" => "Normal",
            "High" => "High",
        );

        $searchStatus = "";
        $optionQuery = "";
        if (count($_POST) > 0) {

            $creator_query = "";
            $assigned_query = "";

            if (is_array($_POST['reportmembers']) && sizeof($_POST['reportmembers']) > 0) {
                $creator_query = " AND creator_id IN (" . implode(",", $_POST['reportmembers']) . ") ";
                $assigned_query = " AND assigned_id  IN (" . implode(",", $_POST['reportmembers']) . ") ";
            }
            if ($_POST['reports'] == 0) {
                //Estimated Commission
                $searchStatus = " AND status NOT IN (7,9,3)";
            } elseif ($_POST['reports'] == 1) {
                //"Actual Commission"
                $searchStatus = " AND status =9 ";
            } elseif ($_POST['reports'] == 2) {
                //"Receivables"
                $searchStatus = " AND status IN (7,8) ";
            } elseif ($_POST['reports'] == 3) {
                //"Dispatched"
                $searchStatus = " AND status =6 ";
            } elseif ($_POST['reports'] == 4) {
                //"Schedule Pickup"
                $searchStatus = " AND status =8 ";
            } elseif ($_POST['reports'] == 5) {
                //"Schedule Delivery"
                $searchStatus = " AND status =9 ";
            }

            if ($_POST['report_option'] == 1) {
                $timeperiod = $_POST['timeperiod'];
                $timeperiod = $timeperiod + 1;
                $optionQuery = " AND  MONTH(created)='$timeperiod' ";
            } elseif ($_POST['report_option'] == 2) {

                $startDate = trim($_POST['report_start_date']);
                $endDate = trim($_POST['report_end_date']);
                if ($startDate != "") {
                    $startDateArr = explode("/", $startDate);
                    $startDate = $startDateArr[2] . "-" . $startDateArr[0] . "-" . $startDateArr[1];
                }
                if ($endDate != "") {
                    $endDateArr = explode("/", $endDate);
                    $endDate = $endDateArr[2] . "-" . $endDateArr[0] . "-" . $endDateArr[1];
                }

                $optionQuery = " AND (date_format(created,'%Y-%m-%d') between date_format('$startDate','%Y-%m-%d') and date_format('$endDate','%Y-%m-%d')) ";
            }

            if (!is_array($this->input)) {
                $this->input = array();
            }

            $this->input['reports'] = $_POST['reports'];
            $this->input['reportmembers'] = $_POST['reportmembers'];
            $this->input['timeperiod'] = $_POST['timeperiod'];
            $this->input['report_start_date'] = $_POST['report_start_date'];
            $this->input['report_end_date'] = $_POST['report_end_date'];

            $this->daffny->tpl->report_option = $_POST['report_option'];
        }

        $this->form->TextField("entity_id", 255, array('style' => ""), "Entity Id", "</td><td colspan=\"3\">");
        $this->form->TextField("task", 255, array('style' => ""), "Subject", "</td><td colspan=\"3\">");
        $this->form->TextField("taskdate", 10, array('style' => "12"), "Start Date", " ");
        $this->form->TextField("duedate", 10, array('style' => "width: 100px;"), "Due Date", "</td><td>");
        $this->form->ComboBox("status", $statusArr, array('style' => "width: 180px;"), "Status", "</td><td>");
        $this->form->ComboBox("priority", $priorityArr, array('style' => "width: 100px;"), "Priority", "</td><td>");
        $this->form->CheckBox("reminder", array(), "Reminder", "</td><td>");
        $this->form->TextField("reminder_date", 10, array('style' => ""), "", "</td><td>");
        $this->form->ComboBox("reminder_time", array("" => "Time") + $TimeArr, array('style' => 'width:100px;'), '', "</td><td>");

        $membersArr = $this->get_members();
        $this->daffny->tpl->company_members = $membersArr;
        $this->form->ComboBox("taskmembers", $membersArr, array("id" => "taskmembers", "multiple" => "multiple", "selected" => $_SESSION['member']['id']), "", "</td><td>");

        $this->form->TextArea("taskdata", 15, 10, array("style" => "height:100px"), "Task", "</td ><td colspan='3'>");

        $reportsArr = array(
            "Estimated Commission",
            "Actual Commission",
            "Receivables",
            "Dispatched",
            "Schedule Pickup",
            "Schedule Delivery",
        );

        $this->form->ComboBox("reports", $reportsArr, array("id" => "reports", "style" => "width:150px;"), "", "</td><td>");
        $this->form->ComboBox("reportmembers", $membersArr, array("id" => "reportmembers", "multiple" => "multiple", "style" => "width:150px;"), "", "</td><td>");
        $timeperiodArr = array(
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December",
        );
        $this->form->ComboBox("timeperiod", $timeperiodArr, array("style" => "width:150px;"), "Time Period", "</td><td>");
        $this->form->TextField("report_start_date", 10, array('style' => "width: 100px;"), "Date Range", "</td><td>");
        $this->form->TextField("report_end_date", 10, array('style' => "width: 100px;"), "", "</td><td>");

        $cp = new Member($this->daffny->DB);
        $this->input['companyname'] = htmlspecialchars($cp->getCompanyProfileById(getParentId())->companyname);
        $this->daffny->tpl->timeArr = $TimeArr;

        try {
            $this->initGlobals();
            $this->applyOrder("app_tasks");
            $this->order->setDefault("id", "desc");
            $taskManager = new TaskManager($this->daffny->DB);

            $where = "";
            if ($taskType == "completed") {
                $where = " AND completed = 1 AND deleted = 0 ";
            } elseif ($taskType == "deleted") {
                $where = " AND deleted = 1 ";
            } elseif ($taskType == "history") {
                $where = "";
            } else {
                $where = " AND completed = 0 AND deleted = 0 ";
            }

            $search_string = "";

            if (isset($_POST['search_task_widget'])) {
                $search_string = "AND (`message` LIKE '%" . $_POST['search_task_widget'] . "%' OR `taskdata` LIKE '%" . $_POST['search_task_widget'] . "%' )";
            }

            $where .= $search_string;

            $this->daffny->tpl->data = $taskManager->getConditionalTask(
                $_SESSION['member_id'],
                $this->order->getOrder(),
                $_SESSION['per_page'],
                $where
            );

            $this->pager = $taskManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);

            $permmissionCondAssign = "";
            if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
                $permmissionCondAssign = " AND `assigned_id` = '" . (int) $_SESSION['view_id'] . "' ";
            } else {
                $permmissionCondAssign = " AND `assigned_id` IN (" . implode(', ', Member::getCompanyMembers($this->daffny->DB, $_SESSION['member']['parent_id'])) . ")";
            }

            $permmissionCondCreator = "";
            if (isset($_SESSION['view_id']) && ($_SESSION['view_id'] != -1)) {
                $permmissionCondCreator = " AND `creator_id` = '" . (int) $_SESSION['view_id'] . "' ";
            } else {
                $permmissionCondCreator = " AND `creator_id` IN (" . implode(', ', Member::getCompanyMembers($this->daffny->DB, $_SESSION['member']['parent_id'])) . ")";
            }

            $sql = "SELECT *,1 as created_assigned
				FROM app_entity_commission
				WHERE id!=0  " . $creator_query . " " . $searchStatus . " " . $optionQuery . " " . $permmissionCondCreator . "
				UNION
				SELECT *,2 as created_assigned
				FROM app_entity_commission
				WHERE id!=0  " . $assigned_query . " " . $searchStatus . " " . $optionQuery . " " . $permmissionCondAssign . "
				ORDER BY id desc";
            $rows = $this->daffny->DB->selectRows($sql);

            if (count($rows) > 0) {
                $commissionData = array();
                foreach ($rows as $row) {
                    $tempArr = array();
                    $tempArr['entity_id'] = $row['entity_id'];
                    $tempArr['number'] = $row['number'];
                    $tempArr['created'] = $row['created'];
                    $tempArr['total_tariff_stored'] = $row['total_tariff_stored'];
                    $tempArr['carrier_pay_stored'] = $row['carrier_pay_stored'];
                    $tempArr['type'] = $row['type'];
                    $tempArr['status'] = $row['status'];
                    $tempArr['reffered_id'] = $row['reffered_id'];
                    $tempArr['reffered_by'] = $row['reffered_by'];
                    $tempArr['account_id'] = $row['account_id'];
                    $tempArr['company_name'] = $row['company_name'];
                    $tempArr['creator_id'] = $row['creator_id'];
                    $tempArr['creator_name'] = $row['creator_name'];
                    $tempArr['assigned_id'] = $row['assigned_id'];
                    $tempArr['assign_name'] = $row['assign_name'];
                    $tempArr['commission'] = $row['commission'];
                    $tempArr['deposit'] = $row['total_tariff_stored'] - $row['carrier_pay_stored'];
                    $tempArr['intial_percentage'] = $row['intial_percentage'];
                    $tempArr['residual_percentage'] = $row['residual_percentage'];
                    $tempArr['commission_payed'] = $row['commission_payed'];
                    $tempArr['commission_got'] = $row['commission_got'];

                    if ($row['created_assigned'] == 1) { // created
                        $tempArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $row['commission_got']) / 100;
                    } elseif ($row['created_assigned'] == 2) { // assigned
                        $tempArr['commission_got_amount'] = (($row['total_tariff_stored'] - $row['carrier_pay_stored']) * $row['commission']) / 100;
                    }

                    $tempArr['commission_type'] = $row['commission_type'];
                    $tempArr['created_assigned'] = $row['created_assigned'];

                    $commissionData[] = $tempArr;
                }

                $this->daffny->tpl->commissionData = $commissionData;
            }
        } catch (FDException $e) {
            print $e;
        }
    }

    // to get the summary
    protected function get_summary()
    {

        $taskManager = new TaskManager($this->daffny->DB);

        $entityManager = new EntityManager($this->daffny->DB);
        $quotesCount = $entityManager->getCount(Entity::TYPE_QUOTE);
        $ordersCount = $entityManager->getCount(Entity::TYPE_ORDER);
        $arr = array(
            "new_leads" => $entityManager->getNewLeadsCount()
            , "quotes_follow" => $quotesCount[0]
            , "quotes_hold" => $quotesCount[Entity::STATUS_ONHOLD]
            , "orders_qty" => $ordersCount[Entity::STATUS_ACTIVE]
            , "orders_posted" => $ordersCount[Entity::STATUS_POSTED]
            , "orders_notsigned" => $ordersCount[Entity::STATUS_NOTSIGNED]
            , "orders_dispatched" => $ordersCount[Entity::STATUS_DISPATCHED]
            , "orders_picked" => $ordersCount[Entity::STATUS_PICKEDUP]
            , "orders_issue" => $ordersCount[Entity::STATUS_ISSUES]
            , "orders_delivered" => $ordersCount[Entity::STATUS_DELIVERED],
        );

        return $arr;
    }

    // to get list of active members
    protected function get_members()
    {
        $members_arr = array();
        $q = $this->daffny->DB->select("id, contactname", "members", "WHERE `parent_id` = " . getParentId() . " AND status = 'Active'");
        while ($row = $this->daffny->DB->fetch_row($q)) {
            $members_arr[$row['id']] = $row['contactname'];
        }
        return $members_arr;
    }

}
