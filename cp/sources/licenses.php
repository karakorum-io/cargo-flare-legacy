<?

/***************************************************************************************************
* Control Panel - Licenses                                                                                 *
*                                                                                                  *
* Client: 	PitBullTax                                                                             *
* Version: 	1.1                                                                                    *
* Date:    	2010-05-31                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
****************************************************************************************************/


class CpLicenses extends CpAction
{
	public $daffny;
	public $title;
	public $tplname;
	public $input;
	public $err = array();

	public function init()
	{
		switch (get_var("licenses"))
		{
			case "renew":
				$this->renew();
				break;

			case "renewals":
				$this->renewals();
				break;
			case "addons":
				$this->addons();
				break;

			case "cancel":
				$this->cancel();
				break;

    		case "block":
				$this->block();
				break;

            case "change_payment_type":
				$this->change_payment_type();
				break;

    		case "unblock":
				$this->unblock();
				break;

			default:
				$this->show();
				break;
		}
		$this->construct();
	}

    public function show()
    {
        $this->title = "Licenses Management";
        $this->tplname = "licenses.list";

        if (isset($_POST["on_page"]))
        {
            $_SESSION["cp_onpage"] = post_var("on_page");
        }

        $this->input = array(
             "s_period" => $this->daffny->html->select("period", array("" => "All", "1" => "Current Month", "2" => "Current Year", "3" => "Date Range"), get_var("period"), array("id"=>"period", "style"=>"width:136px;"))
           , "s_date_from" => stripslashes(get_var("date_from"))
           , "s_date_to" => stripslashes(get_var("date_to"))
           , "s_member_name" => stripslashes(get_var("member_name"))
           , "s_product_code" => stripslashes(get_var("product_code"))
           , "s_status" => $this->daffny->html->select("status", array("" => "All", "active" => "Active", "expired" => "Expired", "closed" => "Closed"), get_var("status"), array("id"=>"status", "style"=>"width:136px;"))
        );

        $this->showMessage();

        // Limit
        $where = "";
        if (get_var("member_name") != "") {
            $where .= $where == "" ? " WHERE " : " AND ";
            $where .= "CONCAT(e.first_name, ' ', e.last_name) LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("member_name"))."%'";
        }
        if (get_var("product_code") != "") {
            $where .= $where == "" ? " WHERE " : " AND ";
            $where .= "d.code LIKE '%".mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("product_code"))."%'";
        }
        if (get_var("status") != "") {
            $where .= $where == "" ? " WHERE " : " AND ";
            $where .= "a.status = '".mysqli_real_escape_string($this->daffny->DB->connection_id, get_var("status"))."'";
        }
        switch (get_var("period"))
        {
        	case "1":
        	{
                $where .= $where == "" ? " WHERE " : " AND ";
                $where .= "b.register_date BETWEEN CAST(CONCAT(YEAR(NOW()), '-', MONTH(NOW()), '-01') AS DATE) AND DATE_ADD(CAST(CONCAT(YEAR(NOW()), '-', (MONTH(NOW()) + 1), '-01') AS DATE), INTERVAL -1 SECOND)";

        		break;
        	}
        	case "2":
        	{
                $where .= $where == "" ? " WHERE " : " AND ";
                $where .= "b.register_date BETWEEN CAST(CONCAT(YEAR(NOW()), '-01-01') AS DATE) AND DATE_ADD(CAST(CONCAT((YEAR(NOW()) + 1), '-01-01') AS DATE), INTERVAL -1 SECOND)";

        		break;
        	}
        	case "3":
        	{
        		if (get_var("date_from") != "") {
            		$where .= $where == "" ? " WHERE " : " AND ";
            		$where .= "b.register_date >= '".convertUSA2SQLDate(get_var("date_from"))." 00:00:00'";
        		}
        		if (get_var("date_to") != "") {
            		$where .= $where == "" ? " WHERE " : " AND ";
            		$where .= "b.register_date <= '".convertUSA2SQLDate(get_var("date_to"))." 23:59:59'";
        		}
        		break;
        	}
        }

        $where .= $where == "" ? " WHERE " : " AND ";
		$where .= " d.type_id <> '4' ";


        $tables = " licenses a ".
                  " INNER JOIN orders b ON b.id = a.order_id ".
                  " INNER JOIN order_details c ON c.order_id = b.id ".
                  " INNER JOIN products d ON d.id = c.product_id ".
                  " INNER JOIN members e ON e.id = b.member_id ";

