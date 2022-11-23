<?php

class PayPal
{
    /**
    * Environment
    * Can be 'sandbox' or 'beta-sandbox' or 'live'
    * 
    * @var string
    */
    public $Environment = "sandbox";
    
    /**
    * Method name
    * 
    * @var string
    */
    public $Method = null;
    
    /**
    * API version
    * 
    * @var mixed
    */
    public $Version = "51.0";
    
    
    /**
    * API username
    * 
    * @var string
    */
    public $apiUserName;
    
    /**
    * API password
    * 
    * @var string
    */
    public $apiPassword;
    
    /**
    * API signature
    * 
    * @var string
    */
    public $apiSignature;
    
    
    /**
    * How you want to obtain payment
    * 
    * 'Authorization' indicatesthat this payment is a basic authorization subject to settlementwith PayPal Authorization & Capture.
    * 'Sale' indicates that this is a final salefor which you are requesting payment.
    * Characterlength and limit: Up to 13 single-byte alphabetic characters.
    * Note: Order isnot allowed for Direct Payment.
    * @var string
    */
    public $paymentAction = "Sale";
    
    /**
    * IPaddress of the payer’s browser
    * 
    * PayPal records thisIP addresses as a means to detect possible fraud.
    * Characterlength and limitations: 15 single-byte characters, including periods,for example: 255.255.255.255.
    * 
    * @var string
    */
    public $ipAddress;
    
    /**
    * Typeof credit card
    * 
    * Character length and limitations: Up to tensingle-byte alphabetic characters.
    * Allowable values: 'Visa', 'MasterCard', 'Discover', 'Amex', 'Maestro'm 'Solo'
    * For Canada, only MasterCard and Visa are allowable; Interac debit cards are not supported.
    * Note: If the creditcard type is Maestro or Solo, the CURRENCYCODE must be GBP. In addition, either STARTDATE or ISSUENUMBER mustbe specified.
    * 
    * @var string
    */
    public $creditCardType;
    
    /**
    * Creditcard number
    * 
    * Character length and limitations: numeric charactersonly.
    * No spaces or punctutation. Must conform with modulo and lengthrequired by each credit card type.
    * 
    * @var int
    */
    public $creditCardNumber;
    
    /**
    * Credit card expiration date.
    * 
    * Thisfield is required if you are using recurring payments with directpayments.
    * Format: MMYYYY
    * Character length and limitations:Six single-byte alphanumeric characters, including leading zero.
    * 
    * @var int
    */
    public $expDate;
    
    /**
    * CardVerification Value, version 2.
    * 
    * Your Merchant Account settings determine whetherthis field is required.
    * Character length for Visa, MasterCard, andDiscover: exactly three digits.Character length for American Express:exactly four digits.
    * To comply with credit card processing regulations,you must not store this value after a transaction has been completed.
    * 
    * @var int
    */
    public $CVV2;
    
    /**
    * Monthand year that Maestro or Solo card was issued, the MMYYYY format.
    * 
    * Characterlength: Must be six digits, including leading zero.
    * 
    * @var int
    */
    public $startDate;
    
    /**
    * Issuenumber of Maestro or Solo card.
    * 
    * Character length: two numeric digitsmaximum.
    * 
    * @var mixed
    */
    public $issueNumber;
    
    /**
    * Email address of payer
    * 
    * Character length and limitations: 127 single-bytecharacters.
    * 
    * @var string
    */
    public $email;
    
    /**
    * Payer’s first name
    * 
    * Character length and limitations: 25 single-bytecharacters.
    * 
    * @var string
    */
    public $firstName;
    
    /**
    * Payer’s middle name
    * 
    * Character length and limitations: 25 single-bytecharacters.
    * 
    * @var string
    */
    public $middleName;
    
    /**
    * Payer’s last name
    * 
    * Character length and limitations: 25 single-bytecharacters.
    * 
    * @var string
    */
    public $lastName;
    
    /**
    * First street address
    * 
    * Character length and limitations: 100 single-bytecharacters.
    * 
    * @var string
    */
    public $street;
    
    /**
    * Second street address
    * 
    * Character length and limitations: 100 single-bytecharacters.
    * 
    * @var string
    */
    public $street2;
    
    /**
    * Name of city
    * 
    * Character length and limitations: 40 single-bytecharacters.
    * 
    * @var string
    */
    public $city;
    
    /**
    * State or province
    * 
    * Character length and limitations: 40 single-bytecharacters.
    * 
    * @var string
    */
    public $state;
    
    /**
    * Country code
    * 
    * Character limit: Two single-byte characters.
    * 
    * @var string
    */
    public $countryCode = "US";
    
