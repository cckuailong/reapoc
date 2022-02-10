<?php

use Tribe__Utils__Array as Arr;

/**
 * Handles most actions related to a Ticket or Multiple ones
 */
class Tribe__Tickets__Tickets_Handler {
	/**
	 * Post Meta key for the ticket header
	 *
	 * @since 4.6
	 *
	 * @var string
	 */
	public $key_image_header = '_tribe_ticket_header';

	/**
	 * Post Meta key for event ecommerce provider
	 *
	 * @since 4.6
	 *
	 * @var string
	 */
	public $key_provider_field = '_tribe_default_ticket_provider';

	/**
	 * Post meta key for the ticket capacity
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $key_capacity = '_tribe_ticket_capacity';

	/**
	 * Post meta key for the ticket start date
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $key_start_date = '_ticket_start_date';

	/**
	 * Post meta key for the ticket end date
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $key_end_date = '_ticket_end_date';

	/**
	 * Post meta key for the manual updated meta keys
	 *
	 * @since  4.6
	 *
	 * @var    string
	 */
	public $key_manual_updated = '_tribe_ticket_manual_updated';

	/**
	 * Meta data key we store show_description under
	 *
	 * @since 4.6
	 *
	 * @var string
	 */
	public $key_show_description = '_tribe_ticket_show_description';

	/**
	 * String to represent unlimited tickets
	 * translated in the constructor
	 *
	 * @since 4.6
	 *
	 * @var string
	 */
	public $unlimited_term = 'Unlimited';

	/**
	 *    Class constructor.
	 */
	public function __construct() {
		$this->unlimited_term = __( 'Unlimited', 'event-tickets' );

		$this->add_hooks();

		$this->path = trailingslashit(  dirname( dirname( dirname( __FILE__ ) ) ) );
	}

	/**
	 * Add hooks for saving/meta.
	 *
	 * @since 4.11.4
	 */
	public function add_hooks() {
		$main = Tribe__Tickets__Main::instance();

		foreach ( $main->post_types() as $post_type ) {
			add_action( 'save_post_' . $post_type, array( $this, 'save_post' ) );
		}

		add_filter( 'get_post_metadata', array( $this, 'filter_capacity_support' ), 15, 4 );
		add_filter( 'updated_postmeta', array( $this, 'update_shared_tickets_capacity' ), 15, 4 );

		add_filter( 'updated_postmeta', array( $this, 'update_meta_date' ), 15, 4 );
		add_action( 'wp_insert_post', array( $this, 'update_start_date' ), 15, 3 );

		add_filter( 'tribe_tickets_my_tickets_allow_email_resend_on_attendee_email_update', [ $this, 'maybe_disable_email_resend' ], 9, 3 );

		// Stock actions.
		add_action( 'event_tickets_attendee_ticket_deleted', [ $this, 'maybe_increase_global_stock_data' ], 10, 2 );
	}

	/**
	 * Remove hooks for saving/meta.
	 *
	 * @since 4.11.4
	 */
	public function remove_hooks() {
		$main = Tribe__Tickets__Main::instance();

		foreach ( $main->post_types() as $post_type ) {
			remove_action( 'save_post_' . $post_type, array( $this, 'save_post' ) );
		}

		remove_filter( 'get_post_metadata', array( $this, 'filter_capacity_support' ), 15 );
		remove_filter( 'updated_postmeta', array( $this, 'update_shared_tickets_capacity' ), 15 );

		remove_filter( 'updated_postmeta', array( $this, 'update_meta_date' ), 15 );
		remove_action( 'wp_insert_post', array( $this, 'update_start_date' ), 15 );
	}

	/**
	 * On updating a few meta keys we flag that it was manually updated so we can do
	 * fancy matching for the updating of the event start and end date
	 *
	 * @since  4.6
	 *
	 * @param  int     $meta_id         MID
	 * @param  int     $object_id       Which Post we are dealing with
	 * @param  string  $meta_key        Which meta key we are fetching
	 * @param  int     $event_capacity  To which value the event Capacity was update to
	 *
	 * @return int
	 */
	public function flag_manual_update( $meta_id, $object_id, $meta_key, $date ) {
		$keys = array(
			$this->key_start_date,
			$this->key_end_date,
		);

		// Bail on not Date meta updates
		if ( ! in_array( $meta_key, $keys ) ) {
			return;
		}

		$updated = get_post_meta( $object_id, $this->key_manual_updated );

		// Bail if it was ever manually updated
		if ( in_array( $meta_key, $updated ) ) {
			return;
		}

		// the updated metakey to the list
		add_post_meta( $object_id, $this->key_manual_updated, $meta_key );

		return;
	}

	/**
	 * Verify if we have Manual Changes for a given Meta Key
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket/post we are dealing with here
	 * @param  string|null  $for     If we are looking for one specific key or any
	 *
	 * @return boolean
	 */
	public function has_manual_update( $ticket, $for = null ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		$updated = get_post_meta( $ticket->ID, $this->key_manual_updated );

		if ( is_null( $for ) ) {
			return ! empty( $updated );
		}

		return in_array( $for, $updated );
	}

	/**
	 * Allow us to Toggle flagging the update of Date Meta
	 *
	 * @since   4.6
	 *
	 * @param   boolean  $toggle  Should activate or not?
	 *
	 * @return  void
	 */
	public function toggle_manual_update_flag( $toggle = true ) {
		if ( true === (bool) $toggle ) {
			add_filter( 'updated_postmeta', array( $this, 'flag_manual_update' ), 15, 4 );
		} else {
			remove_filter( 'updated_postmeta', array( $this, 'flag_manual_update' ), 15 );
		}
	}

	/**
	 * On update of the event start date we update the ticket end date
	 * if it wasn't manually updated
	 *
	 * @since  4.6
	 *
	 * @param  int     $meta_id    MID
	 * @param  int     $object_id  Which Post we are dealing with
	 * @param  string  $meta_key   Which meta key we are fetching
	 * @param  string  $date       Value save on the DB
	 *
	 * @return boolean
	 */
	public function update_meta_date( $meta_id, $object_id, $meta_key, $date ) {
		$meta_map = array(
			'_EventStartDate' => $this->key_end_date,
		);

		// Bail when it's not on the Map Meta
		if ( ! isset( $meta_map[ $meta_key ] ) ) {
			return false;
		}

		$event_types = Tribe__Tickets__Main::instance()->post_types();
		$post_type = get_post_type( $object_id );

		// Bail on non event like post type
		if ( ! in_array( $post_type, $event_types ) ) {
			return false;
		}

		$update_meta = $meta_map[ $meta_key ];
		$tickets = $this->get_tickets_ids( $object_id );

		foreach ( $tickets as $ticket ) {
			// Skip tickets with manual updates to that meta
			if ( $this->has_manual_update( $ticket, $update_meta ) ) {
				continue;
			}

			update_post_meta( $ticket, $update_meta, $date );
		}

		return true;
	}

