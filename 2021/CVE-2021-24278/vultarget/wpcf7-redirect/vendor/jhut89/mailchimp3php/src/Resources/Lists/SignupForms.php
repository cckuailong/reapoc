<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class SignupForms
 * @package MailchimpAPI\Resources\Lists
 */
class SignupForms extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/signup-forms/';

    /**
     * SignupForms constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $form_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $form_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $form_id);
    }
}
