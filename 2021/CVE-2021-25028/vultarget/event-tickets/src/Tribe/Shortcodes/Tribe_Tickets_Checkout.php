<?php
/**
 * Shortcode [tribe_tickets_checkout].
 *
 * @since   5.1.6
 * @package Tribe\Tickets\Shortcodes
 */

namespace Tribe\Tickets\Shortcodes;

use TEC\Tickets\Commerce\Checkout;
use Tribe\Shortcode\Shortcode_Abstract;
use TEC\Tickets\Commerce\Gateways\PayPal\Merchant;
use TEC\Tickets\Commerce\Gateways\PayPal\Settings;
use Tribe__Tickets__Editor__Template;

/**
 * Class for Shortcode Tribe_Tickets_Checkout.
 *
 * @since   5.1.6
 * @package Tribe\Tickets\Shortcodes
 */
class Tribe_Tickets_Checkout extends Shortcode_Abstract {

	/**
	 * {@inheritDoc}
	 */
	protected $slug = 'tribe_tickets_checkout';

	/**
	 * {@inheritDoc}
	 */
	public function get_html() {
		$context = tribe_context();

		if ( is_admin() && ! $context->doing_ajax() ) {
			return '';
		}

		/** @var Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		$merchant = tribe( Merchant::class );

		$data = tribe( Checkout::class )->prepare_data_for_template( $_POST );

		$post     = get_post( $data['post_id'] );
		$is_event = 'tribe_events' === $post->post_type;
		$event    = null;
		if ( $is_event && function_exists( 'tribe_get_event' ) ) {
			$event = tribe_get_event( $post );
		}

		$args = [
			'merchant' => $merchant,
			'post'     => $post,
			'event'    => $event,
			'provider' => $data['provider'],
			'tickets'  => $data['tickets'],
		];

		$args['paypal_attribution_id'] = \TEC\Tickets\Commerce\Gateways\PayPal\Gateway::ATTRIBUTION_ID;

		// Add the rendering attributes into global context.
		$template->add_template_globals( $args );

		// Enqueue assets.
		tribe_asset_enqueue_group( 'tribe-tickets-commerce-checkout' );

		return $template->template( 'v2/tickets/commerce/checkout', $args, false );
	}

}
