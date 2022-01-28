<?php

namespace MailchimpTests;


use MailchimpAPI\Resources\EcommerceStores;

/**
 * Class EcommerceStoresTest
 * @package MailchimpTests
 */
class EcommerceStoresTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testEcommerceStoresCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(),
            "The Ecommerce Stores collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testEcommerceStoresInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1),
            "The Ecommerce Stores instance endpoint should be constructed correctly"
        );
    }
}
