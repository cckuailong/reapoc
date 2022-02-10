<?php
namespace MercadoPago;
use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute; 


/**
 * Instore Order class
 * @link https://www.mercadopago.com/developers/en/reference/instore_orders/_mpmobile_instore_qr_user_id_external_id/post/ Click here for more infos
 * 
 * @RestMethod(resource="/mpmobile/instore/qr/:user_id/:external_id", method="create")
 */
class InstoreOrder extends Entity
{
    /**
     * id
     * @Attribute()
     * @var int
     */
    protected $id;

    /**
     * external_reference
     * @Attribute()
     * @var string
     */
    protected $external_reference;

    /**
     * notification_url
     * @Attribute()
     * @var string
     */
    protected $notification_url;

    /**
     * items
     * @Attribute()
     * @var array
     */
    protected $items;

}
