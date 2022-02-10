<?php
/**
 * Refund class file
 */
namespace MercadoPago\AdvancedPayments;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;
use MercadoPago\Entity;

/**
 * Refund class
 * @RestMethod(resource="/v1/advanced_payments/:advanced_payment_id/refunds", method="create")
 * @RestMethod(resource="/v1/advanced_payments/:advanced_payment_id/refunds/:refund_id", method="read")
 */
class Refund extends Entity {

    /**
     * id
     * @Attribute()
     * @var string
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
