<?php

namespace MailchimpAPI\Resources\EcommerceStores;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Customers
 * @package MailchimpAPI\Resources\EcommerceStores
 */
class Customers extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/customers/';

    /**
     * Customers constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $customer_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $customer_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $customer_id);
    }
}
