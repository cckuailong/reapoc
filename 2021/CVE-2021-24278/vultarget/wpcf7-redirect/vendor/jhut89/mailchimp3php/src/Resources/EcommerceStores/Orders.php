<?php

namespace MailchimpAPI\Resources\EcommerceStores;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\EcommerceStores\Orders\Lines;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Orders
 * @package MailchimpAPI\Resources\EcommerceStores
 */
class Orders extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/orders/';

    /**
     * Orders constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $order_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $order_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $order_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $line_id
     * @return Lines
     */
    public function lines($line_id = null)
    {
        return new Lines(
            $this->getRequest(),
            $this->getSettings(),
            $line_id
        );
    }
}
