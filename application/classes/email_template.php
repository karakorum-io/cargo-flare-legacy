<?php

class EmailTemplate extends FdObject {

    const TABLE = "app_emailtemplates";
    const SEND_TYPE_TEXT = 0;
    const SEND_TYPE_HTML = 1;

    const SYS_INIT_QUOTE = 1;
    const SYS_QUOTE_FL_PHONED_M = 2;
    const SYS_QUOTE_FL_PHONED_S = 3;
    const SYS_QUOTE_FL_EMAIL = 4;
    const SYS_QUOTE_FL_FAX = 5;
    const SYS_QUOTE_FORM_ATT = 6;
    const SYS_ORDER_INVOICE_ATT = 7;
    const SYS_ORDER_CONFIRM = 8;
    const SYS_ORDER_FORM_ATT = 9;
    const SYS_ORDER_PAYMENT_RCVD = 10;
    const SYS_ORDER_DISP_NOTIFY = 11;
    const SYS_ORDER_DISP_SHEET_ATT = 12;
    const SYS_ORDER_RECEIPT_ATT = 13;
    const SYS_ORDER_THANKS = 14;
    const SYS_ORDER_DEALER_SIGN = 15;
    const SYS_ORDER_DEALER_ORDER = 16;
    const SYS_ORDER_SIGN_REMIND = 17;
    const SYS_ORDER_DISPATCH_LINK = 18;
    const SYS_ORDER_DISPATCH_LINK_CARGOFLARE = 11;
	
	const SYS_ORDER_PICKUP_MAIL = 20;
	const SYS_ORDER_DELIVERED_MAIL = 21;

    // version 1.0.2 email templates
    const SYS_QUOTES_REUQEST_2_MAIL = 22;
	
	const BULK_MAIL_TYPE_PICKUP = 6;
	const BULK_MAIL_TYPE_DELIVER = 7;

    /**
     * @var template $tpl
     */
    protected $tpl = null;
	protected $params = array();
	protected $attachments = array();
	/**
	 * @protected Entity $entity;
	 */
	protected $entity = null;

    public function setTemplateBuilder($tpl) {
        if (!($tpl instanceof template))
            throw new FDException("EmailTemplate->setTemplateBuilder: invalid template builder");
        $this->tpl = $tpl;
    }

	protected function setAttachments($add = array()) {
		$attachments = $this->db->selectRows("t.`name`, t.`body`", "`app_formtemplates` t, `app_emailtemplates_att` a", "WHERE t.`id` = a.`form_id` AND a.`template_id` = ".$this->id);
		foreach($attachments as $val) {
			$params = $this->tpl->get_vars($val['body']);
			$p = array();
			foreach ($params as $param) {
				$p[$param] = "";
			}
			$params = self::fillParams($this->entity, $p, self::SEND_TYPE_HTML);
			$params = array_merge($params, $add);
			$this->attachments[$val['name'].".html"] = $this->tpl->get_parsed_from_array($val['body'], $params);
		}
	}

    public function loadTemplate($sys_id, $owner_id, $entity = null, $add = array(), $is_default=true,$accountIDMatchCarrier=0) {
        if (is_null($this->db))
            throw new FDException("EmailTemplate->loadTemplate: DB Helper not set");
        if (!ctype_digit((string) $sys_id) || !ctype_digit((string) $owner_id))
            throw new FDException("EmailTemplate->loadTemplate: invalid input data");
        if ($is_default){
            $templateId = $this->db->selectField("id", self::TABLE, "WHERE `sys_id` = " . (int) $sys_id . " AND `owner_id` = " . (int) $owner_id);
        }else{
            $templateId = $this->db->selectField("id", self::TABLE, "WHERE `id` = " . (int) $sys_id . " AND `owner_id` = " . (int) $owner_id);
        }

        if (!$templateId)
            throw new FDException("EmailTemplate->loadTemplate: Template not found in DB");
        $this->load($templateId);
		if ($entity instanceof Entity) {
			$this->entity = $entity;
			$params = $this->tpl->get_vars(($this->send_type == EmailTemplate::SEND_TYPE_HTML)?$this->body_html:$this->body_text);
			$params = array_merge($params, $this->tpl->get_vars($this->to_address));
			$params = array_merge($params, $this->tpl->get_vars($this->from_address));
			$params = array_merge($params, $this->tpl->get_vars($this->from_name));
			$params = array_merge($params, $this->tpl->get_vars($this->subject));
			$params = array_merge($params, $this->tpl->get_vars($this->bcc_addresses));
			$params = array_merge($params);
			$p = array();
			foreach ($params as $param) {
				$p[$param] = "";
			}
			$this->params = self::fillParams($this->entity, $p, $this->send_type,false,null,$accountIDMatchCarrier);  
			$this->params = array_merge($this->params, $add);
		}
		$this->setAttachments($add);
    }

    public function loadTemplate_v2($sys_id, $owner_id, $entity = null, $add = array(), $is_default=true,$accountIDMatchCarrier=0) {
        if (is_null($this->db))
            throw new FDException("EmailTemplate->loadTemplate: DB Helper not set");
        if (!ctype_digit((string) $sys_id) || !ctype_digit((string) $owner_id))
            throw new FDException("EmailTemplate->loadTemplate: invalid input data");
        if ($is_default){
            $templateId = $this->db->selectField("id", self::TABLE, "WHERE `sys_id` = " . (int) $sys_id . " AND `owner_id` = " . (int) $owner_id);
        }else{
            $templateId = $this->db->selectField("id", self::TABLE, "WHERE `id` = " . (int) $sys_id . " AND `owner_id` = " . (int) $owner_id);
        }

        if (!$templateId)
            throw new FDException("EmailTemplate->loadTemplate: Template not found in DB");
        $this->load($templateId);
		if ($entity instanceof Entity) {
			$this->entity = $entity;
			$params = $this->tpl->get_vars(($this->send_type == EmailTemplate::SEND_TYPE_HTML)?$this->body_html:$this->body_text);
			$params = array_merge($params, $this->tpl->get_vars($this->to_address));
			$params = array_merge($params, $this->tpl->get_vars($this->from_address));
			$params = array_merge($params, $this->tpl->get_vars($this->from_name));
			$params = array_merge($params, $this->tpl->get_vars($this->subject));
			$params = array_merge($params, $this->tpl->get_vars($this->bcc_addresses));
			$params = array_merge($params);
			$p = array();
			foreach ($params as $param) {
				$p[$param] = "";
			}
			$this->params = self::fillParams($this->entity, $p, $this->send_type,false,null,$accountIDMatchCarrier);  
			$this->params = array_merge($this->params, $add);
		}
		$this->setAttachments($add);
    }

