<?php


/**
 * Class Tribe__Tickets__CSV_Importer__RSVP_Importer
 */
class Tribe__Tickets__CSV_Importer__RSVP_Importer extends Tribe__Events__Importer__File_Importer {

	/**
	 * @var array
	 */
	protected $required_fields = array( 'event_name', 'ticket_name' );

	/**
	 * @var array
	 */
	protected static $event_name_cache = array();

	/**
	 * @var array
	 */
	protected static $ticket_name_cache = array();

	/**
	 * @var Tribe__Tickets__RSVP
	 */
	protected $rsvp_tickets;

	/**
	 * @var bool|string
	 */
	protected $row_message = false;

	/**
	 * The class constructor proxy method.
	 *
	 * @param Tribe__Events__Importer__File_Importer|null $instance The default instance that would be used for the type.
	 * @param Tribe__Events__Importer__File_Reader        $file_reader
	 *
	 * @return Tribe__Tickets__CSV_Importer__RSVP_Importer
	 */
	public static function instance( $instance, Tribe__Events__Importer__File_Reader $file_reader ) {
		return new self( $file_reader );
	}

	/**
	 * Resets that class static caches
	 */
	public static function reset_cache() {
		self::$event_name_cache  = array();
		self::$ticket_name_cache = array();
	}

	/**
	 * Tribe__Tickets__CSV_Importer__RSVP_Importer constructor.
	 *
	 * @param Tribe__Events__Importer__File_Reader                  $file_reader
	 * @param Tribe__Events__Importer__Featured_Image_Uploader|null $featured_image_uploader
	 * @param Tribe__Tickets__RSVP|null                             $rsvp_tickets
	 */
	public function __construct(
		Tribe__Events__Importer__File_Reader $file_reader,
		Tribe__Events__Importer__Featured_Image_Uploader $featured_image_uploader = null,
		Tribe__Tickets__RSVP $rsvp_tickets = null
	) {
		parent::__construct( $file_reader, $featured_image_uploader );
		$this->rsvp_tickets = ! empty( $rsvp_tickets ) ? $rsvp_tickets : Tribe__Tickets__RSVP::get_instance();

		add_action( 'tribe_aggregator_record_activity_wakeup', array( $this, 'register_rsvp_activity' ) );
	}

	/**
	 * @param array $record
	 *
	 * @return bool
	 */
	public function match_existing_post( array $record ) {
		$event = $this->get_event_from( $record );

		if ( empty( $event ) ) {
			return false;
		}

		$ticket_name = $this->get_value_by_key( $record, 'ticket_name' );
		$cache_key   = $ticket_name . '-' . $event->ID;

		if ( isset( self::$ticket_name_cache[ $cache_key ] ) ) {
			return self::$ticket_name_cache[ $cache_key ];
		}

		$ticket_post = get_page_by_title( $ticket_name, OBJECT, $this->rsvp_tickets->ticket_object );
		if ( empty( $ticket_post ) ) {
			return false;
		}

		$ticket = $this->rsvp_tickets->get_ticket( $event->ID, $ticket_post->ID );

		$match = $ticket->get_event() == $event ? true : false;

		self::$ticket_name_cache[ $cache_key ] = $match;

		return $match;
	}

	/**
	 * @param       $post_id
	 * @param array $record
	 */
	public function update_post( $post_id, array $record ) {
		// nothing is updated in existing tickets
		if ( $this->is_aggregator && ! empty( $this->aggregator_record ) ) {
			$this->aggregator_record->meta['activity']->add( 'tribe_rsvp_tickets', 'skipped', $post_id );
		}
	}

	/**
	 * @param array $record
	 *
	 * @return int|bool Either the new RSVP ticket post ID or `false` on failure.
	 */
	public function create_post( array $record ) {
		$event = $this->get_event_from( $record );
		$data  = $this->get_ticket_data_from( $record );

		/**
		 * Add an opportunity to change the data for the RSVP created via a CSV file
		 *
		 * @since 4.7.3
		 *
		 * @param array
		 */
		$data = (array) apply_filters( 'tribe_tickets_import_rsvp_data', $data );
		$ticket_id = $this->rsvp_tickets->ticket_add( $event->ID, $data );

		$ticket_name = $this->get_value_by_key( $record, 'ticket_name' );
		$cache_key   = $ticket_name . '-' . $event->ID;

		self::$ticket_name_cache[ $cache_key ] = true;

		if ( $this->is_aggregator && ! empty( $this->aggregator_record ) ) {
			$this->aggregator_record->meta['activity']->add( 'rsvp_tickets', 'created', $ticket_id );
		}

		return $ticket_id;
	}

