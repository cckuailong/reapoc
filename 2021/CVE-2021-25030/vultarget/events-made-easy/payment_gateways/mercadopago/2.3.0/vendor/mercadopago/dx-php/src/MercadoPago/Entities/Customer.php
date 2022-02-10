<?php
namespace MercadoPago;

use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * This class allows you to store customers data safely to improve the shopping experience.
 *  
 * This will allow your customer to complete their purchases much faster and easily when used in conjunction with the Cards class.
 *
 * @link https://mercadopago.com/developers/en/guides/online-payments/web-tokenize-checkout/customers-and-cards Click here for more infos
 * 
 * @RestMethod(resource="/v1/customers/:id", method="read")
 * @RestMethod(resource="/v1/customers/search", method="search")
 * @RestMethod(resource="/v1/customers/", method="create")
 * @RestMethod(resource="/v1/customers/:id", method="update")
 * @RestMethod(resource="/v1/customers/:id", method="delete")
 */

class Customer extends Entity
{
    /**
     * email
     * @Attribute(primaryKey = true)
     * @var string
     */
    protected $email;

    /**
     * id
     * @Attribute()
     * @var string
     */
    protected $id;

    /**
     * first_name
     * @Attribute()
     * @var string
     */
    protected $first_name;

    /**
     * last_name
     * @Attribute()
     * @var string
     */
    protected $last_name;

    /**
     * phone
     * @Attribute()
     * @var object
     */
    protected $phone;

    /**
     * identification
     * @Attribute()
     * @var object
     */
    protected $identification;

    /**
     * default_address
     * @Attribute()
     * @var string
     */
    protected $default_address;

    /**
     * address
     * @Attribute()
     * @var object
     */
    protected $address;

    /**
     * date_registered
     * @Attribute()
     * @var string
     */
    protected $date_registered;

    /**
     * description
     * @Attribute()
     * @var string
     */
    protected $description;

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

    /**
     * metadata
     * @Attribute()
     * @var object
     */
    protected $metadata;

    /**
     * default_card
     * @Attribute()
     * @var string
     */
    protected $default_card;

    /**
     * card
     * @Attribute()
     * @var array
     */
    protected $cards;

    /**
     * addresses
     * @Attribute()
     * @var array
     */
    protected $addresses;

    /**
     * live_mode
     * @Attribute(type = "boolean")
     * @var boolean
     */
    protected $live_mode;

}
