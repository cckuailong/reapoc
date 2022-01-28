<?php

namespace MailchimpTests;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Requests\MailchimpConnection;
use MailchimpTests\TestingUtilities\StubbableMailchimp;
use PHPUnit\Framework\TestCase;
use MailchimpAPI\Mailchimp;

/**
 * Class MailChimpTestCase
 * @package MailchimpTests
 */
class MailChimpTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $apikey;
    /**
     * @var Mailchimp
     */
    protected $mailchimp;
    /**
     * @var StubbableMailchimp
     */
    protected $stub_mailchimp;
    /**
     * @var string
     */
    protected $client_id;
    /**
     * @var string
     */
    protected $redirect_uri;
    /**
     * @var \MailchimpAPI\Requests\MailchimpRequest
     */
    protected $request;
    /**
     * @var \MailchimpAPI\Settings\MailchimpSettings
     */
    protected $settings;

    /**
     * MailChimpTestCase constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     * @throws \MailchimpAPI\MailchimpException
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->apikey = "123abc123abc123abc123abc123abc12-us0";
        $this->mailchimp = new Mailchimp($this->apikey);
        $this->stub_mailchimp = $this->getStubMailchimp();
        $this->client_id =   '12345676543';
        $this->redirect_uri =  'https://www.some-domain.com/callback_file.php';
        $this->request = $this->mailchimp->request;
        $this->settings = $this->mailchimp->settings;
    }

    /**
     * @return string
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * @return StubbableMailchimp
     * @throws MailchimpException
     */
    public function getStubMailchimp()
    {
        $mockConnection = $this->createMock(MailchimpConnection::class);
        $mockConnection
            ->method("execute")
            ->willReturn("not a response");
        return new StubbableMailchimp($this->apikey, $mockConnection);
    }

    /**
     * @param $expected_endpoint
     * @param $chain_to_be_tested
     * @param $message
     */
    protected function endpointUrlBuildTest($expected_endpoint, $chain_to_be_tested, $message)
    {
        $expected_url = $this->expectedUrl($expected_endpoint);
        $mc = $chain_to_be_tested;
        self::assertEquals(
            $expected_url,
            $mc->getRequest()->getUrl(),
            $message
        );
    }

    /**
     * @param $endpoint
     * @return string
     */
    private function expectedUrl($endpoint)
    {
        return $this->request->getBaseUrl() . $endpoint;
    }
}
