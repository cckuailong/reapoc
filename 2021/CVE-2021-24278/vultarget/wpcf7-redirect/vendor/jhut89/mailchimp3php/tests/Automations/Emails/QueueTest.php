<?php

namespace MailchimpTests\Automations\Emails;

use MailchimpAPI\Resources\Automations;
use MailchimpAPI\Resources\Automations\Emails;
use MailchimpAPI\Resources\Automations\Emails\Queue;
use MailchimpTests\MailChimpTestCase;

/**
 * Class QueueTest
 * @package MailchimpTests\Automations\Emails
 */
class QueueTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1 . Emails::URL_COMPONENT . 1 . Queue::URL_COMPONENT,
            $this->mailchimp->automations(1)->emails(1)->queue(),
            "The Queue collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1 . Emails::URL_COMPONENT . 1 . Queue::URL_COMPONENT . 1,
            $this->mailchimp->automations(1)->emails(1)->queue(1),
            "The Queue instance endpoint should be constructed correctly"
        );
    }
}
