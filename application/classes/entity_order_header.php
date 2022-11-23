<?php

/* * ************************************************************************************************
 * Entity class
 * This class represent one entity
 *
 * Client:		FreightDragon
 * Version:		1.0
 * Date:			2011-11-14
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2011 FreightDragon. - All Rights Reserved 
 * ************************************************************************************************* */

/**
 * @property int $id
 * @property int $type
 * @property string $number
 * @property string $prefix
 * @property int $email_id
 * @property int $source_id
 * @property int $assigned_id
 * @property int $before_assigned_id
 * @property int $carrier_id
 * @property string $received
 * @property int $shipper_id
 * @property int $origin_id
 * @property int $destination_id
 * @property string $distance
 * @property string $est_ship_date
 * @property string $load_date
 * @property int $load_date_type
 * @property string $delivery_date
 * @property int $delivery_date_type
 * @property string $actual_ship_date
 * @property string $avail_pickup_date
 * @property string $actual_pickup_date
 * @property int $vehicles_run
 * @property int $ship_via
 * @property string $referred_by
 * @property string $buyer_number
 * @property string $booking_number
 * @property int $status
 * @property string $status_update
 * @property string $created
 * @property string $quoted
 * @property string $ordered
 * @property string $posted
 * @property string $pickedup
 * @property string $delivered
 * @property string $dispatched
 * @property string $archived
 * @property string $information
 * @property int $include_shipper_comment
 * @property int $balance_paid_by
 * @property int $deleted
 * @property int $blocked_by
 * @property string $blocked_time
 * @property int $is_reimbursable
 * @property string $pickup_terminal_fee
 * @property string $dropoff_terminal_fee
 * @property string $total_tariff_stored
 * @property string $carrier_pay_stored
 * @property int $is_firstfollowup
 * @property string $hash
 */
require_once(ROOT_PATH . "libs/phpmailer/class.phpmailer.php");

class EntityOrderHeader extends FdObject
{

    const TABLE = "app_order_header";

    protected $memberObjects = array();

    const TYPE_LEAD = 1;
    const TYPE_QUOTE = 2;
    const TYPE_ORDER = 3;
    const TYPE_CLEAD = 4;

    const REIMBURSABLE_NO = 0;
    const REIMBURSABLE_YES = 1;

    const FIRSTFOLLOWUPPED_NO = 0;
    const FIRSTFOLLOWUPPED_YES = 1;

    const STATUS_UNREADABLE = -1;
    const STATUS_ACTIVE = 1;
    const STATUS_ONHOLD = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_POSTED = 4;
    const STATUS_NOTSIGNED = 5;
    const STATUS_DISPATCHED = 6;
    const STATUS_ISSUES = 7;
    const STATUS_PICKEDUP = 8;
    const STATUS_DELIVERED = 9;
	
	const STATUS_PRIORITY = 10;
    const STATUS_DEAD = 11;
  
	const STATUS_CPRIORITY = 12;
    const STATUS_CDEAD = 13;
	const STATUS_CASSIGNED = 14;
    const STATUS_CUNREADABLE = 15;
	const STATUS_CONHOLD = 16;
   	const STATUS_CACTIVE = 17;
	const STATUS_CARCHIVED = 18;
	
	const STATUS_ASSIGNED = 19;
	const STATUS_CONVERTED = 20;
	
    const STATUS_LQUOTED = 21;
	const STATUS_LFOLLOWUP = 22;
	const STATUS_LEXPIRED = 23;
	const STATUS_LDUPLICATE = 24;
	const STATUS_LAPPOINMENT = 25;
	
	const STATUS_CQUOTED = 26;
	const STATUS_CFOLLOWUP = 27;
	const STATUS_CEXPIRED = 28;
	const STATUS_CDUPLICATE = 29;
	const STATUS_CAPPOINMENT = 30;
    const DATE_TYPE_ESTIMATED = 1;
    const DATE_TYPE_EXACTLY = 2;
    const DATE_TYPE_NO_EARLY = 3;
    const DATE_TYPE_NO_LATER = 4;

    //const BALANCE_ADDITIONAL = 1;
    const BALANCE_COD_TO_CARRIER_CASH = 2;
    const BALANCE_COD_TO_CARRIER_CHECK = 3;
	const BALANCE_COD_TO_CARRIER_COMCHECK = 16;
	const BALANCE_COD_TO_CARRIER_QUICKPAY = 17;
    //const BALANCE_COD_TO_DTERMINAL_CASH = 4;
    //const BALANCE_COD_TO_DTERMINAL_CHECK = 5;
    //const BALANCE_COD_TO_PTERMINAL_CASH = 6;
    //const BALANCE_COD_TO_PTERMINAL_CHECK = 7;
    const BALANCE_COP_TO_CARRIER_CASH = 8;
    const BALANCE_COP_TO_CARRIER_CHECK = 9;
    const BALANCE_COP_TO_CARRIER_COMCHECK = 18;
    const BALANCE_COP_TO_CARRIER_QUICKPAY = 19;
    //const BALANCE_CARRIER_INVOICE = 10;
    //const BALANCE_INVOICE_CARRIER = 11;

    const BALANCE_COMPANY_OWES_CARRIER_CASH = 12;
    const BALANCE_COMPANY_OWES_CARRIER_CHECK = 13;
    const BALANCE_COMPANY_OWES_CARRIER_COMCHECK = 20;
    const BALANCE_COMPANY_OWES_CARRIER_QUICKPAY = 21;
    const BALANCE_COMPANY_OWES_CARRIER_ACH = 24;

    const BALANCE_CARRIER_OWES_COMPANY_CASH = 14;
    const BALANCE_CARRIER_OWES_COMPANY_CHECK = 15;
    const BALANCE_CARRIER_OWES_COMPANY_COMCHECK = 22;
    const BALANCE_CARRIER_OWES_COMPANY_QUICKPAY = 23;

    const WIRE_TRANSFER = 1;
    const MONEY_ORDER = 2;
    const CREDIT_CARD = 3;
    const PARSONAL_CHECK = 4;
	const COMPANY_CHECK = 5;
    const ACH = 6;
	

    const CENTRAL_DISPATCH_EMAIL_TO = "cdupd-v4@centraldispatch.com";
    //const CENTRAL_DISPATCH_EMAIL_TO = "central@ritewayautotransport.com";

    const TITLE_FIRST_AVAIL = "1st Avail";
    const TITLE_PICKUP_DELIVERY = "Pickup";


    public static $attributeTitles = array(
        'distance' => 'Distance',
        'est_ship_date' => 'Estimate Ship. Date',
        'avail_pickup_date' => 'First Avail. Pickup Date',
//        'vehicles_run' => 'Vehicle(s) Run',
        'ship_via' => 'Ship Via',
        'referred_by' => 'Referred By',
        'status' => 'Status',
        'quoted' => 'Quoted',
        'ordered' => 'Ordered',
        'posted' => 'Posted',
        'dispatched' => 'Dispatched',
        'pickedup' => 'Picked Up',
        'delivered' => 'Delivered',
        'archived' => 'Archived',
        'is_reimbursable' => 'Is Reimbursable',
        'received' => 'Received',
        'information' => 'Information',
        'buyer_number' => 'Buyer Number',
        'booking_number' => 'Booking Number',
        'balance_paid_by' => 'Balance Paid By',
        'pickup_terminal_fee' => 'Pickup Terminal Fee',
        'dropoff_terminal_fee' => 'Dropoff Teminal Fee',
        'include_shipper_comment' => 'Include Shipper Comment',
        'load_date' => 'Load Date',
        'load_date_type' => 'Load Date Type',
        'delivery_date' => 'Delivery Date',
        'delivery_date_type' => 'Delivery Date Type',
        'actual_pickup_date' => 'Actual Pickup Date',
        'actual_ship_date' => 'Actual Ship Date'
    );
    public static $type_name = array(
        self::TYPE_LEAD => "Lead",
        self::TYPE_QUOTE => "Quote",
        self::TYPE_ORDER => "Order",
		self::TYPE_CLEAD => "Created"
    );
    public static $reimbursable_name = array(
        self::REIMBURSABLE_NO => "NO",
        self::REIMBURSABLE_YES => "YES"
    );
    public static $status_name = array(
        self::STATUS_UNREADABLE => "Unreadable",
        self::STATUS_ACTIVE => "Active",
        self::STATUS_ONHOLD => "OnHold",
        self::STATUS_ARCHIVED => "Archived",
        self::STATUS_POSTED => "Posted to FD",
        self::STATUS_NOTSIGNED => "Not Signed",
        self::STATUS_DISPATCHED => "Dispatched",
        self::STATUS_ISSUES => "Issues",
        self::STATUS_PICKEDUP => "Picked Up",
        self:: STATUS_DELIVERED => "Delivered",
		self::STATUS_PRIORITY => "Picked Up",
        self:: STATUS_DEAD => "Delivered",
		
		self::STATUS_CACTIVE => "Active",
        self::STATUS_CONHOLD => "OnHold",
        self::STATUS_CARCHIVED => "Archived",
        self::STATUS_CUNREADABLE => "Unreadable",
        self::STATUS_CASSIGNED => "Assigned",
        self::STATUS_CDEAD => "Dead",
        self::STATUS_CPRIORITY => "Priority",
		self::STATUS_CASSIGNED => "Assigned",
		self::STATUS_ASSIGNED => "Assigned",
		self::STATUS_LQUOTED => "Quoted",
        self::STATUS_LFOLLOWUP => "Follow up",
        self::STATUS_LEXPIRED => "Expired",
		self::STATUS_LDUPLICATE => "Duplicate",
		self::STATUS_LAPPOINMENT => "Appointment",
		
		self::STATUS_CQUOTED => "Quoted",
        self::STATUS_CFOLLOWUP => "Follow up",
        self::STATUS_CEXPIRED => "Expired",
		self::STATUS_CDUPLICATE => "Duplicate",
		self::STATUS_CAPPOINMENT => "Appointment"
	
    );
	
	public static $status_name_ontime = array(
        self::STATUS_DISPATCHED => "Dispatched",
        self::STATUS_ISSUES => "Issues",
        self::STATUS_PICKEDUP => "Picked Up",
        self:: STATUS_DELIVERED => "Delivered"		
    );
	
	public static $status_name_orders = array(
        self::STATUS_ACTIVE => "Active",
        self::STATUS_ONHOLD => "OnHold",
        self::STATUS_ARCHIVED => "Archived",
        self::STATUS_POSTED => "Posted to FD",
        self::STATUS_NOTSIGNED => "Not Signed",
        self::STATUS_DISPATCHED => "Dispatched",
        self::STATUS_ISSUES => "Issues",
        self::STATUS_PICKEDUP => "Picked Up",
        self:: STATUS_DELIVERED => "Delivered"	
    );


    public static $vehicles_run_string = array(
        1 => "No",
        2 => "Yes"
    );
    public static $vehicles_run_string_export = array(
        1 => "inop",
        2 => "operable",
    );
    public static $ship_via_string = array(
        1 => "Open",
        2 => "Enclosed",
        3 => "Driveaway"
    );
    public static $balance_paid_by_string = array(
        //self::BALANCE_ADDITIONAL => "Additional Shipper Pre-payment",
        self::BALANCE_COD_TO_CARRIER_CASH => "COD to Carrier (Cash/Certified Funds)",
        self::BALANCE_COD_TO_CARRIER_CHECK => "COD to Carrier (Check)",
        self::BALANCE_COD_TO_CARRIER_COMCHECK => "COD to Carrier (Comcheck)",
        self::BALANCE_COD_TO_CARRIER_QUICKPAY => "COD to Carrier (QuickPay)",
        //self::BALANCE_COD_TO_DTERMINAL_CASH => "COD to Delivery Terminal (Cash/Certified Funds)",
        //self::BALANCE_COD_TO_DTERMINAL_CHECK => "COD to Delivery Terminal (Check)",
        //self::BALANCE_COD_TO_PTERMINAL_CASH => "COD to Pickup Terminal (Cash/Certified Funds)",
        //self::BALANCE_COD_TO_PTERMINAL_CHECK => "COD to Pickup Terminal (Check)",
        self::BALANCE_COP_TO_CARRIER_CASH => "COP to Carrier (On Pickup) (Cash/Certified Funds)",
        self::BALANCE_COP_TO_CARRIER_CHECK => "COP to Carrier (On Pickup) (Check)",
        self::BALANCE_COP_TO_CARRIER_COMCHECK => "COP to Carrier (On Pickup) (Comcheck)",
        self::BALANCE_COP_TO_CARRIER_QUICKPAY => "COP to Carrier (On Pickup) (QuickPay)",
        //self::BALANCE_CARRIER_INVOICE => "Carrier Invoice",
        //self::BALANCE_INVOICE_CARRIER => "Invoice Carrier",
        self::BALANCE_COMPANY_OWES_CARRIER_CASH => "Company owes Carrier (Cash/Certified Funds)",
        self::BALANCE_COMPANY_OWES_CARRIER_CHECK => "Company owes Carrier (Check)",
        self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK => "Company owes Carrier (Comcheck)",
        self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY => "Company owes Carrier (QuickPay)",
		self::BALANCE_COMPANY_OWES_CARRIER_ACH => "Company owes Carrier (ACH)",
        self::BALANCE_CARRIER_OWES_COMPANY_CASH => "Carrier owes Company (Cash/Certified Funds)",
        self::BALANCE_CARRIER_OWES_COMPANY_CHECK => "Carrier owes Company (Check)",
        self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK => "Carrier owes Company (Comcheck)",
        self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY => "Carrier owes Company (QuickPay)",
    );

    public static $date_type_string = array(
        self::DATE_TYPE_ESTIMATED => "Estimated",
        self::DATE_TYPE_EXACTLY => "Exactly",
        self::DATE_TYPE_NO_EARLY => "Not Earlier Than",
        self::DATE_TYPE_NO_LATER => "Not Later Than"
    );

    public static $date_type_abbr_string = array(
        self::DATE_TYPE_ESTIMATED => "est",
        self::DATE_TYPE_EXACTLY => "ex",
        self::DATE_TYPE_NO_EARLY => "net",
        self::DATE_TYPE_NO_LATER => "nlt"
    );

    public function getNumber()
    {
        $number = "";
        if (trim($this->prefix) != "") {
            $number .= $this->prefix . "-";
        }
        $number .= $this->number;
        return $number;
    }

    public function __get($name)
    {
        if ($name == 'total_tariff' || $name == 'total_deposit') {
            if (!isset($this->attributes[$name]))
                $this->getVehicles();
        }
        return parent::__get($name);
    }

    /**
     * Entity::getTariffToShipper()
     *
     * @return string formatted decimal
     * @throws FDException
     */
    public function getTariffToShipper()
    {
        return number_format($this->tariff_to_shipper, 2, ".", ",");
    }

    /**
     * Entity::getTariffByCustomer()
     *
     * @return string formatted decimal
     * @throws FDException
     */
    public function getTariffByCustomer()
    {
        return number_format($this->tariff_by_customer, 2, ".", ",");
    }

    /**
     * Entity::getAssigned()
     *
     * @param bool $reload
     * @return Member object
     * @throws FDException
     */
    public function getAssigned($reload = false)
    {
        if ($reload || !isset($this->memberObjects['assigned'])) {
            $member = new Member($this->db);
            $member->load($this->assigned_id);
            $this->memberObjects['assigned'] = $member;
        }
        return $this->memberObjects['assigned'];
    }
	
	/**
     * Entity::getCreator()
     *
     * @param bool $reload
     * @return Member object
     * @throws FDException
     */
    public function getCreator($reload = false)
    {
        if ($reload || !isset($this->memberObjects['creator'])) {
            $member = new Member($this->db);
            $member->load($this->creator_id);
            $this->memberObjects['creator'] = $member;
        }
        return $this->memberObjects['creator'];
    }

    /**
     * Entity::getBeforeAssigned()
     *
     * @param bool $reload
     * @return Member object
     * @throws FDException
     */
    public function getBeforeAssigned($reload = false)
    {
        if ($reload || !isset($this->memberObjects['before_assigned'])) {
            $member = new Member($this->db);
            $member->load($this->before_assigned_id);
            $this->memberObjects['before_assigned'] = $member;
        }
        return $this->memberObjects['before_assigned'];
    }

    /**
     * Entity::getShipper()
     *
     * @param bool $reload
     * @return Shipper object
     * @throws FDException
     */
    public function getShipper($reload = false)
    {
        if ($reload || !isset($this->memberObjects['shipper'])) {
            $shipper = new Shipper($this->db);
            $shipper->load($this->shipper_id);
            $this->memberObjects['shipper'] = $shipper;
        }
        return $this->memberObjects['shipper'];
    }

    /**
     * Entity::getAccount()
     *
     * @param bool $reload
     * @return Member Account object
     * @throws FDException
     */
    public function getAccount($reload = false)
    {
		  
	    if ($this->account_id == '0') return null;
	
        if ($reload || !isset($this->memberObjects['account'])) {
			try
			{
                 $account = new Account($this->db);
			
				$account->load($this->account_id);
				$this->memberObjects['account'] = $account;
				
			} catch (FDException $e) {
			    return null;
			}	
		    
        }
		
        return $this->memberObjects['account'];
    }

    /**
     * Entity::getCarrier()
     *
     * @param bool $reload
     * @return Member Carrier object
     * @throws FDException
     */
    public function getCarrier($reload = false)
    {
		  
	    if ($this->carrier_id == '0') return null;
	
        if ($reload || !isset($this->memberObjects['carrier'])) {
			try
			{
                 $carrier = new Account($this->db);
			
				$carrier->load($this->carrier_id);
				$this->memberObjects['carrier'] = $carrier;
				
			} catch (FDException $e) {
			    return null;
			}	
		    
        }
		
        return $this->memberObjects['carrier'];
    }

