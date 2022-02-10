<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

/**
 * Generic Request object used to send requests to servers
 *
 * @package Bitpay
 */
class Request implements RequestInterface
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * $schema://$hostname/$path
     *
     * @var string
     */
    protected $uri;

    /**
     * See the RequestInterface for all the valid methods
     *
     * @var string
     */
    protected $method;

    /**
     * This should be something such as `test.bitpay.com` or just `bitpay.com`
     *
     * @var string
     */
    protected $host;

    /**
     * The path is added to the end of the host
     *
     * @var string
     */
    protected $path;

    /**
     * Default is 443 but should be changed by whatever is passed in through the Adapter. 
     *
     * @var integer
     */
    protected $port;

    /**
     */
    public function __construct()
    {
        // Set some sane default headers
        $this->headers = array(
            'Content-Type'         => 'application/json',
            'X-BitPay-Plugin-Info' => null,
        );

        $this->port = 443;

        // Default method is POST
        $this->method = self::METHOD_POST;
    }

    /**
     * Converts this request into a standard HTTP/1.1 message to be sent over
     * the wire
     *
     * @return string
     */
    public function __toString()
    {
        $request = sprintf("%s %s HTTP/1.1\r\n", $this->getMethod(), $this->getUriWithPort());
        $request .= $this->getHeadersAsString();
        $request .= $this->getBody();

        return trim($request);
    }

    /**
     * @inheritdoc
     */
    public function isMethod($method)
    {
        return (strtoupper($method) == strtoupper($this->method));
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * This is called in the Adapter
     *
     * @inheritdoc
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @inheritdoc
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the method of the request, for known methods see the
     * RequestInterface
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @inheritdoc
     */
    public function getSchema()
    {
        return 'https';
    }

    /**
     * @inheritdoc
     */
    public function getUri()
    {
        return sprintf(
            '%s://%s/%s',
            $this->getSchema(),
            $this->getHost(),
            $this->getPath()
        );
    }

    /**
     * @inheritdoc
     */
    public function getUriWithPort()
    {
        return sprintf(
            '%s://%s:%s/%s',
            $this->getSchema(),
            $this->getHost(),
            $this->getPort(),
            $this->getPath()
        );
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the host for the request
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        // remove invalid headers
        $headers = $this->headers;
        foreach ($headers as $header => $value) {
            if (empty($header) || empty($value)) {
                unset($headers[$header]);
            }
        }

        return $headers;
    }

    public function getHeaderFields()
    {
        $fields = array();
        foreach ($this->getHeaders() as $header => $value) {
            $fields[] = sprintf('%s: %s', $header, $value);
        }

        return $fields;
    }

    /**
     * @return string
     */
    public function getHeadersAsString()
    {
        $headers = $this->getHeaders();
        $return  = '';

        foreach ($headers as $h => $v) {
            $return .= sprintf("%s: %s\r\n", $h, $v);
        }

        return $return."\r\n";
    }

    /**
     * Set a http header for the request
     *
     * @param string $header
     * @param string $value
     */
    public function setHeader($header, $value)
    {
        if (is_array($value)) {
            throw new \Exception('Could not set the header: '.$header);
        }
        $this->headers[$header] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * The the body of the request
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        $this->setHeader('Content-Length', strlen($body));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $host
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }
}
