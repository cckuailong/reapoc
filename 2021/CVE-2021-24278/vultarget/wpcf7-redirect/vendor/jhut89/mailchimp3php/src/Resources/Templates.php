<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\Templates\DefaultContent;
use MailchimpAPI\Settings\MailchimpSettings;


/**
 * Class Templates
 * @package MailchimpAPI\Resources
 */
class Templates extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/templates/';

    /**
     * Templates constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $template_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $template_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $template_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @return DefaultContent
     */
    public function defaultContent()
    {
        return new DefaultContent(
            $this->getRequest(),
            $this->getSettings()
        );
    }
}
