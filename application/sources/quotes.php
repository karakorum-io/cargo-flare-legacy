<?php

require_once DAFFNY_PATH . "libs/upload.php";

class ApplicationQuotes extends ApplicationAction
{
    public $section = "Quotes";

    public function construct()
    {
        $this->out .= $this->daffny->tpl->build('quotes.common');

        $this->daffny->tpl->form_templates = $this->form->ComboBox('form_templates', array('' => 'Select One') + $this->getFormTemplates("quotes"), array('style' => 'width:130px;',  'onChange' => 'printSelectedQuoteForm()'), "", "", true);
        $this->daffny->tpl->email_templates = $this->form->ComboBox('email_templates', array('' => 'Select One') + $this->getEmailTemplates("quotes"), array('style' => 'width:130px;', 'onChange' => 'emailSelectedQuoteFormNew()'), "", "", true);

        $em = new EntityManager($this->daffny->DB);
        $ec = $em->getCountHeaderLead(Entity::TYPE_CLEAD);
        $this->input['cexpired_count'] = $ec[Entity::STATUS_LEXPIRED];
        $this->input['cduplicate_count'] = $ec[Entity::STATUS_LDUPLICATE];
        $this->input['unreadables'] = $ec[Entity::STATUS_UNREADABLE];
        parent::construct();
    }

    public function idx()
    {
        try {
            $this->form->ComboBox('followup_type', FollowUp::getTypes(), array('style' => 'width:120px;'), '', '');
            $this->form->TextField('followup_when', 10, array('style' => 'width: 120px;', '', ''));
            $this->loadQuotesPage(Entity::STATUS_ACTIVE);
        } catch (FDException $e) {
            redirect(getLink(''));
        }
    }

    public function onhold()
    {
        try {
            $this->loadQuotesPage(Entity::STATUS_ONHOLD);
        } catch (FDException $e) {
            redirect(getLink('quotes'));
        }
    }

    public function archived()
    {
        try {
            $this->loadQuotesPage(Entity::STATUS_ARCHIVED);
        } catch (FDException $e) {
            redirect(getLink('quotes'));
        }
    }

