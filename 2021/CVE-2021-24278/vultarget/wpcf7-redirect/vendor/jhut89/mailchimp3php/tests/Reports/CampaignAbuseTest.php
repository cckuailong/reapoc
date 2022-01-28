<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports\CampaignAbuse;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;

/**
 * Class CampaignAbuseTest
 * @package MailchimpTests\Reports
 */
class CampaignAbuseTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . CampaignAbuse::URL_COMPONENT,
            $this->mailchimp->reports(1)->abuse(),
            "The Campaign Abuse collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . CampaignAbuse::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->abuse(1),
            "The Campaign Abuse instance endpoint should be constructed correctly"
        );
    }
}
