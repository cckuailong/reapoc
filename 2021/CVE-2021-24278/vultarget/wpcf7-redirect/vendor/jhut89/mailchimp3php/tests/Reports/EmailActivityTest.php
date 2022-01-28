<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports;
use MailchimpAPI\Resources\Reports\EmailActivity;
use MailchimpTests\MailChimpTestCase;

/**
 * Class EmailActivityTest
 * @package MailchimpTests\Reports
 */
class EmailActivityTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . EmailActivity::URL_COMPONENT,
            $this->mailchimp->reports(1)->emailActivity(),
            "The Email Activity collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . EmailActivity::URL_COMPONENT . 1,
            $this->mailchimp->reports(1)->emailActivity(1),
            "The Email Activity instance endpoint should be constructed correctly"
        );
    }
}
