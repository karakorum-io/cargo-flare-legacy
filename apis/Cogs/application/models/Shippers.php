<?php
/**
 * Shipper Model
 *
 * This is the Model for all the operations related to the Members.
 *
 * @category	Controller
 * @author      Chetu
 * @link	https://www.chetu.com/
 */

class Shippers extends CI_Model {
    
    /*
     * This is default function of the model which loads with all the function
     * in the model
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function __construct(){
        parent::__construct();{
            $this->load->helper('url');
            $this->load->library('form_validation');
            $this->load->database();
            $this->load->library('email');
        }
    }
    
    /*
     * This is the to insert data in the database related to Members Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function insert($table, $data){
        $this->db->insert($table,$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This is the to delete data in the database related to Members Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function delete($table, $val){
        $result = $this->db->query("DELETE from `{$table}` WHERE `key`='{$val}'");
        return $result;
    }
    
    /**
     * This is the to get data in the database related to Members Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function select($table, $data){
        foreach ($data as $key => $value){
            $this->db->where($key, $value);
        }
        $query = $this->db->get($table);
        return $query->result_array();
    }
    
    /**
     * This is the to get data in the database related to Members Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function selectAllWithLimits($table, $limit1, $limit2){
        
        $query = $this->db->query("SELECT * FROM `{$table}` LIMIT {$limit1}, {$limit2} ");        
        return $query->result_array();
        
    }
    
    /**
     * This is the to get data in the database related to Shippers Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function selectNumRows($table, $data){
        
        foreach ($data as $key => $value){
            $this->db->where($key, $value);
        }
        $query = $this->db->get($table);
        
        return $query->result_id->num_rows;
        
    }
    
    /**
     * This is the to get data in the database related to Shippers Controller
     * 
     * @author          Chetu
     * @lastUpdateDate  16112017
     * @return          Nothing
     */
    public function update($table, $id, $field, $data ) {
        
        $this->db->where($id, $field);
        $this->db->update($table, $data);
        
    }
    
    /**
     * This function to get user id from api key
     * 
     * @author      Chetu
     * @output      Nothing
     * @lastUpdated 16112017
     */
    function getUserId($apiKey){
        
        $query = $this->db->query("SELECT `user_id` FROM `api_keys` WHERE `key`='{$apiKey}'");
        $result = $query->result();
        return $result[0]->user_id;
        
    }
    
    /**
     * This function to get member id of shipper
     * 
     * @author      Chetu
     * @return      Member id
     * @lastUpdated 30112017
     */
    function getMemberId($apiKey){
        
        //        $query = $this->db->query("SELECT `id` FROM `members` WHERE `key`='{$apiKey}'");
        //        $result = $query->result();
        //        return $result[0]->id;
        
    }
    
    /**
     * This function to get access key
     * 
     * @author      Chetu
     * @output      Nothing
     * @lastUpdated 22112017
     */
    function accessKey(){
        
        $query = $this->db->query("SELECT `accessKey` FROM `api_access_key` WHERE `status`=1");
        $result = $query->result();       
        return $result[0]->accessKey;        
    }
    
