<?php
/***************************************************************************************************
* Public Part  - Authorize.net Class for Shopping process
*
* Client: 	FreightDragon
* Version: 	1.1
* Date:    	2011-11-22
* Author:  	C.A.W., Inc. dba INTECHCENTER
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:	techsupport@intechcenter.com
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved
****************************************************************************************************/

class AuthorizeNet {
	public $postUrl = /*"https://secure.authorize.net/gateway/transact.dll";*/  "https://test.authorize.net/gateway/transact.dll";

    public $login;
    public $tran_key;

	public $firstName;
	public $lastName;
	public $address;
	public $city;
	public $state;
	public $zip;

	public $cardNumber;
	public $expMonth;
	public $expYear;
	public $amount;
	public $description;

	public $message;

	private $__isDebug;

	public function __construct($isDebug = false) {
		$this->__isDebug = $isDebug;
	}

	/**
	 * Process an order
	 */
	public function process() {
		$postValues = $this->__getPostValues();

		$postStr = "";
		foreach ($postValues as $key => $value) {
			$postStr .= sprintf('%s=%s&', $key, urlencode($value));
		}
		$postStr = rtrim($postStr, "& ");

		$request = curl_init($this->postUrl);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $postStr);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
		$postResponse = curl_exec($request);
		curl_close($request);

		$responseArr = explode($postValues['x_delim_char'], $postResponse);

		if (!empty($responseArr[3])) {
			$this->message = $responseArr[3];
		}
		else {
			$this->message = "Unknown error.";
		}

		/*
		1 = Approved
		2 = Declined
		3 = Error
		4 = Held for Review
		*/
		if ((int)$responseArr[0] != 1) {
			return false;
		}

		return true;
	}

	private function __getPostValues() {
		return array(
			'x_login'			=> $this->login,
			'x_tran_key'		=> $this->tran_key,
			'x_test_request'	=> $this->__isDebug ? "TRUE" : "FALSE",

			'x_version'			=> "3.1",
			'x_delim_data'		=> "TRUE",
			'x_delim_char'		=> "|",
			'x_relay_response'	=> "FALSE",

			'x_type'			=> "AUTH_CAPTURE",
			'x_method'			=> "CC",
			'x_card_num'		=> $this->cardNumber,
			'x_card_code'		=> $this->cardCode,
			'x_exp_date'		=> $this->expMonth.$this->expYear,

			'x_amount'			=> $this->amount,
			'x_description'		=> $this->description,

			'x_first_name'		=> $this->firstName,
			'x_last_name'		=> $this->lastName,
			'x_address'			=> $this->address,
			'x_city'			=> $this->city,
			'x_state'			=> $this->state,
			'x_zip'				=> $this->zip
		);
	}
}

?>