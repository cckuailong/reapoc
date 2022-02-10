<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * The Sage Pay Form integration method API
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayFormApi extends SagepayAbstractApi
{

    /**
     * Integration method
     *
     * @var string
     */
    protected $integrationMethod = SAGEPAY_FORM;

    /**
     *
     * @param SagepaySettings $config
     */
    public function __construct(SagepaySettings $config)
    {
        parent::__construct($config);
        $this->mandatory = array(
            'VendorTxCode',
            'Amount',
            'Currency',
            'Description',
            'SuccessURL',
            'FailureURL',
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
     * Return urlencoded string based on data
     *
     * @uses SagepayUtil::arrayToQueryString
     * @return string
     */
    public function getQueryData()
    {
        // Replace after implemeting right View content
        return SagepayUtil::arrayToQueryString($this->data);
    }

    /**
     * Generate values for payment.
     * Ensure that post data is setted to request with SagepayAbstractApi::setData()
     *
     * @see SagepayAbstractApi::createRequest()
     * @uses SagepayCommon::encryptedOrder
     * @return array The response from Sage Pay
     */
    public function createRequest()
    {
        $this->addConfiguredValues();
        return SagepayCommon::encryptedOrder($this);
    }

}

