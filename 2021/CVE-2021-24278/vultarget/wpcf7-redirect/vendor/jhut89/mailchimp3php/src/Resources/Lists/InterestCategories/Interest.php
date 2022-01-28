<?php

namespace MailchimpAPI\Resources\Lists\InterestCategories;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Interest
 * @package MailchimpAPI\Resources\Lists\InterestCategories
 */
class Interest extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/interests/';

    /**
     * Interest constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $interest_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $interest_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $interest_id);
    }
}
