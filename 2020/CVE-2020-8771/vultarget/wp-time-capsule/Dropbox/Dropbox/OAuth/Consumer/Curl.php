<?php

/**
 * OAuth consumer using PHP cURL
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @package Dropbox\OAuth
 * @subpackage Consumer
 */
class WPTC_Dropbox_OAuth_Consumer_Curl extends WPTC_Dropbox_OAuth_Consumer_ConsumerAbstract {

	/**
	 * Default cURL options
	 * @var array
	 */

	protected $defaultOptions = array(
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_VERBOSE        => true,
		CURLOPT_HEADER         => true,
		CURLINFO_HEADER_OUT    => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => false,
	);

	/**
	 * Store the last response form the API
	 * @var mixed
	 */
	protected $lastResponse = null;

	/**
	 * Set properties and begin authentication
	 * @param string $key
	 * @param string $secret
	 */
	public function __construct($key, $secret) {
		// Check the cURL extension is loaded
		if (!extension_loaded('curl')) {
			throw new Exception('The cURL OAuth consumer requires the cURL extension');
		}

		$this->consumerKey = $key;
		$this->consumerSecret = $secret;
	}

	/**
	 * Execute an API call
	 * @todo Improve error handling
	 * @param  string        $method     The HTTP method
	 * @param  string        $url        The API endpoint
	 * @param  string        $call       The API method to call
	 * @param  array         $additional Additional parameters
	 * @return string|object stdClass
	 */


