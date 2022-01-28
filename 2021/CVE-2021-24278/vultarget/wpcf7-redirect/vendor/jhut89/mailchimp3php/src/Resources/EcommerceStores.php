<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\EcommerceStores\Carts;
use MailchimpAPI\Resources\EcommerceStores\Customers;
use MailchimpAPI\Resources\EcommerceStores\Orders;
use MailchimpAPI\Resources\EcommerceStores\Products;
use MailchimpAPI\Resources\EcommerceStores\PromoRules;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class EcommerceStores
 * @package MailchimpAPI\Resources
 */
class EcommerceStores extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/ecommerce/stores/';

    /**
     * EcommerceStores constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $store_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $store_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $store_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------


    /**
     * @param null $customer_id
     * @return Customers
     */
    public function customers($customer_id = null)
    {
        return new Customers(
            $this->getRequest(),
            $this->getSettings(),
            $customer_id
        );
    }

    /**
     * @param null $product_id
     * @return Products
     */
    public function products($product_id = null)
    {
        return new Products(
            $this->getRequest(),
            $this->getSettings(),
            $product_id
        );
    }


    /**
     * @param null $order_id
     * @return Orders
     */
    public function orders($order_id = null)
    {
        return new Orders(
            $this->getRequest(),
            $this->getSettings(),
            $order_id
        );
    }

    /**
     * @param null $cart_id
     * @return Carts
     */
    public function carts($cart_id = null)
    {
        return new Carts(
            $this->getRequest(),
            $this->getSettings(),
            $cart_id
        );
    }

    /**
     * @param null $promo_rule_id
     * @return PromoRules
     */
    public function promoRules($promo_rule_id = null)
    {
        return new PromoRules(
            $this->getRequest(),
            $this->getSettings(),
            $promo_rule_id
        );
    }
}
