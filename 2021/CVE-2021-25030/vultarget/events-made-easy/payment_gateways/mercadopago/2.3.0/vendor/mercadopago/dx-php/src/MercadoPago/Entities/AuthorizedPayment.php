<?php
/**
 * Authorized Payments class file
 */
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Authorized Payments Class
 *
 * @RestMethod(resource="/authorized_payment", method="create")
 * @RestMethod(resource="/authorized_payment/:id", method="read")
 * @RestMethod(resource="/authorized_payment/search", method="search")
 * @RestMethod(resource="/authorized_payment/:id", method="update")
 */

class AuthorizedPayment extends Entity
{
    /**
     * id
     * @Attribute(primaryKey = true)
     * @var int
     */
    protected $id;

    /**
     * preapprova_id
     * @Attribute(type = "string")
     * @var int
     */
    protected $preapproval_id;

    /**
     * type
     * @Attribute(type = "string")
     * @var string
     */
    protected $type;

    /**
     * status
     * @Attribute(type = "string")
     * @var string
     */
    protected $status;

    /**
     * date_created
     * @Attribute(type = "date")
     * @var string
     */
    protected $date_created;

    /**
     * last_modified
     * @Attribute(type = "date")
     * @var string
     */
    protected $last_modified;

    /**
     * transaction_amount
     * @Attribute(type = "float")
     * @var float
     */
    protected $transaction_amount;

    /**
     * currency_id
     * @Attribute(type = "string")
     * @var string
     */
    protected $currency_id;

    /**
     * reason
     * @Attribute(type = "string")
     * @var string
     */
    protected $reason;

     /**
      * external_reference
     * @Attribute(type = "string")
     * @var string
     */
    protected $external_reference;

     /**
      * payment
     * @Attribute(type = "object")
     * @var object
     */
    protected $payment;

    /**
     * rejection_code
     * @Attribute(type = "string")
     * @var string
     */
    protected $rejection_code;

    /**
     * retry_attempt
     * @Attribute(type = "string")
     * @var string
     */
    protected $retry_attempt;

    /**
     * next_retry_date
     * @Attribute(type = "date")
     * @var string
     */
    protected $next_retry_date;

    /**
     * last_retry_date
     * @Attribute(type = "date")
     * @var string
     */
    protected $last_retry_date;

    /**
     * expire_date
     * @Attribute(type = "date")
     * @var string
     */
    protected $expire_date;

    /**
     * debit_date
     * @Attribute(type = "date")
     * @var string
     */
    protected $debit_date;

    /**
     * coupon_code
     * @Attribute(type = "string")
     * @var string
     */
    protected $coupon_code;


}