    /**
    * U.S. ZIP code or other country-specific postal code
    * 
    * Characterlength and limitations: 20 single-byte characters.
    * 
    * @var string
    */
    public $zip;
    
    /**
    * Phone number
    * 
    * Character length and limit: 20 single-byte characters.
    * 
    * @var string
    */
    public $phoneNum;
    
    /**
    * Thetotal cost of the transaction to the customer.
    * 
    * If shipping costand tax charges are known, include them in this value; if not, thisvalue should be the current sub-total of the order.
    * If thetransaction includes one or more one-time purchases, this fieldmust be equal to the sum of the purchases.
    * Set this fieldto 0 if the transaction does not include a one-time purchase; forexample, when you set up a billing agreement for a recurring paymentthat is not immediately charged.
    * Limitations: Must not exceed$10,000 USD in any currency. No currency symbol. Must have two decimalplaces, decimal separator must be a period (.), and the optionalthousands separator must be a comma (,).
    * 
    * @var mixed
    */
    public $amount;
    
    /**
    * Athree-character currency code.
    * 
    * Default: USD. 
    * 
    * @var string
    */
    public $currencyCode = "USD";
    
    /**
    * Additional data (key => value)
    * 
    * @example array('SHIPTONAME' => "John Stone")
    * @var mixed
    */
    public $additionalData = array();
    
    /**
    * CURL Error number
    * 
    * @var int
    */
    private $curlErrno = 0;
    
    /**
    * CURL Error text
    * 
    * @var int
    */
    private $curlError;
    
    
    /**
    * Send request
    * 
    */
    public function sendRequest()
    {
        return $this->sendRequestCurl();
    }
    
    /**
    * Send request via CURL
    * 
    */
    private function sendRequestCurl()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getEndPoint());
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 170);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $request = $this->getNvpRequest();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch))
        {
            $this->errno = curl_errno($ch);
            $this->error = curl_error($ch);
            
            return false;
        }
        
        curl_close($ch);
        
        return $this->deformatNvp($response);
    }
    
    /**
    * Get url to paypal api
    * 
    */
    private function getEndPoint()
    {
        if (in_array($this->Environment, array("sandbox", "beta-sandbox")))
        {
            return "https://api-3t.{$this->Environment}.paypal.com/nvp";
        }
        
        return "https://api-3t.paypal.com/nvp";
    }
    
    /**
    * Prepare request string
    * 
    */
    private function getNvpRequest()
    {
        $req_arr = array(
            'METHOD'         => $this->Method
          , 'VERSION'        => $this->Version
          
          , 'USER'           => $this->apiUserName
          , 'PWD'            => $this->apiPassword
          , 'SIGNATURE'      => $this->apiSignature
          , 'PAYMENTACTION'  => $this->paymentAction
          , 'IPADDRESS'      => $this->ipAddress
          
          , 'CREDITCARDTYPE' => $this->creditCardType
          , 'ACCT'           => $this->creditCardNumber
          , 'EXPDATE'        => $this->expDate
          , 'CVV2'           => $this->CVV2
          , 'STARTDATE'      => $this->startDate
          , 'ISSUENUMBER'    => $this->issueNumber
          
          , 'EMAIL'          => $this->email
          , 'FIRSTNAME'      => $this->firstName
          , 'MIDDLENAME'     => $this->middleName
          , 'LASTNAME'       => $this->lastName
          , 'STREET'         => $this->street
          , 'STREET2'        => $this->street2
          , 'CITY'           => $this->city
          , 'STATE'          => $this->state
          , 'COUNTRYCODE'    => $this->countryCode
          , 'ZIP'            => $this->zip
          , 'PHONENUM'       => $this->phoneNum
          
          , 'AMT'            => $this->amount
          , 'CURRENCYCODE'   => $this->currencyCode
        );
        
        $req_arr = array_merge($req_arr, $this->additionalData);
        
        foreach ($req_arr as $key => $value)
        {
            $value = trim($value);
            
            if (is_null($value) || empty($value))
            {
                unset($req_arr[$key]);
                
                continue;
            }
            
            $req_arr[$key] = sprintf("%s=%s", $key, urlencode($value));
        }
        
        return join("&", $req_arr);
    }
    
    /**
    * This function will take NVPString and convert it to an Associative Array and it will decode the response.
    * It is usefull to search for a particular key and displaying arrays.
    * 
    * @param string
    * @return array
    */
    private function deformatNvp($nvpstr)
    {
        $intial = 0;
        $nvpArray = array();
        
        while (strlen($nvpstr))
        {
            $keypos = strpos($nvpstr, '=');
            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
            
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
            
            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        
        return $nvpArray;
    }
    
}
?>