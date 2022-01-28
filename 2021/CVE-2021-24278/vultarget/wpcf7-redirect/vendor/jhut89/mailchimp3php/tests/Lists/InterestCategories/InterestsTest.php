<?php

namespace MailchimpTests\Lists\InterestCategories;

use MailchimpAPI\Resources\Lists\InterestCategories\Interest;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\InterestCategories;

/**
 * Class InterestsTest
 * @package MailchimpTests\Lists\InterestCategories
 */
class InterestsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . InterestCategories::URL_COMPONENT . 1 . Interest::URL_COMPONENT,
            $this->mailchimp->lists(1)->interestCategories(1)->interests(),
            "The Interest collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . InterestCategories::URL_COMPONENT . 1 . Interest::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->interestCategories(1)->interests(1),
            "The Interest instance endpoint should be constructed correctly"
        );
    }
}
