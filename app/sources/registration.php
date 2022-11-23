<?php

require_once(CLASS_PATH . "memberapp.php");

class AppRegistration extends Memberapp
{

    public $title = "Registration";

    public function idx()
    {
	    //$this->tplname = "registration.coming_soon";
	    //return;
        $this->tplname = "registration.plans";
        if (!isGuest()) {
            redirect(getLink(getUserDir(), "profile"));
        }
     
	    $row = $this->daffny->DB->selectRows("*","plan_type","where status=1");
        if (!empty($row)) {
	        $this->daffny->tpl->planType = $row;
        }
		
    }
	
	
	 public function planinfo()
    {
	    //$this->tplname = "registration.coming_soon";
	    //return;
        $this->tplname = "registration.form";
		//print_r($_POST);
	    $plan = (int)$_POST['plan'];
		$ptype = $_POST['plantype'];
		
		if (!isset($plan) || $plan=="") {
            $this->setFlashInfo("Please select a plan.");
			 redirect(getLink("registration"));
        }
		
        if (!isGuest()) {
            redirect(getLink(getUserDir(), "profile"));
        }
		
       $row = $this->daffny->DB->selectRows("*","plan_type","where status=1");
        if (!empty($row)) {
	        $this->daffny->tpl->planType = $row;
        }
      
        if (isset($_POST['submit']) && $sql_arr = $this->checkForm() ) {
           
			
			$arr = array(             "plan" => $plan
									  , "contactname" => $sql_arr['contactname']
									  , "companyname" => $sql_arr['companyname']
									  , "email" => $sql_arr['email']
									  , "phone" => $sql_arr['phone']
									  , "mcnumber" => $sql_arr['mcnumber']
									  , "type" => implode(", ",$sql_arr['type'])
									  , "message" => $sql_arr['message']
										
								);
			$this->daffny->DB->insert("members_applied", $arr);
			
			$insid = $this->daffny->DB->get_insert_id();
			$ptypeSize = sizeof($ptype);
			for($i=0;$i<$ptypeSize;$i++)
		    {
				 $typearr = array("member_applied_id" => $insid
								  , "plan_type_id" => $ptype[$i]
								);
				 $this->daffny->DB->insert("members_plan_type", $typearr);
			}
             $this->setFlashInfo("Registration successfully done.");
			 //redirect(getLink("registration"));
		}
		else
		{
			$this->input['contactname'] = $_POST['contactname'];
			$this->input['companyname'] = $_POST['companyname'];
			$this->input['email'] 		= $_POST['email'];
			$this->input['phone'] 		= $_POST['phone'];
			$this->input['mcnumber'] 	= $_POST['mcnumber'];
			$this->input['message'] 	= $_POST['message'];
			$this->input['type'] 		= $_POST['type'];
		}
        
		$this->form->TextField("contactname", 255, array(), $this->requiredTxt . "Contact Name", "</td><td>");
        $this->form->TextField("companyname", 255, array(), $this->requiredTxt . "Company name", "</td><td>");
		$this->form->TextField("email", 255, array(), $this->requiredTxt . "E-mail", "</td><td>");
        $this->form->TextField("phone", 32, array('class' => 'phone'), $this->requiredTxt . "Phone", "</td><td>");
		
		$this->form->TextField("mcnumber", 255, array(), $this->requiredTxt . "MC Number", "</td><td>");
		
        $this->form->ComboBox("type", array("Auto Transport Broker" => "Auto Transport Broker", "Auto Transport Carrer" => "Auto Transport Carrer"), array("style" => "width:200px; height:40px;", 'multiple' => 'multiple'), $this->requiredTxt . "Type", "</td><td>");
       // $this->form->TextField('address', 255, array(), $this->requiredTxt . "Address", '</td><td>');
       // $this->form->TextField('address2', 255, array(), '&nbsp;', '</td><td>');
        //$this->form->TextField('city', 64, array(), $this->requiredTxt . "City", '</td><td>');
       // $this->form->ComboBox('state', $this->getStates(true), array(), $this->requiredTxt . "State", "</td><td>");
       // $this->form->TextField('zip', 10, array('class' => 'zip'), $this->requiredTxt . "Zip Code", "</td><td>");
      $this->form->TextArea("message", 50, 20, array('style' => 'height:100px;width:250px'), "Message", "</td><td>");

        
    }

