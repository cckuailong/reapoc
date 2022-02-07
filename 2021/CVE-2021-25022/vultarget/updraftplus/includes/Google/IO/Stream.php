<?php
/*
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Http Streams based implementation of Google_IO.
 *
 * @author Stuart Langley <slangley@google.com>
 */

if (!class_exists('UDP_Google_Client')) {
  require_once dirname(__FILE__) . '/../autoload.php';
}

class UDP_Google_IO_Stream extends UDP_Google_IO_Abstract
{
  const TIMEOUT = "timeout";
  const ZLIB = "compress.zlib://";
  private $options = array();
  private $trappedErrorNumber;
  private $trappedErrorString;

  private static $DEFAULT_HTTP_CONTEXT = array(
    "follow_location" => 0,
    "ignore_errors" => 1,
  );

  private static $DEFAULT_SSL_CONTEXT = array(
    "verify_peer" => true,
  );

  public function __construct(UDP_Google_Client $client)
  {
    if (!ini_get('allow_url_fopen')) {
      $error = 'The stream IO handler requires the allow_url_fopen runtime ' .
               'configuration to be enabled';
      $client->getLogger()->critical($error);
      throw new UDP_Google_IO_Exception($error);
    }

    parent::__construct($client);
  }

  /**
   * Execute an HTTP Request
   *
   * @param Google_Http_Request $request the http request to be executed
   * @return array containing response headers, body, and http code
   * @throws UDP_Google_IO_Exception on curl or IO error
   */
  public function executeRequest(UDP_Google_Http_Request $request)
  {
    $default_options = stream_context_get_options(stream_context_get_default());

    $requestHttpContext = array_key_exists('http', $default_options) ?
        $default_options['http'] : array();

    if ($request->getPostBody()) {
      $requestHttpContext["content"] = $request->getPostBody();
    }

    $requestHeaders = $request->getRequestHeaders();
    if ($requestHeaders && is_array($requestHeaders)) {
      $headers = "";
      foreach ($requestHeaders as $k => $v) {
        $headers .= "$k: $v\r\n";
      }
      $requestHttpContext["header"] = $headers;
    }

    $requestHttpContext["method"] = $request->getRequestMethod();
    $requestHttpContext["user_agent"] = $request->getUserAgent();

    $requestSslContext = array_key_exists('ssl', $default_options) ?
        $default_options['ssl'] : array();

# UpdraftPlus patch
//     if (!array_key_exists("cafile", $requestSslContext)) {
//       $requestSslContext["cafile"] = dirname(__FILE__) . '/cacerts.pem';
//     }

    $url = $request->getUrl();

    if (preg_match('#^https?://([^/]+)/#', $url, $umatches)) { $cname = $umatches[1]; } else { $cname = false; }

# UpdraftPlus patch
// Added
if (empty($this->options['disable_verify_peer'])) {
	$requestSslContext['verify_peer'] = true;
	if (version_compare(PHP_VERSION, '5.6.0', '>=')) {
		if (!empty($cname)) $requestSslContext['peer_name'] = $cname;
	} else {
		if (!empty($cname)) {
			$requestSslContext['CN_match'] = $cname;
			$retry_on_fail = true;
		}
	}
} else {
	$requestSslContext['allow_self_signed'] = true;
}
if (!empty($this->options['cafile'])) $requestSslContext['cafile'] = $this->options['cafile'];

    $options = array(
        "http" => array_merge(
            self::$DEFAULT_HTTP_CONTEXT,
            $requestHttpContext
        ),
        "ssl" => array_merge(
# UpdraftPlus patch
//             self::$DEFAULT_SSL_CONTEXT,
            $requestSslContext
        )
    );

    $context = stream_context_create($options);

# UpdraftPlus patch
//     $url = $request->getUrl();

    if ($request->canGzip()) {
      $url = self::ZLIB . $url;
    }

    $this->client->getLogger()->debug(
        'Stream request',
        array(
            'url' => $url,
            'method' => $request->getRequestMethod(),
            'headers' => $requestHeaders,
            'body' => $request->getPostBody()
        )
    );

    // We are trapping any thrown errors in this method only and
    // throwing an exception.
    $this->trappedErrorNumber = null;
    $this->trappedErrorString = null;

    // START - error trap.
    set_error_handler(array($this, 'trapError'));
    $fh = fopen($url, 'r', false, $context);

# UpdraftPLus patch
    if (!$fh && isset($retry_on_fail) && !empty($cname) && 'www.googleapis.com' == $cname) {
// Reset
	$this->trappedErrorNumber = null;
	$this->trappedErrorString = null;
       global $updraftplus;
       $updraftplus->log("Using Stream, and fopen failed; retrying different CN match to try to overcome");
       // www.googleapis.com does not match the cert now being presented - *.storage.googleapis.com; presumably, PHP's stream handler isn't handling alternative names properly. Rather than turn off all verification, let's retry with a new name to match.
       $options['ssl']['CN_match'] = 'www.storage.googleapis.com';
       $context = stream_context_create($options);
       $fh = fopen($url, 'r', false, $context);
    }

    restore_error_handler();
    // END - error trap.

    if ($this->trappedErrorNumber) {
      $error = sprintf(
          "HTTP Error: Unable to connect: '%s'",
          $this->trappedErrorString
      );

      $this->client->getLogger()->error('Stream ' . $error);
      throw new UDP_Google_IO_Exception($error, $this->trappedErrorNumber);
    }

    $response_data = false;
    $respHttpCode = self::UNKNOWN_CODE;
    if ($fh) {
      if (isset($this->options[self::TIMEOUT])) {
        stream_set_timeout($fh, $this->options[self::TIMEOUT]);
      }

      $response_data = stream_get_contents($fh);
      fclose($fh);

      $respHttpCode = $this->getHttpResponseCode($http_response_header);
    }

    if (false === $response_data) {
      $error = sprintf(
          "HTTP Error: Unable to connect: '%s'",
          $respHttpCode
      );

      $this->client->getLogger()->error('Stream ' . $error);
      throw new UDP_Google_IO_Exception($error, $respHttpCode);
    }

    $responseHeaders = $this->getHttpResponseHeaders($http_response_header);

    $this->client->getLogger()->debug(
        'Stream response',
        array(
            'code' => $respHttpCode,
            'headers' => $responseHeaders,
            'body' => $response_data,
        )
    );

    return array($response_data, $responseHeaders, $respHttpCode);
  }

