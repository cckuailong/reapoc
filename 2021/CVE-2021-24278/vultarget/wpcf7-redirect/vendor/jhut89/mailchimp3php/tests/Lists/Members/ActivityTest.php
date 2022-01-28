<?php

namespace MailchimpTests\Lists\Members;

use MailchimpAPI\Resources\Lists\Members\Activity;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\Members;
use MailchimpTests\MailChimpTestCase;

/**
 * Class ActivityTest
 * @package MailchimpTests\Lists\Members
 */
class ActivityTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Activity::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(1)->activity(),
            "The Activity collection endpoint should be constructed correctly"
        );
    }
}
