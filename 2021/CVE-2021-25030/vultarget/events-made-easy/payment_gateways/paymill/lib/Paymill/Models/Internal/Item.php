<?php

namespace Paymill\Models\Internal;

class Item
{
    const FIELD_NAME = 'name';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_ITEM_NUMBER = 'item_number';
    const FIELD_URL = 'url';
    const FIELD_AMOUNT = 'amount';
    const FIELD_QUANTITY = 'quantity';

    /**
     * @var string
     */
    protected $_name;

    /**
     * @var string
     */
    protected $_description;

    /**
     * @var string
     */
    protected $_itemNumber;

    /**
     * @var string
     */
    protected $_url;

    /**
     * @var string
     */
    protected $_amount;

    /**
     * @var int
     */
    protected $_quantity;

    /**
     * Get _name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get _description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->_description = $description;

        return $this;
    }

    /**
     * Get _itemNumber
     *
     * @return string
     */
    public function getItemNumber()
    {
        return $this->_itemNumber;
    }

    /**
     * Set itemNumber
     *
     * @param string $itemNumber
     *
     * @return $this
     */
    public function setItemNumber($itemNumber)
    {
        $this->_itemNumber = $itemNumber;

        return $this;
    }

    /**
     * Get _url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->_url = $url;

        return $this;
    }

    /**
     * Get _amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;

        return $this;
    }

    /**
     * Get _quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * Set quantity
     *
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = $quantity;

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
            static::FIELD_NAME => $this->_name,
            static::FIELD_DESCRIPTION => $this->_description,
            static::FIELD_ITEM_NUMBER => $this->_itemNumber,
            static::FIELD_AMOUNT => $this->_amount,
            static::FIELD_QUANTITY => $this->_quantity,
            static::FIELD_URL => $this->_url,
        );
    }
}
