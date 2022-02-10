<?php
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * The cards class is the way to store card data of your customers safely to improve the shopping experience.
 *
 * This will allow your customers to complete their purchases much faster and easily, since they will not have to complete their card data again.
 *  
 * This class must be used in conjunction with the Customer class.
 *
 * @link https://www.mercadopago.com/developers/en/guides/online-payments/web-tokenize-checkout/customers-and-cards Click here for more infos
 * 
 * @RestMethod(resource="/v1/customers/:customer_id/cards", method="create")
 * @RestMethod(resource="/v1/customers/:customer_id/cards/:id", method="read")
 * @RestMethod(resource="/v1/customers/:customer_id/cards/:id", method="update")
 * @RestMethod(resource="/v1/customers/:customer_id/cards/:id", method="delete")
 */

class Card extends Entity
{
    /**
     * id
     * @Attribute(primaryKey = true)
     * @var int
     */
    protected $id;

    /**
     * customer_id
     * @Attribute(required = true)
     * @var string
     */
    protected $customer_id;

    /**
     * expiration_month
     * @Attribute()
     * @var int
     */
    protected $expiration_month;

    /**
     * expiration_year
     * @Attribute()
     * @var int
     */
    protected $expiration_year;

    /**
     * first_six_digits
     * @Attribute()
     * @var string
     */
    protected $first_six_digits;

    /**
     * last_four_digits
     * @Attribute()
     * @var string
     */
    protected $last_four_digits;

    /**
     * payment_method
     * @Attribute()
     * @var object
     */
    protected $payment_method;

    /**
     * security_code
     * @Attribute()
     * @var object
     */
    protected $security_code;

    /**
     * issuer
     * @Attribute()
     * @var object
     */
    protected $issuer;

    /**
     * cardholder
     * @Attribute()
     * @var object
     */
    protected $cardholder;

    /**
     * date_created
     * @Attribute()
     * @var string
     */
    protected $date_created;

    /**
     * date_last_updated
     * @Attribute()
     * @var string
     */
    protected $date_last_updated;


}