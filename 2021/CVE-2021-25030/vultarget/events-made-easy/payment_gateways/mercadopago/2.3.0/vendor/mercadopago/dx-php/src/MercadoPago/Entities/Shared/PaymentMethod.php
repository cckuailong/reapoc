<?php
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Payment Method class
 * @link https://www.mercadopago.com/developers/en/reference/payment_methods/_payment_methods/get/ Click here for more infos
 * 
 * @RestMethod(resource="/v1/payment_methods", method="list")
 */

class PaymentMethod extends Entity
{
    /**
     * id
     * @Attribute(primaryKey = true)
     * @var string
     */
    protected $id;

    /**
     * name
     * @Attribute(type = "string")
     * @var string
     */
    protected $name;

    /**
     * payment_type_id
     * @Attribute(type = "string")
     * @var string
     */
    protected $payment_type_id;

    /**
     * status
     * @Attribute(type = "string")
     * @var string
     */
    protected $status;

    /**
     * secure_thumbnail
     * @Attribute(type = "string")
     * @var string
     */
    protected $secure_thumbnail;

    /**
     * thumbnail
     * @Attribute(type = "string")
     * @var string
     */
    protected $thumbnail;

    /**
     * deferred_capture
     * @Attribute(type = "string")
     * @var string
     */
    protected $deferred_capture;

    /**
     * settings
     * @Attribute()
     * @var object
     */
    protected $settings;

    /**
     * additional_info_needed
     * @Attribute()
     * @var string
     */
    protected $additional_info_needed;

    /**
     * min_allowed_amount
     * @Attribute(type = "float")
     * @var float
     */
    protected $min_allowed_amount;

    /**
     * max_allowed_amount
     * @Attribute(type = "float")
     * @var float
     */
    protected $max_allowed_amount;

    /**
     * accreditation_time
     * @Attribute(type = "integer")
     * @var int
     */
    protected $accreditation_time;

    /**
     * financial_institutions
     * @Attribute(type = "")
     * @var object
     */
    protected $financial_institutions;

    /**
     * processing_modes
     * @Attribute(type = "")
     * @var array
     */
    protected $processing_modes;
}