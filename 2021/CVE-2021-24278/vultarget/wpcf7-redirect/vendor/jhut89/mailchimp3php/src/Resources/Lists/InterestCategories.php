<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\Lists\InterestCategories\Interest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;


/**
 * Class InterestCategories
 * @package MailchimpAPI\Resources\Lists
 */
class InterestCategories extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/interest-categories/';

    /**
     * InterestCategories constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $interest_category_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $interest_category_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $interest_category_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $interest_id
     * @return Interest
     */
    public function interests($interest_id = null)
    {
        return new Interest(
            $this->getRequest(),
            $this->getSettings(),
            $interest_id
        );
    }
}
