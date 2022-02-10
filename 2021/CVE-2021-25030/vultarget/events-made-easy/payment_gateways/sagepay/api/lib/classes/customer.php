<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * The customer details
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayCustomer
{

    /**
     * The middle initial of the customer
     *
     * @var string
     */
    private $_customerMiddleInitial;

    /**
     * The date of birth of the customer
     *
     * @var string
     */
    private $_customerBirth;

    /**
     * The work phone number of the customer
     *
     * @var string
     */
    private $_customerWorkPhone;

    /**
     * The mobile number of the customer
     *
     * @var string
     */
    private $_customerMobilePhone;

    /**
     * Whether the customer is a previous customer or new
     *
     * @var boolean
     */
    private $_previousCust = 0;

    /**
     * The number of days since the card was first seen
     *
     * @var int
     */
    private $_timeOnFile = 0;

    /**
     * The ID of the customer
     *
     * @var string
     */
    private $_customerId;

    /**
     * List of fields that should be exported to customer XML
     *
     * @var array
     */
    private $_exportFields = array(
        'customerMiddleInitial',
        'customerBirth',
        'customerWorkPhone',
        'customerMobilePhone',
        'previousCust',
        'timeOnFile',
        'customerId'
    );

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = array(
        'customerMiddleInitial' => array(
            array('exactLength', array(1)),
        ),
        'customerBirth' => array(
            array('exactLength', array(10)),
            array('regex', array("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}*$/")),
        ),
        'customerWorkPhone' => array(
            array('minLength', array(11)),
            array('maxLength', array(19)),
            array('regex', array("/^[0-9\-a-zA-Z+\s()]*$/")),
        ),
        'customerMobilePhone' => array(
            array('minLength', array(11)),
            array('maxLength', array(19)),
            array('regex', array("/^[0-9\-a-zA-Z+\s()]*$/")),
        ),
        'previousCust' => array(
            array('exactLength', array(1)),
            array('regex', array("/^[01]$/")),
        ),
        'timeOnFile' => array(
            array('maxLength', array(16)),
            array('regex', array("/^[0-9]+$/")),
        ),
        'customerId' => array(
            array('regex', array("/^[A-Za-z0-9]$/")),
        ),

    );

    /**
     * Get middle initial of the customer
     *
     * @return string
     */
    public function getCustomerMiddleInitial()
    {
        return $this->_customerMiddleInitial;
    }

    /**
     * Set middle initial of the customer
     *
     * @param string $customerMiddleInitial
     */
    public function setCustomerMiddleInitial($customerMiddleInitial)
    {
        $this->_customerMiddleInitial = $customerMiddleInitial;
    }

    /**
     * Get date of birth of the customer
     *
     * @return string
     */
    public function getCustomerBirth()
    {
        return $this->_customerBirth;
    }

    /**
     * Set date of birth of the customer
     *
     * @param string $customerBirth
     */
    public function setCustomerBirth($customerBirth)
    {
        $this->_customerBirth = $customerBirth;
    }

    /**
     * Get work phone number of the customer.
     *
     * @return string
     */
    public function getCustomerWorkPhone()
    {
        return $this->_customerWorkPhone;
    }

    /**
     * Set work phone number of the customer.
     *
     * @param string $customerWorkPhone
     */
    public function setCustomerWorkPhone($customerWorkPhone)
    {
        $this->_customerWorkPhone = $customerWorkPhone;
    }

    /**
     * Get mobile number of the customer
     *
     * @return string
     */
    public function getCustomerMobilePhone()
    {
        return $this->_customerMobilePhone;
    }

    /**
     * Set mobile number of the customer
     *
     * @param string $customerMobilePhone
     */
    public function setCustomerMobilePhone($customerMobilePhone)
    {
        $this->_customerMobilePhone = $customerMobilePhone;
    }

    /**
     * Get is a previous customer
     *
     * @return int
     */
    public function getPreviousCust()
    {
        return $this->_previousCust;
    }

    /**
     * Set is a previous customer
     *
     * @param int $previousCust
     */
    public function setPreviousCust($previousCust)
    {
        $this->_previousCust = intval(!!$previousCust);
    }

    /**
     * Get the number of days since the card was first seen.
     *
     * @return int
     */
    public function getTimeOnFile()
    {
        return $this->_timeOnFile;
    }

    /**
     * Set the number of days since the card was first seen.
     *
     * @param int $timeOnFile
     */
    public function setTimeOnFile($timeOnFile)
    {
        $this->_timeOnFile = intval($timeOnFile);
    }

    /**
     * Get customer ID
     *
     * @return string
     */
    public function getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Set customer ID
     *
     * @param string $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->_customerId = $customerId;
    }

    /**
     * Export customer details as XML string
     *
     * @return string XML with customer details
     */
    public function export()
    {
        $dom = new DOMDocument();
        $dom->loadXML("<customer></customer>");
        foreach ($this->_exportFields as $field)
        {
            $value = NULL;
            $getter = 'get' . ucfirst($field);
            if (method_exists($this, $getter))
            {
                $value = $this->$getter();
            }

            if (empty($value))
            {
                continue;
            }
            $node = $dom->createElement($field, $value);
            $dom->documentElement->appendChild($node);
        }
        return $dom->saveXML($dom->documentElement);
    }

}

