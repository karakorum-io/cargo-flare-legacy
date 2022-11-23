<?php

/**
 * Quotes Model
 *
 * This is the Model for all the operations related to the Quotes.
 *
 * @category	Controller
 * @author      Chetu
 * @link	https://www.chetu.com/
 */

class Quotes extends CI_Model {
    
    const STATUS = 1;
    const TABLE = 'app_entities';
    const ENTITY_TYPE = 1;
    
    // default constructor
    public function __construct(){
        parent::__construct();{
            $this->load->helper('url');
            $this->load->library('form_validation');
            $this->load->database();
            $this->load->library('email');
        }
    }
    
    // to get next entity number
    function get_next_number($account_id){
        $this->db->select('owner_id');
        $this->db->where('id', $account_id);
        $query = $this->db->get('app_accounts');
        $owner_id = $query->result();
        $owner_id = $owner_id[0]->owner_id;
        
        $sql = "SELECT parent_id FROM members WHERE id = ".$owner_id;
        $query = $this->db->query($sql);
        $owner_id = $query->result();
        $owner_id = $owner_id[0]->parent_id;
        
        $sql = "SELECT max_lead_number FROM app_company_profile WHERE owner_id = ".$owner_id;
        $query = $this->db->query($sql);
        $max_num = $query->result();
        $max_num = $max_num[0]->max_lead_number;
        $sql = "SELECT lead_start_number FROM app_defaultsettings WHERE owner_id = ".$owner_id;
        $query = $this->db->query($sql);
        $start_num = $query->result();
        $start_num = $start_num[0]->lead_start_number;
        $nextNumber = $max_num + $start_num + 1;
        $max_num = $max_num + 1;
        $res = $this->db->query("UPDATE `app_company_profile` SET max_lead_number = {$max_num} WHERE `owner_id` = {$owner_id}");
        return $nextNumber;
    }
    
    /**
     * This is the to insert data in the database related to Quotes
     * 
     * @param Array $data Quotes data array
     * @author          Chetu
     * @lastUpdateDate  29112017
     * @return          inserted id
     */
    public function createQuote($data){
        $this->db->insert('app_entities',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        } else {
            return FALSE;
        }
    }
    
