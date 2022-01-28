<?php

namespace MailchimpAPI\Resources\Campaigns;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Feedback
 * @package MailchimpAPI\Resources\Campaigns
 */
class Feedback extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/feedback/';

    /**
     * Feedback constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $feedback_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $feedback_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $feedback_id);
    }
}
