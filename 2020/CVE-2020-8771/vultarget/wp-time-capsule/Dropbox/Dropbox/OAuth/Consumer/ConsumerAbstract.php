<?php

/**
* Abstract OAuth consumer
* @author Ben Tadiar <ben@handcraftedbyben.co.uk>
* @link https://github.com/benthedesigner/dropbox
* @package Dropbox\OAuth
* @subpackage Consumer
*/

abstract class WPTC_Dropbox_OAuth_Consumer_ConsumerAbstract
{
	// Dropbox web endpoint
	const WEB_URL = 'https://www.dropbox.com/';

	// OAuth flow methods
	const REQUEST_TOKEN_METHOD = 'oauth2/REQUEST_TOKEN_METHOD';
	const AUTHORISE_METHOD = 'oauth2/authorize';
	const ACCESS_TOKEN_METHOD = 'oauth2/token';
	const API_URL = 'https://api.dropbox.com/1/';
	const OAUTH_UPGRADE = 'oauth2/token_from_oauth1';

	/**
	 * Signature method, either PLAINTEXT or HMAC-SHA1
	 * @var string
	 */
	private $sigMethod = 'PLAINTEXT';

	/**
	 * Output file handle
	 * @var null|resource
	 */
	protected $outFile = null;

	/**
	 * Input file handle
	 * @var null|resource
	 */
	protected $inFile = null;

	/**
	 * OAuth token
	 * @var stdclass
	 */
	private $token = null;

	/**
	* Acquire an unauthorised request token
	* @link http://tools.ietf.org/html/rfc5849#section-2.1
	* @return void
	*/
	public function getRequestToken()
	{
		$url = WPTC_Dropbox_API::API_URL_V2 . self::REQUEST_TOKEN_METHOD;
		$response = $this->fetch('POST', $url, '');

		return $this->parseTokenString($response['body']);
	}

	/**
	* Build the user authorisation URL
	* @return string
	*/
	public function getAuthoriseUrl()
	{
		$params = array(
			'client_id' => WPTC_DROPBOX_CLIENT_ID,
			'response_type' => 'code',
			'redirect_uri' => WPTC_DROPBOX_REDIRECT_URL,
			'state' => WPTC_DROPBOX_WP_REDIRECT_URL,
			'force_reapprove' => 'true',
		);

		// wptc_log($params, '--------$params--------');

		// Build the URL and redirect the user
		$query = '?' . http_build_query($params, '', '&');
		$url = self::WEB_URL . self::AUTHORISE_METHOD . $query;

		return $url;
	}

	public function upgradeOAuth()
	{
		// N.B. This call only exists under API v1 - i.e. there is no APIv2 equivalent. Hence the APIv1 endpoint (API_URL) is used, and not the v2 (API_URL_V2)

		$url = self::API_URL . self::OAUTH_UPGRADE;
		$config = WPTC_Factory::get('config');
		$this->token = new stdClass();
		$this->token->oauth_token = $config->get_option('access_token');
		$this->token->oauth_token_secret  = $config->get_option('access_token_secret');
		// wptc_log(array(), '--------upgradeOAuth--------');
		// wptc_log($this->token, '--------$token upgradeOAuth--------');
		$response = $this->fetch('POST', $url, '');
		// wptc_log($response, '--------$response upgradeOAuth--------');
		return $response['body'];
	}

	/**
	 * Acquire an access token
	 * Tokens acquired at this point should be stored to
	 * prevent having to request new tokens for each API call
	 * @link http://tools.ietf.org/html/rfc5849#section-2.3
	 */
	public function getAccessToken()
	{
		// Get the signed request URL
		$response = $this->fetch('POST', WPTC_Dropbox_API::API_URL_V2, self::ACCESS_TOKEN_METHOD);
		// wptc_log($response, '--------$response getAccessToken--------');
		return $this->parseTokenString($response['body']);
	}

