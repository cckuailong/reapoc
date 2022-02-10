<?php
/**
 * Handles moving attendees from a post to another.
 */
class Tribe__Tickets__Admin__Move_Tickets {
	protected $dialog_name = 'move_tickets';
	protected $ticket_history;
	protected $has_multiple_providers = false;
	protected $ticket_provider = '';

	/**
	 * The attendees currently being operated on.
	 *
	 * Structure is an array indexed by attendee ID, with each value being
	 * the attendee array itself.
	 *
	 * @var array
	 */
	protected $attendees = array();

	public function setup() {
		$this->ticket_history();

		add_action( 'admin_init', array( $this, 'dialog' ) );
		add_filter( 'tribe_events_tickets_attendees_table_bulk_actions', array( $this, 'bulk_actions' ) );
		add_action( 'wp_ajax_move_tickets', array( $this, 'move_tickets_request' ) );
		add_action( 'tribe_tickets_ticket_type_moved', array( $this, 'move_all_tickets_for_type' ), 10, 4 );
		add_action( 'wp_ajax_move_tickets_get_post_types', array( $this, 'get_post_types' ) );
		add_action( 'wp_ajax_move_tickets_get_post_choices', array( $this, 'get_post_choices' ) );
		add_action( 'wp_ajax_move_tickets_get_ticket_types', array( $this, 'get_ticket_types' ) );
		add_action( 'tribe_tickets_all_tickets_moved', array( $this, 'notify_attendees' ), 10, 4 );
	}

	/**
	 * @return Tribe__Tickets__Admin__Ticket_History
	 */
	public function ticket_history() {
		if ( ! isset( $this->ticket_history ) ) {
			$this->ticket_history = new Tribe__Tickets__Admin__Ticket_History;
		};

		return $this->ticket_history;
	}

	/**
	 * Sets up the move tickets dialog.
	 */
	public function dialog() {
		if ( ! $this->is_move_tickets_dialog() ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['check'], 'move_tickets' ) ) {
			return;
		}

		$event_id = absint( tribe_get_request_var( 'event_id', tribe_get_request_var( 'post', 0 ) ) );

		// Bail when we dont have the event
		if ( 0 === $event_id ) {
			return;
		}

		$attendee_ids = tribe_get_request_var( 'ticket_ids', '' );
		$attendee_ids = array_map( 'intval', explode( '|', $attendee_ids ) );
		$attendee_ids = array_filter( $attendee_ids );

		$this->build_attendee_list( $attendee_ids, $event_id );

		/**
		 * Provides an opportunity to modify the template variables used in the
		 * move tickets dialog.
		 *
		 * @param array $template_vars
		 */
		$template_vars = (array) apply_filters( 'tribe_tickets_move_tickets_template_vars', array(
			'title'              => __( 'Move Attendees', 'event-tickets' ),
			'mode'               => 'move_tickets',
			'check'              => wp_create_nonce( 'move_tickets' ),
			'event_name'         => get_the_title( $event_id ),
			'attendees'          => $this->attendees,
			'multiple_providers' => $this->has_multiple_providers,
		) );

		set_current_screen();
		define( 'IFRAME_REQUEST', true );
		$this->dialog_assets();
		iframe_header( $template_vars['title'] );

		extract( $template_vars );
		include EVENT_TICKETS_DIR . '/src/admin-views/move-tickets.php';

