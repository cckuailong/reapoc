<?php

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\Segments;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class SegmentsTest
 * @package MailchimpTests\Lists
 */
class SegmentsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Segments::URL_COMPONENT,
            $this->mailchimp->lists(1)->segments(),
            "The Segments collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Segments::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->segments(1),
            "The Segments instance endpoint should be constructed correctly"
        );
    }
}
