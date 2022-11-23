<?php

/**
 * Class made specifically to deal with the wall boards at generic level
 * 
 * @author  Chetu Inc.
 * @version 1.0
 */
require_once(ROOT_PATH . "/libs/mpdf/mpdf.php");

class AppWallboards extends AppAction{
    
    public function view(){
        try{
            $hash = $_GET['hash'];            
            $this->tplname = "wallboards.board";
            
            $query = "SELECT `id`,`title`,`created_at` FROM `app_wallboards` WHERE `hash` = '".$hash."'";
            $sql = $this->daffny->DB->query($query);
            $data = mysqli_fetch_assoc($sql);
            
            $query = "SELECT `agent_id`,`agent_name` FROM `app_wallboard_agents` WHERE `wallboard_id` = '".$data['id']."'";
            $assignedAgents = $this->daffny->DB->query($query);
                        
            $agents = array();
            $agentIds = array();
            $agentnameAndId = array();
            
            $iterations = 0;
            while($row = mysqli_fetch_assoc($assignedAgents)){
                $agents[] = $row;
                $agentIds[] = $row['agent_id'];
                $agentnameAndId[$iterations]['id'] = $row['agent_id'];
                $agentnameAndId[$iterations]['name'] = $row['agent_name'];
                $iterations++;
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
            
            $dates = array();
            for ($i = 0; $i < 7; $i++, $ts += 86400) {
                $newdate = strtotime ( '-1 day' , $ts ) ;
                if($i===0){
                    $fromDate = $newdate;
                }
                if($i===6){
                    $toDate = $newdate;
                }
                
                $dates[] = date("Y-m-d", $newdate);
            }
            
            $commaSeperatedDates = implode(",",$dates);
            $commaSeperatedAgents = implode(",",$agentIds);
            
            $this->daffny->tpl->data = $data;
            $this->daffny->tpl->fromDate = date("m-d-Y l", $fromDate);
            $this->daffny->tpl->toDate = date("m-d-Y l", $toDate);
            $this->daffny->tpl->weekDates = $commaSeperatedDates;
            $this->daffny->tpl->assignedAgents = $commaSeperatedAgents;
            $this->daffny->tpl->agentnameAndId = $agentnameAndId;       
            
            $this->renderParentLayout = false;   
        } catch(Exception $error){
            print_r($error);
        }        
    }

    public function version2()
    {
        $this->tplname = "wallboards.boardv2";

        $firstday = date('Y-m-d 00:00:00', strtotime("this week"));
        $lastday = date('Y-m-d', strtotime('+7 day', strtotime("this week")));
        $query = "SELECT * FROM `wallboard` WHERE `created_at` >= '".$firstday."' AND  `created_at` < '".$lastday."' ORDER BY created_at DESC";
        $sql = $this->daffny->DB->query($query);

        $data = [];
        while($r = mysqli_fetch_assoc($sql)){
            $data[] = $r;
        }
        
        $wallboard_entries = [];
        foreach ($data as $key => $value) {
            $wallboard_entries[$value['agent_name']][] = $value;
        }

        $this->daffny->tpl->data = $wallboard_entries;
        
        $this->renderParentLayout = false;  
        
    }

    public function getVersion2()
    {
        $firstday = date('Y-m-d 00:00:00', strtotime("this week"));
        $lastday = date('Y-m-d', strtotime('+7 day', strtotime("this week")));
        $query = "SELECT * FROM `wallboard` WHERE `created_at` >= '".$firstday."' AND  `created_at` < '".$lastday."'";
        $sql = $this->daffny->DB->query($query);

        $data = [];
        while($r = mysqli_fetch_assoc($sql)){
            $data[] = $r;
        }
        
        print_r($data);
    }

    public function view_wallboard_instance(){
        try{
            $hash = $_GET['hash'];            
            $this->tplname = "wallboards.board_iframe";
            
            $query = "SELECT `id`,`title`,`created_at` FROM `app_wallboards` WHERE `hash` = '".$hash."'";
            $sql = $this->daffny->DB->query($query);
            $data = mysqli_fetch_assoc($sql);
            
            $query = "SELECT `agent_id`,`agent_name` FROM `app_wallboard_agents` WHERE `wallboard_id` = '".$data['id']."'";
            $assignedAgents = $this->daffny->DB->query($query);
                        
            $agents = array();
            $agentIds = array();
            $agentnameAndId = array();
            
            $iterations = 0;
            while($row = mysqli_fetch_assoc($assignedAgents)){
                $agents[] = $row;
                $agentIds[] = $row['agent_id'];
                $agentnameAndId[$iterations]['id'] = $row['agent_id'];
                $agentnameAndId[$iterations]['name'] = $row['agent_name'];
                $iterations++;
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
            
            $dates = array();
            for ($i = 0; $i < 7; $i++, $ts += 86400) {
                $newdate = strtotime ( '-1 day' , $ts ) ;
                if($i===0){
                    $fromDate = $newdate;
                }
                if($i===6){
                    $toDate = $newdate;
                }
                
                $dates[] = date("Y-m-d", $newdate);
            }
            
            $commaSeperatedDates = implode(",",$dates);
            $commaSeperatedAgents = implode(",",$agentIds);
            
            $this->daffny->tpl->data = $data;
            $this->daffny->tpl->fromDate = date("m-d-Y l", $fromDate);
            $this->daffny->tpl->toDate = date("m-d-Y l", $toDate);
            $this->daffny->tpl->weekDates = $commaSeperatedDates;
            $this->daffny->tpl->assignedAgents = $commaSeperatedAgents;
            $this->daffny->tpl->agentnameAndId = $agentnameAndId;       
            
            $this->renderParentLayout = false;   
        } catch(Exception $error){
            print_r($error);
        }        
    }

    public function view_pending_dispatch()
    {
        try{
            
            $hash = $_GET['hash'];
            $parent = explode("-",$hash)[1];

            $this->tplname = "wallboards.pending_dispatch";

            $query = "SELECT * FROM `app_pending_dispatches` WHERE `parent_id` = '".$parent."'";
            $wallboards = $this->daffny->DB->query($query);
            $data = array();

            while( $r = mysqli_fetch_assoc($wallboards) ){
                $data[] = $r;
            }
            $this->daffny->data = $data;
            $this->daffny->parent = $parent;
            $this->renderParentLayout = false; 

        } catch(Exception $e){
            die($e);
        }
    }

    public function pending_dispatch()
    {
        try{
            
            $hash = $_GET['hash'];
            $parent = explode("-",$hash)[1];

            $this->tplname = "wallboards.live";

            $query = "SELECT * FROM `app_pending_dispatches` WHERE `parent_id` = '".$parent."'";
            $wallboards = $this->daffny->DB->query($query);
            $data = array();

            while( $r = mysqli_fetch_assoc($wallboards) ){
                $data[] = $r;
            }
            $this->daffny->data = $data;
            $this->daffny->parent = $parent;

        } catch(Exception $e){
            die($e);
        }
    }
    
    public function udpate_pending_dispatch()
    {
        $parentId = $_POST['parent'];
        $query = "SELECT *, NOW() as `now` FROM `app_pending_dispatches` WHERE `parent_id` = '".$parentId."'";

        $wallboards = $this->daffny->DB->query($query);
        $data = array();

        $i = 0;
        while( $r = mysqli_fetch_assoc($wallboards) ){
            $data[$i]['id'] = $r['id'];
            $data[$i]['order_id'] = $r['order_id'];
            $data[$i]['entity_id'] = $r['entity_id'];
            $data[$i]['parent_id'] = $r['parent_id'];
            $data[$i]['comment'] = $r['comment'];
            $data[$i]['creator_id'] = $r['creator_id'];
            $data[$i]['creator_name'] = $r['creator_name'];
            $data[$i]['carrier_name'] = $r['carrier_name'];
            $data[$i]['carrier_contact'] = $r['carrier_contact'];
            $data[$i]['carrier_email'] = $r['carrier_email'];
            $data[$i]['carrier_phone'] = $this->format_phone_us($r['carrier_phone']);
            $start_date = new DateTime($r['created_at']);
            $since_start = $start_date->diff(new DateTime($r['now']));
            $data[$i]['created_at'] = str_pad($since_start->h, 2, '0', STR_PAD_LEFT).":".str_pad($since_start->i, 2, '0', STR_PAD_LEFT).":".str_pad($since_start->s, 2, '0', STR_PAD_LEFT);
            $data[$i]['updated_at'] = $r['updated_at'];
            $data[$i]['deleted_at'] = $r['deleted_at'];
            $i++;
        }

        echo json_encode($out = array(
            'success' => true,
            'data' => $data
        ));die;
    }

    private function format_phone_us($phone)
    {
        // note: making sure we have something
        if (!isset($phone{3})) {return '';}
        // note: strip out everything but numbers
        $phone = preg_replace("/[^0-9]/", "", $phone);
        $length = strlen($phone);
        switch ($length) {
            case 7:
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
                break;
            case 10:
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
                break;
            case 11:
                return preg_replace("/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/", "$1($2) $3-$4", $phone);
                break;
            default:
                return $phone;
                break;
        }
    }

    public function exportToExcel()
    {
        echo "this is export to excel function";
    }

    public function test()
    {
        try{
            
            $hash = $_GET['hash'];
            $parent = explode("-",$hash)[1];

            $this->tplname = "wallboards.responsive-list";

            $query = "SELECT * FROM `app_pending_dispatches` WHERE `parent_id` = '".$parent."'";
            $wallboards = $this->daffny->DB->query($query);
            $data = array();

            while( $r = mysqli_fetch_assoc($wallboards) ){
                $data[] = $r;
            }
            $this->daffny->data = $data;
            $this->daffny->parent = $parent;

        } catch(Exception $e){
            die($e);
        }
    }
}