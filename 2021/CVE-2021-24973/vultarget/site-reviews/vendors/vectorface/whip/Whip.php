<?php
/**
 * @package vectorface/whip v0.3.2
 */
namespace GeminiLabs\Vectorface\Whip;

use Exception;
use GeminiLabs\Vectorface\Whip\IpRange\IpWhitelist;
use GeminiLabs\Vectorface\Whip\Request\RequestAdapter;
use GeminiLabs\Vectorface\Whip\Request\SuperglobalRequestAdapter;

/**
 * A class for accurately looking up a client's IP address.
 * This class checks a call time configurable list of headers in the $_SERVER
 * superglobal to determine the client's IP address.
 * @copyright Vectorface, Inc 2015
 * @author Daniel Bruce <dbruce1126@gmail.com>
 * @author Cory Darby <ckdarby@vectorface.com>
 */
class Whip
{
    /** The whitelist key for IPv4 addresses */
    const IPV4 = IpWhitelist::IPV4;

    /** The whitelist key for IPv6 addresses */
    const IPV6 = IpWhitelist::IPV6;

    /** Indicates all header methods will be used. */
    const ALL_METHODS        = 255;
    /** Indicates the REMOTE_ADDR method will be used. */
    const REMOTE_ADDR        = 1;
    /** Indicates a set of possible proxy headers will be used. */
    const PROXY_HEADERS      = 2;
    /** Indicates any CloudFlare specific headers will be used. */
    const CLOUDFLARE_HEADERS = 4;
    /** Indicates any Incapsula specific headers will be used. */
    const INCAPSULA_HEADERS  = 8;
    /** Indicates custom listed headers will be used. */
    const CUSTOM_HEADERS     = 128;

    /** The array of mapped header strings. */
    private static $headers = array(
        self::CUSTOM_HEADERS     => array(),
        self::INCAPSULA_HEADERS  => array(
            'incap-client-ip'
        ),
        self::CLOUDFLARE_HEADERS => array(
            'cf-connecting-ip'
        ),
        self::PROXY_HEADERS      => array(
            'client-ip',
            'x-forwarded-for',
            'x-forwarded',
            'x-cluster-client-ip',
            'forwarded-for',
            'forwarded',
            'x-real-ip',
        ),
    );

    /** the bitmask of enabled methods */
    private $enabled;

    /** the array of IP whitelist ranges to check against */
    private $whitelist;

    /**
     * An object holding the source of addresses we will check
     *
     * @var RequestAdapter
     */
    private $source;

    /**
     * Constructor for the class.
     * @param int $enabled The bitmask of enabled headers.
     * @param array $whitelists The array of IP ranges to be whitelisted.
     * @param mixed $source A supported source of IP data.
     */
    public function __construct($enabled = self::ALL_METHODS, array $whitelists = array(), $source = null)
    {
        $this->enabled   = (int) $enabled;
        if (isset($source)) {
            $this->setSource($source);
        }
        $this->whitelist = array();
        foreach ($whitelists as $header => $ipRanges) {
            $header = $this->normalizeHeaderName($header);
            $this->whitelist[$header] = new IpWhitelist($ipRanges);
        }
    }

    /**
     * Adds a custom header to the list.
     * @param string $header The custom header to add.
     * @return Whip Returns $this.
     */
    public function addCustomHeader($header)
    {
        self::$headers[self::CUSTOM_HEADERS][] = $this->normalizeHeaderName($header);
        return $this;
    }

    /**
     * Sets the source data used to lookup the addresses.
     *
     * @param $source The source array.
     * @return Whip Returns $this.
     */
    public function setSource($source)
    {
        $this->source = $this->getRequestAdapter($source);

        return $this;
    }

