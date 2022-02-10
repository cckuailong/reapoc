<?php
/**
 * Handles most actions related to an Attendeees or Multiple ones
 */
class Tribe__Tickets__Attendees {
	/**
	 * Hook of the admin page for attendees
	 *
	 * @since  4.6.2
	 *
	 * @var string
	 */
	public $page_id;

	/**
	 * WP_Post_List children for Attendees
	 *
	 * @since  4.6.2
	 *
	 * @var Tribe__Tickets__Attendees_Table
	 */
	public $attendees_table;

	/**
	 * Hooks all the required actions and filters in WordPress
	 *
	 * @since 4.6.2
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );

		add_action( 'tribe_report_page_after_text_label', [ $this, 'include_export_button_title' ], 25, 2 );
		add_action( 'tribe_tabbed_view_heading_after_text_label', [ $this, 'include_export_button_title' ], 25, 2 );
		add_action( 'tribe_events_tickets_attendees_totals_top', array( $this, 'print_checkedin_totals' ), 0 );
		add_action( 'tribe_tickets_attendees_event_details_list_top', array( $this, 'event_details_top' ), 20 );
		add_action( 'tribe_tickets_plus_report_event_details_list_top', array( $this, 'event_details_top' ), 20 );
		add_action( 'tribe_tickets_report_event_details_list_top', array( $this, 'event_details_top' ), 20 );

		add_action( 'tribe_tickets_attendees_event_details_list_top', array( $this, 'event_action_links' ), 25 );
		add_action( 'tribe_tickets_plus_report_event_details_list_top', array( $this, 'event_action_links' ), 25 );
		add_action( 'tribe_tickets_register_attendees_page', array( $this, 'add_dynamic_parent' ) );
		add_action( 'tribe_tickets_report_event_details_list_top', array( $this, 'event_action_links' ), 25 );

		add_filter( 'post_row_actions', array( $this, 'filter_admin_row_actions' ) );
		add_filter( 'page_row_actions', array( $this, 'filter_admin_row_actions' ) );
	}

	/**
	 * Returns the Attendees Post Type Slug (mostly used for RSVP)
	 *
	 * @since 4.6.2
	 *
	 * @return string
	 */
	public function slug() {
		return 'tickets-attendees';
	}

	/**
	 * Returns the current post being handled.
	 *
	 * @since 4.6.2
	 *
	 * @return array|bool|null|WP_Post
	 */
	public function get_post() {
		return empty( $this->attendees_table->event ) ? false : $this->attendees_table->event;
	}

	/**
	 * Injects event post type
	 *
	 * @since 4.6.2
	 *
	 * @param int $event_id
	 */
	public function event_details_top( $event_id ) {
		$pto = get_post_type_object( get_post_type( $event_id ) );

		echo '
			<li class="post-type">
				<strong>' . esc_html__( 'Post type', 'event-tickets' ) . ': </strong>
				' . esc_html( strtolower( $pto->labels->singular_name ) ) . '
			</li>
		';
	}

	/**
	 * Injects action links into the attendee screen.
	 *
	 * @since 4.6.2
	 *
	 * @param $event_id
	 */
	public function event_action_links( $event_id ) {

		/**
		 * Allows for control of the specific "edit post" URLs used for event Sales and Attendees Reports.
		 *
		 * @since 4.6.2
		 *
		 * @param string $link The deafult "edit post" URL.
		 * @param int $event_id The Post ID of the event.
		 */
		$edit_post_link = apply_filters( 'tribe_tickets_event_action_links_edit_url', get_edit_post_link( $event_id ), $event_id );

		$post     = get_post( $event_id );
		$pto      = get_post_type_object( $post->post_type );
		$singular = $pto->labels->singular_name;

		$edit         = esc_html( sprintf( _x( 'Edit %s', 'attendee event actions', 'event-tickets' ), $singular ) );
		$view         = esc_html( sprintf( _x( 'View %s', 'attendee event actions', 'event-tickets' ), $singular ) );

		$action_links = array(
			'<a href="' . esc_url( $edit_post_link ) . '" title="' . esc_attr_x( 'Edit', 'attendee event actions', 'event-tickets' ) . '">' . $edit . '</a>',
			'<a href="' . esc_url( get_permalink( $event_id ) ) . '" title="' . esc_attr_x( 'View', 'attendee event actions', 'event-tickets' ) . '">' . $view . '</a>',
		);

		/**
		 * Provides an opportunity to add and remove action links from the attendee screen summary box.
		 *
		 * @param array $action_links
		 * @param int $event_id
		 */
		$action_links = (array) apply_filters( 'tribe_tickets_attendees_event_action_links', $action_links, $event_id );

		if ( empty( $action_links ) ) {
			return;
		}

		echo wp_kses_post( '<li class="event-actions">' . implode( ' | ', $action_links ) . '</li>' );
	}


