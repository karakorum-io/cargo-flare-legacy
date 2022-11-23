<?php

/**
 * Class to deal with operations related to wall boards
 * 
 * @author Chetu Inc.
 * @version 1.0 
 */
require_once(ROOT_PATH . "libs/excel/PHPExcel.php");
require_once(ROOT_PATH . "libs/excel/PHPExcel/Writer/Excel5.php");

class ApplicationWallboards extends ApplicationAction {

    public $title = "Manage Wallboards";
    public $section = "Manage Wallboards";

    /**
     * Default constructor for the class loading dependencies and more
     * 
     * @author Chetu Inc.
     * @version 1.0 
     * @return object
     */
    public function construct() {
        return parent::construct();
    }

    /**
     * List all wall boards
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @return void 
     */
    public function idx() {
        $this->breadcrumbs = $this->getBreadCrumbs("Wallboards");
        $this->tplname = "wallboards.list";		$html = '<div style="text-align: left; clear:both; padding-bottom:5px; padding-top:5px;">    			<img style="vertical-align:middle;" src="'.SITE_IN.'images/icons/add.gif" alt="Add" width="16" height="16" />			<a href="'.getLink("wallboards", "add_edit").'">				&nbsp;&nbsp;Add Wallboard			</a>		</div><br><br>';
        $this->daffny->tpl->emptyText = $html."<center><h4>Wallboards not found.</h4></center>";
        $this->applyPager("app_wallboards", "", "WHERE `agent_parent` = '".$_SESSION['member']['parent_id']."'");
        $this->applyOrder("app_wallboards");
        $sql = "SELECT * FROM app_wallboards WHERE `agent_parent` = '".$_SESSION['member']['parent_id']."' ".$this->order->getOrder().$this->pager->getLimit();
        $this->getGridData($sql);
    }
    
    /**
     * Function to add and update wall boards in the web application
     * 
     * @author Chetu Inc.
     * @version 1.0 
     * @return void 
     */
    public function add_edit() {
        
        $parentId = $_SESSION['member']['parent_id'];
        
        $id = explode("/",$_GET['url']);
        $ID =$id[2];            
        $this->tplname = "wallboards.form";
        $this->title .= ($ID > 0 ? " - Edit" : " - Add");
        
        if (isset($_POST['submit'])) {
            $sql_arr = $this->getTplPostValues();
            
            $agentList = explode(",",$_POST['agentList'][0]);
            $agentName = explode(",",$_POST['agentName'][0]);
            $totalAgents = count($agentList);
            
            $this->isEmpty("title", "Name");
            $this->isEmpty("hash", "Hash");
            
            $sql_arr['agents'] = $totalAgents;			
            $sql_arr['agent_parent'] = $_SESSION['member']['parent_id'];
            
            if ($ID > 0) {
                $sql_arr['updated_at'] = date('Y-d-m h:i:s');
            }
            
            if (!count($this->err)) {
                $sql_arr1 = $this->daffny->DB->PrepareSql("app_wallboards", $sql_arr);                                
                if ($ID > 0) {
                    
                    /**
                     * Update Wall boards
                     */
                    $this->daffny->DB->update("app_wallboards", $sql_arr1, "id ='".$ID."'");
                    
                    $sql = "DELETE FROM `app_wallboard_agents` WHERE `wallboard_id` = '".$ID."'";
                    $this->daffny->DB->query($sql);
                    
                    for($i=0;$i<$totalAgents;$i++){                        
                        $sql = "INSERT INTO `app_wallboard_agents` (`wallboard_id`, `agent_id`,`agent_name`,`updated_at`) VALUES ('".$ID."', '".$agentList[$i]."','".$agentName[$i]."','".date('Y-m-d h:i:s')."')";
                        $this->daffny->DB->query($sql);
                    }
                    $this->setFlashInfo("Information has been updated.");                    
                } else {
                    /**
                     * Add new Wall boards
                     */                    
                    $this->daffny->DB->insert("app_wallboards", $sql_arr1);
                    $this->setFlashInfo("Information has been added.");
                    $insertedId = $this->daffny->DB->get_insert_id();
                    if($insertedId > 0){
                        
                        for($i=0;$i<$totalAgents;$i++){
                            $sql = "INSERT INTO `app_wallboard_agents` (`wallboard_id`, `agent_id`,`agent_name`) VALUES ('".$insertedId."', '".$agentList[$i]."','".$agentName[$i]."')";
                            $this->daffny->DB->query($sql);
                        }
                        $this->setFlashInfo("Information has been added.");
                    }
                }                
                if($ID > 0){
                    redirect(getLink("wallboards", "add_edit",$ID));
                } else {
                    redirect(getLink("wallboards"));
                }                
            }            
        }
        
        if ($ID > 0) {  
            
            $isEdit = true;
            $sql = "SELECT * FROM app_wallboards WHERE id = '".$ID."'";
            $row = $this->daffny->DB->selectRow($sql);
                                   
            $this->input = $row;
            $this->breadcrumbs = $this->getBreadCrumbs(
                array(
                    getLink("wallboards") => "Wallboards",
                    getLink("wallboards","add_edit", $ID) => htmlspecialchars($row['title']),
                    '' => "Edit"
                )
            );
            
            $sql = "SELECT `agent_id`,`agent_name` FROM app_wallboard_agents WHERE wallboard_id = '".$ID."'";            
            $agents = $this->daffny->DB->query($sql);
                                    
            $agentList = array();
            
            while($result = mysqli_fetch_assoc($agents)){
                $agentList[] = $result;
            }
            
            $this->daffny->agents = $agentList;
            
            $sql = "SELECT `id`,`contactname` FROM `members` WHERE status = 'Active' AND `parent_id` = '".$parentId."' ";
            $availAgents = $this->daffny->DB->query($sql);
            
            $availAgentList = array();
            
            while($result = mysqli_fetch_assoc($availAgents)){
                $availAgentList[] = $result;
            }
            
            foreach($agentList as $key => $value) {
                   
                $match = $value['agent_id'];
                    foreach($availAgentList as $key => $value) {
                      if($value['id'] == $match){
                        unset($availAgentList[$key]);
                    } 
                } 
               
             }
             
            $this->daffny->availAgents = $availAgentList;
            
        } else { 
            $isEdit =false;
            $sql = "SELECT `id`,`contactname` FROM `members` WHERE status = 'Active' AND `parent_id` = '".$parentId."' ";
			
            $availAgents = $this->daffny->DB->query($sql);
            
            $availAgentList = array();
            
            while($result = mysqli_fetch_assoc($availAgents)){
                $availAgentList[] = $result;
            }
            
            $this->daffny->availAgents = $availAgentList;
            
            $this->breadcrumbs = $this->getBreadCrumbs(
                array(
                    getLink("wallboards") => "Wallboards",
                    '' => "Add New"
                )
            );
        }
        
        $this->daffny->isEdit = $isEdit;
        $this->form->TextField("title", 255, array(), $this->requiredTxt . "Wallboard Title", "</td><td>");        
        $this->form->TextField("forward_email", 255, array("class" => "email"), "EOD Report Email", "</td><td>");
        $this->form->TextField("hash", 255, array(),$this->requiredTxt."Hash", "</td><td>");
        $this->form->ComboBox("status", array(1 => "Active", 0 => "Inactive"), array("style" => ""), $this->requiredTxt."Status","</td><td>");
    }
    
