<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Reports;

/**
 * Class ReportsTest
 * @package MailchimpTests
 */
class ReportsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testReportsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT,
            $this->mailchimp->reports(),
            "The Reports collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testReportsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1,
            $this->mailchimp->reports(1),
            "The Reports instance endpoint should be constructed correctly"
        );
    }
}
