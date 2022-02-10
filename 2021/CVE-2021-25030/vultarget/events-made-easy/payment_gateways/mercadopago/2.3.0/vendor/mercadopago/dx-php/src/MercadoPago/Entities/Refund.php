<?php
/**
 * Refund class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * refund class
 * @RestMethod(resource="/v1/payments/:payment_id/refunds", method="create")
 * @RestMethod(resource="/v1/payments/:payment_id/refunds/:id", method="read")
 */
class Refund extends Entity {

    /**
     * id
     * @Attribute()
     * @var int
     */
    protected $id;

    /**
     * payment_id
     * @Attribute(serialize=false)
     * @var int
     */
    protected $payment_id;

    /**
     * amount
     * @Attribute()
     * @var float
     */
    protected $amount;

    /**
     * metadata
     * @Attribute()
     * @var object
     */
    protected $metadata;

    /**
     * source
     * @Attribute()
     * @var object
     */
    protected $source;

    /**
     * date_created
     * @Attribute(readOnly=true)
     * @var \DateTime
     */
    protected $date_created;

}
