<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class SearchCampaigns
 * @package MailchimpAPI\Resources
 */
class SearchCampaigns extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/search-campaigns/';

    /**
     * SearchCampaigns constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
