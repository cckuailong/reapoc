<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * The customer details used for payment process
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayCustomerDetails
{

    /**
     * The first name of the customer
     *
     * @var string
     */
    private $_firstname = '';

    /**
     * The last name of the customer
     *
     * @var string
     */
    private $_lastname = '';

    /**
     * The first address of the customer
     *
     * @var string
     */
    private $_address1 = '';

    /**
     * The second address of the customer
     *
     * @var string
     */
    private $_address2 = '';

    /**
     * The email of the customer
     *
     * @var string
     */
    private $_email = '';

    /**
     * The phone number of the customer
     *
     * @var string
     */
    private $_phone = '';

    /**
     * The city of the customer
     *
     * @var string
     */
    private $_city = '';

    /**
     * The postcode of the customer
     *
     * @var string
     */
    private $_postcode = '';

    /**
     * The country of the customer
     *
     * @var string
     */
    private $_country = '';

    /**
     * The state of the customer
     *
     * @var string
     */
    private $_state = '';

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = array(
        'firstname' => array(
            array('notEmpty'),
            array('maxLength', array(20)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\\\\\/&\.\']*$/")),
        ),
        'lastname' => array(
            array('notEmpty'),
            array('maxLength', array(20)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\\\\\/&\.\']*$/")),
        ),
        'address1' => array(
            array('notEmpty'),
            array('maxLength', array(100)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\+\'\\\\\/&:,\.\-()]*$/")),
        ),
        'address2' => array(
            array('maxLength', array(100)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\+\'\\\\\/&:,\.\-()]*$/")),
        ),
        'email' => array(
            array('maxLength', array(255)),
            array('email'),
        ),
        'phone' => array(
            array('maxLength', array(20)),
            array('regex', array("/^[0-9\-a-zA-Z+\s()]*$/")),
        ),
        'city' => array(
            array('notEmpty'),
            array('maxLength', array(40)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\+\'\\\\\/&:,\.\-()]*$/")),
        ),
        'postcode' => array(
            array('maxLength', array(10)),
            array('regex', array("/^[a-zA-Z0-9\s-]*$/")),
        ),
        'country' => array(
            array('notEmpty'),
            array('maxLength', array(2)),
            array('regex', array("/^[A-Z]{2}$/")),
        ),
        'state' => array(
            array('maxLength', array(2)),
            array('regex', array("/^([A-Z]{2})*$/")),
        ),
    );
    
    
    /**
     * Reading data from inaccessible properties.
     *
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        $privateName = "_" . $name;
        if (property_exists($this, $privateName))
        {
            return $this->$privateName;
        }
        return null;
    }

    /**
     * Writing data to inaccessible properties
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $privateName = "_" . $name;
        if (property_exists($this, $privateName))
        {
            $this->$privateName = $value;
        }
    }

    /**
     * Constructor for SagepayCustomerDetails
     */
    public function __construct()
    {
        $this->rules['state'][] = array(array($this, 'validUsa'));
        $this->rules['postcode'][] = array(array($this, 'notEmptyZipCodeUK'));
    }

    /**
     * Get default postcode if the address supplied didn't have one
     *
     * @param string $default   The default value to use when not found or empty
     *
     * @return string
     */
    public function getPostCode($default = '')
    {
        if (empty($this->_postcode))
        {
            $this->_postcode = $default;
        }
        return $this->_postcode;
    }

    /**
     * Validates values using validation rules and return the result
     *
     * @return string[]
     */
    public function validate()
    {
        $errors = array();
        foreach ($this->rules as $key => $rule)
        {
            $propertyValue = $this->$key;
            $validator = new SagepayValidator($propertyValue, $rule);
            if (!$validator->isValid())
            {
                $errors[$key] = $validator->getErrors();
            }
        }
        return $errors;
    }

    /**
     * Validate State Code for US only
     * Validate State Code for other country not US
     *
     * @param string $value
     *
     * @return boolean
     */
    public function validUsa($value)
    {
        if ($this->_country == 'US')
        {
            return SagepayValid::notEmpty($value);
        }
        else
        {
            return SagepayValid::equals($value, "");
        }
    }

    /**
     * Validate Zip Code for UK only
     *
     * @param string $value
     *
     * @return boolean
     */
    public function notEmptyZipCodeUK($value)
    {
        if ($this->_country == 'GB')
        {
            return SagepayValid::notEmpty($value);
        }
        return true;
    }

}
