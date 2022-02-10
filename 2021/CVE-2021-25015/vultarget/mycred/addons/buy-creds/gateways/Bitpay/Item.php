<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\Client;

/**
 *
 * @package Bitpay
 */
class Item implements ItemInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var integer
     */
    protected $quantity;

    /**
     * @var boolean
     */
    protected $physical;

    /**
     */
    public function __construct()
    {
        $this->physical = false;
    }

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return ItemInterface
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return ItemInterface
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price A float, integer, or en_US formatted numeric string
     *
     * @return ItemInterface
     */
    public function setPrice($price)
    {
        if (is_string($price)) {
            $this->checkPriceFormat($price);
        }

        $this->price = (float)$price;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param integer $quantity
     *
     * @return ItemInterface
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isPhysical()
    {
        return $this->physical;
    }

    /**
     * @param boolean $physical
     *
     * @return ItemInterface
     */
    public function setPhysical($physical)
    {
        $this->physical = (boolean)$physical;

        return $this;
    }

    /**
     * Checks the new price to include BTC
     * values with more than 6 decimals.
     *
     * @param string $price The price value to check
     * @throws \Exception
     */
    protected function checkPriceFormat($price)
    {
        if (preg_match('/^[0-9]+?[\.,][0-9]{1,6}?$/', $price) !== 1) {
            throw new \Bitpay\Client\ArgumentException("Price must be formatted as a float");
        }
    }
}
