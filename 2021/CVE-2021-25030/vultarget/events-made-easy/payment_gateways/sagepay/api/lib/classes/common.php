<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Common utility functions shared by all SagePay interfaces
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayCommon
{
	 static protected $nonSensitiveRequestDataArray = array("VPSProtocol", "TxType", "Vendor", "VendorTxCode", "Amount","Currency", "Description");
     static protected $nonSensitiveResponseDataArray = array("VPSProtocol", "Status", "StatusDetail", "VPSTxId");

    /**
     * Generate a unique VendorTxId
     *
     * @param string $orderId Order ID.
     * @param string $type    Type of transaction
     * @param string $prefix  Override the prefix
     *
     * @return string Returns a unique string that can be used as VendorTxId
     */
    static function vendorTxCode($orderId, $type = false, $prefix = false)
    {
        $parts = array();
        // add prefix
        if (!empty($prefix))
        {
            $parts[] = $prefix;
        }
        // add type
        if (!empty($type))
        {
            $parts[] = $type;
        }
        // add order id
        if (!empty($orderId))
        {
            $parts[] = $orderId;
        }

        $parts[] = rand(0, 1000000000);
        $vendorTxCode = implode('-', $parts);
        
        while (strlen($vendorTxCode) > 40)
        {
            array_shift($parts);
            $vendorTxCode = implode('-', $parts);
        }
        return $vendorTxCode;
    }

    /**
     * Extract an order id from a VendorTxCode
     *
     * @param string $vendorTxCode a valid VendorTxCode
     *
     * @return boolean|string Returns the Order Id or boolean false
     */
    static function vendorTxCode2OrderId($vendorTxCode)
    {
        $orderId = false;

        if (!empty($vendorTxCode))
        {
            $parts = explode('-', $vendorTxCode);
            // at the very least there should be 2 parts
            if (count($parts) >= 2)
            {
                $orderId = $parts[count($parts) - 2];
            }
        }

        return $orderId;
    }

    /**
     * Send a POST request to SagePay and return the response as an array.
     *
     * @param string $url  The url to POST to.
     * @param array $data  The data to post.
     * @param int $ttl cURL time of execution
     * @param string $caCertPath path to SSL certificate
     * 
     * @return array The response from Sage Pay.
     */
    static public function requestPost($url, $data, $ttl = 30, $caCertPath = '')
    {
        set_time_limit(60);
        $output = array();
        $curlSession = curl_init();

        curl_setopt($curlSession, CURLOPT_URL, $url);
        curl_setopt($curlSession, CURLOPT_HEADER, 0);
        curl_setopt($curlSession, CURLOPT_POST, 1);
        curl_setopt($curlSession, CURLOPT_POSTFIELDS, SagepayUtil::arrayToQueryString($data));
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_TIMEOUT, $ttl);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 2);

        if (!empty($caCertPath))
        {
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curlSession, CURLOPT_CAINFO, $caCertPath);
        } 
        else
        {
            curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, 0);
        }

        $rawresponse = curl_exec($curlSession);
        if (curl_getinfo($curlSession, CURLINFO_HTTP_CODE) !== 200)
        {
            $output['Status'] = "FAIL";
            $output['StatusDetails'] = "Server Response: " . curl_getinfo($curlSession, CURLINFO_HTTP_CODE);
            $output['Response'] = $rawresponse;

            return $output;
        }
        if (curl_error($curlSession))
        {
            $output['Status'] = "FAIL";
            $output['StatusDetail'] = curl_error($curlSession);
            $output['Response'] = $rawresponse;
            return $output;
        }

        curl_close($curlSession);
      
        $requestForLog= SagepayUtil::arrayToQueryStringRemovingSensitiveData($data,self::$nonSensitiveRequestDataArray ) ; 
        $response = SagepayUtil::queryStringToArray($rawresponse, "\r\n");
        $responseForLog= SagepayUtil::queryStringToArrayRemovingSensitiveData($rawresponse, "\r\n", self::$nonSensitiveResponseDataArray );         
		
		SagepayUtil::log("Request:" . PHP_EOL . $requestForLog);
        SagepayUtil::log("Response:" . PHP_EOL . json_encode($responseForLog));
	
        return array_merge($output, $response);
    }


    /**
     * Encrypt the order details ready to send to SagePay Server.
     *
     * @param SagepayAbstractApi $request   The request instance.
     * @throws SagepayApiException
     *
     * @return array|string  Returns a String for Form integration method or an array for Server / Direct.
     */
    static public function encryptedOrder(SagepayAbstractApi $request)
    {
        $settings = $request->getConfig();
        $basket = $request->getBasket();
        $address = $request->getAddressList();
        $integrationMethod = $request->getIntegrationMethod();
        $paneValues = $request->getPaneValues();
        // Determine the transaction type based on the payment gateway settings.
        $txType = $settings->getTxType();

        $billingAddress = $address[0];
        $deliveryAddress = isset($address[1]) ? $address[1] : null;

        $query = array(
            'VPSProtocol' => $settings->getProtocolVersion(),
            'Vendor' => $settings->getVendorName(),
            'VendorTxCode' => self::vendorTxCode($basket->getId(), $txType, $settings->getVendorName()),
            'Amount' => number_format($basket->getAmount(), 2, '.', ''),
            'Currency' => $settings->getCurrency(),
            'Description' => $basket->getDescription(),
            'CustomerName' => $billingAddress->firstname . ' ' . $billingAddress->lastname,
            'CustomerEMail' => $billingAddress->email,
            'VendorEMail' => $settings->getVendorEmail(),
            'SendEMail' => $settings->getSendEmail(),
            'eMailMessage' => $settings->getEmailMessage(),
            'BillingSurname' => $billingAddress->lastname,
            'BillingFirstnames' => $billingAddress->firstname,
            'BillingAddress1' => $billingAddress->address1,
            'BillingAddress2' => $billingAddress->address2,
            'BillingCity' => $billingAddress->city,
            'BillingPostCode' => $billingAddress->getPostCode(),
            'BillingCountry' => $billingAddress->country,
            'BillingPhone' => $billingAddress->phone,
            'ApplyAVSCV2' => $settings->getApplyAvsCv2(),
            'Apply3DSecure' => $settings->getApply3dSecure(),
            'AllowGiftAid' => $settings->getAllowGiftAid(),
            'BillingAgreement' => $settings->getBillingAgreement()
        );
        
        $query += $request->getData();

        $customer = $request->getCustomer();
        if ($customer instanceof SagepayCustomer)
        {
            $query += self::_setAuxValue($query, 'CustomerXML', $customer->export());
        }
        $query += self::_setAuxValue($query, 'VendorData', $settings->getVendorData());
        $query += self::_setAuxValue($query, 'ReferrerID', $settings->getPartnerId());
        $query += self::_setAuxValue($query, 'Language', $settings->getLanguage());



        // Add check for state for US addresses only.
        if ($billingAddress->country == 'US')
        {
            $query['BillingState'] = $billingAddress->state;
        }

        //Override with supplied delivery address if we have one .
        $query += self::_populateDeliveryDetails($billingAddress, $deliveryAddress);

        if (isset($paneValues['cardType']) && empty($paneValues['cardType']))
        {
            $integrationMethod = SAGEPAY_TOKEN;
        }

        // Check if we need to encode cart.
        if (!$settings->basketAsXmlDisabled())
        {
            $query['BasketXML'] = $basket->exportAsXml();
        } 
        else
        {
            $query['Basket'] = $basket->exportAsXml(false);
        }
        
        if (count($settings->getSurcharges()) > 0)
        {
            $surcharges = new SagepaySurcharge();
            $surcharges->setSurcharges($settings->getSurcharges());
            $query['SurchargeXML'] = $surcharges->export();
        }
        
        switch ($integrationMethod)
        {
            case SAGEPAY_FORM:
                // Unset unused values
                unset($query['VPSProtocol']);
                unset($query['Vendor']);
                unset($query['TxType']);

                $env = $settings->getEnv();
                
                $query['SuccessURL'] = $settings->getFullFormSuccessUrl();
                $query['FailureURL'] = $settings->getFullFormFailureUrl();
                
                $request->setData($query);
                $queryStr = SagepayUtil::arrayToQueryString($query);

                $formValues = array();
                $formValues['Vendor'] = $settings->getVendorName();
                $formValues['VPSProtocol'] = $settings->getProtocolVersion();
                $formValues['TxType'] = $txType;
                $formValues['Crypt'] = SagepayUtil::encryptAes($queryStr, $settings->getFormEncryptionPassword($env));
                // Encrypt order details using base64 and the secret key from the settings.
                return $formValues;

            case SAGEPAY_SERVER:
                $query['NotificationURL'] = $settings->getFullServerNotificationUrl();
                $query['TxType'] = $txType;
                $query['Profile'] = $settings->getServerProfile();
                $query['StoreToken'] = 1;
                $query += self::_setAuxValue($query, 'AccountType', $settings->getAccountType());
                return $query;

            case SAGEPAY_DIRECT:
                $query = array_merge($query, self::_getCardDetails($paneValues));
                $query['TxType'] = $txType;
                $query['CardHolder'] = $billingAddress->firstname . ' ' . $billingAddress->lastname;

                // Add 3D Secure flag only if the 3d Secure module is enabled for DIRECT.
                $query['Apply3DSecure'] = $settings->getApply3dSecure();
                $query += self::_setAuxValue($query, 'AccountType', $settings->getAccountType());
                return $query;

            case SAGEPAY_PAYPAL:
                $query['TxType'] = $txType;
                $query['CardType'] = 'PAYPAL';
                $query['PayPalCallbackURL'] = $settings->getPaypalCallbackUrl() . '?vtx=' . $query['VendorTxCode'];
                return $query;

            case SAGEPAY_TOKEN:
                $query['TxType'] = $txType;
                $query['Token'] = $paneValues['token'];
                $query['CV2'] = $paneValues['cv2'];
                $query['AllowGiftAid'] = $paneValues['giftAid'] ? 1 : 0;
                $query += self::_setAuxValue($query, 'AccountType', $settings->getAccountType());
                $query['StoreToken'] = 1;
                $query['ApplyAVSCV2'] = 2;
                return $query;
            default :
                throw new SagepayApiException('Invalid integration type');
        }
    }

    /**
     * Get the card details
     *
     * @param array $creditCard
     *
     * @return array
     */
    static private function _getCardDetails($creditCard)
    {
        $query = array();

        if (isset($creditCard['startDate']) && !empty($creditCard['startDate']))
        {
            $query['StartDate'] = $creditCard['startDate'];
        }

        $query['CardType'] = isset($creditCard['cardType']) ? self::_lookupCardType($creditCard['cardType']) : '';
        $query['CardNumber'] = isset($creditCard['cardNumber']) ? $creditCard['cardNumber'] : '';
        $query['ExpiryDate'] = isset($creditCard['expiryDate']) ? $creditCard['expiryDate'] : '';
        $query['CV2'] = isset($creditCard['cv2']) ? $creditCard['cv2'] : '';
        $query['AllowGiftAid'] = (isset($creditCard['giftAid']) && $creditCard['giftAid']) ? 1 : 0;
        return $query;
    }

    /**
     * Populate Delivery Details if exist, otherwise populate with billing details.
     *
     * @param SagepayCustomerDetails $billingDetails
     * @param SagepayCustomerDetails|null $deliveryDetails
     *
     * @return array
     */
    static private function _populateDeliveryDetails(SagepayCustomerDetails $billingDetails,
    SagepayCustomerDetails $deliveryDetails = null)
    {
        $query = array();
        if (is_null($deliveryDetails))
        {
            $deliveryDetails = $billingDetails;
        }
        $query['DeliverySurname'] = $deliveryDetails->lastname;
        $query['DeliveryFirstnames'] = $deliveryDetails->firstname;
        $query['DeliveryAddress1'] = $deliveryDetails->address1;
        $query['DeliveryAddress2'] = $deliveryDetails->address2;
        $query['DeliveryPhone'] = $deliveryDetails->phone;
        $query['DeliveryCity'] = $deliveryDetails->city;
        $query['DeliveryPostCode'] = $deliveryDetails->getPostCode();
        $query['DeliveryCountry'] = $deliveryDetails->country;

        if ($deliveryDetails->country == 'US' && $deliveryDetails->state)
        {
            $query['DeliveryState'] = $deliveryDetails->state;
        }
        return $query;
    }

    static private function _setAuxValue(array $query, $key, $value)
    {
        if (!empty($value))
        {
            $query[$key] = $value;
        }
        return $query;
    }

    /**
     * Seeks and returns the card type
     *
     * @param string $cardType
     *
     * @return string
     */
    static private function _lookupCardType($cardType)
    {
        switch ($cardType)
        {
            case 'mastercard':
                return 'MC';
            case 'visaelectron':
                return 'UKE';
            default:
                return $cardType;
        }
    }

}
