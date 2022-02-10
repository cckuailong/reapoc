<?php

namespace Paymill\Models\Request;

/**
 * Class ChecksumBase
 * @package Paymill\Models\Request
 */
abstract class ChecksumBase extends Base
{
    /**
     * Checksum type Paypal.
     */
    const TYPE_PAYPAL = 'paypal';

    /**
     * Checksum type SOFORT
     */
    const TYPE_SOFORT = 'sofort';

    /**
     * Checksum type KontoSecure
     */
    const TYPE_KONTOSECURE = 'kontosecure';

    /**
     * Checksum type
     *
     * @var string
     */
    private $checksumType = null;

    /**
     * @var string
     */
    private $amount = null;

    /**
     * @var array
     */
    private $currency = null;

    /**
     * @var array
     */
    private $description = null;

    /**
     * @var string
     */
    private $returnUrl = null;

    /**
     * @var string
     */
    private $cancelUrl = null;

    /**
     * @var null|string
     */
    private $appId = null;

    /**
     * @var null|string
     */
    private $feeAmount = null;

    /**
     * @var null|string
     */
    private $feeCurrency = null;

    /**
     * @var null|string
     */
    private $feePayment = null;

    /**
     * Client identifier
     *
     * @var string $client
     */
    private $client;

    /**
     * API resource.
     *
     * @var string
     */
    protected $_serviceResource = 'checksums/';

    /**
     * Sets the identifier of the Client for the transaction
     *
     * @param string $clientId Client identifier
     *
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Returns the identifier of the Client associated with the checksum. If no client is available null will be returned
     *
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set amount
     *
     * @param string $amount Amount in s the smallest unit (e.g. Cent)
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string Amount in s the smallest unit (e.g. Cent)
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set checksum type
     *
     * @param string $checksumType
     *
     * @return $this
     */
    public function setChecksumType($checksumType)
    {
        $this->checksumType = $checksumType;

        return $this;
    }

    /**
     * Get checksum type
     *
     * @return string
     *
     * @return $this
     */
    public function getChecksumType()
    {
        return $this->checksumType;
    }

    /**
     * Set currency
     *
     * @param string $currency (alpha 3)
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string (alpha 3)
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get return url
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Set return url
     *
     * @param string $returnUrl return url
     *
     * @return $this
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * Get cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Set cancel url
     *
     * @param string $cancelUrl cancel url
     *
     * @return $this
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * Set app ID
     *
     * @param null|string $appId
     *
     * @return $this
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Get app Id
     *
     * @return null|string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Set fee amount
     *
     * @param null|string $feeAmount
     *
     * @return $this
     */
    public function setFeeAmount($feeAmount)
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }

    /**
     * Get fee amount
     *
     * @return null|string
     */
    public function getFeeAmount()
    {
        return $this->feeAmount;
    }

    /**
     * Set fee currency
     *
     * @param null|string $feeCurrency
     *
     * @return $this
     */
    public function setFeeCurrency($feeCurrency)
    {
        $this->feeCurrency = $feeCurrency;

        return $this;
    }

    /**
     * Get fee currency
     *
     * @return null|string
     */
    public function getFeeCurrency()
    {
        return $this->feeCurrency;
    }

    /**
     * Set fee payment
     *
     * @param null|string $feePayment
     *
     * @return $this
     */
    public function setFeePayment($feePayment)
    {
        $this->feePayment = $feePayment;

        return $this;
    }

    /**
     * get fee payment
     *
     * @return null|string
     */
    public function getFeePayment()
    {
        return $this->feePayment;
    }

    public function parameterize($method)
    {
        $parameterArray = array();
        switch ($method) {
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
            case 'create':
                if($this->getChecksumType()) {
                    $parameterArray['checksum_type'] = $this->getChecksumType();
                }

                if($this->getAmount()) {
                    $parameterArray['amount'] = $this->getAmount();
                }

                if($this->getCurrency()) {
                    $parameterArray['currency'] = $this->getCurrency();
                }

                if($this->getDescription()){
                    $parameterArray['description'] = $this->getDescription();
                }

                if($this->getReturnUrl()){
                    $parameterArray['return_url'] = $this->getReturnUrl();
                }

                if($this->getCancelUrl()){
                    $parameterArray['cancel_url'] = $this->getCancelUrl();
                }

                if($this->getClient()) {
                    $parameterArray['client'] = $this->getClient();
                }

                // Unite params:

                if($this->getAppId()) {
                    $parameterArray['app_id'] = $this->getAppId();
                }

                if($this->getFeeAmount()) {
                    $parameterArray['fee_amount'] = $this->getFeeAmount();
                }

                if($this->getFeeCurrency()) {
                    $parameterArray['fee_currency'] = $this->getFeeCurrency();
                }

                if($this->getFeePayment()) {
                    $parameterArray['fee_payment'] = $this->getFeePayment();
                }

                break;
        }

        return $parameterArray;
    }
}