 public function fetch($method, $url, $call, array $additional = array(), $isChunkDownload = array()) {
		// Get the signed request URL
		$request = $this->getSignedRequest($method, $url, $call, $additional);

		if (!empty($additional['headers'])) {
			unset($additional['headers']);
		}

		if ($request === false) {
			throw new Exception("Upgrade failed", 401);
		}
		// Initialise and execute a cURL request
		$handle = curl_init($request['url']);

		// Get the default options array
		$options = $this->defaultOptions;
		$options[CURLOPT_CAINFO] = dirname(__FILE__) . '/ca-bundle.pem';

		//Disabling this as of now
		// if (get_option('updraft_ssl_disableverify')) {
		// 	$options[CURLOPT_SSL_VERIFYPEER] = false;
		// } else {
		// 	$options[CURLOPT_SSL_VERIFYPEER] = true;
		// }
		// if (!defined('WPTC_BRIDGE')) {
		if (!defined('WPTC_BRIDGE')) {
			if (!class_exists('WP_HTTP_Proxy')){
				if (!defined('WPTC_BRIDGE')) {
					require_once(ABSPATH.WPINC.'/class-http.php');
				} else {
					throw new Exception("WP_HTTP_Proxy Class not foound", 500);
				}
			}
			$proxy = new WP_HTTP_Proxy();

			if ($proxy->is_enabled()) {
				# WP_HTTP_Proxy returns empty strings if nothing is set
				$user = $proxy->username();
				$pass = $proxy->password();
				$host = $proxy->host();
				$port = (int)$proxy->port();
				if (empty($port)) $port = 8080;
				if (!empty($host) && $proxy->send_through_proxy($request['url'])) {
					  $options[CURLOPT_PROXY] = $host;
					  $options[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
					  $options[CURLOPT_PROXYPORT] = $port;
					  if (!empty($user) && !empty($pass)) {
							$options[CURLOPT_PROXYAUTH] = CURLAUTH_ANY;
							$options[CURLOPT_PROXYUSERPWD] = sprintf('%s:%s', $user, $pass);
					  }
				}
			}
		}
		if (isset($request['headers'])) $options[CURLOPT_HTTPHEADER] = $request['headers'];

		/*
			Add check to see if it's an API v2 call if so then json encode the contents. This is so that it is backwards compatible with API v1 endpoints.
		 */
		if (isset($additional['api_v2']) && !empty($request['postfields'])) {
			$request['postfields'] = json_encode($request['postfields']);
		}

		if($call == '2/users/get_current_account'){
			$request['postfields'] = json_encode(null);
			array_push($request['headers'], "Content-Type: application/json");

			if (isset($request['headers'])) $options[CURLOPT_HTTPHEADER] = $request['headers'];
		}

		if ($method == 'GET' && $this->outFile) { // GET
			// wptc_log(array(), '--------GET REQUEST AND DOWNOAD FILE--------');
			$options[CURLOPT_RETURNTRANSFER] = false;
			$options[CURLOPT_HEADER] = false;
			$options[CURLOPT_FILE] = $this->outFile;
			$options[CURLOPT_BINARYTRANSFER] = true;
			$options[CURLOPT_FAILONERROR] = true;
			/*
				Not sure if this is used, keeping it here for backwards compatibility at the moment.
				With API v2 the headers are set in the $request they are set above if they are set.
			 */
			if (isset($additional['headers'])) $options[CURLOPT_HTTPHEADER] = $additional['headers'];
			$this->outFile = null;

			if (!empty($isChunkDownload)) {
				//am adding the chunked download details here
				$c_offset = /* 0;	 */$isChunkDownload['c_offset'];
				$c_limit = /* 1024000;	 */$isChunkDownload['c_limit'];
				$this_range = '' . $c_offset . '-' . $c_limit . '';
				$options[CURLOPT_RANGE] = $this_range;
				// wptc_log($options, '--------$options--------');
			}
		}  elseif ($method == 'POST' && $this->outFile) { // POST request for download a file
			// wptc_log(array(), '--------POST REQUEST AND FILE DOWNLOAD--------');
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_RETURNTRANSFER] = false;
			$options[CURLOPT_HEADER] = false;
			$options[CURLOPT_FILE] = $this->outFile;
			$options[CURLOPT_BINARYTRANSFER] = true;
			$options[CURLOPT_FAILONERROR] = true;
			$this->outFile = null;
		} elseif ($method == 'POST' && $this->inFile) { // POST request for upload a file
			// wptc_log(array(), '--------POST REQUEST AND FILE UPLOAD--------');
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = $this->inFile;
		} elseif ($method == 'POST') { // POST request
			// wptc_log(array(), '--------POST REQUEST--------');
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = $request['postfields'];
		} elseif ($method == 'PUT' && $this->inFile) { // PUT request
			// wptc_log(array(), '--------PUT REQUEST--------');
			$options[CURLOPT_PUT] = true;
			$options[CURLOPT_INFILE] = $this->inFile;
			// @todo Update so the data is not loaded into memory to get its size
			$options[CURLOPT_INFILESIZE] = strlen(stream_get_contents($this->inFile));
			fseek($this->inFile, 0);
			$this->inFile = null;
		}


		// Set the cURL options at once
		curl_setopt_array($handle, $options);

		// Execute, get any error and close
		$response = curl_exec($handle);
		$error = curl_error($handle);
		$getinfo = curl_getinfo($handle);

		curl_close($handle);

		//Check i cURL failed because of server issue.
		if ($response === false || WPTC_Base_Factory::get('Wptc_App_Functions')->is_retry_allowed_curl_status($getinfo['http_code'])) {
			throw new WPTC_Dropbox_CurlException($error);
		} else {
			// Parse the response if it is a string
			if (is_string($response)) {
				$response = $this->parse($response);
			}

			// Set the last response
			$this->lastResponse = $response;

			$code = (!empty($response['code'])) ? $response['code'] : $getinfo['http_code'];

			// The API doesn't return an error message for the 304 status code...
			// 304's are only returned when the path supplied during metadata calls has not been modified
			if ($code == 304) {
				$response['body'] = new stdClass;
				$response['body']->error = 'The folder contents have not changed';
			}

			// Check if an error occurred and throw an Exception
			if (!empty($response['body']->error) || $code >= 400) {
				// Dropbox returns error messages inconsistently...
				if (!empty($response['body']->error) && $response['body']->error instanceof stdClass) {
					$array = array_values((array) $response['body']->error);
					//Dropbox API v2 only throws 409 errors if this error is a incorrect_offset then we need the entire error array not just the message. PHP Exception messages have to be a string so JSON encode the array.
					if (is_array($array) && @strpos($array[0] , 'incorrect_offset') !== false) {
						$message = json_encode($array);
					} elseif (is_array($array) && @strpos($array[0] , 'lookup_failed') !== false ) {
						//re-structure the array so it is correctly formatted for API
						//Note: Dropbox v2 returns different errors at different stages hence this fix
						$correctOffset = array(
							'0' => $array[1]->{'.tag'},
							'1' => $array[1]->correct_offset
						);

						$message = json_encode($correctOffset);
					} else {
						$message = $array[0];
					}
				} elseif (!empty($response['body']->error)) {
					$message = $response['body']->error;
				} elseif (is_string($response['body'])) {
					// 31 Mar 2017 - This case has been found to exist; though the docs imply that there's always an 'error' property and that what is returned in JSON, we found a case of this being returned just as a simple string, but detectable via an HTTP 400: Error in call to API function "files/upload_session/append_v2": HTTP header "Dropbox-API-Arg": cursor.offset: expected integer, got string
					$message = $response['body'];
				} else {
					$message = "HTTP bad response code: $code";
				}

				// Throw an Exception with the appropriate with the appropriate message and code
				switch ($code) {
					case 304:
						throw new WPTC_Dropbox_NotModifiedException($message, 304);
					case 400:
						throw new WPTC_Dropbox_BadRequestException($message, 400);
					case 404:
						throw new WPTC_Dropbox_NotFoundException($message, 404);
					case 406:
						throw new WPTC_Dropbox_NotAcceptableException($message, 406);
					case 415:
						throw new WPTC_Dropbox_UnsupportedMediaTypeException($message, 415);
					case 401:
						//401 means oauth token is expired continue to manually handle the exception depending on the situation
						throw new WPTC_Dropbox_TokenExpired($message, 401);
						// continue;
					case 409:
						//409 in API V2 every error will return with a 409 to find out what the error is the error description should be checked.
						throw new WPTC_Dropbox_Exception($message, $code);
					default:
						throw new WPTC_Dropbox_Exception($message, $code);
				}
			}

			return $response;
		}
	}
	/**
	 * Parse a cURL response
	 * @param  string $response
	 * @return array
	 */
	private function parse($response)
	{
		// Explode the response into headers and body parts (separated by double EOL)
		list($headers, $response) = explode("\r\n\r\n", $response, 2);

		// Explode response headers
		$lines = explode("\r\n", $headers);

		// If the status code is 100, the API server must send a final response
		// We need to explode the response again to get the actual response
		if (preg_match('#^HTTP/1.1 100#i', $lines[0])) {
			list($headers, $response) = explode("\r\n\r\n", $response, 2);
			$lines = explode("\r\n", $headers);
		}

		// Get the HTTP response code from the first line
		$first = array_shift($lines);
		$pattern = '#^HTTP/1.1 ([0-9]{3})#i';
		preg_match($pattern, $first, $matches);
		$code = $matches[1];

		// Parse the remaining headers into an associative array
		$headers = array();
		foreach ($lines as $line) {
			list($k, $v) = explode(': ', $line, 2);
			$headers[strtolower($k)] = $v;
		}

		// If the response body is not a JSON encoded string
		// we'll return the entire response body
		if (!$body = json_decode($response)) {
			$body = $response;
		}

		 if (is_string($body)) {
			 $body_lines = explode("\r\n", $body);
			 if (preg_match('#^HTTP/1.1 100#i', $body_lines[0]) && preg_match('#^HTTP/1.#i', $body_lines[2])) {
			 return $this->parse($body);
			 }
		 }

		return array('code' => $code, 'body' => $body, 'headers' => $headers);
	}

	/**
	 * Return the response for the last API request
	 * @return mixed
	 */
	public function getlastResponse() {
		return $this->lastResponse;
	}
}
