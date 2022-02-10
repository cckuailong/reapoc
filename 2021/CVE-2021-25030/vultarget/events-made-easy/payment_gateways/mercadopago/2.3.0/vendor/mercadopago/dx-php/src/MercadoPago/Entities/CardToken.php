<?php
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * Card Token class
 * This class will allow you to send your customers card data for Mercado Pago server and receive a token to complete the payments transactions.
 * 
 * @RestMethod(resource="/v1/card_tokens?public_key=:public_key", method="create")
 * @RestMethod(resource="/v1/card_tokens?public_key=:public_key", method="read")
 * @RestMethod(resource="/v1/card_tokens?public_key=:public_key", method="update")
 */

class CardToken extends Entity
{
    /**
     * card_id
     * @Attribute(primaryKey = true)
     * @var int
     */
    protected $card_id;

    /**
     * public_key
     * @Attribute()
     * @var string
     */
    protected $public_key;

    /**
     * first_six_digits
     * @Attribute()
     * @var string
     */
    protected $first_six_digits;

    /**
     * luhn_validation
     * @Attribute()
     * @var string
     */
    protected $luhn_validation;

    /**
     * date_used
     * @Attribute()
     * @var string
     */
    protected $date_used;

    /**
     * status
     * @Attribute()
     * @var string
     */
    protected $status;

    /**
     * date_due
     * @Attribute()
     * @var string
     */
    protected $date_due;

    /**
     * card_number_length
     * @Attribute()
     * @var int
     */
    protected $card_number_length;

    /**
     * id
     * @Attribute()
     * @var int
     */
    protected $id;

    /**
     * security_code_length
     * @Attribute()
     * @var int
     */
    protected $security_code_length;

    /**
     * expiration_year
     * @Attribute()
     * @var int
     */
    protected $expiration_year;

    /**
     * expiration_month
     * @Attribute()
     * @var int
     */
    protected $expiration_month;

    /**
     * date_last_updated
     * @Attribute()
     * @var string
     */
    protected $date_last_updated;

    /**
     * last_four_digits
     * @Attribute()
     * @var string
     */
    protected $last_four_digits;

    /**
     * cardholder
     * @Attribute()
     * @var string
     */
    protected $cardholder;

    /**
     * date_created
     * @Attribute()
     * @var string
     */
    protected $date_created;

}