	/**
	 * Generate signed request URL
	 * See inline comments for description
	 * @link http://tools.ietf.org/html/rfc5849#section-3.4
	 * @param  string $method     HTTP request method
	 * @param  string $url        API endpoint to send the request to
	 * @param  string $call       API call to send
	 * @param  array  $additional Additional parameters as an associative array
	 * @return array
	 */
   protected function getSignedRequest($method, $url, $call, array $additional = array())
	{
		// wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		// Get the request/access token
		$token = $this->getToken();
		// wptc_log($token, '------- getSignedRequesttoken--------');
		// Prepare the standard request parameters differently for OAuth1 and OAuth2; we still need OAuth1 to make the request to the upgrade token endpoint
		if (isset($token->token_type)) {
			$params = array(
				'access_token' => $token->oauth_token,
			);

			/*
				To keep this API backwards compatible with the API v1 endpoints all v2 endpoints will also send to this method a api_v2 parameter this will then return just the access token as the signed request is not needed for any calls.
			 */

			if (isset($additional['api_v2']) && $additional['api_v2'] == true) {
				unset($additional['api_v2']);
				if (isset($additional['content_download']) && $additional['content_download'] == true) {
					unset($additional['content_download']);
					$headers = array(
						'Authorization: Bearer '.$params['access_token'],
						'Content-Type:',
						'Dropbox-API-Arg: '.json_encode($additional),
					);
					$additional = '';
				} else if (isset($additional['content_upload']) && $additional['content_upload'] == true) {
					unset($additional['content_upload']);
					$headers = array(
						'Authorization: Bearer '.$params['access_token'],
						'Content-Type: application/octet-stream',
						'Dropbox-API-Arg: '.json_encode($additional),
					);
					$additional = '';
				} else {
					$headers = array(
						'Authorization: Bearer '.$params['access_token'],
						/*Disabling content type because of followng error
							Error: Error in call to API function "users/get_current_account": request body: could not decode input as JSON
							Solution : Do not send json content type by default for requests
						**/
						// 'Content-Type: application/json',
					);
				}

				if (!empty($additional['headers'])) {
					$headers = array_merge($headers, $additional['headers']);
					unset($additional['headers']);
				}

				return array(
					'url' => $url . $call,
					'postfields' => $additional,
					'headers' => $headers,
				);
			}
		} else {
			if(WPTC_Factory::get('config')->get_option('dropbox_oauth_upgraded', true)){
				return false;
			}
			// Generate a random string for the request
			$nonce = md5(time() . uniqid('', true));
			$params = array(
				'oauth_consumer_key' => WPTC_DROPBOX_CLIENT_ID,
				'oauth_token' => $this->token->oauth_token,
				'oauth_signature_method' => $this->sigMethod,
				'oauth_version' => '1.0',
				// Generate nonce and timestamp if signature method is HMAC-SHA1
				'oauth_timestamp' => ($this->sigMethod == 'HMAC-SHA1') ? time() : null,
				'oauth_nonce' => ($this->sigMethod == 'HMAC-SHA1') ? $nonce : null,
			);
		}

		// Merge with the additional request parameters
		$params = array_merge($params, $additional);
		ksort($params);

		// URL encode each parameter to RFC3986 for use in the base string
		$encoded = array();
		foreach($params as $param => $value) {
			if ($value !== null) {
				// If the value is a file upload (prefixed with @), replace it with
				// the destination filename, the file path will be sent in POSTFIELDS
				if (isset($value[0]) && $value[0] === '@') $value = $params['filename'];
				# Prevent spurious PHP warning by only doing non-arrays
				if (!is_array($value)) $encoded[] = $this->encode($param) . '=' . $this->encode($value);
			} else {
				unset($params[$param]);
			}
		}

		// Build the first part of the string
		$base = $method . '&' . $this->encode($url . $call) . '&';

		// Re-encode the encoded parameter string and append to $base
		$base .= $this->encode(implode('&', $encoded));

		// Concatenate the secrets with an ampersand
		$key = WPTC_DROPBOX_CLIENT_SECRET . '&' . $this->token->oauth_token_secret;

		// Get the signature string based on signature method
		$signature = $this->getSignature($base, $key);
		$params['oauth_signature'] = $signature;

		// Build the signed request URL
		$query = '?' . http_build_query($params, '', '&');
		return array(
			'url' => $url . $call . $query,
			'postfields' => $params,
		);
	}

	/**
	 * Generate the oauth_signature for a request
	 * @param string $base Signature base string, used by HMAC-SHA1
	 * @param string $key  Concatenated consumer and token secrets
	 */
	private function getSignature($base, $key)
	{
		switch ($this->sigMethod) {
			case 'PLAINTEXT':
				$signature = $key;
				break;
			case 'HMAC-SHA1':
				$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
				break;
		}

		return $signature;
	}

	/**
	 * Set the token to use for OAuth requests
	 * @param stdtclass $token A key secret pair
	 */
	public function setToken($token)
	{
		if (!is_object($token))
			throw new Exception('Token is invalid.');

		$this->token = $token;

		return $this;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function resetToken()
	{
		$token = new stdClass;
		$token->oauth_token = false;
		$token->oauth_token_secret = false;

		$this->setToken($token);

		return $this;
	}

	/**
	 * Set the OAuth signature method
	 * @param  string $method Either PLAINTEXT or HMAC-SHA1
	 * @return void
	 */
	public function setSignatureMethod($method)
	{
		$method = strtoupper($method);

		switch ($method) {
			case 'PLAINTEXT':
			case 'HMAC-SHA1':
				$this->sigMethod = $method;
				break;
			default:
				throw new Exception('Unsupported signature method ' . $method);
		}
	}

	/**
	 * Set the output file
	 * @param resource Resource to stream response data to
	 * @return void
	 */
	public function setOutFile($handle)
	{
		if (!is_resource($handle) || get_resource_type($handle) != 'stream') {
			throw new Exception('Outfile must be a stream resource');
		}
		$this->outFile = $handle;
	}

	/**
	 * Set the input file
	 * @param resource Resource to read data from
	 * @return void
	 */
	public function setInFile($handle)
	{
		$this->inFile = $handle;
	}

	/**
	* Parse response parameters for a token into an object
	* Dropbox returns tokens in the response parameters, and
	* not a JSON encoded object as per other API requests
	* @link http://oauth.net/core/1.0/#response_parameters
	* @param string $response
	* @return object stdClass
	*/
	private function parseTokenString($response)
	{
		if (!$response)
			throw new Exception('Response cannot be null');

		$parts = explode('&', $response);
		$token = new stdClass();
		foreach ($parts as $part) {
			list($k, $v) = explode('=', $part, 2);
			$k = strtolower($k);
			$token->$k = $v;
		}

		return $token;
	}

	/**
	 * Encode a value to RFC3986
	 * This is a convenience method to decode ~ symbols encoded
	 * by rawurldecode. This will encode all characters except
	 * the unreserved set, ALPHA, DIGIT, '-', '.', '_', '~'
	 * @link http://tools.ietf.org/html/rfc5849#section-3.6
	 * @param mixed $value
	 */
	private function encode($value)
	{
		return str_replace('%7E', '~', rawurlencode($value));
	}
}
