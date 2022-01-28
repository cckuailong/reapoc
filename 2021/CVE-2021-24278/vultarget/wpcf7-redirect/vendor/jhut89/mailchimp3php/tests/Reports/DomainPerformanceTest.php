<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\DomainPerformance;
use MailchimpTests\MailChimpTestCase;

/**
 * Class DomainPerformanceTest
 * @package MailchimpTests\Reports
 */
class DomainPerformanceTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . DomainPerformance::URL_COMPONENT,
            $this->mailchimp->reports(1)->domainPerformance(),
            "The Domain Performance collection endpoint should be constructed correctly"
        );
    }
}
