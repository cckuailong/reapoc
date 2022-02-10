<?php

/**
 * Class Tribe__Tickets__Ticket_Repository
 *
 * The basic ticket repository.
 *
 * @since 4.8
 */
class Tribe__Tickets__Ticket_Repository extends Tribe__Repository {

	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @var string
	 */
	protected $filter_name = 'tickets';

	/**
	 * Tribe__Tickets__Ticket_Repository constructor.
	 *
	 * @since 4.8
	 */
	public function __construct() {
		parent::__construct();

		$this->create_args['post_type'] = current( $this->ticket_types() );

		$this->default_args = [
			'post_type' => $this->ticket_types(),
			'orderby'   => [ 'date', 'ID' ],
		];

		$this->schema = array_merge( $this->schema, [
			'event'             => [ $this, 'filter_by_event' ],
			'event_not_in'      => [ $this, 'filter_by_event_not_in' ],
			'is_available'      => [ $this, 'filter_by_availability' ],
			'provider'          => [ $this, 'filter_by_provider' ],
			'attendees_min'     => [ $this, 'filter_by_attendees_min' ],
			'attendees_max'     => [ $this, 'filter_by_attendees_max' ],
			'attendees_between' => [ $this, 'filter_by_attendees_between' ],
			'checkedin_min'     => [ $this, 'filter_by_checkedin_min' ],
			'checkedin_max'     => [ $this, 'filter_by_checkedin_max' ],
			'checkedin_between' => [ $this, 'filter_by_checkedin_between' ],
			'capacity_min'      => [ $this, 'filter_by_capacity_min' ],
			'capacity_max'      => [ $this, 'filter_by_capacity_max' ],
			'capacity_between'  => [ $this, 'filter_by_capacity_between' ],
			'available_from'    => [ $this, 'filter_by_available_from' ],
			'available_until'   => [ $this, 'filter_by_available_until' ],
			'event_status'      => [ $this, 'filter_by_event_status' ],
			'has_attendee_meta' => [ $this, 'filter_by_attendee_meta_existence' ],
			'currency_code'     => [ $this, 'filter_by_currency_code' ],
			'is_active'         => [ $this, 'filter_by_active' ],
		] );
	}

	/**
	 * Returns an array of the ticket types handled by this repository.
	 *
	 * Extending repository classes should override this to add more ticket types.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function ticket_types() {
		return [
			'rsvp'                         => 'tribe_rsvp_tickets',
			'tribe-commerce'               => 'tribe_tpp_tickets',
			TEC\Tickets\Commerce::PROVIDER => TEC\Tickets\Commerce\Ticket::POSTTYPE,
		];
	}

	/**
	 * Filters tickets by a specific event.
	 *
	 * @since 4.8
	 *
	 * @param int|array $event_id
	 */
	public function filter_by_event( $event_id ) {
		$this->by( 'meta_in', $this->ticket_to_event_keys(), $event_id );
	}

	/**
	 * Returns the list of meta keys relating a Ticket to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.8
	 *
	 * @return array
	 */
	public function ticket_to_event_keys() {
		return [
			'rsvp'           => '_tribe_rsvp_for_event',
			'tribe-commerce' => '_tribe_tpp_for_event',
		];
	}

	/**
	 * Filters tickets by not being related to a specific event.
	 *
	 * @since 4.8
	 *
	 * @param int|array $event_id
	 */
	public function filter_by_event_not_in( $event_id ) {
		$this->by( 'meta_not_in', $this->ticket_to_event_keys(), $event_id );
	}

	/**
	 * Sets up the query to filter tickets by availability.
	 *
	 * @since 4.8
	 *
	 * @param bool $is_available
	 */
	public function filter_by_availability( $is_available ) {
		$want_available = (bool) $is_available;

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler   = tribe( 'tickets.handler' );
		$capacity_meta_key = $tickets_handler->key_capacity;

		if ( $want_available ) {
			$this->where( 'meta_gt', $capacity_meta_key, 0 );
		} else {
			$this->where( 'meta_equals', $capacity_meta_key, 0 );
		}
	}

