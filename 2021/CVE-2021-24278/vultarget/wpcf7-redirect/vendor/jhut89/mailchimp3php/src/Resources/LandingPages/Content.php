<?php


namespace MailchimpAPI\Resources\LandingPages;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Content
 * @package MailchimpAPI\Resources\LandingPages
 */
class Content extends ApiResource
{
    /**
     *
     */
    const URL_COMPONENT = "/content/";

    /**
     * Content constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
