<?php
/**
 * Tax class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\Attribute;

/**
 * Tax class
 */
class Tax extends Entity
{
    /**
     * type
     * @Attribute(type = "string")
     * @var string
     */
    protected $type;

    /**
     * value
     * @Attribute(type = "float")
     * @var float
     */
    protected $value;
}
