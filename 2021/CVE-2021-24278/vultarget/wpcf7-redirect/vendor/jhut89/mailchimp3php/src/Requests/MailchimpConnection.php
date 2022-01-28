<?php

namespace MailchimpAPI\Requests;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Responses\FailureResponse;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\Responses\SuccessResponse;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class MailchimpConnection
 * @package MailchimpAPI\Requests
 */
class MailchimpConnection implements HttpRequest
{

    /**
     * Custom user agent for this library
     */
    const USER_AGENT = 'jhut89/Mailchimp-API-3.0-PHP (https://github.com/Jhut89/Mailchimp-API-3.0-PHP)';

    /**
     * The url used to request an access token from mailchimp
     */
    const TOKEN_REQUEST_URL = 'https://login.mailchimp.com/oauth2/token';

    /**
     * The url used to request metadata about an access token
     */
    const OAUTH_METADATA_URL = 'https://login.mailchimp.com/oauth2/metadata/';

    /**
     * The current request object passed into this connection
     * @var MailchimpRequest
     */
    private $current_request;

    /**
     * The current settings object passed into this connection
     * @var MailChimpSettings
     */
    private $current_settings;

    /**
     * Raw response from mailchimp api
     * @var string
     */
    private $response;

    /**
     * Response body
     * @var string
     */
    private $response_body;

    /**
     * An integer representation of the http response code
     * @var int
     */
    private $http_code;

    /**
     * The parsed response headers from the request
     * @var array
     */
    private $headers = [];

    /**
     * The curl handle for this connection
     * @var resource
     */
    private $handle;

    /**
     * A holder for the option that are set on this connections handle
     * @var array
     */
    private $current_options = [];


    /**
     * MailchimpConnection constructor.
     *
     * @param MailchimpRequest       $request
     * @param MailchimpSettings|null $settings
     *
     * @throws MailchimpException
     */
    public function __construct(MailchimpRequest &$request, MailchimpSettings &$settings = null)
    {
        $this->current_request = $request;

        $settings ?
            $this->current_settings = $settings :
            $this->current_settings = new MailchimpSettings();

        $this->handle = curl_init();

        $this->prepareHandle();
        $this->setHandlerOptionsForMethod();
    }

    /**
     * Prepares this connections handle for execution
     *
     * @return void
     *
     * @throws MailchimpException
     */
    private function prepareHandle()
    {
        // set the URL for this request
        $this->setOption(CURLOPT_URL, $this->current_request->getUrl());

        // set headers to be sent
        $this->setOption(CURLOPT_HTTPHEADER, $this->current_request->getHeaders());

        // set custom user-agent
        $this->setOption(CURLOPT_USERAGENT, self::USER_AGENT);

        // make response returnable
        $this->setOption(CURLOPT_RETURNTRANSFER, true);

        // get headers in return
        $this->setOption(CURLOPT_HEADER, true);

        // set verify ssl
        $this->setOption(CURLOPT_SSL_VERIFYPEER, $this->current_settings->shouldVerifySsl());

        // set the callback to run against each of the response headers
        $this->setOption(CURLOPT_HEADERFUNCTION, [&$this, "parseResponseHeader"]);

        // if an custom curl settings are present set them now
        $this->setCustomHandleOptions($this->current_settings->getCustomCurlSettings());
    }

    /**
     * Set custom curl handler options
     *
     * @param array $options
     */
    private function setCustomHandleOptions(array $options)
    {
        if (!empty($options)) {
            foreach ($options as $option => $value) {
                $this->setOption($option, $value);
            }
        }
    }

    /**
     * Prepares the handler for a request based on the requests method
     * @return void
     */
    private function setHandlerOptionsForMethod()
    {
        $method = $this->current_request->getMethod();

        switch ($method) {
            case MailchimpRequest::POST:
                $this->setOption(CURLOPT_POST, true);
                $this->setOption(CURLOPT_POSTFIELDS, $this
                    ->current_request
                    ->getPayload());
                break;
            case MailchimpRequest::PUT:
            case MailchimpRequest::PATCH:
                $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
                $this->setOption(CURLOPT_POSTFIELDS, $this
                    ->current_request
                    ->getPayload());
                break;
            case MailchimpRequest::DELETE:
                $this->setOption(CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
    }

    /**
     * Executes a connection with the current request and settings
     *
     * @param bool $close close this connection after execution
     *
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function execute($close = true)
    {
        $this->response = $this->executeCurl();
        if (!$this->response) {
            throw new MailchimpException("The curl request failed: " . $this->getError());
        }

        $this->http_code = $this->getInfo(CURLINFO_HTTP_CODE);
        $head_len = $this->getInfo(CURLINFO_HEADER_SIZE);
        $this->response_body = substr(
            $this->response,
            $head_len,
            strlen($this->response)
        );

        if ($close) {
            $this->close();
        }

        if ($this->isSuccess()) {
            return new SuccessResponse(
                $this->headers,
                $this->response_body,
                $this->http_code,
                $this->current_request->getSuccessCallback()
            );
        } else {
            return new FailureResponse(
                $this->headers,
                $this->response_body,
                $this->http_code,
                $this->current_request->getFailureCallback()
            );
        }
    }

    /**
     * Gets the currently set curl options by key
     *
     * @param $key
     *
     * @return mixed
     */
    public function getCurrentOption($key)
    {
        return $this->current_options[$key];
    }

    /**
     * Bulk set curl options
     * Update current settings
     *
     * @param array $options
     */
    public function setCurrentOptions($options)
    {
        $this->current_options = [];
        foreach ($options as $option_name => $option_value) {
            $this->setOption($option_name, $option_value);
        }
    }

    /**
     * Sets a curl option on the handler
     * Updates the current settings array with ne setting
     * @inheritdoc
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
        $this->current_options[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function executeCurl()
    {
        return curl_exec($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    /**
     * @return string
     */
    public function getError()
    {
        return curl_error($this->handle);
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        curl_close($this->handle);
    }

    /**
     * Called statically during prepareHandle();
     *
     * @param $handle
     * @param $header
     *
     * @return int
     */
    private function parseResponseHeader($handle, $header)
    {
        $header_length = strlen($header);
        $header_array = explode(':', $header, 2);
        if (count($header_array) == 2) {
            $this->pushToHeaders($header_array);
        }

        return $header_length;
    }

    /**
     * @param array $header
     */
    private function pushToHeaders($header)
    {
        $this->headers[$header[0]] = trim($header[1]);
    }

    /**
     * A function for evaluating if a connection was successful
     * @return bool
     */
    private function isSuccess()
    {
        return ($this->http_code > 199 && $this->http_code < 300);
    }
}
