<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class BatchOperations
 * @package MailchimpAPI\Resources
 */
class BatchOperations extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/batches/';

    /**
     * BatchOperations constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $batch_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $batch_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $batch_id);
    }
}
