<?php

namespace MailchimpTests\TestingUtilities;


use MailchimpAPI\Mailchimp;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;
use MailchimpAPI\Requests\MailchimpConnection;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class StubbableMailchimp
 *
 * Used when stubs are needed for testing without network requests
 *
 * @package MailchimpTests
 */
class StubbableMailchimp extends Mailchimp
{

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mocked_connection;


    /**
     * StubableMailchimp constructor.
     * @param $request
     * @param $mocked_connection
     * @throws \MailchimpAPI\MailchimpException
     */
    public function __construct($request, $mocked_connection)
    {
        parent::__construct($request);
        $this->mocked_connection = $mocked_connection;
    }


    /**
     * Overrides getConnection() in order to return a mocked connection
     *
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @return MailchimpConnection|PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getConnection(MailchimpRequest $request, MailchimpSettings $settings)
    {
        return $this->mocked_connection;
    }


    /**
     * Overrides getStaticConnection() in order to return a mocked connection
     *
     * @param MailchimpRequest $request
     * @return MailchimpConnection|PHPUnit_Framework_MockObject_MockObject
     */
    protected static function getStaticConnection(MailchimpRequest $request)
    {
        $test_case = new MailChimpTestCase();
        $mock_builder = new MockBuilder($test_case, MailchimpConnection::class);
        $mocked_connection = $mock_builder
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mocked_connection
            ->method('execute')
            ->willReturn('not a response');
        return $mocked_connection;
    }
}