		iframe_footer();
		exit();
	}

	/**
	 * Enqueues all assets and data required by the move tickets dialog.
	 */
	protected function dialog_assets() {
		// Ensure common admin CSS is enqueued within this screen
		add_filter( 'tribe_asset_enqueue_tribe-common-admin', '__return_true', 20 );

		/**
		 * Provides an opportunity to modify the variables passed to the move
		 * tickets JS code.
		 *
		 * @param array $script_data
		 */
		$data = apply_filters( 'tribe_tickets_move_tickets_script_data', array(
			'check' => wp_create_nonce( 'move_tickets' ),
			'unexpected_failure' => '<p>' . __( 'Woops! We could not complete the requested operation due to an unforeseen problem.', 'event-tickets' ) . '</p>',
			'update_post_list_failure' => __( 'Unable to update the post list. Please refresh the page and try again.', 'event-tickets' ),
			'no_posts_found' => __( 'No results found - you may need to widen your search criteria.', 'event-tickets' ),
			'no_ticket_types_found' => __( 'No ticket types were found for this post.', 'event-tickets' ),
			'loading_msg' => __( 'Loading, please wait&hellip;', 'event-tickets' ),
			'src_post_id' => absint( tribe_get_request_var( 'event_id', tribe_get_request_var( 'post', 0 ) ) ),
			'ticket_ids' => array_keys( $this->attendees ),
			'provider' => $this->ticket_provider,
			'mode' => 'move_tickets',
		) );

		tribe_asset(
			Tribe__Tickets__Main::instance(),
			'tribe-move-tickets-dialog',
			'move-tickets-dialog.js',
			array( 'jquery' ),
			'admin_enqueue_scripts',
			array(
				'localize' => array(
					'name' => 'tribe_move_tickets_data',
					'data' => $data,
				),
			)
		);
	}

	/**
	 * Indicates if the current request is for the "move tickets type"
	 * dialog or not.
	 *
	 * @return bool
	 */
	protected function is_move_tickets_dialog() {
		return ( isset( $_GET['dialog'] ) && $this->dialog_name === $_GET['dialog'] );
	}

	/**
	 * @return string
	 */
	public function dialog_name() {
		return $this->dialog_name;
	}

	/**
	 * Takes the provided array of attendee IDs and uses it to populate
	 * $this->attendees, to determine which ticket provider we're using
	 * or if the range of tickets includes more than one provider.
	 *
	 * @param array $attendee_ids
	 * @param int   $event_id
	 */
	protected function build_attendee_list( array $attendee_ids, $event_id ) {
		$this->attendees              = [];
		$this->ticket_provider        = '';
		$this->has_multiple_providers = false;

		$args = [
			'in' => $attendee_ids,
		];

		$attendee_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $event_id, $args );

		foreach ( $attendee_data['attendees'] as $attendee ) {

			if ( empty( $attendee['attendee_id'] ) ) {
				$attendee['attendee_id'] = $attendee['ID'];
			}

			$attendee_id = (int) $attendee['attendee_id'];

			$this->attendees[ $attendee_id ] = $attendee;

			$provider = (string) $this->get_ticket_provider( $attendee );

			if (
				! empty( $this->ticket_provider )
				&& ! empty( $provider )
				&& $this->ticket_provider !== $provider
			) {
				$this->has_multiple_providers = true;
			}

			$this->ticket_provider = $provider;
		}
	}

	/**
	 * Given an attendee array, attempts to determine which ticket provider owns it (Woo, RSVP, etc).
	 *
	 * @param array $attendee
	 *
	 * @return string
	 */
	protected function get_ticket_provider( array $attendee ) {
		if ( ! isset( $attendee['product_id'] ) ) {
			return '';
		}

		$ticket_type = Tribe__Tickets__Tickets::load_ticket_object( $attendee['product_id'] );

		if ( ! $ticket_type ) {
			return '';
		}

		if ( property_exists( $ticket_type, 'provider_class' ) ) {
			return $ticket_type->provider_class;
		}

		return '';
	}

	/**
	 * Adds a "Move" option to the attendee screen bulk action selector.
	 *
	 * There is not a corresponding bulk action handler, as such, because when this
	 * is selected further handling will be managed via JS (and interaction will be
	 * through a modal interface).
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	public function bulk_actions( array $actions ) {
		if ( tribe( 'tickets.attendees' )->user_can_manage_attendees() && is_admin() ) {
			$actions['move'] = _x( 'Move', 'attendee screen bulk actions', 'event-tickets' );
		}

		return $actions;
	}

	/**
	 * Responds to ajax requests for a list of supported post types.
	 */
	public function get_post_types() {
		if ( ! wp_verify_nonce( $_POST['check' ], 'move_tickets' ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( array( 'posts' => $this->get_post_types_list() ) );
	}

	/**
	 * Returns a list of post types for which tickets are currently enabled.
	 *
	 * The list is expressed as an array in the following format:
	 *
	 *     [ 'slug' => 'name', ... ]
	 *
	 * @return array
	 */
	protected function get_post_types_list() {
		$types_list = array( 'all' => __( 'All supported types', 'tribe-tickets' ) );

		foreach ( Tribe__Tickets__Main::instance()->post_types() as $type ) {
			$pto = get_post_type_object( $type );
			$types_list[ $type ] = $pto->label;
		}

		return $types_list;
	}

	/**
	 * Responds to requests for a list of possible destination posts.
	 */
	public function get_post_choices() {
		if ( ! wp_verify_nonce( $_POST['check' ], 'move_tickets' ) ) {
			wp_send_json_error();
		}

		$args = wp_parse_args( $_POST, array(
			'post_type'    => '',
			'search_terms' => '',
			'ignore'       => '',
		) );

		wp_send_json_success(
			array(
				'posts' => $this->get_possible_matches( $args ),
			)
		);
	}

	/**
	 * Returns a list of posts that could be possible homes for a ticket
	 * type, given the constraints in optional array $request (if not set,
	 * looks in $_POST for the corresponding values):
	 *
	 * - 'post_type': string or array of post types
	 * - 'search_term': string used for searching posts to narrow the field
	 *
	 * @param array|null $request post parameters (or looks at $_POST if not set)
	 *
	 * @return array
	 */
	protected function get_possible_matches( array $request = null ) {
		// Take the params from $request if set, else look at $_POST
		$params = wp_parse_args( is_null( $request ) ? $_POST : $request, array(
			'post_type' => array(),
			'search_terms' => '',
			'ignore' => '',
		) );

		// The post_type argument should be an array (of all possible types, if not specified)
		$post_types = array_filter( (array) $params['post_type'] );

		if ( empty( $post_types ) || 'all' === $params['post_type'] ) {
			$post_types = array_keys( $this->get_post_types_list() );
		}

		/**
		 * Controls the number of posts returned when searching for posts that
		 * can serve as ticket hosts.
		 *
		 * @param int $limit
		 */
		$limit = (int) apply_filters( 'tribe_tickets_find_ticket_type_host_posts_limit', 100 );

		$ignore_ids = (array) $params['ignore'];
		$ignore_ids = array_map( 'absint', $ignore_ids );
		$ignore_ids = array_filter( $ignore_ids );

		$query_args = array(
			'post_type'      => $post_types,
			'posts_per_page' => $limit,
			'eventDisplay'   => 'custom',
			'orderby'        => 'title',
			'order'          => 'ASC',
			's'              => $params['search_terms'],
			'post__not_in'   => $ignore_ids,
		);

		$posts = get_posts( $query_args );

		return $this->format_post_list( $posts );
	}

	/**
	 * Given an array of WP_Post objects, returns an array containing the post title
	 * of each (with the post ID as the index).
	 *
	 * @param array $query_results
	 *
	 * @return array
	 */
	protected function format_post_list( array $query_results ) {
		$posts = array();

		foreach ( $query_results as $wp_post ) {
			/** This filter is documented in wp-includes/post-template.php */
			$title = apply_filters( 'the_title', $wp_post->post_title, $wp_post->ID );

			// Append the event start date if there is one, ie for events
			if ( $wp_post->_EventStartDate ) {
				$title .= ' (' . tribe_get_start_date( $wp_post->ID ) . ')';
			}

			$posts[ $wp_post->ID ] = $title;
		}

		return $posts;
	}

	/**
	 * Returns a list of ticket types available in a specific post
	 * (belonging to a specific provider).
	 */
	public function get_ticket_types() {
		if ( ! wp_verify_nonce( $_POST['check' ], 'move_tickets' ) ) {
			wp_send_json_error();
		}

		$args = wp_parse_args( $_POST, array(
			'post_id'  => '',
			'provider' => '',
		) );

		wp_send_json_success(
			array(
				'posts' => $this->get_ticket_type_matches( $args['post_id'], $args['provider'], $args['ticket_ids'] ),
			)
		);
	}

	/**
	 * Builds a list of ticket types in the designated post and belonging to
	 * the specified provider.
	 *
	 * @param int    $target_post_id
	 * @param string $provider
	 * @param array $ticket_ids
	 *
	 * @return array
	 */
	protected function get_ticket_type_matches( $target_post_id, $provider, $ticket_ids = array() ) {
		$ticket_types = array();
		$ticket_ids = array_map( 'absint', array_filter( $ticket_ids, 'is_numeric' ) );

		foreach ( Tribe__Tickets__Tickets::get_event_tickets( $target_post_id ) as $ticket ) {
			if ( stripslashes( $provider ) !== $ticket->provider_class ) {
				continue;
			}

			if ( in_array( $ticket->ID, $ticket_ids ) ) {
				continue;
			}

			$ticket_types[ absint( $ticket->ID ) ] = esc_html( $ticket->name );
		}

		return $ticket_types;
	}

	/**
	 * Listens for and handles requests to reassign tickets from one ticket type to another.
	 *
	 * @since 4.10.9 Use customizable ticket name functions.
	 */
	public function move_tickets_request() {
		if ( ! wp_verify_nonce( $_POST['check'], 'move_tickets' ) ) {
			wp_send_json_error();
		}

		$args = wp_parse_args( $_POST, array(
			'ticket_ids'     => '',
			'target_type_id' => '',
			'src_post_id'    => '',
			'target_post_id' => '',
		) );

		$src_post_id    = absint( $args['src_post_id' ] );
		$ticket_ids     = array_map( 'intval', (array) $args['ticket_ids' ] );
		$target_type_id = absint( $args['target_type_id'] );
		$target_post_id = absint( $args['target_post_id'] );

		if ( ! $ticket_ids || ! $target_type_id ) {
			wp_send_json_error( array(
				'message' => esc_html(
					sprintf(
						__( '%1$s could not be moved: valid %2$s IDs or a destination ID were not provided.', 'event-tickets' ),
						tribe_get_ticket_label_plural( 'move_tickets_request_error' ),
						tribe_get_ticket_label_singular( 'move_tickets_request_error' )
					)
				)
			) );
		}

		$moved_tickets = $this->move_tickets( $ticket_ids, $target_type_id, $src_post_id, $target_post_id );

		if ( ! $moved_tickets ) {
			wp_send_json_error( array(
				'message' => esc_html( sprintf( __( '%s could not be moved: there was an unexpected failure during reassignment.', 'event-tickets' ), tribe_get_ticket_label_plural( 'move_tickets_request_error' )
				) )
			) );
		}

		$remove_tickets = ( $src_post_id != $target_post_id ) ? $ticket_ids : null;

		// Include details of the new ticket type the tickets were reassigned to
		$moved_to = sprintf(
			_x( 'assigned to %s', 'moved tickets success message fragment', 'event-tickets' ),
			'<a href="' . esc_url( get_admin_url( null, '/post.php?post=' . $target_type_id . '&action=edit' ) ) . '" target="_blank">' . get_the_title( $target_type_id ) . '</a>'
		);

		// If that ticket type is hosted by a different event post, prepend details of that also
		if ( $src_post_id !== $target_post_id ) {
			$moved_to = sprintf(
				_x( 'moved to %s and', 'moved tickets success message fragment', 'event-tickets' ),
				'<a href="' . esc_url( get_admin_url( null, '/post.php?post=' . $target_post_id . '&action=edit' ) ) . '" target="_blank">' . get_the_title( $target_post_id ) . '</a>'
			) . ' ' . $moved_to;
		}

		wp_send_json_success( array(
			'message' => sprintf(
				_n(
					'%1$d attendee for %2$s was successfully %3$s. By default, we adjust capacity and stock, however, we recommend reviewing each as needed to ensure numbers are correct. This attendee will receive an email notifying them of the change.',
					'%1$d attendees for %2$s were successfully %3$s. By default, we adjust capacity and stock, however, we recommend reviewing each as needed to ensure numbers are correct. These attendees will receive an email notifying them of the change.',
					$moved_tickets,
					'event-tickets'
				),
				$moved_tickets,
				'<a href="' . esc_url( get_admin_url( null, '/post.php?post=' . $src_post_id . '&action=edit' ) ) . '" target="_blank">' . get_the_title( $src_post_id ) . '</a>',
				$moved_to
			),
			'remove_tickets' => $remove_tickets,
		) );
	}

	/**
	 * Moves tickets to a new ticket type.
	 *
	 * The target ticket type *must* belong to the same provider as the tickets being
	 * moved (ie, you cannot move RSVP tickets to a WooCommerce ticket type, nor can
	 * a mix of RSVP and WooCommerce tickets be moved to a new ticket type).
	 *
	 * @param array $ticket_ids
	 * @param int   $tgt_ticket_type_id
	 * @param int   $src_event_id
	 * @param int   $tgt_event_id
	 *
	 * @return int number of successfully moved tickets (zero upon failure to move any)
	 */
	public function move_tickets( array $ticket_ids, $tgt_ticket_type_id, $src_event_id, $tgt_event_id ) {
		$ticket_ids       = array_map( 'intval', $ticket_ids );
		$instigator_id    = get_current_user_id();
		$ticket_type      = Tribe__Tickets__Tickets::load_ticket_object( $tgt_ticket_type_id );
		$successful_moves = 0;

		if ( ! $ticket_type ) {
			return 0;
		}

		$ticket_objects = [];
		$providers      = [];

		$args = [
			'in' => $ticket_ids,
		];

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		$attendee_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $src_event_id, $args );

		foreach ( $attendee_data['attendees'] as $issued_ticket ) {
			$ticket_objects[] = $issued_ticket;

			$providers[ $issued_ticket['provider'] ] = call_user_func( array( $issued_ticket['provider'], 'get_instance' ) );
		}

		// We expect to have found as many tickets as were specified
		if ( count( $ticket_objects ) !== count( $ticket_ids ) ) {
			return 0;
		}

		// Check that the tickets are homogeneous in relation to the ticket provider.
		if ( 1 !== count( $providers ) ) {
			return 0;
		}

		$provider_class   = key( $providers );
		$ticket_type_key  = constant( $provider_class . '::ATTENDEE_PRODUCT_KEY' );
		$ticket_event_key = constant( $provider_class . '::ATTENDEE_EVENT_KEY' );

		if ( empty( $ticket_type_key ) || empty( $ticket_event_key ) ) {
			return 0;
		}

		foreach ( $ticket_objects as $ticket ) {
			$ticket_id          = $ticket['attendee_id'];
			$product_id         = $ticket['product_id'];
			$src_ticket_type_id = get_post_meta( $ticket_id, $ticket_type_key, true );
			$src_qty_sold       = (int) get_post_meta( $src_ticket_type_id, 'total_sales', true );
			$tgt_qty_sold       = (int) get_post_meta( $tgt_ticket_type_id, 'total_sales', true );

			//get stock levels for RSVP Tickets
			if ( 'Tribe__Tickets__RSVP' === $ticket['provider'] ) {
				$src_stock = (int) get_post_meta( $src_ticket_type_id, '_stock', true );
				$tgt_stock = (int) get_post_meta( $tgt_ticket_type_id, '_stock', true );
			}

			/**
			 * Fires immediately before a ticket is moved.
			 *
			 * @param int $ticket_type_id
			 * @param int $tgt_ticket_type_id
			 * @param int $tgt_event_id
			 * @param int $instigator_id
			 */
			do_action( 'tribe_tickets_ticket_before_move', $ticket_id, $tgt_ticket_type_id, $tgt_event_id, $instigator_id );

			/**
			 * Actual moving happens
			 */
			$tgt_event_cap = new Tribe__Tickets__Global_Stock( $tgt_event_id );

			$src_mode = get_post_meta( $product_id, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true );

			// When the Mode is not `own` we have to check and modify some stuff
			if ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE !== $src_mode ) {
				// If we have Source cap and not on Target, we set it up
				if ( ! $tgt_event_cap->is_enabled() ) {
					$src_event_capacity = tribe_tickets_get_capacity( $src_event_id );

					// Activate Shared Capacity on the Ticket
					$tgt_event_cap->enable();

					// Setup the Stock level to match Source capacity
					$tgt_event_cap->set_stock_level( $src_event_capacity );

					// Update the Target event with the Capacity from the Source
					update_post_meta( $tgt_event_id, $tickets_handler->key_capacity, $src_event_capacity );
				} elseif ( Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $src_mode || Tribe__Tickets__Global_Stock::GLOBAL_STOCK_MODE === $src_mode ) {
					// Check if we have capped to avoid ticket cap over event cap
					$src_ticket_capacity = tribe_tickets_get_capacity( $product_id );
					$tgt_event_capacity = tribe_tickets_get_capacity( $tgt_event_id );

					// Don't allow ticket capacity to be bigger than Target Event Cap
					if ( $src_ticket_capacity > $tgt_event_capacity ) {
						update_post_meta( $ticket_id, $tickets_handler->key_capacity, $tgt_event_capacity );
					}
				}
			}

			update_post_meta( $ticket_id, $ticket_type_key, $tgt_ticket_type_id );
			update_post_meta( $ticket_id, $ticket_event_key, $tgt_event_id );

			// adjust sales numbers - don't allow negatives
			$src_qty_sold--;
			$tgt_qty_sold++;
			update_post_meta( $src_ticket_type_id, 'total_sales', $src_qty_sold );
			update_post_meta( $tgt_ticket_type_id, 'total_sales', $tgt_qty_sold );

			//adjust stock numbers for RSVP Tickets
			if ( 'Tribe__Tickets__RSVP' === $ticket['provider'] ) {
				$src_stock ++;
				$tgt_stock --;
				update_post_meta( $src_ticket_type_id, '_stock', $src_stock );
				update_post_meta( $tgt_ticket_type_id, '_stock', $tgt_stock );
			}

			$history_message = sprintf(
				__( 'This ticket was moved to %1$s %2$s from %3$s %4$s', 'event-tickets' ),
				'<a href="' . esc_url( get_the_permalink( $tgt_event_id ) ) . '" target="_blank">' . get_the_title( $tgt_event_id ) . '</a>',
				'<a href="' . esc_url( get_the_permalink( $tgt_ticket_type_id ) ) . '" target="_blank">(' . get_the_title( $tgt_ticket_type_id ) . ')</a>',
				'<a href="' . esc_url( get_the_permalink( $src_event_id ) ) . '" target="_blank">' . get_the_title( $src_event_id ) . '</a>',
				'<a href="' . esc_url( get_the_permalink( $src_ticket_type_id ) ) . '" target="_blank">(' . get_the_title( $src_ticket_type_id ) . ')</a>'
			);

			$history_data = array(
				'ticket_ids' => $ticket_ids,
				'src_event_id' => $src_event_id,
				'tgt_event_id' => $tgt_event_id,
				'tgt_ticket_type_id' => $tgt_ticket_type_id,
			);

			Tribe__Post_History::load( $ticket_id )->add_entry( $history_message, $history_data );

			/**
			 * Fires when a ticket is relocated from ticket type to another, which may be in
			 * a different post altogether.
			 *
			 * @param int $ticket_id                the ticket which has been moved
			 * @param int $src_ticket_type_id       the ticket type it belonged to originally
			 * @param int $tgt_ticket_type_id       the ticket type it now belongs to
			 * @param int $src_event_id             the event/post which the ticket originally belonged to
			 * @param int $tgt_event_id             the event/post which the ticket now belongs to
			 * @param int $instigator_id            the user who initiated the change
			 */
			do_action( 'tribe_tickets_ticket_moved', $ticket_id, $src_ticket_type_id, $tgt_ticket_type_id, $src_event_id, $tgt_event_id, $instigator_id );

			$successful_moves++;
		}

		// Clear attendee cache now that the attendees have moved.
		foreach ( $providers as $provider ) {
			$provider->clear_attendees_cache( $src_event_id );
			$provider->clear_attendees_cache( $tgt_event_id );
		}

		/**
		 * Fires when all of the specified ticket IDs have been moved
		 *
		 * @param array $ticket_ids          each ticket ID
		 * @param int   $tgt_ticket_type_id  the ticket type they were moved to
		 * @param int   $src_event_id        the event they belonged to prior to the move
		 * @param int   $tgt_event_id        the event they belong to after the move
		 */
		do_action( 'tribe_tickets_all_tickets_moved', $ticket_ids, $tgt_ticket_type_id, $src_event_id, $tgt_event_id );

		return $successful_moves;
	}

	/**
	 * Notifies the ticket owners that their tickets have been moved to a new ticket
	 * type.
	 *
	 * @param array $ticket_ids
	 * @param int   $tgt_ticket_type_id
	 * @param int   $src_event_id
	 * @param int   $tgt_event_id
	 */
	public function notify_attendees( $ticket_ids, $tgt_ticket_type_id, $src_event_id, $tgt_event_id ) {
		$to_notify = array();

		$args = [
			'in' => $ticket_ids,
			'by' => [
				'ticket' => $tgt_ticket_type_id,
			],
		];

		$attendee_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $tgt_event_id, $args );

		// Build a list of email addresses we want to send notifications of the change to
		foreach ( $attendee_data['attendees'] as $attendee ) {
			// Skip if an email address isn't available
			if ( ! isset( $attendee['purchaser_email'] ) ) {
				continue;
			}

			if ( ! isset( $to_notify[ $attendee['purchaser_email'] ] ) ) {
				$to_notify[ $attendee['purchaser_email'] ] = array( $attendee );
			} else {
				$to_notify[ $attendee['purchaser_email'] ][] = $attendee;
			}
		}

		foreach ( $to_notify as $email_addr => $affected_tickets ) {
			/**
			 * Sets the moved ticket email address.
			 *
			 * @param string $email_addr
			 */
			$to = apply_filters( 'tribe_tickets_ticket_moved_email_recipient', $email_addr );

			/**
			 * Sets any attachments for the moved ticket email address.
			 *
			 * @param array $attachments
			 */
			$attachments = apply_filters( 'tribe_tickets_ticket_moved_email_attachments', array() );

			/**
			 * Sets the HTML for the moved ticket email.
			 *
			 * @param string $html
			 */
			$content = apply_filters( 'tribe_tickets_ticket_moved_email_content',
				$this->generate_email_content( $tgt_ticket_type_id, $src_event_id, $tgt_event_id, $affected_tickets )
			);

			/**
			 * Sets any headers for the moved tickets email.
			 *
			 * @param array $headers
			 */
			$headers = apply_filters( 'tribe_tickets_ticket_moved_email_headers',
				array( 'Content-type: text/html' )
			);

			/**
			 * Sets the subject line for the moved tickets email.
			 *
			 * @param string $subject
			 */
			$subject = apply_filters( 'tribe_tickets_ticket_moved_email_subject',
				sprintf( __( 'Changes to your tickets from %s', 'event-tickets' ), get_bloginfo( 'name' ) )
			);

			wp_mail( $to, $subject, $content, $headers, $attachments );
		}
	}

	/**
	 * @param int   $tgt_ticket_type_id
	 * @param int   $src_event_id
	 * @param int   $tgt_event_id
	 * @param array $affected_tickets
	 *
	 * @return string
	 */
	protected function generate_email_content( $tgt_ticket_type_id, $src_event_id, $tgt_event_id, $affected_tickets ) {
		$vars = array(
			'original_event_id'   => $src_event_id,
			'original_event_name' => get_the_title( $src_event_id ),
			'new_event_id'        => $tgt_event_id,
			'new_event_name'      => get_the_title( $tgt_event_id ),
			'ticket_type_id'      => $tgt_ticket_type_id,
			'ticket_type_name'    => get_the_title( $tgt_ticket_type_id ),
			'affected_tickets'    => $affected_tickets,
		);

		return tribe_tickets_get_template_part( 'tickets/email-tickets-moved', null, $vars, false );
	}

	/**
	 * When a ticket type is moved, the tickets need to move with it. This callback takes
	 * care of that process.
	 *
	 * @see Tribe__Tickets__Admin__Move_Ticket_Types::move_ticket_type()
	 *
	 * @param int $ticket_type_id
	 * @param int $destination_post_id
	 * @param int $src_post_id
	 * @param int $instigator_id
	 */
	public function move_all_tickets_for_type( $ticket_type_id, $destination_post_id, $src_post_id, $instigator_id ) {
		$args = [
			'by' => [
				'ticket' => $ticket_type_id,
			],
		];

		$attendee_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $src_post_id, $args );

		foreach ( $attendee_data['attendees'] as $issued_ticket ) {
			if ( ! class_exists( $issued_ticket['provider'] ) ) {
				continue;
			}

			$issued_ticket_id = $issued_ticket['attendee_id'];

			// Move the ticket to the destination post
			$event_key = constant( $issued_ticket['provider'] . '::ATTENDEE_EVENT_KEY' );
			update_post_meta( $issued_ticket_id, $event_key, $destination_post_id );

			// Maintain an audit trail
			$history_message = sprintf(
				__( 'This ticket was moved to %1$s from %2$s', 'event-tickets' ),
				'<a href="' . esc_url( get_the_permalink( $destination_post_id ) ) . '" target="_blank">' . get_the_title( $destination_post_id ) . '</a>',
				'<a href="' . esc_url( get_the_permalink( $src_post_id ) ) . '" target="_blank">' . get_the_title( $src_post_id ) . '</a>'
			);

			$history_data = array(
				'src_event_id' => $src_post_id,
				'tgt_event_id' => $destination_post_id,
			);

			Tribe__Post_History::load( $issued_ticket_id )->add_entry( $history_message, $history_data );
		}
	}
}
