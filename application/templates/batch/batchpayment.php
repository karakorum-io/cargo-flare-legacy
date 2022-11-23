<?php
/***************************************************************************************************
* Transportation Management Software
*
* Client:			FreightDragon
* Version:			1.0
* Start Date:		2011-10-05
* Author:			Freight Genie LLC
* E-mail:			admin@freightdragon.com
*
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
require_once(DAFFNY_PATH . "libs/upload.php");

class ApplicationBatchpayment extends ApplicationAction
{

    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('orders.common');

        $this->daffny->tpl->form_templates = $this->form->ComboBox('form_templates', array('' => 'Select One')+$this->getFormTemplates("orders"), array('style' => 'width:130px;','onChange'=>'printSelectedOrderForm()'), "", "", true);
        $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One')+$this->getEmailTemplates("orders"), array('style' => 'width:130px;','onChange'=>'emailSelectedOrderFormNew()'), "", "", true);

        parent::construct();
    }

    public function idx()
    {
        try {
            //$this->loadOrdersPageNew(Entity::STATUS_ACTIVE);
			$this->tplname = "batch.batch";
            $this->title = "Batch Payments";
			
			$this->form->TextArea("batch_order_ids", 15, 10, array("style" => "height:100px; width:380px;"), $this->requiredTxt . "Enter Order ID", "</td><td>");
		
        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink(''));
        }
    }
	
	public function batchsubmit()
    {
        try {
			print_r($_POST);
            $this->loadOrdersPageNew(Entity::STATUS_ACTIVE);
			
        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink(''));
        }
    }



    public function payments()
    {
        $this->check_access('payments');
        try {
            if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) throw new UserException('Invalid Order ID', getLink('orders'));
            $this->tplname = "orders.payments";
            $this->title = "Order Payments";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
			
            if (isset($_POST['payment_type'])) {
				
				/********** Log ********/
			    $info = "Order Payment";
			    $applog =  new Applog($this->daffny->DB);
			    $applog->createInformation($info);
				
                //if ($entity->readonly) throw new UserException("Access dentied", getLink('oreders/payments/id' . $_GET['id']));
                $insert_arr = array();
                switch ($_POST['payment_type']) {
                    case "internally":
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                        $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received']));
                        $from_to = explode("-", $_POST['from_to']);
                        $insert_arr['fromid'] = $from_to[0];
                        $insert_arr['toid'] = $from_to[1];
                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                        $insert_arr['amount'] = number_format((float)$_POST['amount'], 2, '.', '');
                        //$insert_arr['notes'] = $_POST['notes'];
                        $insert_arr['method'] = $_POST['method'];
                        $insert_arr['transaction_id'] = $_POST['transaction_id'];
                        switch ($_POST['method']) {
                            case "9":
                                $insert_arr['cc_number'] = $_POST['cc_numb'];
                                if ($_POST['cc_type'] != 0) {
                                    $insert_arr['cc_type'] = $_POST['cc_type'];
                                } else {
                                    $insert_arr['cc_type'] = $_POST['cc_type_other'];
                                }
                                $insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
                                $insert_arr['cc_auth'] = $_POST['cc_auth'];
                                break;
                            case "1":
                            case "2":
                            case "3":
                            case "4":
                                $insert_arr['check'] = $_POST['ch_number'];
                                break;
                        }
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string)$_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");
							
							/* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment updated internally for the amount of $ ".number_format((float)$_POST['amount'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
                        } else {
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");
							
							/* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment processed internally for the amount of $ ".number_format((float)$_POST['amount'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
                        }
                        break;
					case "carrier":
					
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                        $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_carrier']));
                        $from_to = explode("-", $_POST['from_to_carrier']);
                        $insert_arr['fromid'] = $from_to[0];
                        $insert_arr['toid'] = $from_to[1];
                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                        $insert_arr['amount'] = number_format((float)$_POST['amount_carrier'], 2, '.', '');
                        //$insert_arr['notes'] = $_POST['notes'];
                        $insert_arr['method'] = $_POST['method'];
                        $insert_arr['transaction_id'] = $_POST['transaction_id'];
                        switch ($_POST['method']) {
                            case "9":
                                $insert_arr['cc_number'] = $_POST['cc_numb'];
                                if ($_POST['cc_type'] != 0) {
                                    $insert_arr['cc_type'] = $_POST['cc_type'];
                                } else {
                                    $insert_arr['cc_type'] = $_POST['cc_type_other'];
                                }
                                $insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
                                $insert_arr['cc_auth'] = $_POST['cc_auth'];
                                break;
                            case "1":
                            case "2":
                            case "3":
                            case "4":
                                $insert_arr['check'] = $_POST['ch_number'];
                                break;
                        }
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string)$_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");
							
							    /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment updated internally for the amount of $ ".number_format((float)$_POST['amount_carrier'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
								
                        } else {
						
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");
							
							 /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment processed internally for the amount of $ ".number_format((float)$_POST['amount_carrier'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
                        }
                        break;
					 case "terminal":
					 
                        $insert_arr['entity_id'] = $_GET['id'];
                        $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                        $insert_arr['date_received'] = date("Y-m-d", strtotime($_POST['date_received_terminal']));
                        $from_to = explode("-", $_POST['from_to_terminal']);
                        $insert_arr['fromid'] = $from_to[0];
                        $insert_arr['toid'] = $from_to[1];
                        $insert_arr['entered_by'] = $_SESSION['member_id'];
                        $insert_arr['amount'] = number_format((float)$_POST['amount_terminal'], 2, '.', '');
                        //$insert_arr['notes'] = $_POST['notes'];
                        $insert_arr['method'] = $_POST['method'];
                        $insert_arr['transaction_id'] = $_POST['transaction_id'];
                        switch ($_POST['method']) {
                            case "9":
                                $insert_arr['cc_number'] = $_POST['cc_numb'];
                                if ($_POST['cc_type'] != 0) {
                                    $insert_arr['cc_type'] = $_POST['cc_type'];
                                } else {
                                    $insert_arr['cc_type'] = $_POST['cc_type_other'];
                                }
                                $insert_arr['cc_exp'] = date("Y-m-d", strtotime($_POST['cc_exp_year'] . "-" . $_POST['cc_exp_month'] . "-01"));
                                $insert_arr['cc_auth'] = $_POST['cc_auth'];
                                break;
                            case "1":
                            case "2":
                            case "3":
                            case "4":
                                $insert_arr['check'] = $_POST['ch_number'];
                                break;
                        }
                        $payment = new Payment($this->daffny->DB);
                        if (isset($_POST['payment_id']) && ctype_digit((string)$_POST['payment_id'])) {
                            $payment->load($_POST['payment_id']);
                            unset($insert_arr['entity_id']);
                            $payment->update($insert_arr);
                            $this->setFlashInfo("Your payment has been updated.");
							
							    /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment updated internally for the amount of $ ".number_format((float)$_POST['amount_terminal'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
								
                        } else {
						
                            $payment->create($insert_arr);
                            $this->setFlashInfo("Your payment has been processed.");
							
							 /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $_GET['id'],
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment processed internally for the amount of $ ".number_format((float)$_POST['amount_terminal'], 2, '.', ''));
								
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
                        }
                        break;	
                    case "gateway":

                        $defaultSettings = new DefaultSettings($this->daffny->DB);
                        $defaultSettings->getByOwnerId(getParentId());

                        if (in_array($defaultSettings->current_gateway, array(1, 2))) {

                            if ($defaultSettings->current_gateway == 1) { //PayPal
                                if (trim($defaultSettings->paypal_api_username) == ""
                                    || trim($defaultSettings->paypal_api_password) == ""
                                    || trim($defaultSettings->paypal_api_signature) == ""
                                ) {
                                    $this->err[] = "PayPal: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }

                            if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                if (trim($defaultSettings->anet_api_login_id) == ""
                                    || trim($defaultSettings->anet_trans_key) == ""
                                ) {
                                    $this->err[] = "Autorize.net: Please complete API Credentials under 'My Profile > Default Settings'";
                                }
                            }
                        } else {
                            $this->err[] = "There is no active Payments Gateway under 'My Profile > Default Settings'";
                        }

                        $amount = 0;

                        if (isset($_POST['gw_pt_type']) && in_array($_POST['gw_pt_type'], array("other", "deposit", "balance"))) {
                            switch (post_var("gw_pt_type")) {
                                case "deposit":
                                    $amount = (float)post_var("deposit_pay");//$entity->total_deposit;
                                    break;
                                case "balance":
                                    $amount = (float)post_var("tariff_pay");//$entity->total_tariff;
                                    break;
                                case "other":
                                    $amount = (float)post_var("other_amount");
                                    break;
                            }
                        } else {
                            $this->err[] = 'Please choose Payment Amount';
                        }

                        if ($amount == 0) {
                            $this->err[] = 'Amount can not be $0.00.';
                        }

                        //$this->isEmpty("cc_fname", "First Name");
                        //$this->isEmpty("cc_lname", "Last Name");
                        //$this->isEmpty("cc_address", "Address");
                        //$this->isEmpty("cc_city", "City");
                        //$this->isEmpty("cc_state", "State");
                        //$this->isEmpty("cc_zip", "Zip Code");
                        //$this->isEmpty("cc_cvv2", "CVV");
                        $this->isEmpty("cc_number", "CC Number");
                        $this->isEmpty("cc_type", "CC Type");
                        $this->isEmpty("cc_month", "Exp. Month");
                        $this->isEmpty("cc_year", "Exp. Year");

                        $arr = array(
                            "other_amount" => post_var("other_amount")
                        , "gw_pt_type" => post_var("gw_pt_type")
                        , "cc_fname" => post_var("cc_fname")
                        , "cc_lname" => post_var("cc_lname")
                        , "cc_address" => post_var("cc_address")
                        , "cc_city" => post_var("cc_city")
                        , "cc_state" => post_var("cc_state")
                        , "cc_zip" => post_var("cc_zip")
                        , "cc_cvv2" => post_var("cc_cvv2")
                        , "cc_number" => post_var("cc_number")
                        , "cc_type" => post_var("cc_type")
                        , "cc_month" => post_var("cc_month")
                        , "cc_year" => post_var("cc_year")
                        , "cc_type_name" => Payment::getCCTypeById(post_var("cc_type"))
                        );
                        $this->input = $arr;


                        $pay_arr = $arr + array(
                            "amount" => (float)$amount
                        , "paypal_api_username" => trim($defaultSettings->paypal_api_username)
                        , "paypal_api_password" => trim($defaultSettings->paypal_api_password)
                        , "paypal_api_signature" => trim($defaultSettings->paypal_api_signature)
                        , "anet_api_login_id" => trim($defaultSettings->anet_api_login_id)
                        , "anet_trans_key" => trim($defaultSettings->anet_trans_key)
                        , "notify_email" => trim($defaultSettings->notify_email)
                        , "order_number" => trim($entity->getNumber())
                        );

                        $ret = array();
                        /* Process payments */
                        if (!count($this->err)) {
                            if ($defaultSettings->current_gateway == 2) { //Authorize.net
                                $ret = $this->processAuthorize($pay_arr);
                            }
                            if ($defaultSettings->current_gateway == 1) { //PayPal
                                $ret = $this->processPayPal($pay_arr);
                            }

                            //place //
                            if (isset($ret['success']) && $ret['success'] == true) {
								
                                //insert
                                $insert_arr['entity_id'] = (int)$_GET['id'];
                                $insert_arr['number'] = Payment::getNextNumber($_GET['id'], $this->daffny->DB);
                                $insert_arr['date_received'] = date("Y-m-d H:i:s");
                                $insert_arr['fromid'] = Payment::SBJ_SHIPPER;
                                $insert_arr['toid'] =  Payment::SBJ_COMPANY;
                                $insert_arr['entered_by'] = $_SESSION['member_id'];
                                $insert_arr['amount'] = number_format((float)$pay_arr['amount'], 2, '.', '');
                                $insert_arr['notes'] = ($defaultSettings->current_gateway == 2 ? "Authorize.net " : "PayPal ") . $ret['transaction_id'];
                                $insert_arr['method'] = Payment::M_CC;
                                $insert_arr['transaction_id'] = $ret['transaction_id'];
                                $insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
                                $insert_arr['cc_type'] = $pay_arr['cc_type_name'];
                                $insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
                                $payment = new Payment($this->daffny->DB);
                                $payment->create($insert_arr);

                               
                                 if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) != '' && trim($entity->archived) == '')         {
		                             $entity->setStatus(Entity::STATUS_DELIVERED);
	                              }

								 /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $entity->id,
									"sender_id" => $_SESSION['member_id'],
									"type" => 3,
									"system_admin" => 1,
									"text" => "CREDIT CARD PROCESSED FOR THE AMOUNT OF $ ".number_format((float)$pay_arr['amount'], 2, '.', ''));
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
								
								try {
								 	$paymentcard = new Paymentcard($this->daffny->DB);
									$pc_arr = $pay_arr;
									$pc_arr['entity_id'] = (int)$_GET['id'];
									$pc_arr['owner_id'] = getParentId();
									$paymentcard->key = $this->daffny->cfg['security_salt'];
									$paymentcard->create($pc_arr);
                                } catch (Exception $exc) {}
								  
                                $this->setFlashInfo("Your payment has been processed.");
                                redirect(getLink("orders", "payments", "id", (int)$_GET['id']));
                            } else {
                                $this->err[] = $ret['error'];
								/* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $entity->id,
									"sender_id" => $_SESSION['member_id'],
									"type" => 3,
									"system_admin" => 1,
									"text" => "Payment Error:".$ret['error']);
								//print_r($note_array);
								$note = new Note($this->daffny->DB);
								$note->create($note_array);
                            }
                        }
                        break;
                    default:
                        throw new UserException("Invalid form data", getLink('orders/payments/id/' . $_GET['id']));
                        break;
                }
				
	            if ($entity->status == Entity::STATUS_ISSUES && $entity->isPaidOff() && trim($entity->delivered) != '' && trim($entity->archived) == '') {
		            $entity->setStatus(Entity::STATUS_DELIVERED);
	            }
				
            }
			
			
			
            $entity->getVehicles();
            $this->applyOrder(Payment::TABLE);
			
            $this->order->setDefault('date_received', 'desc');
            $paymentManager = new PaymentManager($this->daffny->DB);
            $this->daffny->tpl->payments = $paymentManager->getPayments($_GET['id'], $this->order->getOrder(), $_SESSION['per_page']);
            $this->pager = $paymentManager->getPager();
			
			
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['pager'] = $pager_html;
            $this->daffny->tpl->entity = $entity;
			
			
			$notes = $entity->getNotes(false," order by id desc ");
		    $this->daffny->tpl->notes = $notes;
			
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => 'Orders', getLink('orders/show/id/' . $_GET['id']) => 'Order #' . $entity->getNumber(), '' => 'Payments'));
            $this->form->TextField('date_received', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
            $this->form->TextField('amount', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->TextField('transaction_id', '32', array(), "Transaction ID", "</td><td>");
			
			$from_to_options = array(
                '' => 'Select One',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
                Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company'
            );
            $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");
			
			$from_to_options_carrier = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_CARRIER => 'Shipper to Carrier',
            );
			$this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
			$this->form->ComboBox('from_to_carrier', $from_to_options_carrier, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
			$this->form->TextField('date_received_carrier', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
			
			$from_to_options_terminal = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
                Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
                Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company'
            );
			$this->form->TextField('amount_terminal', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
			$this->form->ComboBox('from_to_terminal', $from_to_options_terminal, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
			$this->form->TextField('date_received_terminal', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
			
            /*$from_to_options = array(
                '' => 'Select One',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
                Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company',
                Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
                Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company'
            );
			
            $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");*/
           // $this->form->TextArea('notes', 10, 10, array('style' => 'height: 50px;'), 'Notes', '</td><td valign="top">');

            //$this->form->helperPaymentType("gw_pt_type", $entity->getTotalDeposit(), $entity->getTotalTariff(), $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));
            /* CC cards */
            /* Prefill CC */
            $entityCreditCard = $entity->getCreditCard();
			
            if (!isset($_POST['submit'])) {
				
				$paymentCard = new Paymentcard($this->daffny->DB);
				
                $paymentCard->key = $this->daffny->cfg['security_salt'];
                $paymentCard->loadLastCC((int)$_GET['id'], getParentId());
                if ($paymentCard->isLoaded()) {
                    $this->input['cc_fname'] = $paymentCard->cc_fname;
                    $this->input['cc_lname'] = $paymentCard->cc_lname;
                    $this->input['cc_address'] = $paymentCard->cc_address;
                    $this->input['cc_city'] = $paymentCard->cc_city;
                    $this->input['cc_state'] = $paymentCard->cc_state;
                    $this->input['cc_zip'] = $paymentCard->cc_zip;
                    $this->input['cc_cvv2'] = $paymentCard->cc_cvv2;
                    $this->input['cc_number'] = $paymentCard->cc_number;
                    $this->input['cc_type'] = $paymentCard->cc_type;
                    $this->input['cc_month'] = $paymentCard->cc_month;
                    $this->input['cc_year'] = $paymentCard->cc_year;
                } else {
					
                    $this->input['cc_fname'] = $entityCreditCard->fname;
                    $this->input['cc_lname'] = $entityCreditCard->lname;
                    $this->input['cc_address'] = $entityCreditCard->address;
                    $this->input['cc_city'] = $entityCreditCard->city;
                    $this->input['cc_state'] = $entityCreditCard->state;
                    $this->input['cc_zip'] = $entityCreditCard->zip;
                    $this->input['cc_cvv2'] = $entityCreditCard->cvv2;
                    $this->input['cc_number'] = $entityCreditCard->number;
                    $this->input['cc_type'] = $entityCreditCard->type;
                    $this->input['cc_month'] = $entityCreditCard->month;
                    $this->input['cc_year'] = $entityCreditCard->year;
                }
            }

            $this->form->TextField("cc_fname", 50, array(), "First Name", "</td><td>");
            $this->form->TextField("cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
            $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
            $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
            $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");


            $this->input['e_cc_fname'] = $entityCreditCard->fname;
            $this->input['e_cc_lname'] = $entityCreditCard->lname;
            $this->input['e_cc_address'] = $entityCreditCard->address;
            $this->input['e_cc_city'] = $entityCreditCard->city;
            $this->input['e_cc_state'] = $entityCreditCard->state;
            $this->input['e_cc_zip'] = $entityCreditCard->zip;
            $this->input['e_cc_cvv2'] = $entityCreditCard->cvv2;
            if ($_SESSION['member']['id'] == $_SESSION['member']['parent_id']) {
                $this->input['e_cc_number'] = $entityCreditCard->number;
            } else {
                $last_four = substr($entityCreditCard->number, strlen($entityCreditCard->number) - 5, 4);
                $this->input['e_cc_number'] = "000000000000" . $last_four;
            }
            $this->input['e_cc_type'] = $entityCreditCard->type;
            $this->input['e_cc_month'] = $entityCreditCard->month;
            $this->input['e_cc_year'] = $entityCreditCard->year;

            $this->form->TextField("e_cc_fname", 50, array(),  "First Name", "</td><td>");
            $this->form->TextField("e_cc_lname", 50, array(),  "Last Name", "</td><td>");
            $this->form->TextField("e_cc_address", 255, array(),  "Address", "</td><td>");
            $this->form->TextField("e_cc_city", 100, array(),  "City", "</td><td>");
            $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"),  "State", "</td><td>");
            $this->form->TextField("e_cc_zip", 11, array("class" => "zip", "style" => "width:100px;"),  "Zip Code", "</td><td>");
            $this->form->TextField("e_cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"),  "CVV", "</td><td>");
            $this->form->TextField("e_cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");

            if (!isset($_POST['payment_type']) || $_POST['payment_type'] == "") {
                $this->input['payment_type_selector'] = 'internally';
            } else {
                $this->input['payment_type_selector'] = $_POST['payment_type'];
            }
            //$this->form->helperGWType("payment_type_selector");
            $member = new Member($this->daffny->DB);
			$member->load($_SESSION['member_id']);
		    $company = $member->getCompanyProfile();
			/*
			print "<pre>".$_SESSION['member_id'];
			print_r($company);
			print "</pre>";
			*/
			$is_carrier = 1;
			if($company->is_carrier == 1)
			  $is_carrier = 0;
			
            $this->form->helperGWType("payment_type_selector",array(),$is_carrier);
			
			$this->daffny->tpl->is_carrier = $is_carrier;
			
            $balances = array(
                'shipper' => 0,
                'carrier' => 0,
                'pterminal' => 0,
                'dterminal' => 0,
            );

            $depositRemains = 0;
			$shipperRemains = 0;

            // We owe them
            switch ($entity->balance_paid_by) {
                //case Entity::BALANCE_INVOICE_CARRIER:
                case Entity::BALANCE_COP_TO_CARRIER_CASH:
                case Entity::BALANCE_COP_TO_CARRIER_CHECK:
                case Entity::BALANCE_COP_TO_CARRIER_COMCHECK:
                case Entity::BALANCE_COP_TO_CARRIER_QUICKPAY:
                case Entity::BALANCE_COD_TO_CARRIER_CASH:
                case Entity::BALANCE_COD_TO_CARRIER_CHECK:
                case Entity::BALANCE_COD_TO_CARRIER_COMCHECK:
                case Entity::BALANCE_COD_TO_CARRIER_QUICKPAY:
					$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
					//$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_SHIPPER, false);
					$balances['we_carrier'] = 0;
					$balances['we_shipper'] = 0;
					$balances['they_carrier'] = 0;
					$balances['they_shipper'] = $entity->getTotalDeposit(false) - $shipperPaid;
					$balances['they_shipper_paid'] = $shipperPaid;
					
					$depositRemains = $entity->getTotalDeposit(false) - $shipperPaid;;
			        $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;
					
					break;
                //--
                case Entity::BALANCE_COMPANY_OWES_CARRIER_CASH:
                case Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK:
                case Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
                case Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
				case Entity::BALANCE_COMPANY_OWES_CARRIER_ACH:
				
					$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
					$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
					///$shipperPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_COMPANY, Payment::SBJ_SHIPPER, false); 
					$balances['they_carrier'] = 0;
					$balances['we_shipper'] = 0;
					$balances['we_carrier'] = $entity->getCarrierPay(false) + $entity->getPickupTerminalFee(false) + $entity->getDropoffTerminalFee(false) - $carrierPaid;
					$balances['we_carrier_paid'] = $carrierPaid;
					$balances['they_shipper'] = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;
					$balances['they_shipper_paid'] = $shipperPaid;
					
					$depositRemains = $entity->getTotalDeposit(false) - $shipperPaid;
			        $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $shipperPaid;
					
		            break;
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CASH:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
                case Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
					$carrierPaid = $paymentManager->getFilteredPaymentsTotals($entity->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
					$balances['we_shipper'] = 0;
					$balances['we_carrier'] = 0;
					$balances['they_shipper'] = 0;
					$balances['they_carrier'] = $entity->getTotalDeposit(false) - $carrierPaid;
					$balances['they_carrier_paid'] = $carrierPaid;
					
					$depositRemains = $entity->getTotalDeposit(false) - $carrierPaid;
			        $shipperRemains = $entity->getCost(false) + $entity->getTotalDeposit(false) - $carrierPaid;
					
                    break;
	            default:
		            $balances['we_carrier'] = 0;
		            $balances['we_shipper'] = 0;
		            $balances['they_carrier'] = 0;
		            $balances['they_shipper'] = 0;
		            break;
            }
			
			 if($depositRemains<0)
			   $depositRemains = 0;
			 if($shipperRemains<0)  
			   $shipperRemains = 0;
			   
			   
			  $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));  
			
            foreach ($balances as $key => $balance) {
	            if (stripos($key, '_paid') === false) {
		            if (isset($balances[$key.'_paid'])) {
			            if ($balance > 0) {
				            $this->input[$key] = "<span class='red'>$ " . number_format(abs($balance), 2)."</span>";
			            } else {
				            $this->input[$key] = '';
			            }
			            if ($balances[$key.'_paid'] > 0) {
				            $this->input[$key.'_paid'] = "<span class='green'>$ " . number_format(abs($balances[$key.'_paid']), 2)."</span>";
			            } else {
				            $this->input[$key.'_paid'] = '';
			            }
		            } else {
			            if ($balance > 0) {
			                $this->input[$key] = "<span class='red'>$ " . number_format(abs($balance), 2)."</span>";
			            } else {
				            $this->input[$key] = '$ 0.00';
			            }
			            $this->input[$key.'_paid'] = '';
		            }
	            }
            }

        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }


    public function history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) throw new UserException("Invalid Order ID", getLink('orders'));
            $this->tplname = "orders.history";
            $this->title = "Order History";
            $this->section = "Orders";
            $this->applyOrder("app_history");
            $this->order->setDefault('change_date', 'desc');
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", getLink("orders/show/id/" . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "History"));
            $historyManager = new HistoryManager($this->daffny->DB);
            $this->daffny->tpl->history = $historyManager->getHistory($this->order->getOrder(), $_SESSION['per_page'], " `entity_id` = " . (int)$_GET['id']);
            $this->pager = $historyManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }

    public function show()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) throw new UserException("Invalid Order ID", getLink('orders'));
            $this->tplname = "orders.detail";
            $this->title = "Order Details";
			
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
			
			/********** Log ********/
			$info = "Order Details-".$entity->number."(".$entity->id.")";
			$applog =  new Applog($this->daffny->DB);
			$applog->createInformation($info);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int)$_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => ""), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => ""), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
	        if ($ds = $entity->getDispatchSheet()) {
		        $this->daffny->tpl->dispatchSheet = $ds;
	        }
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            //$notes = $entity->getNotes(false, $this->order->getOrder());
			$notes = $entity->getNotes(false," order by id desc ");
			
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();
        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }
	
	public function showsearch()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) throw new UserException("Invalid Order ID", getLink('orders'));
            $this->tplname = "orders.detailsearch";
            $this->title = "Order Details";
			
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
			
			/********** Log ********/
			$info = "Order Details-".$entity->number."(".$entity->id.")";
			$applog =  new Applog($this->daffny->DB);
			$applog->createInformation($info);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int)$_GET['id']);
            $this->form->TextField("mail_to", 255, array(), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => ""), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; "), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
	        if ($ds = $entity->getDispatchSheet()) {
		        $this->daffny->tpl->dispatchSheet = $ds;
	        }
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            //$notes = $entity->getNotes(false, $this->order->getOrder());
			$notes = $entity->getNotes(false," order by id desc ");
			
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", 'Order #' . $entity->getNumber()));
            $this->getDispatchForm();
        } catch (FDException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
			$applog =  new Applog($this->daffny->DB);
			$applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect($e->getRedirectUrl());
        }
    }


   

    protected function loadOrdersPage($status)
    {
		
		
        $this->tplname = "orders.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = "orders.orders";
		
		$this->applyOrder(Entity::TABLE);
	    $this->order->Fields[] = 'shipper';
	    $this->order->Fields[] = 'origin';
	    $this->order->Fields[] = 'destination';
	    $this->order->Fields[] = 'avail';
	    $this->order->Fields[] = 'tariff';
        
		
        switch ($status) {
            case Entity::STATUS_ACTIVE:
                $this->title = "My Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders"));
				$this->order->setDefault('created', 'desc');
                break;
            case Entity::STATUS_ONHOLD:
                $this->title = "Orders On Hold";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Hold'));
				$this->order->setDefault('hold_date', 'desc');
                break;
            case Entity::STATUS_ARCHIVED:
                $this->title = "Orders Archived";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Archived'));
				$this->order->setDefault('archived', 'desc');
                break;
            case Entity::STATUS_POSTED:
                $this->title = "Orders Posted to FB";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Posted CD'));
				$this->order->setDefault('posted', 'desc');
                break;
            case Entity::STATUS_NOTSIGNED:
                $this->title = "Orders Notsigned";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Not Signed'));
				$this->order->setDefault('not_signed', 'desc');
                break;
            case Entity::STATUS_DISPATCHED:
                $this->title = "Orders Dispatched";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Dispatched'));
				$this->order->setDefault('dispatched', 'desc');
                break;
            case Entity::STATUS_PICKEDUP:
                $this->title = "Orders Picked Up";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Picked Up'));
				$this->order->setDefault('actual_pickup_date', 'desc');
                break;
            case Entity::STATUS_DELIVERED:
                $this->title = "Orders Delviered";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Delivered'));
				$this->order->setDefault('delivered', 'desc');
                break;
            case Entity::STATUS_ISSUES:
                $this->title = "Orders Issues";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'Issue'));
				$this->order->setDefault('issue_date', 'desc');
                break;
            default:
                $this->title = "All Orders";
                $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('orders') => "Orders", '' => 'All'));
				$this->order->setDefault('id', 'desc');
                break;
        }
        
		/********** Log ********/
        $info = "Show order listing: ".$this->title;
		$applog =  new Applog($this->daffny->DB);
		$applog->createInformation($info);
		
        $entityManager = new EntityManager($this->daffny->DB);

        switch ($this->order->CurrentOrder) {
            case 'created':
                $this->order->setTableIndex('e');
                break;
        }

        if (!is_null($status)) {
            $this->daffny->tpl->entities = $entityManager->getEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $status, $_SESSION['per_page']);
        } else {
            $this->daffny->tpl->entities = $entityManager->getAllEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $_SESSION['per_page']);
        }
        $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);
		
        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
        , 'current_page' => $this->pager->CurrentPage
        , 'pages_total' => $this->pager->PagesTotal
        , 'records_total' => $this->pager->RecordsTotal
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
		
		
        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
		
        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
		
		$this->form->ComboBox('issue_type',
			array('' => 'Select One','1' => 'By Customer','2' => 'By Carrier','3' => 'Partial payments') , array("elementname" => "select","style"=>"width:150px;","class" => "elementname" ,"onchange"=>"getElementById('issue_form').submit();"), '', '</td><td>');
		
        $this->getDispatchForm();
		
		$this->getPaymentForm();
    }
	
 protected function loadOrdersPageNew($status)
    {
		
		
		
        $this->tplname = "orders.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = "batch.batch_payment";
		//$this->tplname = "batch.batch_payment";
		
		$this->applyOrder(Entity::TABLE);
	    $this->order->Fields[] = 'shipper';
	    $this->order->Fields[] = 'origin';
	    $this->order->Fields[] = 'destination';
	    $this->order->Fields[] = 'avail';
	    $this->order->Fields[] = 'tariff';
        
		
        
		
		
        $entityManager = new EntityManager($this->daffny->DB);

       
/*
        if (!is_null($status)) {
            $this->daffny->tpl->entities = $entityManager->getEntitiesArrData(Entity::TYPE_ORDER, $this->order->getOrder(), $status, $_SESSION['per_page']);
        } else {
            $this->daffny->tpl->entities = $entityManager->getAllEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $_SESSION['per_page']);
        }
	*/	
		$this->daffny->tpl->entities = $entityManager->getAllEntities(Entity::TYPE_ORDER, $this->order->getOrder(), $_SESSION['per_page']);
        $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);
		
        $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
        $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
        $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
        $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
        $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
        $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
        $this->pager = $entityManager->getPager();
        $tpl_arr = array(
            'navigation' => $this->pager->getNavigation()
        , 'current_page' => $this->pager->CurrentPage
        , 'pages_total' => $this->pager->PagesTotal
        , 'records_total' => $this->pager->RecordsTotal
        );
        $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
		
		
        $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
		
        $this->section = "Orders";
        $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
		
		$this->form->ComboBox('issue_type',
			array('' => 'Select One','1' => 'By Customer','2' => 'By Carrier','3' => 'Partial payments') , array("elementname" => "select","style"=>"width:150px;","class" => "elementname" ,"onchange"=>"getElementById('issue_form').submit();"), '', '</td><td>');
		
        
    
   }
	
	
