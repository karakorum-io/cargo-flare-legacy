<?php

require_once(CLASS_PATH . "memberapp.php");

class CpRegistration extends Memberapp
{

    public $title = "Registration";

    public function idx()
    {
	    //$this->tplname = "registration.coming_soon";
	    //return;
        $this->tplname = "registration.form";
		
        if($_SESSION['admin_here']!=1)//if (!isMember()) 
		{
		  redirect(getLink(getUserDir(), "profile"));
        }

        $aid = (int)get_var("aid");
		
        if (!isset($_SESSION['coupon_code'])) {
            $_SESSION['coupon_code'] = "";
        }

        if (isset($_POST['submit']) && $sql_arr = $this->checkForm()) {

            try {
                $this->daffny->DB->transaction('start');
                if (!$this->daffny->DB->isTransaction)
                    throw new FDException('Transaction failed');
                if ($memberId = $this->create($sql_arr)) {

                    $companyProfile = new CompanyProfile($this->daffny->DB);
                    $companyProfile->getByOwnerId($memberId);
                    $product = new Product($this->daffny->DB);
                    $product->load($sql_arr['products']);
                    $discount = 0;
                    $item_discount = array();
                    $item_discount[$product->id] = $this->calculateDiscount($product->id, $_SESSION['coupon_code']);
                    $amount = $product->price - $item_discount[$product->id];
                    $discount += $item_discount[$product->id];
                    if ((int)$sql_arr['additional_number'] > 0) {
                        $additional = new Product($this->daffny->DB);
                        $additional->load($sql_arr['additional']);
                        $item_discount[$additional->id] = $this->calculateDiscount($additional->id, $_SESSION['coupon_code']);
                        $amount += ($additional->price - $item_discount[$additional->id]) * (int)$sql_arr['additional_number'];
                        $discount += $item_discount[$additional->id];
                    }

                    if ((int)$sql_arr['storages'] > 0) {
                        $storage = new Product($this->daffny->DB);
                        $storage->load($sql_arr['storages']);
                        $item_discount[$storage->id] = $this->calculateDiscount($storage->id, $_SESSION['coupon_code']);
                        $amount += ($storage->price - $item_discount[$storage->id]);
                        $discount += $item_discount[$storage->id];
                    }

                    if ((int)$sql_arr['addon_aq'] > 0) {
                        $addon_aq = new Product($this->daffny->DB);
                        $addon_aq->load($sql_arr['addon_aq']);
                        $item_discount[$addon_aq->id] = $this->calculateDiscount($addon_aq->id, $_SESSION['coupon_code']);
                        $amount += ($addon_aq->price - $item_discount[$addon_aq->id]);
                        $discount += $item_discount[$addon_aq->id];
                    }

                    $coupon_id = $this->daffny->DB->selectField('id', Coupon::TABLE, "WHERE `code` LIKE('" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_SESSION['coupon_code']) . "')");

                    $orderData = array(
                        'member_id' => $memberId,
                        'status' => Orders::STATUS_PENDING,
                        'amount' => $amount,
                        'discount' => $discount,
                        'dirtytotal' => $amount + $discount,
                        'coupon_id' => $coupon_id,
                        'first_name' => trim($sql_arr['card_first_name']),
                        'last_name' => trim($sql_arr['card_last_name']),
                        'company' => $sql_arr['companyname'],
                        'address' => $sql_arr['address'],
                        'city' => $sql_arr['city'],
                        'state' => $sql_arr['state'],
                        'zip' => $sql_arr['zip'],
                        'card_type_id' => validate_cc_number($sql_arr['card_number']),
                        'card_number' => $sql_arr['card_number'],
                        'card_cvv2' => $sql_arr['card_cvv2'],
                        'card_expire' => $sql_arr['card_expire_month'] . substr($sql_arr['card_expire_year'], 2),
                        'card_first_name' => $sql_arr['card_first_name'],
                        'card_last_name' => $sql_arr['card_last_name'],
                        'did_any_help' => $sql_arr['did_any_help'],
                        'who_help' => $sql_arr['who_help']
                    );

                    $order = new Orders($this->daffny->DB);
                    $order->create($orderData);

                    $this->daffny->DB->insert('orders_details', array(
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => 1,
                            'price' => $product->price,
                            'discount' => $item_discount[$product->id],
                            'total' => $product->price - $item_discount[$product->id])
                    );

                    if (isset($additional)) {
                        $this->daffny->DB->insert('orders_details', array('order_id' => $order->id, 'product_id' => $additional->id, 'quantity' => $sql_arr['additional_number'], 'price' => $additional->price, 'discount' => $item_discount[$additional->id], 'total' => ($additional->price - $item_discount[$additional->id]) * $sql_arr['additional_number']));
                    }

                    if (isset($storage)) {
                        $this->daffny->DB->insert('orders_details', array(
                                'order_id' => $order->id,
                                'product_id' => $storage->id,
                                'quantity' => 1,
                                'price' => $storage->price,
                                'discount' => $item_discount[$storage->id],
                                'total' => $storage->price - $item_discount[$storage->id]
                            )
                        );
                    }

                    if (isset($addon_aq)) {
                        $this->daffny->DB->insert('orders_details', array(
                                'order_id' => $order->id,
                                'product_id' => $addon_aq->id,
                                'quantity' => 1,
                                'price' => $addon_aq->price,
                                'discount' => $item_discount[$addon_aq->id],
                                'total' => $addon_aq->price - $item_discount[$addon_aq->id]
                            )
                        );
                    }

                    if ($order->processAuthorize()) {
                    //if ( 1==1 ) {
                        $creditCard = new Creditcard($this->daffny->DB);
                        $creditCard->create(array(
                            'owner_id' => $order->member_id,
                            'cc_fname' => $order->first_name,
                            'cc_lname' => $order->last_name,
                            'cc_address' => $order->address,
                            'cc_city' => $order->city,
                            'cc_state' => $order->state,
                            'cc_zip' => $order->zip,
                            'cc_number' => $sql_arr['card_number'],
                            'cc_cvv2' => $order->card_cvv2,
                            'cc_type' => $order->card_type_id,
                            'cc_month' => $order->card_expire_month,
                            'cc_year' => '20' . $order->card_expire_year,
                        ));
                        $defaultSettings = new DefaultSettings($this->daffny->DB);
                        $defaultSettings->getByOwnerId($memberId);
                        $defaultSettings->update(array(
                            'billing_autopay' => 1,
                            'billing_cc_id' => $creditCard->id,
                        ));
                        $license = new License($this->daffny->DB);
                        $expire = new DateTime();
                        $expire->add(new DateInterval('P1' . ($product->period_id == Product::PERIOD_MONTH ? 'M' : 'Y')));

                        //get renewal product

                        $license->create(array(
                            'owner_id' => $memberId,
                            'order_id' => $order->id,
                            'users' => $sql_arr['additional_number'],
                            'expire' => $expire->format('Y-m-d'),
                            'period_type' => $product->period_id,
                            'product_id' => $product->id,
                            'storage_id' => isset($storage) ? $storage->id : 'NULL',
                            'addon_aq_id' => isset($addon_aq) ? $addon_aq->id : 'NULL',
                            'renewal_product_id' => $product->getRenewalProductId(),
                            'renewal_storage_id' => isset($storage) ? $storage->id : 'NULL',
                            'renewal_addon_aq_id' => isset($addon_aq) ? $addon_aq->id : 'NULL',
                            'renewal_users' => $sql_arr['additional_number']
                        ));

                        $_SESSION['invoice'] = $this->generateInvoice(array(
                            'initial' => $product->id,
                            'number' => $sql_arr['additional_number'],
                            'additional' => (isset($additional) ? $additional->id : null),
                            'storage' => (isset($storage) ? $storage->id : null),
                            'addon_aq' => (isset($addon_aq) ? $addon_aq->id : null),
                            'code' => $_SESSION['coupon_code'],
                            'card_first_name' => $sql_arr['card_first_name'],
                            'card_last_name' => $sql_arr['card_last_name'],
                            'card_number' => '*** ...' . substr($sql_arr['card_number'], -4),
                            'card_expiration' => $sql_arr['card_expire_month'] . '/' . substr($sql_arr['card_expire_year'], -2),
                            'order_id' => $order->id,
                            'order_date' => $order->register_date,

                            'email' => $sql_arr['email'],
                            'first_name' => trim($sql_arr['card_first_name']),
                            'last_name' => trim($sql_arr['card_last_name']),
                            'company' => $sql_arr['companyname'],
                            'address' => $sql_arr['address'],
                            'city' => $sql_arr['city'],
                            'state' => $sql_arr['state'],
                            'zip' => $sql_arr['zip'],
                            'phone' => $sql_arr['phone'],
                            'short' => false,
                            'header' => true,
                            'print' => true
                        ));
                        $billing = new Billing($this->daffny->DB);
                        $billing->create(array(
                            'type' => Billing::TYPE_CHARGE,
                            'owner_id' => $memberId,
                            'added' => date('Y-m-d H:i:s'),
                            'amount' => $order->amount,
                            'description' => 'License Charge',
                            'transaction_id' => ''
                        ));
                        $billing = new Billing($this->daffny->DB);
                        $billing->create(array(
                            'type' => Billing::TYPE_PAYMENT,
                            'owner_id' => $memberId,
                            'added' => date('Y-m-d H:i:s'),
                            'amount' => $order->amount,
                            'description' => 'License Payment',
                            'transaction_id' => $order->transaction_id,
                        ));


                        $this->daffny->DB->transaction('commit');

                        $member = new Member($this->daffny->DB);
                        $member->load($memberId);

                        $companyProfile->update(array("is_frozen" => 0));

                        $sql_arr = $sql_arr + array("account_id" => $member->id);

                        $this->sendWelcomeEmail($sql_arr['contactname'], $sql_arr['email'], $sql_arr);

                        $this->sendEmail($sql_arr['contactname'], $sql_arr['email'], "License purchased", 'license', $sql_arr + array('id' => $memberId));
                        redirect(getLink("registration", "success"));
                    } else {
                        $this->daffny->DB->transaction('rollback');
                        $this->setFlashError("Purchase license transaction was failed. Invalid CC Number / CVV.");
                    }
					
					
					
					
                } else {
                    throw new FDException('Failed to create new Member');
                }
            } catch (FDException $e) {
                $this->daffny->DB->transaction('rollback');
                $this->setFlashError($e->getMessage());
                //$this->setFlashError("Unexpected error occurred. Please contact system administrator");
            }
        }

       
         
