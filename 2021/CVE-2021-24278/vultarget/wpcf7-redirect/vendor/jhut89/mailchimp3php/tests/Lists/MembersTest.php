<?php
/**
 * Created by IntelliJ IDEA.
 * User: hutch
 * Date: 7/2/18
 * Time: 5:22 PM
 */

namespace MailchimpTests\Lists;

use MailchimpAPI\Resources\Lists\Members;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Lists;

/**
 * Class MembersTest
 * @package MailchimpTests\Lists
 */
class MembersTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT,
            $this->mailchimp->lists(1)->members(),
            "The Members collection endpoint should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Lists::URL_COMPONENT . 1 . Members::URL_COMPONENT . 1,
            $this->mailchimp->lists(1)->members(1),
            "The Members instance endpoint should be constructed correctly"
        );
    }
}
