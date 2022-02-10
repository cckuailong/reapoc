<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'rtbReviewAsk' ) ) {
/**
 * Class to handle plugin review ask
 *
 * @since 2.0.15
 */
class rtbReviewAsk {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'maybe_add_review_ask' ) );


		add_action( 'wp_ajax_nopriv_rtb-hide-review-ask' , array( 'rtbHelper' , 'admin_nopriv_ajax' ) );
		add_action( 'wp_ajax_rtb-hide-review-ask', array( $this, 'hide_review_ask' ) );

		add_action( 'wp_ajax_nopriv_rtb-send-feedback' , array( 'rtbHelper' , 'admin_nopriv_ajax' ) );
		add_action( 'wp_ajax_rtb-send-feedback', array( $this, 'send_feedback' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_review_ask_scripts') );
	}

	public function maybe_add_review_ask() {
		$ask_review_time = get_option( 'rtb-review-ask-time' );

		$install_time = get_option( 'rtb-installation-time' );
		if ( ! $install_time ) { update_option( 'rtb-installation-time', time() ); }

		$ask_review_time = $ask_review_time != '' ? $ask_review_time : $install_time + 3600*24*4;

		if ($ask_review_time < time() and $install_time != '' and $install_time < time() - 3600*24*4) {
			
			global $pagenow;

			if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) { ?>
	
				<div class='notice notice-info is-dismissible rtb-main-dashboard-review-ask' style='display:none'>
					<div class='rtb-review-ask-plugin-icon'></div>
					<div class='rtb-review-ask-text'>
						<p class='rtb-review-ask-starting-text'>Enjoying using the Five-Star Restaurant Reservations?</p>
						<p class='rtb-review-ask-feedback-text rtb-hidden'>Help us make the plugin better! Please take a minute to rate the plugin. Thanks!</p>
						<p class='rtb-review-ask-review-text rtb-hidden'>Please let us know what we could do to make the plugin better!<br /><span>(If you would like a response, please include your email address.)</span></p>
						<p class='rtb-review-ask-thank-you-text rtb-hidden'>Thank you for taking the time to help us!</p>
					</div>
					<div class='rtb-review-ask-actions'>
						<div class='rtb-review-ask-action rtb-review-ask-not-really rtb-review-ask-white'>Not Really</div>
						<div class='rtb-review-ask-action rtb-review-ask-yes rtb-review-ask-green'>Yes!</div>
						<div class='rtb-review-ask-action rtb-review-ask-no-thanks rtb-review-ask-white rtb-hidden'>No Thanks</div>
						<a href='https://wordpress.org/support/plugin/restaurant-reservations/reviews/' target='_blank'>
							<div class='rtb-review-ask-action rtb-review-ask-review rtb-review-ask-green rtb-hidden'>OK, Sure</div>
						</a>
					</div>
					<div class='rtb-review-ask-feedback-form rtb-hidden'>
						<div class='rtb-review-ask-feedback-explanation'>
							<textarea></textarea>
							<br>
							<input type="email" name="feedback_email_address" placeholder="<?php _e('Email Address', 'restaurant-reservations'); ?>">
						</div>
						<div class='rtb-review-ask-send-feedback rtb-review-ask-action rtb-review-ask-green'>Send Feedback</div>
					</div>
					<div class='rtb-clear'></div>
				</div>

			<?php
			}
		}
		else {
			wp_dequeue_script( 'rtb-review-ask-js' );
		}
	}

	public function enqueue_review_ask_scripts() {
		wp_enqueue_style( 'rtb-review-ask-css', RTB_PLUGIN_URL . '/assets/css/dashboard-review-ask.css' );
		wp_enqueue_script( 'rtb-review-ask-js', RTB_PLUGIN_URL . '/assets/js/dashboard-review-ask.js', array( 'jquery' ), RTB_VERSION, true  );
		wp_localize_script(
			'rtb-review-ask-js',
			'rtb_review_ask',
			array(
				'nonce' => wp_create_nonce( 'rtb-review-ask' )
			)
		);
	}

	public function hide_review_ask() {

		// Authenticate request
		if ( !check_ajax_referer( 'rtb-review-ask', 'nonce' ) || !current_user_can( 'manage_bookings' ) ) {
			rtbHelper::admin_nopriv_ajax();
		}

		$ask_review_time = sanitize_text_field( $_POST['ask_review_time'] );

		if ( get_option( 'rtb-review-ask-time' ) < time() + 3600*24 * $ask_review_time ) {
			update_option( 'rtb-review-ask-time', time() + 3600*24 * $ask_review_time );
		}

		die();
	}

	public function send_feedback() {

		// Authenticate request
		if ( !check_ajax_referer( 'rtb-review-ask', 'nonce' ) || !current_user_can( 'manage_bookings' ) ) {
			rtbHelper::admin_nopriv_ajax();
		}

		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
		
		$feedback = sanitize_text_field( $_POST['feedback'] );
		$feedback .= '<br /><br />Email Address: ';
		$feedback .= sanitize_text_field( $_POST['email_address'] );

		wp_mail( 'contact@fivestarplugins.com', 'RTB Feedback - Dashboard Form', $feedback, $headers );

		die();
	} 
}

}