<?php


namespace MailchimpTests\Responses;


use MailchimpAPI\Responses\SuccessResponse;
use PHPUnit\Framework\TestCase;

class SuccessResponseTest extends TestCase
{
    public function testSuccessCallback()
    {
        $called = false;

        $callback = function () use (&$called) {
            $called = true;
        };

        (new SuccessResponse([], '', 200, $callback));
        $this->assertTrue($called == true, 'The callback should be called by SuccessResponse');
    }
}