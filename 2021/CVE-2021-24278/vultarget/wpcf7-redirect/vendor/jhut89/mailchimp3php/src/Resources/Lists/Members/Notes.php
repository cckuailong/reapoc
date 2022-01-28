<?php

namespace MailchimpAPI\Resources\Lists\Members;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Notes
 * @package MailchimpAPI\Resources\Lists\Members
 */
class Notes extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/notes/';

    /**
     * Notes constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $note_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $note_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $note_id);
    }
}