    protected function checkRequired($val, $label)
    {
        if (post_var($val) == '')
            $this->err[$val] = "Field <strong>{$label}</strong> can not be empty.";
    }

    public function ajax()
    {
        $out = array('success' => false);
        $this->err = array();
        switch ($_POST['page']) {
            case 1:
                $out['success'] = true;
                break;
            case 2:
                if (post_var('additional_number') > 0) {
                    $product = new Product($this->daffny->DB);
                    $product->load(post_var('products'));
                    $additional = new Product($this->daffny->DB);
                    $additional->load(post_var('additional'));
                    if ($product->period_id != $additional->period_id) {
                        $this->err['additional'] = "Invalid <strong>Additional License</strong>";
                    }
                }
                $this->checkRequired('contactname', 'Contact Name');
                $this->checkRequired('companyname', 'Company Name');
                $this->checkRequired('email', 'Email');
                $this->checkRequired('phone', 'Phone');
                $this->checkRequired('address', 'Address');
                $this->checkRequired('city', 'City');
                $this->checkRequired('state', 'State');
                $this->checkRequired('zip', 'Zip Code');
                $this->checkRequired('username', 'Username');
                $this->checkRequired('password_hint', 'Password Hint');
                $this->checkRequired('password', 'Password');
                $this->checkRequired('password_confirm', 'Password confirmation');
                //check password
                $password = trim(post_var("password"));
                if ($password == "") {
                    $this->err[] = "<strong>'Password'</strong> is required.";
                } else {
                    if (strlen($password) < 6 || strlen($password) > 10) {
                        $this->err[] = "<strong>'Password'</strong> must be between 6 and 10 characters.";
                    }
                    if (!preg_match("/[a-zA-Z]/", $password)) {
                        $this->err[] = "<strong>'Password'</strong> must contain at least 1 alpha character.";
                    }
                    if (!preg_match("/[0-9]/", $password)) {
                        $this->err[] = "<strong>'Password'</strong> must contain at least 1 numeric character.";
                    }
                    if (!preg_match("/[\$%&?()*^<>\/\\\]/", $password)) {
                        $this->err[] = "<strong>'Password'</strong> must contain at least 1 special character. (<strong>\%$^&*()<>?/</strong>).";
                    }
                    if ($password != trim(post_var("password_confirm"))) {
                        $this->err[] = "<strong>'Password'</strong> and <strong>'Confirm Password'</strong> do not match.";
                    }
                }

                $this->checkEmail('email', 'Email');
                if (count($this->err) == 0) {
                    $out['success'] = true;
                } else {
                    $out['errors'] = $this->err;
                }
                break;
            case 3:
                $this->checkRequired('card_first_name', "Cardholder First Name");
                $this->checkRequired('card_last_name', "Cardholder Last Name");
                $this->checkRequired('card_number', 'Card Number');
                $this->checkRequired('card_cvv2', 'CVV');
                $this->checkRequired('billing_address', 'Address');
                $this->checkRequired('billing_city', 'City');
                $this->checkRequired('billing_state', 'State');
                $this->checkRequired('billing_zip', 'Zip Code');
                if (strtotime(post_var('card_expire_year') . '-' . post_var('card_expire_month') . '-01') < time()) {
                    $this->err['card_expire_month'] = "Credit Card expired";
                }
                if (!($cardType = validate_cc_number(post_var('card_number')))) {
                    $this->err['card_number'] = "Invalid Card number format";
                } else {
                    if ($cardType != post_var('card_type')) {
                        $this->err['card_type'] = "Invalid Card type";
                    }
                }
                if (count($this->err) == 0) {
                    $out['coupon_success'] = "";
                    if (isset($_POST["coupon_code"]) && trim($_POST["coupon_code"]) != "") {
                        $_SESSION["coupon_code"] = $_POST["coupon_code"];
                        if ($this->validateCoupon($_SESSION["coupon_code"]) == 0) {
                            $out['coupon_success'] = "Bad coupon code.";
                        } else {
                            $out['coupon_success'] = "Coupon has been applied.";
                        }
                    }

                    $out['success'] = true;
                    $out['data'] = $this->generateInvoice(array(
                        'initial' => post_var('products'),
                        'additional' => post_var('additional'),
                        'storage' => post_var('storages'),
                        'addon_aq' => post_var('addon_aq'),
                        'number' => post_var('additional_number'),
                        'code' => $_SESSION['coupon_code'],
                        'card_first_name' => post_var('card_first_name'),
                        'card_last_name' => post_var('card_last_name'),
                        'card_number' => '*** ... ' . substr(post_var('card_number'), -4),
                        'card_expiration' => post_var('card_expire_month') . '/' . substr(post_var('card_expire_year'), -2),
                    ));
                } else {
                    $out['errors'] = $this->err;
                }
                break;
        }
        die(json_encode($out));
    }

