<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

use TEC\Tickets\Commerce\Attendee;
use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Settings;
use TEC\Tickets\Commerce\Status\Pending;
use TEC\Tickets\Commerce\Status\Status_Abstract;
use TEC\Tickets\Commerce\Status\Status_Handler;
use TEC\Tickets\Commerce\Status\Status_Interface;
use TEC\Tickets\Commerce\Ticket;
use Tribe__Utils__Array as Arr;

/**
 * Class Decrease_Stock
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Flag_Actions
 */
class Decrease_Stock extends Flag_Action_Abstract {
	/**
	 * {@inheritDoc}
	 */
	protected $flags = [
		'decrease_stock',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $post_types = [
		Order::POSTTYPE
	];
	/**
	 * Hooks any WordPress filters related to this Flag Action.
	 *
	 * @since 5.1.10
	 */
	public function hook() {
		parent::hook();

		$status = $this->get_status_when_to_trigger();
		add_filter( "tec_tickets_commerce_order_status_{$status->get_slug()}_get_flags", [ $this, 'modify_status_flags' ], 10, 3 );
	}

	/**
	 * Returns the instance of the status we trigger this flag action.
	 *
	 * @since 5.1.10
	 *
	 * @return Status_Abstract
	 */
	public function get_status_when_to_trigger() {
		$status = tribe( Status_Handler::class )->get_by_slug( tribe_get_option( Settings::$option_stock_handling, Pending::SLUG ) );

		if ( ! $status instanceof Status_Abstract ) {
			$status = tribe( Pending::class );
		}

		return $status;
	}

	/**
	 * Include generate_attendee flag to either Completed or Pending
	 *
	 * @since 5.1.10
	 *
	 * @param string[]        $flags  Which flags will trigger this action.
	 * @param \WP_Post        $post   Post object.
	 * @param Status_Abstract $status Instance of action flag we are triggering.
	 *
	 * @return string[]
	 */
	public function modify_status_flags( $flags, $post, $status ) {
		$flags[] = 'decrease_stock';

		return $flags;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( Status_Interface $new_status, $old_status, \WP_Post $post ) {
		if ( empty( $post->items ) ) {
			return;
		}

		foreach ( $post->items as $ticket_id => $item ) {
			$ticket = \Tribe__Tickets__Tickets::load_ticket_object( $item['ticket_id'] );
			if ( null === $ticket ) {
				continue;
			}

			if ( ! $ticket->manage_stock() ) {
				continue;
			}

			$quantity = Arr::get( $item, 'quantity', 1 );

			// Skip generating for zero-ed items.
			if ( 0 >= $quantity ) {
				continue;
			}

			update_post_meta( $ticket->ID, Ticket::$stock_meta_key, $ticket->stock() - $quantity );
		}
	}
}