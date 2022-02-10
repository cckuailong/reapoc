<?php
/**
 * Track class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\Attribute;

/**
 * Track class
 */
class Track extends Entity
{
    /**
     * type
     * @Attribute(type = "string")
     * @var string
     */
    protected $type;

    /**
     * value
     * @Attribute(type = "object")
     * @var object
     */
    protected $value;
}