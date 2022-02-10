<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/**
 * Class Tribe__Tickets__Attendees_Table
 *
 * See documentation for WP_List_Table
 */
class Tribe__Tickets__Attendees_Table extends WP_List_Table {

	/**
	 * Store a possible Event.
	 *
	 * @var mixed $event
	 */
	public $event = false;

	/**
	 * The name (what gets submitted to the server) of our search box input.
	 *
	 * @var string $search_box_input_name
	 */
	private $search_box_input_name = 's';

	/**
	 * The user option that will be used to store the number of attendees per page to show.
	 *
	 * @var string $per_page_option
	 */
	protected $per_page_option;

	/**
	 * Class constructor
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args additional arguments/overrides
	 *
	 */
	public function __construct( $args = [] ) {
		/**
		 * This class' parent defaults to 's', but we want to change that on the front-end (e.g. Community) to avoid
		 * the possibility of triggering the theme's Search template.
		 */
		if ( ! is_admin() ) {
			$this->search_box_input_name = 'search';
		}

		$screen = get_current_screen();

		$args = wp_parse_args( $args, [
			'singular' => 'attendee',
			'plural'   => 'attendees',
			'ajax'     => true,
			'screen'   => $screen,
		] );

		$this->per_page_option = Tribe__Tickets__Admin__Screen_Options__Attendees::$per_page_user_option;

		if ( ! is_null( $screen ) ) {
			$screen->add_option( 'per_page', [
				'label'  => __( 'Number of attendees per page:', 'event-tickets' ),
				'option' => $this->per_page_option,
			] );
		}

		// Fetch the event Object
		if ( ! empty( $_GET['event_id'] ) ) {
			$this->event = get_post( absint( $_GET['event_id'] ) );
		}

		parent::__construct( apply_filters( 'tribe_events_tickets_attendees_table_args', $args ) );
	}

