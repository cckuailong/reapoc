<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Client;

/**
 * Generic Response object used to parse a response from a server
 *
 * @package Bitpay
 */
class Response implements ResponseInterface
{
    /**
     * @var string
     */
    protected $raw;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var integer
     */
    protected $statusCode;

    /**
     */
    public function __construct($raw = null)
    {
        $this->headers = array();
        $this->raw     = $raw;
    }

    /**
     * Returns the raw http response
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->raw;
    }

    /**
     * @param string $rawResponse
     * @return Response
     */
    public static function createFromRawResponse($rawResponse)
    {
        $response = new self($rawResponse);
        //remove HTTP 100 responses
        $delimiter = "\r\n\r\n";// HTTP header delimiter
        //check if the 100 Continue header exists
        while (preg_match('#^HTTP/[0-9\\.]+\s+100\s+Continue#i', $rawResponse)) {
            $tmp = explode($delimiter, $rawResponse, 2);// grab the 100 Continue header
            $rawResponse = $tmp[1];// update the response, purging the most recent 100 Continue header
        }// repeat
        
        $lines    = preg_split('/(\\r?\\n)/', $rawResponse);
        $linesLen = count($lines);

        for ($i = 0; $i < $linesLen; $i++) {
            if (0 == $i) {
                preg_match('/^HTTP\/(\d\.\d)\s(\d+)\s(.+)/', $lines[$i], $statusLine);
    
                $response->setStatusCode($statusCode = $statusLine[2]);

                continue;
            }

            if (empty($lines[$i])) {
                $body = array_slice($lines, $i + 1);
                $response->setBody(implode("\n", $body));

                break;
            }

            if (strpos($lines[$i], ':') !== false) {
                $headerParts = explode(':', $lines[$i]);
                $response->setHeader($headerParts[0], $headerParts[1]);
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param integer
     *
     * @return ResponseInterface
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (integer) $statusCode;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the body of the response
     *
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function setHeader($header, $value)
    {
        $this->headers[$header] = $value;

        return $this;
    }
}