        $page_navi = $this->daffny->load_lib("page_navigator");
        $page_navi->on_page = $_SESSION["cp_onpage"];
        $page_navi->where = $where;
        $page_navi->links = 4;
        $page_navi->init($tables);
        $this->input["pages"] = $page_navi->get_list(3);
		$this->pagerSelect();

        // Order
        $ord = $this->daffny->load_lib("order");
        $ord->setdefault("id", "DESC");
        $this->input["id"] = $ord->get_title("id", "ID");
        $this->input["product_name"] = $ord->get_title("product_name", "Product");
        $this->input["member_name"] = $ord->get_title("member_name", "Customer");
        $this->input["register_date"] = $ord->get_title("b.register_date", "Register Date");
        $this->input["expiration_date"] = $ord->get_title("a.expiration_date", "Expiration Date");
        $this->input["order_id"] = $ord->get_title("order_id", "Order #");
        $this->input["status"] = $ord->get_title("status", "Status");

        if (post_var("action") == "export_selected")
        {
        	$where .= $where == "" ? " WHERE " : " AND ";
        	$where .= " a.id IN (".join(",", @$_POST["is_check"]).") ";
        	$this->export($where.$ord->orderby);
        }
        if (get_var("action") == "export_all_found")
        {
        	$this->export($where.$ord->orderby);
        }

        $sql = " SELECT a.id ".
               "      , LPAD(a.order_id, 5, '0') AS order_id ".
               "      , a.expiration_date ".
               "      , DATE_FORMAT(a.expiration_date, '%m/%d/%Y') AS expiration_date_format ".
               "      , CASE change_to_annual WHEN 1 THEN "._tzf("a.changed", "%m/%d/%Y", $this->daffny->cfg['cp_timezone'])." ELSE '' END AS changed ".
               "      , a.status ".
               "      , b.member_id ".
               "      , b.register_date ".
               "      , "._tzf("b.register_date", "%m/%d/%Y", $this->daffny->cfg['cp_timezone'])." AS register_date_format ".
               "      , CONCAT('#', d.code, ' ', d.name) AS product_name ".
               "      , d.type_id ".
               "      , d.period_id ".
               "      , CONCAT(e.first_name, ' ', e.last_name) AS member_name ".
               "      , (SELECT COUNT(*) FROM license_renewals WHERE license_id = a.id) AS renewal_count ".
               "      , (SELECT COUNT(*) FROM licenses_addons WHERE license_id = a.id) AS addon_count ".
               "      , (SELECT COUNT(*) FROM products WHERE code = d.renewal_code AND is_delete = 0) AS is_renewal ".
               "      , a.is_cancel ".
               "   FROM ".$tables.
               $where.$ord->orderby.$page_navi->limit;
        $this->daffny->DB->query($sql);

