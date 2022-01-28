<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Account;

/**
 * Class AccountTest
 * @package MailchimpTests
 */
final class AccountTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testRootUrl()
    {
        $this->endpointUrlBuildTest(
            Account::URL_COMPONENT,
            $this->mailchimp->account(),
            "The root url should be constructed correctly"
        );
    }
}
