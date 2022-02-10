<?php

namespace Paymill\API;

use \Exception;

/**
 * It's incorrect to test for the function itself. Since we know exactly when the
 * json_decode function was introduced. So we test the PHP version instead.
 */
if (version_compare(PHP_VERSION, '5.2.0', '<')) {
    throw new \Exception('Your PHP version is too old: install the PECL JSON extension');
} else if (!function_exists('json_decode')) {
    throw new \Exception('The JSON extension is missing: install it.');
}

/**
 * Check if the cURL extension is enabled.
 *
 */
if (!extension_loaded('curl')) {
    throw new \Exception('Please install the PHP cURL extension');
}

/**
 * Curl
 */
class Curl extends CommunicationAbstract
{

    private $_apiKey;
    private $_apiUrl;
    private $_extraOptions;

    /**
     * cURL HTTP client constructor
     *
     * @param string $apiKey
     * @param string $apiEndpoint
     * @param array $extracURL
     *   Extra cURL options. The array is keyed by the name of the cURL
     *   options.
     */
    public function __construct($apiKey, $apiEndpoint = 'https://api.paymill.com/v2.1/', array $extracURL = array())
    {
        $this->_apiKey = $apiKey;
        $this->_apiUrl = $apiEndpoint;
        /**
         * Proxy support. The proxy can be SOCKS5 or HTTP.
         * Also the connection could be tunneled through.
         */
        $this->_extraOptions = $extracURL;
    }

    /**
     * Perform HTTP request to REST endpoint
     *
     * @param string $action
     * @param array $params
     * @param string $method
     * @return array
     */
    public function requestApi($action = '', $params = array(), $method = 'POST')
    {
        $curlOpts = array(
            CURLOPT_URL => $this->_apiUrl . $action,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_USERAGENT => 'Paymill-php/0.0.2',
            CURLOPT_SSL_VERIFYPEER => true
        );

        // Add extra options to cURL if defined.
        if (!empty($this->_extraOptions)) {
            $curlOpts = $this->_extraOptions + $curlOpts;
        }

        if ('GET' === $method || 'DELETE' === $method) {
            if (0 !== count($params)) {
                $curlOpts[CURLOPT_URL] .= false === strpos($curlOpts[CURLOPT_URL], '?') ? '?' : '&';
                $curlOpts[CURLOPT_URL] .= http_build_query($params, null, '&');
            }
        } else {
            $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }

        if ($this->_apiKey) {
            $curlOpts[CURLOPT_USERPWD] = $this->_apiKey . ':';
        }
        $curl = curl_init();
        $this->_curlOpts($curl, $curlOpts);
        $responseBody = $this->_curlExec($curl);
        $responseInfo = $this->_curlInfo($curl);

        if ($responseBody === false) {
            $responseBody = array('error' => $this->_curlError($curl));
        }
        curl_close($curl);

        if ('application/json' === $responseInfo['content_type']) {
            $responseBody = json_decode($responseBody, true);
        } elseif (strpos(strtolower($responseInfo['content_type']), 'text/csv') !== false
            && !isset($responseBody['error'])
        ) {
            return $responseBody;
        }

        $result = array(
            'header' => array(
                'status' => $responseInfo['http_code'],
                'reason' => null,
            ),
            'body' => $responseBody
        );

        return $result;
    }

    /**
     * Wraps the curl_setopt_array function call
     *
     * @param resource $curl curl resource handle
     * @param array $curlOpts array containing curl options
     * @return bool
     */
    protected function _curlOpts($curl, array $curlOpts)
    {
        return curl_setopt_array($curl, $curlOpts);
    }

    /**
     * Wraps the curlExec function call
     * @param resource $curl cURL handle passed to curl_exec
     * @return mixed
     */
    protected function _curlExec($curl)
    {
        return curl_exec($curl);
    }

    /**
     * Wraps the curlInfo function call
     * @param resource $curl cURL handle passed to curl_getinfo
     * @return mixed
     */
    protected function _curlInfo($curl)
    {
        return curl_getinfo($curl);
    }

    /**
     * Wraps the curlError function call
     * @param resource $curl cURL handle passed to curl_error
     * @return mixed
     */
    protected function _curlError($curl)
    {
        return curl_error($curl);
    }
}