	/**
	 * Sets up the query to filter tickets by provider.
	 *
	 * @since 4.8
	 *
	 * @param string|array $provider
	 */
	public function filter_by_provider( $provider ) {
		$providers = Tribe__Utils__Array::list_to_array( $provider );
		$meta_keys = Tribe__Utils__Array::map_or_discard( (array) $providers, $this->ticket_to_event_keys() );

		$this->by( 'meta_exists', $meta_keys );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a minimum
	 * number of attendees.
	 *
	 * @since 4.8
	 *
	 * @param int $attendees_min
	 */
	public function filter_by_attendees_min( $attendees_min ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_min( $attendees->attendee_to_ticket_keys(), $attendees_min );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a maximum
	 * number of attendees.
	 *
	 * @since 4.8
	 *
	 * @param int $attendees_max
	 */
	public function filter_by_attendees_max( $attendees_max ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_max( $attendees->attendee_to_ticket_keys(), $attendees_max );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a number
	 * of attendees between two values.
	 *
	 * @since 4.8
	 *
	 * @param int $attendees_min
	 * @param int $attendees_max
	 */
	public function filter_by_attendees_between( $attendees_min, $attendees_max ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_between( $attendees->attendee_to_ticket_keys(), $attendees_min, $attendees_max );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a minimum
	 * number of checked-in attendees.
	 *
	 * @since 4.8
	 *
	 * @param int $checkedin_min
	 */
	public function filter_by_checkedin_min( $checkedin_min ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_min( $attendees->attendee_to_ticket_keys(), $checkedin_min, $attendees->checked_in_keys(), '1' );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a maximum
	 * number of checked-in attendees.
	 *
	 * @since 4.8
	 *
	 * @param int $checkedin_max
	 */
	public function filter_by_checkedin_max( $checkedin_max ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_max( $attendees->attendee_to_ticket_keys(), $checkedin_max, $attendees->checked_in_keys(), '1' );
	}

	/**
	 * Adds a WHERE clause to the query to filter tickets that have a number
	 * of checked-in attendees between two values.
	 *
	 * @since 4.8
	 *
	 * @param int $checkedin_min
	 * @param int $checkedin_max
	 */
	public function filter_by_checkedin_between( $checkedin_min, $checkedin_max ) {
		/** @var Tribe__Tickets__Attendee_Repository $attendees */
		$attendees = tribe_attendees();

		$this->by_related_to_between( $attendees->attendee_to_ticket_keys(), $checkedin_min, $checkedin_max, $attendees->checked_in_keys(), '1' );
	}

	/**
	 * Filters tickets by a minimum capacity.
	 *
	 * @since 4.8
	 *
	 * @param int $capacity_min
	 */
	public function filter_by_capacity_min( $capacity_min ) {
		/**
		 * Tickets with unlimited capacity will have a `_capacity` meta of `-1`
		 * but they will always satisfy any minimum capacity requirement
		 * so we need to use a custom query.
		 */

		/** @var wpdb $wpdb */
		global $wpdb;

		$min = $this->prepare_value( $capacity_min, '%d' );
		$this->join_clause( "JOIN {$wpdb->postmeta} capacity_min ON {$wpdb->posts}.ID = capacity_min.post_id" );

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		$capacity_meta_key = $this->prepare_value( $tickets_handler->key_capacity, '%s' );
		$this->where_clause( "capacity_min.meta_key = {$capacity_meta_key} AND (capacity_min.meta_value >= {$min} OR capacity_min.meta_value < 0)" );
	}

	/**
	 * Filters tickets by a maximum capacity.
	 *
	 * @since 4.8
	 *
	 * @param int $capacity_max
	 */
	public function filter_by_capacity_max( $capacity_max ) {
		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		/**
		 * Tickets with unlimited capacity will have a `_capacity` meta of `-1`
		 * but they should not satisfy any maximum capacity requirement
		 * so we need to use a BETWEEN query.
		 */
		$this->by( 'meta_between', $tickets_handler->key_capacity, [ 0, $capacity_max ], 'NUMERIC' );
	}

	/**
	 * Filters tickets by a minimum and maximum capacity.
	 *
	 * @since 4.8
	 *
	 * @param int $capacity_min
	 * @param int $capacity_max
	 */
	public function filter_by_capacity_between( $capacity_min, $capacity_max ) {
		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		$this->by( 'meta_between', $tickets_handler->key_capacity, [
			(int) $capacity_min,
			(int) $capacity_max,
		], 'NUMERIC' );
	}

	/**
	 * Filters tickets by their available date being starting on a date.
	 *
	 * @since 4.8
	 *
	 * @param string|int $date
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function filter_by_available_from( $date ) {
		// the input is a UTC date or timestamp
		$utc_date_string = is_numeric( $date ) ? "@{$date}" : $date;
		$utc_date        = new DateTime( $utc_date_string, new DateTimeZone( 'UTC' ) );
		$from            = Tribe__Timezones::to_tz( $utc_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT ), Tribe__Timezones::wp_timezone_string() );

		return [
			'meta_query' => [
				'available-from' => [
					'not-exists' => [
						'key'     => '_ticket_start_date',
						'compare' => 'NOT EXISTS',
					],
					'relation'   => 'OR',
					'from'       => [
						'key'     => '_ticket_start_date',
						'compare' => '>=',
						'value'   => $from,
					],
				],
			],
		];
	}

	/**
	 * Filters tickets by their available date being until a date.
	 *
	 * @since 4.8
	 *
	 * @param string|int $date
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function filter_by_available_until( $date ) {
		// the input is a UTC date or timestamp
		$utc_date_string = is_numeric( $date ) ? "@{$date}" : $date;
		$utc_date        = new DateTime( $utc_date_string, new DateTimeZone( 'UTC' ) );
		$until           = Tribe__Timezones::to_tz( $utc_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT ), Tribe__Timezones::wp_timezone_string() );

		return [
			'meta_query' => [
				'available-until' => [
					'not-exists' => [
						'key'     => '_ticket_end_date',
						'compare' => 'NOT EXISTS',
					],
					'relation'   => 'OR',
					'from'       => [
						'key'     => '_ticket_end_date',
						'compare' => '<=',
						'value'   => $until,
					],
				],
			],
		];
	}

	/**
	 * Filters tickets by if they are currently available or available in the future.
	 *
	 * @since 5.2.0
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function filter_by_active() {
		// the input is a UTC date or timestamp
		$utc_date = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
		$now      = Tribe__Timezones::to_tz( $utc_date->format( Tribe__Date_Utils::DBDATETIMEFORMAT ), Tribe__Timezones::wp_timezone_string() );

		return [
			'meta_query' => [
				'available-until' => [
					'not-exists' => [
						'key'     => '_ticket_end_date',
						'compare' => 'NOT EXISTS',
					],
					'relation'   => 'OR',
					'from'       => [
						'key'     => '_ticket_end_date',
						'compare' => '>',
						'value'   => $now,
					],
				],
			],
		];
	}

	/**
	 * Filters tickets to only get those related to posts with a specific status.
	 *
	 * @since 4.8
	 *
	 * @param string|array $event_status
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the requested statuses are not accessible by the user.
	 * @throws Tribe__Repository__Usage_Error
	 */
	public function filter_by_event_status( $event_status ) {
		$statuses = Tribe__Utils__Array::list_to_array( $event_status );

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
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results( 'The user cannot read posts with the requested post statuses.' );
		}

		$this->where_meta_related_by( $this->ticket_to_event_keys(), 'IN', 'post_status', $statuses );
	}

	/**
	 * Filters tickets depending on them having additional
	 * information available and active or not.
	 *
	 * @since 4.8
	 *
	 * @param bool $exists
	 *
	 * @return array
	 */
	public function filter_by_attendee_meta_existence( $exists ) {
		if ( ! class_exists( 'Tribe__Tickets_Plus__Meta' ) ) {
			return [];
		}

		if ( $exists ) {
			return [
				'meta_query' => [
					'by-attendee-meta-availability' => [
						'is-enabled' => [
							'key'     => Tribe__Tickets_Plus__Meta::ENABLE_META_KEY,
							'compare' => '=',
							'value'   => 'yes',
						],
						'relation'   => 'AND',
						'has-meta'   => [
							'key'     => Tribe__Tickets_Plus__Meta::META_KEY,
							'compare' => 'EXISTS',
						],
					],
				],
			];
		}

		return [
			'meta_query' => [
				'by-attendee-meta-availability' => [
					'is-not-enabled' => [
						'key'     => Tribe__Tickets_Plus__Meta::ENABLE_META_KEY,
						'compare' => '!=',
						'value'   => 'yes',
					],
					'relation'       => 'OR',
					'not-exists'     => [
						'key'     => Tribe__Tickets_Plus__Meta::ENABLE_META_KEY,
						'compare' => 'NOT EXISTS',
					],
				],
			],
		];
	}

	/**
	 * Filters tickets by their provider currency codes.
	 *
	 * Applying this filter automatically excludes RSVP tickets that, being free, have
	 * no currency and hence no code.
	 *
	 * @since 4.8
	 *
	 * @param string|array $currency_code A 3-letter currency code, an array of CSV list of
	 *                                    3-letter currency codes.
	 *
	 * @throws Tribe__Repository__Void_Query_Exception If the queried currency code would make it
	 *                                                 so that no ticket would match the query.
	 */
	public function filter_by_currency_code( $currency_code ) {
		$queried_codes = Tribe__Utils__Array::list_to_array( $currency_code );

		if ( empty( $queried_codes ) ) {
			return;
		}

		$queried_codes = array_map( 'strtoupper', $queried_codes );

		$keys             = $this->ticket_to_event_keys();
		$provider_symbols = $this->ticket_provider_symbols();

		$in_keys = [];

		foreach ( $provider_symbols as $provider_slug => $provider_code ) {
			$intersected = array_intersect( (array) $provider_code, $queried_codes );

			if ( count( $intersected ) === 0 ) {
				continue;
			}

			$in_keys[] = $keys[ $provider_slug ];
		}

		if ( empty( $in_keys ) ) {
			$reason = 'No active provider has one of the queried currency symbols';
			throw Tribe__Repository__Void_Query_Exception::because_the_query_would_yield_no_results( $reason );
		}

		$this->by( 'meta_exists', $in_keys );
	}

	/**
	 * Get list of provider symbols.
	 *
	 * @since 4.10.6
	 *
	 * @return array List of provider symbols.
	 */
	public function ticket_provider_symbols() {
		$provider_symbols = [];

		if ( tribe( 'tickets.commerce.paypal' )->is_active() ) {
			/** @var Tribe__Tickets__Commerce__Currency $currency */
			$currency = tribe( 'tickets.commerce.paypal.currency' );

			$provider_symbols['tribe-commerce'] = $currency->get_currency_code();
		}

		return $provider_symbols;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create() {
		// Prevent creation of Tickets through the default ORM.
		if ( 1 !== count( $this->ticket_types() ) ) {
			return false;
		}

		return parent::create();
	}
}