<?php

namespace MailchimpAPI\Resources\Campaigns;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Content
 * @package MailchimpAPI\Resources\Campaigns
 */
class Content extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/content/';

    /**
     * Content constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
