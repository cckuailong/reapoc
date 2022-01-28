<?php

namespace MailchimpAPI\Resources\Lists\Members;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Events
 * @package MailchimpAPI\Resources\Lists\Members
 */
class Events extends ApiResource
{
    /**
     * The url component for this resource
     */
    const URL_COMPONENT = '/events/';

    /**
     * Events constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings|null $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
