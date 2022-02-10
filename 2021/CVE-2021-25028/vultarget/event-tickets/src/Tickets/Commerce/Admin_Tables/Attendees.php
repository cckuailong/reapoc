<?php
/**
 * Attendees Table
 *
 * @package TEC\Tickets
 */

namespace TEC\Tickets\Commerce\Admin_Tables;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/screen.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use TEC\Tickets\Commerce\Attendee;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Ticket;
use WP_List_Table;

/**
 * Class Admin Tables for Attendees
 *
 * @since 5.2.0
 */
class Attendees extends WP_List_Table {

	/**
	 * Legacy Attendees Table Controller
	 *
	 * @var \Tribe__Tickets__Attendees_Table
	 */
	private $legacy_attendees_table;

	/**
	 * The name attribute of the search box input
	 *
	 * @var string
	 */
	private $search_box_input_name = 's';

	/**
	 *  Documented in WP_List_Table
	 *
	 * @since 5.2.0
	 *
	 * @param array|string $args Array or string of arguments.
	 */
	public function __construct( $args = [] ) {
		$this->legacy_attendees_table = new \Tribe__Tickets__Attendees_Table();

		/**
		 * This class' parent defaults to 's', but we want to change that on the front-end (e.g. Community) to avoid
		 * the possibility of triggering the theme's Search template.
		 */
		if ( ! is_admin() ) {
			$this->search_box_input_name = 'search';
		}

		$screen = get_current_screen();

		$args = wp_parse_args(
			$args,
			[
				'singular' => 'attendee',
				'plural'   => 'attendees',
				'ajax'     => true,
				'screen'   => $screen,
			]
		);

		$this->per_page_option = \Tribe__Tickets__Admin__Screen_Options__Attendees::$per_page_user_option;

		if ( ! is_null( $screen ) ) {
			$screen->add_option(
				'per_page',
				[
					'label'  => __( 'Number of attendees per page:', 'event-tickets' ),
					'option' => $this->per_page_option,
				]
			);
		}

		// Fetch the event Object.
		if ( ! empty( $_GET['event_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->event = get_post( absint( $_GET['event_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		parent::__construct( apply_filters( 'tribe_events_tickets_attendees_table_args', $args ) );
	}

	/**
	 * Enqueues the JS and CSS for the attendees page in the admin
	 *
	 * @since 5.2.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_assets( $hook ) {
		/**
		 * Filter the Page Slugs the Attendees Page CSS and JS Loads
		 *
		 * @param array array( $this->page_id ) an array of admin slugs
		 */
		if ( ! in_array( $hook, apply_filters( 'tribe_filter_attendee_page_slug', [ $this->page_id ] ) ) ) {
			return;
		}

		$tickets_main = tribe( 'tickets.main' );

		tribe_asset(
			$tickets_main,
			'tickets-report-css',
			'tickets-report.css',
			[],
			null,
			[]
		);

		tribe_asset(
			$tickets_main,
			'tickets-report-print-css',
			'tickets-report-print.css',
			[],
			null,
			[
				'media' => 'print',
			]
		);

		tribe_asset(
			$tickets_main,
			'tickets-commerce-report-attendees',
			'tickets-attendees.js',
			[ 'jquery' ],
			null,
			[]
		);

		tribe_asset_enqueue( 'tickets-report-css' );
		tribe_asset_enqueue( 'tickets-report-print-css' );
		tribe_asset_enqueue( 'tickets-commerce-report-attendees' );

		add_thickbox();

		$move_url_args = [
			'dialog'    => \Tribe__Tickets__Main::instance()->move_tickets()->dialog_name(),
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
		 * @since 5.2.0
		 *
		 * @param array $config_data List of configuration data to be localized.
		 */
		$config_data = apply_filters( 'tribe_tickets_attendees_report_js_config', $config_data );

		wp_localize_script( $this->slug() . '-js', 'Attendees', $config_data );
	}

	/**
	 * Loads the WP-Pointer for the Attendees screen
	 *
	 * @since 5.2.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function load_pointers( $hook ) {

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		$pointer   = [];

		if ( version_compare( get_bloginfo( 'version' ), '3.3', '>' ) && ! in_array( 'attendees_filters', $dismissed ) ) {
			$pointer = [
				'pointer_id' => 'attendees_filters',
				'target'     => '#screen-options-link-wrap',
				'options'    => [
					'content'  => sprintf( '<h3> %s </h3> <p> %s </p>', esc_html__( 'Columns', 'event-tickets' ), esc_html__( 'You can use Screen Options to select which columns you want to see. The selection works in the table below, in the email, for print and for the CSV export.', 'event-tickets' ) ),
					'position' => [
						'edge'  => 'top',
						'align' => 'right',
					],
				],
			];
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_style( 'wp-pointer' );
		}

		wp_localize_script( $this->slug() . '-js', 'AttendeesPointer', $pointer );
	}

	/**
	 * Returns the  list of columns.
	 *
	 * @since 5.2.0
	 * @return array An associative array in the format [ <slug> => <title> ]
	 */
	public function get_columns() {
		$columns = [
			'cb'            => __( 'Checkbox', 'event-tickets' ),
			'ticket'        => __( 'Ticket', 'event-tickets' ),
			'primary_info'  => __( 'Primary Information', 'event-tickets' ),
			'meta_details'  => __( 'Details', 'event-tickets' ),
			'security_code' => __( 'Security Code', 'event-tickets' ),
			'status'        => __( 'Status', 'event-tickets' ),
			'check_in'      => __( 'Check In', 'event-tickets' ),
		];

		return $columns;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 5.2.0
	 */
	public function prepare_items() {
		$this->legacy_attendees_table->process_actions();

		$post_id = tribe_get_request_var( 'post_id', 0 );
		$post_id = tribe_get_request_var( 'event_id', $post_id );

		$this->post_id = $post_id;

		$search = tribe_get_request_var( $this->search_box_input_name );
		$page   = absint( tribe_get_request_var( 'paged', 0 ) );

		$arguments = [
			'page'               => $page,
			'posts_per_page'     => $this->per_page_option,
			'return_total_found' => true,
		];

		if ( ! empty( $search ) ) {
			$arguments['search'] = $search;

			$search_keys = array_keys( $this->get_search_options() );

			/**
			 * Filters the item keys that can be used to filter attendees while searching them.
			 *
			 * @since 5.2.0
			 * @since 5.2.0
			 *
			 * @param array  $search_keys The keys that can be used to search attendees.
			 * @param array  $items       (deprecated) The attendees list.
			 * @param string $search      The current search string.
			 */
			$search_keys = apply_filters( 'tribe_tickets_search_attendees_by', $search_keys, [], $search );

			// Default selection.
			$search_key = 'purchaser_name';

			$search_type = sanitize_text_field( tribe_get_request_var( 'tribe_attendee_search_type' ) );

			if (
				$search_type
				&& in_array( $search_type, $search_keys, true )
			) {
				$search_key = $search_type;
			}

			$search_like_keys = [
				'purchaser_name',
				'purchaser_email',
				'holder_name',
				'holder_email',
			];

			/**
			 * Filters the item keys that support LIKE matching to filter attendees while searching them.
			 *
			 * @since 5.2.0
			 *
			 * @param array  $search_like_keys The keys that support LIKE matching.
			 * @param array  $search_keys      The keys that can be used to search attendees.
			 * @param string $search           The current search string.
			 */
			$search_like_keys = apply_filters( 'tribe_tickets_search_attendees_by_like', $search_like_keys, $search_keys, $search );

			// Update search key if it supports LIKE matching.
			if ( in_array( $search_key, $search_like_keys, true ) ) {
				$search_key .= '__like';
				$search      = '%' . $search . '%';
			}

			// Only get matches that have search phrase in the key.
			$arguments['by'] = [
				$search_key => [
					$search,
				],
			];
		}

		if ( ! empty( $post_id ) ) {
			$arguments['events'] = $post_id;
		}

		$item_data = \Tribe__Tickets__Tickets::get_event_attendees_by_args( $post_id, $arguments );

		$this->items = array_map(
			function ( $attendee ) {
				$attendee = new \WP_Post( (object) $attendee );

				return tribe( Attendee::class )->get_attendee( $attendee );
			},
			$item_data['attendees']
		);

		// $this->items = $item_data['attendees'];

		$pagination_args = [
			'total_items' => count( $this->items ),
			'per_page'    => $this->per_page_option,
		];

		if ( ! empty( $this->items ) ) {
			$pagination_args['total_items'] = count( $this->items );
		}

		$this->set_pagination_args( $pagination_args );
	}

	/**
	 * Get the allowed search types and their descriptions.
	 *
	 * @see   \Tribe__Tickets__Attendee_Repository::__construct() List of valid ORM args.
	 *
	 * @since 5.2.0
	 *
	 * @return array
	 */
	private function get_search_options() {
		return [
			'purchaser_name'  => esc_html_x( 'Search by Purchaser Name', 'Attendees Table search options', 'event-tickets' ),
			'purchaser_email' => esc_html_x( 'Search by Purchaser Email', 'Attendees Table search options', 'event-tickets' ),
			'holder_name'     => esc_html_x( 'Search by Ticket Holder Name', 'Attendees Table search options', 'event-tickets' ),
			'holder_email'    => esc_html_x( 'Search by Ticket Holder Email', 'Attendees Table search options', 'event-tickets' ),
			'user'            => esc_html_x( 'Search by User ID', 'Attendees Table search options', 'event-tickets' ),
			'order_status'    => esc_html_x( 'Search by Order Status', 'Attendees Table search options', 'event-tickets' ),
			'order'           => esc_html_x( 'Search by Order ID', 'Attendees Table search options', 'event-tickets' ),
			'security_code'   => esc_html_x( 'Search by Security Code', 'Attendees Table search options', 'event-tickets' ),
			'ID'              => esc_html( sprintf( _x( 'Search by %s ID', 'Attendees Table search options', 'event-tickets' ), tribe_get_ticket_label_singular( 'attendees_table_search_box_ticket_id' ) ) ),
			'product_id'      => esc_html_x( 'Search by Product ID', 'Attendees Table search options', 'event-tickets' ),
		];
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 */
	public function single_row( $item ) {
		$checked = '';
		if ( 1 === (int) $item->check_in ) {
			$checked = ' tickets_checked ';
		}

		$status = 'complete';
		if ( ! empty( $item->order_status ) ) {
			$status = $item->order_status;
		}

		echo '<tr class="' . esc_attr( $checked . $status ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';

		/**
		 * Hook to allow for the insertion of data after an attendee table row.
		 *
		 * @var $item array of an Attendee's data
		 */
		do_action( 'event_tickets_attendees_table_after_row', (array) $item );
	}

	/**
	 * Handler for the columns that don't have a specific column_{name} handler function.
	 *
	 * @param \WP_Post $item   row object.
	 * @param string   $column the column name.
	 *
	 * @return string
	 */
	public function column_default( $item, $column ) {
		$value = empty( $item->{$column} ) ? '' : $item->{$column};

		return apply_filters( 'tribe_events_tickets_attendees_table_column', $value, (array) $item, $column );
	}

	/**
	 * Content for the ticket column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function column_ticket( $item ) {
		$unique_id   = tribe( Attendee::class )->get_unique_id( $item );
		$ticket      = get_post( tribe( Attendee::class )->get_ticket_id( $item ) );
		$dash        = '';
		$title       = $ticket->post_title;
		$attendee_id = ! empty( $item->attendee_id ) ? $item->attendee_id : $item->ID;

		if ( ! empty( $title ) ) {
			$dash = ' &ndash; ';
		}

		$output[] = sprintf(
			'<div class="event-tickets-ticket-name">%1$s [#%2$d]%3$s %4$s</div>',
			esc_html( $unique_id ),
			(int) $attendee_id,
			esc_html( $dash ),
			esc_html( $title )
		);

		$output[] = $this->get_row_actions( $item );

		/**
		 * Hook to allow for the insertion of additional content in the ticket table cell
		 *
		 * @param \WP_Post $item row object.
		 */
		do_action( 'event_tickets_attendees_table_ticket_column', $item );

		return implode( '', $output );
	}

	/**
	 * Content for the primary info column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function column_primary_info( $item ) {

		$name  = $item->holder_name ? $item->holder_name : '';
		$email = $item->holder_email ? $item->holder_email : '';

		return sprintf(
			'
				<div class="purchaser_name">%1$s</div>
				<div class="purchaser_email">%2$s</div>
			',
			esc_html( $name ),
			esc_html( $email )
		);
	}

	/**
	 * Content for the security code column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function column_security_code( $item ) {
		$security_code = tribe( Attendee::class )->get_security_code( $item );

		return esc_html( $security_code );
	}

	/**
	 * Content for the status column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		if ( $item->is_legacy_attendee ) {
			return $this->legacy_attendees_table->column_status( (array) $item );
		}

		return tribe( Attendee::class )->get_status_label( $item );
	}

	/**
	 * Content for the check in column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return false|string
	 */
	public function column_check_in( $item ) {
		return $this->legacy_attendees_table->column_check_in( (array) $item );
	}

	/**
	 * Content for the checkbox column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		$provider = ! empty( $item->provider ) ? $item->provider : null;

		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', esc_attr( $this->_args['singular'] ), esc_attr( $item->attendee_id . '|' . $provider ) );
	}

	/**
	 * Adds a set of default row actions to each item in the attendee list table.
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $item row object.
	 *
	 * @return string
	 */
	public function get_row_actions( $item ) {
		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		if ( ! $attendees->user_can_manage_attendees( 0, $this->event_id ) ) {
			return '';
		}

		$default_actions = [];
		$provider        = ! empty( $item->provider ) ? $item->provider : null;
		$not_going       = empty( $item->order_status ) || 'no' === $item->order_status || 'cancelled' === $item->order_status || 'refunded' === $item->order_status;

		if ( ! $not_going ) {
			$default_actions[] = sprintf(
				'<span class="inline">
					<a href="#" class="tickets_checkin" data-attendee-id="%1$d" data-event-id="%2$d" data-provider="%3$s">' . esc_html_x( 'Check In', 'row action', 'event-tickets' ) . '</a>
					<a href="#" class="tickets_uncheckin" data-attendee-id="%1$d" data-event-id="%2$d" data-provider="%3$s">' . esc_html_x( 'Undo Check In', 'row action', 'event-tickets' ) . '</a>
				</span>',
				esc_attr( $item->attendee_id ),
				esc_attr( $this->event_id ),
				esc_attr( $provider )
			);
		}

		if ( is_admin() ) {
			$default_actions[] = '<span class="inline move-ticket"> <a href="#">' . esc_html_x( 'Move', 'row action', 'event-tickets' ) . '</a> </span>';
		}

		$attendee = esc_attr( $item->attendee_id . '|' . $provider );
		$nonce    = wp_create_nonce( 'do_item_action_' . $attendee );

		$delete_url = esc_url(
			add_query_arg(
				[
					'action'   => 'delete_attendee',
					'nonce'    => $nonce,
					'attendee' => $attendee,
				]
			)
		);

		$default_actions[] = '<span class="trash"><a href="' . $delete_url . '">' . esc_html_x( 'Delete', 'row action', 'event-tickets' ) . '</a></span>';

		$default_actions = apply_filters( 'event_tickets_attendees_table_row_actions', $default_actions, (array) $item );

		$row_actions = implode( ' | ', $default_actions );

		return empty( $row_actions ) ? '' : '<div class="row-actions">' . $row_actions . '</div>';
	}

	/**
	 * Displays the search box.
	 *
	 * @since 5.2.0
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {
		return $this->legacy_attendees_table->search_box( $text, $input_id );
	}

	/**
	 * Retrieves the list of bulk actions available for this table.
	 *
	 * @since 5.2.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return $this->legacy_attendees_table->get_bulk_actions();
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @since 5.2.0
	 *
	 * @param string $which the control name.
	 */
	public function extra_tablenav( $which ) {
		return $this->legacy_attendees_table->extra_tablenav( $which );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 5.2.0
	 */
	public function no_items() {
		esc_html_e( 'No matching attendees found.', 'event-tickets' );
	}

	/**
	 * Overrides the list of CSS classes for the WP_List_Table table tag.
	 * This function is not hookable in core, so it needs to be overridden!
	 *
	 * @since 5.2.0
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		$classes = [ 'widefat', 'striped', 'attendees', 'tribe-attendees' ];

		if ( is_admin() ) {
			$classes[] = 'fixed';
		}

		/**
		 * Filters the default classes added to the attendees report `WP_List_Table`.
		 *
		 * @since 4.10.7
		 *
		 * @param array $classes The array of classes to be applied.
		 */
		$classes = apply_filters( 'tribe_tickets_attendees_table_classes', $classes );

		return $classes;
	}

}
