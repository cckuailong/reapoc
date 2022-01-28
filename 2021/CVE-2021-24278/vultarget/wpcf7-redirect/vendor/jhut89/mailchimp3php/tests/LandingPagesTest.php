<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\LandingPages;

class LandingPagesTest extends MailChimpTestCase
{
    public function testLandingPagesCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            LandingPages::URL_COMPONENT,
            $this->mailchimp->landingPages(),
            "The Landing Pages collection endpoint should be constructed correctly"
        );
    }

    public function testLandingPagesInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            LandingPages::URL_COMPONENT . 1,
            $this->mailchimp->landingPages(1),
            "The Landing Pages instance endpoint should be constructed correctly"
        );
    }
}
