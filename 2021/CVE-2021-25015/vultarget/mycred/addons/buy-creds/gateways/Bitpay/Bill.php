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
class Bill implements BillInterface
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var CurrencyInterface
     */
    protected $currency;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $address;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $showRate;

    /**
     * @var boolean
     */
    protected $archived;

    /**
     */
    public function __construct()
    {
        $this->address  = array();
        $this->archived = false;
        $this->currency = new Currency();
        $this->items    = array();
    }

    /**
     * @inheritdoc
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ItemInterface $item
     *
     * @return BillInterface
     */
    public function addItem(ItemInterface $item)
    {
        if (!empty($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return BillInterface
     */
    public function setCurrency(CurrencyInterface $currency)
    {
        if (!empty($currency)) {
            $this->currency = $currency;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BillInterface
     */
    public function setName($name)
    {
        if (!empty($name) && ctype_print($name)) {
            $this->name = trim($name);
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
     * @param array $address
     *
     * @return BillInterface
     */
    public function setAddress($address)
    {
        if (!empty($address) && is_array($address)) {
            $this->address = $address;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return BillInterface
     */
    public function setCity($city)
    {
        if (!empty($city) && ctype_print($city)) {
            $this->city = trim($city);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return BillInterface
     */
    public function setState($state)
    {
        if (!empty($state) && ctype_print($state)) {
            $this->state = trim($state);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     *
     * @return BillInterface
     */
    public function setZip($zip)
    {
        if (!empty($zip) && ctype_print($zip)) {
            $this->zip = trim($zip);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return BillInterface
     */
    public function setCountry($country)
    {
        if (!empty($country) && ctype_print($country)) {
            $this->country = trim($country);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return BillInterface
     */
    public function setEmail($email)
    {
        if (!empty($email) && ctype_print($email)) {
            $this->email = trim($email);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return BillInterface
     */
    public function setPhone($phone)
    {
        if (!empty($phone) && ctype_print($phone)) {
            $this->phone = trim($phone);
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
     * @param string $status
     *
     * @return BillInterface
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
    public function getShowRate()
    {
        return $this->showRate;
    }

    /**
     * @param string $showRate
     *
     * @return BillInterface
     */
    public function setShowRate($showRate)
    {
        if (!empty($showRate) && ctype_print($showRate)) {
            $this->showRate = trim($showRate);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArchived()
    {
        return $this->archived;
    }

    /**
     * @param boolean $archived
     *
     * @return BillInterface
     */
    public function setArchived($archived)
    {
        $this->archived = (boolean) $archived;

        return $this;
    }
}
