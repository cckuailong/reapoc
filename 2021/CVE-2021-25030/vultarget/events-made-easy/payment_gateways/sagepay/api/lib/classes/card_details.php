<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * The card details used for payment process
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayCardDetails
{

    /**
     * The card type
     *
     * @var string
     */
    private $_cardType;

    /**
     * The card number
     *
     * @var string
     */
    private $_cardNumber;

    /**
     * The Cardholder Name as it appears on the card
     *
     * @var string
     */
    private $_cardHolder;

    /**
     * The start date of card
     *
     * @var string
     */
    private $_startDate;

    /**
     * The ecpiry date of card
     *
     * @var string
     */
    private $_expiryDate;

    /**
     * The Card Verification Value
     *
     * @var string
     */
    private $_cv2;

    /**
     * Allows the gift aid acceptance box to appear for this transaction on the payment page
     *
     * @var string
     */
    private $_giftAid;

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = array(
        'cardNumber' => array(
            array('notEmpty'),
            array('creditCard'),
        ),
        'cardHolder' => array(
            array('notEmpty'),
            array('maxLength', array(20)),
            array('regex', array("/^[a-zA-Z\xC0-\xFF0-9\s\\\\\/&\.\']*$/")),
        ),
        'startDate' => array(
            array('regex', array("/^([0-9]{4})*$/")),
        ),
        'expiryDate' => array(
            array('notEmpty'),
            array('regex', array("/^([0-9]{4})*$/")),
        ),
        'cv2' => array(
            array('notEmpty'),
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
     * Validates values using validation rules and return the result
     *
     * @return array
     */
    public function validate()
    {
        if ($this->cardType == 'AMEX')
        {
            $this->rules['cv2'][] = array('exactLength', array(3, 4));
        }
        else
        {
            $this->rules['cv2'][] = array('exactLength', array(3));
        }

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

}
