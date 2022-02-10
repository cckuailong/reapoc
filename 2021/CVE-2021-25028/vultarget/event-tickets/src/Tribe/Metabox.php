<?php

/**
 *    Class in charge of registering and displaying
 *  the tickets metabox in the event edit screen.
 *  Metabox will only be added if there's a
 *     Tickets Pro provider (child of TribeTickets)
 *     available.
 */
class Tribe__Tickets__Metabox {

	/**
	 * Configure all action and filters user by this Class
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'add_meta_boxes', array( $this, 'configure' ) );

		add_action( 'tribe_events_tickets_bottom_right', array( $this, 'get_ticket_controls' ), 10, 2 );

		add_action( 'wp_ajax_tribe-ticket-panels', array( $this, 'ajax_panels' ) );

		add_action( 'wp_ajax_tribe-ticket-add', array( $this, 'ajax_ticket_add' ) );
		add_action( 'wp_ajax_tribe-ticket-edit', array( $this, 'ajax_ticket_edit' ) );
		add_action( 'wp_ajax_tribe-ticket-delete', array( $this, 'ajax_ticket_delete' ) );

		add_action( 'wp_ajax_tribe-ticket-checkin', array( $this, 'ajax_attendee_checkin' ) );
		add_action( 'wp_ajax_tribe-ticket-uncheckin', array( $this, 'ajax_attendee_uncheckin' ) );
	}

	/**
	 * Configures the Tickets Editor into a Post Type
	 *
	 * @since  4.6.2
	 *
	 * @param  string $post_type Which post type we are trying to configure
	 *
	 * @return void
	 */
	public function configure( $post_type = null ) {
		$modules = Tribe__Tickets__Tickets::modules();
		if ( empty( $modules ) ) {
			return;
		}

		if ( ! in_array( $post_type, Tribe__Tickets__Main::instance()->post_types() ) ) {
			return;
		}

		add_meta_box(
			'tribetickets',
			esc_html( tribe_get_ticket_label_plural( 'meta_box_title' ) ),
			array( $this, 'render' ),
			$post_type,
			'normal',
			'high',
			array(
				'__back_compat_meta_box' => true,
			)
		);

		// If we get here means that we will need Thickbox
		add_thickbox();
	}

	/**
	 * Render the actual Metabox
	 *
	 * @since  4.6.2
	 *
	 * @param  int   $post_id  Which post we are dealing with
	 *
	 * @return string|bool
	 */
	public function render( $post_id ) {
		$modules = Tribe__Tickets__Tickets::modules();

		if ( empty( $modules ) ) {
			return false;
		}

		$post = get_post( $post_id );

		// Prepare all the variables required.
		$start_date = date( 'Y-m-d H:00:00' );
		$end_date   = date( 'Y-m-d H:00:00' );
		$start_time = Tribe__Date_Utils::time_only( $start_date, false );
		$end_time   = Tribe__Date_Utils::time_only( $start_date, false );

		$show_global_stock = Tribe__Tickets__Tickets::global_stock_available();
		$tickets           = Tribe__Tickets__Tickets::get_event_tickets( $post->ID );
		$global_stock      = new Tribe__Tickets__Global_Stock( $post->ID );

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		return $admin_views->template( [ 'editor', 'metabox' ], get_defined_vars() );
	}

	/**
	 * Refreshes panels after ajax calls that change data
	 *
	 * @since  4.6.2
	 *
	 * @return string html content of the panels
	 */
	public function ajax_panels() {
		$post_id = absint( tribe_get_request_var( 'post_id', 0 ) );

		// Didn't get a post id to work with - bail
		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid Post ID', 'event-tickets' ) );
		}

		// Overwrites for a few templates that use get_the_ID() and get_post()
		global $post;

		$post = get_post( $post_id );
		$data = wp_parse_args( tribe_get_request_var( array( 'data' ), array() ), array() );
		$notice = tribe_get_request_var( 'tribe-notice', false );

