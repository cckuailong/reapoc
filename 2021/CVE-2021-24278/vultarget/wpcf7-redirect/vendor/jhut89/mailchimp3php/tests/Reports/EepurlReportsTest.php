<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\EepurlReports;
use MailchimpTests\MailChimpTestCase;

/**
 * Class EepurlReportsTest
 * @package MailchimpTests\Reports
 */
class EepurlReportsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . EepurlReports::URL_COMPONENT,
            $this->mailchimp->reports(1)->eepurlReports(),
            "The Eepurl Reports collection endpoint should be constructed correctly"
        );
    }
}
