<?php

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists;
use MailchimpAPI\Resources\Lists\AbuseReports;
use MailchimpTests\MailChimpTestCase;

/**
 * Class AbuseReportsTest
 * @package MailchimpTests\Lists
 */
class AbuseReportsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . AbuseReports::URL_COMPONENT,
            $this->mailchimp->lists(1)->abuseReports(),
            "The Abuse Reports collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . AbuseReports::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->abuseReports(1),
            "The Abuse Reports instance endpoint should be constructed correctly"
        );
    }
}
