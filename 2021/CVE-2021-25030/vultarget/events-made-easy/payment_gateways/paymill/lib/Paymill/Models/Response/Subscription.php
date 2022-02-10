<?php

namespace Paymill\Models\Response;

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
     * @var \Paymill\Models\Response\Offer
     */
    private $_offer;

    /**
     * @var boolean
     */
    private $_livemode;

    /**
     * @var integer
     */
    private $_trialStart;

    /**
     * @var integer
     */
    private $_trialEnd;

    /**
     * @var integer
     */
    private $_nextCaptureAt;

    /**
     * @var integer
     */
    private $_canceledAt;

    /**
     * @var \Paymill\Models\Response\Payment
     */
    private $_payment;

    /**
     * @var \Paymill\Models\Response\Client
     */
    private $_client;

    /**
     * @var integer
     */
    private $_startAt;

    /**
     * @var boolean
     */
    private $_isCanceled;

    /**
     * @var boolean
     */
    private $_isDeleted;

    /**
     * @var string
     */
    private $_status;

    /**
     * @var string
     */
    private $_periodOfValidity;

    /**
     * @var int
     */
    private $_endOfPeriod;

    /**
     * @var int
     */
    private $_amountChangeType;

    /**
     * @var int
     */
    private $_offerChangeType;

    /**
     * @var int
     */
    private $_amount;

    /**
     * @var int
     */
    private $_tempAmount;

    /**
     * @var string
     */
    private $_mandateReference;

    /**
     * Returns the model of the offer the subscription is based on
     * @return \Paymill\Models\Response\Offer
     */
    public function getOffer()
    {
        return $this->_offer;
    }

    /**
     * Sets the model of the offer the subscription is based on
     * @param \Paymill\Models\Response\Offer $offer
     * @return \Paymill\Models\Response\Subscription
     */
    public function setOffer($offer)
    {
        $this->_offer = $offer;
        return $this;
    }

    /**
     * Returns the flag determining whether this subscription was issued while being in live mode or not.
     * @return boolean
     */
    public function getLivemode()
    {
        return $this->_livemode;
    }

    /**
     * Sets the flag determining whether this subscription was issued while being in live mode or not.
     * @param string $livemode
     * @return \Paymill\Models\Response\Subscription
     */
    public function setLivemode($livemode)
    {
        $this->_livemode = $livemode;
        return $this;
    }


    /**
     * Returns the Unix-Timestamp for the trial period start
     * @return integer
     */
    public function getTrialStart()
    {
        return $this->_trialStart;
    }

    /**
     * Sets the Unix-Timestamp for the trial period start
     * @param integer $trialStart
     * @return \Paymill\Models\Response\Subscription
     */
    public function setTrialStart($trialStart)
    {
        $this->_trialStart = $trialStart;
        return $this;
    }

    /**
     * Returns the Unix-Timestamp for the trial period end.
     * @return integer
     */
    public function getTrialEnd()
    {
        return $this->_trialEnd;
    }

    /**
     * Sets the Unix-Timestamp for the trial period end.
     * @param integer $trialEnd
     * @return \Paymill\Models\Response\Subscription
     */
    public function setTrialEnd($trialEnd)
    {
        $this->_trialEnd = $trialEnd;
        return $this;
    }

    /**
     * Returns the Unix-Timestamp for the next charge.
     * @return integer
     */
    public function getNextCaptureAt()
    {
        return $this->_nextCaptureAt;
    }

    /**
     * Sets the Unix-Timestamp for the next charge.
     * @param integer $nextCaptureAt
     * @return \Paymill\Models\Response\Subscription
     */
    public function setNextCaptureAt($nextCaptureAt)
    {
        $this->_nextCaptureAt = $nextCaptureAt;
        return $this;
    }

    /**
     * Returns the Unix-Timestamp for the cancel date.
     * @return integer
     */
    public function getCanceledAt()
    {
        return $this->_canceledAt;
    }

    /**
     * Sets the Unix-Timestamp for the cancel date.
     * @param integer $canceledAt
     * @return \Paymill\Models\Response\Subscription
     */
    public function setCanceledAt($canceledAt)
    {
        $this->_canceledAt = $canceledAt;
        return $this;
    }

    /**
     * Returns the payment object registered with this subscription
     * @return \Paymill\Models\Response\Payment
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Sets the payment object registered with this subscription
     * @param \Paymill\Models\Response\Payment $payment
     * @return \Paymill\Models\Response\Subscription
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }

    /**
     * Returns the client associated with this subscription
     * @return \Paymill\Models\Response\Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Sets the client associated with this subscription
     * @param \Paymill\Models\Response\Client $client
     * @return \Paymill\Models\Response\Subscription
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
     * @return \Paymill\Models\Response\Subscription
     */
    public function setStartAt($startAt)
    {
        $this->_startAt = $startAt;
        return $this;
    }

    /**
     * (un)cancel subscription
     * @param boolean $canceled
     * @return \Paymill\Models\Response\Subscription
     */
    public function setIsCanceled($canceled)
    {
        $this->_isCanceled = $canceled;
        return $this;
    }

    /**
     * Returns whether subscription is canceled or not
     * @return boolean
     */
    public function getIsCanceled()
    {
        return $this->_isCanceled;

    }

    /**
     * (un)delete subscription
     * @param boolean $deleted
     * @return \Paymill\Models\Response\Subscription
     */
    public function setIsDeleted($deleted)
    {
        $this->_isDeleted = $deleted;
        return $this;
    }

    /**
     * Returns whether subscription is deleted or not
     * @return boolean
     */
    public function getIsDeleted()
    {
        return $this->_isDeleted;
    }

    /**
     * Sets the status of subscription
     * @param string $status
     * @return \Paymill\Models\Response\Subscription
     */
    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * Returns subscription status
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Set the period of time the subscription shall be active/valid (starting creation date)
     * @param $perdiodOfValidity
     * @return \Paymill\Models\Response\Subscription
     */
    public function setPeriodOfValidity($periodOfValidity)
    {
        $this->_periodOfValidity = $periodOfValidity;
        return $this;
    }

    /**
     * Returns the period of time the subscriptions is valid (starting creation date)
     * @return string
     */
    public function getPeriodOfValidity()
    {
        return $this->_periodOfValidity;
    }

    /**
     * Sets the end of validity period
     * @param int $endOfPeriod
     *
     * @return Subscription
     */
    public function setEndOfPeriod($endOfPeriod)
    {
        $this->_endOfPeriod = $endOfPeriod;

        return $this;
    }

    /**
     * Returns the end of validity period
     * @return int
     */
    public function getEndOfPeriod()
    {
        return $this->_endOfPeriod;
    }

    /**
     * Set amount change type
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
     * Return amount change type
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
        $this->_offerChangeType;
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
     * Set subscription amount
     * @param $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
        return $this;
    }

    /**
     * Return subscription amount
     * @return int
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Set subscription temp_amount
     * @param $tempAmount
     *
     * @return $this
     */
    public function setTempAmount($tempAmount)
    {
        $this->_tempAmount = $tempAmount;
        return $this;
    }

    /**
     * Return subscription temp_amount
     * @return int
     */
    public function getTempAmount()
    {
        return $this->_tempAmount;
    }

    /** Set mandate reference mandate_reference
     * @param string $mandateReference
     */
    public function setMandateReference($mandateReference)
    {
        $this->_mandateReference = $mandateReference;
    }

    /**
     * Return mandate reference mandate_reference
     * @return string
     */
    public function getMandateReference()
    {
        return $this->_mandateReference;
    }

}
