<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * Used to manage keys
 *
 * @package Bitcore
 */
class KeyManager
{
    /**
     * @var Bitpay\Storage\StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(\Bitpay\Storage\StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param KeyInterface $key
     */
    public function persist(KeyInterface $key)
    {
        $this->storage->persist($key);
    }

    /**
     * @return KeyInterface
     */
    public function load($id)
    {
        return $this->storage->load($id);
    }
}