    /**
     * Entity::getQuoted()
     *
     * @param string $format
     * @return string formatted date
     * @throws FDException
     */
    public function getQuoted($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->quoted));
    }

    /**
     * Entity::getOrdered()
     *
     * @param string $format
     * @return string formatted date
     * @throws FDException
     */
    public function getOrdered($format = "m/d/Y H:i:s")
    {
        $tz = isset($_SESSION['timezone']) ? $_SESSION['timezone'] : 'America/New_York';
        $date = new DateTime($this->ordered, new DateTimeZone($tz));
        return (is_null($this->ordered)) ? "" : gmdate($format, $date->getTimestamp());
    }

    public function getDispatched($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->dispatched));
    }

    public function getArchived($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->archived));
    }

    /**
     * Entity::getDelivered()
     *
     * @param string $format
     * @return string formatted date
     * @throws FDException
     */
    public function getDelivered($format = "m/d/Y H:i:s")
    {
        return (is_null($this->delivered)) ? "" : date($format, strtotime($this->delivered));
    }

    /**
     * Entity::getReceived()
     *
     * @param string $format
     * @return string formatted date
     * @throws FDException
     */
    public function getReceived($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->received));
    }

    /**
     * Entity::getFirstAvail()
     *
     * @param string $format - date format, default is "m/d/Y H:i:s"
     * @return string formatted date and time
     * @throws FDException
     */
    public function getFirstAvail($format = "m/d/Y H:i:s")
    {
        if (strtotime($this->avail_pickup_date) == 0) return "";
        return date($format, strtotime($this->avail_pickup_date));
    }

    public function getLoadDate($format = "m/d/Y")
    {
        if (strtotime($this->load_date) == 0) return "";
        return date($format, strtotime($this->load_date));
    }
	
	public function getPostDate($format = "m/d/Y")
    {
        if (strtotime($this->posted) == 0) return "";
        return date($format, strtotime($this->posted));
    }

    public function getDeliveryDate($format = "m/d/Y")
    {
        if (strtotime($this->load_date) == 0) return "";
        return date($format, strtotime($this->delivery_date));
    }

    public function getLoadDateWithAbbr($format = "m/d/Y")
    {
        if (strtotime($this->load_date) == 0) return "N/A";
        $abbr = $this->load_date_type > 0 ? self::$date_type_string[(int)$this->load_date_type] : "";
        return $abbr . "<br />" . date($format, strtotime($this->load_date));
    }

    public function getDeliveryDateWithAbbr($format = "m/d/Y")
    {
        if (strtotime($this->load_date) == 0) return "N/A";
        $abbr = $this->delivery_date_type > 0 ? self::$date_type_string[(int)$this->delivery_date_type] : "";
        return $abbr . "<br />" . date($format, strtotime($this->delivery_date));
    }

    /**
     * Entity::getShipDate()
     *
     * @param string $format
     * @return string formatted date
     * @throws FDException
     */
    public function getShipDate($format = "m/d/Y H:i:s")
    {
        if (strtotime($this->est_ship_date) == 0) return "";
        return date($format, strtotime($this->est_ship_date));
    }

    /**
     * Entity::getStatusUpdated()
     *
     * @param string $format
     * @return string foramtted date
     * @throws FDException
     */
    public function getStatusUpdated($format = "m/d/Y H:i:s")
    {
        return date($format, strtotime($this->status_update));
    }

    /**
     * Entity::getNotes()
     *
     * @param bool $reload - true if need to reload notes
     * @param null $order
     * @return array of Note objects grouped by type
     */
    public function getNotes($reload = false, $order = null)
    {
        if ($reload || !isset($this->memberObjects['notes'])) {
            $notes = new NoteManager($this->db);
            $this->memberObjects['notes'] = $notes->getNotes($this->id, $order);
        }
        return $this->memberObjects['notes'];
    }
	
	
	public function getNewNotes($reload = false, $order = null)
    {
		  $notesArr = array();
          $notes = new NoteManager($this->db);
          $notesArr = $notes->getNewNotes($this->id, $order);
        
        return $notesArr;
		
    }

    /**
     * Entity::getVehicles()
     *
     * @param bool $reload
     * @return array of Vehicle objects
     * @throws FDException
     */
    public function getVehicles($reload = false)
    {
        if ($reload || !isset($this->memberObjects['vehicles'])) {
            $vehicleManager = new VehicleManager($this->db);
            $vehicles = $vehicleManager->getVehicles($this->id, $this->type);
            $vehicleManager->total_tariff;
//            $cp = (float)$vehicleManager->total_tariff - (float)$vehicleManager->total_deposit + (float)$this->attributes['pickup_terminal_fee'] + (float)$this->attributes['dropoff_terminal_fee'];
            $this->attributes['total_tariff'] = $vehicleManager->total_tariff + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
            $this->attributes['total_deposit'] = $vehicleManager->total_deposit;
            $this->attributes['carrier_pay'] = $vehicleManager->total_carrier_pay;
            $this->memberObjects['vehicles'] = $vehicles;

            $this->update(array("total_tariff_stored" => $vehicleManager->total_tariff));
            $this->update(array("carrier_pay_stored" => $vehicleManager->total_carrier_pay));
        }
        return $this->memberObjects['vehicles'];
    }

    /**
     * Entity::printVehicles()
     * Print Vehicles
     * @param bool $html
     * @return string
     */
    public function printVehicles($html = true)
    {
        $vehicles = $this->getVehicles();
        if (count($vehicles) == 0) {
            $out = "No";
        } elseif (count($vehicles) == 1) {
            $vehicle = $vehicles[0];
            if ($html) {
                $out = $vehicle->make . " " . $vehicle->model . "<br/>";
                $out .= $vehicle->year . " " . $vehicle->type . " " . imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) . ($vehicle->inop ? '<span style="color:#f00">&nbsp;Inop</span>' : '');
            } else {
                $out = $vehicle->make . " " . $vehicle->model . " " . $vehicle->year . " " . $vehicle->type . ($vehicle->inop ? ' Inop' : '');
            }
        } else {
            if ($html) {
                $out = "<span class=\"like-link multi-vehicles\">Multiple Vehicles</span>";
                $out .= "<div class=\"vehicles-info\">";
                foreach ($vehicles as $key => $vehicle) {
                    $out .= "<div " . (($key % 2) ? "style=\"background-color: #161616;padding: 5px;\"" : "style=\"background-color: #000;padding: 5px;\"") . ">";
                    $out .= "<p>" . $vehicle->make . " " . $vehicle->model . "</p>";
                    $out .= $vehicle->year . " " . $vehicle->type . " " . imageLink($vehicle->year . " " . $vehicle->make . " " . $vehicle->model . " " . $vehicle->type) . ($vehicle->inop ? '<span style="color:#f00">&nbsp;Inop</span>' : '');
                    $out .= "<br/></div>";
                }
                $out .= "</div><br/>";
            } else { //if not html
                $out = "";
                foreach ($vehicles as $vehicle) {
                    $out .= $vehicle->make . " " . $vehicle->model . " " . $vehicle->year . " " . $vehicle->type . ($vehicle->inop ? ' Inop' : '');
                }
            }
        }
        return $out;
    }

    /**
     * Entity::getTotalTariff()
     *
     * @param bool $format - if true - return formatted decimal with currency symbol
     * @return string formatted decimal with currency symbol
     * @throws FDException
     */
    public function getTotalTariff($format = true)
    {
        return ($format) ? ("$ " . number_format((float)$this->total_tariff, 2, ".", ",")) : $this->total_tariff;
    }

    /**
     * Entity::getCost()
     * @param bool $format
     * @return string|float formatted decimal with currency symbol
     */
    public function getCost($format = true)
    {
        $cost = $this->getCarrierPay(false) + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
        if ($format) {
            return "$ " . number_format((float)$cost, 2, ".", ",");
        } else {
            return $cost;
        }
    }

    /**
     * Entity::getTotalDeposit()
     *
     * @param bool $format - if true - return formatted decimal with currency symbol
     * @return string|float
     * @throws FDException
     */
    public function getTotalDeposit($format = true)
    {
        return ($format) ? ("$ " . number_format((float)$this->total_deposit, 2, ".", ",")) : $this->total_deposit;
    }


    /**
     * Entity::getPickupTerminalFee()
     *
     * @param bool $format - if true - return formatted decimal with currency symbol
     * @return string|float
     * @throws FDException
     */
    public function getPickupTerminalFee($format = true)
    {
        return ($format) ? ("$ " . number_format((float)$this->pickup_terminal_fee, 2, ".", ",")) : $this->pickup_terminal_fee;
    }

    /**
     * Entity::getDropoffTerminalFee()
     *
     * @param bool $format - if true - return formatted decimal with currency symbol
     * @return string|float
     * @throws FDException
     */
    public function getDropoffTerminalFee($format = true)
    {
        return ($format) ? ("$ " . number_format((float)$this->dropoff_terminal_fee, 2, ".", ",")) : $this->dropoff_terminal_fee;
    }

    /**
     * Entity::getCarrierPay()
     *
     * @param bool $format - if true - return formatted decimal with currency symbol
     * @return string|float
     * @throws FDException
     */
    public function getCarrierPay($format = true)
    {
        return ($format) ? ("$ " . number_format((float)$this->carrier_pay, 2, ".", ",")) : $this->carrier_pay;
    }

    /**
     * Entity::getSource()
     *
     * @param bool $reload - true if need to reload source
     * @return Source object
     * @throws FDException
     */
    public function getSource($reload = false)
    {
        if ($reload || !isset($this->memberObjects['source'])) {
            $source = new Leadsource($this->db);
            $source->load($this->source_id);
            $this->memberObjects['source'] = $source;
        }
        return $this->memberObjects['source'];
    }
	
   public static function getSources($db)
    {
       
        $sourceManager = new LeadsourceManager($db);
		$sources = $sourceManager->get("order by domain asc", null, " `status` =1 ");
        return $sources;
    }
    /**
     * Entity::getOrigin()
     *
     * @param bool $reload - true if need to reload origin
     * @return Origin object
     * @throws FDException
     */
    public function getOrigin($reload = false)
    {
        if ($reload || !isset($this->memberObjects['origin'])) {
            $origin = new Origin($this->db);
            $origin->load($this->origin_id);
            $this->memberObjects['origin'] = $origin;
        }
        return $this->memberObjects['origin'];
    }

    /**
     * Entity::getInopName()
     * for Reports
     * @return Name string
     * @throws FDException
     */
    public function getInopName()
    {
        return self::$vehicles_run_string[$this->vehicles_run];
    }

    /**
     * Entity::getInopExportName()
     * for Export
     * @return string
     * @throws FDException
     */
    public function getInopExportName()
    {
        return self::$vehicles_run_string_export[$this->vehicles_run];
    }

    /**
     * Entity::getDestination()
     *
     * @param bool $reload - true if need to realod destination
     * @return Destination object
     * @throws FDException
     */
    public function getDestination($reload = false)
    {
        if ($reload || !isset($this->memberObjects['destination'])) {
            $destination = new Destination($this->db);
            $destination->load($this->destination_id);
            $this->memberObjects['destination'] = $destination;
        }
        return $this->memberObjects['destination'];
    }

    /**
     * Entity::getShipVia()
     * @return string Ship Via string value
     * @throws FDException
     */
    public function getShipVia()
    {
        return ($this->ship_via != 0) ? self::$ship_via_string[$this->ship_via] : "";
    }

    /**
     * Entity::getReferrers()
     *
     * @param int $member_id - ID of any company member
     * @param mysql $db - instance of Daffny's mysql object
     * @return array of Refferer objects
     * @throws FDException
     */
    public static function getReferrers($member_id, $db)
    {
        $member = new Member($db);
        $member->load($member_id);
        $referrerManager = new ReferrerManager($db);
        $referrers = $referrerManager->get(null, null, "`owner_id` = " . $member->getOwnerId() . " AND `status` = " . Referrer::STATUS_ACTIVE);
        return $referrers;
    }

    /**
     * Entity::getEmail()
     *
     * @return LeadEmail object
     * @throws FDException
     */
    public function getEmail()
    {
        $email = new LeadEmail($this->db);
        $email->load($this->email_id);
        return $email;
    }

    /* SETTERS */

    /**
     * Entity::setStatus()
     *
     * @param mixed $status - Entity status
     * @throws FDException
     */
    public function setStatus($status = null) {
        
        /*
          if ($status == self::STATUS_DELIVERED && !$this->isPaidOff()) {
          $status = self::STATUS_ISSUES;
          //$this->update(array('delivered' => date('Y-m-d H:i:s')));
          $this->update(array('delivered' => date('Y-m-d H:i:s'),'issue_date' => date('Y-m-d H:i:s')));
          }
         */
        if ($status == self::STATUS_DELIVERED) {
            
            if (!$this->isPaidOff()) {
                $status = self::STATUS_ISSUES;
                $this->update(array('issue_date' => date('Y-m-d H:i:s')));
            } else {
                // $this->update(array('delivered'=>date('Y-m-d H:i:s')));
                /* $member = $this->getAssigned();
                  $company = $member->getCompanyProfile();
                  $delivery_confirmation_mail =  $company->delivery_confirmation_mail;
                  if($delivery_confirmation_mail !=""){
                  $mail = new FdMailer(true);
                  $mail->isHTML();
                  $mail->Body = 'Order #'.$this->getNumber().' delivered';
                  $mail->Subject =  'Order #'.$this->getNumber().' delivered';
                  $mail->AddAddress($delivery_confirmation_mail);
                  //$mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
                  $mail->setFrom('noreply@freightdragon.com');
                  $mail->send();
                  }
                 */
            }
        }
       
        if (!array_key_exists($status, self::$status_name))
            throw new FDException("Invalid Status");
        $this->update(array("status" => (int) $status, "pre_status" => $this->status, 'status_update' => date("Y-m-d H:i:s")));

        if ($status == Entity::STATUS_ONHOLD) {
            $this->update(array("hold_date" => date("Y-m-d H:i:s")));
        }

        if ($status == Entity::STATUS_DISPATCHED) {
            $this->update(array("dispatched" => date("Y-m-d H:i:s")));
        }
        if ($status == self::STATUS_ARCHIVED) {
            $this->update(array("archived" => date("Y-m-d H:i:s"), "dispatched" => "NULL", "not_signed" => "NULL"));
        }

        if ($status == self::STATUS_POSTED) {
            $this->update(array("posted" => date("Y-m-d H:i:s")));
            $this->postToCentralDispatch(1);
        } else {
            /*chetu added Id*/
            $parentId = $_SESSION['parent_id'];
            //echo "Parent Id Object".$this->id."<br>";die;
            //if ($status != self::STATUS_ARCHIVED)
            if ($this->getAssigned()->parent_id == $parentId)
                $this->deleteFromCentralDispatch();
        }

        $this->updateHeaderTable();
		
        if ($status == self::STATUS_DELIVERED) {
            $this->sendThankYou();
        }
    }

	/**
     * Entity::setStatusAndDate()
     *
     * @param mixed $status - Entity status
     * @throws FDException
     */
	 /*
    public function setStatusAndDate($status = null,$dateTime = null,$dateStatus = false)
    {
	    

     if (!array_key_exists($status, self::$status_name)) throw new FDException("Invalid Status");
       
	    if ($status == Entity::STATUS_PICKEDUP) {
			if($dateTime==null)
			   $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s")));
			elseif($dateStatus==true)   
			  $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s"),'actual_pickup_date'=>date("Y-m-d", strtotime($dateTime))));
			else
			  $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s")));
		}
		
		if ($status == self::STATUS_DELIVERED && !$this->isPaidOff()) {
		    $status = self::STATUS_ISSUES;
		  
		  
			if($dateTime==null)
			   $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s"),'issue_date' => date('Y-m-d H:i:s')));
			 elseif($dateStatus==true)   
			   $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s"),'delivered'=>date("Y-m-d", strtotime($dateTime)),'issue_date' => date('Y-m-d H:i:s')));
			 else
			   $this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s"),'issue_date' => date('Y-m-d H:i:s'))); 
	    }
		if ($status == self::STATUS_DELIVERED) {
            $this->sendThankYou();
        }
		
		
   }
*/

 public function setStatusAndDate($status = null,$dateTime = null,$dateStatus = false)
    {
	   /*
		if ($status == self::STATUS_DELIVERED && !$this->isPaidOff()) {
		    $status = self::STATUS_ISSUES;
		  
		     if($dateStatus==true)   
			   $this->update(array('delivered'=>date("Y-m-d", strtotime($dateTime)),'issue_date' => date('Y-m-d H:i:s')));
			 else
			   $this->update(array('issue_date' => date('Y-m-d H:i:s'))); 
	    }
		*/
		
		if ($status == self::STATUS_DELIVERED ) {
			if(!$this->isPaidOff()){
		      $status = self::STATUS_ISSUES;
			  $this->update(array("status" => (int)$status,'issue_date' => date('Y-m-d H:i:s'),'delivered'=>date("Y-m-d H:i:s", strtotime($dateTime))));
			           $member = $this->getAssigned();
                       $company = $member->getCompanyProfile();
					   $delivery_confirmation_mail =  $company->delivery_confirmation_mail;
					   if($delivery_confirmation_mail !=""){
			                $mail = new FdMailer(true);
							$mail->isHTML();
							$mail->Body = 'Order #'.$this->getNumber().' delivered';
							$mail->Subject =  'Order #'.$this->getNumber().' delivered';
							$mail->AddAddress($delivery_confirmation_mail);
							//$mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
							//$mail->setFrom('noreply@freightdragon.com');
							
							if($this->getAssigned()->parent_id == 1)
							   $mail->setFrom('noreply@freightdragon.com');
							else
							  $mail->setFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email);
							
							
							$mail->send();
							
					   } 
					   					   
					   if($this->make_payment() ==1 )
					   {
						   $status = self::STATUS_DELIVERED;
						   $this->update(array('delivered'=>date("Y-m-d H:i:s", strtotime($dateTime)),'issue_date' => date('Y-m-d H:i:s')));
						   
					   }
			}
			else{
			  $this->update(array('delivered'=>date("Y-m-d H:i:s", strtotime($dateTime)),'issue_date' => date('Y-m-d H:i:s')));
			   
			}
			    
	    }
		
		
		if (!array_key_exists($status, self::$status_name)) throw new FDException("Invalid Status");
		$this->update(array("status" => (int)$status, 'status_update' => date("Y-m-d H:i:s")));
       
	    if ($status == Entity::STATUS_PICKEDUP) {
			if($dateStatus==true){   
			  $this->update(array('actual_pickup_date'=>date("Y-m-d", strtotime($dateTime))));
			 $this->make_payment(); 
			}
			
		}
		
		$this->updateHeaderTable();
		
		if ($status == self::STATUS_DELIVERED) {
            $this->sendThankYou();
        }
		
		
   }
   
   public function isPaidOffCarrier() {
		$paymentManager = new PaymentManager($this->db);
		$owe = 0;
		switch ($this->balance_paid_by) {
			
			case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
			case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
			case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				
				$cost = 0;
				if($this->getCost(false)==0)
				  $cost = $this->carrier_pay_stored + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
				else
				   $cost = $this->getCost(false);
				 
                
			   $carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false); 
			   $owe = $cost - $carrierPaid;
			  // if($Carrier_owe > 0)
				  // $owe = $Carrier_owe;
				       
				  //print "--".$owe;
				
				break;
			case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
			case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
				$owe += $this->getTotalDeposit(false) - $carrierPaid;
				 //print "==".$owe;
				break;
		}
		return (float)$owe <= 0;
	}
	
	public function isPaidOff() {
		$paymentManager = new PaymentManager($this->db);
		$owe = 0;
		switch ($this->balance_paid_by) {
			case self::BALANCE_COP_TO_CARRIER_CASH:
			case self::BALANCE_COP_TO_CARRIER_CHECK:
			case self::BALANCE_COP_TO_CARRIER_COMCHECK:
			case self::BALANCE_COP_TO_CARRIER_QUICKPAY:
			case self::BALANCE_COD_TO_CARRIER_CASH:
			case self::BALANCE_COD_TO_CARRIER_CHECK:
			case self::BALANCE_COD_TO_CARRIER_COMCHECK:
			case self::BALANCE_COD_TO_CARRIER_QUICKPAY:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				$owe += $this->getTotalDeposit(false) - $shipperPaid;
				break;
			case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
			case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
			case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				
				$cost = 0;
				if($this->getCost(false)==0)
				  $cost = $this->carrier_pay_stored + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
				else
				   $cost = $this->getCost(false);
				 
                //$owe += $this->getCost(false) + $this->getTotalDeposit(false) - $shipperPaid;				 
				$owe += $cost + $this->getTotalDeposit(false) - $shipperPaid;
				
				  if($owe <= 0)
				  {	
					   $carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false); 					   
					   $Carrier_owe = $cost - $carrierPaid;
					   if($Carrier_owe > 0)
					       $owe = $Carrier_owe;
				  }
				  
				
				break;
			case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
			case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
				$owe += $this->getTotalDeposit(false) - $carrierPaid;
				break;
		}
		return (float)$owe <= 0;
	}

		
	public function isPaidOffColor() {
		
		$paymentManager = new PaymentManager($this->db);
		$owe = 0;
		
		$isColor = array(
                'total' => 0,
                'carrier' => 0,
                'deposit' => 0
            );
		
		switch ($this->balance_paid_by) {
			case self::BALANCE_COP_TO_CARRIER_CASH:
			case self::BALANCE_COP_TO_CARRIER_CHECK:
			case self::BALANCE_COP_TO_CARRIER_COMCHECK:
			case self::BALANCE_COP_TO_CARRIER_QUICKPAY:
			case self::BALANCE_COD_TO_CARRIER_CASH:
			case self::BALANCE_COD_TO_CARRIER_CHECK:
			case self::BALANCE_COD_TO_CARRIER_COMCHECK:
			case self::BALANCE_COD_TO_CARRIER_QUICKPAY:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				
				$owe = $this->getTotalDeposit(false) - $shipperPaid;
				if($owe <=0)
				  $isColor['deposit'] = 1;
				else
				  $isColor['deposit'] = 2;
				
				  
				break;
			case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
			case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
			case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
				
				
				$cost = 0;
				if($this->getCost(false)==0)
				  $cost = $this->carrier_pay_stored + $this->getPickupTerminalFee(false) + $this->getDropoffTerminalFee(false);
				else
				   $cost = $this->getCost(false);
				   
				 $owe = $cost - $carrierPaid;  
				//$owe = $this->getCost(false) - $carrierPaid;
				
				if($owe <=0)
				  $isColor['carrier'] = 1;
				else
				  $isColor['carrier'] = 2; 
				  
				$owe = $this->getTotalDeposit(false) - $shipperPaid;
				
				if($owe <=0)
				  $isColor['deposit'] = 1;
				else
				  $isColor['deposit'] = 2;  
				  
				 $owe = $cost + $this->getTotalDeposit(false) - $shipperPaid;
				 if($owe <=0)
				  $isColor['total'] = 1;
				 else
				  $isColor['total'] = 2;
				  
				break;
			case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
			case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
				
				$owe = $this->getTotalDeposit(false) - $carrierPaid;
				
				if($owe <=0)
				  $isColor['deposit'] = 1; 
				else
				  $isColor['deposit'] = 2;

				break;
		}
		return $isColor;
	
	}

