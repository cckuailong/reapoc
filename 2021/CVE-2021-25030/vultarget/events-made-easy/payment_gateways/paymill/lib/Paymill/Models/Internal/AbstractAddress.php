<?php

namespace Paymill\Models\Internal;

/**
 * Abstract model address
 */
abstract class AbstractAddress
{
    const TYPE_SHIPPING = 'shipping_address';
    const TYPE_BILLING = 'billing_address';

    const FIELD_NAME = 'name';
    const FIELD_STREET_ADDRESS = 'street_address';
    const FIELD_STREET_ADDRESS_ADDITION = 'street_address_addition';
    const FIELD_CITY = 'city';
    const FIELD_STATE = 'state';
    const FIELD_POSTAL_CODE = 'postal_code';
    const FIELD_COUNTRY = 'country';
    const FIELD_PHONE = 'phone';

    /**
     * @var string|null
     */
    protected $_name;

    /**
     * @var string|null
     */
    protected $_streetAddress;

    /**
     * @var string|null
     */
    protected $_streetAddressAddition;

    /**
     * @var string|null
     */
    protected $_city;

    /**
     * @var string|null
     */
    protected $_state;

    /**
     * @var string|null
     */
    protected $_postalCode;

    /**
     * @var string|null
     */
    protected $_country;

    /**
     * @var string|null
     */
    protected $_phone;

    /**
     * Get name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set name
     *
     * @param null|string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get streetAddress
     *
     * @return null|string
     */
    public function getStreetAddress()
    {
        return $this->_streetAddress;
    }

    /**
     * Set streetAddress
     *
     * @param null|string $streetAddress
     *
     * @return $this
     */
    public function setStreetAddress($streetAddress)
    {
        $this->_streetAddress = $streetAddress;

        return $this;
    }

    /**
     * Get streetAddressAddition
     *
     * @return null|string
     */
    public function getStreetAddressAddition()
    {
        return $this->_streetAddressAddition;
    }

    /**
     * Set streetAddressAddition
     *
     * @param null|string $streetAddressAddition
     *
     * @return $this
     */
    public function setStreetAddressAddition($streetAddressAddition)
    {
        $this->_streetAddressAddition = $streetAddressAddition;

        return $this;
    }

    /**
     * Get city
     *
     * @return null|string
     */
    public function getCity()
    {
        return $this->_city;
    }

    /**
     * Set city
     *
     * @param null|string $city
     *
     * @return $this
     */
    public function setCity($city)
    {
        $this->_city = $city;

        return $this;
    }

    /**
     * Get state
     *
     * @return null|string
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Set state
     *
     * @param null|string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->_state = $state;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return null|string
     */
    public function getPostalCode()
    {
        return $this->_postalCode;
    }

    /**
     * Set postalCode
     *
     * @param null|string $postalCode
     *
     * @return $this
     */
    public function setPostalCode($postalCode)
    {
        $this->_postalCode = $postalCode;

        return $this;
    }

    /**
     * Get country
     *
     * @return null|string
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Set country
     *
     * @param null|string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->_country = $country;

        return $this;
    }

    /**
     * Get phone
     *
     * @return null|string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * Set phone
     *
     * @param null|string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->_phone = $phone;

        return $this;
    }

    /**
     * Converts model to array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            static::FIELD_NAME                    => $this->_name,
            static::FIELD_STREET_ADDRESS          => $this->_streetAddress,
            static::FIELD_STREET_ADDRESS_ADDITION => $this->_streetAddressAddition,
            static::FIELD_CITY                    => $this->_city,
            static::FIELD_POSTAL_CODE             => $this->_postalCode,
            static::FIELD_COUNTRY                 => $this->_country,
            static::FIELD_STATE                   => $this->_state,
            static::FIELD_PHONE                   => $this->_phone,
        );
    }
}
