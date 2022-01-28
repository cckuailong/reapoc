<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;


/**
 * Class Activity
 * @package MailchimpAPI\Resources\Lists
 */
class Activity extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/activity/';

    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
