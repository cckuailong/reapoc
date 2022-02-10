<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbNotifications' ) ) {
/**
 * Class to process notifications for Restaurant Reservations
 *
 * This class contains the registered notifications and sends them when the
 * event is triggered.
 *
 * @since 0.0.1
 */
class rtbNotifications {

	/**
	 * Booking object (class rtbBooking)
	 *
	 * @var object
	 * @since 0.0.1
	 */
	public $booking;

	/**
	 * Array of rtbNotification objects
	 *
	 * @var array
	 * @since 0.0.1
	 */
	public $notifications;

	/**
	 * Register notifications hook early so that other early hooks can
	 * be used by the notification system.
	 * @since 0.0.1
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_notifications' ) );

		add_action( 'init', array( $this, 'maybe_send_daily_summary' ), 12 );
	}

	/**
	 * Register notifications
	 * @since 0.0.1
	 */
	public function register_notifications() {

		// Hook into all events that require notifications
		$hooks = array(
			'rtb_insert_booking'		=> array( $this, 'new_submission' ), 					// Booking submitted
			'rtb_booking_paid'			=> array( $this, 'new_submission' ), 					// Booking deposit paid
			'rtb_confirmed_booking'		=> array( $this, 'new_confirmed_submission' ), 			// Booking confirmed
			'pending_to_confirmed'		=> array( $this, 'pending_to_confirmed' ), 				// Booking confirmed
			'pending_to_closed'			=> array( $this, 'pending_to_closed' ), 				// Booking can not be made
			'pending_to_cancelled'		=> array( $this, 'booking_cancelled' ), 				// Booking cancelled
			'confirmed_to_cancelled'	=> array( $this, 'booking_cancelled' ), 				// Booking cancelled
		);

		$hooks = apply_filters( 'rtb_notification_transition_callbacks', $hooks );

		foreach ( $hooks as $hook => $callback ) {
			add_action( $hook, $callback );
		}

		// Register notifications
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.Email.class.php' );

		$this->notifications = array(
			new rtbNotificationEmail( 'new_submission', 'user' ),
			new rtbNotificationEmail( 'pending_to_confirmed', 'user' ),
			new rtbNotificationEmail( 'pending_to_closed', 'user' ),
			new rtbNotificationEmail( 'booking_cancelled', 'user' ),
		);

		global $rtb_controller;
		if ( $rtb_controller->settings->get_setting( 'admin-email-option' ) ) {
			$this->notifications[] = new rtbNotificationEmail( 'new_submission', 'admin' );
		}

		if ( $rtb_controller->settings->get_setting( 'admin-confirmed-email-option' ) ) {
			$this->notifications[] = new rtbNotificationEmail( 'rtb_confirmed_booking', 'admin' );
		}

		if ( $rtb_controller->settings->get_setting( 'admin-cancelled-email-option' ) ) {
			$this->notifications[] = new rtbNotificationEmail( 'booking_cancelled', 'admin' );
		}

		$this->notifications = apply_filters( 'rtb_notifications', $this->notifications );
	}

	/**
	 * Send a summary of bookings today, if enabled
	 * @since 2.3.6
	 */
	public function maybe_send_daily_summary() {
		global $rtb_controller;

		if ( empty( $rtb_controller->settings->get_setting( 'daily-summary-address' ) ) ) { return; }

		if ( get_option( 'rtb-daily-summary-send-date' ) == date( 'Y-m-d' ) ) { return; }

		require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );

		$display_table = $rtb_controller->permissions->check_permission( 'premium_table_restrictions' ) && $rtb_controller->settings->get_setting( 'enable-tables' );

		$args = array(
			'post_type' 		=> 'rtb-booking',
			'posts_per_page'	=> -1,
			'date_query' 		=> array(
				'year' 				=> date( 'Y' ),
				'month' 			=> date( 'm' ),
				'day' 				=> date( 'd' )
			),
			'post_status' 		=> array_keys( $rtb_controller->cpts->booking_statuses ),
			'orderby' 			=> 'date',
			'order' 			=> 'ASC'
		);

		$bookings = get_posts( $args );

		ob_start();

		?>
		<?php _e('Please find a summary of today\'s reservations in the table below.', 'restaurant-reservations'); ?>

