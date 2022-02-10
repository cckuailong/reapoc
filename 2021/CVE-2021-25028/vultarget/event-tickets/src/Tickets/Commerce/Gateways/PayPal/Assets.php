<?php
/**
 * Handles registering and setup for assets on Ticket Commerce.
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Gateways\PayPal\REST\On_Boarding_Endpoint;
use TEC\Tickets\Commerce\Gateways\PayPal\REST\Order_Endpoint;

/**
 * Class Assets.
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Assets extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.6
	 */
	public function register() {
		$plugin = \Tribe__Tickets__Main::instance();
		tribe_asset(
			$plugin,
			'tribe-tickets-admin-commerce-paypal-commerce-partner-js',
			$this->get_partner_js_url(),
			[],
			'admin_enqueue_scripts',
			[
				'localize' => [
					[
						'name' => 'tribeTicketsCommercePayPaCommerce',
						'data' => [
							'translations' => [
								'confirmPaypalAccountDisconnection' => esc_html__( 'Disconnect PayPal Account', 'event-tickets' ),
								'disconnectPayPalAccount'           => esc_html__( 'Are you sure you want to disconnect your PayPal account?', 'event-tickets' ),
								'connectSuccessTitle'               => esc_html__( 'You’re connected to PayPal! Here’s what’s next...', 'event-tickets' ),
								'pciWarning'                        => sprintf(
									__(
										'PayPal allows you to accept credit or debit cards directly on your website. Because of
										this, your site needs to maintain <a href="%1$s" target="_blank">PCI-DDS compliance</a>.
										Event Tickets never stores sensitive information like card details to your server and works
										seamlessly with SSL certificates. Compliance is comprised of, but not limited to:', 'event-tickets'
									),
									// @todo Replace this URL.
									'https://www.theeventscalendar.com/documentation/resources/pci-compliance/'
								),
								'pciComplianceInstructions'         => [
									esc_html__( 'Using a trusted, secure hosting provider – preferably one which claims and actively promotes PCI compliance.', 'event-tickets' ),
									esc_html__( 'Maintain security best practices when setting passwords and limit access to your server.', 'event-tickets' ),
									esc_html__( 'Implement an SSL certificate to keep your payments secure.', 'event-tickets' ),
									esc_html__( 'Keep plugins up to date to ensure latest security fixes are present.', 'event-tickets' ),
								],
								'liveWarning'                       => tec_tickets_commerce_is_sandbox_mode()
									? esc_html__( 'You have connected your account for test mode. You will need to connect again once you are in live mode.', 'event-tickets' )
									: '',
							],
						],
					],
				],
			]
		);

		/**
		 * This file is intentionally enqueued on every page of the administration.
		 */
		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-paypal-global-admin-styles',
			'tickets-commerce/gateway/paypal/admin-global.css',
			[],
			'admin_enqueue_scripts',
			[]
		);

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-paypal-checkout',
			'commerce/gateway/paypal/checkout.js',
			[
				'jquery',
				'tribe-common',
				'tribe-tickets-loader',
				'tribe-tickets-commerce-js',
				'tribe-tickets-commerce-notice-js',
			],
			null,
			[
				'groups'       => [
					'tec-tickets-commerce-gateway-paypal',
				],
				'conditionals' => [ $this, 'should_enqueue_assets' ],
				'localize'     => [
					'name' => 'tecTicketsCommerceGatewayPayPalCheckout',
					'data' => static function () {
						return [
							'orderEndpoint' => tribe( Order_Endpoint::class )->get_route_url(),
							'advancedPayments' => [
								'fieldPlaceholders' => [
									'cvv' => esc_html__( 'E.g.: 123', 'event-tickets' ),
									'expirationDate' => esc_html__( 'E.g.: 03/26', 'event-tickets' ),
									'number' => esc_html__( 'E.g.: 4111 1111 1111 1111', 'event-tickets' ),
									'zipCode' => esc_html__( 'E.g.: 01020', 'event-tickets' ),
								]
							],
						];
					},
				],
			]
		);

		tribe_asset(
			$plugin,
			'tec-tickets-commerce-gateway-paypal-signup',
			'commerce/gateway/paypal/signup.js',
			[
				'jquery',
				'tribe-common',
				'tribe-tickets-commerce-js',
			],
			'admin_enqueue_scripts',
			[
				'conditionals' => [ $this, 'should_enqueue_assets_payments_tab' ],
				'localize'     => [
					'name' => 'tecTicketsCommerceGatewayPayPalSignup',
					'data' => static function () {
						return [
							'onboardNonce'          => wp_create_nonce( 'tec-tc-on-boarded' ),
							'refreshConnectNonce'   => wp_create_nonce( 'tec-tickets-commerce-gateway-paypal-refresh-connect-url' ),
							'onboardingEndpointUrl' => tribe( On_Boarding_Endpoint::class )->get_route_url(),
						];
					},
				],
			]
		);

		// Tickets Commerce PayPal main frontend styles.
		tribe_asset(
			$plugin,
			'tribe-tickets-commerce-paypal-style',
			'tickets-commerce/gateway/paypal.css',
			[
				'tribe-common-skeleton-style',
				'tribe-common-full-style',
			],
			null,
			[
				'groups' => [
					'tribe-tickets-commerce',
					'tribe-tickets-commerce-checkout',
				],
				'print'  => true,
			]
		);

	}

	/**
	 * Get PayPal partner JS asset url.
	 *
	 * @since 5.1.6
	 *
	 * @return string
	 */
	private function get_partner_js_url() {
		$client = tribe( Client::class );

		return sprintf(
			'%1$swebapps/merchantboarding/js/lib/lightbox/partner.js',
			$client->get_home_page_url()
		);
	}

	/**
	 * Define if the assets for `PayPal` should be enqueued or not.
	 *
	 * @since 5.1.10
	 *
	 * @return bool If the `PayPal` assets should be enqueued or not.
	 */
	public function should_enqueue_assets() {
		return tribe( Gateway::class )->is_active();
	}

	/**
	 * Define if the assets for `PayPal` should be enqueued or not.
	 *
	 * @since 5.1.10
	 *
	 * @return bool If the `PayPal` assets should be enqueued or not.
	 */
	public function should_enqueue_assets_payments_tab() {
		return 'payments' === tribe_get_request_var( 'tab' ) && \Tribe__Settings::instance()->adminSlug === tribe_get_request_var( 'page' );
	}
}
