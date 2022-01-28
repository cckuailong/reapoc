<?php

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\MergeFields;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class MergeFieldsTest
 * @package MailchimpTests\Lists
 */
class MergeFieldsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . MergeFields::URL_COMPONENT,
            $this->mailchimp->lists(1)->mergeFields(),
            "The Merge Fields collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . MergeFields::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->mergeFields(1),
            "The Merge Fields instance endpoint should be constructed correctly"
        );
    }
}
