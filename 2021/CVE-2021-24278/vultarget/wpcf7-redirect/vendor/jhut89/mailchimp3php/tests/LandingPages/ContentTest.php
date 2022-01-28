<?php

namespace MailchimpTests\LandingPages;

use MailchimpAPI\Resources\LandingPages;
use MailchimpAPI\Resources\LandingPages\Content;
use MailchimpTests\MailChimpTestCase;

class ContentTest extends MailChimpTestCase
{
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            LandingPages::URL_COMPONENT . 1 . Content::URL_COMPONENT,
            $this->mailchimp->landingPages(1)->content(),
            "The Landing Pages Content endpoint should be constructed correctly"
        );
    }
}