        $this->daffny->tpl->outdata = array();
        while ($row = $this->daffny->DB->fetch_row())
        {
            $row["status"] = ucfirst($row["status"]);
            $row["is_auto_renewal"] = (($row["status"] != "Closed") && $row["is_renewal"] && !$row["is_cancel"] ? "Yes" : "No");
            $this->daffny->tpl->outdata[] = $row;
        }
    }

    function export($where = "")
    {
    	$sql = " SELECT a.id ".
    	       "      , CONCAT('#', d.code, ' ', d.name) AS product_name ".
    	       "      , CONCAT(e.first_name, ' ', e.last_name) AS member_name ".
    	       "      , "._tzf("b.register_date", "%m/%d/%Y", $this->daffny->cfg['cp_timezone'])." AS register_date_format ".
    	       "      , "._tzf("a.expiration_date", "%m/%d/%Y", $this->daffny->cfg['cp_timezone'])." AS expiration_date_format ".
    	       "      , LPAD(a.order_id, 5, '0') AS order_id ".
    	       "      , a.status ".
    	       "   FROM licenses a ".
               "        INNER JOIN orders b ON b.id = a.order_id ".
               "        INNER JOIN order_details c ON c.order_id = b.id ".
               "        INNER JOIN products d ON d.id = c.product_id ".
               "        INNER JOIN members e ON e.id = b.member_id ".
    	       $where;

    	$header = array("ID", "Product", "Customer", "Register Date", "Expiration Date", "Order #", "Status");
		$buffer = exportCSVRecord($header);
    	$result = $this->daffny->DB->query($sql);
    	while ($row = $this->daffny->DB->fetch_row($result))
    	{
            $row["status"] = ucfirst($row["status"]);

            $buffer .= exportCSVRecord($row);
    	}

        $file_name = "Licenses.csv";
        header("Content-Type: application; filename=\"".$file_name."\"");
        header("Content-Disposition: attachment; filename=\"".$file_name."\"");
        header("Content-Description: \"".$file_name."\"");
        header("Expires: 0");
        header("Cache-Control: private");
        header("Pragma: cache");

        echo $buffer;
        exit();
    }
    public function change_payment_type()
    {
        $id = (int)post_var("id");
        $this->daffny->DB->query("UPDATE licenses SET change_to_annual = 1, changed=NOW() WHERE id=$id");
        echo $this->json->encode(array('success' => true, 'message' => "Changed on ".date("m/d/Y", time())));
        exit;
    }
    public function renewals()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $this->title = "Renewals";
        $this->tplname = "licenses.renewals";

        if (isset($_POST["on_page"]))
        {
            $_SESSION["cp_onpage"] = post_var("on_page");
        }

        // Limit
        $where = " WHERE a.license_id = '".$id."' ";

        $page_navi = $this->daffny->load_lib("page_navigator");
        $page_navi->on_page = $_SESSION["cp_onpage"];
        $page_navi->where = $where;
        $page_navi->links = 4;
        $page_navi->init("license_renewals  a");
        $this->input["pages"] = $page_navi->get_list(3);
		$this->pagerSelect();

        // Order
        $ord = $this->daffny->load_lib("order");
        $ord->setdefault("id", "DESC");
        $this->input["id"] = $ord->get_title("id", "ID");
        $this->input["product_name"] = $ord->get_title("product_name", "Product");
        $this->input["register_date"] = $ord->get_title("register_date", "Register Date");
        $this->input["order_id"] = $ord->get_title("order_id", "Order #");

        $sql = " SELECT a.id ".
               "      , LPAD(a.order_id, 5, '0') AS order_id ".
               "      , b.register_date ".
               "      , "._tzf("b.register_date", "%m/%d/%Y", $this->daffny->cfg['cp_timezone'])." AS register_date_format ".
               "      , CONCAT('#', d.code, ' ', d.name) AS product_name ".
               "   FROM license_renewals  a ".
               "        INNER JOIN orders b ON b.id = a.order_id ".
               "        INNER JOIN order_details c ON c.order_id = b.id ".
               "        INNER JOIN products d ON d.id = c.product_id ".
               $where.$ord->orderby.$page_navi->limit;
        $this->daffny->DB->query($sql);

        $this->daffny->tpl->outdata = array();
        while ($row = $this->daffny->DB->fetch_row())
        {
            $this->daffny->tpl->outdata[] = $row;
        }
    }

    public function addons()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $this->title = "Add-ons";
        $this->tplname = "licenses.addons";

        if (isset($_POST["on_page"]))
        {
            $_SESSION["cp_onpage"] = post_var("on_page");
        }

        // Limit
        $where = " WHERE a.license_id = '".$id."' ";

        $page_navi = $this->daffny->load_lib("page_navigator");
        $page_navi->on_page = $_SESSION["cp_onpage"];
        $page_navi->where = $where;
        $page_navi->links = 4;
        $page_navi->init("licenses_addons  a");
        $this->input["pages"] = $page_navi->get_list(3);
		$this->pagerSelect();

        // Order
        $ord = $this->daffny->load_lib("order");
        $ord->setdefault("id", "DESC");
        $this->input["id"] = $ord->get_title("id", "ID");
        $this->input["product_name"] = $ord->get_title("product_name", "Product");
        $this->input["order_id"] = $ord->get_title("order_id", "Order #");

        $sql = " SELECT a.id ".
               "      , LPAD(a.order_id, 5, '0') AS order_id ".
               "      , CONCAT('#', d.code, ' ', d.name) AS product_name ".
               "   FROM licenses_addons a ".
               "        INNER JOIN orders b ON b.id = a.order_id ".
               "        INNER JOIN products d ON d.id = a.product_id ".
		$where.$ord->orderby.$page_navi->limit;
        $this->daffny->DB->query($sql);
        $this->daffny->tpl->outdata = array();
        while ($row = $this->daffny->DB->fetch_row()){
            $this->daffny->tpl->outdata[] = $row;
        }
    }

    public function renew()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $sql = " SELECT e.* ".
               "      , f.id AS product_id ".
               "      , f.code AS product_code ".
               "      , f.name AS product_name ".
               "      , f.price AS product_price ".
               "      , a.change_to_annual ".
               "      , f.period_id AS product_period_id ".
               "      , b.affiliate_id ".
               "      , d.commission ".
               "   FROM licenses a ".
               "        INNER JOIN orders b ON b.id = a.order_id ".
               "        INNER JOIN order_details c ON c.order_id = b.id ".
               "        INNER JOIN products d ON d.id = c.product_id ".
               "        INNER JOIN members e ON e.id = b.member_id ".
               "        INNER JOIN products f ON f.code = CASE a.change_to_annual WHEN 1 THEN d.renewal_code_annual ELSE d.renewal_code END ".
               "                             AND f.is_delete = 0 ".
               "  WHERE a.id = '".$id."' ";


        $this->daffny->DB->query($sql);
        if (!$order = $this->daffny->DB->fetch_row())
        {
			$_SESSION['err_message'] = "Failed to renew selected license.";
			redirect("index.php?licenses");
        }

        $addons = $this->daffny->DB->selectRows("
                                p.code AS product_code
                              , p.name AS product_name
                              , p.id AS product_id
                              , la.qty AS product_qty
                              , p.price * la.qty AS product_price
                          ", "licenses_addons la
                   INNER JOIN products p ON la.product_id = p.id
                          ", "
                          WHERE p.period_id = '{$order["product_period_id"]}' AND la.license_id = '".$id."'
                   UNION SELECT
                              pp.code AS product_code
                            , pp.name AS product_name
                            , pp.id AS product_id
                            , la.qty AS product_qty
                            , pp.price * la.qty AS product_price
                         FROM licenses_addons la
                   INNER JOIN licenses l ON la.license_id = l.id
                   INNER JOIN products p ON la.product_id = p.id
                   INNER JOIN products pp ON pp.id = p.renewal_code_annual
                        WHERE la.license_id = '".$id."'
                          AND l.change_to_annual = 1
                          ");

        $renew = array(
            "product_code"  => $order["product_code"]
           ,"product_name"  => $order["product_name"]
           ,"product_id"    => $order["product_id"]
           ,"product_qty"   => 1
           ,"product_price" => $order["product_price"]
        );
        $this->daffny->tpl->outdata = array_merge(array($renew), $addons);

        unset($_SESSION['product_ids']);
        $_SESSION['product_ids'][$renew['product_id']] = $renew['product_qty'];
        foreach($addons as $k => $v)
        {
            $_SESSION['product_ids'][$v['product_id']] = $v['product_qty'];
        }

        $this->title = "Renew License";
        $this->tplname = "licenses.form";

        if (isset($_POST["save"]))
        {
            $card_type_id       = post_var("card_type_id");
            $card_first_name   = post_var("card_first_name");
						$card_last_name   = post_var("card_last_name");
            $card_number        = str_replace(" ", "", post_var("card_number"));
            $card_expire_month  = post_var("card_expire_month");
            $card_expire_year   = post_var("card_expire_year");
            $card_cvv2          = post_var("card_cvv2");

            $cc_address         = post_var("cc_address");
            $cc_city            = post_var("cc_city");
            $cc_state           = post_var("cc_state");
            $cc_zip             = post_var("cc_zip");

            $coupon_id          = post_var("coupon_id");

            $this->is_empty("card_first_name", "Card Holder First Name");
						$this->is_empty("card_last_name", "Card Holder Last Name");
            $this->is_empty("card_number", "Credit Card Number");
            $this->is_empty("card_expire_month", "Expiration Date (month)");
            $this->is_empty("card_expire_year", "Expiration Date (year)");
            $this->is_empty("card_cvv2", "CVV code");

            $this->is_empty("cc_address", "Credit Card Street Address");
            $this->is_empty("cc_city", "Credit Card City");
            $this->is_empty("cc_state", "Credit Card State");
            $this->is_empty("cc_zip", "Credit Card Zip Code");

           	if (!count($this->err))
            {
                $sql_arr = array(
                    "first_name"            => $order["first_name"]
                  , "last_name"             => $order["last_name"]
                  , "company"               => $order["company"]
                  , "address"               => $order["address"]
                  , "address2"              => $order["address2"]
                  , "city"                  => $order["city"]
                  , "state"                 => $order["state"]
                  , "zip"                   => $order["zip"]
                  , "phone"                 => $order["phone"]
                  , "fax"                   => $order["fax"]

                  , "email"                 => $order["email"]
                  , "password"              => ""
                  , "question_id"           => $order["question_id"]
                  , "answer"                => $order["answer"]

                  , "card_type_id"          => $card_type_id
                  , "card_first_name"       => $card_first_name
									, "card_last_name"        => $card_last_name
                  , "card_number"           => $card_number
                  , "card_expire"           => $card_expire_month.$card_expire_year
                  , "card_cvv2"             => $card_cvv2

                  , "cc_address"            => $cc_address
                  , "cc_city"               => $cc_city
                  , "cc_state"              => $cc_state
                  , "cc_zip"                => $cc_zip

                  , "affiliate_id"          => $order["affiliate_id"]
                  , "session_id"            => ""
                  , "coupon_id"             => $coupon_id

                  , "product_id"            => $order["product_id"]
                  , "commission"            => $order["commission"]
                );

            	unset($_SESSION["order_id"]);
            	unset($_SESSION["order_message"]);

                $result = $this->place_order($_SESSION['product_ids'], $sql_arr, $order["id"], true, $id);
                switch ($result["code"])
                {
                	case 0:
                	{
                        $_SESSION["order_id"] = $result["message"];
                        redirect("index.php?orders=register-done");

                		break;
                	}
                	case 1:
                	{
                        $_SESSION["order_message"] = $result["message"];
                        redirect("index.php?orders=register-done");

                		break;
                	}
                	case 2:
                	{
                		$this->err[] = $result["message"];
                		break;
                	}
                	default:
                	{
                		$this->err[] = "Unknown error happen.";
                	}
                }
			}
        }

        $this->input = $this->save_form_vars();

        if (!isset($_POST["save"]))
        {
            foreach ($order as $k => $v)
            {
                $this->input[$k] = $v;
            }
            $this->input["card_expire_month"] = substr($this->input["card_expire"], 0, 2);
            $this->input["card_expire_year"] = substr($this->input["card_expire"], 2, 2);
        }

        $this->input["card_number_format"] = $this->hideCCNumber($this->input["card_number"]);
        $this->input["card_cvv2_format"] = str_repeat("*", strlen($this->input["card_cvv2"]));

        $this->input["cc_state"] = $this->daffny->html->select("cc_state", $this->getStates(), @$this->input["cc_state"], array("id"=>"cc_state"));
        $this->input["card_type_id"] = $this->daffny->html->select("card_type_id", $this->getCardTypes(), $this->input["card_type_id"], array("id"=>"card_type_id"));
        $this->input["card_expire_month"] = $this->daffny->html->select("card_expire_month", $this->getMonths(), $this->input["card_expire_month"], array("id"=>"card_expire_month", "style"=>"width: 40px;"));
        $this->input["card_expire_year"] = $this->daffny->html->select("card_expire_year", $this->getYears(), $this->input["card_expire_year"], array("id"=>"card_expire_year", "style"=>"width: 60px;"));
        $this->input["errors"] = $this->daffny->msg($this->err);
    }

    public function cancel()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $this->daffny->DB->update("licenses", array("is_cancel" => 1), "id = '".$id."'");

        $_SESSION["inf_message"] = "<strong>License # ".$id."</strong> has been canceled.";
        redirect("index.php?licenses");
    }

    public function block()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $this->daffny->DB->update("licenses", array("status" => "closed"), "id = '".$id."'");

        $_SESSION["inf_message"] = "<strong>License # ".$id."</strong> has been blocked.";
        redirect("index.php?licenses");
    }

    public function unblock()
    {
		$id = (int)get_var("id");
		if ($id == 0) {
			$_SESSION['err_message'] = "License ID is invalid.";
			redirect("index.php?licenses");
		}

        $this->daffny->DB->update("licenses", array("status" => "active"), "id = '".$id."'");

        $_SESSION["inf_message"] = "<strong>License # ".$id."</strong> has been unblocked.";
        redirect("index.php?licenses");
    }
}
?>
