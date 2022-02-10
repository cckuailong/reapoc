<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * The Sage Pay Direct integration method API
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayDirectApi extends SagepayAbstractApi
{

    /**
     * The Server URL for integration methods
     *
     * @var string
     */
    private $_vpsDirectUrl;

    /**
     * Integration method
     *
     * @var string
     */
    protected $integrationMethod = SAGEPAY_DIRECT;

    /**
     * Constructor for SagepayDirectApi
     *
     * @param SagepaySettings $config
     */
    public function __construct(SagepaySettings $config)
    {
        parent::__construct($config);
        $this->_vpsDirectUrl = $config->getPurchaseUrl('direct');
        $this->mandatory = array(
            'VPSProtocol',
            'TxType',
            'Vendor',
            'VendorTxCode',
            'Amount',
            'Currency',
            'Description',
            'BillingSurname',
            'BillingFirstnames',
            'BillingAddress1',
            'BillingCity',
            'BillingPostCode',
            'BillingCountry',
            'DeliverySurname',
            'DeliveryFirstnames',
            'DeliveryAddress1',
            'DeliveryCity',
            'DeliveryPostCode',
            'DeliveryCountry',
        );
    }

    /**
     * Generate values for payment.
     * Ensure that post data is setted to request with SagepayAbstractApi::setData()
     *
     * @see SagepayAbstractApi::createRequest()
     * @return array The response from Sage Pay
     */
    public function createRequest()
    {
        $this->data = SagepayCommon::encryptedOrder($this);
        $this->addConfiguredValues();
        $this->checkMandatoryFields();

        $ttl = $this->config->getRequestTimeout();
        $caCert = $this->config->getCaCertPath();
        return SagepayCommon::requestPost($this->_vpsDirectUrl, $this->data, $ttl, $caCert);
    }

    /**
     * Set integrationMethod
     *
     * @param string $integrationMethod
     */
    public function setIntegrationMethod($integrationMethod)
    {
        if (in_array($integrationMethod, array(SAGEPAY_DIRECT, SAGEPAY_PAYPAL, SAGEPAY_TOKEN)))
        {
            $this->integrationMethod = $integrationMethod;
        }
    }

    /**
     * @see SagepayAbstractApi::getQueryData()
     * @return null
     */
    public function getQueryData()
    {
        return null;
    }

    /**
     * Get vpsDirectUrl
     *
     * @return string
     */
    public function getVpsDirectUrl()
    {
        return $this->_vpsDirectUrl;
    }

    /**
     * Set vpsDirectUrl
     *
     * @uses SagepayValid::url Validate URL field
     * @param string $vpsDirectUrl
     */
    public function setVpsDirectUrl($vpsDirectUrl)
    {
        if (SagepayValid::url($vpsDirectUrl))
        {
            $this->_vpsDirectUrl = $vpsDirectUrl;
        }
    }


}