    /**
     * This function / action to validate user every time when user is logged in 
     * 
     * @author      Chetu
     * @output      Nothing
     * @lastUpdated 16112017
     */
    public function checkLogin($apiKey){
                 
        /* Validating API Key */
        $result = $this->selectNumRows('api_keys',array('key'=>$apiKey));
        
        if($result){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Functionality linked to database in order to pull the information related to
     * entity count
     * 
     * @author shahrukh charlie
     * @param Array
     * @return Array
     * @version 1.0
     */
    function FetchEntityCounts($Params){
        
        $AccountID = $Params['account_id'];
        $Type = $Params['type'];

        $Query = "SELECT".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 1 and type = {$Type}) AS `MyOrders`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 2 and type = {$Type}) AS `OnHold`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 3 and type = {$Type}) AS `Cancelled`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 4 and type = {$Type}) AS `Posted`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 5 and type = {$Type}) AS `NotSigned`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 6 and type = {$Type}) AS `Dispatched`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 7 and type = {$Type}) AS `Issues`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 8 and type = {$Type}) AS `PickedUp`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 9 and type = {$Type}) AS `Delivered`";

        $Query = $this->db->query($Query);
        $Result = $Query->result();

        return $Result;
    }
    
    /**
     * Functionality to pull user profile
     * 
     * @author shahrukh
     * @version 1.0
     */
    public function UserProfile($AccountID){

        $Fields = "company_name, first_name, last_name, phone1, phone2, cell, fax, email, tax_id_num,";
        $Fields .= "address1, address2, city, state, zip_code, country";
        $Query = "SELECT {$Fields} FROM `app_accounts` WHERE `id` = {$AccountID}";

        $Query = $this->db->query($Query);
        $Result = $Query->result();

        return $Result;
    }

    /**
     * Modify user profile API
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function ModifyProfile($Params){
        $ID = $Params['id'];
        
        $Fields = "company_name, first_name, last_name, phone1, phone2, cell, fax, email, tax_id_num,";
        $Fields .= "address1, address2, city, state, zip_code, country";
        $Query = "SELECT {$Fields} FROM `app_accounts` WHERE `id` = {$ID}";
        
        $Query = $this->db->query($Query);
        $OldData = $Query->result();

        $Modifications =" `company_name` = '".$Params['company_name']."', `first_name` = '".$Params['first_name']."', last_name = '".$Params['last_name'];
        $Modifications .="',`phone1` = '".$Params['phone1']."', `phone2` = '".$Params['phone2']."', `fax` = '".$Params['fax']."', cell = '".$Params['cell']."', tax_id_num = '".$Params['tax_id_num']."' ";
        $Modifications .=",`address1` = '".$Params['address1']."', `address2` = '".$Params['address2']."', city = '".$Params['city'];
        $Modifications .= "', `state` = '".$Params['state']."', `zip_code` = '".$Params['zip_code']."', country = '".$Params['country']."'";
        $Query = "UPDATE `app_accounts` SET {$Modifications} WHERE `id` = {$ID} ";

        $result = $this->db->query($Query);

        unset($Params['id']);
        /**
         * Log change history
         */
        $data = array(
            "AccountID" => $ID,
            "OlderValues" => json_encode($OldData[0]) ,
            "UpdatedValues" => json_encode($Params)
        );
        $this->db->insert('CP_USER_PROFILE_HISTORY', $data);

        return $result;
    }

    /**
     * Functionality to pull profile update history based on account id
     * 
     * @author shahrukh
     * @version 1.0
     */
    public function GetHistory($ID){

        $Query = "SELECT * FROM CP_USER_PROFILE_HISTORY WHERE AccountID = {$ID}";
        $Query = $this->db->query($Query);

        $Result = $Query->result();

        return $Result[0];
    }

    /**
     * Functionality to pull Agent Contact information from Database
     * 
     * @param   $AccountID
     * @param   $EntityID
     * @author  Shahrukh
     * @version 1.0
     */
    public function GetAgentInformation($AccountID, $EntityID){
        $Query = "SELECT `assigned_id` FROM `app_order_header` WHERE `entityid` = {$EntityID}";
        $QueryData = $this->db->query($Query);
        $QueryData = $QueryData->result();
        $AssignedID = $QueryData[0]->assigned_id;

        $Query = "SELECT `parent_id`,`contactname`,`email`,`phone` FROM `members` WHERE id = {$AssignedID}";
        $QueryData = $this->db->query($Query);
        $QueryData = $QueryData->result();

        $ParentID = $QueryData[0]->parent_id;
        $EMail = $QueryData[0]->email;
        $Phone = $QueryData[0]->phone;
        $AssignedAgent = $QueryData[0]->contactname;

        $Query = "SELECT `phone_tollfree`,`dispatch_phone`,`address1`,`address2`,`city`,`state`,`zip_code` FROM `app_company_profile` WHERE `owner_id` = {$ParentID}";
        $QueryData = $this->db->query($Query);
        $QueryData = $QueryData->result();
        $TollFree = $QueryData[0]->phone_tollfree;
        $DispatchNumber = $QueryData[0]->dispatch_phone;
        $Address1 = $QueryData[0]->address1;
        $Address2 = $QueryData[0]->address2;
        $City = $QueryData[0]->city;
        $State = $QueryData[0]->state;
        $ZipCode = $QueryData[0]->zip_code;
        
        $CompanyAddress = $Address1.",";

        if($Address2 != ""){
            $CompanyAddress .= $Address2.",";
        }

        $CompanyAddress .= $City.",";
        $CompanyAddress .= $State.",";
        $CompanyAddress .= $ZipCode.",";
        
        return array(
            "AssignedAgent" => $AssignedAgent,
            "TollFree" => $TollFree,
            "DispatchNumber" => $DispatchNumber,
            "DirectDial" => $Phone,
            "EmailAddress" => $EMail,
            "CompanyAddress"=> $CompanyAddress
        );
    }
    
    /**
     * Functionality to obatined saved cards on the basis of account id
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function GetAccountCreditCards($AccountID){
        $result = $this->select("AccountsCCInformation",array("AccountID"=>$AccountID));
        return $result;
    }

    /**
     * Functionality to search entity 
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function search($string,$AccountID){
        $query = "SELECT * FROM `app_order_header` WHERE account_id = ".$AccountID." AND (`type` = 2 OR `type` = 3) AND (`number` LIKE '%".$string."%' OR `prefix` LIKE '%".$string."%' OR `Vehiclevin` LIKE '%".$string."%' OR `Vehiclemake` LIKE '%".$string."%' OR `Vehiclemodel` LIKE '%".$string."%' OR `Vehicletype` LIKE '%".$string."%' ";
        $query .= " OR `Origincity` LIKE '%".$string."%' OR `Originstate` LIKE '%".$string."%' OR `Originzip` LIKE '%".$string."%' ";
        $query .= " OR `Destinationcity` LIKE '%".$string."%' OR `Destinationstate` LIKE '%".$string."%' OR `Destinationzip` LIKE '%".$string."%') ";
        $query = $this->db->query($query);
        $result = $query->result(); 
        return $result;
    }

    /**
     * Functionality to get cmpany logo based on account id
     * 
     * @author Shahrukh
     * @version 1.0
     */
    public function getLogo($account_id){
        
        // getting owner_id
        $sql = "SELECT owner_id FROM app_accounts WHERE id = ".$account_id;
        $query = $this->db->query($sql);
        $result = $query->result();

        // getting parent_id for owner
        $sql = "SELECT parent_id FROM members WHERE id = ". $result[0]->owner_id;
        $query = $this->db->query($sql);
        $result = $query->result();

        // returning company logo
        return array(
            'small_logo' => 'https://freightdragon.com/uploads/company/'. $result[0]->parent_id."_small.jpg",
            'logo' => 'https://freightdragon.com/uploads/company/'. $result[0]->parent_id.".jpg"
        );
    }

    /**
     * Functionality to get quotes count
     * 
     * @author charlie
     * @version 1.0
     */
    public function quotesCount($Params){
        $AccountID = $Params['account_id'];
        $Type = $Params['type'];

        $Query = "SELECT".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` = 1 and type = {$Type}) AS `AwaitingQuotes`,".
        "(SELECT COUNT(*) FROM app_order_header WHERE account_id = {$AccountID} AND `status` IN (21,22) and type = {$Type}) AS `Quotes`";

        $Query = $this->db->query($Query);
        $Result = $Query->result();

        return $Result;
    }

    // get shipper assigned member id and name
    function get_assigned($account_id){
        $sql = "SELECT owner_id FROM app_accounts WHERE id = ".$account_id;
        $query = $this->db->query($sql);
        $owner = $query->result();
        return $owner[0]->owner_id;
    }
}