    /**
     * This is the to insert data in the database related to Vehicle in quotes
     * 
     * @param Array $vehicles Vehicles data array
     * @author          Chetu
     * @lastUpdateDate  29112017
     * @return          inserted id
     */
    public function createVehicle($vehicles){
        
        $insertedVehicles = array();
        
        for($i=0;$i< count($vehicles);$i++){
            
            $vehicleData = array(
                'entity_id' => $vehicles[$i]['entity_id'],
                'year'=> $vehicles[$i]['year'],
                'make'=>$vehicles[$i]['make'],
                'model'=>$vehicles[$i]['model'],
                'type'=>$vehicles[$i]['type'],
                'inop'=>$vehicles[$i]['inop']                
            );
            $this->db->insert('app_vehicles',$vehicleData);
            $num = $this->db->insert_id();
            $insertedVehicles[$i]= $num;
        }
        
        if(count($insertedVehicles)>0){
            return $insertedVehicles;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This is the to insert data in the database related to Origin in quotes
     * 
     * @param Array $data Origin data array
     * @author          Chetu
     * @lastUpdateDate  29112017
     * @return          inserted id
     */
    public function createOrigin($data){
        $this->db->insert('app_locations',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This is the to insert data in the database related to destination in quotes
     * 
     * @param Array $data Destination data array
     * @author          Chetu
     * @lastUpdateDate  29112017
     * @return          inserted id
     */
    public function createDestination($data){
        $this->db->insert('app_locations',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This is the to insert data in the database related to destination in quotes
     * 
     * @param Array $data Shipper data array
     * @author          Chetu
     * @lastUpdateDate  29112017
     * @return          inserted id
     */
    public function createShipper($data){
        
        $this->db->insert('app_shippers',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This is the to update data in the database related to entity table
     * 
     * @param Array $data Entity data array
     * @param Int $id Entity Id
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          true/ false
     */
    public function updateEntity($data,$id){
        
        $updateData= "";
        foreach($data as $key => $value){
           $updateData .= $key." = '".$value."',";
        }
        $updateData = substr($updateData, 0, strlen($updateData)-1); 
        
        $result = $this->db->query("UPDATE `app_entities` SET {$updateData}  WHERE `id` = {$id}");
        return $result;
    }
    
    /**
     * This is the to copy entities in app_order_header
     * 
     * @param Int $entityId entity id
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          true/ false
     */
    public function copyInOrderHeader($entityId){
       
        /* calling stored procedure */
        $result = $this->db->query("CALL insert_app_order_header('{$entityId}')");
        return $result;
       
    }
    
    /**
     * This is the to insert followup data for quotes
     * 
     * @param Array $data followup data
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          true/ false
     */
    public function craeteFollowUp($data){
        $this->db->insert('app_followups',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        }        
    }
    
    /**
     * This is the to get first date follow up
     * 
     * @param Int $shipperOwnerId shippers parent/ owner id
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          first date efollow up
     */
    public function getFirstFollowUp($shipperOwnerId){
        
        $sql = "SELECT parent_id FROM members WHERE id = ".$shipperOwnerId;
        $query = $this->db->query($sql);
        $owner_id = $query->result();
        $shipperOwnerId = $owner_id[0]->parent_id;
        
        $query = $this->db->query("SELECT `first_quote_followup` FROM `app_defaultsettings` WHERE `owner_id`='{$shipperOwnerId}'");
        $result = $query->result();
        return $result[0]->first_quote_followup;
        
    }
    
    /**
     * This is the to insert notes in app_notes
     * 
     * @param Array $data notes data
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          true / false
     */
    public function createNotes($data){
        
        $this->db->insert('app_notes',$data);
        $num = $this->db->insert_id();
        if($num){
            return $num;
        }else{
            return FALSE;
        } 
        
    }
    
    /**
     * This is the to create unique hash for quotes
     * 
     * @author          Chetu
     * @lastUpdateDate  30112017
     * @return          true / false
     */
    public function findFreeHash() {
       do {
            $hash = md5(mt_rand() . time());
            //echo "SELECT COUNT(*) FROM ". self::TABLE." WHERE `hash` LIKE '".$hash."'<br>";
            $query = $this->db->query("SELECT COUNT(*) as `count` FROM `app_entities` WHERE `hash` LIKE '{$hash}'");
            $result = $query->result();            
        } while ($result[0]->count != 0);
        
        return $hash;
    }
    
    /**
     * Function to get all order count on the basis or dent status
     * 
     * @param integer $account_id
     * @param integer $status
     */
    public function get_quotes_count($account_id,$status){
        $sql = "SELECT count(*) as `all_quotes` FROM `app_order_header`"
        . " WHERE `account_id` = {$account_id} AND `type` = '" . self::ENTITY_TYPE . "' AND  `status` = {$status} ";
        
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    /**
     * Function used to pull the quotes listings on the basis of account id and
     * passed status from app_order_header
     * 
     * @param int $userId account id of a shipper
     * @param int $status Status of an order
     * @author Chetu Inc.
     * @return Array Order listings
     */
    public function getQuotesList($userId, $status,$from,$to) {

        $data = array(
            'account_id' => $userId,
            'status' => $status,
            'type' => self::ENTITY_TYPE
        );

        $fields = " `status`,`entityid`,`number`,`prefix`,(`NotesCount1`+`NotesCount2`+`NotesCount3`) as `notesCount`,`balance_paid_by`,`esigned`,`vehicleid`,`TotalVehicle`,`Vehicleyear`,`Vehiclemake`,`Vehiclemodel`,`Vehicletype`,`Origincity`,`Originstate`,`Originzip`,`Destinationcity`,`Destinationstate`,`Destinationzip`,`created` ,`avail_pickup_date`,`posted`,`archived`,`load_date`,`load_date_type`,`delivery_date`,`delivery_date_type`,`actual_pickup_date`,`hold_date`,`carrier_pay_stored`,`total_tariff_stored`,(`total_tariff_stored`-`carrier_pay_stored`) as `deposite` ";
        $query = "SELECT " . $fields . " FROM `app_order_header` WHERE `account_id`= '" . $userId . "' AND `type` = '" . self::ENTITY_TYPE . "' AND `status` = '" . $status . "'  LIMIT {$from}, {$to} ";
        $query = $this->db->query($query);
        $result = $query->result_array();

        for ($i = 0; $i < count($result); $i++) {
            $result[$i]['pickup'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
            $result[$i]['delivery'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');

            if ($result[$i]['status'] == 4 || $result[$i]['status'] == 1) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "1st avil",
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['avail_pickup_date']))
                    );
                }
                if (strtotime($result[$i]['posted']) > 0) {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "Posted",
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['posted']))
                    );
                }
            } elseif ($result[$i]['status'] == 3) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "1st avail",
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['avail_pickup_date']))
                    );
                }
                if ($result[$i]['archived'] != "") {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "Cancelled",
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['archived']))
                    );
                }
            } elseif ($result[$i]['status'] == 7 || $result[$i]['status'] == 9) {
                if (strtotime($result[$i]['load_date']) == 0) {
                    $abbr = "N/A";
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "ETA Pickup " . $abbr,
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['load_date']))
                    );
                } else {
                    $abbr = $result[$i]['load_date_type'] > 0 ? self::DATE_TYPE[(int) $result[$i]['load_date_type']] : "";
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "ETA Pickup " . $abbr,
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['load_date']))
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = "N/A";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "ETA Delivery " . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? self::DATE_TYPE[(int) $result[$i]['delivery_date_type']] : "";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "ETA Delivery " . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                }
            } elseif ($result[$i]['status'] == 5 || $result[$i]['status'] == 6) {
                if (strtotime($result[$i]['load_date']) == 0) {
                    $abbr = "N/A";
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "ETA Pickup " . $abbr,
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['load_date']))
                    );
                } else {
                    $abbr = $result[$i]['load_date_type'] > 0 ? self::DATE_TYPE[(int) $result[$i]['load_date_type']] : "";
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "ETA Pickup " . $abbr,
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['load_date']))
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = "N/A";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "ETA Delivery " . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? self::DATE_TYPE[(int) $result[$i]['delivery_date_type']] : "";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "ETA Delivery " . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                }
            } elseif ($result[$i]['status'] == 8) {
                if (strtotime($result[$i]['actual_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "Pickup ",
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['actual_pickup_date']))
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = "N/A";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "" . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? self::DATE_TYPE[(int) $result[$i]['delivery_date_type']] : "";
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "" . $abbr,
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['delivery_date']))
                    );
                }
            } elseif ($result[$i]['status'] == 2) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => "1st avail ",
                        'pickupDate' => date("m/d/y", strtotime($result[$i]['avail_pickup_date']))
                    );
                }

                if ($result[$i]['hold_date'] != "") {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => "Hold ",
                        'deliveryDate' => date("m/d/y", strtotime($result[$i]['hold_date']))
                    );
                }
            } else {
                $result[$i]['pickup'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
                $result[$i]['delivery'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
            }                        
        }
        return $result;
    }
    
    /**
     * Function used to pull detailed information of a quote on the basis of entity id passed
     * 
     * @author Chetu Inc.
     * @version 1.0
     * @response Array containing quotes details
     */
    public function getQuoteData($entity_id, $account_id){
        $quotes_details = array();
        
        if(is_numeric ($entity_id)){
            
            $sql = "SELECT * FROM `app_order_header` WHERE `entityid` = {$entity_id} AND account_id = {$account_id}";
            $query = $this->db->query($sql);
            $from_app_order_header = $query->result_array();
            
            if(count($from_app_order_header)>0){
                
                $shipper_id = $from_app_order_header[0]['shipper_id'];
                $origin_id = $from_app_order_header[0]['origin_id'];
                $destination_id = $from_app_order_header[0]['destination_id'];
                
                $sql = "SELECT * FROM `app_shippers` WHERE id = {$shipper_id}";
                $query = $this->db->query($sql);
                $from_app_shippers = $query->result_array();
                
                $sql = "SELECT * FROM `app_locations` WHERE id = {$origin_id}";
                $query = $this->db->query($sql);
                $from_app_locations_origin = $query->result_array();
                
                $sql = "SELECT * FROM `app_locations` WHERE id = {$destination_id}";
                $query = $this->db->query($sql);
                $from_app_location_destination = $query->result_array();
                
                $sql = "SELECT * FROM `app_notes` WHERE entity_id = {$entity_id}";
                $query = $this->db->query($sql);
                $from_app_notes = $query->result_array();
                
                $sql = "SELECT * FROM `app_vehicles` WHERE entity_id = {$entity_id}";
                $query = $this->db->query($sql);
                $from_app_vehicles = $query->result_array();
                
                $notes_data = array();
                foreach($from_app_notes as $notes){
                    $notes_data[] = $notes;           
                }

                $vehicles_data = array();
                foreach($from_app_vehicles as $vehicles){
                    $vehicles_data[] = $vehicles;           
                }
                
                $quotes_details['quote_data'] = $from_app_order_header[0];
                $quotes_details['shipper_data'] = $from_app_shippers[0];
                $quotes_details['origin_data'] = $from_app_locations_origin[0];
                $quotes_details['destination_data'] = $from_app_location_destination[0];
                $quotes_details['notes_data'] = $notes_data;
                $quotes_details['vehicles_data'] = $vehicles_data;                
            }
            
        } else {
             $quotes_details['error'] = Response::NON_INTEGER_VALUE;
        }
        return $quotes_details;
    }
}