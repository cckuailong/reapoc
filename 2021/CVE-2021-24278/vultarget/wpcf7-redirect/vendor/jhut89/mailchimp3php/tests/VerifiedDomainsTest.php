<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\VerifiedDomains;

class VerifiedDomainsTest extends MailChimpTestCase
{
    public function testVerifiedDomainsCollectionEndpoint()
    {
        $this->endpointUrlBuildTest(
            VerifiedDomains::URL_COMPONENT,
            $this->mailchimp->verifiedDomains(),
            "The verified domains collection endpoint should be constructed correctly"
        );
    }

    public function testVerifiedDomainsInstanceEndpoint()
    {
        $this->endpointUrlBuildTest(
            VerifiedDomains::URL_COMPONENT . 'my-domain',
            $this->mailchimp->verifiedDomains('my-domain'),
            "The verified domains instance endpoint should be constructed correctly"
        );
    }
}