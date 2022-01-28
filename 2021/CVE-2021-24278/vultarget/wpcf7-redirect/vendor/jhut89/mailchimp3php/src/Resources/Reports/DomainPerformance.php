<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class DomainPerformance
 * @package MailchimpAPI\Resources\Reports
 */
class DomainPerformance extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/domain-performance/';

    /**
     * DomainPerformance constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT);
    }
}
