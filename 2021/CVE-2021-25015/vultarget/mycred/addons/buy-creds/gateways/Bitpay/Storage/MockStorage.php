<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

/**
 * @codeCoverageIgnore
 * @package Bitcore
 */
class MockStorage implements StorageInterface
{
    public function persist(\Bitpay\KeyInterface $key)
    {
    }

    public function load($id)
    {
        return;
    }
}
