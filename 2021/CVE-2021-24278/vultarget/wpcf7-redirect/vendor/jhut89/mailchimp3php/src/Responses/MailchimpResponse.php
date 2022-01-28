<?php

namespace MailchimpAPI\Responses;

use MailchimpAPI\MailchimpException;

/**
 * Class MailchimpResponse
 * @package MailchimpAPI\Responses
 */
abstract class MailchimpResponse
{
    /**
     * The headers returned from a request
     * @var array
     */
    private $headers = [];

    /**
     * The body returned from a request
     * @var string
     */
    private $body;

    /**
     * The http response code for a request
     * @var int
     */
    private $http_code;

    /**
     * MailchimpResponse constructor.
     *
     * @param $headers
     * @param $body
     * @param $http_code
     */
    public function __construct($headers, $body, $http_code)
    {
        $this->setHeaders($headers);
        $this->setBody($body);
        $this->setHttpCode($http_code);
    }

    /**
     * Get the response headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets the headers on this response object
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Gets the http code for this response object
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * Sets the http response code for this response object
     *
     * @param mixed $http_code
     */
    public function setHttpCode($http_code)
    {
        $this->http_code = $http_code;
    }

    /**
     * Sets the body for this response object
     *
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Gets the body for this response object
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Deserializes the response body to PHP object or array
     *
     * @param bool $to_array should we deserialize to an array
     *
     * @return mixed
     * @throws MailchimpException when cant deserialize response
     */
    public function deserialize($to_array = false)
    {
        $decoded = json_decode($this->body, (bool)$to_array);
        if (!$decoded) {
            throw new MailchimpException("Unable to deserialize response");
        }
        return $decoded;
    }

    /**
     * Return whether $this is a SuccessResponse
     * @return bool
     */
    public function wasSuccess()
    {
        return $this instanceof SuccessResponse;
    }

    /**
     * Return whether $this is a SuccessResponse
     * @return bool
     */
    public function wasFailure()
    {
        return $this instanceof FailureResponse;
    }
}
