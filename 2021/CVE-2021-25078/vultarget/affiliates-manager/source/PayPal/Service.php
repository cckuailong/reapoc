<?php
/**
 * @author John Hargrove
 *
 * Date: 1/2/11
 * Time: 11:35 PM
 */

require_once "MassPayRequest.php";
require_once "ServiceException.php";
require_once "Response.php";

class WPAM_PayPal_Service
{
	const
		PAYPAL_API_ENDPOINT_SANDBOX = 'https://api-3t.sandbox.paypal.com/nvp',
		PAYPAL_API_ENDPOINT_LIVE = 'https://api-3t.paypal.com/nvp';
	
	private $apiUser;
	private $apiPassword;
	private $apiSignature;
	private $apiEndPoint;

	public function __construct($apiEndPoint, $apiUser, $apiPassword, $apiSignature)
	{
		$this->apiUser = $apiUser;
		$this->apiPassword = $apiPassword;
		$this->apiSignature = $apiSignature;
		$this->apiEndPoint = $apiEndPoint;
	}

	public function doMassPay(WPAM_PayPal_MassPayRequest $request)
	{
		$response = $this->executeRequest('MassPay', $request->getFields());

		return $response;
	}

	private function executeRequest($method, array $fields)
	{
		$currency = WPAM_MoneyHelper::getCurrencyCode();
		
		$fields = array_merge(
			$fields,
			array(
				'USER' => $this->apiUser,
				'PWD' => $this->apiPassword,
				'VERSION' => '65.0',
				'SIGNATURE' => $this->apiSignature,
				'METHOD' => $method,
				'CURRENCYCODE' => $currency
			)
		);

                WPAM_Logger::log_debug('PayPal MassPay post data:');
                WPAM_Logger::log_debug_array($fields);
		$postData = http_build_query($fields, NULL, '&');
		$response = $this->executePayPalRequest($postData);
                WPAM_Logger::log_debug('PayPal MassPay response:');
                WPAM_Logger::log_debug_array($response);
		return new WPAM_PayPal_Response($response);
	}

	private function executePayPalRequest( $postData )
	{
                $args = array(
			'body'        => $postData,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'AffiliatesManager/' . WPAM_VERSION
		);
		$response = wp_safe_remote_post( $this->apiEndPoint, $args );

		if ( is_wp_error( $response ) ) {
			throw new WPAM_PayPal_ServiceException( sprintf( __( "POST failed\nerrors:\n%serrordata:\n%s", 'affiliates-manager' ), print_r($response->errors, true), print_r($response->error_data, true) ) );
		} elseif ( isset( $response['response']['code'] ) && $response['response']['code'] == 200 ) {
			return $response['body'];
		}

		throw new WPAM_PayPal_ServiceException( sprintf( __( 'Unknown response: %s', 'affiliates-manager' ), print_r( $response, true ) ) );
	}
}
