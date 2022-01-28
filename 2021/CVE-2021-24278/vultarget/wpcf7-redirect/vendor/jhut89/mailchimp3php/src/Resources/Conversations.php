<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\Conversations\Messages;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Conversations
 * @package MailchimpAPI\Resources
 */
class Conversations extends ApiResource
{
    /**
     * The conversations endpoint url component
     */
    const URL_COMPONENT = '/conversations/';


    /**
     * Conversations constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $conversation_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $conversation_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $conversation_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------


    /**
     * @param null $message_id
     * @return Messages
     */
    public function messages($message_id = null)
    {
        return new Messages(
            $this->getRequest(),
            $this->getSettings(),
            $message_id
        );
    }
}
