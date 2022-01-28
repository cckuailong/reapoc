<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Account
 * is a representation of the root of the mailchimp api
 * @package Mailchimp_API\Resources
 */
class Account extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/';


    /**
     * Account constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
