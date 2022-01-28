<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Unsubscribes
 * @package MailchimpAPI\Resources\Reports
 */
class Unsubscribes extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/unsubscribed/';

    /**
     * Unsubscribes constructor.
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
