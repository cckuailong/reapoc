<?php

namespace MailchimpAPI\Resources\Automations\Emails;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Queue
 * @package MailchimpAPI\Resources\Automations\Emails
 */
class Queue extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/queue/';

    /**
     * Queue constructor.
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
