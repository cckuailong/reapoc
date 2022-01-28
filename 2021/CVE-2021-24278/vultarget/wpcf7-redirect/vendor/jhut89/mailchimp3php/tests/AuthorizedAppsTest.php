<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\AuthorizedApps;

/**
 * Class AuthorizedAppsTest
 * @package MailchimpTests
 */
final class AuthorizedAppsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testAuthorizedAppsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            AuthorizedApps::URL_COMPONENT,
            $this->mailchimp->apps(),
            "The authorized apps url should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testAuthorizedAppsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            AuthorizedApps::URL_COMPONENT . 1,
            $this->mailchimp->apps(1),
            "The authorized apps url should be constructed correctly"
        );
    }
}
