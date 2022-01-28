<?php

namespace MailchimpAPI\Resources\Lists\Members;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Goals
 * @package MailchimpAPI\Resources\Lists\Members
 */
class Goals extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/goals/';

    /**
     * Goals constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