    public function getBody() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        if ($this->send_type == self::SEND_TYPE_TEXT) {
            file_put_contents('#- '.date('Y/m/d h:i:s').' Auto-quoting-log.txt', "".print_r($$response[$i]['enitity_id'],true). PHP_EOL, FILE_APPEND | LOCK_EX);
            return $this->tpl->get_parsed_from_array($this->body_text, $this->params);
        } elseif ($this->send_type == self::SEND_TYPE_HTML) {
            return $this->tpl->get_parsed_from_array($this->body_html, $this->params);
        }
        return null;
    }

    public function getToAddress() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        return $this->tpl->get_parsed_from_array($this->to_address, $this->params);
    }

    public function getFromAddress() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        return $this->tpl->get_parsed_from_array($this->from_address, $this->params);
    }

    public function getFromName() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        return $this->tpl->get_parsed_from_array($this->from_name, $this->params);
    }

    public function getSubject() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        return $this->tpl->get_parsed_from_array($this->subject, $this->params);
    }

    public function getBCCs() {
        if (is_null($this->tpl))
            throw new FDException("EmailTemplate->getBody: Template Builder not set");
        if (!$this->loaded)
            throw new FDException("EmailTemplate->getBody: Template not loaded");
        return $this->tpl->get_parsed_from_array($this->bcc_addresses, $this->params);
    }

	public function getAttachments() {
		return $this->attachments;
	}

    public function getTemplateName() {
        return $this->name;
    }

    public static function getVehiclesTpl($vehicles, $sendType) {
        $params = array();
        if (count($vehicles) > 0) {
            $list = array();
            $list_formatted = array();
            $list_price = array();
            $list_price_formatted = array();
            $tariff = 0;
            $deposit = 0;
            foreach ($vehicles as $vehicle) {
	            /* @var Vehicle $vehicle */
                $tariff += $vehicle->tariff;
                $deposit += $vehicle->deposit;
                $list[] = "{$vehicle->year} {$vehicle->make} {$vehicle->model} ".(($vehicle->inop)?'Inoperable':'Operable');
                $list_price[] = "{$vehicle->year} {$vehicle->make} {$vehicle->model} ".(($vehicle->inop)?'Inoperable':'Operable')." ({$vehicle->getTariff()}/{$vehicle->getDeposit()} dep.)";
                if ($sendType == self::SEND_TYPE_TEXT) {
                    $list_formatted[] = "{$vehicle->year} {$vehicle->make} {$vehicle->model} ".(($vehicle->inop)?'Inoperable':'Operable');
                    $list_price_formatted[] = "{$vehicle->year} {$vehicle->make} {$vehicle->model} ({$vehicle->getTariff()}/{$vehicle->getDeposit()} dep.)";
                } else {
                    //inline styles for yahoo, gmail
                    $list_formatted[] = "<tr><td style=\"\">{$vehicle->year}</td><td style=\"\">{$vehicle->make}</td><td style=\"\">{$vehicle->model}</td><td style=\"\">{$vehicle->lot}</td><td style=\"\">{$vehicle->vin}</td><td style=\"\">{$vehicle->plate}</td><td style=\"\">{$vehicle->state}</td><td style=\"\">{$vehicle->color}</td><td style=\"\">".(($vehicle->inop)?'Yes':'No')."</td></tr>";
                    $list_price_formatted[] = "<tr><td style=\"\">{$vehicle->year}</td><td style=\"\">{$vehicle->make}</td><td style=\"\">{$vehicle->model}</td><td style=\"\">{$vehicle->lot}</td><td style=\"\">{$vehicle->vin}</td><td style=\"\">{$vehicle->plate}</td><td style=\"\">{$vehicle->state}</td><td style=\"\">{$vehicle->color}</td><td style=\"\">".(($vehicle->inop)?'Yes':'No')."</td><td style=\"\">{$vehicle->getTariff()}/{$vehicle->getDeposit()}</td></tr>";
                }
            }
            $params['vehicle_list'] = implode(", ", $list);
            $params['vehicle_price'] = implode(", ", $list_price);
            if ($sendType == EmailTemplate::SEND_TYPE_TEXT) {
                $params['vehicle_list_format'] = implode("\n", $list_formatted);
                $params['vehicle_list_price_format'] = implode("\n", $list_price_formatted);
                $params['vehicle_table'] = implode("\n", $list_price_formatted);
            } else {
                //inline styles for yahoo, gmail
                $params['vehicle_list_format'] = '<table class="table table-bordered table-responsive table-striped">';
                $params['vehicle_list_format'].= '<tr><th style="">Year</th><th  style="">Make</th><th  style="">Model</th><th  style="">Lot #</th><th  style="">Vin #</th><th  style="">Plate #</th><th  style="">State</th><th  style="">Color</th><th style="">Inop</th></tr>';
                $params['vehicle_list_format'].= implode('', $list_formatted) . '</table>';

                $params['vehicle_list_price_format'] = '<table cellspacing="0" cellpadding="5" border="0" class="vehicles-table">';
                $params['vehicle_list_price_format'].= '<tr><th  style="">Year</th><th  style="">Make</th><th  style="">Model</th><th  style="">Lot #</th><th  style="">Vin #</th><th  style="">Plate #</th><th  style="">State</th><th  style="">Color</th><th  style="">Inop</th><th  style="border-bottom:#38b 1px solid; border-left:#38b 1px solid; border-top:#38b 1px solid; border-right:#38b 1px solid; background-color: #EEEEEE; font-weight: bold; text-align: center; padding: 3px 20px;">Price/Deposit</th></tr>';
                $params['vehicle_list_price_format'].= implode('', $list_price_formatted) . '</table>';

                $params['vehicle_table'] = '<table class="table table-responsive table-bordered table-striped">';
                $params['vehicle_table'].= '<tr><th style="">Year</th><th  style="">Make</th><th  style="">Model</th><th  style="">Lot #</th><th  style="">Vin #</th><th  style="">Plate #</th><th  style="">State</th><th  style="">Color</th><th style="">Inop</th></tr>';
                $params['vehicle_table'].= implode('', $list_formatted) . '</table>';
            }
        }
        return $params;
    }

    public static function getVehiclesTplInvoice($vehicles, $sendType) {
        $params = array();
            
            if (count($vehicles) > 0) {
            
                $list_formatted = array();
                $tariff = 0;
                $deposit = 0;

                foreach ($vehicles as $vehicle) {

                /* @var Vehicle $vehicle */
                    $tariff += $vehicle->tariff;
                    $deposit += $vehicle->deposit;               
                    
                        //inline styles for yahoo, gmail
                        $list_formatted[] = ""
                            . "<tr>"
                            . "<td style=\"\">{$vehicle->year}</td>"
                            . "<td style=\"\">{$vehicle->make}</td>"
                            . "<td style=\"\">{$vehicle->model}</td>"
                            . "<td style=\"\">{$vehicle->lot}</td>"
                            . "<td style=\"\">{$vehicle->vin}</td>"
                            . "<td style=\"\">{$vehicle->plate}</td>"
                            . "<td style=\"\">{$vehicle->state}</td>"
                            . "<td style=\"\">{$vehicle->color}</td>"
                            . "<td style=\"\">".(($vehicle->inop)?'Yes':'No')."</td>"
                            . "</tr>";
                            
                        
                        //chetu added code
                        $list_formatted_new[] = ""
                            . "<tr>"
                            . "<td style=\"\">{$vehicle->year}</td>"
                            . "<td style=\"\">{$vehicle->make}</td>"
                            . "<td style=\"\">{$vehicle->model}</td>"
                            . "<td style=\"\">{$vehicle->lot}</td>"
                            . "<td style=\"\">{$vehicle->vin}</td>"
                            . "<td style=\"\">{$vehicle->plate}</td>"
                            . "<td style=\"\">{$vehicle->state}</td>"
                            . "<td style=\"\">{$vehicle->color}</td>"
                            . "<td style=\"\">{$vehicle->lot}</td>"
                            . "<td style=\"\">{$vehicle->tariff}</td>"
                            . "<td style=\"\">".(($vehicle->inop)?'Yes':'No')."</td>"
                            . "</tr>";
                        //chetu added code ends
                }

                
                $NumOfVehicles = 10-count($vehicles);
                for($i=0;$i<$NumOfVehicles;$i++)
                {
                    $list_formatted[] = "<tr>"
                                            . "<td height=\"20\" style=\"\"></td>&nbsp;"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "</tr>";
                                    
                                    //chetu added code
                                    $list_formatted_new[] = "<tr>"
                                            . "<td height=\"20\" style=\"\"></td>&nbsp;"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "</tr>";
                                    //chetu added code ends
                }
                
                    //inline styles for yahoo, gmail

                    $params['vehicle_table_invoice'] = '<table class="table table-responsive table-bordered table-striped">';

                    $params['vehicle_table_invoice'].= '<tr>'
                        . '<th style="">Year</th>'
                        . '<th  style="">Make</th>'
                        . '<th  style="">Model</th>'
                        . '<th  style="">Lot #</th>'
                        . '<th  style="">Vin #</th>'
                        . '<th  style="">Plate #</th>'
                        . '<th  style="">State</th>'
                        . '<th  style="">Color</th>'
                        . '<th style="">Inop</th>'
                        . '</tr>';

                    $params['vehicle_table_invoice'].= implode('', $list_formatted) . '</table>'; 
            }
            return $params;   
    }

    public static function getVehiclesTplInvoiceAdd($vehicles, $sendType) {
        $params = array();
            
            if (count($vehicles) > 0) {
            
                $list_formatted = array();
                $tariff = 0;
                $deposit = 0;

                foreach ($vehicles as $vehicle) {

                /* @var Vehicle $vehicle */
                    $tariff += $vehicle->tariff;
                    $deposit += $vehicle->deposit;               
                    
                    
                        //chetu added code
                        $list_formatted_new[] = ""
                            . "<tr>"
                            . "<td style=\"\">{$vehicle->year}</td>"
                            . "<td style=\"\">{$vehicle->make}</td>"
                            . "<td style=\"\">{$vehicle->model}</td>"
                            . "<td style=\"\">{$vehicle->lot}</td>"
                            . "<td style=\"\">{$vehicle->vin}</td>"
                            . "<td style=\"\">{$vehicle->plate}</td>"
                            . "<td style=\"\">{$vehicle->state}</td>"
                            . "<td style=\"\">{$vehicle->color}</td>"
                            . "<td style=\"\">{$vehicle->tariff}</td>"
                            . "<td style=\"\">".(($vehicle->inop)?'Yes':'No')."</td>"
                            . "</tr>";
                        //chetu added code ends
                }

                
                $NumOfVehicles = 10-count($vehicles);
                
                /*for($i=0;$i<$NumOfVehicles;$i++)
                {
                    
                                    
                                    //chetu added code
                                    $list_formatted_new[] = "<tr>"
                                            . "<td height=\"20\" style=\"\"></td>&nbsp;"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\"\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "<td height=\"20\" style=\" background-color: #FFFFFF; text-align: left; padding: 3px 5px;\"></td>"
                                            . "</tr>";
                                    //chetu added code ends
                }*/
                
                    
                    
                    //Chetu Added Code 
                    $params['vehicle_table_invoice_cost_per_unit'] = '<table class="table table-responsive table-bordered table-striped">';

                    $params['vehicle_table_invoice_cost_per_unit'].= '<tr>'
                        . '<th style="">Year</th>'
                        . '<th  style="">Make</th>'
                        . '<th  style="">Model</th>'
                        . '<th  style="">Lot #</th>'
                        . '<th  style="">Vin #</th>'
                        . '<th  style="">Plate #</th>'
                        . '<th  style="">State</th>'
                        . '<th  style="">Color</th>'
                        . '<th  style="">Tariff</th>'
                        . '<th style="">Inop</th>'
                        . '</tr>';

                    $params['vehicle_table_invoice_cost_per_unit'].= implode('', $list_formatted_new) . '</table>'; 
                    //Chetu Added Code ends

            }
            return $params;   
    }

    //Chetu added function
    public static function reviewLinkGenerater($vehicles, $sendType, $entity) {
        $method = $_SERVER['HTTPS']?'https':'http'; 
        $params = array();
        $params['reviewLink'] = "<a href='$method://$_SERVER[HTTP_HOST]/review/orders/id/" . $entity->attributes['id'] ."'>Click Here </a>";
        return $params;
    }

    public static function getVehiclesTplMatchCarrier($vehicles, $sendType) {

        $params = array();
            
            if (count($vehicles) > 0) {

            
                $list_formatted = array();

                $tariff = 0;

                $deposit = 0;

                foreach ($vehicles as $vehicle) {

                    /* @var Vehicle $vehicle */

                    $tariff += $vehicle->tariff;

                    $deposit += $vehicle->deposit;

                
                        //inline styles for yahoo, gmail

                        $list_formatted[] = "<tr><td >".(($vehicle->inop)?'Yes':'No')."</td></tr>";

                }

                
                //inline styles for yahoo, gmail

                    $params['vehicle_table_short'] = '<table class="table table-responsive table-bordered table-striped">';

                    $params['vehicle_table_short'].= '<tr><th style="">Year</th><th  style="">Make</th><th  style="">Model</th><th  style="">Inop</th></tr>';

                    $params['vehicle_table_short'].= implode('', $list_formatted) . '</table>';

                

            }

            return $params;

        
    }

    public static function getLocationTpl($location,$format,$title) {
        $params = array();
             
                $params[$format] = '
		 <table width="50%" cellpadding="1" cellpadding="1" border="0"  style="border:#38b 1px solid;  background-color: #f4f4f4;">
		   <tr><td colspan="3" align="left" style="border:#cccccc 1px solid;  background-color: #cccccc; font-weight: bold; text-align: center; padding: 3px 20px;"><b>'.$title.' Information</b></td></tr>
		   <tr> <td style="line-height:15px;" width="20%"><strong>City</strong></td><td width="4%" align="center"><b>:</b></td><td>'. $location->city.'</td></tr>
		   <tr> <td style="line-height:15px;"><strong>State</strong></td><td width="4%" align="center"><b>:</b></td><td>'. $location->state.'</td></tr>
		   <tr> <td style="line-height:15px;"><strong>Zip</strong></td><td width="4%" align="center"><b>:</b></td><td>'. $location->zip.'</td></tr>
		  
         </table>  ';
         
        return $params;
    }

	public static function getPaymentsTpl($payments, $sendType) {
		$ret = "";
		if (count($payments) > 0) {
			$paymentsTpl = array();
			foreach ($payments as $payment) {
				/* @var Payment $payment */
				$paymentsTpl[] = $payment->getDate("m/d/Y") . ": " . $payment->getAmount();
			}
			$ret = ($sendType == self::SEND_TYPE_HTML)?implode("<br/>", $paymentsTpl):implode("\n", $paymentsTpl);
		}
		return $ret;
	}

    /**
     * @static
     * @param Entity $entity
     * @param array $params
     * @param int $sendType
	 * @return array
	 */
    public static function fillParams($entity, $params, $sendType, $sign_form = false, $signature = null,$accountIDMatchCarrier=0) {
		if (!($entity instanceof Entity)) return array();
        try {$notes = $entity->getNotes();} catch (Exception $e) {}
        try {$vehicles = $entity->getVehicles();} catch (Exception $e) {}
        try {$shipper = $entity->getShipper();} catch (Exception $e) {}
		
        try {
            $member = $entity->getAssigned();
            $company = $member->getCompanyProfile();
            $settings = $company->getDefaultSettings();
        } catch (Exception $e) {}
        try {$dispatch = $entity->getDispatchSheet();} catch (Exception $e) {}
        try {$origin = $entity->getOrigin();} catch (Exception $e) {}
        try {$destination = $entity->getDestination();} catch (Exception $e) {}
        foreach ($params as $pk => $pv) {
            try {
                switch ($pk) { 
				    case 'assigned_user_phone':						
				        $params[$pk] = $member->phone;						
				         break;
                    case 'distance':
					     $distance ='';
					     if($entity->distance!=0)
						    $distance ='Estimated Mileage '.$entity->distance.' miles';
						$params[$pk] = $distance;
						break;
					case 'smtp_from_email':						
					     $params[$pk] = $settings->smtp_from_email;					
						 break;
					case 'order_link':
						$params[$pk] = BASE_PATH."order/confirm/hash/".$entity->hash;
						break;
                    case 'email_tracking_pixel':
                            $params[$pk] = "<img src='https://cargoflare.com/track-open-email.php?id=".$entity->id."&hash=REPLACE_WITH_HASH'>";
                            break;
                    case 'email_tracking_pixel_logo':
                        $image = '
                                    <img src="https://cargoflare.com/styles/cargo_flare/logo.png" width="230"><br/>
                                    <img src="https://cargoflare.com/track-open-email.php?id='.$entity->id.'&hash=REPLACE_WITH_HASH">
                                ';
                        $params[$pk] = $image;
                        break;
					case 'order_link_esign_total':
						$params[$pk] = BASE_PATH."order/confirm_total/hash/".$entity->hash;
						break;	
					case 'order_commercial_link':
                        $params[$pk] = BASE_PATH."order/confirmCommercial/hash/".$entity->hash;
                         break;
                    case 'b2b_order_terms':
                        $params[$pk] = BASE_PATH . "account/B2BOrderTerms/id/" . rand(1000000000, 9999999999) . $entity->account_id;
                        break;
				    case 'match_carrier_unsubscribe':
                         $params[$pk] = BASE_PATH."order/carrierUnsubscribed/hash/".md5($accountIDMatchCarrier);
                         break;		
					case 'pickup_link':
						$params[$pk] = BASE_PATH."order/pickup/hash/".$entity->pickup_hash;
						break;
				    case 'delivered_link':
						$params[$pk] = BASE_PATH."order/delivered/hash/".$entity->delivered_hash;
						break;
	                case 'today':
                        $params[$pk] = date("m/d/Y");
                        break;
                    // Entity Information
                    case 'entity_number':
                        $params[$pk] = $entity->getNumber();
                        break;
				    case 'creator_email':
					    $email = "";
					    if($entity->creator_id !=0 ){
					       $creator = $entity->getCreator();
						   $email = $creator->email;
						}
						
                        $params[$pk] = $email;
                        break;		
                    case 'sign_this_form':
	                    if ($sign_form) {
		                   // $params[$pk] = '<button id="sign_button" style="padding:5px 10px">SIGN THIS FORM</button>';
							
							$params[$pk] = '<table width="70%" bgcolor="#FFFFFF" cellspacing="2" cellpadding="2" style="margin: 10px auto; background-color:#f4f4f4; border:1px solid #ccc;">
                   <tbody><tr>
                        <td colspan="2">
                        
                        <div id="signature_tool">
                            <div class="type_selector">
                                <table border="0" cellspacing="0" cellpadding="0">
								<td>&nbsp;</td>
                                    <tbody><tr>
                                        <td><input type="radio" checked="checked" id="sign_type_text" value="text" name="sign_type"></td>
                                        <td><label for="sign_type_text"><a>Signature</a></label></td>
                                        <td colspan="4"> </td>
                                        <!--td><input type="radio" name="sign_type" value="draw" id="sign_type_draw"/></td>
                                        <td><label for="sign_type_draw">Draw Signature</label></td-->
                                    </tr>
									<tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td colspan="4">
                                            <div style="display:none" id="sign_draw_controls" class="sign-controls">
                                                <button id="signature-undo" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" style="-moz-user-select: none;">Undo</span></button>
                                                <button id="signature-clear" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" style="-moz-user-select: none;">Clear</span></button>
                                                <button id="signature-save-draw" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" style="-moz-user-select: none;">Save</span></button>
                                                <button onclick="rejectDispatchSheet(590)" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" style="-moz-user-select: none;">Reject</span></button>
                                                <div id="paper" style="width: 400px; height: 100px;"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="400" height="100"><desc>Created with Rapha�l</desc><defs/><path fill="none" stroke="#000000" style="stroke-linecap: round; stroke-linejoin: round;" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                                                    <div id="sign-result" style="width: 400px; height: 100px;">
                                                        <img alt="Signature" src="#">
                                                    </div>
                                                </div>
                                        </td>
                                    </tr>
                                </tbody></table>
                            </div>
                            <div id="sign_write_controls" class="sign-controls">
                                <table border="0" cellspacing="0" cellpadding="0">
                                     
                                    <tbody><tr>
                                        <td><label for="sign_name">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enter Your Name:&nbsp;</label></td>
                                        <td><input type="text" class="form-box-textfield latin" id="sign_name" maxlength="64" size="49"></td>
                                       
                                    </tr>
									<tr><td>&nbsp;</td></tr>
                                    <tr>
                                      <td><label for="sign_name">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Leave us a Note:&nbsp;</label></td>
                                      <td>
                                         <textarea id="notes" rows="5" cols="50"></textarea>
                                      </td>
                                    </tr>
									<tr><td>&nbsp;</td></tr>
                                    <tr>
                                      <td align="center" colspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button style="margin: 0 10px;" id="signature-save-text" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" style="-moz-user-select: none;">Click Here to e-Sign</span></button>
                                           
                                      </td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>									
                                </tbody></table>
                            </div>
                            
                        </div>
                        </td>
                     </tr>
				   
			    </tbody></table>';
	                    } elseif (!is_null($signature)) {
		                    $params[$pk] = '<img src="data:image/png;base64,'.base64_encode($signature).'" /><br/><br/><strong>'.date('m/d/Y').'</strong>';
	                    } else {
		                    $params[$pk] = '';
	                    }
	                    break;
                    case 'entity_created':
                        if ($entity->type == Entity::TYPE_LEAD) {
                            $params[$pk] = date("m/d/Y", strtotime($entity->created));
                        } elseif ($entity->type == Entity::TYPE_QUOTE) {
                            $params[$pk] = date("m/d/Y", strtotime($entity->quoted));
                        } else {
                            $params[$pk] = date("m/d/Y", strtotime($entity->ordered));
                        }
                        break;
                    case 'entity_id':
                        $params[$pk] = $entity->id;
                        break;
                    case 'entity_note_to_shipper':
                        $params[$pk] = (count($notes[Note::TYPE_TO]) > 0) ? $notes[Note::TYPE_TO][0]->text : "";
                        break;
                    case 'entity_note_from_shipper':
                        $params[$pk] = (count($notes[Note::TYPE_FROM]) > 0) ? $notes[Note::TYPE_FROM][0]->text : "";
                        break;
					case 'entity_total_price':
						$params[$pk] = $entity->getTotalPrice();
						break;
                    case 'entity_total_tariff':
                        $params[$pk] = $entity->getTotalTariff();
                        break;
                    case 'entity_total_deposit':
                        $params[$pk] = $entity->getTotalDeposit();
                        break;
                    case 'entity_carrier_pay':
                        $params[$pk] = $entity->getCarrierPay();
                        break;
					case 'entity_grand_total':
					    $TotalPrice = $entity->getTotalTariff(false);
						$CarrierPay = $entity->getCarrierPay(false);
						$TotalDeposit = $entity->getTotalDeposit(false);
                        $params[$pk] = ("$ " . number_format((float)($TotalPrice + $CarrierPay + $TotalDeposit), 2, ".", ","));
					    break;	
					case 'customer_selected_payment_option':
                        $params[$pk] = $entity->getPaymentOption($entity->customer_balance_paid_by);
                        break;
					
					case 'get_selected_payment_options':
                        if(in_array($entity->balance_paid_by, array(2, 3 , 16 , 17))){   
							$payments_terms_dispatch = "COD / COP";
							
							$BalancePaidBy = "*COD";
							$CarrierCheck = "[X]";
							$shipper_check  = "[X]";
							$consignee_check = "[ ]";
							
							if($entity->balance_paid_by == 2)
							  $funds_check = "[X]";
							else
							  $funds_check = "[ ]";
							  
							if($entity->balance_paid_by == 3) 
							  $check_check = "[X]";
							else
							  $check_check = "[ ]"; 
							  
						 }
						 elseif(in_array($entity->balance_paid_by, array(8,9,18,19))){   
							$payments_terms_dispatch = "COD / COP";
							
							$BalancePaidBy = "*COP";
							$CarrierCheck = "[X]";
							$shipper_check  = "[X]";
							$consignee_check = "[ ]";
							
							if($entity->balance_paid_by == 8)
							  $funds_check = "[X]";
							else
							  $funds_check = "[ ]";
							  
							if($entity->balance_paid_by == 9) 
							  $check_check = "[X]";
							else
							  $check_check = "[ ]"; 
							
						 }
						 elseif(in_array($entity->balance_paid_by, array(12, 13 , 20 , 21))){ 
						   $CarrierCheck = "[ ]";
						   $shipper_check  = "[X]";
						   $consignee_check = "[ ]";
						   $BalancePaidBy = "*COC";
						  
						   
							if($entity->balance_paid_by == 12)
							  $funds_check = "[X]";
							else
							  $funds_check = "[ ]";
							  
							if($entity->balance_paid_by == 13) 
							  $check_check = "[X]";
							else
							  $check_check = "[ ]";  
						 }
						 elseif(in_array($entity->balance_paid_by, array(14, 15 , 22 , 23))){ 
						   
						   $CarrierCheck = "[ ]";
						   $shipper_check  = "[ ]";
						   $consignee_check = "[X]";
						   $BalancePaidBy = "*CAC";
							
							
							if($entity->balance_paid_by == 14)
							  $funds_check = "[X]";
							else
							  $funds_check = "[ ]";
							if($entity->balance_paid_by == 15) 
							  $check_check = "[X]";
							else
							  $check_check = "[ ]";
						 }
						 else{
							
							$CarrierCheck = "[ ]";
							$shipper_check  = "[ ]";
							$consignee_check = "[ ]";
							$funds_check = "[ ]";
							$check_check = "[ ]";
							
							  
						 }
						 $params['CarrierCheck'] = $CarrierCheck;
						 $params['shipper_check'] = $shipper_check;
						 $params['consignee_check'] = $consignee_check;
						 $params['funds_check'] = $funds_check;
						 $params['check_check'] = $check_check;
                        break;
				 		
                    case 'entity_balance_paid_by':
                        $params[$pk] = (!is_null($entity->balance_paid_by))?(Entity::$balance_paid_by_string[$entity->balance_paid_by]):"";
                        break;
					case 'payments_total_pay_amount':
					  $params[$pk] = $entity->getShipperPaymentsAmount();
					  
					  //$entity->getTotalPaymentAmount();
					  break;  
                    case 'payments_total_due_amount':
					  $params[$pk] = $entity->getTotalDuePaymentAmount();
					  break;
                    case 'entity_est_ship_date':
                        $params[$pk] = $entity->getEstDeliveryDate("m/d/Y");
                        break;
                    case 'entity_avail_pickup_date':
                        $params[$pk] = $entity->getFirstAvail("m/d/Y");
                        break;
                    case 'entity_load_date':
                        $params[$pk] = (!is_null($entity->load_date_type))?(Entity::$date_type_string[$entity->load_date_type] . " " . $entity->getLoadDate("m/d/Y")):"";
                        break;
                    case 'entity_delivery_date':
                        $params[$pk] = (!is_null($entity->delivery_date_type))?(Entity::$date_type_string[$entity->delivery_date_type] . " " . $entity->getDeliveryDate("m/d/Y")):"";
                        break;
                    case 'entity_pickedup_date':
                        $params[$pk] = $entity->getActualPickUpDate("m/d/Y");
                        break;
                    case 'entity_delivered_date':
                        $params[$pk] = $entity->getActualDeliveryDate("m/d/Y");
                        break;
                    case 'entity_ship_via':
                        $params[$pk] = Entity::$ship_via_string[$entity->ship_via];
                        break;
					// Payments Information
					case 'payments_shipper':
						$params[$pk] = self::getPaymentsTpl($entity->getShipperPayments(), $sendType);
						break;
					case 'payments_shipper_amount':
						$params[$pk] = $entity->getShipperPaymentsAmount();
						break;
					case 'payments_deposit_due':
						$params[$pk] = $entity->getDepositDue();
						break;
					case 'payments_amount_due':
						$params[$pk] = $entity->getAmountDue();
						break;
                    //Vehicles Information
                    case 'vehicle_table':
                    case 'vehicle_list':
                    case 'vehicle_list_format':
                    case 'vehicle_list_price':
                    case 'vehicle_list_price_format':
                        $vehiclesParams = self::getVehiclesTpl($vehicles, $sendType);
                        $params[$pk] = $vehiclesParams[$pk];
                        break;
					case 'vehicle_table_invoice':
                        $vehiclesParams = self::getVehiclesTplInvoice($vehicles, $sendType);
                        $params[$pk] = $vehiclesParams[$pk];
                        break;
                        
                        /*chetu added case*/
                        case 'vehicle_table_invoice_cost_per_unit':
                        $vehiclesParams = self::getVehiclesTplInvoiceAdd($vehicles, $sendType);
                        $params[$pk] = $vehiclesParams[$pk];
                        break;
					/*chetu added case*/
					/* Chetu Added Case */
                    case 'reviewLink':
                        $vehiclesParams = self::reviewLinkGenerater($vehicles, $sendType,$entity);
                        $params[$pk] = $vehiclesParams[$pk];
                        break;
                    /* Chetu Added Case Closed */
                        
                    
                        case 'vehicle_table_short':
                        $vehiclesParams = self::getVehiclesTplMatchCarrier($vehicles, $sendType);
                        $params[$pk] = $vehiclesParams[$pk];
                        break;
                    
						
					case 'number_of_vehicle':
					    $vehicleStr = 'Vehicle';
					    $number_of_vehicle = count($vehicles);
						if($number_of_vehicle > 1)
						  $vehicleStr .= 's';
						$params[$pk] = $number_of_vehicle." ".$vehicleStr;
					break;
                    // Shipper Information
                    case 'shipper_first_name':
                        $params[$pk] = $shipper->fname;
                        break;
                    case 'shipper_last_name':
                        $params[$pk] = $shipper->lname;
                        break;
                    case 'shipper_company_name':
                        $params[$pk] = $shipper->company;
                        break;
                    case 'shipper_email':
                        $params[$pk] = $shipper->email;
                        break;
                    case 'shipper_phone1_ext':
                        $params[$pk] = $shipper->phone1_ext;
                        break;
                    case 'shipper_phone2_ext':
                        $params[$pk] = $shipper->phone2_ext;
                        break;
                    case 'shipper_phone':
                        $params[$pk] = $shipper->phone1;
                        break;
                    case 'shipper_phone2':
                        $params[$pk] = $shipper->phone2;
                        break;
                    case 'shipper_phone_cell':
                        $params[$pk] = $shipper->mobile;
                        break;
                    case 'shipper_phone_fax':
                        $params[$pk] = $shipper->fax;
                        break;
                    case 'shipper_address':
                        $params[$pk] = $shipper->address1;
                        break;
                    case 'shipper_address2':
                        $params[$pk] = $shipper->address2;
                        break;
                    case 'shipper_city':
                        $params[$pk] = $shipper->city;
                        break;
                    case 'shipper_state':
                        $params[$pk] = $shipper->state;
                        break;
					case 'shipper_state_data':
			   
                        $params[$pk] = $state_data;
                        break;	
                    case 'shipper_zip':
                        $params[$pk] = $shipper->zip;
                        break;
                    case 'shipper_country':
                        $params[$pk] = $shipper->country;
                        break;
				    case 'shipper_hours':
                        $params[$pk] = $shipper->shipper_hours;
                        break;		
                    // Company Information
                    case 'company_name':
                        $params[$pk] = $company->companyname;
                        break;
					case 'company_logo_file_path':
					      $filePath = UPLOADS_PATH."company".DIRECTORY_SEPARATOR.$member->getOwnerId()."_small.jpg";
					      $params[$pk] = '<img src="'.$filePath.'"  width="270" height="65"/>';
						
                        break;
                    case 'company_logo':
                        $filePath = UPLOADS_PATH."company".DIRECTORY_SEPARATOR.$member->getOwnerId()."_small.jpg";
                        if (file_exists($filePath)){
                            $encoded_data = base64_encode(file_get_contents($filePath));
                            $params[$pk] = "data:image/jpg;base64,".$encoded_data;
                        }else{
                            $params[$pk] = "data:image/jpg;base64,iVBORw0KGgoAAAANSUhEUgAAAIsAAAAyCAYAAABs3ChCAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA01pVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoMTMuMCAyMDEyMDMwNS5tLjQxNSAyMDEyLzAzLzA1OjIxOjAwOjAwKSAgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkQ3NDM1REYzQUIwODExRTE4MTZBRjFGREJDMDU4MEYwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkQ3NDM1REY0QUIwODExRTE4MTZBRjFGREJDMDU4MEYwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RDc0MzVERjFBQjA4MTFFMTgxNkFGMUZEQkMwNTgwRjAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RDc0MzVERjJBQjA4MTFFMTgxNkFGMUZEQkMwNTgwRjAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1l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";
                        }
                        break;
                    case 'company_website':
                        $params[$pk] = $company->site;
                        break;
                    case 'company_description':
                        $params[$pk] = $company->description;
                        break;
                    case 'company_owner':
                        $params[$pk] = $company->owner;
                        break;
                    case 'company_address':
                        $params[$pk] = trim($company->address1 . " " . $company->address2);
                        break;
                    case 'company_city':
                        $params[$pk] = $company->city;
                        break;
                    case 'company_state':
                        $params[$pk] = $company->state;
                        break;
                    case 'company_zip':
                        $params[$pk] = $company->zip_code;
                        break;
                    case 'company_phone_local':
                        $params[$pk] = $company->phone_local;
                        break;
                    case 'company_phone_tollfree':
                        $params[$pk] = $company->phone_tollfree;
                        break;
                    case 'company_phone_cell':
                        $params[$pk] = $company->phone_cell;
                        break;
                    case 'company_phone_fax':
                        $params[$pk] = $company->fax;
                        break;
                    case 'company_email':
                        $params[$pk] = $company->email;
                        break;
                    case 'company_order_terms':
                        $params[$pk] = $settings->order_terms;
                        break;
                    case 'B2B_order_terms':
                        $params[$pk] = $settings->commercial_terms;
                        break;
                    case 'company_dispatch_terms':
                        $params[$pk] = $settings->dispatch_terms;
                        break;
                    case 'company_dispatch_phone':
                        $params[$pk] = $company->dispatch_phone;
                        break;
                    case 'company_dispatch_fax':
                        $params[$pk] = $company->dispatch_fax;
                        break;
                    case 'company_dispatch_email':
                        $params[$pk] = $company->dispatch_email;
                        break;
                    case 'company_dispatch_contact':
                        $params[$pk] = $company->dispatch_contact;
                        break;
                    // Carrier Information
                    case 'carrier_company':
                        $params[$pk] = $dispatch->carrier_company_name;
                        break;
                    case 'carrier_contact':
                        $params[$pk] = $dispatch->carrier_contact_name;
                        break;
                    case 'carrier_phone1':
                        $params[$pk] = $dispatch->carrier_phone_1;
                        break;
                    case 'carrier_phone2':
                        $params[$pk] = $dispatch->carrier_phone_2;
                        break;
                    case 'carrier_phone1_ext':
                        $params[$pk] = $dispatch->carrier_phone1_ext;
                        break;
                    case 'carrier_phone2_ext':
                        $params[$pk] = $dispatch->carrier_phone2_ext;
                        break;
                    case 'carrier_phone_cell':
                        $params[$pk] = $dispatch->carrier_phone_cell;
                        break;
                    case 'carrier_fax':
                        $params[$pk] = $dispatch->carrier_fax;
                        break;
                    case 'carrier_email':
                        $params[$pk] = $dispatch->carrier_email;
                        break;
                    case 'carrier_driver_name':
                        $params[$pk] = $dispatch->carrier_driver_name;
                        break;
                    case 'carrier_driver_phone':
                        $params[$pk] = $dispatch->carrier_driver_phone;
                        break;
                    // User Information
                    case 'user_name':
                        $params[$pk] = $member->contactname;
                        break;
                    case 'user_email':
                        $params[$pk] = $member->email;
                        break;
                    case 'user_phone':
                        $params[$pk] = $member->phone;
                        break;
                    // Origin Information
	                case 'origin_address1':
						$params[$pk] = $origin->address1;
		                break;
	                case 'origin_address2':
		                $params[$pk] = $origin->address2;
		                break;
                    case 'origin_city':
                        $params[$pk] = $origin->city;
                        break;
                    case 'origin_state':
                        $params[$pk] = $origin->state;
                        break;
                    case 'origin_zip':
                        $params[$pk] = $origin->zip;
                        break;
                    case 'origin_country':
                        $params[$pk] = $origin->country;
                        break;
                    case 'origin_contact':
                        $params[$pk] = $origin->name;
                        break;
                    case 'origin_company':
                        $params[$pk] = $origin->company;
                        break;
                    case 'origin_phone1':
                        $params[$pk] = $origin->phone1;
                        break;
                    case 'origin_phone2':
                        $params[$pk] = $origin->phone2;
                        break;
                    case 'origin_phone3':
                        $params[$pk] = $origin->phone3;
                        break;
                    case 'origin_phone4':
                        $params[$pk] = $origin->phone4;
                        break;
					case 'origin_phone1_ext':
                        $params[$pk] = $origin->phone1_ext;
                        break;
                    case 'origin_phone2_ext':
                        $params[$pk] = $origin->phone2_ext;
                        break;
                    case 'origin_phone3_ext':
                        $params[$pk] = $origin->phone3_ext;
                        break;	
					case 'origin_phone4_ext':
                        $params[$pk] = $origin->phone4_ext;
                        break;	
                    case 'origin_phone_cell':
                        $params[$pk] = $origin->phone_cell;
                        break;
                    case 'origin_terminal_fee':
                        $params[$pk] = $origin->pickup_terminal_fee;
                        break;
                    case 'origin_buyer_number':
                        $params[$pk] = $entity->buyer_number;
                        break;
                    case 'origin_auction_name':
                        $params[$pk] = $origin->auction_name;
                        break;
                    case 'origin_booking_number':
                        $params[$pk] = $entity->booking_number;
                        break;

                    case 'origin_format':
                        $locationParams = self::getLocationTpl($origin,'origin_format','Pickup');
                        $params[$pk] = $locationParams[$pk];
                        break; 
                    // Destination Information
	                case 'destination_address1':
		                $params[$pk] = $destination->address1;
		                break;
	                case 'destination_address2':
		                $params[$pk] = $destination->address2;
		                break;
                    case 'destination_city':
                        $params[$pk] = $destination->city;
                        break;
                    case 'destination_state':
                        $params[$pk] = $destination->state;
                        break;
                    case 'destination_zip':
                        $params[$pk] = $destination->zip;
                        break;
                    case 'destination_country':
                        $params[$pk] = $destination->country;
                        break;
                    case 'destination_contact':
                        $params[$pk] = $destination->name;
                        break;
                    case 'destination_company':
                        $params[$pk] = $destination->company;
                        break;
                    case 'destination_phone1':
                        $params[$pk] = $destination->phone1;
                        break;
                    case 'destination_phone2':
                        $params[$pk] = $destination->phone2;
                        break;
                    case 'destination_phone3':
                        $params[$pk] = $destination->phone3;
                        break;
                     case 'destination_phone4':
                        $params[$pk] = $destination->phone4;
                        break;
					 case 'destination_phone1_ext':
                        $params[$pk] = $destination->phone1_ext;
                        break;
                    case 'destination_phone2_ext':
                        $params[$pk] = $destination->phone2_ext;
                        break;
                    case 'destination_phone3_ext':
                        $params[$pk] = $destination->phone3_ext;
                        break;
					case 'destination_phone4_ext':
                        $params[$pk] = $destination->phone4_ext;
                        break;
                    case 'destination_phone_cell':
                        $params[$pk] = $destination->phone_cell;
                        break;
                    case 'destination_terminal_fee':
                        $params[$pk] = $destination->dropoff_terminal_fee;
                        break;
                    case 'destination_format':
                        $locationParams = self::getLocationTpl($destination,'destination_format',"Delivery");
                        $params[$pk] = $locationParams[$pk];
                        break;
                }
            } catch (Exception $e) {
                
            }
        }
        return $params;
    }

	/**
	 * @param int $member_id
	 * @param mysql $db
	 */
	public static function createEmailAndFormTemplates($member_id, $db) {
		$orderTemplateId = null;
		$quoteTemplateId = null;
		$invoiceTemplateId = null;
        $confirmTemplateId = null;
        $initialTemplateId = null;
		$orderFormId = null;
		$quoteFormId = null;
		$invoiceFormId = null;
		$templates = $db->selectRows('*', self::TABLE, "WHERE `is_default` = 1");
		foreach($templates as $template) {
			$template['sys_id'] = $template['id'];
			$template['owner_id'] = $member_id;
			unset($template['id']);
			unset($template['is_default']);
			$db->insert(self::TABLE, $template);
			switch ($template['sys_id']) {
				case self::SYS_QUOTE_FORM_ATT:
					$quoteTemplateId = $db->get_insert_id();
					break;
				case self::SYS_ORDER_INVOICE_ATT:
					$invoiceTemplateId = $db->get_insert_id();
					break;
				case self::SYS_ORDER_FORM_ATT:
					$orderTemplateId = $db->get_insert_id();
					break;
                case self::SYS_ORDER_CONFIRM:
                    $confirmTemplateId = $db->get_insert_id();
                    break;
                case self::SYS_INIT_QUOTE:
                    $initialTemplateId = $db->get_insert_id();
                    break;
			}
		}
		$forms = $db->selectRows('*', FormTemplate::TABLE, "WHERE `is_default` = 1");
		foreach ($forms as $form) {
			$form['sys_id'] = $form['id'];
			$form['owner_id'] = $member_id;
			unset($form['id']);
			unset($form['is_default']);
			$db->insert(FormTemplate::TABLE, $form);
			switch ($form['sys_id']) {
				case FormTemplate::SYS_QUOTE:
					$quoteFormId = $db->get_insert_id();
					break;
				case FormTemplate::SYS_INVOICE:
					$invoiceFormId = $db->get_insert_id();
					break;
				case FormTemplate::SYS_ORDER:
					$orderFormId = $db->get_insert_id();
					break;
			}
		}
		$db->insert('app_emailtemplates_att', array(
			'owner_id' => $member_id,
			'template_id' => $quoteTemplateId,
			'form_id' => $quoteFormId,
		));
		$db->insert('app_emailtemplates_att', array(
			'owner_id' => $member_id,
			'template_id' => $invoiceTemplateId,
			'form_id' => $invoiceFormId,
		));
		$db->insert('app_emailtemplates_att', array(
			'owner_id' => $member_id,
			'template_id' => $orderTemplateId,
			'form_id' => $invoiceFormId,
		));
        $db->insert('app_emailtemplates_att', array(
            'owner_id' => $member_id,
            'template_id' => $confirmTemplateId,
            'form_id' => $orderFormId,
        ));
        $db->insert('app_emailtemplates_att', array(
            'owner_id' => $member_id,
            'template_id' => $initialTemplateId,
            'form_id' => $quoteFormId,
        ));
	}
}