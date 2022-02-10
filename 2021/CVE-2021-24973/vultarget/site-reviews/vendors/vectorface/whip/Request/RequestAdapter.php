<?php

namespace GeminiLabs\Vectorface\Whip\Request;

/**
 * RequestAdapter: Interface for different request formats.
 */
interface RequestAdapter
{
    /**
     * Get the remote address, as seen by this request format.
     *
     * @return string The remote address. IPv4 or IPv6, as applicable.
     */
    public function getRemoteAddr();

    /**
     * Get a key/value mapping of request headers, keys in lowercase.
     *
     * @return string[] An associative array mapping headers to values.
     */
    public function getHeaders();
}
