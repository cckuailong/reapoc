<?php
/**
 * Plugin review class.
 * Prompts users to give a review of the plugin on WordPress.org after a period of usage.
 *
 * Heavily based on code by Rhys Wynne
 * https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @version   1.0
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */

if ( ! class_exists( 'CMP_Feedback' ) ) :

	/**
	 * The feedback.
	 */
	class CMP_Feedback {

	/**
	 * Private variables.
	 *
	 * These should be customised for each project.
	 */
	private $slug;        // The plugin slug
	private $name;        // The plugin name
	private $time_limit;  // The time limit at which notice is shown

	/**
	 * Variables.
	 */
	public $nobug_option;

	/**
	 * Fire the constructor up :)
	 */
	public function __construct( $args ) {

		$this->slug        = $args['slug'];
		$this->name        = $args['name'];
		
		if ( isset( $args['time_limit'] ) ) {
			$this->time_limit  = $args['time_limit'];
		} else {
			$this->time_limit = WEEK_IN_SECONDS;
		}


		$this->nobug_option = $this->slug . '-no-bug';

		// Loading main functionality
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
	}

	/**
	 * Seconds to words.
	 */
	public function seconds_to_words( $seconds ) {

		// Get the years
		$years = ( intval( $seconds ) / YEAR_IN_SECONDS ) % 100;
		if ( $years > 1 ) {
			return sprintf( __( '%s years', $this->slug ), $years );
		} elseif ( $years > 0) {
			return __( 'a year', $this->slug );
		}

		// Get the weeks
		$weeks = ( intval( $seconds ) / WEEK_IN_SECONDS ) % 52;
		if ( $weeks > 1 ) {
			return sprintf( __( '%s weeks', $this->slug ), $weeks );
		} elseif ( $weeks > 0) {
			return __( 'a week', $this->slug );
		}

		// Get the days
		$days = ( intval( $seconds ) / DAY_IN_SECONDS ) % 7;
		if ( $days > 1 ) {
			return sprintf( __( '%s days', $this->slug ), $days );
		} elseif ( $days > 0) {
			return __( 'a day', $this->slug );
		}

		// Get the hours
		$hours = ( intval( $seconds ) / HOUR_IN_SECONDS ) % 24;
		if ( $hours > 1 ) {
			return sprintf( __( '%s hours', $this->slug ), $hours );
		} elseif ( $hours > 0) {
			return __( 'an hour', $this->slug );
		}

		// Get the minutes
		$minutes = ( intval( $seconds ) / MINUTE_IN_SECONDS ) % 60;
		if ( $minutes > 1 ) {
			return sprintf( __( '%s minutes', $this->slug ), $minutes );
		} elseif ( $minutes > 0) {
			return __( 'a minute', $this->slug );
		}

		// Get the seconds
		$seconds = intval( $seconds ) % 60;
		if ( $seconds > 1 ) {
			return sprintf( __( '%s seconds', $this->slug ), $seconds );
		} elseif ( $seconds > 0) {
			return __( 'a second', $this->slug );
		}

		return;
	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		if ( true != get_site_option( $this->nobug_option ) ) {

			// If not installation date set, then add it
			$install_date = get_site_option( $this->slug . '-activation-date' );
			if ( '' == $install_date ) {
				add_site_option( $this->slug . '-activation-date', time() );
			}

			// If difference between install date and now is greater than time limit, then display notice
			if ( $install_date != false && ( time() - $install_date ) >  $this->time_limit  ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}

		}

	}

	/**
	 * Display Admin Notice, asking for a review.
	 */
	public function display_admin_notice() {

		$screen = get_current_screen(); 

		if ( isset( $screen->base ) && 'plugins' == $screen->base ) {

			$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'cmp-feedback-nounce' );
			$time = $this->seconds_to_words( time() - get_site_option( $this->slug . '-activation-date' ) );
			?>
			
			<style>
				 .cmp-feedback.updated {border-left-color: #18a0d2;position: relative;min-height: 90px;}
				 .cmp-notice-icon {float: left;margin-right: 1em;margin-top: 1em;}
				 .cmp-leave-feedback {text-align: right;position: absolute;right: 1em;bottom: 1em;}
				 @media screen and (max-width: 1366px) { .cmp-leave-feedback {position: relative;bottom: initial;margin: 1em 0;} }

			</style>

			<div class="cmp-feedback updated">
				<div class="cmp-notice-icon">
					<img src="<?php echo plugins_url('../img/cmp.png', __FILE__);?>" alt="CMP Logo" class="cmp-logo">
				</div>

				<h3><?php _e('Do you like CMP - Coming soon & Maintenace Plugin?', 'cmp-coming-soon-maintenance');?></h3>
				<span><?php printf( esc_html__( 'You have been using %1$s plugin for %2$s now! Please leave a quick review or feedback to help us grow our little plugin. Thank you.', 'cmp-coming-soon-maintenance' ), esc_html( $this->name ), esc_html( $time ) ); ?></span>
				<div class="cmp-leave-feedback">
					<?php printf( '<a href="%1$s" class="button button-primary cmp-feedback-button" target="_blank">%2$s</a>', esc_url( 'https://wordpress.org/support/plugin/cmp-coming-soon-maintenance/reviews/?rate=5#new-post' ), esc_html__( 'Leave feedback', 'cmp-coming-soon-maintenance' ) ); ?>
					<div><a href="<?php echo esc_url( $no_bug_url ); ?>" class="cmp-dismiss"><?php echo esc_html__( 'Dismiss', 'cmp-coming-soon-maintenance' ); ?></a></div>
				</div>
			</div>

			<?php
		}

	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page
		if ( ! isset( $_GET['_wpnonce'] ) || ( ! wp_verify_nonce( $_GET['_wpnonce'], 'cmp-feedback-nounce' ) || ! is_admin() || ! isset( $_GET[ $this->nobug_option ] ) || ! current_user_can( 'manage_options' ) ) ) {
			return;
		}

		add_site_option( $this->nobug_option, true );

	}

}
endif;

/*
* Instantiate the CMP_Feedback class.
*/
new CMP_Feedback( array(
	'slug'       => 'cmp-coming-soon-maintenance',
	'name'       => __( 'CMP - Coming Soon & Maintenance', 'cmp-coming-soon-maintenance' ),
	'time_limit' => WEEK_IN_SECONDS,
) );
