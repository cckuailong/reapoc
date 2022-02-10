<?php
/**
 * Management for Global Stock on events and tickets
 *
 * @since  4.1
 */
class Tribe__Tickets__Global_Stock {
	/**
	 * Post meta key used to store the global stock flag on events.
	 *
	 * @since 4.1
	 *
	 * @var   string
	 */
	const GLOBAL_STOCK_ENABLED = '_tribe_ticket_use_global_stock';

	/**
	 * Post meta key used to store the actual global stock level on events.
	 *
	 * @since 4.1
	 *
	 * @var   string
	 */
	const GLOBAL_STOCK_LEVEL = '_tribe_ticket_global_stock_level';

	/**
	 * Flag used to indicate that a ticket will use the global stock.
	 *
	 * @since 4.1
	 *
	 * @var   string
	 */
	const GLOBAL_STOCK_MODE = 'global';

	/**
	 * Flag used to indicate that a ticket will use the global stock,
	 * but that a cap has been placed on the total number of sales for
	 * this ticket type.
	 *
	 * @since 4.1
	 *
	 * @var   string
	 */
	const CAPPED_STOCK_MODE = 'capped';

	/**
	 * Flag used to indicate that, if global stock is in effect for
	 * an event, the specific ticket this flag is applied to will
	 * maintain it's own inventory rather than draw from the global
	 * pool.
	 *
	 * @since 4.1
	 *
	 * @var   string
	 */
	const OWN_STOCK_MODE = 'own';

	/**
	 * Post meta key used to store the ticket global stock mode.
	 *
	 * @since 4.6
	 *
	 * @var   string
	 */
	const TICKET_STOCK_MODE = '_global_stock_mode';

	/**
	 * Post meta key used to store the ticket global stock cap.
	 *
	 * @since 4.6
	 *
	 * @var   string
	 */
	const TICKET_STOCK_CAP = '_global_stock_cap';

	/**
	 * Which post we are dealing with for this instance of stock
	 *
	 * @since 4.6
	 *
	 * @var   int $post_id
	 */
	public $post_id;

	/**
	 * Creates an instance for a given Event
	 *
	 * @since  4.1
	 *
	 * @param int $post_id
	 */
	public function __construct( $post_id ) {
		$this->post_id = absint( $post_id );
	}

	/**
	 * Enables global stock control for the current post.
	 *
	 * As a convenience, false can be passed to this method to disable rather
	 * than enable global stock.
	 *
	 * @since  4.1
	 * @since  4.6 Added a return so we can check if it was enabled correctly
	 *
	 * @param  bool $yes
	 *
	 * @return bool|int
	 */
	public function enable( $yes = true ) {
		return update_post_meta( $this->post_id, self::GLOBAL_STOCK_ENABLED, tribe_is_truthy( $yes ) );
	}

	/**
	 * Disables global stock control for the current post.
	 *
	 * As a convenience, false can be passed to this method to enable rather
	 * than disable global stock.
	 *
	 * @since  4.1
	 * @since  4.6 Added a return so we can check if it was enabled correctly
	 *
	 * @param  bool $yes
	 *
	 * @return bool|int
	 */
	public function disable( $yes = true ) {
		return update_post_meta( $this->post_id, self::GLOBAL_STOCK_ENABLED, ! tribe_is_truthy( $yes ) );
	}

	/**
	 * Indicates if global stock is enabled for this post.
	 *
	 * @since  4.1
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return tribe_is_truthy( get_post_meta( $this->post_id, self::GLOBAL_STOCK_ENABLED, true ) );
	}

	/**
	 * Sets the global stock level for the current post.
	 *
	 * @since  4.1
	 * @since  4.6  Added a Return
	 * @since  4.11.4 Added new $force parameter.
	 *
	 * @param int     $quantity Quantity to set for stock.
	 * @param boolean $force    Whether to force setting stock, even if capacity is less.
	 *
	 * @return bool|int
	 */
	public function set_stock_level( $quantity, $force = false ) {
		$capacity = tribe_tickets_get_capacity( $this->post_id );
		$quantity = (int) $quantity;

		// When we are dealing with non-unlimited capacities verify before updating the Post.
		if (
			! $force
			&& ! is_null( $capacity ) // We need to verify null to prevent capacity check when it doesn't exist.
			&& 0 <= $capacity // Only non-unlimited capacities.
			&& $capacity < $quantity // Only if quantity is more than capacity allows.
		) {
			$quantity = $capacity;
		}

		$status = update_post_meta( $this->post_id, self::GLOBAL_STOCK_LEVEL, $quantity );

		/**
		 * Fires when the global stock level is set/changed.
		 *
		 * @since  4.1
		 * @since  4.6 Added $status param
		 *
		 * @param  int  $post_id
		 * @param  int  $quantity
		 * @param  bool $status
		 */
		do_action( 'tribe_tickets_global_stock_level_changed', $this->post_id, $quantity, $status );

		return $status;
	}

	/**
	 * Returns the post's global stock--the shared maximum available, not the remaining available.
	 *
	 * @since  4.1
	 *
	 * @return int
	 */
	public function get_stock_level() {
		return (int) get_post_meta( $this->post_id, self::GLOBAL_STOCK_LEVEL, true );
	}

	/**
	 * Returns a count of the number of global ticket sales for this event.
	 *
	 * @since  4.1
	 * @since  4.6  Introduced $pending Param
	 *
	 * @param  bool  $pending  Includes Pending Tickets on the Sales total
	 *
	 * @return int
	 */
	public function tickets_sold( $pending = false ) {
		$sales = 0;

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );
		$tickets = $tickets_handler->get_event_shared_tickets( $this->post_id );

		/** @var Tribe__Tickets__Ticket_Object $ticket */
		foreach ( $tickets as $ticket ) {
			$sales += (int) $ticket->qty_sold();

			// Allow for fetching the pending with the Sold ones
			if ( true === (bool) $pending ) {
				$sales += (int) $ticket->qty_pending();
			}
		}

		return $sales;
	}
}
