<?php
/**
 * Tracking functions for reporting plugin usage to the PowerPack Elements site for users that have opted in
 *
 * @copyright Copyright (c) 2015, Pippin Williamson
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.3
 *
 * @package PowerPackElements
 */

namespace PowerpackElementsLite\Classes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Usage tracking.
 *
 * @access public
 * @since  1.3
 * @return void
 */
class UsageTracking {
	/**
	 * The data to send to the remote site.
	 *
	 * @var array $data
	 * @access private
	 */
	private $data;

	/**
	 * Remote site.
	 *
	 * @var string $site_url
	 * @access private
	 */
	private $site_url = 'https://powerpackelements.com/';

	/**
	 * Get things going.
	 *
	 * @access public
	 */
	public function __construct() {
		//add_action( 'init', array( $this, 'schedule_send' ) );
		//add_action( 'init', array( $this, 'create_recurring_schedule' ) );
		//add_filter( 'cron_schedules', array( $this, 'cron_add_weekly' ) );
		//add_action( 'pp_admin_after_settings_saved', array( $this, 'check_for_settings_optin' ), 10, 2 );
		//add_action( 'admin_init', array( $this, 'act_on_tracking_decision' ) );
		add_action( 'admin_init', array( $this, 'hook_notices' ) );
	}

	/**
	 * Hook some notices and perform actions.
	 *
	 * @access public
	 */
	public function hook_notices() {
		if ( isset( $_GET['pp_admin_action'] ) && isset( $_GET['_nonce'] ) ) {
			$action = sanitize_text_field( wp_unslash( $_GET['pp_admin_action'] ) );
			$nonce = wp_unslash( $_GET['_nonce'] ); // @codingStandardsIgnoreLine.
			if ( wp_verify_nonce( $nonce, 'pp_admin_notice_nonce' ) ) {
				if ( 'review_maybe_later' === $action ) {
					update_option( 'pp_review_later_date', current_time( 'mysql' ) );
				}
				if ( 'review_already_did' === $action ) {
					update_option( 'pp_review_already_did', 'yes' );
				}
				if ( 'do_not_upgrade' === $action ) {
					update_option( 'pp_do_not_upgrade_to_pro', 'yes' );
				}
			}

			wp_safe_redirect( esc_url_raw( remove_query_arg( array( 'pp_admin_action', '_nonce' ) ) ) );
		}

		if ( isset( $_GET['page'] ) && 'powerpack-settings' === $_GET['page'] ) {
			remove_all_actions( 'admin_notices' );
		}

		//add_action( 'admin_notices', [ $this, 'tracking_admin_notice' ] );
		add_action( 'admin_notices', [ $this, 'review_plugin_notice' ] );
		add_action( 'admin_notices', [ $this, 'pro_upgrade_notice' ] );
	}

	/**
	 * Add weekly schedule for cron.
	 *
	 * @param 	array $schedules Array of cron schedules.
	 * @access 	public
	 * @return 	array
	 */
	public function cron_add_weekly( $schedules ) {
		$schedules['ppeweekly'] = array(
			'interval' => 604800,
			'display' => 'Weekly',
		);
		return $schedules;
	}

	/**
	 * Create recurring schedule.
	 *
	 * @access public
	 */
	public function create_recurring_schedule() {
		// check if event scheduled before.
		if ( ! wp_next_scheduled( 'pp_recurring_cron_job' ) ) {
			// schedule event to run after every day.
			wp_schedule_event( time(), 'ppeweekly', 'pp_recurring_cron_job' );
		}
	}

	/**
	 * Check if the user has opted into tracking.
	 *
	 * @access private
	 * @return bool
	 */
	private function tracking_allowed() {
		$setting = get_option( 'pp_allowed_tracking', false );

		return 'on' === $setting;
	}