public function isPaidOffValue() {
		$paymentManager = new PaymentManager($this->db);
		$owe = 0;
		
		$isValue = array(
                'totalPayValue' => 0
            );
		
		switch ($this->balance_paid_by) {
			case self::BALANCE_COP_TO_CARRIER_CASH:
			case self::BALANCE_COP_TO_CARRIER_CHECK:
			case self::BALANCE_COP_TO_CARRIER_COMCHECK:
			case self::BALANCE_COP_TO_CARRIER_QUICKPAY:
			case self::BALANCE_COD_TO_CARRIER_CASH:
			case self::BALANCE_COD_TO_CARRIER_CHECK:
			case self::BALANCE_COD_TO_CARRIER_COMCHECK:
			case self::BALANCE_COD_TO_CARRIER_QUICKPAY:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				
				//$owe = $this->getTotalTariff(false) - $shipperPaid;
				$owe = $shipperPaid;
				//if($owe >0)
				   $isValue['totalPayValue'] = $owe;
				
				  
				break;
			case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
			case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
			case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
			case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
				$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
				 
				  
				 //$owe = $this->getTotalTariff(false) - ($carrierPaid+$shipperPaid);
				 $owe = ($carrierPaid+$shipperPaid);
				 //if($owe >0)
				   $isValue['totalPayValue'] = $owe;
				  
				break;
			case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
			case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
			case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
				$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
				//$owe = $this->getTotalTariff(false) - $carrierPaid;
				
				$owe = $carrierPaid;
				//if($owe >0)
				   $isValue['totalPayValue'] = $owe;

				break;
		}
		return $isValue;
	}	
	
	
    /*
     * Set status is first followupped
     * Default settings (First quote follow-up:	<> days since quoted)
     *
     */

    public function setFirstFollowUpped()
    {
        $this->update(array("is_firstfollowup" => self::FIRSTFOLLOWUPPED_YES));
    }

    /**
     * Entity::assign()
     *
     * @param mixed $assign_id - ID of member to assign
     * @throws FDException
     */
    public function assign($assign_id = null)
    {
        if (!ctype_digit((string)$assign_id)) throw new FDException("Invalid Assign ID");
       // $this->update(array('assigned_id' => $assign_id, 'before_assigned_id' => $this->assigned_id));
	   $this->update(array('assigned_id' => $assign_id, 'before_assigned_id' => $this->assigned_id,'assigned_date' => date('Y-m-d H:i:s')));
	   
	   if($this->type == self::TYPE_CLEAD && $this->status == self::STATUS_CACTIVE )
	     $this->update(array('status' =>self::STATUS_CASSIGNED));
		 
	    if($this->type == self::TYPE_LEAD && $this->status == self::STATUS_ACTIVE )
	     $this->update(array('status' =>self::STATUS_ACTIVE));
    }

    /**
     * Entity::setType()
     *
     * @param int $type - Entity status
     * @throws FDException
     */
    protected function setType($type)
    {
        if (!array_key_exists($type, self::$type_name)) throw new FDException("Invalid entity type");
        $this->update(array('type' => $type));
    }

    /* TOOLS */

    /**
     * Entity::load()
     *
     * @param int $id - $id of Entity to load
     * @return \FdObject|void
     * @throws FDException
     */
    public function load($id = null)
    {
        parent::load($id);
        $this->attributes['duplicate'] = false;
        $this->loadPermissions();
    }

    // shahrukh charlie added this function to get source name
    public function get_source_name($source_id){
        $sql = "SELECT company_name FROM app_leadsources WHERE id =".$source_id; 
        $result =$this->db->query($sql);
        $row = $this->db->fetch_row($result);
        return $row['company_name'];
	}

    public function loadForeignEntity($id = null)
    {
        parent::load($id);
        $this->attributes['readonly'] = true;
    }

    /**
     * Entity::loadPermissions()
     *
     * @throws FDException
     *Author chetu
     */
                        
    protected function loadQueuePermissions(){
        $this->attributes['readonly'] = true;
    }
    
    protected function loadPermissions()
    {
        if (!isset($_SESSION['iamcron']) || !$_SESSION['iamcron']) {
            $member = new Member($this->db);
            $member->load($this->assigned_id);
            if ($member->id != getParentId() && $member->parent_id != getParentId() && $this->carrier_id != getParentId())
                $this->attributes['readonly'] = true;
            //return;
            //throw new FDException('Access Denied!');
            if ($this->status == self::STATUS_ARCHIVED || $this->status == self::STATUS_DELIVERED) {
                $this->attributes['readonly'] = true;
                return;
            }
            if ($_SESSION['member_id'] == $this->assigned_id) {
                $this->attributes['readonly'] = false;
            } else {
                switch ($this->type) {
                    case self::TYPE_LEAD:
                        $this->attributes['readonly'] = ($_SESSION['member']['access_leads'] == 2) ? false : true;
                        break;
                    case self::TYPE_QUOTE:
                        $this->attributes['readonly'] = ($_SESSION['member']['access_quotes'] == 2) ? false : true;
                        break;
                    case self::TYPE_ORDER:
                        $this->attributes['readonly'] = ($_SESSION['member']['access_orders'] == 2) ? false : true;
                        break;
                }
            }
        }
    }

    /**
     * Entity::split()
     * Split entity by selected vehicles
     *
     * @param mixed $vehicle_ids - array of Vehicle id's to split
     * @return Entity - new entity
     * @throws FDException
     */
    public function split($vehicle_ids = null)
    {
        if (!$this->loaded){
	        throw new FDException("Entity not loaded.");
        }
        if (!is_array($vehicle_ids)){
	        throw new FDException("invalid Vehicle ID's data");
        }

        $insert_arr = $this->attributes;

        unset($insert_arr['id']);
        unset($insert_arr['created']);
        $insert_arr['number'] = $this->number;
        $new_source = $this->getSource()->selfclone();
        $new_shipper = $this->getShipper()->selfclone();
        $new_origin = $this->getOrigin()->selfclone();
        $new_destination = $this->getDestination()->selfclone();
        $insert_arr['source_id'] = $new_source->id;
        $insert_arr['shipper_id'] = $new_shipper->id;
        $insert_arr['origin_id'] = $new_origin->id;
        $insert_arr['destination_id'] = $new_destination->id;
        $insert_arr['prefix'] = $this->getNewPrefix();
	    $insert_arr['hash'] = self::findFreeHash($this->db);
        $new_entity = new Entity($this->db);

        $new_entity->create($insert_arr);

        foreach ($vehicle_ids as $vehicle_id) {
            $vehicle = new Vehicle($this->db);
            $vehicle->load($vehicle_id);
            if ($vehicle->entity_id != $this->id)
                continue;
            $vehicle->update(array('entity_id' => $new_entity->id));
        }
        $new_entity->updateHeaderTable();
        return $new_entity->id;
    }

    /**
     * Entity::update()
     * Update object data and save changes to history
     *
     * @param mixed $data - array of new values
     * @throws FDException
     */
    public function update($data = null)
    {
        if (isset($data['status'])) {
            $data['status_update'] = date("Y-m-d H:i:s");
        }
        $old_values = $this->attributes;
        parent::update($data);
        $new_values = $this->attributes;
		
        foreach ($new_values as $key => $value) {
            if (($key == "distance") && ($value == 'NULL') && (!ctype_digit((string)$old_values[$key])))
                continue;
            if ($old_values[$key] != $value) {
                if (in_array($key, array('id', 'type', 'status_update', 'readonly', 'created', 'number', 'prefix', 'quoted', 'ordered', 'blocked_by', 'blocked_time', 'duplicate', 'is_firstfollowup')))
                    continue;
                if (stripos($key, '_id') === false) {
                    if (isset($old_values[$key])) {
                        if (($key == "status") && ($value != "")) {
                            $old_values[$key] = self::$status_name[$old_values[$key]];
                            $value = self::$status_name[$value];
                        }
//                        if (($key == "vehicles_run") && ($value != "")) {
//                            $old_values[$key] = self::$vehicles_run_string[$old_values[$key]];
//                            $value = self::$vehicles_run_string[$value];
//                        }
                        if (($key == "ship_via") && ($value != "")) {
                            $old_values[$key] = self::$ship_via_string[$old_values[$key]];
                            $value = self::$ship_via_string[$value];
                        }
                        if (($key == "load_date_type" || $key == "delivery_date_type") && ($value != "")) {
                            $old_values[$key] = self::$date_type_string[$old_values[$key]];
                            $value = self::$date_type_string[$value];
                        }
                    }
                    if (array_key_exists($key, self::$attributeTitles) && array_key_exists($key, $old_values)) {
                        History::add($this->db, $this->id, self::$attributeTitles[$key], $old_values[$key], $value);
                    }
                } else {
                    if ($key == 'attribute_id') {
                        $old_member = new Member($this->db);
                        $old_member->load($old_values[$key]);
                        $new_member = new Member($this->db);
                        $new_member->load($value);
                        History::add($this->db, $this->id, self::$attributeTitles[$key], $old_member->contactname, $new_member->contactname);
                    }
                }
            }
        }
		
        $this->load($this->id);
    }

    /**
     * @param mysql $db
     *
     * @return string
     */
    public static function findFreeHash($db)
    {
        do {
            $hash = md5(mt_rand() . time());
        } while ($db->selectValue('COUNT(*)', self::TABLE, "WHERE `hash` LIKE '" . mysqli_real_escape_string($db->connection_id, $hash) . "'") != 0);
        return $hash;
    }

    /**
     * Entity::create()
     * Creates new Entity record in DB and loads it
     *
     * @param mixed $data - array of neccessery data for DB
     * @return int|void
     * @throws FDException
     */
    public function create($data = null)
    {
        if (isset($data['assigned_id']) && !isset($data['number'])) {
            $member = new Member($this->db);
            $member->load($data['assigned_id']);
            $companyProfile = $member->getCompanyProfile();
            $data['number'] = $companyProfile->getNextNumber();
            $data['hash'] = self::findFreeHash($this->db);
        }
        parent::create($data);
        $new_values = $this->attributes;
        foreach ($new_values as $key => $value) {
            if (in_array($key, array('id', 'type', 'status_update', 'readonly', 'created', 'number', 'prefix', 'quoted', 'ordered', 'deleted', 'blocked_by', 'blocked_time', 'duplicate', 'is_firstfollowup')))
                continue;
            if (stripos($key, '_id') === false) {
                if ($key == "status" && ctype_digit((string)$value)) {
                    $value = self::$status_name[$value];
                }
                if ($key == "ship_via" && ctype_digit((string)$value)) {
                    $value = self::$ship_via_string[$value];
                }
                if (array_key_exists($key, self::$attributeTitles)) {
                    History::add($this->db, $this->id, self::$attributeTitles[$key], '', $value);
                }
            } else {
                if ($key == 'attribute_id') {
                    $new_member = new Member($this->db);
                    $new_member->load($value);
                    History::add($this->db, $this->id, self::$attributeTitles[$key], '', $new_member->contactname);
                }
            }
        }
    }

    public function convertToQuote()
    {
        if ($this->type != self::TYPE_LEAD)
            throw new FDException("Invalid Entity type");
        if ($this->status != self::STATUS_ACTIVE)
            throw new FDException("Invalid Entity status");
        $this->setType(Entity::TYPE_QUOTE);
        $prefix = $this->getNewPrefix();
        $this->update(array('quoted' => date('Y-m-d H:i:s'), 'prefix' => $prefix));
        $this->getVehicles(true);

	    $followup = new FollowUp($this->db);
	    $days = (int)$this->getAssigned()->getDefaultSettings()->first_quote_followup;
		$followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int)date("m"), (int)date("d")+$days, (int)date("Y"))), $this->id);
    }

public function convertToQuoteNew()
    {
        
        if ($this->type != self::TYPE_LEAD)
            throw new FDException("Invalid Entity type");
        if ($this->status != self::STATUS_ACTIVE)
            throw new FDException("Invalid Entity status");
        $this->setStatus(Entity::STATUS_LQUOTED);
        $prefix = $this->getNewPrefix();
        $this->update(array('quoted' => date('Y-m-d H:i:s'), 'prefix' => $prefix));
        $this->getVehicles(true);
     /*
	    $followup = new FollowUp($this->db);
	    $days = (int)$this->getAssigned()->getDefaultSettings()->first_quote_followup;
		$followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int)date("m"), (int)date("d")+$days, (int)date("Y"))), $this->id);
		*/
    }
 
 public function convertToOrder()
    {
        if ($this->type != self::TYPE_QUOTE)
            throw new FDException("Invalid Entity type");
        if ($this->status != self::STATUS_ACTIVE)
            throw new FDException("Invalid Entity status");
        $this->setType(Entity::TYPE_ORDER);

        $prefix = $this->getNewPrefix();
        $this->update(array(
            'ordered' => date('Y-m-d H:i:s'),
            'prefix' => $prefix,
            'avail_pickup_date' => $this->est_ship_date,
        ));
    }

   public function convertLeadToOrder()
    {
		
       /* if ($this->type != self::TYPE_LEAD)
            throw new FDException("Invalid Entity type");
		*/	
        //if ($this->status != self::STATUS_ACTIVE)
            //throw new FDException("Invalid Entity status");
        $this->setType(self::TYPE_ORDER);
        $this->setStatus(self::STATUS_ACTIVE);
        $prefix = $this->getNewPrefix();
        $this->update(array(
            'ordered' => date('Y-m-d H:i:s'),
            'prefix' => $prefix,
            //'avail_pickup_date' => $this->est_ship_date,
        ));
		//print "------".$this->type;
		//$this->sendSystemEmail(691);
		if($this->getAssigned()->parent_id==1)
		  $this->sendSystemEmail(691, array(), false);
		elseif($this->getAssigned()->parent_id==159)
		  $this->sendSystemEmail(712, array(), false);
		
    }
	
    public function getNewPrefix()
    {
        do {
            $prefix = (string)mt_rand(0, 9);
            $prefix .= chr(mt_rand(65, 90));
            $prefix .= chr(mt_rand(65, 90));
            $row = $this->db->selectRow("COUNT(`id`) as cnt", self::TABLE, "WHERE `prefix` = '{$prefix}' AND `number` = '" . $this->number . "'");
            if ($this->db->isError)
                throw new FDException("MySQL query error");
        } while ($row['cnt'] != 0);
        return $prefix;
    }

    public function getPayments()
    {
        $paymentManager = new PaymentManager($this->db);
        return $paymentManager->getPayments($this->id);
    }

    public function autoQuoting()
    {
        $aqs = $this->getAssigned()->getAutoQuotingSettings();
        if ($aqs->is_enabled == 0) {
            return;
        }
        $origin = $this->getOrigin();
        $destination = $this->getDestination();
        $vehicles = $this->getVehicles();
        $aqm = new AutoQuotingManager($this->db);
        $deposit = (float)$this->getAssigned()->getDefaultSettings()->order_deposit;
        $deposit_type = $this->getAssigned()->getDefaultSettings()->order_deposit_type;
        $quoted = 0;
        foreach ($vehicles as $vehicle) {
            if ((float)$vehicle->tariff == 0) {

                $amount = (float)$aqm->getChargeAmount($origin, $destination, $vehicle, $this->getAssigned()->getParent()->id, $this->getShipDate('Y-m-d H:i:s'));
                if ($amount > 0) {
                    $deposit = ($deposit_type == "amount") ? $deposit : (0.01 * $amount * $deposit);
                    $vehicle->update(array(
                        'tariff' => $amount + $deposit,
                        'carrier_pay' => $amount,
                        'deposit' => $deposit,
                    ));
                    $this->db->insert("`app_autoquoting_quotes`", array(
                        "owner_id" => $this->getAssigned()->parent_id,
                        "date" => date("Y-m-d")
                    ));
                    $quoted++;
                }
            }
        }

        //convert to the quote and send email
        if ($this->type == self::TYPE_LEAD && $quoted > 0) {
            $this->convertToQuote();
            $this->sendInitialQuote();
        }
    }

   protected function getSystemEmailContent($type, $add = array(), $is_default = true)
	{
		
		$tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
		$emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
		$emailContentArr  = array();
		$emailContentArr['subject'] = $emailTemplate->getSubject();
		$emailContentArr['from'] = $emailTemplate->getFromAddress();
		$emailContentArr['fromname'] = $emailTemplate->getFromName();
		$emailContentArr['to'] = $emailTemplate->getToAddress();
		$emailContentArr['bcc'] = $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind;
            
		$emailContentArr['body'] = $emailTemplate->getBody();
		$sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =".getParentId(); 
                $result =$this->db->query($sql);
                $row = $this->db->fetch_row($result);
                $emailContentArr['atttype'].=$row['attach_type'];
		
                
                 $att=array_keys($emailTemplate->getAttachments());
                    if (!empty($att)){
                         foreach ($att as $atts){
                        $emailContentArr['att'] .= pathinfo($atts, PATHINFO_FILENAME);
                        $emailContentArr['att'] .="<br>";
                        }
                    }else{
                        $emailContentArr['att']="No attachment"; 
                    }
		
		
		return $emailContentArr;
		
		
	}
	
	
	protected function sendSystemEmailNewCustomSend($type, $add = array(), $is_default = true,$emailArr)
    {
		
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
      try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            $mail->Body = $emailArr['body'];
            $mail->Subject = $emailArr['subject'];
            $mail->AddAddress($emailArr['to']);
            if(trim($emailArr['mail_extra'])!="")
             $mail->AddAddress($emailArr['mail_extra']);
            if(trim($emailArr['cc'])!="")
             $mail->AddCC($emailArr['cc']);
            if(trim($emailArr['bcc'])!="")
            $mail->addBCC($emailArr['bcc']);
            
			
			if($this->getAssigned()->parent_id == 1)
              $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            else
			  $mail->SetFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email, $emailTemplate->getFromName());
			  
			$mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
//            foreach ($bcc_s as $bcc) {
//                $bcc = trim($bcc);
//                if ($bcc != "") {
//                    $mail->AddBCC($bcc);
//                }
//            }
			
