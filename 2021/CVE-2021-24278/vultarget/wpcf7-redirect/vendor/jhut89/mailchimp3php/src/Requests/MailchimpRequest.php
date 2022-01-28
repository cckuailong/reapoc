<?php

namespace MailchimpAPI\Requests;

use MailchimpAPI\MailchimpException;

/**
 * Class MailchimpRequest
 *
 * A class for structuring a request for
 * and MailChimp API
 *
 * @package Mailchimp_API\Requests
 */
class MailchimpRequest
{
    /**
     * GET
     */
    const GET = "GET";

    /**
     * POST
     */
    const POST = "POST";

    /**
     * PUT
     */
    const PUT = "PUT";

    /**
     * PATCH
     */
    const PATCH = "PATCH";

    /**
     * DELETE
     */
    const DELETE = "DELETE";

    /**
     * An array of valid methods that can be used for a request
     * @var array
     */
    private static $valid_methods = [self::GET, self::POST, self::PATCH, self::PUT, self::DELETE];

    /*************************************
     * Request Components
     *************************************/

    /**
     * The base URL for a request
     * @var string
     */
    private $base_url;

    /**
     * The endpoint portion of the URL
     * @var string
     */
    private $endpoint;

    /**
     * The query string for the request URL
     * @var string
     */
    private $query_string;


    /**
     * The exploded API key
     * @var array
     */
    private $exp_apikey;

    /**
     * The API key used to set the auth header
     * @var null
     */
    private $apikey;

    /**
     * The payload that is serialized and sent
     * @var object
     */
    private $payload;

    /**
     * The method for this request
     * @var string
     */
    private $method;

    /**
     * The headers for this request
     * @var array
     */
    private $headers;

    /**
     * The success callback to be executed on a successful request
     * @var callable
     */
    private $success_callback;

    /**
     * The failure callback to be executed on a failed request
     * @var callable
     */
    private $failure_callback;

    /**
     * MailchimpRequest constructor.
     *
     * @param $apikey
     *
     * @throws MailchimpException
     */
    public function __construct($apikey = null)
    {
        if (!$apikey) {
            $this->setHeaders([]);
            return;
        }

        $this->apikey = $apikey;
        $this->exp_apikey = explode('-', trim($apikey));
        $this->setAuth();
        $this->checkKey($this->exp_apikey);
        $data_center = $this->exp_apikey[1];

        $this->setBaseUrl("Https://" . $data_center . ".api.mailchimp.com/3.0");
    }

    /*************************************
     * GETTERS
     *************************************/

    /**
     * Get the APi key
     * @return mixed
     */
    public function getApikey()
    {
        return $this->apikey;
    }

    /**
     * Get the endpoint
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * Get the exploded api key
     * @return mixed
     */
    public function getExpApikey()
    {
        return $this->exp_apikey;
    }

    /**
     * Get the payload
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Get the method
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the base URL
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Get the valid methods
     * @return array
     */
    public function getValidMethods()
    {
        return self::$valid_methods;
    }

    /**
     * Get the headers
     * @return array
     * @throws MailchimpException
     */
    public function getHeaders()
    {
        if (!is_array($this->headers)) {
            throw new MailchimpException("Request headers must be of type array");
        }
        return $this->headers;
    }

    /**
     * Gets the entire request URI
     * @return string
     */
    public function getUrl()
    {
        return $this->base_url . $this->endpoint . $this->query_string;
    }

    /**
     * Get the success callback
     * @return callable
     */
    public function getSuccessCallback()
    {
        return $this->success_callback;
    }

    /**
     * Get the failure callback
     * @return callable
     */
    public function getFailureCallback()
    {
        return $this->failure_callback;
    }

    /*************************************
     * SETTERS
     *************************************/

    /**
     * Set the api key
     *
     * @param mixed $apikey
     */
    public function setApikey($apikey)
    {
        $this->apikey = $apikey;
    }

