<?php

require_once ROOT_PATH . "libs/excel/PHPExcel.php";
require_once ROOT_PATH . "libs/excel/PHPExcel/Writer/Excel5.php";

class ApplicationReports extends ApplicationAction
{

    public $title = "Reports";
    public $section = "Reports";
    public $tplname = "reports.show";
    private $time_periods = array(
        "1" => "Current Month"
        , "2" => "Last Month"
        , "3" => "Last Quarter"
        , "4" => "Current Year"
        , "5" => "All Time",
    );

    //Styles for build EXCEL reports
    private $lineFont = array(
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            ),
        ),
    );

    private $titleFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '16',
            'bold' => true,
            'color' => array('rgb' => '3E8AB0'),
        ),
    );

    private $headFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '10',
            'italic' => true,
            'color' => array('rgb' => 'ffffff'),
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => '2996cc'),
        ),
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            ),
        ),
    );
    private $totalFont = array(
        'font' => array(
            'name' => 'Arial Cyr',
            'size' => '10',
            'bold' => true,
        ),
        'borders' => array(
            'outline' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => '000000'),
            ),
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'e0e0e0'),
        ),
    );
    private $smallFont = array(
        'font' => array('size' => 9),
    );

    public function construct()
    {
        if (!$this->check_access('reports')) {
            $this->setFlashError('Access Denied.');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array('' => "Reports"));
        /* Dashboard */
        $this->form->ComboBox("users_ids", $this->getUsers(), array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"2\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->input["define_as"] = 0; /* 1st radio by Default */
        $this->form->helperDefineAs("define_as");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
    }

    /**
     * Build jPlot Graph via Ajax
     * Slide 34: Dashboard should graphically interpret Sales report below
     * by default for Current Month, and with ability to change time frames.
     * X-axis: Time interval (if the year is displayed on a monthly basis)
     * Y-axis: The number of leads, quotes and orders (all three separate columns (bold and different colors), indicating the number above.
     * Specify the Conversion Rate under the graph (Order #: Quote #).
     *
     */
    public function graph()
    {

        $out = array("success" => false, "error" => "Undefined error");
        try {
            $arr = array(
                "users_ids" => post_var("users_ids")
                , "time_period" => post_var("time_period")
                , "start_date" => post_var("start_date")
                , "end_date" => post_var("end_date")
                , "define_as" => (int) post_var("define_as") == 1 ? true : false,
            );

            if (post_var("ptype") == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $start_date = $tp[0];
                $end_date = $tp[1];
            } elseif (post_var("ptype") == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $start_date = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $end_date = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            } else { // Set By Default Current Month
                $start_date = date("Y-m-d 00:00:00");
                $end_date = date("Y-m-d 23:59:59");
            }

            if (!count($this->err)) { // Build Chart
                $entityManager = new EntityManager($this->daffny->DB);
                $sales_data = $entityManager->getDashBoardSales(getParentId(), $start_date, $end_date, $arr["users_ids"], $arr["define_as"]);

                $out = array(
                    "success" => true
                    , "error" => ""
                    , "leads" => $sales_data["leads"]
                    , "quotes" => $sales_data["quotes"]
                    , "orders" => $sales_data["orders"]
                    , "ticks" => $sales_data["ticks"]
                    , "range" => $sales_data["range"],
                );
            } else { // Show Error
                $out = array("success" => false, "error" => implode("<br />", $this->err));
            }
        } catch (FDException $e) {
            $out = array("success" => false, "error" => "Access Denied");
        }
        echo json_encode($out);
        exit;
    }

    /**
     * Slide 34
     * Sales Report
     * (User, All Leads, All Quotes, All Orders,
     * Conversion Rate (Oder # : Quote #),
     * Dispatched Orders (quantity), Tariffs, Carrier Pay,
     * Terminal Fee (Pickup on top, Delivery on bottom),
     * Gross Profit (Tariff – Carrier Pay – Terminal Fee),
     * Profit Margin % (Gross Profit : Tariffs),
     * Average Profit per Order (Gross Profit : Dispatched Orders).
     *
     */
    public function sales()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Sales report"));
        $this->tplname = "reports.sales.show";

        $start_date = "";
        $end_date = "";
        $users_ids = array();

        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $arr = array();
        if (isset($_POST['submit']) || $is_export) {
            $arr = array(
                "time_period" => post_var("time_period"),
                "start_date" => post_var("start_date"),
                "end_date" => post_var("end_date"),
                "define_as" => (int) post_var("define_as") == 1 ? true : false,
            );

            $this->input = $arr;

            $users_ids = post_var("users_ids");

            if (post_var("ptype") == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $start_date = $tp[0];
                $end_date = $tp[1];
            }

            if (post_var("ptype") == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $start_date = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $end_date = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }

        // Generate report
        $sales = array();
        $totals = array();
        if (!count($this->err) && $start_date != "" && $end_date != "") {
            $this->daffny->tpl->sales = array();

            if (!is_array($users_ids)) {
                $users_ids = array();
            }

            //Get Users
            $where = " id IN ('" . implode("','", $users_ids) . "')";

            $user = new Member();
            $users = $user->getCompanyMembers($this->daffny->DB, getParentId(), $where, true);

            $totals = array(
                "leads" => 0,
                "quotes" => 0,
                "orders" => 0,
                "conv_rate" => 0,
                "dispatched" => 0,
                "tariffs" => 0,
                "carrier_pay" => 0,
                "terminal_feesP" => 0,
                "terminal_feesD" => 0,
                "gross_profit" => 0,
                "profit_margin" => 0,
                "average_profit" => 0,
            );

            $entityManager = new EntityManager($this->daffny->DB);
            // For each users get sales
            foreach ($users as $key => $value) {
                $lqo = $entityManager->getSalesReport(getParentId(), $start_date, $end_date, $key, "assigned_id", $arr['define_as']);
                $sales[$key] = array(
                    "id" => $key,
                    "name" => $value,
                    "start_date" => $start_date,
                    "end_date" => $end_date,
                    "leads" => $lqo['leads'],
                    "quotes" => $lqo['quotes'],
                    "orders" => $lqo['orders'],
                    "conv_rate" => $lqo['conv_rate'],
                    "dispatched" => $lqo['dispatched'],
                    "tariffs" => $lqo['tariffs'],
                    "carrier_pay" => $lqo['carrier_pay'],
                    "terminal_feesP" => $lqo['terminal_feesP'],
                    "terminal_feesD" => $lqo['terminal_feesD'],
                    "gross_profit" => $lqo['gross_profit'],
                    "profit_margin" => $lqo['profit_margin'],
                    "average_profit" => $lqo['average_profit'],
                );

                // Calculate totals

                $totals['leads'] += $lqo['leads'];
                $totals['quotes'] += $lqo['quotes'];
                $totals['orders'] += $lqo['orders'];
                $totals["dispatched"] += $lqo['dispatched'];
                $totals["tariffs"] += $lqo['tariffs'];
                $totals["carrier_pay"] += $lqo['carrier_pay'];
                $totals["terminal_feesP"] += $lqo['terminal_feesP'];
                $totals["terminal_feesD"] += $lqo['terminal_feesD'];
            }

            if ($totals["quotes"] > 0) {
                $totals["conv_rate"] = ($totals["orders"] / $totals["quotes"]) * 100;
            }
            $totals["gross_profit"] = $totals["tariffs"] - $totals["carrier_pay"] - $totals["terminal_feesP"] - $totals["terminal_feesD"];
            if ($totals["tariffs"] > 0) {
                $totals["profit_margin"] = (($totals["gross_profit"] / $totals["tariffs"])) * 100;
            }
            if ($totals["dispatched"] > 0) {
                $totals["average_profit"] = $lqo["tariffs"] / $lqo["orders"];
            }

            if ($is_export) { // Build Export end exit
                $this->export_sales($sales, $totals, $start_date, $end_date);
            }
        }

        $this->daffny->tpl->sales = $sales;
        $this->daffny->tpl->totals = $totals;

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");

        $this->input['ptype1ch'] = "";
        $this->input['ptype2ch'] = "";
        if (isset($_POST['ptype']) && $_POST['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
        }
    }

    public function salesnew()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Sales report"));
        $this->tplname = "reports.sales.shownew";

        $start_date = "";
        $end_date = "";
        $users_ids = array();

        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $arr = array();
        if (isset($_POST['submit']) || $is_export) {
            $arr = array(
                "time_period" => post_var("time_period"),
                "start_date" => post_var("start_date"),
                "end_date" => post_var("end_date"),
                "define_as" => (int) post_var("define_as") == 1 ? true : false,
            );

            $this->input = $arr;

            $users_ids = post_var("users_ids");

            if (post_var("ptype") == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $start_date = $tp[0];
                $end_date = $tp[1];
            }

            if (post_var("ptype") == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $start_date = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $end_date = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }

        // Generate report
        $sales = array();
        $totals = array();
        if (!count($this->err) && $start_date != "" && $end_date != "") {
            $this->daffny->tpl->sales = array();

            if (!is_array($users_ids)) {
                $users_ids = array();
            }

            //Get Users
            $where = " id IN ('" . implode("','", $users_ids) . "')";
            $user = new Member();
            $users = $user->getCompanyMembers($this->daffny->DB, getParentId(), $where, true);

            $entityManager = new EntityManager($this->daffny->DB);
            $lqo = $entityManager->getSalesReportNew(getParentId(), $start_date, $end_date, $users, "assigned_id", $arr['define_as']);

        }
        $this->daffny->tpl->sales = $lqo;
        $this->daffny->tpl->totals = $totals;

        $this->form->helperMLTPL("users_ids[]", $this->getUsers(), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        if (!isset($this->input["define_as"])) {
            $this->input["define_as"] = 0;
        }
        $this->form->helperDefineAs("define_as");
        $this->input['ptype1ch'] = "";
        $this->input['ptype2ch'] = "";
        if (isset($_POST['ptype']) && $_POST['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
        }
    }

    public function paynew()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Sales report"));
        $this->tplname = "reports.sales.paynew";

        $start_date = "";
        $end_date = "";
        $users_ids = array();

        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $arr = array();
        if (isset($_POST['submit']) || $is_export) {
            $arr = array(
                "time_period" => post_var("time_period"),
                "start_date" => post_var("start_date"),
                "end_date" => post_var("end_date"),
                "define_as" => (int) post_var("define_as") == 1 ? true : false,
            );

            $this->input = $arr;

            $users_ids = post_var("users_ids");

            if (post_var("ptype") == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $start_date = $tp[0];
                $end_date = $tp[1];
            }

            if (post_var("ptype") == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $start_date = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $end_date = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }

        // Generate report
        $sales = array();
        $totals = array();
        if (!count($this->err) && $start_date != "" && $end_date != "") {
            $this->daffny->tpl->sales = array();

            if (!is_array($users_ids)) {
                $users_ids = array();
            }

            //Get Users
            $where = " id IN ('" . implode("','", $users_ids) . "')";
            $user = new Member();
            $users = $user->getCompanyMembers($this->daffny->DB, getParentId(), $where, true);
            $entityManager = new EntityManager($this->daffny->DB);

            $lqo = null;
            try{
                $lqo = $entityManager->getPayReportNew(getParentId(), $start_date, $end_date, $users, "assigned_id", $arr['define_as']);
            } catch(Exception $e){
                die($e->getMessage());
            }
            

        }

        $data = $this->daffny->tpl->sales = $lqo;
        $this->daffny->tpl->totals = $totals;
        if ($is_export) { // Build Export end exit
            $this->export_payment($data);
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        if (!isset($this->input["define_as"])) {
            $this->input["define_as"] = 0;
        }
        $this->form->helperDefineAs("define_as");
        $this->input['ptype1ch'] = "";
        $this->input['ptype2ch'] = "";
        if (isset($_POST['ptype']) && $_POST['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
        }
    }

    /**
     * Orders report
     * Slide 34
     * (Order ID, Order date, Delivery date, Description, Shipper, Carrier, Status (New, Posted to Board, Dispatched – Not Signed, Dispatched – Signed, Picked Up, Delivered, Assumed Delivered, Cancelled), Reimbursable (Yes/No), Assigned To, Cost (=Carrier Pay + Terminal Fee), Total Price (=Tariff).
     * This report can be additional filtered by Order ID and Shipper and also exclude all cancelled orders.
     */
    public function orders()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Orders"));
        $this->tplname = "reports.orders.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $users_ids = array();
        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["status_id"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {

            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));
            $_SESSION["referred_by"] = post_var("referred_by");
            $_SESSION["source_name"] = post_var("source_name");

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];
        $report_arr["status_id"] = $_SESSION["status_id"];
        $report_arr["referred_by"] = $_SESSION["referred_by"];
        $report_arr["source_name"] = $_SESSION["source_name"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');
        if (!count($this->err)) {
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getOrdersReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getOrdersReport(null, null, $report_arr, getParentId());
                $this->export_orders($data);
            }
        }

        // prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $source_name = $_SESSION["source_name"];
        if (!is_array($source_name)) {
            $source_name = array();
        }

        $referred_by = $_SESSION["referred_by"];
        if (!is_array($referred_by)) {
            $referred_by = array();
        }

        // fetching all source id
        $sql = "SELECT id,company_name FROM app_leadsources WHERE `status` = 1";
        $sources = $this->daffny->DB->query($sql);
        $source_id = array();
        while ($row = mysqli_fetch_assoc($sources)) {
            $source_id[$row['id']] = $row['company_name'];
        }

        // fetching all referer id
        $sql = "SELECT id,name FROM app_referrers WHERE `status` = 1";
        $ref = $this->daffny->DB->query($sql);
        $ref_id = array();
        while ($row = mysqli_fetch_assoc($ref)) {
            $ref_id[$row['id']] = $row['name'];
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->helperMLTPL("referred_by[]", $ref_id, $referred_by, array("id" => "referred_by", "multiple" => "multiple"), "Referred", "</td><td colspan=\"3\">");
        $this->form->helperMLTPL("source_name[]", $source_id, $source_name, array("id" => "source_name", "multiple" => "multiple"), "Source", "</td><td colspan=\"3\">");

        $this->form->ComboBox("status_id", array("" => "-- All --") + Entity::$status_name_orders, array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Ship Via", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Payments received Report
     * Slide 35
     * (Date, Order ID, Shipper, Amount, Payment Method, Entered By, Reference #, Notes, Check #, Last 4 digits of CC, CC Type, CC Expiration, Authorization Code, Transaction ID).
     * This report can be additional filtered by Order ID, Shipper, Reference # and Transaction ID.
     *
     */
    public function payments()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Payments received"));
        $this->tplname = "reports.payments.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }
        //      set defaults for search fields
        $users_ids = array();
        if (!isset($_SESSION["transaction_id"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["reference_no"] = "";
            $_SESSION["transaction_id"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["reference_no"] = trim(post_var("reference_no"));
            $_SESSION["transaction_id"] = trim(post_var("transaction_id"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "reference_no" => $_SESSION["reference_no"]
            , "transaction_id" => $_SESSION["transaction_id"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];

        //      Generate report
        $this->daffny->tpl->payments = array();
        if (!count($this->err)) {
            if (!$is_export) {
                $this->applyOrder(Entity::TABLE);
                $this->order->setDefault('id', 'desc');
                $paymentManager = new PaymentManager($this->daffny->DB);
                $this->daffny->tpl->payments = $paymentManager->getPaymentsReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($paymentManager->getPager());
            } else {
                $paymentManager = new PaymentManager($this->daffny->DB);
                $data = $this->daffny->tpl->payments = $paymentManager->getPaymentsReport(null, null, $report_arr, getParentId());
                $this->export_payments($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsers(), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Shipper", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("reference_no", 100, array(), "Reference #", "</td><td colspan=\"3\">");
        $this->form->TextField("transaction_id", 100, array(), "Transaction ID", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Slide 34
     * Carriers Report
     * Carriers (Name, Company Name, Contact, Alt. Contact, Phone, Alt. Phone, Fax, Email, Address Line 1, Address Line 2, City, State/Province, Zip/Postal code, Country, Print on Check As, Tax ID, Notes
     * This report displays all vendors up to date and can be additionally filtered by Company’s name.
     */
    public function carriers()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Carriers"));
        $this->tplname = "reports.carriers.show";

        $is_export = false;

        if (!isset($_SESSION["company_name"])) {
            $_SESSION["company_name"] = "";
        }

        if (isset($_POST['submit'])) {
            $_SESSION["company_name"] = trim(post_var("company_name"));
            //if is export
            if (isset($_POST["export"]) || isset($_POST["export_x"])) {
                $is_export = true;
            }
        }

        $this->input["company_name"] = htmlspecialchars($_SESSION["company_name"]);
        $comp_name = "";

        if ($_SESSION["company_name"] != "") {
            $comp_name = mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION["company_name"]);
        }

        if (!$is_export) {
            $this->applyOrder(Account::TABLE);
            $this->order->setDefault('id', 'desc');
            $accountsManager = new AccountManager($this->daffny->DB);
            $this->daffny->tpl->carriers = $accountsManager->getCarriersReports($this->order->getOrder(), $_SESSION['per_page'], $comp_name, getParentId());
            $this->setPager($accountsManager->getPager());
        } else {
            $accountsManager = new AccountManager($this->daffny->DB);
            $data = $accountsManager->getCarriersReports(null, null, $comp_name, getParentId());
            $this->export_carriers($data);
        }

        $this->form->TextField("company_name", 100, array(), "Company Name", "</td><td>");
    }

    /**
     * Shippers Repors
     * Slide 35
     * Fields: Customer Name, Company Name, First Name, Last Name, Contact, Phone, Alt. Phone, Fax, Email, Address Line 1, Address Line 2, City, State/Province, Zip/Postal code, Country, Notes
     * This report displays all customers up to date
     * and can be additionally filtered by Customer’s name.
     */
    public function shippers()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Shippers"));
        $this->tplname = "reports.shippers.show";

        $is_export = false;

        if (!isset($_SESSION["ptype"])) {
            $_SESSION["customers_name"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
        }

        if (isset($_POST['submit'])) {
            $_SESSION["customers_name"] = trim(post_var("customers_name"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));

            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
            //if is export
            if (isset($_POST["export"]) || isset($_POST["export_x"])) {
                $is_export = true;
            }
        }

        $cust_name = "";

        if ($_SESSION["customers_name"] != "") {
            $cust_name = mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION["customers_name"]);
        }

        //      Collect data in array
        $search_arr = array(
            "customers_name" => $_SESSION["customers_name"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"],
        );

        //prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        //      prepare search conditions for query
        $report_arr = array();
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];

        $this->daffny->tpl->start_date = $_SESSION["start_date"];
        $this->daffny->tpl->end_date = $_SESSION["end_date"];

        if (!$is_export) {

            $this->applyOrder(Account::TABLE);
            $this->order->setDefault('id', 'desc');
            $accountManager = new AccountManager($this->daffny->DB);
            $this->daffny->tpl->shippers = $accountManager->getShippersReports($this->order->getOrder(), $_SESSION['per_page'], $cust_name, $report_arr, getParentId());
            $this->setPager($accountManager->getPager());
        } else {
            $accountManager = new AccountManager($this->daffny->DB);
            $data = $accountManager->getShippersReportsExport(null, null, $cust_name, $report_arr, getParentId());
            $this->export_shippers($data, $_SESSION["start_date"], $_SESSION["end_date"], $_POST['token']);

        }

        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("customers_name", 100, array(), "Customer's Name", "</td><td>");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Accounts Payable/Receivable Report
     * Order information (Order ID, Order date, Carrier, Shipper, Origin, Destination, ETD);
     * Original Order Terms (Tariff, Carrier Pay, Terminal Fee (Pickup on top, Delivery on bottom), Deposit, COD, Profit);
     * Accounts Receivable (From Shipper, From Broker (do not display this option for Brokers accounts), From Carrier, From Pickup Terminal, From Drop-off Terminal),
     * Accounts Payable (To Shipper, To Broker (do not display this option for Brokers accounts), To Carrier, To Pickup Terminal, To Drop-off Terminal).
     * Slide 35
     */
    public function accounts()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Accounts Payable/Receivable"));
        $this->tplname = "reports.accounts.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }
        //      set defaults for search fields
        $users_ids = array();
        if (!isset($_SESSION["include_orders"])) {
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
            $_SESSION["include_orders"] = 0;
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["include_orders"] = (post_var("include_orders") == "1" ? 1 : 0);

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "include_orders" => $_SESSION["include_orders"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        if (!count($this->err)) {
            if (!$is_export) {

                $this->applyOrder(Entity::TABLE);
                $this->order->setDefault('id', 'desc');
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getAccountsReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getAccountsReport(null, null, $report_arr, getParentId());
                $this->export_accounts($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsers(), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->CheckBox("include_orders", array(), "Include orders that are not dispatched", " ");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Lead Sources Report
     * Slide 35
     * Lead Sources (Lead Source, All Leads, All Quotes, All Orders, Conversion rate (Order : Quote), Dispatched Orders, Tariffs, Carrier Pay, Terminal Fees, Gross Profit, Profit Margin, Average Profit per Order
     * This report displays all lead sources up to date and can be additionally filtered by Lead Source name.
     */
    public function lead_sources()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Lead Sources"));
        $this->tplname = "reports.lead_sources.show";

        $ls_ids = array();
        $start_date = "";
        $end_date = "";

        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        if (isset($_POST['submit']) || $is_export) {

            $arr = array(
                "time_period" => post_var("time_period")
                , "start_date" => post_var("start_date")
                , "end_date" => post_var("end_date")
                , "define_as" => (int) post_var("define_as") == 1 ? true : false,
            );
            $this->input = $arr;
            $ls_ids = post_var("ls_ids");

            if (post_var("ptype") == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $start_date = $tp[0];
                $end_date = $tp[1];
            }

            if (post_var("ptype") == 2) {

                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $start_date = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $end_date = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }

        // Generate report
        $lss = array();
        $totals = array();
        if (!count($this->err) && ($start_date != "" && $end_date != "")) {
            //Get Lead Sources
            $this->daffny->tpl->lss = array();
            $leadsourceManager = new LeadsourceManager($this->daffny->DB);
            $entityManager = new EntityManager($this->daffny->DB);

            if (!is_array($ls_ids)) {
                $ls_ids = array();
            }

            $ls = $leadsourceManager->getForReport(getParentId(), $ls_ids);

            $lqo = array();
            $totals = array(
                "leads" => 0
                , "quotes" => 0
                , "orders" => 0
                , "conv_rate" => 0
                , "dispatched" => 0
                , "tariffs" => 0
                , "carrier_pay" => 0
                , "terminal_fees" => 0
                , "gross_profit" => 0
                , "profit_margin" => 0
                , "average_profit" => 0,
            );
            foreach ($ls as $key => $value) {

                $lqo = $entityManager->getSalesReport(getParentId(), $start_date, $end_date, $key, "source_id", $arr["define_as"]);

                $lss[$key] = array(
                    "id" => $key
                    , "name" => $value
                    , "leads" => $lqo['leads']
                    , "quotes" => $lqo['quotes']
                    , "orders" => $lqo['orders']
                    , "conv_rate" => $lqo['conv_rate']
                    , "dispatched" => $lqo['dispatched']
                    , "tariffs" => $lqo['tariffs']
                    , "carrier_pay" => $lqo['carrier_pay']
                    , "terminal_feesP" => $lqo['terminal_feesP']
                    , "terminal_feesD" => $lqo['terminal_feesD']
                    , "gross_profit" => $lqo['gross_profit']
                    , "profit_margin" => $lqo['profit_margin']
                    , "average_profit" => $lqo['average_profit'],
                );

                $totals['leads'] += $lqo['leads'];
                $totals['quotes'] += $lqo['quotes'];
                $totals['orders'] += $lqo['orders'];
                $totals["dispatched"] += $lqo['dispatched'];
                $totals["tariffs"] += $lqo['tariffs'];
                $totals["carrier_pay"] += $lqo['carrier_pay'];
                $totals["terminal_fees"] += $lqo['terminal_feesP'] + $lqo['terminal_feesD'];
            }

            if ($totals["quotes"] > 0) {
                $totals["conv_rate"] = ($totals["orders"] / $totals["quotes"]) * 100;
            }
            $totals["gross_profit"] = $totals["tariffs"] - $totals["carrier_pay"] - $totals["terminal_fees"];
            if ($totals["tariffs"] > 0) {
                $totals["profit_margin"] = ceil(($totals["gross_profit"] / $totals["tariffs"])) * 100;
            }
            if ($totals["dispatched"] > 0) {
                $totals["average_profit"] = $totals["gross_profit"] / $totals["dispatched"];
            }

            //export to Excel
            if ($is_export) {
                $this->export_lead_sources($lss, $totals, $start_date, $end_date);
            }
        }

        $this->daffny->tpl->lss = $lss;
        $this->daffny->tpl->totals = $totals;

        $this->form->helperMLTPL("ls_ids[]", $this->getLeadSources(), $ls_ids, array("id" => "ls_ids", "multiple" => "multiple"), "Lead Sources", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");

        if (!isset($this->input["define_as"])) {
            $this->input["define_as"] = 0; //1st radio by Default
        }
        $this->form->helperDefineAs("define_as");

        $this->input['ptype1ch'] = "";
        $this->input['ptype2ch'] = "";
        if (isset($_POST['ptype']) && $_POST['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
        }
    }

    /**
     * On-Time Load/Delivery
     * Slide 35
     * (Order ID, Status, Carrier, Shipper, Vehicles, Origin, Destination, Est. Load date, Actual Pick up date, Deviation (+/- days), Est. Delivery date, Actual Delivery date, Deviation (+/- days), Last Internal Note).
     * This report displays all loads/deliveries and can be additionally filtered by Order ID, Status, Carrier, Shipper.
     * If Estimated Load date is more than 3 days from Actual Pick up date, or Estimated Delivery date is more than 3 days apart from Actual Delivery date, highlight such records in light red.
     *
     */
    public function on_time()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "On-Time Load/Delivery"));
        $this->tplname = "reports.on_time.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $users_ids = array();
        if (!isset($_SESSION["order_id"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
            $_SESSION["status_id"] = "";
            $_SESSION["carrier_name"] = "";
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));
            $_SESSION["carrier_name"] = trim(post_var("carrier_name"));

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"]
            , "carrier_name" => $_SESSION["carrier_name"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        if (!count($this->err)) {
            if (!$is_export) {
                $this->applyOrder(Entity::TABLE);
                $this->order->setDefault('id', 'desc');
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getOrdersReportOnTime($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getOrdersReportOnTime(null, null, $report_arr, getParentId());
                $this->export_ontime($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->ComboBox("status_id", array("0" => "-- All --") + Entity::$status_name_ontime, array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Shipper", "</td><td colspan=\"3\">");
        $this->form->TextField("carrier_name", 100, array(), "Carrier", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Quotes Report
     * Slide 35
     * Quotes (Quote ID, Assigned To, Before Quote (who it was assigned before), Quote date, Quote time, Pickup State, Pickup Zip code, Pickup City, Dropoff State, Dropoff Zip Code, Dropoff City, Estimated Ship date, Inop (Yes/No), Ship Via, First Name, Last Name, Company, Email, Phone, Alt. Phone, Cell, Fax, Address Line 1, Address Line 2, City, State, Zip, Country, Referrer, Lead Source, Vehicles Year, Make, Model, Year, Type, Tariff, Deposit Required).
     * Thisreport displays all quotes and can be additionally filtered by Quote ID, Assigned To, First + Last Name, Email or Phone Number.
     */
    public function quotes()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Quotes"));
        $this->tplname = "reports.quotes.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $users_ids = array();
        if (!isset($_SESSION["quote_id"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["quote_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["quote_id"] = trim(post_var("quote_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "quote_id" => $_SESSION["quote_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];

        //      Generate report
        $this->daffny->tpl->quotes = array();
        if (!count($this->err)) {
            if (!$is_export) {

                $this->applyOrder(Entity::TABLE);
                $this->order->setDefault('id', 'desc');
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->quotes = $entityManager->getQuotesReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->quotes = $entityManager->getQuotesReport(null, null, $report_arr, getParentId());
                $this->export_quotes($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Ship Via", "</td><td colspan=\"3\">");
        $this->form->TextField("quote_id", 100, array(), "Quote ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Get Date Range by Type
     *
     *
     * @param tinyint $type
     * @return array { 0-Start Date, 1-End Date  }
     */
    private function getTimePeriod($type)
    {

        $d1 = date("Y-m-d 00:00:00");
        $d2 = date("Y-m-d 23:59:59");

        switch ($type) {
            case "1":
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y")));
                $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m") + 1, 0, date("Y")));
                break;
            case "2":
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 1, 1, date("Y")));
                $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), 0, date("Y")));
                break;
            case "3":
                // Get a quarter in the year from a month
                $startmth = date("m") - 3 - ((date("m") - 1) % 3);
                // Fix up Jan - Feb to get LAST year's quarter dates (Oct - Dec)
                $year = date("Y");
                if ($startmth == -2) {
                    $startmth += 12;
                    $year -= 1;
                }
                $endmth = $startmth + 2;
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, $startmth, 1, $year));
                $d2 = date("Y-m-d H:i:s", mktime(23, 59, 59, $endmth, date("t", mktime(0, 0, 0, $endmth, 1, $year)), $year));
                break;
            case "4":
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, date("Y")));
                $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
                break;
            case "5":
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, 2011));
                $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
                break;
            default:
                $d1 = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, 2011));
                $d2 = date("Y-m-d H:i:s", mktime(0, 0, 0, 12, 31, date("Y")));
        }

        return array(
            "0" => $d1
            , "1" => $d2,
        );
    }

    /**
     * Build Excel Report (Lead Sources)
     *
     * @param array $lss - Sales by Lead source
     * @param array $t - Totals
     * @param datetime $start_date
     * @param datetime $end_date
     *
     */
    final private function export_lead_sources($lss, $t, $start_date, $end_date)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Lead Sources');
        //      Build header
        $sht->getCellByColumnAndRow(0, 1)->setValue("Lead Sources Report");
        $sht->getCellByColumnAndRow(0, 2)->setValue("Start Date:");
        $sht->getCellByColumnAndRow(0, 3)->setValue("End Date:");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(1, 2)->setValue(date("m/d/Y", strtotime($start_date)));
        $sht->getCellByColumnAndRow(1, 3)->setValue(date("m/d/Y", strtotime($end_date)));
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Print grid header
        $i = 6;
        $sht->getCellByColumnAndRow(0, $i)->setValue("Name");
        $sht->getColumnDimension('A')->setWidth(25);
        $sht->getCellByColumnAndRow(1, $i)->setValue("Leads");
        $sht->getCellByColumnAndRow(2, $i)->setValue("Quotes");
        $sht->getCellByColumnAndRow(3, $i)->setValue("Orders");
        $sht->getCellByColumnAndRow(4, $i)->setValue("Conv Rate");
        $sht->getCellByColumnAndRow(5, $i)->setValue("Dispatched");
        $sht->getCellByColumnAndRow(6, $i)->setValue("Tariffs");
        $sht->getCellByColumnAndRow(7, $i)->setValue("Carrier Pay");
        $sht->getCellByColumnAndRow(8, $i)->setValue("Gross Profit");
        $sht->getCellByColumnAndRow(9, $i)->setValue("Profit Margin");
        $sht->getCellByColumnAndRow(10, $i)->setValue("Average Profit per Order");
        //Set Grid header Styles
        for ($j = 0; $j <= 10; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //Print logo Image
        $this->printLogo($excl->getActiveSheet());

        //Build grid
        if (count($lss) > 0) {
            foreach ($lss as $s => $l) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($l["name"]);
                $sht->getCellByColumnAndRow(1, $i)->setValue($l["leads"]);
                $sht->getCellByColumnAndRow(2, $i)->setValue($l["quotes"]);
                $sht->getCellByColumnAndRow(3, $i)->setValue($l["orders"]);
                $sht->getCellByColumnAndRow(4, $i)->setValue(number_format($l["conv_rate"], 2) . "%");
                $sht->getCellByColumnAndRow(5, $i)->setValue($l["dispatched"]);
                $sht->getCellByColumnAndRow(6, $i)->setValue("$" . number_format($l["tariffs"], 2));
                $sht->getCellByColumnAndRow(7, $i)->setValue("$" . number_format($l["carrier_pay"], 2));
                $sht->getCellByColumnAndRow(8, $i)->setValue("$" . number_format($l["gross_profit"], 2));
                $sht->getCellByColumnAndRow(9, $i)->setValue(number_format($l["profit_margin"], 2) . "%");
                $sht->getCellByColumnAndRow(10, $i)->setValue("$" . number_format($l["average_profit"], 2));

                for ($k = 0; $k <= 10; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
            //Print Totals
            $i++;
            for ($j = 0; $j <= 10; $j++) {
                $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->totalFont);
            }
            $sht->getCellByColumnAndRow(0, $i)->setValue("Totals");
            $sht->getCellByColumnAndRow(1, $i)->setValue($t["leads"]);
            $sht->getCellByColumnAndRow(2, $i)->setValue($t["quotes"]);
            $sht->getCellByColumnAndRow(3, $i)->setValue($t["orders"]);
            $sht->getCellByColumnAndRow(4, $i)->setValue(number_format($t["conv_rate"], 2) . "%");
            $sht->getCellByColumnAndRow(5, $i)->setValue($t["dispatched"]);
            $sht->getCellByColumnAndRow(6, $i)->setValue("$" . number_format($t["tariffs"], 2));
            $sht->getCellByColumnAndRow(7, $i)->setValue("$" . number_format($t["carrier_pay"], 2));
            $sht->getCellByColumnAndRow(8, $i)->setValue("$" . number_format($t["gross_profit"], 2));
            $sht->getCellByColumnAndRow(9, $i)->setValue(number_format($t["profit_margin"], 2) . "%");
            $sht->getCellByColumnAndRow(10, $i)->setValue("$" . number_format($t["average_profit"], 2));
        }

        //      output file
        $this->outputExcel($excl, "lead_sources");
    }

    /**
     * Build Excel Report (Sales)
     *
     * @param array $sales - Array with sales
     * @param array $t - Array with Totals
     * @param datetime $start_date
     * @param datetime $end_date
     */
    final private function export_sales($sales, $t, $start_date, $end_date)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Sales');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Sales Report");
        $sht->getCellByColumnAndRow(0, 2)->setValue("Start Date:");
        $sht->getCellByColumnAndRow(0, 3)->setValue("End Date:");
        $sht->getCellByColumnAndRow(5, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(5, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(1, 2)->setValue(date("m/d/Y", strtotime($start_date)));
        $sht->getCellByColumnAndRow(1, 3)->setValue(date("m/d/Y", strtotime($end_date)));
        $sht->getCellByColumnAndRow(6, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(6, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(5, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(6, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(5, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(6, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $sht->getCellByColumnAndRow(0, $i)->setValue("User");
        $sht->getColumnDimension('A')->setWidth(25);
        $sht->getCellByColumnAndRow(1, $i)->setValue("Orders");
        $sht->getCellByColumnAndRow(2, $i)->setValue("Dispatched");
        $sht->getCellByColumnAndRow(3, $i)->setValue("Tariffs");
        $sht->getCellByColumnAndRow(4, $i)->setValue("Carrier Pay");
        $sht->getCellByColumnAndRow(5, $i)->setValue("Gross Profit");
        $sht->getCellByColumnAndRow(6, $i)->setValue("Profit Margin");
        $sht->getCellByColumnAndRow(7, $i)->setValue("Average Profit per Order");

        for ($j = 0; $j <= 7; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //logo
        $this->printLogo($excl->getActiveSheet());

        //build sales grid
        if (count($sales) > 0) {
            foreach ($sales as $s => $l) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($l["name"]);
                $sht->getCellByColumnAndRow(1, $i)->setValue($l["orders"]);
                $sht->getCellByColumnAndRow(2, $i)->setValue($l["dispatched"]);
                $sht->getCellByColumnAndRow(3, $i)->setValue("$" . number_format($l["tariffs"], 2));
                $sht->getCellByColumnAndRow(4, $i)->setValue("$" . number_format($l["carrier_pay"], 2));
                $sht->getCellByColumnAndRow(5, $i)->setValue("$" . number_format($l["gross_profit"], 2));
                $sht->getCellByColumnAndRow(6, $i)->setValue(number_format($l["profit_margin"], 2) . "%");
                $sht->getCellByColumnAndRow(7, $i)->setValue("$" . number_format($l["average_profit"], 2));

                for ($k = 0; $k <= 7; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
            //totals
            $i++;

            for ($j = 0; $j <= 7; $j++) {
                $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->totalFont);
            }
            $sht->getCellByColumnAndRow(0, $i)->setValue("Totals");
            $sht->getCellByColumnAndRow(1, $i)->setValue($t["orders"]);
            $sht->getCellByColumnAndRow(2, $i)->setValue($t["dispatched"]);
            $sht->getCellByColumnAndRow(3, $i)->setValue("$" . number_format($t["tariffs"], 2));
            $sht->getCellByColumnAndRow(4, $i)->setValue("$" . number_format($t["carrier_pay"], 2));
            $sht->getCellByColumnAndRow(5, $i)->setValue("$" . number_format($t["gross_profit"], 2));
            $sht->getCellByColumnAndRow(6, $i)->setValue(number_format($t["profit_margin"], 2) . "%");
            $sht->getCellByColumnAndRow(7, $i)->setValue("$" . number_format($t["average_profit"], 2));
        }
        //      output
        $this->outputExcel($excl, "sales");
    }

    /**
     * Build Excel Report (Sales)
     *
     * @param array $sales - Array with sales
     * @param array $t - Array with Totals
     * @param datetime $start_date
     * @param datetime $end_date
     */
    final private function export_payment($data)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Payments');

        //Build Header with user data
        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Payments received Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Date Recieved"
            , "Recieved Day"
            , "aNo. of payments"
            , "GP%"
            , "Payment IN"
            , "Broker Fee(s)"
            , "Carrier Fee(s)"
            , "pNo. of payments"
            , "Carrier Payments"
            , "Refund Processed"
            , "Diffrenece",
        );

        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        if (count($data) > 0) {
            foreach ($data as $p) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($p["date_received"]);
                $sht->getCellByColumnAndRow(1, $i)->setValue($p["date_received_day"]);
                $sht->getCellByColumnAndRow(2, $i)->setValue($p["AR_NoOfPayment"]);
                $sht->getCellByColumnAndRow(3, $i)->setValue(number_format($p["AR_Pay_InTotalDeposit / AR_Pay_InTotalTariff"]) * 100, 2);
                $sht->getCellByColumnAndRow(4, $i)->setValue(number_format($p["AR_Pay_In"]), 2);
                $sht->getCellByColumnAndRow(5, $i)->setValue(number_format($p["AR_Pay_InTotalDeposit"], 2));
                $sht->getCellByColumnAndRow(6, $i)->setValue(number_format($p["AR_Pay_InCarrier_pay"], 2));
                $sht->getCellByColumnAndRow(7, $i)->setValue($p["AP_NoOfPayment"]);
                $sht->getCellByColumnAndRow(8, $i)->setValue(number_format($p["AP_Pay_OutCarrier_pay"]), 2);
                $sht->getCellByColumnAndRow(10, $i)->setValue('$0.00');
                $sht->getCellByColumnAndRow(9, $i)->setValue(number_format($p["AR_Pay_In"] - $p["AP_Pay_Out"]), 2);

                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "payments");
    }

    /**
     * Get Users for build Combobox
     * @return array
     */
    private function getUsers()
    {
        $member = new Member();
        return $member->getCompanyMembers($this->daffny->DB, getParentId(), "", true);
    }

    private function getUsersByStatus($where)
    {
        $member = new Member();
        return $member->getCompanyMembersByStatus($this->daffny->DB, getParentId(), $where, true);
    }

    /**
     * Get Lead Sources for Combobox
     * @return array
     */
    private function getLeadSources()
    {
        $leadsourceManager = new LeadsourceManager($this->daffny->DB);
        return $leadsourceManager->getLeadSourcesCombo(getParentId());
    }

    /**
     * Build Excel Report (Shippers)
     *
     * @param array $data
     */
    final private function export_shippers($data, $start_date, $end_date, $token)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Shippers');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Shippers Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(0, 2)->setValue("Start Date:");
        $sht->getCellByColumnAndRow(0, 3)->setValue("End Date:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(1, 2)->setValue(date("m/d/Y", strtotime($start_date)));
        $sht->getCellByColumnAndRow(1, 3)->setValue(date("m/d/Y", strtotime($end_date)));
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(13, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(13, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(13, 5)->applyFromArray($this->smallFont);
        //Grid header
        $i = 6;
        $sht->getCellByColumnAndRow(0, $i)->setValue("ID");
        $sht->getCellByColumnAndRow(1, $i)->setValue("Name");
        $sht->getCellByColumnAndRow(2, $i)->setValue("Company");
        $sht->getCellByColumnAndRow(3, $i)->setValue("Phone 1");
        $sht->getCellByColumnAndRow(4, $i)->setValue("Phone 2");
        $sht->getCellByColumnAndRow(5, $i)->setValue("Email");
        $sht->getCellByColumnAndRow(6, $i)->setValue("Source");
        $sht->getCellByColumnAndRow(7, $i)->setValue("Assigned To");
        $sht->getCellByColumnAndRow(8, $i)->setValue("Orders");
        $sht->getCellByColumnAndRow(9, $i)->setValue("Tariff");
        $sht->getCellByColumnAndRow(10, $i)->setValue("Carrier Pay");
        $sht->getCellByColumnAndRow(11, $i)->setValue("Deposits");
        $sht->getCellByColumnAndRow(12, $i)->setValue("Open Invoices");
        $sht->getCellByColumnAndRow(13, $i)->setValue("Payments Processed");
        //apply format for header
        for ($j = 0; $j <= 13; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //logo
        $this->printLogo($excl->getActiveSheet());

        //build sales grid
        if (count($data) > 0) {
            $dataStart = $i + 1;
            foreach ($data as $s => $l) {
                $i++;
                $sourceName = htmlspecialchars($l["source_name"] == "" ? "NONE" : $l["source_name"]);
                $d = array(
                    $l["account_id"],
                    $l['shipperfname'] . " " . $l['shipperlname'],
                    $l['shippercompany'],
                    $l['shipperphone1'],
                    $l['shipperphone2'],
                    $l['shipperemail'],
                    $sourceName,
                    htmlspecialchars($l["AssignedName"]),
                    $l["orders"],
                    "$" . $l["tariff"],
                    "$" . $l["carrier"],
                    "$" . $l["deposit"],
                    $l["invoices"],
                    $l["payments"],
                );
                $sht->fromArray($d, null, 'A' . $i);
            }
            $sht->getStyle("A$dataStart:N$i")->applyFromArray($this->lineFont);
        }
        //output
        $this->outputExcel($excl, "shippers");
    }

    final private function export_carriers($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Carriers');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Carriers Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);
        //Grid header
        $i = 6;
        $sht->getCellByColumnAndRow(0, $i)->setValue("ID");
        $sht->getCellByColumnAndRow(1, $i)->setValue("Carrier Name");
        $sht->getCellByColumnAndRow(2, $i)->setValue("Print Check Name");
        $sht->getCellByColumnAndRow(3, $i)->setValue("Tax ID Number");
        $sht->getCellByColumnAndRow(4, $i)->setValue("Contact Information1");
        $sht->getCellByColumnAndRow(5, $i)->setValue("Contact Information2");
        $sht->getCellByColumnAndRow(6, $i)->setValue("Mobile");
        $sht->getCellByColumnAndRow(7, $i)->setValue("Fax Number");
        $sht->getCellByColumnAndRow(8, $i)->setValue("Email Addresses");
        $sht->getCellByColumnAndRow(9, $i)->setValue("Dispatches");
        $sht->getCellByColumnAndRow(10, $i)->setValue("Open Bills");
        $sht->getCellByColumnAndRow(11, $i)->setValue("Payments Made");
        $sht->getCellByColumnAndRow(12, $i)->setValue("Total Tariff");
        //      apply format for header
        for ($j = 0; $j <= 12; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $s => $l) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($l["id"]);
                $sht->getCellByColumnAndRow(1, $i)->setValue($l["company_name"]);
                $sht->getCellByColumnAndRow(2, $i)->setValue($l["print_name"]);
                $sht->getCellByColumnAndRow(3, $i)->setValue($l["tax_id_num"] . " MC Number : " . $l["insurance_iccmcnumber"]);
                $sht->getCellByColumnAndRow(4, $i)->setValue($l["contact_name1"] == "" ? $l["phone1"] : $l["contact_name1"] . " ," . $l["phone1"]);
                $sht->getCellByColumnAndRow(5, $i)->setValue($l["contact_name2"] == "" ? $l["phone2"] : $l["contact_name2"] . " ," . $l["phone2"]);
                $sht->getCellByColumnAndRow(6, $i)->setValue($l["mobile"]);
                $sht->getCellByColumnAndRow(7, $i)->setValue($l["fax"]);
                $sht->getCellByColumnAndRow(8, $i)->setValue($l["email"]);
                $sht->getCellByColumnAndRow(9, $i)->setValue($l["orders"]);
                $sht->getCellByColumnAndRow(10, $i)->setValue($l["invoices"]);
                $sht->getCellByColumnAndRow(11, $i)->setValue($l["payments"]);
                $sht->getCellByColumnAndRow(12, $i)->setValue("$" . $l["tariff"]);
                //              apply format for grid body
                for ($k = 0; $k <= 12; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "carriers");
    }

    /**
     * Export Quotes
     *
     * @param array $data
     */
    final private function export_quotes($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Quotes');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Quotes Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "ID"
            , "Assigned To"
            , "Before Quote"
            , "Quote date"
            , "Quote time"
            , "Pickup"
            , "Dropoff"
            , "Estimated Ship date"
            , "Ship Via"
            , "Shipper"
            , "Shipper Email"
            , "Shipper Phone"
            , "Referrer"
            , "Lead Source"
            , "Vehicles"
            , "Tariff"
            , "Deposit Required",
        );
        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $q) {
                $vechicles = $q->getVehicles();
                foreach ($vechicles as $v) {
                    $i++;
                    $sht->getCellByColumnAndRow(0, $i)->setValue($q->id);
                    $sht->getCellByColumnAndRow(1, $i)->setValue($q->getAssigned()->username);
                    $sht->getCellByColumnAndRow(2, $i)->setValue($q->before_assigned_id == "" ? "" : $q->getBeforeAssigned()->username);
                    $sht->getCellByColumnAndRow(3, $i)->setValue($q->getQuoted("m/d/Y"));
                    $sht->getCellByColumnAndRow(4, $i)->setValue($q->getQuoted("h:i A"));
                    $sht->getCellByColumnAndRow(5, $i)->setValue($q->getOrigin()->state . " " . $q->getOrigin()->city . " " . $q->getOrigin()->zip);
                    $sht->getCellByColumnAndRow(6, $i)->setValue($q->getDestination()->state . " " . $q->getDestination()->city . " " . $q->getDestination()->zip);
                    $sht->getCellByColumnAndRow(7, $i)->setValue($q->getShipDate("m/d/Y"));
                    $sht->getCellByColumnAndRow(8, $i)->setValue($q->getShipper()->company);
                    $sht->getCellByColumnAndRow(9, $i)->setValue($q->getShipper()->fname . " " . $q->getShipper()->lname);
                    $sht->getCellByColumnAndRow(10, $i)->setValue($q->getShipper()->email);
                    $sht->getCellByColumnAndRow(11, $i)->setValue($q->getShipper()->phone1 . " " . $q->getShipper()->phone2);
                    $sht->getCellByColumnAndRow(12, $i)->setValue($q->referred_by);
                    $sht->getCellByColumnAndRow(13, $i)->setValue($q->getSource()->name);
                    $sht->getCellByColumnAndRow(14, $i)->setValue($v->year . " " . $v->make . " " . $v->model . " " . $v->type . " " . $v->vin);
                    $sht->getCellByColumnAndRow(15, $i)->setValue($q->getTotalTariff(true));
                    $sht->getCellByColumnAndRow(16, $i)->setValue($q->getTotalDeposit());

                    // apply format for grid body
                    for ($k = 0; $k < $m; $k++) {
                        $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                    }
                }
            }
        }
        //      output
        $this->outputExcel($excl, "quotes");
    }

    /**
     * Export Queue
     *
     * @param array $data
     * @author chetu
     */
    final private function exportQueue($data)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('QuickBook Queue');
        //Build Header with user data
        $sht->getCellByColumnAndRow(8, 1)->setValue("QB Report");
        $sht->getCellByColumnAndRow(7, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(7, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(9, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(9, 5)->setValue("Frieght Dragon");
        //Set styles for header
        $sht->getStyleByColumnAndRow(8, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Quick Book Queue ID"
            , "Enqueue date"
            , "QB Action"
            , "Ident"
            , "QB Status"
            , "Message"
            , "Suggestion",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }

        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;

                $status = array(
                    "s" => "Success",
                    "q" => "Queue",
                    "e" => "Error",
                    "i" => "Information",
                );

                $errorMessageNumber = explode(":", $o->msg);
                $errorNumber = $errorMessageNumber[0];

                $suggestion = array(
                    "500" => "Check if user exists anymore",
                    "3260" => "Contact Admin you dont have permission",
                    "3240" => "N/A",
                    "3210" => "Tried to pay over amount, Please lower the amount",
                    "3200" => "N/A",
                    "3180" => "More than one user modification seen",
                    "3176" => "Transaction in progress, please try again later",
                    "3175" => "Transaction in progress, please try again later",
                    "3173" => "Try searching the record for this INDENT ID",
                    "3170" => "Some other user modifying this record",
                    "3150" => "Unable to detect transaction ID please try again later",
                    "3140" => "Please check if user exists ?",
                    "3120" => "Try using appropriate Values",
                    "3100" => "Name Already in use, tr using some other name",
                    "3090" => "Please remove Colon from the string",
                    "3070" => "RefNumber too long check it again",
                    "3040" => "Please check the amount Entered Again",
                    "3000" => "please check the given list ID",
                    "-2" => "N/A",
                    "-2" => "Contact Admin",
                );

                $sht->getCellByColumnAndRow(0, $i)->setValue($o->quickbooks_queue_id);
                $sht->getCellByColumnAndRow(1, $i)->setValue($o->enqueue_datetime);
                $sht->getCellByColumnAndRow(2, $i)->setValue($o->qb_action);
                $sht->getCellByColumnAndRow(3, $i)->setValue($o->ident);
                $sht->getCellByColumnAndRow(4, $i)->setValue($status[$o->qb_status]);
                $sht->getCellByColumnAndRow(5, $i)->setValue($o->msg);
                $sht->getCellByColumnAndRow(6, $i)->setValue($suggestion[$errorNumber]);

                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }

        //      output
        $this->outputExcel($excl, "qblogs");
    }

    /**
     * Export Orders
     *
     * @param array $data
     */
    final private function export_orders($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Orders');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Orders Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Order date"
            , "Assigned To"
            , "Status"
            , "Source ID"
            , "Referred Name"
            , "Shipper Name"
            , "Company"
            , "Shipper Type"
            , "Carrier"
            , "Total Tarriff"
            , "Carrier Pay"
            , "Deposit",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //logo
        $this->printLogo($excl->getActiveSheet());

        //build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;

                $id_temp = ($o->prefix) ? $id = $o->prefix . "-" . $o->number : $id = $o->number;
                $s1 = $o->getShipper();
                $shipper_id_temp = (htmlspecialchars(trim($o->shipper_id) == "" ? "" : $s1->fname . " " . $s1->lname) . " " . "\n" . $s1->email);
                $company_temp = (trim($o->shipper_id) == "" ? "" : $s1->company);
                $comp_name_temp = trim($o->carrier_id) > 0 ? $o->getCarrier()->company_name : "";

                $q = "SELECT company_name FROM app_leadsources WHERE `id` =" . $o->source_id;
                $r = $this->daffny->DB->query($q);
                $rw = $this->daffny->DB->fetch_row($r);

                $d = array(
                    $id_temp,
                    date("m/d/y h:i a", strtotime($o->created)),
                    $o->getAssigned()->username,
                    Entity::$status_name[$o->status],
                    $rw['company_name'],
                    $o->referred_by,
                    $shipper_id_temp,
                    $company_temp,
                    $s1->shipper_type,
                    $comp_name_temp,
                    $o->total_tariff_stored,
                    "$" . $o->carrier_pay_stored,
                    "$" . number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2),
                );
                $sht->fromArray($d, null, 'A' . $i);
            }
        }
        //output
        $this->outputExcel($excl, "orders");
    }

    /**
     * Export Dispatch
     *
     * @param array $data
     */
    final private function exportDispatch($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Dispatch Activity');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Dispatch Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Assigned Name"
            , "Dispatch Date"
            , "Company Name"
            , "Order Type"
            , "Total Tariff/ Total Deposit",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue(($o->prefix) ? $id = $o->prefix . "-" . $o->number : $id = $o->number);
                try { $contactName = $o->getAssigned()->contactname;} catch (EXCEPTION $e) {$contactName = "---";}
                $sht->getCellByColumnAndRow(1, $i)->setValue($contactName);

                $sht->getCellByColumnAndRow(2, $i)->setValue($o->getDispatched("m/d/Y h:i A"));
                $sht->getCellByColumnAndRow(3, $i)->setValue($o->getCarrier()->company_name);
                if ($o->balance_paid_by == 2) {
                    $balance_paid_by = "COD - Cash/Certified Funds";
                } else if ($o->balance_paid_by == 3) {
                    $balance_paid_by = "COD - Check";
                } else if ($o->balance_paid_by == 8) {
                    $balance_paid_by = "COP - Cash/Certified Funds";
                } else if ($o->balance_paid_by == 9) {
                    $balance_paid_by = "COP - Check";
                } else if ($o->balance_paid_by == 12) {
                    $balance_paid_by = "Billing - Cash/Certified Funds";
                } else if ($o->balance_paid_by == 13) {
                    $balance_paid_by = "Billing - Check";
                } else if ($o->balance_paid_by == 20) {
                    $balance_paid_by = "Billing - Comcheck";
                } else if ($o->balance_paid_by == 21) {
                    $balance_paid_by = "Billing - QuickPay";
                } else if ($o->balance_paid_by == 24) {
                    $balance_paid_by = "Billing - ACH";
                } else if ($o->balance_paid_by == 14) {
                    $balance_paid_by = "Invoice - Cash/Certified Funds";
                } else if ($o->balance_paid_by == 15) {
                    $balance_paid_by = "Invoice - Check";
                } else if ($o->balance_paid_by == 22) {
                    $balance_paid_by = "Invoice - Comcheck";
                } else {
                    $balance_paid_by = "Invoice - QuickPay";
                }
                $sht->getCellByColumnAndRow(4, $i)->setValue($balance_paid_by);

                if ($o->balance_paid_by == 2) {
                    $amount = number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                } else if ($o->balance_paid_by == 3) {
                    $amount = number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                } else if ($o->balance_paid_by == 8) {
                    $amount = number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                } else if ($o->balance_paid_by == 9) {
                    $amount = number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2);
                } else {
                    $amount = $o->total_tariff_stored;
                }

                $sht->getCellByColumnAndRow(5, $i)->setValue($amount);
                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "dispatchReport");
    }

    /**
     * Export Payments
     *
     * @param array $data
     */
    final private function export_payments($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Payments received');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Payments received Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Date"
            , "Order ID"
            , "Shipper"
            , "Amount"
            , "Payment Method"
            , "Entered By"
            , "Reference #"
            , "Notes"
            , "Check #"
            , "Card #"
            , "Card Type"
            , "Card Expiration"
            , "Authorization Code"
            , "Transaction ID",
        );
        //      Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $p) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($p->getDate("m/d/Y"));
                $sht->getCellByColumnAndRow(1, $i)->setValue($p->entity_id);
                $sht->getCellByColumnAndRow(2, $i)->setValue($p->getEntity()->getShipper()->fname . " " . $p->getEntity()->getShipper()->lname);
                $sht->getCellByColumnAndRow(3, $i)->setValue(number_format($p->amount, 2));
                $sht->getCellByColumnAndRow(4, $i)->setValue(Payment::$method_name[$p->method]);
                $sht->getCellByColumnAndRow(5, $i)->setValue($p->getEnteredBy());
                $sht->getCellByColumnAndRow(6, $i)->setValue($p->number);
                $sht->getCellByColumnAndRow(7, $i)->setValue($p->notes);
                $sht->getCellByColumnAndRow(8, $i)->setValue($p->check);
                $sht->getCellByColumnAndRow(9, $i)->setValue(hideCCNumber($p->cc_number, 2));
                $sht->getCellByColumnAndRow(10, $i)->setValue($p->cc_type);
                $sht->getCellByColumnAndRow(11, $i)->setValue($p->getCCExp("m/Y"));
                $sht->getCellByColumnAndRow(12, $i)->setValue($p->cc_auth);
                $sht->getCellByColumnAndRow(13, $i)->setValue($p->transaction_id);
                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }

        //      output
        $this->outputExcel($excl, "payments");
    }

    /**
     * Export Accounts Payable/Receivable
     *
     * @param array $data
     */
    final private function export_accounts($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Accounts Payable Receivable');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Accounts Payable/Receivable");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Order date"
            , "Carrier"
            , "Shipper"
            , "Origin"
            , "Destination"
            , "Tariff"
            , "Carrier Pay"
            , "Pickup Terminal Fee"
            , "Drop-off Terminal Fee"
            , "Deposit"
            , "Profit"
            , "From Shipper"
            , "From Broker"
            , "From Carrier"
            , "From Pickup Terminal"
            , "From Drop-off Terminal"
            , "To Shipper"
            , "To Broker"
            , "To Carrier"
            , "To Pickup Terminal"
            , "To Drop-off Terminal",
        );
        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        $pm = new PaymentManager($this->daffny->DB);
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($o->id);
                $sht->getCellByColumnAndRow(1, $i)->setValue($o->getOrdered("m/d/Y h:i A"));
                $sht->getCellByColumnAndRow(2, $i)->setValue((trim($o->carrier_id) == "" ? "" : $o->getCarrier()->company_name));
                $sht->getCellByColumnAndRow(3, $i)->setValue((trim($o->shipper_id) == "" ? "" : $o->getShipper()->fname . " " . $o->getShipper()->lname));
                $sht->getCellByColumnAndRow(4, $i)->setValue($o->origin_id == "" ? "" : htmlspecialchars(formatAddress("", "", strtoupper($o->getOrigin()->city), $o->getOrigin()->state, $o->getOrigin()->zip)));
                $sht->getCellByColumnAndRow(5, $i)->setValue($o->destination_id == "" ? "" : htmlspecialchars(formatAddress("", "", strtoupper($o->getDestination()->city), $o->getDestination()->state, $o->getDestination()->zip)));
                $sht->getCellByColumnAndRow(6, $i)->setValue($o->getTotalTariff());
                $sht->getCellByColumnAndRow(7, $i)->setValue($o->getCarrierPay());
                $sht->getCellByColumnAndRow(8, $i)->setValue($o->getPickupTerminalFee());
                $sht->getCellByColumnAndRow(9, $i)->setValue($o->getDropoffTerminalFee());
                $sht->getCellByColumnAndRow(10, $i)->setValue($o->getTotalDeposit());
                $sht->getCellByColumnAndRow(11, $i)->setValue(number_format(($o->getTotalTariff(false) - $o->getCost(false)), 2));
                $sht->getCellByColumnAndRow(12, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_SHIPPER, null));
                $sht->getCellByColumnAndRow(13, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_COMPANY, null));
                $sht->getCellByColumnAndRow(14, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_CARRIER, null));
                $sht->getCellByColumnAndRow(15, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_TERMINAL_P, null));
                $sht->getCellByColumnAndRow(16, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, Payment::SBJ_TERMINAL_D, null));
                $sht->getCellByColumnAndRow(17, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_SHIPPER));
                $sht->getCellByColumnAndRow(18, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_COMPANY));
                $sht->getCellByColumnAndRow(19, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_CARRIER));
                $sht->getCellByColumnAndRow(20, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_TERMINAL_P));
                $sht->getCellByColumnAndRow(21, $i)->setValue($pm->getFilteredPaymentsTotals($o->id, null, Payment::SBJ_TERMINAL_D));

                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "payable_receivable");
    }

    /**
     * Export Orders
     *
     * @param array $data
     */
    final private function export_ontime($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle("On-Time Load-Delivery");

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("On-Time Load/Delivery Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Shipper"
            , "Vehicles"
            , "Origin City"
            , "Destination City"
            , "Est. Load date"
            , "Actual Pick up date"
            , "Deviation (+/- days)"
            , "Est. Delivery date"
            , "Actual Delivery date"
        );
        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($o->getNumber());
                $sht->getCellByColumnAndRow(1, $i)->setValue(trim($o->shipper_id) == "" ? "" : $o->getShipper()->fname . " " . $o->getShipper()->lname);
                $sht->getCellByColumnAndRow(2, $i)->setValue($o->printVehicles(false));
                $sht->getCellByColumnAndRow(3, $i)->setValue($o->origin_id == "" ? "" : strtoupper($o->getOrigin()->city) . " " . $o->getOrigin()->state . " " . $o->getOrigin()->zip);
                $sht->getCellByColumnAndRow(4, $i)->setValue($o->destination_id == "" ? "" : strtoupper($o->getDestination()->city) . " " . $o->getDestination()->state . " " . $o->getDestination()->zip);
                $sht->getCellByColumnAndRow(5, $i)->setValue($o->getEstLoadDate("m/d/Y"));
                $sht->getCellByColumnAndRow(6, $i)->setValue($o->getActualPickUpDate("m/d/Y"));
                $sht->getCellByColumnAndRow(7, $i)->setValue($o->getPickUpDeviation("m/d/Y"));
                $sht->getCellByColumnAndRow(8, $i)->setValue($o->getEstDeliveryDate("m/d/Y"));
                $sht->getCellByColumnAndRow(9, $i)->setValue($o->getActualDeliveryDate("m/d/Y"));
                $sht->getCellByColumnAndRow(10, $i)->setValue($o->getDeliveryDeviation("m/d/Y"));

                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "load_delivery");
    }

    /**
     * Print Logo
     *
     * @param object $sheet - Active Sheet
     */
    final private function printLogo($sheet)
    {
        $iDrowing = new PHPExcel_Worksheet_Drawing();
        $iDrowing->setPath(ROOT_PATH . 'images/logo_excel.png');
        $iDrowing->setCoordinates('I2');
        $iDrowing->setOffsetY(-10);
        $iDrowing->setOffsetX(-20);
        $iDrowing->setWorksheet($sheet);
    }

    /**
     * Output Excel file
     * @param object $excl - Excel O
     * @param String $name - Report name
     */
    final private function outputExcel($excl, $name)
    {
        $objWriter = new PHPExcel_Writer_Excel5($excl);
        $rel_path = 'temp/' . $name . '-' . $_SESSION['member']['id'] . '.' . 'xls';
        $filepath = __DIR__ . '/../../' . $rel_path;
        unlink($filepath);
        $objWriter->save($filepath);
        $excl->disconnectWorksheets();
        echo $rel_path;
        unset($excl);
        unset($objWriter);
        exit();
    }

    /**
     * Commission report
     * Slide 34
     * (Order ID, Order date, Delivery date, Description, Shipper, Carrier, Status (New, Posted to Board, Dispatched – Not Signed, Dispatched – Signed, Picked Up, Delivered, Assumed Delivered, Cancelled), Reimbursable (Yes/No), Assigned To, Cost (=Carrier Pay + Terminal Fee), Total Price (=Tariff).
     * This report can be additional filtered by Order ID and Shipper and also exclude all cancelled orders.
     */
    public function commission()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Commission"));
        $this->tplname = "reports.commission.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }
        //      set defaults for search fields
        $start_date = date("Y-m-d 00:00:00");
        $end_date = date("Y-m-d 23:59:59");
        $users_ids = array();
        if (!isset($_SESSION["reports"])) {
            $_SESSION["reports"] = "";
            $_SESSION["users_ids"] = array();
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;

        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["reports"] = trim(post_var("reports"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "reports" => $_SESSION["reports"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["reports"] = $_SESSION["reports"];
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];
        $report_arr["report_option"] = $_SESSION["ptype"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        if (!count($this->err)) {
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getCommissionReport(null, $_SESSION['per_page'], $report_arr, getParentId());

            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getCommissionReport(null, null, $report_arr, getParentId());
                $this->export_commission($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $reportsArr = array("Commission",
            "Receivables",
            "Dispatched",
            "Schedule Pickup",
            "Schedule Delivery");
        $this->form->ComboBox("reports", $reportsArr, array("id" => "reports", "style" => ""), "Report", "</td><td td colspan=\"3\">");
        $this->form->helperMLTPL("users_ids[]", $this->getUsers(), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Ship Via", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Export Orders
     *
     * @param array $data
     */
    final private function export_commission($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Orders');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Orders Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 1;

        $titles = array(
            "Order ID"
            , "Total"
            , "Deposite"
            , "Created"
            , "User"
            , "%Commission"
            , "Commission"
            , "Type"
        );
        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            $totalValue = 0;
            $depositeValue = 0;
            $commissionValue = 0;
            foreach ($data as $commission) {
                $i++;

                $totalValue += $commission['total_tariff_stored'];
                $depositeValue += $commission['deposit'];
                $commission_got_amount += $commission['commission_got_amount'];

                $sht->getCellByColumnAndRow(0, $i)->setValue($commission['number']);
                $sht->getCellByColumnAndRow(1, $i)->setValue("$" . number_format($commission['total_tariff_stored'], 2, ".", ""));
                $sht->getCellByColumnAndRow(2, $i)->setValue("$" . $commission['deposit']);
                $sht->getCellByColumnAndRow(3, $i)->setValue(date("m/d/y", strtotime($commission['created'])));

                if ($commission['created_assigned'] == 2) { // ASSIGN
                    $sht->getCellByColumnAndRow(4, $i)->setValue($commission['assign_name'] . "%");
                    $sht->getCellByColumnAndRow(5, $i)->setValue("" . number_format($commission['commission'], 2, ".", "") . "%");
                    $sht->getCellByColumnAndRow(6, $i)->setValue("$" . number_format($commission['commission_got_amount'], 2, ".", ""));
                    $sht->getCellByColumnAndRow(7, $i)->setValue("Assigned");

                } elseif ($commission['created_assigned'] == 1) {
                    $sht->getCellByColumnAndRow(4, $i)->setValue($commission['creator_name'] . "%");
                    $sht->getCellByColumnAndRow(5, $i)->setValue("" . number_format($commission['commission_got'], 2, ".", "") . "%");
                    $sht->getCellByColumnAndRow(6, $i)->setValue("$" . number_format($commission['commission_got_amount'], 2, ".", ""));
                    $sht->getCellByColumnAndRow(7, $i)->setValue("Created");

                }

                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "commission");
    }

    public function shipperpayments()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Shippers"));
        $this->tplname = "reports.shippers.paymenthistory";

        $is_export = false;

        if (!isset($_SESSION["ptype"])) {
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["ptype"] == 1;
        }
        if (isset($_POST['submit'])) {
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));

            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
            //if is export
            if (isset($_POST["export"]) || isset($_POST["export_x"])) {
                $is_export = true;
            }
        }

        $cust_name = "";

        if ($_SESSION["customers_name"] != "") {
            $cust_name = mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION["customers_name"]);
        }

        //      Collect data in array
        $search_arr = array(
            "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"],
        );

        //prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        //      prepare search conditions for query
        $report_arr = array();
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];

        $this->daffny->tpl->start_date = $_SESSION["start_date"];
        $this->daffny->tpl->end_date = $_SESSION["end_date"];
        if (!$is_export) {

            $this->applyOrder(Account::TABLE);
            $this->order->setDefault('id', 'desc');
            $accountManager = new AccountManager($this->daffny->DB);
            $this->daffny->tpl->shippers = $accountManager->getShipperPaymentHistoryReports($this->order->getOrder(), $_SESSION['per_page'], $cust_name, $report_arr, getParentId());
            $this->setPager($accountManager->getPager());
        } else {
            $accountManager = new AccountManager($this->daffny->DB);
            $data = $accountManager->getShipperPaymentHistoryReports(null, null, $cust_name, $report_arr, getParentId());
            $this->export_shippersPaymentHistory($data, $_SESSION["start_date"], $_SESSION["end_date"]);
        }

        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Build Excel Report (Shippers)
     *
     * @param array $data
     */
    final private function export_shippersPaymentHistory($data, $start_date, $end_date)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Shippers Payments History');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Shippers Payments History Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(0, 2)->setValue("Start Date:");
        $sht->getCellByColumnAndRow(0, 3)->setValue("End Date:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(1, 2)->setValue(date("m/d/Y", strtotime($start_date)));
        $sht->getCellByColumnAndRow(1, 3)->setValue(date("m/d/Y", strtotime($end_date)));
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 7;
        $sht->getCellByColumnAndRow(0, $i)->setValue("ID");
        $sht->getCellByColumnAndRow(1, $i)->setValue("Shipper");
        $sht->getCellByColumnAndRow(2, $i)->setValue("Tariff");
        $sht->getCellByColumnAndRow(3, $i)->setValue("Carrier Pay");
        $sht->getCellByColumnAndRow(4, $i)->setValue("Deposits");
        $sht->getCellByColumnAndRow(5, $i)->setValue("PaidShipperAmount");
        $sht->getCellByColumnAndRow(6, $i)->setValue("LastOrderDate");
        $sht->getCellByColumnAndRow(7, $i)->setValue("LastPaidDate");
        $sht->getCellByColumnAndRow(8, $i)->setValue("LastInvoiceDate");
        $sht->getCellByColumnAndRow(9, $i)->setValue("orderCount");
        $sht->getCellByColumnAndRow(10, $i)->setValue("TotalofDaysToPay");
        $sht->getCellByColumnAndRow(11, $i)->setValue("AvgDayToPay");

        //apply format for header
        for ($j = 0; $j <= 11; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //logo
        $this->printLogo($excl->getActiveSheet());

        //build sales grid
        if (count($data) > 0) {
            foreach ($data as $s => $l) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($l["account_id"]);
                $sht->getCellByColumnAndRow(1, $i)->setValue($l['shipperfname'] . " " . $l['shipperlname'] . " ,\n" . $l['shippercompany']);
                $sht->getCellByColumnAndRow(2, $i)->setValue("$" . $l["total_tariff"]);
                $sht->getCellByColumnAndRow(3, $i)->setValue("$" . $l["total_carrier_pay"]);
                $sht->getCellByColumnAndRow(4, $i)->setValue("$" . $l["total_deposite"]);
                $sht->getCellByColumnAndRow(5, $i)->setValue($l["PaidShipperAmount"]);

                $sht->getCellByColumnAndRow(6, $i)->setValue($l["LastOrderDate"]);
                $sht->getCellByColumnAndRow(7, $i)->setValue($l["LastPaidDate"]);
                $sht->getCellByColumnAndRow(8, $i)->setValue($l["LastInvoiceDate"]);
                $sht->getCellByColumnAndRow(9, $i)->setValue($l["orderCount"]);
                $sht->getCellByColumnAndRow(10, $i)->setValue($l["TotalofDaysToPay"]);
                $sht->getCellByColumnAndRow(11, $i)->setValue($l["AvgDayToPay"]);
                //apply format for grid body
                for ($k = 0; $k <= 11; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //output
        $this->outputExcel($excl, "shippers_payment_history");
    }

    /**
     * QuickBooks Log report
     * Added by Chetu Inc
     * Dispay the logs inofmration form the QuickBooks queue table
     * This report can be additional filtered by status.
     */
    public function qblogs()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "QuickBooks Logs"));
        $this->tplname = "reports.qblogs.show";
        //Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["status_id"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["status_id"] = $_SESSION["status_id"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);

        if (isset($_GET['order']) && isset($_GET['arrow'])) {
            $order = "ORDER BY " . $_GET['order'] . " " . $_GET['arrow'];
        } else {
            $order = "ORDER BY quickbooks_queue_id DESC";
        }

        if (!count($this->err)) {

            $entityManager = new EntityManager($this->daffny->DB);
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->qblogs = $entityManager->qbQueue($order, $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {

                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->qblogs = $entityManager->qbQueue(null, null, $report_arr, getParentId());
                $this->exportQueue($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->ComboBox("status_id", array("" => "-- All --", "q" => "Queue", "s" => "Success", "e" => "Error"), array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", array("5" => "-- All --", "1" => "Current Month", "2" => "Previous Month"), array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }
    
    public function dispatchActivity()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('reports') => 'Reports', '' => 'Dispatch Activity'));
        $this->tplname = 'reports.dispatch.show';

        // Is Export?
        $is_export = false;
        if (isset($_POST['export']) || isset($_POST['export_x'])) {
            $is_export = true;
        }
        // set defaults for search fields
        $users_ids = array();
        if (!isset($_SESSION['ship_via'])) {
            $_SESSION['ship_via'] = '';
            $_SESSION['order_id'] = '';
            $_SESSION['email'] = '';
            $_SESSION['status_id'] = '';
            $_SESSION['phone'] = '';
            $_SESSION['time_period'] = '';
            $_SESSION['start_date'] = date('d/m/Y');
            $_SESSION['end_date'] = date('d/m/Y');
            $_SESSION['start_date2'] = date('d/m/Y');
            $_SESSION['end_date2'] = date('d/m/Y');
            $_SESSION['ptype'] = 1;
            $_SESSION['users_ids'] = array();
        }

        if (isset($_POST['submit']) || $is_export) {
            //Write in session for paginations and orders
            $_SESSION['ship_via'] = trim(post_var('ship_via'));
            $_SESSION['order_id'] = trim(post_var('order_id'));
            $_SESSION['email'] = trim(post_var('email'));
            $_SESSION['phone'] = trim(post_var('phone'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['users_ids'] = post_var('users_ids');
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));
            $_SESSION['status_id'] = trim(post_var('status_id'));

            //Check dates
            if ($_SESSION['ptype'] == 1) {
                $tp = $this->getTimePeriod(post_var('time_period'));
                $_SESSION['start_date'] = $tp[0];
                $_SESSION['end_date'] = $tp[1];
            }

            if ($_SESSION['ptype'] == 2) {
                $this->isEmpty('start_date', 'Start Date');
                $this->isEmpty('end_date', 'End Date');
                $_SESSION['start_date'] = $this->validateDate(post_var('start_date'), 'Start Date').' 00:00:00';
                $_SESSION['end_date'] = $this->validateDate(post_var('end_date'), 'End Date').' 23:59:59';
            }
        }

        // Collect data in array
        $search_arr = array(
            'ship_via' => $_SESSION['ship_via'], 'order_id' => $_SESSION['order_id'], 'email' => $_SESSION['email'], 'phone' => $_SESSION['phone'], 'time_period' => $_SESSION['time_period'], 'start_date' => $_SESSION['start_date2'], 'end_date' => $_SESSION['end_date2'], 'ptype' => $_SESSION['ptype'], 'status_id' => $_SESSION['status_id'],
        );

        // prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr['start_date'] = $_SESSION['start_date'];
        $report_arr['end_date'] = $_SESSION['end_date'];
        $report_arr['users_ids'] = $_SESSION['users_ids'];
        $report_arr['status_id'] = $_SESSION['status_id'];

        // Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');

        if (!count($this->err)) {
            if (!$is_export) {
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getDispatchReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getDispatchReport(null, null, $report_arr, getParentId());
                $this->exportDispatch($data);
            }
        }

        // Counts data
        $entity_data = $entityManager->getDispatchReport(null, null, $report_arr, getParentId());
        $entities = array();
        foreach($entity_data as $o){
            $entities[] = $o->id;
        }
        $entities = implode(",",$entities);
        
        $qry = "SELECT SUM(amount) as Amount FROM app_payments WHERE fromid IN (1,2) AND toid = 3 AND Void = 0 AND entity_id IN (".$entities.")";
        $resp = $this->daffny->DB->query($qry);
        $this->daffny->tpl->amount_received = mysqli_fetch_assoc($resp)['Amount'];

        $qry = "SELECT count(*) as `Number` FROM app_payments WHERE fromid IN (1,2) AND toid = 3 AND Void = 0 AND entity_id IN (".$entities.")";
        $resp = $this->daffny->DB->query($qry);
        $this->daffny->tpl->payment_received = mysqli_fetch_assoc($resp)['Number'];

        $qry = "SELECT count(*) as `Number` FROM Invoices WHERE Void = 0 AND Paid = 0 AND EntityID IN (".$entities.")";
        $resp = $this->daffny->DB->query($qry);
        $this->daffny->tpl->un_paid = mysqli_fetch_assoc($resp)['Number'];

        $qry = "SELECT SUM(amount) as Amount FROM app_payments WHERE fromid IN (2) AND toid = 1 AND entity_id IN (" .$entities.")";
        $resp = $this->daffny->DB->query($qry);
        $this->daffny->tpl->amount_sent = mysqli_fetch_assoc($resp)['Amount'];

        $qry = "SELECT count(*) as `Number` FROM app_payments WHERE fromid IN (2) AND toid = 1 AND entity_id IN (" .$entities.")";
        $resp = $this->daffny->DB->query($qry);
        $this->daffny->tpl->payment_sent = mysqli_fetch_assoc($resp)['Number'];

        $unpaid_invoices = 0;
        foreach($entity_data as $o){
            $qry = "SELECT count(*) as `Number` FROM app_payments WHERE fromid IN (2) AND toid = 1 AND entity_id = " .$o->id."";
            $resp = $this->daffny->DB->query($qry);

            if( mysqli_fetch_assoc($resp)['Number'] == 0 ){
                $unpaid_invoices = $unpaid_invoices + 1;
            }
        }
        
        $this->daffny->tpl->unpaid_invoices = $unpaid_invoices;

        // prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION['users_ids'];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL('users_ids[]', $this->getUsersByStatus(" status ='Active' "), $users_ids, array('id' => 'users_ids', 'multiple' => 'multiple'), 'User', '</td><td colspan="3">');
        $this->form->ComboBox('time_period', $this->time_periods, array('style' => 'width:150px;'), '', '');
        $this->form->DateField('start_date', 10, array(), '', '');
        $this->form->DateField('end_date', 10, array(), '', '');

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = 'checked="checked"';
            $this->input['ptype1ch'] = '';
        } else {
            $this->input['ptype1ch'] = 'checked="checked"';
            $this->input['ptype2ch'] = '';
        }
    }

    /**
     * Export Review
     * Chetu Added function
     * @param array $data
     */
    final private function exportReview($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Customer Reviews');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Customer Review Report");
        $sht->getCellByColumnAndRow(0, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(0, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(2, 4)->setValue(date("m/d/Y h:i:s a", strtotime(date("Y-m-d H:i:s"))));
        $sht->getCellByColumnAndRow(2, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Assigned To"
            , "Order Rating"
            , "Order Comment"
            , "Carrier Rating"
            , "Carreir Comment"
            , "Rated At"
            , "Carrier Information",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;
                $sht->getCellByColumnAndRow(0, $i)->setValue($o->orderId);
                $sht->getCellByColumnAndRow(1, $i)->setValue($o->assignedName);
                $sht->getCellByColumnAndRow(2, $i)->setValue($o->ratings);
                $sht->getCellByColumnAndRow(3, $i)->setValue($o->comment);
                $sht->getStyleByColumnAndRow(3, $i)->getAlignment()->setWrapText(true);

                $sht->getCellByColumnAndRow(4, $i)->setValue($o->car_rating);

                $sht->getCellByColumnAndRow(5, $i)->setValue($o->car_comment);
                $sht->getStyleByColumnAndRow(5, $i)->getAlignment()->setWrapText(true);

                $sht->getCellByColumnAndRow(6, $i)->setValue(date("m/d/Y h:i:s a", strtotime($o->created_at)));
                $sht->getCellByColumnAndRow(7, $i)->setValue($o->company_name . ", " . $o->carrierName . ", " . $o->phone1 . ", " . $o->carrierEmail);
                // apply format for grid body
                for ($k = 0; $k < $m; $k++) {
                    $sht->getStyleByColumnAndRow($k, $i)->applyFromArray($this->lineFont);
                }
            }
        }
        //      output
        $this->outputExcel($excl, "reviewReport");
    }

    /**
     * Feedback review report
     * Added by Chetu Inc
     * Dispay the logs inofmration form the customer feeback review table
     * This report can be additional filtered by status.
     */
    public function review()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Review"));
        $this->tplname = "reports.review.show";

        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["status_id"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("m/d/Y");
            $_SESSION["end_date"] = date("m/d/Y");
            $_SESSION["start_date2"] = date("m/d/Y");
            $_SESSION["end_date2"] = date("m/d/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {
            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["status_id"] = $_SESSION["status_id"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);

        if (isset($_GET['order']) && isset($_GET['arrow'])) {
            $order = "ORDER BY " . $_GET['order'] . " " . $_GET['arrow'];
        } else {
            $order = "ORDER BY id DESC";
        }

        if (!count($this->err)) {
            $entityManager = new EntityManager($this->daffny->DB);
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->review = $entityManager->getReviewReport($order, $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {

                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->review = $entityManager->getReviewReport(null, null, $report_arr, getParentId());
                $this->exportReview($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->ComboBox("status_id", array("" => "-- All --", "q" => "Queue", "s" => "Success", "e" => "Error"), array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", array("5" => "-- All --", "1" => "Current Month", "2" => "Previous Month"), array("style" => ""), "", "");

        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Functionality to get Print Checks based on filters
     *
     * @return void
     * @author shahrukhusmaani@live.com
     * @version 1.0
     */
    public function print_check_report()
    {

        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Print Checks Report"));
        $this->tplname = "reports.print_check.show";

        // Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        if (isset($_POST['submit']) || $is_export) {
            // Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));

            // Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }

        }

        // Collect data in array
        $search_arr = array(
            "start_date" => $_SESSION["start_date2"],
            "end_date" => $_SESSION["end_date2"],
        );

        // Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"],
        );

        // prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];

        // Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder('app_payments_check');
        $this->order->setDefault('id', 'desc');

        if (!count($this->err)) {
            if (!$is_export) {
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->get_print_check_report($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->daffny->tpl->accuont_number = $_POST['account_number'];
                $this->setPager($entityManager->getPager());
            } else {
                if ($_POST['exportType'] == 1) {
                    $entityManager = new EntityManager($this->daffny->DB);
                    $data = $this->daffny->tpl->orders = $entityManager->get_print_check_report(null, null, $report_arr, getParentId());
                    $this->export_check_report_to_excel($data, $_POST['account_number']);
                } else {

                    // generate csv file
                    $entityManager = new EntityManager($this->daffny->DB);
                    $data = $this->daffny->tpl->orders = $entityManager->get_print_check_report(null, null, $report_arr, getParentId());

                    if ($_POST['account_number'] == null) {
                        $account = "";
                    } else {
                        $acount = $_POST['account_number'];
                    }

                    $file_name = '../uploads/reports/princt_check_report' . date('ymdhis') . '.csv';

                    file_put_contents($file_name, 'I,Account Number, Date Issue, Check Number, Amount, Print Name' . PHP_EOL, FILE_APPEND | LOCK_EX);
                    if (count($data) > 0) {
                        foreach ($data as $o) {
                            $line = $o->id . "," . $account . "," . $o->created . "," . $o->check_number . "," . $o->amount . "," . $o->print_name;
                            file_put_contents($file_name, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
                            redirect(getLink('uploads', 'reports', $file_name));
                        }
                    }
                }
            }
        }

        // prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("account_number", 100, array(), "", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    /**
     * Function to export the print check report in excel
     *
     * @param $data Array Data containing object array
     * @version 1.0
     * @author shahrukhusmaani@live.com
     */
    public function export_check_report_to_excel($data, $account = null)
    {
        if ($account == null) {
            $account = "";
        }
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);

        $sht = $excl->getActiveSheet();
        $sht->setTitle('Print Check Report');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Print Check Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);

        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 7;
        $titles = array(
            "I"
            , "Account Number"
            , "Date Issue"
            , "Check Number"
            , "Amount"
            , "Print Name",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }

        //apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }

        //logo
        $this->printLogo($excl->getActiveSheet());

        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;
                $d = array(
                    $o->id,
                    $account,
                    $o->created,
                    $o->check_number,
                    $o->amount,
                    $o->print_name,
                );
                $sht->fromArray($d, null, 'A' . $i);
            }
        }

        $this->outputExcel($excl, "print_check_report");
    }

    /* !#chetu */
    public function arReport()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Orders"));
        $this->tplname = "reports.arReport.show2";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }
        
        $users_ids = array();
        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["status_id"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
            $_SESSION["status"] = array(0 => 6, 1 => 7, 2 => 8);
            $_SESSION["reportType"] = 'ar';
            $_SESSION["groupBy"] = '';
        }

        if (isset($_POST['submit']) || $is_export) {

            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));
            $_SESSION["status"] = post_var("status");
            $_SESSION["reportType"] = post_var("reportType");
            $_SESSION["groupBy"] = post_var("groupBy");
            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"]
            , "status" => $_SESSION["status"]
            , "reportType" => $_SESSION["reportType"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];
        $report_arr["status_id"] = $_SESSION["status_id"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');
        if (!count($this->err)) {
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getArReport($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getArReport(null, null, $report_arr, getParentId());
                $this->export_ar($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus("status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("status_id", array("" => "-- All --", "6" => "Dispatched", "7" => "Issues", "8" => "Picked Up", "9" => "Delivered"), array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Search", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    final private function export_ar($data)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('AR Repost');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Accounts Recievable Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 7;
        $titles = array(
            "Order ID"
            , "Date"
            , "Account Id"
            , "First Name"
            , "Last Name"
            , "Company"
            , "Aging"
            , "Open Balance",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //      apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //      logo
        $this->printLogo($excl->getActiveSheet());

        //      build sales grid
        if (count($data) > 0) {
            $netAmount = 0;
            $paymentManager = new PaymentManager($this->daffny->DB);
            foreach ($data as $o) {

                $openBalance = 0;
                $orderId = ($o->prefix) ? $id = $o->prefix . "-" . $o->number : $id = $o->number;
                $account_id = $o->account_id;
                $first_name = $o->getAccountCustom(false, 'first_name');
                $last_name = $o->getAccountCustom(false, 'last_name');
                $company_name = $o->getAccountCustom(false, 'company_name');
                if ($o->status == 7) {
                    $date1 = date('Y-m-d');
                    $date2 = $o->delivered;
                    $datetime1 = strtotime($date2);
                    $datetime2 = strtotime($date1);
                    $secs = $datetime2 - $datetime1;
                    $age = floor($secs / 86400);
                } else {
                    $age = "";
                }

                $total = $o->total_tariff_stored;
                $carrier_pay = $o->carrier_pay_stored;
                $deposit = $total - $carrier_pay;
                $reportType = $_SESSION['reportType'];

                switch ($o->balance_paid_by) {
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
                    case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:
                        $carrierPaid = $paymentManager->getFilteredPaymentsTotals($o->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
                        $shipperPaid = $paymentManager->getFilteredPaymentsTotals($o->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
                        ///$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_SHIPPER, false);
                        $balances['they_carrier'] = 0;
                        $balances['we_shipper'] = 0;
                        $balances['we_carrier'] = $o->getCarrierPay(false) + $o->getPickupTerminalFee(false) + $o->getDropoffTerminalFee(false) - $carrierPaid;
                        $balances['we_carrier_paid'] = $carrierPaid;
                        $balances['they_shipper'] = $o->getCost(false) + $o->getTotalDeposit(false) - $shipperPaid;
                        $balances['they_shipper_paid'] = $shipperPaid;

                        $carrierRemains = $o->getCarrierPay(false) + $o->getPickupTerminalFee(false) + $o->getDropoffTerminalFee(false) - $carrierPaid;
                        $depositRemains = $o->getTotalDeposit(false) - $shipperPaid;
                        $shipperRemains = $o->getCost(false) + $o->getTotalDeposit(false) - $shipperPaid;
                        $amountType = 2;
                        if ($reportType == 'ar') {
                            $company_name = $o->getAccountCustom(false, 'company_name');
                            $openBalance = "$" . $shipperRemains;
                        } else {
                            $company_name = $o->getCarrier()->company_name;
                            $openBalance = $balances['we_carrier'] - $balances['they_shipper'];
                        }

                        break;
                    default:
                        break;
                }
                if ($o->balance_paid_by == 2 || $o->balance_paid_by == 3 || $o->balance_paid_by == 8 || $o->balance_paid_by == 9) {
                    $openBalance = $o->getDepositDue();
                }
                $i++;
                $d = array(
                    $orderId,
                    date("m/d/y h:i a", strtotime($o->dispatched)),
                    $account_id,
                    $first_name,
                    $last_name,
                    $company_name,
                    $age,
                    $openBalance,
                );

                $netAmount = $netAmount + $openBalance;
                $sht->fromArray($d, null, 'A' . $i);

            }

            $sht->getCellByColumnAndRow(0, $i + 1)->setValue("Total:");
            $sht->getCellByColumnAndRow(7, $i + 1)->setValue($netAmount);
        }
        //      output
        if ($_SESSION['reportType'] == 'ar') {
            $this->outputExcel($excl, "ar_reports");
        } else {
            $this->outputExcel($excl, "ap_reports");
        }

    }

    public function payment_received()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Orders"));
        $this->tplname = "reports.payment_received.show";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }
        
        $users_ids = array();
        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["status_id"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("m/d/Y");
            $_SESSION["end_date"] = date("m/d/Y");
            $_SESSION["start_date2"] = date("m/d/Y");
            $_SESSION["end_date2"] = date("m/d/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
            $_SESSION["status"] = array(0 => 6, 1 => 7, 2 => 8);
            $_SESSION["reportType"] = 'ar';
            $_SESSION["groupBy"] = '';
        }
        
        if (isset($_POST['submit']) || $is_export) {

            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["status_id"] = trim(post_var("status_id"));
            $_SESSION["status"] = post_var("status");
            $_SESSION["reportType"] = post_var("reportType");
            $_SESSION["groupBy"] = post_var("groupBy");
            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
            , "status_id" => $_SESSION["status_id"]
            , "status" => $_SESSION["status"]
            , "reportType" => $_SESSION["reportType"],
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];
        $report_arr["status_id"] = $_SESSION["status_id"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');
        if (!count($this->err)) {
            if (!$is_export) {

                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->get_payment_received_report($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->get_payment_received_report(null, null, $report_arr, getParentId());
                $this->export_payment_received($data);
            }
        }

        //      prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus("status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->ComboBox("status_id", array("" => "-- All --", "6" => "Dispatched", "7" => "Issues", "8" => "Picked Up", "9" => "Delivered"), array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->TextField("start_date", 10, array(), "", "");
        $this->form->TextField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Search", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    final private function export_payment_received($data)
    {
        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Payment Received Report');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Payment Received Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        $i = 7;
        $titles = ["OrderID","Date","Company/Customer Name","Agent","Type","Amount Charged","Deposit","Balance","Carrier Pay","Carrier Balance","Method"];

        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }

        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }

        //$this->printLogo($excl->getActiveSheet());

        if (count($data) > 0) {

            $methods = [
                1 => 'Personal Check',
                2 => 'Company Check',
                3 => 'Cashier Check',
                4 => 'Comcheck',
                5 => 'Cash',
                6 => 'Electronic Transfer',
                7 => 'Other',
                8 => 'Money Order',
                9 => 'Credit Card',
                999 => 'Debit',
                998 => 'Credit'
            ];

            $balance_paid_by = [
                2 => 'COD - Cash/Certified Funds',
                3 => 'COD - Check',
                8 => 'COP - Cash/Certified Funds',
                9 => 'COP - Check',
                12 => 'Billing - Cash/Certified Funds',
                13 => 'Billing - Check',
                20 => 'Billing - Comcheck',
                21 => 'Billing - QuickPay'
            ];

            $data_record = [];

            foreach ($data as $ind => $p) {
                $data_record[$p['payment_data']['entity_id']][$p['payment_data']['number']] = [
                    'order_id' => $p['payment_data']['order_id'],
                    'amount' => $p['payment_data']['amount'],
                    'date_received' => $p['payment_data']['date_received'],
                    'fromid' => $p['payment_data']['fromid'],
                    'toid' => $p['payment_data']['toid'],
                    'method' => $p['payment_data']['method'],
                    'number' => $p['payment_data']['number'],
                    'order_id' => $p['data']['order_id'],
                    'assigned_name' => $p['data']['assigned_name'],
                    'shipper' => $p['data']['shipper'],
                    'deposit' => $p['data']['deposit'],
                    'carrier_pay' => $p['data']['carrier_pay'],
                    'balance_paid_by' => $p['data']['balance_paid_by'],
                ];
            }

            foreach ($data_record as $k => $p) {
                if(count($p) > 1){
                    $deposit = 0;
                    $c_pay = 0;

                    foreach ($p as $k => $v) {
                        $deposit = str_replace(",","",substr($v['deposit'], 2));
                        $c_pay = str_replace(",","",substr($v['carrier_pay'], 2));
                    }

                    $index = 0;

                    foreach ($p as $k => $v) {

                        $i++;

                        $amount = $v['amount'];
                        $balance = 0;
                        $c_balance = 0;

                        if($amount > $deposit){
                            $balance = 0;
                        } else {
                            $balance = $deposit - $amount;
                        }

                        if(!in_array($v['balance_paid_by'], [2,3,8,9])){
                            // when billing

                            if($index == 0){
                                $c_balance = $c_pay - ($amount - $deposit);
                            } else {
                                if($balance != 0){
                                    $c_balance = $c_pay - ($amount - $deposit);
                                } else {
                                    $c_balance = $c_pay - $amount;
                                }
                            }
                            
                            if($c_balance < 0){
                                $c_balance = 0;
                            }
                        }

                        $index++;

                        $d = [
                            $v['order_id'],
                            date('m-d-Y', strtotime($v['date_received'])),
                            $v['shipper'],
                            $v['assigned_name'],
                            $balance_paid_by[$v['balance_paid_by']],
                            $amount,
                            $deposit,
                            $balance,
                            $c_pay,
                            $c_balance,
                            $methods[$v['method']]
                        ];
                        $sht->fromArray($d, null, 'A' . $i);
                    }
                } else {
                    foreach ($p as $k => $v) {
                        $i++;

                        $amount = $v['amount'];
                        $deposit = str_replace(",","",substr($v['deposit'], 2));
                        $c_pay = str_replace(",","",substr($v['carrier_pay'], 2));
                        $balance = 0;
                        $c_balance = 0;

                        if($amount > $deposit){
                            $balance = 0;
                        } else {
                            $balance = $deposit - $amount;
                        }

                        if(in_array($v['balance_paid_by'], [2,3,8,9])){
                            // nothing when COD
                        } else {
                            // when billing
                            $c_balance = $c_pay - ($amount - $deposit);
                            if($c_balance < 0){
                                $c_balance = 0;
                            }
                        }
                        
                        $d = [
                            $v['order_id'],
                            date('m-d-Y', strtotime($v['date_received'])),
                            $v['shipper'],
                            $v['assigned_name'],
                            $balance_paid_by[$v['balance_paid_by']],
                            $amount,
                            $deposit,
                            $balance,
                            $c_pay,
                            $c_balance,
                            $methods[$v['method']]
                        ];
                        $sht->fromArray($d, null, 'A' . $i);
                    }
                }
            }
        }

        $this->outputExcel($excl, "Payment-Received-Report | ".date('m-d-y h m A'));
    }

    public function cancelled_orders()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("reports") => "Reports", "" => "Orders"));
        $this->tplname = "reports.cancelled_orders.view";

        //      Is Export?
        $is_export = false;
        if (isset($_POST["export"]) || isset($_POST["export_x"])) {
            $is_export = true;
        }

        $users_ids = array();
        if (!isset($_SESSION["ship_via"])) {
            $_SESSION["ship_via"] = "";
            $_SESSION["order_id"] = "";
            $_SESSION["email"] = "";
            $_SESSION["phone"] = "";
            $_SESSION["time_period"] = "";
            $_SESSION["start_date"] = date("d/m/Y");
            $_SESSION["end_date"] = date("d/m/Y");
            $_SESSION["start_date2"] = date("d/m/Y");
            $_SESSION["end_date2"] = date("d/m/Y");
            $_SESSION["ptype"] = 1;
            $_SESSION["users_ids"] = array();
        }

        if (isset($_POST['submit']) || $is_export) {

            //          Write in session for paginations and orders
            $_SESSION["ship_via"] = trim(post_var("ship_via"));
            $_SESSION["order_id"] = trim(post_var("order_id"));
            $_SESSION["email"] = trim(post_var("email"));
            $_SESSION["phone"] = trim(post_var("phone"));
            $_SESSION["time_period"] = trim(post_var("time_period"));
            $_SESSION["start_date"] = trim(post_var("start_date"));
            $_SESSION["end_date"] = trim(post_var("end_date"));
            $_SESSION["ptype"] = trim(post_var("ptype"));
            $_SESSION["users_ids"] = post_var("users_ids");
            $_SESSION["start_date2"] = trim(post_var("start_date"));
            $_SESSION["end_date2"] = trim(post_var("end_date"));
            $_SESSION["referred_by"] = post_var("referred_by");
            $_SESSION["source_name"] = post_var("source_name");

            //          Check dates
            if ($_SESSION["ptype"] == 1) {
                $tp = $this->getTimePeriod(post_var("time_period"));
                $_SESSION["start_date"] = $tp[0];
                $_SESSION["end_date"] = $tp[1];
            }

            if ($_SESSION["ptype"] == 2) {
                $this->isEmpty("start_date", "Start Date");
                $this->isEmpty("end_date", "End Date");
                $_SESSION["start_date"] = $this->validateDate(post_var("start_date"), "Start Date") . " 00:00:00";
                $_SESSION["end_date"] = $this->validateDate(post_var("end_date"), "End Date") . " 23:59:59";
            }
        }
        //      Collect data in array
        $search_arr = array(
            "ship_via" => $_SESSION["ship_via"]
            , "order_id" => $_SESSION["order_id"]
            , "email" => $_SESSION["email"]
            , "phone" => $_SESSION["phone"]
            , "time_period" => $_SESSION["time_period"]
            , "start_date" => $_SESSION["start_date2"]
            , "end_date" => $_SESSION["end_date2"]
            , "ptype" => $_SESSION["ptype"]
        );

        //      prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr["start_date"] = $_SESSION["start_date"];
        $report_arr["end_date"] = $_SESSION["end_date"];
        $report_arr["users_ids"] = $_SESSION["users_ids"];
        $report_arr["status_id"] = $_SESSION["status_id"];
        $report_arr["referred_by"] = $_SESSION["referred_by"];
        $report_arr["source_name"] = $_SESSION["source_name"];

        //      Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');
        if (!count($this->err)) {
            if (!$is_export) {
                $entityManager = new EntityManager($this->daffny->DB);
                $this->daffny->tpl->orders = $entityManager->getCancelledOrdersReportOnTime($this->order->getOrder(), $_SESSION['per_page'], $report_arr, getParentId());
                $this->setPager($entityManager->getPager());
            } else {
                $entityManager = new EntityManager($this->daffny->DB);
                $data = $this->daffny->tpl->orders = $entityManager->getCancelledOrdersReportOnTime(null, null, $report_arr, getParentId());
                $this->export_cancelled_orders($data);
            }
        }

        // prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $users_ids = $_SESSION["users_ids"];
        if (!is_array($users_ids)) {
            $users_ids = array();
        }

        $source_name = $_SESSION["source_name"];
        if (!is_array($source_name)) {
            $source_name = array();
        }

        $referred_by = $_SESSION["referred_by"];
        if (!is_array($referred_by)) {
            $referred_by = array();
        }

        // fetching all source id
        $sql = "SELECT id,company_name FROM app_leadsources WHERE `status` = 1";
        $sources = $this->daffny->DB->query($sql);
        $source_id = array();
        while ($row = mysqli_fetch_assoc($sources)) {
            $source_id[$row['id']] = $row['company_name'];
        }

        // fetching all referer id
        $sql = "SELECT id,name FROM app_referrers WHERE `status` = 1";
        $ref = $this->daffny->DB->query($sql);
        $ref_id = array();
        while ($row = mysqli_fetch_assoc($ref)) {
            $ref_id[$row['id']] = $row['name'];
        }

        $this->form->helperMLTPL("users_ids[]", $this->getUsersByStatus(" status ='Active' "), $users_ids, array("id" => "users_ids", "multiple" => "multiple"), "User", "</td><td colspan=\"3\">");
        $this->form->helperMLTPL("referred_by[]", $ref_id, $referred_by, array("id" => "referred_by", "multiple" => "multiple"), "Referred", "</td><td colspan=\"3\">");
        $this->form->helperMLTPL("source_name[]", $source_id, $source_name, array("id" => "source_name", "multiple" => "multiple"), "Source", "</td><td colspan=\"3\">");

        $this->form->ComboBox("status_id", array("" => "-- All --") + Entity::$status_name_orders, array("style" => ""), "Status", "</td><td colspan=\"3\">");
        $this->form->ComboBox("time_period", $this->time_periods, array("style" => ""), "", "");
        $this->form->DateField("start_date", 10, array(), "", "");
        $this->form->DateField("end_date", 10, array(), "", "");
        $this->form->TextField("ship_via", 100, array(), "Ship Via", "</td><td colspan=\"3\">");
        $this->form->TextField("order_id", 100, array(), "Order ID", "</td><td colspan=\"3\">");
        $this->form->TextField("email", 100, array(), "Email", "</td><td colspan=\"3\">");
        $this->form->TextField("phone", 100, array("class" => "phone"), "Phone", "</td><td colspan=\"3\">");

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = "checked=\"checked\"";
            $this->input['ptype1ch'] = "";
        } else {
            $this->input['ptype1ch'] = "checked=\"checked\"";
            $this->input['ptype2ch'] = "";
        }
    }

    final private function export_cancelled_orders($data)
    {

        $excl = new PHPExcel();
        $excl->setActiveSheetIndex(0);
        $sht = $excl->getActiveSheet();
        $sht->setTitle('Orders');

        //Build Header with user data
        $sht->getCellByColumnAndRow(0, 1)->setValue("Orders Report");
        $sht->getCellByColumnAndRow(9, 4)->setValue("Generated:");
        $sht->getCellByColumnAndRow(9, 5)->setValue("By:");
        $sht->getCellByColumnAndRow(10, 4)->setValue(date("Y-m-d H:i:s"));
        $sht->getCellByColumnAndRow(10, 5)->setValue($_SESSION['member']['contactname']);
        //Set styles for header
        $sht->getStyleByColumnAndRow(0, 1)->applyFromArray($this->titleFont);
        $sht->getStyleByColumnAndRow(9, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 4)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(9, 5)->applyFromArray($this->smallFont);
        $sht->getStyleByColumnAndRow(10, 5)->applyFromArray($this->smallFont);

        //Grid header
        $i = 6;
        $titles = array(
            "Order ID"
            , "Order Placed"
            , "Cancelled On"
            , "1st Avail"
            , "Cancelled After"
            , "Reason"
            , "Assigned To"
            , "Cancelled By"
            , "Total Tarriff"
            , "Carrier Pay"
            , "Deposit",
        );

        //Print titles
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
        //apply format for header
        for ($j = 0; $j < $m; $j++) {
            $sht->getStyleByColumnAndRow($j, $i)->applyFromArray($this->headFont);
            $sht->getStyleByColumnAndRow($j, $i)->getAlignment()->setWrapText(true);
        }
        //logo
        //$this->printLogo($excl->getActiveSheet());

        //build sales grid
        if (count($data) > 0) {
            foreach ($data as $o) {
                $i++;

                $id_temp = ($o->prefix) ? $id = $o->prefix . "-" . $o->number : $id = $o->number;
                $s1 = $o->getShipper();
                $shipper_id_temp = (htmlspecialchars(trim($o->shipper_id) == "" ? "" : $s1->fname . " " . $s1->lname) . " " . "\n" . $s1->email);
                $company_temp = (trim($o->shipper_id) == "" ? "" : $s1->company);
                $comp_name_temp = trim($o->carrier_id) > 0 ? $o->getCarrier()->company_name : "";

                $days = strtotime($o->archived) - strtotime($o->created);

                $sql = "SELECT `text` FROM app_notes WHERE `entity_id` = ".$o->id." AND `text` LIKE '%canceled%'";
                $query = $this->daffny->DB->query($sql);
                $row = mysqli_fetch_assoc($query)['text'];
                $row = explode(" ",$row);

                $d = array(
                    $id_temp,
                    date("m/d/y h:i a", strtotime($o->created)),
                    date("m/d/y h:i a", strtotime($o->archived)),
                    date("m/d/y h:i a", strtotime($o->avail_pickup_date)),
                    abs(round($days / (60 * 60 * 24)))." Days",
                    $o->cancel_reason,
                    $o->getAssigned()->contactname,
                    $row[0],
                    $o->total_tariff_stored,
                    "$" . $o->carrier_pay_stored,
                    "$" . number_format($o->total_tariff_stored - $o->carrier_pay_stored, 2),
                );
                $sht->fromArray($d, null, 'A' . $i);
            }
        }
        //output
        $this->outputExcel($excl, "orders");
    }

    public function daily_dispatch_hourly_report()
    {
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('reports') => 'Reports',
                '' => 'Daily Dispatch Hourly Report'
            )
        );
        $this->tplname = 'reports.hourly_dispatch.show';

        // is export requested or not
        $is_export = false;
        if (isset($_POST['export_csv'])) {
            $is_export = true;
        }

        // is graph request
        $is_graph = false;
        if (isset($_POST['plot_graph'])) {
            $is_graph = true;
        }

        // default values in filter
        if (!isset($_SESSION['select_month'])) {
            $_SESSION['select_month'] = date('m');
            $_SESSION['select_year'] = date('Y');
        }

        // when form submitted override default values in filter
        if (isset($_POST['submit']) || $is_export || $is_graph) {
            $_SESSION['select_month'] = trim(post_var('select_month'));
            $_SESSION['select_year'] = trim(post_var('select_year'));
        }
        
        // collect data in array
        $search_arr = array(
            'select_month' => $_SESSION['select_month'],
            'select_year' => $_SESSION['select_year']
        );

        // prepare search conditions for query
        $report_arr = $search_arr;
        $report_arr['select_month'] = $_SESSION['select_month'];
        $report_arr['select_year'] = $_SESSION['select_year'];

        // Generate report
        $this->daffny->tpl->orders = array();
        $this->applyOrder(Entity::TABLE);
        $this->order->setDefault('id', 'desc');
        $month = 0;
        $year = 0;
        $last_day = 0;

        if (!count($this->err)) {
            $month = $report_arr['select_month'];
            $year = $report_arr['select_year'];
            $last_day  = date('t', strtotime(date($year."-".$month.'-t')));
            
            $qry = "SELECT id,prefix,number,(total_tariff_stored - carrier_pay_stored) as deposit, dispatched FROM ".Entity::TABLE." WHERE parentid = ".$_SESSION['member']['parent_id']." AND dispatched >= '".date($year."-".$month."-01 00:00:00")."' AND dispatched <= '".date($year."-".$month."-".$last_day." 23:59:59")."'";
            $res = $this->daffny->DB->query($qry);
            
            $data = array();
            while($row = mysqli_fetch_assoc($res)){
                $data[] = $row;
            }

            // structuring data
            $structure_data = array();
            for($i=1;$i<=$last_day;$i++){
                $index = str_pad($i, 2, '0', STR_PAD_LEFT);

                $structure_data[$index] = array();
                for($j=1;$j<=24;$j++){
                    $structure_data[$index]['hours'][$j] = 0;
                    $structure_data[$index]['deposit'][$j] = 0;
                    $structure_data[$index]['count'] = 0;

                    foreach($data as $key => $value){
                        $day = date('d',strtotime($value['dispatched']));
                        $hour = date('H',strtotime($value['dispatched']));

                        if($index == $day){
                            $structure_data[$index]['count'] = $structure_data[$index]['count'] + 1;
                            if($j == $hour){
                                $structure_data[$index]['hours'][$j] = $structure_data[$index]['hours'][$j] + 1;
                                $structure_data[$index]['deposit'][$j] = $structure_data[$index]['deposit'][$j] + $value['deposit'];
                            }
                        }
                    }
                }
            }

            // calculating hourly deposit
            $hourly_deposit = array();
            for($i=0;$i<=$last_day;$i++){
                $index = str_pad($i, 2, '0', STR_PAD_LEFT);
                foreach($structure_data[$index]['deposit'] as $k => $v){
                    $hourly_deposit[$k] = $hourly_deposit[$k]  +$v;
                }
            }

            // calculating hourly total
            $hourly_total = array();
            for($i=0;$i<=$last_day;$i++){
                $index = str_pad($i, 2, '0', STR_PAD_LEFT);
                foreach($structure_data[$index]['hours'] as $k => $v){
                    $hourly_total[$k] = $hourly_total[$k]  +$v;
                }
            }

            if (!$is_export) {
                $this->daffny->tpl->month = $month;
                $this->daffny->tpl->year = $year;
                $this->daffny->tpl->last_day = $last_day;
                $this->daffny->tpl->data = $structure_data;
                $this->daffny->tpl->plot_graph = $is_graph;
                $this->daffny->tpl->hourly_deposit = $hourly_deposit;
                $this->daffny->tpl->hourly_total = $hourly_total;
            } else {

                $header = array("Dates");
                for($i=1;$i<=12;$i++){
                    $from = $i;
                    $from = str_pad($from, 2, '0', STR_PAD_LEFT)." AM";

                    $to = $i + 1;
                    if($to > 12){
                        $to = 01;
                    }
                    $to = str_pad($to, 2, '0', STR_PAD_LEFT)." ".( ($i+1) == 13 ? "PM" : "AM");

                    $header[] =  $from."-".$to;
                }
                for($i=1;$i<=12;$i++){
                    $from = $i;
                    $from = str_pad($from, 2, '0', STR_PAD_LEFT)." PM";

                    $to = $i + 1;
                    if($to > 12){
                        $to = 01;
                    }
                    $to = str_pad($to, 2, '0', STR_PAD_LEFT)." ".( ($i+1) == 13 ? "AM" : "PM");

                    $header[] =  $from."-".$to;
                }
                $header[] = "Rate";
                $header[] = "Net";

                $filename = "DailyDispatch.csv";		 
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=\"$filename\"");
                $fh = fopen( 'php://output', 'w' );
                fputcsv($fh, $header);

                $grand_total_dispatch = 0;
                foreach($structure_data as $key => $value){
                    
                    $dataset = array($month."-".$key."-".$year);
                    $hourly_count = 0;
                    foreach($value['hours'] as $k => $v){
                        if($v == 0){
                            // nothing to do
                        } else {
                            $hourly_count++;
                        }
                        
                        $dataset[] = $v;
                    }
                    $dataset[] = number_format(($value['count']/$hourly_count),2);
                    $dataset[] = $value['count'];
                    fputcsv($fh, $dataset);

                    $grand_total_dispatch = $grand_total_dispatch  + $value['count'];
                }

                $net_total_array = array("Totals");
                $net_day_total = 0;
                foreach($hourly_total as $key => $value){
                    $net_day_total = $net_day_total + $value;
                    $net_total_array[] = $value;
                }
                $net_total_array[] = "";
                $net_total_array[] = $net_day_total;
                fputcsv($fh, $net_total_array);

                $net_deposit_array = array("Deposits");
                $net_day_deposit = 0;
                foreach($hourly_deposit as $key => $value){
                    $net_day_deposit = $net_day_deposit + $value;
                    $net_deposit_array[] = $value;
                }
                $net_deposit_array[] = "";
                $net_deposit_array[] = $net_day_deposit;
                fputcsv($fh, $net_deposit_array);
                fputcsv($fh, array('','','','','','','','','','','','','','','','','','','','','','','','','','Per Day',number_format(($grand_total_dispatch/$last_day),2)));

                fclose($fh);
                exit();
            }
        }

        // prepare input fields
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }
        $months = array(
            '01'=>"January",
            '02'=>"Feburary",
            '03'=>"March",
            '04'=>"April",
            '05'=>"May",
            '06'=>"June",
            '07'=>"July",
            '08'=>"August",
            '09'=>"September",
            '10'=>"October",
            '11'=>"November",
            '12'=>"December",
        );
        $this->form->ComboBox('select_month', $months, array('style' => 'width:150px;'), '', '');
        
        $years = array();
        for($i=2000;$i<2051;$i++){
            $years[$i] = $i;
        }
        $this->form->ComboBox('select_year', $years, array('style' => 'width:150px;'), '', '');
    }
}