	/**
	 * @param array $record
	 *
	 * @return bool|WP_Post
	 */
	protected function get_event_from( array $record ) {
		$event_name = $this->get_value_by_key( $record, 'event_name' );

		if ( empty( $event_name ) ) {
			return false;
		}

		if ( isset( self::$event_name_cache[ $event_name ] ) ) {
			return self::$event_name_cache[ $event_name ];
		}

		// by title
		$event = get_page_by_title( $event_name, OBJECT, Tribe__Events__Main::POSTTYPE );
		if ( empty( $event ) ) {
			// by slug
			$event = get_page_by_path( $event_name, OBJECT, Tribe__Events__Main::POSTTYPE );
		}
		if ( empty( $event ) ) {
			// by ID
			$event = get_post( $event_name );
		}

		$event = ! empty( $event ) ? $event : false;

		self::$event_name_cache[ $event_name ] = $event;

		return $event;
	}

	/**
	 * @param array $record
	 *
	 * @return array
	 */
	protected function get_ticket_data_from( array $record ) {
		$data                       = array();
		$data['ticket_name']        = $this->get_value_by_key( $record, 'ticket_name' );
		$data['ticket_description'] = $this->get_value_by_key( $record, 'ticket_description' );
		$data['ticket_start_date']  = $this->get_value_by_key( $record, 'ticket_start_sale_date' );
		$data['ticket_end_date']    = $this->get_value_by_key( $record, 'ticket_end_sale_date' );

		$show_description = trim( (string) $this->get_value_by_key( $record, 'ticket_show_description' ) );
		if ( tribe_is_truthy( $show_description ) ) {
			$data['ticket_show_description'] = $show_description;
		}

		$ticket_start_sale_time = $this->get_value_by_key( $record, 'ticket_start_sale_time' );

		if ( ! empty( $data['ticket_start_date'] ) && ! empty( $ticket_start_sale_time ) ) {
			$start_date = new DateTime( $data['ticket_start_date'] . ' ' . $ticket_start_sale_time );

			$data['ticket_start_meridian'] = $start_date->format( 'A' );
			$data['ticket_start_time']     = $start_date->format( 'H:i:00' );
		}

		$ticket_end_sale_time = $this->get_value_by_key( $record, 'ticket_end_sale_time' );

		if ( ! empty( $data['ticket_end_date'] ) && ! empty( $ticket_end_sale_time ) ) {
			$end_date = new DateTime( $data['ticket_end_date'] . ' ' . $ticket_end_sale_time );

			$data['ticket_end_meridian'] = $end_date->format( 'A' );
			$data['ticket_end_time']     = $end_date->format( 'H:i:00' );
		}

		$stock = $this->get_value_by_key( $record, 'ticket_stock' );
		$capacity = $this->get_value_by_key( $record, 'ticket_capacity' );

		if ( empty( $capacity ) ) {
			$capacity = $stock;
		}

		$data['tribe-ticket']['capacity'] = $capacity;
		$data['tribe-ticket']['stock'] = $stock;

		return $data;
	}

	/**
	 * @param array $record
	 *
	 * @return bool
	 */
	public function is_valid_record( array $record ) {
		$valid = parent::is_valid_record( $record );
		if ( empty( $valid ) ) {
			return false;
		}

		$event = $this->get_event_from( $record );

		if ( empty( $event ) ) {
			return false;
		}

		if ( function_exists( 'tribe_is_recurring_event' ) ) {
			$is_recurring = tribe_is_recurring_event( $event->ID );

			if ( $is_recurring ) {
				$this->row_message = sprintf( esc_html__( 'Recurring event tickets are not supported, event %s.', 'event-tickets' ), $event->post_title );
			}

			return ! $is_recurring;
		}
		$this->row_message = false;

		return true;
	}

	/**
	 * @param $row
	 *
	 * @return string
	 */
	protected function get_skipped_row_message( $row ) {
		return $this->row_message === false ? parent::get_skipped_row_message( $row ) : $this->row_message;
	}

	/**
	 * Registers the RSVP post type as a trackable activity
	 *
	 * @param Tribe__Events__Aggregator__Record__Activity $activity
	 */
	public function register_rsvp_activity( $activity ) {
		$activity->register( 'tribe_rsvp_tickets', array( 'rsvp', 'rsvp_tickets' ) );
	}
}
