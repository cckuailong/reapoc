<?php

namespace MailchimpTests\Reports\ClickReports;

use MailchimpAPI\Resources\Reports\ClickReports\Members;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\ClickReports;

class MembersTest extends MailChimpTestCase
{
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . ClickReports::URL_COMPONENT . 1 . Members::URL_COMPONENT,
            $this->mailchimp->reports(1)->clickReports(1)->members(),
            "The Click Reports Members collection endpoint should be constructed correctly"
        );
    }

    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . ClickReports::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->clickReports(1)->members(1),
            "The Click Reports Members instance endpoint should be constructed correctly"
        );
    }
}
