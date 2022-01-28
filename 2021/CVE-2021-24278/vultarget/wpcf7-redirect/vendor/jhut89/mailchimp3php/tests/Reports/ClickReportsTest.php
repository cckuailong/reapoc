<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\ClickReports;
use MailchimpTests\MailChimpTestCase;

/**
 * Class ClickReportsTest
 * @package MailchimpTests\Reports
 */
class ClickReportsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . ClickReports::URL_COMPONENT,
            $this->mailchimp->reports(1)->clickReports(),
            "The Click Reports collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . ClickReports::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->clickReports(1),
            "The Click Reports instance endpoint should be constructed correctly"
        );
    }
}