    protected function calculateDiscount($product_id, $code)
    {
	    $sql = "
	    SELECT IF (cd.is_percent_discount = 1, cd.discount * p.price / 100, cd.discount) as `discount`, p.id as product_id
	    FROM coupons c, coupon_details cd, products p
	    WHERE p.id = " . (int)$product_id . "
			AND c.`status` = 'active'
			AND c.`is_delete` = 0
			AND c.`code` LIKE '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $code) . "'
			AND c.id = cd.coupon_id
			AND p.id = cd.product_id
			AND IF(c.`time_to_use` = 0, TRUE,
				IF(c.is_per_customer = 1,
					1,
					(SELECT COUNT(*) FROM `orders` WHERE `coupon_id` = c.`id` AND `status` = " . Orders::STATUS_PROCESSED . ")
				) < c.`time_to_use`
			)
			AND IF(c.is_never_expire = 1, DATE_ADD(NOW(), INTERVAL 1 YEAR), c.expire_date) > NOW()
	    ";
	    $res = $this->daffny->DB->query($sql);
        if (!empty($res)) {
	        $row = $this->daffny->DB->fetch_row($res);
            return $row['discount'];
        }
        return 0;
    }

    protected function generateInvoice($data)
    {

        if (isset($data['short'])) {
            $this->daffny->tpl->short = true;
        }
        if (isset($data['header'])) {
            $this->daffny->tpl->header = true;
        }
        if (isset($data['print'])) {
            $this->daffny->tpl->print = true;
        }
        $discount = 0;
        $product = new Product($this->daffny->DB);
        $product->load($data['initial']);
        $discount += $this->calculateDiscount($product->id, $data['code']);
        if ($data['number'] > 0) {
            $additional = new Product($this->daffny->DB);
            $additional->load($data['additional']);
            $discount += $this->calculateDiscount($additional->id, $data['code']) * $data['number'];
        }
        if ((int)$data['storage'] > 0) {
            $storage = new Product($this->daffny->DB);
            $storage->load($data['storage']);
            $discount += $this->calculateDiscount($storage->id, $data['code']);
        }

        if ((int)$data['addon_aq'] > 0) {
            $addon_aq = new Product($this->daffny->DB);
            $addon_aq->load($data['addon_aq']);
            $discount += $this->calculateDiscount($addon_aq->id, $data['code']);
        }

        if ($discount > 0) {
            $this->daffny->tpl->discount = $discount;
        }
        $this->daffny->tpl->products = array(
            array(
                'item' => $product->code,
                'product' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
                'total' => $product->price * 1,
            )
        );
        if (isset($additional)) {
            $this->daffny->tpl->products[] = array(
                'item' => $additional->code,
                'product' => $additional->name,
                'quantity' => $data['number'],
                'price' => $additional->price,
                'total' => $additional->price * $data['number'],
            );
        }

        if (isset($storage)) {
            $this->daffny->tpl->products[] = array(
                'item' => $storage->code,
                'product' => $storage->name,
                'quantity' => 1,
                'price' => $storage->price,
                'total' => $storage->price,
            );
        }

        if (isset($addon_aq)) {
            $this->daffny->tpl->products[] = array(
                'item' => $addon_aq->code,
                'product' => $addon_aq->name,
                'quantity' => 1,
                'price' => $addon_aq->price,
                'total' => $addon_aq->price,
            );
        }

        $this->input = $data;
        return $this->daffny->tpl->build('registration.invoice', $this->input);
    }

