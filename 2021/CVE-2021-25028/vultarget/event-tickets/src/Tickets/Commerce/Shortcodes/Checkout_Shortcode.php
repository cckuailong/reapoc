<?php
/**
 * Shortcode [tec_tickets_checkout].
 *
 * @since   5.1.9
 * @package TEC\Tickets\Commerce
 */

namespace TEC\Tickets\Commerce\Shortcodes;

use TEC\Tickets\Commerce\Checkout;
use TEC\Tickets\Commerce\Cart;
use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Completed;
use TEC\Tickets\Commerce\Status\Created;
use Tribe__Tickets__Editor__Template;
use TEC\Tickets\Commerce\Utils\Price;

use TEC\Tickets\Commerce\Gateways\Manager;
use TEC\Tickets\Commerce\Gateways\PayPal\Merchant;

/**
 * Class for Shortcode Tribe_Tickets_Checkout.
 *
 * @since   5.1.9
 * @package Tribe\Tickets\Shortcodes
 */
class Checkout_Shortcode extends Shortcode_Abstract {

	/**
	 * Id of the current shortcode for filtering purposes.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $shortcode_id = 'checkout';

	/**
	 * {@inheritDoc}
	 */
	public function setup_template_vars() {
		$items      = tribe( Cart::class )->get_items_in_cart( true );
		$sections   = array_unique( array_filter( wp_list_pluck( $items, 'event_id' ) ) );
		$sub_totals = array_filter( wp_list_pluck( $items, 'sub_total' ) );

		$args = [
			'provider_id'        => Module::class,
			'provider'           => tribe( Module::class ),
			'items'              => $items,
			'sections'           => $sections,
			'total_value'        => tribe_format_currency( Price::total( $sub_totals ) ),
			'must_login'         => ! is_user_logged_in() && tribe( Module::class )->login_required(),
			'login_url'          => tribe( Checkout::class )->get_login_url(),
			'registration_url'   => tribe( Checkout::class )->get_registration_url(),
			'is_tec_active'      => defined( 'TRIBE_EVENTS_FILE' ) && class_exists( 'Tribe__Events__Main' ),
			'gateways'           => tribe( Manager::class )->get_gateways(),
			'gateways_active'    => $this->get_gateways_active(),
			'gateways_connected' => $this->get_gateways_connected(),
		];

		$this->template_vars = $args;
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_html() {
		$context = tribe_context();

		if ( is_admin() && ! $context->doing_ajax() ) {
			return '';
		}

		$args = $this->get_template_vars();

		// Add the rendering attributes into global context.
		$this->get_template()->add_template_globals( $args );

		return $this->get_template()->template( 'checkout', $args, false );
	}

	/**
	 * Get the number of active gateways.
	 *
	 * @since 5.1.10
	 *
	 * @return int The number of active gateways.
	 */
	public function get_gateways_active() {
		$gateways        = tribe( Manager::class )->get_gateways();
		$gateways_active = array_filter( array_map( static function ( $gateway ) {
			return $gateway::is_active() && $gateway::should_show() ? $gateway : null;
		}, $gateways ) );

		return count( $gateways_active );
	}

	/**
	 * Get the number of connected gateways.
	 *
	 * @since 5.2.0
	 *
	 * @return int The number of connected gateways.
	 */
	public function get_gateways_connected() {
		$gateways = tribe( Manager::class )->get_gateways();

		$gateways_connected = array_filter( array_map( static function ( $gateway ) {
			return $gateway::is_connected() && $gateway::should_show() ? $gateway : null;
		}, $gateways ) );

		return count( $gateways_connected );
	}

	/**
	 * Enqueue the assets related to this shortcode, static method to avoid having to generate a new instance.
	 *
	 * @since 5.2.0
	 */
	public static function enqueue_assets() {
		// Enqueue assets.
		tribe_asset_enqueue_group( 'tribe-tickets-commerce-checkout' );
	}

}