		$data = Tribe__Utils__Array::get( $data, array( 'tribe-tickets' ), null );

		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		// Save if the info was passed
		if ( ! empty( $data ) ) {
			$tickets_handler->save_order( $post->ID, isset( $data['list'] ) ? $data['list'] : null );
			$tickets_handler->save_form_settings( $post->ID, isset( $data['settings'] ) ? $data['settings'] : null );
		}

		$return = $this->get_panels( $post );
		$return['notice'] = $this->notice( $notice );

		/**
		 * Allows filtering the data by other plugins/ecommerce solutions©
		 *
		 * @since 4.6
		 *
		 * @param array the return data
		 * @param int the post/event id
		 */
		$return = apply_filters( 'tribe_tickets_ajax_refresh_tables', $return, $post->ID );

		wp_send_json_success( $return );
	}

	/**
	 * Get the Panels for a given post.
	 *
	 * @since  4.6.2
	 *
	 * @param int|WP_Post $post
	 * @param int         $ticket_id
	 *
	 * @return array
	 */
	public function get_panels( $post, $ticket_id = null ) {
		if ( ! $post instanceof WP_Post ) {
			$post = get_post( $post );
		}

		// Bail on invalid post.
		if ( ! $post instanceof WP_Post ) {
			return [];
		}

		// Overwrites for a few templates that use get_the_ID() and get_post()
		$GLOBALS['post'] = $post;

		// Let's create tickets list markup to return
		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post->ID );

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		$panels = [
			'list'     => $admin_views->template( 'editor/panel/list', [ 'post_id' => $post->ID, 'tickets' => $tickets ], false ),
			'settings' => $admin_views->template( 'editor/panel/settings', [ 'post_id' => $post->ID ], false ),
			'ticket'   => $admin_views->template( 'editor/panel/ticket', [ 'post_id' => $post->ID, 'ticket_id' => $ticket_id ], false ),
		];

		return $panels;
	}

	/**
	 * Sanitizes the data for the new/edit ticket ajax call, and calls the child save_ticket function.
	 *
	 * @since 4.6.2
	 * @since 4.10.9 Use customizable ticket name functions.
	 */
	public function ajax_ticket_add() {
		$post_id = absint( tribe_get_request_var( 'post_id', 0 ) );

		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid parent Post', 'event-tickets' ) );
		}

		/**
		 * This is needed because a provider can implement a dynamic set of fields.
		 * Each provider is responsible for sanitizing these values.
		 */
		$data = wp_parse_args( tribe_get_request_var( array( 'data' ), array() ), array() );

		if ( ! $this->has_permission( $post_id, $_POST, 'add_ticket_nonce' ) ) {
			wp_send_json_error( esc_html( sprintf( __( 'Failed to add the %s. Refresh the page to try again.', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_add_error' ) ) ) );
		}

		if ( ! isset( $data['ticket_provider'] ) || ! $this->module_is_valid( $data['ticket_provider'] ) ) {
			wp_send_json_error( esc_html__( 'Commerce Provider invalid', 'event-tickets' ) );
		}

		// Get the Provider
		$module = call_user_func( [ $data['ticket_provider'], 'get_instance' ] );

		if ( ! $module instanceof Tribe__Tickets__Tickets ) {
			return new WP_Error(
				'bad_request',
				__( 'Commerce Module invalid', 'event-tickets' ),
				[ 'status' => 400 ]
			);
		}

		// Do the actual adding
		$ticket_id = $module->ticket_add( $post_id, $data );

		// Successful?
		if ( $ticket_id ) {
			/**
			 * Fire action when a ticket has been added
			 *
			 * @param int $post_id ID of parent "event" post
			 */
			do_action( 'tribe_tickets_ticket_added', $post_id );
		} else {
			wp_send_json_error( esc_html( sprintf( __( 'Failed to add the %s', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_add_error' ) ) ) );
		}

		$return = $this->get_panels( $post_id );
		$return['notice'] = $this->notice( 'ticket-add' );

		/**
		 * Filters the return data for ticket add
		 *
		 * @param array $return Array of data to return to the ajax call
		 * @param int $post_id ID of parent "event" post
		 */
		$return = apply_filters( 'event_tickets_ajax_ticket_add_data', $return, $post_id );

		wp_send_json_success( $return );
	}

	/**
	 * Returns the data from a single ticket to populate the edit form.
	 *
	 * @since   4.6.2
	 * @since   4.10.9 Use customizable ticket name functions.
	 * @since   4.12.3 Update detecting ticket provider to account for possibly inactive provider. Remove unused vars.
	 */
	public function ajax_ticket_edit() {
		$post_id = absint( tribe_get_request_var( 'post_id', 0 ) );

		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid parent Post', 'event-tickets' ) );
		}

		$ticket_id = absint( tribe_get_request_var( 'ticket_id', 0 ) );

		if ( ! $ticket_id ) {
			wp_send_json_error( esc_html( sprintf( __( 'Invalid %s', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_edit_error' ) ) ) );
		}

		if ( ! $this->has_permission( $post_id, $_POST, 'edit_ticket_nonce' ) ) {
			wp_send_json_error( esc_html( sprintf( __( 'Failed to edit the %s. Refresh the page to try again.', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_edit_error' ) ) ) );
		}

		$provider = tribe_tickets_get_ticket_provider( $ticket_id );

		if ( empty( $provider ) ) {
			wp_send_json_error( esc_html__( 'Commerce Module invalid', 'event-tickets' ) );
		}

		$return = $this->get_panels( $post_id, $ticket_id );

		/**
		 * Provides an opportunity for final adjustments to the data used to populate
		 * the edit-ticket form.
		 *
		 * @param array $return     Data for the JSON response
		 * @param int   $post_id    Post ID
		 * @param int   $ticket_id  Ticket ID
		 */
		$return = (array) apply_filters( 'tribe_events_tickets_ajax_ticket_edit', $return, $post_id, $ticket_id );

		wp_send_json_success( $return );
	}

	/**
	 * Sanitizes the data for the delete ticket ajax call, and calls the child delete_ticket
	 * function.
	 *
	 * @since  4.6.2
	 */
	public function ajax_ticket_delete() {
		$post_id = absint( tribe_get_request_var( 'post_id', 0 ) );

		if ( ! $post_id ) {
			wp_send_json_error( esc_html__( 'Invalid parent Post', 'event-tickets' ) );
		}

		$ticket_id = absint( tribe_get_request_var( 'ticket_id', 0 ) );

		if ( ! $ticket_id ) {
			wp_send_json_error( esc_html( sprintf( __( 'Invalid %s', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_delete_error' ) ) ) );
		}

		if ( ! $this->has_permission( $post_id, $_POST, 'remove_ticket_nonce' ) ) {
			wp_send_json_error( esc_html( sprintf( __( 'Failed to delete the %s. Refresh the page to try again.', 'event-tickets' ), tribe_get_ticket_label_singular( 'ajax_ticket_delete_error' ) ) ) );
		}

		$provider = tribe_tickets_get_ticket_provider( $ticket_id );

		if ( empty( $provider ) ) {
			wp_send_json_error( esc_html__( 'Commerce Module invalid', 'event-tickets' ) );
		}

		// Pass the control to the child object
		$return = $provider->delete_ticket( $post_id, $ticket_id );

		// Successfully deleted?
		if ( $return ) {
			$return           = $this->get_panels( $post_id );
			$return['notice'] = $this->notice( 'ticket-delete' );

			/**
			 * Fire action when a ticket has been deleted
			 *
			 * @param int $post_id ID of parent "event" post
			 */
			do_action( 'tribe_tickets_ticket_deleted', $post_id );
		}

		wp_send_json_success( $return );
	}

	/**
	 * Handles the check-in ajax call, and calls the checkin method.
	 *
	 * @since  4.6.2
	 * @since  4.12.3 Use new helper method to account for possibly inactive ticket provider.
	 */
	public function ajax_attendee_checkin() {
		$event_id    = Tribe__Utils__Array::get( $_POST, 'event_ID', false );
		$attendee_id = Tribe__Utils__Array::get( $_POST, 'attendee_id', false );

		if ( empty( $attendee_id ) ) {
			wp_send_json_error( __( 'The attendee ID is missing from the request parameters.', 'event-tickets' ) );
		}

		$provider = Tribe__Utils__Array::get( $_POST, 'provider', false );

		$provider = Tribe__Tickets__Tickets::get_ticket_provider_instance( $provider );

		if ( empty( $provider ) ) {
			wp_send_json_error( esc_html__( 'Commerce Module invalid', 'event-tickets' ) );
		}

		if (
			empty( $_POST['nonce'] )
			|| ! wp_verify_nonce( $_POST['nonce'], 'checkin' )
			|| ! $this->user_can( 'edit_posts', $attendee_id )
		) {
			wp_send_json_error( "Cheatin' huh?" );
		}

		// Pass the control to the child object
		$did_checkin = $provider->checkin( $attendee_id );

		$provider->clear_attendees_cache( $event_id );

		wp_send_json_success( $did_checkin );
	}

	/**
	 * Handles the check-in ajax call, and calls the uncheckin method.
	 *
	 * @since  4.6.2
	 * @since  4.12.3 Use new helper method to account for possibly inactive ticket provider.
	 */
	public function ajax_attendee_uncheckin() {
		$event_id    = Tribe__Utils__Array::get( $_POST, 'event_ID', false );
		$attendee_id = Tribe__Utils__Array::get( $_POST, 'attendee_id', false );

		if ( empty( $attendee_id ) ) {
			wp_send_json_error( __( 'The attendee ID is missing from the request parameters.', 'event-tickets' ) );
		}

		$provider = Tribe__Utils__Array::get( $_POST, 'provider', false );

		$provider = Tribe__Tickets__Tickets::get_ticket_provider_instance( $provider );

		if ( empty( $provider ) ) {
			wp_send_json_error( esc_html__( 'Commerce Module invalid', 'event-tickets' ) );
		}

		if (
			empty( $_POST['nonce'] )
			|| ! wp_verify_nonce( $_POST['nonce'], 'uncheckin' )
			|| ! $this->user_can( 'edit_posts', $attendee_id )
		) {
			wp_send_json_error( "Cheatin' huh?" );
		}

		// Pass the control to the child object
		$did_uncheckin = $provider->uncheckin( $attendee_id );

		$provider->clear_attendees_cache( $event_id );

		wp_send_json_success( $did_uncheckin );
	}

	/**
	 * Get the controls (move, delete) as a string.
	 *
	 * @since  4.6.2
	 *
	 * @param int     $post_id
	 * @param int     $ticket_id
	 * @param boolean $echo
	 *
	 * @return string
	 */
	public function get_ticket_controls( $post_id, $ticket_id = 0, $echo = true ) {
		$provider = tribe_tickets_get_ticket_provider( $ticket_id );

		if ( empty( $provider ) ) {
			return '';
		}

		if ( empty( $ticket_id ) ) {
			return '';
		}

		$ticket = $provider->get_ticket( $post_id, $ticket_id );

		if ( empty( $ticket ) ) {
			return '';
		}

		$controls = [];

		if ( tribe_is_truthy( tribe_get_request_var( 'is_admin', true ) ) ) {
			$controls[] = $provider->get_ticket_move_link( $post_id, $ticket );
		}
		$controls[] = $provider->get_ticket_delete_link( $ticket );

		$html = join( ' | ', $controls );

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * test if the nonce is correct and the current user has the correct permissions
	 *
	 * @since  4.6.2
	 *
	 * @param WP_Post|int $post
	 * @param array       $data
	 * @param string      $nonce_action
	 *
	 * @return boolean
	 */
	public function has_permission( $post, $data, $nonce_action ) {

		if ( ! $post instanceof WP_Post ) {
			if ( ! is_numeric( $post ) ) {
				return false;
			}

			$post = get_post( $post );
		}

		if ( empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], $nonce_action ) ) {
			return false;
		}

		return current_user_can( 'edit_event_tickets' ) || current_user_can( get_post_type_object( $post->post_type )->cap->edit_posts );
	}

	/**
	 * Tests if the user has the specified capability in relation to whatever post type
	 * the attendee object relates to.
	 *
	 * For example, if the attendee was generated for a ticket set up in relation to a
	 * post of the `banana` type, the generic capability "edit_posts" will be mapped to
	 * "edit_bananas" or whatever is appropriate.
	 *
	 * @internal for internal plugin use only (in spite of having public visibility)
	 *
	 * @since  4.6.2
	 *
	 * @see    tribe( 'tickets.attendees' )->user_can
	 *
	 * @param  string $generic_cap
	 * @param  int    $attendee_id
	 *
	 * @return boolean
	 */
	public function user_can( $generic_cap, $attendee_id ) {
		/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		$connections = $tickets_handler->get_object_connections( $attendee_id );

		if ( ! $connections->event ) {
			return false;
		}

		/** @var Tribe__Tickets__Attendees $tickets_attendees */
		$tickets_attendees = tribe( 'tickets.attendees' );

		return $tickets_attendees->user_can( $generic_cap, $connections->event );
	}

	/**
	 * Returns whether a class name is a valid active module/provider.
	 *
	 * @since  4.6.2
	 *
	 * @param  string  $module  class name of module
	 *
	 * @return bool
	 */
	public function module_is_valid( $module ) {
		return array_key_exists( $module, Tribe__Tickets__Tickets::modules() );
	}

	/**
	 * Returns the markup for a notice in the admin
	 *
	 * @since  4.6.2
	 *
	 * @param  string $msg Text for the notice
	 *
	 * @return string Notice with markup
	 */
	protected function notice( $msg ) {
		return sprintf( '<div class="wrap"><div class="updated"><p>%s</p></div></div>', $msg );
	}

	/**
	 * Decimal Character Asset Localization (used on Community Tickets)
	 *
	 * @todo   We need to deprecate this
	 *
	 * @return void
	 */
	public static function localize_decimal_character() {
		$locale  = localeconv();
		$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

		/**
		 * Filter the decimal point character used in the price
		 */
		$decimal = apply_filters( 'tribe_event_ticket_decimal_point', $decimal );

		wp_localize_script( 'event-tickets-js', 'price_format', array(
			'decimal' => $decimal,
			'decimal_error' => __( 'Please enter in without thousand separators and currency symbols.', 'event-tickets' ),
		) );
	}


	/************************
	 *                      *
	 *  Deprecated Methods  *
	 *                      *
	 ************************/
	// @codingStandardsIgnoreStart

	/**
	 * Refreshes panel settings after canceling saving
	 *
	 * @deprecated 4.6.2
	 * @since 4.6
	 *
	 * @return string html content of the panel settings
	 */
	public function ajax_refresh_settings() {

	}

	/**
	 * @deprecated 4.6.2
	 *
	 * @return void
	 */
	public function ajax_handler_save_settings() {

	}

	/**
	 * Registers the tickets metabox if there's at least
	 * one Tribe Tickets module (provider) enabled
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $post_type
	 */
	public static function maybe_add_meta_box( $post_type ) {
		tribe( 'tickets.metabox' )->configure( $post_type );
	}

	/**
	 * Loads the content of the tickets metabox if there's at
	 * least one Tribe Tickets module (provider) enabled
	 *
	 * @deprecated 4.6.2
	 *
	 * @param $post_id
	 */
	public static function do_modules_metaboxes( $post_id ) {
		tribe( 'tickets.metabox' )->render( $post_id );
	}

	/**
	 * Enqueue the tickets metabox JS and CSS
	 *
	 * @deprecated 4.6
	 *
	 * @param $unused_hook
	 */
	public static function add_admin_scripts( $unused_hook ) {
		_deprecated_function( __METHOD__, '4.6', 'Tribe__Tickets__Assets::admin_enqueue_scripts' );
	}

	// @codingStandardsIgnoreEnd
}
