<?php

namespace MailchimpAPI\Resources\Templates;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class DefaultContent
 * @package MailchimpAPI\Resources\Templates
 */
class DefaultContent extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/default-content/';

    /**
     * DefaultContent constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
