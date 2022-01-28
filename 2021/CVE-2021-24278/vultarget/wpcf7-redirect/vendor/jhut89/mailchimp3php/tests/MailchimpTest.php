<?php

namespace MailchimpTests;

use MailchimpAPI\Settings\MailchimpSettings;
use MailchimpAPI\Requests\MailchimpRequest;

final class MailchimpTest extends MailChimpTestCase
{
    public function testAuthKeysSet()
    {
        $expected_auth_string = "Authorization: apikey " . $this->apikey;
        $mc = $this->mailchimp;
        $auth = $mc->request->getHeaders()[0];

        //ASSERTIONS
        self::assertEquals($this->apikey, $mc->apikey, "The apikey must  be set on the parent mailchimp class");
        self::assertEquals(
            $expected_auth_string,
            $auth,
            "The auth string must be correctly set"
        );
    }

    public function testGetAuthUrl()
    {
        $mc = $this->mailchimp;
        $url = $mc::getAuthUrl($this->client_id, $this->redirect_uri);

        $expected_uri = "https://login.mailchimp.com/oauth2/authorize";
        $expected_uri .= "?client_id=12345676543";
        $expected_uri .= "&redirect_uri=" . urlencode($this->redirect_uri);
        $expected_uri .= "&response_type=code";

        //ASSERTION
        $this->assertEquals($expected_uri, $url, "oAuth URI should be correctly constructed");
    }

    public function testApikeySet()
    {
        self::assertTrue(isset($this->mailchimp->apikey));
    }

    public function testRequestSet()
    {
        self::assertTrue(
            $this->mailchimp->request instanceof MailchimpRequest,
            "The request must be an instance of a MailChimpRequest"
        );
    }

    public function testSettingsSet()
    {
        self::assertTrue(
            $this->mailchimp->settings instanceof MailchimpSettings,
            "The settings must be an instance of a MailChimpSettings"
        );
    }
}
