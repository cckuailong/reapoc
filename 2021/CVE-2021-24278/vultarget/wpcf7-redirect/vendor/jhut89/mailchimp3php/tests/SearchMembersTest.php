<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\SearchMembers;

/**
 * Class SearchMembersTest
 * @package MailchimpTests
 */
class SearchMembersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testSearchMembersCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            SearchMembers::URL_COMPONENT,
            $this->mailchimp->searchMembers(),
            "The Search Members collection endpoint should be constructed correctly"
        );
    }
}
