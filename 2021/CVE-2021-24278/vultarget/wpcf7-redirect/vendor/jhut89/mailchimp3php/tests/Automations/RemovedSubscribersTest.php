<?php

namespace MailchimpTests\Automations;

use MailchimpAPI\Resources\Automations;
use MailchimpAPI\Resources\Automations\RemovedSubscribers;
use MailchimpTests\MailChimpTestCase;

/**
 * Class RemovedSubscribersTest
 * @package MailchimpTests\Automations
 */
class RemovedSubscribersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1 . RemovedSubscribers::URL_COMPONENT,
            $this->mailchimp->automations(1)->removedSubscribers(),
            "The Removed Subscribers collection endpoint should be constructed correctly"
        );
    }
}
