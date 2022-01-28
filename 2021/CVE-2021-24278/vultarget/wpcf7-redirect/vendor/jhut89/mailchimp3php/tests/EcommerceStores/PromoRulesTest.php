<?php

namespace MailchimpTests\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\PromoRules;
use MailchimpTests\MailChimpTestCase;

class PromoRulesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . PromoRules::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->promoRules(),
            "The Promo Rules collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . PromoRules::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->promoRules(1),
            "The Promo Rules instance endpoint should be constructed correctly"
        );
    }
}