	/**
	 * Updates the Start date of all non-modified tickets when an Ticket supported Post is saved
	 *
	 * @since  4.6
	 *
	 * @param  int      $post_id  Which post we are updating here
	 * @param  WP_Post  $post     Object of the current post updating
	 * @param  boolean  $update   If we are updating or creating a post
	 *
	 * @return boolean
	 */
	public function update_start_date( $post_id, $post, $update ) {
		// Bail on Revision
		if ( wp_is_post_revision( $post_id ) ) {
			return false;
		}

		// Bail if the CPT doens't accept tickets
		if ( ! tribe_tickets_post_type_enabled( $post->post_type ) ) {
			return false;
		}

		$meta_key = $this->key_start_date;
		$tickets = $this->get_tickets_ids( $post_id );

		foreach ( $tickets as $ticket_id ) {
			// Skip tickets with manual updates to that meta
			if ( $this->has_manual_update( $ticket_id, $meta_key ) ) {
				continue;
			}

			$current_date = get_post_meta( $ticket_id, $meta_key, true );
			// Skip if the ticket has already a date
			if ( ! empty( $current_date ) ) {
				continue;
			}

			// 30 min
			$round = 30;
			if ( class_exists( 'Tribe__Events__Main' ) ) {
				$round = (int) tribe( 'tec.admin.event-meta-box' )->get_timepicker_step( 'start' );
			}
			// Convert to seconds
			$round *= MINUTE_IN_SECONDS;
			$date = strtotime( $post->post_date );
			$date = round( $date / $round ) * $round;
			$date = date( Tribe__Date_Utils::DBDATETIMEFORMAT, $date );

			update_post_meta( $ticket_id, $meta_key, $date );
		}

		return true;
	}

	/**
	 * Returns which possible connections an Object might have
	 *
	 * @since  4.6.2
	 *
	 * @return object
	 *         {
	 *             'provider' => (mixed|null)
	 *             'event' => (int|null)
	 *             'product' => (int|null)
	 *             'order' => (int|string|null)
	 *             'order_item' => (int|null)
	 *         }
	 */
	public function get_connections_template() {
		// If you add any new Items here, update the Docblock
		$connections = (object) [
			'provider'   => null,
			'event'      => null,
			'product'    => null,
			'order'      => null,
			'order_item' => null,
		];

		return $connections;
	}

	/**
	 * The simplest way to grab all the relationships from Any Ticket related objects
	 *
	 * On RSVPs Attendees and Orders are the same Post
	 *
	 * @see    \Tribe__Tickets__Tickets_Handler::get_connections_template()
	 *
	 * @since  4.6.2
	 * @since  4.12.3 Use new helper methods for getting provider, including accounting for possibly inactive provider.
	 *
	 * @param int|WP_Post $object Which object you are trying to figure out.
	 *
	 * @return object
	 */
	public function get_object_connections( $object ) {
		$connections = $this->get_connections_template();

		if ( ! $object instanceof WP_Post ) {
			$object = get_post( $object );
		}

		if ( ! $object instanceof WP_Post ) {
			return $connections;
		}

		/** @var Tribe__Tickets__Status__Manager $status */
		$status = tribe( 'tickets.status' );

		$relationships = [
			'event'      => [
				// RSVP
				'_tribe_rsvp_event'          => 'rsvp',
				'_tribe_rsvp_for_event'      => 'rsvp',

				// PayPal tickets
				'_tribe_tpp_event'           => 'tpp',
				'_tribe_tpp_for_event'       => 'tpp',

				// Tickets Commerce.
				\TEC\Tickets\Commerce\Attendee::$event_relation_meta_key => 'tc',

				// EDD
				'_tribe_eddticket_event'     => 'edd',
				'_tribe_eddticket_for_event' => 'edd',

				// Woo
				'_tribe_wooticket_event'     => 'woo',
				'_tribe_wooticket_for_event' => 'woo',
			],
			'product'    => [
				// RSVP
				'_tribe_rsvp_product'      => 'rsvp',

				// PayPal tickets
				'_tribe_tpp_product'       => 'tpp',

				// EDD
				'_tribe_eddticket_product' => 'edd',

				// Woo
				'_tribe_wooticket_product' => 'woo',
			],
			'order'      => [
				// RSVP
				'_tribe_rsvp_order'      => 'rsvp',

				// PayPal tickets
				'_tribe_tpp_order'       => 'tpp',

				// Tickets Commerce.
				\TEC\Tickets\Commerce\Attendee::$order_relation_meta_key => 'tc',

				// EDD
				'_tribe_eddticket_order' => 'edd',

				// Woo
				'_tribe_wooticket_order' => 'woo',

			],
			'order_item' => [
				// PayPal tickets
				'_tribe_tpp_order'            => 'tpp',

				// Tickets Commerce.
				\TEC\Tickets\Commerce\Attendee::$order_relation_meta_key => 'tc',

				// Woo
				'_tribe_wooticket_order_item' => 'woo',
			],
		];

		/**
		 * Allow filtering the relationships so providers can add their own strings.
		 *
		 * @since 4.12.3
		 *
		 * @param array $relationships List of relationship information for providers.
		 */
		$relationships = apply_filters( 'tribe_tickets_handler_relationships', $relationships );

		foreach ( $relationships as $what => $keys ) {
			foreach ( $keys as $key => $provider_slug ) {
				// Skip any key that doesn't exist.
				if ( ! metadata_exists( 'post', $object->ID, $key ) ) {
					continue;
				}

				// When we don't have a provider yet we test and fetch it
				if ( empty( $connections->provider ) ) {
					$connections->provider = $status->get_provider_class_from_slug( $provider_slug );
				}

				// Fetch it
				$meta = get_post_meta( $object->ID, $key, true );

				// Makes sure we have clean data
				if ( empty( $meta ) ) {
					$meta = null;
				} elseif ( is_numeric( $meta ) ) {
					$meta = (int) $meta;
				}

				// The meta value as a connection
				$connections->{$what} = $meta;
			}
		}

		$connections->provider = Tribe__Tickets__Tickets::get_ticket_provider_instance( $connections->provider );

		return $connections;
	}

	/**
	 * Gets the Tickets from a Post
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $post
	 * @return array
	 */
	public function get_tickets_ids( $post = null ) {
		$modules = Tribe__Tickets__Tickets::modules();
		$args = array(
			'post_type'      => array(),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'publish',
			'order_by'       => 'menu_order',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'OR',
			),
		);

		foreach ( $modules as $provider_class => $name ) {
			$provider = call_user_func( array( $provider_class, 'get_instance' ) );
			$module_args = $provider->get_tickets_query_args( $post );

			$args['post_type'] = array_merge( $args['post_type'], $module_args['post_type'] );
			$args['meta_query'] = array_merge( $args['meta_query'], $module_args['meta_query'] );
		}

		$query = new WP_Query( $args );

