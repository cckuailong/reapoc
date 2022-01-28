<?php

namespace MailchimpTests\UtilityTests;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpTests\MailChimpTestCase;

final class MailchimpRequestTest extends MailChimpTestCase
{
    private $requestInstance;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->requestInstance = new MailchimpRequest($this->apikey);
    }

    public function testAuthHeadersSet()
    {
        $expected = 'Authorization: apikey ' . $this->apikey;
        $actual = $this->requestInstance->getHeaders()[0];

        self::assertEquals($expected, $actual, "Auth Header should be appropriately set");
    }

    public function testApikeySet()
    {
        self::assertEquals(
            $this->apikey,
            $this->requestInstance->getApikey(),
            "The request API Key should be set correctly"
        );
    }

    public function testBaseUrlSet()
    {
        $expected = "Https://" . explode('-', trim($this->apikey))[1] . ".api.mailchimp.com/3.0";
        $actual = $this->requestInstance->getBaseUrl();

        self::assertEquals($expected, $actual, "Base Url should be appropriately set");
    }

    /**
     * @expectedException
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCheckKeyFailure()
    {
        $this->expectException(MailchimpException::class);
        $this->requestInstance->checkKey('not an API key');
    }

    public function testPersistentCallbacks()
    {
        $foo = function () {
            // do stuff
        };

        // set success callback
        $this->mailchimp->request->setSuccessCallback($foo);
        // set failure callback
        $this->mailchimp->request->setFailureCallback($foo);

        // from same request make a new AuthorizedApps
        $apps = $this->mailchimp->apps();

        self::assertEquals(
            $foo,
            $apps->getRequest()->getSuccessCallback(),
            "the success callback after making a chain should be the same as when it was set"
        );

        self::assertEquals(
            $foo,
            $apps->getRequest()->getFailureCallback(),
            "the failure callback after making a chain should be the same as when it was set"
        );
    }
}
