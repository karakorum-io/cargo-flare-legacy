<?php

/***************************************************************************************************
* Public Part  - PayPal Class
*
* Client: FreightDragon
* Version: 	1.1
* Date:    	2011-11-22
* Author:  	C.A.W., Inc. dba INTECHCENTER
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
* E-mail:	techsupport@intechcenter.com
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved
****************************************************************************************************/

class PayPalPro
{
    var $api_username;
    var $api_password;
    var $api_signature;

    var $api_endpoint = "https://api-3t.sandbox.paypal.com/nvp"; //test
    //var $api_endpoint = "https://api-3t.paypal.com/nvp";
    var $api_version = "51.0";

    var $methodName = "DoDirectPayment";
    //var $paymentType = "Authorization";
    var $paymentType = "Sale";

    var $dataAmount;
    var $dataCreditCardType;
    var $dataCreditCardNumber;
    var $dataExpMonth;
    var $dataExpYear;
    var $dataCVV2;
    
    var $dataFirstName;
    var $dataLastName;
    var $dataStreet;
    var $dataCity;
    var $dataState;
    var $dataZip;
    var $dataCountry = "US";
    var $dataCurrency = "USD";
    var $dataProductName;
    var $dataProductID;

    var $errno = 0;
    var $error = "";

    /**
    * hash_call: Function to perform the API call to PayPal using API signature
    * returns an associtive array containing the response from the server.
    */
    function hash_call()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_endpoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_TIMEOUT, 4000);
        curl_setopt($ch, CURLOPT_POST, 1);

        // NVPRequest for submitting to server
        $nvpreq = "METHOD=".urlencode($this->methodName)
                . "&VERSION=".urlencode($this->api_version)
                . "&PWD=".urlencode($this->api_password)
                . "&USER=".urlencode($this->api_username)
                . "&SIGNATURE=".urlencode($this->api_signature)
                . "&PAYMENTACTION=".urlencode($this->paymentType)
                . "&AMT=".urlencode($this->dataAmount)
                . "&CREDITCARDTYPE=".urlencode($this->dataCreditCardType)
                . "&ACCT=".urlencode($this->dataCreditCardNumber)
                . "&EXPDATE=".urlencode($this->dataExpMonth).urlencode($this->dataExpYear)
                . "&CVV2=".urlencode($this->dataCVV2)
                . "&FIRSTNAME=".$this->dataFirstName
                . "&LASTNAME=".$this->dataLastName
                . "&STREET=".urlencode($this->dataStreet)
                . "&CITY=".urlencode($this->dataCity)
                . "&STATE=".urlencode($this->dataState)
                . "&ZIP=".urlencode($this->dataZip)
                . "&COUNTRYCODE=".urlencode($this->dataCountry)
                . "&CURRENCYCODE=".urlencode($this->dataCurrency)
                . "&NOSHIPPING=1"
                . "&L_NAME0=".urlencode($this->dataProductName)
                . "&L_NUMBER0=".urlencode($this->dataProductID)
                . "&L_DESC0=".urlencode($this->dataProductName)
                . "&L_AMT0=".urlencode($this->dataAmount)
                . "&L_QTY0=1"
                . "&SHIPPINGAMT=".urlencode('0.00');

        // setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        // getting response from server
        $response = curl_exec($ch);

        // convrting NVPResponse to an Associative Array
        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);

        if (curl_errno($ch))
        {
            $this->errno = curl_errno($ch);
            $this->error = curl_error($ch);

            return false;
        }
        else {
            curl_close($ch);
        }

        return $nvpResArray;
    }

    /**
    * This function will take NVPString and convert it to an Associative Array and it will decode the response.
    * It is usefull to search for a particular key and displaying arrays.
    * @nvpstr is NVPString.
    * @nvpArray is Associative Array.
    */
    function deformatNVP($nvpstr)
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