<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\GoogleAds;

class GoogleAdsTest extends MailChimpTestCase
{
    public function testGoogleAdsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            GoogleAds::URL_COMPONENT,
            $this->mailchimp->googleAds(),
            "The Facebook Ads collection endpoint should be constructed correctly"
        );
    }

    public function testGoogleAdsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            GoogleAds::URL_COMPONENT . 1,
            $this->mailchimp->googleAds(1),
            "The Facebook Ads instance endpoint should be constructed correctly"
        );
    }
}