//			$email_extra = trim($emailArr['mail_extra']);
//			if($email_extra !=""){
//				$email_extra_arr = explode(",",$email_extra);
//				foreach ($email_extra_arr as $email_ext) {
//					$email_ext = trim($email_ext);
//					if ($email_ext != "") {
//						$mail->AddBCC($email_ext);
//					}
//				}
//			}
			//$mail->AddBCC("neeraj@freightdragon.com");
			//$mail->AddBCC("admin@ritewayautotransport.com");
			
            $att = $emailTemplate->getAttachments();
    if (count($att) > 0) {          
//           $sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =".getParentId();           
//           $result =$this->db->query($sql);
//           $row = $this->db->fetch_row($result);
            if ($emailArr['attach_type']>0){
                foreach ($att as $name => $attachment) {
                    $filename = pathinfo($name, PATHINFO_FILENAME);
                    $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()). ".pdf";
                    $this->getPdfFromHtml($attFile,$attachment);                    
                    $attachments[$filename.'.pdf'] = $attFile ;
                }
            }else{
                foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".html";
                    file_put_contents($attFile, $attachment);
                    $attachments[$name] = $attFile;
                } 
            }
    }

			if($_SESSION['member_id']==1 && $emailTemplate->sys_id == 7 && $type == 690){
				 
				foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/order_confirmation.pdf"; //" . md5(mt_rand()) . "
					$this->getPdfNewEmail("F", $attFile,$attachment);
                    $attachments['Order_Confirmation.pdf'] = $attFile;
                }
				 
			 }
			
            if ($emailTemplate->sys_id == EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $path = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".pdf";
                $this->getDispatchSheet()->getPdf("F", $path);
                $attachments["DispatchSheet.pdf"] = $path;
            }
            foreach ($attachments as $name => $attachment) {
                $mail->AddAttachment($attachment, $name);
            }
			
            $mail->Send();
			
			
			$member = new Member($this->db);
			$member->load($_SESSION['member_id']);
			$notes_str = $member->contactname ." sent '".$emailTemplate->name."' to ". $emailArr['to'];
			
			if($emailArr['cc']!="")
			    $notes_str .= " also CC this mail to ".$emailArr['cc'];
			else
			   $notes_str .= ".";
			 /* UPDATE NOTE */
								$note_array = array(
									"entity_id" => $this->id,
									"sender_id" => $_SESSION['member_id'],
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => $notes_str//$this->getAssigned()->contactname." sent " .$emailArr['subject'] ." on date " .date('Y-m-d H:i:s')
									);
								
								$note = new Note($this->db);
								$note->create($note_array);
			
        } catch (phpmailerException $e) {
			//print $e->getMessage();
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }
    public function getPdfFromHtml($path,$html) {
        ob_start();
          require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
          $mpdf = new mPDF('utf-8', '', 0, '', 10, 10, 0, 0 , 0, 0 );
          $mpdf->WriteHTML($html);
        ob_end_clean();
          $mpdf->Output($path);
    }
	
	public function getPdfNewEmail($out = "D", $path = "DispatchSheet.pdf",$html) {
		//$entity = new Entity($this->db);
        //$entity->load($this->entity_id);
        $member = $this->getAssigned();
		
		//print "-----".$path;
		
		ob_start();
		require_once(ROOT_PATH."/libs/mpdf/mpdf.php");
		$pdf = new mPDF('utf-8', 'A4', '8', 'DejaVuSans', 10, 10, 7, 7, 10, 10);
		  
		$pdf->SetAuthor($this->getAssigned()->getCompanyProfile()->companyname);
		$pdf->SetSubject("Dispatch Sheet");
		$pdf->SetTitle("Dispatch Sheet");
		$pdf->SetCreator("FreightDragon.com");
		//$pdf->SetAutoPageBreak(true, 30);
		//$pdf->setAutoTopMargin='pad';
		//$pdf->SetTopMargin(22);
		
		$pdf->writeHTML("<style>".file_get_contents(ROOT_PATH."styles/application_email_pdf.css")."</style>", 1);

	    $pdf->writeHTML($html, 2);
	 	
		ob_end_clean();
		$pdf->Output($path, $out);
		
		
		
	}
	
    // $is_default = true for default system templates
    // set to false for needed template id
    protected function sendSystemEmail($type, $add = array(), $is_default = true)
    {
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
        try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            $mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();
            $mail->AddAddress($emailTemplate->getToAddress());

            //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
			
			if($this->getAssigned()->parent_id == 1)
              $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            else
			  $mail->SetFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email, $emailTemplate->getFromName());
			  
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
            $att = $emailTemplate->getAttachments();
            if (count($att) > 0) {
                foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".html";
                    file_put_contents($attFile, $attachment);
                    $attachments[$name] = $attFile;
                }
            }
            if ($emailTemplate->sys_id == EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $path = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".pdf";
                $this->getDispatchSheet()->getPdfNew("F", $path);
                $attachments["DispatchSheet.pdf"] = $path;
            }
            foreach ($attachments as $name => $attachment) {
                $mail->AddAttachment($attachment, $name);
            }
            $mail->Send();
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
    }

    public function sendInitialQuote()
    {
        //if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_INIT_QUOTE);
    }

    public function sendSelectedQuoteTemplate($id)
    {
        //if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail($id, array(), false);
    }

	public function sendSelectedQuoteTemplateNew($id)
    {
		$emailContentArr  = array();
       // if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $emailContentArr  = $this->getSystemEmailContent($id, array(), false);
		return $emailContentArr;
		
    }
	
	public function sendSelectedQuoteTemplateNewCustomSend($id,$emailArr)
    {
		
        //if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmailNewCustomSend($id, array(), false,$emailArr);
		
    }
	
    public function sendSelectedOrderTemplate($id)
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail($id, array(), false);
    }

	public function sendSelectedOrderTemplateNew($id)
    {
		$emailContentArr  = array();
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $emailContentArr  = $this->getSystemEmailContent($id, array(), false);
		return $emailContentArr;
		
    }
	
	/*
	* Send Email	
	*/
	public function sendEmailWithoutForm($id){
		$emailContentArr  = array();
       // if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $emailContentArr  = $this->getEmailContent($id, array(), false);
		return $emailContentArr;
		
    }
	
	function getEmailContent($type, $add = array(), $is_default = true){
		$tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
		$emailTemplate->setTemplateBuilder($tpl);
		$emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
		$emailContentArr = array();
		$emailContentArr['subject'] = $emailTemplate->getSubject();
		$emailContentArr['from'] = $emailTemplate->getFromAddress();
		$emailContentArr['fromname'] = $emailTemplate->getFromName();
		$emailContentArr['to'] = $emailTemplate->getToAddress();
                $emailContentArr['cc'] = "";
		$emailContentArr['bcc'] = $emailTemplate->getBCCs(); //. "," . $this->getAssigned()->getDefaultSettings()->email_blind;
		$emailContentArr['body'] = $emailTemplate->getBody();
  
                $sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =".getParentId(); 
                $result =$this->db->query($sql);
                $row = $this->db->fetch_row($result);
                $emailContentArr['atttype'].=$row['attach_type'];
                
                $att=array_keys($emailTemplate->getAttachments());
                    if (!empty($att)){
                         foreach ($att as $atts){
                        $emailContentArr['att'] .= pathinfo($atts, PATHINFO_FILENAME);
                        $emailContentArr['att'] .="<br>";
                        }
                    }else{
                        $emailContentArr['att']="No attachment"; 
                    }
		return $emailContentArr;
	}
	
	public function sendSelectedOrderTemplateNewCustomSend($id,$emailArr)
    {
		
        //if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmailNewCustomSend($id, array(), false,$emailArr);
		
    }

    public function sendFollowUpQuote($type)
    {
        if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail($type);
    }

    public function sendOrderConfirmation()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_CONFIRM);
    }
	
	public function sendCommercialOrderConfirmation()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(19);
    }

    public function sendPaymentReceived($amount)
    {
        //if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        //$this->sendSystemEmail(EmailTemplate::SYS_ORDER_PAYMENT_RCVD, array("payment_amount" => "$ " . number_format((float)$amount, 2)));
    }

    public function sendOrderDispatched()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_DISP_NOTIFY);
    }

    public function sendThankYou()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_THANKS);
    }

    public function sendInvoice()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_INVOICE_ATT);
    }

    public function sendQuoteForm()
    {
        if ($this->type != self::TYPE_QUOTE) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_QUOTE_FORM_ATT);
    }

    public function sendDispatchLink($add)
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_DISPATCH_LINK, $add);
    }

    public function sendOrderForm()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_FORM_ATT);
    }

    public function sendDispatchSheet()
    {
        if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
        $this->sendSystemEmail(EmailTemplate::SYS_ORDER_DISP_SHEET_ATT);
    }

    public function checkDuplicate($add = '')
    {
        $sql = "
			SELECT
				e.`id`
			FROM
				" . self::TABLE . " e,
				" . Origin::TABLE . " o,
				" . Destination::TABLE . " d,
				" . Shipper::TABLE . " s
			WHERE
				e.`type` = " . $this->type . "
				AND e.`id` != " . $this->id . "
				AND e.`status` = " . self::STATUS_ACTIVE . "
				{$add}
				AND e.`assigned_id` IN (SELECT `id` FROM " . Member::TABLE . " WHERE `parent_id` = " . $this->getAssigned()->parent_id . ")
				AND s.`id` = e.`shipper_id`
				AND o.`id` = e.`origin_id`
				AND d.`id` = e.`destination_id`
				AND s.`fname` LIKE('" . $this->getShipper()->fname . "')
				AND s.`lname` LIKE('" . $this->getShipper()->lname . "')
				AND o.`city` LIKE('" . $this->getOrigin()->city . "')
				AND o.`state` LIKE('" . $this->getOrigin()->state . "')
				AND o.`country` LIKE('" . $this->getOrigin()->country . "')
				AND d.`city` LIKE('" . $this->getDestination()->city . "')
				AND d.`state` LIKE('" . $this->getDestination()->state . "')
				AND d.`country` LIKE('" . $this->getDestination()->country . "')
			";
			
			/* $sql = "
			SELECT
				e.`id`
			FROM
				app_order_header as e
			WHERE
				e.`type` = " . $this->type . "
				AND e.`id` != " . $this->id . "
				AND e.`status` = " . self::STATUS_ACTIVE . "
				{$add}
				AND e.`assigned_id` IN (SELECT `id` FROM " . Member::TABLE . " WHERE `parent_id` = " . $this->getAssigned()->parent_id . ")
				
				AND e.`shipperfname` LIKE('" . $this->getShipper()->fname . "')
				AND e.`shipperlname` LIKE('" . $this->getShipper()->lname . "')
				AND e.`Origincity` LIKE('" . $this->getOrigin()->city . "')
				AND e.`Originstate` LIKE('" . $this->getOrigin()->state . "')
				AND e.`Destinationcity` LIKE('" . $this->getDestination()->city . "')
				AND e.`Destinationstate` LIKE('" . $this->getDestination()->state . "')
				
			";*/
			
        $result = $this->db->query($sql);
        if ($this->db->isError)
            throw new FDException("MySQL query error");
        if (mysqli_num_rows($result) > 0)
            $this->attributes['duplicate'] = true;
    }

    public function isBlocked()
    {
        if (!ctype_digit((string)$this->blocked_by) && !ctype_digit((string)$this->blocked_time))
            return false;
        if (($this->blocked_by == $_SESSION['member_id']) || ((time() - $this->blocked_time) > (60*60*24)))
            return false;
        return true;
    }
	public function blockedByMember()
    {     
		$sql = "SELECT m.contactname
                FROM members m
                WHERE m.id = '" . $this->blocked_by . "'";
        $memberList = $this->db->selectRows($sql);		
        foreach ($memberList as $i => $mL) {
            $memberList = $mL;     
        }
            return  $memberList['contactname'];
    }

    public function setBlock()
    {
        if ($this->isBlocked())
            return;
        $this->update(array(
            'blocked_by' => $_SESSION['member_id'],
            'blocked_time' => time()
        ));
    } 
	
    public function updateBlock()
    {
        if ($this->blocked_by != $_SESSION['member_id'])
            return;
        $this->update(array(
            'blocked_by' => $_SESSION['member_id'],
            'blocked_time' => time()
        ));
    }
    public function unsetBlock()
    {
        if (!$this->isBlocked())
            return;
        if ($this->blocked_by != $_SESSION['member_id'])
            return;
        $this->update(array(
            'blocked_by' => 'NULL',
            'blocked_time' => 'NULL'
        ));
    }

    /**
     * Entity::getEstLoadDate()
     * @param string $format
     * @return string formatted date Est. Load Date
     *
     */
    final public function getEstLoadDate($format = "Y-m-d H:i:s")
    {
        return (is_null($this->avail_pickup_date)) ? "" : date($format, strtotime($this->avail_pickup_date));
    }

    /**
     * getActualPickUpDate()
     * @param string $format
     * @return string formatted date Actual Pick up Date
     *
     */
    final public function getActualPickUpDate($format = "Y-m-d H:i:s")
    {
        return (is_null($this->actual_pickup_date)) ? "" : date($format, strtotime($this->actual_pickup_date));
    }

    /**
     * getPickUpDeviation()
     * @return int Actual Pick up date Deviation (+/- days)
     *
     */
    final public function getPickUpDeviation()
    {
        return $this->getDatesDifference($this->avail_pickup_date, $this->actual_pickup_date);
    }

    /**
     * getEstDeliveryDate()
     * @param string $format
     * @return string formatted date Est. Delivery date
     *
     */
    final public function getEstDeliveryDate($format = "Y-m-d H:i:s")
    {
        return (is_null($this->est_ship_date)) ? "" : date($format, strtotime($this->est_ship_date));
    }

    /**
     * getActualDeliveryDate()
     * @param string $format
     * @return string Actual Delivery date
     */
    final public function getActualDeliveryDate($format = "Y-m-d H:i:s")
    {
        return (is_null($this->actual_ship_date)) ? "" : date($format, strtotime($this->actual_ship_date));
    }

    /**
     * getDeliveryDeviation()
     * @return int Actual Delivery date Deviation (+/- days)
     */
    final public function getDeliveryDeviation()
    {
        return $this->getDatesDifference($this->est_ship_date, $this->actual_ship_date);
    }

    /**
     * getLastInternalNote()
     * @return string Last Internal Note
     */
    final public function getLastInternalNote()
    {
        $notes = new NoteManager($this->db);
        $note = $notes->getLastInternalNote($this->id);
        return $note;
    }

    /**
     * Get difference between 2 dates in days
     *
     * @param string $date1
     * @param string $date2
     * @return float difference +/-
     */
    private static function getDatesDifference($date1, $date2)
    {
        $range = 0;
        if ($date1 != "" && $date2 != "") {
            $d1 = explode("-", substr($date1, 0, 10));
            $d2 = explode("-", substr($date2, 0, 10));
            //Revert to time
            $date1 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);
            $date2 = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]);
            $difference = ($date2 - $date1); //difference in seconds
            $range = ($difference / 86400); //difference in days
        }
        return $range;
    }

    /**
     * getEntities()
     * Return Entities Ids
     *
     * @param mysql $db
     * @param string $where
     * @return array of Entity IDs
     * @throws FDException
     */
    public static function getEntities($db, $where = "")
    {
        if (!($db instanceof mysql)) throw new FDException("Invalid DB Helper");
        $entity_ids = $db->selectRows("`id`", self::TABLE, "WHERE " . $where);
        $entities = array();
        foreach ($entity_ids as $value) {
            $entities[] = $value["id"];
        }
        return $entities;
    }

    /**
     * getDispatchSheet()
     * Return current Dispatch Sheet of this Order
     * @param bool $reload - if true reload saved Dispatch Sheet
     * @return DispatchSheet
     * @throws FDException
     */
    public function getDispatchSheet($reload = false)
    {
        if ($reload || !isset($this->memberObjects['dispatchSheet'])) {
            if ($this->type != self::TYPE_ORDER) throw new FDException("Invalid Entity Type");
            $dispatchSheetManager = new DispatchSheetManager($this->db);
            $dispatchSheet = new DispatchSheet($this->db);
            $dsId = $dispatchSheetManager->getDispatchSheetByOrderId($this->id);
            $this->memberObjects['dispatchSheet'] = (is_null($dsId)) ? null : $dispatchSheet->load($dsId);
        }
        return $this->memberObjects['dispatchSheet'];
    }

    public function getShipperPayments()
    {
        $paymentManager = new PaymentManager($this->db);
        return $paymentManager->getFilteredPayments($this->id, Payment::SBJ_SHIPPER);
    }

    /**
     * @param bool $format
     * @return float|string $amount
     */
    public function getShipperPaymentsAmount($format = true)
    {
        $amount = 0;
        $payments = $this->getShipperPayments();
        foreach ($payments as $payment) {
            /* @var Payment $payment */
            $amount += $payment->amount;
        }
        if ($format) {
            return "$ " . number_format($amount, 2);
        } else {
            return (float)$amount;
        }
    }

    /**
     * @param bool $format
     * @return float|string $depositDue
     */
    public function getDepositDue($format = true)
    {
        $depositDue = 0;
        $amount = 0;
        $payments = $this->getShipperPayments();
        foreach ($payments as $payment) {
            /* @var Payment $payment */
            $amount += $payment->amount;
        }
        if ($amount < $this->total_deposit) {
            $depositDue = (float)$this->total_deposit - (float)$amount;
        }
        if ($format) {
            return "$ " . number_format($depositDue, 2);
        } else {
            return (float)$depositDue;
        }
    }

    /**
     * @param bool $format
     * @return float|string $amount
     */
    public function getAmountDue($format = true)
    {
        $amount = $this->getTotalPrice(false) - $this->getShipperPaymentsAmount(false);
        if ($format) {
            return "$ " . number_format($amount, 2);
        } else {
            return (float)$amount;
        }
    }

    /**
     * @param bool $format
     * @return float|string $price
     */
    public function getTotalPrice($format = true)
    {
        $price = (float)$this->total_tariff + (float)$this->pickup_terminal_fee + (float)$this->dropoff_terminal_fee;
        if ($format) {
            return "$ " . number_format($price, 2);
        } else {
            return (float)$price;
        }
    }

public function getTotalPaymentAmount($format=true)
    {
        $paymentManager = new PaymentManager($this->db);
        return $paymentManager->getFilteredPaymentsTotals($this->id, null,null,$format);
    }
	
