<?php

namespace MailchimpAPI\Resources\EcommerceStores;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\EcommerceStores\Carts\Lines;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Carts
 * @package MailchimpAPI\Resources\EcommerceStores
 */
class Carts extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/carts/';

    /**
     * Carts constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $cart_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $cart_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $cart_id);
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