	/**
	 * Setup the data that is going to be tracked.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_data() {
		$data = array();

		// Retrieve current theme info.
		$theme_data = wp_get_theme();
		$theme = $theme_data->Name . ' ' . $theme_data->Version; // @codingStandardsIgnoreLine.

		$data['php_version'] = phpversion();
		$data['edd_version'] = POWERPACK_ELEMENTS_LITE_VER;
		$data['wp_version'] = get_bloginfo( 'version' );
		$data['server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : ''; // @codingStandardsIgnoreLine.

		$data['install_date'] = get_option( 'pp_install_date', 'not set' );

		$data['multisite'] = is_multisite();
		$data['url'] = home_url();
		$data['theme'] = $theme;
		$data['email'] = get_bloginfo( 'admin_email' );

		// Retrieve current plugin information.
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				// Remove active plugins from list so we can show active and inactive separately.
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins'] = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['locale'] = ($data['wp_version'] >= 4.7) ? get_user_locale() : get_locale();

		$current_user = wp_get_current_user();

		$data['user_firstname'] = esc_html( $current_user->user_firstname );
		$data['user_lastname'] = esc_html( $current_user->user_lastname );
		$data['user_email'] = esc_html( $current_user->user_email );

		$this->data = $data;
	}

	/**
	 * Send the data to the remote server.
	 *
	 * @param boolean $override Force checkin.
	 * @param boolean $ignore_last_checkin Ignore last checkin.
	 * @access private
	 * @return mixed
	 */
	public function send_checkin( $override = false, $ignore_last_checkin = false ) {
		$home_url = trailingslashit( home_url() );
		// Allows us to stop our own site from checking in, and a filter for our additional sites.
		if ( $this->site_url === $home_url || apply_filters( 'pp_disable_tracking_checkin', false ) ) {
			return false;
		}

		if ( ! $this->tracking_allowed() && ! $override ) {
			return false;
		}

		// Send a maximum of once per week.
		$last_send = $this->get_last_send();
		if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
			return false;
		}

		$this->setup_data();

		$request = wp_remote_post(
			$this->site_url . '?edd_action=checkin', array(
			'method' => 'POST',
			'timeout' => 20,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking' => true,
			'body' => $this->data,
			'user-agent' => 'EDD/' . POWERPACK_ELEMENTS_LITE_VER . '; ' . get_bloginfo( 'url' ),
			)
		);

		if ( is_wp_error( $request ) ) {
			return $request;
		}

		update_option( 'pp_tracking_last_send', time() );

