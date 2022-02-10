<?php

namespace Tribe\Tickets\Repositories;

use Tribe__Repository;
use Tribe__Utils__Array as Arr;

/**
 * The repository functionality for Ticket Orders.
 *
 * @since 5.1.0
 */
class Order extends Tribe__Repository {

	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @since 5.1.0
	 *
	 * @var string
	 */
	protected $filter_name = 'tickets-orders';

	/**
	 * Key name to use when limiting lists of keys.
	 *
	 * @since 5.1.0
	 *
	 * @var string
	 */
	protected $key_name = '';

	/**
	 * The attendee provider object used to interact with the Order.
	 *
	 * @since 5.1.0
	 *
	 * @var Tribe__Tickets__Tickets
	 */
	public $attendee_provider;

	/**
	 * The list of supported order statuses.
	 *
	 * @since 5.1.0
	 *
	 * @var array An array of all the order statuses supported by the repository.
	 */
	protected static $order_statuses;

	/**
	 * The list of supported public order statuses.
	 *
	 * @since 5.1.0
	 *
	 * @var array
	 */
	protected static $public_order_statuses;

	/**
	 * The list of supported private order statuses.
	 *
	 * @since 5.1.0
	 *
	 * @var array
	 */
	protected static $private_order_statuses;

	/**
	 * Tribe__Tickets__Attendee_Repository constructor.
	 *
	 * @since 5.1.0
	 */
	public function __construct() {
		parent::__construct();

		// The extending repository must specify $this->default_args['post_type'] and set it to a valid post type.
		$this->default_args = array_merge( $this->default_args, [
			'post_type'   => '_no_order_type_set',
			'orderby'     => [ 'date', 'title', 'ID' ],
			'post_status' => 'any',
		] );

		$this->init_order_statuses();

		$this->schema = [
			'order_status' => [ $this, 'filter_by_order_status' ],
		];
	}

	/**
	 * Initialize the order statuses needed for the Orders repository.
	 *
	 * @since 5.1.0
	 */
	protected function init_order_statuses() {
		// Statuses already generated.
		if ( ! empty( self::$order_statuses ) ) {
			return;
		}

		/** @var \Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		/**
		 * Allow filtering the list of all order statuses supported by the Orders repository.
		 *
		 * @since 5.1.0
		 *
		 * @param array $statuses List of all order statuses.
		 */
		$statuses = apply_filters( 'tribe_tickets_repositories_order_statuses', [] );

		// Enforce lowercase for comparison purposes.
		$statuses = array_map( 'strtolower', $statuses );

		// Prevent unnecessary duplicates.
		$statuses = array_unique( $statuses );

		// Store for reuse.
		self::$order_statuses = $statuses;

		/**
		 * Allow filtering the list of public order statuses supported by the Orders repository.
		 *
		 * @since 5.1.0
		 *
		 * @param array $public_order_statuses List of public order statuses.
		 */
		self::$public_order_statuses = apply_filters( 'tribe_tickets_repositories_order_public_statuses', [] );

		// Set up the initial private order statuses.
		$private_order_statuses = array_diff( self::$order_statuses, self::$public_order_statuses );

		/**
		 * Allow filtering the list of private order statuses supported by the Orders repository.
		 *
		 * @since 5.1.0
		 *
		 * @param array $private_order_statuses List of private order statuses.
		 */
		self::$private_order_statuses = apply_filters( 'tribe_tickets_repositories_order_private_statuses', $private_order_statuses );
	}

	/**
	 * Filters attendee to only get those related to orders with a specific status.
	 *
	 * @since 5.1.6
	 *
	 * @param string|array $order_status Order status.
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the requested statuses are not accessible by the user.
	 */
	public function filter_by_order_status( $order_status ) {
		$statuses = Arr::list_to_array( $order_status );

		$has_manage_access = current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' );

		// Map the `any` meta-status.
		if ( 1 === count( $statuses ) && 'any' === $statuses[0] ) {
			if ( ! $has_manage_access ) {
				$statuses = [ 'public' ];
			} else {
				// No need to filter if the user can read all posts.
				return;
			}
		}

		// Allow the user to define singular statuses or the meta-status "public".
		if ( in_array( 'public', $statuses, true ) ) {
			$statuses = array_unique( array_merge( $statuses, self::$public_order_statuses ) );
		}

		// Allow the user to define singular statuses or the meta-status "private".
		if ( in_array( 'private', $statuses, true ) ) {
			$statuses = array_unique( array_merge( $statuses, self::$private_order_statuses ) );
		}

		// Remove any status the user cannot access.
		if ( ! $has_manage_access ) {
			$statuses = array_intersect( $statuses, self::$public_order_statuses );
		}

		if ( empty( $statuses ) ) {
			throw Void_Query_Exception::because_the_query_would_yield_no_results( 'The user cannot access the requested attendee order statuses.' );
		}

		$this->by( 'post_status', $statuses );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.1.0
	 *
	 * @return WP_Post|false The new post object or false if unsuccessful.
	 */
	public function create() {
		// Extended repositories must handle their own order creation.
		return false;
	}

	/**
	 * Create an order for a ticket.
	 *
	 * @since 5.1.0
	 *
	 * @param array                                  $order_data List of order data to be saved.
	 * @param null|int|Tribe__Tickets__Ticket_Object $ticket     The ticket object, ticket ID, or null if not relying on it.
	 *
	 * @return int|string|false The order ID or false if not created.
	 */
	public function create_order_for_ticket( $order_data, $ticket = null ) {
		// Bail if we already have an order.
		if ( ! empty( $order_data['order_id'] ) ) {
			return false;
		}

		$tickets = Arr::get( $order_data, 'tickets', [] );

		if ( empty( $order_data['tickets'] ) ) {
			$ticket_id = $ticket;

			if ( is_object( $ticket ) ) {
				// Detect ticket ID from the object.
				$ticket_id = $ticket->ID;
			} elseif ( empty( $ticket ) && isset( $order_data['ticket_id'] ) ) {
				// Detect the ticket ID from the order data.
				$ticket_id = $order_data['ticket_id'];
			}

			// Bail if no valid ticket ID.
			if ( $ticket_id < 1 ) {
				return false;
			}

			$order_data['tickets'] = [
				[
					'id'       => $ticket_id,
					'quantity' => 1,
				],
			];
		}

		/**
		 * Allow filtering the order data being used to create an order for the ticket.
		 *
		 * @since 5.1.0
		 *
		 * @param array                         $order_data List of order data to be saved.
		 * @param Tribe__Tickets__Ticket_Object $ticket     The ticket object or null if not relying on it.
		 */
		$order_data = apply_filters( 'tribe_tickets_repositories_order_create_order_for_ticket_order_args', $order_data, $ticket );

		// Check if order creation is disabled.
		if ( empty( $order_data ) ) {
			return false;
		}

		try {
			$order = $this->set_args( $order_data )->create();
		} catch ( Tribe__Repository__Usage_Error $exception ) {
			return false;
		}

		// Check if order was created.
		if ( ! $order ) {
			return false;
		}

		return $order->ID;
	}
}