    /**
     * Function to delete Wall
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @return void 
     */
    public function delete(){
        
        $id = explode("/",$_GET['url']);
        $ID =$id[2];
        
        $query = "DELETE FROM `app_wallboards` WHERE `id`= '".$ID."' ";
        $this->daffny->DB->query($query);
        $sql = "DELETE FROM `app_wallboard_agents` WHERE `wallboard_id` = '".$ID."'";
        $this->daffny->DB->query($sql);
        
        redirect(getLink("wallboards"));
    }
    
    function export(){
        
        $id = explode("/",$_GET['url']);
        $ID =$id[2];
        
        $query = "SELECT `agent_id`,`agent_name` FROM `app_wallboard_agents` WHERE `wallboard_id` = '".$data['id']."'";
        $assignedAgents = $this->daffny->DB->query($query);
        
        $assignedAgents = array();
        while($row = mysqli_fetch_assoc($assignedAgents)){
            $assignedAgents[] = $row;                
        }
            
        $date = date('Y/m/d');
        $ts = strtotime($date);
        $dow = date('w', $ts);
        $offset = $dow - 1;

        if ($offset < 0) {
            $offset = 6;
        }

        $ts = $ts - $offset * 86400;

        $fromDate;
        $toDate;

        $weekDates = array();
        for ($i = 0; $i < 7; $i++, $ts += 86400) {
            $newdate = strtotime ( '-1 day' , $ts ) ;
            if($i===0){
                $fromDate = $newdate;
            }
            if($i===6){
                $toDate = $newdate;
            }

            $weekDates[] = date("Y-m-d", $newdate);
        }
        
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

        $i = 6;
        $titles = array(
            "Order ID"
            , "Assigned To"
            , "Order Rating"
            , "Order Comment"
            , "Carrier Rating"
            , "Carreir Comment"
            , "Rated At"
            , "Carrier Information"
        );

        
        $m = 0;
        foreach ($titles as $val) {
            $sht->getCellByColumnAndRow($m, $i)->setValue($val);
            $m++;
        }
            
        $sht->getCellByColumnAndRow(0, 2)->setValue("ID");
        $sht->getCellByColumnAndRow(1, 2)->setValue("XX");
        $sht->getCellByColumnAndRow(2, 2)->setValue("YY");
        $sht->getCellByColumnAndRow(3, 2)->setValue("ZZ");
        $sht->getCellByColumnAndRow(4, 2)->setValue("");
        $sht->getCellByColumnAndRow(5, 2)->setValue("");
        $sht->getStyleByColumnAndRow(5, 2)->getAlignment()->setValue("");
        $sht->getCellByColumnAndRow(6, 2)->setValue("");
        $sht->getCellByColumnAndRow(7, 2)->setValue("");
        $this->outputExcel($excl, "reviewReport");
        
        /**
         * getting on to listing page again
         */
        $this->idx();
    }

    function pending_dispatch()
    {
        $parentId = $_SESSION['member']['parent_id'];
        $this->breadcrumbs = $this->getBreadCrumbs("Pending Dispatches");
        $this->tplname = "orders.PendingDispatch.list";

        $this->applyPager("app_pending_dispatches", "", "WHERE `deleted_at` IS NULL AND parent_id = ".$parentId);
        $this->applyOrder("app_pending_dispatches");
        $sql = "SELECT * FROM app_pending_dispatches WHERE `deleted_at` IS NULL AND parent_id = ".$parentId." ORDER BY created_at ASC ";
        $this->get_grid_data($sql);
    }

    function delete_pending_dispatch()
    {
        $id = explode("/",$_GET['url']);
        $ID =$id[3];
        
        $query = "DELETE FROM `app_pending_dispatches` WHERE `entity_id`= '".$ID."' ";
        $this->daffny->DB->query($query);

        $query = "UPDATE `app_entities` SET is_pending_dispatch = 0 WHERE `id`= '".$ID."' ";
        $this->daffny->DB->query($query);

        redirect(getLink("wallboards","pending_dispatch"));
    }

}
