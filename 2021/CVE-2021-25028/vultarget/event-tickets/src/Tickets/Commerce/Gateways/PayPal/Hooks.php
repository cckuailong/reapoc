<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( TEC\Tickets\Commerce\Gateways\PayPal\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'tickets.commerce.gateways.paypal.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( TEC\Tickets\Commerce\Gateways\PayPal\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'tickets.commerce.gateways.paypal.hooks' ), 'some_method' ] );
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Notice_Handler;
use TEC\Tickets\Commerce\Settings;
use TEC\Tickets\Commerce\Shortcodes\Shortcode_Abstract;
use TEC\Tickets\Commerce\Gateways\PayPal\Gateway;
use TEC\Tickets\Commerce\Status\Completed;

use Tribe__Utils__Array as Arr;


/**
 * Class Hooks.
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.6
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by each Tickets Commerce component.
	 *
	 * @since 5.1.6
	 */
	protected function add_actions() {
		// REST API Endpoint registration.
		add_action( 'rest_api_init', [ $this, 'register_endpoints' ] );
		add_action( 'tec_tickets_commerce_admin_process_action:paypal-disconnect', [ $this, 'handle_action_disconnect' ] );
		add_action( 'tec_tickets_commerce_admin_process_action:paypal-refresh-access-token', [ $this, 'handle_action_refresh_token' ] );
		add_action( 'tec_tickets_commerce_admin_process_action:paypal-refresh-user-info', [ $this, 'handle_action_refresh_user_info' ] );
		add_action( 'tec_tickets_commerce_admin_process_action:paypal-refresh-webhook', [ $this, 'handle_action_refresh_webhook' ] );
		add_action( 'tec_tickets_commerce_admin_process_action:paypal-resync-connection', [ $this, 'handle_action_refresh_connection' ] );

		add_action( 'tribe_template_after_include:tickets/v2/commerce/checkout/footer', [ $this, 'include_payment_buttons' ], 15, 3 );
		add_action( 'tribe_template_after_include:tickets/v2/commerce/checkout/footer', [ $this, 'include_advanced_payments' ], 20, 3 );
		add_action( 'tribe_template_after_include:tickets/v2/commerce/checkout/footer', [ $this, 'include_client_js_sdk_script' ], 30, 3 );
		add_action( 'wp_ajax_tec_tickets_commerce_gateway_paypal_refresh_connect_url', [ $this, 'ajax_refresh_connect_url' ] );
		add_action( 'admin_init', [ $this, 'render_ssl_notice' ] );

		add_action( 'tribe_template_after_include:tickets/v2/commerce/order/details/order-number', [ $this, 'include_capture_id_success_page' ], 10, 3 );
	}

	/**
	 * Adds the filters required by each Tickets Commerce component.
	 *
	 * @since 5.1.6
	 */
	protected function add_filters() {
		add_filter( 'tec_tickets_commerce_gateways', [ $this, 'filter_add_gateway' ], 10, 2 );
		add_filter( 'tec_tickets_commerce_success_shortcode_checkout_page_paypal_template_vars', [ $this, 'include_checkout_page_vars' ], 10, 2 );
		add_filter( 'tec_tickets_commerce_success_shortcode_success_page_paypal_template_vars', [ $this, 'include_success_page_vars' ], 10, 2 );
		add_filter( 'tec_tickets_commerce_notice_messages', [ $this, 'include_admin_notices' ] );
		add_filter( 'tribe-events-save-options', [ $this, 'flush_transients_when_toggling_sandbox_mode' ] );
	}

	/**
	 * Resolve the refresh of the URL when the coutry changes.
	 *
	 * @since 5.2.0
	 *
	 *
	 * @return false|string
	 */
	public function ajax_refresh_connect_url() {
		return $this->container->make( Signup::class )->ajax_refresh_connect_url();
	}

	/**
	 * Filters the shortcode template vars for the Checkout page template.
	 *
	 * @since 5.1.9
	 *
	 * @param array              $template_vars
	 * @param Shortcode_Abstract $shortcode
	 *
	 * @return array
	 */
	public function include_checkout_page_vars( $template_vars, $shortcode ) {
		$template_vars['merchant'] = tribe( Merchant::class );

		return $template_vars;
	}

	/**
	 * Filters the shortcode template vars for the Checkout page template.
	 *
	 * @since 5.1.9
	 *
	 * @param array              $template_vars
	 * @param Shortcode_Abstract $shortcode
	 *
	 * @return array
	 */
	public function include_success_page_vars( $template_vars, $shortcode ) {
		$template_vars['merchant'] = tribe( Merchant::class );

		return $template_vars;
	}

	/**
	 * Include the Client JS SDK script into checkout.
	 *
	 * @since 5.1.9
	 *
	 * @param string           $file     Which file we are loading.
	 * @param string           $name     Name of file file
	 * @param \Tribe__Template $template Which Template object is being used.
	 *
	 */
	public function include_client_js_sdk_script( $file, $name, $template ) {
		echo tribe( Buttons::class )->get_checkout_script();
	}

	/**
	 * Include the payment buttons from PayPal into the Checkout page.
	 *
	 * @since 5.1.9
	 *
	 * @param string           $file     Which file we are loading.
	 * @param string           $name     Name of file file
	 * @param \Tribe__Template $template Which Template object is being used.
	 */
	public function include_payment_buttons( $file, $name, $template ) {
		$this->container->make( Buttons::class )->include_payment_buttons( $file, $name, $template );
	}

	/**
	 * Include the advanced payment fields from PayPal in the Checkout page.
	 *
	 * @since 5.2.0
	 *
	 * @param string           $file     Which file we are loading.
	 * @param string           $name     Name of file file
	 * @param \Tribe__Template $template Which Template object is being used.
	 */
	public function include_advanced_payments( $file, $name, $template ) {
		$this->container->make( Buttons::class )->include_advanced_payments( $file, $name, $template );
	}

	/**
	 * Handles the disconnecting of the merchant.
	 *
	 * @since 5.1.9
	 *
	 * @since 5.2.0 Display info on disconnect.
	 */
	public function handle_action_disconnect() {
		$disconnected = $this->container->make( Merchant::class )->disconnect();
		$notices      = $this->container->make( Notice_Handler::class );

		if ( ! $disconnected ) {
			$notices->trigger_admin( 'tc-paypal-disconnect-failed' );

			return;
		}

		$notices->trigger_admin( 'tc-paypal-disconnected' );
	}

	/**
	 * Handles the refreshing of the token from PayPal for this merchant.
	 *
	 * @since 5.1.9
	 */
	public function handle_action_refresh_token() {
		$merchant   = $this->container->make( Merchant::class );
		$token_data = $this->container->make( Client::class )->get_access_token_from_client_credentials( $merchant->get_client_id(), $merchant->get_client_secret() );
		$notices    = $this->container->make( Notice_Handler::class );

		// Check if API response is valid for token data.
		if ( ! is_array( $token_data ) || ! isset( $token_data['access_token'] ) ) {
			$message = $notices->get_message_data( 'tc-paypal-refresh-token-failed' );
			$this->container->make( Gateway::class )->handle_invalid_response( $token_data, $message['content'] );

			return;
		}

		$saved = $merchant->save_access_token_data( $token_data );

		if ( ! $saved ) {
			$notices->trigger_admin( 'tc-paypal-refresh-token-failed' );

			return;
		}

		$notices->trigger_admin( 'tc-paypal-refresh-token' );
	}

	/**
	 * Handles the refreshing of the user info from PayPal for this merchant.
	 *
	 * @since 5.1.9
	 *
	 */
	public function handle_action_refresh_user_info() {
		$merchant  = $this->container->make( Merchant::class );
		$user_info = $this->container->make( Client::class )->get_user_info();
		$notices   = $this->container->make( Notice_Handler::class );

		// Check if API response is valid for user info.
		if ( ! isset( $user_info['user_id'] ) ) {
			$message = $notices->get_message_data( 'tc-paypal-refresh-user-info-failed' );
			$this->container->make( Gateway::class )->handle_invalid_response( $user_info, $message['content'], 'tc-invalid-user-info-response' );

			return;
		}

		$merchant->save_user_info( $user_info );

		$notices->trigger_admin( 'tc-paypal-refresh-user-info' );
	}

	/**
	 * Handles the refreshing of the webhook on PayPal for this site/merchant.
	 *
	 * @since 5.1.10
	 *
	 * @since 5.2.0 Display error|success messages.
	 */
	public function handle_action_refresh_webhook() {
		$updated = $this->container->make( Webhooks::class )->create_or_update_existing();
		$notices = $this->container->make( Notice_Handler::class );

		if ( is_wp_error( $updated ) ) {
			$content = empty( $updated->get_error_message() ) ? $updated->get_error_code() : $updated->get_error_message();
			$notices->trigger_admin( 'tc-paypal-refresh-webhook-api-error', [ 'content' => $content ] );
			$notices->trigger_admin( 'tc-paypal-refresh-webhook-failed' );

			return;
		}

		if ( ! $updated ) {
			$notices->trigger_admin( 'tc-paypal-refresh-webhook-failed' );

			return;
		}

		$notices->trigger_admin( 'tc-paypal-refresh-webhook-success' );
	}

	/**
	 * Handles the refreshing the entire connection with PayPal.
	 *
	 * @since 5.2.0
	 */
	public function handle_action_refresh_connection() {
		$this->handle_action_refresh_token();
		$this->handle_action_refresh_user_info();
		$this->handle_action_refresh_webhook();
	}

	/**
	 * Register the Endpoints from Paypal.
	 *
	 * @since 5.1.9
	 */
	public function register_endpoints() {
		$this->container->make( REST::class )->register_endpoints();
	}

	/**
	 * Add this gateway to the list of available.
	 *
	 * @since 5.1.6
	 *
	 * @param array $gateways List of available gateways.
	 *
	 * @return array
	 */
	public function filter_add_gateway( array $gateways = [] ) {
		return $this->container->make( Gateway::class )->register_gateway( $gateways );
	}

	/**
	 * Render SSL requirement notice.
	 *
	 * @since 5.2.0
	 */
	public function render_ssl_notice() {
		$page = tribe_get_request_var( 'page' );
		$tab  = tribe_get_request_var( 'tab' );

		if ( \Tribe__Settings::instance()->adminSlug !== $page || 'payments' !== $tab || is_ssl() ) {
			return;
		}

		$this->container->make( Notice_Handler::class )->trigger_admin( 'tc-paypal-ssl-not-available' );
	}

	/**
	 * Include PayPal admin notices for Ticket Commerce.
	 *
	 * @since 5.2.0
	 *
	 * @param array $messages Array of messages.
	 *
	 * @return array
	 */
	public function include_admin_notices( $messages ) {
		return array_merge( $messages, $this->container->make( Gateway::class )->get_admin_notices() );
	}

	/**
	 * Includes the Capture ID in the success page of the PayPal Gateway orders.
	 *
	 * @since 5.2.0
	 *
	 * @param string           $file     Which file we are loading.
	 * @param string           $name     The name of the file.
	 * @param \Tribe__Template $template Which Template object is being used.
	 */
	public function include_capture_id_success_page( $file, $name, $template ) {
		$order = $template->get( 'order' );

		// Bail if the order is not set to complete.
		if ( empty( $order->gateway_payload[ Completed::SLUG ] ) ) {
			return;
		}

		$capture_payload = end( $order->gateway_payload[ Completed::SLUG ] );
		$capture_id      = Arr::get( $capture_payload, [ 'purchase_units', 0, 'payments', 'captures', 0, 'id' ] );

		// Couldn't find a valid Capture ID.
		if ( ! $capture_id ) {
			return;
		}

		$template->template( 'gateway/paypal/order/details/capture-id', [ 'capture_id' => $capture_id ] );
	}

	/**
	 * Checks if the transient data needs to be flushed when saving options and deletes it if appropriate
	 *
	 * @since 5.2.0
	 *
	 * @param array $options the list of plugin options set for saving
	 *
	 * @return array
	 */
	public function flush_transients_when_toggling_sandbox_mode( $options ) {
		return $this->container->make( Signup::class )->maybe_delete_transient_data( $options );
	}
}
