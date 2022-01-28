<?php

namespace MailchimpTests\Settings;

use MailchimpAPI\Requests\MailchimpConnection;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;
use MailchimpTests\MailChimpTestCase;

class MailchimpSettingsTest extends MailChimpTestCase
{
    public function test_set_custom_curl_opts()
    {
        $mailchimp_request = new MailchimpRequest();
        $mailchimp_settings = $this->getMockBuilder(MailchimpSettings::class)
            ->setMethods(['getCustomCurlSettings', 'shouldVerifySsl'])
            ->getMock();

        $mailchimp_settings->method('shouldVerifySsl')
            ->willReturn(true);

        $mailchimp_settings->method('getCustomCurlSettings')
            ->willReturn([]);

        $mailchimp_settings->expects($this->once())
            ->method('getCustomCurlSettings');

        new MailchimpConnection($mailchimp_request, $mailchimp_settings);
    }
}
