<?php

namespace TEC\Tickets\Commerce\Repositories;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Module;
use \Tribe__Repository;
use TEC\Tickets\Commerce\Order;
use Tribe__Repository__Usage_Error as Usage_Error;

use Tribe__Utils__Array as Arr;
use Tribe__Date_Utils as Dates;

/**
 * Class Order
 *
 * @since 5.1.9
 */
class Order_Repository extends Tribe__Repository {
	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $filter_name = 'tc_orders';

	/**
	 * Key name to use when limiting lists of keys.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $key_name = \TEC\Tickets\Commerce::ABBR;

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();

		$insert_status = tribe( Commerce\Status\Status_Handler::class )->get_insert_status();

		// Set the order post type.
		$this->default_args['post_type']   = Order::POSTTYPE;
		$this->default_args['post_status'] = $insert_status->get_wp_slug();
		$this->create_args['post_status']  = $insert_status->get_wp_slug();
		$this->create_args['post_type']    = Order::POSTTYPE;
		$this->create_args['currency']     = tribe_get_option( Commerce\Settings::$option_currency_code, 'USD' );

		// Add event specific aliases.
		$this->update_fields_aliases = array_merge(
			$this->update_fields_aliases,
			[
				'gateway'              => Order::$gateway_meta_key,
				'gateway_order_id'     => Order::$gateway_order_id_meta_key,
				'items'                => Order::$items_meta_key,
				'total_value'          => Order::$total_value_meta_key,
				'currency'             => Order::$currency_meta_key,
				'purchaser_user_id'    => Order::$purchaser_user_id_meta_key,
				'purchaser_full_name'  => Order::$purchaser_full_name_meta_key,
				'purchaser_first_name' => Order::$purchaser_first_name_meta_key,
				'purchaser_last_name'  => Order::$purchaser_last_name_meta_key,
				'purchaser_email'      => Order::$purchaser_email_meta_key,
				'hash'                 => Order::$hash_meta_key,
			]
		);

		$this->schema = array_merge(
			$this->schema,
			[
				'tickets'     => [ $this, 'filter_by_tickets' ],
				'tickets_not' => [ $this, 'filter_by_tickets_not' ],
				'events'      => [ $this, 'filter_by_events' ],
				'events_not'  => [ $this, 'filter_by_events_not' ],
			]
		);

		$this->add_simple_meta_schema_entry( 'gateway', Order::$gateway_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'gateway_order_id', Order::$gateway_order_id_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'currency', Order::$currency_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'purchaser_full_name', Order::$purchaser_full_name_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'purchaser_first_name', Order::$purchaser_first_name_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'purchaser_last_name', Order::$purchaser_last_name_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'purchaser_email', Order::$purchaser_email_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'hash', Order::$hash_meta_key, 'meta_equals' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function format_item( $id ) {
		$formatted = null === $this->formatter
			? tec_tc_get_order( $id )
			: $this->formatter->format_item( $id );

		/**
		 * Filters a single formatted order result.
		 *
		 * @since 5.1.9
		 *
		 * @param mixed|\WP_Post                $formatted The formatted event result, usually a post object.
		 * @param int                           $id        The formatted post ID.
		 * @param \Tribe__Repository__Interface $this      The current repository object.
		 */
		$formatted = apply_filters( 'tec_tickets_commerce_repository_order_format', $formatted, $id, $this );

