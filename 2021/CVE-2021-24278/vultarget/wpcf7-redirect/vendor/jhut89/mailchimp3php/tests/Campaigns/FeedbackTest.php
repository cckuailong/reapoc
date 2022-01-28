<?php

namespace MailchimpTests\Campaigns;

use MailchimpAPI\Resources\Campaigns;
use MailchimpAPI\Resources\Campaigns\Feedback;
use MailchimpTests\MailChimpTestCase;


/**
 * Class FeedbackTest
 * @package MailchimpTests\Campaigns
 */
class FeedbackTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT . 1 . Feedback::URL_COMPONENT,
            $this->mailchimp->campaigns(1)->feedback(),
            "The Feedback collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Campaigns::URL_COMPONENT . 1 . Feedback::URL_COMPONENT . 1,
            $this->mailchimp->campaigns(1)->feedback(1),
            "The Feedback instance endpoint should be constructed correctly"
        );
    }
}
