<?php

use Tribe__Utils__Array as Arr;

/**
 * Class Tribe__Tickets__Attendee_Repository
 *
 * The basic Attendee repository.
 *
 * @since 4.8
 */
class Tribe__Tickets__Attendee_Repository extends Tribe__Repository {

	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @var string
	 */
	protected $filter_name = 'attendees';

	/**
	 * Key name to use when limiting lists of keys.
	 *
	 * @since 5.1.0
	 *
	 * @var string
	 */
	protected $key_name = '';

	/**
	 * @var array An array of all the order statuses supported by the repository.
	 */
	protected static $order_statuses;

	/**
	 * The attendee provider object.
	 *
	 * @since 5.1.0
	 *
	 * @var Tribe__Tickets__Tickets
	 */
	protected $attendee_provider;

	/**
	 * @var array An array of all the public order statuses supported by the repository.
	 *            This list is hand compiled as reduced and easier to maintain.
	 */
	protected static $public_order_statuses = [
		'yes',     // RSVP
		'completed', // PayPal Legacy
		'wc-completed', // WooCommerce
		'publish', // Easy Digital Downloads
	];

	/**
	 * @var array An array of all the private order statuses supported by the repository.
	 */
	protected static $private_order_statuses;

	/**
	 * Tribe__Tickets__Attendee_Repository constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->create_args['post_type']   = current( $this->attendee_types() );
		$this->create_args['post_status'] = 'publish';
		$this->create_args['ping_status'] = 'closed';

		$this->default_args = array_merge( $this->default_args, [
			'post_type'   => $this->attendee_types(),
			'orderby'     => [ 'date', 'title', 'ID' ],
			'post_status' => 'any',
		] );

		// Add initial simple schema.
		$this->add_simple_meta_schema_entry( 'event', $this->attendee_to_event_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'event__not_in', $this->attendee_to_event_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'ticket', $this->attendee_to_ticket_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'ticket__not_in', $this->attendee_to_ticket_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'order', $this->attendee_to_order_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'order__not_in', $this->attendee_to_order_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'product_id', $this->attendee_to_ticket_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'product_id__not_in', $this->attendee_to_ticket_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'purchaser_name', $this->purchaser_name_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'purchaser_name__not_in', $this->purchaser_name_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'purchaser_name__like', $this->purchaser_name_keys(), 'meta_like' );
		$this->add_simple_meta_schema_entry( 'purchaser_email', $this->purchaser_email_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'purchaser_email__not_in', $this->purchaser_email_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'purchaser_email__like', $this->purchaser_email_keys(), 'meta_like' );
		$this->add_simple_meta_schema_entry( 'holder_name', $this->holder_name_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'holder_name__not_in', $this->holder_name_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'holder_name__like', $this->holder_name_keys(), 'meta_like' );
		$this->add_simple_meta_schema_entry( 'holder_email', $this->holder_email_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'holder_email__not_in', $this->holder_email_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'holder_email__like', $this->holder_email_keys(), 'meta_like' );
		$this->add_simple_meta_schema_entry( 'security_code', $this->security_code_keys(), 'meta_in' );
		$this->add_simple_meta_schema_entry( 'security_code__not_in', $this->security_code_keys(), 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'user', '_tribe_tickets_attendee_user_id', 'meta_in' );
		$this->add_simple_meta_schema_entry( 'user__not_in', '_tribe_tickets_attendee_user_id', 'meta_not_in' );
		$this->add_simple_meta_schema_entry( 'price', '_paid_price' );

		$this->schema = array_merge( $this->schema, [
			'checkedin'             => [ $this, 'filter_by_checkedin' ],
			'event__show_attendees' => [ $this, 'filter_by_show_attendees' ],
			'event_status'          => [ $this, 'filter_by_event_status' ],
			'has_attendee_meta'     => [ $this, 'filter_by_attendee_meta_existence' ],
			'optout'                => [ $this, 'filter_by_optout' ],
			'order_status__not_in'  => [ $this, 'filter_by_order_status_not_in' ],
			'order_status'          => [ $this, 'filter_by_order_status' ],
			'price_max'             => [ $this, 'filter_by_price_max' ],
			'price_min'             => [ $this, 'filter_by_price_min' ],
			'provider__not_in'      => [ $this, 'filter_by_provider_not_in' ],
			'provider'              => [ $this, 'filter_by_provider' ],
			'rsvp_status__or_none'  => [ $this, 'filter_by_rsvp_status_or_none' ],
			'rsvp_status'           => [ $this, 'filter_by_rsvp_status' ],
		] );

		// Add object default aliases.
		$this->update_fields_aliases = array_merge(
			$this->update_fields_aliases,
			[
				'ticket_id'      => '_tribe_tickets_ticket_id',
				'event_id'       => '_tribe_tickets_post_id',
				'post_id'        => '_tribe_tickets_post_id',
				'security_code'  => '_tribe_tickets_security_code',
				'order_id'       => '_tribe_tickets_order_id',
				'optout'         => '_tribe_tickets_optout',
				'user_id'        => '_tribe_tickets_user_id',
				'price_paid'     => '_tribe_tickets_price_paid',
				'price_currency' => '_tribe_tickets_price_currency_symbol',
				'full_name'      => '_tribe_tickets_full_name',
				'email'          => '_tribe_tickets_email',
			]
		);

		$this->init_order_statuses();
	}

	/**
	 * Returns an array of the attendee types handled by this repository.
	 *
	 * Extending repository classes should override this to add more attendee types.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function attendee_types() {
		return [
			'rsvp'                          => 'tribe_rsvp_attendees',
			'tribe-commerce'                => 'tribe_tpp_attendees',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::POSTTYPE,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function attendee_to_event_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_event',
			'tribe-commerce'                => '_tribe_tpp_event',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$event_relation_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Ticket.
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function attendee_to_ticket_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_product',
			'tribe-commerce'                => '_tribe_tpp_product',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$ticket_relation_meta_key,
		];
	}

	/**
	 * Returns a list of meta keys relating an attendee to the order
	 * that generated it.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	protected function attendee_to_order_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_order',
			'tribe-commerce'                => '_tribe_tpp_order',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$order_relation_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.10.6
	 *
	 * @return array
	 */
	public function purchaser_name_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_full_name',
			'tribe-commerce'                => '_tribe_tpp_full_name',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$purchaser_name_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.10.6
	 *
	 * @return array
	 */
	public function purchaser_email_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_email',
			'tribe-commerce'                => '_tribe_tpp_email',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$purchaser_email_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 5.2.1
	 *
	 * @return array
	 */
	public function holder_name_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_full_name',
			'tribe-commerce'                => '_tribe_tickets_full_name',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$full_name_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 5.2.1
	 *
	 * @return array
	 */
	public function holder_email_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_email',
			'tribe-commerce'                => '_tribe_tickets_email',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$email_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.10.6
	 *
	 * @return array
	 */
	public function security_code_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_security_code',
			'tribe-commerce'                => '_tribe_tpp_security_code',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$security_code_meta_key,
		];
	}

	/**
	 * Returns the list of meta keys denoting an Attendee optout choice.
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function attendee_optout_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_attendee_optout',
			'tribe-commerce'                => '_tribe_tpp_attendee_optout',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$optout_meta_key,
		];
	}

	/**
	 * Returns a list of meta keys indicating an attendee checkin status.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function checked_in_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_checkedin',
			'tribe-commerce'                => '_tribe_tpp_checkedin',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$checked_in_meta_key,
		];
	}

	/**
	 * Provides arguments to filter attendees by their optout status.
	 *
	 * @since 4.8
	 *
	 * @param string $optout An optout option, supported 'yes','no','any'.
	 *
	 * @return array|null
	 */
	public function filter_by_optout( $optout ) {
		global $wpdb;

		switch ( $optout ) {
			case 'any':
				return null;
				break;
			case 'no':
				$this->by( 'meta_not_in', $this->attendee_optout_keys(), [ 'yes', 1 ] );
				break;
			case 'yes':
				$this->by( 'meta_in', $this->attendee_optout_keys(), [ 'yes', 1 ] );
				break;
			case 'no_or_none':
				$optout_keys = $this->attendee_optout_keys();
				$optout_keys = array_map( [ $wpdb, '_real_escape' ], $optout_keys );
				$optout_keys = "'" . implode( "', '", $optout_keys ) . "'";

				$this->filter_query->join( "
					LEFT JOIN {$wpdb->postmeta} attendee_optout
					ON ( attendee_optout.post_id = {$wpdb->posts}.ID
						AND attendee_optout.meta_key IN ( {$optout_keys} ) )
				" );

				$this->filter_query->where( "(
					attendee_optout.post_id IS NULL
					OR attendee_optout.meta_value NOT IN ( 'yes', '1' )
				)" );

				break;
		}

		return null;
	}

	/**
	 * Provides arguments to filter attendees by a specific RSVP status.
	 *
	 * @since 4.8
	 *
	 * @param string $rsvp_status
	 *
	 * @return array
	 */
	public function filter_by_rsvp_status( $rsvp_status ) {
		return Tribe__Repository__Query_Filters::meta_in(
			Tribe__Tickets__RSVP::ATTENDEE_RSVP_KEY,
			$rsvp_status,
			'by-rsvp-status'
		);
	}

	/**
	 * Provides arguments to filter attendees by a specific RSVP status or no status at all.
	 *
	 * Mind that we allow tickets not to have an RSVP status at all and
	 * still match. This assumes that all RSVP tickets will have a status
	 * assigned (which is the default behaviour).
	 *
	 * @since 4.8
	 *
	 * @param string $rsvp_status
	 *
	 * @return array
	 */
	public function filter_by_rsvp_status_or_none( $rsvp_status ) {
		return Tribe__Repository__Query_Filters::meta_in_or_not_exists(
			Tribe__Tickets__RSVP::ATTENDEE_RSVP_KEY,
			$rsvp_status,
			'by-rsvp-status-or-none'
		);
	}

	/**
	 * Provides arguments to filter attendees by the ticket provider.
	 *
	 * To avoid lengthy queries we check if a provider specific meta
	 * key relating the Attendee to the event (a post) is set.
	 *
	 * @since 4.8
	 *
	 * @param string|array $provider A provider supported slug or an
	 *                               array of supported provider slugs.
	 *
	 * @return array
	 */
	public function filter_by_provider( $provider ) {
		$providers = Arr::list_to_array( $provider );
		$meta_keys = Arr::map_or_discard( (array) $providers, $this->attendee_to_event_keys() );

		$this->by( 'meta_exists', $meta_keys );
	}

	/**
	 * Provides arguments to exclude attendees by the ticket provider.
	 *
	 * To avoid lengthy queries we check if a provider specific meta
	 * key relating the Attendee to the event (a post) is not set.
	 *
	 * @since 4.8
	 *
	 * @param string|array $provider A provider supported slug or an
	 *                               array of supported provider slugs.
	 *
	 * @return array
	 */
	public function filter_by_provider_not_in( $provider ) {
		$providers = Arr::list_to_array( $provider );
		$meta_keys = Arr::map_or_discard( (array) $providers, $this->attendee_to_event_keys() );

		$this->by( 'meta_not_exists', $meta_keys );
	}

	/**
	 * Filters attendee to only get those related to posts with a specific status.
	 *
	 * @since 4.8
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the requested statuses are not accessible by the user.
	 *
	 * @param string|array $event_status
	 *
	 */
	public function filter_by_event_status( $event_status ) {
		$statuses = Arr::list_to_array( $event_status );

		$can_read_private_posts = current_user_can( 'read_private_posts' );

		// map the `any` meta-status
		if ( 1 === count( $statuses ) && 'any' === $statuses[0] ) {
			if ( ! $can_read_private_posts ) {
				$statuses = [ 'publish' ];
			} else {
				// no need to filter if the user can read all posts
				return;
			}
		}

		if ( ! $can_read_private_posts ) {
			$event_status = array_intersect( $statuses, [ 'publish' ] );
		}

		if ( empty( $event_status ) ) {
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results(
				'The user cannot read posts with the requested post statuses.'
			);
		}

		$this->where_meta_related_by(
			$this->attendee_to_event_keys(),
			'IN',
			'post_status',
			$statuses
		);
	}

	/**
	 * Filters attendee to only get those related to posts with "Show attendees list on event page" set to true.
	 *
	 *
	 * @since 4.11.1
	 */
	public function filter_by_show_attendees() {
		$this->where_meta_related_by_meta(
			$this->attendee_to_event_keys(),
			'=',
			'_tribe_hide_attendees_list',
			1,
			true
		);
	}

	/**
	 * Filters attendee to only get those related to orders with a specific ID.
	 *
	 * @since TVD
	 *
	 * @param string|array $order_id Order ID(s).
	 */
	public function filter_by_order( $order_id ) {
		$order_ids = Arr::list_to_array( $order_id );

		$this->by( 'meta_in', $this->attendee_to_order_keys(), $order_ids );
	}

	/**
	 * Filters attendee to only get those related to orders with a specific status.
	 *
	 * @since 4.8
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the requested statuses are not accessible by the user.
	 *
	 * @param string       $type         Type of matching (in, not_in, like).
	 *
	 * @param string|array $order_status Order status.
	 */
	public function filter_by_order_status( $order_status, $type = 'in' ) {
		$statuses = Arr::list_to_array( $order_status );

		$has_manage_access = current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' );

		// map the `any` meta-status
		if ( 1 === count( $statuses ) && 'any' === $statuses[0] ) {
			if ( ! $has_manage_access ) {
				$statuses = [ 'public' ];
			} else {
				// no need to filter if the user can read all posts
				return;
			}
		}

		// Allow the user to define singular statuses or the meta-status "public"
		if ( in_array( 'public', $statuses, true ) ) {
			$statuses = array_unique( array_merge( $statuses, self::$public_order_statuses ) );
		}

		// Allow the user to define singular statuses or the meta-status "private"
		if ( in_array( 'private', $statuses, true ) ) {
			$statuses = array_unique( array_merge( $statuses, self::$private_order_statuses ) );
		}

		// Remove any status the user cannot access
		if ( ! $has_manage_access ) {
			$statuses = array_intersect( $statuses, self::$public_order_statuses );
		}

		if ( empty( $statuses ) ) {
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results(
				'The user cannot access the requested attendee order statuses.'
			);
		}

		/** @var wpdb $wpdb */
		global $wpdb;

		$value_operator = 'IN';
		$value_clause   = "( '" . implode( "','", array_map( [ $wpdb, '_escape' ], $statuses ) ) . "' )";

		if ( 'not_in' === $type ) {
			$value_operator = 'NOT IN';
		}

		$has_plus_providers = class_exists( 'Tribe__Tickets_Plus__Commerce__EDD__Main' )
		                      || class_exists( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' );

		$this->filter_query->join( "
			LEFT JOIN {$wpdb->postmeta} order_status_meta
			ON order_status_meta.post_id = {$wpdb->posts}.ID
		", 'order-status-meta' );

		$et_where_clause = "
			(
				order_status_meta.meta_key IN ( '_tribe_rsvp_status', '_tribe_tpp_status' )
				AND order_status_meta.meta_value {$value_operator} {$value_clause}
			)
		";

		if ( ! $has_plus_providers ) {
			$this->filter_query->where( $et_where_clause );
		} else {
			$this->filter_query->join( "
				LEFT JOIN {$wpdb->posts} order_status_post
				ON order_status_post.ID = order_status_meta.meta_value
			", 'order-status-post' );

			$this->filter_query->where( "
				(
					{$et_where_clause}
					OR (
						order_status_meta.meta_key IN ( '_tribe_wooticket_order','_tribe_eddticket_order' )
						AND order_status_post.post_status {$value_operator} {$value_clause}
					)
				)
			" );
		}
	}

	/**
	 * Filters attendee to only get those not related to orders with a specific status.
	 *
	 * @since 4.10.6
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the requested statuses are not accessible by the user.
	 *
	 * @param string|array $order_status
	 *
	 */
	public function filter_by_order_status_not_in( $order_status ) {
		$this->filter_by_order_status( $order_status, 'not_in' );
	}

	/**
	 * Filters Attendees by a minimum paid price.
	 *
	 * @since 4.8
	 *
	 * @param int $price_min
	 */
	public function filter_by_price_min( $price_min ) {
		$this->by( 'meta_gte', '_paid_price', (int) $price_min );
	}

	/**
	 * Filters Attendees by a maximum paid price.
	 *
	 * @since 4.8
	 *
	 * @param int $price_max
	 */
	public function filter_by_price_max( $price_max ) {
		$this->by( 'meta_lte', '_paid_price', (int) $price_max );
	}

	/**
	 * Filters attendee depending on them having additional
	 * information or not.
	 *
	 * @since 4.8
	 *
	 * @param bool $exists
	 */
	public function filter_by_attendee_meta_existence( $exists ) {
		if ( $exists ) {
			$this->by( 'meta_exists', '_tribe_tickets_meta' );
		} else {
			$this->by( 'meta_not_exists', '_tribe_tickets_meta' );
		}
	}

	/**
	 * Filters attendees depending on their checkedin status.
	 *
	 * @since 4.8
	 *
	 * @param bool $checkedin
	 *
	 * @return array
	 */
	public function filter_by_checkedin( $checkedin ) {
		$meta_keys = $this->checked_in_keys();

		if ( tribe_is_truthy( $checkedin ) ) {
			return Tribe__Repository__Query_Filters::meta_in( $meta_keys, '1', 'is-checked-in' );
		}

		return Tribe__Repository__Query_Filters::meta_not_in_or_not_exists( $meta_keys, '1', 'is-not-checked-in' );
	}

	/**
	 * Bootstrap method called once per request to compile the available
	 * order statuses.
	 *
	 * @since 4.8
	 *
	 * @return bool|string
	 */
	protected function init_order_statuses() {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		if ( empty( self::$order_statuses ) ) {
			// For RSVP tickets the order status is the going status
			$statuses = [ 'yes', 'no' ];

			if ( tribe( 'tickets.commerce.paypal' )->is_active() ) {
				$statuses = array_merge( $statuses, $status_mgr->get_statuses_by_action( 'all', 'tpp' ) );
			}

			if (
				class_exists( 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' )
				&& function_exists( 'wc_get_order_statuses' )
			) {
				$statuses = array_merge( $statuses, $status_mgr->get_statuses_by_action( 'all', 'woo' ) );
			}

			if (
				class_exists( 'Tribe__Tickets_Plus__Commerce__EDD__Main' )
				&& function_exists( 'edd_get_payment_statuses' )
			) {
				$edd_statuses = $status_mgr->get_statuses_by_action( 'all', 'edd' );

				// Remove complete status.
				$edd_statuses = array_diff( [ 'Complete' ], $edd_statuses );

				$statuses = array_merge( $statuses, $edd_statuses );
			}

			// Enforce lowercase for comparison purposes.
			$statuses = array_map( 'strtolower', $statuses );

			// Prevent unnecessary duplicates.
			$statuses = array_unique( $statuses );

			self::$order_statuses         = $statuses;
			self::$private_order_statuses = array_diff( $statuses, self::$public_order_statuses );
		}
	}

	/**
	 * Get key from list of keys if it exists and fallback to empty array.
	 *
	 * @since 4.10.5
	 *
	 * @param string $key  Key name.
	 * @param array  $list List of keys.
	 *
	 * @return array List of matching keys.
	 */
	protected function limit_list( $key, $list ) {
		if ( ! array_key_exists( $key, $list ) ) {
			return [];
		}

		return [
			$key => $list[ $key ],
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return WP_Post|false The new post object or false if unsuccessful.
	 */
	public function create() {
		/*
		 * Only create if we are using a specific attendee context. The post type used is
		 * entirely dependent on the provider-specific implementation for attendees.
		 */
		if ( ! $this->key_name ) {
			return false;
		}

		/*
		 * Only create if we have a ticket set.
		 */
		if ( ! isset( $this->updates['ticket_id'] ) ) {
			return false;
		}

		return parent::create();
	}

	/**
	 * Create an attendee object from ticket and attendee data.
	 *
	 * @since 5.1.0
	 *
	 * @throws Tribe__Repository__Usage_Error If the argument types are not set as expected.
	 *
	 * @param array                             $attendee_data List of additional attendee data.
	 *
	 * @param Tribe__Tickets__Ticket_Object|int $ticket        The ticket object or ID.
	 *
	 * @return WP_Post|false The new post object or false if unsuccessful.
	 *
	 */
	public function create_attendee_for_ticket( $ticket, $attendee_data ) {
		// Attempt to get the ticket object from the ticket ID.
		if ( is_numeric( $ticket ) && $this->attendee_provider ) {
			$ticket = $this->attendee_provider->get_ticket( null, $ticket );
		}

		// Require the ticket be a ticket object.
		if ( ! $ticket instanceof Tribe__Tickets__Ticket_Object ) {
			throw new Tribe__Repository__Usage_Error( 'You must provide a valid ticket ID or object when creating an attendee from the Attendees Repository class' );
		}

		// Set the attendee arguments accordingly.
		$this->set_attendee_args( $attendee_data, $ticket );

		// Update the attendee data for referencing with what we handled in the set_attendee_args().
		$attendee_data = array_merge( $attendee_data, $this->updates );

		// Create the new attendee.
		$attendee = $this->create();

		if ( $attendee ) {
			// Handle any further attendee updates.
			$this->save_extra_attendee_data( $attendee, $attendee_data, $ticket );

			// Trigger creation actions.
			$this->trigger_create_actions( $attendee, $attendee_data, $ticket );
		}

		return $attendee;
	}

	/**
	 * Create an attendee object from ticket and attendee data.
	 *
	 * @since 5.1.0
	 *
	 * @throws Tribe__Repository__Usage_Error If the argument types are not set as expected.
	 *
	 * @param bool  $return_promise Whether to return a promise object or just the ids
	 *                              of the updated posts; if `true` then a promise will
	 *                              be returned whether the update is happening in background
	 *                              or not.
	 *
	 * @param array $attendee_data  List of attendee data to be saved.
	 *
	 * @return array|Tribe__Promise A list of the post IDs that have been (synchronous) or will
	 *                              be (asynchronous) updated if `$return_promise` is set to `false`;
	 *                              the Promise object if `$return_promise` is set to `true`.
	 *
	 */
	public function update_attendee( $attendee_data, $return_promise = false ) {
		if ( empty( $attendee_data['attendee_id'] ) ) {
			throw new Tribe__Repository__Usage_Error( 'You must provide the attendee_id when updating an attendee from the Attendees Repository class' );
		}

		$this->by( 'id', $attendee_data['attendee_id'] );

		/**
		 * Filter the attendee data before updating the attendee.
		 *
		 * @since 5.1.2
		 *
		 * @param array                               $attendee_data Attendee data that needs to be updated.
		 * @param Tribe__Tickets__Attendee_Repository $this          The Tickets Attendee ORM object.
		 */
		$attendee_data = apply_filters( 'tribe_tickets_attendee_repository_update_attendee_data_args_before_update', $attendee_data, $this );

		// Set the attendee arguments accordingly.
		$this->set_attendee_args( $attendee_data );

		// Update the attendee.
		$saved = $this->save( $return_promise );

		if ( $return_promise ) {
			$repository = $this;

			return $saved->then(
				static function () use ( $repository, $attendee_data ) {
					// Trigger the update actions.
					$repository->trigger_update_actions( $attendee_data );
				}
			);
		}

		// Trigger the update actions.
		$this->trigger_update_actions( $attendee_data );

		return $saved;
	}

	/**
	 * Set arguments for attendee.
	 *
	 * @since 5.1.0
	 *
	 * @throws Tribe__Repository__Usage_Error If the argument types are not set as expected.
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
	 *
	 * @param array                         $attendee_data List of additional attendee data.
	 */
	public function set_attendee_args( $attendee_data, $ticket = null ) {
		$args = [
			'attendee_id'       => null,
			'title'             => null,
			'full_name'         => null,
			'email'             => null,
			'ticket_id'         => $ticket ? $ticket->ID : null,
			'post_id'           => $ticket ? $ticket->get_event_id() : null,
			'order_id'          => null,
			'order_attendee_id' => null,
			'user_id'           => null,
			'attendee_status'   => null,
			'price_paid'        => null,
			'optout'            => null,
		];

		$args = array_merge( $args, $attendee_data );

		$attendee_id = null;

		$ignored = [
			'send_ticket_email',
			'send_ticket_email_args',
		];

		// Remove ignored arguments from being saved.
		foreach ( $ignored as $ignore ) {
			if ( isset( $args[ $ignore ] ) ) {
				unset( $args[ $ignore ] );
			}
		}

		// Unset the attendee ID if found.
		if ( isset( $args['attendee_id'] ) ) {
			$attendee_id = $args['attendee_id'];

			unset( $args['attendee_id'] );
		}

		// Do some extra set up if creating an attendee.
		if ( null === $attendee_id ) {
			// Default attendees to opted-out.
			if ( null === $args['optout'] ) {
				$args['optout'] = 1;
			}

			// Attempt to create order if none set.
			if ( empty( $args['order_id'] ) && $ticket ) {
				$order_id = $this->create_order_for_attendee( $args, $ticket );

				if ( $order_id ) {
					$args['order_id'] = $order_id;
				}
			}

			// If the title is empty, set the title from the full name.
			if ( empty( $args['title'] ) && $args['full_name'] ) {
				$args['title'] = $args['full_name'];

				// Maybe add the Order ID.
				if ( $args['order_id'] ) {
					$args['title'] = $args['order_id'] . ' | ' . $args['title'];
				}

				// Maybe add the Order Attendee ID.
				if ( null !== $args['order_attendee_id'] ) {
					$args['title'] .= ' | ' . $args['order_attendee_id'];
				}
			}

			// Maybe handle setting the User ID based on information we already have.
			if ( empty( $args['user_id'] ) && ! empty( $args['email'] ) && $this->attendee_provider ) {
				$user_id = $this->attendee_provider->maybe_setup_attendee_user_from_email( $args['email'], $args );

				if ( $user_id ) {
					$args['user_id'] = $user_id;
				}
			}

			if ( isset( $args['optout'] ) ) {
				// Enforce a 0/1 value for the optout value.
				$args['optout'] = (int) tribe_is_truthy( $args['optout'] );
			}
		}

		// Handle any customizations per provider for the attendee arguments.
		$args = $this->setup_attendee_args( $args, $attendee_data, $ticket );

		/**
		 * Allow filtering the arguments to set for the attendee.
		 *
		 * @since 5.1.0
		 *
		 * @param array                         $args          List of arguments to set for the attendee.
		 * @param array                         $attendee_data List of additional attendee data.
		 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
		 */
		$args = apply_filters( 'tribe_tickets_attendee_repository_set_attendee_args', $args, $attendee_data, $ticket );

		// Maybe run filter if using a provider key name.
		if ( $this->key_name ) {
			/**
			 * Allow filtering the arguments to set for the attendee by provider key name.
			 *
			 * @since 5.1.0
			 *
			 * @param array                         $args          List of arguments to set for the attendee.
			 * @param array                         $attendee_data List of additional attendee data.
			 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
			 */
			$args = apply_filters( 'tribe_tickets_attendee_repository_set_attendee_args_' . $this->key_name, $args, $attendee_data, $ticket );
		}

		// Remove arguments that are null.
		$args = array_filter(
			$args,
			static function ( $value ) {
				return ! is_null( $value );
			}
		);

		// Remove unused arguments from saving.
		if ( isset( $args['order_attendee_id'] ) ) {
			unset( $args['order_attendee_id'] );
		}

		$this->set_args( $args );
	}

	/**
	 * Set up the arguments to set for the attendee for this provider.
	 *
	 * @since 5.1.0
	 *
	 * @param array                              $args          List of arguments to set for the attendee.
	 * @param array                              $attendee_data List of additional attendee data.
	 * @param null|Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
	 *
	 * @return array List of arguments to set for the attendee.
	 */
	public function setup_attendee_args( $args, $attendee_data, $ticket = null ) {
		// Providers can override this.
		return $args;
	}

	/**
	 * Save extra attendee data after creation of attendee.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Post                       $attendee      The attendee object.
	 * @param array                         $attendee_data List of additional attendee data.
	 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object.
	 */
	public function save_extra_attendee_data( $attendee, $attendee_data, $ticket ) {
		$args = [];

		// Set up security code if it was not already customized.
		if ( empty( $attendee_data['security_code'] ) && $this->attendee_provider ) {
			$key = $attendee->ID;

			if ( ! empty( $attendee_data['order_id'] ) ) {
				$key = $attendee_data['order_id'] . '_' . $key;
			}

			$args['security_code'] = $this->attendee_provider->generate_security_code( $key );
		}

		/**
		 * Allow filtering the arguments to be used when saving extra attendee data.
		 *
		 * @since 5.1.0
		 *
		 * @param array                         $args          List of arguments to set for the attendee.
		 * @param WP_Post                       $attendee      The attendee object.
		 * @param array                         $attendee_data List of additional attendee data.
		 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object.
		 */
		$args = apply_filters( 'tribe_tickets_attendee_repository_save_extra_attendee_data_args', $args, $attendee, $attendee_data, $ticket );

		// Maybe run filter if using a provider key name.
		if ( $this->key_name ) {
			/**
			 * Allow filtering the arguments to be used when saving extra attendee data by provider key name.
			 *
			 * @since 5.1.0
			 *
			 * @param array                         $args          List of arguments to set for the attendee.
			 * @param WP_Post                       $attendee      The attendee object.
			 * @param array                         $attendee_data List of additional attendee data.
			 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object.
			 */
			$args = apply_filters( 'tribe_tickets_attendee_repository_save_extra_attendee_data_args_' . $this->key_name, $args, $attendee, $attendee_data, $ticket );
		}

		// If no args are set to be saved, bail.
		if ( empty( $args ) ) {
			return;
		}

		$query = tribe_attendees( $this->key_name );

		$query->by( 'id', $attendee->ID );

		try {
			$query->set_args( $args );
		} catch ( Tribe__Repository__Usage_Error $e ) {
			do_action( 'tribe_log', 'error', __CLASS__, [ 'message' => $e->getMessage() ] );

			return;
		}

		$query->save();
	}

	/**
	 * Trigger the creation actions needed based on the provider.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Post                       $attendee      The attendee object.
	 * @param array                         $attendee_data List of additional attendee data.
	 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object.
	 */
	public function trigger_create_actions( $attendee, $attendee_data, $ticket ) {
		/**
		 * Allow hooking into after the attendee has been created.
		 *
		 * @since 5.1.0
		 *
		 * @param WP_Post                             $attendee      The attendee object.
		 * @param array                               $attendee_data List of additional attendee data.
		 * @param Tribe__Tickets__Ticket_Object       $ticket        The ticket object.
		 * @param Tribe__Tickets__Attendee_Repository $repository    The current repository object.
		 */
		do_action( 'tribe_tickets_attendee_repository_create_attendee_for_ticket_after_create', $attendee, $attendee_data, $ticket, $this );

		// Maybe run filter if using a provider key name.
		if ( $this->key_name ) {
			/**
			 * Allow hooking into after the attendee has been created by provider key name.
			 *
			 * @since 5.1.0
			 *
			 * @param WP_Post                             $attendee      The attendee object.
			 * @param array                               $attendee_data List of additional attendee data.
			 * @param Tribe__Tickets__Ticket_Object       $ticket        The ticket object.
			 * @param Tribe__Tickets__Attendee_Repository $repository    The current repository object.
			 */
			do_action( 'tribe_tickets_attendee_repository_create_attendee_for_ticket_after_create_' . $this->key_name, $attendee, $attendee_data, $ticket, $this );
		}

		// Maybe send the attendee email.
		$this->maybe_send_attendee_email( $attendee->ID, $attendee_data );

		// Handle clearing the caches.
		if ( $this->attendee_provider ) {
			// Clear the attendee cache if post_id is provided.
			if ( ! empty( $this->updates['post_id'] ) ) {
				$this->attendee_provider->clear_attendees_cache( $this->updates['post_id'] );
			}

			// Clear the ticket cache if ticket is provided.
			if ( $ticket ) {
				$this->attendee_provider->clear_ticket_cache( $ticket->ID );
			}
		}
	}

	/**
	 * Trigger the update actions needed based on the provider.
	 *
	 * @since 5.1.0
	 *
	 * @param array $attendee_data List of attendee data to be saved.
	 */
	public function trigger_update_actions( $attendee_data ) {
		/**
		 * Allow hooking into after the attendee has been updated.
		 *
		 * @since 5.1.0
		 *
		 * @param array                               $attendee_data List of attendee data to be saved.
		 * @param Tribe__Tickets__Attendee_Repository $repository    The current repository object.
		 */
		do_action( 'tribe_tickets_attendee_repository_update_attendee_after_update', $attendee_data, $this );

		// Maybe run filter if using a provider key name.
		if ( $this->key_name ) {
			/**
			 * Allow hooking into after the attendee has been updated by provider key name.
			 *
			 * @since 5.1.0
			 *
			 * @param array                               $attendee_data List of attendee data to be saved.
			 * @param Tribe__Tickets__Attendee_Repository $repository    The current repository object.
			 */
			do_action( "tribe_tickets_attendee_repository_update_attendee_after_update_{$this->key_name}", $attendee_data, $this );
		}

		// Maybe send the attendee email.
		$this->maybe_send_attendee_email( $attendee_data['attendee_id'], $attendee_data );

		// Clear the attendee cache if post_id is provided.
		if ( ! empty( $this->updates['post_id'] ) && $this->attendee_provider ) {
			$this->attendee_provider->clear_attendees_cache( $this->updates['post_id'] );
		}
	}

	/**
	 * Create an order for an attendee.
	 *
	 * @since 5.1.0
	 *
	 * @param array                                  $attendee_data List of attendee data to reference.
	 * @param null|int|Tribe__Tickets__Ticket_Object $ticket        The ticket object, ticket ID, or null if not relying on it.
	 *
	 * @return int|string|false The order ID or false if not created.
	 */
	public function create_order_for_attendee( $attendee_data, $ticket = null ) {
		// Bail if we already have an attendee or order.
		if ( ! empty( $attendee_data['attendee_id'] ) || ! empty( $attendee_data['order_id'] ) ) {
			return false;
		}

		// Attempt to generate a new order.
		$orders = tribe_tickets_orders( $this->key_name );

		// Bail if provider-specific order repository not found.
		if ( empty( $orders->key_name ) ) {
			return false;
		}

		$tickets = Arr::get( $attendee_data, 'tickets', [] );

		if ( empty( $tickets ) ) {
			$ticket_id = $ticket;

			if ( is_object( $ticket ) ) {
				// Detect ticket ID from the object.
				$ticket_id = $ticket->ID;
			} elseif ( empty( $ticket ) && isset( $attendee_data['ticket_id'] ) ) {
				// Detect the ticket ID from the attendee data.
				$ticket_id = $attendee_data['ticket_id'];
			}

			// Bail if no valid ticket ID.
			if ( $ticket_id < 1 ) {
				return false;
			}

			$tickets = [
				[
					'id'       => $ticket_id,
					'quantity' => 1,
				],
			];
		}

		$order_args = [
			'full_name'    => Arr::get( $attendee_data, 'full_name' ),
			'email'        => Arr::get( $attendee_data, 'email' ),
			'user_id'      => Arr::get( $attendee_data, 'user_id' ),
			'order_status' => Arr::get( $attendee_data, 'attendee_status' ),
			'tickets'      => $tickets,
		];

		/**
		 * Allow filtering the order data being used to create an order for the attendee.
		 *
		 * @since 5.1.0
		 *
		 * @param array                         $order_args    List of order data to be saved.
		 * @param array                         $attendee_data List of additional attendee data.
		 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
		 */
		$order_args = apply_filters( 'tribe_tickets_attendee_repository_create_order_for_attendee_order_args', $order_args, $attendee_data, $ticket );

		// Check if order creation is disabled.
		if ( empty( $order_args ) ) {
			return false;
		}

		try {
			$order = $orders->create_order_for_ticket( $order_args );
		} catch ( Tribe__Repository__Usage_Error $exception ) {
			return false;
		}

		return $order;
	}

	/**
	 * Maybe send the attendee email for an attendee.
	 *
	 * @since 5.1.0
	 *
	 * @param int   $attendee_id   The attendee ID.
	 * @param array $attendee_data List of attendee data that was used for saving.
	 */
	protected function maybe_send_attendee_email( $attendee_id, $attendee_data ) {
		$send_ticket_email      = (bool) Arr::get( $attendee_data, 'send_ticket_email', false );
		$send_ticket_email_args = (array) Arr::get( $attendee_data, 'send_ticket_email_args', [] );

		// Check if we need to send the ticket email.
		if ( ! $send_ticket_email ) {
			return;
		}

		// Check if we have an attendee provider object set.
		if ( ! $this->attendee_provider ) {
			return;
		}

		$attendee_tickets = [
			$attendee_id,
		];

		$this->attendee_provider->send_tickets_email_for_attendees( $attendee_tickets, $send_ticket_email_args );
	}
}
