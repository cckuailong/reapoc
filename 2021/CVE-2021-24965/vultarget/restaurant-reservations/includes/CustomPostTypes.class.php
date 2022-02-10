<?php
/**
 * Class to handle all custom post type definitions for Restaurant Reservations
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'rtbCustomPostTypes' ) ) {
class rtbCustomPostTypes {

	// Array of valid post statuses
	// @sa set_booking_statuses()
	public $booking_statuses = array();

	// Cached select fields for booking statuses
	public $status_select_html = array();

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'init', array( $this, 'load_cpts' ) );

		// Set up $booking_statuses array and register new post statuses
		add_action( 'init', array( $this, 'set_booking_statuses' ) );
		add_filter( 'rtb_post_statuses_args' , array( $this, 'add_arrived_status' ) );
		add_filter( 'rtb_post_statuses_args' , array( $this, 'add_cancelled_status' ) );
		add_filter( 'rtb_post_statuses_args' , array( $this, 'add_payment_failed_status' ) );

		// Display the count of pending bookings
		add_action( 'admin_footer', array( $this, 'show_pending_count' ) );

		// Maintain the count of pending bookings
		add_action( 'rtb_insert_booking', array( $this, 'update_pending_count' ) );
		add_action( 'rtb_update_booking', array( $this, 'update_pending_count' ) );
		add_action( 'transition_post_status', array( $this, 'maybe_update_pending_count' ), 999, 3 );

	}

	/**
	 * Initialize custom post types
	 * @since 0.1
	 */
	public function load_cpts() {
		global $rtb_controller;

		// Define the booking custom post type
		$args = array(
			'labels' => array(
				'name'               => __( 'Bookings',                   'restaurant-reservations' ),
				'singular_name'      => __( 'Booking',                    'restaurant-reservations' ),
				'menu_name'          => __( 'Bookings',                   'restaurant-reservations' ),
				'name_admin_bar'     => __( 'Bookings',                   'restaurant-reservations' ),
				'add_new'            => __( 'Add New',                 	  'restaurant-reservations' ),
				'add_new_item'       => __( 'Add New Booking',            'restaurant-reservations' ),
				'edit_item'          => __( 'Edit Booking',               'restaurant-reservations' ),
				'new_item'           => __( 'New Booking',                'restaurant-reservations' ),
				'view_item'          => __( 'View Booking',               'restaurant-reservations' ),
				'search_items'       => __( 'Search Bookings',            'restaurant-reservations' ),
				'not_found'          => __( 'No bookings found',          'restaurant-reservations' ),
				'not_found_in_trash' => __( 'No bookings found in trash', 'restaurant-reservations' ),
				'all_items'          => __( 'All Bookings',               'restaurant-reservations' ),
			),
			'menu_icon' => 'dashicons-calendar',
			'public' => false,
			'supports' => array(
				'title',
				'revisions'
			)
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'rtb_booking_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'rtb_booking_pre_register' );

		// Register the post type
		register_post_type( RTB_BOOKING_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'rtb_booking_post_register' );

		if ( $rtb_controller->permissions->check_permission( 'custom_fields' ) ) {
			// Define the field custom post type
			$args = array(
				'labels' => array(
					'name'               => __( 'Field',                    'custom-fields-for-rtb' ),
					'singular_name'      => __( 'Field',                    'custom-fields-for-rtb' ),
					'menu_name'          => __( 'Fields',                   'custom-fields-for-rtb' ),
					'name_admin_bar'     => __( 'Fields',                   'custom-fields-for-rtb' ),
					'add_new'            => __( 'Add Field',                'custom-fields-for-rtb' ),
					'add_new_item'       => __( 'Add New Field',            'custom-fields-for-rtb' ),
					'edit_item'          => __( 'Edit Field',               'custom-fields-for-rtb' ),
					'new_item'           => __( 'New Field',                'custom-fields-for-rtb' ),
					'view_item'          => __( 'View Field',               'custom-fields-for-rtb' ),
					'search_items'       => __( 'Search Fields',            'custom-fields-for-rtb' ),
					'not_found'          => __( 'No fields found',          'custom-fields-for-rtb' ),
					'not_found_in_trash' => __( 'No fields found in trash', 'custom-fields-for-rtb' ),
					'all_items'          => __( 'All Fields',               'custom-fields-for-rtb' ),
				),
				'public' => false
			);
	
			$args = apply_filters( 'cffrtb_field_post_type_args', $args );
	
			register_post_type( 'cffrtb_field', $args );
		}
	}

	/**
	 * Set an array of valid booking statuses and register any custom statuses
	 * @since 0.0.1
	 */
	public function set_booking_statuses() {

		$this->booking_statuses['pending'] = array(
			'label'						=> _x( 'Pending', 'Booking status when it is pending review', 'restaurant-reservations' ),
			'default'					=> true, // Whether or not this status is part of WP Core
			'user_selectable'	=> true, // Whether or not a user can set a booking to this status
			'label_count' 		=> _n_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>'),
		);

		$this->booking_statuses['confirmed'] = array (
			'label'                     => _x( 'Confirmed', 'Booking status for a confirmed booking', 'restaurant-reservations' ),
			'default'					=> false, // Whether or not this status is part of WP Core
			'user_selectable'			=> true, // Whether or not a user can set a booking to this status
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'restaurant-reservations' ),
		);

		$this->booking_statuses['closed'] = array(
			'label'                     => _x( 'Closed', 'Booking status for a closed booking', 'restaurant-reservations' ),
			'default'					=> false, // Whether or not this status is part of WP Core
			'user_selectable'			=> true, // Whether or not a user can set a booking to this status
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>', 'restaurant-reservations' )
		);

		// Let addons hook in to add/edit/remove post statuses
		$this->booking_statuses = apply_filters( 'rtb_post_statuses_args', $this->booking_statuses );

		// Register the custom post statuses
		foreach ( $this->booking_statuses as $status => $args ) {
			if ( $args['default'] === false ) {
				register_post_status( $status, $args );
			}
		}

	}


	/**
	* @since 2.1.0
	* Adds in a "Cancelled" status if the option to allow guest to cancel
	* their reservation has been toggled on.
	*/
	public function add_cancelled_status( $booking_statuses = array() ) {
		global $rtb_controller;

		if ( $rtb_controller->settings->get_setting( 'allow-cancellations' ) ) {
			$booking_statuses['cancelled'] = array(
				'label'                     => _x( 'Cancelled', 'The guest has cancelled their reservation themselves.', 'restaurant-reservations' ),
				'default'					=> false, // Whether or not this status is part of WP Core
				'user_selectable'			=> false, // Whether or not a user can set a booking to this status
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'restaurant-reservations' )
			);
		}

		return $booking_statuses;
	}


	/**
	* @since 2.0.0
	* Adds in an "Arrived" status if the option to check guests in on arrival 
	* has been toggled on.
	*/
	public function add_arrived_status( $booking_statuses = array() ) {
		global $rtb_controller;

		if ( $rtb_controller->settings->get_setting( 'view-bookings-arrivals' ) ) {
			$booking_statuses['arrived'] = array(
				'label'                     => _x( 'Arrived', 'The guests have arrived for their reservation', 'restaurant-reservations' ),
				'default'					=> false, // Whether or not this status is part of WP Core
				'user_selectable'			=> true, // Whether or not a user can set a booking to this status
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Arrived <span class="count">(%s)</span>', 'Arrived <span class="count">(%s)</span>', 'restaurant-reservations' )
			);
		}

		return $booking_statuses;
	}

	/**
	* @since 2.1.9
	* Adds in a "Payment Failed" status if the option to require deposits when
	* booking a reservation has been toggled on.
	*/
	public function add_payment_failed_status( $booking_statuses = array() ) {
		global $rtb_controller;

		if ( $rtb_controller->settings->get_setting( 'require-deposit' ) ) {
			$booking_statuses['payment_failed'] = array(
				'label'                     => _x( 'Payment Failed', 'The guest has tried to make a payment but it was declined.', 'restaurant-reservations' ),
				'default'					=> false, // Whether or not this status is part of WP Core
				'user_selectable'			=> false, // Whether or not a user can set a booking to this status
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Payment Failed <span class="count">(%s)</span>', 'Payment Failed <span class="count">(%s)</span>', 'restaurant-reservations' )
			);

			// This is an intermediate status when payment is pending
			$booking_statuses['payment_pending'] = array(
				'label'						=> _x( 'Payment Pending', 'The guest has booked but payment is pending', 'restaurant-reservations' ),
				'default'					=> false, // Whether or not this status is part of WP Core
				'user_selectable' => false, // Whether or not a user can set a booking to this status
				'public'          => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Payment Pending <span class="count">(%s)</span>', 'Payment Pending <span class="count">(%s)</span>', 'restaurant-reservations' )
			);
		}

		return $booking_statuses;
	}

	/**
	 * Print an HTML element to select a booking status
	 * @since 0.0.1
	 * @note This is no longer used in the bookings table, but it could be
	 *	useful in the future, so leave it in for now (0.0.1) until the plugin is
	 *	more fleshed out.
	 */
	public function print_booking_status_select( $current = false ) {

		if ( $current === false ) {
			$current = 'none';
		}

		// Output stored select field if available
		if ( !empty( $this->status_select_html[$current] ) ) {
			return $this->status_select_html[$current];
		}

		ob_start();
		?>

		<select name="rtb-select-status">
		<?php foreach ( $this->booking_statuses as $status => $args ) : ?>
			<?php if ( $args['user_selectable'] === true ) : ?>
			<option value="<?php echo esc_attr( $status ); ?>"<?php echo $status == $current ? ' selected="selected"' : ''; ?>><?php echo esc_attr( $args['label'] ); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
		</select>

		<?php
		$output = ob_get_clean();

		// Store output so we don't need to loop for every row
		$this->status_select_html[$current] = $output;

		return $output;

	}

	/**
	 * Delete a booking request (or send to trash)
	 *
	 * @since 0.0.1
	 */
	public function delete_booking( $id ) {

		$id = absint( $id );
		if ( !current_user_can( 'manage_bookings' ) ) {
			return false;
		}

		$booking = get_post( $id );

		if ( !$this->is_valid_booking_post_object( $booking ) ) {
			return false;
		}

		// If we're already looking at trashed posts, delete it for good.
		// Otherwise, just send it to trash.
		if ( !empty( $_GET['status'] ) && $_GET['status'] == 'trash' ) {
			$screen = get_current_screen();
			if ( $screen->base == 'toplevel_page_rtb-bookings' ) {
				$result = wp_delete_post( $id, true );
			}
		} else {
			$result = wp_trash_post( $id );
		}

		if ( $result === false ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Update a booking status.
	 * @since 0.0.1
	 */
	function update_booking_status( $id, $status ) {

		$id = absint( $id );
		if ( !current_user_can( 'manage_bookings' ) ) {
			return false;
		}

		if ( !$this->is_valid_booking_status( $status ) ) {
			return false;
		}

		$booking = get_post( $id );

		if ( !$this->is_valid_booking_post_object( $booking ) ) {
			return false;
		}

		if ( $booking->post_status === $status ) {
			return null;
		}

		$result = wp_update_post(
			array(
				'ID'			=> $id,
				'post_status'	=> $status,
				'edit_date'		=> current_time( 'mysql' ),
			)
		);

		return $result ? true : false;
	}

	/**
	 * Check if status is valid for bookings
	 * @since 0.0.1
	 */
	public function is_valid_booking_status( $status ) {
		return isset( $this->booking_statuses[$status] ) ? true : false;
	}

	/**
	 * Check if booking is a valid Post object with the correct post type
	 * @since 0.0.1
	 */
	public function is_valid_booking_post_object( $booking ) {
		return !is_wp_error( $booking ) && is_object( $booking ) && $booking->post_type == RTB_BOOKING_POST_TYPE;
	}

	/**
	 * Show the count of upcoming pending bookings in admin nav menu
	 *
	 * This is hooked to admin_footer to ensure that any actions on the page
	 * which might effect booking statuses have already fired, such as the
	 * bulk actions on the bookings page, which are processed after the nav
	 * menu has been loaded.
	 *
	 * @since 1.7.5
	 */
	public function show_pending_count() {

		global $rtb_controller;
		$rtb_controller->cpts->update_pending_count();
		$pending_count = get_option( 'rtb_pending_count', 0 );

		if ( !$pending_count ) {
			return;
		}

		$pending_bubble = ' <span class="update-plugins count-' . (int) $pending_count . '">' .
				'<span class="plugin-count" aria-hidden="true">' . (int) $pending_count . '</span></span>';

		?>

		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$( '#toplevel_page_rtb-bookings > a .wp-menu-name' ).append( '<?php echo $pending_bubble; ?>' );
			});
		</script>

		<?php
	}

	/**
	 * Update the count of upcoming pending bookings
	 *
	 * This is hooked to fire whenever a booking is added or updated. But it can
	 * also be called directly once to set the initial option.
	 *
	 * @param rtbBooking $booking Optional. The booking being added or updated.
	 * @since 1.7.5
	 */
	public function update_pending_count( $booking = null ) {

		global $wpdb;
		$current_date_time = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - HOUR_IN_SECONDS );
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(ID) FROM {$wpdb->prefix}posts WHERE post_type=%s AND post_status='pending' AND post_date>=%s;",
				RTB_BOOKING_POST_TYPE,
				$current_date_time
			)
		);

		update_option( 'rtb_pending_count', (int) $count );
	}

	/**
	 * Update the count of upcoming pending bookings whenever a booking status
	 * is modified
	 *
	 * @param string $new_status The status being transitioned to
	 * @param string $old_status The status the post used to have
	 * @param WP_Post $post The post being transitioned
	 * @since 1.7.5
	 */
	public function maybe_update_pending_count( $new_status, $old_status, $post ) {
		if ( $post->post_type === RTB_BOOKING_POST_TYPE ) {
			$this->update_pending_count();
		}
	}

}
} // endif;
