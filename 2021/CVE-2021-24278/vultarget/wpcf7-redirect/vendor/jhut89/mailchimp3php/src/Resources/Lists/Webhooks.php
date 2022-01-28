<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Webhooks
 * @package MailchimpAPI\Resources\Lists
 */
class Webhooks extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/webhooks/';

    /**
     * Webhooks constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $webhook_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $webhook_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $webhook_id);
    }
}
