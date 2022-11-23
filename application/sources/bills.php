<?php
require_once("../libs/mpdf/mpdf.php"); 

class ApplicationBills extends ApplicationAction
{
    private $time_periods = array(
        '5' => 'All Time',
        '1' => 'Current Month', 
        '2' => 'Last Month', 
        '3' => 'Last Quarter', 
        '4' => 'Current Year'
    );

    /**
     * Default constructor of the class, load on class call
     *
     * @author Shahrukh
     */
    public function construct()
    {
        // checking access
        if ($_SESSION['member']['pay_check_system_access'] == 0) {
            $this->setFlashError('You dont have access to view Payment System');
            redirect(getLink());
        }
        return parent::construct();
    }

    public function idx()
    {
        redirect(getLink("bills","uploaded"));
    }

    public function upload()
    {
        if (isset($_FILES['Invoice']) && isset($_POST['EntityID'])) {
            if (isset($_POST['Amount']) && ($_POST['Amount'] != 0)) {
                if ($_FILES['Invoice']['size'] > 0) {
                    if (false) {
                        $this->setFlashError('This order either has invoice paid/ voided or available to be paid.');
                    } else {
                        $entity = new Entity($this->daffny->DB);

                        try {
                            $entity->load($_POST['EntityID']);

                            // cut file extension
                            $fileExtention = $_FILES['Invoice']['name'];
                            $fileExtention = explode(".", $fileExtention);
                            $fileExtention = $fileExtention[1];

                            $newFileName = $entity->prefix . "-" . $entity->number . "-" . date('Ymdhis') . "." . $fileExtention;

                            // moving uploaded file
                            $targetPath = UPLOADS_PATH . "/Invoices/" . $newFileName;
                            move_uploaded_file($_FILES['Invoice']['tmp_name'], $targetPath);

                            // formatting upload date
                            $uploadDate = explode("/", $_POST['UploadDate']);
                            $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                            $sql_arr = array(
                                'EntityID' => $entity->id,
                                'OrderID' => $entity->prefix . "-" . $entity->number,
                                'AccountID' => $entity->account_id,
                                'MemberID' => $entity->assigned_id,
                                'CarrierID' => $entity->carrier_id,
                                'CarrierName' => $entity->getDispatchSheet()->carrier_company_name,
                                'Amount' => $_POST['Amount'],
                                'PaymentType' => $_POST['PaymentType'],
                                'ProcessingFees' => $_POST['ProcessingFees'],
                                'FeesType' => $_POST['FeesType'],
                                'Age' => $_POST['Age'],
                                'MaturityDate' => date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $_POST['Age'] . ' days')),
                                'Invoice' => $newFileName,
                                'UploaderID' => $_SESSION['member']['id'],
                                'UploaderName' => $_SESSION['member']['contactname'],
                                'CreatedAt' => $uploadDate,
                            );

                            if (isset($_POST['UploadDate']) && ($_POST['UploadDate'] != "")) {
                                $sql_arr['CreatedAt'] = date("Y-m-d h:i:s", strtotime($_POST['UploadDate']));
                            }

                            $ins_arr = $this->daffny->DB->PrepareSql('Invoices', $sql_arr);
                            $this->daffny->DB->insert('Invoices', $ins_arr);

                            $insid = $this->daffny->DB->get_insert_id();

                            /* UPDATE NOTE */
                            $note_array = array(
                                "entity_id" => $entity->id,
                                "sender_id" => $entity->assigned_id,
                                "status" => 1,
                                "type" => 3,
                                "system_admin" => 0,
                                "text" => "<green>Carrier bill has been uploaded for this order</green>"
                            );                            
                            $note = new Note($this->daffny->DB);
                            $note->create($note_array);

                            $this->setFlashInfo('Bill Added Successfully!');
                        } catch (Exception $e) {
                            $this->setFlashError('Invalid Order ID');
                        }
                    }
                } else {
                    $this->setFlashError('Invalid or No File Uploaded');
                }
            } else {
                $this->setFlashError('Amount cannot be 0');
            }
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function update_invoice()
    {
        if (isset($_POST['UpdateInvoiceID'])) {
            if (isset($_POST['CarInvoiceAmount']) && ($_POST['CarInvoiceAmount'] != 0)) {
                $entity = new Entity($this->daffny->DB);
                try {
                    $entity->load($_POST['UpdateInvoiceEntityID']);
                } catch (Exception $e) {
                    $this->setFlashError('Invalid Order ID');
                }

                // formatting upload date
                $uploadDate = explode("/", $_POST['CarInvoiceCreated']);
                $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                $update_arr = array(
                    'CarrierName' => $_POST['CarInvoiceName'],
                    'Amount' => $_POST['CarInvoiceAmount'],
                    'PaymentType' => $_POST['CarPayType'],
                    'ProcessingFees' => $_POST['ProcessingFees'] ? $_POST['ProcessingFees'] : null,
                    'FeesType' => $_POST['FeesType'],
                    'Age' => $_POST['CarInvoiceAge'],
                    'MaturityDate' => date('Y-m-d', strtotime($uploadDate . ' + ' . $_POST['CarInvoiceAge'] . ' days')),
                    'CreatedAt' => $uploadDate,
                );

                if ($_FILES['CarInvoiceDoc']['size'] > 0) {
                    // cut file extension
                    $fileExtention = $_FILES['CarInvoiceDoc']['name'];
                    $fileExtention = explode(".", $fileExtention);
                    $fileExtention = $fileExtention[1];

                    $newFileName = $entity->prefix . "-" . $entity->number . "-" . date('Ymdhis') . "." . $fileExtention;

                    // moving uploaded file
                    $targetPath = UPLOADS_PATH . "/Invoices/" . $newFileName;
                    move_uploaded_file($_FILES['CarInvoiceDoc']['tmp_name'], $targetPath);

                    $update_arr['Invoice'] = $newFileName;
                }

                $this->daffny->DB->update('Invoices', $update_arr, "ID = '" . $_POST['UpdateInvoiceID'] . "' ");
                $this->setFlashInfo('Bill Updated Successfully!');

            } else {
                $this->setFlashError('Amount cannot be 0');
            }
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function save_step1()
    {
        if (isset($_POST['UpdateInvoiceID'])) {
            if (isset($_POST['CarInvoiceAmount']) && ($_POST['CarInvoiceAmount'] != 0)) {
                $entity = new Entity($this->daffny->DB);
                try {
                    $entity->load($_POST['UpdateInvoiceEntityID']);
                } catch (Exception $e) {
                    $this->setFlashError('Invalid Order ID');
                }

                // formatting upload date
                $uploadDate = explode("/", $_POST['CarInvoiceCreated']);
                $uploadDate = $uploadDate[2] . "-" . $uploadDate[0] . "-" . $uploadDate[1];

                $update_arr = array(
                    'CarrierName' => $_POST['CarInvoiceName'],
                    'Amount' => $_POST['CarInvoiceAmount'],
                    'PaymentType' => $_POST['CarPayType'],
                    'ProcessingFees' => $_POST['ProcessingFees'] ? $_POST['ProcessingFees'] : null,
                    'FeesType' => $_POST['FeesType'] ? $_POST['FeesType'] : 0,
                    'Age' => $_POST['CarInvoiceAge'],
                    'MaturityDate' => date('Y-m-d', strtotime($uploadDate . ' + ' . $_POST['CarInvoiceAge'] . ' days')),
                    'CreatedAt' => $uploadDate,
                );

                $this->daffny->DB->update('Invoices', $update_arr, "ID = '" . $_POST['UpdateInvoiceID'] . "' ");
                echo json_encode(['success'=>true, 'message'=>'Invoice udpated successfully!']);
            } else {
                echo json_encode(['success'=>false, 'message'=>'Something went wrong!1']);
            }
        } else {
            echo json_encode(['success'=>false, 'message'=>'Something went wrong!2']);
        }
        exit();
    }

    public function step1(){
        if (isset($_POST['EntityID'])) {
            if (isset($_POST['Amount']) && ($_POST['Amount'] != 0)) {
                $entity = new Entity($this->daffny->DB);
                try {
                    $entity->load($_POST['EntityID']);

                    $sql_arr = array(
                        'EntityID' => $entity->id,
                        'OrderID' => $entity->prefix . "-" . $entity->number,
                        'AccountID' => $entity->account_id,
                        'MemberID' => $entity->assigned_id,
                        'CarrierID' => $entity->carrier_id,
                        'CarrierName' => $entity->getDispatchSheet()->carrier_company_name ? $entity->getDispatchSheet()->carrier_company_name  : "",
                        'Amount' => $_POST['Amount'],
                        'PaymentType' => $_POST['PaymentType'],
                        'ProcessingFees' => $_POST['ProcessingFees'],
                        'FeesType' => $_POST['FeesType'],
                        'Age' => $_POST['Age'],
                        'MaturityDate' => date('Y-m-d', strtotime(date('Y-m-d') . ' + ' . $_POST['Age'] . ' days')),
                        'Invoice' => 'In New Table',
                        'UploaderID' => $_SESSION['member']['id'],
                        'UploaderName' => $_SESSION['member']['contactname'],
                        'CreatedAt' => date('Y-m-d h:i:s'),
                    );

                    $ins_arr = $this->daffny->DB->PrepareSql('Invoices', $sql_arr);
                    $this->daffny->DB->insert('Invoices', $ins_arr);

                    $insid = $this->daffny->DB->get_insert_id();

                    /* UPDATE NOTE */
                    $note_array = array(
                        "entity_id" => $entity->id,
                        "sender_id" => $entity->assigned_id,
                        "status" => 1,
                        "type" => 3,
                        "system_admin" => 0,
                        "text" => "<green>Carrier bill has been uploaded for this order</green>"
                    );                            
                    $note = new Note($this->daffny->DB);
                    $note->create($note_array);

                    echo json_encode(['success'=>true, 'id'=>$insid]);
                } catch (Exception $e) {
                    echo json_encode(['success'=>false]);
                }
            } else {
                echo json_encode(['success'=>false]);
            }
            die;
        }
    }

    public function hold_unhold()
    {
        if (isset($_GET['InvoiceID'])) {
            $res = $this->daffny->DB->query("SELECT Hold FROM Invoices WHERE ID = " . $_GET['InvoiceID']);
            $Hold = mysqli_fetch_assoc($res)['Hold'];

            if ($Hold == 0) {
                $this->daffny->DB->query("UPDATE Invoices SET Hold = 1 WHERE ID = " . $_GET['InvoiceID']);
            } else {
                $this->daffny->DB->query("UPDATE Invoices SET Hold = 0 WHERE ID = " . $_GET['InvoiceID']);
            }

            redirect($_SERVER['HTTP_REFERER']);

        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete()
    {
        if(isset($_GET['InvoiceID'])){
            $res = $this->daffny->DB->query("SELECT Invoice FROM Invoices WHERE ID = ".$_GET['InvoiceID']);
            $Invoice = mysqli_fetch_assoc($res)['Invoice'];
            
            $sql = "DELETE FROM Invoices WHERE ID = ".$_GET['InvoiceID'];
            $res = $this->daffny->DB->query($sql);

            unlink('../uploads/Invoices/'.$Invoice);
            $this->setFlashInfo('Deleted Successfully!');
            redirect($_SERVER['HTTP_REFERER']);

        } else {
            $this->setFlashError('Invalid Bill');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function clear_unclear()
    {
        $sql = "UPDATE Invoices SET Clear = ".$_POST['flag']." WHERE ID IN (".implode(",",$_POST['IDs']).")";
        $this->daffny->DB->query($sql);

        if($_POST['flag'] == 1 ){
            $msg = "Cleared";
        } else {
            $msg = "Un Cleared";
        }

        $this->setFlashInfo('Successfully '.$msg.' !');

        echo json_encode(array('success' => true));
        die;
    }

    public function void_bills()
    {
        for($i=0; $i<count($_POST['EntityID']); $i++){
            $sql = "UPDATE app_payments_check SET Void = 1 WHERE PaymentID = ".$_POST['PaymentID'][$i];
            $this->daffny->DB->query($sql);

            $sql = "UPDATE app_payments SET Void = 1, deleted = 1 WHERE id = ".$_POST['PaymentID'][$i];
            $this->daffny->DB->query($sql);

            $sql = "UPDATE Invoices SET Void = 1, Paid = 0, Deleted = 0 WHERE PaymentID = ".$_POST['PaymentID'][$i];
            $this->daffny->DB->query($sql);

            $entity = new Entity($this->daffny->DB);
            $entity->load($_POST['EntityID'][$i]);

            // when order status is delivered (i.e. 9) than changing status to issues / pending payments
            if($entity->status == 9){
                $sql = "UPDATE app_order_header SET status = 7, pre_status = 9 WHERE entityid = ".$_POST['EntityID'][$i];
                $this->daffny->DB->query($sql);

                $sql = "UPDATE app_entities SET status = 7, pre_status = 9 WHERE id = ".$_POST['EntityID'][$i];
                $this->daffny->DB->query($sql);
            }

            $sql = "SELECT amount_format, check_number FROM app_payments_check WHERE PaymentID = ".$_POST['PaymentID'][$i];
            $res = $this->daffny->DB->query($sql);
            $checkNumber = "";
            $Amount = "";
            while($r = mysqli_fetch_assoc($res)){
                $checkNumber = $r['check_number'];
                $Amount = $r['amount_format'];
            }
            $NoteMessage = "<red>".$_SESSION['member']['contactname']." has been VOIDED amount $ ".$Amount."  Company Check #".$checkNumber;

            $member_id = $_SESSION['member_id'];
            $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
            $sql .= "VALUES( '".$_POST['EntityID'][$i]."', '".$member_id."','3', '".$NoteMessage."', '1', '1')";
            $this->daffny->DB->query($sql);
        }
        echo json_encode(array('success'=> true));
        exit;
    }

    public function update_txn_id()
    {
        if($_POST['txn_id'] == ""){
            echo json_encode(array('success'=>false,'message'=>'TxnID cannot be left blank')); die;
        } else {
            $sql = "UPDATE Invoices SET TxnID = '".$_POST['txn_id']."' WHERE ID = ".$_POST['bill_id'];
            $this->daffny->DB->query($sql);
            echo json_encode(array('success'=>true)); die;
        }
    }

    public function update_txn_ids()
    {
        $_POST['Invoices'] = implode(",",$_POST['Invoices']);
        $sql = "UPDATE Invoices SET TxnID = '".$_POST['TxnID']."' WHERE ID IN (".$_POST['Invoices'].")";
        $this->daffny->DB->query($sql);
        echo json_encode(array('success'=>true)); die;
    } 

    public function uploaded()
    {
        $this->title = 'Bills | Uploaded';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.uploaded';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'uploaded_bills') => 'Uploaded Bills',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
                // nothing to do here
            } else {
                $where .= " AND ";
            }
            $where .= " (CreatedAt >= '" . $startDate."' AND CreatedAt <= '" . $endDate . "') ";
        }

        $this->applyPager("Invoices", "", $where);
        $this->applyOrder("Invoices");
        $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices ".$where." " . $this->order->getOrder() . $this->pager->getLimit();
        $this->get_grid_data($sql);

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function pending_checks()
    {
        $this->title = 'Bills | Pending Checks';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.pending_checks';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'pending_checks') => 'Pending Checks',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE HOLD = 0 AND PaymentType = 13 AND Paid = 0 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND ( OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ) ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y')."-12-31";
            }

            $where .= " AND ";
            $where .= " (MaturityDate >= '" . $startDate."' AND MaturityDate <= '" . $endDate . "') ";
        }

        $this->applyPager("Invoices", "", $where);
        $this->applyOrder("Invoices");
        
        if($this->order->getOrder() == ""){
            $orderBy = " ORDER BY (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) DESC ";
        } else {
            $orderBy = $this->order->getOrder();
        }

        $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices ".$where." " . $orderBy . $this->pager->getLimit();
        $this->get_grid_data($sql);

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function pending_ach()
    {
        $this->title = 'Bills | Pending ACH';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.pending_ach';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'pending_ach') => 'Pending ACH',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Hold = 0 AND PaymentType = 24 AND Paid = 0 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  (OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%') "; 
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (MaturityDate >= '" . $startDate."' AND MaturityDate <= '" . $endDate . "') ";
        }

        $this->applyPager("Invoices", "", $where);
        $this->applyOrder("Invoices");
        $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices ".$where." " . $this->order->getOrder() . $this->pager->getLimit();
        $this->get_grid_data($sql);

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function on_hold()
    {
        $this->title = 'Bills | On Hold';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.on_hold';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'on_hold') => 'On Hold',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Deleted = 0 AND Hold = 1 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (MaturityDate >= '" . $startDate."' AND MaturityDate <= '" . $endDate . "') ";
        }

        $this->applyPager("Invoices", "", $where);
        $this->applyOrder("Invoices");
        $sql = "SELECT *, Age as Term, (DATEDIFF(CURRENT_DATE(),`CreatedAt`)) as Age FROM Invoices ".$where." " . $this->order->getOrder() . $this->pager->getLimit();
        $this->get_grid_data($sql);

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function paid()
    {
        if($_GET['type']=="cleared_check"){
            $this->cleared_check();
        } else if($_GET['type']=="uncleared_check") {
            $this->uncleared_check();
        } else if($_GET['type']=="cleared_ach") {
            $this->cleared_ach();
        } else {
            $this->uncleared_ach();
        }
    }

    public function cleared_check()
    {
        $this->title = 'Bills | Paid Checks';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.cleared_check';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'paid', 'type','checks') => 'Paid Checks',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Paid = 1 AND PaymentType = 13 AND Clear = 1 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (PaidDate >= '" . $startDate."' AND PaidDate <= '" . $endDate . "') ";
        }

        $_SESSION['order_by_field'] = $_POST['order_by_field'];
        $_SESSION['asc_desc'] = $_POST['asc_desc'];
        if($_SESSION['asc_desc'] == "NONE" || $_SESSION['asc_desc'] == ""){
            $ordering = " ";
        } else {
            $ordering = " ORDER BY `".$_POST['order_by_field']."` ".$_POST['asc_desc'];
        }
        
        if($_GET['view_type'] == 1){
            // view all
            $sql = "SELECT *, Age as Term, (SELECT check_number from app_payments_check WHERE id = CheckID) as CheckID FROM Invoices ".$where. " ".$ordering;
            $this->get_grid_data($sql);
        } else {
            // view with pagination
            $this->applyPager("Invoices", "", $where);
            $this->applyOrder("Invoices");
            $sql = "SELECT *, Age as Term, (SELECT check_number from app_payments_check WHERE id = CheckID) as CheckID FROM Invoices ".$where." " . $ordering . $this->pager->getLimit();
            $this->get_grid_data($sql);
        }

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '</td><td colspan="3">');
        $this->form->ComboBox('time_period', $this->time_periods, array(), '', '');
        $this->form->DateField('start_date', 10, array(), '', '');
        $this->form->DateField('end_date', 10, array(), '', '');

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = 'checked="checked"';
            $this->input['ptype1ch'] = '';
        } else {
            $this->input['ptype1ch'] = 'checked="checked"';
            $this->input['ptype2ch'] = '';
        }

