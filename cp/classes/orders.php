<?php
/**
 * @version		1.0
 * @since		09.08.12
 * @author		Oleg Ilyushyn, C.A.W., Inc. dba INTECHCENTER
 * @address		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * @email		techsupport@intechcenter.com
 * @copyright	2012 Intechcenter. All Rights Reserved
 *
 * @property int $id
 * @property int $member_id
 * @property int $coupon_id
 * @property float $amount
 * @property string $first_name
 * @property string $last_name
 * @property string $company
 * @property string $address
 * @property string city
 * @property string state
 * @property string zip
 * @property int $card_type_id
 * @property string $card_first_name
 * @property string $card_last_name
 * @property string $card_number
 * @property string $card_expire
 * @property string $card_cvv2
 * @property string $transaction_id
 * @property string $response
 * @property int $status
 * @property int $is_delete
 * @property string $register_date
 */ 
class Orders extends FdObject {
	const TABLE = 'orders';

	const TYPE_VISA = 1;
	const TYPE_MASTERCARD = 2;
	const TYPE_AMERICAN_EXPRESS = 3;
	const TYPE_DISCOVER = 4;

	const STATUS_PENDING = 1;
	const STATUS_PROCESSED = 2;
	const STATUS_FAILED = 3;
	const STATUS_CANCELLED = 4;
	const STATUS_RECALLED = 5;

	public $card_expire_month;
	public $card_expire_year;

	public function load($id = null) {
		parent::load($id);
		if (strlen($this->card_expire) == 4) {
			$this->card_expire_month = substr($this->card_expire, 0, 2);
			$this->card_expire_year = substr($this->card_expire, 2);
		}
		if ($GLOBALS['CONF']['DES_ENCRYPT']){
				$salt = (isset($GLOBALS['security_salt'])?$GLOBALS['security_salt']:$_SERVER['HTTP_HOST']);
				$row = $this->db->selectRow("DES_DECRYPT(card_number, '".$salt."') as card_number", Orders::TABLE, "WHERE `id` = ".$this->id);
		}else{
				$row = $this->db->selectRow("card_number as card_number", Orders::TABLE, "WHERE `id` = ".$this->id);
		}
		$this->attributes['card_number'] = $row['card_number'];
	}

	public function create($data = null) {
		$ret = parent::create($data);
		if (isset($data['card_number'])) {
				if ($GLOBALS['CONF']['DES_ENCRYPT']){
						$salt = (isset($GLOBALS['security_salt'])?$GLOBALS['security_salt']:$_SERVER['HTTP_HOST']);
						$data['card_number'] = "DES_ENCRYPT('".$data['card_number']."', '".$salt."')";
				}
			$this->db->update(self::TABLE, array('card_number' => $data['card_number']), '`id` = '.$ret);
		}
		return $ret;
	}

	public static function getCardTypes() {
		return array(
			self::TYPE_VISA => 'Visa',
			self::TYPE_MASTERCARD => 'MasterCard',
			self::TYPE_AMERICAN_EXPRESS => 'American Express',
			self::TYPE_DISCOVER => 'Discover',
		);
	}

	public static function getCardTypeLabel($type) {
		$types = self::getCardTypes();
		return $types[$type];
	}

	public static function getStatuses() {
		return array(
			self::STATUS_PENDING => 'Pending',
			self::STATUS_PROCESSED => 'Processed',
			self::STATUS_FAILED => 'Failed',
			self::STATUS_CANCELLED => 'Cancelled',
			self::STATUS_RECALLED => 'Recalled',
		);
	}

	public static function getStatusLabel($status) {
		$statuses = self::getStatuses();
		return $statuses[$status];
	}

	public static function getExpireMonths() {
		$months = array();
		for ($i = 1; $i <= 12; $i++) {
			$months[sprintf('%02d', $i)] = sprintf('%02d', $i);
		}
		return $months;
	}

	public static function getExpiredYears() {
		$years = array();
		for($i = 0; $i <= 10; $i++) {
			$years[(int)date('Y')+$i] = (int)date('Y')+$i;
		}
		return $years;
	}

	public function processAuthorize(){
		if ($this->amount <= 0) {
			$this->update(array(
				'status' => self::STATUS_PROCESSED,
			));
			return true;
		};
		$api_login = $this->db->selectField('value', 'settings', "WHERE `name` = 'anet_api_login_id'");
		$api_pwd = $this->db->selectField('value', 'settings', "WHERE `name` = 'anet_trans_key'");
		$transaction = new AuthorizeNetAIM($api_login, $api_pwd);
		$transaction->setSandbox($GLOBALS['CONF']['anet_sandbox']);

		$transaction->setFields(array(
			'amount'     => $this->amount,
			'card_num'   => $this->card_number,
			'exp_date'   => $this->card_expire_month."/".$this->card_expire_year,
			'card_code'  => $this->card_cvv2,
			'first_name' => $this->first_name,
			'last_name'  => $this->last_name,
			'address'    => $this->address,
			'city'       => $this->city,
			'state'      => $this->state,
			'zip'        => $this->zip,
			'description'=> "Freight Dragon: Order #".$this->id,
			'invoice_num'=> $this->id,
		));
		$response = $transaction->authorizeAndCapture();
		if ($response->approved) {
			$this->update(array(
				'transaction_id' => $response->transaction_id,
				'status' => self::STATUS_PROCESSED,
			));
			return true;
		} else {
			$this->update(array(
				'response' => $response->response_reason_text,
				'status' => self::STATUS_FAILED,
			));
//			var_dump($transaction);
//			var_dump($response);
			//throw new FDException($response->response_reason_text);
			return false;
		}
	}
}