    public function unarchived()
    {
        try {

            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);
            $update_arr = array(
                'type' => Entity::TYPE_QUOTE,
                'status' => Entity::STATUS_ACTIVE,
            );

            $entity->update($update_arr);

            $this->loadQuotesPage(Entity::STATUS_ARCHIVED);

        } catch (FDException $e) {
            redirect(getLink('quotes/archived'));
        }
    }

    public function followup()
    {
        try {
            $this->form->ComboBox('followup_type', FollowUp::getTypes(), array('style' => 'width:120px;'), '', '');
            $this->form->TextField('followup_when', 10, array('style' => 'width: 120px;', '', ''));
            $this->loadQuotesPage(Entity::STATUS_LFOLLOWUP);
        } catch (FDException $e) {
            redirect(getLink(''));
        }
        // try {
        //     $this->initGlobals();
        //     $this->tplname = "quotes.main";
        //     $this->title = "Quotes";
        //     $data_tpl = "quotes.quotes";
        //     $this->daffny->tpl->status = Entity::STATUS_ARCHIVED;
        //     $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Follow-up Today"));
        //     $this->applyOrder(Entity::TABLE);
        //     $this->order->setDefault('id', 'desc');
        //     $entityManager = new EntityManager($this->daffny->DB);
        //     $this->daffny->tpl->entities = $entityManager->getFollowupQuotes($this->order->getOrder(), $_SESSION['per_page']);
        //     $entities_count = $entityManager->getCountHeader_v2(Entity::TYPE_QUOTE);
        //     $this->input['expired_count'] = $entities_count[23];
        //     $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
        //     $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
        //     $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
        //     $this->input['followup_count'] = $entities_count[0];
        //     $this->daffny->tpl->followup_count = $entities_count[0];
        //     $this->pager = $entityManager->getPager();
        //     $tpl_arr = array(
        //         'navigation' => $this->pager->getNavigation(),
        //         'current_page' => $this->pager->CurrentPage,
        //         'pages_total' => $this->pager->PagesTotal,
        //         'records_total' => $this->pager->RecordsTotal,
        //     );
        //     $pager_html = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        //     $this->input['content'] = $this->daffny->tpl->build($data_tpl, array("pager" => $pager_html));
        //     $this->input['task_minibox'] = $this->daffny->tpl->build('task_minibox');
        // } catch (FDException $e) {
        //     redirect(getLink('quotes'));
        // }
    }

    public function expired()
    {
        try {
            $this->loadQuotesPage(Entity::STATUS_LEXPIRED);
        } catch (FDException $e) {
            redirect(getLink('quotes'));
        }
    }

    public function duplicates()
    {
        try {
            $this->loadQuotesPage(Entity::STATUS_LDUPLICATE);
        } catch (FDException $e) {
            redirect(getLink('quotes'));
        }
    }

    public function unreadables()
    {
        try {
            $this->loadQuotesPage(Entity::STATUS_UNREADABLE);
        } catch (FDException $e) {
            redirect(getLink('quotes'));
        }
    }

    private function loadQuotesPage($status)
    {
        try{
            $this->tplname = "quotes.main";
            $this->daffny->tpl->status = $status;
            $data_tpl = "quotes.quotesnew";

            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipperfname';
            $this->order->Fields[] = 'Origincity';
            $this->order->Fields[] = 'Destinationcity';
            $this->order->Fields[] = 'est_ship_date';
            $this->order->Fields[] = 'entityid';
            $this->order->Fields[] = 'quoted';
            //$this->order->setDefault('id', 'desc');

            $this->order->setDefault('id', 'desc');

            $this->daffny->DB->query("SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;");

            switch ($status) {
                case Entity::STATUS_ACTIVE:
                    $this->title = "Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes"));
                    $this->daffny->tpl->status = Entity::STATUS_ACTIVE;
                    break;
                case Entity::STATUS_ONHOLD:
                    $this->title = "Quotes On Hold";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Hold"));
                    $this->daffny->tpl->status = Entity::STATUS_ONHOLD;
                    break;
                case Entity::STATUS_ARCHIVED:
                    $this->title = "Archived Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Archived"));
                    $this->daffny->tpl->status = Entity::STATUS_ARCHIVED;
                    break;
                case Entity::STATUS_LEXPIRED:
                    $this->title = "Expired Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Expired"));
                    $this->daffny->tpl->status = Entity::STATUS_LEXPIRED;
                    break;
                case Entity::STATUS_LDUPLICATE:
                    $this->title = "Duplicate Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Duplicates"));
                    $this->daffny->tpl->status = Entity::STATUS_LDUPLICATE;
                    break;
                case Entity::STATUS_UNREADABLE:
                    $this->title = "Unreadable Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => "Unreadables"));
                    $this->daffny->tpl->status = Entity::STATUS_UNREADABLE;
                    break;
                default:
                    $this->title = "Quotes";
                    $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes"));
                    $this->daffny->tpl->status = Entity::STATUS_ACTIVE;
                    break;
            }

            $this->applyOrder("app_order_header e");
            $this->order->Fields[] = 'shipper';
            $this->order->Fields[] = 'origin';
            $this->order->Fields[] = 'destination';
            $this->order->Fields[] = 'avail';
            $this->order->Fields[] = 'tariff';
            $this->order->setDefault('id', 'desc');
            $entityManager = new EntityManager($this->daffny->DB);

            if($status == Entity::STATUS_LEXPIRED){
                $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew_v2(Entity::TYPE_QUOTE, $this->order->getOrder(), $status, $_SESSION['per_page']);
            } else if($status == Entity::STATUS_LDUPLICATE){
                $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_LEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);
            } else if($status == Entity::STATUS_UNREADABLE){
                $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew(Entity::TYPE_LEAD, $this->order->getOrder(), $status, $_SESSION['per_page']);
            } else {
                $this->daffny->tpl->entities = $entityManager->getEntitiesArrDataNew_v2(Entity::TYPE_QUOTE, $this->order->getOrder(), $status, $_SESSION['per_page']);
            }
            
            $entities_count = $entityManager->getCountHeader_v2(Entity::TYPE_QUOTE);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['expired_count'] = $entities_count[23];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['followup_count'] = $entities_count[22];
            
            $this->daffny->tpl->followup_count = $entities_count[22];
            $this->daffny->tpl->expired_count = $entities_count[23];

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

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->DB->query("COMMIT;");
        } catch(Exception $e) {
            print_r($e);
            die("Something went wrong!");
        }
    }

    public function show()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $this->tplname = "quotes.detail";
            $this->title = "Quote Details";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            //$notes = $entity->getNotes(false, $this->order->getOrder());
            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", 'Quote #' . $entity->getNumber()));

        } catch (FDException $e) {
            redirect(getLink("quotes"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink("quotes"));
        }
    }

    public function mail_history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $this->tplname = "quotes.mail_history";
            $this->title = "Quote Details";
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);

            /* Documents */
            $this->daffny->tpl->files = $this->getFiles((int) $_GET['id']);
            $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->form->TextField("mail_to_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
            $this->form->TextField("mail_subject_new", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
            $this->form->TextArea("mail_body_new", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");

            $this->daffny->tpl->entity = $entity;
            $this->applyOrder(Note::TABLE);
            $this->order->setDefault('id', 'asc');
            //$notes = $entity->getNotes(false, $this->order->getOrder());
            $notes = $entity->getNotes(false, " order by id desc ");
            $this->daffny->tpl->notes = $notes;

            $sql = "SELECT * FROM entity_email_log WHERE entity_id = '" . $_GET['id'] . "' ORDER BY created_at DESC";
            $history = $this->daffny->DB->selectRows($sql);
            $this->daffny->tpl->history = $history;
            
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", 'Quote #' . $entity->getNumber()));

        } catch (FDException $e) {
            redirect(getLink("quotes"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink("quotes"));
        }
    }

    public function history()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $this->tplname = "quotes.history";
            $this->title = "Quote History";
            $this->applyOrder(History::TABLE);
            $entity = new Entity($this->daffny->DB);
            $entity->load($_GET['id']);
            $this->daffny->tpl->entity = $entity;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", getLink("quotes/show/id/" . $_GET['id']) => "Quote #" . $entity->getNumber(), '' => "History"));
            $historyManager = new HistoryManager($this->daffny->DB);
            $this->daffny->tpl->history = $historyManager->getHistory($this->order->getOrder(), $_SESSION['per_page'], " `entity_id` = " . (int) $_GET['id']);
            $this->pager = $historyManager->getPager();
            $tpl_arr = array(
                'navigation' => $this->pager->getNavigation()
                , 'current_page' => $this->pager->CurrentPage
                , 'pages_total' => $this->pager->PagesTotal
                , 'records_total' => $this->pager->RecordsTotal,
            );
            $this->input['pager'] = $this->daffny->tpl->build('grid_pager', $tpl_arr);
        } catch (FDException $e) {
            redirect(getLink("quotes"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink("quotes"));
        }
    }

    public function edit()
    {
        try {
            if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
                throw new UserException("Invalid Quote ID");
            }

            $this->tplname = "quotes.create";
            $entity = new Entity($this->daffny->DB);
            $entity->load((int) $_GET['id']);
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('quotes') => "Quotes", getLink('quotes/show/id/' . $_GET['id']) => "Quote #" . $entity->getNumber(), '' => "Edit"));
            if (isset($_GET['convert'])) {
                $this->title = "Convert Quote #" . $entity->getNumber() . ' to Order';
            } else {
                $this->title = "Edit Quote #" . $entity->getNumber();
            }
            $this->input['title'] = $this->title;
            if ($entity->readonly) {
                throw new UserException("Access Denied");
            }

            $this->daffny->tpl->entity = $entity;
            $this->daffny->tpl->vehicles = $entity->getVehicles();
            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm()) {
                $this->saveQuote($sql_arr, $entity);
                if (isset($_POST['convert'])) {
                    $entity->convertToOrder();
                    //call storedprocess
                    $entity->updateHeaderTable();
                    redirect(getLink('orders/show/id/' . $entity->id));
                }
                $_POST = array();
                //redirect(getLink("quotes"));
            } else {
                $this->postToInput();
                if (count($this->err)) {
                    $this->setFlashError("<div class='form-errors'><p>" . implode("</p><p>", $this->err) . "</p></div>");
                }
            }
            //if (count($_POST) == 0) {
                $this->fillEditForm($entity);
            //}
            $entity->updateHeaderTable();
            $this->getEditForm();
        } catch (FDException $e) {
            redirect(getLink("quotes"));
        } catch (UserException $e) {
            $this->setFlashError($e->getMessage());
            redirect(getLink("quotes"));
        }
    }

    private function getLeadSourceEmail($leadSourceId) {
        // fetched assigned members
        $res = $this->daffny->DB->query("SELECT member_id FROM app_defaultsettings_ass WHERE leadsource_id = ".$leadSourceId);
        
        $members = [];
        while($r = mysqli_fetch_assoc($res)){
            $members[] = $r['member_id'];
        }

        // comma seperated members
        $members = implode(",", $members);

        $res = $this->daffny->DB->query("SELECT email,contactname FROM members WHERE id IN (".$members.")");
        $emails = [];

        $i = 0;
        while($r = mysqli_fetch_assoc($res)){
            $emails[$i]['mail'] = $r['email'];
            $emails[$i]['name'] = $r['contactname'];

            $i++;
        }

        return $emails;
    }

    public function sendQuoteCreateNotification($mails, $assignedEmail){

        foreach ($mails as $key => $m) {
            try {
                if(strtolower($assignedEmail[0]) == strtolower($m['mail'])){
                    $mail = new FdMailer(true);
                    $mail->isHTML();

                    $mail->Body = "New lead has been assigned to you located at QUEST REQUEST";
                    $mail->Subject = "New Lead";

                    // $mail->AddAddress($m['mail']);
                    $mail->AddAddress('shahrukhusmaani@live.com');
                    $mail->SetFrom('no-reply@transportmasters.net', 'TransportMasters');
                    
                    $mail->Send();

                    echo "<br>Sent for :".$m['mail']."<br>";
                } else {
                    echo "<br>Not sending for : ".$m['mail']."<br>";
                }
            } catch (phpmailerException $e) {
                throw new FDException("Mailer Exception: " . $e->getMessage());
            }
        }
    }

    public function create()
    {
        try {
            $this->initGlobals();
            $this->tplname = "quotes.create";
            $this->title = "Create Quote";
            $this->input['title'] = $this->title;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('quotes') => "Quotes", '' => "Create"));

            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }

            $this->daffny->tpl->referrers_arr = $referrers_arr;

            if (isset($_POST['submit']) && $sql_arr = $this->checkEditForm(true)) {
                $this->createQuote($sql_arr);
            } else {
                if (isset($_POST['submit']) && isset($_POST['tariff'])) {
                    $total_tariff = 0;
                    $total_deposit = 0;
                    foreach ($_POST['tariff'] as $k => $tariff) {
                        $total_tariff += $tariff;
                        $total_deposit += $_POST['deposit'][$k];
                    }
                    $this->input['total_tariff'] = "$ " . number_format($total_tariff, 2);
                    $this->input['total_deposit'] = "$ " . number_format($total_deposit, 2);
                    $this->input['carrier_pay'] = '$ ' . number_format($total_tariff - $total_deposit, 2);
                } else {
                    $this->input['total_tariff'] = "$ 0.00";
                    $this->input['total_deposit'] = "$ 0.00";
                    $this->input['carrier_pay'] = '$ 0.00';
                }

                $this->input['referred_by'] = $_POST['referred_by'];
                
                if (count($this->err)) {
                    $this->setFlashError("<div class='form-errors'><p>" . implode("</p><p>", $this->err) . "</p></div>");
                }
            }
            
            $this->getEditForm();
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            redirect(getLink("quotes"));
        }
    }

    public function createquick()
    {
        try {
            $this->initGlobals();
            $this->tplname = "quotes.createquick";
            $this->title = "Create Quote";
            $this->input['title'] = $this->title;
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink('quotes') => "Quotes", '' => "Create"));

            $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
            $referrers_arr = array();
            foreach ($referrers as $referrer) {
                $referrers_arr[$referrer->id] = $referrer->name;
            }
            //print_r($referrers_arr);
            $this->daffny->tpl->referrers_arr = $referrers_arr;
            if (isset($_POST['submit']) && $sql_arr = $this->checkEditFormQuick(true)) {
                $this->createQuoteQuick($sql_arr);
            } else {
                if (isset($_POST['submit']) && isset($_POST['tariff'])) {
                    $total_tariff = 0;
                    $total_deposit = 0;
                    foreach ($_POST['tariff'] as $k => $tariff) {
                        $total_tariff += $tariff;
                        $total_deposit += $_POST['deposit'][$k];
                    }
                    $this->input['total_tariff'] = "$ " . number_format($total_tariff, 2);
                    $this->input['total_deposit'] = "$ " . number_format($total_deposit, 2);
                    $this->input['carrier_pay'] = '$ ' . number_format($total_tariff - $total_deposit, 2);
                } else {
                    $this->input['total_tariff'] = "$ 0.00";
                    $this->input['total_deposit'] = "$ 0.00";
                    $this->input['carrier_pay'] = '$ 0.00';

                    $this->input['shipping_ship_via'] = 1;
                }
                //$this->postToInput();
                if (count($this->err)) {
                    $this->setFlashError("<div class='form-errors'><p>" . implode("</p><p>", $this->err) . "</p></div>");
                }
            }
            //$entity->updateHeaderTable();
            $this->getEditForm();

        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            redirect(getLink("quotes"));
        }
    }

    public function search()
    {
        try {
            // if (count($_POST) == 0) redirect(getLink("quotes"));
            $this->tplname = "quotes.main";
            $data_tpl = "quotes.quotes";
            $this->title = "Quotes search result";

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
            $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => 'Search'));
            //$this->daffny->tpl->entities = $entityManager->search(Entity::TYPE_QUOTE, $_POST['search_type'], $_POST['search_string'], $_SESSION['per_page']);
            $this->daffny->tpl->entities = $entityManager->searchAll(Entity::TYPE_QUOTE, $search_type, $_GET['search_string'], $_SESSION['per_page']);

            $this->daffny->tpl->status = Entity::STATUS_ARCHIVED;
            $entities_count = $entityManager->getCount(Entity::TYPE_QUOTE);
            $this->input['active_count'] = $entities_count[Entity::STATUS_ACTIVE];
            $this->input['onhold_count'] = $entities_count[Entity::STATUS_ONHOLD];
            $this->input['archived_count'] = $entities_count[Entity::STATUS_ARCHIVED];
            $this->input['followup_count'] = $entities_count[0];
            $this->daffny->tpl->followup_count = $entities_count[0];
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
            redirect(getLink("quotes"));
        }
    }

    protected function getEditForm()
    {
        $member = new Member($this->daffny->DB);
        $member->load($_SESSION['member_id']);
        $this->daffny->tpl->isAutoQuoteAlowed = $member->isAutoQuoteAllowed();
        /* SHIPPER */
        $this->form->TextField("shipper", 32, array(), "Shipper", "</td><td>");

        $this->form->TextField("shipper_fname", 32, array('tabindex' => 1), $this->requiredTxt . "First Name", "</td><td>");
        $this->form->TextField("shipper_lname", 32, array('tabindex' => 2), "Last Name", "</td><td>");
        $this->form->TextField("shipper_company", 64, array('tabindex' => 3, 'class' => 'shipper_company-model'), "Company", "<span class='required' id='shipper_company-span' style='display:none;'>*</span></td><td>");
        $this->form->ComboBox('shipper_type',array('' => 'Select One', 'Residential' => 'Residential ', 'Commercial' => 'Commercial'), array('tabindex' => 4, "elementname" => "select", "class" => "elementname", 'onChange' => 'typeselected();'), $this->requiredTxt . 'Shipper Type', '</td><td>');
        $this->form->TextField("shipper_hours", 200, array('tabindex' => 5), "Hours", "</td><td>");
        $this->form->TextField("shipper_email", 100, array('class' => 'email', 'tabindex' => 6),"Email", "</td><td>");
        $this->form->TextField("shipper_phone1", 32, array('class' => 'phone', 'tabindex' => 7), $this->requiredTxt . "Phone", "</td><td>");
        $this->form->TextField("shipper_phone2", 32, array('class' => 'phone', 'tabindex' => 8), "Phone 2", "</td><td>");
        $this->form->TextField("shipper_mobile", 32, array('class' => 'phone', 'tabindex' => 9), "Mobile", "</td><td>");
        $this->form->TextField("shipper_fax", 32, array('tabindex' => 10), "Fax", "</td><td>");
        
        // if ($this->input['referred_by'] == "") {
        //     $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
        //     $referrers_arr = array('' => 'Select One');
        //     foreach ($referrers as $referrer) {
        //         $referrers_arr[$referrer->id] = $referrer->name;
        //     }
        // } else {

        //     $referrer_name_value = "";
        //     if ($this->input['referred_by'] != "") {
        //         $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $this->input['referred_id'] . "'");
        //         if (!empty($row_referrer)) {
        //             $referrer_name_value = $row_referrer['name'];
        //         }
        //     }
        //     $referrers_arr = array($this->input['referred_by'] => $referrer_name_value);
        // }
        $referrers = Entity::getReferrers($_SESSION['member_id'], $this->daffny->DB);
        $referrers_arr = array('' => 'Select One');
        foreach ($referrers as $referrer) {
            $referrers_arr[$referrer->id] = $referrer->name;
        }

        $this->form->ComboBox("referred_by", array('' => 'Select One') + $referrers_arr, array('tabindex' => 11), $this->requiredTxt . "Source", "</td><td>");
        $this->form->TextField("shipper_address1", 64, array('tabindex' => 12), "Address", "</td><td>");
        $this->form->TextField("shipper_address2", 64, array('tabindex' => 13), "Address 2", "</td><td>");
        $this->form->TextField("shipper_city", 32, array('class' => 'geo-city', 'tabindex' => 14), "City", "</td><td>");
        $this->form->ComboBox('shipper_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:180px;', 'tabindex' => 15), "State/Zip", "</td><td>", true);
        $this->form->TextField("shipper_zip", 8, array('style' => 'width:70px;margin-left:7px;', 'class' => 'zip', 'tabindex' => 16), "", "");
        $this->form->ComboBox_v2("shipper_country", $this->getCountries(), array('tabindex' => 17), "Country", "</td><td>");
        $this->form->CheckBox("shipper_add", array('tabindex' => 17), "Add to saved shippers list", "</td><td>");

        /* ORIGIN */
        $this->form->TextField("origin_city", 32, array('class' => 'geo-city', 'tabindex' => 18), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('origin_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:360px;', 'tabindex' => 19), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("origin_zip", 64, array('style' => 'width:105px;', 'class' => 'zip', 'tabindex' => 20), "", "");
        $this->form->ComboBox("origin_country", $this->getCountries(), array('tabindex' => 21), "Country", "</td><td>");
        /* DESTINATION */
        $this->form->TextField("destination_city", 32, array('class' => 'geo-city', 'tabindex' => 22), $this->requiredTxt . "City", "</td><td>");
        $this->form->ComboBox('destination_state', array('' => 'Select One', 'United States' => $this->getStates(), 'Canada' => $this->getCanadaStates()), array('style' => 'width:360px;', 'tabindex' => 23), $this->requiredTxt . "State/Zip", "</td><td>", true);
        $this->form->TextField("destination_zip", 64, array('style' => 'width:105px;', 'class' => 'zip', 'tabindex' => 24), "", "");
        $this->form->ComboBox("destination_country", $this->getCountries(), array('tabindex' => 25), "Country", "</td><td>");
        /* SHIPPING INFORMATION */
        $this->form->TextField("shipping_est_date", 8, array('class' => 'datepicker', 'tabindex' => 26), "Estimated Ship Date", "</td><td>");
        $this->form->ComboBox("shipping_ship_via", array('' => 'Select One') + Entity::$ship_via_string, array('tabindex' => 27), "Ship Via", "</td><td>");
        $this->form->TextArea("shipping_notes", 4, 10, array('style' => 'height:80px;', 'tabindex' => 28), "Notes from Shipper", "</td><td rowspan=\"3\">");
        /* ADDITIONAL */
        $this->form->TextArea("note_to_shipper", 4, 10, array('style' => 'height: 80px;', 'tabindex' => 29), "Note to Shipper", "</td><td>");

    }

    protected function fillEditForm(Entity $entity)
    {
        /* Load Shipper Data */
        $shipper = $entity->getShipper(true);
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

        $this->input['shipper_type'] = $shipper->shipper_type;
        $this->input['shipper_hours'] = $shipper->shipper_hours;

        /* Load Origin Data */
        $origin = $entity->getOrigin(true);
        $this->input['origin_city'] = $origin->city;
        $this->input['origin_state'] = $origin->state;
        $this->input['origin_zip'] = $origin->zip;
        $this->input['origin_country'] = $origin->country;
        /* Load Destination Data */
        $destination = $entity->getDestination(true);
        $this->input['destination_city'] = $destination->city;
        $this->input['destination_state'] = $destination->state;
        $this->input['destination_zip'] = $destination->zip;
        $this->input['destination_country'] = $destination->country;
        /* Load Shipping Information */
        $this->input['shipping_est_date'] = $entity->getShipDate("m/d/Y");
        $this->input['shipping_vehicles_run'] = $entity->vehicles_run;
        $this->input['shipping_ship_via'] = $entity->ship_via;
        $this->input['total_tariff'] = $entity->getTotalTariff();
        $this->input['total_deposit'] = $entity->getTotalDeposit();
        $this->input['carrier_pay'] = $entity->getCarrierPay();
        $this->input['referred_by'] = $_POST['referred_by'];
        $this->input['referred_id'] = $entity->referred_id;
        /* Load Shipper Note */
        $notes = $entity->getNotes();
        if (isset($notes[Note::TYPE_FROM][0])) {
            $this->input['shipping_notes'] = $notes[Note::TYPE_FROM][0]->text;
        } else {
            $this->input['shipping_notes'] = "";
        }
    }

    protected function checkEditForm($create = false)
    {
        $sql_arr = $_POST;

        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_phone1' => "Shipper Phone",
            'shipper_type' => "Shipper Type",
            'referred_by' => 'Reffered By',
        );

        if($sql_arr['shipper_type'] == 'Commercial'){
            $checkEmpty['shipper_company'] = "Shipper Company";
        }

        if($sql_arr['origin_country'] == 'US' || $sql_arr['origin_country'] == 'CA'){
            $checkEmpty['origin_city'] = "Origin City";
            $checkEmpty['origin_state'] = "Origin State";
            $checkEmpty['origin_zip'] = "Origin Zip";
        }

        if($sql_arr['destination_country'] == 'US' || $sql_arr['destination_country'] == 'CA'){
            $checkEmpty['destination_city'] = "Destination City";
            $checkEmpty['destination_state'] = "Destination State";
            $checkEmpty['destination_zip'] = "Destination Zip";
        }

        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        if (count($this->err)) {
            foreach ($sql_arr as $key => $value) {
                $this->input[$key] = $value;
            }
            return false;
        }
        return $sql_arr;
    }

    protected function checkEditFormQuick($create = false)
    {
        $sql_arr = $_POST;
        $checkEmpty = array(
            'shipper_fname' => "Shipper First Name",
            'shipper_lname' => "Shipper Last Name",
            'shipper_email' => "Shipper Email",
            'shipper_phone1' => "Shipper Phone",
            //'shipper_type' => "Shipper Type",
            'origin_city' => "Origin City",
            //'origin_country' => 'Origin Country',
            //'origin_zip' => 'Origin Zip',
            'destination_city' => "Destination City",
            //'destination_country' => 'Destination Country',
            //'destination_zip' => 'Destination Zip',
            'shipping_est_date' => 'Estimate Ship Date',
        //            'shipping_vehicles_run' => 'Vehicle(s) Run',
            'shipping_ship_via' => 'Ship Via',
        );
        if ($create) {
            if (!isset($_POST['year'])) {
                $this->err[] = "You must add at least one vehicle";
            }

        }

        $checkEmpty['referred_by'] = "Referred By";
        foreach ($checkEmpty as $field => $label) {
            $this->isEmpty($field, $label);
        }

        if (trim(post_var("origin_state")) == "") {
            $this->isEmpty('origin_state', "Origin State");
        }
        if (trim(post_var("destination_state")) == "") {
            $this->isEmpty('destination_state', "Destination State");
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

    protected function saveQuote(array $newData, Entity $entity)
    {
        try {
            $this->daffny->DB->transaction("start");
            /* UPDATE SHIPPER */
            $shipper = $entity->getShipper();
            $update_arr = array(
                'fname' => $newData['shipper_fname'],
                'lname' => $newData['shipper_lname'],
                'email' => $newData['shipper_email'],
                'company' => $newData['shipper_company'],
                'phone1' => str_replace("-", "", $newData['shipper_phone1']),
                'phone2' => str_replace("-", "", $newData['shipper_phone2']),
                'mobile' => str_replace("-", "", $newData['shipper_mobile']),
                'fax' => $newData['shipper_fax'],
                'address1' => $newData['shipper_address1'],
                'address2' => $newData['shipper_address2'],
                'state' => $newData['shipper_state'],
                'zip' => $newData['shipper_zip'],
                'country' => $newData['shipper_country'],
                'shipper_type' => $newData['shipper_type'],
                'shipper_hours' => $newData['shipper_hours'],
            );
            $shipper->update($update_arr);

            /* UPDATE ORIGIN */
            $origin = $entity->getOrigin();
            $update_arr = array(
                'city' => $newData['origin_city'],
                'state' => $newData['origin_state'],
                'zip' => $newData['origin_zip'],
                'country' => $newData['origin_country'],
            );
            $origin->update($update_arr);

            /* UPDATE DESTINATION */
            $destination = $entity->getDestination();
            $update_arr = array(
                'city' => $newData['destination_city'],
                'state' => $newData['destination_state'],
                'zip' => $newData['destination_zip'],
                'country' => $newData['destination_country'],
            );
            $destination->update($update_arr);

            /* UPDATE NOTE */
            $notes = $entity->getNotes();
            if (count($notes[Note::TYPE_FROM]) != 0) {
                $note = $notes[Note::TYPE_FROM][0];
                $update_arr = array(
                    'text' => $newData['shipping_notes'],
                );
                $note->update($update_arr);
            } else {
                $note = new Note($this->daffny->DB);
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'text' => $newData['shipping_notes'],
                    'type' => Note::TYPE_FROM,
                );
                $note->create($insert_arr);
            }
            $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
            if (!is_null($distance)) {
                $distance = RouteHelper::getMiles((float) $distance);
            } else {
                $distance = 'NULL';
            }
            $update_arr = array(
        //                'vehicles_run' => (int)$newData['shipping_vehicles_run'],
                'ship_via' => (int) $newData['shipping_ship_via'],
                'est_ship_date' => date("Y-m-d", strtotime($newData['shipping_est_date'])),
                'referred_by' => $newData['referred_by'],
                'distance' => $distance,
            );
            $entity->update($update_arr);
            //call storedprocess
            $entity->updateHeaderTable();
            $this->daffny->DB->transaction("commit");
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            throw $e;
        }
    }

    protected function createQuote(array $quoteData)
    {
        try {
            $this->daffny->DB->transaction('start');

            $referrer_name_value = "";
            $salesrep = "";
            if ($quoteData['referred_by'] != "") {
                $row_referrer = $this->daffny->DB->select_one("name,salesrep", "app_referrers", "WHERE  id = '" . $quoteData['referred_by'] . "'");
                if (!empty($row_referrer)) {
                    $referrer_name_value = $row_referrer['name'];
                    $salesrep = $row_referrer['salesrep'];

                }
            }

            /* Create Quote */
            $insert_arr = array(
                'type' => Entity::TYPE_QUOTE,
                'quoted' => date("Y-m-d H:i:s"),
                'received' => date("Y-m-d H:i:s"),
                //'source_id' => $quoteData['referred_by'],
                'creator_id' => $_SESSION['member_id'],
                'assigned_id' => $_SESSION['member_id'],
                'parentid' => getParentId(),
                'salesrepid' => $salesrep,
                'est_ship_date' => date("Y-m-d", strtotime($quoteData['shipping_est_date'])),
                'status' => Entity::STATUS_ACTIVE,
                'vehicles_run' => $quoteData['shipping_vehicles_run'],
                'ship_via' => $quoteData['shipping_ship_via'],
                'information' => $quoteData['note_to_shipper'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $quoteData['referred_by'],
            );

            $entity = new Entity($this->daffny->DB);
            $entity->create($insert_arr);

            /* Create Shipper */
            $shipper = new Shipper($this->daffny->DB);
            $insert_arr = array(
                'fname' => $quoteData['shipper_fname'],
                'lname' => $quoteData['shipper_lname'],
                'email' => $quoteData['shipper_email'],
                'company' => $quoteData['shipper_company'],
                'phone1' => str_replace("-", "", $quoteData['shipper_phone1']),
                'phone2' => str_replace("-", "", $quoteData['shipper_phone2']),
                'mobile' => str_replace("-", "", $quoteData['shipper_mobile']),
                'fax' => $quoteData['shipper_fax'],
                'address1' => $quoteData['shipper_address1'],
                'address2' => $quoteData['shipper_address2'],
                'city' => $quoteData['shipper_city'],
                'state' => $quoteData['shipper_state'],
                'zip' => $quoteData['shipper_zip'],
                'country' => $quoteData['shipper_country'],
                'shipper_type' => $quoteData['shipper_type'],
                'shipper_hours' => $quoteData['shipper_hours'],
            );
            $shipper->create($insert_arr, $entity->id);
            /* Create Origin */
            $origin = new Origin($this->daffny->DB);
            $insert_arr = array(
                'city' => $quoteData['origin_city'],
                'state' => $quoteData['origin_state'],
                'zip' => $quoteData['origin_zip'],
                'country' => $quoteData['origin_country'],
            );
            $origin->create($insert_arr, $entity->id);
            /* Create Destination */
            $destination = new Destination($this->daffny->DB);
            $insert_arr = array(
                'city' => $quoteData['destination_city'],
                'state' => $quoteData['destination_state'],
                'zip' => $quoteData['destination_zip'],
                'country' => $quoteData['destination_country'],
            );
            $destination->create($insert_arr, $entity->id);
            /* Update Quote */
            try {
                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
            } catch (FDException $e) {
                $distance = 'NULL';
            }
            $update_arr = array(
                'shipper_id' => $shipper->id,
                'origin_id' => $origin->id,
                'destination_id' => $destination->id,
                'distance' => $distance,
            );
            $entity->update($update_arr);
            /* Create Vehicles */
            foreach ($quoteData['year'] as $i => $year) {
                $vehicle = new Vehicle($this->daffny->DB);
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'year' => $quoteData['year'][$i],
                    'make' => $quoteData['make'][$i],
                    'model' => $quoteData['model'][$i],
                    'type' => $quoteData['type'][$i],
                    'lot' => $quoteData['lot'][$i],
                    'vin' => $quoteData['vin'][$i],
                    'plate' => $quoteData['plate'][$i],
                    'state' => $quoteData['state'][$i],
                    'color' => $quoteData['color'][$i],
                    'inop' => $quoteData['inop'][$i],
                    'tariff' => $quoteData['tariff'][$i],
                    'deposit' => $quoteData['deposit'][$i],
                    'carrier_pay' => $quoteData['tariff'][$i] - $quoteData['deposit'][$i],
                );
                $vehicle->create($insert_arr);
            }

            if (trim($quoteData['note_to_shipper']) != "") {
                $note = new Note($this->daffny->DB);
                $note->create(array('entity_id' => $entity->id, 'text' => $quoteData['note_to_shipper'], 'sender_id' => $_SESSION['member']['id'], 'type' => Note::TYPE_INTERNAL));
            }

            /* Save Shipper in Accounts */
            
            if ($quoteData['shipper_company']) {
                $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_company']) . "' AND state='" . $quoteData['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_lname']) . "' AND `is_shipper` = 1)");
            } else {
                $rowShipper = $this->daffny->DB->selectRow("id", "app_accounts", "WHERE (`company_name` ='' AND state='" . $quoteData['shipper_state'] . "' AND city='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_city']) . "' AND first_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_fname']) . "' AND last_name='" . mysqli_real_escape_string($this->daffny->DB->connection_id, $quoteData['shipper_lname']) . "' AND `is_shipper` = 1)");
            }

            $account = new Account($this->daffny->DB);
            $accountArray = array(
                'owner_id' => getParentId(),
                'company_name' => $quoteData['shipper_company'],
                'status' => Account::STATUS_ACTIVE,
                'is_carrier' => 0,
                'is_shipper' => 1,
                'is_location' => 0,
                'first_name' => $quoteData['shipper_fname'],
                'last_name' => $quoteData['shipper_lname'],
                'email' => $quoteData['shipper_email'],
                'phone1' => str_replace("-", "", $quoteData['shipper_phone1']),
                'phone2' => str_replace("-", "", $quoteData['shipper_phone2']),
                'cell' => str_replace("-", "", $quoteData['shipper_mobile']),
                'fax' => $quoteData['shipper_fax'],
                'address1' => $quoteData['shipper_address1'],
                'address2' => $quoteData['shipper_address2'],
                'city' => $quoteData['shipper_city'],
                'state' => $quoteData['shipper_state'],
                'state_other' => $quoteData['shipper_state'],
                'zip_code' => $quoteData['shipper_zip'],
                'country' => $quoteData['shipper_country'],
                'shipper_type' => $quoteData['shipper_type'],
                'hours_of_operation' => $quoteData['shipper_hours'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $quoteData['referred_by'],
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

            //Follow up
            $followup = new FollowUp($this->daffny->DB);
            $days = (int) $entity->getAssigned()->getDefaultSettings()->first_quote_followup;
            $followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int) date("m"), (int) date("d") + $days, (int) date("Y"))), $entity->id);

            /* Create Note */
            if (trim($quoteData['shipping_notes']) != "") {
                $note = new Note($this->daffny->DB);
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'type' => Note::TYPE_FROM,
                    'text' => $quoteData['shipping_notes'],
                );
                $note->create($insert_arr);
            }
            
            if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
                try {
                    $entity->sendInitialQuote();
                    $entity->sendOrderConfirmation();
                } catch (Exception $e) {
                    $this->setFlashError("Failed to send Email");
                }
            }

            $entity->getVehicles(true);

            //call stored procedure to update app_order_header
            $entity->updateHeaderTable();
            $this->daffny->DB->transaction("commit");

            $this->setFlashInfo("Quote has been successfully saved");
            redirect(getLink('quotes/show/id', $entity->id));
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            throw $e;
        }
    }

    public function getMemberEmail($memberId) {
        $res = $this->daffny->DB->query("SELECT email FROM members WHERE id = ".$memberId);
        
        $emails = [];
        while($r = mysqli_fetch_assoc($res)){
            $emails[] = $r['email'];
        }
        return $emails;
    }

    protected function createQuoteQuick(array $quoteData)
    {

        try {
            $this->daffny->DB->transaction('start');

            $referrer_name_value = "";
            if ($quoteData['referred_by'] != "") {
                $row_referrer = $this->daffny->DB->select_one("name", "app_referrers", "WHERE  id = '" . $quoteData['referred_by'] . "'");
                if (!empty($row_referrer)) {
                    $referrer_name_value = $row_referrer['name'];

                }
            }

            /* Save Shipper in Accounts */
            if (isset($quoteData['shipper_add']) && ($quoteData['shipper_add'] == 1)) {
                $account = new Account($this->daffny->DB);
                $insert_arr = array(
                    'owner_id' => $_SESSION['member']['parent_id'],
                    'is_shipper' => 1,
                    'company_name' => $quoteData['shipper_company'],
                    'first_name' => $quoteData['shipper_fname'],
                    'last_name' => $quoteData['shipper_lname'],
                    'email' => $quoteData['shipper_email'],
                    'phone1' => str_replace("-", "", $quoteData['shipper_phone1']),
                    'phone2' => str_replace("-", "", $quoteData['shipper_phone2']),
                    'cell' => str_replace("-", "", $quoteData['shipper_mobile']),
                    'fax' => $quoteData['shipper_fax'],
                    'address1' => $quoteData['shipper_address1'],
                    'address2' => $quoteData['shipper_address2'],
                    'city' => $quoteData['shipper_city'],
                    'state' => $quoteData['shipper_state'],
                    'zip_code' => $quoteData['shipper_zip'],
                    'country' => $quoteData['shipper_country'],
                    'referred_by' => $referrer_name_value,
                    'referred_id' => $quoteData['referred_by'],

                );
                $account->create($insert_arr);
            }

            /* Create Quote */
            $insert_arr = array(
                'type' => Entity::TYPE_LEAD,
                'quoted' => date("Y-m-d H:i:s"),
                'creator_id' => $_SESSION['member_id'],
                'assigned_id' => $_SESSION['member_id'],
                'parentid' => getParentId(),
                'est_ship_date' => date("Y-m-d", strtotime($quoteData['shipping_est_date'])),
                'status' => Entity::STATUS_LQUOTED,
                'vehicles_run' => $quoteData['shipping_vehicles_run'],
                'ship_via' => $quoteData['shipping_ship_via'],
                'information' => $quoteData['note_to_shipper'],
                'referred_by' => $referrer_name_value,
                'referred_id' => $quoteData['referred_by'],
                //$quoteData['referred_by']
            );
            $entity = new Entity($this->daffny->DB);
            $entity->create($insert_arr);
            /* Create Shipper */
            $shipper = new Shipper($this->daffny->DB);
            $insert_arr = array(
                'fname' => $quoteData['shipper_fname'],
                'lname' => $quoteData['shipper_lname'],
                'email' => $quoteData['shipper_email'],
                'company' => $quoteData['shipper_company'],
                'phone1' => str_replace("-", "", $quoteData['shipper_phone1']),
                'phone2' => str_replace("-", "", $quoteData['shipper_phone2']),
                'mobile' => str_replace("-", "", $quoteData['shipper_mobile']),
                'fax' => $quoteData['shipper_fax'],
                'address1' => $quoteData['shipper_address1'],
                'address2' => $quoteData['shipper_address2'],
                'city' => $quoteData['shipper_city'],
                'state' => $quoteData['shipper_state'],
                'zip' => $quoteData['shipper_zip'],
                'country' => $quoteData['shipper_country'],
                'shipper_type' => $quoteData['shipper_type'],
                'shipper_hours' => $quoteData['shipper_hours'],
            );
            $shipper->create($insert_arr, $entity->id);
            /* Create Origin */
            $origin = new Origin($this->daffny->DB);
            $insert_arr = array(
                'city' => $quoteData['origin_city'],
                'state' => $quoteData['origin_state'],
                'zip' => $quoteData['origin_zip'],
                'country' => $quoteData['origin_country'],
            );
            $origin->create($insert_arr, $entity->id);
            /* Create Destination */
            $destination = new Destination($this->daffny->DB);
            $insert_arr = array(
                'city' => $quoteData['destination_city'],
                'state' => $quoteData['destination_state'],
                'zip' => $quoteData['destination_zip'],
                'country' => $quoteData['destination_country'],
            );
            $destination->create($insert_arr, $entity->id);
            /* Update Quote */
            try {
                $distance = RouteHelper::getRouteDistance($origin->city . "," . $origin->state . "," . $origin->country, $destination->city . "," . $destination->state . "," . $destination->country);
                if (!is_null($distance)) {
                    $distance = RouteHelper::getMiles((float) $distance);
                } else {
                    $distance = 'NULL';
                }
            } catch (FDException $e) {
                $distance = 'NULL';
            }
            $update_arr = array(
                'shipper_id' => $shipper->id,
                'origin_id' => $origin->id,
                'destination_id' => $destination->id,
                'distance' => $distance,
            );
            $entity->update($update_arr);
            /* Create Vehicles */
            foreach ($quoteData['year'] as $i => $year) {
                if ($year != "") {

                    $vehicle = new Vehicle($this->daffny->DB);
                    $insert_arr = array(
                        'entity_id' => $entity->id,
                        'year' => $quoteData['year'][$i],
                        'make' => $quoteData['make'][$i],
                        'model' => $quoteData['model'][$i],
                        'type' => $quoteData['type'][$i],
                        'lot' => $quoteData['lot'][$i],
                        'vin' => $quoteData['vin'][$i],
                        'plate' => $quoteData['plate'][$i],
                        'state' => $quoteData['state'][$i],
                        'color' => $quoteData['color'][$i],
                        'inop' => $quoteData['inop'][$i],
                        'carrier_pay' => $quoteData['carrier_pay'][$i],
                        'deposit' => $quoteData['deposit'][$i],
                        'tariff' => $quoteData['carrier_pay'][$i] + $quoteData['deposit'][$i],
                    );
                    $vehicle->create($insert_arr);
                }
            }

            //Follow up
            $followup = new FollowUp($this->daffny->DB);
            $days = (int) $entity->getAssigned()->getDefaultSettings()->first_quote_followup;
            $followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int) date("m"), (int) date("d") + $days, (int) date("Y"))), $entity->id);

            /* Create Note */
            if (trim($quoteData['shipping_notes']) != "") {
                $note = new Note($this->daffny->DB);
                $insert_arr = array(
                    'entity_id' => $entity->id,
                    'type' => Note::TYPE_FROM,
                    'text' => $quoteData['shipping_notes'],
                );
                $note->create($insert_arr);
            }
            if (isset($_POST['send_email']) && $_POST['send_email'] == '1') {
                try {
                    $entity->sendInitialQuote();
                    $entity->sendOrderConfirmation();
                } catch (Exception $e) {
                    $this->setFlashError("Failed to send Email");
                }
            }
            //call storeproc
            $this->daffny->DB->transaction("commit");
            $this->setFlashInfo("Quote saccessfully saved");
            $entity->updateHeaderTable();
            //redirect(getLink('quotes'));
            redirect(getLink('leads/showimported/id', $entity->id));
        } catch (FDException $e) {
            $this->daffny->DB->transaction("rollback");
            throw $e;
        }
    }

    public function uploads()
    {
        $ID = (int) get_var("id");
        if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
            throw new UserException("Invalid Quote ID");
        }

        $this->tplname = "quotes.uploads";
        $this->title = "Documents";
        $entity = new Entity($this->daffny->DB);
        $entity->load($_GET['id']);
        $this->daffny->tpl->entity = $entity;
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", getLink("quotes/show/id/" . $_GET['id']) => "Quote #" . $entity->getNumber(), '' => "Documents"));

        $this->daffny->tpl->files = $this->getFiles($ID);
        $this->form->FileFiled("files_upload", array(), "Upload", "</td><td>");

        $this->form->TextField("mail_to", 255, array("style" => "width:280px;"), $this->requiredTxt . "Email", "</td><td>");
        $this->form->TextField("mail_subject", 255, array("style" => "width:280px;"), $this->requiredTxt . "Subject", "</td><td>");
        $this->form->TextArea("mail_body", 15, 10, array("style" => "height:100px; width:280px;"), $this->requiredTxt . "Body", "</td><td>");
    }

    /* Upload documents */
    public function upload_file()
    {
        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "entity/";
        $upload->max_file_size = 3 * 1024 * 1024;
        $upload->form_field = "file";
        $upload->make_script_safe = 1;
        $upload->allowed_file_ext = array("pdf", "doc", "docx", "xls", "xlsx", "jpg", "jpeg", "png", "tiff", "wpd");
        $upload->save_as_file_name = md5(time() . "-" . rand()) . time();
        $upload->upload_process();

        switch ($upload->error_no) {
            case 0:
                {
                    //check storage space
                    $license = new License($this->daffny->DB);
                    $license->loadCurrentLicenseByMemberId(getParentId());
                    $space = $license->getCurrentStorageSpace();
                    $used = $license->getUsedStorageSpace();

                    if ($used > $space) {
                        die("ERROR:Storage space exceeded.");
                    } else {

                        $sql_arr = array(
                            'name_original' => $_FILES[$upload->form_field]['name'],
                            'name_on_server' => $upload->save_as_file_name,
                            'size' => $_FILES[$upload->form_field]['size'],
                            'type' => $upload->file_extension,
                            'date_uploaded' => "now()",
                            'owner_id' => getParentId(),
                            'status' => 0,
                        );
                        $ins_arr = $this->daffny->DB->PrepareSql("app_uploads", $sql_arr);
                        $this->daffny->DB->insert("app_uploads", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();

                        $this->daffny->DB->insert("app_entity_uploads", array("entity_id" => $id, "upload_id" => $insid));

                        $out = getFileImageByType($upload->file_extension) . " ";
                        $out .= '<a href="' . getLink("quotes", "getdocs", "id", $insid) . '">' . $_FILES[$upload->form_field]['name'] . '</a>';
                        $out .= " (" . size_format($_FILES[$upload->form_field]['size']) . ") ";
                        $out .= '&nbsp;&nbsp;<a href="#" onclick="sendFile(\'' . $insid . '\', \'' . $sql_arr['name_original'] . '\')">Email</a>';
                        $out .= "&nbsp;&nbsp;&nbsp;<a href=\"#\" onclick=\"return deleteFile('" . getLink("quotes", "delete-file") . "','" . $insid . "');\"><img src=\"" . SITE_IN . "images/icons/delete.png\" alt=\"delete\" style=\"vertical-align:middle;\" width=\"16\" height=\"16\" /></a>";
                        die("<li id=\"file-" . $insid . "\">" . $out . "</li>");
                    }
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

    public function delete_file()
    {
        $out = array('success' => false);
        $id = (int) get_var('id');
        try {
            if ($row = $this->daffny->DB->selectRow('*', "app_uploads", "WHERE id = '$id' AND owner_id = '" . getParentId() . "'")) {
                if ($this->daffny->DB->isError) {
                    throw new Exception($this->getDBErrorMessage());
                } else {
                    $file_path = UPLOADS_PATH . "entity/" . $row["name_on_server"];
                    $this->daffny->DB->delete('app_uploads', "id = '" . $id . "'");
                    $this->daffny->DB->delete('app_entity_uploads', "upload_id = '" . $id . "'");
                    $out = array('success' => true);
                    @unlink($file_path);
                }
            }
        } catch (FDException $e) {
        }
        die(json_encode($out));
    }

    protected function getFiles($id)
    {
        $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
                 ORDER BY u.date_uploaded";
        $FilesList = $this->daffny->DB->selectRows($sql);
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            $files[$i]['img'] = getFileImageByType($file['type'], "Download " . $file['name_original']);
            $files[$i]['size_formated'] = size_format($file['size']);
        }
        return $files;
    }

    public function getdocs()
    {
        $ID = (int) get_var("id");
        $file = $this->daffny->DB->select_one("*", "app_uploads", "WHERE id = '" . $ID . "' AND owner_id = '" . getParentId() . "'");
        if (!empty($file)) {

            $file_path = UPLOADS_PATH . "entity/" . $file["name_on_server"];
            $file_name = $file["name_original"];
            $file_size = $file["size"];
            if (file_exists($file_path)) {
                if (strtolower($file["type"]) == "pdf") {
                    header("Content-Type: application/pdf; filename=\"" . $file_name . "\"");
                    //header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                } else {
                    header("Content-Type: application; filename=\"" . $file_name . "\"");
                    header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
                }
                header("Content-Description: \"" . $file_name . "\"");
                header("Content-length: " . $file_size);
                header("Expires: 0");
                header("Cache-Control: private");
                header("Pragma: cache");
                $fptr = @fopen($file_path, "r");
                $buffer = @fread($fptr, filesize($file_path));
                @fclose($fptr);
                echo $buffer;
                exit(0);
            }
        }
        header("HTTP/1.0 404 Not Found");
        exit(0);
    }

    public function import()
    {
        $this->tplname = "quotes.import";
        $this->title = "Import Quotes";
        $this->breadcrumbs = $this->getBreadCrumbs(array(getLink("quotes") => "Quotes", '' => 'Import'));
        $companyMembers = $this->getCompanyMembers();
        $membersData = array();
        foreach ($companyMembers as $member) {
            $membersData[$member->id] = $member->contactname;
        }
        $this->form->ComboBox("assigned_id", $membersData, array('style' => 'width:185px;'), "Assign Quotes to", "</td><td>");
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
                        $result = $import->importQuotes($upload->saved_upload_name, post_var('assigned_id'), $this->daffny->DB);
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
}