		<table class='rtb-view-bookings-table'>
			<thead>
				<tr>
					<th><?php _e('Time', 'restaurant-reservations'); ?></th>
					<th><?php _e('Party', 'restaurant-reservations'); ?></th>
					<th><?php _e('Name', 'restaurant-reservations'); ?></th>
					<th><?php _e('Email', 'restaurant-reservations'); ?></th>
					<th><?php _e('Phone', 'restaurant-reservations'); ?></th>
					<?php if ( $display_table ) {?> <th><?php _e('Table', 'restaurant-reservations'); ?></th><?php } ?>
					<th><?php _e('Status', 'restaurant-reservations'); ?></th>
					<th><?php _e('Details', 'restaurant-reservations'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $bookings as $booking ) { ?>
					<?php $booking_object = new rtbBooking(); ?>
					<?php $booking_object->load_post( $booking ); ?>
					<tr>
						<td><?php echo date( 'H:i:s', strtotime( $booking_object->date ) ); ?></td>
						<td><?php echo $booking_object->party; ?></td>
						<td><?php echo $booking_object->name; ?></td>
						<td><?php echo $booking_object->email; ?></td>
						<td><?php echo $booking_object->phone; ?></td>
						<?php if ( $display_table ) { $table = implode(', ', $booking_object->table ); echo "<td>{$table}</td>"; } ?>
						<td><?php echo $rtb_controller->cpts->booking_statuses[$booking_object->post_status]['label'] ?></td>
						<td><?php echo apply_filters( 'rtb_bookings_table_column_details', $booking_object->message, $booking_object ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<?php 

		$email_content = ob_get_clean();

		$notification = new rtbNotificationEmail( 'daily_summary', 'admin' );

		$notification->to_email = $rtb_controller->settings->get_setting( 'daily-summary-address' );
		$notification->from_email = $rtb_controller->settings->get_setting( 'reply-to-address' );
		$notification->from_name = $rtb_controller->settings->get_setting( 'reply-to-name' );
		$notification->subject = __( 'Daily Email Summary', 'restaurant-reservations' );
		$notification->manual_message = $email_content;

		if ( $notification->prepare_notification() ) {
 
			if ( $notification->send_notification() ) {

				update_option( 'rtb-daily-summary-send-date', date( 'Y-m-d' ) );
			}
		}
	}

	/**
	 * Set booking data
	 * @since 0.0.1
	 */
	public function set_booking( $booking_post ) {
		require_once( RTB_PLUGIN_DIR . '/includes/Booking.class.php' );
		$this->booking = new rtbBooking();
		$this->booking->load_wp_post( $booking_post );
	}

	/**
	 * New booking submissions
	 *
	 * @var object $booking
	 * @since 0.0.1
	 */
	public function new_submission( $booking ) {

		// Bail early if $booking is not a rtbBooking object
		if ( get_class( $booking ) != 'rtbBooking' ) {
			return;
		}

		// trigger an event so that admin notifications for a new confirmed booking can be sent
		if ( $booking->post_status == 'confirmed' ) { 
			do_action( 'rtb_confirmed_booking', get_post( $booking->ID ) );
		}

		// If the post status is not pending, trigger a post status
		// transition as though it's gone from pending_to_{status}
		if ( $booking->post_status != 'pending' and $booking->post_status != 'draft' ) {
			do_action( 'pending_to_' . $booking->post_status, get_post( $booking->ID ) );

		// Otherwise proceed with the new_submission event
		} else {
			$this->booking = $booking;
			$this->event( 'new_submission' );
		}
	}

	/**
	 * New confirmed booking
	 * @since 2.1.0
	 */
	public function new_confirmed_submission( $booking_post ) {

		if ( $booking_post->post_type != RTB_BOOKING_POST_TYPE ) {
			return;
		}

		$this->set_booking( $booking_post );

		$this->event( 'rtb_confirmed_booking' );

	}

	/**
	 * Booking confirmed
	 * @since 0.0.1
	 */
	public function pending_to_confirmed( $booking_post ) {

		if ( $booking_post->post_type != RTB_BOOKING_POST_TYPE ) {
			return;
		}

		$this->set_booking( $booking_post );

		$this->event( 'pending_to_confirmed' );

	}

	/**
	 * Booking can not be made
	 * @since 0.0.1
	 */
	public function pending_to_closed( $booking_post ) {

		if ( $booking_post->post_type != RTB_BOOKING_POST_TYPE ) {
			return;
		}

		$this->set_booking( $booking_post );

		$this->event( 'pending_to_closed' );

	}

	/**
	 * Booking has been cancelled by the guest
	 */
	public function booking_cancelled( $booking_post ) {

		if ( $booking_post->post_type != RTB_BOOKING_POST_TYPE ) {
			return;
		}

		$this->set_booking( $booking_post );

		$this->event( 'booking_cancelled' );

	}

	/**
	 * Booking was confirmed and is now completed. Send out an optional
	 * follow-up email.
	 *
	 * @since 0.0.1
	 */
	public function confirmed_to_closed( $booking_post ) {

		if ( $booking_post->post_type != RTB_BOOKING_POST_TYPE ) {
			return;
		}

		$this->set_booking( $booking_post );

		$this->event( 'confirmed_to_closed' );

	}

	/**
	 * Process notifications for an event
	 * @since 0.0.1
	 */
	public function event( $event ) {

		foreach( $this->notifications as $notification ) {

			if ( $event == $notification->event ) {
				$notification->set_booking( $this->booking );
				if ( $notification->prepare_notification() ) { 
					do_action( 'rtb_send_notification_before', $notification );
					$notification->send_notification(); 
					do_action( 'rtb_send_notification_after', $notification );
				}
			}
		}

	}

}
} // endif;