		return $query->posts;
	}

	/**
	 * On the Update of a Object (Event, Page, Post...) we need to do some other actions:
	 * - Object needs to have Shared Stock Enabled
	 * - Object needs a Shared Stock level to be set
	 * - Shared tickets have their capacity and stock updated
	 *
	 * @since  4.6
	 *
	 * @param  int     $meta_id         MID
	 * @param  int     $object_id       Which Post we are dealing with
	 * @param  string  $meta_key        Which meta key we are fetching
	 * @param  int     $event_capacity  To which value the event Capacity was update to
	 *
	 * @return boolean
	 */
	public function update_shared_tickets_capacity( $meta_id, $object_id, $meta_key, $event_capacity ) {
		// Bail on non-capacity
		if ( $this->key_capacity !== $meta_key ) {
			return false;
		}

		$event_types = Tribe__Tickets__Main::instance()->post_types();

		// Bail on non event like post type
		if ( ! in_array( get_post_type( $object_id ), $event_types, true ) ) {
			return false;
		}

		// We don't accept any non-numeric values here.
		if ( ! is_numeric( $event_capacity ) ) {
			return false;
		}

		// Make sure we are updating the Shared Stock when we update it's capacity
		$object_stock = new Tribe__Tickets__Global_Stock( $object_id );

		// Make sure that we have stock enabled (backwards compatibility)
		$object_stock->enable();

		$completes = array();

		// Get all Tickets
		$tickets = $this->get_tickets_ids( $object_id );

		foreach ( $tickets as $ticket ) {
			$mode = get_post_meta( $ticket, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true );

			// Skip any tickets that are not Shared
			if (
				Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE !== $mode
				&& Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE !== $mode
			) {
				continue;
			}

			$capacity = tribe_tickets_get_capacity( $ticket );

			// When Global Capacity is higher than local ticket one's we bail
			if (
				Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $mode
			) {
				$capped_capacity = $capacity;
				if ( $event_capacity < $capacity ) {
					$capped_capacity = $event_capacity;
				}

				// Otherwise we update tickets required
				tribe_tickets_update_capacity( $ticket, $capped_capacity );
			}

			$totals = $this->get_ticket_totals( $ticket );
			$completes[] = $complete = $totals['pending'] + $totals['sold'];

			$stock = $event_capacity - $complete;
			update_post_meta( $ticket, '_stock', $stock );

			// Makes sure we mark it as in Stock for the status
			if ( 0 !== $stock ) {
				update_post_meta( $ticket, '_stock_status', 'instock' );
			}
		}

		// Setup the Stock level
		$new_object_stock = $event_capacity - array_sum( $completes );
		$object_stock->set_stock_level( $new_object_stock );

		return true;
	}

	/**
	 * Allows us to create capacity when none is defined for an older ticket
	 * It will define the new Capacity based on Stock + Tickets Pending + Tickets Sold
	 *
	 * Important to note that we cannot use `get_ticket()` or `new Ticket_Object` in here
	 * due to triggering of a Infinite loop
	 *
	 * @since  4.6
	 *
	 * @param  mixed   $value      Previous value set
	 * @param  int     $object_id  Which Post we are dealing with
	 * @param  string  $meta_key   Which meta key we are fetching
	 *
	 * @return int
	 */
	public function filter_capacity_support( $value, $object_id, $meta_key, $single = true ) {
		// Something has been already set
		if ( ! is_null( $value ) ) {
			return $value;
		}

		// We only care about Capacity Key
		if ( $this->key_capacity !== $meta_key ) {
			return $value;
		}

		// We remove the Check to allow a fair usage of `metadata_exists`
		remove_filter( 'get_post_metadata', array( $this, 'filter_capacity_support' ), 15 );

		// Bail when we already have the MetaKey saved
		if ( metadata_exists( 'post', $object_id, $meta_key ) ) {
			return get_post_meta( $object_id, $meta_key, $single );
		}

		// Do the migration
		$capacity = $this->migrate_object_capacity( $object_id );

		if ( false === $capacity ) {
			$capacity = '';
		} elseif ( is_int( $capacity ) ) {
			$capacity = (string) $capacity;
		}

		// Hook it back up
		add_filter( 'get_post_metadata', array( $this, 'filter_capacity_support' ), 15, 4 );

		// This prevents get_post_meta without single param to break
		if ( ! $single ) {
			$capacity = (array) $capacity;
		}

		return $capacity;
	}

	/**
	 * Migrates a given Post Object capacity from Legacy Version
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $object  Which Post ID
	 *
	 * @return bool|int
	 */
	public function migrate_object_capacity( $object ) {
		if ( ! $object instanceof WP_Post ) {
			$object = get_post( $object );
		}

		if ( ! $object instanceof WP_Post ) {
			return false;
		}

		// Bail when we don't have a legacy version
		if ( ! tribe( 'tickets.version' )->is_legacy( $object->ID ) ) {
			return false;
		}

		// Defaults to null
		$capacity = null;

		if ( tribe_tickets_post_type_enabled( $object->post_type ) ) {
			$event_stock_obj = new Tribe__Tickets__Global_Stock( $object->ID );

			// Fetches the Current Stock Level
			$capacity = $event_stock_obj->get_stock_level();
			$tickets  = $this->get_tickets_ids( $object->ID );

			foreach ( $tickets as $ticket ) {
				// Indy tickets don't get added to the Event
				if ( ! $this->has_shared_capacity( $ticket ) ) {
					continue;
				}

				$totals = $this->get_ticket_totals( $ticket );

				$capacity += $totals['sold'] + $totals['pending'];
			}
		} else {

			// In here we deal with Tickets migration from legacy
			$mode = get_post_meta( $object->ID, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true );
			$totals = $this->get_ticket_totals( $object->ID );
			$connections = $this->get_object_connections( $object );

			// When migrating we might get Tickets/RSVP without a mode so we set it to Indy Ticket
			if ( ! metadata_exists( 'post', $object->ID, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE ) ) {
				$mode = 'own';
			}

			if ( Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $mode ) {
				$capacity = (int) trim( get_post_meta( $object->ID, Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP, true ) );
				$capacity += $totals['sold'] + $totals['pending'];
			} elseif ( Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE === $mode ) {
				// When using Global we don't set a ticket cap
				$capacity = null;
			} elseif ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === $mode ) {
				/**
				 * Due to a bug in version 4.5.6 of our code RSVP doesn't lower the Stock
				 * so when setting up the capacity we need to avoid counting solds.
				 */
				if (
					$connections->provider instanceof Tribe__Tickets__Tickets
					&& 'Tribe__Tickets__RSVP' === $connections->provider->class_name
				) {
					$capacity = $totals['stock'];
				} else {
					$capacity = array_sum( $totals );
				}
			} else {
				$capacity = -1;
			}

			// Fetch ticket event ID for Updating capacity on event
			$event_id = tribe_events_get_ticket_event( $object->ID );

			// Apply to the Event
			if ( ! empty( $event_id ) ) {
				$this->migrate_object_capacity( $event_id );
			}
		}

		// Bail when we didn't have a capacity
		if ( is_null( $capacity ) ) {
			// Also still update the version, so we don't hit this method all the time
			tribe( 'tickets.version' )->update( $object->ID );

			return false;
		}

		$updated = update_post_meta( $object->ID, $this->key_capacity, $capacity );

		// If we updated the Capacity for legacy update the version
		if ( $updated ) {
			tribe( 'tickets.version' )->update( $object->ID );
		}

		return $capacity;
	}

	/**
	 * Gets the Total of Stock, Sold and Pending for a given ticket
	 *
	 * @since  4.6
	 *
	 * @param  int|WP_Post  $ticket  Which ticket
	 *
	 * @return array
	 */
	public function get_ticket_totals( $ticket ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return [];
		}

		$provider = tribe_tickets_get_ticket_provider( $ticket->ID );

		$totals = [
			'stock'   => get_post_meta( $ticket->ID, '_stock', true ),
			'sold'    => 0,
			'pending' => 0,
		];

		if ( $provider instanceof Tribe__Tickets_Plus__Commerce__EDD__Main ) {
			$totals['sold']    = $provider->stock()->get_purchased_inventory( $ticket->ID, [ 'publish' ] );
			$totals['pending'] = $provider->stock()->count_incomplete_order_items( $ticket->ID );
		} elseif ( $provider instanceof Tribe__Tickets_Plus__Commerce__WooCommerce__Main ) {
			$totals['sold']    = get_post_meta( $ticket->ID, 'total_sales', true );
			$totals['pending'] = $provider->get_qty_pending( $ticket->ID, true );
		} else {
			$totals['sold'] = get_post_meta( $ticket->ID, 'total_sales', true );
		}

		$totals = array_map( 'intval', $totals );

		// Remove Pending from total
		$totals['sold'] -= $totals['pending'];

		return $totals;
	}

	/**
	 * Gets the Total of Stock, Sold and Pending for a given Post
	 * And if there is any Unlimited
	 *
	 * @since  4.6.2
	 *
	 * @param  int|WP_Post $post Which ticket
	 *
	 * @return array
	 */
	public function get_post_totals( $post ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof WP_Post ) {
			return [];
		}

		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post->ID );
		$global  = new Tribe__Tickets__Global_Stock( $post->ID );

		$totals  = [
			'has_unlimited' => false,
			'has_shared'    => $global->is_enabled(),
			'tickets'       => count( $tickets ),
			'capacity'      => (int) tribe_get_event_capacity( $post ), // Could be null.
			'sold'          => 0,
			'pending'       => 0,
			'stock'         => 0,
		];

		foreach ( $tickets as $ticket ) {
			$ticket_totals = $this->get_ticket_totals( $ticket->ID );
			$totals['sold'] += $ticket_totals['sold'];
			$totals['pending'] += $ticket_totals['pending'];

			if ( ! $this->has_shared_capacity( $ticket ) && ! $this->is_unlimited_ticket( $ticket ) ) {
				$totals['stock'] += $ticket_totals['stock'];
			}

			// Check if we have any unlimited tickets. Only have to do this once.
			if ( ! $totals['has_unlimited'] && $this->is_unlimited_ticket( $ticket ) ) {
				$totals['has_unlimited'] = true;
			}
		}

		// We only want to do this once per event.
		if ( $totals['has_shared'] ) {
			$totals['stock'] += $global->get_stock_level();
			$totals['has_shared'] = true;
		}

		if ( $totals['has_unlimited'] ) {
			$totals['stock'] = -1;
		}

		return $totals;
	}

	/**
	 * Returns whether a ticket has unlimited capacity
	 *
	 * @since   4.6
	 *
	 * @param   int|WP_Post|object  $ticket
	 *
	 * @return  bool
	 */
	public function is_ticket_managing_stock( $ticket ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		// Defaults to managing Stock so we don't have Unlimited
		$manage_stock = true;

		// If it exists we use it
		if ( metadata_exists( 'post', $ticket->ID, '_manage_stock' ) ) {
			$manage_stock = get_post_meta( $ticket->ID, '_manage_stock', true );
		}

		return tribe_is_truthy( $manage_stock );
	}

	/**
	 * Returns whether a ticket has unlimited capacity
	 *
	 * @since   4.6
	 *
	 * @param   int|WP_Post|object  $ticket
	 *
	 * @return  bool
	 */
	public function is_unlimited_ticket( $ticket ) {
		return -1 === tribe_tickets_get_capacity( $ticket->ID );
	}

	/**
	 * Returns whether a ticket uses Shared Capacity
	 *
	 * @since   4.6
	 *
	 * @param   int|WP_Post|object  $ticket
	 *
	 * @return  bool
	 */
	public function has_shared_capacity( $ticket ) {
		if ( ! $ticket instanceof WP_Post ) {
			$ticket = get_post( $ticket );
		}

		if ( ! $ticket instanceof WP_Post ) {
			return false;
		}

		$stock_mode = get_post_meta( $ticket->ID, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true );

		return Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $stock_mode || Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE === $stock_mode;
	}

	/**
	 * Returns whether a given object has the correct Provider for a Post or Ticket
	 *
	 * @since   4.7
	 * @since   4.12.3 Account for possibly inactive ticket provider and better checking for default ETP provider.
	 *
	 * @param int|WP_Post                    $post
	 * @param Tribe__Tickets__Tickets|string $provider
	 *
	 * @return  bool
	 */
	public function is_correct_provider( $post, $provider ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$provider = Tribe__Tickets__Tickets::get_ticket_provider_instance( $provider );

		if( empty( $provider ) ) {
			return false;
		}

		if ( tribe_tickets_post_type_enabled( $post->post_type ) ) {
			$default_provider = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $post->ID );
		} else {
			$default_provider = tribe_tickets_get_ticket_provider( $post->ID );
		}

		if ( $default_provider instanceof Tribe__Tickets__Tickets ) {
			$default_provider = $default_provider->class_name;
		}

		if (
			empty( $default_provider )
			&& class_exists( 'Tribe__Tickets_Plus__Main' )
		) {
			$woo = 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main';
			$edd = 'Tribe__Tickets_Plus__Commerce__EDD__Main';

			if ( tribe_tickets_is_provider_active( $woo ) ) {
				$default_provider = $woo;
			} elseif ( tribe_tickets_is_provider_active( $edd ) ) {
				$default_provider = $edd;
			}
		}

		if ( empty( $default_provider ) ) {
			$default_provider = Tribe__Tickets__Tickets::get_default_module();
		}

		return $default_provider === $provider->class_name;
	}

	/**
	 * Checks if there are any unlimited tickets, optionally by stock mode or ticket type
	 *
	 * @since 4.6
	 *
	 * @param int|object (null) $post Post or Post ID tickets are attached to
	 * @param string (null) the stock mode we're concerned with
	 *			can be one of the following:
	 *				Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE ('global')
	 *				Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE ('capped')
	 *				Tribe__Tickets__Global_Stock::OWN_STOCK_MODE ('own')
	 * @param string (null) $provider_class the ticket provider class ex: Tribe__Tickets__RSVP
	 * @return boolean whether there is a ticket (within the provided parameters) with an unlimited stock
	 */
	public function has_unlimited_stock( $post = null, $stock_mode = null, $provider_class = null ) {
		$post_id = Tribe__Main::post_id_helper( $post );
		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		/** @var Tribe__Tickets__Ticket_Object $ticket */
		foreach ( $tickets as $index => $ticket ) {
			// Eliminate tickets by stock mode
			if ( ! is_null( $stock_mode ) && $ticket->global_stock_mode() !== $stock_mode ) {
				unset( $tickets[ $ticket ] );
				continue;
			}

			// Eliminate tickets by provider class
			if ( ! is_null( $provider_class ) && $ticket->provider_class !== $provider_class ) {
				unset( $tickets[ $ticket ] );
				continue;
			}

			if ( $this->is_unlimited_ticket( $ticket ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the total event capacity.
	 *
	 * @deprecated 4.12.0
	 *
	 * @since      4.6
	 *
	 * @param int|object $post Post ID or object to which tickets are attached.
	 *
	 * @return int
	 */
	public function get_total_event_capacity( $post = null ) {
		_deprecated_function( __METHOD__, '4.12.3', 'tribe_get_event_capacity()' );

		$post_id = Tribe__Main::post_id_helper( $post );
		$total   = 0;

		if ( 0 === $post_id ) {
			return $total;
		}

		$has_shared_tickets = 0 !== count( $this->get_event_shared_tickets( $post_id ) );

		if ( $has_shared_tickets ) {
			$total = tribe_get_event_capacity( $post_id );
		}

		// short circuit unlimited stock
		if ( -1 === $total ) {
			return $total;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		// Bail when we don't have Tickets
		if ( empty( $tickets ) ) {
			return $total;
		}

		/** @var Tribe__Tickets__Ticket_Object $ticket */
		foreach ( $tickets as $ticket ) {
			// Skip shared cap Tickets as it's added when we fetch the total
			if (
				Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $ticket->global_stock_mode()
				|| Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE === $ticket->global_stock_mode()
			) {
				continue;
			}

			$capacity = $ticket->capacity();

			if ( -1 === $capacity || '' === $capacity ) {
				$total = -1;
				break;
			}

			$capacity = is_numeric( $capacity ) ? (int) $capacity : 0;

			$total += $capacity;
		}

		return (int) apply_filters( 'tribe_tickets_total_event_capacity', $total, $post_id );
	}

	/**
	 * Get an array list of unlimited tickets for an event.
	 *
	 * @since 4.6
	 *
	 * @param int|object (null) $post Post or Post ID tickets are attached to
	 *
	 * @return array List of unlimited tickets for an event.
	 */
	public function get_event_unlimited_tickets( $post = null ) {
		$post_id     = Tribe__Main::post_id_helper( $post );
		$ticket_list = [];

		if ( 0 === $post_id ) {
			return $ticket_list;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		if ( empty( $tickets ) ) {
			return $ticket_list;
		}

		foreach ( $tickets as $ticket ) {
			if ( ! $this->is_unlimited_ticket( $ticket ) ) {
				continue;
			}

			$ticket_list[] = $ticket;
		}

		return $ticket_list;
	}

	/**
	 * Get an array list of independent tickets for an event.
	 *
	 * @since 4.6
	 *
	 * @param int|object (null) $post Post or Post ID tickets are attached to
	 *
	 * @return array List of independent tickets for an event.
	 */
	public function get_event_independent_tickets( $post = null ) {
		$post_id     = Tribe__Main::post_id_helper( $post );
		$ticket_list = [];

		if ( 0 === $post_id ) {
			return $ticket_list;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		if ( empty( $tickets ) ) {
			return $ticket_list;
		}

		foreach ( $tickets as $ticket ) {
			if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE != $ticket->global_stock_mode() || 'Tribe__Tickets__RSVP' === $ticket->provider_class ) {
				continue;
			}

			// Failsafe - should not include unlimited tickets
			if ( $this->is_unlimited_ticket( $ticket ) ) {
				continue;
			}

			$ticket_list[] = $ticket;
		}

		return $ticket_list;
	}

	/**
	 * Get an array list of RSVPs for an event.
	 *
	 * @since 4.6
	 *
	 * @param int|object (null) $post Post or Post ID tickets are attached to.
	 *
	 * @return array List of RSVPs for an event.
	 */
	public function get_event_rsvp_tickets( $post = null ) {
		$post_id     = Tribe__Main::post_id_helper( $post );
		$ticket_list = [];

		if ( 0 === $post_id ) {
			return $ticket_list;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		if ( empty( $tickets ) ) {
			return $ticket_list;
		}

		foreach ( $tickets as $ticket ) {
			if ( 'Tribe__Tickets__RSVP' !== $ticket->provider_class ) {
				continue;
			}

			$ticket_list[] = $ticket;
		}

		return $ticket_list;
	}

	/**
	 * Gets the maximum quantity able to be purchased in a single Add to Cart action for a given ticket.
	 *
	 * If a ticket's actual ticket stock available is Unlimited, this will return the maximum allowed to be purchased
	 * in a single action (i.e. always zero or greater).
	 *
	 * @see    \Tribe__Tickets__Ticket_Object::available() The actual ticket stock available, allowing -1 for Unlimited.
	 *
	 * @since  4.8.1
	 * @since  4.11.5 Return a zero or positive integer and add a maximum able to be purchased in a single action,
	 *               for sanity and performance reasons.
	 *
	 * @param int|string $ticket_id Ticket from which to fetch purchase max.
	 *
	 * @return int A non-negative integer of how many tickets can be purchased in a single "add to cart" type of action
	 *             (allows zero but not `-1` for Unlimited). If oversold, will be corrected to zero.
	 */
	public function get_ticket_max_purchase( $ticket_id ) {
		/** @var Tribe__Cache $cache */
		$cache = tribe( 'cache' );
		$key   = __METHOD__ . '-' . $ticket_id;

		if ( isset( $cache[ $key ] ) ) {
			return $cache[ $key ];
		}

		$event = tribe_events_get_ticket_event( $ticket_id );

		if ( ! $event instanceof WP_Post ) {
			return 0;
		}

		$provider = tribe_tickets_get_ticket_provider( $ticket_id );

		if ( empty( $provider ) ) {
			return 0;
		}

		$ticket = $provider->get_ticket( $event, $ticket_id );

		if ( ! $ticket instanceof Tribe__Tickets__Ticket_Object ) {
			return 0;
		}

		$max_at_a_time = $this->get_max_qty_limit_per_transaction( $ticket );

		// The actual ticket stock, not limited by Max At A Time.
		$stock_available = $ticket->available();

		// Change Unlimited to Max At A Time.
		if ( - 1 === $stock_available ) {
			$stock_available = $max_at_a_time;
		}

		$available_at_a_time = min( $stock_available, $max_at_a_time );

		/**
		 * Allows filtering the quantity available displayed below the ticket
		 * quantity input for purchase of this one ticket.
		 *
		 * If less than the maximum quantity available, will restrict that as well.
		 *
		 * @since 4.8.1
		 *
		 * @param int                           $available_at_a_time Max purchase quantity, as restricted by Max At A Time.
		 * @param Tribe__Tickets__Ticket_Object $ticket              Ticket object.
		 * @param WP_Post                       $event               Event post.
		 * @param int                           $ticket_id           Raw ticket ID.
		 */
		$available_at_a_time = apply_filters( 'tribe_tickets_get_ticket_max_purchase', $available_at_a_time, $ticket, $event, $ticket_id );

		// Protect against filters passing `-1` as unlimited (from filters not yet updated for logic from version 4.11.5).
		if ( - 1 === $available_at_a_time ) {
			$available_at_a_time = $max_at_a_time;
		}

		// If somehow oversold, set max allowed to zero.
		if ( 0 > $available_at_a_time ) {
			$available_at_a_time = 0;
		}

		$cache[ $key ] = $available_at_a_time;

		return $available_at_a_time;
	}

	/**
	 * Get the maximum quantity allowed to be added to cart in a single action, for performance and sanity reasons.
	 *
	 * @since 4.11.5
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket Ticket object.
	 *
	 * @return int
	 */
	private function get_max_qty_limit_per_transaction( Tribe__Tickets__Ticket_Object $ticket ) {
		$default_max = 100;

		/**
		 * Cap the amount of tickets able to be purchased at a single time (single "add to cart" action)
		 * for sanity and performance reasons.
		 *
		 * Anything less than `1` will be ignored and reset to the default.
		 *
		 * @since 4.11.5
		 *
		 * @param int                           $default_max Maximum quantity allowed at one time (only applicable if
		 *                                                   the ticket stock available is greater).
		 * @param Tribe__Tickets__Ticket_Object $ticket      Ticket object.
		 *
		 * @return int
		 */
		$max_at_a_time = absint(
			apply_filters( 'tribe_tickets_get_ticket_default_max_purchase', $default_max, $ticket )
		);

		// Don't allow less than 1.
		if ( 1 > $max_at_a_time ) {
			$max_at_a_time = $default_max;
		}

		return $max_at_a_time;
	}

	/**
	 * Get an array list of shared capacity tickets for an event.
	 *
	 * @since 4.6
	 *
	 * @param int|object (null) $post Post or Post ID tickets are attached to
	 *
	 * @return array List of shared capacity tickets for an event.
	 */
	public function get_event_shared_tickets( $post = null ) {
		$post_id     = Tribe__Main::post_id_helper( $post );
		$ticket_list = [];

		if ( 0 === $post_id ) {
			return $ticket_list;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post_id );

		if ( empty( $tickets ) ) {
			return $ticket_list;
		}

		foreach ( $tickets as $ticket ) {
			$stock_mode = $ticket->global_stock_mode();
			if ( empty( $stock_mode ) || Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === $stock_mode ) {
				continue;
			}

			// Failsafe - should not include unlimited tickets
			if ( $this->is_unlimited_ticket( $ticket ) ) {
				continue;
			}

			$ticket_list[] = $ticket;
		}

		return $ticket_list;
	}

	/**
	 * Gets the Default mode in which tickets will be generated
	 *
	 * @since  4.6.2
	 *
	 * @return string
	 */
	public function get_default_capacity_mode() {
		/**
		 * Filter Default Ticket Capacity Type
		 *
		 * @since 4.6
		 *
		 * @param string 'global'
		 *
		 * @return string ('global','capped','own','')
		 *
		 */
		return apply_filters( 'tribe_tickets_default_ticket_capacity_type', 'global' );
	}

	/**
	 * Saves the Ticket Editor related tickets on Save of the Parent Post
	 *
	 * Due to how we can have multiple Post Types where we can attach tickets we have one place where
	 * all panels will save, because `save_post_$post_type` requires a loop
	 *
	 * @since  4.6.2
	 *
	 * @param  int  $post  Post that will be saved
	 *
	 * @return string
	 */
	public function save_post( $post ) {
		// We're calling this during post save, so the save nonce has already been checked.

		// don't do anything on autosave, auto-draft, or massupdates
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return false;
		}

		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		// Bail on Invalid post
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		$this->save_form_settings( $post );
		$this->save_order( $post );

		/**
		 * Allows us to Run any actions related to a Post that has Tickets
		 *
		 * @since  4.6.2
		 *
		 * @param  WP_Post $post Which post we are saving
		 */
		do_action( 'tribe_tickets_save_post', $post );
	}

	/**
	 * Saves the Ticket Editor settings form
	 *
	 * @since  4.6.2
	 *
	 * @param  int   $post  Post that will be saved
	 * @param  array $data  Params that will be used to save
	 *
	 * @return string
	 */
	public function save_form_settings( $post, $data = null ) {
		// Don't do anything on autosave, auto-draft, or mass updates.
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return false;
		}

		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		// Bail on Invalid post
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// If we didn't get any Ticket data we fetch from the $_POST
		if ( is_null( $data ) ) {
			$data = tribe_get_request_var( array( 'tribe-tickets', 'settings' ), null );
		}

		if ( empty( $data ) ) {
			return false;
		}

		/**
		 * Allow other plugins to hook into this to add settings
		 *
		 * @since 4.6
		 *
		 * @param array $data the array of parameters to filter
		 */
		do_action( 'tribe_events_save_tickets_settings', $data );

		if ( isset( $data['event_capacity'] ) && is_numeric( $data['event_capacity'] ) ) {
			tribe_tickets_update_capacity( $post, $data['event_capacity'] );
		}

		if ( ! empty( $data['header_image_id'] ) ) {
			update_post_meta( $post->ID, $this->key_image_header, $data['header_image_id'] );
		} else {
			delete_post_meta( $post->ID, $this->key_image_header );
		}

		// We reversed this logic on the back end
		if ( class_exists( 'Tribe__Tickets_Plus__Attendees_List' ) ) {
			/** @var \Tribe__Editor $editor */
			$editor = tribe( 'editor' );

			// Only update this meta if not using blocks.
			if ( ! $editor->is_events_using_blocks() ) {
				// Enforce meta value when saving after checking for block/shortcode later.
				if ( ! empty( $data['show_attendees'] ) ) {
					add_filter( 'tribe_tickets_event_is_showing_attendee_list', '__return_true' );
				}

				update_post_meta( $post->ID, \Tribe\Tickets\Events\Attendees_List::HIDE_META_KEY, ! empty( $data['show_attendees'] ) );
			}
		}

		// Change the default ticket provider
		if ( ! empty( $data['default_provider'] ) ) {
			update_post_meta( $post->ID, $this->key_provider_field, $data['default_provider'] );
		} else {
			delete_post_meta( $post->ID, $this->key_provider_field );
		}
	}

	/**
	 * Save the the drag-n-drop ticket order
	 *
	 * @since 4.6
	 *
	 * @param int $post
	 *
	 */
	public function save_order( $post, $tickets = null ) {
		// We're calling this during post save, so the save nonce has already been checked.

		// don't do anything on autosave, auto-draft, or massupdates
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return false;
		}

		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		// Bail on Invalid post
		if ( ! $post instanceof WP_Post ) {
			return false;
		}

		// If we didn't get any Ticket data we fetch from the $_POST
		if ( is_null( $tickets ) ) {
			$tickets = tribe_get_request_var( array( 'tribe-tickets', 'list' ), null );
		}

		if ( empty( $tickets ) ) {
			return false;
		}

		foreach ( $tickets as $id => $ticket ) {
			if ( ! isset( $ticket['order'] ) ) {
				continue;
			}

			$args = array(
				'ID'         => absint( $id ),
				'menu_order' => (int) $ticket['order'],
			);

			$updated[] = wp_update_post( $args );
		}

		// Verify if any failed
		return ! in_array( 0, $updated );
	}

	/**
	 * Sorts tickets according to stored menu_order
	 *
	 * @since  4.6
	 *
	 * @param  object  $a  First  Compare item
	 * @param  object  $b  Second Compare item
	 *
	 * @return array
	 */
	protected function sort_by_menu_order( $a, $b ) {
		return $a->menu_order - $b->menu_order;
	}

	/**
	 * Sorts tickets according to stored menu_order
	 *
	 * @since 4.6
	 *
	 * @param array $tickets array of ticket objects
	 *
	 * @return array - sorted array of ticket objects
	 */
	public function sort_tickets_by_menu_order( $tickets ) {
		foreach ( $tickets as $key => $ticket ) {
			// make sure they are ordered correctly
			$orderpost          = get_post( $ticket->ID );
			$ticket->menu_order = $orderpost->menu_order;
		}

		usort( $tickets, array( $this, 'sort_by_menu_order' ) );

		return $tickets;
	}

	/**
	 * Determine whether the ticket is accessible to the current user.
	 *
	 * @since 4.11.0
	 *
	 * @param int $ticket_id Ticket ID.
	 *
	 * @return true|WP_Error True if the ticket is accessible or a `WP_Error` if the user cannot access
	 *                       the current ticket at all.
	 */
	public function is_ticket_readable( $ticket_id ) {
		$ticket_post = get_post( $ticket_id );

		if ( ! $ticket_post instanceof WP_Post ) {
			return new WP_Error( 'ticket-not-found', 'ticket-not-found', array( 'status' => 404 ) );
		}

		$ticket_post_type_object = get_post_type_object( $ticket_post->post_type );

		if ( null === $ticket_post_type_object ) {
			return new WP_Error( 'ticket-provider-not-found', 'ticket-provider-not-found', array( 'status' => 500 ) );
		}

		$read_cap = $ticket_post_type_object->cap->read_post;

		if ( ! ( 'publish' === $ticket_post->post_status || current_user_can( $read_cap, $ticket_id ) ) ) {
			return new WP_Error( 'ticket-not-accessible', 'ticket-not-accessible', array( 'status' => 401 ) );
		}

		/**
		 * Not only the ticket should be accessible by the user but the event too should be.
		 */
		$event = tribe_events_get_ticket_event( $ticket_id );

		if ( ! $event instanceof WP_Post ) {
			return new WP_Error( 'ticket-not-accessible', 'ticket-not-accessible', array( 'status' => 401 ) );
		}

		$event_post_type_object = get_post_type_object( $event->post_type );
		$read_cap               = $event_post_type_object->cap->read_post;

		if ( ! ( 'publish' === $event->post_status || current_user_can( $read_cap, $event->ID ) ) ) {
			return new WP_Error( 'ticket-not-accessible', 'ticket-not-accessible', array( 'status' => 401 ) );
		}

		return true;
	}

	/**
	 * Maybe disable the email resend if the attendee has reached their max limit.
	 *
	 * @since 5.1.0
	 *
	 * @param bool         $allow_resending_email Whether to allow email resending.
	 * @param WP_Post|null $ticket                The ticket post object if available, otherwise null.
	 * @param array|null   $attendee              The attendee information if available, otherwise null.
	 *
	 * @return bool Whether to allow email resending.
	 */
	public function maybe_disable_email_resend( $allow_resending_email, $ticket = null, $attendee = null ) {
		// Check if we have an attendee to reference or resending has been disabled already.
		if ( ! is_array( $attendee ) || ! $allow_resending_email ) {
			return $allow_resending_email;
		}

		$ticket_sent = (int) Arr::get( $attendee, 'ticket_sent', 0 );

		/**
		 * Allow filtering the maximum number of emails can be resent to an attendee.
		 *
		 * Return -1 to remove the limit entirely.
		 *
		 * @since 5.1.0
		 *
		 * @param int          $max_resend_limit The maximum number of emails can be resent to an attendee.
		 * @param WP_Post|null $ticket           The ticket post object if available, otherwise null.
		 * @param array|null   $attendee         The attendee information if available, otherwise null.
		 */
		$max_resend_limit = apply_filters( 'tribe_tickets_handler_email_max_resend_limit', 2, $ticket, $attendee );

		// Check if limit is unlimited or if the attendee has been at/sent below the current limit.
		if ( -1 === $max_resend_limit || $ticket_sent <= $max_resend_limit ) {
			return $allow_resending_email;
		}

		return false;
	}

	/**
	 * Static Singleton Factory Method
	 *
	 * @return Tribe__Tickets__Tickets_Handler
	 */
	public static function instance() {
		return tribe( 'tickets.handler' );
	}

	/**
	 * Increment the global stock data for an Event if Shared stock is available.
	 *
	 * @since 5.1.5
	 *
	 * @param int $post_id Post or Event ID.
	 * @param int $attendee_id Attendee ID.
	 */
	public function maybe_increase_global_stock_data( $post_id, $attendee_id ) {

		$global_stock = new Tribe__Tickets__Global_Stock( $post_id );

		if ( ! $global_stock->is_enabled() ) {
			return;
		}

		$prev_stock = $global_stock->get_stock_level();

		$global_stock->set_stock_level( $prev_stock + 1 );
	}

	/************************
	 *                      *
	 *  Deprecated Methods  *
	 *                      *
	 ************************/
	// @codingStandardsIgnoreStart

	/**
	 * Slug of the admin page for attendees
	 *
	 * @deprecated 4.6.2
	 *
	 * @var string
	 */
	public static $attendees_slug = 'tickets-attendees';

	/**
	 * Save or delete the image header for tickets on an event
	 *
	 * @deprecated 4.6.2
	 *
	 * @param int $post_id
	 */
	public function save_image_header( $post_id ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.handler' )->save_settings()" );
	}

	/**
	 * Saves the event ticket settings via ajax
	 *
	 * @deprecated 4.6.2
	 *
	 * @since 4.6
	 */
	public function ajax_handler_save_settings() {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.metabox' )->ajax_settings()" );
		return tribe( 'tickets.metabox' )->ajax_settings();

	}

	/**
	 * Includes the tickets metabox inside the Event edit screen
	 *
	 * @deprecated 4.6.2
	 *
	 * @param  WP_Post $post
	 *
	 * @return string
	 */
	public function do_meta_box( $post ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.metabox' )->render( \$post )" );
		return tribe( 'tickets.metabox' )->render( $post );
	}

	/**
	 * Returns the attachment ID for the header image for a event.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $event_id
	 *
	 * @return mixed
	 */
	public function get_header_image_id( $event_id ) {
		_deprecated_function( __METHOD__, '4.6.2', "get_post_meta( \$event_id, tribe( 'tickets.handler' )->key_image_header, true );" );
		return get_post_meta( $event_id, tribe( 'tickets.handler' )->key_image_header, true );
	}

	/**
	 * Render the ticket row into the ticket table
	 *
	 * @deprecated 4.6.2
	 *
	 * @since 4.6
	 *
	 * @param Tribe__Tickets__Ticket_Object $ticket
	 */
	public function render_ticket_row( $ticket ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.admin.views' )->template( array( 'editor', 'ticket-row' ) )" );
		tribe( 'tickets.admin.views' )->template( array( 'editor', 'list-row' ), array( 'ticket' => $ticket ) );
	}

	/**
	 * Returns the markup for the History for a Given Ticket
	 *
	 * @deprecated 4.6.2
	 *
	 * @param  int    $ticket_id
	 *
	 * @return string
	 */
	public function get_history_content( $post_id, $ticket ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.admin.views' )->template( 'settings_admin_panel' )" );
		return tribe( 'tickets.admin.views' )->template( 'tickets-history', array( 'post_id' => $post_id, 'ticket' => $ticket ), false );
	}

	/**
	 * Returns the markup for the Settings Panel for Tickets
	 *
	 * @deprecated 4.6.2
	 *
	 * @param  int    $post_id
	 *
	 * @return string
	 */
	public function get_settings_panel( $post_id ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.admin.views' )->template( 'settings_admin_panel' )" );
		return tribe( 'tickets.admin.views' )->template( 'settings_admin_panel', array( 'post_id' => $post_id ), false );
	}

	/**
	 * Echoes the markup for the tickets list in the tickets metabox
	 *
	 * @deprecated 4.6.2
	 *
	 * @param int   $deprecated event ID
	 * @param array $tickets
	 */
	public function ticket_list_markup( $deprecated, $tickets = array() ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.admin.views' )->template( 'list' )" );

		tribe( 'tickets.admin.views' )->template( 'list', array( 'tickets' => $tickets ) );
	}

	/**
	 * Returns the markup for the tickets list in the tickets metabox
	 *
	 * @deprecated 4.6.2
	 *
	 * @param array $tickets
	 *
	 * @return string
	 */
	public function get_ticket_list_markup( $tickets = array() ) {
		_deprecated_function( __METHOD__, '4.6.2', "tribe( 'tickets.admin.views' )->template( 'list' )" );

		return tribe( 'tickets.admin.views' )->template( 'list', array( 'tickets' => $tickets ), false );
	}

	/**
	 * Whether the ticket handler should render the title in the attendees report.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param bool $should_render_title
	 */
	public function should_render_title( $deprecated ) {
		_deprecated_function( __METHOD__, '4.6.2', 'add_filter( \'tribe_tickets_attendees_show_title\', \'_return_false\' );' );
		return true;
	}

	/**
	 * Returns the current post being handled.
	 *
	 * @deprecated 4.6.2
	 *
	 * @return array|bool|null|WP_Post
	 */
	public function get_post() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::get_post' );
		return tribe( 'tickets.attendees' )->get_post();
	}

	/**
	 * Print Check In Totals at top of Column
	 *
	 * @deprecated 4.6.2
	 *
	 */
	public function print_checkedin_totals() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::print_checkedin_totals' );
		tribe( 'tickets.attendees' )->print_checkedin_totals();
	}

	/**
	 * Returns the full URL to the attendees report page.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_attendee_report_link( $post ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::get_report_link' );
		return tribe( 'tickets.attendees' )->get_report_link( $post );
	}

	/**
	 * Adds the "attendees" link in the admin list row actions for each event.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	public function attendees_row_action( $actions ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::filter_admin_row_actions' );
		return tribe( 'tickets.attendees' )->filter_admin_row_actions( $actions );
	}

	/**
	 * Registers the Attendees admin page
	 */
	public function attendees_page_register() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::register_page' );
		tribe( 'tickets.attendees' )->register_page();
	}

	/**
	 * Enqueues the JS and CSS for the attendees page in the admin
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $hook
	 */
	public function attendees_page_load_css_js( $hook ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::enqueue_assets' );
		tribe( 'tickets.attendees' )->enqueue_assets( $hook );
	}

	/**
	 * Loads the WP-Pointer for the Attendees screen
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $hook
	 */
	public function attendees_page_load_pointers( $hook ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::load_pointers' );
		tribe( 'tickets.attendees' )->load_pointers( $hook );
	}

	/**
	 * Sets up the Attendees screen data.
	 *
	 * @deprecated 4.6.2
	 *
	 */
	public function attendees_page_screen_setup() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::screen_setup' );
		tribe( 'tickets.attendees' )->screen_setup();
	}

	/**
	 * @deprecated 4.6.2
	 */
	public function attendees_admin_body_class( $body_classes ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::filter_admin_body_class' );
		tribe( 'tickets.attendees' )->filter_admin_body_class( $body_classes );
	}

	/**
	 * Sets the browser title for the Attendees admin page.
	 * Uses the event title.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $admin_title
	 * @param $unused_title
	 *
	 * @return string
	 */
	public function attendees_admin_title( $admin_title, $unused_title ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::filter_admin_title' );
		tribe( 'tickets.attendees' )->filter_admin_title( $admin_title, $unused_title );
	}

	/**
	 * Renders the Attendees page
	 *
	 * @deprecated 4.6.2
	 */
	public function attendees_page_inside() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::render' );
		tribe( 'tickets.attendees' )->render();
	}

	/**
	 * Generates a list of attendees taking into account the Screen Options.
	 * It's used both for the Email functionality, as for the CSV export.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $event_id
	 *
	 * @return array
	 */
	private function generate_filtered_attendees_list( $event_id ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::generate_filtered_list' );

		tribe( 'tickets.attendees' )->generate_filtered_list( $event_id );
	}

	/**
	 * Checks if the user requested a CSV export from the attendees list.
	 * If so, generates the download and finishes the execution.
	 *
	 * @deprecated 4.6.2
	 */
	public function maybe_generate_attendees_csv() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::maybe_generate_csv' );
		tribe( 'tickets.attendees' )->maybe_generate_csv();
	}

	/**
	 * Handles the "send to email" action for the attendees list.
	 *
	 * @deprecated 4.6.2
	 */
	public function send_attendee_mail_list() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::send_mail_list' );
		tribe( 'tickets.attendees' )->send_mail_list();
	}

	/**
	 * Injects event post type
	 *
	 * @deprecated 4.6.2
	 */
	public function event_details_top() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::event_details_top' );
		tribe( 'tickets.attendees' )->event_details_top();
	}

	/**
	 * Injects action links into the attendee screen.
	 *
	 * @deprecated 4.6.2
	 */
	public function event_action_links() {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::event_action_links' );
		tribe( 'tickets.attendees' )->event_action_links();
	}

	/**
	 * Sets the content type for the attendees to email functionality.
	 * Allows for sending an HTML email.
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $content_type
	 *
	 * @return string
	 */
	public function set_contenttype( $content_type ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::set_contenttype' );
	}

	/**
	 * Tests if the user has the specified capability in relation to whatever post type
	 * the ticket relates to.
	 *
	 * For example, if tickets are created for the banana post type, the generic capability
	 * "edit_posts" will be mapped to "edit_bananas" or whatever is appropriate.
	 *
	 * @deprecated 4.6.2
	 *
	 * @internal for internal plugin use only (in spite of having public visibility)
	 *
	 * @param  string $generic_cap
	 * @param  int    $event_id
	 * @return boolean
	 */
	public function user_can( $generic_cap, $event_id ) {
		_deprecated_function( __METHOD__, '4.6.2', 'Tribe__Tickets__Attendees::user_can' );
	}

	// @codingStandardsIgnoreEnd
}
