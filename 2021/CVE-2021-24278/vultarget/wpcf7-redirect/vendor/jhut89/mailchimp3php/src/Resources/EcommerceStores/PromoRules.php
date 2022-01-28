<?php
namespace MailchimpAPI\Resources\EcommerceStores;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Resources\EcommerceStores\PromoRules\PromoCodes;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class PromoRules
 * @package MailchimpAPI\Resources\EcommerceStores
 */
class PromoRules extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/promo-rules/';

    /**
     * PromoRules constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $promo_rule_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $promo_rule_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $promo_rule_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------


    /**
     * @param null $promo_code_id
     * @return PromoCodes
     */
    public function promoCodes($promo_code_id = null)
    {
        return new PromoCodes(
            $this->getRequest(),
            $this->getSettings(),
            $promo_code_id
        );
    }
}