    /**
     * Returns the IP address of the client using the given methods.
     * @param mixed $source (optional) The source data. If omitted, the class
     *        will use the value passed to Whip::setSource or fallback to
     *        $_SERVER.
     * @return string Returns the IP address as a string or false if no
     *         IP address could be found.
     */
    public function getIpAddress($source = null)
    {
        $source = $this->getRequestAdapter($this->coalesceSources($source));
        $remoteAddr = $source->getRemoteAddr();
        $requestHeaders = $source->getHeaders();

        foreach (self::$headers as $key => $headers) {
            if (!$this->isMethodUsable($key, $remoteAddr)) {
                continue;
            }

            if ($ipAddress = $this->extractAddressFromHeaders($requestHeaders, $headers)) {
                return $ipAddress;
            }
        }

        if ($remoteAddr && ($this->enabled & self::REMOTE_ADDR)) {
            return $remoteAddr;
        }

        return false;
    }

    /**
     * Returns the valid IP address or false if no valid IP address was found.
     * @param mixed $source (optional) The source data. If omitted, the class
     *        will use the value passed to Whip::setSource or fallback to
     *        $_SERVER.
     * @return string|false Returns the IP address (as a string) of the client or false
     *         if no valid IP address was found.
     */
    public function getValidIpAddress($source = null)
    {
        $ipAddress = $this->getIpAddress($source);
        if (false === $ipAddress || false === @inet_pton($ipAddress)) {
            return false;
        }
        return $ipAddress;
    }

    /**
     * Normalizes HTTP header name representations.
     *
     * HTTP_MY_HEADER and My-Header would be transformed to my-header.
     *
     * @param string $header The original header name.
     * @return string The normalized header name.
     */
    private function normalizeHeaderName($header)
    {
        if (strpos($header, 'HTTP_') === 0) {
            $header = str_replace('_', '-', substr($header, 5));
        }
        return strtolower($header);
    }

    /**
     * Finds the first element in $headers that is present in $_SERVER and
     * returns the IP address mapped to that value.
     * If the IP address is a list of comma separated values, the last value
     * in the list will be returned.
     * If no IP address is found, we return false.
     * @param array $requestHeaders The request headers to pull data from.
     * @param array $headers The list of headers to check.
     * @return string|false Returns the IP address as a string or false if no IP
     *         IP address was found.
     */
    private function extractAddressFromHeaders($requestHeaders, $headers)
    {
        foreach ($headers as $header) {
            if (!empty($requestHeaders[$header])) {
                $list = explode(',', $requestHeaders[$header]);
                return trim(end($list));
            }
        }
        return false;
    }

    /**
     * Returns whether or not the given method is enabled and usable.
     *
     * This method checks if the method is enabled and whether the method's data
     * is usable given it's IP whitelist.
     *
     * @param string $key The source key.
     * @param string $ipAddress The IP address.
     * @return boolean Returns true if the IP address is whitelisted and false
     *         otherwise. Returns true if the source does not have a whitelist
     *         specified.
     */
    private function isMethodUsable($key, $ipAddress)
    {
        if (!($key & $this->enabled)) {
            return false;
        }
        if (!isset($this->whitelist[$key])) {
            return true;
        }
        return $this->whitelist[$key]->isIpWhitelisted($ipAddress);
    }

    /**
     * Get a source/request adapter for a given source of IP data.
     *
     * @param mixed $source A supported source of request data.
     * @return RequestAdapter A RequestAdapter implementation for the given source.
     */
    private function getRequestAdapter($source)
    {
        if ($source instanceof RequestAdapter) {
            return $source;
        } elseif (is_array($source)) {
            return new SuperglobalRequestAdapter($source);
        }

        throw new \InvalidArgumentException("Unknown IP source.");
    }

    /**
     * Given available sources, get the first available source of IP data.
     *
     * @param mixed $source A source data argument, if available.
     * @return mixed The best available source, after fallbacks.
     */
    private function coalesceSources($source = null)
    {
        if (isset($source)) {
            return $source;
        } elseif (isset($this->source)) {
            return $this->source;
        }

        return $_SERVER;
    }
}
