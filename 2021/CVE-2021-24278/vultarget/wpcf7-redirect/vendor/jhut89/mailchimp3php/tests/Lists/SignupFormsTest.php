<?php

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\SignupForms;
use MailchimpAPI\Resources\Lists;
use MailchimpTests\MailChimpTestCase;

/**
 * Class SignupFormsTest
 * @package MailchimpTests\Lists
 */
class SignupFormsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . SignupForms::URL_COMPONENT,
            $this->mailchimp->lists(1)->signupForms(),
            "The Signup Forms collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . SignupForms::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->signupForms(1),
            "The Signup Forms instance endpoint should be constructed correctly"
        );
    }
}
