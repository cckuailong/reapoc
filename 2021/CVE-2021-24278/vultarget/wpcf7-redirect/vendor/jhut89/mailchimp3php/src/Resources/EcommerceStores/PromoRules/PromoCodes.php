<?php


namespace MailchimpAPI\Resources\EcommerceStores\PromoRules;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class PromoCodes
 * @package MailchimpAPI\Resources\EcommerceStores\PromoRules
 */
class PromoCodes extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/promo-codes/';

    /**
     * PromoCodes constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $promo_code_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $promo_code_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $promo_code_id);
    }
}
