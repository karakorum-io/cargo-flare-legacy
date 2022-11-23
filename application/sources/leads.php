<?php
class ApplicationLeads extends ApplicationAction
{
    public $section = "Leads";

    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('quotes.common');
        $this->daffny->tpl->form_templates = $this->form->ComboBox('form_templates', array('' => 'Select One') + $this->getFormTemplates("quotes"), array('style' => 'width:130px;', 'onChange' => 'printSelectedQuoteForm()'), "", "", true);

        if ($_GET['id'] > 0) {
            $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One') + $this->getEmailTemplates("quotes"), array('style' => 'width:130px;', 'onChange' => 'emailSelectedQuoteFormNew()'), "", "", true);
        } else {
            $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One') + $this->getEmailTemplates("quotes"), array('style' => 'width:130px;', 'onChange' => 'emailSelectedLeadFormNew(\'entity\')'), "", "", true);
        }

        parent::construct();
    }

    public function idx()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_ACTIVE);
        } catch (FDException $e) {
            redirect(getLink(''));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink(''));
        }
    }

    public function onhold()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_ONHOLD);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function quoted()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_LQUOTED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function follow()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_LFOLLOWUP);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function expired()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_LEXPIRED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }
    public function duplicate()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_LDUPLICATE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }
    public function appointment()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_LAPPOINMENT);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function unreadable()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_UNREADABLE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function archived()
    {
        try {
            $this->loadLeadsPage(Entity::STATUS_ARCHIVED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function assigned()
    {
        try {

            $this->loadLeadsPage(Entity::STATUS_ASSIGNED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cpriority()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CPRIORITY);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            //print $e;
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cdead()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CDEAD);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function created()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CACTIVE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cquoted()
    {
        try {
            $this->listcreatedquote(Entity::STATUS_CQUOTED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cfollow()
    {
        try {
            $this->listcreatedquote(Entity::STATUS_CFOLLOWUP);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cexpired()
    {
        try {
            $this->listcreatedquote(Entity::STATUS_CEXPIRED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cduplicate()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CDUPLICATE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cappointment()
    {
        try {
            $this->listcreatedquote(Entity::STATUS_CAPPOINMENT);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cassigned()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CASSIGNED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function conhold()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CONHOLD);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function cunreadable()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CUNREADABLE);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function carchived()
    {
        try {
            $this->loadLeadsPageNew(Entity::STATUS_CARCHIVED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function converted()
    {
        try {
            $this->loadLeadsPageConverted(Entity::STATUS_CARCHIVED);
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function show()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                redirect(getLink('leads'));
            }

            $this->tplname = "leads.detail";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Leads Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Lead #" . $entity->getNumber()));
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function showcreated()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                redirect(getLink('leads'));
            }

            redirect(getLink("quotes", "show", "id", (int) $_GET['id']));

            $this->tplname = "leads.detail_created";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
			
            $info = "Leads Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Lead #" . $entity->getNumber()));

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function showimported()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                redirect(getLink('leads'));
            }

            $this->tplname = "leads.detail_imported";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            $info = "Leads Details-" . $entity->number . "(" . $entity->id . ")";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Lead #" . $entity->getNumber()));
            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function create()
    {
        try {
            $this->initGlobals();
            $this->tplname = "leads.create";
            $this->title = "Create Lead";
            $this->input['title'] = $this->title;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('leads') => "Leads", '' => "Create"));

            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }

            $this->daffny->tpl->referrers_arr = $referrers_arr;

            if (isset($_POST['submit']) && $sql_arr = $this->checkEditFormCreate_v2()) {
                $info = "Create Lead";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);
                $this->createLead($sql_arr);
            } else {

                if (isset($_POST['submit']) && isset($_POST['tariff'])) {
                    $total_tariff = 0;
                    $total_deposit = 0;
                    foreach ($_POST['tariff'] as $k => $tariff) {
                        $total_tariff += $tariff;
                        $total_deposit += $_POST['deposit'][$k];
                    }
                    print $carrier_pay = $total_tariff - $total_deposit;
                    $this->input['carrier_pay'] = "$ " . number_format($carrier_pay, 2);
                    $this->input['total_tariff'] = "$ " . number_format($total_tariff, 2);
                    $this->input['total_deposit'] = "$ " . number_format($total_deposit, 2);

                } else {
                    $this->input['total_tariff'] = "$ 0.00";
                    $this->input['total_deposit'] = "$ 0.00";
                    $this->input['carrier_pay'] = "$ 0.00";
                }

                if (count($this->err)) {
                    $this->setFlashError("<div class='form-errors'><p>" . implode("</p><p>", $this->err) . "</p></div>");
                }
            }
            $this->getEditForm();
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

    protected function createLead(array $leadData)
    {
        
        try {
            $this->daffny->DB->transaction('start');

            /* Save Shipper in Accounts */
            if (isset($loadData['shipper_add']) && ($loadData['shipper_add'] == 1)) {
            }

            $referrer_name_value = "";
            $salesrep = "";
            
            if ($leadData['referred_by'] != "") {
               
                $row_referrer = $this->daffny->DB->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $leadData['referred_by'] . "'");
                
                if (!empty($row_referrer)) {
                    $referrer_name_value = $row_referrer['name'];
                    $salesrep = $row_referrer['salesrep'];
                }
            }

            $_SESSION["buysell"] = post_var("buysell");
            $_SESSION["buysell_days"] = post_var("buysell_days");

            /* Create Lead */
            $insert_arr = array(
                'type' => Entity::TYPE_CLEAD,
                'quoted' => date("Y-m-d H:i:s"),
                'creator_id' => $_SESSION['member_id'],
                'assigned_id' => $_SESSION['member_id'],
                'parentid' => getParentId(),
                'salesrepid' => $salesrep,
                'status' => Entity::STATUS_CACTIVE,
                'vehicles_run' => $leadData['shipping_vehicles_run'],
                'ship_via' => $leadData['shipping_ship_via'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $leadData['referred_by'],
                'lead_type' => 1,
                'website' => $leadData['website'],
                'buysell' => json_encode($leadData['buysell']),
                'buysell_days' => json_encode($leadData['buysell_days']),
                'referred_by' => $referrer_name_value,
                'referred_id' => $leadData['referred_by'],
            );

            if($leadData['next_shipping_date']){
                $insert_arr['next_shipping_date'] = date("Y-m-d", strtotime($leadData['next_shipping_date']));
            }

            if($leadData['avail_pickup_date']){
                $insert_arr['avail_pickup_date'] = date("Y-m-d", strtotime($leadData['avail_pickup_date']));
                $insert_arr['est_ship_date'] = date("Y-m-d", strtotime($leadData['avail_pickup_date']));
            }

            if($leadData['calling_for']){
                $insert_arr['calling_for'] = $leadData['calling_for'];
            }

            $entity = new Entity($this->daffny->DB);
            $entity->create($insert_arr);
            
            /* Create Shipper */
            $shipper = new Shipper($this->daffny->DB);
            $insert_arr = array(
                'fname' => $leadData['shipper_fname'],
                'lname' => $leadData['shipper_lname'],
                'email' => $leadData['shipper_email'],
                'company' => $leadData['shipper_company'],
                'phone1' => str_replace("-", "", $leadData['shipper_phone1']),
                'phone2' => str_replace("-", "", $leadData['shipper_phone2']),
                'mobile' => str_replace("-", "", $leadData['shipper_mobile']),
                'fax' => $leadData['shipper_fax'],
                'address1' => $leadData['shipper_address1'],
                'address2' => $leadData['shipper_address2'],
                'city' => $leadData['shipper_city'],
                'state' => $leadData['shipper_state'],
                'zip' => $leadData['shipper_zip'],
                'country' => $leadData['shipper_country'],
                'shipper_hours' => $leadData['shipper_hours'],
                'units_per_month' => $leadData['units_per_month'],
                'shipment_type' => $leadData['shipment_type'],
                'shipper_type' => $leadData['shipper_type'],
            );
            $shipper->create($insert_arr, $entity->id);
            /* Create Origin */
            $origin = new Origin($this->daffny->DB);
            $insert_arr = array(
                'address1' => $leadData['origin_address'],
                'city' => $leadData['origin_city'],
                'state' => $leadData['origin_state'],
                'zip' => $leadData['origin_state'],
                'country' => 'US',
            );
            $origin->create($insert_arr, $entity->id);
            /* Create Destination */
            $destination = new Destination($this->daffny->DB);
            $insert_arr = array(
                'address1' => $leadData['destination_address'],
                'city' => $leadData['destination_city'],
                'state' => $leadData['destination_state'],
                'zip' => $leadData['destination_zip'],
                'country' => 'US',
            );
            $destination->create($insert_arr, $entity->id);
            /* Update Quote */
            $distance = 'NULL';
            $update_arr = array(
                'shipper_id' => $shipper->id,
                'origin_id' => $origin->id,
                'destination_id' => $destination->id,
                'assigned_date' => $entity->created,
                'distance' => $distance,
            );
            $entity->update($update_arr);
            /* Create Vehicles */
            //Follow up
            $followup = new FollowUp($this->daffny->DB);
            $days = (int) $entity->getAssigned()->getDefaultSettings()->first_quote_followup;
            $followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int) date("m"), (int) date("d") + $days, (int) date("Y"))), $entity->id);

            /* Create Note */
            if (trim($leadData['note_to_shipper']) != "") {

                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $leadData['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));

            }

            /* Save Shipper in Accounts */
            
            if ($leadData['shipper_company']) {
                $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_company']) . "' AND state='" . $leadData['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_lname']) . "' AND `is_shipper` = 1)");
            } else {
                $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='' AND state='" . $leadData['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $leadData['shipper_lname']) . "' AND `is_shipper` = 1)");
            }

            $account = new Account($this->daffny->DB);
            $accountArray = array(
                'owner_id' => getParentId(),
                'company_name' => $leadData['shipper_company'],
                'status' => Account::STATUS_ACTIVE,
                'is_carrier' => 0,
                'is_shipper' => 1,
                'is_location' => 0,
                'first_name' => $leadData['shipper_fname'],
                'last_name' => $leadData['shipper_lname'],
                'email' => $leadData['shipper_email'],
                'phone1' => str_replace("-", "", $leadData['shipper_phone1']),
                'phone2' => str_replace("-", "", $leadData['shipper_phone2']),
                'cell' => str_replace("-", "", $leadData['shipper_mobile']),
                'fax' => $leadData['shipper_fax'],
                'address1' => $leadData['shipper_address1'],
                'address2' => $leadData['shipper_address2'],
                'city' => $leadData['shipper_city'],
                'state' => $leadData['shipper_state'],
                'state_other' => $leadData['shipper_state'],
                'zip_code' => $leadData['shipper_zip'],
                'country' => $leadData['shipper_country'],
                'shipper_type' => $leadData['shipper_type'],
                'hours_of_operation' => $leadData['shipper_hours'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $leadData['referred_by'],
            );

            if (empty($rowShipper)) {

                $account->create($accountArray);
                
                $update_account_id_arr = array(
                    'account_id' => $account->id,
                );

                $entity->update($update_account_id_arr);

            } else {

                $upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $accountArray);
                $this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

                // Update Entity
                $update_account_id_arr = array(
                    'account_id' => $rowShipper["id"],
                );
                $entity->update($update_account_id_arr);
            }

            $entity->updateHeaderTable();
            $this->daffny->DB->transaction("commit");
            $this->setFlashInfo("Lead has been successfully saved");
            $_SESSION["buysell"] = '';
            $_SESSION["buysell_days"] = '';

            redirect(getLink('leads/created/'));
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            print $e;
        }
    }

    public function edit()
    {
        try {
            $this->tplname = 'leads.edit';
            $this->input['error'] = "";
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Lead ID");
            }

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            if ($entity->readonly) {
                throw new UserException("Access Denied.");
            }

            if (isset($_POST['submit']) && $leadData = $this->checkEditFormCreate()) {

                $_SESSION["buysell"] = post_var("buysell");
                $_SESSION["buysell_days"] = post_var("buysell_days");

                $info = "Edit Lead-" . $entity->number . "(" . $entity->id . ")";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();

                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                /* Update Shipper */
                $update_arr = array(
                    'fname' => $leadData['shipper_fname'],
                    'lname' => $leadData['shipper_lname'],
                    'email' => $leadData['shipper_email'],
                    'company' => $leadData['shipper_company'],
                    'phone1' => str_replace("-", "", $leadData['shipper_phone1']),
                    'phone2' => str_replace("-", "", $leadData['shipper_phone2']),
                    'mobile' => str_replace("-", "", $leadData['shipper_mobile']),
                    'fax' => $leadData['shipper_fax'],
                    'address1' => $leadData['shipper_address1'],
                    'address2' => $leadData['shipper_address2'],
                    'city' => $leadData['shipper_city'],
                    'state' => $leadData['shipper_state'],
                    'zip' => $leadData['shipper_zip'],
                    'country' => $leadData['shipper_country'],
                    'shipper_hours' => $leadData['shipper_hours'],
                    'units_per_month' => $leadData['units_per_month'],
                    'shipment_type' => $leadData['shipment_type'],
                    'shipper_type' => $leadData['shipper_type'],
                );
                $shipper->update($update_arr);

                $referrer_name_value = "";
                if ($leadData['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $leadData['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }

                /* Update Lead */
                $update_arr = array(
                    'quoted' => date("Y-m-d H:i:s"),
                    'est_ship_date' => date("Y-m-d", strtotime($leadData['shipping_est_date'])),
                    'vehicles_run' => $leadData['shipping_vehicles_run'],
                    'ship_via' => $leadData['shipping_ship_via'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $leadData['referred_by'],
                    'lead_type' => 1,
                    'website' => $leadData['website'],
                    'buysell' => json_encode($leadData['buysell']),
                    'buysell_days' => json_encode($leadData['buysell_days']),
                    'next_shipping_date' => date("Y-m-d", strtotime($leadData['next_shipping_date'])),
                );
                $entity->update($update_arr);

                if (trim($_POST['internal_note']) != "") {
                    $note_array = array(
                        "entity_id" => $entity->id,
                        "sender_id" => $_SESSION['member_id'],
                        "type" => Note::TYPE_INTERNAL,
                        "status" => 1,
                        "text" => rawurldecode($_POST['internal_note']));
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);
                }

                $this->daffny->DB->transaction("commit");
                $this->setFlashInfo("Lead successfully saved");
                $entity->updateHeaderTable();
                redirect(getLink('leads/show/id/' . $_GET['id']));
                $_POST = array();
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
            if (count($_POST) == 0) {
                /* Load Shipper data */
                $shipper = $entity->getShipper();
                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = formatPhone($shipper->phone1);
                $this->input['shipper_phone2'] = formatPhone($shipper->phone2);
                $this->input['shipper_mobile'] = formatPhone($shipper->mobile);
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;
                $this->input['shipper_hours'] = $shipper->shipper_hours;
                $this->input['units_per_month'] = $shipper->units_per_month;
                $this->input['shipment_type'] = $shipper->shipment_type;
                $this->input['referred_by'] = $entity->referred_id;
                $this->input['website'] = $entity->website;
                $this->input['buysell'] = json_decode($entity->buysell);
                $this->input['buysell_days'] = json_decode($entity->buysell_days);
                $this->input['next_shipping_date'] = date("m/d/Y", strtotime($entity->next_shipping_date));

                $_SESSION["buysell"] = $this->input['buysell'];
                $_SESSION["buysell_days"] = $this->input['buysell_days'];
            }

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", getLink("leads/show/id/" . $_GET['id']) => "Lead #" . $entity->getNumber(), '' => "Edit"));
            $this->getEditForm();

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function editimported()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('leads/show/id/' . $_GET['id']));
            }

            $this->tplname = "leads.edit_imported";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('leads') => "leads", getLink('leads/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Edit"));
            $this->title = "Edit Order #" . $entity->number;

            //Ask if need to post to CD?
            $ask_post_to_cd = false;
            $settings = $entity->getAssigned()->getDefaultSettings();
            if ($entity->status == Entity::STATUS_POSTED) {

                if ($settings->central_dispatch_uid != "" && $settings->central_dispatch_post == 1) {
                    $ask_post_to_cd = true;
                }
            }

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditFormImported(false, $settings->referrer_status, $entity->status)) { 

                $info = "Edit Order-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();

                $referrer_name_value = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }
				$shipper = new Account($this->daffny->DB);
				$shipperArr = array(
					'owner_id' => getParentId(),
					'company_name' => $sql_arr['shipper_company'],
					'status' => Account::STATUS_ACTIVE,
					'is_carrier' => 0,
					'is_shipper' => 1,
					'is_location' => 0,
					'first_name' => $sql_arr['shipper_fname'],
					'last_name' => $sql_arr['shipper_lname'],
					'email' => $sql_arr['shipper_email'],
					'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
					'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
					'cell' => str_replace("-", "", $sql_arr['shipper_mobile']),
					'fax' => $sql_arr['shipper_fax'],
					'address1' => $sql_arr['shipper_address1'],
					'address2' => $sql_arr['shipper_address2'],
					'city' => $sql_arr['shipper_city'],
					'state' => $sql_arr['shipper_state'],
					'state_other' => $sql_arr['shipper_state'],
					'zip_code' => $sql_arr['shipper_zip'],
					'country' => $sql_arr['shipper_country'],
					'shipper_type' => $sql_arr['shipper_type'],
					'hours_of_operation' => $sql_arr['shipper_hours'],
					'referred_by' => $referrer_name_value,
					'referred_id' => $sql_arr['referred_by'],

				);

				if ($sql_arr['shipper_company']) {
					$rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						(`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_company']) . "' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");

				} else {

					$rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE
						(`company_name` ='' AND state='" . $sql_arr['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $sql_arr['shipper_lname']) . "' AND `is_shipper` = 1)");

				}

				if (empty($rowShipper)) {
					$shipper->create($shipperArr);
					// Update Entity
					$update_account_id_arr = array(
						'account_id' => $shipper->id,
					);
					$entity->update($update_account_id_arr);

				} else {
					if ($rowShipper["id"] != '' && $sql_arr['shipper_company'] != "") {
						unset($shipperArr['referred_by']);
						unset($shipperArr['referred_id']);
						$upd_account_arr = $this->daffny->DB->PrepareSql("app_accounts", $shipperArr);
						$this->daffny->DB->update("app_accounts", $upd_account_arr, "id = '" . $rowShipper["id"] . "' ");

						// Update Entity
						$update_account_id_arr = array(
							'account_id' => $rowShipper["id"],
						);
						$entity->update($update_account_id_arr);
					}
				}

                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                if ($sql_arr['shipper_country'] != "US") {
                    $sql_arr['shipper_state'] = $sql_arr['shipper_state2'];
                }
                $update_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
                );
                $shipper->update($update_arr);

                /* UPDATE ORIGIN */
                $origin = $entity->getOrigin();
                if ($sql_arr['origin_country'] != "US") {
                    $sql_arr['origin_state'] = $sql_arr['origin_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                );
                $origin->update($update_arr);

                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['destination_country'] != "US") {
                    $sql_arr['destination_state'] = $sql_arr['destination_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                );

                $destination->update($update_arr);

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
                $update_arr = array(
                    'ship_via' => (int) $sql_arr['shipping_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                    'load_date_type' => (int) $sql_arr['load_date_type'],
                    'delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'payments_terms' => $sql_arr['payments_terms'],
                    'blocked_by' => 'NULL',
                    'est_ship_date' => date("Y-m-d", strtotime($sql_arr['est_ship_date'])),
                    'avail_pickup_date' => date("Y-m-d", strtotime($sql_arr['est_ship_date'])),
                    'blocked_time' => 'NULL',

                );

                if (is_null($this->input['source_id']) || $this->input['source_id'] == '') {
                    $update_arr['source_id'] = $sql_arr['source_id'];
                } else {
                    $update_arr['referred_by'] = $referrer_name_value;
                    $update_arr['referred_id'] = $sql_arr['referred_by'];
                }

                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }

                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }

                        if ($NotesStr != "") {
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );

                        $vehicleValue->update($insert_arr);
                    }
                }

                $this->daffny->DB->transaction("commit");
                if (post_var('send_email') == '1') {
                    $entity->sendOrderConfirmation();
                }
                $_POST = array();
                $entity->getVehicles(true);

                $this->setFlashInfo("Lead Updated");
                $this->daffny->DB->query("CALL Set_Batch_ReferredBy_EntityID('" . $entity->id . "')");
                $entity->updateHeaderTable();
                redirect(getLink("leads", "showimported", "id", (int) $_GET['id']));
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
                    $this->setFlashError($this->err);
                }
            }

            if (count($_POST) == 0) {
                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = formatPhone($shipper->phone1);
                $this->input['shipper_phone2'] = formatPhone($shipper->phone2);
                $this->input['shipper_mobile'] = formatPhone($shipper->mobile);
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_state2'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;

                $this->input['shipper_type'] = $shipper->shipper_type;
                $this->input['shipper_hours'] = $shipper->shipper_hours;

                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['origin_address1'] = $origin->address1;
                $this->input['origin_address2'] = $origin->address2;
                $this->input['origin_city'] = $origin->city;
                $this->input['origin_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['origin_zip'] = $origin->zip;
                $this->input['origin_country'] = $origin->country;
                $this->input['origin_contact_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['origin_company_name'] = $origin->company;
                $this->input['origin_phone1'] = formatPhone($origin->phone1);
                $this->input['origin_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['origin_mobile'] = formatPhone($origin->phone_cell);
                $this->input['origin_buyer_number'] = $entity->buyer_number;
                $this->input['origin_booking_number'] = $entity->booking_number;

                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;

                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['destination_address1'] = $destination->address1;
                $this->input['destination_address2'] = $destination->address2;
                $this->input['destination_city'] = $destination->city;
                $this->input['destination_state'] = $destination->state;
                $this->input['destination_state2'] = $destination->state;
                $this->input['destination_zip'] = $destination->zip;
                $this->input['destination_country'] = $destination->country;
                $this->input['destination_contact_name'] = $destination->name;
                $this->input['destination_company_name'] = $destination->company;
                $this->input['destination_phone1'] = formatPhone($destination->phone1);
                $this->input['destination_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['destination_mobile'] = formatPhone($destination->phone_cell);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['destination_booking_number'] = $destination->booking_number;
                $this->input['destination_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;

                /* Load Shipping Information */
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['est_ship_date'] = (strtotime($entity->est_ship_date) != 0) ? $entity->getShipDate("m/d/Y") : "";
                $this->input['load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['load_date_type'] = $entity->load_date_type;
                $this->input['delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['delivery_date_type'] = $entity->delivery_date_type;
                $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
                $this->input['shipping_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['referred_by'] = $entity->referred_id;
                $this->input['source_id'] = $entity->source_id;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['pickup_terminal_fee'] = $entity->pickup_terminal_fee;
                $this->input['delivery_terminal_fee'] = $entity->dropoff_terminal_fee;
                $this->input['payments_terms'] = $entity->payments_terms;

                /* Load Shipper Note */
                $notes = $entity->getNotes();
                if (isset($notes[Note::TYPE_FROM][0])) {
                    $this->input['notes_from_shipper'] = $notes[Note::TYPE_FROM][0]->text;
                } else {
                    $this->input['notes_from_shipper'] = "";
                }
            }
            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            $this->daffny->tpl->ask_post_to_cd = $ask_post_to_cd;

            $this->form->TextArea("payments_terms", 2, 10, array('style' => 'height:77px;width:230px;', 'tabindex' => 69), $this->requiredTxt . "Carrier Payment Terms", "</td><td>");
            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->getEditFormImported($settings->referrer_status, $entity->status);

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders'));
        }
    }

    public function edit_orig()
    {
        try {
            $this->tplname = 'leads.edit';
            $this->input['error'] = "";
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Lead ID");
            }

            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            if ($entity->readonly) {
                throw new UserException("Access Denied.");
            }

            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm($entity)) {
                $entity = new Entity($this->daffny->DB);
                $entity->load($_GET['id']);
                $shipper = $entity->getShipper();
                if ($sql_arr['shipper_country'] != "US") {
                    $sql_arr['shipper_state'] = $sql_arr['shipper_state2'];
                }
                $update_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => $sql_arr['shipper_phone1'],
                    'phone2' => $sql_arr['shipper_phone2'],
                    'mobile' => $sql_arr['shipper_mobile'],
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                );
                $shipper->update($update_arr);

                $origin = $entity->getOrigin();
                if ($sql_arr['origin_country'] != "US") {
                    $sql_arr['origin_state'] = $sql_arr['origin_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                );
                $origin->update($update_arr);

                $destination = $entity->getDestination();
                if ($sql_arr['destination_country'] != "US") {
                    $sql_arr['destination_state'] = $sql_arr['destination_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                );
                $destination->update($update_arr);

                $notes = $entity->getNotes();
                if (count($notes[Note::TYPE_FROM]) != 0) {
                    $note = $notes[Note::TYPE_FROM][0];
                    $note->update(array('text' => $sql_arr['shipping_notes']));
                } else {
                    $note = new Note($this->daffny->DB);
                    $note->create(array('entity_id' => $entity->id, 'text' => $sql_arr['shipping_notes'], 'type' => Note::TYPE_FROM));
                }
                $entity->update(array(
                    'ship_via' => (int) $sql_arr['shipping_ship_via'],
                    'est_ship_date' => date("Y-m-d", strtotime($sql_arr['shipping_est_date'])),
                ));
                if ($entity->status == Entity::STATUS_UNREADABLE) {
                    $entity->setStatus(Entity::STATUS_ACTIVE);
                }
                $_POST = array();
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
            if (count($_POST) == 0) {
                /* Load Shipper data */
                $shipper = $entity->getShipper();
                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = $shipper->phone1;
                $this->input['shipper_phone2'] = $shipper->phone2;
                $this->input['shipper_mobile'] = $shipper->mobile;
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;
                /* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['origin_city'] = $origin->city;
                $this->input['origin_state'] = $origin->state;
                $this->input['origin_zip'] = $origin->zip;
                $this->input['origin_country'] = $origin->country;
                /* Load Destination Data */
                $destiantion = $entity->getDestination();
                $this->input['destination_city'] = $destiantion->city;
                $this->input['destination_state'] = $destiantion->state;
                $this->input['destination_zip'] = $destiantion->zip;
                $this->input['destination_country'] = $destiantion->country;
                /* Load Shipping Information */
                $this->input['shipping_est_date'] = $entity->getShipDate("m/d/Y");
                $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
                $this->input['shipping_ship_via'] = $entity->ship_via;
                /* Load Shipper Note */
                $notes = $entity->getNotes();
                if (isset($notes[Note::TYPE_FROM][0])) {
                    $this->input['shipping_notes'] = $notes[Note::TYPE_FROM][0]->getText();
                }
            }
            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", getLink("leads/show/id/" . $_GET['id']) => "Lead #" . $entity->getNumber(), '' => "Edit"));
            $this->getEditForm();
        } catch (FDException $e) {
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Lead ID");
            }

            $this->tplname = "leads.history";
            $this->title = "Lead History";
            $this->applyOrder(History::TABLE);
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", getLink("leads/show/id/" . $_GET['id']) => "Lead #" . $entity->getNumber(), '' => "History"));
            $historyManager = new HistoryManager($this->daffny->DB);
            $this->daffny->tpl->history = $historyManager->getHistory($this->order->getOrder(), $_SESSION['per_page'], "`entity_id` = " . (int) $_GET['id']);
            $this->pager = $historyManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function createdhistory()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Lead ID");
            }

            $this->tplname = "leads.created_history";
            $this->title = "Created Lead History";
            $this->applyOrder(History::TABLE);
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", getLink("leads/show/id/" . $_GET['id']) => "Lead #" . $entity->getNumber(), '' => "History"));
            $historyManager = new HistoryManager($this->daffny->DB);
            $this->daffny->tpl->history = $historyManager->getHistory($this->order->getOrder(), $_SESSION['per_page'], "`entity_id` = " . (int) $_GET['id']);
            $this->pager = $historyManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function email()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Lead ID");
            }

            $this->tplname = "leads.email";
            $this->title = "Lead Original E-Mail";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", getLink("leads/email/id/" . $_GET['id']) => "Lead #" . $entity->getNumber(), '' => "Original E-Mail"));
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('leads'));
        } catch (UserException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('leads'));
        }
    }

    public function search()
    {
        try {
            $this->daffny->tpl->status = Entity::STATUS_ARCHIVED;
            $this->tplname = "leads.main";
            $data_tpl = "leads.leads";
            $this->title = "Leads search result";
            $this->applyOrder("app_order_header e");

            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'avail_pickup_date';
            $this->order->Fields[] = 'dispatched';
            $this->order->Fields[] = 'delivered';
            $this->order->Fields[] = 'posted';
            $this->order->Fields[] = 'not_signed';
            $this->order->Fields[] = 'issue_date';
            $this->order->Fields[] = 'hold_date';
            $this->order->Fields[] = 'created';

            $this->order->setDefault('created', 'desc');

            $info = "Search Lead";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $search_type = array();
            $search_type[] = $_GET['type1'];
            $search_type[] = $_GET['type2'];
            $search_type[] = $_GET['type3'];
            $search_type[] = $_GET['type4'];
            $search_type[] = $_GET['type5'];
            $search_type[] = $_GET['type6'];
            $search_type[] = $_GET['type7'];
            $search_type[] = $_GET['type8'];
            $search_type[] = $_GET['type9'];
            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => 'Search'));
            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataHeader($_GET['lead_search_type'], $search_type, $_GET['search_string'], "", $_SESSION['per_page'], $this->order->getOrder());
            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

    private function loadLeadsPage($status)
    {

        $this->tplname = "leads.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = 'leads.leads';

        $this->daffny->DB->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");
        try {
            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'entityid';
            $this->order->Fields[] = 'last_activity_date';
            $this->order->Fields[] = 'assigned_date';
            $this->order->Fields[] = 'received';

            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'avail_pickup_date';
            $this->order->Fields[] = 'est_ship_date';
            $this->order->Fields[] = 'created';

            $this->order->setDefault('created', 'desc');

            switch ($status) {
                case Entity::STATUS_ACTIVE:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    $this->order->setDefault('received', 'desc');
                    break;
                case Entity::STATUS_ONHOLD:
                    $this->title = "Leads On Hold";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Hold"));
                    break;
                case Entity::STATUS_UNREADABLE:
                    $this->title = "Unreadable Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", 'Unreadable'));
                    break;
                case Entity::STATUS_ARCHIVED:
                    $this->title = "Archived Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Archived"));
                    break;
                case Entity::STATUS_PRIORITY:
                    $this->title = "Priority Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Priority"));
                    break;
                case Entity::STATUS_DEAD:
                    $this->title = "Dead Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Dead"));
                    break;
                case Entity::STATUS_LQUOTED:
                    $this->title = "Qoated";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Dead"));
                    $this->order->setDefault('received', 'desc');
                    break;
                default:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
            }

            $info = "Lead Listing:" . $this->title;
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);

            switch ($this->order->CurrentOrder) {
                case 'created':
                //$this->order->setTableIndex('e');
                case 'assigned_date':
                    //$this->order->setTableIndex('e');
				break;
            }

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_LEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);

            $this->daffny->tpl->lcreated = $created;

            $vehicleType = $this->daffny->DB->selectRows("*", 'app_vehicles_types', "");
            $this->daffny->tpl->vehicleType = $vehicleType;
            $entities_count = $entityManager->getCountHeaderLead(Entity::TYPE_LEAD);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['unreadable_count'] = $entities_count[Entity::STATUS_UNREADABLE];
            $this->input['assigned_count'] = $entities_count[Entity::STATUS_ASSIGNED];
            $this->input['quoted_count'] = $entities_count[Entity::STATUS_LQUOTED];
            $this->input['follow_count'] = $entities_count[Entity::STATUS_LFOLLOWUP];
            $this->input['expired_count'] = $entities_count[Entity::STATUS_LEXPIRED];
            $this->input['duplicate_count'] = $entities_count[Entity::STATUS_LDUPLICATE];
            $this->input['appointment_count'] = $entities_count[Entity::STATUS_LAPPOINMENT];

            $this->pager = $entityManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $AM_PM = 'AM';
            $TimeArr = array();
            for ($i = 0.15, $j = 0.15, $m = 0.15; $i <= 24; $i += 0.15, $j += 0.15, $m += 0.15) {
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

            $this->form->TextField("app_date", 10, array('style' => 'width: 100px;', 'tabindex' => 58), 'Appointment Date', "</td><td>");
            $this->form->ComboBox("app_time", array("" => "Time") + $TimeArr, array('style' => 'width:100px;'), 'Appointment Time', "</td><td>");
            $this->form->TextArea("app_note", 15, 10, array("style" => "height:77px; width:230px;"), "Notes", "</td ><td>");
            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->DB->query("COMMIT;");
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }

    }

    private function loadLeadsPageNew($status)
    {
        $this->tplname = "leads.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = 'leads.leads_created';
        try {
            $this->daffny->DB->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");
            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipper';
            $this->order->Fields[] = 'origin';
            $this->order->Fields[] = 'destination';
            $this->order->Fields[] = 'avail';
            $this->order->Fields[] = 'last_activity_date';
            $this->order->Fields[] = 'assigned_date';
            $this->order->setDefault('assigned_date', 'desc');

            switch ($status) {
                case Entity::STATUS_CACTIVE:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
                case Entity::STATUS_CONHOLD:
                    $this->title = "Leads On Hold";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Hold"));
                    break;
                case Entity::STATUS_CUNREADABLE:
                    $this->title = "Unreadable Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", 'Unreadable'));
                    break;
                case Entity::STATUS_CARCHIVED:
                    $this->title = "Archived Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Archived"));
                    break;
                case Entity::STATUS_CPRIORITY:
                    $this->title = "Priority Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Priority"));
                    break;
                case Entity::STATUS_CDEAD:
                    $this->title = "Dead Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Dead"));
                    break;
                default:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
            }

            $info = "Lead Listing:" . $this->title;
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);

            switch ($this->order->CurrentOrder) {
                case 'created':
                    $this->order->setTableIndex('e');
                case 'assigned':
                    $this->order->setTableIndex('e');
                    break;
            }

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_CLEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);

            $entities_count = $entityManager->getCountHeaderLead(Entity::TYPE_CLEAD);
            $this->input['active_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_CONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_CARCHIVED];
            $this->input['unreadable_count'] = $entities_count[Entity::STATUS_CUNREADABLE];
            $this->input['created_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['priority_count'] = $entities_count[Entity::STATUS_CPRIORITY];
            $this->input['dead_count'] = $entities_count[Entity::STATUS_CDEAD];
            $this->input['cquoted_count'] = $entities_count[Entity::STATUS_CQUOTED];
            $this->input['cfollow_count'] = $entities_count[Entity::STATUS_CFOLLOWUP];
            $this->input['cexpired_count'] = $entities_count[Entity::STATUS_CEXPIRED];
            $this->input['cduplicate_count'] = $entities_count[Entity::STATUS_CDUPLICATE];
            $this->input['cappointment_count'] = $entities_count[Entity::STATUS_CAPPOINMENT];
            $this->input['assigned_count'] = $entities_count[Entity::STATUS_CASSIGNED];

            $entitiesCoverted_count = $entityManager->getCountConverted(Entity::TYPE_CLEAD);
            $this->input['converted_count'] = $entitiesCoverted_count[Entity::STATUS_CONVERTED];

            $this->pager = $entityManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

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

            $this->form->TextField("app_date", 10, array('style' => 'width: 100px;', 'tabindex' => 58), 'Appointment Date', "</td><td>");
            $this->form->ComboBox("app_time", array("" => "Time") + $TimeArr, array('style' => 'width:100px;'), 'Appointment Time', "</td><td>");
            $this->form->TextArea("app_note", 15, 10, array("style" => "height:77px; width:230px;"), "Notes", "</td ><td>");

            $this->daffny->DB->query("COMMIT;");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

    private function loadLeadsPageConverted($status)
    {
        $this->tplname = "leads.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = 'leads.leads_converted';
        try {
            $this->daffny->DB->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");
            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipper';
            $this->order->Fields[] = 'origin';
            $this->order->Fields[] = 'destination';
            $this->order->Fields[] = 'avail';
            $this->order->Fields[] = 'last_activity_date';
            $this->order->setDefault('assigned_date', 'desc');

            switch ($status) {
                case Entity::STATUS_CACTIVE:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
                case Entity::STATUS_CONHOLD:
                    $this->title = "Leads On Hold";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Hold"));
                    break;
                case Entity::STATUS_CUNREADABLE:
                    $this->title = "Unreadable Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", 'Unreadable'));
                    break;
                case Entity::STATUS_CARCHIVED:
                    $this->title = "Archived Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Archived"));
                    break;
                case Entity::STATUS_CPRIORITY:
                    $this->title = "Priority Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Priority"));
                    break;
                case Entity::STATUS_CDEAD:
                    $this->title = "Dead Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Dead"));
                    break;
                default:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
            }

            $info = "Lead Listing:" . $this->title;
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);

            switch ($this->order->CurrentOrder) {
                case 'created':
                    $this->order->setTableIndex('e');
                case 'assigned':
                    $this->order->setTableIndex('e');
                    break;
            }

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataConverted(Entity::TYPE_CLEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);

            $entities_count = $entityManager->getCountHeaderLead(Entity::TYPE_CLEAD);
            $this->input['active_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_CONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_CARCHIVED];
            $this->input['unreadable_count'] = $entities_count[Entity::STATUS_CUNREADABLE];
            $this->input['created_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['priority_count'] = $entities_count[Entity::STATUS_CPRIORITY];
            $this->input['dead_count'] = $entities_count[Entity::STATUS_CDEAD];
            $this->input['assigned_count'] = $entities_count[Entity::STATUS_CASSIGNED];
            $this->input['cquoted_count'] = $entities_count[Entity::STATUS_CQUOTED];
            $this->input['cfollow_count'] = $entities_count[Entity::STATUS_CFOLLOWUP];
            $this->input['cexpired_count'] = $entities_count[Entity::STATUS_CEXPIRED];
            $this->input['cduplicate_count'] = $entities_count[Entity::STATUS_CDUPLICATE];
            $this->input['cappointment_count'] = $entities_count[Entity::STATUS_CAPPOINMENT];
            $entitiesCoverted_count = $entityManager->getCountConverted(Entity::TYPE_CLEAD);
            $this->input['converted_count'] = $entitiesCoverted_count[Entity::STATUS_CONVERTED];

            $this->pager = $entityManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $this->daffny->DB->query("COMMIT;");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

    protected function getEditForm()
    {

        /* SHIPPER */
        $this->form->TextField("shipper", 32, array(), "Shipper", "</td><td>");

        $this->form->TextField("shipper_fname", 32, array('tabindex' => 1), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array('tabindex' => 2), "Last Name", "</td><td>");
        $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "</td><td>");
        $this->form->ComboBox('shipper_type', array(''=>'Select One','Commercial' => 'Commercial', 'Residential' => 'Residential'), array('tabindex' => 4), $this->requiredTxt . 'Shipper Type', '</td><td>');
        $this->form->TextField("shipper_hours", 200, array('tabindex' => 5), "Hours", "</td><td>");
        $this->form->CheckBox("shipper_add", array('tabindex' => 6), "Add to saved shippers list", "</td><td>");

        $this->form->TextField("shipper_email", 100, array('class' => 'email', 'tabindex' => 7), "Email", "</td><td>");

        $this->form->TextField("shipper_phone1", 32, array('class' => 'phone', 'tabindex' => 8), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone2", 32, array('class' => 'phone', 'tabindex' => 9), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone', 'tabindex' => 10), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('tabindex' => 11), "Fax", "</td><td>");

        $referrers_arrTemp = array();
        if ($this->input['referred_by'] == "" || $this->input['referred_by'] == 0) {
            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array('' => 'Select One');
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
            $referrers_arrTemp[] = "Select One";
        } else {
            $referrer_name_value = "";
            if ($this->input['referred_by'] != "") {
                $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $this->input['referred_by'] . "'");
                if (!empty($row_referrer)) {
                    $referrer_name_value = $row_referrer['name'];

                }
            }
            $referrers_arr = array($this->input['referred_by'] => $referrer_name_value);
        }
        $referrers_arrTemp = $referrers_arr;
        $this->form->ComboBox("referred_by", $referrers_arrTemp, array('tabindex' => 12), $this->requiredTxt . "Source", "</td><td>");

        $this->form->ComboBox("units_per_month", array('' => 'Select One', "1-10" => "1-10", "10-20" => "10-20", "20-30" => "20-30", "30+" => "30+"), array('tabindex' => 13),  "Units per Month", "</td><td>");

        $this->form->TextField("shipper_address1", 64, array('tabindex' => 14), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array('tabindex' => 15), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city', 'tabindex' => 16), "City", "</td><td>");
        $this->form->ComboBox('shipper_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:130px;', 'tabindex' => 17), "State/Zip", "</td><td>", true);
        $this->form->TextField("shipper_zip", 8, array('style' => 'width:70px;margin-left:7px;', 'class' => 'zip', 'tabindex' => 18), "", "");
        $this->form->ComboBox("shipper_country", $this->getCountries(), array('tabindex' => 19), "Country", "</td><td>");
        $this->form->ComboBox("shipment_type", array('' => 'Select One', "1" => "Full load", "2" => "Singles", "3" => "Both"), array('tabindex' => 20, 'style' => ''),  "Shipment", "</td><td>");
        $this->form->TextField("website", 64, array('tabindex' => 21), "Website", "</td><td>");

        $buysell = array("Not Applicable" => "Not Applicable", "Manhelm" => "Manhelm", "Adessa" => "Adessa", "Smart Auction" => "Smart Auction", "Ove.com" => "Ove.com", "Other" => "Other");
        $buysellSelected = $_SESSION["buysell"];

        $this->form->helperMLTPL("buysell[]", $buysell, $buysellSelected, array("id" => "buysell", "multiple" => "multiple", 'tabindex' => 22), "Auctions", "</td><td colspan=\"3\">");

        $busell_days = array("1" => "Mon", "2" => "Tue", "3" => "Wed", "4" => "Thu", "5" => "Fri", "6" => "Sat", "7" => "Sun");
        $busell_daysSelected = $_SESSION["buysell_days"];

        $this->form->helperMLTPL("buysell_days[]", $busell_days, $busell_daysSelected, array("id" => "buysell_days", "multiple" => "multiple", 'tabindex' => 23), "Auction Days", "</td><td colspan=\"3\">");
        $this->form->TextField("next_shipping_date", 8, array('class' => 'datepicker', 'tabindex' => 24), "Follow up on", "</td><td>");
        $this->form->TextArea("note_to_shipper", 4, 20, array('style' => 'height:80px;', 'tabindex' => 21), "", "");

        $this->form->TextField("avail_pickup_date", 8, array('class' => 'datepicker'), $this->requiredTxt ." Est Shipping Date", "</td><td>");

        /* ORIGIN */
        $this->form->TextField("origin_city", 32, array('class' => 'geo-city', 'tabindex' => 16),  "City", "</td><td>");
        $this->form->ComboBox('origin_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => '', 'tabindex' => 17), "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 64, array('style' => '', 'class' => 'zip', 'tabindex' => 18), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array('tabindex' => 19), "Country", "</td><td>");
        /* DESTINATION */
        $this->form->TextField("destination_city", 32, array('class' => 'geo-city', 'tabindex' => 20),  "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => '', 'tabindex' => 21),  "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 64, array('style' => '', 'class' => 'zip', 'tabindex' => 22), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array('tabindex' => 23), "Country", "</td><td>");
        /* SHIPPING INFORMATION */
        $this->form->TextField("shipping_est_date", 8, array('class' => 'datepicker', 'tabindex' => 24),  "Estimated Ship Date", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", array('' => 'Select One') + Entity::$ship_via_string, array('tabindex' => 26), $this->requiredTxt . "Ship Via", "</td><td>");
        $this->form->TextArea("shipping_notes", 4, 10, array('style' => 'height:80px;', 'tabindex' => 27), "Notes from Shipper", "</td><td rowspan=\"3\">");
        /* ADDITIONAL */

    }

    protected function checkEditFormCreate_v2($create = false)
    {
        $sql_arr = $_POST;

        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_phone1' => "Shipper Phone",
            'calling_for' => "Reason for call",
            'note_to_shipper' => "Notes",
            'shipper_type' => "Shipment",
            'avail_pickup_date' => "1st Avail Date"
        );

        if($_POST['shipper_type'] == 'Commercial'){
            $checkEmpty['shipper_company'] = "Shipper Company";
        }

        if ($create) {
            if (!isset($_POST['year'])) {
                $this->err[] = "You must add at least one vehicle";
            }

        }
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }
        $this->checkEmail('shipper_email', "Shipper E-mail");
        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    protected function checkEditFormCreate($create = false)
    {
        $sql_arr = $_POST;
        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_lname' => "Shipper Last Name",
            'shipper_company' => "Shipper Company",
            'shipper_email' => "Shipper Email",
            'shipper_phone1' => "Shipper Phone",
            'referred_by' => "Referred By",
            'units_per_month' => "Units/Month",
            'shipment_type' => "Shipment",
        );
        if ($create) {
            if (!isset($_POST['year'])) {
                $this->err[] = "You must add at least one vehicle";
            }

        }
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }
        $this->checkEmail('shipper_email', "Shipper E-mail");
        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    /**
     * @param Entity $entity
     * @return array|bool
     */
    protected function checkEditForm($entity)
    {
        $sql_arr = $_POST;
        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_lname' => "Shipper Last Name",
            'shipper_email' => "Shipper Email",
            'shipper_phone1' => "Shipper Phone",
            'origin_city' => "Origin City",
            'origin_country' => 'Origin Country',
            'origin_zip' => 'Origin Zip',
            'destination_city' => "Destination City",
            'destination_country' => 'Destination Country',
            'destination_zip' => 'Destination Zip',
            'shipping_est_date' => 'Estimate Ship Date',
            'shipping_ship_via' => 'Ship Via',
        );

        if (count($entity->getVehicles()) == 0) {
            $this->err[] = "You should add at least one Vehicle";
        }
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }
        if ((trim(post_var("origin_state")) == "") && (trim(post_var("origin_state2")) == "")) {
            $this->isEmpty('origin_state', "Origin State");
        }
        if ((trim(post_var("destination_state")) == "") && (trim(post_var("destination_state2")) == "")) {
            $this->isEmpty('destination_state', "Destination State");
        }
        $this->checkEmail('shipper_email', "Shipper E-mail");
        if (count($this->err)) {
            return false;
        }
        return $sql_arr;
    }

    public function generate()
    {
        $firstNames = array('John', 'Jack', 'Bill', 'Tim', 'Steve', 'Alex', 'Mike');
        $lastNames = array('Rembo', 'Smith', 'Doe', 'Newton', 'Johnson', 'Jackson', 'Nickson');
        $countries = array("USA", "Canada");
        $states = array_keys(array_merge($this->getStates(), $this->getCanadaStates()));
        $cities = array("New York", "Albuquerque", "Los Angeles", "Coral Springs", "Las Vegas", "Chicago", "Washington");
        $companies = array("Sony", "Adidas", "Google", "Apple", "Microsoft", "Nike", "Audi", "BMW");
        $vehiclesMakes = array("BMW", "Audi", "Porche", "Bugatti", "Ferrari", "ZAZ", "Renault", "Volkswagen");
        $vehiclesModels = array("e36", "A0", "911", "Veyron", "F50", "Matiz", "Logan", "Golf");
        $vehiclesTypes = array("Sedan Midsize", "Sedan Small", "Convertible", "Van", "Coupe");
        $count = (isset($_GET['count']) && is_numeric($_GET['count'])) ? ((int) $_GET['count']) : 1;
        for ($j = 0; $j < $count; $j++) {
            $insert_arr = array(
                'received' => date("Y-m-d H:i:s"),
                'type' => Entity::TYPE_LEAD,
                'assigned_id' => $_SESSION['member_id'],
                'est_ship_date' => date("Y-m-d"),
                'status' => Entity::STATUS_ACTIVE,
                'vehicles_run' => mt_rand(1, 2),
                'ship_via' => mt_rand(1, 3),
            );
            $entity = new Entity($this->daffny->DB);
            $entity->create($insert_arr);
            // Create Shipper
            $insert_arr = array(
                'fname' => $firstNames[mt_rand(0, count($firstNames) - 1)],
                'lname' => $lastNames[mt_rand(0, count($lastNames) - 1)],
                'email' => strtolower($firstNames[mt_rand(0, count($firstNames) - 1)] . "@" . $lastNames[mt_rand(0, count($lastNames) - 1)] . ".tmp"),
                'company' => $companies[mt_rand(0, count($companies) - 1)],
                'phone1' => '(768) 521-4467',
                'phone2' => '(321) 521-4367',
                'phone3' => '(525) 521-4462',
                'mobile' => '(525) 521-4463',
                'fax' => '(525) 521-4464',
                'address1' => 'Lenina 1, 1',
                'address2' => '',
                'city' => $cities[mt_rand(0, count($cities) - 1)],
                'state' => $states[mt_rand(0, count($states) - 1)],
                'zip' => sprintf("%05u", mt_rand(5, 99999)),
                'country' => $countries[mt_rand(0, count($countries) - 1)],
            );
            $shipper = new Shipper($this->daffny->DB);
            $shipper->create($insert_arr);
            // Create Origin
            $insert_arr = array(
                'city' => $cities[mt_rand(0, count($cities) - 1)],
                'state' => $states[mt_rand(0, count($states) - 1)],
                'zip' => sprintf("%05u", mt_rand(5, 99999)),
                'country' => $countries[mt_rand(0, count($countries) - 1)],
            );
            $origin = new Origin($this->daffny->DB);
            $origin->create($insert_arr);
            // Create Destination
            $insert_arr = array(
                'city' => $cities[mt_rand(0, count($cities) - 1)],
                'state' => $states[mt_rand(0, count($states) - 1)],
                'zip' => sprintf("%05u", mt_rand(5, 99999)),
                'country' => $countries[mt_rand(0, count($countries) - 1)],
            );

            $destination = new Destination($this->daffny->DB);
            $destination->create($insert_arr);
            $update_arr = array(
                'shipper_id' => $shipper->id,
                'origin_id' => $origin->id,
                'destination_id' => $destination->id,
                'distance' => $distance,
            );
            $entity->update($update_arr);

            // Create Vehicles
            for ($i = 0; $i <= mt_rand(0, 3); $i++) {
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'year' => mt_rand(1960, 2012),
                    'make' => $vehiclesMakes[mt_rand(0, count($vehiclesMakes) - 1)],
                    'model' => $vehiclesModels[mt_rand(0, count($vehiclesModels) - 1)],
                    'type' => $vehiclesTypes[mt_rand(0, count($vehiclesTypes) - 1)],
                );
                $vehicle = new Vehicle($this->daffny->DB);
                $vehicle->create($insert_arr);
            }
        }
        redirect(getLink('leads'));
    }

    public function import()
    {

        set_time_limit(800000);
        ini_set('memory_limit', '3500M');
        ini_set('upload_max_filesize', '128M');
        ini_set('post_max_size', '128M');
        ini_set('max_input_time', 800000);

        $this->tplname = "leads.import";
        $this->title = "Import Leads";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => 'Import'));
        if (count($_FILES)) {
            $upload = new upload();
            $upload->out_file_dir = UPLOADS_PATH . "entity/";
            $upload->max_file_size = 50 * 1024 * 1024;
            $upload->form_field = "import";
            $upload->make_script_safe = 1;
            $upload->allowed_file_ext = array("xls", "xlsx", "csv");
            $upload->save_as_file_name = md5(time() . mt_rand()) . '_import';
            $upload->upload_process();
            try {
                switch ($upload->error_no) {
                    case 0:
                        $import = new Import();
                        $result = $import->importLeads($upload->saved_upload_name, $_SESSION['member_id'], $this->daffny->DB);
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
                die('ERROR: ' . $e->getMessage());
            }
        }
    }

    protected function getEditFormImported($referrer_status = 0, $ostatus = -3)
    {
        $readonly = '';
        $disabled = '';

        $member = new Member($this->daffny->DB);
        $member->load($_SESSION['member_id']);
        $this->daffny->tpl->isAutoQuoteAlowed = $member->isAutoQuoteAllowed();
        /* SHIPPER */
        $this->form->ComboBox("shipper", array("" => "New Shipper"), array('style' => 'width:190px;', "$disabled" => "$disabled"), "Select Shipper", "</td><td>");
        $this->form->TextField("shipper_fname", 32, array('tabindex' => 1, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array('tabindex' => 2, "class" => "elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "Last Name", "</td><td>");
        $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model', "$readonly" => "$readonly"), "Company", "<span class='required' id='shipper_company-span' style='display:none;'>*</span></td><td>");
        $this->form->ComboBox('shipper_type',array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled", 'onChange' => 'typeselected();'), $this->requiredTxt . 'Shipper Type    ', '</td><td>');
        $this->form->TextField("shipper_hours", 200, array('tabindex' => 5, "$readonly" => "$readonly"), "Hours", "</td><td>");
        $this->form->TextField("shipper_email", 100, array('class' => 'email', 'tabindex' => 6, "class" => "elementname", "elementname" => "input"), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("shipper_phone1", 32, array('tabindex' => 7, "class" => "phone elementname", "elementname" => "input", "$readonly" => "$readonly"), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone2", 32, array('class' => 'phone', 'tabindex' => 8, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone', 'tabindex' => 9, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('tabindex' => 10, "$readonly" => "$readonly"), "Fax", "</td><td>");
        $this->form->TextField("shipper_address1", 64, array('tabindex' => 12, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array('tabindex' => 13, "$readonly" => "$readonly"), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city', 'tabindex' => 14, "$readonly" => "$readonly"), "City", "</td><td>");
        $this->form->ComboBox('shipper_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:130px;', 'tabindex' => 15, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), "State/Zip", "</td><td>", true);
        $this->form->TextField("shipper_zip", 8, array('style' => 'width:70px;margin-left:7px;', 'class' => 'zip', 'tabindex' => 16, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("shipper_country", $this->getCountries(), array('tabindex' => 17, "$disabled" => "$disabled"), "Country", "</td><td>");
        /* ORIGIN */
        $this->form->TextField("origin_address1", 255, array('tabindex' => 18, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("origin_address2", 255, array('tabindex' => 19, "$readonly" => "$readonly"), "&nbsp;", "</td><td>");
        $this->form->TextField("origin_city", 255, array('class' => 'geo-city', 'tabindex' => 20, "elementname" => "input", "class" => "elementname", "$readonly" => "$readonly"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('origin_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => '', 'tabindex' => 21, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 10, array('style' => '', 'class' => 'zip', 'tabindex' => 22, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array('tabindex' => 23, "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->ComboBox('origin_type',array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 24, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled", 'onChange' => 'origintypeselected();'), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("origin_hours", 200, array('tabindex' => 25, "$readonly" => "$readonly"), "Hours", "</td><td>");
        /* ORIGIN CONTACT */
        $this->form->CheckBox("origin_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("origin_contact_name", 255, array('tabindex' => 26, "$readonly" => "$readonly"), "Contact Name", "</td><td>");
        $this->form->TextField("origin_contact_name2", 255, array('tabindex' => 27, "$readonly" => "$readonly"), "Contact Name 2", "</td><td>");
        $this->form->TextField("origin_company_name", 255, array('tabindex' => 28, "$readonly" => "$readonly"), "Company Name", "<span class='required' id='origin_company-span' style='display:none;'>*</span></td><td>");
        $this->form->TextField("origin_auction_name", 255, array('tabindex' => 29, "$readonly" => "$readonly"), "Auction Name", "<span class='required' id='origin_auction-span' style='display:none;'>*</span></td><td>");
        $this->form->TextField("origin_booking_number", 100, array('tabindex' => 30, "$readonly" => "$readonly"), "Booking Number", "</td><td>");
        $this->form->TextField("origin_buyer_number", 100, array('tabindex' => 31, "$readonly" => "$readonly"), "Buyer Number", "</td><td>");
        $this->form->TextField("origin_phone1", 255, array('class' => 'phone', 'tabindex' => 32, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("origin_phone2", 255, array('class' => 'phone', 'tabindex' => 33, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("origin_phone3", 255, array('class' => 'phone', 'tabindex' => 34, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("origin_mobile", 255, array('class' => 'phone', 'tabindex' => 35, "$readonly" => "$readonly"), "Mobile", "</td><td>");
        $this->form->TextField("origin_fax", 32, array('tabindex' => 36, "$readonly" => "$readonly"), "Fax", "</td><td>");

        /* DESTINATION */
        $this->form->TextField("destination_address1", 255, array('tabindex' => 37, "$readonly" => "$readonly"), "Address", "</td><td>");
        $this->form->TextField("destination_address2", 255, array('tabindex' => 38, "$readonly" => "$readonly"), "&nbsp;", "</td><td>");
        $this->form->TextField("destination_city", 255, array('class' => 'geo-city', 'tabindex' => 39, "elementname" => "input", "class" => "elementname", "$readonly" => "$readonly"), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => "Select One", 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => '', 'tabindex' => 40, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 10, array('style' => '', 'class' => 'zip', 'tabindex' => 41, "$readonly" => "$readonly"), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array('tabindex' => 42, "$disabled" => "$disabled"), "Country", "</td><td>");
        $this->form->ComboBox('destination_type',array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 43, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . 'Location Type  ', '</td><td>');
        $this->form->TextField("destination_hours", 200, array('tabindex' => 44, "$readonly" => "$readonly"), "Hours", "</td><td>");
        /* DESTINATION CONTACT */
        $this->form->CheckBox("destination_use_as_contact", array(), "Use as contact", "&nbsp;");
        $this->form->TextField("destination_contact_name", 255, array('tabindex' => 45, "$readonly" => "$readonly"), "Contact Name", "</td><td>");
        $this->form->TextField("destination_contact_name2", 255, array('tabindex' => 46, "$readonly" => "$readonly"), "Contact Name 2", "</td><td>");
        $this->form->TextField("destination_company_name", 255, array('tabindex' => 47, "$readonly" => "$readonly"), "Company Name", "</td><td>");
        $this->form->TextField("destination_auction_name", 255, array('tabindex' => 48, "$readonly" => "$readonly"), "Auction Name", "</td><td>");
        $this->form->TextField("destination_booking_number", 100, array('tabindex' => 49, "$readonly" => "$readonly"), "Booking Number", "</td><td>");
        $this->form->TextField("destination_buyer_number", 100, array('tabindex' => 50, "$readonly" => "$readonly"), "Buyer Number", "</td><td>");
        $this->form->TextField("destination_phone1", 255, array('class' => 'phone', 'tabindex' => 51, "$readonly" => "$readonly"), "Phone 1", "</td><td>");
        $this->form->TextField("destination_phone2", 255, array('class' => 'phone', 'tabindex' => 52, "$readonly" => "$readonly"), "Phone 2", "</td><td>");
        $this->form->TextField("destination_phone3", 255, array('class' => 'phone', 'tabindex' => 53, "$readonly" => "$readonly"), "Phone 3", "</td><td>");
        $this->form->TextField("destination_mobile", 255, array('class' => 'phone', 'tabindex' => 54, "$readonly" => "$readonly"), "Mobile", "</td><td>");

        $this->form->TextField("destination_fax", 32, array('tabindex' => 55, "$readonly" => "$readonly"), "Fax", "</td><td>");

        /* SHIPPING INFORMATION */// 'class' => 'datepicker',
        $this->form->TextField("avail_pickup_date", 10, array('style' => 'width: 100px;', 'tabindex' => 56, "$readonly" => "$readonly"), $this->requiredTxt . "1st Avail. Pickup Date", "</td><td>");
        $this->form->ComboBox("load_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 57, "$disabled" => "$disabled"), "Load Date", "</td><td>");
        $this->form->TextField("load_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 58, "$readonly" => "$readonly"));
        $this->form->ComboBox("delivery_date_type", array('' => 'Select One') + Entity::$date_type_string, array('style' => 'width: 100px;', 'tabindex' => 59, "$disabled" => "$disabled"), "Delivery Date", "</td><td>");
        $this->form->TextField("delivery_date", 10, array('class' => 'datepicker', 'style' => 'width: 100px;', 'tabindex' => 60, "$readonly" => "$readonly"));
        $this->form->ComboBox("shipping_vehicles_run", array('' => 'Select One') + Entity::$vehicles_run_string, array('tabindex' => 61, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "Vehicle(s) Run", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", array('' => 'Select One') + Entity::$ship_via_string, array('tabindex' => 62, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "Ship Via", "</td><td valign=\"top\">");

        $this->form->TextArea("notes_for_shipper", 2, 10, array('style' => 'height:40px;', "onkeyup" => "countChar(this)", "maxlength" => "60", 'tabindex' => 63, "$disabled" => "$disabled"), "Add special Note to appeared on FreightBoard ", "</td><td>");
        $this->form->TextArea("notes_from_shipper", 2, 10, array('style' => 'height:40px;', 'tabindex' => 64, "$disabled" => "$disabled"), "Special Dispatch Instructions", "</td><td>");
        $shipper_comment_attr = array('tabindex' => 65);
        if (isset($this->input['include_shipper_comment']) && $this->input['include_shipper_comment'] == "1") {
            $shipper_comment_attr["checked"] = "checked";
        }

        $this->form->CheckBox("include_shipper_comment", $shipper_comment_attr, "Include Shipper Comment on Dispatch Sheet", "&nbsp;");
        /* PRICING INFORMATION */
        $balance_paid_by = array(
            '' => 'Select One',
            'Cash on Delivery to Carrier' => array(
                Entity::BALANCE_COD_TO_CARRIER_CASH => 'COD - Cash/Certified Funds',
                Entity::BALANCE_COD_TO_CARRIER_CHECK => 'COD - Check',
            ),
            
            'Cash on Pickup to Carrier' => array(
                Entity::BALANCE_COP_TO_CARRIER_CASH => 'COP - Cash/Certified Funds',
                Entity::BALANCE_COP_TO_CARRIER_CHECK => 'COP - Check',
            ),
            'Broker is paying Carrier' => array(
                Entity::BALANCE_COMPANY_OWES_CARRIER_CASH => 'Billing - Cash/Certified Funds',
                Entity::BALANCE_COMPANY_OWES_CARRIER_CHECK => 'Billing - Check',
                Entity::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => 'Billing - Comcheck',
                Entity::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => 'Billing - QuickPay',
                Entity::BALANCE_COMPANY_OWES_CARRIER_ACH => 'Billing - ACH',

            ),
            'Carrier is paying Broker' => array(
                Entity::BALANCE_CARRIER_OWES_COMPANY_CASH => 'Invoice - Cash/Certified Funds',
                Entity::BALANCE_CARRIER_OWES_COMPANY_CHECK => 'Invoice - Check',
                Entity::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => 'Invoice - Comcheck',
                Entity::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => 'Invoice - QuickPay',
            ),
        );
        $this->form->ComboBox("balance_paid_by", $balance_paid_by, array('tabindex' => 68, "elementname" => "select", "class" => "elementname", "$disabled" => "$disabled"), $this->requiredTxt . "How is the carrier getting paid?", "</td><td>");

        /* PAYMENT INFORMATION */
        $costomer_balance_paid_by = array(
            '' => 'Select One',
            Entity::ACH => '1 - ACH',
            Entity::COMPANY_CHECK => '2 - Company Check',
            Entity::CREDIT_CARD => '3 - Credit Card',
            Entity::MONEY_ORDER => '4 - Money Order',
            Entity::PARSONAL_CHECK => '5 - Personal Check',
            Entity::WIRE_TRANSFER => '6 - Wire - Transfer',

        );

        $this->form->ComboBox("customer_balance_paid_by", $costomer_balance_paid_by, array('tabindex' => 69, "elementname" => "select", "class" => "elementname", "onchange" => "selectPayment();", "$disabled" => "$disabled"), $this->requiredTxt . "How is the customer paying us?", "</td><td>");

        $this->form->TextField("pickup_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 66, "$disabled" => "$disabled"), "Pickup Terminal Fee", "</td><td>$&nbsp;");
        $this->form->TextField("delivery_terminal_fee", 32, array('class' => 'decimal', 'style' => 'width:120px;', 'tabindex' => 67, "$disabled" => "$disabled"), "Delivery Terminal Fee", "</td><td>$&nbsp;");

        //Credit Card Information
        $this->form->TextField("e_cc_fname", 50, array('tabindex' => 70, "$disabled" => "$disabled"), "First Name", "</td><td>");
        $this->form->TextField("e_cc_lname", 50, array('tabindex' => 71, "$disabled" => "$disabled"), "Last Name", "</td><td>");
        $this->form->ComboBox("e_cc_type", array("" => "--Select--") + $this->getCCTypes(), array('tabindex' => 72, "style" => "width:150px;", "$disabled" => "$disabled"), "Type", "</td><td>");

        $this->form->TextField("e_cc_number", 16, array('tabindex' => 73, "class" => "creditcard", "$disabled" => "$disabled"), "Card Number", "</td><td>");
        $this->form->TextField("e_cc_cvv2", 4, array('tabindex' => 74, "class" => "cvv", "style" => "width:75px;", "$disabled" => "$disabled"), "CVV", "</td><td>");
        $this->form->ComboBox("e_cc_month", array("" => "--") + $this->months, array('tabindex' => 75, "style" => "width:50px;", "$disabled" => "$disabled"), "Exp. Date", "</td><td>");
        $this->form->ComboBox("e_cc_year", array("" => "--") + $this->getCCYears(), array('tabindex' => 76, "style" => "width:75px;", "$disabled" => "$disabled"), "", "");

        $this->form->TextField("e_cc_address", 255, array('tabindex' => 77, "$disabled" => "$disabled"), "Address", "</td><td>");
        $this->form->TextField("e_cc_city", 100, array('tabindex' => 78, "$disabled" => "$disabled"), "City", "</td><td>");
        $this->form->ComboBox("e_cc_state", array("" => "Select State") + $this->getStates(), array('tabindex' => 79, "style" => "width:150px;", "$disabled" => "$disabled"), "State", "</td><td>");
        $this->form->TextField("e_cc_zip", 11, array('tabindex' => 80, "class" => "zip", "style" => "width:100px;", "$disabled" => "$disabled"), "Zip Code", "</td><td>");

        if ($this->input['referred_by'] == "" || $this->input['referred_by'] == 0) {
            // Additional
            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array('' => 'Select One');
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
        } else {
			$referrer_name_value = "";
			if ($this->input['referred_by'] != "") {
				$row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $this->input['referred_by'] . "'");
				if (!empty($row_referrer)) {
					$referrer_name_value = $row_referrer['name'];

				}
			}
			$referrers_arr = array($this->input['referred_by'] => $referrer_name_value);
        }

        $this->form->ComboBox("referred_by", $referrers_arr, array('tabindex' => 11, "$disabled" => "$disabled"), $this->requiredTxt . "Sources", "</td><td>");
        
		if (is_null($this->input['source_id']) || $this->input['source_id'] == '') {
            // Additional
            $sources = Entity::getSources($this->daffny->DB);
            $sources_arr = array('' => 'Select One');
            foreach ($sources as $source) {
                $sources_arr[$source->id] = $source->company_name;
            }

        } else {

            $source_name_value = "";
            if ($this->input['source_id'] != "") {
                $row_source = $this->daffny->DB->select_one("domain,company_name", "app_leadsources", "WHERE  id = '" . $this->input['source_id'] . "'");
                if (!empty($row_source)) {
                    $source_name_value = $row_source['company_name'];

                }
            }
            $sources_arr = array($this->input['source_id'] => $source_name_value);

        }

        $this->form->ComboBox("source_id", $sources_arr, array('tabindex' => 11, "$disabled" => "$disabled"), $this->requiredTxt . "Sources", "</td><td>");
        $this->form->TextArea("note_to_shipper", 4, 10, array('style' => 'height: 80px;', 'tabindex' => 56, "$disabled" => "$disabled"), "", "</td><td align='center'>");
        $this->form->TextField("est_ship_date", 8, array('class' => 'datepicker', 'tabindex' => 24), $this->requiredTxt . "Estimated Ship Date", "</td><td>");
    }

    protected function checkEditFormImported($create = false, $referrer_status = 0, $status = -2)
    {
        $sql_arr = $_POST;
        $sql_arr['shipper_email'] = trim($sql_arr['shipper_email']);
        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_lname' => "Shipper Last Name",
            'shipper_email' => "Shipper Email",
            'shipper_phone1' => "Shipper Phone",
            'shipper_type' => "Shipper Type",
            'origin_city' => "Pickup City",
            'origin_country' => 'Pickup Country',
            'origin_zip' => 'Pickup Zip',
            'destination_city' => "Delivery City",
            'destination_country' => 'Delivery Country',
            'destination_zip' => 'Delivery Zip',
            'est_ship_date' => 'Estimated Ship Date',
            'shipping_ship_via' => 'Ship Via',

        );

        if ($_POST['source_id'] > 0) {
            $checkEmpty['source_id'] = "Source";
        } else {
            $checkEmpty['referred_by'] = "Referred By";
        }

        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        if ((trim(post_var("origin_state")) == "")) {
            $this->isEmpty('origin_state', "From State");
        }

        if ((trim(post_var("destination_state")) == "")) {
            $this->isEmpty('destination_state', "To State");
        }

        if (post_var("shipper_type") == "Commercial" && trim(post_var("shipper_hours")) == "") {
            $this->err[] = "Field <strong>Hours</strong> is mandatory for Commercial shippers.";
        }

        if (post_var("shipper_type") == "Commercial" && trim(post_var("shipper_company")) == "") {
            $this->err[] = "Field <strong>Company</strong> is mandatory for Commercial shippers.";
        }

        $this->checkEmail('shipper_email', "Shipper E-mail");
        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    public function editcreatedquote()
    {

        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Order ID", getLink('orders'));
            }

            $this->tplname = "leads.edit_created";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);
            if ($entity->readonly) {
                throw new UserException("Access Denied", getLink('leads'));
            }

            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('leads') => "leads", getLink('leads/show/id/' . $_GET['id']) => "Order #" . $entity->getNumber(), '' => "Edit"));
            $this->title = "Edit Quotes #" . $entity->number;

            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            if ((isset($_POST['submit']) || isset($_POST['submit_btn'])) && $sql_arr = $this->checkEditFormImported(false, $settings->referrer_status, $entity->status)) {
                
                $info = "Edit Quotes-" . $entity->number . "( " . $entity->id . " )";
                $applog = new Applog($this->daffny->DB);
                $applog->createInformation($info);

                $this->daffny->DB->transaction();

                $referrer_name_value = "";
                if ($sql_arr['referred_by'] != "") {
                    $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $sql_arr['referred_by'] . "'");
                    if (!empty($row_referrer)) {
                        $referrer_name_value = $row_referrer['name'];

                    }
                }

                /* UPDATE SHIPPER */
                $shipper = $entity->getShipper();
                if ($sql_arr['shipper_country'] != "US") {
                    $sql_arr['shipper_state'] = $sql_arr['shipper_state2'];
                }
                $update_arr = array(
                    'fname' => $sql_arr['shipper_fname'],
                    'lname' => $sql_arr['shipper_lname'],
                    'email' => $sql_arr['shipper_email'],
                    'company' => $sql_arr['shipper_company'],
                    'phone1' => str_replace("-", "", $sql_arr['shipper_phone1']),
                    'phone2' => str_replace("-", "", $sql_arr['shipper_phone2']),
                    'mobile' => str_replace("-", "", $sql_arr['shipper_mobile']),
                    'fax' => $sql_arr['shipper_fax'],
                    'address1' => $sql_arr['shipper_address1'],
                    'address2' => $sql_arr['shipper_address2'],
                    'city' => $sql_arr['shipper_city'],
                    'state' => $sql_arr['shipper_state'],
                    'zip' => $sql_arr['shipper_zip'],
                    'country' => $sql_arr['shipper_country'],
                    'shipper_type' => $sql_arr['shipper_type'],
                    'shipper_hours' => $sql_arr['shipper_hours'],
                );
                $shipper->update($update_arr);

                /* UPDATE ORIGIN */
                $origin = $entity->getOrigin();
                if ($sql_arr['origin_country'] != "US") {
                    $sql_arr['origin_state'] = $sql_arr['origin_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['origin_city'],
                    'state' => $sql_arr['origin_state'],
                    'zip' => $sql_arr['origin_zip'],
                    'country' => $sql_arr['origin_country'],
                );
                $origin->update($update_arr);

                /* UPDATE DESTINATION */
                $destination = $entity->getDestination();
                if ($sql_arr['destination_country'] != "US") {
                    $sql_arr['destination_state'] = $sql_arr['destination_state2'];
                }
                $update_arr = array(
                    'city' => $sql_arr['destination_city'],
                    'state' => $sql_arr['destination_state'],
                    'zip' => $sql_arr['destination_zip'],
                    'country' => $sql_arr['destination_country'],
                );

                $destination->update($update_arr);

                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
                $update_arr = array(
                    'ship_via' => (int) $sql_arr['shipping_ship_via'],
                    'avail_pickup_date' => ($sql_arr['avail_pickup_date'] == "" ? '' : date("Y-m-d", strtotime($sql_arr['avail_pickup_date']))),
                    'load_date' => empty($sql_arr['load_date']) ? '' : date("Y-m-d", strtotime($sql_arr['load_date'])),
                    'load_date_type' => (int) $sql_arr['load_date_type'],
                    'delivery_date' => empty($sql_arr['delivery_date']) ? '' : date("Y-m-d", strtotime($sql_arr['delivery_date'])),
                    'delivery_date_type' => (int) $sql_arr['delivery_date_type'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $sql_arr['referred_by'],
                    'distance' => $distance,
                    'information' => $sql_arr['notes_for_shipper'],
                    'include_shipper_comment' => (isset($sql_arr['include_shipper_comment']) ? "1" : "NULL"),
                    'balance_paid_by' => $sql_arr['balance_paid_by'],
                    'customer_balance_paid_by' => $sql_arr['customer_balance_paid_by'],
                    'pickup_terminal_fee' => $sql_arr['pickup_terminal_fee'],
                    'dropoff_terminal_fee' => $sql_arr['delivery_terminal_fee'],
                    'buyer_number' => $sql_arr['origin_buyer_number'],
                    'booking_number' => $sql_arr['origin_booking_number'],
                    'payments_terms' => $sql_arr['payments_terms'],
                    'blocked_by' => 'NULL',
                    'est_ship_date' => date("Y-m-d", strtotime($sql_arr['est_ship_date'])),
                    'blocked_time' => 'NULL',

                );

                $entity->update($update_arr);

                if (is_array($_POST['vehicle_tariff']) && sizeof($_POST['vehicle_tariff']) > 0) {
                    // update Vehicles
                    foreach ($_POST['vehicle_tariff'] as $key => $val) {
                        $vehicleValue = new Vehicle($this->daffny->DB);
                        $vehicleValue->load($key);

                        $NotesStr = "";
                        if ($vehicleValue->tariff != (float) rawurldecode($_POST['vehicle_tariff'][$key])) {
                            $NotesStr = "Total tarrif amount changed $" . $vehicleValue->tariff . " to $" . number_format((float) rawurldecode($_POST['vehicle_tariff'][$key]), 2, '.', '');
                        }

                        if ($vehicleValue->deposit != rawurldecode($_POST['vehicle_deposit'][$key])) {
                            if ($NotesStr != "") {
                                $NotesStr .= " | ";
                            }

                            $NotesStr .= "Deposit amount changed $" . $vehicleValue->deposit . " to $" . number_format((float) rawurldecode($_POST['vehicle_deposit'][$key]), 2, '.', '');
                        }
                        if ($NotesStr != "") {

                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $_SESSION['member_id'],
                                "type" => 3,
                                "text" => $NotesStr);

                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);
                        }

                        $insert_arr = array(
                            'tariff' => $_POST['vehicle_tariff'][$key],
                            'deposit' => $_POST['vehicle_deposit'][$key],
                            'carrier_pay' => $_POST['vehicle_tariff'][$key] - $_POST['vehicle_deposit'][$key],
                        );

                        $vehicleValue->update($insert_arr);
                    }
                }

                $this->daffny->DB->transaction("commit");
                if (post_var('send_email') == '1') {
                    $entity->sendOrderConfirmation();
                }
                $_POST = array();
                $entity->getVehicles(true);

                $this->setFlashInfo("Lead Updated");
                $this->daffny->DB->query("CALL Set_Batch_ReferredBy_EntityID('" . $entity->id . "')");
                $entity->updateHeaderTable();
                redirect(getLink("leads", "showcreated", "id", (int) $_GET['id']));
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
                    $this->setFlashError($this->err);
                }
            }

            if (count($_POST) == 0) {

                /* Load Shipper Data */
                $shipper = $entity->getShipper();
                if (!is_array($this->input)) {
                    $this->input = array();
                }

                $this->input['shipper_fname'] = $shipper->fname;
                $this->input['shipper_lname'] = $shipper->lname;
                $this->input['shipper_company'] = $shipper->company;
                $this->input['shipper_email'] = $shipper->email;
                $this->input['shipper_phone1'] = formatPhone($shipper->phone1);
                $this->input['shipper_phone2'] = formatPhone($shipper->phone2);
                $this->input['shipper_mobile'] = formatPhone($shipper->mobile);
                $this->input['shipper_fax'] = $shipper->fax;
                $this->input['shipper_address1'] = $shipper->address1;
                $this->input['shipper_address2'] = $shipper->address2;
                $this->input['shipper_city'] = $shipper->city;
                $this->input['shipper_state'] = $shipper->state;
                $this->input['shipper_state2'] = $shipper->state;
                $this->input['shipper_zip'] = $shipper->zip;
                $this->input['shipper_country'] = $shipper->country;
                $this->input['shipper_type'] = $shipper->shipper_type;
                $this->input['shipper_hours'] = $shipper->shipper_hours;

				/* Load Origin Data */
                $origin = $entity->getOrigin();
                $this->input['origin_address1'] = $origin->address1;
                $this->input['origin_address2'] = $origin->address2;
                $this->input['origin_city'] = $origin->city;
                $this->input['origin_state'] = $origin->state;
                $this->input['origin_state2'] = $origin->state;
                $this->input['origin_zip'] = $origin->zip;
                $this->input['origin_country'] = $origin->country;
                $this->input['origin_contact_name'] = $origin->name;
                $this->input['origin_auction_name'] = $origin->auction_name;
                $this->input['origin_company_name'] = $origin->company;
                $this->input['origin_phone1'] = formatPhone($origin->phone1);
                $this->input['origin_phone2'] = formatPhone($origin->phone2);
                $this->input['origin_phone3'] = formatPhone($origin->phone3);
                $this->input['origin_mobile'] = formatPhone($origin->phone_cell);
                $this->input['origin_buyer_number'] = $entity->buyer_number;
                $this->input['origin_booking_number'] = $entity->booking_number;
                $this->input['origin_contact_name2'] = $origin->name2;
                $this->input['origin_fax'] = $origin->fax;
                $this->input['origin_type'] = $origin->location_type;
                $this->input['origin_hours'] = $origin->hours;

                /* Load Destination Data */
                $destination = $entity->getDestination();
                $this->input['destination_address1'] = $destination->address1;
                $this->input['destination_address2'] = $destination->address2;
                $this->input['destination_city'] = $destination->city;
                $this->input['destination_state'] = $destination->state;
                $this->input['destination_state2'] = $destination->state;
                $this->input['destination_zip'] = $destination->zip;
                $this->input['destination_country'] = $destination->country;
                $this->input['destination_contact_name'] = $destination->name;
                $this->input['destination_company_name'] = $destination->company;
                $this->input['destination_phone1'] = formatPhone($destination->phone1);
                $this->input['destination_phone2'] = formatPhone($destination->phone2);
                $this->input['destination_phone3'] = formatPhone($destination->phone3);
                $this->input['destination_mobile'] = formatPhone($destination->phone_cell);
                $this->input['destination_contact_name2'] = $destination->name2;
                $this->input['destination_auction_name'] = $destination->auction_name;
                $this->input['destination_booking_number'] = $destination->booking_number;
                $this->input['destination_buyer_number'] = $destination->buyer_number;
                $this->input['destination_fax'] = $destination->fax;
                $this->input['destination_type'] = $destination->location_type;
                $this->input['destination_hours'] = $destination->hours;

                /* Load Shipping Information */
                $this->input['avail_pickup_date'] = (strtotime($entity->avail_pickup_date) != 0) ? $entity->getFirstAvail("m/d/Y") : "";
                $this->input['load_date'] = (strtotime($entity->load_date) != 0) ? $entity->getLoadDate("m/d/Y") : "";
                $this->input['load_date_type'] = $entity->load_date_type;
                $this->input['delivery_date'] = (strtotime($entity->delivery_date) != 0) ? $entity->getDeliveryDate("m/d/Y") : "";
                $this->input['delivery_date_type'] = $entity->delivery_date_type;
                $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
                $this->input['shipping_ship_via'] = $entity->ship_via;
                $this->input['total_tariff'] = $entity->getTotalTariff();
                $this->input['total_deposit'] = $entity->getTotalDeposit();
                $this->input['referred_by'] = $entity->referred_id;
                $this->input['notes_for_shipper'] = $entity->information;
                $this->input['include_shipper_comment'] = $entity->include_shipper_comment;
                $this->input['balance_paid_by'] = $entity->balance_paid_by;
                $this->input['customer_balance_paid_by'] = $entity->customer_balance_paid_by;
                $this->input['pickup_terminal_fee'] = $entity->pickup_terminal_fee;
                $this->input['delivery_terminal_fee'] = $entity->dropoff_terminal_fee;
                $this->input['payments_terms'] = $entity->payments_terms;
                $this->input['est_ship_date'] = $entity->est_ship_date;

                /* Load Shipper Note */
                $notes = $entity->getNotes();
                if (isset($notes[Note::TYPE_FROM][0])) {
                    $this->input['notes_from_shipper'] = $notes[Note::TYPE_FROM][0]->text;
                } else {
                    $this->input['notes_from_shipper'] = "";
                }

            }
            $this->input['total_tariff'] = $entity->getTotalTariff();
            $this->input['total_deposit'] = $entity->getTotalDeposit();
            $this->input['carrier_pay'] = $entity->getCarrierPay();

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->getEditFormImported($settings->referrer_status, $entity->status);

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink('orders'));
        } catch (UserException $e) {
            $this->daffny->DB->transaction("rollback");
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            $this->setFlashError($e->getMessage());
            redirect(getLink('orders'));
        }
    }

    public function listcreatedquote($status)
    {
        $this->tplname = "leads.main";
        $this->daffny->tpl->status = $status;
        $data_tpl = 'leads.leads_created_quotes';
        try {
            $this->daffny->DB->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");
            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipper';
            $this->order->Fields[] = 'origin';
            $this->order->Fields[] = 'destination';
            $this->order->Fields[] = 'avail';
            $this->order->Fields[] = 'last_activity_date';
            $this->order->Fields[] = 'assigned_date';
            $this->order->setDefault('created', 'desc');

            switch ($status) {
                case Entity::STATUS_CACTIVE:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
                case Entity::STATUS_CONHOLD:
                    $this->title = "Leads On Hold";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Hold"));
                    break;
                case Entity::STATUS_CUNREADABLE:
                    $this->title = "Unreadable Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", 'Unreadable'));
                    break;
                case Entity::STATUS_CARCHIVED:
                    $this->title = "Archived Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Archived"));
                    break;
                case Entity::STATUS_CPRIORITY:
                    $this->title = "Priority Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Priority"));
                    break;
                case Entity::STATUS_CDEAD:
                    $this->title = "Dead Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => "Dead"));
                    break;
                default:
                    $this->title = "Leads";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads"));
                    break;
            }

            $info = "Lead Listing:" . $this->title;
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $entityManager = new EntityManager($this->daffny->DB);

            switch ($this->order->CurrentOrder) {
                case 'created':
                    $this->order->setTableIndex('e');
                case 'assigned':
                    $this->order->setTableIndex('e');
                    break;
            }

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_CLEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);
            $entities_count = $entityManager->getCountHeaderLead(Entity::TYPE_CLEAD);
            $this->input['active_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_CONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_CARCHIVED];
            $this->input['unreadable_count'] = $entities_count[Entity::STATUS_CUNREADABLE];
            $this->input['created_count'] = $entities_count[Entity::STATUS_CACTIVE];
            $this->input['priority_count'] = $entities_count[Entity::STATUS_CPRIORITY];
            $this->input['dead_count'] = $entities_count[Entity::STATUS_CDEAD];
            $this->input['cquoted_count'] = $entities_count[Entity::STATUS_CQUOTED];
            $this->input['cfollow_count'] = $entities_count[Entity::STATUS_CFOLLOWUP];
            $this->input['cexpired_count'] = $entities_count[Entity::STATUS_CEXPIRED];
            $this->input['cduplicate_count'] = $entities_count[Entity::STATUS_CDUPLICATE];
            $this->input['cappointment_count'] = $entities_count[Entity::STATUS_CAPPOINMENT];
            $this->input['assigned_count'] = $entities_count[Entity::STATUS_CASSIGNED];

            $entitiesCoverted_count = $entityManager->getCountConverted(Entity::TYPE_CLEAD);
            $this->input['converted_count'] = $entitiesCoverted_count[Entity::STATUS_CONVERTED];

            $this->pager = $entityManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');

            $AM_PM = 'AM';
            $TimeArr = array();
            for ($i = 0.15, $j = 0.15, $m = 0.15; $i <= 24; $i += 0.15, $j += 0.15, $m += 0.15) {
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

            $this->form->TextField("app_date", 10, array('style' => 'width: 100px;', 'tabindex' => 58), 'Appointment Date', "</td><td>");
            $this->form->ComboBox("app_time", array("" => "Time") + $TimeArr, array('style' => 'width:100px;'), 'Appointment Time', "</td><td>");
            $this->form->TextArea("app_note", 15, 10, array("style" => "height:77px; width:230px;"), "Notes", "</td ><td>");
            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_cc_new", 255, array("style" => "width:280px;"), "CC", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->DB->query("COMMIT;");

        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

    public function searchorders()
    {
        try {
            $this->daffny->tpl->status = Entity::STATUS_ARCHIVED;
            $this->tplname = "leads.main";

            $this->title = "Leads search result";
            if ($_GET['etype'] == 4) {

                if ($_GET['mtype'] == Entity::STATUS_CQUOTED ||
                    $_GET['mtype'] == Entity::STATUS_CFOLLOWUP ||
                    $_GET['mtype'] == Entity::STATUS_CEXPIRED ||
                    $_GET['mtype'] == Entity::STATUS_CAPPOINMENT
                ) {
                    $data_tpl = 'leads.leads_created_quotes';
                } else {
                    $data_tpl = 'leads.leads_created';
                }

                $this->applyOrder("app_order_header e");
                $this->order->Fields[] = 'id';
                $this->order->Fields[] = 'entityid';
                $this->order->Fields[] = 'shipperfname';
                $this->order->Fields[] = 'Origincity';
                $this->order->Fields[] = 'Destinationcity';
                $this->order->Fields[] = 'avail_pickup_date';
                $this->order->Fields[] = 'last_activity_date';
                $this->order->Fields[] = 'assigned_date';
                $this->order->Fields[] = 'quoted';

                $this->order->setDefault('assigned_date', 'desc');
            } else {
                $data_tpl = "leads.leads";

                $this->applyOrder("app_order_header e");
                $this->order->Fields[] = 'id';
                $this->order->Fields[] = 'entityid';
                $this->order->Fields[] = 'last_activity_date';
                $this->order->Fields[] = 'assigned_date';
                $this->order->Fields[] = 'received';
                $this->order->Fields[] = 'shipperfname';
                $this->order->Fields[] = 'Origincity';
                $this->order->Fields[] = 'Destinationcity';
                $this->order->Fields[] = 'avail_pickup_date';
                $this->order->Fields[] = 'est_ship_date';
                $this->order->Fields[] = 'created';

                $this->order->setDefault('created', 'desc');
            }

            $info = "Search Lead";
            $applog = new Applog($this->daffny->DB);
            $applog->createInformation($info);

            $search_type = array();
            $search_type[] = $_GET['type1'];
            $search_type[] = $_GET['type2'];
            $search_type[] = $_GET['type3'];
            $search_type[] = $_GET['type4'];
            $search_type[] = $_GET['type5'];
            $search_type[] = $_GET['type6'];
            $search_type[] = $_GET['type7'];
            $search_type[] = $_GET['type8'];
            $search_type[] = $_GET['type9'];
            if (isset($_GET['mtype']) && trim($_GET['mtype']) != '' && ctype_digit((string) $_GET['mtype']) && !isset($_GET['tab'])) {
                $mtype_string = " AND e.status=" . $_GET['mtype'];
                $status = $_GET['mtype'];
            } else {
            }
            $this->daffny->tpl->status = $status;

            $entityManager = new EntityManager($this->daffny->DB);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("leads") => "Leads", '' => 'Search'));
            $leadType = Entity::TYPE_CLEAD;
            if ($_GET['etype'] == 1 || ($_GET['lead_search_type'] == Entity::TYPE_LEAD && !$_GET['etype'])) {
                $leadType = Entity::TYPE_LEAD;
            } elseif ($_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype'])) {
                $leadType = Entity::TYPE_CLEAD;
            } elseif ($_GET['etype'] == 2 || ($_GET['lead_search_type'] == Entity::TYPE_QUOTES && !$_GET['etype'])) {
                $leadType = Entity::TYPE_QUOTE;
            }

            $this->daffny->tpl->entities = $entityManager->getEntitiesArrSearch_leads($leadType, $search_type, $_GET['search_string'], $mtype_string, $_SESSION['per_page'], $this->order->getOrder(), $op);

            $entities_count = $entityManager->getEntitiesArrSearchCount($leadType, $search_type, $_GET['search_string'], '', $_SESSION['per_page'], $this->order->getOrder());
            $this->input['imported_lead_count'] = $entities_count[51];
            $this->input['created_lead_count'] = $entities_count[52];
            $this->input['order_count'] = $entities_count[53];
            if ($_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype'])) {
                $this->input['active_count'] = $entities_count[Entity::STATUS_CACTIVE];
                $this->input['onhold_count'] = $entities_count[Entity::STATUS_CONHOLD];
                $this->input['archived_count'] = $entities_count[Entity::STATUS_CARCHIVED];
                $this->input['unreadable_count'] = $entities_count[Entity::STATUS_CUNREADABLE];
                $this->input['created_count'] = $entities_count[Entity::STATUS_CACTIVE];
                $this->input['priority_count'] = $entities_count[Entity::STATUS_CPRIORITY];
                $this->input['dead_count'] = $entities_count[Entity::STATUS_CDEAD];
                $this->input['cquoted_count'] = $entities_count[Entity::STATUS_CQUOTED];
                $this->input['cfollow_count'] = $entities_count[Entity::STATUS_CFOLLOWUP];
                $this->input['cexpired_count'] = $entities_count[Entity::STATUS_CEXPIRED];
                $this->input['cduplicate_count'] = $entities_count[Entity::STATUS_CDUPLICATE];
                $this->input['cappointment_count'] = $entities_count[Entity::STATUS_CAPPOINMENT];
                $this->input['assigned_count'] = $entities_count[Entity::STATUS_CASSIGNED];
                $this->input['quotes_count'] = $entities_count[54];

            } elseif ($_GET['etype'] == 2 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype'])) {
                $this->input['active_count'] = $entities_count[Entity::STATUS_CACTIVE];
                $this->input['onhold_count'] = $entities_count[Entity::STATUS_CONHOLD];
                $this->input['archived_count'] = $entities_count[Entity::STATUS_CARCHIVED];
                $this->input['unreadable_count'] = $entities_count[Entity::STATUS_CUNREADABLE];
                $this->input['created_count'] = $entities_count[Entity::STATUS_CACTIVE];
                $this->input['priority_count'] = $entities_count[Entity::STATUS_CPRIORITY];
                $this->input['dead_count'] = $entities_count[Entity::STATUS_CDEAD];
                $this->input['cquoted_count'] = $entities_count[Entity::STATUS_CQUOTED];
                $this->input['cfollow_count'] = $entities_count[Entity::STATUS_CFOLLOWUP];
                $this->input['cexpired_count'] = $entities_count[Entity::STATUS_CEXPIRED];
                $this->input['cduplicate_count'] = $entities_count[Entity::STATUS_CDUPLICATE];
                $this->input['cappointment_count'] = $entities_count[Entity::STATUS_CAPPOINMENT];
                $this->input['assigned_count'] = $entities_count[Entity::STATUS_CASSIGNED];
                $this->input['quotes_count'] = $entities_count[54];

            } else {
                $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
                $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
                $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
                $this->input['unreadable_count'] = $entities_count[Entity::STATUS_UNREADABLE];
                $this->input['assigned_count'] = $entities_count[Entity::STATUS_ASSIGNED];
                $this->input['quoted_count'] = $entities_count[Entity::STATUS_LQUOTED];
                $this->input['follow_count'] = $entities_count[Entity::STATUS_LFOLLOWUP];
                $this->input['expired_count'] = $entities_count[Entity::STATUS_LEXPIRED];
                $this->input['duplicate_count'] = $entities_count[Entity::STATUS_LDUPLICATE];
                $this->input['appointment_count'] = $entities_count[Entity::STATUS_LAPPOINMENT];
                $this->input['quotes_count'] = $entities_count[54];
            }
            $this->pager = $entityManager->getPager();
            $this->input['search_count'] = $this->pager->RecordsTotal;
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
            $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
            $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
        } catch (FDException $e) {
            $applog = new Applog($this->daffny->DB);
            $applog->createException($e);
            redirect(getLink("leads"));
        }
    }

}
