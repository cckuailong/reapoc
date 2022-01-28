<?php

namespace MailchimpAPI\Responses;

/**
 * Class SuccessResponse
 * @package MailchimpAPI\Responses
 */
class SuccessResponse extends MailchimpResponse
{
    /**
     * SuccessResponse constructor.
     *
     * @param array         $headers
     * @param string        $body
     * @param int           $http_code
     * @param callable|null $success_callback
     */
    public function __construct($headers, $body, $http_code, callable $success_callback = null)
    {
        parent::__construct($headers, $body, $http_code);

        if ($success_callback) {
            call_user_func($success_callback, $this);
        }
    }
}
