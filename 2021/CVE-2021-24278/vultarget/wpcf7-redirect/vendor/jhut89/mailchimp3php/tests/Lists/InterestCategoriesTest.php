<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutch
 * Date: 7/2/18
 * Time: 5:19 PM
 */

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\InterestCategories;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class InterestCategoriesTest
 * @package MailchimpTests\Lists
 */
class InterestCategoriesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . InterestCategories::URL_COMPONENT,
            $this->mailchimp->lists(1)->interestCategories(),
            "The Interest Categories collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . InterestCategories::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->interestCategories(1),
            "The Interest Categories instance endpoint should be constructed correctly"
        );
    }
}
