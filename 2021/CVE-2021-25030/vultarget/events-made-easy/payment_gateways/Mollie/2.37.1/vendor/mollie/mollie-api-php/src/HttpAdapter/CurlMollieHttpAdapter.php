<?php

namespace Mollie\Api\HttpAdapter;

use Composer\CaBundle\CaBundle;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

final class CurlMollieHttpAdapter implements MollieHttpAdapterInterface
{
    /**
     * Default response timeout (in seconds).
     */
    const DEFAULT_TIMEOUT = 10;

    /**
     * Default connect timeout (in seconds).
     */
    const DEFAULT_CONNECT_TIMEOUT = 2;

    /**
     * HTTP status code for an empty ok response.
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * @param string $httpMethod
     * @param string $url
     * @param array $headers
     * @param $httpBody
     * @return \stdClass|void|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function send($httpMethod, $url, $headers, $httpBody)
    {
        $curl = curl_init($url);
        $headers["Content-Type"] = "application/json";

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->parseHeaders($headers));
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_CONNECT_TIMEOUT);
        curl_setopt($curl, CURLOPT_TIMEOUT, self::DEFAULT_TIMEOUT);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_CAINFO, CaBundle::getBundledCaBundlePath());

        switch ($httpMethod) {
            case MollieApiClient::HTTP_POST:
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS,  $httpBody);

                break;
            case MollieApiClient::HTTP_GET:
                break;
            case MollieApiClient::HTTP_PATCH:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $httpBody);

                break;
            case MollieApiClient::HTTP_DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS,  $httpBody);

                break;
            default:
                throw new \InvalidArgumentException("Invalid http method: ". $httpMethod);
        }

        $response = curl_exec($curl);

        if ($response === false) {
            throw new ApiException("Curl error: " . curl_error($curl));
        }

        $statusCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

        curl_close($curl);

        return $this->parseResponseBody($response, $statusCode, $httpBody);
    }

    /**
     * The version number for the underlying http client, if available.
     * @example Guzzle/6.3
     *
     * @return string|null
     */
    public function versionString()
    {
        return 'Curl/*';
    }

    /**
     * @param string $response
     * @param int $statusCode
     * @param string $httpBody
     * @return \stdClass|null
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    protected function parseResponseBody($response, $statusCode, $httpBody)
    {
        if (empty($response)) {
            if ($statusCode === self::HTTP_NO_CONTENT) {
                return null;
            }

            throw new ApiException("No response body found.");
        }

        $body = @json_decode($response);

        // GUARDS
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$response}'.");
        }

        if (isset($body->error)) {
            throw new ApiException($body->error->message);
        }

        if ($statusCode >= 400) {
            $message = "Error executing API call ({$body->status}: {$body->title}): {$body->detail}";

            $field = null;

            if (! empty($body->field)) {
                $field = $body->field;
            }

            if (isset($body->_links, $body->_links->documentation)) {
                $message .= ". Documentation: {$body->_links->documentation->href}";
            }

            if ($httpBody) {
                $message .= ". Request body: {$httpBody}";
            }

            throw new ApiException($message, $statusCode, $field);
        }

        return $body;
    }

    protected function parseHeaders($headers)
    {
        $result = [];

        foreach ($headers as $key => $value) {
            $result[] = $key .': ' . $value;
        }

        return $result;
    }
}
