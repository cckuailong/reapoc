<?php

namespace MailchimpAPI\Resources\Lists\Members;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Tags
 * @package MailchimpAPI\Resources\Lists\Members
 */
class Tags extends ApiResource
{
    /**
     * The url component for this resource
     */
    const URL_COMPONENT = '/tags/';

    /**
     * Tags constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings|null $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
