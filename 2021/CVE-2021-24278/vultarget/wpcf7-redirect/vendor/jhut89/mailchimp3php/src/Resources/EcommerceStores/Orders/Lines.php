<?php

namespace MailchimpAPI\Resources\EcommerceStores\Orders;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Lines
 * @package MailchimpAPI\Resources\EcommerceStores\Orders
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
     * @param $line_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $line_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $line_id);
    }
}