        if( $_POST['asc_desc'] == "ASC" ){
            $this->input['order_asc'] = 'checked="checked"';
            $this->input['order_desc'] = '';
        } elseif( $_POST['asc_desc'] == "DESC" ){
            $this->input['order_desc'] = 'checked="checked"';
            $this->input['order_asc'] = '';
        } else {
            $this->input['order_none'] = 'checked="checked"';
            $this->input['order_asc'] = '';
        }
    }

    public function uncleared_check()
    {
        $this->title = 'Bills | Paid Checks';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.uncleared_checks';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'paid', 'type','checks') => 'Paid Checks',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Paid = 1 AND PaymentType = 13 AND Clear = 0 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (PaidDate >= '" . $startDate."' AND PaidDate <= '" . $endDate . "') ";
        }

        $_SESSION['order_by_field'] = $_POST['order_by_field'];
        $_SESSION['asc_desc'] = $_POST['asc_desc'];

        if($_SESSION['asc_desc'] == "NONE" || $_SESSION['asc_desc'] == ""){
            $ordering = "";
        } else {
            $ordering = " ORDER BY `".$_POST['order_by_field']."` ".$_POST['asc_desc'];
        }

        if($_GET['view_type'] == 1){
            // view all
            $sql = "SELECT *, Age as Term, (SELECT check_number from app_payments_check WHERE id = CheckID) as CheckID FROM Invoices ".$where." ".$ordering;
            $this->get_grid_data($sql);
        } else {
            $this->applyPager("Invoices", "", $where);
            $this->applyOrder("Invoices");
            $sql = "SELECT *, Age as Term, (SELECT check_number from app_payments_check WHERE id = CheckID) as CheckID FROM Invoices ".$where." " . $ordering  . $this->pager->getLimit();
            $this->get_grid_data($sql);
        }

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '</td><td colspan="3">');
        $this->form->ComboBox('time_period', $this->time_periods, array(), '', '');
        $this->form->DateField('start_date', 10, array(), '', '');
        $this->form->DateField('end_date', 10, array(), '', '');

        if ($this->input['ptype'] == 2) {
            $this->input['ptype2ch'] = 'checked="checked"';
            $this->input['ptype1ch'] = '';
        } else {
            $this->input['ptype1ch'] = 'checked="checked"';
            $this->input['ptype2ch'] = '';
        }

        if( $_SESSION['asc_desc'] == "ASC" ){
            $this->input['order_asc'] = 'checked="checked"';
            $this->input['order_desc'] = '';
        } elseif( $_SESSION['asc_desc'] == "DESC" ){
            $this->input['order_desc'] = 'checked="checked"';
            $this->input['order_asc'] = '';
        } else {
            $this->input['order_none'] = 'checked="checked"';
            $this->input['order_asc'] = '';
        }
    }

    public function cleared_ach()
    {
        $this->title = 'Bills | Paid ACH';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.cleared_ach';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'paid','type','ach') => 'Paid ACH',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Paid = 1 AND PaymentType = 24 AND Clear = 1 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (PaidDate >= '" . $startDate."' AND PaidDate <= '" . $endDate . "') ";
        }

        if($_GET['view_type'] == 1){
            // view all
            $sql = "SELECT *, Age as Term, (SELECT transaction_id from app_payments WHERE id = PaymentID) as TxnID FROM Invoices ".$where." ";
            $this->get_grid_data($sql);
        } else {
            $this->applyPager("Invoices", "", $where);
            $this->applyOrder("Invoices");
            $sql = "SELECT *, Age as Term, (SELECT transaction_id from app_payments WHERE id = PaymentID) as TxnID FROM Invoices ".$where." " . $this->order->getOrder() . $this->pager->getLimit();
            $this->get_grid_data($sql);
        }

        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function uncleared_ach()
    {
        $this->title = 'Bills | Paid ACH';
        $this->section = 'Bill Manager';
        $this->tplname = 'bills.uncleared_ach';
        $this->breadcrumbs = $this->getBreadCrumbs(
            array(
                getLink('bills', 'uploaded_bills') => 'Bills',
                getLink('bills', 'paid','type','ach') => 'Paid ACH',
            )
        );
        $this->daffny->tpl->count = $this->get_bill_counts();

        // when form submitted for search
        if (isset($_POST['submit'])) {
            $_SESSION['search_bill'] = trim(post_var('search_bill'));
            $_SESSION['time_period'] = trim(post_var('time_period'));
            $_SESSION['start_date'] = trim(post_var('start_date'));
            $_SESSION['end_date'] = trim(post_var('end_date'));
            $_SESSION['ptype'] = trim(post_var('ptype'));
            $_SESSION['start_date2'] = trim(post_var('start_date'));
            $_SESSION['end_date2'] = trim(post_var('end_date'));

            // Check dates
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

        $search_arr = array(
            'search_bill' => $_SESSION['search_bill'], 
            'time_period' => $_SESSION['time_period'], 
            'start_date' => $_SESSION['start_date2'], 
            'end_date' => $_SESSION['end_date2'], 
            'ptype' => $_SESSION['ptype']
        );

        // prepare search conditions for query
        $filters = $search_arr;
        $filters['start_date'] = $_SESSION['start_date'];
        $filters['end_date'] = $_SESSION['end_date'];

        $where = " WHERE Paid = 1 AND PaymentType = 24 AND Clear = 0 ";
        if($search_arr['search_bill'] == "null" || $search_arr['search_bill'] == ""){
            // do nothing
        } else {
            $where .= " AND  OrderID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierID LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " CarrierName LIKE '%" . $search_arr['search_bill'] . "%' OR ";
            $where .= " TxnID LIKE '%" . $search_arr['search_bill'] . "%' ";
        }

        if ($filters['start_date'] != "null" && $filters['end_date'] != "null") {
            $startDate = $filters['start_date'];
            $endDate = $filters['end_date'];

            if($startDate == ""){
                $startDate = "2011-01-01";
            }

            if($endDate == ""){
                $endDate = date('Y-m-d h:i:s');
            }

            $where .= " AND ";
            $where .= " (PaidDate >= '" . $startDate."' AND PaidDate <= '" . $endDate . "') ";
        }

        if($_GET['view_type'] == 1){
            // view all
            $sql = "SELECT *, Age as Term, (SELECT transaction_id from app_payments WHERE id = PaymentID) as TxnID FROM Invoices ".$where;
            $this->get_grid_data($sql);
        } else {
            $this->applyPager("Invoices", "", $where);
            $this->applyOrder("Invoices");
            $sql = "SELECT *, Age as Term, (SELECT transaction_id from app_payments WHERE id = PaymentID) as TxnID FROM Invoices ".$where." " . $this->order->getOrder() . $this->pager->getLimit();
            $this->get_grid_data($sql);
        }
        // prepare search input fields
        $search_arr['search_bill'] = "";
        foreach ($search_arr as $k => $v) {
            $this->input[$k] = htmlspecialchars($v);
        }

        $this->form->TextField('search_bill', 100, array(), 'Search', '');
        $this->form->ComboBox('time_period', $this->time_periods, null, '', '');
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

    public function print_ach_receipts()
    {
        $InvoiceIds = implode(",",$_POST['IDs']);
        $sql = "SELECT * FROM Invoices WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
        $res = $this->daffny->DB->query($sql);
        $invoiceData = array();
        while($r = mysqli_fetch_assoc($res)){
            $invoiceData[] = $r;
        }
        // create folder if not exists
        if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
            mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
        }
        // create file
        $fileName = "ACH-Recipts-".date('Y-m-d his').".pdf";
        $fullPath = ROOT_PATH."uploads/Invoices/ACH/".$fileName;
        ob_start();
        $mpdf = new mPDF(['format' => 'Legal']);

        $fileContents = "<h2>ACH Receipt Issued To : ".$_SESSION['member']['contactname']."</h2>";
        $fileContents .= "<table width='100%'>";
        $fileContents .= "<tr><td>Carrier Name</td><td>OrderID</td><td>Check/TxnID</td><td>Carrier Fees</td><td>Processing Fees</td><td>Actual Fees</td><td>Date</td></tr>";
        
        $totalAmount = 0;
        for($i=0; $i<count($invoiceData); $i++){
            //$mpdf->AddPage();
            if($invoiceData[$i]['PaymentType'] == 13){
                $sql = "SELECT `check` FROM app_payments WHERE `id` = ".$invoiceData[$i]['PaymentID'];
                $IDTxnCheck = $this->daffny->DB->query($sql);
                $TxnCheck = mysqli_fetch_assoc($IDTxnCheck)['check'];
            } else {
                $TxnCheck = $invoiceData[$i]['TxnID'];
            }
            
            $fileContents .= "<tr><td>".$invoiceData[$i]['CarrierName']."</td><td>".$invoiceData[$i]['OrderID']."</td><td>".$TxnCheck."</td><td>$".$invoiceData[$i]['Amount']."</td><td>$".$invoiceData[$i]['ProcessingFees']."</td><td>$".($invoiceData[$i]['Amount']-$invoiceData[$i]['ProcessingFees'])."</td><td>".date('m-d-Y')."</td></tr>";

            $sql = "SELECT count(*) as `Number` FROM app_payments WHERE `entity_id` = ".$invoiceData[$i]['EntityID'];
            $PaymentNumber = $this->daffny->DB->query($sql);
            $PaymentNumber = mysqli_fetch_assoc($PaymentNumber)['Number'];

            $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount, processing_fees, payment_type, fees_type,method, transaction_id,entered_by)";
            $sql .= "VALUES( '".$invoiceData[$i]['EntityID']."', '".($PaymentNumber+1)."', '".date('Y-m-d')."', '1', '3', '".($invoiceData[$i]['Amount']-$invoiceData[$i]['ProcessingFees'])."', '".$invoiceData[$i]['ProcessingFees']."' , '".$invoiceData[$i]['PaymentType']."', '".$invoiceData[$i]['FeesType']."','6','".$invoiceData[$i]['TxnID']."','".$_SESSION['member']['id']."' )";
            $res = $this->daffny->DB->query($sql);
            $insertedPayID = $this->daffny->DB->get_insert_id();

            $sql = "INSERT INTO app_payments (entity_id,number,date_received,fromid,toid,amount, payment_type, fees_type,method, transaction_id,entered_by)";
            $sql .= "VALUES( '".$invoiceData[$i]['EntityID']."', '".($PaymentNumber+2)."', '".date('Y-m-d')."', '1', '3', '".($invoiceData[$i]['ProcessingFees'])."' , '".$invoiceData[$i]['PaymentType']."', '".$invoiceData[$i]['FeesType']."','99','".$invoiceData[$i]['TxnID']."','".$_SESSION['member']['id']."' )";
            $res = $this->daffny->DB->query($sql);
            $this->daffny->DB->get_insert_id();

            $currentDate = date('Y-m-d h:i:s');
            $sql = "UPDATE Invoices SET PaidDate = '".$currentDate."', PaymentID = '".$insertedPayID."', TxnID = '".$invoiceData[$i]['TxnID']."' WHERE `ID` = ".$invoiceData[$i]['ID'];
            $PaymentNumber = $this->daffny->DB->query($sql);

            $totalAmount = $totalAmount + $invoiceData[$i]['Amount'];
            $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
            $NoteMessage = "<green>Carrier has been paid amount $ ".number_format((float) $invoiceData[$i]['Amount'], 2, ".", ",")." by ACH Payment Fees";
            $sql .= "VALUES( '".$invoiceData[$i]['EntityID']."', '".$_SESSION['member_id']."','3', '".$NoteMessage."', '1', '1')";
            $this->daffny->DB->query($sql); 
        }

        $fileContents .= "<tr><td colspan='5' align='center'> Total Amount : $".$totalAmount."</td></tr>";
        $fileContents .= "</table>";
        $mpdf->WriteHTML($fileContents);

        ob_end_clean();

        $res = $mpdf->Output($fullPath);

        $sql = "UPDATE Invoices SET Deleted = 1, Paid = 1 WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
        $this->daffny->DB->query($sql);

        $out = array('URL' => $fileName);
        echo json_encode($out);
        die;

    }

    public function printclear_unclear_recipts()
    {
        $InvoiceIds = implode(",",$_POST['IDs']);

        
        $sql = "SELECT * FROM Invoices WHERE `ID` IN (".$InvoiceIds.") AND Hold =  0";
        $res = $this->daffny->DB->query($sql);

        $invoiceData = array();
        while($r = mysqli_fetch_assoc($res)){
            $invoiceData[] = $r;
        }

        // create folder if not exists
        if (!file_exists(ROOT_PATH."uploads/Invoices/")) {
            mkdir(ROOT_PATH."uploads/Invoices/", 0777, true);
        }
        
        if($_POST['flag'] == 1 ){
            $msg = "Cleared";
            $notes_msg = "has confirmed";
        } else {
            $msg = "UnCleared";
            $notes_msg = "has un-confirmed";
        }

        // create file
        $fileName = $msg."-".date('Y-m-d his').".pdf";
        $fullPath = ROOT_PATH."uploads/Invoices/Clear_UnClear/".$fileName;
        
        ob_start();
        $mpdf = new mPDF(['format' => 'Legal']);

        $fileContents = "<h2>Check Cleared By : ".$_SESSION['member']['contactname']."</h2>";
        $fileContents .= "<table width='100%'>";
        $fileContents .= "<tr><td>Carrier Name</td><td>Check/TxnID</td><td>Amount</td><td>Date</td><td>Status</td></tr>";
        
        $totalAmount = 0;
        for($i=0; $i<count($invoiceData); $i++){
            //$mpdf->AddPage();
            if($invoiceData[$i]['PaymentType'] == 13){
                $sql = "SELECT `check` FROM app_payments WHERE `id` = ".$invoiceData[$i]['PaymentID'];
                $IDTxnCheck = $this->daffny->DB->query($sql);
                $TxnCheck = mysqli_fetch_assoc($IDTxnCheck)['check'];
            } else {
                $TxnCheck = $invoiceData[$i]['TxnID'];
            }
            
            $fileContents .= "<tr><td>".$invoiceData[$i]['CarrierName']."</td><td>".$TxnCheck."</td><td>$".$invoiceData[$i]['Amount']."</td><td>".date('m-d-Y')."</td><td>".$msg ."</td></tr>";

            $totalAmount = $totalAmount + $invoiceData[$i]['Amount'];
            $sql = "INSERT INTO app_notes (entity_id,sender_id,`type`,`text`,`status`,system_admin)";
            $NoteMessage = $_SESSION['member']['contactname']." ".$notes_msg." that payment has been processed by the bank.";
            $sql .= "VALUES( '".$invoiceData[$i]['EntityID']."', '".$_SESSION['member_id']."','3', '".$NoteMessage."', '1', '1')";
            $this->daffny->DB->query($sql); 
        }

        $fileContents .= "<tr><td colspan='5' align='center'> Total Amount : $".$totalAmount."</td></tr>";
        $fileContents .= "</table>";
        $mpdf->WriteHTML($fileContents);

        ob_end_clean();

        $res = $mpdf->Output($fullPath);
        $out = array('URL' => "https://cargoflare.com/uploads/Invoices/Clear_UnClear/".$fileName);
        echo json_encode($out);
        die;
    }

    public function upload_file()
    {
        $id = (int) get_var("id");
        $upload = new upload();
        $upload->out_file_dir = UPLOADS_PATH . "Bills/";
        $upload->max_file_size = 50 * 1024 * 1024;
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

                        $res = $this->daffny->DB->query("SELECT * FROM app_order_header WHERE entityid = " . $_GET['entity']);
                        while ($r = mysqli_fetch_assoc($res)) {
                            $res = $r;
                        }

                        $sql_arr = array(
                            'invoice_id' => $_GET['id'],
                            'path' => $upload->save_as_file_name,
                            'extension' => $upload->file_extension
                        );

                        $ins_arr = $this->daffny->DB->PrepareSql("invoice_documents", $sql_arr);
                        $this->daffny->DB->insert("invoice_documents", $ins_arr);
                        $insid = $this->daffny->DB->get_insert_id();

                        echo json_encode(["file"=>$upload->save_as_file_name,'id'=>$insid]);
                        die;
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

    public function deleteUploaded() {
        $sql = "DELETE FROM Invoices WHERE ID = ".$_POST['invoiceId'];
        $res = $this->daffny->DB->query($sql);

        $sql = "DELETE FROM invoice_documents WHERE invoice_id = ".$_POST['invoiceId'];
        $res = $this->daffny->DB->query($sql);
        die(true);
    }

    public function deleteUploadedFiles() 
    {
        $sql = "DELETE FROM invoice_documents WHERE id = ".$_POST['id'];
        $res = $this->daffny->DB->query($sql);
        die(true);
    }

    public function getInvoiceData()
    {
        $res = $this->daffny->DB->query("SELECT * FROM Invoices WHERE id = " . $_POST['id']);
        
        $row = array();
        while($r = mysqli_fetch_assoc($res)){
            $row[] = $r;
        }

        $res2 = $this->daffny->DB->query("SELECT * FROM invoice_documents WHERE invoice_id = " . $_POST['id']);
        
        $row2 = array();
        while($r2 = mysqli_fetch_assoc($res2)){
            $row2[] = $r2;
        }

        $docs = "";
        $count = 1;

        foreach($row2 as $r){
            $docs .= "<a href='/uploads/Bills/".$r['path']."' target='_blank'>Document ".$count."</a><br>";
            $count++;
        }

        $html = "<h3>Invoice Preview</h3>
                    <table class='table table-striped table-bordered'>
                        <tr>
                            <td>Order ID</td><td>".$row[0]['OrderID']."</td>
                        </tr>
                        <tr>
                            <td>Carried Name</td><td>".$row[0]['CarrierName']."</td>
                        </tr>
                        <tr>
                            <td>Amount</td><td>".$row[0]['Amount']."</td>
                        </tr>
                        <tr>
                            <td>Age</td><td>".$row[0]['Age']."</td>
                        </tr>
                        <tr>
                            <td>Documents</td><td>".$docs."</td>
                        </tr>
                    </table>";
        
        echo json_encode($html);die;
    }

    private function get_bill_counts()
    {

        // fetching counts
        $sql = " SELECT ( SELECT count(*) as Counts FROM Invoices ) as `uploaded`, ";
        $sql .= " ( SELECT count(*) as Counts FROM Invoices WHERE PaymentType = 13 AND Deleted = 0 AND Hold = 0 ORDER BY Age DESC ) as `checks`, ";
        $sql .= " ( SELECT count(*) as Counts FROM Invoices WHERE PaymentType = 24 AND Deleted = 0 AND Hold = 0 ORDER BY Age DESC ) as `ach`, ";
        $sql .= " ( SELECT count(*) as Counts FROM Invoices WHERE Deleted = 0 AND Hold = 1 ORDER BY Age DESC ) as `on_hold`, ";
        $sql .= " ( SELECT count(*) as Counts FROM `Invoices` WHERE Paid = 1 ORDER BY PaidDate DESC ) as `paid`, ";
        $sql .= " ( SELECT count(*) as Counts FROM `Invoices` WHERE Paid = 1 AND PaymentType = 13 AND Clear = 1 ORDER BY PaidDate DESC ) as `paid_checks_cleared`, ";
        $sql .= " ( SELECT count(*) as Counts FROM `Invoices` WHERE Paid = 1 AND PaymentType = 13 AND Clear = 0 ORDER BY PaidDate DESC ) as `paid_checks_uncleared`, "; 
        $sql .= " ( SELECT count(*) as Counts FROM `Invoices` WHERE Paid = 1 AND PaymentType = 24 AND Clear = 1 ORDER BY PaidDate DESC ) as `paid_ach_cleared`, ";
        $sql .= " ( SELECT count(*) as Counts FROM `Invoices` WHERE Paid = 1 AND PaymentType = 24 AND Clear = 0 ORDER BY PaidDate DESC ) as `paid_ach_uncleared` ";

        $res = $this->daffny->DB->query($sql);
        $counts = mysqli_fetch_assoc($res);

        return array(
            'uploaded' => $counts['uploaded'],
            'pending_check' => $counts['checks'],
            'pending_ach' => $counts['ach'],
            'on_hold' => $counts['on_hold'],
            'paid' => $counts['paid'],
            'paid_checks_cleared' => $counts['paid_checks_cleared'],
            'paid_checks_uncleared' => $counts['paid_checks_uncleared'],
            'paid_ach_cleared' => $counts['paid_ach_cleared'],
            'paid_ach_uncleared' => $counts['paid_ach_uncleared']
        );
    }

    private function getTimePeriod($type)
    {
        $d1 = date('Y-m-d 00:00:00');
        $d2 = date('Y-m-d 23:59:59');

        switch ($type) {
            case '1':
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
                $d2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m') + 1, 0, date('Y')));
                break;
            case '2':
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m') - 1, 1, date('Y')));
                $d2 = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), 0, date('Y')));
                break;
            case '3':
                // Get a quarter in the year from a month
                $startmth = date('m') - 3 - ((date('m') - 1) % 3);
                // Fix up Jan - Feb to get LAST year's quarter dates (Oct - Dec)
                $year = date('Y');
                if ($startmth == -2) {
                    $startmth += 12;
                    --$year;
                }
                $endmth = $startmth + 2;
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, $startmth, 1, $year));
                $d2 = date('Y-m-d H:i:s', mktime(23, 59, 59, $endmth, date('t', mktime(0, 0, 0, $endmth, 1, $year)), $year));
                break;
            case '4':
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, date('Y')));
                $d2 = date('Y-m-d H:i:s', mktime(0, 0, 0, 12, 31, date('Y')));
                break;
            case '5':
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, 2011));
                $d2 = date('Y-m-d H:i:s', mktime(0, 0, 0, 12, 31, date('Y')));
                break;
            default:
                $d1 = date('Y-m-d H:i:s', mktime(0, 0, 0, 1, 1, 2011));
                $d2 = date('Y-m-d H:i:s', mktime(0, 0, 0, 12, 31, date('Y')));
        }

        return array(
            '0' => $d1, '1' => $d2,
        );
    }

}