    /**
     * Sets the Authorization header for this request
     */
    public function setAuth()
    {
        if (!is_array($this->headers)) {
            $this->setHeaders([]);
        }
        array_push($this->headers, 'Authorization: apikey ' . $this->apikey);
    }

    /**
     * Sets the payload for a request
     *
     * @param mixed   $payload
     * @param boolean $shouldSerialize
     *
     * @throws MailchimpException when cant serialize payload
     */
    public function setPayload($payload, $shouldSerialize = true)
    {
        if ($shouldSerialize) {
            $payload = $this->serializePayload($payload);
        }
        $this->payload = $payload;
    }

    /**
     * Sets the endpoint for the request
     *
     * @param mixed $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Sets the request method
     *
     * @param mixed $method
     *
     * @throws MailchimpException
     */
    public function setMethod($method)
    {
        if (!in_array($method, self::$valid_methods)) {
            throw new MailchimpException("Method not allowed");
        }

        $this->method = $method;
    }

    /**
     * Sets the base URL
     *
     * @param mixed $base_url
     */
    public function setBaseUrl($base_url)
    {
        $this->base_url = $base_url;
    }

    /**
     * Sets the query string from an array
     *
     * @param array $query_array
     */
    public function setQueryString($query_array)
    {
        $this->query_string = $this->constructQueryParams($query_array);
    }

    /**
     * Sets the request headers
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Sets the success callback
     *
     * @param callable $success_callback
     */
    public function setSuccessCallback(callable $success_callback)
    {
        $this->success_callback = $success_callback;
    }

    /**
     * Sets the failure callback
     *
     * @param callable $failure_callback
     */
    public function setFailureCallback(callable $failure_callback)
    {
        $this->failure_callback = $failure_callback;
    }

    /*************************************
     * Helpers
     *************************************/

    /**
     * JSON serializes the current payload
     *
     * @param $payload
     *
     * @return mixed
     * @throws MailchimpException
     */
    public function serializePayload($payload)
    {
        $encoded = json_encode($payload);

        if (!$encoded) {
            throw new MailchimpException("Unable to serialize payload");
        }

        return $encoded;
    }

    /**
     * Construct a query string from an array
     *
     * @param array $query_input
     *
     * @return string
     */
    public function constructQueryParams($query_input)
    {
        $query_string = '?';
        foreach ($query_input as $parameter => $value) {
            $encoded_value = urlencode($value);
            $query_string .= $parameter . '=' . $encoded_value . '&';
        }
        $query_string = trim($query_string, '&');
        return $query_string;
    }

    /**
     * Adds a new header
     *
     * @param string $header_string
     */
    public function addHeader($header_string)
    {
        if (!is_array($this->headers)) {
            $this->headers = [];
        }
        array_push($this->headers, $header_string);
    }

    /**
     * Pushes a string to the end of the current endpoint
     *
     * @param string $string
     */
    public function appendToEndpoint($string)
    {
        $this->endpoint = $this->endpoint .= $string;
    }

    /**
     * Checks for a valid API key
     *
     * @param $exp_apikey
     *
     * @throws MailchimpException
     */
    public function checkKey($exp_apikey)
    {

        if (strlen($exp_apikey[0]) < 10) {
            throw new MailchimpException('You must provide a valid API key');
        }

        if (!isset($exp_apikey[1])) {
            throw new MailchimpException(
                'You must provided the data-center at the end of your API key'
            );
        }
    }

    /**
     * Returns a new request using the same APIkey
     * @throws MailchimpException
     */
    public function reset()
    {
        $apikey = $this->apikey;
        $request_vars = get_object_vars($this);
        foreach ($request_vars as $key => $value) {
            if (in_array($key, ['success_callback', 'failure_callback'], true)) {
                continue;
            }
            
            $this->$key = null;
            if ('headers' === $key) {
                $this->$key = [];
            }
        }
        self::__construct($apikey);
    }
}
