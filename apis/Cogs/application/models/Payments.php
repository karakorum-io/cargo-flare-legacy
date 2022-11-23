<?php

/**
 * This the model for Payment related operation.
 *
 * @author Chetu Inc.
 *
 * @version 1.0
 *
 * @see www.chetu.com
 */
class Payments extends CI_Model
{
    const TABLE = 'app_payments';
    const COMPANY = 1;
    const SHIPPER = 2;
    const CARRIER = 3;
    const PICKUP = 4;
    const DELIVERY = 5;

    /**
     * Constructor to load the dependencies at the time of controller call.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->library('email');
    }

    /**
     * Function for calculating total amount paid via different modes and authorities.
     *
     * @param int  $entity_id
     * @param int  $from
     * @param int  $to
     * @param bool $format
     *
     * @return int
     */
    public function getFilteredPaymentsTotals($entity_id, $from = null, $to = null, $format = true)
    {
        $where = " `entity_id` = '".(int) $entity_id."' AND `deleted` = 0";

        if (!is_null($from)) {
            $where .= ' AND `fromid` = '.(int) $from;
        }

        if (!is_null($to)) {
            $where .= ' AND `toid` = '.(int) $to;
        }

        $query = 'SELECT `amount` FROM '.self::TABLE.' WHERE '.$where;
        $query = $this->db->query($query);
        $result = $query->result_array();
        $amount = 0;

        for ($i = 0; $i < count($result); ++$i) {
            $amount = $amount + (int) $result[$i]['amount'];
        }

        return $amount;
    }

    /**
     * Functionality to pull payment gateway information from database.
     *
     * @author Shahrukh charlie
     *
     * @version 1.0
     */
    public function GetGateWay($EntityID, $AccountID)
    {
        $Query = "SELECT `parentid` FROM `app_order_header` WHERE `entityid` = '{$EntityID}' AND `account_id` = '{$AccountID}'";
        $Query = $this->db->query($Query);
        $result = $Query->result_array();
        for ($i = 0; $i < count($result); ++$i) {
            $ParentID = (int) $result[$i]['parentid'];
        }

        $Fields = 'paypal_api_username, paypal_api_password, paypal_api_signature, anet_api_login_id, anet_trans_key, gateway_api_username, gateway_api_signature, current_gateway';

        $Query = "SELECT {$Fields} FROM `app_defaultsettings` WHERE owner_id = '{$ParentID}'";
        $Query = $this->db->query($Query);
        $result = $Query->result_array();

        $Information = array();

        for ($i = 0; $i < count($result); ++$i) {
            $CurrentGateWay = (int) $result[$i]['current_gateway'];

            switch ($CurrentGateWay) {
                case 1:
                    $Information = array(
                        'CurrentGateWay' => $CurrentGateWay,
                        'PayPalUserName' => $result[$i]['paypal_api_username'],
                        'PayPalPassword' => $result[$i]['paypal_api_password'],
                        'PayPalSignature' => $result[$i]['paypal_api_signature'],
                    );
                    break;
                case 2:
                    $Information = array(
                        'CurrentGateWay' => $CurrentGateWay,
                        'AnetLoginId' => $result[$i]['anet_api_login_id'],
                        'AnetTransKey' => $result[$i]['anet_trans_key'],
                    );
                    break;
                case 3:
                    $Information = array(
                        'CurrentGateWay' => $CurrentGateWay,
                        'GateWayUserName' => $result[$i]['gateway_api_username'],
                        'GateWaySignature' => $result[$i]['gateway_api_signature'],
                    );
                    break;
            }
        }

        return $Information;
    }

    /**
     * Record process payment data in freighdragon.
     *
     * @author Shahrukh Charlie
     *
     * @version 1.0
     */
    public function RecordProcessPayment($data)
    {
        /**
         * Get count of existing payments.
         */
        $Query = "SELECT COUNT(`number`) AS `PaymentCount` FROM `app_payments` WHERE `entity_id` = {$data['entity_id']} ";

        $Query = $this->db->query($Query);
        $result = $Query->result_array();
        for ($i = 0; $i < count($result); ++$i) {
            $PaymentCount = (int) $result[$i]['PaymentCount'];
        }

        ++$PaymentCount;

        $data['number'] = $PaymentCount;
        $data['fromid'] = 2;
        $data['toid'] = 3;
        $data['cc_exp'] = $data['cc_exp_year'].'-'.$data['cc_exp_month'].'01';

        unset($data['cc_exp_month']);
        unset($data['cc_exp_year']);

        if ($data['cc_type'] == 1) {
            $data['cc_type'] = 'Visa';
        } elseif ($data['cc_type'] == 2) {
            $data['cc_type'] = 'MasterCard';
        } elseif ($data['cc_type'] == 2) {
            $data['cc_type'] = 'Amex';
        } else {
            $data['cc_type'] = 'Discover';
        }

        $this->db->insert(self::TABLE, $data);
        $InsertedID = $this->db->insert_id();

        return $InsertedID;
    }
}
