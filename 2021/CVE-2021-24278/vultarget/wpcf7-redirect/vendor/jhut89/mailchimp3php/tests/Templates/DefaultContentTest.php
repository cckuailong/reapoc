<?php

namespace MailchimpTests\Templates;

use MailchimpAPI\Resources\Templates\DefaultContent;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Templates;

/**
 * Class DefaultContentTest
 * @package MailchimpTests\Templates
 */
class DefaultContentTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Templates::URL_COMPONENT . 1 . DefaultContent::URL_COMPONENT,
            $this->mailchimp->templates(1)->defaultContent(),
            "The Default Content collection endpoint should be constructed correctly"
        );
    }
}
