<?php

namespace Paymill\Models\Response;

/**
 * Offer Model
 * An offer is a recurring plan which a user can subscribe to. 
 * You can create different offers with different plan attributes e.g. a monthly or a yearly based paid offer/plan.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-offers
 */
class Offer extends Base
{
    /**
     * Name of the offer
     * @var string
     */
    private $_name;
    
    /**
     * Every interval the specified amount will be charged. Only integer values are allowed (e.g. 42.00 = 4200)
     * @var integer 
     */
    private $_amount;
    
    /**
     * ISO 4217 formatted currency code
     * @var string
     */
    private $_currency;
    
    /**
     * Defining how often the client should be charged. Format: number DAY | WEEK | MONTH | YEAR Example: 2 DAY
     * @var string 
     */
    private $_interval;
    
    /**
     * Give it a try or charge directly
     * @var integer|null
     */
    private $_trialPeriodDays;
    
    /**
     * Number of active and inactive subscribers
     * @var array
     * @example subscriptionCount = array(active => [integer or string], inactive => [integer or string])
     */
    private $_subscriptionCount = array();

    /**
     * Returns Your name for this offer
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets Your name for this offer
     * @param string $name
     * @return \Paymill\Models\Response\Offer
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Returns the amount as an integer
     * @return integer
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Sets the amount.
     * Every interval the specified amount will be charged. Only integer values are allowed (e.g. 42.00 = 4200)
     * @param integer $amount
     * @return \Paymill\Models\Response\Offer
     */
    public function setAmount($amount)
    {
        $this->_amount = (int)$amount;
        return $this;
    }

    /**
     * Returns the interval defining how often the client should be charged.
     * @return string
     */
    public function getInterval()
    {
        return $this->_interval;
    }

    /**
     * Sets the interval defining how often the client should be charged. 
     * @example Format: number DAY | WEEK | MONTH | YEAR Example: 2 DAY
     * @param string $interval
     * @return \Paymill\Models\Response\Offer
     */
    public function setInterval($interval)
    {
        $this->_interval = $interval;
        return $this;
    }

    /**
     * Returns the number of days to try
     * @return integer
     */
    public function getTrialPeriodDays()
    {
        return $this->_trialPeriodDays;
    }

    /**
     * Sets the number of days to try
     * @param integer $trialPeriodDays
     * @return \Paymill\Models\Response\Offer
     */
    public function setTrialPeriodDays($trialPeriodDays)
    {
        $this->_trialPeriodDays = $trialPeriodDays;
        return $this;
    }

    /**
     * Returns the subscriptionCount array
     * @return array
     */
    public function getSubscriptionCount()
    {
        return $this->_subscriptionCount;
    }

    /**
     * Sets the subscriptionCount array
     * @param string|integer $active
     * @param string|integer $inactive
     * @return \Paymill\Models\Response\Offer
     */
    public function setSubscriptionCount($active, $inactive)
    {
        $this->_subscriptionCount['active'] = $active;
        $this->_subscriptionCount['inactive'] = $inactive;
        return $this;
    }

    /**
     * Returns the currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Sets the currency
     * @param string $currency
     * @return Offer
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

}