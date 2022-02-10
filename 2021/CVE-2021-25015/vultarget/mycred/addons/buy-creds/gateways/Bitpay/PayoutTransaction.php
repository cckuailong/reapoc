<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Class PayoutTransaction
 * @package Bitpay
 */
class PayoutTransaction implements PayoutTransactionInterface
{
    /**
     * @var string
     */
    protected $txid;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $date;

    /**
     * @inheritdoc
     */
    public function getTransactionId()
    {
        return $this->txid;
    }

    /**
     * Set transaction ID for payout.
     * @param $txid
     * @return $this
     */
    public function setTransactionId($txid)
    {
        if (!empty($txid)) {
            $this->txid = $txid;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the amount of bitcoin paid in the paout.
     * @param $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        if (!empty($amount)) {
            $this->amount = $amount;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the date and time of when the payment was sent.
     * @param $date
     * @return $this
     */
    public function setDate($date)
    {
        if (!empty($date)) {
            $this->date = $date;
        }

        return $this;
    }
}
