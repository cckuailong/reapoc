<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * @since 4.8.1
 */
abstract class SpinnerApiAbstract extends Core\Plugin\ComponentAbstract implements SpinnerApiInterface
{
    /**
     * Spins content using a remote API.
     *
     * @since 4.8.1
     * @param mixed $content
     * @param array $options Options for spinning.
     * @return mixed
     */
    public function spin($content, $options = array())
    {
        $uri = $this->getQueryUri($options);
        $response = $this->query($uri, $content, $options);

        return $response;
    }

    /**
     * Happens immediately before the spin request is sent to the remote API.
     *
     * @since 4.8.1
     * @param mixed $request The request that is about to be sent.
     * @param string $uri The URI, to which the request is about to be sent.
     * @return mixed The new request, possibly modified
     */
    protected function _beforeQuery($request, $uri = null)
    {
        return $request;
    }

    /**
     * Happens immediately after the spin request is sent to the remote API.
     *
     * @since 4.8.1
     * @param mixed $response The response that was received.
     * @param string $uri The URI, to which the request was sent.
     * @return mixed The new response, possibly modified
     */
    protected function _afterQuery($response, $uri = null)
    {
        return $response;
    }

    /**
     * Sends the spinning query to the remote API.
     *
     * @since 4.8.1
     * @param string $uri The URI to send the query to.
     * @param mixed $request The request to send.
     * @return mixed The query response.
     */
    public function query($uri, $request, $options = array())
    {
        $request = $this->_beforeQuery($request, $uri);
        $response = $this->_sendRequest($uri, $request);
        $response = $this->_afterQuery($response, $uri);
        
        return $response;
    }

    /**
     * Does the actual sending of the request.
     *
     * @since 4.8.1
     * @return mixed The low-level API response.
     */
    abstract protected function _sendRequest($uri, $request);

    /**
     * Get the actual URI, to which the spin request will be sent.
     *
     * @since 4.8.1
     * @return string The complete URI, to which a spin request should be sent.
     */
    abstract public function getQueryUri($options = array());
}