        $this->input = $this->SaveFormVars();
        $this->input['form_action'] = getLink(getUserDir("2"), "registration");
        //print "------------".$aid;
		
	  $PlanInfo = "";
	  
	  if($aid>0)
	   {
          $sql = "SELECT 
		              id
					 , plan 
		             , contactname
		             , companyname
					 , email
		             , phone
					 , mcnumber
		             , type
					 , message
                     , DATE_FORMAT(create_date, '%m/%d/%Y %H:%i:%s') as create_date
                  FROM members_applied 
                  WHERE is_deleted = 0 and id='$aid'";
				  
		       $row = $this->daffny->DB->selectRow($sql);
				//print_r($row);
				if (!empty($row)) {
					
					  
					      $this->input['contactname'] = $row['contactname']; 
						  $this->input['companyname'] = $row['companyname']; 
						  $this->input['email'] = $row['email']; 
						  $this->input['phone'] = $row['phone']; 
						  $this->input['type'] = $row['type']; 
						  
						$plan = $row['plan'];
						
						if($plan == 1)
						   $PlanInfo = "Economy $150"; 
						elseif($plan == 2)
						   $PlanInfo = "Delux $199.99";
						elseif($plan == 3)
						   $PlanInfo = "Ultimate $399.97 ";   

					
				}
	   }
		
		
		$this->daffny->tpl->PlanInfo = $PlanInfo;
		
