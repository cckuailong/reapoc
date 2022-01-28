<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\SentTo;
use MailchimpTests\MailChimpTestCase;

/**
 * Class SentToTest
 * @package MailchimpTests\Reports
 */
class SentToTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . SentTo::URL_COMPONENT,
            $this->mailchimp->reports(1)->sentTo(),
            "The Sent To collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . SentTo::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->sentTo(1),
            "The Sent To instance endpoint should be constructed correctly"
        );
    }
}
