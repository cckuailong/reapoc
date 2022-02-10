<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Details of the shopping basket item
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayItem
{

    /**
     * The item description
     *
     * @var string
     */
    private $_description = '';

    /**
     * The unique product identifier code
     *
     * @var string
     */
    private $_productSku;

    /**
     * Item product code
     *
     * @var string
     */
    private $_productCode;

    /**
     * Quantity of the item ordered
     *
     * @var integer
     */
    private $_quantity = 0;

    /**
     * The cost of the item before tax
     *
     * @var float
     */
    private $_unitNetAmount = 0.0;

    /**
     * The amount of tax on the item
     *
     * @var float
     */
    private $_unitTaxAmount = 0.0;

    /**
     * The total cost of the item with tax
     *
     * @var float
     */
    private $_unitGrossAmount;

    /**
     * The total cost of the line including quantity and tax
     *
     * @var float
     */
    private $_totalGrossAmount;

    /**
     * The first name of the recipient of this item
     *
     * @var string
     */
    private $_recipientFName;

    /**
     * The last name of the recipient of this item
     *
     * @var string
     */
    private $_recipientLName;

    /**
     * The middle initial of the recipient of this item
     *
     * @var string
     */
    private $_recipientMName;

    /**
     * The salutation of the recipient of this item
     *
     * @var string
     */
    private $_recipientSal;

    /**
     * The email of the recipient of this item
     *
     * @var string
     */
    private $_recipientEmail;

    /**
     * The phone number of the recipient of this item
     *
     * @var string
     */
    private $_recipientPhone;

    /**
     * The first address line of the recipient of this item
     *
     * @var string
     */
    private $_recipientAdd1;

    /**
     * The second address line of the recipient of this item
     *
     * @var string
     */
    private $_recipientAdd2;

    /**
     * The city of the recipient of this item
     *
     * @var string
     */
    private $_recipientCity;

    /**
     * If in the US, the 2 letter code for the state of the recipient of this item
     *
     * @var string
     */
    private $_recipientState;

    /**
     * The 2 letter country code (ISO 3166) of the recipient of this item
     *
     * @var string
     */
    private $_recipientCountry;

    /**
     * The postcode of the recipient of this item
     *
     * @var string
     */
    private $_recipientPostCode;

    /**
     * The shipping item number
     *
     * @var string
     */
    private $_itemShipNo;

    /**
     * Gift message associated with this item
     *
     * @var string
     */
    private $_itemGiftMsg;

    /**
     * Get description
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
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * Get unique product identifier code
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->_productSku;
    }

    /**
     * Set unique product identifier code
     *
     * @param string $productSku
     */
    public function setProductSku($productSku)
    {
        $this->_productSku = $productSku;
    }

    /**
     * Get product code
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->_productCode;
    }

    /**
     * Set product code
     *
     * @param string $productCode
     */
    public function setProductCode($productCode)
    {
        $this->_productCode = $productCode;
    }

    /**
     * Get quantity of the item ordered
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * Set quantity of the item ordered
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->_quantity = intval($quantity);
    }

    /**
     * Get cost of the item before tax
     *
     * @return float
     */
    public function getUnitNetAmount()
    {
        return $this->_unitNetAmount;
    }

    /**
     * Set cost of the item before tax
     *
     * @param float $unitNetAmount
     */
    public function setUnitNetAmount($unitNetAmount)
    {
        $this->_unitNetAmount = floatval($unitNetAmount);
    }

    /**
     * Get amount of tax on the item
     *
     * @return float
     */
    public function getUnitTaxAmount()
    {
        return $this->_unitTaxAmount;
    }

    /**
     * Set amount of tax on the item
     *
     * @param float $unitTaxAmount
     */
    public function setUnitTaxAmount($unitTaxAmount)
    {
        $this->_unitTaxAmount = floatval($unitTaxAmount);
    }

    /**
     * Get total cost of the item with tax
     *
     * @return float
     */
    public function getUnitGrossAmount()
    {
        return $this->_unitNetAmount + $this->_unitTaxAmount;
    }

    /**
     * Get total cost of the line including quantity and tax
     *
     * @return float
     */
    public function getTotalGrossAmount()
    {
        return $this->getUnitGrossAmount() * $this->getQuantity();
    }

    /**
     * Get first name of the recipient of this item
     *
     * @return string
     */
    public function getRecipientFName()
    {
        return $this->_recipientFName;
    }

    /**
     * Set first name of the recipient of this item
     *
     * @param string $recipientFName
     */
    public function setRecipientFName($recipientFName)
    {
        $this->_recipientFName = $recipientFName;
    }

    /**
     * Get last name of the recipient of this item
     *
     * @return string
     */
    public function getRecipientLName()
    {
        return $this->_recipientLName;
    }

    /**
     * Set last name of the recipient of this item
     *
     * @param string $recipientLName
     */
    public function setRecipientLName($recipientLName)
    {
        $this->_recipientLName = $recipientLName;
    }

    /**
     * Get middle initial of the recipient of this item
     *
     * @return string
     */
    public function getRecipientMName()
    {
        return $this->_recipientMName;
    }

    /**
     * Set middle initial of the recipient of this item
     *
     * @param string $recipientMName
     */
    public function setRecipientMName($recipientMName)
    {
        $this->_recipientMName = $recipientMName;
    }

    /**
     * Get salutation of the recipient of this item
     *
     * @return string
     */
    public function getRecipientSal()
    {
        return $this->_recipientSal;
    }

    /**
     * Set salutation of the recipient of this item
     *
     * @param string $recipientSal
     */
    public function setRecipientSal($recipientSal)
    {
        $this->_recipientSal = $recipientSal;
    }

    /**
     * Get email of the recipient of this item
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->_recipientEmail;
    }

    /**
     * Set email of the recipient of this item
     *
     * @param string $recipientEmail
     */
    public function setRecipientEmail($recipientEmail)
    {
        $this->_recipientEmail = $recipientEmail;
    }

    /**
     * Get phone number of the recipient of this item
     *
     * @return string
     */
    public function getRecipientPhone()
    {
        return $this->_recipientPhone;
    }

    /**
     * Set phone number of the recipient of this item
     *
     * @param string $recipientPhone
     */
    public function setRecipientPhone($recipientPhone)
    {
        $this->_recipientPhone = $recipientPhone;
    }

    /**
     * Get first address line of the recipient of this item
     *
     * @return string
     */
    public function getRecipientAdd1()
    {
        return $this->_recipientAdd1;
    }

    /**
     * Set first address line of the recipient of this item
     *
     * @param string $recipientAdd1
     */
    public function setRecipientAdd1($recipientAdd1)
    {
        $this->_recipientAdd1 = $recipientAdd1;
    }

    /**
     * Get second address line of the recipient of this item
     *
     * @return string
     */
    public function getRecipientAdd2()
    {
        return $this->_recipientAdd2;
    }

    /**
     * Set second address line of the recipient of this item
     *
     * @param string $recipientAdd2
     */
    public function setRecipientAdd2($recipientAdd2)
    {
        $this->_recipientAdd2 = $recipientAdd2;
    }

    /**
     * Get city of the recipient of this item
     *
     * @return string
     */
    public function getRecipientCity()
    {
        return $this->_recipientCity;
    }

    /**
     * Set city of the recipient of this item
     *
     * @param string $recipientCity
     */
    public function setRecipientCity($recipientCity)
    {
        $this->_recipientCity = $recipientCity;
    }

    /**
     * Get code for the state of the recipient of this item
     *
     * @return string
     */
    public function getRecipientState()
    {
        return $this->_recipientState;
    }

    /**
     * Set code for the state of the recipient of this item
     *
     * @param string $recipientState
     */
    public function setRecipientState($recipientState)
    {
        $this->_recipientState = $recipientState;
    }

    /**
     * Get country code of the recipient of this item
     *
     * @return string
     */
    public function getRecipientCountry()
    {
        return $this->_recipientCountry;
    }

    /**
     * Set country code of the recipient of this item
     *
     * @param string $recipientCountry
     */
    public function setRecipientCountry($recipientCountry)
    {
        $this->_recipientCountry = $recipientCountry;
    }

    /**
     * Get postcode of the recipient of this item
     *
     * @return string
     */
    public function getRecipientPostCode()
    {
        return $this->_recipientPostCode;
    }

    /**
     * Set postcode of the recipient of this item
     *
     * @param string $recipientPostCode
     */
    public function setRecipientPostCode($recipientPostCode)
    {
        $this->_recipientPostCode = $recipientPostCode;
    }

    /**
     * Get shipping item number
     *
     * @return string
     */
    public function getItemShipNo()
    {
        return $this->_itemShipNo;
    }

    /**
     * Set shipping item number
     *
     * @param string $itemShipNo
     */
    public function setItemShipNo($itemShipNo)
    {
        $this->_itemShipNo = $itemShipNo;
    }

    /**
     * Get gift message associated with this item
     *
     * @return string
     */
    public function getItemGiftMsg()
    {
        return $this->_itemGiftMsg;
    }

    /**
     * Set gift message associated with this item
     *
     * @param string $itemGiftMsg
     */
    public function setItemGiftMsg($itemGiftMsg)
    {
        $this->_itemGiftMsg = $itemGiftMsg;
    }

    /**
     * Create a DOMNode from property
     *
     * @param DOMDocument $basket
     *
     * @return DOMNode
     */
    public function asDomElement(DOMDocument $basket)
    {
        $item = $basket->createElement('item');
        $props = get_class_vars('SagepayItem');
        foreach ($props as $name => $value)
        {
            $name = substr($name, 1);
            if (substr($name, 0, 9) === 'recipient')
            {
                continue;
            }
            $getter = "get" . strtoupper($name);
            $value = $this->$getter();

            $node = null;
            if (is_string($value) || is_int($value))
            {
                $node = $basket->createElement($name, trim($value));
            }
            else if (is_float($value))
            {
                $node = $basket->createElement($name, number_format($value, 2));
            }
            if ($node !== null)
            {
                $item->appendChild($node);
            }
        }
        return $item;
    }

    /**
     * Return a array of the item properties
     *
     * @return array
     */
    public function asArray()
    {
        return array(
            'item' => $this->getDescription(),
            'quantity' => $this->getQuantity(),
            'value' => $this->getUnitNetAmount(),
            'tax' => $this->getUnitTaxAmount(),
            'itemTotal' => $this->getUnitGrossAmount(),
            'lineTotal' => $this->getTotalGrossAmount()
        );
    }

}
