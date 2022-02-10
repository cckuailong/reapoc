<?php
namespace MercadoPago;
use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute; 


/**
 * POS class
 * @link https://www.mercadopago.com/developers/en/reference/pos/_pos/post/ Click here for more infos
 * 
 * @RestMethod(resource="/pos/:id", method="read")
 * @RestMethod(resource="/pos", method="create")
 * @RestMethod(resource="/pos/:id", method="update")
 * @RestMethod(resource="/pos/:id", method="delete")
 * @RestMethod(resource="/pos", method="search")
 */
class POS extends Entity
{
    /**
     * id
     * @Attribute()
     * @var string
     */
    protected $id;
    
    /**
     * name
     * @Attribute()
     * @var string
     */
    protected $name;

    /**
     * fixed_amount
     * @Attribute()
     * @var float
     */
    protected $fixed_amount;

    /**
     * category
     * @Attribute()
     * @var int
     */
    protected $category;

    /**
     * store_id
     * @Attribute()
     * @var string
     */
    protected $store_id;

    /**
     * external_reference
     * @Attribute()
     * @var string
     */
    protected $external_id;

    /**
     * qr
     * @Attribute()
     * @var object
     */
    protected $qr;

    /**
     * status
     * @Attribute()
     * @var string
     */
    protected $status;

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
     * uuid
     * @Attribute()
     * @var string
     */
    protected $uuid;

    /**
     * compatible_id
     * @Attribute()
     * @var string
     */
    protected $compatible_id;

    /**
     * user_id
     * @Attribute()
     * @var int
     */
    protected $user_id;

}
