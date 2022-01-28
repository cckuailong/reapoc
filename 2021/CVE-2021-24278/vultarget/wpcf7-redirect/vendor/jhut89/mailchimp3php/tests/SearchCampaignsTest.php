<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\SearchCampaigns;

/**
 * Class SearchCampaignsTest
 * @package MailchimpTests
 */
class SearchCampaignsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testSearchCampaignsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            SearchCampaigns::URL_COMPONENT,
            $this->mailchimp->searchCampaigns(),
            "The Search Campaigns collection endpoint should be constructed correctly"
        );
    }
}
