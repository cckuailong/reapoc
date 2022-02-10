<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class rtbInstallationWalkthrough {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_install_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_install_screen_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );

		add_action( 'admin_head', array( $this, 'admin_enqueue') );

		add_action( 'wp_ajax_nopriv_rtb-welcome-add-menu-page' , array( 'rtbHelper' , 'admin_nopriv_ajax' ) );
		add_action( 'wp_ajax_rtb-welcome-add-menu-page', array( $this, 'add_reservations_page' ) );
		add_action( 'wp_ajax_nopriv_rtb-welcome-set-schedule' , array( 'rtbHelper' , 'admin_nopriv_ajax' ) );
		add_action( 'wp_ajax_rtb-welcome-set-schedule', array( $this, 'set_schedule' ) );
		add_action( 'wp_ajax_nopriv_rtb-welcome-set-options' , array( 'rtbHelper' , 'admin_nopriv_ajax' ) );
		add_action( 'wp_ajax_rtb-welcome-set-options', array( $this, 'set_options' ) );
	}

	public function redirect() {
		if ( ! get_transient( 'rtb-getting-started' ) ) 
			return;

		delete_transient( 'rtb-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		$bookings = get_posts( array( 'post_type' => 'rtb-booking', 'post_status' => 'any' ) );
		if ( ! empty( $bookings ) ) {
			set_transient( 'rtb-admin-install-notice', true, 5 );
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=rtb-getting-started' ) );
		exit;
	}

	public function register_install_screen() {
		add_dashboard_page(
			esc_html__( 'Five-Star Restaurant Reservations - Welcome!', 'restaurant-reservations' ),
			esc_html__( 'Five-Star Restaurant Reservations - Welcome!', 'restaurant-reservations' ),
			'manage_options',
			'rtb-getting-started',
			array($this, 'display_install_screen')
		);
	}

	public function hide_install_screen_menu_item() {
		remove_submenu_page( 'index.php', 'rtb-getting-started' );
	}

	public function add_reservations_page() {

		// Authenticate request
		if ( !check_ajax_referer( 'rtb-getting-started', 'nonce' ) || !current_user_can( 'manage_bookings' ) ) {
			rtbHelper::admin_nopriv_ajax();
		}

		$reservations_page = wp_insert_post(array(
			'post_title' => (isset($_POST['reservations_page_title']) ? sanitize_text_field( $_POST['reservations_page_title'] ) : ''),
			'post_content' => '',
			'post_status' => 'publish',
			'post_type' => 'page'
		));

		if ( $reservations_page ) { 
			$rtb_options = get_option( 'rtb-settings' );
			$rtb_options['booking-page'] = $reservations_page;
			update_option( 'rtb-settings', $rtb_options );
		}
	
		exit();
	}

	public function set_schedule() {

		// Authenticate request
		if ( !check_ajax_referer( 'rtb-getting-started', 'nonce' ) || !current_user_can( 'manage_bookings' ) ) {
			rtbHelper::admin_nopriv_ajax();
		}

		$rtb_options = get_option( 'rtb-settings' );

		$rtb_options['schedule-open'] = rtbHelper::sanitize_text_field_recursive( $_POST['schedule_open'] );

		update_option( 'rtb-settings', $rtb_options );

		exit();
	}

	public function set_options() {

		// Authenticate request
		if ( !check_ajax_referer( 'rtb-getting-started', 'nonce' ) || !current_user_can( 'manage_bookings' ) ) {
			rtbHelper::admin_nopriv_ajax();
		}

		$rtb_options = get_option( 'rtb-settings' );
		$rtb_options['party-size-min'] = sanitize_text_field( $_POST['party_size_min'] );
		$rtb_options['party-size'] = sanitize_text_field( $_POST['party_size'] );
		$rtb_options['early-bookings'] = sanitize_text_field( $_POST['early_bookings'] );
		$rtb_options['late-bookings'] = sanitize_text_field( $_POST['late_bookings'] );
		$rtb_options['time-interval'] = sanitize_text_field( $_POST['time_interval'] );
		update_option( 'rtb-settings', $rtb_options );

		exit();
	}

	function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'rtb-getting-started' ) { return; }

		wp_enqueue_style( 'rtb-admin-css', RTB_PLUGIN_URL . '/lib/simple-admin-pages/css/admin.css', array(), RTB_VERSION );
		wp_enqueue_style( 'rtb-welcome-screen', RTB_PLUGIN_URL . '/assets/css/admin-rtb-welcome-screen.css', array(), RTB_VERSION );
		wp_enqueue_style( 'pickadate-default', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/themes/default.css', array(), RTB_VERSION );
		wp_enqueue_style( 'pickadate-date', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/themes/default.date.css', array(), RTB_VERSION );
		wp_enqueue_style( 'pickadate-time', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/themes/default.time.css', array(), RTB_VERSION );
		
		wp_enqueue_script( 'rtb-getting-started', RTB_PLUGIN_URL . '/assets/js/admin-rtb-welcome-screen.js', array('jquery'), RTB_VERSION );
		wp_localize_script(
			'rtb-getting-started',
			'rtb_getting_started',
			array(
				'nonce' => wp_create_nonce( 'rtb-getting-started' )
			)
		);

		wp_enqueue_script( 'pickadate', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/picker.js', array('jquery'), RTB_VERSION, true );
		wp_enqueue_script( 'pickadate-date', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/picker.date.js', array('jquery'), RTB_VERSION, true );
		wp_enqueue_script( 'pickadate-time', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/picker.time.js', array('jquery'), RTB_VERSION, true );
		wp_enqueue_script( 'pickadate-legacy', RTB_PLUGIN_URL . '/lib/simple-admin-pages/lib/pickadate/legacy.js', array('jquery'), RTB_VERSION, true );
		wp_enqueue_script( 'sap-scheduler', RTB_PLUGIN_URL . '/lib/simple-admin-pages/js/scheduler.js', array('jquery'), RTB_VERSION, true );

		$sap_scheduler_settings[ 'schedule-open' ] = array(
			'time_interval' 	=> 15,
			'time_format'		=> 'h:i A',
			'date_format'		=> 'd mmmm, yyyy',
			'template'			=> $this->get_template(),
			'weekdays'			=> array(
				'monday'			=> 'Mo',
				'tuesday'			=> 'Tu',
				'wednesday'			=> 'We',
				'thursday'			=> 'Th',
				'friday'			=> 'Fr',
				'saturday'			=> 'Sa',
				'sunday'			=> 'Su',
			),
			'weeks'				=> array(
				'first'				=> '1st',
				'second'			=> '2nd',
				'third'				=> '3rd',
				'fourth'			=> '4th',
				'last'				=> 'last',
			),
			'disable_weekdays'	=> false,
			'disable_weeks'		=> true,
			'disable_date'		=> true,
			'disable_time'		=> false,
			'disable_multiple'	=> false,
			'summaries'			=> array(
				'never' 			=> __( 'Never', 'restaurant-reservations' ),
				'weekly_always' 	=> __( 'Every day', 'restaurant-reservations' ),
				'monthly_weekdays' 	=> sprintf( __( '%s on the %s week of the month', 'restaurant-reservations' ), '{days}', '{weeks}' ),
				'monthly_weeks' 	=> sprintf( __( '%s week of the month', 'restaurant-reservations' ), '{weeks}' ),
				'all_day' 			=> __( 'All day', 'restaurant-reservations' ),
				'before' 			=> __( 'Ends at', 'restaurant-reservations' ),
				'after' 			=> __( 'Starts at', 'restaurant-reservations' ),
				'separator'			=> __( '&mdash', 'restaurant-reservations' ),
			),
		);

		// This gets called multiple times, but only the last call is actually
		// pushed to the script.
		wp_localize_script(
			'sap-scheduler',
			'sap_scheduler',
			array(
				'settings' => $sap_scheduler_settings
			)
		);
	}

	public function display_install_screen() { ?>
		<div class='rtb-welcome-screen'>
			<?php if (!isset($_GET['exclude'])) { ?>
			<div class='rtb-welcome-screen-header'>
				<h1><?php _e('Welcome to the Five-Star Restaurant Reservations Plugin', 'restaurant-reservations'); ?></h1>
				<p><?php _e('Thanks for choosing the Five-Star Restaurant Reservations! The following will help you get started with the setup of your reservations system by creating your reservations page, setting times when bookings are allowed as well as configuring a few key options.', 'restaurant-reservations'); ?></p>
			</div>
			<?php } ?>
<?php if (!isset($_GET['exclude'])) { ?>		
			<div class='rtb-welcome-screen-box rtb-welcome-screen-reservations_page rtb-welcome-screen-open' data-screen='reservations_page'>
				<h2><?php _e('Add a Reservations Page', 'restaurant-reservations'); ?></h2>
				<div class='rtb-welcome-screen-box-content'>
					<p><?php _e('You can create a dedicated reservations booking page below, or skip this step and add your reservations to a page you\'ve already created manually.', 'restaurant-reservations'); ?></p>
					<div class='rtb-welcome-screen-menu-page'>
						<div class='rtb-welcome-screen-add-reservations-page-name rtb-welcome-screen-box-content-divs'><label><?php _e('Page Title:', 'restaurant-reservations'); ?></label><input type='text' value='Reservations' /></div>
						<div class='rtb-welcome-screen-add-reservations-page-button' data-nextaction='schedule_open'><?php _e('Create Page', 'restaurant-reservations'); ?></div>
					</div>
					<div class='rtb-welcome-screen-next-button rtb-welcome-screen-next-button-not-top-margin' data-nextaction='schedule_open'><?php _e('Next Step', 'restaurant-reservations'); ?></div>
					<div class='clear'></div>
				</div>
			</div>
<?php } ?>		
			<div class='rtb-welcome-screen-box rtb-welcome-screen-schedule_open' data-screen='schedule_open'>
				<h2><?php echo (isset($_GET['exclude']) ? '1.' : '2.') . __(' Create Booking Schedule', 'restaurant-reservations'); ?></h2>
				<div class='rtb-welcome-screen-box-content'>
					<p><?php _e('Choose what times each week your restaurant is available to book reservations.', 'restaurant-reservations'); ?></p>
					<div class='rtb-welcome-screen-created-schedule-open'>
						<div class="sap-scheduler" id="schedule-open"></div>
						<div class="sap-add-scheduler">
							<a href="#" class="button">
								<?php _e('Add new scheduling rule', 'restaurant-reservations' ); ?>
							</a>
						</div>
					</div>
					<div class='rtb-welcome-screen-save-schedule-open-button'><?php _e('Save Schedule', 'restaurant-reservations'); ?></div>
					<div class="rtb-welcome-clear"></div>
					<div class='rtb-welcome-screen-next-button' data-nextaction='options'><?php _e('Next Step', 'restaurant-reservations'); ?></div>
					<div class='rtb-welcome-screen-previous-button' data-previousaction='reservations_page'><?php _e('Previous Step', 'restaurant-reservations'); ?></div>
					<div class='clear'></div>
				</div>
			</div>

			<div class='rtb-welcome-screen-box rtb-welcome-screen-options' data-screen='options'>
				<h2><?php echo (isset($_GET['exclude']) ? '2.' : '3.') . __(' Set Key Options', 'restaurant-reservations'); ?></h2>
				<div class='rtb-welcome-screen-box-content'>
					<p><?php _e('Set a min/max party size for bookings, choose how early and late bookings can be made, and pick the time interval between different booking options.', 'restaurant-reservations'); ?></p>
					<div class='rtb-welcome-screen-option'>
						<label><?php _e('Min Party Size:', 'restaurant-reservations'); ?></label>
						<select name='min-party-size'>
							<?php for ( $i = 1; $i <= 100; $i++ ) { ?>
								<option value='<?php echo $i; ?>'><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class='rtb-welcome-screen-option'>
						<label><?php _e('Max Party Size:', 'restaurant-reservations'); ?></label>
						<select name='party-size'>
							<option value='0'><?php _e('Any Size', 'restaurant-reservations' ); ?></option>
							<?php for ( $i = 1; $i <= 100; $i++ ) { ?>
								<option value='<?php echo $i; ?>'><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class='rtb-welcome-screen-option'>
						<label><?php _e('Early Bookings:', 'restaurant-reservations'); ?></label>
						<select name='early-bookings'>
							<option><?php _e('Any Time', 'restaurant-reservations' ); ?></option>
							<option value='1'><?php _e('From 1 day in advance', 'restaurant-reservations' ); ?></option>
							<option value='7'><?php _e('From 1 week in advance', 'restaurant-reservations' ); ?></option>
							<option value='14'><?php _e('From 2 weeks in advance', 'restaurant-reservations' ); ?></option>
							<option value='30'><?php _e('From 30 days in advance', 'restaurant-reservations' ); ?></option>
							<option value='90'><?php _e('From 90 days in advance', 'restaurant-reservations' ); ?></option>
						</select>
					</div>
					<div class='rtb-welcome-screen-option'>
						<label><?php _e('Late Bookings:', 'restaurant-reservations'); ?></label>
						<select name='late-bookings'>
							<option><?php _e('Up to the last minute', 'restaurant-reservations' ); ?></option>
							<option value='15'><?php _e('At least 15 minutes in advance', 'restaurant-reservations' ); ?></option>
							<option value='30'><?php _e('At least 30 minutes in advance', 'restaurant-reservations' ); ?></option>
							<option value='45'><?php _e('At least 45 minutes in advance', 'restaurant-reservations' ); ?></option>
							<option value='60'><?php _e('At least 1 hour in advance', 'restaurant-reservations' ); ?></option>
							<option value='240'><?php _e('At least 4 hours in advance', 'restaurant-reservations' ); ?></option>
							<option value='1440'><?php _e('At least 24 hours in advance', 'restaurant-reservations' ); ?></option>
							<option value='same_day'><?php _e('Block same-day-bookings', 'restaurant-reservations' ); ?></option>
						</select>
					</div>
					<div class='rtb-welcome-screen-option'>
						<label><?php _e('Time Interval:', 'restaurant-reservations'); ?></label>
						<select name='time-interval'>
							<option value='30'><?php _e('Every 30 minutes', 'restaurant-reservations' ); ?></option>
							<option value='15'><?php _e('Every 15 minutes', 'restaurant-reservations' ); ?></option>
							<option value='10'><?php _e('Every 10 minutes', 'restaurant-reservations' ); ?></option>
							<option value='5'><?php _e('Every 5 minutes', 'restaurant-reservations' ); ?></option>
						</select>
					</div>
					<div class='rtb-welcome-screen-save-options-button'><?php _e('Save Options', 'restaurant-reservations'); ?></div>
					<div class="rtb-welcome-clear"></div>
					<div class='rtb-welcome-screen-previous-button' data-previousaction='schedule_open'><?php _e('Previous Step', 'restaurant-reservations'); ?></div>
					<div class='rtb-welcome-screen-finish-button'><a href='admin.php?page=rtb-dashboard'><?php _e('Finish', 'restaurant-reservations'); ?></a></div>
					<div class='clear'></div>
				</div>
			</div>
		
			<div class='rtb-welcome-screen-skip-container'>
				<a href='admin.php?page=rtb-dashboard'><div class='rtb-welcome-screen-skip-button'><?php _e('Skip Setup', 'restaurant-reservations'); ?></div></a>
			</div>
		</div>

	<?php }

	/**
	 * Retrieve the template for a scheduling rule
	 * @since 2.0
	 */
	public function get_template( $id = 0, $values = array(), $list = false ) {

		$date_format = 'weekly';
		$time_format = 'all-day';

		$weekdays = array(
			'monday'		=> 'Mo',
			'tuesday'		=> 'Tu',
			'wednesday'		=> 'We',
			'thursday'		=> 'Th',
			'friday'		=> 'Fr',
			'saturday'		=> 'Sa',
			'sunday'		=> 'Su',
		);

		ob_start();
		?>

		<div class="sap-scheduler-rule clearfix<?php echo $list ? ' list' : ''; ?>">
			<div class="sap-scheduler-date weekly">
				<ul class="sap-selector">

					<li>
						<div class="dashicons dashicons-calendar"></div>
						<?php _e( 'Weekly', 'restaurant-reservations' ); ?>
					</li>
				
				</ul>

				<ul class="sap-scheduler-weekdays">
					<li class="label">
						<?php _e( 'Days of the week', 'restaurant-reservations' ); ?>
					</li>
				<?php
					foreach ( $weekdays as $slug => $label ) :
						$input_name = 'rtb-setting[schedule_open][' . $id . '][weekdays][' . esc_attr( $slug ) . ']';
				?>
					<li>
						&nbsp;<input type="checkbox" name="<?php echo $input_name; ?>" id="<?php echo $input_name; ?>" value="1" data-day="<?php echo esc_attr( $slug ); ?>"><label for="<?php echo $input_name; ?>"><?php echo ucfirst( $label ); ?></label>
					</li>
				<?php endforeach; ?>
				</ul>

			</div>

			<div class="sap-scheduler-time all-day">

				<ul class="sap-selector">
					<li>
						<div class="dashicons dashicons-clock"></div>
						<a href="#" data-format="time-slot">
							<?php _e( 'Time', 'restaurant-reservations' ); ?>
						</a>
					</li>
					<li>
						<a href="#" data-format="all-day" class="selected">
							<?php _e( 'All day', 'restaurant-reservations' ); ?>
						</a>
					</li>
				</ul>

				<div class="sap-scheduler-time-input clearfix">

					<div class="start">
						<label for="rtb-setting[schedule_open][<?php echo $id; ?>][time][start]">
							<?php _e( 'Start', 'restaurant-reservations' ); ?>
						</label>
						<input type="text" name="<?php echo 'rtb-setting[schedule_open][' . $id . '][time][start]'; ?>" id="<?php echo 'rtb-setting[schedule_open][' . $id . '][time][start]'; ?>">
					</div>

					<div class="end">
						<label for="rtb-setting[schedule_open][<?php echo $id; ?>][time][end]">
							<?php _e( 'End', 'restaurant-reservations' ); ?>
						</label>
						<input type="text" name="<?php echo'rtb-setting[schedule_open][' . $id . '][time][end]'; ?>" id="<?php echo 'rtb-setting[schedule_open][' . $id . '][time][end]'; ?>">
					</div>

				</div>

				<div class="sap-scheduler-all-day">
					<?php printf( __( 'All day long. Want to %sset a time slot%s?', 'restaurant-reservations' ), '<a href="#" data-format="time-slot">', '</a>' ); ?>
				</div>

			</div>

			<div class="sap-scheduler-brief">
				<div class="date">
					<div class="dashicons dashicons-calendar"></div>
					<span class="value"></span>
				</div>
				<div class="time">
					<div class="dashicons dashicons-clock"></div>
					<span class="value"></span>
				</div>
			</div>
			<div class="sap-scheduler-control">
				<a href="#" class="toggle" title="<?php _e( 'Open and close this rule', 'restaurant-reservations' ); ?>">
					<div class="dashicons dashicons-<?php echo $list ? 'edit' : 'arrow-up-alt2'; ?>"></div>
					<span class="screen-reader-text">
						<?php _e( 'Open and close this rule', 'restaurant-reservations' ); ?>
					</span>
				</a>
				<a href="#" class="delete" title="<?php _e( 'Delete rule', 'restaurant-reservations' ); ?>">
					<div class="dashicons dashicons-dismiss"></div>
					<span class="screen-reader-text">
						<?php _e( 'Delete scheduling rule', 'restaurant-reservations' ); ?>
					</span>
				</a>
			</div>
		</div>

		<?php
		$output = ob_get_clean();

		return $output;
	}
}


?>