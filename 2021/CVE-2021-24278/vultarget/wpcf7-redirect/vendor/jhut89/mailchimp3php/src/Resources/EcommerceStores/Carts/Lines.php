<?php

namespace MailchimpAPI\Resources\EcommerceStores\Carts;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Lines
 * @package MailchimpAPI\Resources\EcommerceStores\Carts
 */
class Lines extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/lines/';

    /**
     * Lines constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $line_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $line_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $line_id);
    }
}
