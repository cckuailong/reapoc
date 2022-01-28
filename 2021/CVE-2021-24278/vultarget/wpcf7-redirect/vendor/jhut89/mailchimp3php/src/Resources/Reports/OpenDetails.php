<?php


namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class OpenDetails
 * @package MailchimpAPI\Resources\Reports
 */
class OpenDetails extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/open-details/';

    /**
     * OpenDetails constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