    protected function create($sql_arr)
    {
        $sql_arr['chmod'] = 2;


        $MemberID = $this->saveMember($sql_arr);

        $this->collectSubscribers("members", $sql_arr);
        if ($this->dbError()) {
            $this->daffny->DB->delete("members", "id = '" . $MemberID . "'");
            return false;
        }
        return $MemberID;
    }

   

    /**
     * Validate submited form
     *
     * @param int $MemberID
     *
     * @return array $sql_arr
     */
    protected function checkForm($MemberID = 0)
    {
        $sql_arr = $_POST;
   
        $checkEmpty = array(
             'contactname' => "Name"
           , 'companyname' => "Company Name"
           , 'phone' => "Phone"
           , 'email' => 'E-mail'
           , 'mcnumber' => 'mcnumber'
           , 'message' => 'message'
        );

        if (isset($_POST['companyname'])) {
            $row = $this->daffny->DB->selectRow("COUNT(*) as cnt", "app_company_profile", "WHERE `companyname` LIKE '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_POST['companyname']) . "'");
            if ($row['cnt'] > 0) {
                $this->err[] = "Company <strong>{$_POST['companyname']}</strong> is already registered in our system.";
            }
        }
       
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        $this->validateMember($sql_arr, $MemberID);
        if (count($this->err)) {
            return false;
        }

/*
        $sql_arr['did_any_help'] = (int)post_var("did_any_help");
        if ($sql_arr['did_any_help'] == 1) {
            $sql_arr['who_help'] = post_var("who_help");
        }
*/
        return $sql_arr;
    }


    public function success()
    {
        $this->tplname = "registration.success";
        if (isset($_SESSION['invoice'])) {
            $this->input['invoice'] = $_SESSION['invoice'];
        } else {
            redirect(getLink("registration"));
        }
    }

    private function validateCoupon($coupon_code)
    {
        $coupon_id = 0;

        $sql = " SELECT * " .
            "   FROM coupons a " .
            "  WHERE a.code = '" . mysqli_real_escape_string($this->daffny->DB->connection_id, strtoupper($coupon_code)) . "' " .
            "    AND a.status = 'active' " .
            "    AND a.is_delete = 0 " .
            "    AND (CASE WHEN a.time_to_use IS NOT NULL " .
            "              THEN (CASE WHEN a.is_per_customer = 1 " .
            "                         THEN (SELECT COUNT(*) " .
            "                                 FROM orders " .
            "                                  WHERE coupon_id = a.id " .
            "                                  AND status = 'processed' " .
            "                                  AND is_delete = 0 " .
            "                              ) <= a.time_to_use " .
            "                         ELSE (SELECT COUNT(*) " .
            "                                 FROM orders " .
            "                                WHERE coupon_id = a.id " .
            "                                  AND status = 'processed' " .
            "                                  AND is_delete = 0 " .
            "                              ) <= a.time_to_use " .
            "                    END " .
            "                   ) " .
            "              ELSE TRUE " .
            "         END " .
            "        ) " .
            "    AND (CASE WHEN a.is_never_expire = 0 " .
            "              THEN a.expire_date > NOW() " .
            "              ELSE TRUE " .
            "         END " .
            "        ) ";
        $result = $this->daffny->DB->query($sql);
        if ($row = $this->daffny->DB->fetch_row($result)) {
            $coupon_id = $row["id"];
        }

        return $coupon_id;
    }


}