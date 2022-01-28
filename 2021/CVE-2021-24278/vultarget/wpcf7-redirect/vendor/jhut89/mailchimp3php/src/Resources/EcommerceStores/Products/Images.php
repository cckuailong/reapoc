<?php

namespace MailchimpAPI\Resources\EcommerceStores\Products;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Images
 * @package MailchimpAPI\Resources\EcommerceStores\Products
 */
class Images extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/images/';

    /**
     * Images constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $image_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $image_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $image_id);
    }
}
