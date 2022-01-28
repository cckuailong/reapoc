<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports\CampaignAdvice;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;

/**
 * Class CampaignAdviceTest
 * @package MailchimpTests\Reports
 */
class CampaignAdviceTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . CampaignAdvice::URL_COMPONENT,
            $this->mailchimp->reports(1)->advice(),
            "The Campaign Advice collection endpoint should be constructed correctly"
        );
    }
}
