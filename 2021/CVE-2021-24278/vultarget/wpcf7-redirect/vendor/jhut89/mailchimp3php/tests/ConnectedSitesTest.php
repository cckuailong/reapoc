<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\ConnectedSites;

class ConnectedSitesTest extends MailChimpTestCase
{
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            ConnectedSites::URL_COMPONENT,
            $this->mailchimp->connectedSites(),
            "The Connected Sites url should be constructed correctly"
        );
    }

    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            ConnectedSites::URL_COMPONENT . 1,
            $this->mailchimp->connectedSites(1),
            "The Connected Sites url should be constructed correctly"
        );
    }
}
