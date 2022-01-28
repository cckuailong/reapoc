<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutch
 * Date: 7/2/18
 * Time: 4:36 PM
 */

namespace MailchimpTests\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores;
use MailchimpAPI\Resources\EcommerceStores\Carts;
use MailchimpTests\MailChimpTestCase;

/**
 * Class CartsTest
 * @package MailchimpTests\EcommerceStores
 */
class CartsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Carts::URL_COMPONENT,
            $this->mailchimp->ecommerceStores(1)->carts(),
            "The Carts collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            EcommerceStores::URL_COMPONENT . 1 . Carts::URL_COMPONENT . 1,
            $this->mailchimp->ecommerceStores(1)->carts(1),
            "The Carts instance endpoint should be constructed correctly"
        );
    }
}
