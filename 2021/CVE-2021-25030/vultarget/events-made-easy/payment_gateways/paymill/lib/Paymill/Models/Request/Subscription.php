<?php

namespace Paymill\Models\Request;

/**
 * Subscription Model
 * Subscriptions allow you to charge recurring payments on a client’s credit card / to a client’s direct debit.
 * A subscription connects a client to the offers-object. A client can have several subscriptions to different offers,
 * but only one subscription to the same offer.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-subscriptions
 */
class Subscription extends Base
{

    /**
     * @var string
     */
    private $_name;

    /**
     * @var int
     */
    private $_amount;

    /**
     * @var string
     */
    private $_currency;

    /**
     * @var string
     */
    private $_interval;

    /**
     * @var string
     */
    private $_offer;

    /**
     * @var string
     */
    private $_payment;

    /**
     * @var string
     */
    private $_token;

    /**
     * @var string
     */
    private $_client;

    /**
     * @var integer
     */
    private $_startAt;

    /**
     * @var string
     */
    private $_periodOfValidity;

    /**
     * @var boolean
     */
    private $_pause;

    /**
     * @var int timestamp
     */
    private $_trialEnd;

    /**
     * @var int
     */
    private $_amountChangeType;

    /**
     * @var int
     */
    private $_offerChangeType;

    /**
     * @var
     */
    private $_remove;

    /**
     * @var string
     */
    private $_mandateReference;

    /**
     * Creates an instance of the subscription request model
     */
    public function __construct()
    {
        $this->_serviceResource = 'Subscriptions/';
    }


    /**
     * Returns name of subscription
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Sets name of the subscription
     * @param $name string
     * @return \Paymill\Models\Request\Subscription
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
     * @return \Paymill\Models\Request\Subscription
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
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
     * Additionally a special day of the week can be appended (unless daily interval)
     * @example Format: number DAY || number WEEK | MONTH | YEAR [, MONDAY | TUESDAY | ... | SUNDAY] Example: 3 WEEK, MONDAY
     * @param string $interval
     * @return \Paymill\Models\Request\Subscription
     */
    public function setInterval($interval)
    {
        $this->_interval = $interval;
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
     * @return \Paymill\Models\Request\Subscription
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }


    public function getOffer()
    {
        return $this->_offer;
    }

    /**
     * Sets the identifier of the offer the subscription is based on
     * @param string $offer
     * @return \Paymill\Models\Request\Subscription
     */
    public function setOffer($offer)
    {
        $this->_offer = $offer;
        return $this;
    }

    /**
     * Returns the identifier of the payment object registered with this subscription
     * @return string
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Sets the identifier of the payment object registered with this subscription
     * @param string $payment
     * @return \Paymill\Models\Request\Subscription
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }

    /**
     * Returns the id of the client associated with this subscription
     * @return string
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Sets the id of the client associated with this subscription
     * @param string $client
     * @return \Paymill\Models\Request\Subscription
     */
    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * Returns the Unix-Timestamp for the trial period start
     * @return integer
     */
    public function getStartAt()
    {
        return $this->_startAt;
    }

    /**
     * Sets the Unix-Timestamp for the trial period start
     * @param integer $startAt
     * @return \Paymill\Models\Request\Subscription
     */
    public function setStartAt($startAt)
    {
        $this->_startAt = $startAt;
        return $this;
    }

    /**
     * Sets the period of validity the subscriptions shall be active (starting creation date)
     * @param $periodOfValidity string
     * @return \Paymill\Models\Request\Subscription
     */
    public function setPeriodOfValidity($periodOfValidity)
    {
        $this->_periodOfValidity = $periodOfValidity;
        return $this;
    }

    /**
     * Returns period of validity
     * @return string
     */
    public function getPeriodOfValidity()
    {
        return $this->_periodOfValidity;
    }

    /**
     * Returns if subscription is paused or not
     * @return boolean
     */
    public function getPause()
    {
        return $this->_pause;
    }

    /**
     * Sets the state of subscription to paused or unpaused
     * @param $pause boolean
     * @return \Paymill\Models\Request\Subscription
     */
    public function setPause($pause)
    {
        $this->_pause = $pause;
        return $this;
    }

    /**
     * returns timestamp of subscription start
     * @return mixed
     */
    public function getTrialEnd()
    {
        return $this->_trialEnd;
    }

    /**
     * set timestamp for when subscription shall start
     * @param $trialEnd
     * @return $this
     */
    public function setTrialEnd($trialEnd)
    {
        $this->_trialEnd = $trialEnd;
        return $this;
    }

