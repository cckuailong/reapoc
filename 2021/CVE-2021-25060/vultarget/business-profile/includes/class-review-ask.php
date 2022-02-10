<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bpfwpReviewAsk' ) ) {
/**
 * Class to handle plugin review ask
 *
 * @since 2.0.4
 */
class bpfwpReviewAsk {

	public function __construct() {

		add_action( 'admin_notices', array( $this, 'maybe_add_review_ask' ) );

		add_action( 'wp_ajax_bpfwp_hide_review_ask', array( $this, 'hide_review_ask' ) );
		add_action( 'wp_ajax_bpfwp_send_feedback', array( $this, 'send_feedback' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_review_ask_scripts') );
	}

	public function maybe_add_review_ask() {

		$ask_review_time = get_option( 'bpfwp-review-ask-time' );

		$install_time = get_option( 'bpfwp-installation-time' );
		if ( ! $install_time ) { update_option( 'bpfwp-installation-time', time() ); }

		$ask_review_time = $ask_review_time != '' ? $ask_review_time : $install_time + 3600*24*4;

		if ($ask_review_time < time() and $install_time != '' and $install_time < time() - 3600*24*4) {
			
			global $pagenow;

			if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) { ?>
	
				<div class='notice notice-info is-dismissible bpfwp-main-dashboard-review-ask' style='display:none'>
					<div class='bpfwp-review-ask-plugin-icon'></div>
					<div class='bpfwp-review-ask-text'>
						<p class='bpfwp-review-ask-starting-text'>Enjoying using the Five-Star Business Profile and Schema plugin?</p>
						<p class='bpfwp-review-ask-feedback-text bpfwp-hidden'>Help us make the plugin better! Please take a minute to rate the plugin. Thanks!</p>
						<p class='bpfwp-review-ask-review-text bpfwp-hidden'>Please let us know what we could do to make the plugin better!<br /><span>(If you would like a response, please include your email address.)</span></p>
						<p class='bpfwp-review-ask-thank-you-text bpfwp-hidden'>Thank you for taking the time to help us!</p>
					</div>
					<div class='bpfwp-review-ask-actions'>
						<div class='bpfwp-review-ask-action bpfwp-review-ask-not-really bpfwp-review-ask-white'>Not Really</div>
						<div class='bpfwp-review-ask-action bpfwp-review-ask-yes bpfwp-review-ask-green'>Yes!</div>
						<div class='bpfwp-review-ask-action bpfwp-review-ask-no-thanks bpfwp-review-ask-white bpfwp-hidden'>No Thanks</div>
						<a href='https://wordpress.org/support/plugin/business-profile/reviews/' target='_blank'>
							<div class='bpfwp-review-ask-action bpfwp-review-ask-review bpfwp-review-ask-green bpfwp-hidden'>OK, Sure</div>
						</a>
					</div>
					<div class='bpfwp-review-ask-feedback-form bpfwp-hidden'>
						<div class='bpfwp-review-ask-feedback-explanation'>
							<textarea></textarea>
							<br>
							<input type="email" name="feedback_email_address" placeholder="<?php _e('Email Address', 'business-profile'); ?>">
						</div>
						<div class='bpfwp-review-ask-send-feedback bpfwp-review-ask-action bpfwp-review-ask-green'>Send Feedback</div>
					</div>
					<div class='bpfwp-clear'></div>
				</div>

			<?php
			}
		}
		else {
			wp_dequeue_script( 'bpfwp-review-ask-js' );
		}
	}

	public function enqueue_review_ask_scripts() {

		wp_enqueue_style( 'bpfwp-review-ask-css', BPFWP_PLUGIN_URL . '/assets/css/dashboard-review-ask.css', array(),BPFWP_VERSION );
		wp_enqueue_script( 'bpfwp-review-ask-js', BPFWP_PLUGIN_URL . '/assets/js/dashboard-review-ask.js', array( 'jquery' ), BPFWP_VERSION, true  );

		wp_localize_script(
			'bpfwp-review-ask-js',
			'bpfwp_review_ask',
			array(
				'nonce' => wp_create_nonce( 'bpfwp-review-ask-js' )
			)
		);
	}

	public function hide_review_ask() {

		// Authenticate request
		if ( ! check_ajax_referer( 'bpfwp-review-ask-js', 'nonce' ) ) {

			bpfwpHelper::admin_nopriv_ajax();
		}

		$ask_review_time = sanitize_text_field( $_POST['ask_review_time'] );

		if ( get_option( 'bpfwp-review-ask-time' ) < time() + 3600*24 * $ask_review_time ) {
			update_option( 'bpfwp-review-ask-time', time() + 3600*24 * $ask_review_time );
		}

		die();
	}

	public function send_feedback() {

		// Authenticate request
		if ( ! check_ajax_referer( 'bpfwp-review-ask-js', 'nonce' ) ) {
			
			bpfwpHelper::admin_nopriv_ajax();
		}
		
		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
		$feedback = sanitize_text_field( $_POST['feedback'] );
		$feedback .= '<br /><br />Email Address: ';
		$feedback .= sanitize_email( $_POST['email_address'] );

		wp_mail('contact@fivestarplugins.com', 'BPFWP Feedback - Dashboard Form', $feedback, $headers);

		die();
	}
}

}