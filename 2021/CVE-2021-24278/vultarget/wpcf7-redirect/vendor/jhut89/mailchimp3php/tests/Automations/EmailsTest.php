<?php

namespace MailchimpTests\Automations;

use MailchimpAPI\Resources\Automations;
use MailchimpAPI\Resources\Automations\Emails;
use MailchimpTests\MailChimpTestCase;

/**
 * Class EmailsTest
 * @package MailchimpTests\Automations
 */
class EmailsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1 . Emails::URL_COMPONENT,
            $this->mailchimp->automations(1)->emails(),
            "The Emails collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1 . Emails::URL_COMPONENT . 1,
            $this->mailchimp->automations(1)->emails(1),
            "The Emails instance endpoint should be constructed correctly"
        );
    }
}
