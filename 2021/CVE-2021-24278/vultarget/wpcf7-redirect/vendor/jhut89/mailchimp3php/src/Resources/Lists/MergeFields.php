<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class MergeFields
 * @package MailchimpAPI\Resources\Lists
 */
class MergeFields extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/merge-fields/';

    /**
     * MergeFields constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $merge_field_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $merge_field_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $merge_field_id);
    }
}
