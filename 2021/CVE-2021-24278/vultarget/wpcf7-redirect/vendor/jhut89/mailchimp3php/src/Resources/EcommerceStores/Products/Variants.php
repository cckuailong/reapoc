<?php

namespace MailchimpAPI\Resources\EcommerceStores\Products;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Variants
 * @package MailchimpAPI\Resources\EcommerceStores\Products
 */
class Variants extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/variants/';

    /**
     * Variants constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $variant_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $variant_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $variant_id);
    }
}
