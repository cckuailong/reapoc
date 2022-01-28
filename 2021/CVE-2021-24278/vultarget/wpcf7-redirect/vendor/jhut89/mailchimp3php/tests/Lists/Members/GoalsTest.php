<?php

namespace MailchimpTests\Lists\Members;

use MailchimpAPI\Resources\Lists\Members\Goals;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\Members;

/**
 * Class GoalsTest
 * @package MailchimpTests\Lists\Members
 */
class GoalsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Goals::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(1)->goals(),
            "The Goals collection endpoint should be constructed correctly"
        );
    }
}
