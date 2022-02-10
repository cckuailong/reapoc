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
class Testnet implements NetworkInterface
{
    public function getName()
    {
        return 'testnet';
    }

    public function getAddressVersion()
    {
        return 0x6f;
    }

    public function getApiHost()
    {
        return 'test.bitpay.com';
    }

    public function getApiPort()
    {
        return 443;
    }
}
