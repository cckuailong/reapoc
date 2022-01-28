<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports\Unsubscribes;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;

class UnsubscribesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . Unsubscribes::URL_COMPONENT,
            $this->mailchimp->reports(1)->unsubscribes(),
            "The Unsubscribes collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . Unsubscribes::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->unsubscribes(1),
            "The Unsubscribes instance endpoint should be constructed correctly"
        );
    }
}
