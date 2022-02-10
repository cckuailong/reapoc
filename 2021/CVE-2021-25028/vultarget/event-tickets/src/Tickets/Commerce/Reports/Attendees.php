<?php
/**
 * Attendees Report
 *
 * @package TEC\Tickets
 */

namespace TEC\Tickets\Commerce\Reports;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Admin_Tables;
use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Utils\Price;

/**
 * Class Reports for Attendees
 *
 * @since 5.2.0
 */
class Attendees extends Report_Abstract {

	/**
	 * Slug of the admin page for attendees
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	public static $page_slug = 'tickets-commerce-attendees';

	/**
	 * Order Pages ID on the menu.
	 *
	 * @since 5.2.0
	 *
	 * @var string The menu slug of the orders page
	 */
	public $attendees_page;

	/**
	 * Gets the Orders Report.
	 *
	 * @since 5.2.0
	 * @return string
	 */
	public function get_title() {
		$post_id = tribe_get_request_var( 'event_id' );

		// translators: The title of an event's Attendee List page in the dashboard. %1$s is the name of the event. %2$d is numeric the event ID.
		return sprintf( __( 'Attendees for: %1$s [#%2$d]', 'event-tickets' ), esc_html( get_the_title( $post_id ) ), (int) $post_id );
	}

	/**
	 * Hooks the actions and filter required by the class.
	 *
	 * @since 5.2.0
	 */
	public function hook() {
		add_action( 'admin_menu', [ $this, 'register_attendees_page' ] );
	}

