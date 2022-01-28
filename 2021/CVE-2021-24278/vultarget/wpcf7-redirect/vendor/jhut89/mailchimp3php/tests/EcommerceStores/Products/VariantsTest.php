<?php

namespace MailchimpTests\EcommerceStores\Products;

use MailchimpAPI\Resources\EcommerceStores\Products\Variants;
use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Products;
use MailchimpTests\MailChimpTestCase;

/**
 * Class VariantsTest
 * @package MailchimpTests\EcommerceStores\Products
 */
class VariantsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Products::URL_COMPONENT . 1 . Variants::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->products(1)->variants(),
            "The Variants collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Products::URL_COMPONENT . 1 . Variants::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->products(1)->variants(1),
            "The Variants instance endpoint should be constructed correctly"
        );
    }
}