	/**
	 * Print Check In Totals at top of Column
	 *
	 * @since 4.6.2
	 */
	public function print_checkedin_totals() {
		$total_checked_in = Tribe__Tickets__Main::instance()->attendance_totals()->get_total_checked_in();

		echo '<div class="totals-header"><h3>' . esc_html_x( 'Checked in:', 'attendee summary', 'event-tickets' ) . '</h3> <span id="total_checkedin">' . absint( $total_checked_in ) . '</span></div>';
	}

	/**
	 * Returns the full URL to the attendees report page.
	 *
	 * @since 4.6.2
	 *
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_report_link( $post ) {
		$args = array(
			'post_type' => $post->post_type,
			'page'      => $this->slug(),
			'event_id'  => $post->ID,
		);

		// Remove the post type from the admin URL as ?post_type=post is the same as edit.php and is not required for posts.
		if ( 'post' === $post->post_type ) {
			unset( $args['post_type'] );
		}

		$url = add_query_arg( $args, admin_url( 'edit.php' ) );

		/**
		 * Filter the Attendee Report Url
		 *
		 * @since 5.0.3
		 *
		 * @param string $url  a url to attendee report
		 * @param int    $post ->ID post id
		 */
		$url = apply_filters( 'tribe_ticket_filter_attendee_report_link', $url, $post->ID );

