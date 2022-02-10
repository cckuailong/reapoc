<?php
/**
 * Documentation class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\Attribute;
use MercadoPago\Annotation\DenyDynamicAttribute;

/**
 * Documentation class
 */
class Documentation extends Entity
{
    /**
     * type
     * @Attribute(type = "string", readOnly = true)
     * @var string
    */
    protected $type;

    /**
     * url
     * @Attribute(type = "string", readOnly = true)
     * @var string
    */
    protected $url;

    /**
     * description
    * @Attribute(type = "string", readOnly = true)
     * @var string
    */
    protected $description;

}
