<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\FacebookAds;

class FacebookAdsTest extends MailChimpTestCase
{

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testFacebookAdsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            FacebookAds::URL_COMPONENT,
            $this->mailchimp->facebookAds(),
            "The Facebook Ads collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testFacebookAdsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            FacebookAds::URL_COMPONENT . 1,
            $this->mailchimp->facebookAds(1),
            "The Facebook Ads instance endpoint should be constructed correctly"
        );
    }
}