		return true;
	}

	/**
	 * Check for a new opt-in on settings save.
	 *
	 * This runs during the sanitation of General settings, thus the return.
	 *
	 * @access public
	 * @return mixed
	 */
	public function check_for_settings_optin() {
		// Send an initial check in on settings save.
		if ( isset( $_POST['pp_allowed_tracking'] ) && 'on' === wp_unslash( $_POST['pp_allowed_tracking'] ) ) { // @codingStandardsIgnoreLine.
			$this->send_checkin( true );
		}
	}

	/**
	 * Act on tracking descision.
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function act_on_tracking_decision() {
		if ( isset( $_GET['pp_admin_action'] ) ) {
			if ( 'pp_opt_into_tracking' === $_GET['pp_admin_action'] ) {
				$this->check_for_optin();
			}

			if ( 'pp_opt_out_of_tracking' === $_GET['pp_admin_action'] ) {
				$this->check_for_optout();
			}
		}
	}

	/**
	 * Check for a new opt-in via the admin notice.
	 *
	 * @access public
	 * @return void
	 */
	public function check_for_optin() {
		update_option( 'pp_allowed_tracking', 'on' );

		$this->send_checkin( true );

		update_option( 'pp_tracking_notice', '1' );

		wp_safe_redirect( remove_query_arg( 'pp_admin_action' ) );
	}

	/**
	 * Check for a new opt-in via the admin notice.
	 *
	 * @access public
	 * @return void
	 */
	public function check_for_optout() {
		delete_option( 'pp_allowed_tracking' );
		update_option( 'pp_tracking_notice', '1' );
		wp_safe_redirect( remove_query_arg( 'pp_admin_action' ) );
		exit;
	}

	/**
	 * Get the last time a checkin was sent.
	 *
	 * @access private
	 * @return false|string
	 */
	private function get_last_send() {
		return get_option( 'pp_tracking_last_send' );
	}

	/**
	 * Schedule a weekly checkin.
	 *
	 * @access public
	 * @return void
	 */
	public function schedule_send() {
		// We send once a week (while tracking is allowed) to check in, which can be used to determine active sites.
		add_action( 'pp_recurring_cron_job', array( $this, 'send_checkin' ) );
	}

	/**
	 * Display the admin notice to users that have not opted-in or out.
	 *
	 * @access public
	 * @return void
	 */
	public function tracking_admin_notice() {
		$hide_notice = get_option( 'pp_tracking_notice' );

		if ( $hide_notice ) {
			return;
		}

		if ( $this->tracking_allowed() ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( stristr( network_site_url( '/' ), 'dev' ) !== false
			|| stristr( network_site_url( '/' ), 'localhost' ) !== false
			|| stristr( network_site_url( '/' ), ':8888' ) !== false // This is common with MAMP on OS X.
			|| in_array( wp_unslash( $_SERVER['REMOTE_ADDR'] ), array( '127.0.0.1', '::1' ), true ) // @codingStandardsIgnoreLine.
		) {
			update_option( 'pp_tracking_notice', '1' );
		} else {

			$optin_url = add_query_arg( 'pp_admin_action', 'pp_opt_into_tracking' );
			$optout_url = add_query_arg( 'pp_admin_action', 'pp_opt_out_of_tracking' );

			$source = substr( md5( get_bloginfo( 'name' ) ), 0, 10 );
			$store_url = $this->site_url . 'pricing/?utm_source=' . $source . '&utm_medium=admin&utm_term=notice&utm_campaign=PPEUsageTracking';

			echo '<div class="notice notice-info updated"><p>';
			printf(
				// translators: %1$s denotes plugin name, %2$s denotes title text, %3$s denotes percentile, %4$s denotes store URL.
				__( 'Want to help make %1$s even more awesome? Allow us to <a href="#pp-what-we-collect" title="%2$s">collect non-sensitive</a> diagnostic data and plugin usage information. Opt-in to tracking and we will send you a special 15%3$s discount code for <a href="%4$s">Premium Upgrade</a>.', 'powerpack' ),
				'<strong>PowerPack Elements</strong>',
				esc_html__( 'Click here to check what we collect.', 'powerpack' ),
				'%',
				esc_url( $store_url )
			);
			echo '</p>';
			echo '<p id="pp-what-we-collect" style="display: none;">';
			echo esc_html__( 'We collect WordPress and PHP version, plugin and theme version, server environment, website, user first name, user last name, and email address to send you the discount code. No sensitive data is tracked.', 'powerpack' );
			echo '</p>';
			echo '<p>';
			echo '<a href="' . esc_url( $optin_url ) . '" class="button-primary">' . esc_html__( 'Sure! I\'d love to help', 'powerpack' ) . '</a>';
			echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="button-secondary">' . esc_html__( 'No thanks', 'powerpack' ) . '</a>';
			echo '</p></div>';
			?>
			<script type="text/javascript">
			;(function($) {
				$('a[href="#pp-what-we-collect"]').on('click', function(e) {
					e.preventDefault();
					$( $(this).attr('href') ).slideToggle('fast');
				});
			})(jQuery);
			</script>
			<?php
		} // End if().
	}

	/**
	 * Render notice for plugin review.
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function review_plugin_notice() {
		if ( 'yes' === get_option( 'pp_review_already_did' ) ) {
			return;
		}

		$maybe_later_date = get_option( 'pp_review_later_date' );

		if ( ! empty( $maybe_later_date ) ) {
			$diff = round( ( time() - strtotime( $maybe_later_date ) ) / 24 / 60 / 60 );

			if ( $diff < 7 ) {
				return;
			}
		} else {
			$install_date = get_option( 'pp_install_date' );

			if ( ! $install_date || empty( $install_date ) ) {
				return;
			}

			$diff = round( ( time() - strtotime( $install_date ) ) / 24 / 60 / 60 );

			if ( $diff < 7 ) {
				return;
			}
		}

		$nonce = wp_create_nonce( 'pp_admin_notice_nonce' );

		$review_url = 'https://wordpress.org/support/plugin/powerpack-lite-for-elementor/reviews/?filter=5#new-post';
		$maybe_later_url = add_query_arg(
			array(
				'pp_admin_action' 	=> 'review_maybe_later',
				'_nonce'			=> $nonce,
			)
		);
		$already_did_url = add_query_arg(
			array(
				'pp_admin_action' 	=> 'review_already_did',
				'_nonce'			=> $nonce,
			)
		);

		$notice = sprintf(
			// translators: %1$s denotes plugin name, %2$s denotes opening anchor tag, %3$s denots closing anchor tag.
			__( 'Hey, It seems you have been using %1$s for at least 7 days now - that\'s awesome!<br>Could you please do us a BIG favor and give it a %2$s5-star rating on WordPress?%3$s This will help us spread the word and boost our motivation - thanks!', 'powerpack' ),
			'<strong>PowerPack Elements Lite</strong>',
			'<a href="' . esc_url( $review_url ) . '" target="_blank">',
			'</a>'
		);
		?>
		<?php $this->print_notices_common_style(); ?>
		<style>
		.pp-review-notice {
			display: block;
		}
		.pp-review-notice p {
			line-height: 22px;
		}
		.pp-review-notice .pp-notice-buttons {
			margin: 10px 0;
			display: flex;
			align-items: center;
		}
		.pp-review-notice .pp-notice-buttons a {
			margin-right: 13px;
			text-decoration: none;
		}
		.pp-review-notice .pp-notice-buttons .dashicons {
			margin-right: 5px;
		}
		</style>
		<div class="pp-review-notice pp--notice notice notice-info is-dismissible">
			<p><?php echo $notice; // @codingStandardsIgnoreLine. ?></p>
			<div class="pp-notice-buttons">
				<a href="<?php echo esc_url( $review_url ); ?>" target="_blank" class="pp-button-primary"><?php esc_html_e( 'Ok, you deserve it', 'powerpack' ); ?></a>
				<span class="dashicons dashicons-calendar"></span>
				<a href="<?php echo esc_url_raw( $maybe_later_url ); ?>"><?php esc_html_e( 'Nope, maybe later', 'powerpack' ); ?></a>
				<span class="dashicons dashicons-smiley"></span>
				<a href="<?php echo esc_url_raw( $already_did_url ); ?>"><?php esc_html_e( 'I already did', 'powerpack' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render notice for Pro upgrade.
	 *
	 * @access 	public
	 * @return 	void
	 */
	public function pro_upgrade_notice() {
		if ( 'yes' === get_option( 'pp_do_not_upgrade_to_pro' ) ) {
			return;
		}

		$install_date = get_option( 'pp_install_date' );

		if ( ! $install_date || empty( $install_date ) ) {
			return;
		}

		$diff = round( ( time() - strtotime( $install_date ) ) / 24 / 60 / 60 );

		if ( $diff < 23 ) {
			return;
		}

		$nonce = wp_create_nonce( 'pp_admin_notice_nonce' );

		$upgrade_url = 'https://powerpackelements.com/upgrade/?utm_source=wporg&utm_medium=notice&utm_campaign=lite_offer';
		$no_upgrade_url = add_query_arg(
			array(
				'pp_admin_action' 	=> 'do_not_upgrade',
				'_nonce'			=> $nonce,
			)
		);

		$notice = __( '<strong>Exclusive Offer!</strong> We don\'t run promotions very often. But for a limited time we are offering an exclusive <strong>20% discount</strong> to all users of Free PowerPack Elementor addon.', 'powerpack' );
		$button_text = __( 'Get this offer', 'powerpack' );

		if ( class_exists( 'WooCommerce' ) ) {
			$notice = __( 'Upgrade to <strong>PowerPack Pro for Elementor</strong> and Get WooCommerce Elementor Widgets like Product Grid, Checkout Styler, Off-Canvas Cart, etc.', 'powerpack' );
			$upgrade_url = 'http://powerpackelements.com/woocommerce-elementor-widgets/?utm_source=wporg&utm_medium=notice&utm_campaign=woo_upgrade';
			$button_text = __( 'Explore now', 'powerpack' );
		}
		?>
		<?php $this->print_notices_common_style(); ?>
		<style>
		.pp-upgrade-notice {
			padding-left: 5px;
			padding-top: 5px;
			padding-bottom: 5px;
			border: 0;
		}
		.pp-upgrade-notice .pp-notice-wrap {
			display: flex;
			align-items: center;
		}
		.pp-upgrade-notice .pp-notice-col-left {
			width: <?php echo class_exists( 'WooCommerce' ) ? 8 : 10; ?>%;
    		min-width: 75px;
		}
		.pp-upgrade-notice .pp-notice-col-right {
			padding-left: 15px;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
		}
		.pp-upgrade-notice img {
			display: block;
			max-width: 100%;
		}
		.pp-upgrade-notice p {
			line-height: 22px;
			font-size: 14px;
			margin: 0;
		}
		.pp-upgrade-notice .pp-notice-buttons {
			margin: 10px 0;
			margin-bottom: 0;
			display: flex;
    		align-items: center;
		}
		.pp-upgrade-notice .pp-notice-buttons .dashicons {
			margin-right: 5px;
		}
		</style>
		<div class="pp-upgrade-notice pp--notice notice notice-success is-dismissible">
			<div class="pp-notice-wrap">
				<div class="pp-notice-col-left">
					<img src="<?php echo POWERPACK_ELEMENTS_LITE_URL; ?>assets/images/icon-256x256.png" />
				</div>
				<div class="pp-notice-col-right">
					<p><?php echo $notice; // @codingStandardsIgnoreLine. ?></p>
					<div class="pp-notice-buttons">
						<a href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" class="pp-button-primary"><?php echo $button_text; ?></a>
						<a href="<?php echo esc_url_raw( $no_upgrade_url ); ?>"><?php esc_html_e( 'I\'m not interested', 'powerpack' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function print_notices_common_style() {
		?>
		<style>
		.pp--notice {
			--brand-color: #4849d7;
			display: block;
			border-top: 0;
			border-bottom: 0;
			border-right: 0;
			border-left-color: var(--brand-color);
			box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1);
		}
		.pp--notice a {
			color: var(--brand-color);
		}
		.pp--notice .pp-notice-buttons a {
			margin-right: 13px;
		}
		.pp--notice .pp-notice-buttons a.pp-button-primary {
			background: var(--brand-color);
			color: #fff;
			text-decoration: none;
			padding: 6px 12px;
			border-radius: 4px;
		}
		</style>
		<?php
	}

	/**
	 * Get singleton class instance.
	 *
	 * @access 	public
	 * @return 	object
	 */
	public static function get_instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}
}
