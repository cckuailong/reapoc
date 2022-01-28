<?php

namespace MailchimpTests\EcommerceStores\Carts;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Carts;
use MailchimpAPI\Resources\EcommerceStores\Carts\Lines;
use MailchimpTests\MailChimpTestCase;

/**
 * Class LinesTest
 * @package MailchimpTests\EcommerceStores\Carts
 */
class LinesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Carts::URL_COMPONENT . 1 . Lines::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->carts(1)->lines(),
            "The Lines collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Carts::URL_COMPONENT . 1 . Lines::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->carts(1)->lines(1),
            "The Lines instance endpoint should be constructed correctly"
        );
    }
}
