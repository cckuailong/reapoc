<?php
// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Tribe__Tickets__Tickets_View {

	/**
	 * Get (and instantiate, if necessary) the instance of the class.
	 *
	 * @static
	 * @return self
	 */
	public static function instance() {
		return tribe( 'tickets.tickets-view' );
	}

	/**
	 * Hook the necessary filters and Actions!
	 *
	 * @static
	 * @return self
	 */
	public static function hook() {
		$myself = self::instance();

		add_action( 'template_redirect', [ $myself, 'authorization_redirect' ] );
		add_action( 'template_redirect', [ $myself, 'update_tickets' ] );

		// Generate Non TEC Permalink.
		add_action( 'generate_rewrite_rules', [ $myself, 'add_non_event_permalinks' ] );
		add_filter( 'query_vars', [ $myself, 'add_query_vars' ] );
		add_action( 'parse_request', [ $myself, 'prevent_page_redirect' ] );
		add_filter( 'the_content', [ $myself, 'intercept_content' ] );
		add_action( 'parse_request', [ $myself, 'maybe_regenerate_rewrite_rules' ] );
		add_filter( 'tribe_events_views_v2_bootstrap_should_display_single', [ $myself, 'intercept_views_v2_single_display' ], 15, 4 );

		// Only Applies this to TEC users.
		if ( class_exists( 'Tribe__Events__Rewrite' ) ) {
			add_action( 'tribe_events_pre_rewrite', [ $myself, 'add_permalink' ] );
			add_filter( 'tribe_events_rewrite_base_slugs', [ $myself, 'add_rewrite_base_slug' ] );
		}

		// Intercept Template file for Tickets.
		add_action( 'pre_get_posts', [ $myself, 'modify_ticket_display_query' ] );
		add_filter( 'tribe_events_template_single-event.php', [ $myself, 'intercept_template' ], 20 );

		// We will inject on the Priority 4, to be happen before RSVP.
		add_action( 'tribe_events_single_event_after_the_meta', [ $myself, 'inject_link_template' ], 4 );
		add_filter( 'the_content', [ $myself, 'inject_link_template_the_content' ], 9 );

		return $myself;
	}

	/**
	 * By default WordPress has a nasty if query_var['p'] is a page then redirect to the page,
	 * so we will change the variables accordingly.
	 *
	 * @param  WP_Query $query The current Query.
	 * @return void
	 */
	public function prevent_page_redirect( $query ) {
		$is_correct_page = isset( $query->query_vars['tribe-edit-orders'] ) && $query->query_vars['tribe-edit-orders'];

		if ( ! $is_correct_page ) {
			return;
		}

		// This has no Performance problems, since get_post uses caching and we use this method later on.
		$post = isset( $query->query_vars['p'] ) ? get_post( absint( $query->query_vars['p'] ) ) : 0;
		if ( ! $post ) {
			return;
		}

		if ( ! tribe_tickets_post_type_enabled( $post->post_type ) ) {
			return;
		}

		$query->query_vars['post_type'] = $post->post_type;

		if ( 'page' === $post->post_type ) {
			// Set `page_id` for faster query.
			$query->query_vars['page_id'] = $post->ID;
		}

	}

	/**
	 * Tries to Flush the Rewrite rules.
	 *
	 * @return void
	 */
	public function maybe_regenerate_rewrite_rules() {
		// if they don't have any rewrite rules, do nothing
		// Don't try to run stuff for non-logged users (too time consuming)
		if ( ! is_array( $GLOBALS['wp_rewrite']->rules ) || ! is_user_logged_in() ) {
			return;
		}

		$rules = $this->rewrite_rules_array();

		$diff = array_diff( $rules, $GLOBALS['wp_rewrite']->rules );
		$key_diff = array_diff_assoc( $rules, $GLOBALS['wp_rewrite']->rules );

		if ( empty( $diff ) && empty( $key_diff ) ) {
			return;
		}

		flush_rewrite_rules();
	}

	/**
	 * Gets the List of Rewrite rules we are using here.
	 *
	 * @return array
	 */
	public function rewrite_rules_array() {
		$bases = $this->add_rewrite_base_slug();

		$rules = [
			sanitize_title_with_dashes( $bases['tickets'][0] ) . '/([0-9]{1,})/?' => 'index.php?p=$matches[1]&tribe-edit-orders=1',
		];

		return $rules;
	}

	/**
	 * For non events the links will be a little bit weird, but it's the safest way.
	 *
	 * @param WP_Rewrite $wp_rewrite
	 */
	public function add_non_event_permalinks( WP_Rewrite $wp_rewrite  ) {
		$wp_rewrite->rules = $this->rewrite_rules_array() + $wp_rewrite->rules;
	}

	/**
	 * Register a new public (URL query parameters can use it) Query Var to allow tickets editing.
	 *
	 * @see \WP::parse_request()
	 *
	 * @param array $vars
	 *
	 * @return array
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'tribe-edit-orders';
		return $vars;
	}


	/**
	 * Sort Attendee by Order Status to Process Not Going First.
	 *
	 * @since 4.7.1
	 *
	 * @param $a array An array of ticket id and status.
	 * @param $b array An array of ticket id and status.
	 *
	 * @return int
	 */
	public function sort_attendees( $a, $b ) {
		return strcmp( $a['order_status'], $b['order_status'] );
	}

	/**
	 * Update the RSVP and Tickets values for each Attendee.
	 */
	public function update_tickets() {
		$is_correct_page = $this->is_edit_page();

		// Now fetch the display and check it
		if (
			'tickets' !== get_query_var( 'eventDisplay', false )
			&& ! $is_correct_page
		) {
			return;
		}

		if (
			empty( $_POST['process-tickets'] )
			|| (
				empty( $_POST['tribe_tickets']['attendees'] )
				&& empty( $_POST['attendee'] )
				&& empty( $_POST['tribe-tickets-meta'] )
			)
		) {
			return;
		}

		$post_id = get_the_ID();

		$attendees = [];

		if ( isset( $_POST['tribe_tickets']['attendees'] ) ) {
			$attendees = $_POST['tribe_tickets']['attendees'];
		} elseif ( isset( $_POST['attendee'] ) ) {
			$attendees = $_POST['attendee'];
		}

		// Sort list to handle all not attending first.
		$attendees = wp_list_sort( $attendees, 'order_status', 'ASC', true );

		foreach ( $attendees as $attendee_id => $attendee_data ) {
			/**
			 * Allow Commerce providers to process updates for each attendee from the My Tickets page.
			 *
			 * @param array $attendee_data Information that we are trying to save.
			 * @param int   $attendee_id   The attendee ID.
			 * @param int   $post_id       The event/post ID.
			 */
			do_action( 'event_tickets_attendee_update', $attendee_data, (int) $attendee_id, $post_id );
		}

		/**
		 * Allow functionality to be hooked into after all of the attendees have been updated from the My Tickets page.
		 *
		 * @since 5.1.0 Added the $attendees value to the action for further integration.
		 *
		 * @param int   $post_id   The event/post ID.
		 * @param array $attendees List of attendees and their data that was saved.
		 */
		do_action( 'event_tickets_after_attendees_update', $post_id, $attendees );

		// After editing the values, we update the transient.
		Tribe__Post_Transient::instance()->delete( $post_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );

		// If it's not events CPT
		$url = $this->get_tickets_page_url( $post_id, ! $is_correct_page );
		$url = add_query_arg( 'tribe_updated', 1, $url );
		wp_safe_redirect( esc_url_raw( $url ) );
		exit;
	}

	/**
	 * Helper function to generate the Link to the tickets page of an event.
	 *
	 * @since 4.7.1
	 *
	 * @param $event_id
	 * @param $is_event_page
	 *
	 * @return string|void
	 */
	public function get_tickets_page_url( $event_id, $is_event_page ) {
		$has_plain_permalink = '' === get_option( 'permalink_structure' );
		$event_url = get_permalink( $event_id );

		// Is on the Event post type
		if ( $is_event_page ) {
			$link = $has_plain_permalink
				? add_query_arg( 'eventDisplay', 'tickets', untrailingslashit( $event_url ) )
				: trailingslashit( $event_url ) . 'tickets';
		} else {
			$link = $has_plain_permalink
				? add_query_arg( 'tribe-edit-orders', 1, untrailingslashit( $event_url ) )
				: home_url( '/tickets/' . $event_id );
		}

		return $link;
	}

	/**
	 * Makes sure only logged users can See the Tickets page.
	 *
	 * @return void
	 */
	public function authorization_redirect() {
		/**
		 * @todo Remove this after we implement the Rewrites in Common
		 */
		$is_event_query = ! empty( $GLOBALS['wp_query']->tribe_is_event_query );

		// When it's not Events Query and we have TEC active we dont care
		if ( class_exists( 'Tribe__Events__Main' ) && ! $is_event_query ) {
			return;
		}

		// If we got here and it's a 404 + single
		if ( is_single() && is_404() ) {
			return;
		}

		// Now fetch the display and check it
		if ( 'tickets' !== get_query_var( 'eventDisplay', false ) && ! $this->is_edit_page() ) {
			return;
		}

		// Only goes to the Redirect if user is not logged in
		if ( is_user_logged_in() ) {
			return;
		}

		// Loop back to the Event, this page is only for Logged users
		wp_redirect( get_permalink() );
		exit;
	}

	/**
	 * To allow `tickets` to be translatable we need to add it as a base.
	 *
	 * @param  array $bases The translatable bases.
	 * @return array
	 */
	public function add_rewrite_base_slug( $bases = [] ) {
		/**
		 * Allows users to filter and change the base for the order page
		 *
		 * @param string $slug
		 * @param array  $bases
		 */
		$bases['tickets'] = (array) apply_filters( 'event_tickets_rewrite_slug_orders_page', 'tickets', $bases );

		return $bases;
	}


	/**
	 * Checks if this is the ticket page based on the current query var.
	 *
	 * This only works after parse_query has run.
	 *
	 * @return bool
	 */
	public function is_edit_page() {
		return false !== get_query_var( 'tribe-edit-orders', false );
	}

	/**
	 * Adds the Permalink for the tickets end point.
	 *
	 * @param Tribe__Events__Rewrite $rewrite
	 */
	public function add_permalink( Tribe__Events__Rewrite $rewrite ) {

		// Adds the 'tickets' endpoint for single event pages.
		$rewrite->single(
			[ '{{ tickets }}' ],
			[
				Tribe__Events__Main::POSTTYPE => '%1',
				'post_type' => Tribe__Events__Main::POSTTYPE,
				'eventDisplay' => 'tickets',
			]
		);

		// Adds the `tickets` endpoint for recurring events
		$rewrite->single(
			[ '(\d{4}-\d{2}-\d{2})', '{{ tickets }}' ],
			[
				Tribe__Events__Main::POSTTYPE => '%1',
				'eventDate' => '%2',
				'post_type' => Tribe__Events__Main::POSTTYPE,
				'eventDisplay' => 'tickets',
			]
		);

	}

	/**
	 * Filter to make sure Tickets eventDisplay properly displays the Tickets page.
	 *
	 * @since 4.11.2
	 *
	 * @param bool   $should_display_single If we should display single or not.
	 * @param string $view_slug             Which view slug we are working with.
	 *
	 * @return bool
	 */
	public function intercept_views_v2_single_display( $should_display_single, $view_slug ) {
		if ( 'tickets' === $view_slug ) {
			return true;
		}

		return $should_display_single;
	}

	/**
	 * Intercepts the_content from the posts to include the orders structure.
	 *
	 * @since 4.11.2 Avoid running when it shouldn't by bailing if not in main query loop on a single post.
	 *
	 * @param string $content Normally the_content of a post.
	 *
	 * @return string
	 */
	public function intercept_content( $content = '' ) {
		// Now fetch the display and check it
		$display = get_query_var( 'eventDisplay', false );

		// Prevents firing more than it needs to outside of the loop.
		if (
			! is_single()
			|| ! in_the_loop()
			|| ! is_main_query()
			|| (
				'tickets' !== $display
				&& ! $this->is_edit_page()
			)
		) {
			return $content;
		}

		tribe_asset_enqueue_group( 'tribe-tickets-page-assets' );

		ob_start();

		include Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/orders.php' );

		$content = ob_get_clean();

		return $content;
	}

	/**
	 * Modify the front end ticket list display for it to always display
	 * even when Hide From Event Listings is checked for an event.
	 *
	 * @since 4.7.3
	 *
	 * @param $query WP_Query Query object.
	 *
	 */
	public function modify_ticket_display_query( $query ) {
		if ( empty( $query->tribe_is_event_query ) ) {
			return;
		}

		if ( 'tickets' !== get_query_var( 'eventDisplay', false ) ) {
			return;
		}

		$query->set( 'post__not_in', '' );

		// Do not attempt to filter the query for this custom view.
		$query->set( 'tribe_suppress_query_filters', true );
	}

	/**
	 * We need to intercept the template loading and load the correct file.
	 *
	 * @param string $old_file Non important variable with the previous path.
	 *
	 * @return string          The correct File path for the tickets endpoint.
	 */
	public function intercept_template( $old_file ) {
		global $wp_query;

		/**
		 * @todo Remove this after we implement the Rewrites in Common
		 */
		$is_event_query = ! empty( $wp_query->tribe_is_event_query );

		// When it's not our query we don't care
		if ( ! $is_event_query ) {
			return $old_file;
		}

		// If we got here and it's a 404 + single
		if ( is_single() && is_404() ) {
			return $old_file;
		}

		// Now fetch the display and check it
		$display = get_query_var( 'eventDisplay', false );

		if ( 'tickets' !== $display && ! $this->is_edit_page() ) {
			return $old_file;
		}

		tribe_asset_enqueue_group( 'tribe-tickets-page-assets' );

		// Fetch the correct file using the Tickets Hierarchy
		$file = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/orders.php' );

		return $file;
	}

	/**
	 * Injects the Link to The front-end Tickets page normally
	 * at `tribe_events_single_event_after_the_meta`.
	 *
	 * @return void
	 */
	public function inject_link_template() {
		/**
		 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
		 *
		 * @since 4.5.6
		 *
		 * @param boolean $already_rendered Whether the order link template has already been rendered.
		 */
		$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

		if ( $already_rendered ) {
			return;
		}

		$event_id = get_the_ID();
		$user_id  = get_current_user_id();

		if ( ! $this->has_rsvp_attendees( $event_id, $user_id ) && ! $this->has_ticket_attendees( $event_id, $user_id ) ) {
			return;
		}

		if ( $this->is_edit_page() ) {
			return;
		}

		if ( ! tribe_tickets_post_type_enabled( get_post_type() ) ) {
			return;
		}

		$file = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/orders-link.php' );

		/**
		 * @since 4.10.8 Attempt to load from old location to account for pre-existing theme overrides. If not found,
		 *            go through the motions with the new location.
		 */
		if ( empty( $file ) ) {
			$file = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/view-link.php' );
		}

		include $file;

		add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
	}

	/**
	 * Injects the Link to The front-end Tickets page to non Events.
	 *
	 * @param string $content  The content form the post.
	 * @return string $content
	 */
	public function inject_link_template_the_content( $content ) {
		// Prevents firing more then it needs too outside of the loop
		$in_the_loop = isset( $GLOBALS['wp_query']->in_the_loop ) && $GLOBALS['wp_query']->in_the_loop;

		$post_id = get_the_ID();
		$user_id = get_current_user_id();

		// if the current post type doesn't have tickets enabled for it, bail
		if ( ! tribe_tickets_post_type_enabled( get_post_type( $post_id ) ) ) {
			return $content;
		}

		/**
		 * @todo Remove this after we implement the Rewrites in Common
		 */
		$is_event_query = ! empty( $GLOBALS['wp_query']->tribe_is_event_query );

		// When it's not our query we don't care
		if ( ( class_exists( 'Tribe__Events__Main' ) && $is_event_query ) || ! $in_the_loop ) {
			return $content;
		}

		// If we have this we are already on the tickets page
		if ( $this->is_edit_page() ) {
			return $content;
		}

		if ( ! $this->has_rsvp_attendees( $post_id, $user_id ) && ! $this->has_ticket_attendees( $post_id, $user_id ) ) {
			return $content;
		}

		ob_start();

		$file = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/orders-link.php' );

		/**
		 * @since 4.10.8 Attempt to load from old location to account for pre-existing theme overrides. If not found,
		 *            go through the motions with the new location.
		 */
		if ( empty( $file ) ) {
			$file = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/view-link.php' );
		}

		include $file;

		$content .= ob_get_clean();

		add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );

		return $content;
	}

	/**
	 * Fetches from the Cached attendees list the ones that are relevant for this user and event.
	 * Important to note that this method will return the attendees organized by order id.
	 *
	 * @param  int       $event_id      The Event ID we're checking.
	 * @param  int|null  $user_id       An Optional User ID.
	 * @param  boolean   $include_rsvp  If this should include RSVP, default is false.
	 * @return array                    List of Attendees grouped by order id.
	 */
	public function get_event_attendees_by_order( $event_id, $user_id = null, $include_rsvp = false ) {
		if ( ! $user_id ) {
			$attendees = Tribe__Tickets__Tickets::get_event_attendees( $event_id );
		} else {
			// If we have a user_id then limit by that.
			$args = [
				'by' => [
					'user' => $user_id,
				],
			];

			$attendee_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $event_id, $args );

			$attendees = $attendee_data['attendees'];
		}

		$orders = [];

		foreach ( $attendees as $key => $attendee ) {
			// Ignore RSVP if we don't tell it specifically
			if ( 'rsvp' === $attendee['provider_slug'] && ! $include_rsvp ) {
				continue;
			}

			$orders[ (int) $attendee['order_id'] ][] = $attendee;
		}

		return $orders;
	}

	/**
	 * Fetches from the Cached attendees list the ones that are relevant for this user and event.
	 * Important to note that this method will return the attendees from RSVP.
	 *
	 * @param  int       $event_id     The Event ID we're checking.
	 * @param  int|null  $user_id      An Optional User ID.
	 * @return array                   Array with the RSVP attendees.
	 */
	public function get_event_rsvp_attendees( $event_id, $user_id = null ) {
		$attendees = [];

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		if ( null === $user_id ) {
			return $rsvp->get_attendees_by_id( $event_id );
		}

		return $rsvp->get_attendees_by_user_id( $user_id, $event_id );
	}

	/**
	 * Groups RSVP attendees by purchaser name/email.
	 *
	 * @param int $event_id The Event ID we're checking.
	 * @param int|null $user_id An optional user ID.
	 * @return array Array with the RSVP attendees grouped by purchaser name/email.
	 */
	public function get_event_rsvp_attendees_by_purchaser( $event_id, $user_id = null ) {
		$attendees = $this->get_event_rsvp_attendees( $event_id, $user_id );

		if ( ! $attendees ) {
			return [];
		}

		$attendee_groups = [];
		foreach ( $attendees as $attendee ) {
			$key = $attendee['purchaser_name'] . '::' . $attendee['purchaser_email'];

			if ( ! isset( $attendee_groups[ $key ] ) ) {
				$attendee_groups[ $key ] = [];
			}

			$attendee_groups[ $key ][] = $attendee;
		}

		return $attendee_groups;
	}

	/**
	 * Gets a List of Possible RSVP answers.
	 *
	 * @param string $selected    Allows users to check if an option exists or get it's label.
	 * @param bool   $just_labels Whether just the options labels should be returned.
	 *
	 * @return array|bool An array containing the RSVP states, an array containing the selected
	 *                    option data or `false` if the selected option does not exist.
	 */
	public function get_rsvp_options( $selected = null, $just_labels = true ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$options = $status_mgr->get_status_options( 'rsvp' );

		/**
		 * Allow users to add more RSVP options.
		 *
		 * Additional RSVP options should be specified in the following formats:
		 *
		 *      [
		 *          'slug' => 'Option 1 label',
		 *          'slug' => [ 'label' => 'Option 3 label' ],
		 *          'slug' => [ 'label' => 'Option 2 label', 'decrease_stock_by' => 1 ],
		 *      ]
		 *
		 * The `decrease_stock_by` key can be omitted and will default to `1`.
		 *
		 * @param array $options
		 * @param string $selected
		 */
		$options = apply_filters( 'event_tickets_rsvp_options', $options, $selected );

		$options = array_filter( $options, [ $this, 'has_rsvp_format' ] );
		array_walk( $options, [ $this, 'normalize_rsvp_option' ] );

		// If an option was passed return it's label, but if doesn't exist return false
		if ( null !== $selected ) {
			return isset( $options[ $selected  ] ) ?
                $options[ $selected  ]['label'] : false;
		}

		return $just_labels ?
			array_combine( array_keys( $options ), wp_list_pluck( $options, 'label' ) )
			: $options;
	}

	/**
	 * Check if the RSVP option is a valid one.
	 *
	 * @param  string  $option Which rsvp option to check.
	 * @return boolean
	 */
	public function is_valid_rsvp_option( $option ) {
		return in_array( $option, array_keys( $this->get_rsvp_options() ) );
	}

	/**
	 * Counts the amount of RSVP attendees.
	 *
	 * @param int      $event_id The Event ID we're checking.
	 * @param int|null $user_id  An Optional User ID.
	 *
	 * @return int
	 */
	public function count_rsvp_attendees( $event_id, $user_id = null ) {
		if ( ! $user_id && null !== $user_id ) {
			// No attendees for this user.
			return 0;
		}

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		// Get total attendees count for all users.
		if ( ! $user_id ) {
			return $rsvp->get_attendees_count( $event_id );
		}

		// Get total attendees count for this user.
		return $rsvp->get_attendees_count_by_user( $event_id, $user_id );
	}

	/**
	 * Counts the Amount of Tickets attendees.
	 *
	 * @param  int       $event_id     The Event ID we're checking.
	 * @param  int|null  $user_id      An Optional User ID.
	 * @return int
	 */
	public function count_ticket_attendees( $event_id, $user_id = null ) {
		if ( ! $user_id && null !== $user_id ) {
			// No attendees for this user.
			return 0;
		}

		$args = [
			'by' => [
				'provider__not_in' => 'rsvp',
				'status'           => 'publish',
			],
		];

		// Get total attendees count for this user.
		if ( $user_id ) {
			$args['by']['user'] = $user_id;
		}

		return Tribe__Tickets__Tickets::get_event_attendees_count( $event_id, $args );
	}

	/**
	 * Verifies if we have RSVP attendees for this user and event.
	 *
	 * @param  int       $event_id     The Event ID we're checking.
	 * @param  int|null  $user_id      An Optional User ID.
	 * @return int
	 */
	public function has_rsvp_attendees( $event_id, $user_id = null ) {
		$rsvp_orders = $this->count_rsvp_attendees( $event_id, $user_id );
		return ! empty( $rsvp_orders );
	}

	/**
	 * Verifies if we have Tickets attendees for this user and event
	 *
	 * @param  int       $event_id     The Event ID we're checking.
	 * @param  int|null  $user_id      An Optional User ID.
	 * @return int
	 */
	public function has_ticket_attendees( $event_id, $user_id = null ) {
		$ticket_orders = $this->count_ticket_attendees( $event_id, $user_id );
		return ! empty( $ticket_orders );
	}

	/**
	 * Gets the name(s) of the type(s) of ticket(s) the specified user (optional) has for the specified event.
	 *
	 * @since 4.2
	 * @since 4.10.8 Deprecated the 3rd parameter (whether or not to use 'plurals') in favor of figuring it out per type.
	 *
	 * @param int      $event_id   The Event ID we're checking.
	 * @param int|null $user_id    An optional User ID.
	 * @param null     $deprecated Deprecated argument.
	 *
	 * @return string
	 */
	public function get_description_rsvp_ticket( $event_id, $user_id = null, $deprecated = null ) {
		$descriptions = [];

		$rsvp_count = $this->count_rsvp_attendees( $event_id, $user_id );

		$ticket_count = $this->count_ticket_attendees( $event_id, $user_id );

		if ( 1 === $rsvp_count ) {
			$descriptions[] = tribe_get_rsvp_label_singular( 'tickets_view_description' );
		} elseif ( 1 < $rsvp_count ) {
			$descriptions[] = tribe_get_rsvp_label_plural( 'tickets_view_description' );
		}

		if ( 1 === $ticket_count ) {
			$descriptions[] = tribe_get_ticket_label_singular( 'tickets_view_description' );
		} elseif ( 1 < $ticket_count ) {
			$descriptions[] = tribe_get_ticket_label_plural( 'tickets_view_description' );
		}

		// Just return false if array is empty
		if ( empty( $descriptions ) ) {
			return '';
		}

		return esc_html( implode( _x( ' and ', 'separator if there are both RSVPs and Tickets', 'event-tickets' ), $descriptions ) );
	}

	/**
	 * Creates the HTML for the Select Element for RSVP options.
	 *
	 * @param  string $name     The Name of the Field.
	 * @param  string $selected The Current selected option.
	 * @param  int  $event_id   The Event/Post ID (optional).
	 * @param  int  $ticket_id  The Ticket/RSVP ID (optional).
	 * @return void
	 */
	public function render_rsvp_selector( $name, $selected, $event_id = null, $ticket_id = null ) {
		$options = $this->get_rsvp_options();

		?>
		<select <?php echo $this->get_restriction_attr( $event_id, $ticket_id ); ?> name="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $options as $value => $label ): ?>
			<option <?php selected( $selected, $value ); ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Verifies if the Given Event has RSVP restricted.
	 *
	 * @param  int  $event_id   The Event/Post ID (optional).
	 * @param  int  $ticket_id  The Ticket/RSVP ID (optional).
	 * @param  int  $user_id    A User ID (optional).
	 * @return boolean
	 */
	public function is_rsvp_restricted( $event_id = null, $ticket_id = null, $user_id = null ) {
		// By default we always pass the current User
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		/**
		 * Allow users to filter if this Event or Ticket has Restricted RSVP
		 *
		 * @param  boolean  $restricted Is this Event or Ticket Restricted?
		 * @param  int      $event_id   The Event/Post ID (optional)
		 * @param  int      $ticket_id  The Ticket/RSVP ID (optional)
		 * @param  int      $user_id    An User ID (optional)
		 */
		return apply_filters( 'event_tickets_is_rsvp_restricted', false, $event_id, $ticket_id, $user_id );
	}

	/**
	 * Gets a HTML Attribute for input/select/textarea to be disabled.
	 *
	 * @param  int  $event_id   The Event/Post ID (optional).
	 * @param  int  $ticket_id  The Ticket/RSVP ID (optional).
	 * @return boolean
	 */
	public function get_restriction_attr( $event_id = null, $ticket_id = null ) {
		$is_disabled = '';

		if ( $this->is_rsvp_restricted( $event_id, $ticket_id ) ) {
			$is_disabled = 'disabled title="' . esc_attr( sprintf( __( 'This %s is no longer active.', 'event-tickets' ), tribe_get_rsvp_label_singular( 'rsvp_restricted_title_text' ) ) ) . '"';
		}

		return $is_disabled;
	}

	/**
	 * Creates the HTML for the status of the  RSVP choice.
	 *
	 * @param  string $name     The Name of the Field.
	 * @param  string $selected The Current selected option.
	 * @param  int  $event_id   The Event/Post ID (optional).
	 * @param  int  $ticket_id  The Ticket/RSVP ID (optional).
	 * @return void
	 */
	public function render_rsvp_status( $name, $selected, $event_id = null, $ticket_id = null ) {
		$options = $this->get_rsvp_options();
		echo sprintf( '<span>%s</span>', esc_html( $options[ $selected ] ) );
	}

	/**
	 * Prunes RSVP options that are arrays and are not defining a label.
	 *
	 * @param array|string $option
	 *
	 * @return bool
	 */
	protected function has_rsvp_format( $option ) {
		if ( ! is_array( $option ) ) {
			return true;
		}

		// label is the bare minimum
		if ( ! isset( $option['label'] ) ) {
			return false;
		}

		return empty( $option['decrease_stock_by'] )
		       || (
					is_numeric( $option['decrease_stock_by'] )
		            && intval( $option['decrease_stock_by'] ) == $option['decrease_stock_by']
		            && intval( $option['decrease_stock_by'] ) >= 0
		       );
	}

	/**
	 * Normalizes the RSVP option conforming it to the array format.
	 *
	 * @param array|string $option
	 */
	protected function normalize_rsvp_option( &$option ) {
		$label_only_format = ! is_array( $option );
		if ( $label_only_format ) {
			$option = [ 'label' => $option, 'decrease_stock_by' => 1 ];
		} else {
			$option['decrease_stock_by'] = isset( $option['decrease_stock_by'] ) ? $option['decrease_stock_by'] : 1;
		}
	}

	/**
	 * Gets the block template "out of context" and makes it usable for non-Block Editor views.
	 *
	 * @since 4.11.0
	 * @since 4.12.3 Update usage of get_event_ticket_provider().
	 *
	 * @param WP_Post|int $post The post object or ID.
	 * @param boolean     $echo Whether to echo the output or not.
	 *
	 * @return string The block HTML.
	 */
	public function get_tickets_block( $post, $echo = true ) {
		if ( empty( $post ) ) {
			return '';
		}

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		// If password protected, do not display content.
		if ( post_password_required() ) {
			return '';
		}

		$post_id = $post->ID;

		$provider = Tribe__Tickets__Tickets::get_event_ticket_provider_object( $post_id );

		// Protect against ticket that exists but is of a type that is not enabled.
		if ( empty( $provider ) ) {
			return '';
		}

		// No need to handle RSVPs here.
		if ( 'Tribe__Tickets__RSVP' === $provider->class_name ) {
			return '';
		}

		/** @var Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		/** @var Tribe__Tickets__Editor__Blocks__Tickets $blocks_tickets */
		$blocks_tickets = tribe( 'tickets.editor.blocks.tickets' );

		/** @var Tribe__Settings_Manager $settings_manager */
		$settings_manager = tribe( 'settings.manager' );

		$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', null );

		/**
		 * Overwrites the threshold to display "# tickets left".
		 *
		 * @since 4.11.1
		 *
		 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
		 * @param int   $post_id   WP_Post/Event ID.
		 */
		$threshold = absint( apply_filters( 'tribe_display_tickets_block_tickets_left_threshold', $threshold, $post_id ) );

		/**
		 * Allow filtering of the button name for the tickets block.
		 *
		 * @since 4.11.0
		 *
		 * @param string $button_name The button name. Set to cart-button to send to cart on submit, or set to checkout-button to send to checkout on submit.
		 */
		$submit_button_name = apply_filters( 'tribe_tickets_ticket_block_submit', 'cart-button' );

		/**
		 * Show original price on sale.
		 *
		 * @param bool Whether the original price should be shown on sale or not. Default is true.
		 *
		 * @return bool Whether the original price should be shown on sale or not.
		 */
		$show_original_price_on_sale = apply_filters( 'tribe_tickets_show_original_price_on_sale', true );

		// Load assets manually.
		$blocks_tickets->assets();

		$tickets = $provider->get_tickets( $post_id );

		$args = [
			'post_id'                     => $post_id,
			'provider'                    => $provider,
			'provider_id'                 => $provider->class_name,
			'tickets'                     => $tickets,
			'cart_classes'                => [ 'tribe-block', 'tribe-tickets' ], // @todo: deprecate with V1.
			'tickets_on_sale'             => $blocks_tickets->get_tickets_on_sale( $tickets ),
			'has_tickets_on_sale'         => tribe_events_has_tickets_on_sale( $post_id ),
			'is_sale_past'                => $blocks_tickets->get_is_sale_past( $tickets ),
			'is_sale_future'              => $blocks_tickets->get_is_sale_future( $tickets ),
			'currency'                    => tribe( 'tickets.commerce.currency' ),
			'handler'                     => tribe( 'tickets.handler' ),
			'privacy'                     => tribe( 'tickets.privacy' ),
			'threshold'                   => $threshold,
			'must_login'                  => ! is_user_logged_in() && $provider->login_required(),
			'show_original_price_on_sale' => $show_original_price_on_sale,
			'is_mini'                     => null,
			'is_modal'                    => null,
			'submit_button_name'          => $submit_button_name,
			'cart_url'                    => method_exists( $provider, 'get_cart_url' ) ? $provider->get_cart_url() : '',
			'checkout_url'                => method_exists( $provider, 'get_checkout_url' ) ? $provider->get_checkout_url() : '',
		];

		/**
		 * Add the rendering attributes into global context.
		 *
		 * Start with the following for template files loading this global context.
		 * Keep all templates with this starter block of comments updated if these global args update.
		 *
		 * @var Tribe__Tickets__Editor__Template   $this                        [Global] Template object.
		 * @var int                                $post_id                     [Global] The current Post ID to which tickets are attached.
		 * @var Tribe__Tickets__Tickets            $provider                    [Global] The tickets provider class.
		 * @var string                             $provider_id                 [Global] The tickets provider class name.
		 * @var Tribe__Tickets__Ticket_Object[]    $tickets                     [Global] List of tickets.
		 * @var array                              $cart_classes                [Global] CSS classes.
		 * @var Tribe__Tickets__Ticket_Object[]    $tickets_on_sale             [Global] List of tickets on sale.
		 * @var bool                               $has_tickets_on_sale         [Global] True if the event has any tickets on sale.
		 * @var bool                               $is_sale_past                [Global] True if tickets' sale dates are all in the past.
		 * @var bool                               $is_sale_future              [Global] True if no ticket sale dates have started yet.
		 * @var Tribe__Tickets__Commerce__Currency $currency                    [Global] Tribe Currency object.
		 * @var Tribe__Tickets__Tickets_Handler    $handler                     [Global] Tribe Tickets Handler object.
		 * @var Tribe__Tickets__Privacy            $privacy                     [Global] Tribe Tickets Privacy object.
		 * @var int                                $threshold                   [Global] The count at which "number of tickets left" message appears.
		 * @var bool                               $show_original_price_on_sale [Global] Show original price on sale.
		 * @var null|bool                          $is_mini                     [Global] If in "mini cart" context.
		 * @var null|bool                          $is_modal                    [Global] Whether the modal is enabled.
		 * @var string                             $submit_button_name          [Global] The button name for the tickets block.
		 * @var string                             $cart_url                    [Global] Link to Cart (could be empty).
		 * @var string                             $checkout_url                [Global] Link to Checkout (could be empty).
		 */
		$template->add_template_globals( $args );

		// Add local vars to ensure that the data is passed properly within WP_Query Loop.
		$template->set_values( $args, true );

		// Enqueue assets.
		tribe_asset_enqueue_group( 'tribe-tickets-block-assets' );

		if ( tribe_tickets_new_views_is_enabled() ) {
			$before_content = '';

			/**
			 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
			 *
			 * @since 4.5.6
			 *
			 * @param boolean $already_rendered Whether the order link template has already been rendered.
			 *
			 * @see Tribe__Tickets__Tickets_View::inject_link_template()
			 */
			$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

			// Output order links / view link if we haven't already (for RSVPs).
			if ( ! $already_rendered ) {
				$before_content = $template->template( 'blocks/attendees/order-links', [], $echo );

				if ( empty( $before_content ) ) {
					$before_content = $template->template( 'blocks/attendees/view-link', [], $echo );
				}

				add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
			}

			return $before_content . $template->template( 'v2/tickets', [], $echo );
		}

		return $template->template( 'blocks/tickets', [], $echo );
	}

	/**
	 * Gets the RSVP block template "out of context" and makes it usable for Classic views.
	 *
	 * @since 4.12.3
	 *
	 * @param WP_Post|int $post The post object or ID.
	 * @param boolean     $echo Whether to echo the output or not.
	 *
	 * @return string The block HTML.
	 */
	public function get_rsvp_block( $post, $echo = true ) {
		if ( empty( $post ) ) {
			return '';
		}

		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if (
			empty( $post )
			|| ! ( $post instanceof WP_Post )
		) {
			return '';
		}

		// If password protected then do not display content.
		if ( post_password_required( $post ) ) {
			return '';
		}

		$post_id = $post->ID;

		/** @var \Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		/** @var \Tribe__Tickets__Editor__Blocks__Rsvp $blocks_rsvp */
		$blocks_rsvp = tribe( 'tickets.editor.blocks.rsvp' );

		/** @var Tribe__Tickets__RSVP $rsvp */
		$rsvp = tribe( 'tickets.rsvp' );

		// Check if the call is coming from a shortcode.
		$doing_shortcode = tribe_doing_shortcode( 'tribe_tickets_rsvp' );

		// Get the RSVP block HTML ID.
		$block_html_id  = 'rsvp-now';
		$block_html_id .= $doing_shortcode ? '-' . uniqid() : '';

		// Load assets manually.
		$blocks_rsvp->assets();

		$tickets        = $blocks_rsvp->get_tickets( $post_id );
		$active_tickets = $blocks_rsvp->get_active_tickets( $tickets );
		$past_tickets   = $blocks_rsvp->get_all_tickets_past( $tickets );

		$args = [
			'post_id'             => $post_id,
			'attributes'          => $blocks_rsvp->attributes(),
			'active_rsvps'        => $active_tickets,
			'all_past'            => $past_tickets,
			'has_rsvps'           => ! empty( $tickets ),
			'has_active_rsvps'    => ! empty( $active_tickets ),
			'must_login'          => ! is_user_logged_in() && $rsvp->login_required(),
			'login_url'           => Tribe__Tickets__Tickets::get_login_url( $post_id ),
			'threshold'           => $blocks_rsvp->get_threshold( $post_id ),
			'step'                => null,
			'opt_in_checked'      => false,
			'opt_in_attendee_ids' => '',
			'opt_in_nonce'        => '',
			'doing_shortcode'     => $doing_shortcode,
			'block_html_id'       => $block_html_id,
			'going'               => tribe_get_request_var( 'going', '' ),
		];

		/**
		 * Add the rendering attributes into global context.
		 *
		 * Start with the following for template files loading this global context.
		 * Keep all templates with this starter block of comments updated if these global args update.
		 *
		 * @var Tribe__Tickets__Editor__Template $this                Template object.
		 * @var int                              $post_id             [Global] The current Post ID to which RSVPs are attached.
		 * @var array                            $attributes          [Global] RSVP attributes (could be empty).
		 * @var Tribe__Tickets__Ticket_Object[]  $active_rsvps        [Global] List of RSVPs.
		 * @var bool                             $all_past            [Global] True if RSVPs availability dates are all in the past.
		 * @var bool                             $has_rsvps           [Global] True if the event has any RSVPs.
		 * @var bool                             $has_active_rsvps    [Global] True if the event has any RSVPs available.
		 * @var bool                             $must_login          [Global] True if login is required and user is not logged in..
		 * @var string                           $login_url           [Global] The site's login URL.
		 * @var int                              $threshold           [Global] The count at which "number of tickets left" message appears.
		 * @var null|string                      $step                [Global] The point we're at in the loading process.
		 * @var bool                             $opt_in_checked      [Global] Whether appearing in Attendee List was checked.
		 * @var string                           $opt_in_attendee_ids [Global] The list of attendee IDs to send in the form submission.
		 * @var string                           $opt_in_nonce        [Global] The nonce for opt-in AJAX requests.
		 * @var bool                             $doing_shortcode     [Global] True if detected within context of shortcode output.
		 * @var bool                             $block_html_id       [Global] The RSVP block HTML ID. $doing_shortcode may alter it.
		 */
		$template->add_template_globals( $args );

		ob_start();

		/**
		 * Allow for the addition of content (namely the "Who's Attending?" list) above the ticket form.
		 *
		 * @since 4.5.5
		 */
		do_action( 'tribe_tickets_before_front_end_ticket_form' );

		/**
		 * A flag we can set via filter, e.g. at the end of this method, to ensure this template only shows once.
		 *
		 * @since 4.5.6
		 *
		 * @param boolean $already_rendered Whether the order link template has already been rendered.
		 *
		 * @see Tribe__Tickets__Tickets_View::inject_link_template()
		 */
		$already_rendered = apply_filters( 'tribe_tickets_order_link_template_already_rendered', false );

		// Output order links / view link if we haven't already (for RSVPs).
		if ( ! $already_rendered ) {
			$template->template( 'tickets/view-link' );

			add_filter( 'tribe_tickets_order_link_template_already_rendered', '__return_true' );
		}

		$before_content = ob_get_clean();

		// Maybe echo the content from the action.
		if ( $echo ) {
			echo $before_content;

			$before_content = '';
		}

		// Maybe render the new views.
		if ( tribe_tickets_rsvp_new_views_is_enabled() ) {
			// Enqueue new assets.
			tribe_asset_enqueue_group( 'tribe-tickets-rsvp' );
			tribe_asset_enqueue( 'tribe-tickets-rsvp-style' );
			tribe_asset_enqueue( 'tribe-tickets-forms-style' );
			// @todo: Remove this once we solve the common breakpoints vs container based.
			tribe_asset_enqueue( 'tribe-common-responsive' );

			return $before_content . $template->template( 'v2/rsvp', $args, $echo );
		}

		// Enqueue assets.
		tribe_asset_enqueue( 'tribe-tickets-gutenberg-rsvp' );
		tribe_asset_enqueue( 'tribe-tickets-gutenberg-block-rsvp-style' );

		return $before_content . $template->template( 'blocks/rsvp', $args, $echo );
	}
}
