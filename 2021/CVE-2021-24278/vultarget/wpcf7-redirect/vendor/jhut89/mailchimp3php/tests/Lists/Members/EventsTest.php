<?php
namespace MailchimpTests\Lists\Members;

use MailchimpAPI\Resources\Lists\Members\Events;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\Members;
use MailchimpTests\MailChimpTestCase;

/**
 * Class EventsTest
 * @package MailchimpTests\Lists\Members
 */
class EventsTest extends MailChimpTestCase
{
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Events::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(1)->events(),
            "The Member Events collection endpoint should be constructed correctly"
        );
    }
}