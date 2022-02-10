<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Interface PayoutInstructionInterface
 * @package Bitpay
 */
interface PayoutInstructionInterface
{

    /**
     * Get Bitpay ID for this instruction.
     * @return string
     */
    public function getId();

    /**
     * Get Label for instruction payout.
     * @return string
     */
    public function getLabel();

    /**
     * Get the bitcoin address for the recipient.
     * @return string
     */
    public function getAddress();

    /**
     * Return the amount to pay the recipient.
     * @return string
     */
    public function getAmount();

    /**
     * Get the BTC array (once rates are set)
     * @return array
     */
    public function getBtc();

    /**
     * Return the status of this payout instruction
     * @return string
     */
    public function getStatus();

    /**
     * Return the transactions for this payout
     * @return array
     */
    public function getTransactions();
}
