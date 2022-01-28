<?php

namespace MailchimpTests\Lists\Members;

use MailchimpAPI\Resources\Lists\Members\Notes;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\Members;
use MailchimpTests\MailChimpTestCase;

/**
 * Class NotesTest
 * @package MailchimpTests\Lists\Members
 */
class NotesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Notes::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(1)->notes(),
            "The Notes collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Notes::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->members(1)->notes(1),
            "The Notes instance endpoint should be constructed correctly"
        );
    }
}
