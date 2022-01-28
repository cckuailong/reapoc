<?php

namespace MailchimpTests\Lists\Segments;

use MailchimpAPI\Resources\Lists\Segments\Members;
use MailchimpAPI\Resources\Lists\Segments;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class MembersTest
 * @package MailchimpTests\Lists\Segments
 */
class MembersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Segments::URL_COMPONENT . 1 . Members::URL_COMPONENT,
            $this->mailchimp->lists(1)->segments(1)->members(),
            "The Members collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Segments::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->segments(1)->members(1),
            "The Members instance endpoint should be constructed correctly"
        );
    }
}