public function getTotalDuePaymentAmount($format=true)
    {
        
		//$payAmount = $this->getTotalPaymentAmount(false);
		$payAmount = $this->getShipperPaymentsAmount(false);
		
		$totalAmount = $this->getTotalPrice(false);
		
		$amount = $totalAmount - $payAmount;
		
		if ($format) {
            return "$ " . number_format($amount, 2);
        } else {
            return (float)$amount;
        }
    }



    /**
     * @param bool $reload
     * @return EntityCreditcard
     */
    public function getCreditCard($reload = false)
    {
        if ($reload || !isset($this->memberObjects['creditCard'])) {
            $creditCard = new EntityCreditcard($this->db);
            $this->memberObjects['creditCard'] = $creditCard->loadByEntityId($this->id);
        }
        return $this->memberObjects['creditCard'];
    }

    /**
     * @param bool $reload
     * @return EntityDoc[]
     */
    public function getDocs($reload = false)
    {
        if ($reload || !isset($this->memberObjects['entityDocs'])) {
            $this->memberObjects['entityDocs'] = EntityDoc::getEntityDocs($this->db, $this->id);
        }
        return $this->memberObjects['entityDocs'];
    }

    /**
     * @param string $hash
     * @return \FdObject|void
     */
    public function loadByHash($hash)
    {
        $id = $this->db->selectField('id', self::TABLE, "WHERE `hash` LIKE '" . mysqli_real_escape_string($this->db->connection_id, $hash) . "'");
        return $this->load($id);
    }
	
	 public function loadByHashColumn($hash,$hashColumn='hash')
    {
        $id = $this->db->selectField('id', self::TABLE, "WHERE ".$hashColumn." LIKE '" . mysqli_real_escape_string($this->db->connection_id, $hash) . "'");
        return $this->load($id);
    }

    /**
     * AUTOMATED POSTING/REMOVAL OF VEHICLE LISTINGS ON CENTRALDISPATCH
     */
    public function postToCentralDispatch($posttype)
    {
        $settings = $this->getAssigned()->getDefaultSettings();
        $central_dispatch_uid = $settings->central_dispatch_uid;
        $central_dispatch_post = $settings->central_dispatch_post;
        $email_from = $this->getAssigned()->getCompanyProfile()->email;

        $vehicles = $this->getVehicles(); //for calculate carrier pay

        if ($central_dispatch_uid != "" && $email_from != "" && $central_dispatch_post == 1) {
            //build import string

            $command_uid = "UID(" . $central_dispatch_uid . ")*";
            //Command
            $command_act = "DELETE(" . $this->getNumber() . ")*";
            //1. Order ID:
            $command = addcslashes(trim($this->getNumber()), ",") . ",";
            //2. Pickup City:
            $command .= addcslashes(trim($this->getOrigin()->city), ",") . ",";
            //3. Pickup State:
            $command .= strtoupper(addcslashes($this->state2Id(trim($this->getOrigin()->state)), ",")) . ",";
            //4. Pickup Zip:
            $command .= addcslashes(trim($this->getOrigin()->zip), ",") . ",";
            //5. Delivery City:
            $command .= addcslashes(trim($this->getDestination()->city), ",") . ",";
            //6. Delivery State:
            $command .= strtoupper(addcslashes($this->state2Id(trim($this->getDestination()->state)), ",")) . ",";
            //7. Delivery Zip:
            $command .= addcslashes(trim($this->getDestination()->zip), ",") . ",";
            //8. Carrier Pay:
            $command .= $this->carrier_pay . ",";

            $codcops = array(
                self::BALANCE_COD_TO_CARRIER_CASH,
                self::BALANCE_COD_TO_CARRIER_CHECK,
                //self::BALANCE_COD_TO_DTERMINAL_CASH,
                //self::BALANCE_COD_TO_DTERMINAL_CHECK,
                //self::BALANCE_COD_TO_PTERMINAL_CASH,
                //self::BALANCE_COD_TO_PTERMINAL_CHECK,
                self::BALANCE_COP_TO_CARRIER_CASH,
                self::BALANCE_COP_TO_CARRIER_CHECK,
				//self::BALANCE_COMPANY_OWES_CARRIER_CASH,
                //self::BALANCE_COMPANY_OWES_CARRIER_CHECK,
				//self::BALANCE_CARRIER_OWES_COMPANY_CASH,
                //self::BALANCE_CARRIER_OWES_COMPANY_CHECK,
            );

            $cash_certified_funds = array(
                self::BALANCE_COD_TO_CARRIER_CASH,
                //self::BALANCE_COD_TO_DTERMINAL_CASH,
                //self::BALANCE_COD_TO_PTERMINAL_CASH,
                self::BALANCE_COP_TO_CARRIER_CASH,
				//self::BALANCE_COMPANY_OWES_CARRIER_CASH,
				//self::BALANCE_CARRIER_OWES_COMPANY_CASH,
            );
            $check = array(
                self::BALANCE_COD_TO_CARRIER_CHECK,
                //self::BALANCE_COD_TO_DTERMINAL_CHECK,
                //self::BALANCE_COD_TO_PTERMINAL_CHECK,
                self::BALANCE_COP_TO_CARRIER_CHECK,
				//self::BALANCE_COMPANY_OWES_CARRIER_CHECK,
				//self::BALANCE_CARRIER_OWES_COMPANY_CHECK,
            );

            $pickup = array(
                self::BALANCE_COP_TO_CARRIER_CASH,
                self::BALANCE_COP_TO_CARRIER_CHECK,
            );

            $codcop_amount = $this-> total_tariff + $this->pickup_terminal_fee + $this->dropoff_terminal_fee - $this->total_deposit;
           

            if ($codcop_amount > 0) {
                if (in_array($this->balance_paid_by, $codcops)) {
                    //9. COD/COP Amount:
                    $command .= $codcop_amount . ",";
                    //10. COD/COP Method:
                    if (in_array($this->balance_paid_by, $cash_certified_funds)) {
                        $command .= "cash/certified funds,";
                    } else {
                        $command .= "check,";
                    }
                    //11. COD/COP Timing:
                    if (in_array($this->balance_paid_by, $pickup)) {
                        $command .= "pickup,";
                    } else {
                        $command .= "delivery,";
                    }
                } else {
                    //9. COD/COP Amount:
                    $command .= "0.00,";
                    //10. COD/COP Method:
                    $command .= "cash/certified funds,";
                    //11. COD/COP Timing:
                    $command .= "delivery,";
                }
            } else {
                //9. COD/COP Amount:
                $command .= "0.00,";
                //10. COD/COP Method:
                $command .= "cash/certified funds,";
                //11. COD/COP Timing:
                $command .= "delivery,";
            }

            //12. Remaining Balance Payment Method:
            $command .= "none,";
            //13. Ship Method:
            $command .= strtolower($this->getShipVia()) . ",";
            
            //18. Vehicle(s):

            $vs = array();
			$inopValue = "operable";
            if (count($vehicles) > 0) {
                foreach ($vehicles as $vehicle) {
                    /*if (in_array($vehicle->type, array("Boat", "Car", "Motorcycle", "Pickup", "RV", "SUV", "Travel Trailer", "Van"))) {
                        $type = $vehicle->type;
                    } else {
                        $type = "Other: " . $vehicle->type;
                    }
					*/

					$valueVT = $this->getVehicleType($vehicle->type);
					if($valueVT != -1){
						 $type = $valueVT;
					} else {
                        $type = "Other: " . $vehicle->type;
                    }

					if($vehicle->inop == 1)
					   $inopValue = "inop";
					   
                    $vs[] = addcslashes($vehicle->year . "|" . $vehicle->make . "|" . $vehicle->model . "|" . $type, ",");
                }
                //$command .= implode(";", $vs);
            }
			
             $firstAvail = $this->getPostDate("Y-m-d");
			if(strtotime(trim($this->getFirstAvail("Y-m-d"))) >= strtotime(date('Y-m-d')) )
		         $firstAvail = $this->getFirstAvail("Y-m-d");
				 
			//14. Vehicle Operable:
            $command .= $inopValue . ",";//$this->getInopExportName() . ",";
            //15. First Available (YYYY-MM-DD):
            $command .=  $firstAvail . ",";//$this->getFirstAvail("Y-m-d") . ",";
            //16. Display Until:
            $command .= date("Y-m-d",strtotime(date("Y-m-d", strtotime($firstAvail)) . "+1 month")) . ",";//$firstAvail . ",";//$this->getFirstAvail("Y-m-d") . ",";
            //17. Additional Info:
			/*
            $notes = $this->getNotes();
            if (isset($notes[Note::TYPE_FROM][0])) {
                $command .= addcslashes(substr($notes[Note::TYPE_FROM][0]->text, 0, 60), ",") . ",";
            } else {
                $command .= ",";
            }
			*/
			
			$command .= addcslashes(substr($this->information, 0, 60), ",").",";
			
			
			if (count($vehicles) > 0) {
			    $command .= implode(";", $vs);
			}
			
            //strip asterisks
            //end of command
            $message = $command_uid . $command_act . str_replace("*", "", $command) . "*";
             //print $message;
//print self::CENTRAL_DISPATCH_EMAIL_TO."==".self::CENTRAL_DISPATCH_EMAIL_BCC;
            try {
                $mail = new FdMailer(true);
                $mail->IsHTML(false);
                $mail->Body = $message;
                $mail->Subject = "posting request to CD for ID " . $this->getNumber() . "";
                $mail->SetFrom("posting@freightdragon.com");
                $mail->AddAddress(self::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
				$mail->AddBCC("testing@freightdragon.com");
                //$mail->AddBCC(self::CENTRAL_DISPATCH_EMAIL_BCC);
				
                ob_start();
                //$ret = $mail->Send();
			   $ret = $mail->SendToCD();
			  //exit;
                $mailer_output = ob_get_clean();
                if (!$ret) {
				    
                    throw new Exception($mailer_output . "\n");
                } else {
                    History::add($this->db, $this->id, "CENTRAL DISPATCH", ($posttype == 1 ? "ADD ORDER TO CD" : "REPOST ORDER"), date("Y-m-d H:i:s"));
                    $ret = true;
                }
            } catch (phpmailerException $e) {
                $ret = $e->getMessage();
            } catch (Exception $e) {

            }
            //echo $ret;
            //send mail
            //update entity
            //update history
            //update log
            //unpost action
            //edit/save action
            //repost with confirm if edit
            //add to the settings post
            //only on FreightDragon Freightboard or FreightDragon And CentralDispatch

            //4. In what cases we should delete posted to CD order from CD?
            //Once the unit is dispatched to a carrier or when removed from the freight board or when placed on hold status
        }
    }


    public function deleteFromCentralDispatch()
    {

        $settings = $this->getAssigned()->getDefaultSettings();
        $central_dispatch_uid = $settings->central_dispatch_uid;
        $central_dispatch_post = $settings->central_dispatch_post;
        $email_from = $this->getAssigned()->getCompanyProfile()->email;

        if ($central_dispatch_uid != "" && $email_from != "" && $central_dispatch_post == 1) {
            $command_uid = "UID(" . $central_dispatch_uid . ")*";
            $command_act = "DELETE(" . $this->getNumber() . ")*";
            $message = $command_uid . str_replace("*", "", $command_act) . "*";

             try {
                $mail = new FdMailer(true);
                $mail->IsHTML(false);
                $mail->Body = $message;
                $mail->Subject = "Removal Request from CD for ID " . $this->getNumber() . "";
                $mail->SetFrom("posting@freightdragon.com");
                $mail->AddAddress(self::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
				//$mail->AddBCC("posting@freightdragon.com");
				//$mail->AddCC("neeraj@freightdragon.com");
                //$mail->AddBCC(self::CENTRAL_DISPATCH_EMAIL_BCC);
                ob_start();
                //$ret = $mail->Send();
			    $ret = $mail->SendToCD();
			  //exit;
                $mailer_output = ob_get_clean();
                if (!$ret) {
				    
                    throw new Exception($mailer_output . "\n");
                } else {
                    $ret = true;
                    History::add($this->db, $this->id, "CENTRAL DISPATCH", "REMOVE FROM CD", date("Y-m-d H:i:s"));
                }
            } catch (phpmailerException $e) {
                $ret = $e->getMessage();
            } catch (Exception $e) {

            }

           /*
            try {
				$mail->Host = 'smtp.mailgun.org';  
				$mail->Port = '587';                                    // Set the SMTP port
                $mail->SMTPAuth = true;                            // Enable SMTP authentication
                $mail->SMTPSecure = 'tls';
                $mail->Username = 'posting@freightdragon.com';                // SMTP username
                $mail->Password = 'U&Qb#tSUq4joBJ';                 // SMTP password
                $mail = new FdMailer(true);
                $mail->IsHTML(false);
                $mail->Body = $message;
                $mail->Subject = "Removal Request from CD for ID " . $this->getNumber() . "";
                $mail->SetFrom("posting@freightdragon.com");
                $mail->AddAddress(self::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
				$mail->AddBCC("posting@freightdragon.com");
                //$mail->AddBCC(self::CENTRAL_DISPATCH_EMAIL_BCC);
                ob_start();
                $ret = $mail->Send();
                $mailer_output = ob_get_clean();
                if (!$ret) {
                    throw new Exception($mailer_output . "\n");
                } else {
                    $ret = true;
                    History::add($this->db, $this->id, "CENTRAL DISPATCH", "REMOVE FROM CD", date("Y-m-d H:i:s"));
                }
            } catch (phpmailerException $e) {
                $ret = $e->getMessage();
            } catch (Exception $e) {

            }
			
			*/
        }

    }


    private function state2Id($state)
    {
        return $state;
    }
	
	
		    /**
     * AUTOMATED POSTING/REMOVAL OF VEHICLE LISTINGS ON CENTRALDISPATCH
     */
    public function repostToCentralDispatch($posttype)
    {
		
		
        $settings = $this->getAssigned()->getDefaultSettings();
		
        $central_dispatch_uid = $settings->central_dispatch_uid;
		
        $central_dispatch_post = $settings->central_dispatch_post;
        $email_from = $this->getAssigned()->getCompanyProfile()->email;

        $vehicles = $this->getVehicles(); //for calculate carrier pay

        //print "<br>settings : central_dispatch_uid - $central_dispatch_uid , central_dispatch_post - $central_dispatch_post , email_from - $email_from<br>";
        if ($central_dispatch_uid != "" && $email_from != "" && $central_dispatch_post == 1) {
            //build import string

            $command_uid = "UID(" . $central_dispatch_uid . ")*";
            //Command
            $command_act = "DELETE(" . $this->getNumber() . ")*";
            //1. Order ID:
            $command = addcslashes(trim($this->getNumber()), ",") . ",";
            //2. Pickup City:
            $command .= addcslashes(trim($this->getOrigin()->city), ",") . ",";
            //3. Pickup State:
            $command .= strtoupper(addcslashes($this->state2Id(trim($this->getOrigin()->state)), ",")) . ",";
            //4. Pickup Zip:
            $command .= addcslashes(trim($this->getOrigin()->zip), ",") . ",";
            //5. Delivery City:
            $command .= addcslashes(trim($this->getDestination()->city), ",") . ",";
            //6. Delivery State:
            $command .= strtoupper(addcslashes($this->state2Id(trim($this->getDestination()->state)), ",")) . ",";
            //7. Delivery Zip:
            $command .= addcslashes(trim($this->getDestination()->zip), ",") . ",";
            //8. Carrier Pay:
            $command .= $this->carrier_pay . ",";

            $codcops = array(
                self::BALANCE_COD_TO_CARRIER_CASH,
                self::BALANCE_COD_TO_CARRIER_CHECK,
                //self::BALANCE_COD_TO_DTERMINAL_CASH,
                //self::BALANCE_COD_TO_DTERMINAL_CHECK,
                //self::BALANCE_COD_TO_PTERMINAL_CASH,
                //self::BALANCE_COD_TO_PTERMINAL_CHECK,
                self::BALANCE_COP_TO_CARRIER_CASH,
                self::BALANCE_COP_TO_CARRIER_CHECK,
				//self::BALANCE_COMPANY_OWES_CARRIER_CASH,
                //self::BALANCE_COMPANY_OWES_CARRIER_CHECK,
				//self::BALANCE_CARRIER_OWES_COMPANY_CASH,
                //self::BALANCE_CARRIER_OWES_COMPANY_CHECK,
            );

            $cash_certified_funds = array(
                self::BALANCE_COD_TO_CARRIER_CASH,
                //self::BALANCE_COD_TO_DTERMINAL_CASH,
                //self::BALANCE_COD_TO_PTERMINAL_CASH,
                self::BALANCE_COP_TO_CARRIER_CASH,
				//self::BALANCE_COMPANY_OWES_CARRIER_CASH,
				//self::BALANCE_CARRIER_OWES_COMPANY_CASH,
            );
            $check = array(
                self::BALANCE_COD_TO_CARRIER_CHECK,
                //self::BALANCE_COD_TO_DTERMINAL_CHECK,
                //self::BALANCE_COD_TO_PTERMINAL_CHECK,
                self::BALANCE_COP_TO_CARRIER_CHECK,
				//self::BALANCE_COMPANY_OWES_CARRIER_CHECK,
				//self::BALANCE_CARRIER_OWES_COMPANY_CHECK,
            );

            $pickup = array(
                self::BALANCE_COP_TO_CARRIER_CASH,
                self::BALANCE_COP_TO_CARRIER_CHECK,
            );

            $codcop_amount = $this-> total_tariff + $this->pickup_terminal_fee + $this->dropoff_terminal_fee - $this->total_deposit;
           

            if ($codcop_amount > 0) {
                if (in_array($this->balance_paid_by, $codcops)) {
                    //9. COD/COP Amount:
                    $command .= $codcop_amount . ",";
                    //10. COD/COP Method:
                    if (in_array($this->balance_paid_by, $cash_certified_funds)) {
                        $command .= "cash/certified funds,";
                    } else {
                        $command .= "check,";
                    }
                    //11. COD/COP Timing:
                    if (in_array($this->balance_paid_by, $pickup)) {
                        $command .= "pickup,";
                    } else {
                        $command .= "delivery,";
                    }
                } else {
                    //9. COD/COP Amount:
                    $command .= "0.00,";
                    //10. COD/COP Method:
                    $command .= "cash/certified funds,";
                    //11. COD/COP Timing:
                    $command .= "delivery,";
                }
            } else {
                //9. COD/COP Amount:
                $command .= "0.00,";
                //10. COD/COP Method:
                $command .= "cash/certified funds,";
                //11. COD/COP Timing:
                $command .= "delivery,";
            }

            //12. Remaining Balance Payment Method:
            $command .= "none,";
            //13. Ship Method:
            $command .= strtolower($this->getShipVia()) . ",";
            
            //18. Vehicle(s):
             
            $vs = array();
			$inopValue = "operable";
			
			if (count($vehicles) > 0) {
                foreach ($vehicles as $vehicle) {
                    /*if (in_array($vehicle->type, array("Boat", "Car", "Motorcycle", "Pickup", "RV", "SUV", "Travel Trailer", "Van"))) {
                        $type = $vehicle->type;
                    } else {
                        $type = "Other: " . $vehicle->type;
                    }
					*/
					$valueVT = $this->getVehicleType($vehicle->type);
					if($valueVT != -1){
						 $type = $valueVT;
					} else {
                        $type = "Other: " . $vehicle->type;
                    }

					if($vehicle->inop == 1)
					   $inopValue = "inop";
                   
                   $vs[] = addcslashes($vehicle->year . "|" . $vehicle->make . "|" . $vehicle->model . "|" . $type, ",");
				  
                }
                
            }
			
			$firstAvail = $this->getPostDate("Y-m-d");
			if(strtotime(trim($this->getFirstAvail("Y-m-d"))) >= strtotime(date('Y-m-d')) )
		         $firstAvail = $this->getFirstAvail("Y-m-d");
		    elseif(strtotime(trim($this->getPostDate("Y-m-d"))) < strtotime(date('Y-m-d')) )
			     $firstAvail =  date('Y-m-d');
			
			//14. Vehicle Operable:
            $command .= $inopValue . ",";//$this->getInopExportName() . ",";
            //15. First Available (YYYY-MM-DD):
            $command .= $firstAvail. ",";//$this->getFirstAvail("Y-m-d") . ",";
            //16. Display Until:
			$command .= date("Y-m-d",strtotime(date("Y-m-d", strtotime($firstAvail)) . "+1 month")) . ",";//$firstAvail . ",";//$this->getFirstAvail("Y-m-d") . ",";
            //$command .= $firstAvail. ","; //date("Y-m-d"). ",";//$this->getFirstAvail("Y-m-d") . ","; 
			
            //17. Additional Info:
			/*
            $notes = $this->getNotes();
            if (isset($notes[Note::TYPE_FROM][0])) {
                $command .= addcslashes(substr($notes[Note::TYPE_FROM][0]->text, 0, 60), ",") . ",";
            } else {
                $command .= ",";
            }
			*/
			
			$command .= addcslashes(substr($this->information, 0, 60), ",").",";
			
			if (count($vehicles) > 0) {
			    $command .= implode(";", $vs);
			}

            //strip asterisks
            //end of command
            $message = $command_uid . $command_act . str_replace("*", "", $command) . "*";
			
			print "<br> CENTRAL DISPATCH : ".$message;
			
			  $mailData = array(
							'entity_id' => $this->id,	
                            'fromAddress' => $email_from,
                            'toAddress' => self::CENTRAL_DISPATCH_EMAIL_TO,
                            'cc' => "",
                            'bcc' => "",
                            'subject' => "FD Test Import",
                            'body' => $message,
						    'type' => 1,
							'sent' => 0
                    );
				$this->db->insert('app_mail_sent', $mailData);
			
				
				
               try {
                /*$mail = new FdMailer(true);
				
                $mail->IsHTML(false);
                $mail->Body = $message;
                $mail->Subject = "FD Test Import";
                $mail->SetFrom($email_from);
                $mail->AddAddress(self::CENTRAL_DISPATCH_EMAIL_TO, "Central Dispatch");
                //$mail->AddBCC(self::CENTRAL_DISPATCH_EMAIL_BCC);
				
                ob_start();
				
                $ret = $mail->Send();
				
				//$ret = $mail->SendToCD($mailData,1);
				
                $mailer_output = ob_get_clean();
                if (!$ret) {
                    throw new Exception($mailer_output . "\n");
                } else {
                    History::add($this->db, $this->id, "CENTRAL DISPATCH", ($posttype == 1 ? "ADD ORDER TO CD" : "REPOST ORDER"), date("Y-m-d H:i:s"));
                    $ret = true;
					
                }*/
				
				History::add($this->db, $this->id, "CENTRAL DISPATCH", ($posttype == 1 ? "ADD ORDER TO CD" : "REPOST ORDER"), date("Y-m-d H:i:s"));
                    $ret = true;
            } catch (phpmailerException $e) {
                $ret = $e->getMessage();
				
            } catch (Exception $e) {
                
            }

            //echo $ret;
            //send mail
            //update entity
            //update history
            //update log
            //unpost action
            //edit/save action
            //repost with confirm if edit
            //add to the settings post
            //only on FreightDragon Freightboard or FreightDragon And CentralDispatch

            //4. In what cases we should delete posted to CD order from CD?
            //Once the unit is dispatched to a carrier or when removed from the freight board or when placed on hold status
        }
		else{
			print "<br>entity : settings not found.<br>";
		  $ret = false;
		}
		
		return $ret;
    }
	
	function getVehicleType($vehicleTypeValue)
	{
	    $vehicleType = array("Coupe" => "car", "Sedan Small" => "car", "Sedan Midsize" => "car", "Sedan Large" => "car",  "Convertible" => "car", "Pickup Small" => "Pickup", "Pickup Crew Cab" => "Pickup", "Pickup Full-size" => "Pickup", "Pickup Extd. Cab" => "Pickup", "RV" => "RV", "SUV Small" => "SUV", "SUV Mid-size" => "SUV", "SUV Large" => "SUV", "Travel Trailer" => "Travel Trailer", "Van Mini" => "Van", "Van Full-size" => "Van", "Van Extd. Lenght" => "Van", "Van Pop-Top" => "Van", "Motorcycle" => "Motorcycle", "Boat" => "Boat");
		
         if (array_key_exists($vehicleTypeValue, $vehicleType)) {
			  return $vehicleType[$vehicleTypeValue];
		 }
		 return -1;
	}
	
	
	public function getFiles($id)
    {
		  $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
					AND `name_original` LIKE  'Signed%'
                 ORDER BY u.date_uploaded Desc limit 0,1";
        $FilesList = $this->db->selectRows($sql);
		
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            
        }
        return $files;
    }
	
	public function getCommercialFiles($id)
    {
		  $sql = "SELECT u.*
                  FROM app_entity_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.entity_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
					AND `name_original` LIKE  'B2B%'
                 ORDER BY u.date_uploaded Desc limit 0,1";
        $FilesList = $this->db->selectRows($sql);
		
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            
        }
        return $files;
    }
	
	public function getCommercialFilesShipper($id)
    {
		  $sql = "SELECT u.*
                  FROM app_accounts_uploads au
                  LEFT JOIN app_uploads u ON au.upload_id = u.id
                 WHERE au.account_id = '" . $id . "'
                    AND u.owner_id = '" . getParentId() . "'
					AND `name_original` LIKE  'B2B%'
                 ORDER BY u.date_uploaded Desc limit 0,1";
        $FilesList = $this->db->selectRows($sql);
		
        $files = array();
        foreach ($FilesList as $i => $file) {
            $files[$i] = $file;
            
        }
        return $files;
    }
	
	public function getPaymentOption($type)
    {
	
		$optionStr = "";	
		if($type == Entity::WIRE_TRANSFER)
		  $optionStr = "Wire - Transfer";
		elseif($type == Entity::MONEY_ORDER)
		  $optionStr = "Money Order";
		elseif($type == Entity::CREDIT_CARD)
		  //if($format == true)
		    $optionStr = "Credit Card";
		  //else
		    //$optionStr = "Credit Card";
		elseif($type == Entity::PARSONAL_CHECK)
		  $optionStr = "Personal Check";
		elseif($type == Entity::COMPANY_CHECK)
		  $optionStr = "Company Check";
		elseif($type == Entity::ACH)
		  $optionStr = "ACH";
		else
		  $optionStr = "N/A";
		  
		  return $optionStr;
	}
	
	
	public function processAuthorize($pay)
    {
        $api_login = $pay['anet_api_login_id'];
        $api_pwd = $pay['anet_trans_key'];
//			$notify_email = $pay['notify_email'];
        $api_amount = $pay['amount'];

        $pay_success = false;
        $pay_reason = "";
        $transaction_id = "";

        $transaction = new AuthorizeNetAIM($api_login, $api_pwd);
        $transaction->setSandbox($GLOBALS['CONF']['anet_sandbox']);

        $transaction->setFields(
            array(
                'amount' => $api_amount
            , 'card_num' => $pay['cc_number']
            , 'exp_date' => $pay['cc_month'] . "/" . $pay['cc_year']
            , 'card_code' => $pay['cc_cvv2']
            , 'first_name' => $pay['cc_fname']
            , 'last_name' => $pay['cc_lname']
            , 'address' => $pay['cc_address']
            , 'city' => $pay['cc_city']
            , 'state' => $pay['cc_state']
            , 'zip' => $pay['cc_zip']
            , 'description' => "Freight Dragon: Order#" . $pay['order_number']
            , 'invoice_num' => $pay['order_number']
            )
        );
        $response = $transaction->authorizeAndCapture();
        if ($response->approved) {
            return array("success" => true
            , "transaction_id" => $response->transaction_id
            );
        } else {
            return array("success" => false
            , "error" => $response->response_reason_text
            );
        }
    }
	
	public function processMDSIP($pay)
    {
        $api_login = $pay['gateway_api_username'];
        $api_pwd = $pay['gateway_api_password'];
//			$notify_email = $pay['notify_email'];
        $api_amount = $pay['amount'];

        $pay_success = false;
        $pay_reason = "";
        $transaction_id = "";
		//print_r($pay);

        $gw = new gwapi;
        $gw->setLogin($api_login, $api_pwd);
		
									
			$gw->setBilling(
							     $pay['cc_fname'],
								 $pay['cc_lname'],
								 $pay['company'],
								 $pay['cc_address'],
								 $pay['address2'], 
								 $pay['cc_city'],
					             $pay['cc_state'],
								 $pay['cc_zip'],
								 "US",
								 $pay['phone1'],
								 $pay['phone2'],
								 $pay['email'],
					             "www.freightdragon.com");
			
			$gw->setShipping($pay['cc_fname'],
								 $pay['cc_lname'],
								 $pay['company'],
								 $pay['cc_address'],
								 $pay['address2'], 
								 $pay['cc_city'],
					             $pay['cc_state'],
								 $pay['cc_zip'],
								 "US",
								 $pay['email'],
					             "www.freightdragon.com");
			
			$gw->setOrder($pay['orderid'],$pay['orderdescription'],$pay['tax'], $pay['shipping'], $pay['cc_zip'],$pay['ipaddress']);
			
		
			$r = $gw->doSale($api_amount,$pay["cc_number"],$pay["cc_month"] . $pay["cc_year"],$pay["cc_cvv2"]);
			//$r = $gw->doAuth("0.25","4246315182103521","1019","243");
			//$r = $gw->doSale("0.25","41111111111111","0719");
			 $response = $gw->responses['responsetext'];
			 
				

        if ($response == "APPROVED") {
            return array("success" => true
            , "transaction_id" => $gw->responses['transactionid']
            );
        } else { 
            return array("success" => false
            , "error" => $gw->responses['responsetext']
            );
        }
    }
	
	public function updateHash(){
		    if ($this->hash=="") {
							$update_arr = array(
								   'hash' => self::findFreeHash($this->db)
								);
							
							$this->update($update_arr);
            	}	
	}
	
	public function updateHeaderTable()
	{
	   $result = $this->db->query("CALL insert_app_order_header('".$this->id."')");	
	}
	
	public function convertCreatedLeadToOrder()
    {
		if ($this->type != self::TYPE_CLEAD)
            throw new FDException("Invalid Entity type");
       // if ($this->status != self::STATUS_CACTIVE)
            //throw new FDException("Invalid Entity status");
			//print "1111111";
        $this->setType(Entity::TYPE_ORDER);

        $prefix = $this->getNewPrefix();
        $this->update(array(
            'ordered' => date('Y-m-d H:i:s'),
            'prefix' => $prefix,
			'status' => self::STATUS_ACTIVE
            //'avail_pickup_date' => $this->est_ship_date,
        ));
		//$this->sendSystemEmail(691);
		//$this->sendSystemEmail(691, array(), false);
		if($this->getAssigned()->parent_id==1)
		  $this->sendSystemEmail(691, array(), false);
		elseif($_SESSION['member_id']==159)
		  $this->sendSystemEmail(712, array(), false);
    }
	
	public function convertCreatedLleadToQuote()
    {
		
		
        if ($this->type != self::TYPE_CLEAD)
            throw new FDException("Invalid Entity type");
        //if ($this->status != self::STATUS_CACTIVE && $this->status != self::STATUS_CASSIGNED)
           // throw new FDException("Invalid Entity status");
        //$this->setType(Entity::TYPE_QUOTE);  
		$this->setStatus(Entity::STATUS_CQUOTED);
        $prefix = $this->getNewPrefix();
        $this->update(array('quoted' => date('Y-m-d H:i:s'), 'prefix' => $prefix));
        $this->getVehicles(true);
/*
	    $followup = new FollowUp($this->db);
	    $days = (int)$this->getAssigned()->getDefaultSettings()->first_quote_followup;
		$followup->setFolowUp(0, date("M-d-Y", mktime(0, 0, 0, (int)date("m"), (int)date("d")+$days, (int)date("Y"))), $this->id);
	*/
	}
	
	public function make_payment()
	{
		
		$member = $this->getAssigned();
		$card_batch_payment = $member->getDefaultSettings()->card_batch_payment;
		$card_batch_setting = $member->getDefaultSettings()->card_batch;
		$card_payment_esigned = $member->getDefaultSettings()->card_payment_esigned;
			
		 // print "<br>--------Member Start ".$member->id."-------setting :".$card_batch_setting."------status:".$this->status."---customer_balance_paid_by: ".$this->customer_balance_paid_by."----<br><br>";
		  
		  if($card_batch_payment != 0 && $card_batch_setting != 0 && $this->customer_balance_paid_by ==3 && $this->auto_payment ==0)
		  {
			  $statusValidate = false;
			  
			  if($card_batch_setting == $this->status){
			      $statusValidate = true;
				  if($card_payment_esigned == 1)
				  {
					  if($this->esigned==1 || $this->esigned==2)
					   $statusValidate = true;
					  else
				        $statusValidate = false; 
				  }
				  
			  }
			  /*
			 elseif($card_payment_esigned == 1)
			  {
				  if($this->esigned==1 || $this->esigned==2)
				   $statusValidate = true;
			  }
			  */
			  if($statusValidate )
			  {
						
				   $paymentManager = new PaymentManager($this->db);
					$depositRemains = 0;
					$shipperRemains = 0;
					$amountType =0;
					// We owe them
					switch ($this->balance_paid_by) {
						//case Entity::BALANCE_INVOICE_CARRIER:
						case self::BALANCE_COP_TO_CARRIER_CASH:
						case self::BALANCE_COP_TO_CARRIER_CHECK:
						case self::BALANCE_COP_TO_CARRIER_COMCHECK:
						case self::BALANCE_COP_TO_CARRIER_QUICKPAY:
						case self::BALANCE_COD_TO_CARRIER_CASH:
						case self::BALANCE_COD_TO_CARRIER_CHECK:
						case self::BALANCE_COD_TO_CARRIER_COMCHECK:
						case self::BALANCE_COD_TO_CARRIER_QUICKPAY:
							$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
							
							$depositRemains = $this->getTotalDeposit(false) - $shipperPaid;;
							$shipperRemains = $this->getCost(false) + $this->getTotalDeposit(false) - $shipperPaid;
							$amountType =1;
							break;
						//--
						case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
						case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
						case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
						case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
						case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
						
							$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_COMPANY, Payment::SBJ_CARRIER, false);
							$shipperPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_SHIPPER, Payment::SBJ_COMPANY, false);
							
							$depositRemains = $this->getTotalDeposit(false) - $shipperPaid;
							$shipperRemains = $this->getCost(false) + $this->getTotalDeposit(false) - $shipperPaid;
							$amountType =2;
							break;
						case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
						case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
						case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
						case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
							$carrierPaid = $paymentManager->getFilteredPaymentsTotals($this->id, Payment::SBJ_CARRIER, Payment::SBJ_COMPANY, false);
							
							$depositRemains = $this->getTotalDeposit(false) - $carrierPaid;
							$shipperRemains = $this->getCost(false) + $this->getTotalDeposit(false) - $carrierPaid;
							$amountType =3;
							break;
						default:
							break;
					}
					
					 if($depositRemains<0)
					   $depositRemains = 0;
					 if($shipperRemains<0)  
					   $shipperRemains = 0;
					   
					  if($amountType==1 || $amountType==3)  
					   $shipperRemains = $depositRemains;
					   
					
						
						$paymentCard = new Paymentcard($this->db);
						
						$paymentCard->key = $daffny->cfg['security_salt'];
						$paymentCard->loadLastCC($this->id, (int)$member->id);
						//print "<pre>";
						if ($paymentCard->isLoaded()) { 
							
							$cc_fname 		= $paymentCard->cc_fname;
							$cc_lname 		= $paymentCard->cc_lname;
							$cc_address 	= $paymentCard->cc_address;
							$cc_city 		= $paymentCard->cc_city;
							$cc_state 		= $paymentCard->cc_state;
							$cc_zip 		= $paymentCard->cc_zip;
							$cc_cvv2 		= $paymentCard->cc_cvv2;
							$cc_number 		= $paymentCard->cc_number;
							$cc_type 		= $paymentCard->cc_type;
							$cc_month 		= $paymentCard->cc_month;
							$cc_year 		= $paymentCard->cc_year;
						} else {
							$entityCreditCard = $this->getCreditCard();
							
							$cc_fname 	= $entityCreditCard->fname; 
							$cc_lname 	= $entityCreditCard->lname;
							$cc_address = $entityCreditCard->address;
							$cc_city 	= $entityCreditCard->city;
							$cc_state 	= $entityCreditCard->state;
							$cc_zip 	= $entityCreditCard->zip; 
							$cc_cvv2 	= $entityCreditCard->cvv2;
							$cc_number 	= $entityCreditCard->number;
							$cc_type 	= $entityCreditCard->type;
							$cc_month 	= $entityCreditCard->month;
							$cc_year 	= $entityCreditCard->year;
						}
					
						$err ="";
						$DefaultSettings = $member->getDefaultSettings();
						if (in_array($DefaultSettings->current_gateway, array(1, 2,3))) {
		
									if ($DefaultSettings->current_gateway == 1) { //PayPal
										if (trim($DefaultSettings->paypal_api_username) == ""
											|| trim($DefaultSettings->paypal_api_password) == ""
											|| trim($DefaultSettings->paypal_api_signature) == ""
										) {
											$err .= "PayPal: Please complete API Credentials under 'My Profile > Default Settings'";
										}
									}
		
									if ($DefaultSettings->current_gateway == 2) { //Authorize.net
										if (trim($DefaultSettings->anet_api_login_id) == ""
											|| trim($DefaultSettings->anet_trans_key) == ""
										) {
											$err .= "Autorize.net: Please complete API Credentials under 'My Profile > Default Settings'";
										}
									}
									
									if ($DefaultSettings->current_gateway == 3) { //Authorize.net  
										if (trim($DefaultSettings->gateway_api_username) == ""
											|| trim($DefaultSettings->gateway_api_password) == ""
										) {
											$this->err[] = "Payment Gateway: Please complete API Credentials under 'My Profile > Default Settings'";
										}
									}
								} else {
									$err .= "There is no active Payments Gateway under 'My Profile > Default Settings'";
								}
		
								$amount = $shipperRemains;
								
								if ($amount <= 0) {
									$err .= 'Amount can not be $'.$amount.'.';
								}
								if($cc_number=="" || $cc_type=="" || $cc_number=="" ||$cc_month=="" ||$cc_year=="" )
								{
									$err .= 'Credit card values are missing.';
								}
								
								$arr = array(
							   // "other_amount" => post_var("other_amount")
							   // , 
								"gw_pt_type" => "balance"
								, "cc_fname" => $cc_fname
								, "cc_lname" => $cc_lname
								, "cc_address" =>  $cc_address
								, "cc_city" =>  $cc_city
								, "cc_state" => $cc_state
								, "cc_zip" =>  $cc_zip
								, "cc_cvv2" => $cc_cvv2
								, "cc_number" => $cc_number
								, "cc_type" => $cc_type
								, "cc_month" => $cc_month
								, "cc_year" =>  $cc_year
								, "cc_type_name" => Payment::getCCTypeById($cc_type)
								);
								
								$pay_arr = $arr + array(
									"amount" => (float)$amount
								, "paypal_api_username" => trim($DefaultSettings->paypal_api_username)
								, "paypal_api_password" => trim($DefaultSettingss->paypal_api_password)
								, "paypal_api_signature" => trim($DefaultSettings->paypal_api_signature)
								, "anet_api_login_id" => trim($DefaultSettings->anet_api_login_id)
								, "anet_trans_key" => trim($DefaultSettings->anet_trans_key)
								, "gateway_api_username" => trim($DefaultSettings->gateway_api_username)
                                , "gateway_api_password" => trim($DefaultSettings->gateway_api_password)
								, "notify_email" => trim($DefaultSettings->notify_email)
								, "order_number" => trim($this->getNumber())
								);
							   
							  
								$ret = array();
								
								if ($err == "") {
									
									if ($DefaultSettings->current_gateway == 2) { //Authorize.net
									    $ret = $this->processAuthorize($pay_arr);
									}
									if ($DefaultSettings->current_gateway == 1) { //PayPal
										//$ret = $this->processPayPal($pay_arr);
									}
									
									if ($DefaultSettings->current_gateway == 3) { //MDSIP
										$shipper = $this->getShipper();
										
										$pay_arr1 = $pay_arr + array(
											'orderid' => $this->id,
											'orderdescription' => $this->getNumber(),
											'tax' => '',
											'shipping' => 1,
											'ponumber' => 2,					
											'ipaddress' => '',	
											
											'fname' => $shipper->fname,
											'lname' => $shipper->lname,
											'email' => $shipper->email,
											'company' => $shipper->company,
											'phone1' => formatPhone($shipper->phone1),
											'phone2' => formatPhone($shipper->phone2),
											'mobile' => $shipper->mobile,
											'fax' => $shipper->fax,
											'address1' => $shipper->address1,
											'address2' => $shipper->address2,
											'city' => $shipper->city,
											'state' => $shipper->state,
											'zip' => $shipper->zip,
											'country' => $shipper->country,
											'shipper_type' => $shipper->shipper_type,
											'shipper_hours' => $shipper->shipper_hours
										);
										
							
										
										$ret = $this->processMDSIP($pay_arr1);
									}
				
									//------change------
								    if (isset($ret['success']) && $ret['success'] == true) {
									 //if(0){
										
										
										//insert
										$insert_arr['entity_id'] = (int)$this->id;
										$insert_arr['number'] = Payment::getNextNumber($this->id, $this->db);
										$insert_arr['date_received'] = date("Y-m-d H:i:s");
										$insert_arr['fromid'] = Payment::SBJ_SHIPPER;
										$insert_arr['toid'] =  Payment::SBJ_COMPANY;
										$insert_arr['entered_by'] = $member->id;
										
										$insert_arr['amount'] = number_format((float)$pay_arr['amount'], 2, '.', '');
										$insert_arr['notes'] = ($DefaultSettings->current_gateway == 2 ? "Authorize.net " : "PayPal ") . $ret['transaction_id'];
										$insert_arr['method'] = Payment::M_CC;
										$insert_arr['transaction_id'] = $ret['transaction_id'];
										$insert_arr['cc_number'] = substr($pay_arr['cc_number'], -4);
										$insert_arr['cc_type'] = $pay_arr['cc_type_name'];
										$insert_arr['cc_exp'] = $pay_arr['cc_year'] . "-" . $pay_arr['cc_month'] . "-01";
										
										$payment = new Payment($this->db);
										
										
										try{
										    $payment->create($insert_arr);
										} catch (FDException $e) {
												//print $e;
												
											}
										
										
											
										$note_array = array(
											"entity_id" => $this->id,
											"sender_id" => $member->id,
											"type" => 3,
											"system_admin" => 2,
											"text" => "<green>CREDIT CARD PROCESSED FOR THE AMOUNT OF $ ".number_format((float)$pay_arr['amount'], 2, '.', ''));
										$note = new Note($this->db);
										$note->create($note_array);
										
										
										
										   $sql = "INSERT INTO app_batch_payment_log (`entity_id`, `transaction_id`, `type`, `msg`, `data`) 
											       VALUES('".$this->id."','".$ret['transaction_id']."','2','Your payment has been processed.','". json_encode($insert_arr)."') ";
										    $this->db->query($sql);
											
											
											$sql = "INSERT INTO app_payments_cron_queue (`payment_id`,`entity_id`, `number`, `date_received`, `fromid`, `toid`, `amount`, `method`, `transaction_id`, `entered_by`, `notes`, `cc_number`, `cc_type`, `cc_exp`, `cc_auth`, `check`) 
											       VALUES('".$payment->id."','".$payment->entity_id."','".$payment->number."','".$payment->date_received."','".$payment->fromid."','".$payment->toid."','".$payment->amount."','".$payment->method."','".$payment->transaction_id."','".$payment->entered_by."','".$payment->notes."','".$payment->cc_number."','".$payment->cc_type."','".$payment->cc_exp."','".$payment->cc_auth."','".$payment->check."') ";
										    $this->db->query($sql);
												//print "====succ3=====";
												
										//print "Your payment has been processed.";
										
										return 1;
										
									} else {
										
										$err = $ret['error'];
										
										$note_array = array(
											"entity_id" => $this->id,
											"sender_id" => $member->id,
											"type" => 3,
											"system_admin" => 2,
											"text" => "<red>Payment Error:".$ret['error']
											);
										//------change------
										$note = new Note($this->db);
										$note->create($note_array);
										
										
										
										
													
										 $sql = "INSERT INTO app_batch_payment_log (`entity_id`, `transaction_id`, `type`, `msg`, `data`) 
											       VALUES('".$this->id."','".$ret['transaction_id']."','1','Payment Error: ".json_encode($ret)."','". json_encode($pay_arr)."') ";
										    $this->db->query($sql);
											
									
									$mail = new FdMailer(true);
									$mail->isHTML();
									$mail->Body = 'Credit card is declined for ORDER# '.$this->number;
									$mail->Subject = "Payment Error:".$ret['error'];
									$mail->AddAddress($this->getAssigned()->email, $this->getAssigned()->contactname);
									//$mail->AddCC($order->getAssigned()->email, $order->getAssigned()->contactname);
									//$mail->setFrom('noreply@freightdragon.com');
									if($this->getAssigned()->parent_id == 1)
									   $mail->setFrom('noreply@freightdragon.com');
									else
									  $mail->setFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email);
									
									//$mail->AddAttachment($filePath, 'Order.pdf');
									
									  
									$mail->send();
									
									
									
									  return 0;
									}
								}    /// payment ended
								else
								{
								  
										
											$log_arr = array(
														'entity_id' => $this->id,
														'transaction_id' => '',
														'type' => 1,
														'msg' => $err,
														'data' => json_encode($pay_arr),
														
													);
						
													$this->db->insert("app_batch_payment_log", $log_arr);
											
											return 0;
								}
								
					
			 } // Validate setting with type
			 else
			   return 0;//print "Wrong condition";
			 
		  } 
		  else
		    return 0;// card_batch_setting check	
		
    }
	
