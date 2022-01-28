<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class CampaignAbuse
 * @package MailchimpAPI\Resources\Reports
 */
class CampaignAbuse extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/abuse-reports/';

    /**
     * CampaignAbuse constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $report_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $report_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $report_id);
    }
}
