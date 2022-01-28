<?php
namespace MailchimpTests\Lists\Members;

use MailchimpAPI\Resources\Lists\Members\Tags;
use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\Members;
use MailchimpTests\MailChimpTestCase;

/**
 * Class TagsTest
 * @package MailchimpTests\Lists\Members
 */
class TagsTest extends MailChimpTestCase
{
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1 . Tags::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(1)->tags(),
            "The Member Tags collection endpoint should be constructed correctly"
        );
    }
}