function match_carrier() {
		
		$ccArr = array();
        $carrierIdArr = array();
		$numOfMails = 0;
    $MatchCarrierObj = new MatchCarrier($this->db);
    $origin = $this->getOrigin();
	$destination = $this->getDestination();


		$sql="SELECT ac.id,ac.email  from (

SELECT
	distinct ORG.carrierID
FROM
	(SELECT 
		AAR.account_id as carrierID,
		AAR.id as RoutingID,
		AR.city,
		AR.id,
		AR.lati,
		AR.long,
		AR.state,
		AR.type,
		AR.zip
	FROM app_account_route as AAR inner join app_route AR
	ON AAR.id = AR.route_id
	and type = 'ORG'
	and zip = '".$origin->zip."') as ORG INNER JOIN  
	(SELECT 
		AARD.account_id as carrierID,
		AARD.id as RoutingID,
		ARD.city,
		ARD.id,
		ARD.lati,
		ARD.long,
		ARD.state,
		ARD.type,
		ARD.zip
	FROM app_account_route as AARD inner join app_route ARD
	ON AARD.id = ARD.route_id
	and ARD.type = 'DES'
	and ARD.zip = '".$destination->zip."' ) AS DST 
ON ORG.RoutingID = DST.RoutingID


union 

SELECT
	distinct ORG.carrierID
FROM
	(SELECT 
		AAR.account_id as carrierID,
		AAR.id as RoutingID,
		AR.city,
		AR.id,
		AR.lati,
		AR.long,
		AR.state,
		AR.type,
		AR.zip
	FROM app_account_route as AAR inner join app_route AR
	ON AAR.id = AR.route_id
	and type = 'DES'
	and zip = '".$origin->zip."') as ORG INNER JOIN
	(SELECT 
		AARD.account_id as carrierID,
		AARD.id as RoutingID,
		ARD.city,
		ARD.id,
		ARD.lati,
		ARD.long,
		ARD.state,
		ARD.type,
		ARD.zip
	FROM app_account_route as AARD inner join app_route ARD
	ON AARD.id = ARD.route_id
	and ARD.type = 'ORG'
	and ARD.zip = '".$destination->zip."' ) AS DST 
ON ORG.RoutingID = DST.RoutingID

) as Z INNER JOIN app_accounts as ac
ON Z.carrierID = ac.id