protected function getPaymentForm()
    {
		if (!isset($_POST['payment_type']) || $_POST['payment_type'] == "") {
                $this->input['payment_type_selector'] = 'internally';
            } else {
                $this->input['payment_type_selector'] = $_POST['payment_type'];
            }
		    $member = new Member($this->daffny->DB);
			$member->load($_SESSION['member_id']);
		    
			$company = $member->getCompanyProfile();
			$is_carrier = 1;
			if($company->is_carrier == 1)
			  $is_carrier = 0;
			
            $this->form->helperGWType("payment_type_selector",array(),$is_carrier);
			
			$this->daffny->tpl->is_carrier = $is_carrier;
			
		   $this->form->TextField('date_received', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
			
			
            $this->form->TextField('amount', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
            $this->form->TextField('transaction_id', '32', array(), "Transaction ID", "</td><td>");
            $from_to_options = array(
                '' => 'Select One',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_COMPANY => 'Shipper to Company',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_SHIPPER => 'Company to Shipper',
                Payment::SBJ_CARRIER . '-' . Payment::SBJ_COMPANY => 'Carrier to Company'
            );
            $this->form->ComboBox('from_to', $from_to_options, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
            $this->form->ComboBox('method', array('' => 'Select One') + Payment::$method_name, array(), "Method", "</td><td>");
			
			$from_to_options_carrier = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_CARRIER => 'Company to Carrier',
                Payment::SBJ_SHIPPER . '-' . Payment::SBJ_CARRIER => 'Shipper to Carrier',
            );
			$this->form->TextField('amount_carrier', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
			$this->form->ComboBox('from_to_carrier', $from_to_options_carrier, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
			$this->form->TextField('date_received_carrier', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
			
			$from_to_options_terminal = array(
                '' => 'Select One',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_P => 'Company to Pickup Terminal',
                Payment::SBJ_COMPANY . '-' . Payment::SBJ_TERMINAL_D => 'Company to Delivery Terminal',
                Payment::SBJ_TERMINAL_P . '-' . Payment::SBJ_COMPANY => 'Pickup Terminal to Company',
                Payment::SBJ_TERMINAL_D . '-' . Payment::SBJ_COMPANY => 'Delivery Terminal to Company'
            );
			$this->form->TextField('amount_terminal', '16', array('class' => 'decimal'), $this->requiredTxt . 'Amount', '</td><td valign="top">');
			$this->form->ComboBox('from_to_terminal', $from_to_options_terminal, array(), $this->requiredTxt . "Payment From/To", "</td><td>");
			$this->form->TextField('date_received_terminal', '10', array("style" => "width:100px;"), $this->requiredTxt . "Date Received", "</td><td>");
			
		
		    $this->form->TextField("cc_fname", 50, array("elementname" => "input"), "First Name", "</td><td>");
            $this->form->TextField("cc_lname", 50, array(), "Last Name", "</td><td>");
            $this->form->TextField("cc_address", 255, array(), "Address", "</td><td>");
            $this->form->TextField("cc_city", 100, array(), "City", "</td><td>");
            $this->form->ComboBox("cc_state", array("" => "Select State") + $this->getStates(), array("style" => "width:150px;"), "State", "</td><td>");
            $this->form->TextField("cc_zip", 11, array("class" => "zip", "style" => "width:100px;"), "Zip Code", "</td><td>");
            $this->form->TextField("cc_cvv2", 4, array("class" => "cvv", "style" => "width:75px;"), "CVV", "</td><td>");
            $this->form->TextField("cc_number", 16, array("class" => "creditcard"), $this->requiredTxt . "Card Number", "</td><td>");
            $this->form->ComboBox("cc_type", array("" => "--Select--") + $this->getCCTypes(), array("style" => "width:150px;","id"=>"cc_type_1"), $this->requiredTxt . "Type", "</td><td>");
            $this->form->ComboBox("cc_month", array("" => "--") + $this->months, array("style" => "width:50px;"), $this->requiredTxt . "Exp. Date", "</td><td>");
            $this->form->ComboBox("cc_year", array("" => "--") + $this->getCCYears(), array("style" => "width:75px;"), "", "");
			
			
			   
			 $this->form->helperPaymentType("gw_pt_type", $depositRemains, $shipperRemains, $this->form->MoneyField('other_amount', 16, array(), '', '&nbsp;$'));

	}


    public function search()
    {
		
		
        try {
            if (count($_POST) == 0) throw new UserException('Access Deined', getLink('orders'));
            $this->initGlobals();
            $this->tplname = "orders.main";
            $data_tpl = "orders.orders";
            $this->title = "Orders search results";
            $this->daffny->tpl->status = "Archived";
			
			/********** Log ********/
			    $info = "Search Order";
			    $applog =  new Applog($this->daffny->DB);
			    $applog->createInformation($info);
			
            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("orders") => "Orders", '' => 'Search'));
            //$this->daffny->tpl->entities = $entityManager->search(Entity::TYPE_ORDER, $_POST['search_type'], $_POST['search_string'], $_SESSION['per_page']);
			$this->daffny->tpl->entities = $entityManager->searchAll(Entity::TYPE_ORDER, $_POST['search_type'], $_POST['search_string'], $_SESSION['per_page']);
			
            $entities_count = $entityManager->getCount(Entity::TYPE_ORDER);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['posted_count'] = $entities_count[Entity::STATUS_POSTED];
            $this->input['notsigned_count'] = $entities_count[Entity::STATUS_NOTSIGNED];
            $this->input['dispatched_count'] = $entities_count[Entity::STATUS_DISPATCHED];
            $this->input['pickedup_count'] = $entities_count[Entity::STATUS_PICKEDUP];
            $this->input['delivered_count'] = $entities_count[Entity::STATUS_DELIVERED];
            $this->input['issues_count'] = $entities_count[Entity::STATUS_ISSUES];
            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
            , 'current_page' => $this->pager->CurrentPage
            , 'pages_total' => $this->pager->PagesTotal
            , 'records_total' => $this->pager->RecordsTotal
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->section = "Orders";
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
			$this->getPaymentForm();
        } catch (FDException $e) {
			//$applog =  new Applog($this->daffny->DB);
			//$applog->createException($e);
            //redirect(getLink("orders"));
			print $e;
        } catch (UserException $e) {
			//$applog =  new Applog($this->daffny->DB);
			//$applog->createException($e);
            $this->setFlashError($e->getMessage());
            //redirect($e->getRedirectUrl());
			print $e;
        }
    }

	
	
}
