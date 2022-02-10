<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Network;

/**
 *
 * @package Bitcore
 */
interface NetworkAwareInterface
{
    /**
     * Set the network the object will work with
     *
     * @param NetworkInterface $network
     */
    public function setNetwork(NetworkInterface $network = null);
}
