<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutch
 * Date: 7/2/18
 * Time: 4:40 PM
 */

namespace MailchimpTests\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Customers;
use MailchimpTests\MailChimpTestCase;

/**
 * Class CustomersTest
 * @package MailchimpTests\EcommerceStores
 */
class CustomersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Customers::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->customers(),
            "The Customers collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Customers::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->customers(1),
            "The Customers instance endpoint should be constructed correctly"
        );
    }
}
