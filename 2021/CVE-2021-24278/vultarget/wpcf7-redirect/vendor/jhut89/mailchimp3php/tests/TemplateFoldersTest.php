<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\TemplateFolders;

/**
 * Class TemplateFoldersTest
 * @package MailchimpTests
 */
class TemplateFoldersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testTemplateFoldersCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            TemplateFolders::URL_COMPONENT,
            $this->mailchimp->templateFolders(),
            "The Template Folders collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testTemplateFoldersInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            TemplateFolders::URL_COMPONENT . 1,
            $this->mailchimp->templateFolders(1),
            "The Template Folders instance endpoint should be constructed correctly"
        );
    }
}
