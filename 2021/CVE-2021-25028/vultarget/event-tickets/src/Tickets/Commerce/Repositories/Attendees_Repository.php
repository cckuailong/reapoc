<?php

namespace TEC\Tickets\Commerce\Repositories;


use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Module;
use \Tribe__Repository;
use TEC\Tickets\Commerce\Attendee;
use Tribe__Repository__Usage_Error as Usage_Error;

use Tribe__Utils__Array as Arr;
use Tribe__Date_Utils as Dates;

/**
 * Class Attendees Repository.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Repositories
 */
class Attendees_Repository extends Tribe__Repository {
	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $filter_name = 'tc_attendees';

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

		// Set the order post type.
		$this->default_args['post_type']   = Attendee::POSTTYPE;
		$this->default_args['post_status'] = 'publish';
		$this->create_args['post_status']  = 'publish';
		$this->create_args['post_type']    = Attendee::POSTTYPE;

		$aliases = [
			'order_id'          => 'post_parent',
			'ticket_id'         => Attendee::$ticket_relation_meta_key,
			'event_id'          => Attendee::$event_relation_meta_key,
			'user_id'           => Attendee::$user_relation_meta_key,
			'security_code'     => Attendee::$security_code_meta_key,
			'opt_out'           => Attendee::$optout_meta_key,
			'checked_in'        => Attendee::$checked_in_meta_key,
			'price_paid'        => Attendee::$price_paid_meta_key,
			'currency'          => Attendee::$currency_meta_key,
			'full_name'         => Attendee::$full_name_meta_key,
			'email'             => Attendee::$email_meta_key,
			'is_deleted_ticket' => Attendee::$deleted_ticket_meta_key,
			'ticket_sent'       => Attendee::$ticket_sent_meta_key,
			'is_subscribed'     => Attendee::$subscribed_meta_key,
		];

		/**
		 * Allows for filtering the repository aliases.
		 *
		 * @since 5.2.0
		 *
		 * @param array $aliases Repository aliases.
		 */
		$aliases = apply_filters( 'tec_tickets_commerce_attendees_repository_aliases', $aliases );

		// Add event specific aliases.
		$this->update_fields_aliases = array_merge(
			$this->update_fields_aliases,
			$aliases
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

		$this->add_simple_meta_schema_entry( 'ticket_id', Attendee::$ticket_relation_meta_key, 'meta_equals' );
		$this->add_simple_meta_schema_entry( 'event_id', Attendee::$event_relation_meta_key, 'meta_equals' );
	}

	/**
	 * {@inheritDoc}
	 */
	protected function format_item( $id ) {
		$formatted = null === $this->formatter
			? tec_tc_get_attendee( $id )
			: $this->formatter->format_item( $id );

		/**
		 * Filters a single formatted attendee result.
		 *
		 * @since 5.1.9
		 *
		 * @param mixed|\WP_Post                $formatted The formatted event result, usually a post object.
		 * @param int                           $id        The formatted post ID.
		 * @param \Tribe__Repository__Interface $this      The current repository object.
		 */
		$formatted = apply_filters( 'tec_tickets_commerce_repository_attendee_format', $formatted, $id, $this );

		return $formatted;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_postarr_for_create( array $postarr ) {
		if ( isset( $postarr['meta_input'] ) ) {
			$postarr = $this->filter_meta_input( $postarr );
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

		return parent::filter_postarr_for_update( $postarr, $post_id );
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
//		if ( ! empty( $postarr['meta_input']['purchaser'] ) ) {
//			$postarr = $this->filter_purchaser_input( $postarr, $post_id );
//		}

		return $postarr;
	}

	/**
	 * Cleans up a list of Post IDs into an usable array for DB query.
	 *
	 * @since 5.2.1
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
	 * @since 5.2.1
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

		$this->by( 'meta_in', Attendee::$ticket_relation_meta_key, $tickets );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given ticket/s.
	 *
	 * @since 5.2.1
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

		$this->by( 'meta_not_in', Attendee::$ticket_relation_meta_key, $tickets );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given ticket/s.
	 *
	 * @since 5.2.1
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

		$this->by( 'meta_in', Attendee::$event_relation_meta_key, $events );

		return null;
	}

	/**
	 * Filters order by whether or not it contains a given event/s.
	 *
	 * @since 5.2.1
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

		$this->by( 'meta_not_in', Attendee::$event_relation_meta_key, $events );

		return null;
	}
}