<?php

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\Webhooks;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class WebhooksTest
 * @package MailchimpTests\Lists
 */
class WebhooksTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Webhooks::URL_COMPONENT,
            $this->mailchimp->lists(1)->webhooks(),
            "The Webhooks collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Webhooks::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->webhooks(1),
            "The Webhooks instance endpoint should be constructed correctly"
        );
    }
}
