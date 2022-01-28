<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class GrowthHistory
 * @package MailchimpAPI\Resources\Lists
 */
class GrowthHistory extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/growth-history/';

    /**
     * GrowthHistory constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $month
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $month = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $month);
    }
}
