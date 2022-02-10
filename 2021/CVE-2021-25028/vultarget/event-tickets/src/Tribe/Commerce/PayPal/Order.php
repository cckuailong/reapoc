<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Order
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Order {

	/**
	 * The meta key prefix used to store the order post meta.
	 *
	 * @var string
	 */
	public static $meta_prefix = '_tribe_paypal_';

	/**
	 * @var string The date this Order post has been originally created, format is `Y-m-d H:i:s`
	 */
	protected $created;

	/**
	 * @var string The date this Order post has been last updated, format is `Y-m-d H:i:s`
	 */
	protected $modified;

	/**
	 * @var bool Whether the order previous status was pending or not.
	 */
	protected $was_pending = false;

	/**
	 * A list of attendees for the order.
	 *
	 * @var array
	 */
	protected $attendees = array();

	/**
	 * The PayPal Order ID (hash).
	 *
	 * @var string
	 */
	protected $paypal_order_id = '';

	/**
	 * The order post ID in the WordPress database.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * The order post status.
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * The order post status label
	 *
	 * @var string
	 */
	protected $status_label = '';

	/**
	 * All the ticket post IDs related to the Order.
	 *
	 * @var array
	 */
	protected $ticket_ids = array();

	/**
	 * All the post IDs related to the Order.
	 *
	 * @var array
	 */
	protected $post_ids;

	/**
	 * The meta key that stores the order PayPal hashed meta.
	 *
	 * @var
	 */
	protected $hashed_meta_key = '_paypal_hashed_meta';

	/**
	 * A list of meta keys that are stored one per line in the database
	 * to facilitate SQL queries.
	 *
	 * @var array
	 *
	 * @see update for details about the database persistence.
	 */
	protected $searchable_meta_keys = array(
		'items',
		'mc_gross',
		'mc_currency',
		'payment_date',
		'payment_status',
		'payer_email',
		'attendees',
		'transaction_data',
	);

	/**
	 * An array that stores all the meta for an Order object.
	 *
	 * @var array
	 */
	protected $meta = array();

	/**
	 * Either builds an Order object from a PayPal transaction data and returns it
	 * or fetches an existing Order information.
	 *
	 * @since 4.7
	 *
	 * @param array $transaction_data
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Order|false Either an existing or new order or `false` on
	 *                                                       failure.
	 */
	public static function from_transaction_data( array $transaction_data ) {
		$order_id = Tribe__Utils__Array::get( $transaction_data, 'txn_id', false );

		if ( false === $order_id ) {
			return false;
		}

		$order = self::from_order_id( $order_id );

		$prev_status = null;

		if ( ! $order ) {
			$order = new self();
		} else {
			$prev_status = $order->status;
		}

		$order->hydrate_from_transaction_data( $transaction_data );
		$order->was_pending = $prev_status === Tribe__Tickets__Commerce__PayPal__Stati::$pending;

		return $order;
	}

	/**
	 * Searches for an Order by the Order PayPal ID (hash) or post ID, builds and hydrates it if found.
	 *
	 * @since 4.7
	 *
	 * @param $order_id
	 *
	 * @return bool|Tribe__Tickets__Commerce__PayPal__Order The Order object if found or
	 *                                                       `false` if the Order could not be
	 *                                                       found.
	 */
	public static function from_order_id( $order_id, $use_post_id = false ) {
		$order_post_id = self::find_by_order_id( $order_id, $use_post_id );

		if ( empty( $order_post_id ) ) {
			return false;
		}

		$order = new self();

		$order->hydrate_from_post( $order_post_id );

		return $order;
	}

	/**
	 * Finds an order by the PayPal order ID (hash).
	 *
	 * @since 4.7
	 *
	 * @param string $order_id    The PayPal order ID (hash).
	 * @param bool   $use_post_id Whether the `order_id` parameter should be used as
	 *                            a PayPal Order ID (hash) or as a post ID
	 *
	 * @return int|false Either an existing order post ID or `false` if not found.
	 */
	public static function find_by_order_id( $order_id, $use_post_id = false ) {
		global $wpdb;

		$query = $use_post_id
			? "SELECT ID from {$wpdb->posts} WHERE ID = %d AND post_type = %s"
			: "SELECT ID from {$wpdb->posts} WHERE post_title = %s AND post_type = %s";

		$order_post_id = $wpdb->get_var(
			$wpdb->prepare(
				$query,
				trim( $order_id ),
				Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT
			)
		);

		return ! empty( $order_post_id ) ? (int) $order_post_id : false;
	}

	/**
	 * Fills an order information from a post stored fields and meta.
	 *
	 * This is a database-light operation that will not update
	 * the Order database information, use the `update` method
	 * to update the Order information on the database.
	 *
	 * @since 4.7
	 * @since 4.10.11 Avoid fatal when trying to set class status property.
	 *
	 * @param int        $order_post_id The Order post ID.
	 * @param null|array $fields        List of fields to hydrate, or null for all.
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Order
	 *
	 * @see   update
	 */
	public function hydrate_from_post( $order_post_id, $fields = null ) {
		$order_post = get_post( $order_post_id );

		if (
			! $order_post instanceof WP_Post
			|| Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT !== $order_post->post_type
		) {
			return $this;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );
		$status = $paypal->get_order_statuses();

		$this->paypal_order_id = $order_post->post_title;
		$this->post_id         = $order_post_id;
		$this->status          = $order_post->post_status;

		if ( ! empty( $status[ $order_post->post_status ] ) ) {
			$this->status_label = $status[ $order_post->post_status ];
		}

		$this->created         = $order_post->post_date;
		$this->modified        = $order_post->post_modified;

		$hashed_meta = get_post_meta( $order_post_id, $this->hashed_meta_key, true );

		if ( ! empty( $hashed_meta ) ) {
			foreach ( $hashed_meta as $key => $value ) {
				if ( is_array( $fields ) && ! in_array( $key, $fields, true ) ) {
					continue;
				}

				$this->set_meta( $key, $value );
			}
		}

		foreach ( $this->searchable_meta_keys as $key ) {
			if ( is_array( $fields ) && ! in_array( $key, $fields, true ) ) {
				continue;
			}

			$prefixed_key = self::$meta_prefix . $key;
			$this->set_meta( $key, get_post_meta( $order_post_id, $prefixed_key, true ) );
		}

		/**
		 * Fired after an Order object has been filled from post fields and meta. *
		 *
		 * @since 4.7
		 *
		 * @param Tribe__Tickets__Commerce__PayPal__Order $this
		 */
		do_action( 'tribe_tickets_tpp_order_from_post', $this );

		return $this;
	}

	/**
	 * Fills an order information from a transaction data array.
	 *
	 * This is a database-light operation that will not update
	 * the Order database information, use the `update` method
	 * to update the Order information on the database.
	 *
	 * @since 4.7
	 *
	 * @param array $transaction_data
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Order
	 *
	 * @see   update
	 */
	public function hydrate_from_transaction_data( array $transaction_data ) {
		foreach ( $transaction_data as $key => $value ) {
			$this->set_meta( $key, $value );
		}

		/**
		 * Fired after an Order object has been filled from post fields and meta.
		 *
		 * @since 4.7
		 *
		 * @param Tribe__Tickets__Commerce__PayPal__Order $this
		 * @param array                                   $transaction_data
		 */
		do_action( 'tribe_tickets_tpp_order_from_transaction', $this, $transaction_data );

		return $this;
	}

	/**
	 * Finds orders by a list of criteria.
	 *
	 * @since 4.7
	 *
	 * @param array      $args   {
	 *                         Optional. Arguments to retrieve orders. See WP_Query::parse_query() for all
	 *                         available arguments.
	 *
	 * @type int    $post_id   ID, or array of IDs, of the post(s) Orders should be related to.
	 * @type int    $ticket_id ID, or array of IDs, of the ticket(s) Orders should be related to.
	 * }
	 * @param null|array $fields List of fields to hydrate, or null for all.
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Order[]
	 */
	public static function find_by( array $args = [], $fields = null ) {
		$args = wp_parse_args( $args, [
			'post_type'   => Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT,
			'post_status' => 'any',
			'meta_key'    => self::$meta_prefix . 'payment_date',
			'meta_type'   => 'DATETIME',
			'order'       => 'DESC',
			'orderby'     => 'meta_value',
		] );

		global $wpdb;

		$cache     = new Tribe__Cache;
		$cache_key = self::cache_prefix( 'find_by_' . $cache->make_key( $args ) );

		if (
			isset( $cache[ $cache_key ] )
			&& false !== $cache[ $cache_key ]
		) {
			return $cache[ $cache_key ];
		}

		$meta_query = isset( $args['meta_query'] )
			? $args['meta_query']
			: array( 'relation' => 'AND' );

		if ( ! empty( $args['post_id'] ) ) {
			$related_post_ids              = is_array( $args['post_id'] ) ? $args['post_id'] : array( $args['post_id'] );
			$meta_query['related_post_id'] = array(
				'key'     => self::$meta_prefix . 'post',
				'value'   => $related_post_ids,
				'compare' => 'IN',
			);
			unset( $args['post_id'] );
		}

		if ( ! empty( $args['ticket_id'] ) ) {
			$related_ticket_ids              = is_array( $args['ticket_id'] ) ? $args['ticket_id'] : array( $args['ticket_id'] );
			$meta_query['related_ticket_id'] = array(
				'key'     => self::$meta_prefix . 'ticket',
				'value'   => $related_ticket_ids,
				'compare' => 'IN',
			);
			unset( $args['ticket_id'] );
		}

		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$args['fields'] = 'ids';

		$found = get_posts( $args );

		$orders = [];

		if ( $found ) {
			foreach ( $found as $order_post_id ) {
				$order    = new self();
				$orders[] = $order->hydrate_from_post( $order_post_id, $fields );
			}
		}

		$cache[ $cache_key ] = $orders;

		return $orders;
	}

	/**
	 * Returns a prefixed cache key.
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public static function cache_prefix( $key ) {
		return __CLASS__ . $key;
	}

	/**
	 * Either builds an Order object from a PayPal transaction data and returns it
	 * or fetches an existing Order information.
	 *
	 * @since 4.7
	 *
	 * @param int        $attendee_id An Attendee post ID.
	 * @param null|array $fields      List of fields to hydrate, or null for all.
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Order|false Either an existing or new order or `false` on
	 *                                                       failure.
	 */
	public static function from_attendee_id( $attendee_id, $fields = null ) {
		$order_post_id = get_post_meta( $attendee_id, Tribe__Tickets__Commerce__PayPal__Main::ATTENDEE_ORDER_KEY, true );

		// validate it
		$order_post_id = self::find_by_order_id( $order_post_id );

		if ( empty( $order_post_id ) ) {
			return false;
		}

		$order = new self();

		$order->hydrate_from_post( $order_post_id, $fields );

		return $order;
	}

	/**
	 * Adds an attendee to those related to the Order.
	 *
	 * @param int $attendee_id An attendee post ID.
	 */
	public function add_attendee( $attendee_id ) {
		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		$attendee = $paypal->get_attendee( $attendee_id );

		if ( $this->has_attendee( $attendee_id ) ) {
			$this->remove_attendee( $attendee_id );
		}

		$this->attendees[] = $attendee;
	}

	/**
	 * Whether the Order is related to an attendee or not.
	 *
	 * @since 4.7
	 *
	 * @param int $attendee_id An attendee post ID
	 *
	 * @return bool
	 */
	public function has_attendee( $attendee_id ) {
		$matching = wp_list_filter( $this->attendees, array( 'attendee_id' => $attendee_id ) );

		return ! empty( $matching );
	}

	/**
	 * Removes an attendee from those associated with the order.
	 *
	 * @since 4.7
	 *
	 * @param int $attendee_id An attendee post ID
	 *
	 * @return array
	 */
	public function remove_attendee( $attendee_id ) {
		$filtered = array();

		foreach ( $this->attendees as $attendee ) {
			if ( $attendee['attendee_id'] === $attendee_id ) {
				continue;
			}

			$filtered[] = $attendee;
		}

		$this->attendees = $filtered;
	}

	/**
	 * Returns the Order PayPal ID (hash).
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function paypal_id() {
		return $this->paypal_order_id;
	}

	/**
	 * Returns the attendees for this order.
	 *
	 * @since 4.7
	 *
	 * @return array An array of attendee information.
	 *
	 * @see   Tribe__Tickets__Commerce__PayPal__Main::get_attendee() for the attendee format.
	 */
	public function get_attendees() {
		return $this->attendees;
	}

	/**
	 * Returns the value of a meta field set on the order or all the meta set
	 * on the Order.
	 *
	 * This is a database-light operation: meta is read from the object, not the
	 * database; use `hydrate` methods to populate the meta.
	 *
	 * @param string|null $key
	 *
	 * @return array|mixed Either a specif meta value, `null` if no value is set for
	 *                     the key; all the Order meta if `$key` is `null`.
	 *
	 * @see hydrate_from_post
	 * @see hydrate_from_transaction_data
	 */
	public function get_meta( $key = null ) {
		if ( null === $key ) {
			return $this->meta;
		}

		return isset( $this->meta[ $key ] )
			? $this->meta[ $key ]
			: get_post_meta( $this->post_id, $key, true );
	}

	/**
	 * Sets a meta key value on the Order.
	 *
	 * This is a database-light operation: meta is not written to the
	 * database but only in the object array cache; use `udpate` method
	 * to persist the Order meta.
	 *
	 * @since 4.7
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @see   update
	 */
	public function set_meta( $key, $value ) {
		if ( 0 === strpos( $key, self::$meta_prefix ) ) {
			$key = str_replace( self::$meta_prefix, '', $key );
		}

		switch ( $key ) {
			case 'payment_status':
				if ( ! empty( $value ) ) {
					$this->status = Tribe__Tickets__Commerce__PayPal__Stati::cast_payment_status( $value );
				}

				return;
			case 'txn_id':
				$this->paypal_order_id = $value;

				return;
			case 'attendees':
				$value = is_array( $value ) ? $value : array( $value );
				/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
				$paypal          = tribe( 'tickets.commerce.paypal' );
				$this->attendees = array_filter( array_map( array( $paypal, 'get_attendee' ), $value ) );

				return;
			case 'items':
				$this->meta['items'] = $value;
				$this->ticket_ids    = wp_list_pluck( $value, 'ticket_id' );
				$this->post_ids      = wp_list_pluck( $value, 'post_id' );

				return;
			case 'payment_date':
				$this->meta['payment_date'] = Tribe__Date_Utils::reformat( $value, Tribe__Date_Utils::DBDATETIMEFORMAT );

				return;
			default:
				$this->meta[ $key ] = $value;

				return;
		}

	}

	/**
	 * Updates an order data on the database.
	 *
	 * @since 4.7
	 *
	 * @return int|false Either the updated/created order post ID or `false` if the Order
	 *                   could not be saved.
	 */
	public function update() {
		if ( empty( $this->paypal_order_id ) ) {
			return false;
		}

		$meta_input = array(
			$this->hashed_meta_key           => array(),
			self::$meta_prefix . 'attendees' => wp_list_pluck( $this->attendees, 'attendee_id' ),
		);

		foreach ( $this->meta as $key => $value ) {
			if ( in_array( $key, $this->searchable_meta_keys ) ) {
				$key                = self::$meta_prefix . $key;
				$meta_input[ $key ] = $value;
			} else {
				$meta_input[ $this->hashed_meta_key ][ $key ] = $value;
			}
		}

		if ( empty( $this->status ) ) {
			$this->status = Tribe__Tickets__Commerce__PayPal__Stati::$undefined;
		}

		$postarr = array(
			'post_type'   => Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT,
			'post_title'  => $this->paypal_order_id,
			'post_status' => $this->status,
			'meta_input'  => $meta_input,
		);

		/**
		 * Filters the post array that will be saved to the database for the Order post.
		 *
		 * @since 4.7
		 *
		 * @param array                                   $postarr
		 * @param Tribe__Tickets__Commerce__PayPal__Order $order
		 */
		$postarr = apply_filters( 'tribe_tickets_tpp_order_postarr', $postarr, $this );

		if ( empty( $this->post_id ) ) {
			$post_id = wp_insert_post( $postarr );
		} else {
			// remove any existing PayPal meta before the update
			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d and meta_key LIKE %s",
					$this->post_id,
					self::$meta_prefix . '%'
				)
			);
			wp_cache_delete( $this->post_id, 'post_meta' );

			$postarr['ID'] = $this->post_id;

			$post_id = wp_update_post( $postarr );
		}

		if ( ! empty( $post_id ) ) {
			foreach ( $this->ticket_ids as $ticket_id ) {
				add_post_meta( $post_id, self::$meta_prefix . 'ticket', $ticket_id );
			}

			foreach ( $this->post_ids as $related_post_id ) {
				add_post_meta( $post_id, self::$meta_prefix . 'post', $related_post_id );
			}
		}
	}

	/**
	 * Returns the Order status.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Returns the Order status label.
	 *
	 * @since 4.10
	 *
	 * @return string
	 */
	public function get_status_label() {
		return $this->status_label;
	}

	/**
	 * Returns the revenue generated by the Order.
	 *
	 * @since 4.7
	 *
	 * @return int
	 */
	public function get_revenue() {
		/** @var Tribe__Tickets__Commerce__PayPal__Stati $stati */
		$stati = tribe( 'tickets.commerce.paypal.stati' );

		if ( $stati->is_revenue_generating_status( $this->status ) ) {
			return ! empty( $this->meta['mc_gross'] ) ? (int) $this->meta['mc_gross'] : 0;
		}

		return 0;
	}

	/**
	 * Returns the sub total of order regardless of status
	 *
	 * @since 4.10
	 *
	 * @return int a positive number for sub total
	 */
	public function get_sub_total() {

		return ! empty( $this->meta['mc_gross'] ) ? absint( $this->meta['mc_gross'] ) : 0;

	}

	/**
	 * Returns the line total for this Order.
	 *
	 * Note that the line total might be non-zero when the ticket revenue is, instead,
	 * zero (e.g. pending orders).
	 *
	 * @since 4.7
	 *
	 * @return int|float
	 */
	public function get_line_total() {
		$statuses = array(
			Tribe__Tickets__Commerce__PayPal__Stati::$completed,
			Tribe__Tickets__Commerce__PayPal__Stati::$pending,
			Tribe__Tickets__Commerce__PayPal__Stati::$denied,
		);

		/**
		 * Filters the Order statuses that should display a non-zero line total.
		 *
		 * @since 4.7
		 *
		 * @param array                                   $statuses
		 * @param Tribe__Tickets__Commerce__PayPal__Order $this
		 */
		$statuses = apply_filters( 'tribe_tickets_tpp_order_line_total_statuses', $statuses, $this );

		if ( in_array( $this->status, $statuses ) ) {
			return ! empty( $this->meta['mc_gross'] ) ? (float) $this->meta['mc_gross'] : 0;
		}

		return 0;
	}

	/**
	 * Relates an order with a refund order.
	 *
	 * @since 4.7
	 *
	 * @param string $refund_order_id A PayPal order ID (hash).
	 */
	public function refund_with( $refund_order_id ) {
		$this->status                  = Tribe__Tickets__Commerce__PayPal__Stati::$refunded;
		$this->meta['refund_order_id'] = $refund_order_id;
	}

	/**
	 * Returns the refund order PayPal ID (hash) if any.
	 *
	 * @since 4.7
	 *
	 * @return string|null
	 */
	public function get_refund_order_id() {
		return Tribe__Utils__Array::get( $this->meta, 'refund_order_id', null );
	}

	/**
	 * Returns the quantity of tickets part of the Order.
	 *
	 * @since 4.7
	 *
	 * @param int|null $ticket_id An optional ticket post ID; if this
	 *                            parameter is passed then the method will
	 *                            return the quantity of specific tickets part
	 *                            of the Order.
	 *
	 * @return float|int Either the total quantity of tickets part of the Order
	 *                   or the quantity of a specific ticket part of the Order.
	 */
	public function get_item_quantity( $ticket_id = null ) {
		$items = $this->meta['items'];

		if ( null !== $ticket_id ) {
			$items = wp_list_filter( $items, array( 'ticket_id' => $ticket_id ) );
		}

		$quantities = array_filter( wp_list_pluck( $items, 'quantity' ), 'is_numeric' );

		return ! empty( $quantities )
			? array_sum( array_map( 'intval', $quantities ) )
			: 0;
	}

	/**
	 * Returns the post IDs related ot this Order.
	 *
	 * @since 4.7
	 *
	 * @param string $post_type Only return post IDs for
	 *                          this post type.
	 *
	 * @return array
	 */
	public function get_related_post_ids( $post_type = null ) {
		$post_ids = $this->post_ids;

		if ( null !== $post_type ) {
			$candidates = array_map( 'get_post', $post_ids );
			$filtered   = wp_list_filter( $candidates, array( 'post_type' => $post_type ) );
			$post_ids   = wp_list_pluck( $filtered, 'ID' );
		}

		return $post_ids;
	}

	/**
	 * Deletes an Order and its related data from the database.
	 *
	 * @since 4.7
	 *
	 * @param bool $delete_attendees Whether Attendees for the order should be deleted or not.
	 * @param bool $force_delete     Whether the Order deletion should be forced or not.
	 *
	 * @return false|null|\WP_Post The delete operation exit status.
	 *
	 * @see   wp_delete_post()
	 */
	public function delete( $delete_attendees = true, $force_delete = false ) {
		/**
		 * Fires before an Order, and its related data, is deleted.
		 *
		 * @since 4.7
		 *
		 * @param WP_Post|false|null $post_id          The Order post ID
		 * @param bool               $delete_attendees Whether attendees should be deleted or not
		 * @param bool               $force_delete     Whether the Order deletion should be forced or not
		 * @param Tribe__Tickets__Commerce__PayPal__Order This Order object
		 */
		do_action( 'tribe_tickets_tpp_after_before_delete', $this->post_id, $delete_attendees, $force_delete, $this );

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		foreach ( $this->attendees as $attendee ) {
			if ( $delete_attendees ) {
				$paypal->delete_ticket( (int) $attendee['event_id'], (int) $attendee['attendee_id'] );
			}
			$this->remove_attendee( (int) $attendee['attendee_id'] );
		}

		$deleted = wp_delete_post( $this->post_id, $force_delete );

		/**
		 * Fires after an Order, and its related data, is deleted.
		 *
		 * @since 4.7
		 *
		 * @param WP_Post|false|null $deleted          The exit status of the delete operation
		 * @param bool               $delete_attendees Whether attendees have been deleted or not
		 * @param bool               $force_delete     Whether the Order deletion was forced or not
		 * @param Tribe__Tickets__Commerce__PayPal__Order This Order object
		 */
		do_action( 'tribe_tickets_tpp_after_after_delete', $deleted, $delete_attendees, $force_delete, $this );

		return $deleted;
	}

	/**
	 * Returns the post IDs of the tickets related to this Order.
	 *
	 * @since 4.7
	 *
	 * @return array
	 */
	public function get_ticket_ids() {
		return $this->ticket_ids;
	}

	/**
	 * Returns the local date and time this Order post was originally created.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_creation_date() {
		return $this->created;
	}

	/**
	 * Returns the local date and time this Order post was last updated.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_modified_date() {
		return $this->modified;
	}

	/**
	 * Whether the Order previous status was pending or not.
	 *
	 * @since 4.7
	 *
	 * @return bool
	 */
	public function was_pending() {
		return $this->was_pending;
	}

	/**
	 * Returns the Order post ID.
	 *
	 * Please note that the returned post ID is the Order one,
	 * not that of related posts.
	 *
	 * @since 4.7
	 *
	 * @return int This Order post ID
	 *
	 * @see \Tribe__Tickets__Commerce__PayPal__Order::get_related_post_ids()
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Returns the link to an Order (aka "transaction") link on PayPal.
	 *
	 * @since 4.7
	 *
	 * @param string $paypal_order_id The Order PayPal ID (hash).
	 */
	public static function get_order_link( $paypal_order_id ) {
		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );
		return $gateway->get_transaction_url( $paypal_order_id );
	}
}
