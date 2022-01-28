<?php


namespace MailchimpAPI\Resources;


use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class FacebookAds
 * @package MailchimpAPI\Resources
 */
class FacebookAds extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = "/facebook-ads/";

    /**
     * FacebookAds constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $outreach_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $outreach_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $outreach_id);
    }
}
