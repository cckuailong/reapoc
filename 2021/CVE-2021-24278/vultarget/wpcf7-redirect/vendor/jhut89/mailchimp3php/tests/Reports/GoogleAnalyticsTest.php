<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\GoogleAnalytics;
use MailchimpTests\MailChimpTestCase;

class GoogleAnalyticsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . GoogleAnalytics::URL_COMPONENT,
            $this->mailchimp->reports(1)->googleAnalytics(),
            "The Google Analytics collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . GoogleAnalytics::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->googleAnalytics(1),
            "The Google Analytics instance endpoint should be constructed correctly"
        );
    }
}
