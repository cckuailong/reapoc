<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports\SubReports;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;

/**
 * Class SubReportsTest
 * @package MailchimpTests\Reports
 */
class SubReportsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . SubReports::URL_COMPONENT,
            $this->mailchimp->reports(1)->subReports(),
            "The SubReports collection endpoint should be constructed correctly"
        );
    }
}