    /**
     * set amount change type
     *
     * @param $amountChangeType
     * @return $this
     */
    public function setAmountChangeType($amountChangeType)
    {
        $this->_amountChangeType = $amountChangeType;
        return $this;
    }

    /**
     * get amount change type
     * @return int
     */
    public function getAmountChangeType()
    {
        return $this->_amountChangeType;
    }

    /**
     * Set offer change type
     * @param $offerChangeType
     *
     * @return $this
     */
    public function setOfferChangeType($offerChangeType)
    {
        $this->_offerChangeType = $offerChangeType;
        return $this;
    }

    /**
     * Return offer change type
     * @return int
     */
    public function getOfferChangeType()
    {
        return $this->_offerChangeType;
    }

    /**
     * Returns the token required for the creation of subscription
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the token required for the creation of subscription
     * @param string $token
     * @return \Paymill\Models\Request\Subscription
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Returns true if subscription should also be removed
     * @return mixed
     */
    public function getRemove()
    {
        return $this->_remove;
    }

    /**
     * If set to true subscription will also be removed
     * @param $remove
     * @return \Paymill\Models\Request\Subscription
     */
    public function setRemove($remove)
    {
        $this->_remove = $remove;
        return $this;
    }

    /**
     * Returns mandate reference
     * @return string
     */
    public function getMandateReference()
    {
        return $this->_mandateReference;
    }

    /**
     * Set mandate reference
     *
     * @param string $mandateReference
     *
     * @return $this
     */
    public function setMandateReference($mandateReference)
    {
        $this->_mandateReference = $mandateReference;

        return $this;
    }



    /**
     * Returns an array of parameters customized for the argumented methodname
     * @param string $method
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = array();
        switch ($method) {
            case 'create':
                if (!is_null($this->getClient())) {
                    $parameterArray['client'] = $this->getClient();
                }
                if (!is_null($this->getOffer())) {
                    $parameterArray['offer'] = $this->getOffer();
                }
                $parameterArray['payment'] = $this->getPayment();

                if (!is_null($this->getAmount())) {
                    $parameterArray['amount'] = $this->getAmount();
                }
                if (!is_null($this->getCurrency())) {
                    $parameterArray['currency'] = $this->getCurrency();
                }
                if (!is_null($this->getInterval())) {
                    $parameterArray['interval'] = $this->getInterval();
                }
                if (!is_null($this->getName())) {
                    $parameterArray['name'] = $this->getName();
                }
                if (!is_null($this->getPeriodOfValidity())) {
                    $parameterArray['period_of_validity'] = $this->getPeriodOfValidity();
                }
                if (!is_null($this->getTrialEnd())) {
                    $parameterArray['trial_end']  = $this->getTrialEnd();
                }
                if (!is_null($this->getStartAt())) {
                    $parameterArray['start_at']  = $this->getStartAt();
                }
                if (!is_null($this->getMandateReference())) {
                    $parameterArray['mandate_reference'] = $this->getMandateReference();
                }
                break;
            case 'update':
                if (!is_null($this->getOffer())) {
                    $parameterArray['offer'] = $this->getOffer();
                }
                if (!is_null($this->getPayment())) {
                    $parameterArray['payment'] = $this->getPayment();
                } else {
                    $parameterArray['token'] = $this->getToken();
                }
                if (!is_null($this->getAmount())) {
                    $parameterArray['amount'] = $this->getAmount();
                }
                if (!is_null($this->getCurrency())) {
                    $parameterArray['currency'] = $this->getCurrency();
                }
                if (!is_null($this->getInterval())) {
                    $parameterArray['interval'] = $this->getInterval();
                }
                if (!is_null($this->getName())) {
                    $parameterArray['name'] = $this->getName();
                }
                if (!is_null($this->getPause())) {
                    $parameterArray['pause'] = $this->getPause();
                }
                if (!is_null($this->getPeriodOfValidity())) {
                    $parameterArray['period_of_validity'] = $this->getPeriodOfValidity();
                }
                if (!is_null($this->getTrialEnd())) {
                    $parameterArray['trial_end']  = $this->getTrialEnd();
                }
                if (!is_null($this->getAmountChangeType())) {
                    $parameterArray['amount_change_type'] = $this->getAmountChangeType();
                }
                if (!is_null($this->getOfferChangeType())) {
                    $parameterArray['offer_change_type'] = $this->getOfferChangeType();
                }
                if (!is_null($this->getMandateReference())) {
                    $parameterArray['mandate_reference'] = $this->getMandateReference();
                }
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
            case 'delete':
                if (!is_null($this->getRemove())){
                    $parameterArray['remove'] = $this->getRemove();
                }
                break;
        }

        return $parameterArray;
    }
}
