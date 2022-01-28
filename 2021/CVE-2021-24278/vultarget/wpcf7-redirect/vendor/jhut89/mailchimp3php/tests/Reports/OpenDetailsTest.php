<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\OpenDetails;
use MailchimpTests\MailChimpTestCase;

class OpenDetailsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . OpenDetails::URL_COMPONENT,
            $this->mailchimp->reports(1)->openReports(),
            "The Open Details collection endpoint should be constructed correctly"
        );
    }
}
