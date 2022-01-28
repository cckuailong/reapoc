<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Lists;

/**
 * Class ListsTest
 * @package MailchimpTests
 */
class ListsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testListsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT,
            $this->mailchimp->lists(),
            "The Lists collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testListsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1,
            $this->mailchimp->lists(1),
            "The Lists instance endpoint should be constructed correctly"
        );
    }
}
