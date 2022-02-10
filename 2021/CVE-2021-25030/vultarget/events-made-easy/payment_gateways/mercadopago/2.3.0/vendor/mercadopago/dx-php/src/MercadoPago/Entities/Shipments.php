<?php
/**
 * Shipments class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;
use phpDocumentor\Descriptor\Type\FloatDescriptor;

/**
 * Shipments class
 * @RestMethod(resource="/v1/payments/:payment_id/refunds", method="create")
 * @RestMethod(resource="/v1/payments/:payment_id/refunds/:id", method="read")
 * @deprecated This class is deprecated
 */
class Shipments extends Entity {

    /**
     * mode
     * @Attribute()
     * @var string
     */
    protected $mode;

    /**
     * local_pickup
     * @Attribute()
     * @var boolean
     */
    protected $local_pickup;

    /**
     *free_methods
     * @Attribute()
     * @var array
     */
    protected $free_methods;

    /**
     * cost
     * @Attribute()
     * @var Float
     */
    protected $cost;

    /**
     * free_shipping
     * @Attribute()
     * @var boolean
     */
    protected $free_shipping;

    /**
     * receiver_address
     * @Attribute()
     * @var object
     */
    protected $receiver_address;

    /**
     * dimensions
     * @Attribute()
     * @var object
     */
    protected $dimensions;

    /**
     * default_shipping_method
     * @Attribute()
     * @var string
     */
    protected $default_shipping_method;

}
