<?php


namespace MailchimpTests\Responses;


use MailchimpAPI\Responses\FailureResponse;
use PHPUnit\Framework\TestCase;

class FailureResponseTest extends TestCase
{
    public function testFailureCallback()
    {
        $called = false;

        $callback = function () use (&$called) {
            $called = true;
        };

        (new FailureResponse([], '', 200, $callback));
        $this->assertTrue($called == true, 'The callback should be called by FailureResponse');
    }
}