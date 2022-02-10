<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Storage;

/**
 * @package Bitcore
 */
interface StorageInterface
{
    /**
     * @param KeyInterface $key
     */
    public function persist(\Bitpay\KeyInterface $key);

    /**
     * @param string $id
     *
     * @return KeyInterface
     */
    public function load($id);
}