  /**
   * Set options that update the transport implementation's behavior.
   * @param $options
   */
  public function setOptions($options)
  {
    $this->options = $options + $this->options;
  }

  /**
   * Method to handle errors, used for error handling around
   * stream connection methods.
   */
  public function trapError($errno, $errstr)
  {
    $this->trappedErrorNumber = $errno;
    $this->trappedErrorString = $errstr;
  }

  /**
   * Set the maximum request time in seconds.
   * @param $timeout in seconds
   */
  public function setTimeout($timeout)
  {
    $this->options[self::TIMEOUT] = $timeout;
  }

  /**
   * Get the maximum request time in seconds.
   * @return timeout in seconds
   */
  public function getTimeout()
  {
    return $this->options[self::TIMEOUT];
  }

  /**
   * Test for the presence of a cURL header processing bug
   *
   * {@inheritDoc}
   *
   * @return boolean
   */
  protected function needsQuirk()
  {
    return false;
  }

  protected function getHttpResponseCode($response_headers)
  {
    $header_count = count($response_headers);

    for ($i = 0; $i < $header_count; $i++) {
      $header = $response_headers[$i];
      if (strncasecmp("HTTP", $header, strlen("HTTP")) == 0) {
        $response = explode(' ', $header);
        return $response[1];
      }
    }
    return self::UNKNOWN_CODE;
  }
}