		return $formatted;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_postarr_for_create( array $postarr ) {
		if ( isset( $postarr['meta_input'] ) ) {
			$postarr = $this->filter_meta_input( $postarr );
		}

		if ( ! empty( $postarr['gateway_payload'] ) ) {
			$payload = $postarr['gateway_payload'];
			unset( $postarr['gateway_payload'] );

			$status = tribe( Commerce\Status\Status_Handler::class )->get_by_wp_slug( $this->create_args['post_status'] );

			if ( $status ) {
				$postarr['meta_input'][ Order::get_gateway_payload_meta_key( $status ) ] = $payload;
			}
		}

		return parent::filter_postarr_for_create( $postarr );
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_postarr_for_update( array $postarr, $post_id ) {
		if ( isset( $postarr['meta_input'] ) ) {
			$postarr = $this->filter_meta_input( $postarr, $post_id );
		}

		if ( ! empty( $postarr['tickets_in_order'] ) ) {
			$tickets = array_filter( array_unique( (array) $postarr['tickets_in_order'] ) );
			unset( $postarr['tickets_in_order'] );

			// Delete all of the previous ones when updating.
			delete_post_meta( $post_id, Order::$tickets_in_order_meta_key );

			foreach ( $tickets as $ticket_id ) {
				add_post_meta( $post_id, Order::$tickets_in_order_meta_key, $ticket_id );
			}
		}

		if ( ! empty( $postarr['events_in_order'] ) ) {
			$events = array_filter( array_unique( (array) $postarr['events_in_order'] ) );
			unset( $postarr['events_in_order'] );

			// Delete all of the previous ones when updating.
			delete_post_meta( $post_id, Order::$events_in_order_meta_key );

			foreach ( $events as $event_id ) {
				add_post_meta( $post_id, Order::$events_in_order_meta_key, $event_id );
			}
		}

		if ( ! empty( $postarr['meta_input']['gateway_payload'] ) ) {
			$payload = $postarr['meta_input']['gateway_payload'];
			unset( $postarr['meta_input']['gateway_payload'] );

			$status = tribe( Commerce\Status\Status_Handler::class )->get_by_wp_slug( $postarr['post_status'] );

			if ( $status ) {
				add_post_meta( $post_id, Order::get_gateway_payload_meta_key( $status ), $payload );
			}
		}

		return parent::filter_postarr_for_update( $postarr, $post_id );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_create_callback( array $postarr ) {
		$callback = parent::get_create_callback( $postarr );

		// only modify if the filters didn't change anything.
		if ( 'wp_insert_post' === $callback ) {
			$callback = [ $this, 'create_order_with_meta' ];
		}

		return $callback;
	}

	/**
	 * When creating an order via the repository there are two meta elements that need to be added using
	 * `add_post_meta` with the $unique param set to false.
	 *
	 * So we hijack the default create callback for this repository to allow for that behavior to exist.
	 *
	 * @since 5.1.9
	 *
	 * @param array $postarr The post array that will be used for the creation.
	 *
	 * @return int The Post ID.
	 */
	protected function create_order_with_meta( array $postarr ) {
		$callback = parent::get_create_callback( $postarr );

		$tickets = [];
		if ( ! empty( $postarr['meta_input']['tickets_in_order'] ) ) {
			$tickets = array_filter( array_unique( (array) $postarr['meta_input']['tickets_in_order'] ) );
			unset( $postarr['meta_input']['tickets_in_order'] );
		}

		$events = [];
		if ( ! empty( $postarr['meta_input']['events_in_order'] ) ) {
			$events = array_filter( array_unique( (array) $postarr['meta_input']['events_in_order'] ) );
			unset( $postarr['meta_input']['events_in_order'] );
		}

		$created = call_user_func( $callback, $postarr );

		// Dont add in case we are dealing with a failed insertion.
		if ( ! is_wp_error( $created ) ) {
			foreach ( $events as $event_id ) {
				add_post_meta( $created, Order::$events_in_order_meta_key, $event_id );
			}

			foreach ( $tickets as $ticket_id ) {
				add_post_meta( $created, Order::$tickets_in_order_meta_key, $ticket_id );
			}
		}

		return $created;
	}

	/**
	 * Filters the tickets data from the input so we can properly save the cart items.
	 *
	 * @since 5.1.9
	 *
	 * @param array    $postarr Data set that needs filtering.
	 * @param null|int $post_id When we are dealing with an Update we have an ID here.
	 *
	 * @return array
	 */
	protected function filter_gateway_payload( $postarr, $post_id = null ) {
		$meta  = Arr::get( $postarr, 'meta_input', [] );
		$items = Arr::get( $meta, 'gateway_payload', [] );

		if ( ! empty( $items ) ) {
			$statuses = tribe( Commerce\Status\Status_Handler::class )->get_all();

		}

		return $postarr;
	}

	/**
	 * Filters the tickets data from the input so we can properly save the cart items.
	 *
	 * @since 5.1.9
	 *
	 * @param array    $postarr Data set that needs filtering.
	 * @param null|int $post_id When we are dealing with an Update we have an ID here.
	 *
	 * @return array
	 */
	protected function filter_items_input( $postarr, $post_id = null ) {
		$meta  = Arr::get( $postarr, 'meta_input', [] );
		$items = Arr::get( $meta, Order::$items_meta_key, [] );

		if ( ! empty( $items ) ) {
			$ticket_ids    = array_unique( array_filter( array_values( wp_list_pluck( $items, 'ticket_id' ) ) ) );
			$event_objects = array_map( [ tribe( Module::class ), 'get_event_for_ticket' ], $ticket_ids );
			$event_ids     = array_unique( array_filter( array_values( wp_list_pluck( $event_objects, 'ID' ) ) ) );

			// These will be remove right before actually creating the order.
			$postarr['meta_input']['tickets_in_order'] = $ticket_ids;
			$postarr['meta_input']['events_in_order']  = $event_ids;
		}

		return $postarr;
	}

	/**
	 * Filters the Purchaser data from the input so we can properly save the data.
	 *
	 * @since 5.1.9
	 *
	 * @param array    $postarr Data set that needs filtering.
	 * @param null|int $post_id When we are dealing with an Update we have an ID here.
	 *
	 * @return array
	 */
	protected function filter_purchaser_input( $postarr, $post_id = null ) {
		$meta      = Arr::get( $postarr, 'meta_input', [] );
		$purchaser = Arr::get( $meta, 'purchaser', [] );

		if ( is_numeric( $purchaser ) && $user = get_userdata( $purchaser ) ) {
			$full_name  = $user->display_name;
			$first_name = $user->first_name;
			$last_name  = $user->last_name;
			$email      = $user->user_email;
		} else {
			$full_name  = Arr::get( $purchaser, 'full_name' );
			$first_name = Arr::get( $purchaser, 'first_name' );
			$last_name  = Arr::get( $purchaser, 'last_name' );
			$email      = Arr::get( $purchaser, 'email' );
		}

		// Maybe set the first / last name.
		if ( empty( $first_name ) || empty( $last_name ) ) {
			$first_name = $full_name;
			$last_name  = '';

			// Get first name and last name.
			if ( false !== strpos( $full_name, ' ' ) ) {
				$name_parts = explode( ' ', $full_name );

				// First name is first text.
				$first_name = array_shift( $name_parts );

				// Last name is everything the first text.
				$last_name = implode( ' ', $name_parts );
			}
		}

		$postarr['meta_input'][ Order::$purchaser_email_meta_key ]      = $email;
		$postarr['meta_input'][ Order::$purchaser_full_name_meta_key ]  = $full_name;
		$postarr['meta_input'][ Order::$purchaser_first_name_meta_key ] = $first_name;
		$postarr['meta_input'][ Order::$purchaser_last_name_meta_key ]  = $last_name;

		unset( $postarr['meta_input']['purchaser'] );

		return $postarr;
	}

	/**
	 * Filters and updates the order meta to make sure it makes sense.
	 *
	 * @since 5.1.9
	 *
	 * @param array $postarr The update post array, passed entirely for context purposes.
	 * @param int   $post_id The ID of the event that's being updated.
	 *
	 * @return array The filtered postarr array.
	 */
	protected function filter_meta_input( array $postarr, $post_id = null ) {
		if ( ! empty( $postarr['meta_input']['purchaser'] ) ) {
			$postarr = $this->filter_purchaser_input( $postarr, $post_id );
		}

		if ( ! empty( $postarr['meta_input']['gateway_payload'] ) ) {
			$postarr = $this->filter_gateway_payload( $postarr, $post_id );
		}

		if ( ! empty( $postarr['meta_input'][ Order::$items_meta_key ] ) ) {
			$postarr = $this->filter_items_input( $postarr, $post_id );
		}

		return $postarr;
	}

	/**
	 * Cleans up a list of Post IDs into an usable array for DB query.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post|int[]|\WP_Post[] $posts Which posts we are filtering by.
	 *
	 * @return array
	 */
	protected function clean_post_ids( $posts ) {
		return array_unique( array_filter( array_map( static function ( $post ) {
			if ( is_numeric( $post ) ) {
				return $post;
			}

			if ( $post instanceof \WP_Post ) {
				return $post->ID;
			}

			return null;
		}, (array) $posts ) ) );
	}

	/**
	 * Filters order by whether or not it contains a given ticket/s.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post|int[]|\WP_Post[] $tickets Which tickets we are filtering by.
	 *
	 * @return null
	 */
	public function filter_by_tickets( $tickets = null ) {
		if ( empty( $tickets ) ) {
			return null;
		}

		$tickets = $this->clean_post_ids( $tickets );

		if ( empty( $tickets ) ) {
			return null;
		}

		$this->by( 'meta_in', Order::$tickets_in_order_meta_key, $tickets );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given ticket/s.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post|int[]|\WP_Post[] $tickets Which tickets we are filtering by.
	 *
	 * @return null
	 */
	public function filter_by_tickets_not( $tickets = null ) {
		if ( empty( $tickets ) ) {
			return null;
		}

		$tickets = $this->clean_post_ids( $tickets );

		if ( empty( $tickets ) ) {
			return null;
		}

		$this->by( 'meta_not_in', Order::$tickets_in_order_meta_key, $tickets );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given ticket/s.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post|int[]|\WP_Post[] $events Which events we are filtering by.
	 *
	 * @return null
	 */
	public function filter_by_events( $events = null ) {
		if ( empty( $events ) ) {
			return null;
		}

		$events = $this->clean_post_ids( $events );

		if ( empty( $events ) ) {
			return null;
		}

		$this->by( 'meta_in', Order::$events_in_order_meta_key, $events );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given event/s.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post|int[]|\WP_Post[] $events Which events we are filtering by.
	 *
	 * @return null
	 */
	public function filter_by_events_not( $events = null ) {
		if ( empty( $events ) ) {
			return null;
		}

		$events = $this->clean_post_ids( $events );

		if ( empty( $events ) ) {
			return null;
		}

		$this->by( 'meta_not_in', Order::$events_in_order_meta_key, $events );

		return null;
	}

}