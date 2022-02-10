<?php

namespace IP2Proxy;

/**
 * IP2Proxy web service class.
 */
class WebService
{
	/**
	 * No cURL extension found.
	 *
	 * @var int
	 */
	private const EXCEPTION_NO_CURL = 10001;

	/**
	 * Invalid API key format.
	 *
	 * @var int
	 */
	private const EXCEPTION_INVALID_API_KEY = 10002;

	/**
	 * Web service error.
	 *
	 * @var int
	 */
	private const EXCEPTION_WEB_SERVICE_ERROR = 10003;

	/**
	 * Constructor.
	 *
	 * @param string $apiKey  API key of your IP2Proxy web service
	 * @param string $package Supported IP2Proxy package from PX1 to PX10
	 * @param bool   $useSsl  Enable or disabled HTTPS connection. HTTP is faster but less secure.
	 *
	 * @throws \Exception
	 */
	public function __construct($apiKey, $package = 'PX1', $useSsl = true)
	{
		if (!\extension_loaded('curl')) {
			throw new \Exception(__CLASS__ . ": Please make sure your PHP setup has the 'curl' extension enabled.", self::EXCEPTION_NO_CURL);
		}

		if (!preg_match('/^[0-9A-Z]{10}$/', $apiKey) && $apiKey != 'demo') {
			throw new \Exception(__CLASS__ . ': Please provide a valid IP2Proxy web service API key.', self::EXCEPTION_INVALID_API_KEY);
		}

		if (!preg_match('/^PX[0-9]+$/', $package)) {
			$package = 'PX1';
		}

		$this->apiKey = $apiKey;
		$this->package = $package;
		$this->useSsl = $useSsl;
	}

	/**
	 * This function will look the given IP address up in IP2Proxy web service.
	 *
	 * @param string $ip IP address to look up
	 *
	 * @throws \Exception
	 *
	 * @return array|false
	 */
	public function lookup($ip)
	{
		$response = $this->httpRequest('http' . (($this->useSsl) ? 's' : '') . '://api.ip2proxy.com/?' . http_build_query([
			'key'     => $this->apiKey,
			'ip'      => $ip,
			'package' => $this->package,
		]));

		if (($data = json_decode($response, true)) === null) {
			return false;
		}

		if ($data['response'] != 'OK') {
			throw new \Exception(__CLASS__ . ': ' . $data['response'], self::EXCEPTION_WEB_SERVICE_ERROR);
		}

		return $data;
	}

	/**
	 * Get the remaing credit in your IP2Proxy web service account.
	 *
	 * @return int
	 */
	public function getCredit()
	{
		$response = $this->httpRequest('http' . (($this->useSsl) ? 's' : '') . '://api.ip2proxy.com/?' . http_build_query([
			'key'   => $this->apiKey,
			'check' => true,
		]));

		if (($data = json_decode($response, true)) === null) {
			return 0;
		}

		if (!isset($data['response'])) {
			return 0;
		}

		return $data['response'];
	}

	/**
	 * Open a remote web address.
	 *
	 * @param string $url Website URL
	 *
	 * @return bool|string
	 */
	private function httpRequest($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$response = curl_exec($ch);

		if (!curl_errno($ch)) {
			curl_close($ch);

			return $response;
		}

		curl_close($ch);

		return false;
	}
}