        $this->getForm();
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
     * Profile or Registration form
     *
     */
    protected function getForm()
    {
        $pm = new ProductManager($this->daffny->DB);
        $productsArr = array();
        $storages = array();
        $addon_aq = array();
        $additional = array();

        
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_INITIAL . " AND is_online = 1");
        foreach ($products as $product) {
            $productsArr[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        $this->daffny->tpl->products = $productsArr;
        //
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_ADDITIONAL . " AND is_online = 1");
        foreach ($products as $product) {
            $additional[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        $this->daffny->tpl->additional = $additional;

        //
        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_STORAGE . " AND is_online = 1");
        foreach ($products as $product) {
            $storages[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
            );
        }
        $this->daffny->tpl->storages = $storages;


        $products = $pm->get(null, 100, "type_id = " . Product::TYPE_ADDON_AQ . " AND is_online = 1");
        foreach ($products as $product) {
            $addon_aq[$product->id] = array(
                'name' => $product->name . " ($" . number_format($product->price, 2) . ")",
                'period' => $product->period_id,
                'id' => $product->id,
            );
        }
        $this->daffny->tpl->addon_aq = $addon_aq;

        $this->form->TextField('additional_number', '2', array('class' => 'digit-only', 'style' => 'width: 30px'), "Additional Users", '</td><td>');
		
        $this->form->TextField("contactname", 255, array(), $this->requiredTxt . "Contact Name", "</td><td>");
        $this->form->TextField("companyname", 255, array(), $this->requiredTxt . "Company name", "</td><td>");
        $this->form->TextField("phone", 32, array('class' => 'phone'), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("fax", 32, array('class' => 'phone'), "Fax", "</td><td>");
        $this->form->ComboBox("type", array("1" => "Broker/Dealership", "2" => "Broker/Dealership & Carrier", "3" => "Carrier"), array("style" => "width:200px;"), $this->requiredTxt . "Type", "</td><td>");
        $this->form->TextField('address', 255, array(), $this->requiredTxt . "Address", '</td><td>');
        $this->form->TextField('address2', 255, array(), '&nbsp;', '</td><td>');
        $this->form->TextField('city', 64, array(), $this->requiredTxt . "City", '</td><td>');
        $this->form->ComboBox('state', $this->getStates(true), array(), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('zip', 10, array('class' => 'zip'), $this->requiredTxt . "Zip Code", "</td><td>");


        // Login Information
        $this->form->TextField("username", 255, array(), $this->requiredTxt . "Username", "</td><td>");
        $this->form->TextField("email", 255, array(), $this->requiredTxt . "E-mail", "</td><td>");
        $this->form->PasswordField("password", 32, array(), $this->requiredTxt . "Password", "</td><td>");
        $this->form->PasswordField("password_confirm", 32, array(), $this->requiredTxt . "Confirm password", "</td><td>");
        $this->form->TextField('password_hint', 255, array(), $this->requiredTxt . 'Password Hint', '</td><td>');

        // Payment Information
        $this->form->TextField('card_first_name', 255, array(), $this->requiredTxt . "Cardholder First Name", '</td><td>');
        $this->form->TextField('card_last_name', 255, array(), $this->requiredTxt . "Cardholder Last Name", '</td><td>');
        $this->form->TextField('card_number', 19, array('class' => 'digit-only'), $this->requiredTxt . "Card Number", '</td><td>');
        $this->form->ComboBox('card_type', array('' => 'Select Type') + Orders::getCardTypes(), array(), $this->requiredTxt . "Card Type", '</td><td>');
        $this->form->ComboBox('card_expire_month', Orders::getExpireMonths(), array('style' => 'width:80px;'), $this->requiredTxt . "Expiration Date / CVV", '</td><td>');
        $this->form->ComboBox('card_expire_year', Orders::getExpiredYears(), array('style' => 'width:80px;'));
        $this->form->TextField('card_cvv2', 4, array('class' => 'digit-only', 'style' => 'width:40px;'));

        // Billing Information
        $this->form->CheckBox('billing_same', array(), "Same address used in Contact Information", '&nbsp;');
        $this->form->TextField('billing_address', 255, array(), $this->requiredTxt . "Address", '</td><td>');
        $this->form->TextField('billing_address2', 255, array(), '&nbsp;', '</td><td>');
        $this->form->TextField('billing_city', 64, array(), $this->requiredTxt . "City", '</td><td>');
        $this->form->ComboBox('billing_state', $this->getStates(true), array(), $this->requiredTxt . "State", "</td><td>");
        $this->form->TextField('billing_zip', 10, array('class' => 'zip'), $this->requiredTxt . "Zip Code", "</td><td>");


        $this->form->ComboBox("who_help", array("---Select One---") + $this->getCustomerServiceNames());

        $this->form->CheckBox("i_agree", array(), "I have read, understand and agree with the", "&nbsp;");


        //Content, title
        $row = $this->daffny->DB->selectRow("title, content", "content", "WHERE name = 'registration'");
        if (!empty($row)) {
            $this->title = $row['title'];
            $this->input['content'] = $row['content'];
        }

        if (isset($_GET['id']) && $_GET['id'] != "") {
            $this->input['ref_code'] = htmlspecialchars(strip_tags($_GET['id']));
        }

        $this->form->TextField("ref_code", 10, array(), "Promotional Code", "</td><td>");
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
        , 'username' => "Username"
        , 'phone' => "Phone"
        , 'email' => 'E-mail'
        , 'password' => 'Password'
        , 'password_confirm' => 'Confirm password'
        , 'password_hint' => 'Password Hint'
        );

        if (isset($_POST['companyname'])) {
            $row = $this->daffny->DB->selectRow("COUNT(*) as cnt", "app_company_profile", "WHERE `companyname` LIKE '" . mysqli_real_escape_string($this->daffny->DB->connection_id, $_POST['companyname']) . "'");
            if ($row['cnt'] > 0) {
                $this->err[] = "Company <strong>{$_POST['companyname']}</strong> is already registered in our system.";
            }
        }
        if (!isset($_POST["i_agree"]) || post_var("i_agree") != "1") {
            $this->err[] = "You should accept the Terms of Service";
        }

        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        $this->validateMember($sql_arr, $MemberID);
        if (count($this->err)) {
            return false;
        }

        $sql_arr['did_any_help'] = (int)post_var("did_any_help");
        if ($sql_arr['did_any_help'] == 1) {
            $sql_arr['who_help'] = post_var("who_help");
        }

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