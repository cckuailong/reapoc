<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Class PayoutInstruction
 * @package Bitpay
 */
class PayoutInstruction implements PayoutInstructionInterface
{
    /**
     * A transaction is unpaid when the payout in bitcoin has not yet occured.
     */
    const STATUS_UNPAID = 'unpaid';

    /**
     * A transaction is marked as paid once the payroll is complete and bitcoins are
     * sent to the recipient
     */
    const STATUS_PAID = 'paid';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    protected $btc;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var float
     */
    protected $amount;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     */
    protected $transactions = array();

    public function __construct()
    {
        $this->transactions = array();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the Bitpay ID for this payout instruction
     *
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        if (!empty($id)) {
            $this->id = trim($id);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the employers label for this instruction.
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        if (!empty($label)) {
            $this->label = trim($label);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set the bitcoin address for this instruction.
     * @param $address
     * @return $this
     */
    public function setAddress($address)
    {
        if (!empty($address)) {
            $this->address = trim($address);
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
     * Set the amount for this instruction.
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
    public function getBtc()
    {
        return $this->btc;
    }

    /**
     * Set BTC array (available once rates are set)
     * @param $btc
     * @return $this
     */
    public function setBtc($btc)
    {
        if (!empty($btc) && is_array($btc)) {
            $this->btc = $btc;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status for this instruction
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        if (!empty($status) && ctype_print($status)) {
            $this->status = trim($status);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Add payout transaction to the
     * @param PayoutTransactionInterface $transaction
     * @return $this
     */
    public function addTransaction(PayoutTransactionInterface $transaction)
    {
        if (!empty($transaction)) {
            $this->transactions[] = $transaction;
        }

        return $this;
    }
}
