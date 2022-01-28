<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class SubReports
 * @package MailchimpAPI\Resources\Reports
 */
class SubReports extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/sub-reports/';

    /**
     * SubReports constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
