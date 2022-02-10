<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Interface for integration API
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
abstract class SagepayAbstractApi
{

    /**
     * Settings of Sagepay library
     *
     * @var SagepaySettings
     */
    protected $config;

    /**
     * The shopping basket items and configurations
     *
     * @var SagepayBasket
     */
    protected $basket;

    /**
     * This can be used to supply information on the customer for purposes such as fraud screening.
     *
     * @var SagepayCustomer
     */
    protected $customer;

    /**
     * Integration method
     *
     * @var string
     */
    protected $integrationMethod;

    /**
     * List of customer details
     *
     * @var SagepayCustomerDetails[]
     */
    protected $addressList;

    /**
     *  Card and account details
     *
     * @var array
     */
    protected $paneValues = null;

    /**
     * Required fields that will be sent to gateway
     *
     * @var array
     */
    protected $mandatory = array();

    /**
     *
     * @var array
     */
    protected $optional = array();

    /**
     * Associative array of fields of form
     *
     * @var array
     */
    protected $data = array();

    /**
     * Constructor for SagepayAbstractApi
     *
     * @param SagepaySettings $config
     */
    public function __construct(SagepaySettings $config)
    {
        $this->config = $config;
        $this->_createBasket();
    }

    /**
     * Initialize Basket for current instance
     */
    private function _createBasket()
    {
        $this->basket = new SagepayBasket();
        $this->basket->setAgentId($this->config->getVendorName());
    }

    /**
     * Get config
     *
     * @return SagepaySettings
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get integrationMethod
     *
     * @return string
     */
    public final function getIntegrationMethod()
    {
        return $this->integrationMethod;
    }

    /**
     * Get basket
     *
     * @return SagepayBasket
     */
    public final function getBasket()
    {
        return $this->basket;
    }

    /**
     * Set basket
     *
     * @param SagepayBasket $basket
     */
    public final function setBasket(SagepayBasket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * Set txType
     *
     * @param string $txType
     */
    public final function setTxType($txType)
    {
        $this->txType = $txType;
    }

    /**
     * Get txType
     *
     * @return string
     */
    public final function getTxType()
    {
        return $this->config->getTxType();
    }

    /**
     * Get addressList
     *
     * @return SagepayCustomerDetails[]
     */
    public final function getAddressList()
    {
        return $this->addressList;
    }

    /**
     * Set addressList
     *
     * @param SagepayCustomerDetails[] $addressList
     */
    public final function setAddressList($addressList)
    {
        $this->addressList = $addressList;
    }

    /**
     * Add a set of customer details to addressList
     *
     * @param SagepayCustomerDetails $address
     */
    public final function addAddress(SagepayCustomerDetails $address)
    {
        $this->addressList[] = $address;
    }

    /**
     * Get paneValues
     *
     * @return array
     */
    public final function getPaneValues()
    {
        return $this->paneValues;
    }

    /**
     * Set paneValues
     *
     * @param array $paneValues
     */
    public final function setPaneValues($paneValues)
    {
        $this->paneValues = $paneValues;
    }

    /**
     * Get Customer information
     *
     * @return SagepayCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set Customer information
     *
     * @param SagepayCustomer $customer
     */
    public function setCustomer(SagepayCustomer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * Set data
     *
     * @param array $data
     */
    public final function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return array
     */
    public final function getData()
    {
        return $this->data;
    }

    /**
     * Add a field to data
     *
     * @param string $field
     * @param mixed $value
     */
    public final function setDataField($field, $value = null)
    {
        $this->data[$field] = $value;
    }

    /**
     * Update data values
     *
     * @param array $data
     */
    public final function updateData(array $data)
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Sort data in required order
     */
    protected function sortData()
    {
        $unsorted = $this->data;
        $data = array();
        foreach ($this->mandatory as $key)
        {
            $data[$key] = $unsorted[$key];
            unset($unsorted[$key]);
        }
        $data += $unsorted;
        $this->data = $data;
    }

    /**
     * Check required fields that will be sent to gateway
     *
     * @throws SagepayApiException
     */
    protected function checkMandatoryFields()
    {
        $emptyFields = array();

        foreach ($this->mandatory as $value)
        {
            if (is_null($this->data[$value]))
            {
                $emptyFields[] = $value;
            }
        }

        if (count($emptyFields))
        {
            $fields = implode(', ', $emptyFields);
            $beVerb = count($emptyFields) == 1 ? 'is' : 'are';
            throw new SagepayApiException($fields . ' ' . $beVerb . " empty");
        }
        $this->sortData();
    }

    /**
     * Populate data that is read from configurations
     */
    protected function addConfiguredValues()
    {
        $data = array(
            'Vendor' => $this->config->getVendorName(),
            'VPSProtocol' => $this->config->getProtocolVersion(),
            'Currency' => $this->config->getCurrency(),
            'TxType' => $this->config->getTxType(),
            'SendEMail' => $this->config->getSendEmail(),
            'VendorEMail' => $this->config->getVendorEmail(),
            'Apply3DSecure' => $this->config->getApply3dSecure(),
            'ApplyAVSCV2' => $this->config->getApplyAvsCv2(),
        );

        $partnerId = $this->config->getPartnerId();
        if (!empty($partnerId))
        {
            $data['ReferrerID'] = $partnerId;
        }
        if ($data['SendEMail'] == 1)
        {
            $data['eMailMessage'] = $this->config->getEmailMessage();
        }
        $allowGiftAid = $this->config->getAllowGiftAid();
        if (!isset($this->data['AllowGiftAid']))
        {
            $data['AllowGiftAid'] = $allowGiftAid;
        }

        $this->updateData($data);
    }

    /**
     * Generate values for payment
     *
     * @return string[]
     */
    abstract public function createRequest();

    /**
     * Generate UrlEncoded values
     *
     * @return string urlencoded data of request
     */
    abstract public function getQueryData();
}
