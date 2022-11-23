<?php

// model to deal with orders operation
class Orders extends CI_Model
{
    // default constructor
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
        $this->load->model('payments');
    }

    // get vehicles data based on entity
    public function get_vehicles($entity_id)
    {
        $sql = "SELECT * FROM `app_vehicles` WHERE `entity_id` = {$entity_id}";
        $query = $this->db->query($sql);
        $vehicles = $query->result();

        return $vehicles;
    }

    // get vehicles data based on entity
    public function get_notes($entity_id)
    {
        $sql = "SELECT * FROM `app_notes` WHERE `entity_id` = {$entity_id}";
        $query = $this->db->query($sql);
        $notes = $query->result();

        return $notes;
    }

    public function mark_synced($ids)
    {
        $sql = "UPDATE app_order_header SET synced = 1 WHERE entityid IN ($ids)";
        $res = $this->db->query($sql);

        return $res;
    }

    // get order data on parent id
    public function getOrderData($entity_id, $account_id)
    {
        $order_details = array();

        if (is_numeric($entity_id)) {
            $sql = "SELECT * FROM `app_order_header` WHERE `entityid` = {$entity_id} AND account_id = {$account_id}";
            $query = $this->db->query($sql);
            $from_app_order_header = $query->result_array();

            if (count($from_app_order_header) > 0) {
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

                $sql = "SELECT * FROM `app_notes` WHERE entity_id = {$entity_id} AND  `sender_customer_portal` != 0";
                $query = $this->db->query($sql);
                $from_app_notes = $query->result_array();

                $sql = "SELECT * FROM `app_vehicles` WHERE entity_id = {$entity_id}";
                $query = $this->db->query($sql);
                $from_app_vehicles = $query->result_array();

                $notes_data = array();
                foreach ($from_app_notes as $notes) {
                    $notes_data[] = $notes;
                }

                $vehicles_data = array();
                foreach ($from_app_vehicles as $vehicles) {
                    $vehicles_data[] = $vehicles;
                }

                $order_details['order_data'] = $from_app_order_header[0];
                $order_details['shipper_data'] = $from_app_shippers[0];
                $order_details['origin_data'] = $from_app_locations_origin[0];
                $order_details['destination_data'] = $from_app_location_destination[0];
                $order_details['notes_data'] = $notes_data;
                $order_details['vehicles_data'] = $vehicles_data;
            }
        } else {
            $order_details['error'] = Response::NON_INTEGER_VALUE;
        }

        return $order_details;
    }

    /**
     * Function to return all record count on the basis of some wheres.
     *
     * @param int $account_id
     * @param int $status
     *
     * @return int
     */
    public function get_orders_count($account_id, $status)
    {
        $sql = 'SELECT count(*) as `all_orders` FROM `app_order_header`'
        ." WHERE `account_id` = {$account_id} AND `type` = '".self::ENTITY_TYPE."' AND  `status` = {$status} ";

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     * Function used to pull the order listings on the basis of account id and
     * passed status from app_order_header.
     *
     * @param int $userId account id of a shipper
     * @param int $status Status of an order
     *
     * @author Chetu Inc.
     *
     * @return array Order listings
     */
    public function get_orders($owner_id, $synced, $num_records, $from_date, $to_date)
    {
        $DATE_TYPE = array(
            1 => 'Estimated',
            2 => 'Exactly',
            3 => 'Not Earlier Than',
            4 => 'Not Later Than',
        );

        $BALANCE_PAID_BY = array(
            'COD_COP' => array(2, 3, 8, 9),
            'BILLING' => array(12, 13, 20, 21, 24),
            'INVOICE' => array(14, 15, 22, 23),
        );

        if ($num_records > 3000) {
            $num_records = 3000;
        }

        $fields = ' `aoh`.`ship_via`, `aoh`.`synced`,
         `aoh`.`customer_balance_paid_by` as `payment_information`,
         `aoh`.`account_id`, `aoh`.`carrier_id`,`aoh`.`payments_terms`, `ent`.`delivery_credit`,
         `aoh`.`distance`,`aoh`.`total_tariff`,`aoh`.`total_carrier_pay`,
         `aoh`.`total_deposite`,`aoh`.`status`,`aoh`.`entityid`,`aoh`.`number`,
         `aoh`.`prefix`,`aoh`.`balance_paid_by`,`aoh`.`esigned`,`aoh`.`Origincity`,
         `aoh`.`Originstate`,`aoh`.`Originzip`,`aoh`.`Destinationcity`,
         `aoh`.`Destinationstate`,`aoh`.`Destinationzip`,`aoh`.`created`, 
         `aoh`.`avail_pickup_date`,`aoh`.`posted`,`aoh`.`archived`,`aoh`.`load_date`,
         `aoh`.`load_date_type`,`aoh`.`delivery_date`,
         `aoh`.`delivery_date_type`,`aoh`.`actual_pickup_date`,`aoh`.`hold_date` ';

        if ($from_date == '' || $to_date == '') {
            $where_date_range = '';
        } else {
            $where_date_range = 'AND ( `aoh`.created >= '.$from_date.' OR `aoh`.created <= '.$to_date.') ';
        }

        $query = 'SELECT '.$fields.' FROM `app_order_header` as `aoh` INNER JOIN `app_entities` as `ent` ON `aoh`.`entityid` = `ent`.`id` WHERE `aoh`.`parentid` IN ('.$owner_id.") AND `aoh`.`type` = 3 AND `aoh`.`synced` = {$synced} {$where_date_range} LIMIT 0, {$num_records} ";

        $query = $this->db->query($query);
        $result = $query->result_array();

        for ($i = 0; $i < count($result); ++$i) {
            /*
             * NotesCount not working anymore in OrderHeader, so fetching seperately from app notes
             */
            $result[$i]['notesCount'] = $this->GetNotesCount($result[$i]['entityid']);
            $result[$i]['pickup'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
            $result[$i]['delivery'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');

            if ($result[$i]['status'] == 4 || $result[$i]['status'] == 1) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => '1st avil',
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['avail_pickup_date'])),
                    );
                }
                if (strtotime($result[$i]['posted']) > 0) {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'Posted',
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['posted'])),
                    );
                }
            } elseif ($result[$i]['status'] == 3) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => '1st avail',
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['avail_pickup_date'])),
                    );
                }
                if ($result[$i]['archived'] != '') {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'Cancelled',
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['archived'])),
                    );
                }
            } elseif ($result[$i]['status'] == 7 || $result[$i]['status'] == 9) {
                if (strtotime($result[$i]['load_date']) == 0) {
                    $abbr = 'N/A';
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => 'ETA Pickup '.$abbr,
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['load_date'])),
                    );
                } else {
                    $abbr = $result[$i]['load_date_type'] > 0 ? $DATE_TYPE[(int) $result[$i]['load_date_type']] : '';
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => 'ETA Pickup '.$abbr,
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['load_date'])),
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = 'N/A';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'ETA Delivery '.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? $DATE_TYPE[(int) $result[$i]['delivery_date_type']] : '';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'ETA Delivery '.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                }
            } elseif ($result[$i]['status'] == 5 || $result[$i]['status'] == 6) {
                if (strtotime($result[$i]['load_date']) == 0) {
                    $abbr = 'N/A';
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => 'ETA Pickup '.$abbr,
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['load_date'])),
                    );
                } else {
                    $abbr = $result[$i]['load_date_type'] > 0 ? $DATE_TYPE[(int) $result[$i]['load_date_type']] : '';
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => 'ETA Pickup '.$abbr,
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['load_date'])),
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = 'N/A';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'ETA Delivery '.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? $DATE_TYPE[(int) $result[$i]['delivery_date_type']] : '';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'ETA Delivery '.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                }
            } elseif ($result[$i]['status'] == 8) {
                if (strtotime($result[$i]['actual_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => 'Pickup ',
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['actual_pickup_date'])),
                    );
                }

                if (strtotime($result[$i]['delivery_date']) == 0) {
                    $abbr = 'N/A';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => ''.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                } else {
                    $abbr = $result[$i]['delivery_date_type'] > 0 ? $DATE_TYPE[(int) $result[$i]['delivery_date_type']] : '';
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => ''.$abbr,
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['delivery_date'])),
                    );
                }
            } elseif ($result[$i]['status'] == 2) {
                if (strtotime($result[$i]['avail_pickup_date']) > 0) {
                    $result[$i]['pickup'] = array(
                        'pickupStatus' => '1st avail ',
                        'pickupDate' => date('m/d/y', strtotime($result[$i]['avail_pickup_date'])),
                    );
                }

                if ($result[$i]['hold_date'] != '') {
                    $result[$i]['delivery'] = array(
                        'deliveryStatus' => 'Hold ',
                        'deliveryDate' => date('m/d/y', strtotime($result[$i]['hold_date'])),
                    );
                }
            } else {
                $result[$i]['pickup'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
                $result[$i]['delivery'] = array('deliveryStatus' => 'N/A', 'deliveryDate' => 'N/A');
            }

            $amountToCompany = $this->payments->getFilteredPaymentsTotals($result[$i]['entityid'], Payments::SHIPPER, Payments::COMPANY);
            $amountFromCompany = $this->payments->getFilteredPaymentsTotals($result[$i]['entityid'], Payments::COMPANY, Payments::CARRIER);

            $depositeColor = $this->checkDepositColor($amountToCompany, $result[$i]['total_deposite'], $result[$i]['balance_paid_by']);
            $tariffColor = $this->checkTariffColor($amountToCompany, $result[$i]['total_tariff'], $result[$i]['balance_paid_by']);
            $carrierPayColor = $this->checkCarrierPayColor($amountFromCompany, $result[$i]['total_carrier_pay'], $result[$i]['balance_paid_by']);

            $result[$i]['paymentInformation']['total_tariff'] = array('color' => $tariffColor, 'amount' => $result[$i]['total_tariff']);
            $result[$i]['paymentInformation']['carrier_pay'] = array('color' => $carrierPayColor, 'amount' => $result[$i]['total_carrier_pay']);
            $result[$i]['paymentInformation']['deposite'] = array('color' => $depositeColor, 'amount' => $result[$i]['total_deposite']);
        }

        return $result;
    }

    // function to reset synced orders
    public function reset_synced($owner_id){
        $query = "UPDATE app_order_header SET synced = 0 WHERE parentid = ".$owner_id;
        $this->db->query($query);
        return true;
    }

    // Function to pull all synced orders
    public function get_synced_count($owner_id)
    {
        $query = 'SELECT count(`aoh`.`entityid`) as `AllSynced` FROM `app_order_header` as `aoh` INNER JOIN `app_entities` as `ent` ON `aoh`.`entityid` = `ent`.`id` WHERE `aoh`.`parentid` IN ('.$owner_id.') AND `aoh`.`type` = 3  AND `aoh`.synced = 1';
        $query = $this->db->query($query);
        $result = $query->result_array();

        return $result[0]['AllSynced'];
    }

    // Function to pull all un-synced orders
    public function get_unsynced_count($owner_id)
    {
        $query = 'SELECT count(`aoh`.`entityid`) as `AllUnSynced` FROM `app_order_header` as `aoh` INNER JOIN `app_entities` as `ent` ON `aoh`.`entityid` = `ent`.`id` WHERE `aoh`.`parentid` IN ('.$owner_id.') AND `aoh`.`type` = 3  AND `aoh`.synced = 0';
        $query = $this->db->query($query);
        $result = $query->result_array();

        return $result[0]['AllUnSynced'];
    }

    public function get_associated_members($parent_id)
    {
        $sql = 'SELECT id FROM members WHERE parent_id = '.$parent_id;
        $query = $this->db->query($sql);
        $result = $query->result_array();

        return $result;
    }

    private function GetNotesCount($EntityID)
    {
        $query = "SELECT count(*) as `NotesCount` FROM  `app_notes` WHERE `entity_id` = '".$EntityID."' AND `sender_customer_portal` !=0 ";
        $query = $this->db->query($query);
        $query = $query->result_array();

        return $query[0]['NotesCount'];
    }

    /**
     * Function to fetch the vehicle information on the basis of entity id from
     * vehicle table.
     *
     * @author Chetu Inc.
     *
     * @param int $entityid
     *
     * @return array vehicle information array
     */
    public function getVehicles($entityid)
    {
        $queyr = "SELECT * FROM ".self::VEHICLE_TABLE." WHERE `entity_id` - ".$entity_id;
        $query = $this->db->query($query);
        return $query->result_array();
    }

    /**
     * Function to check deposit amount color.
     *
     * @author Chetu Inc.
     *
     * @param float $amount   amount paid
     * @param float $deposite actual amount to be paid
     *
     * @return string $color amount color
     */
    private function checkDepositColor($amount, $deposite, $balancePaidBy)
    {
        $DATE_TYPE = array(
            1 => 'Estimated',
            2 => 'Exactly',
            3 => 'Not Earlier Than',
            4 => 'Not Later Than',
        );

        $BALANCE_PAID_BY = array(
            'COD_COP' => array(2, 3, 8, 9),
            'BILLING' => array(12, 13, 20, 21, 24),
            'INVOICE' => array(14, 15, 22, 23),
        );

        $AMOUNT_COLOR = array( "red","green","black");

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['COD_COP']) || in_array($balancePaidBy, $BALANCE_PAID_BY['BILLING'])) {
            if (($amount < $deposite)) {
                return $AMOUNT_COLOR[0];
            }
            if (($amount > $deposite) || ($amount == $deposite)) {
                return $AMOUNT_COLOR[1];
            }
        }

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['INVOICE'])) {
            return $AMOUNT_COLOR[0];
        }
    }

    /**
     * Function to check tariff amount color.
     *
     * @author Chetu Inc.
     *
     * @param float $amount amount paid
     * @param float $tariff actual amount to be paid
     *
     * @return string $color amount color
     */
    private function checkTariffColor($amount, $tariff, $balancePaidBy)
    {
        $DATE_TYPE = array(
            1 => 'Estimated',
            2 => 'Exactly',
            3 => 'Not Earlier Than',
            4 => 'Not Later Than',
        );

        $BALANCE_PAID_BY = array(
            'COD_COP' => array(2, 3, 8, 9),
            'BILLING' => array(12, 13, 20, 21, 24),
            'INVOICE' => array(14, 15, 22, 23),
        );

        $AMOUNT_COLOR = array( "red","green","black");

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['COD_COP'])) {
            return $AMOUNT_COLOR[2];
        }

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['BILLING'])) {
            if (($amount < $tariff)) {
                return $AMOUNT_COLOR[0];
            }
            if (($amount > $tariff) || ($amount == $tariff)) {
                return $AMOUNT_COLOR[1];
            }
        }

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['INVOICE'])) {
            return $AMOUNT_COLOR[2];
        }
    }

    /**
     * Function to check tariff amount color.
     *
     * @author Chetu Inc.
     *
     * @param float $amount     amount paid
     * @param float $carrierPay actual amount to be paid
     *
     * @return string $color amount color
     */
    private function checkCarrierPayColor($amount, $carrierPay, $balancePaidBy)
    {
        $DATE_TYPE = array(
            1 => 'Estimated',
            2 => 'Exactly',
            3 => 'Not Earlier Than',
            4 => 'Not Later Than',
        );

        $BALANCE_PAID_BY = array(
            'COD_COP' => array(2, 3, 8, 9),
            'BILLING' => array(12, 13, 20, 21, 24),
            'INVOICE' => array(14, 15, 22, 23),
        );
        $AMOUNT_COLOR = array( "red","green","black");

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['COD_COP'])) {
            return $AMOUNT_COLOR[2];
        }

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['BILLING'])) {
            if (($amount < $carrierPay)) {
                return $AMOUNT_COLOR[0];
            }
            if (($amount > $carrierPay) || ($amount == $carrierPay)) {
                return $AMOUNT_COLOR[1];
            }
        }

        if (in_array($balancePaidBy, $BALANCE_PAID_BY['INVOICE'])) {
            return $AMOUNT_COLOR[2];
        }
    }

    /**
     * Function to pull all order history related information.
     *
     * @author Chetu Inc.
     *
     * @version 1.0
     *
     * @return array
     */
    public function get_history($entity_id)
    {
        $sql = 'SELECT * FROM '.self::HISTORY_TABLE." where `entity_id` = {$entity_id}";
        $query = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    public function get_track_trace($entity_id, $account_id = null)
    {
        $sql = 'SELECT * FROM '.self::TRACK_TRACE_TABLE." where `entity_id` = {$entity_id}";
        $query = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    /**
     * Functionality to get pending order amount based on account_id.
     *
     * @author Shahrukh
     *
     * @version 1.0
     */
    public function CustomerOweAmount($AccountID, $EntityID)
    {
        $Fields = ' `total_tariff_stored`, `carrier_pay_stored`, `balance_paid_by` ';
        $Query = "SELECT {$Fields} FROM `app_order_header` WHERE `account_id` = {$AccountID} AND `entityid` = {$EntityID}";

        $Query = $this->db->query($Query);
        $Result = $Query->result();

        $bpd = $Result[0]->balance_paid_by;
        $Tariff = $Result[0]->total_tariff_stored;
        $CarrierPay = $Result[0]->carrier_pay_stored;

        $cod_type = array(2, 3, 8, 9);
        $billing_type = array(12, 13, 20, 21, 24, 14, 15, 22, 23);

        // net payable when COD
        if (in_array($bpd, $cod_type)) {
            $NetPayable = $Tariff - $CarrierPay;
        }

        // net payable when blling
        if (in_array($bpd, $billing_type)) {
            $NetPayable = $Tariff;
        }

        // calculating amount paid
        $Query = "SELECT SUM(amount) AS `Paid` FROM `app_payments` WHERE `toid` = 1 AND `entity_id` = {$EntityID}";
        $Query = $this->db->query($Query);
        $Result = $Query->result();
        $Paid = $Result[0]->Paid;

        // amount owe to us
        $PendingPayments = $NetPayable - $Paid;

        if ($Paid > $PendingPayments) {
            return array(
                'CreditAmount' => $Paid - $NetPayable,
            );
        } else {
            return array(
                'PendingAmount' => $PendingPayments,
            );
        }
    }
}
