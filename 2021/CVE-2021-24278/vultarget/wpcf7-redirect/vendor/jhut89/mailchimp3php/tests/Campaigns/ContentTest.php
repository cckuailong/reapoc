<?php

namespace MailchimpTests\Campaigns;

use MailchimpAPI\Resources\Campaigns;
use MailchimpAPI\Resources\Campaigns\Content;
use MailchimpTests\MailChimpTestCase;

/**
 * Class ContentTest
 * @package MailchimpTests\Campaigns
 */
class ContentTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT . 1 . Content::URL_COMPONENT,
            $this->mailchimp->campaigns(1)->content(),
            "The Content endpoint should be constructed correctly"
        );
    }
}
