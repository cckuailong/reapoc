<?php

namespace OpenCloud\Common\Exceptions;

use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class HttpResponseException extends \Exception
{
    protected $response;
    protected $request;

    /**
     * Set the request that caused the exception
     *
     * @param RequestInterface $request Request to set
     *
     * @return RequestException
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Get the request that caused the exception
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the response that caused the exception
     *
     * @param Response $response Response to set
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the response that caused the exception
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
