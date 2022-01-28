<?php

namespace MailchimpTests\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Orders;
use MailchimpTests\MailChimpTestCase;

/**
 * Class OrdersTest
 * @package MailchimpTests\EcommerceStores
 */
class OrdersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Orders::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->orders(),
            "The Orders collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Orders::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->orders(1),
            "The Orders instance endpoint should be constructed correctly"
        );
    }
}