		return $url;
	}

	/**
	 * Adds the "attendees" link in the admin list row actions for each event.
	 *
	 * @param $actions
	 *
	 * @return array
	 */
	public function filter_admin_row_actions( $actions ) {
		global $post;

		// Only proceed if we're viewing a tickets-enabled post type.
		if ( ! in_array( $post->post_type, Tribe__Tickets__Main::instance()->post_types() ) ) {
			return $actions;
		}

		$tickets = Tribe__Tickets__Tickets::get_event_tickets( $post->ID );

		// Only proceed if there are tickets.
		if ( empty( $tickets ) ) {
			return $actions;
		}

		$url = $this->get_report_link( $post );

		$actions['tickets_attendees'] = sprintf(
			'<a title="%s" href="%s">%s</a>',
			esc_html__( 'See who purchased tickets to this event', 'event-tickets' ),
			esc_url( $url ),
			esc_html__( 'Attendees', 'event-tickets' )
		);

		return $actions;
	}

	/**
	 * Registers the Attendees admin page
	 *
	 * @since 4.6.2
	 */
	public function register_page() {
		$cap      = 'edit_posts';
		$event_id = absint( ! empty( $_GET['event_id'] ) && is_numeric( $_GET['event_id'] ) ? $_GET['event_id'] : 0 );

		if ( ! current_user_can( 'edit_posts' ) && $event_id ) {
			$event = get_post( $event_id );

			if ( $event instanceof WP_Post && get_current_user_id() === (int) $event->post_author ) {
				$cap = 'read';
			}
		}

		$this->page_id = add_submenu_page(
			null,
			'Attendee list',
			'Attendee list',
			$cap,
			$this->slug(),
			array( $this, 'render' )
		);

		/**
		 * @since 4.7.1
		 *
		 * @param string $page_id
		 */
		do_action( 'tribe_tickets_register_attendees_page', $this->page_id );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pointers' ) );
		add_action( "load-{$this->page_id}", array( $this, 'screen_setup' ) );;
	}

	/**
	 * Add dynamic registered pages that belongs to Tribe__Events__Main::POSTTYPE as those are a subpage of that
	 * parent page.
	 *
	 * @since 4.7.1
	 *
	 * @param string $page_id
	 */
	public function add_dynamic_parent( $page_id = '' ) {
		if ( ! $page_id ) {
			return;
		}

		$options = (array) tribe_get_option( 'ticket-enabled-post-types', array() );
		$venue_has_tickets = class_exists( 'Tribe__Events__Venue' ) && in_array( Tribe__Events__Venue::POSTTYPE, $options, true );
		$organizer_has_tickets = class_exists( 'Tribe__Events__Organizer' ) && in_array( Tribe__Events__Organizer::POSTTYPE, $options, true );

		global $_registered_pages;

		if ( ! is_array( $_registered_pages ) ) {
			return;
		}

		if ( $venue_has_tickets || $organizer_has_tickets ) {
			$dynamic_page = str_replace( 'admin_page', Tribe__Events__Main::POSTTYPE . '_page', $page_id );
			$_registered_pages[ $dynamic_page ] = true;
		}
	}

	/**
	 * Enqueues the JS and CSS for the attendees page in the admin
	 *
	 * @since 4.6.2
	 *
	 * @todo this needs to use tribe_assets()
	 *
	 * @param $hook
	 */
	public function enqueue_assets( $hook ) {
		/**
		 * Filter the Page Slugs the Attendees Page CSS and JS Loads
		 *
		 * @param array array( $this->page_id ) an array of admin slugs
		 */
		if ( ! in_array( $hook, apply_filters( 'tribe_filter_attendee_page_slug', array( $this->page_id ) ) ) ) {
			return;
		}

		$resources_url = plugins_url( 'src/resources', dirname( dirname( __FILE__ ) ) );

		wp_enqueue_style( 'tickets-report-css', $resources_url . '/css/tickets-report.css', array(), Tribe__Tickets__Main::instance()->css_version() );
		wp_enqueue_style( 'tickets-report-print-css', $resources_url . '/css/tickets-report-print.css', array(), Tribe__Tickets__Main::instance()->css_version(), 'print' );
		wp_enqueue_script( $this->slug() . '-js', $resources_url . '/js/tickets-attendees.js', array( 'jquery' ), Tribe__Tickets__Main::instance()->js_version() );

		add_thickbox();

		$move_url_args = [
			'dialog'    => Tribe__Tickets__Main::instance()->move_tickets()->dialog_name(),
			'check'     => wp_create_nonce( 'move_tickets' ),
			'TB_iframe' => 'true',
		];

		$config_data = [
			'nonce'             => wp_create_nonce( 'email-attendee-list' ),
			'required'          => esc_html__( 'You need to select a user or type a valid email address', 'event-tickets' ),
			'sending'           => esc_html__( 'Sending...', 'event-tickets' ),
			'ajaxurl'           => admin_url( 'admin-ajax.php' ),
			'checkin_nonce'     => wp_create_nonce( 'checkin' ),
			'uncheckin_nonce'   => wp_create_nonce( 'uncheckin' ),
			'cannot_move'       => esc_html__( 'You must first select one or more tickets before you can move them!', 'event-tickets' ),
			'move_url'          => add_query_arg( $move_url_args ),
			'confirmation'      => esc_html__( 'Please confirm that you would like to delete this attendee.', 'event-tickets' ),
			'bulk_confirmation' => esc_html__( 'Please confirm you would like to delete these attendees.', 'event-tickets' ),
		];

		/**
		 * Allow filtering the configuration data for the Attendee objects on Attendees report page.
		 *
		 * @since 5.0.4
		 *
		 * @param array $config_data List of configuration data to be localized.
		 */
		$config_data = apply_filters( 'tribe_tickets_attendees_report_js_config', $config_data );

		wp_localize_script( $this->slug() . '-js', 'Attendees', $config_data );
	}

	/**
	 * Loads the WP-Pointer for the Attendees screen
	 *
	 * @since 4.6.2
	 *
	 * @param $hook
	 */
	public function load_pointers( $hook ) {
		if ( $hook != $this->page_id ) {
			return;
		}

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$pointer   = [];

		if ( version_compare( get_bloginfo( 'version' ), '3.3', '>' ) && ! in_array( 'attendees_filters', $dismissed ) ) {
			$pointer = array(
				'pointer_id' => 'attendees_filters',
				'target'     => '#screen-options-link-wrap',
				'options'    => array(
					'content' => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Columns', 'event-tickets' ), esc_html__( 'You can use Screen Options to select which columns you want to see. The selection works in the table below, in the email, for print and for the CSV export.', 'event-tickets' ) ),
					'position' => array( 'edge' => 'top', 'align' => 'right' ),
				),
			);
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
		}

		wp_localize_script( $this->slug() . '-js', 'AttendeesPointer', $pointer );
	}

	/**
	 * Sets up the Attendees screen data.
	 *
	 * @since 4.6.2
	 */
	public function screen_setup() {
		$page = tribe_get_request_var( 'page', false );
		$action = tribe_get_request_var( 'action', false );

		// When on the admin and not on the correct page bail
		if ( is_admin() && $this->slug() !== $page ) {
			return;
		}

		if ( 'email' === $action ) {
			define( 'IFRAME_REQUEST', true );

			// Use iFrame Header -- WP Method
			iframe_header();

			// Check if we need to send an Email!
			$status = false;
			if ( isset( $_POST['tribe-send-email'] ) && $_POST['tribe-send-email'] ) {
				$status = $this->send_mail_list();
			}

			tribe( 'tickets.admin.views' )->template( 'attendees-email', array( 'status' => $status ) );

			// Use iFrame Footer -- WP Method
			iframe_footer();

			// We need nothing else here
			exit;
		} else {
			$this->attendees_table = new Tribe__Tickets__Attendees_Table();

			$this->maybe_generate_csv();

			add_filter( 'admin_title', array( $this, 'filter_admin_title' ), 10, 2 );
			add_filter( 'admin_body_class', array( $this, 'filter_admin_body_class' ) );
		}
	}

	/**
	 * Add admin body class
	 *
	 * @since 4.6.2
	 */
	public function filter_admin_body_class( $body_classes ) {
		return $body_classes . ' plugins-php';
	}

	/**
	 * Sets the browser title for the Attendees admin page.
	 * Uses the event title.
	 *
	 * @since 4.6.2
	 *
	 * @param $admin_title
	 * @param $unused_title
	 *
	 * @return string
	 */
	public function filter_admin_title( $admin_title, $unused_title ) {
		if ( ! empty( $_GET['event_id'] ) ) {
			$event       = get_post( $_GET['event_id'] );
			$admin_title = sprintf( __( '%s - Attendee list', 'event-tickets' ), $event->post_title );
		}

		return $admin_title;
	}

	/**
	 * Renders the Attendees page
	 *
	 * @since 4.6.2
	 */
	public function render() {
		/**
		 * Fires immediately before the content of the attendees screen
		 * is rendered.
		 *
		 * @param $this Tribe__Tickets__Tickets_Handler The current ticket handler instance.
		 */
		do_action( 'tribe_tickets_attendees_page_inside', $this );

		tribe( 'tickets.admin.views' )->template( 'attendees' );
	}

	/**
	 * Generates a list of attendees taking into account the Screen Options.
	 * It's used both for the Email functionality, as well as the CSV export.
	 *
	 * @since 4.6.2
	 *
	 * @param $event_id
	 *
	 * @return array
	 */
	private function generate_filtered_list( $event_id ) {
		/**
		 * Fire immediately prior to the generation of a filtered (exportable) attendee list.
		 *
		 * @param int $event_id
		 */
		do_action( 'tribe_events_tickets_generate_filtered_attendees_list', $event_id );

		if ( empty( $this->page_id ) ) {
			$this->page_id = 'tribe_events_page_tickets-attendees';
		}

		//Add in Columns or get_column_headers() returns nothing
		$filter_name = "manage_{$this->page_id}_columns";
		add_filter( $filter_name, array( $this->attendees_table, 'get_columns' ), 15 );

		$items = Tribe__Tickets__Tickets::get_event_attendees( $event_id );

		//Add Handler for Community Tickets to Prevent Notices in Exports
		if ( ! is_admin() ) {
			$columns = apply_filters( $filter_name, array() );
		} else {
			$columns = array_filter( (array) get_column_headers( get_current_screen() ) );
			$columns = array_map( 'wp_strip_all_tags', $columns  );
		}

		// We dont want HTML inputs, private data or other columns that are superfluous in a CSV export
		$hidden = array_merge( get_hidden_columns( $this->page_id ), array(
			'cb',
			'meta_details',
			'primary_info',
			'provider',
			'purchaser',
			'status',
		) );

		$hidden         = array_flip( $hidden );
		$export_columns = array_diff_key( $columns, $hidden );

		// Add additional expected columns
		$export_columns['order_id']           = esc_html_x( 'Order ID', 'attendee export', 'event-tickets' );
		$export_columns['order_status_label'] = esc_html_x( 'Order Status', 'attendee export', 'event-tickets' );
		$export_columns['attendee_id']        = esc_html( sprintf( _x( '%s ID', 'attendee export', 'event-tickets' ), tribe_get_ticket_label_singular( 'attendee_export_ticket_id' ) ) );
		$export_columns['holder_name']        = esc_html_x( 'Ticket Holder Name', 'attendee export', 'event-tickets' );
		$export_columns['holder_email']       = esc_html_x( 'Ticket Holder Email Address', 'attendee export', 'event-tickets' );
		$export_columns['purchaser_name']     = esc_html_x( 'Purchaser Name', 'attendee export', 'event-tickets' );
		$export_columns['purchaser_email']    = esc_html_x( 'Purchaser Email Address', 'attendee export', 'event-tickets' );

		/**
		 * Used to modify what columns should be shown on the CSV export
		 * The column name should be the Array Index and the Header is the array Value
		 *
		 * @param array Columns, associative array
		 * @param array Items to be exported
		 * @param int   Event ID
		 */
		$export_columns = apply_filters( 'tribe_events_tickets_attendees_csv_export_columns', $export_columns, $items, $event_id );

		// Add the export column headers as the first row
		$rows = array(
			array_values( $export_columns ),
		);

		foreach ( $items as $single_item ) {
			// Fresh row!
			$row = array();

			foreach ( $export_columns as $column_id => $column_name ) {
				// If additional columns have been added to the attendee list table we can obtain the
				// values by calling the table object's column_default() method - any other values
				// should simply be passed back unmodified
				$row[ $column_id ] = $this->attendees_table->column_default( $single_item, $column_id );

				// Special handling for the check_in column
				if ( ! empty( $single_item[ $column_id ] ) && 'check_in' === $column_id && 1 == $single_item[ $column_id ] ) {
					$row[ $column_id ] = esc_html__( 'Yes', 'event-tickets' );
				}

				// Special handling for new human readable id
				if ( 'attendee_id' === $column_id ) {
					if ( isset( $single_item[ $column_id ] ) ) {
						$ticket_unique_id  = get_post_meta( $single_item[ $column_id ], '_unique_id', true );
						$ticket_unique_id  = $ticket_unique_id === '' ? $single_item[ $column_id ] : $ticket_unique_id;
						$row[ $column_id ] = esc_html( $ticket_unique_id );
					}
				}

				// Handle custom columns that might have names containing HTML tags
				$row[ $column_id ] = wp_strip_all_tags( $row[ $column_id ] );
				// Decode HTML Entities.
				$row[ $column_id ] = html_entity_decode( $row[ $column_id ] , ENT_QUOTES | ENT_XML1, 'UTF-8' );
				// Remove line breaks (e.g. from multi-line text field) for valid CSV format. Double quotes necessary here.
				$row[ $column_id ] = str_replace( array( "\r", "\n" ), ' ', $row[ $column_id ] );
			}

			$rows[] = array_values( $row );
		}

		return array_filter( $rows );
	}

	/**
	 * Sanitize rows for CSV usage.
	 *
	 * @since 4.10.7.2
	 *
	 * @param array $rows Rows to be sanitized.
	 *
	 * @return array Sanitized rows.
	 */
	public function sanitize_csv_rows( array $rows ) {
		foreach ( $rows as &$row ) {
			$row = array_map( [ $this, 'sanitize_csv_value' ], $row );
		}

		return $rows;
	}

	/**
	 * Sanitize a value for CSV usage.
	 *
	 * @since 4.10.7.2
	 *
	 * @param mixed $value Value to be sanitized.
	 *
	 * @return string Sanitized value.
	 */
	public function sanitize_csv_value( $value ) {
		if (
			0 === tribe_strpos( $value, '=' )
			|| 0 === tribe_strpos( $value, '+' )
			|| 0 === tribe_strpos( $value, '-' )
			|| 0 === tribe_strpos( $value, '@' )
		) {
			// Prefix the value with a single quote to prevent formula from being processed.
			$value = '\'' . $value;
		}

		return $value;
	}

	/**
	 * Checks if the user requested a CSV export from the attendees list.
	 * If so, generates the download and finishes the execution.
	 *
	 * @since 4.6.2
	 *
	 */
	public function maybe_generate_csv() {
		if ( empty( $_GET['attendees_csv'] ) || empty( $_GET['attendees_csv_nonce'] ) || empty( $_GET['event_id'] ) ) {
			return;
		}

		$event_id = absint( $_GET['event_id'] );

		// Verify event ID is a valid integer and the nonce is accepted.
		if ( empty( $event_id ) || ! wp_verify_nonce( $_GET['attendees_csv_nonce'], 'attendees_csv_nonce' ) ) {
			return;
		}

		$event = get_post( $event_id );

		// Verify event exists and current user has access to it.
		if (
			! $event instanceof WP_Post
			|| ! $this->user_can( 'edit_posts', $event_id )
		) {
			return;
		}

		// Generate filtered list of attendees.
		$items = $this->generate_filtered_list( $event_id );

		// Sanitize items for CSV usage.
		$items = $this->sanitize_csv_rows( $items );

		/**
		 * Allow for filtering and modifying the list of attendees that will be exported via CSV for a given event.
		 *
		 * @param array $items    The array of attendees that will be exported in this CSV file.
		 * @param int   $event_id The ID of the event these attendees are associated with.
		 */
		$items = apply_filters( 'tribe_events_tickets_attendees_csv_items', $items, $event_id );

		if ( ! empty( $items ) ) {
			$charset  = get_option( 'blog_charset' );
			$filename = sanitize_file_name( $event->post_title . '-' . __( 'attendees', 'event-tickets' ) );

			// Output headers so that the file is downloaded rather than displayed.
			header( "Content-Type: text/csv; charset=$charset" );
			header( "Content-Disposition: attachment; filename=$filename.csv" );

			// Create the file pointer connected to the output stream.
			$output = fopen( 'php://output', 'w' );

			/**
			 * Allow filtering the field delimiter used in the CSV export file.
			 *
			 * @since 5.1.3
			 *
			 * @param string $delimiter The field delimiter used in the CSV export file.
			 */
			$delimiter = apply_filters( 'tribe_tickets_attendees_csv_export_delimiter', ',' );

			// Output the lines into the file.
			foreach ( $items as $item ) {
				fputcsv( $output, $item, $delimiter );
			}

			fclose( $output );
			exit;
		}
	}

	/**
	 * Handles the "send to email" action for the attendees list.
	 *
	 * @since 4.6.2
	 *
	 */
	public function send_mail_list() {
		$error = new WP_Error();

		if ( empty( $_GET['event_id'] ) ) {
			$error->add( 'no-event-id', esc_html__( 'Invalid Event ID', 'event-tickets' ), array( 'type' => 'general' ) );

			return $error;
		}

		$cap      = 'edit_posts';
		$event_id = absint( ! empty( $_GET['event_id'] ) && is_numeric( $_GET['event_id'] ) ? $_GET['event_id'] : 0 );

		if ( ! current_user_can( 'edit_posts' ) && $event_id ) {
			$event = get_post( $event_id );

			if ( $event instanceof WP_Post && get_current_user_id() === (int) $event->post_author ) {
				$cap = 'read';
			}
		}

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'email-attendees-list' ) || ! $this->user_can( $cap, $_GET['event_id'] ) ) {
			$error->add( 'nonce-fail', esc_html__( 'Cheatin Huh?', 'event-tickets' ), array( 'type' => 'general' ) );

			return $error;
		}

		if ( empty( $_POST['email_to_address'] ) && ( empty( $_POST['email_to_user'] ) || 0 >= (int) $_POST['email_to_user'] ) ) {
			$error->add( 'empty-fields', esc_html__( 'Empty user and email', 'event-tickets' ), array( 'type' => 'general' ) );

			return $error;
		}

		if ( ! empty( $_POST['email_to_address'] ) ) {
			$type = 'email';
		} else {
			$type = 'user';
		}

		if ( 'email' === $type && ! is_email( $_POST['email_to_address'] ) ) {
			$error->add( 'invalid-email', esc_html__( 'Invalid Email', 'event-tickets' ), array( 'type' => $type ) );

			return $error;
		}

		if ( 'user' === $type && ! is_numeric( $_POST['email_to_user'] ) ) {
			$error->add( 'invalid-user', esc_html__( 'Invalid User ID', 'event-tickets' ), array( 'type' => $type ) );

			return $error;
		}

		/**
		 * Now we know we have valid data
		 */

		if ( 'email' === $type ) {
			// We already check this variable so, no harm here
			$email = $_POST['email_to_address'];
		} else {
			$user = get_user_by( 'id', $_POST['email_to_user'] );

			if ( ! is_object( $user ) ) {
				$error->add( 'invalid-user', esc_html__( 'Invalid User ID', 'event-tickets' ), array( 'type' => $type ) );

				return $error;
			}

			$email = $user->data->user_email;
		}

		$this->attendees_table = new Tribe__Tickets__Attendees_Table();
		$items = $this->generate_filtered_list( $_GET['event_id'] );

		$event = get_post( $_GET['event_id'] );

		ob_start();
		$attendee_tpl = Tribe__Tickets__Templates::get_template_hierarchy( 'tickets/attendees-email.php', array( 'disable_view_check' => true ) );
		include $attendee_tpl;
		$content = ob_get_clean();

		add_filter( 'wp_mail_content_type', array( $this, 'set_contenttype' ) );

		if ( ! wp_mail( $email, sprintf( esc_html__( 'Attendee List for: %s', 'event-tickets' ), $event->post_title ), $content ) ) {
			$error->add( 'email-error', esc_html__( 'Error when sending the email', 'event-tickets' ), array( 'type' => 'general' ) );

			return $error;
		}

		remove_filter( 'wp_mail_content_type', array( $this, 'set_contenttype' ) );

		return esc_html__( 'Email sent successfully!', 'event-tickets' );
	}

	/**
	 * Sets the content type for the attendees to email functionality.
	 * Allows for sending an HTML email.
	 *
	 * @since 4.6.2
	 *
	 * @param $content_type
	 *
	 * @return string
	 */
	public function set_contenttype( $content_type ) {
		return 'text/html';
	}

	/**
	 * Tests if the user has the specified capability in relation to whatever post type
	 * the ticket relates to.
	 *
	 * For example, if tickets are created for the banana post type, the generic capability
	 * "edit_posts" will be mapped to "edit_bananas" or whatever is appropriate.
	 *
	 * @since 4.6.2
	 *
	 * @internal for internal plugin use only (in spite of having public visibility)
	 *
	 * @param  string $generic_cap
	 * @param  int    $event_id
	 * @return boolean
	 */
	public function user_can( $generic_cap, $event_id ) {
		$type = get_post_type( $event_id );

		// It's possible we'll get null back
		if ( null === $type ) {
			return false;
		}

		$type_config = get_post_type_object( $type );

		if ( ! empty( $type_config->cap->{$generic_cap} ) ) {
			return current_user_can( $type_config->cap->{$generic_cap} );
		}

		return false;
	}

	/**
	 * Determines if the current user (or an ID-specified one) is allowed to delete, check-in, and
	 * undo check-in attendees.
	 *
	 * @since 4.6.3
	 *
	 * @param int $user_id Optional. The ID of the user whose access we're checking.
	 *
	 * @return boolean
	 */
	public function user_can_manage_attendees( $user_id = 0, $event_id = '' ) {
		$user_id = 0 === $user_id ? get_current_user_id() : $user_id;
		$user_can = true;

		// bail quickly here as we don't have a user to check
		if ( empty( $user_id ) ) {
			return false;
		}

		/**
		 * Allows customizing the caps a user must have to be allowed to manage attendees.
		 *
		 * @since 4.6.3
		 *
		 * @param array $default_caps The caps a user must have to be allowed to manage attendees.
		 * @param int $user_id The ID of the user whose capabilities are being checked.
		 */
		$required_caps = apply_filters( 'tribe_tickets_caps_can_manage_attendees', array(
			'edit_others_posts'
		), $user_id );

		// Next make sure the user has proper caps in their role.
		foreach ( $required_caps as $cap ) {
			if ( ! user_can( $user_id, $cap ) ) {
				$user_can = false;
				// break on first fail
				break;
			}
		}

		/**
		 * Filter our return value to let other plugins hook in and alter things
		 *
		 * @since 4.10.1
		 *
		 * @param bool $user_can return value, user can or can't
		 * @param int $user_id id of the user we're checking
		 * @param int $event_id id of the event we're checking (matter for checks on event authorship)
		 */
		$user_can = apply_filters( 'tribe_tickets_user_can_manage_attendees', $user_can, $user_id, $event_id );

		return $user_can;
	}

	/**
	 * Create an attendee for any Commerce provider from a ticket.
	 *
	 * @since 5.1.0
	 *
	 * @param Tribe__Tickets__Ticket_Object|int $ticket        Ticket object or ID to create the attendee for.
	 * @param array                             $attendee_data Attendee data to create from.
	 *
	 * @return WP_Post|false The new post object or false if unsuccessful.
	 */
	public function create_attendee( $ticket, $attendee_data ) {
		if ( is_numeric( $ticket ) ) {
			// Try to get provider from the ticket ID.
			$provider = tribe_tickets_get_ticket_provider( (int) $ticket );
		} else {
			// Get provider from ticket object.
			$provider = $ticket->get_provider();
		}

		if ( ! $provider ) {
			return false;
		}

		return $provider->create_attendee( $ticket, $attendee_data );
	}

	/**
	 * Update an attendee for any Commerce provider.
	 *
	 * @since 5.1.0
	 *
	 * @param array|int $attendee      The attendee data or ID for the attendee to update.
	 * @param array     $attendee_data The attendee data to update to.
	 *
	 * @return WP_Post|false The updated post object or false if unsuccessful.
	 */
	public function update_attendee( $attendee, $attendee_data ) {
		$provider = false;

		if ( is_numeric( $attendee ) ) {
			// Try to get provider from the attendee ID.
			$provider = tribe_tickets_get_ticket_provider( (int) $attendee );
		} elseif ( is_array( $attendee ) && isset( $attendee['provider'] ) ) {
			// Try to get provider from the attendee data.
			$provider = Tribe__Tickets__Tickets::get_ticket_provider_instance( $attendee['provider'] );
		}

		if ( ! $provider ) {
			return false;
		}

		return $provider->update_attendee( $attendee, $attendee_data );
	}

	/**
	 * Generate the export URL for exporting attendees.
	 *
	 * @since 5.1.7
	 *
	 * @return string Relative URL for the export.
	 */
	public function get_export_url() {
		return  add_query_arg(
			[
				'attendees_csv'       => true,
				'attendees_csv_nonce' => wp_create_nonce( 'attendees_csv_nonce' ),
			]
		);
	}

	/**
	 * Echo the button for the export that appears next to the attendees page title.
	 *
	 * @since 5.1.7
	 *
	 * @param int $event_id The Post ID of the event.
	 * @param Tribe__Tickets__Attendees $attendees The attendees object.
	 *
	 * @return string Relative URL for the export.
	 */
	public function include_export_button_title( $event_id, Tribe__Tickets__Attendees $attendees = null ){

		// Bail if not on the Attendees page.
		if ( 'tickets-attendees' !== tribe_get_request_var( 'page' ) ) {
			return;
		}

		// If this function is called from the tabbed-view.php file it does not send over $event_id or $attendees.
		// If the $event_id is not an integer we can get the information from the get scope and find the data.
		if ( ! is_int( $event_id ) &&
		     ! empty( tribe_get_request_var( 'event_id' ) )
		) {
			$event_id = tribe_get_request_var( 'event_id' );
			$attendees = tribe( 'tickets.attendees' );
			$attendees->attendees_table->prepare_items();
		}

		// Bail early if there are no attendees.
		if ( empty( $attendees ) ||
		     ! $attendees->attendees_table->has_items()
		) {
			return;
		}

		// Bail early if user is not owner/have permissions.
		if ( ! $this->user_can_manage_attendees( 0, $event_id ) ) {
			return;
		}

		echo sprintf(
			'<a target="_blank" href="%s" class="export action page-title-action" rel="noopener noreferrer">%s</a>',
			esc_url( $export_url = $this->get_export_url() ),
			esc_html__( 'Export', 'event-tickets' )
		) ;
	}
}
