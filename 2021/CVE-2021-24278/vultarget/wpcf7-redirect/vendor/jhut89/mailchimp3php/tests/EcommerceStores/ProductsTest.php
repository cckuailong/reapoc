<?php

namespace MailchimpTests\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Products;
use MailchimpTests\MailChimpTestCase;

/**
 * Class ProductsTest
 * @package MailchimpTests\EcommerceStores
 */
class ProductsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Products::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->products(),
            "The Products collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Products::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->products(1),
            "The Products instance endpoint should be constructed correctly"
        );
    }
}
