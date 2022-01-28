<?php

namespace MailchimpTests\Campaigns;

use MailchimpAPI\Resources\Campaigns;
use MailchimpAPI\Resources\Campaigns\SendChecklist;
use MailchimpTests\MailChimpTestCase;

/**
 * Class SendChecklistTest
 * @package MailchimpTests\Campaigns
 */
class SendChecklistTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT . 1 . SendChecklist::URL_COMPONENT,
            $this->mailchimp->campaigns(1)->checklist(),
            "The Checklist endpoint should be constructed correctly"
        );
    }
}
