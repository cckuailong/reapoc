<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Orders__Sales
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Orders__Sales {

	/**
	 * @var \Tribe__Cache
	 */
	protected $cache;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Orders__Sales constructor.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Cache|null $cache
	 */
	public function __construct( Tribe__Cache $cache = null ) {
		$this->cache = null === $cache ? new Tribe__Cache() : $cache;
	}

	/**
	 * Returns the revenue for a single ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket The ticket object.
	 *
	 * @return int
	 */
	public function get_revenue_for_ticket( Tribe__Tickets__Ticket_Object $ticket ) {
		$revenue = $this->get_unfiltered_revenue_for_ticket( $ticket );



		/**
		 * Filters the revenue for a specific ticket.
		 *
		 * @since 4.7
		 *
		 * @param int                           $revenue The revenue for this ticket.
		 * @param Tribe__Tickets__Ticket_Object $ticket  The ticket object.
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_attendee_revenue', $revenue, $ticket );
	}

	/**
	 * Returns the unfiltered revenue for an ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return float
	 */
	protected function get_unfiltered_revenue_for_ticket( Tribe__Tickets__Ticket_Object $ticket ) {
		return (float) $ticket->price * $ticket->qty_sold();
	}

	/**
	 * Whether an attendee has been assigned a completed order status or not.
	 *
	 * @since 4.7
	 *
	 * @param array $attendee
	 *
	 * @return bool
	 */
	public function is_order_completed( array $attendee ) {
		$order_status = Tribe__Utils__Array::get( $attendee, 'order_status', false );

		if ( false === $order_status || ! in_array( $order_status, $this->get_revenue_generating_order_statuses(), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the filtered list of ticket statuses that should be taken into account when calculating revenue.
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	protected function get_revenue_generating_order_statuses() {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$revenue_generating_order_statuses = $status_mgr->get_statuses_by_action( 'count_completed', 'tpp' );

		/**
		 * Filters the list of ticket statuses that should be taken into account when calculating revenue.
		 *
		 * @since 4.7
		 *
		 * @param  array $revenue_generating_order_statuses
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_revenue_generating_order_statuses', $revenue_generating_order_statuses );
	}

	/**
	 * Returns the total revenue provided a list of tickets.
	 *
	 * @since 4.7
	 *
	 * @param array $tickets An array of ticket objects
	 *
	 * @return int
	 */
	public function get_revenue_for_tickets( array $tickets ) {
		$revenue = array_sum( array_map( array( $this, 'get_revenue_for_ticket' ), $tickets ) );

		/**
		 * Filters the revenue for a list of tickets.
		 *
		 * @since 4.7
		 *
		 * @param int   $revenue The revenue for these tickets.
		 * @param array $tickets The tickets objects.
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_tickets_revenue', $revenue, $tickets );
	}

	/**
	 * Returns the amount this ticket represents in sales terms.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return int
	 */
	public function get_sale_for_ticket( Tribe__Tickets__Ticket_Object $ticket ) {
		$sales_count = $ticket->qty_sold();

		/**
		 * Filters the sales count for an ticket.
		 *
		 * @since 4.7
		 *
		 * @param int   $sales_count The sales count for this ticket; defaults to `1` per ticket
		 *                           with a sales generating order status.
		 * @param Tribe__Tickets__Ticket_Object $ticket    The ticket object.
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_ticket_sales_count', $sales_count, $ticket );
	}

	/**
	 * Returns the amount of sales for a list of tickets.
	 *
	 * @since 4.7
	 *
	 * @param array $tickets
	 *
	 * @return int
	 */
	public function get_sales_for_tickets( $tickets ) {
		$sales = array_sum( array_map( array( $this, 'get_sale_for_ticket' ), $tickets ) );

		/**
		 * Filters the sales for a list of tickets.
		 *
		 * @since 4.7
		 *
		 * @param int   $sales   The sales for these tickets.
		 * @param array $tickets The tickets objects.
		 */
		return apply_filters( 'tribe_tickets_commerce_paypal_tickets_sales', $sales, $tickets );
	}

	/**
	 * Filters a list of attendees returning only those with not completed orders.
	 *
	 * @since 4.7
	 *
	 * @param array $attendees The list of attendees to filter.
	 *
	 * @return array A list of attendees with not completed orders.
	 */
	public function filter_not_completed( array $attendees ) {
		if ( empty( $attendees ) ) {
			return array();
		}

		$completed     = $this->filter_completed( $attendees );
		$completed_ids = wp_list_pluck( $completed, 'attendee_id' );

		$not_completed = array();
		foreach ( $attendees as $attendee ) {
			if ( in_array( $attendee['attendee_id'], $completed_ids ) ) {
				continue;
			}
			$not_completed[] = $attendee;
		}

		return $not_completed;
	}

	/**
	 * Filters a list of attendees returning only those with completed orders.
	 *
	 * @since 4.7
	 *
	 * @param array $attendees The list of attendees to filter.
	 *
	 * @return array A list of attendees with completed orders.
	 */
	public function filter_completed( array $attendees ) {
		if ( empty( $attendees ) ) {
			return array();
		}

		return array_filter( $attendees, array( $this, 'is_order_completed' ) );
	}

	/**
	 * Filters an array of tickets to return only those that have least one sale.
	 *
	 * @since 4.7
	 *
	 * @param array $tickets
	 *
	 * @return array
	 */
	public function filter_sold_tickets( array $tickets ) {
		return array_filter( $tickets, array( $this, 'has_sold' ) );
	}

	/**
	 * Whether the ticket has at least one sale.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return bool
	 */
	public function has_sold( Tribe__Tickets__Ticket_Object $ticket ) {
		return $ticket->qty_sold() > 0;
	}

	/**
	 * Returns the ticket breakdown for the provided tickets.
	 *
	 * @since 4.7
	 *
	 * @param array $tickets
	 *
	 * @return array
	 */
	public function get_tickets_breakdown_for( array $tickets ) {
		$breakdown = array(
			__( 'Completed', 'event-tickets' )     => array(
				'total' => array_sum( array_map( array( $this, 'get_ticket_completed_total' ), $tickets ) ),
				'qty'   => array_sum( array_map( array( $this, 'get_ticket_completed_qty' ), $tickets ) ),
			),
			__( 'Not completed', 'event-tickets' ) => array(
				'total' => array_sum( array_map( array( $this, 'get_ticket_not_completed_total' ), $tickets ) ),
				'qty'   => array_sum( array_map( array( $this, 'get_ticket_not_completed_qty' ), $tickets ) ),
			),
		);

		return $breakdown;
	}

	/**
	 * Returns a list of orders for the post.
	 *
	 * @since 4.7
	 *
	 * @param int   $post_id
	 * @param array $ticket_ids An optional array of ticket IDs to limit the table items.
	 *
	 * @return array
	 */
	public function get_orders_for_post( $post_id, array $ticket_ids = null ) {
		$cache_key = ! empty( $ticket_ids )
			? sprintf( "{$post_id}-%s-orders", implode( '|', $ticket_ids ) )
			: "{$post_id}-orders";

		$cached = $this->cache[ $cache_key ];

		if ( false !== $cached ) {
			return $cached;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		$orders = $paypal->get_orders_by_post_id( $post_id, $ticket_ids, array( 'posts_per_page' => -1 ) );

		$this->cache[ $cache_key ] = $orders;

		return $orders;
	}

	/**
	 * Returns the total revenue from completed orders for the ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return float
	 */
	protected function get_ticket_completed_total( Tribe__Tickets__Ticket_Object $ticket ) {
		return (float) $ticket->qty_sold() * $ticket->price;
	}

	/**
	 * Returns the total number of completed orders for the ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return int
	 */
	protected function get_ticket_completed_qty( Tribe__Tickets__Ticket_Object $ticket ) {
		return $ticket->qty_sold();
	}

	/**
	 * Returns the total revenue from not completed orders for the ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return float
	 */
	protected function get_ticket_not_completed_total( Tribe__Tickets__Ticket_Object $ticket ) {
		return (float) $ticket->qty_pending() * $ticket->price;
	}

	/**
	 * Returns the total number of not completed orders for the ticket.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 *
	 * @return int
	 */
	protected function get_ticket_not_completed_qty( Tribe__Tickets__Ticket_Object $ticket ) {
		return $ticket->qty_pending();
	}

	/**
	 * Filters the available value to allow coherent value when overselling.
	 *
	 * @since 4.7
	 *
	 * @since 4.10.5 - add check for Global Stock
	 *
	 * @param int                            $available
	 * @param \Tribe__Tickets__Ticket_Object $ticket
	 * @param int                            $sold
	 * @param int                            $stock
	 *
	 * @return int
	 */
	public function filter_available( $available, Tribe__Tickets__Ticket_Object $ticket, $sold, $stock ) {
		if (
			'Tribe__Tickets__Commerce__PayPal__Main' !== $ticket->provider_class
			|| -1 === $available
			|| $ticket::UNLIMITED_STOCK === $available
		) {
			return $available;
		}

		// if using global stock then return available
		$event        = Tribe__Tickets__Tickets::find_matching_event( $ticket );
		$global_stock = new Tribe__Tickets__Global_Stock( $event->ID );
		if ( Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE === $ticket->global_stock_mode() && $global_stock->is_enabled() ) {
			return $available;
		}

		return $stock - $sold;
	}

	/**
	 * Get all orders for a product id and return array of order objects
	 *
	 * @since 4.10
	 *
	 * @param $ID int an ID for a tpp product
	 *
	 * @return array an array of order objects
	 */
	public function get_all_orders_by_product_id( $ID ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$all_statuses = (array) $status_mgr->get_statuses_by_action( 'all', 'tpp' );
		$args = [
			'post_type'      => 'tribe_tpp_orders',
			'posts_per_page' => -1,
			'post_status'    => $all_statuses,
			'meta_query'     => [
				[
					'key'   => '_tribe_paypal_ticket',
					'value' => $ID,
				],
			],
			'fields'         => 'ids',
		];

		$all_order_ids_for_ticket  = new WP_Query( $args );
		$order_ids = $all_order_ids_for_ticket->posts;
		if ( empty ( $order_ids ) ) {
			return array();
		}

		$orders = array();
		foreach ( $order_ids as $id ) {

			$order = new Tribe__Tickets__Commerce__PayPal__Order();
			$order = $order->hydrate_from_post( $id );

			//prevent fatal error if no orders
			if ( ! is_wp_error( $order ) ) {
				$orders[ $id ] = $order;
			}
		}

		return $orders;
	}
}
