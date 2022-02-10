<?php
/**
 * Handles all of the post querying.
 *
 * @since   4.12.1
 *
 * @package Tribe\Tickets\Repositories
 */

namespace Tribe\Tickets\Repositories;

use Tribe\Tickets\Repositories\Traits\Event;
use Tribe\Tickets\Repositories\Traits\Post_Attendees;
use Tribe\Tickets\Repositories\Traits\Post_Tickets;
use Tribe__Repository;
use Tribe__Timezones as Timezones;
use Tribe__Utils__Array as Arr;

/**
 * Class Post_Repository.
 *
 * @since   4.12.1
 *
 * @package Tribe\Tickets\Repositories
 */
class Post_Repository extends Tribe__Repository {

	use Post_Attendees;
	use Post_Tickets;
	use Event;

	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @var string
	 */
	protected $filter_name = 'tickets_post';

	/**
	 * Post_Repository constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->default_args = array_merge( $this->default_args, [
			'orderby'                      => 'date',
			'post_status'                  => 'any',
			'post_type'                    => 'any',
			// We'll be handling the dates, let's mark the query as a non-filtered one.
			'tribe_suppress_query_filters' => true,
		] );

		$this->schema = array_merge( $this->schema, [
			// These filter methods are added by the Event trait.
			'ends_after'           => [ $this, 'filter_by_ends_after' ],

			// These filter methods are added by the Post_Tickets trait.
			'cost'                 => [ $this, 'filter_by_cost' ],
			'cost_currency_symbol' => [ $this, 'filter_by_cost_currency_symbol' ],
			'has_tickets'          => [ $this, 'filter_by_has_tickets' ],
			'has_rsvp'             => [ $this, 'filter_by_has_rsvp' ],

			// These filter methods are added by the Post_Attendees trait.
			'has_attendees'        => [ $this, 'filter_by_has_attendees' ],
			'attendee'             => [ $this, 'filter_by_attendee' ],
			'attendee__not_in'     => [ $this, 'filter_by_attendee_not_in' ],
			'attendee_user'        => [ $this, 'filter_by_attendee_user' ],

			// This is not yet working, it needs more debugging to determine why it's not functional yet.
			//'attendee_user__not_in' => [ $this, 'filter_by_attendee_user_not_in' ],
		] );
	}

	/**
	 * Returns an array of the attendee types handled by this repository.
	 *
	 * Extending repository classes should override this to add more attendee types.
	 *
	 * @since 4.12.1
	 *
	 * @return array
	 */
	public function attendee_types() {
		return [
			'rsvp'           => 'tribe_rsvp_attendees',
			'tribe-commerce' => 'tribe_tpp_attendees',
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.12.1
	 *
	 * @return array
	 */
	public function attendee_to_event_keys() {
		return [
			'rsvp'           => '_tribe_rsvp_event',
			'tribe-commerce' => '_tribe_tpp_event',
		];
	}

	/**
	 * Returns the meta key relating an Attendee to a User.
	 *
	 * @since 4.12.1
	 *
	 * @return string
	 */
	public function attendee_to_user_key() {
		return '_tribe_tickets_attendee_user_id';
	}

	/**
	 * Handles the `order_by` clauses for events
	 *
	 * @since 4.12.1
	 *
	 * @param string $order_by The key used to order events; e.g. `event_date` to order events by start date.
	 */
	public function handle_order_by( $order_by ) {
		$check_orderby = $order_by;

		if ( ! is_array( $check_orderby ) ) {
			$check_orderby = explode( ' ', $check_orderby );
		}

		$timestamp_key = 'TIMESTAMP(mt1.meta_value)';

		$after = false;
		$loop  = 0;

		foreach ( $check_orderby as $key => $value ) {
			$loop ++;
			$order_by      = is_numeric( $key ) ? $value : $key;
			$order         = is_numeric( $key ) ? 'ASC' : $value;
			$default_order = Arr::get_in_any( [ $this->query_args, $this->default_args ], 'order', 'ASC' );

			switch ( $order_by ) {
				case 'event_date':
					$this->order_by_date( false, $after );
					break;
				case 'event_date_utc':
					$this->order_by_date( true, $after );
					break;
				default:
					$after = $after || 1 === $loop;
					if ( empty( $this->query_args['orderby'] ) ) {
						$this->query_args['orderby'] = [ $order_by => $order ];
					} else {
						$add = [ $order_by => $order ];
						// Make sure all `orderby` clauses have the shape `<orderby> => <order>`.
						$normalized = [];
						foreach ( $this->query_args['orderby'] as $k => $v ) {
							$the_order_by                = is_numeric( $k ) ? $v : $k;
							$the_order                   = is_numeric( $k ) ? $default_order : $v;
							$normalized[ $the_order_by ] = $the_order;
						}
						$this->query_args['orderby'] = $normalized;
						$this->query_args['orderby'] = array_merge( $this->query_args['orderby'], $add );
					}
					break;
			}
		}
	}

	/**
	 * Overrides the base method to correctly handle the `order_by` clauses before.
	 *
	 * The Event repository handles ordering with some non trivial logic and some query filtering.
	 * To avoid the "stacking" of `orderby` clauses and filters the query filters are added at the very last moment,
	 * right before building the query.
	 *
	 * @since 4.12.1
	 *
	 * @return \WP_Query The built query object.
	 */
	protected function build_query_internally() {
		$order_by = Arr::get_in_any( [ $this->query_args, $this->default_args ], 'orderby', 'event_date' );

		unset( $this->query_args['orderby'], $this->default_args['order_by'] );

		$this->handle_order_by( $order_by );

		return parent::build_query_internally();
	}

	/**
	 * Applies start-date-based ordering to the query.
	 *
	 * @since 4.12.1
	 *
	 * @param bool $use_utc      Whether to use the events UTC start dates or their localized dates.
	 * @param bool $after        Whether to append the order by clause to the ones managed by WordPress or not.
	 *                           Defaults to `false`,to prepend them to the ones managed by WordPress.
	 */
	protected function order_by_date( $use_utc, $after = false ) {
		global $wpdb;

		$meta_alias = 'event_date';
		$meta_key   = '_EventStartDate';

		/**
		 * When the "Use site timezone everywhere" option is checked in events settings,
		 * the UTC time for event start and end times will be used. This filter allows the
		 * disabling of that in certain contexts, so that local (not UTC) event times are used.
		 *
		 * @see Tribe__Events__Repositories__Events::order_by_date
		 *
		 * @since 4.12.1
		 *
		 * @param boolean $force_local_tz Whether to force the local TZ.
		 */
		$force_local_tz = apply_filters( 'tribe_events_query_force_local_tz', false );

		/*
		 * The setting is not being forced by means of a call to the `use_utc` method.
		 * First we check if we've got a UTC ordering request in the `orderby` clause.
		 * After that if the use of the local (to the event) timezone is being forced by a filter.
		 * Finally if the timezone setting is set to use the site-wide timezone or not.
		 */
		if ( $use_utc || ( ! $force_local_tz && Timezones::is_mode( 'site' ) ) ) {
			$meta_alias = 'event_date_utc';
			$meta_key   = '_EventStartDateUTC';
		}

		$postmeta_table = "orderby_{$meta_alias}_meta";

		$filter_id = 'order_by_date';

		$this->filter_query->join( $wpdb->prepare( "
			LEFT JOIN `{$wpdb->postmeta}` AS `{$postmeta_table}`
				ON (
					`{$postmeta_table}`.post_id = `{$wpdb->posts}`.ID
					AND `{$postmeta_table}`.meta_key = %s
				)
			", $meta_key ), $filter_id, true );

		$order = Arr::get_in_any( [ $this->query_args, $this->default_args ], 'order', 'ASC' );
		$this->filter_query->orderby( [ $meta_alias => $order ], $filter_id, true, $after );
		$this->filter_query->fields( "MIN( {$postmeta_table}.meta_value ) AS {$meta_alias}", $filter_id, true );
	}
}
