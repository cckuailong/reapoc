<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class SentTo
 * @package MailchimpAPI\Resources\Reports
 */
class SentTo extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/sent-to/';

    /**
     * SentTo constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $member
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $member)
    {
        parent::__construct($request, $settings);
        if ($member && strpos($member, "@")) {
            $member = md5(strtolower($member));
        }
        $request->appendToEndpoint(self::URL_COMPONENT . $member);
    }
}
