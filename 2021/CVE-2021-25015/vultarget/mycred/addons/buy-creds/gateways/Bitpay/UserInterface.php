<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 *
 * @package Bitpay
 */
interface UserInterface
{
    /**
     * @return string
     */
    public function getPhone();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * $address = array($lineOne, $lineTwo);
     *
     * @return array
     */
    public function getAddress();

    /**
     * @return string
     */
    public function getCity();

    /**
     * @return string
     */
    public function getState();

    /**
     * @return string
     */
    public function getZip();

    /**
     * @return string
     */
    public function getCountry();

    /**
     * @return boolean
     */
    public function getNotify();
}
