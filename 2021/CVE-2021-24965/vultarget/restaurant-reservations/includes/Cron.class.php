<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbCron' ) ) {
/**
 * This class handles scheduling of cron jobs for different notifications
 * such as reservation reminders or when customers are late for their reservations
 *
 * @since 2.0.0
 */
class rtbCron {

	/**
	 * Adds the necessary filter and action calls
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'cron_schedules', array($this, 'add_cron_interval') );

		add_action( 'rtb_cron_jobs', array($this, 'handle_late_arrivals_task') );
		add_action( 'rtb_cron_jobs', array($this, 'handle_reminder_task') );

		// if ( isset($_GET['debug']) ) { add_action('admin_init', array($this, 'handle_reminder_task') ); } // Used for testing
	}

	/**
	 * Adds in 10 minute cron interval
	 *
	 * @var array $schedules
	 * @since 2.0.0
	 */
	public function add_cron_interval( $schedules ) {
		$schedules['ten_minutes'] = array(
			'interval' => 600,
			'display' => esc_html__( 'Every Ten Minutes' )
		);

		return $schedules;
	}

	/**
	 * Creates a scheduled action called by wp_cron every 10 minutes 
	 * The class hooks into those calls for reminders and late arrivals
	 *
	 * @since 2.0.0
	 */
	public function schedule_events() {
		if (! wp_next_scheduled ( 'rtb_cron_jobs' )) {
			wp_schedule_event( time(), 'ten_minutes', 'rtb_cron_jobs' );
		}
	}

	/**
	 * Clears the rtb_cron_job hook so that it's no longer called after the plugin is deactivated
	 *
	 * @since 2.0.0
	 */
	public function unschedule_events() {
		wp_clear_scheduled_hook( 'rtb_cron_jobs' );
	}

	/**
	 * Handles the late arrival event when called by wp_scheduler
	 *
	 * @since 2.0.0
	 */
	public function handle_late_arrivals_task() {
		global $rtb_controller;

		if ( empty( $rtb_controller->settings->get_setting( 'time-late-user' ) ) ) { return; }

		require_once( RTB_PLUGIN_DIR . '/includes/Notification.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.Email.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.SMS.class.php' );

		$bookings = $this->get_late_arrival_posts();

		foreach ($bookings as $booking) {
			
			if ( ! $booking->late_arrival_sent ) {
				if ( $rtb_controller->settings->get_setting( 'late-notification-format' ) == 'text' ) {
					$notification = new rtbNotificationSMS( 'late_user', 'user' ); 
				}
				else {
					$notification = new rtbNotificationEmail( 'late_user', 'user' ); 
				}

				$notification->set_booking($booking);

				$notification->prepare_notification();

				do_action( 'rtb_send_notification_before', $notification );
  				$sent = $notification->send_notification(); 
  				do_action( 'rtb_send_notification_after', $notification );

  				if ( $sent ) {
  					$booking->late_arrival_sent = true;
  					$booking->insert_post_meta();
  				}
			}
		}

		wp_reset_postdata();
	}

	/**
	 * Handles the notification reminders event when called by wp_scheduler
	 *
	 * @since 2.0.0
	 */
	public function handle_reminder_task() {
		global $rtb_controller;

		if ( empty( $rtb_controller->settings->get_setting( 'time-reminder-user' ) ) ) { return; }

		require_once( RTB_PLUGIN_DIR . '/includes/Notification.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.Email.class.php' );
		require_once( RTB_PLUGIN_DIR . '/includes/Notification.SMS.class.php' );

		$bookings = $this->get_reminder_posts();
 		
		foreach ($bookings as $booking) {
			
			if ( ! $booking->reminder_sent ) {
				if ( $rtb_controller->settings->get_setting( 'reminder-notification-format' ) == 'text' ) {
					$notification = new rtbNotificationSMS( 'reminder', 'user' ); 
				}
				else {
					$notification = new rtbNotificationEmail( 'reminder', 'user' ); 
				}
				
				$notification->set_booking($booking);
				
				$notification->prepare_notification();

				do_action( 'rtb_send_notification_before', $notification );
				$sent = $notification->send_notification();
				do_action( 'rtb_send_notification_after', $notification );

				if ( $sent ) {
					$booking->reminder_sent = true;
					$booking->insert_post_meta();
				}
			}
		}

		wp_reset_postdata();
	}

	/**
	 * Gets the bookings that might need reminders sent to them
	 *
	 * @since 2.0.0
	 */
	public function get_late_arrival_posts() {
		global $rtb_controller;

		$time_interval = $this->get_time_interval( 'time-late-user' );

		$after_datetime = new DateTime( 'now', wp_timezone() );
		$before_datetime = new DateTime( 'now', wp_timezone() );

		$after_datetime->setTimestamp( time() - ( $time_interval + 3600 ) );
		$before_datetime->setTimestamp( time() - $time_interval );

		$args = array(
			'post_status' => 'confirmed,',
			'posts_per_page' => -1,
			'date_query' => array(
				'before' => $before_datetime->format( 'Y-m-d H:i:s' ),
				'after' => $after_datetime->format( 'Y-m-d H:i:s' ),
				'column' => 'post_date'
			)
		);
		require_once( RTB_PLUGIN_DIR . '/includes/Query.class.php' );
		$query = new rtbQuery( $args );

		$query->prepare_args();

		return $query->get_bookings();
	}

	/**
	 * Gets the bookings that might need reminders sent to them
	 *
	 * @since 2.0.0
	 */
	public function get_reminder_posts() {
		global $rtb_controller;

		$time_interval = $this->get_time_interval( 'time-reminder-user' );
		$time_interval = new DateInterval( "PT{$time_interval}S" );

		$reminder_time_window_start = new DateTime( 'now', wp_timezone() );
		$one_hour = new DateInterval( "PT1H" );
		$reminder_time_window_start->sub( $one_hour );

		$reminder_time_window_end = new DateTime( 'now', wp_timezone() );
		$reminder_time_window_end->add( $time_interval );

		$args = array(
			'post_status' => 'confirmed,',
			'post_count' => -1,
			'date_query' => array(
				'after' => $reminder_time_window_start->format( 'Y-m-d H:i:s' ),
				'before' => $reminder_time_window_end->format( 'Y-m-d H:i:s' ),
				'column' => 'post_date'
			)
		);

		require_once( RTB_PLUGIN_DIR . '/includes/Query.class.php' );
		$query = new rtbQuery( $args );

		$query->prepare_args();
		
		return $query->get_bookings();
	}

	/**
	 * Converts a time unit and interval into its value in seconds
	 *
	 * @since 2.0.0
	 */
	public function get_time_interval( $setting ) {
		global $rtb_controller;

		$late_arrival_time = $rtb_controller->settings->get_setting( $setting );

		$count = intval( substr( $late_arrival_time, 0, strpos( $late_arrival_time, "_" ) ) );
		$unit = substr( $late_arrival_time, strpos( $late_arrival_time, "_" ) + 1 );

		switch ($unit) {
			case 'days':
				$multiplier = 24*3600;
				break;
			case 'hours':
				$multiplier = 3600;
				break;
			case 'minutes':
				$multiplier = 60;
				break;
			
			default:
				$multiplier = 1;
				break;
		}

		return $count * $multiplier;
	}

}
} // endif;
