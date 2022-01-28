<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class AbuseReports
 * @package MailchimpAPI\Resources\Lists
 */
class AbuseReports extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/abuse-reports/';

    /**
     * AbuseReports constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $report_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $report_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $report_id);
    }
}
