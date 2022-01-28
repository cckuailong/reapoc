<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class AuthorizedApps
 * @package MailchimpAPI\Resources
 */
class AuthorizedApps extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/authorized-apps/';


    /**
     * AuthorizedApps constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $app_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $app_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $app_id);
    }
}
