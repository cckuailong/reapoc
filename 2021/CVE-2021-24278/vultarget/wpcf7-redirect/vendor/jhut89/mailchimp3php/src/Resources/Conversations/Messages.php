<?php

namespace MailchimpAPI\Resources\Conversations;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Messages
 * @package MailchimpAPI\Resource\Conversations
 */
class Messages extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/messages/';

    /**
     * Messages constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $message_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $message_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $message_id);
    }
}
