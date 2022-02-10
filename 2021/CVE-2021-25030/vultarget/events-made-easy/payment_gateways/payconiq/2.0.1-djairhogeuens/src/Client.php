<?php

namespace Payconiq;

use Payconiq\Support\Exceptions\CreatePaymentFailedException;
use Payconiq\Support\Exceptions\RetrievePaymentFailedException;
use Payconiq\Support\Exceptions\GetPaymentsListFailedException;

class Client
{

	const ENVIRONMENT_PROD = 'prod';
	const ENVIRONMENT_EXT = 'ext';

	protected $apiKey;
	protected $endpoint;

	/**
	 * Construct
	 *
	 * @param  string $apiKey		Used to secure request between merchant backend and Payconiq backend.
	 * @param  string $environment	Environment to use when making API calls
	 * 
	 * @return void
	 */
	public function __construct($apiKey = null, $environment = self::ENVIRONMENT_PROD)
	{
		$this->apiKey = $apiKey;
		$this->endpoint = $environment == self::ENVIRONMENT_PROD ? 'https://api.payconiq.com/v3' : 'https://api.ext.payconiq.com/v3';
	}

	/**
	 * Set the endpoint
	 *
	 * @param  string $url  The endpoint of the Payconiq API.
	 *
	 * @return self
	 */
	public function setEndpoint($url)
	{
		$this->endpoint = $url;

		return $this;
	}

	/**
	 * Set the endpoint to test env
	 *
	 * @return self
	 */
	public function setEndpointTest()
	{
		$this->endpoint = 'https://api.ext.payconiq.com/v3';

		return $this;
	}

	/**
	 * Set the API key
	 *
	 * @param  string $apiKey  Used to secure request between merchant backend and Payconiq backend.
	 *
	 * @return self
	 */
	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;

		return $this;
	}

	/**
	 * Create a new payment
	 * 
	 * @param  float $amount		Payment amount in cents
	 * @param  string $currency		Payment currency code in IOS 4217 format
	 * @param  string $reference	External payment reference used to reference the Payconiq payment in the calling party's system
	 * @param  string $callbackUrl  A url to which the merchant or partner will be notified of a payment
	 * @param  string $returnUrl  Return url to return client after paying on payconiq site itself (optional)
	 * 
	 * @return object  payment object
	 * @throws CreatePaymentFailedException  If the response has no transactionid
	 */
	public function createPayment($amount, $currency = 'EUR', $description='', $reference='', $callbackUrl='', $returnUrl = null)
	{
		$data_arr = [
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
			'reference' => $reference,
                        'callbackUrl' => $callbackUrl,
                ];
		if (!empty($returnUrl))
			$data_arr['returnUrl'] = $returnUrl;
		$response = $this->curl('POST', $this->getEndpoint('/payments'), $this->constructHeaders(), $data_arr);

		if (empty($response->paymentId))
			throw new CreatePaymentFailedException($response->message);

		return $response;
	}

	/**
	 * Get payment details of an existing payment
	 *
	 * @param  string $paymentId  The unique Payconiq identifier of a payment as provided by the create payment service
	 *
	 * @return  object  Response object by Payconiq
	 */
	public function retrievePayment($paymentId)
	{
		$response = $this->curl('GET', $this->getEndpoint('/payments/' . $paymentId), $this->constructHeaders());

		if (empty($response->paymentId))
			throw new RetrievePaymentFailedException($response->message);

		return $response;
	}

	/**
	 * Get payments list
	 *
	 * @param  string $reference	External payment reference used to reference the Payconiq payment in the calling party's system
	 * 
	 * @return  array  Response objects by Payconiq
	 */
	public function getPaymentsList($reference)
	{
		$response = $this->curl('POST', $this->getEndpoint('/payments/search'), $this->constructHeaders(), [
			'reference' => $reference
		]);

		if (empty($response->size))
			throw new GetPaymentsListFailedException($response->message);

		return $response->details;
	}

	/**
	 * Get the endpoint for the call
	 *
	 * @param  string $route
	 */
	private function getEndpoint($route = null)
	{
		return $this->endpoint . $route;
	}

	/**
	 * Construct the headers for the cURL call
	 * 
	 * @return array
	 */
	private function constructHeaders()
	{
		return [
			'Content-Type: application/json',
			'Cache-Control: no-cache',
			'Authorization: Bearer ' . $this->apiKey
		];
	}

	/**
	 * cURL request
	 *
	 * @param  string $method
	 * @param  string $url
	 * @param  array $headers
	 * @param  array $parameters
	 *
	 * @return response
	 */
	private static function cURL($method, $url, $headers = [], $parameters = [])
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_VERBOSE, 0);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($parameters));

		$response = curl_exec($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		curl_close($curl);

		return json_decode($body);
	}
}
