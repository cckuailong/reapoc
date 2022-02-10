<?php
/**
 * Chargeback class file
 * @link https://www.mercadopago.com/developers/en/reference/chargebacks/_chargebacks_id/get/ Click here for more infos
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;


/**
 * Chargeback class
 * @RestMethod(resource="/v1/chargebacks/:id", method="read")
 */
class Chargeback extends Entity
{
    /**
     * id
     * @Attribute(primaryKey = true, type = "string", readOnly = true)
     * @var string
     */
    protected $id;

    /**
     * payments
     * @Attribute(type = "array", readOnly = true)
     * @var array
     */
    protected $payments;

    /**
     * amount
     * @Attribute(type = "string", readOnly = true)
     * @var string
     */
    protected $amount;

    /**
     * coverage_applied
     * @Attribute(type = "float", readOnly = true)
     * @var float
     */
    protected $coverage_applied;

    /**
     * coverage_elegible
     * @Attribute(readOnly = true)
     * @var float
     */
    protected $coverage_elegible;

    /**
     * documentation_required
     * @Attribute(readOnly = true)
     * @var mixed
     */
    protected $documentation_required;

    /**
     * documentation_status
     * @Attribute(readOnly = true)
     * @var mixed
     */
    protected $documentation_status;

    /**
     * documentation
     * @Attribute(type = "string", readOnly = true)
     * @var string
     */
    protected $documentation;

    /**
     * date_documentation_deadline
     * @Attribute(type = "array", readOnly = true)
     * @var array
     */
    protected $date_documentation_deadline;

    /**
     * date_created
     * @Attribute(type = "date", readOnly = true)
     * @var \DateTime
     */
    protected $date_created;

    /**
     * date_last_updated
     * @Attribute(type = "date", readOnly = true)
     * @var \DateTime
     */
    protected $date_last_updated;

    /**
     * live_mode
     * @Attribute(type = "boolean", readOnly = true)
     * @var boolean
     */
    protected $live_mode;

}
