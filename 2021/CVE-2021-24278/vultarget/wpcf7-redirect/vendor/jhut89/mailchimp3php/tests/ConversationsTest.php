<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Conversations;

class ConversationsTest extends MailChimpTestCase
{
    public function testConversationsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Conversations::URL_COMPONENT,
            $this->mailchimp->conversations(),
            "The conversations collection endpoint should be constructed correctly"
        );
    }

    public function testConversationsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Conversations::URL_COMPONENT . '1',
            $this->mailchimp->conversations(1),
            "The conversations instance endpoint should be constructed correctly"
        );
    }
}