";	


				   $result = $this->db->query($sql);

					if ($this->db->num_rows() > 0) {
                     	while ($row = $this->db->fetch_row($result)) {
							
							
									$ccArr[] = $row['email'];
									 $carrier_arr = array(
			                             'owner_id' => $_SESSION['member_id'],
										 'entity_id' => $this->id,
										 'account_id' => $row['id'],
										 'email' => $row['email'],
										 'carrier_pay_stored' =>$this->carrier_pay_stored
			            
		                             );
									$MatchCarrierObj->create($carrier_arr);
									
									$carrierIdArr[]=$MatchCarrierObj->id;
									
									$numOfMails++;
						   }
						}
						  
             //----------------------------------------------Other carreir in radius ---------------------------------------------------------
		
	  $where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o 
							ON o.id = e.origin_id where e.id = ".$this->id.")";
			//print  $where;
			
			$rows_origin = $this->db->selectRows('distinct `Lat`, `vLong`,
						
						lat + (40 / 69.1) as origin_lat_front,
						
						lat - (40 / 69.1) as origin_lat_back,
						
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as origin_long_front,
						
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as origin_long_back', " fd_zipcode_database ", " WHERE " . $where);
			
			  if(!empty($rows_origin))
			  {
					///$messages = "<p>Order ID/Entity Id resposted</p><br>";
					//$entities = array();
					//print "<pre>";
					//print_r($rows_origin);
				
			  }
			  
			  
			  $where = " `Zipcode` = (SELECT         o.zip as origin_zip
							FROM  app_entities e
							Left Outer join app_locations o 
							ON o.id = e.destination_id where e.id = ".$this->id.")";
			//print  $where;
			
			$rows_destination = $this->db->selectRows('distinct `Lat`, `vLong`,
						
						lat + (40 / 69.1) as destination_lat_front,
						
						lat - (40 / 69.1) as destination_lat_back,
						
						vLong + (40 / (69.1 * cos(lat/57.3)) ) as destination_long_front,
						
						vLong - (40 / (69.1 * cos(lat/57.3)) ) as destination_long_back', " fd_zipcode_database ", " WHERE " . $where);
			
			  if(!empty($rows_destination))
			  {
					
					//print "<br><br><pre>";
					//print_r($rows_destination);
				
			  }
			  
			if(count($rows_origin)>0 && count($rows_destination)>0)
			 {
				 
				 
				 $sql = " 
				 
				 SELECT acc.id,acc.email FROM 
					  app_entities en
					Left Outer Join  app_dispatch_sheets    ad
					ON en.id = ad.entity_id
					Left Outer Join app_accounts acc 
					ON ad.account_id = acc.id
					INNER JOIN 
					 (
						 SELECT origin.id
									from ( 
												  SELECT  e.id,o.zip,z.Zipcode       
											FROM  app_entities e
											Left Outer join app_locations o 
											ON o.id = e.origin_id 
											inner join (SELECT distinct Zipcode
											FROM `fd_zipcode_database` WHERE lat <= ".$rows_origin[0]['origin_lat_front']."
											
																				and lat >= ".$rows_origin[0]['origin_lat_back']."
																				
																				and vLong <= ".$rows_origin[0]['origin_long_front']."
																				
																				and vlong >= ".$rows_origin[0]['origin_long_back'].") as z
											on o.zip = z.Zipcode
											where e.status = 9 OR e.status = 6 OR e.status = 8
											AND e.dispatched IS NOT NULL 
											AND e.delivered IS NOT NULL 
								) as origin
							
							INNER JOIN
							
								( 
										 SELECT  e.id,o.zip,z.Zipcode       
										FROM  app_entities e
										Left Outer join app_locations o 
										ON o.id = e.destination_id
										inner join (SELECT distinct Zipcode
										FROM `fd_zipcode_database` WHERE lat <= ".$rows_destination[0]['destination_lat_front']."
										
																			and lat >= ".$rows_destination[0]['destination_lat_back']."
																			
																			and vLong <= ".$rows_destination[0]['destination_long_front']."
																			
																			and vlong >= ".$rows_destination[0]['destination_long_back'].") as z
										on o.zip = z.Zipcode
										where (e.status = 9 OR e.status = 6 OR e.status = 8)
										AND e.dispatched IS NOT NULL 
										AND e.delivered IS NOT NULL 
								) as destination
								
							   ON origin.id = destination.id
					   ) as z	on en.id = z.id	
					 Where acc.company_name !=''
					 group by acc.id,acc.company_name
			 
			";
			
				 
				 //print $sql;
					$result =$this->db->query($sql);
					 
					
					
					//$result = $this->db->query("CALL fd_matching_carrier('".$origin->zip."',  '".$destination->zip."', 30)");	
					if ($this->db->num_rows() > 0) {
						
						while ($row = $this->db->fetch_row($result)) {
						  	if(!in_array($row['email'],$ccArr))	{
								$ccArr[] = $row['email'];
								//print "<br>".$row['email'];
								
									 $carrier_arr = array(
			                             'owner_id' => $_SESSION['member_id'],
										 'entity_id' => $this->id,
										 'account_id' => $row['id'],
										 'email' => $row['email'],
										 'carrier_pay_stored' => $this->carrier_pay_stored
										 
			            
		                             );
									$MatchCarrierObj->create($carrier_arr);
									
									$carrierIdArr[] = $MatchCarrierObj->id;
									$numOfMails++;
									
							}
						}
					}
			}
			 
			 
			 /*'
			 '//$result = $this->db->query("CALL fd_matching_carrier('".$origin->zip."',  '".$destination->zip."', 30)");	
					if ($this->db->num_rows() > 0) {
						
						while ($row = $this->db->fetch_row($result)) {
						  		$ccArr[] = $row['email'];
								//print "<br>".$row['email'];
								
									 $carrier_arr = array(
			                             'owner_id' => $_SESSION['member_id'],
										 'entity_id' => $this->id,
										 'account_id' => $row['id'],
										 'email' => $row['email'],
										 'carrier_pay_stored' => $this->carrier_pay_stored
										 
			            
		                             );
									$MatchCarrierObj->create($carrier_arr);
									
									$carrierIdArr[] = $MatchCarrierObj->id;
									$numOfMails++;
									
							
						}
					}
			 
			 
			 */
			/************ Testing ***********/ 
						
						
						$ccArr = array();
						$ccArr[] = "neeraj@freightdragon.com";
						$ccArr[] = "freigtdragon@gmail.com";
						$ccArr[] = "StefanoMadrigal@gmail.com";
						$ccArr[] = "admin@freightdragon.com";
						$numOfMails = 4;
						
						
						$this->sendEmailCustomSend(707, array(), false,$ccArr,$carrierIdArr,$numOfMails);
	}
	
	
	protected function sendEmailCustomSend($type, $add = array(), $is_default = true,$ccArr= array(),$carrierIdArr= array(),$numOfMails=0)
    {
		
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
      try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            
			$mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();
            //$mail->AddAddress($emailTemplate->getToAddress());

            $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
           // if(trim($emailArr['cc'])!="")
             //$mail->AddCC("nkumar@agilesoftsolutions.com");
			    $carrierIdArrSize = sizeof($carrierIdArr);
			    $MatchCarrierObj = new MatchCarrier($this->db);
		        $i=0;
				foreach ($ccArr as $ccEmail) {
					$email_ext = trim($ccEmail);
					if ($email_ext != "") {
						//$email_ext = trim("neeraj@freightdragon.com"); 
						//print "<br>--".$email_ext;
						$mail->ClearAddresses();
						$mail->AddAddress($email_ext);
						$mail->Send();
						
						if($carrierIdArrSize>0)
						{
							$MatchCarrierObj->load($carrierIdArr[$i]);
							$MatchCarrierObj->update(array('mail_status'=>1,'num_mail'=>$numOfMails));
						$i++;
						}
					}
				}
			
 
          
			//$mail->AddBCC("neeraj@freightdragon.com");
			//$mail->AddBCC("admin@ritewayautotransport.com");
			
           ///$mail->Send();
			
			
			
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }
	
	
	function check_match_carrier($entity_old)
	{
		
		       $Flag= false;
			   
			   if($this->getCarrierPay() != $entity_old->getCarrierPay())
			   {
				   print $this->getCarrierPay()."---".$entity_old->getCarrierPay()."--check carrier amount";
				   $Flag= true;
			   }
			   
			  
				if(!$Flag)
				{
					   $origin = $this->getOrigin();
					   $destination = $this->getDestination();
					   
					   $origin_old = $entity_old->getOrigin();
					   $destination_old = $entity_old->getDestination();
					   
					   if($origin_old->city != $origin->city ||
						  $origin_old->state != $origin->state ||
						  $origin_old->zip != $origin->zip ||
						  $destination_old->city != $destination->city ||
						  $destination_old->state != $destination->state ||
						  $destination_old->zip != $destination->zip 
						  
						  )
					   {
						   
							print "--check  route";
					   }
				}
				
				
				
				if(!$Flag)
				{
					if($this->vehicle_update==1){
					  $Flag= true; 
					  print "--check  Vehicle";
					}
					
				}
				return $Flag;
	}
	
	function checkVehicleChange($entity_old)
	{
		
		           $Flag=false;
		            $vehicles = $this->getVehicles();
					$vehicles_old = $entity_old->getVehicles();
					if(count($vehicles)>0 && count($vehicles) != count($vehicles_old))
					{
						$Flag= true; 
						//print "check number of vehicles";
					}
					else
					 {
						 foreach($vehicles_old as $vehicle_old) {
							 
							     $vehicle_new = new Vehicle($this->db);
							     $vehicle_new->load($vehicle_old->id);
								 
								 //print $vehicle_old->year." year ".$vehicle_new->year;
								 
								if($vehicle_old->year != $vehicle_new->year ||
								   $vehicle_old->make != $vehicle_new->make ||
								   $vehicle_old->model != $vehicle_new->model ||
								   $vehicle_old->inop != $vehicle_new->inop ||
								   $vehicle_old->type != $vehicle_new->type ||
								   $vehicle_old->vin != $vehicle_new->vin ||
								   $vehicle_old->lot != $vehicle_new->lot ||
								   $vehicle_old->deposit != $vehicle_new->deposit ||
								   $vehicle_old->tariff != $vehicle_new->tariff 
								   )
								{
									$Flag= true; 
									//print "check vehicles values";
								}
								
							   
						} // for loop
					 }  // else
					 
					 return $Flag;
	}
	
