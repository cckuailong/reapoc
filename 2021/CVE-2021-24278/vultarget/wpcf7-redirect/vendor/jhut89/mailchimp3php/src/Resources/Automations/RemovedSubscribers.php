<?php

namespace MailchimpAPI\Resources\Automations;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class RemovedSubscribers
 * @package MailchimpAPI\Automations
 */
class RemovedSubscribers extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/removed-subscribers/';

    /**
     * RemovedSubscribers constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