	/**
	 * Registers the Tickets Commerce orders page as a plugin options page.
	 *
	 * @since 5.2.0
	 */
	public function register_attendees_page() {
		$candidate_post_id = tribe_get_request_var( 'post_id', 0 );
		$candidate_post_id = tribe_get_request_var( 'event_id', $candidate_post_id );
		$post_id           = absint( $candidate_post_id );

		if ( $post_id !== (int) $candidate_post_id ) {
			return;
		}

		$cap = 'edit_posts';
		if ( ! current_user_can( 'edit_posts' ) && $post_id ) {
			$post = get_post( $post_id );

			if ( $post instanceof WP_Post && get_current_user_id() === (int) $post->post_author ) {
				$cap = 'read';
			}
		}

		$page_title           = __( 'Tickets Commerce Attendees', 'event-tickets' );
		$this->attendees_page = add_submenu_page(
			null,
			$page_title,
			$page_title,
			$cap,
			static::$page_slug,
			[ $this, 'render_page' ]
		);

		$attendees = tribe( Commerce\Admin_Tables\Attendees::class );

		add_filter( 'tribe_filter_attendee_page_slug', [ $this, 'add_attendee_resources_page_slug' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'enqueue_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $attendees, 'load_pointers' ] );
		add_action( 'load-' . $this->attendees_page, [ $this, 'attendees_page_screen_setup' ] );
	}

	/**
	 * Sets the browser title for the Attendees admin page.
	 * Uses the event title.
	 *
	 * @since 5.2.0
	 *
	 * @param string $admin_title The page title in the admin.
	 *
	 * @return string
	 */
	public function filter_admin_title( $admin_title ) {
		if ( ! empty( (int) $_GET['event_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$event = get_post( (int) $_GET['event_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// translators: The title of an event's Attendee List page in the dashboard. %1$s is the name of the event.
			$admin_title = sprintf( __( '%1$s - Attendee list', 'event-tickets' ), $event->post_title );
		}

		return $admin_title;
	}

	/**
	 * Filter the page slugs that the attendee resources will load to add the order page
	 *
	 * @since 5.2.0
	 *
	 * @param array $slugs an array of admin slugs.
	 *
	 * @return array
	 */
	public function add_attendee_resources_page_slug( $slugs ) {
		$slugs[] = $this->attendees_page;

		return $slugs;
	}

	/**
	 * Sets up the attendees page screen.
	 *
	 * @since 5.2.0
	 */
	public function attendees_page_screen_setup() {
		$action = tribe_get_request_var( 'tribe-send-email', false );

		$orders_table = tribe( Commerce\Admin_Tables\Attendees::class );
		$orders_table->prepare_items();

		wp_enqueue_script( 'jquery-ui-dialog' );

		add_filter( 'admin_title', [ $this, 'filter_admin_title' ] );

		if ( $action ) {
			define( 'IFRAME_REQUEST', true );

			// Use iFrame Header -- WP Method.
			iframe_header();

			// Check if we need to send an Email!
			$status = false;
			if ( isset( $_POST['tribe-send-email'] ) && $_POST['tribe-send-email'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$status = tribe( \Tribe__Tickets__Attendees::class )->send_mail_list();
			}

			tribe( 'tickets.admin.views' )->template( 'attendees-email', [ 'status' => $status ] );

			// Use iFrame Footer -- WP Method.
			iframe_footer();

			// We need nothing else here.
			exit;
		} else {
			$this->maybe_generate_csv();

			add_filter( 'admin_title', [ $this, 'filter_admin_title' ], 10, 2 );
		}
	}

	/**
	 * Renders the order page
	 *
	 * @since 5.2.0
	 */
	public function render_page() {

		do_action( 'tribe_tickets_attendees_page_inside', tribe( \Tribe__Tickets__Attendees::class ) );

		$this->get_template()->template( 'attendees', $this->get_template_vars() );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	public function setup_template_vars() {
		$post_id = tribe_get_request_var( 'post_id' );
		$post_id = tribe_get_request_var( 'event_id', $post_id );
		$post    = get_post( $post_id );

		$post_type_object    = get_post_type_object( $post->post_type );
		$post_singular_label = $post_type_object->labels->singular_name;

		$tickets    = \Tribe__Tickets__Tickets::get_event_tickets( $post_id );
		$ticket_ids = tribe_get_request_var( 'product_ids', false );

		if ( false !== $ticket_ids ) {
			$ticket_ids = array_map( 'absint', explode( ',', $ticket_ids ) );
			$ticket_ids = array_filter(
				$ticket_ids,
				static function ( $ticket_id ) {
					return get_post_type( $ticket_id ) === Commerce\Ticket::POSTTYPE;
				}
			);
			$tickets    = array_map( [ tribe( Commerce\Ticket::class ), 'get_ticket' ], $ticket_ids );
		}

		$event_data   = [];
		$tickets_data = [];

		foreach ( $tickets as $ticket ) {
			$quantities      = tribe( Commerce\Ticket::class )->get_status_quantity( $ticket->ID );
			$total_by_status = [];
			foreach ( $quantities as $status_slug => $status_count ) {
				if ( ! isset( $event_data['qty_by_status'][ $status_slug ] ) ) {
					$event_data['qty_by_status'][ $status_slug ] = 0;
				}
				if ( ! isset( $event_data['total_by_status'][ $status_slug ] ) ) {
					$event_data['total_by_status'][ $status_slug ] = [];
				}

				$total_by_status[ $status_slug ]                 = Price::sub_total( $ticket->price, $status_count );
				$event_data['total_by_status'][ $status_slug ][] = $total_by_status[ $status_slug ];

				$event_data['qty_by_status'][ $status_slug ] += (int) $status_count;
			}
			$tickets_data[ $ticket->ID ] = [
				'total_by_status' => $total_by_status,
				'qty_by_status'   => $quantities,
			];
		}

		$event_data['total_by_status'] = array_map(
			static function ( $sub_totals ) {
				return Price::total( $sub_totals );
			},
			$event_data['total_by_status']
		);

		$this->template_vars = [
			'event_data'          => $event_data,
			'export_url'          => '',
			'post'                => $post,
			'post_id'             => $post_id,
			'post_singular_label' => $post_singular_label,
			'post_type_object'    => $post_type_object,
			'report'              => tribe( $this ),
			'table'               => tribe( Admin_Tables\Attendees::class ),
			'tickets'             => $tickets,
			'tickets_data'        => $tickets_data,
			'title'               => $this->get_title(),
			'tooltip'             => tribe( 'tooltip.view' ),
		];

		return $this->template_vars;
	}

	/**
	 * Determines if the "export" button will be displayed by the the title
	 *
	 * @since 5.2.0
	 *
	 * @param int $event_id The event whose attendees may be exported.
	 *
	 * @return bool
	 */
	public function can_export_attendees( $event_id ) {

		if ( tribe_get_request_var( 'page' ) !== static::$page_slug ) {
			return false;
		}

		if ( ! tribe( Admin_Tables\Attendees::class )->has_items() ) {
			return false;
		}

		if ( ! $this->user_can_manage_attendees( \get_current_user_id(), $event_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the current user (or an ID-specified one) is allowed to delete, check-in, and
	 * undo check-in attendees.
	 *
	 * @param int    $user_id  Optional. The ID of the user whose access we're checking.
	 * @param string $event_id Optional. The ID of the event the user is managing.
	 *
	 * @return boolean
	 * @since 5.2.0
	 */
	public function user_can_manage_attendees( $user_id = 0, $event_id = '' ) {
		$user_id  = 0 === $user_id ? get_current_user_id() : $user_id;
		$user_can = true;

		// bail quickly here as we don't have a user to check.
		if ( empty( $user_id ) ) {
			return false;
		}

		/**
		 * Allows customizing the caps a user must have to be allowed to manage attendees.
		 *
		 * @since 5.2.0
		 *
		 * @param int   $user_id      The ID of the user whose capabilities are being checked.
		 *
		 * @param array $default_caps The caps a user must have to be allowed to manage attendees.
		 */
		$required_caps = apply_filters(
			'tribe_tickets_caps_can_manage_attendees',
			[
				'edit_others_posts',
			],
			$user_id
		);

		// Next make sure the user has proper caps in their role.
		foreach ( $required_caps as $cap ) {
			if ( ! user_can( $user_id, $cap ) ) {
				$user_can = false;
				// break on first fail.
				break;
			}
		}

		/**
		 * Filter our return value to let other plugins hook in and alter things
		 *
		 * @since 5.2.0
		 *
		 * @param bool $user_can return value, user can or can't
		 * @param int  $user_id  id of the user we're checking
		 * @param int  $event_id id of the event we're checking (matter for checks on event authorship)
		 */
		$user_can = apply_filters( 'tribe_tickets_user_can_manage_attendees', $user_can, $user_id, $event_id );

		return $user_can;
	}

	/**
	 * Checks if the user requested a CSV export from the attendees list.
	 * If so, generates the download and finishes the execution.
	 *
	 * @since 5.2.0
	 */
	public function maybe_generate_csv() {
		if ( empty( $_GET['attendees_csv'] ) || empty( $_GET['attendees_csv_nonce'] ) || empty( $_GET['event_id'] ) ) {
			return;
		}

		$event_id = absint( $_GET['event_id'] );

		// Verify event ID is a valid integer and the nonce is accepted.
		if ( empty( $event_id ) || ! wp_verify_nonce( $_GET['attendees_csv_nonce'], 'attendees_csv_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$event = get_post( $event_id );

		// Verify event exists and current user has access to it.
		if (
			! $event instanceof \WP_Post
			|| ! current_user_can( 'edit_posts', $event_id )
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
		 * @since 5.2.0
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
				fputcsv( $output, $item, $delimiter ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_fputcsv
			}

			fclose( $output );
			exit;
		}
	}

	/**
	 * Generates a list of attendees taking into account the Screen Options.
	 * It's used both for the Email functionality, as well as the CSV export.
	 *
	 * @since 5.2.0
	 *
	 * @param int $event_id The Event ID.
	 *
	 * @return array
	 */
	private function generate_filtered_list( $event_id ) {
		$this->attendees_table = tribe( Admin_Tables\Attendees::class );
		/**
		 * Fire immediately prior to the generation of a filtered (exportable) attendee list.
		 *
		 * @since 5.2.0
		 *
		 * @param int $event_id
		 */
		do_action( 'tribe_events_tickets_generate_filtered_attendees_list', $event_id );

		if ( empty( $this->page_id ) ) {
			$this->page_id = 'tribe_events_page_tickets-attendees';
		}

		// Add in Columns or get_column_headers() returns nothing.
		$filter_name = "manage_{$this->page_id}_columns";
		add_filter( $filter_name, [ $this->attendees_table, 'get_columns' ], 15 );

		$tickets_class = tribe( \Tribe__Tickets__Tickets::class );
		$items = $tickets_class::get_event_attendees( $event_id );

		// Add Handler for Community Tickets to Prevent Notices in Exports.
		if ( ! is_admin() ) {
			$columns = apply_filters( $filter_name, [] );
		} else {
			$columns = array_filter( (array) get_column_headers( get_current_screen() ) );
			$columns = array_map( 'wp_strip_all_tags', $columns );
		}

		// We dont want HTML inputs, private data or other columns that are superfluous in a CSV export.
		$hidden = array_merge(
			get_hidden_columns( $this->page_id ),
			[
				'cb',
				'meta_details',
				'primary_info',
				'provider',
				'purchaser',
				'status',
			]
		);

		$hidden         = array_flip( $hidden );
		$export_columns = array_diff_key( $columns, $hidden );

		// Add additional expected columns.
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
		 * @since 5.2.0
		 *
		 * @param array Columns, associative array
		 * @param array Items to be exported
		 * @param int   Event ID
		 */
		$export_columns = apply_filters( 'tribe_events_tickets_attendees_csv_export_columns', $export_columns, $items, $event_id );

		// Add the export column headers as the first row.
		$rows = [
			array_values( $export_columns ),
		];

		foreach ( $items as $single_item ) {
			// Fresh row!
			$row         = [];
			$attendee    = tribe( Commerce\Attendee::class )->load_attendee_data( new \WP_Post( (object) $single_item ) );
			$single_item = (array) $attendee;

			foreach ( $export_columns as $column_id => $column_name ) {
				// If additional columns have been added to the attendee list table we can obtain the
				// values by calling the table object's column_default() method - any other values
				// should simply be passed back unmodified.
				$row[ $column_id ] = $this->attendees_table->column_default( $attendee, $column_id );

				// Special handling for the check_in column.
				if ( 'check_in' === $column_id && 1 == $single_item[ $column_id ] ) {
					$row[ $column_id ] = esc_html__( 'Yes', 'event-tickets' );
				}

				// Special handling for new human readable id.
				if ( 'attendee_id' === $column_id ) {
					if ( isset( $single_item[ $column_id ] ) ) {
						$ticket_unique_id  = get_post_meta( $single_item[ $column_id ], '_unique_id', true );
						$ticket_unique_id  = '' === $ticket_unique_id ? $single_item[ $column_id ] : $ticket_unique_id;
						$row[ $column_id ] = esc_html( $ticket_unique_id );
					}
				}

				// Handle custom columns that might have names containing HTML tags.
				$row[ $column_id ] = wp_strip_all_tags( $row[ $column_id ] );
				// Decode HTML Entities.
				$row[ $column_id ] = html_entity_decode( $row[ $column_id ], ENT_QUOTES | ENT_XML1, 'UTF-8' );
				// Remove line breaks (e.g. from multi-line text field) for valid CSV format. Double quotes necessary here.
				$row[ $column_id ] = str_replace( [ "\r", "\n" ], ' ', $row[ $column_id ] );
			}

			$rows[] = array_values( $row );
		}

		return array_filter( $rows );
	}

	/**
	 * Sanitize rows for CSV usage.
	 *
	 * @since 5.2.0
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
	 * @since 5.2.0
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
}