function checkCodCop(){
		$flag = 0;
		switch ($this->balance_paid_by) {
						//case Entity::BALANCE_INVOICE_CARRIER:
						case self::BALANCE_COP_TO_CARRIER_CASH:
						case self::BALANCE_COP_TO_CARRIER_CHECK:
						case self::BALANCE_COP_TO_CARRIER_COMCHECK:
						case self::BALANCE_COP_TO_CARRIER_QUICKPAY:
						case self::BALANCE_COD_TO_CARRIER_CASH:
						case self::BALANCE_COD_TO_CARRIER_CHECK:
						case self::BALANCE_COD_TO_CARRIER_COMCHECK:
						case self::BALANCE_COD_TO_CARRIER_QUICKPAY:
							$flag = 1;
							break;
						//--
						case self::BALANCE_COMPANY_OWES_CARRIER_CASH:
						case self::BALANCE_COMPANY_OWES_CARRIER_CHECK:
						case self::BALANCE_COMPANY_OWES_CARRIER_COMCHECK:
						case self::BALANCE_COMPANY_OWES_CARRIER_QUICKPAY:
						case self::BALANCE_COMPANY_OWES_CARRIER_ACH:
							$flag = 2;
							
							break;
						case self::BALANCE_CARRIER_OWES_COMPANY_CASH:
						case self::BALANCE_CARRIER_OWES_COMPANY_CHECK:
						case self::BALANCE_CARRIER_OWES_COMPANY_COMCHECK:
						case self::BALANCE_CARRIER_OWES_COMPANY_QUICKPAY:
						
							$flag = 3;
							
							break;
						default:
							break;
					}
					return $flag;
        }
    
    /**
     * Function is sending email to the shipper for matched carrier and before 
     * sending the email a hard coded template is created
     */
    function sendMatchCarrierEmail($toEmail,$emailBody,$subject){

        try{
            $mail = new PHPMailer();            
            $mail->IsSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = 'americancartransporters-com.mail.protection.outlook.com';
            $mail->Port = '25';
            $mail->SMTPAuth = false;
            $mail->FromName = 'freightdragon Tech Support';
            $mail->SetFrom('admin@americancartransporters.com');
            $mail->AddAddress($toEmail);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $emailBody;
            $mail->AltBody = 'This is a test using Internal Server SMTP SERVICES ';
            $mail->Send();
        } catch (phpmailerException $e) {
			print $e;
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
    }

    /**
     * Chetu added this function to resolve the problem of sending mail on behalf
     * of some other user. Now this mail will send matching carrier mail using 
     * user's default SMTP settings.
     * 
     * @param type $type Template being used
     * @param type $add  
     * @param type $is_default
     * @param type $toEmail
     * @param type $accountIDMatchCarrier
     * @throws FDException
     */
    function sendEmailMatchCarrierUsingLocalSetting($type, $add = array(), $is_default = true,$toEmail='',$accountIDMatchCarrier=0)
    {
		
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default,$accountIDMatchCarrier);
		
		
      try {
            $localSMTPSetting = $this->getAssigned()->getDefaultSettings();
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->CharSet = 'utf-8';
            $mail->Host = $localSMTPSetting->smtp_server_name;
            $mail->SMTPAuth = false;
            $mail->Username = $localSMTPSetting->smtp_user_name;
            $mail->Password = $localSMTPSetting->smtp_user_password;
            $mail->Port = $localSMTPSetting->smtp_server_port;
            if($localSMTPSetting->smtp_use_ssl){
                $mail->SMTPSecure = 'tls';
            }
            
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->IsHTML();
            }
            //print_r($this->getAssigned()->parent_id);
			//die('XXXXXX');
			
            $mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();

            $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
            $mail->AddAddress($toEmail);
            $mail->Send();
        } catch (phpmailerException $e) {
			print $e;
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }
    
    /**
     * Function to send match carrier email using custom SMTP settings
     * 
     * @author ShahRukh Charlie
     * @version 1.0
     */
    function sendMatchCarrierCustomSMTP($type, $add = array(), $is_default = true,$toEmail='',$accountIDMatchCarrier=0)
    {
		echo "<br>Mail sent from Custom SMTP function<br>";
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default,$accountIDMatchCarrier);
		
		
      try {
            $localSMTPSetting = $this->getAssigned()->getDefaultSettings();
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->CharSet = 'utf-8';
            //smtp server name here
            $mail->Host = 'mx02.anglerlabs.com';
            $mail->SMTPAuth = false;
            // smptp username here
            $mail->Username = 'david.J@ritewayautotransport.com';
            // smtp password here
            $mail->Password =  'H!9LzUfm';
            //smtp port here
            $mail->Port = '25';
            //$mail->SMTPSecure = 'tls';
            //if($localSMTPSetting->smtp_use_ssl){
               // $mail->SMTPSecure = 'tls';
           // }
            
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->IsHTML();
            }
            //print_r($this->getAssigned()->parent_id);
			//die('XXXXXX');
			
            $mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();

            $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
            $mail->AddAddress($toEmail);
            $mail->Send();
        } catch (phpmailerException $e) {
			print $e;
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }
    
    function sendEmailMatchCarrier($type, $add = array(), $is_default = true,$toEmail='',$accountIDMatchCarrier=0)
    {
		
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default,$accountIDMatchCarrier);
		
      try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            
			$mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();
            //$mail->AddAddress($emailTemplate->getToAddress());

            $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
           // if(trim($emailArr['cc'])!="")
             //$mail->AddCC("nkumar@agilesoftsolutions.com");
			  /*  $carrierIdArrSize = sizeof($carrierIdArr);
			    $MatchCarrierObj = new MatchCarrier($this->db);
		        $i=0;
				foreach ($ccArr as $ccEmail) {
					$email_ext = trim($ccEmail);
					if ($email_ext != "") {
						//$email_ext = trim("neeraj@freightdragon.com"); 
						//print "<br>--".$email_ext;
						$mail->ClearAddresses();
						$mail->AddAddress($email_ext);
						$mail->Send();
						
						if($carrierIdArrSize>0)
						{
							$MatchCarrierObj->load($carrierIdArr[$i]);
							$MatchCarrierObj->update(array('mail_status'=>1,'num_mail'=>$numOfMails));
						$i++;
						}
					}
				}
			*/
 
          
			//$mail->AddBCC("neeraj@freightdragon.com");
			//$mail->AddBCC("admin@ritewayautotransport.com");
			 $mail->AddAddress($toEmail);
             $mail->Send();
			
			
			
        } catch (phpmailerException $e) {
			print $e;
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }
/***************************Bulk mail combine *********************/    
  	public function sendSelectedTemplateBulkSendcombine($id,$emailArr,$member_id)
    {
		 return $this->sendSystemEmailBulkcombine($id, array(), false,$emailArr,$member_id);
		
    }
	protected function sendSystemEmailBulkcombine($type, $add = array(), $is_default = true,$emailArr,$member_id=0)
    {
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
		/* !#chetu added function 21-06-2017 */
		$attributes = $emailTemplate->getAttributes();
		$templateName = str_replace(' ', '_', strtolower($attributes['name']));

		
      try {
            $attachments = array();
            $att = $emailTemplate->getAttachments();
            if (count($att) > 0) {
                if ($emailArr['attach_type']>0){
                    foreach ($att as $name => $attachment) {
                        $filename = pathinfo($name, PATHINFO_FILENAME);
                        $attFile = ROOT_PATH."uploads/temp/".md5(mt_rand()).".pdf";
                        $this->getPdfFromHtml($attFile,$attachment);                    
                        $attachments[$filename.'.pdf'] = $attFile ;
                    }
                } else{
                    foreach ($att as $name => $attachment) {
                        $attFile = ROOT_PATH."uploads/temp/".md5(mt_rand()).".html";
                        file_put_contents($attFile, $attachment);
                        $attachments[$name] = $attFile;
                    }
                }  
            }
                            
			if($member_id==1 && $emailTemplate->sys_id == 7 && $type == 690){
				 
				foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/order_confirmation.pdf"; //" . md5(mt_rand()) . "
					$this->getPdfNewEmail("F", $attFile,$attachment);
                    $attachments['Order_Confirmation.pdf'] = $attFile;
                }
				 
			 }
			
            if ($emailTemplate->sys_id == EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $path = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".pdf";
                $this->getDispatchSheet()->getPdf("F", $path);
                $attachments["DispatchSheet.pdf"] = $path;
            }
            foreach ($attachments as $name=>$attachment) {
                    
                $attchData= array(
                    	'name'=>$name,
                        'attachment_path'=>$attachment,
                        'member_id'=>$emailArr['member_id']
                        
                );
                $ins_arr = $this->db->PrepareSql("app_combine_att", $attchData);
		$this->db->insert('app_combine_att', $ins_arr);             
            }
             $member = new Member($this->db);
            $member->load($member_id);
            $notes_str = $member->contactname ." sent '".$emailTemplate->name."' to ". $emailArr['to'] ;

            if($emailArr['cc']!="")
                $notes_str .= " also CC this mail to ".$emailArr['cc'];
            else
               $notes_str .= ".";
/* UPDATE NOTE */
            $note_array = array(
                    "entity_id" => $this->id,
                    "sender_id" => $_SESSION['member_id'],
                    "status" => 1,
                    "type" => 3,
                    "system_admin" => 1,
                    "text" => $notes_str//$this->getAssigned()->contactname." sent " .$emailArr['subject'] ." on date " .date('Y-m-d H:i:s')
                    );

            $note = new Note($this->db);
            $note->create($note_array);
			
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        } catch (Exception $e) {
            print_r($e);
        }
		return $templateName;
    }	
	public function get_zip_originalsize($filename) {
    $size = 0;
    $resource = zip_open($filename);
    while ($dir_resource = zip_read($resource)) {
        $size += zip_entry_filesize($dir_resource);
    }
    zip_close($resource);

    return $size;
}

	/* !#Chetu added parameter in the fucntion $orderId & $archieveName 21-06-2017 */
    public function combineattchformdb($emailArr,$member_id,$orderID = NULL,$archieveName = NULL ){  
       
		$i=0;  
		//print_r($orderID);
		
		
        $muladdress=explode(',',$emailArr['to']); 
        $member = new Member($this->db);
	$member->load($member_id);
      try {
            $mail = new FdMailer(true);
            $mail->isHTML();
            $mail->Body = $emailArr['body'];
            $mail->Subject = $emailArr['subject'];
            foreach ($muladdress as $muladdresses){
                $mail->AddAddress($muladdresses);
            }        
            if(trim($emailArr['cc'])!="")
            $mail->AddCC($emailArr['cc']);
            if(trim($emailArr['bcc'])!="")
            $mail->addBCC($emailArr['bcc']);            
            $mail->SetFrom($member->email,$member->getCompanyProfile()->companyname);

				$sql='SELECT * FROM app_combine_att WHERE member_id='.$emailArr['member_id'];
				$rows = $this->db->selectRows($sql);
				$result =$this->db->query($sql);
				$extsql = $this->db->fetch_row($result);
		 
			   $ext= pathinfo($extsql['name'], PATHINFO_EXTENSION);
				if($ext=='html'){
					$ext=".html";
				}else
					$ext= ".pdf";
				
				
				$zip = new ZipArchive();
				if ($zip->open("../uploads/temp/".$archieveName.".zip", ZIPARCHIVE::CREATE )!==TRUE) {
					exit("cannot open <$archive_file_name>\n");
				}
				
				foreach($rows as $row)
				{
					//echo $row['attachment_path']."<--<br>";
					//$zip->addFromString($row['attachment_path'], "");
					//$zip->addFile("test.txt");
					$zip->addFile($row['attachment_path'], $orderID[$i].'.pdf');
					file_put_contents('zip_file_attachment_sent_log.txt', "Created Files".$orderID[$i].'.pdf' . PHP_EOL, FILE_APPEND | LOCK_EX);
					$i++;
					//$mail->AddAttachment($row['attachment_path'], $i++.'.pdf');
				}
				
				$zip->close();				
				
				$mail->AddAttachment('../uploads/temp/'.$archieveName.'.zip');
				
				try {
						$mail->SendFromCron($member_id);
				} catch( Exception $e){
					print_r($e);
					die('died in custom try');
				}
				
	 
			} catch (phpmailerException $e) {
				throw new FDException("Mailer Exception: " . $e->getMessage());
		}
		echo "Zip path------".ROOT_PATH.'uploads/temp/'.$archieveName.'.zip';
		unlink(ROOT_PATH.'uploads/temp/'.$archieveName.'.zip');
		
        foreach ($rows as $row){
            unlink($row['attachment_path']);
        }
        $sql="DELETE FROM app_combine_att WHERE member_id=".$emailArr['member_id'];
        $this->db->query($sql);
    }
    
/*************************** end Bulk mail combine *********************/  	
	public function getTermMSG(){
		   $terms = "";
		    $defaultsettings = $this->db->selectRow("payments_terms_cod, payments_terms_cop, payments_terms_billing, payments_terms_invoice", "app_defaultsettings", "WHERE owner_id='" . getParentId() . "'");
			
			       $balance_paid_by = $this->balance_paid_by;
				   
				    if (($balance_paid_by == 2) || ($balance_paid_by == 3))
					{
						$terms = $defaultsettings['payments_terms_cod'];
					} 
					else if(($balance_paid_by == 8) || ($balance_paid_by == 9)){
						$terms = $defaultsettings['payments_terms_cop'];
					}
					else if(($balance_paid_by == 12) || ($balance_paid_by == 13) ||($balance_paid_by == 20) || ($balance_paid_by == 21)){
						$terms = $defaultsettings['payments_terms_billing'];
					}
					else if(($balance_paid_by == 14) || ($balance_paid_by == 15) ||($balance_paid_by == 22) || ($balance_paid_by == 23)){
						$terms = $defaultsettings['payments_terms_invoice'];
					}
					else
					{
						$terms = 'No terms condition found.';
					}
					
					return $terms;
		}
	
		/***************************Bulk mail *********************/
	public function sendSelectedTemplateBulkSend($id,$emailArr,$member_id)
    {
		 $this->sendSystemEmailBulk($id, array(), false,$emailArr,$member_id);
		
    }
	
	protected function sendSystemEmailBulk($type, $add = array(), $is_default = true,$emailArr,$member_id=0)
    {
	$muladdress=explode(',',$emailArr['to']); 	
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
		
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
		
      try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            $mail->Body = $emailArr['body'];
            $mail->Subject = $emailArr['subject'];
             foreach ($muladdress as $muladdresses){
                $mail->AddAddress($muladdresses);
            } 
            if(trim($emailArr['cc'])!="")
             $mail->AddCC($emailArr['cc']);
            if(trim($emailArr['bcc'])!="")
            $mail->addBCC($emailArr['bcc']);

            //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
			if($member_id == 1)
              $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            else
			  $mail->SetFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email, $emailTemplate->getFromName());
			
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
//            foreach ($bcc_s as $bcc) {
//                $bcc = trim($bcc);
//                if ($bcc != "") {
//                    $mail->AddBCC($bcc);
//                }
//            }
			
			$email_extra = trim($emailArr['mail_extra']);
			if($email_extra !=""){
				$email_extra_arr = explode(",",$email_extra);
				foreach ($email_extra_arr as $email_ext) {
					$email_ext = trim($email_ext);
					if ($email_ext != "") {
						$mail->AddBCC($email_ext);
					}
				}
			}
			//$mail->AddBCC("neeraj@freightdragon.com");
			//$mail->AddBCC("admin@ritewayautotransport.com");
			
            $att = $emailTemplate->getAttachments();
            if (count($att) > 0) {
                if ($emailArr['attach_type']>0){
                    foreach ($att as $name => $attachment) {
                        $filename = pathinfo($name, PATHINFO_FILENAME);
                        $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()). ".pdf";
                        $this->getPdfFromHtml($attFile,$attachment);                    
                        $attachments[$filename.'.pdf'] = $attFile ;
                    }
                } else{
                    foreach ($att as $name => $attachment) {
                        $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".html";
                        file_put_contents($attFile, $attachment);
                        $attachments[$name] = $attFile;
                    }
                }
            }
			
			
			if($member_id==1 && $emailTemplate->sys_id == 7 && $type == 690){
				 
				foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/order_confirmation.pdf"; //" . md5(mt_rand()) . "
					$this->getPdfNewEmail("F", $attFile,$attachment);
                    $attachments['Order_Confirmation.pdf'] = $attFile;
                }
				 
			 }
			
            if ($emailTemplate->sys_id == EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $path = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".pdf";
                $this->getDispatchSheet()->getPdf("F", $path);
                $attachments["DispatchSheet.pdf"] = $path;
            }
            foreach ($attachments as $name => $attachment) {
                $mail->AddAttachment($attachment, $name);
            }
            $mail->SendFromCron($member_id);
			
			$member = new Member($this->db);
			$member->load($member_id);
			$notes_str = $member->contactname ." sent '".$emailTemplate->name."' to ". $emailArr['to'] ;
			
			if($emailArr['cc']!="")
			    $notes_str .= " also CC this mail to ".$emailArr['cc'];
			else
			   $notes_str .= ".";
			   
			
								$note_array = array(
									"entity_id" => $this->id,
									"sender_id" => $member_id,
									"status" => 1,
									"type" => 3,
									"system_admin" => 1,
									"text" => $notes_str//$this->getAssigned()->contactname." sent " .$emailArr['subject'] ." on date " .date('Y-m-d H:i:s')
									);
								
								$note = new Note($this->db);
								$note->create($note_array);
								
			
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
		
    }

	/**************************Bulk mail **********************/
   public function sendOrderPickupEmail($type,$form_id,$add = array(),$is_default = true,$member_id=0)
    {
           $this->addBulkMail($type,$form_id,$add, $is_default,$member_id);
    }
	
   public function sendOrderDeliveredEmail($type,$form_id,$add = array(),$is_default = true,$member_id=0)
    {
          $this->addBulkMail($type,$form_id,$add, $is_default,$member_id);
    }
	
   protected function addBulkMail($type,$form_id,$add = array(),$is_default = true,$member_id=0)
   {
	                       /****************************Make mail queue****************************/
							$mailData = array(
									'entity_id' => $this->id,
									'is_default' => $is_default?1:0,
									'form_id' =>$form_id,
									'member_id' =>$member_id,
									'fromAddress' => '',
									'toAddress' => '',
									'cc' => "",
									'bcc' => "",
									'subject' => '',
									'body' => '',
									'type' => $type, 
									'sent' => 0
							);
							$ins_arr = $this->db->PrepareSql("app_mail_sent", $mailData);
				           $this->db->insert('app_mail_sent', $ins_arr);
							/****************************Make mail queue END****************************/   
   }
   
	public function sendOrderUpdatePickupDeliveredEmail($type,$form_id,$add = array(),$is_default = true,$member_id=0)
    {
          $this->sendSystemEmailFromCron($form_id,$add, $is_default,$member_id);
    }
	
	
   // $is_default = true for default system templates
    // set to false for needed template id
    protected function sendSystemEmailFromCron($type, $add = array(), $is_default = true,$member_id=0)
    {
        $tpl = new template();
        $emailTemplate = new EmailTemplate($this->db);
        $emailTemplate->setTemplateBuilder($tpl);
        $emailTemplate->loadTemplate($type, $this->getAssigned()->parent_id, $this, $add, $is_default);
        try {
            $mail = new FdMailer(true);
            $attachments = array();
            if ($emailTemplate->send_type == EmailTemplate::SEND_TYPE_HTML) {
                $mail->isHTML();
            }
            $mail->Body = $emailTemplate->getBody();
            $mail->Subject = $emailTemplate->getSubject();
            $mail->AddAddress($emailTemplate->getToAddress());

            //$mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
			if($member_id == 1)
              $mail->SetFrom($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            else
			  $mail->SetFrom($this->getAssigned()->getDefaultSettings()->smtp_from_email, $emailTemplate->getFromName());
			
            $mail->AddReplyTo($emailTemplate->getFromAddress(), $emailTemplate->getFromName());
            $bcc_s = explode(",", $emailTemplate->getBCCs() . "," . $this->getAssigned()->getDefaultSettings()->email_blind);
            foreach ($bcc_s as $bcc) {
                $bcc = trim($bcc);
                if ($bcc != "") {
                    $mail->AddBCC($bcc);
                }
            }
            $att = $emailTemplate->getAttachments();
            if (count($att) > 0) {
                foreach ($att as $name => $attachment) {
                    $attFile = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".html";
                    file_put_contents($attFile, $attachment);
                    $attachments[$name] = $attFile;
                }
            }
            if ($emailTemplate->sys_id == EmailTemplate::SYS_ORDER_DISP_SHEET_ATT) {
                $path = ROOT_PATH . "uploads/temp/" . md5(mt_rand()) . ".pdf";
                $this->getDispatchSheet()->getPdfNew("F", $path);
                $attachments["DispatchSheet.pdf"] = $path;
            }
            foreach ($attachments as $name => $attachment) {
                $mail->AddAttachment($attachment, $name);
            }
            $mail->SendFromCron($member_id);
        } catch (phpmailerException $e) {
            throw new FDException("Mailer Exception: " . $e->getMessage());
        }
        foreach ($attachments as $attachment) {
            unlink($attachment);
        }
    }
	
	public function getAccountCustom($reload = false, $field) {        
        if ($this->account_id == '0')
            return null;

        if ($reload || !isset($this->memberObjects['account'])) {
            try {
                $account = new Account($this->db);
                
                $account->load($this->account_id);
                return $account->$field;
            } catch (FDException $e) {
                return null;
            }
        }

        return $this->memberObjects['account'];
    }

}