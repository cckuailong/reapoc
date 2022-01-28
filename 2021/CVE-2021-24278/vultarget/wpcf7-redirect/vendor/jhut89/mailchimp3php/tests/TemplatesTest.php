<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Templates;

/**
 * Class TemplatesTest
 * @package MailchimpTests
 */
class TemplatesTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testTemplatesCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Templates::URL_COMPONENT,
            $this->mailchimp->templates(),
            "The Templates collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testTemplatesInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Templates::URL_COMPONENT . 1,
            $this->mailchimp->templates(1),
            "The Templates instance endpoint should be constructed correctly"
        );
    }
}
