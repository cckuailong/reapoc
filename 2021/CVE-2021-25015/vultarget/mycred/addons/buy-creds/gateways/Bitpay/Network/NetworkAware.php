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
abstract class NetworkAware implements NetworkAwareInterface
{
    /**
     * @var NetworkInterface
     */
    protected $network;

    /**
     * @inheritdoc
     */
    public function setNetwork(NetworkInterface $network = null)
    {
        $this->network = $network;
    }
}
