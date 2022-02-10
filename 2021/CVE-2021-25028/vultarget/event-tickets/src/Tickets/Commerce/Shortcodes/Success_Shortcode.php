<?php
/**
 * Shortcode [tec_tickets_success].
 *
 * @since   5.1.9
 * @package TEC\Tickets\Commerce
 */

namespace TEC\Tickets\Commerce\Shortcodes;

use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Completed;
use TEC\Tickets\Commerce\Success;

/**
 * Class for Shortcode Tribe_Tickets_Checkout.
 *
 * @since   5.1.9
 * @package Tribe\Tickets\Shortcodes
 */
class Success_Shortcode extends Shortcode_Abstract {

	/**
	 * Id of the current shortcode for filtering purposes.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $shortcode_id = 'success';

	/**
	 * {@inheritDoc}
	 */
	public function setup_template_vars() {
		$order_id = tribe_get_request_var( Success::$order_id_query_arg );
		$order    = tec_tc_orders()->by_args( [
			'status'           => 'any',
			'gateway_order_id' => $order_id,
		] )->first();

		$args = [
			'provider_id'    => Module::class,
			'provider'       => tribe( Module::class ),
			'order_id'       => $order_id,
			'order'          => $order,
			'is_tec_active'  => defined( 'TRIBE_EVENTS_FILE' ) && class_exists( 'Tribe__Events__Main' ),
			'payment_method' => tribe( Order::class )->get_gateway_label( $order ),
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

		// Bail if we're in the blocks editor context.
		if ( $context->doing_rest() ) {
			return '';
		}

		$args = $this->get_template_vars();

		// Add the rendering attributes into global context.
		$this->get_template()->add_template_globals( $args );

		$this->enqueue_assets();

		$html = $this->get_template()->template( 'success', $args, false );

		return $html;
	}

	/**
	 * Enqueue the assets related to this shortcode.
	 *
	 * @since 5.2.0
	 */
	public static function enqueue_assets() {
		$context = tribe_context();

		// Bail if we're in the blocks editor context.
		if ( $context->doing_rest() ) {
			return;
		}

		// Enqueue assets.
		tribe_asset_enqueue_group( 'tribe-tickets-commerce' );
	}

}
