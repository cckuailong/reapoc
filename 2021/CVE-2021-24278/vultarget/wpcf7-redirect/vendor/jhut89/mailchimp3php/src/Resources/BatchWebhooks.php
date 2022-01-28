<?php

namespace MailchimpAPI\Resources;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class BatchWebhooks
 * @package MailchimpAPI\Resources
 */
class BatchWebhooks extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/batch-webhooks/';

    /**
     * BatchWebhooks constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $batch_webhook_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $batch_webhook_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $batch_webhook_id);
    }
}
