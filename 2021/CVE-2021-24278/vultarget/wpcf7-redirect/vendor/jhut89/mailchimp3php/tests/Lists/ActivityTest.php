<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutch
 * Date: 7/2/18
 * Time: 5:11 PM
 */

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\Activity;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class ActivityTest
 * @package MailchimpTests\Lists
 */
class ActivityTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Activity::URL_COMPONENT,
            $this->mailchimp->lists(1)->activity(),
            "The Activity collection endpoint should be constructed correctly"
        );
    }
}
