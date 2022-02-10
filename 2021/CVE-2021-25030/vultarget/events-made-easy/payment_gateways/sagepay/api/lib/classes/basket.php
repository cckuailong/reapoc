<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Contains the items collection and additional details for the shopping basket.
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayBasket
{

    /**
     * The basket ID
     *
     * @var string
     */
    private $_id;

    /**
     * The basket description
     *
     * @var string
     */
    private $_description;

    /**
     * The ID of the seller if using a phone payment
     *
     * @var string
     */
    private $_agentId;

    /**
     * List of items
     *
     * @var SagepayItem[]
     */
    private $_items = array();

    /**
     * The total cost of the line including quantity and tax
     *
     * @var float
     */
    private $_deliveryNetAmount;

    /**
     * The total cost of the line including quantity and tax
     *
     * @var float
     */
    private $_deliveryTaxAmount;

    /**
     * The total cost of the line including quantity and tax
     *
     * @var float
     */
    private $_deliveryGrossAmount;

    /**
     * List of discounts
     *
     * @var array
     */
    private $_discounts = array();

    /**
     * The ship customer ID
     *
     * @var string
     */
    private $_shipId;

    /**
     * The shipping method used
     *
     * @var string
     */
    private $_shippingMethod;

    /**
     * The shipping Fax Number
     *
     * @var string
     */
    private $_shippingFaxNo;

    /**
     * Used to provide tour operator information
     *
     * @var string
     */
    private $_tourOperator;

    /**
     * Used to provide car rental information
     *
     * @var string
     */
    private $_carRental;

    /**
     * Used to provide hotel information
     *
     * @var string
     */
    private $_hotel;

    /**
     * Used to provide cruise information
     *
     * @var string
     */
    private $_cruise;

    /**
     * Used to provide airline information
     *
     * @var string
     */
    private $_airline;

    /**
     * Diners customer reference
     *
     * @var string
     */
    private $_dinerCustomerRef;

    /**
     * List of fields that should be exported to basket XML
     *
     * @var array
     */
    private $_exportFields = array(
        'items',
        'deliveryNetAmount',
        'deliveryTaxAmount',
        'deliveryGrossAmount',
        'discounts',
        'shipId',
        'shippingMethod',
        'shippingFaxNo',
        'tourOperator',
        'carRental',
        'hotel',
        'cruise',
        'airline',
        'dinerCustomerRef'
    );

    /**
     * Used into serialize
     *
     * @var array
     */
    private $_struct = array(
        'item',
        'quantity',
        'value',
        'tax',
        'itemTotal',
        'lineTotal',
    );

    /**
     * Constructor for SagepayBasket
     */
    function __construct()
    {
        $this->_id = (string) time();
    }

    /**
     * Get basket ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set basket ID
     *
     * @param string $id
     */
    public function setId($id)
    {
        if (SagepayValid::digit($id))
        {
            $this->_id = (string) $id;
        }
    }

    /**
     * Get description of goods purchased is displayed on the Sage Pay Form payment page as the customer enters their card details.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Set basket description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->_description = substr($description, 0, 100);
    }

    /**
     * Get ID of the seller if using a phone payment.
     *
     * @return string
     */
    public function getAgentId()
    {
        return $this->_agentId;
    }

    /**
     * Set ID of the seller if using a phone payment.
     *
     * @param string $agentId
     */
    public function setAgentId($agentId)
    {
        if (SagepayValid::regex($agentId, '/^[a-zA-Z0-9\ ]{1,16}$/'))
        {
            $this->_agentId = $agentId;
        }
    }

    /**
     * Get list of items
     *
     * @return SagepayItem[]
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Set the list of  items
     *
     * @param SagepayItem[] $items
     */
    public function setItems(array $items)
    {
        $this->_items = $items;
    }

    /**
     * Add the item to basket
     *
     * @param SagepayItem $item
     */
    public function addItem(SagepayItem $item)
    {
        $this->_items[] = $item;
    }

    /**
     * Get delivery net amount
     *
     * @return type
     */
    public function getDeliveryNetAmount()
    {
        return $this->_deliveryNetAmount;
    }

    /**
     * Set delivery net amount
     *
     * @param float $deliveryNetAmount
     */
    public function setDeliveryNetAmount($deliveryNetAmount)
    {
        $this->_deliveryNetAmount = $deliveryNetAmount;
    }

    /**
     * Get delivery tax
     *
     * @return float
     */
    public function getDeliveryTaxAmount()
    {
        return $this->_deliveryTaxAmount;
    }

    /**
     * Set delivery tax
     *
     * @param float $deliveryTaxAmount
     */
    public function setDeliveryTaxAmount($deliveryTaxAmount)
    {
        $this->_deliveryTaxAmount = $deliveryTaxAmount;
    }

    /**
     * Get delivery gross amount
     *
     * @return float
     */
    public function getDeliveryGrossAmount()
    {
        return $this->_deliveryNetAmount + $this->_deliveryTaxAmount;
    }

    /**
     * Get list of discounts
     *
     * @return array
     */
    public function getDiscounts()
    {
        return $this->_discounts;
    }

    /**
     * Set list of discounts
     *
     * @param array $discounts
     */
    public function setDiscounts(array $discounts)
    {
        $this->_discounts = $discounts;
    }

    /**
     * Get shipping ID
     *
     * @return string
     */
    public function getShipId()
    {
        return $this->_shipId;
    }

    /**
     * Set shipping ID
     *
     * @param string $shipId
     */
    public function setShipId($shipId)
    {
        $this->_shipId = $shipId;
    }

    /**
     * Get shipping method
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->_shippingMethod;
    }

    /**
     * Set shipping method
     *
     * @param string $shippingMethod
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->_shippingMethod = $shippingMethod;
    }

    /**
     * Get shipping fax number
     *
     * @return string
     */
    public function getShippingFaxNo()
    {
        return $this->_shippingFaxNo;
    }

    /**
     * Set shipping fax number
     *
     * @param string $shippingFaxNo
     */
    public function setShippingFaxNo($shippingFaxNo)
    {
        $this->_shippingFaxNo = $shippingFaxNo;
    }

    /**
     * Get tour operator structure
     *
     * @return array
     */
    public function getTourOperator()
    {
        return $this->_tourOperator;
    }

    /**
     * Set tour operator structure
     *
     * @param array $tourOperator
     */
    public function setTourOperator(array $tourOperator)
    {
        $this->_tourOperator = $tourOperator;
    }

    /**
     * Get car rental structure
     *
     * @return array
     */
    public function getCarRental()
    {
        return $this->_carRental;
    }

    /**
     * Set car rental structure
     *
     * @param array $carRental
     */
    public function setCarRental(array $carRental)
    {
        $this->_carRental = $carRental;
    }

    /**
     * Get hotel structure
     *
     * @return array
     */
    public function getHotel()
    {
        return $this->_hotel;
    }

    /**
     * Set hotel structure
     *
     * @param array $hotel
     */
    public function setHotel(array $hotel)
    {
        $this->_hotel = $hotel;
    }

    /**
     * Get cruise structure
     *
     * @return array
     */
    public function getCruise()
    {
        return $this->_cruise;
    }

    /**
     * Set cruise structure
     *
     * @param array $cruise
     */
    public function setCruise(array $cruise)
    {
        $this->_cruise = $cruise;
    }

    /**
     * Get airline structure
     *
     * @return array
     */
    public function getAirline()
    {
        return $this->_airline;
    }

    /**
     * Set airline structure
     *
     * @param array $airline
     */
    public function setAirline(array $airline)
    {
        $this->_airline = $airline;
    }

    /**
     * get dinerCustomerRef
     *
     * @return string
     */
    public function getDinerCustomerRef()
    {
        return $this->_dinerCustomerRef;
    }

    /**
     * Set dinerCustomerRef
     *
     * @param string $dinerCustomerRef
     */
    public function setDinerCustomerRef($dinerCustomerRef)
    {
        $this->_dinerCustomerRef = $dinerCustomerRef;
    }

    /**
     * Get the total amount of basket
     *
     * @return float
     */
    public function getAmount()
    {
        $amount = $this->getDeliveryGrossAmount();
        foreach ($this->_items as $item)
        {
            $amount += $item->getTotalGrossAmount();
        }
        return $amount;
    }

    /**
     * Return xml structured or serialized string depends on $asXml
     *
     * @param  bool  $asXml
     *
     * @return string
     */
    public function exportAsXml($asXml = true)
    {
        if ($asXml)
        {
            return $this->_toXml();
        }
        return $this->_serialize();
    }

    /**
     * Export as string with Sagepay specific format
     *
     * @return type
     */
    private function _serialize()
    {
        $values = array(count($this->_items));
        foreach ($this->_items as $item)
        {
            $itemArr = $item->asArray();
            foreach ($this->_struct as $key)
            {
                $values[] = is_null($itemArr[$key]) ? '---' : $itemArr[$key];
            }
        }
        if ($this->getDeliveryGrossAmount() > 0)
        {
            $values[0]++;
            $values[] = 'Delivery';
            $values[] = 1;
            $values[] = number_format($this->getDeliveryNetAmount(), 2);
            $values[] = number_format($this->getDeliveryTaxAmount(), 2);
            $values[] = number_format($this->getDeliveryGrossAmount(), 2);
            $values[] = number_format($this->getDeliveryGrossAmount(), 2);
        }

        return implode(':', $values);
    }

    /**
     * Export Basket as XML
     *
     * @return string
     */
    private function _toXml()
    {
        $dom = new DOMDocument();
        $dom->formatOutput = false;
        $dom->loadXML('<basket></basket>');
        foreach ($this->_exportFields as $name)
        {
            $value = NULL;
            $getter = "get" . ucfirst($name);
            if (method_exists($this, $getter))
            {
                $value = $this->$getter();
            }

            if (empty($value))
            {
                continue;
            }

            $node = $this->_createDomNode($dom, $value, $name);
            if ($node instanceof DOMNode)
            {
                $dom->documentElement->appendChild($node);
            }
            else if ($node instanceof DOMNodeList)
            {
                for ($i = 0, $n = $node->length; $i < $n; $i++)
                {
                    $child = $node->item(0);
                    if ($child instanceof DOMNode)
                    {
                        $dom->documentElement->appendChild($child);
                    }
                }
            }
        }
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Create a DOMNode from property
     *
     * @param DOMDocument $dom
     * @param string $name
     * @param mixed $value
     * @return DOMNode|DOMNodeList
     */
    private function _createDomNode($dom, $value, $name = null)
    {
        if ($value instanceof SagepayItem)
        {
            return $value->asDomElement($dom);
        }
        else if ($name === null)
        {
            return $dom->createElement($value);
        }
        else if (is_string($value) || is_int($value))
        {
            return $dom->createElement($name, trim($value));
        }
        else if (is_float($value))
        {
            return $dom->createElement($name, number_format($value, 2, '.', ''));
        }
        else if (is_array($value))
        {
            if (count($value) === 0)
            {
                return null;
            }
            $base = $dom->createElement($name);
            if (array_keys($value) !== range(0, count($value) - 1))
            {
                // For Associative Array
                foreach ($value as $_key => $_val)
                {
                    $node = $this->_createDomNode($dom, $_val, $_key);
                    $base->appendChild($node);
                }
                return $base;
            }
            else
            {
                foreach ($value as $_val)
                {
                    $node = $this->_createDomNode($dom, $_val);
                    $base->appendChild($node);
                }
                return $base->childNodes;
            }
        }
    }

}
