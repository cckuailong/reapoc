<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

use TEC\Tickets\Commerce\Communication\Email;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Status_Interface;
use TEC\Tickets\Commerce\Ticket;
use Tribe__Utils__Array as Arr;

/**
 * Class Increase_Stock, normally triggered when refunding on orders get set to not-completed.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Flag_Actions
 */
class Send_Email extends Flag_Action_Abstract {
	/**
	 * {@inheritDoc}
	 */
	protected $flags = [
		'send_email',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $post_types = [
		Order::POSTTYPE
	];

	/**
	 * {@inheritDoc}
	 */
	public function handle( Status_Interface $new_status, $old_status, \WP_Post $order ) {

		// temporary fix for manual attendees first email
		// @todo backend review this logic
		if ( ! empty( $order->gateway ) && 'manual' === $order->gateway && empty( $order->events_in_order ) ) {
			$order->events_in_order[] = $order;
		}


		if ( empty( $order->events_in_order ) || ! is_array( $order->events_in_order ) ) {
			return;
		}

		foreach ( $order->events_in_order as $event_id ) {
			$event = get_post( $event_id );
			if ( ! $event instanceof \WP_Post ) {
				continue;
			}

			/**
			 * If this request is being generated via ajax in the Attendees View admin page, we need to
			 * make sure the email only goes out after all the work to register the order and attendees is
			 * finished, so we hook it to the same hook used to process everything, but make sure it's the last
			 * function to run.
			 *
			 * @todo TribeLegacyCommerce
			 */
			if ( doing_action( 'wp_ajax_tribe_tickets_admin_manager' ) ) {
				add_filter( 'tribe_tickets_admin_manager_request', static function( $response ) use ( $order, $event ) {
					tribe( Email::class )->send_tickets_email( $order->ID, $event->ID );
					return $response;
				}, 9999 );
				return;
			}

			tribe( Email::class )->send_tickets_email( $order->ID, $event->ID );
		}
	}
}
