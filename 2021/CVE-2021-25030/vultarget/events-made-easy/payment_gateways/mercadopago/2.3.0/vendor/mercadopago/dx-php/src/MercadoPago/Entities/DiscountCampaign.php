<?php
/**
 * Discount Campaign class file
 */
namespace MercadoPago;

use http\Params;
use MercadoPago\Annotation\RestMethod;
use MercadoPago\Annotation\RequestParam;
use MercadoPago\Annotation\Attribute;

/**
 * discount Campaign class
 * @RestMethod(resource="/v1/discount_campaigns", method="read")
 */


class DiscountCampaign extends Entity
{
    /**
     * id
     * @Attribute(primaryKey = true)
     * @var int
     */
    protected $id;

    /**
     * name
     * @Attribute()
     * @var string
     */
    protected $name;

    /**
     * percent_off
     * @Attribute()
     * @var float
     */
    protected $percent_off;

    /**
     * amount_off
     * @Attribute()
     * @var float
     */
    protected $amount_off;

    /**
     * coupon_amount
     * @Attribute()
     * @var float
     */
    protected $coupon_amount;

    /**
     * currency_id
     * @Attribute()
     * @var string
     */
    protected $currency_id;


    /**
     * read
     * @param array $options
     * @param array $params
     * @return mixed|null
     * @throws \Exception
     */
    public static function read($options = [], $params = []){
        return parent::read([], $options);
    }
}
