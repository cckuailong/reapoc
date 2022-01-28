<?php

namespace MailchimpAPI\Resources\Lists\Members;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Activity
 * @package MailchimpAPI\Resources\Lists\Members
 */
class Activity extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/activity/';

    /**
     * Activity constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
