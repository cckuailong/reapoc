<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Campaigns;
use MailchimpAPI\MailchimpException;

/**
 * Class CampaignsTest
 * @package MailchimpTests
 */
class CampaignsTest extends MailChimpTestCase
{
    /**
     * @throws MailchimpException
     */
    public function testCampaignFoldersCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT,
            $this->mailchimp->campaigns(),
            "The campaigns collection url should be constructed correctly"
        );
    }

    /**
     * @throws MailchimpException
     */
    public function testCampaignFoldersInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT . 1,
            $this->mailchimp->campaigns(1),
            "The campaigns instance url should be constructed correctly"
        );
    }
}
