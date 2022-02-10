<?php

namespace Paymill\Models\Response;

/**
 * Refund Model
 * Refunds are own objects with own calls for existing transactions. 
 * The refunded amount will be credited to the account of the client.
 */
class Refund extends Base
{
    /**
     * Transaction Model Instance
     * @var \Paymill\Models\Response\Transaction
     */
    private $_transaction;
    
    /**
     * Returns the transaction model
     * @return \Paymill\Models\Response\Transaction
     */
    public function getTransaction()
    {
        return $this->_transaction;
    }

    /**
     * Sets the transaction model
     * @param \Paymill\Models\Response\Transaction $transaction
     *
     * @return \Paymill\Models\Response\Refund
     */
    public function setTransaction($transaction)
    {
        $this->_transaction = $transaction;
        return $this;
    }
    
    /**
     * Amount in the smallest possible unit per currency (for euro, we’re calculating the amount in cents).
     * @var integer
     */
    private $_amount;
    
    /**
     * Returns the amount
     * @return integer
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Sets the amount
     * @param integer $amount
     *
     * @return \Paymill\Models\Response\Refund
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
        return $this;
    }
    
    /**
     * Indicates the current status of this refund. (open, pending, refunded)
     * @var string 
     */
    private $_status;
    
    /**
     * Returns the Status of the refund
     * @return string
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Sets the Status of the refund
     * @param string $status
     *
     * @return \Paymill\Models\Response\Refund
     */
    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }
    
    /**
     * The description given for this refund.
     * @var string
     */
    private $_description;
    
    /**
     * Returns the description of this refund
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the description of this refund
     * @param string $description
     *
     * @return \Paymill\Models\Response\Refund
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * @var string
     */
    private $_reason;

    /**
     * Sets the reason
     * @return string
     */
    public function getReason()
    {
        return $this->_reason;
    }

    /**
     * @param string $reason
     *
     * @return \Paymill\Models\Response\Refund
     */
    public function setReason($reason)
    {
        $this->_reason = $reason;

        return $this;
    }

    /**
     * Whether this refund happend in test- or in livemode.
     * @var boolean 
     */
    private $_livemode;
    
    /**
     * Returns the Livemode flag of this refund
     * @return boolean
     */
    public function getLivemode()
    {
        return $this->_livemode;
    }

    /**
     * Sets the Livemode flag of this refund
     * @param boolean $livemode
     * @return \Paymill\Models\Response\Refund
     */
    public function setLivemode($livemode)
    {
        $this->_livemode = $livemode;
        return $this;
    }
    
    /**
     * @var integer
     */
    private $_responseCode;

    /**
     * Returns the response code
     * @return integer
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }

    /**
     * Sets the response code
     * @param integer $responseCode
     * @return \Paymill\Models\Response\Refund
     */
    public function setResponseCode($responseCode)
    {
        $this->_responseCode = $responseCode;
        return $this;
    }

}