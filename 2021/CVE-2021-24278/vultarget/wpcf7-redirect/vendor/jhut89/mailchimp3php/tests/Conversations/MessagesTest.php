<?php

namespace MailchimpTests\Conversations;

use MailchimpAPI\Resources\Conversations;
use MailchimpAPI\Resources\Conversations\Messages;
use MailchimpTests\MailChimpTestCase;

/**
 * Class MessagesTest
 * @package MailchimpTests\Conversations
 */
class MessagesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Conversations::URL_COMPONENT . 1 . Messages::URL_COMPONENT,
            $this->mailchimp->conversations(1)->messages(),
            "The Messages collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Conversations::URL_COMPONENT . 1 . Messages::URL_COMPONENT . 1,
            $this->mailchimp->conversations(1)->messages(1),
            "The Messages instance endpoint should be constructed correctly"
        );
    }
}
