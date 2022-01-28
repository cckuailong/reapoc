<?php

namespace MailchimpAPI\Resources\Reports\ClickReports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Members
 * @package MailchimpAPI\Resources\Reports\ClickReports
 */
class Members extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/members/';

    /**
     * Members constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $member
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $member = null)
    {
        parent::__construct($request, $settings);
        if ($member && strpos($member, "@")) {
            $member = md5(strtolower($member));
        }
        $request->appendToEndpoint(self::URL_COMPONENT . $member);
    }
}
