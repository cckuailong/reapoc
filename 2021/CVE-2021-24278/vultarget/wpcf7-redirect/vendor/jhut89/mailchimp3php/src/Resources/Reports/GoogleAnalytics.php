<?php


namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class GoogleAnalytics
 * @package MailchimpAPI\Resources\Reports
 */
class GoogleAnalytics extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/google-analytics/';

    /**
     * GoogleAnalytics constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $profile_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $profile_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $profile_id);
    }
}
