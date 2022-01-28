<?php

namespace MailchimpTests;


use MailchimpAPI\Resources\CampaignFolders;

/**
 * Class CampaignFoldersTest
 * @package MailchimpTests
 */
class CampaignFoldersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCampaignFoldersCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            CampaignFolders::URL_COMPONENT,
            $this->mailchimp->campaignFolders(),
            "The campaign folders collection url should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCampaignFoldersInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            CampaignFolders::URL_COMPONENT . 1,
            $this->mailchimp->campaignFolders(1),
            "The campaign folders instance url should be constructed correctly"
        );
    }
}