	/**
	 * Overrides the list of CSS classes for the WP_List_Table table tag.
	 * This function is not hookable in core, so it needs to be overridden!
	 *
	 * @since 4.10.7
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

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	public function get_table_columns() {
		$columns = [
			'cb'           => '<input type="checkbox" />',
			'ticket'       => esc_html( tribe_get_ticket_label_singular( 'attendee_table_column' ) ),
			'primary_info' => esc_html_x( 'Primary Information', 'attendee table', 'event-tickets' ),
			'security'     => esc_html_x( 'Security Code', 'attendee table', 'event-tickets' ),
			'status'       => esc_html_x( 'Status', 'attendee table', 'event-tickets' ),
			'check_in'     => esc_html_x( 'Check in', 'attendee table', 'event-tickets' ),
		];

		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		if ( $attendees->user_can_manage_attendees( 0, $this->event->ID ) ) {
			$columns['check_in'] = esc_html_x( 'Check in', 'attendee table', 'event-tickets' );
		}

		/**
		 * Controls the columns rendered within the attendee screen.
		 *
		 * @param array $columns
		 */
		return apply_filters( 'tribe_tickets_attendee_table_columns', $columns );
	}

	/**
	 * Checks the current user's permissions
	 */
	public function ajax_user_can() {
		return current_user_can( get_post_type_object( $this->screen->post_type )->cap->edit_posts );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->get_table_columns();
	}

	/**
	 * Handler for the columns that don't have a specific column_{name} handler function.
	 *
	 * @param $item
	 * @param $column
	 *
	 * @return string
	 */
	public function column_default( $item, $column ) {
		$value = '';

		// @todo Move the attendee meta bit into ET+ using the filter below.
		if ( ! empty( $item[ $column ] ) ) {
			$value = $item[ $column ];
		} elseif ( ! empty( $item['attendee_meta'][ $column ] ) ) {
			$value = $item['attendee_meta'][ $column ];
		}

		return apply_filters( 'tribe_events_tickets_attendees_table_column', $value, $item, $column );
	}

	/**
	 * Handler for the checkbox column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		$provider = ! empty( $item['provider'] ) ? $item['provider'] : null;

		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', esc_attr( $this->_args['singular'] ), esc_attr( $item['attendee_id'] . '|' . $provider ) );
	}

	/**
	 * Populates the purchaser column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_primary_info( array $item ) {
		$name  = '';
		$email = '';

		if ( ! empty( $item['holder_name'] ) ) {
			$name = $item['holder_name'];
		} elseif ( ! empty( $item['purchaser_name'] ) ) {
			$name = $item['purchaser_name'];
		}

		if ( ! empty( $item['holder_email'] ) ) {
			$email = $item['holder_email'];
		} elseif ( ! empty( $item['purchaser_email'] ) ) {
			$email = $item['purchaser_email'];
		}

		$output = sprintf(
			'
				<div class="purchaser_name">%1$s</div>
				<div class="purchaser_email">%2$s</div>
			',
			esc_html( $name ),
			esc_html( $email )
		);

		/**
		 * Provides an opportunity to modify the Primary Info column content in
		 * the attendees table.
		 *
		 * @since 4.5.2
		 *
		 * @param string $output
		 * @param array  $item
		 */
		return apply_filters( 'event_tickets_attendees_table_primary_info_column', $output, $item );
	}

	/**
	 * Populates the status column.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_status( array $item ) {
		$icon    = '';
		$warning = false;

		// Check if the order_warning flag has been set (to indicate the order has been cancelled, refunded etc)
		if ( isset( $item['order_warning'] ) && $item['order_warning'] ) {
			$warning = true;
		}

		// If the warning flag is set, add the appropriate icon
		if ( $warning ) {
			$icon = sprintf( "<span class='warning'><img src='%s'/></span> ", esc_url( Tribe__Tickets__Main::instance()->plugin_url . 'src/resources/images/warning.png' ) );
		}

		if ( empty( $item['order_status'] ) ) {
			$item['order_status'] = '';
		}

		$label = isset( $item['order_status_label'] ) ? $item['order_status_label'] : ucwords( $item['order_status'] );

		$order_id_url = $this->get_order_id_url( $item );

		if ( ! empty( $order_id_url ) && ! empty( $item['order_id'] ) ) {
			$label = '<a href="' . esc_url( $order_id_url ) . '">#' . esc_html( $item['order_id'] ) . ' &ndash; ' . $label . '</a>';
		}

		/**
		 * Provides an opportunity to modify the order status text within
		 * the attendees table.
		 *
		 * @param string $order_status_html
		 * @param array  $item
		 */
		return apply_filters( 'tribe_tickets_attendees_table_order_status', $icon . $label, $item );
	}

	/**
	 * Retrieves the order id for the specified table row item.
	 *
	 * In some cases, such as when the current item belongs to the RSVP provider, an
	 * empty string may be returned as there is no order screen that can be linekd to.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function get_order_id_url( array $item ) {
		// Backwards compatibility
		if ( empty( $item['order_id_url'] ) ) {
			$item['order_id_url'] = get_edit_post_link( $item['order_id'], true );
		}

		return $item['order_id_url'];
	}

	/**
	 * Handler for the ticket column.
	 *
	 * @since  4.1
	 * @since  4.12.1 Include the raw attendee Post ID in the ticket's ID.
	 *
	 * @param array $item Item whose ticket data should be output.
	 *
	 * @return string
	 */
	public function column_ticket( array $item ) {
		ob_start();

		$attendee_id = trim( $this->get_attendee_id( $item ) );

		if (
			! empty( $attendee_id )
			&& ! empty( $item['attendee_id'] )
		) {
			// Purposefully not forcing strict check here.
			// phpcs:ignore
			if ( $attendee_id != $item['attendee_id'] ) {
				$attendee_id .= sprintf( ' [#%d]', $item['attendee_id'] );
			} else {
				$attendee_id = sprintf( '[#%s]', $attendee_id );
			}
		}

		$attendee_id = esc_html( $attendee_id );

		if ( ! empty( $attendee_id ) ) {
			$attendee_id .= ' &ndash; ';
		}

		?>
		<div class="event-tickets-ticket-name">
			<?php echo $attendee_id; ?>
			<?php echo esc_html( $item['ticket'] ); ?>
		</div>

		<?php
		echo $this->get_row_actions( $item );

		/**
		 * Hook to allow for the insertion of additional content in the ticket table cell
		 *
		 * @var array $item Attendee row item
		 */
		do_action( 'event_tickets_attendees_table_ticket_column', $item );

		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Generates and returns the attendee table row actions.
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	protected function get_row_actions( array $item ) {
		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		if ( ! $attendees->user_can_manage_attendees( 0, $this->event->ID ) ) {
			return '';
		}

		$default_row_actions = $this->add_default_row_actions( [], $item );

		/**
		 * Sets the row action links that display within the ticket column of the
		 * attendee list table.
		 *
		 * @param array $row_actions
		 * @param array $item
		 */
		$row_actions = (array) apply_filters( 'event_tickets_attendees_table_row_actions', $default_row_actions, $item );
		$row_actions = join( ' | ', $row_actions );

		return empty( $row_actions ) ? '' : '<div class="row-actions">' . $row_actions . '</div>';
	}

	/**
	 * Adds a set of default row actions to each item in the attendee list table.
	 *
	 * @param array $row_actions
	 * @param array $item
	 *
	 * @return array
	 */
	public function add_default_row_actions( array $row_actions, array $item ) {
		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		if ( ! $attendees->user_can_manage_attendees( 0, $this->event->ID ) ) {
			return $row_actions;
		}

		$default_actions = [];
		$provider        = ! empty( $item['provider'] ) ? $item['provider'] : null;
		$not_going       = empty( $item['order_status'] ) || $item['order_status'] === 'no' || 'cancelled' === $item['order_status'] || 'refunded' === $item['order_status'];

		if ( is_object( $this->event ) && isset( $this->event->ID ) && ! $not_going ) {
			$default_actions[] = sprintf(
				'<span class="inline">
					<a href="#" class="tickets_checkin" data-attendee-id="%1$d" data-event-id="%2$d" data-provider="%3$s">' . esc_html_x( 'Check In', 'row action', 'event-tickets' ) . '</a>
					<a href="#" class="tickets_uncheckin" data-attendee-id="%1$d" data-event-id="%2$d" data-provider="%3$s">' . esc_html_x( 'Undo Check In', 'row action', 'event-tickets' ) . '</a>
				</span>',
				esc_attr( $item['attendee_id'] ),
				esc_attr( $this->event->ID ),
				esc_attr( $provider )
			);
		}

		if ( is_admin() ) {
			$default_actions[] = '<span class="inline move-ticket"> <a href="#">' . esc_html_x( 'Move', 'row action', 'event-tickets' ) . '</a> </span>';
		}

		$attendee = esc_attr( $item['attendee_id'] . '|' . $provider );
		$attendee = str_replace( '\\', '', $attendee );
		$nonce    = wp_create_nonce( 'do_item_action_' . $attendee );

		$delete_url = esc_url( add_query_arg( [
			'action'   => 'delete_attendee',
			'nonce'    => $nonce,
			'attendee' => $attendee,
		] ) );

		$default_actions[] = '<span class="trash"><a href="' . $delete_url . '">' . esc_html_x( 'Delete', 'row action', 'event-tickets' ) . '</a></span>';

		return array_merge( $row_actions, $default_actions );
	}

	/**
	 * Returns the attendee ID (or "unique ID" if set).
	 *
	 * @param array $item
	 *
	 * @return int|string
	 */
	public function get_attendee_id( $item ) {
		$attendee_id = empty( $item['attendee_id'] ) ? '' : $item['attendee_id'];
		if ( $attendee_id === '' ) {
			return '';
		}

		$unique_id = get_post_meta( $attendee_id, '_unique_id', true );

		if ( $unique_id === '' ) {
			$unique_id = $attendee_id;
		}

		/**
		 * Filters the ticket number; defaults to the ticket unique ID.
		 *
		 * @param string $unique_id A unique string identifier for the ticket.
		 * @param array  $item      The item entry.
		 */
		return apply_filters( 'tribe_events_tickets_attendees_table_attendee_id_column', $unique_id, $item );
	}

	/**
	 * Handler for the check in column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_check_in( $item ) {

		if ( ! tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $this->event->ID ) ) {
			return false;
		}

		$default_checkin_stati = [];
		$provider_slug         = ! empty( $item['provider_slug'] ) ? $item['provider_slug'] : null;
		$order_id              = $item['order_id'];
		$provider              = ! empty( $item['provider'] ) ? $item['provider'] : null;

		/**
		 * Filters the order stati that will allow for a ticket to be checked in for all commerce providers.
		 *
		 * @since 4.1
		 *
		 * @param array  $default_checkin_stati An array of default order stati that will make a ticket eligible for check-in.
		 * @param string $provider_slug         The ticket provider slug.
		 * @param int    $order_id              The order post ID.
		 */
		$check_in_stati = apply_filters( 'event_tickets_attendees_checkin_stati', $default_checkin_stati, $provider_slug, $order_id );

		/**
		 * Filters the order stati that will allow for a ticket to be checked in for a specific commerce provider.
		 *
		 * @since 4.1
		 *
		 * @param array $default_checkin_stati An array of default order stati that will make a ticket eligible for check-in.
		 * @param int   $order_id              The order post ID.
		 */
		$check_in_stati = apply_filters( "event_tickets_attendees_{$provider_slug}_checkin_stati", $check_in_stati, $order_id );

		if (
			! empty( $item['order_status'] )
			&& ! empty( $item['order_id_link_src'] )
			&& is_array( $check_in_stati )
			&& ! in_array( $item['order_status'], $check_in_stati )
		) {
			$button_template = '<a href="%s" class="button-secondary tickets-checkin">%s</a>';

			return sprintf( $button_template, $item['order_id_link_src'], __( 'View order', 'event-tickets' ) );
		}

		$context = [
			'item'            => $item,
			'attendee_table'  => $this,
			'provider'        => $provider,
			'disable_checkin' => empty( $item['order_status'] ) || ! in_array( $item['order_status'], $check_in_stati, true ),
		];

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		$admin_views->template( 'attendees-table/check-in-button', $context );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @param object $item The current item
	 */
	public function single_row( $item ) {
		$checked = '';
		if ( ( (int) $item['check_in'] ) === 1 ) {
			$checked = ' tickets_checked ';
		}

		$status = 'complete';
		if ( ! empty( $item['order_status'] ) ) {
			$status = $item['order_status'];
		}

		echo '<tr class="' . esc_attr( $checked . $status ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';

		/**
		 * Hook to allow for the insertion of data after an attendee table row.
		 *
		 * @var $item array of an Attendee's data
		 */
		do_action( 'event_tickets_attendees_table_after_row', $item );
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * Used for the Print, Email and Export buttons, and for the jQuery based search.
	 *
	 * @see WP_List_Table::display()
	 *
	 * @param string $which (top|bottom)
	 */
	public function extra_tablenav( $which ) {

		// Bail early if user is not owner/have permissions
		if ( ! tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $this->event->ID ) ) {
			return;
		}

		/**
		 * Include TB_iframe JS
		 */
		add_thickbox();

		$parent = 'admin.php';

		/**
		 * Filtert to show email form for non-admins.
		 *
		 * @since 4.10.1
		 *
		 * @param boolean
		 */
		$allow_fe = apply_filters( 'tribe_allow_admin_on_frontend', false );
		if ( $allow_fe && ! is_admin() ) {
			global $wp;
			$parent = untrailingslashit( $wp->request ) . '/';
		}

		$email_link = Tribe__Settings::instance()->get_url( [
			'page'      => 'tickets-attendees',
			'action'    => 'email',
			'event_id'  => $this->event->ID,
			'TB_iframe' => true,
			'width'     => 410,
			'height'    => 300,
			'parent'    => $parent,
		] );

		if ( $allow_fe && ! is_admin() ) {
			$email_link = str_replace( '/wp-admin/', '/', $email_link );
			$email_link = add_query_arg(
				[
					'page'      => null,
					'post_type' => null,
				],
				$email_link
			);
		}

		$nav = [
			'left'  => [
				'print'  => sprintf( '<input type="button" name="print" class="print button action" value="%s">', esc_attr__( 'Print', 'event-tickets' ) ),
				'export' => sprintf( '<a target="_blank" href="%s" class="export button action" rel="noopener noreferrer">%s</a>', esc_url( tribe( 'tickets.attendees' )->get_export_url() ), esc_html__( 'Export', 'event-tickets' ) ),
			],
			'right' => [],
		];

		// Only show the email button if the user is an admin, or we've enableds it via the filter.
		if ( current_user_can( 'edit_posts' ) || $allow_fe ) {
			$nav['left']['email'] = sprintf( '<a class="email button action thickbox" href="%1$s">%2$s</a>', esc_url( $email_link ), esc_html__( 'Email', 'event-tickets' ) );
		}

		/**
		 * Allows for customization of the buttons/options available above and below the Attendees table.
		 *
		 * @param array  $nav   The array of items in the nav, where keys are the name of the item and values are the HTML of the buttons/inputs.
		 * @param string $which Either 'top' or 'bottom'; the location of the current nav items being filtered.
		 */
		$nav = apply_filters( 'tribe_events_tickets_attendees_table_nav', $nav, $which );

		?>
		<div class="alignleft actions attendees-actions"><?php echo implode( $nav['left'] ); ?></div>
		<div class="alignright attendees-filter"><?php echo implode( $nav['right'] ) ?></div>
		<?php
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [];

		if ( tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $this->event->ID ) ) {
			$actions['delete_attendee'] = esc_attr__( 'Delete', 'event-tickets' );
			$actions['check_in']        = esc_attr__( 'Check in', 'event-tickets' );
			$actions['uncheck_in']      = esc_attr__( 'Undo Check in', 'event-tickets' );
		}

		return (array) apply_filters( 'tribe_events_tickets_attendees_table_bulk_actions', $actions );
	}

	/**
	 * Handler for the different bulk actions
	 */
	public function process_actions() {
		if ( ! $this->validate_action_nonce() ) {
			return;
		}

		switch ( $this->current_action() ) {
			case 'check_in':
				$this->do_check_in();
				break;
			case 'uncheck_in':
				$this->do_uncheck_in();
				break;
			case 'delete_attendee':
				$this->do_delete();
				break;
			default:
				/**
				 * Allow for customizing the generic/default action to perform on selected Attendees.
				 *
				 * @param $current_action The action currently being done on the selection of Attendees.
				 */
				do_action( 'tribe_events_tickets_attendees_table_process_bulk_action', $this->current_action() );
				break;
		}
	}

	/**
	 * Indicates if a valid nonce was set for the currently requested bulk or
	 * individual action.
	 *
	 * @return bool
	 */
	protected function validate_action_nonce() {
		// If a bulk action request was posted
		if ( ! empty( $_POST['attendee'] ) && $_POST['attendee'] && wp_verify_nonce( $_POST['_wpnonce'], 'bulk-attendees' ) ) {
			return true;
		}

		// If an individual action was requested
		if ( ! empty( $_GET['attendee'] ) && $_GET['attendee'] && wp_verify_nonce( $_GET['nonce'], 'do_item_action_' . $_GET['attendee'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Returns the list of attendee/tickets for the current bulk or individual
	 * action.
	 *
	 * The format is an array where the elements represent the ticket ID and
	 * provider in the following format:
	 *
	 *     [
	 *         '123|Ticket_Provider',
	 *         '4567|Ticket_Provider',
	 *     ]
	 *
	 * @return array
	 */
	protected function get_action_ids() {
		$action_ids = [];

		if ( isset( $_POST['attendee'] ) ) {
			$action_ids = (array) $_POST['attendee'];
		} elseif ( isset( $_GET['attendee'] ) ) {
			$action_ids = (array) $_GET['attendee'];
		}

		return $action_ids;
	}

	/**
	 * Get the Event ID ( Post ID ) of the Current Attendees Table
	 *
	 * @since 4.10.4
	 *
	 * @return int $event_id the event or post id for the attendee table
	 */
	protected function get_post_id() {

		$event_id = isset( $_GET['event_id'] ) ? $_GET['event_id'] : 0;

		//if not event_id try to use post_id
		$event_id = empty( $event_id ) && isset( $_GET['post_id'] ) ? $_GET['post_id'] : $event_id;

		return absint( $event_id );
	}


	/**
	 * Process the checking-in of selected attendees from the Attendees table.
	 */
	protected function do_check_in() {
		$attendee_ids = $this->get_action_ids();

		if ( ! $attendee_ids ) {
			return;
		}

		foreach ( $attendee_ids as $attendee ) {
			list( $id, $addon ) = $this->attendee_reference( $attendee );

			if ( false === $id ) {
				continue;
			}

			$addon->checkin( $id );
		}
	}

	/**
	 * Process the undoing of a check-in of selected attendees from the Attendees table.
	 */
	protected function do_uncheck_in() {
		$attendee_ids = $this->get_action_ids();

		if ( ! $attendee_ids ) {
			return;
		}

		foreach ( $attendee_ids as $attendee ) {

			list( $id, $addon ) = $this->attendee_reference( $attendee );

			if ( false === $id ) {
				continue;
			}

			$addon->uncheckin( $id );
		}
	}

	/**
	 * Process the deletion of selected attendees from the Attendees table.
	 *
	 * @since 4.10.4 add redirect after completing action
	 *
	 */
	protected function do_delete() {
		$attendee_ids = $this->get_action_ids();

		if ( ! $attendee_ids ) {
			return;
		}

		foreach ( $attendee_ids as $attendee ) {
			list( $id, $addon ) = $this->attendee_reference( $attendee );

			if ( false === $id ) {
				continue;
			}

			$addon->delete_ticket( null, $id );
		}

		// redirect after deleting attendees back to attendee url
		$post = get_post( $this->get_post_id() );
		if ( ! isset( $post->ID ) ) {
			return;
		}

		if ( headers_sent() ) {
			return;
		}

		$redirect_url = tribe( 'tickets.attendees' )->get_report_link( $post );
		wp_safe_redirect( $redirect_url );

		exit;
	}

	/**
	 * Returns the attendee ID and instance of the specific ticketing solution or "addon" used
	 * to handle it.
	 *
	 * This is used in the context of bulk actions where each attendee table entry is identified
	 * by a string of the pattern {id}|{ticket_class} - where possible this method turns that into
	 * an array consisting of the attendee object ID and the relevant ticketing object.
	 *
	 * If this cannot be determined, both array elements will be set to false.
	 *
	 * @param $reference
	 *
	 * @return array
	 */
	protected function attendee_reference( $reference ) {
		$failed = [ false, false ];

		if ( false === strpos( $reference, '|' ) ) {
			return $failed;
		}

		$parts = explode( '|', $reference );

		if ( count( $parts ) < 2 ) {
			return $failed;
		}

		$id = absint( $parts[0] );

		if ( $id <= 0 ) {
			return $failed;
		}

		if (
			tec_tickets_commerce_is_enabled()
			&& get_post_type( $id ) === \TEC\Tickets\Commerce\Attendee::POSTTYPE
		) {
			$addon = tribe( \TEC\Tickets\Commerce\Module::class );
		} else {
			$addon = call_user_func( [ $parts[1], 'get_instance' ] );

			if ( ! is_subclass_of( $addon, 'Tribe__Tickets__Tickets' ) ) {
				return $failed;
			}
		}


		return [ $id, $addon ];
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {
		$this->process_actions();

		$current_page = $this->get_pagenum();
		$per_page     = $this->get_items_per_page( $this->per_page_option );

		$pagination_args = [
			'total_items' => 0,
			'per_page'    => $per_page,
		];

		$args = [
			'page'               => $current_page,
			'per_page'           => $per_page,
			'return_total_found' => true,
		];

		$event_id = empty( $_GET['event_id'] ) ? 0 : absint( $_GET['event_id'] );

		$search = sanitize_text_field( tribe_get_request_var( $this->search_box_input_name ) );

		if ( ! empty( $search ) ) {
			$search_keys = array_keys( $this->get_search_options() );

			/**
			 * Filters the item keys that can be used to filter attendees while searching them.
			 *
			 * @since 4.7
			 * @since 4.10.6 Deprecated usage of $items attendees list.
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
			 * @since 4.10.6
			 *
			 * @param array  $search_like_keys The keys that support LIKE matching.
			 * @param array  $search_keys      The keys that can be used to search attendees.
			 * @param string $search           The current search string.
			 */
			$search_like_keys = apply_filters( 'tribe_tickets_search_attendees_by_like', $search_like_keys, $search_keys, $search );

			// Update search key if it supports LIKE matching.
			if ( in_array( $search_key, $search_like_keys, true ) ) {
				$search_key .= '__like';
				$search     = '%' . $search . '%';
			}

			// Only get matches that have search phrase in the key.
			$args['by'] = [
				$search_key => [
					$search,
				],
			];
		}

		$item_data = Tribe__Tickets__Tickets::get_event_attendees_by_args( $event_id, $args );

		$items = [];

		if ( ! empty( $item_data ) ) {
			$items = $item_data['attendees'];

			$pagination_args['total_items'] = $item_data['total_found'];
		}

		$this->items = $items;

		$this->set_pagination_args( $pagination_args );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 4.7
	 */
	public function no_items() {
		esc_html_e( 'No matching attendees found.', 'event-tickets' );
	}

	/**
	 * Get the allowed search types and their descriptions.
	 *
	 * @see   \Tribe__Tickets__Attendee_Repository::__construct() List of valid ORM args.
	 *
	 * @since 4.10.8
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
	 * {@inheritdoc}
	 */
	public function search_box( $text, $input_id ) {
		// Workaround to show the search box even when no items are found.
		$old_items   = $this->items;
		$this->items = [
			'Temporary',
		];

		// Get normal search box HTML so we can add our own inputs.
		ob_start();
		parent::search_box( $text, $input_id );
		$search_box = ob_get_clean();

		if ( ! is_admin() ) {
			// Give front-end (e.g. Community) a custom input name
			$search_box = str_replace( 'name="s"', 'name="' . esc_attr( $this->search_box_input_name ) . '"', $search_box );
			// And get its value upon reloading the page to display its search results so user knows what they searched for
			$search_box = str_replace( 'value=""', 'value="' . esc_attr( tribe_get_request_var( $this->search_box_input_name ) ) . '"', $search_box );
		}

		$this->items = $old_items;

		/**
		 * Filters the search types to be shown in the search box for filtering attendees.
		 *
		 * @since 4.10.6
		 *
		 * @param array $options List of ORM search types and their labels.
		 */
		$options = apply_filters( 'tribe_tickets_search_attendees_types', $this->get_search_options() );

		// Default selection.
		$selected = 'purchaser_name';

		$search_type = sanitize_text_field( tribe_get_request_var( 'tribe_attendee_search_type' ) );

		if (
			$search_type
			&& array_key_exists( $search_type, $options )
		) {
			$selected = $search_type;
		}

		$args = [
			'options'  => $options,
			'selected' => $selected,
		];

		// Get our search dropdown.

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		$custom_search = $admin_views->template( 'attendees-table-search', $args, false );

		// Add our search type dropdown before the search box input
		$search_box = str_replace( '<input type="search"', $custom_search . '<input type="search"', $search_box );

		echo $search_box;
	}
}
