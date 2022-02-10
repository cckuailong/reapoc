<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdupcpReviewAsk' ) ) {
/**
 * Class to handle plugin review ask
 *
 * @since 5.0.0
 */
class ewdupcpReviewAsk {

	public function __construct() {
		
		add_action( 'admin_notices', array( $this, 'maybe_add_review_ask' ) );

		add_action( 'wp_ajax_ewd_upcp_hide_review_ask', array( $this, 'hide_review_ask' ) );
		add_action( 'wp_ajax_ewd_upcp_send_feedback', array( $this, 'send_feedback' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_review_ask_scripts' ) );
	}

	public function maybe_add_review_ask() { 
		
		$ask_review_time = get_option( 'ewd-upcp-review-ask-time' );

		$install_time = get_option( 'ewd-upcp-installation-time' );
		if ( ! $install_time ) { update_option( 'ewd-upcp-installation-time', time() ); $install_time = time(); }

		$ask_review_time = $ask_review_time != '' ? $ask_review_time : $install_time + 3600*24*4;
		
		if ( $ask_review_time < time() and $install_time != '' and $install_time < time() - 3600*24*4 ) {
			
			global $pagenow;

			if ( $pagenow != 'post.php' && $pagenow != 'post-new.php' ) { ?>
	
				<div class='notice notice-info is-dismissible ewd-upcp-main-dashboard-review-ask' style='display:none'>
					<div class='ewd-upcp-review-ask-plugin-icon'></div>
					<div class='ewd-upcp-review-ask-text'>
						<p class='ewd-upcp-review-ask-starting-text'>Enjoying using the Ultimate Product Catalog?</p>
						<p class='ewd-upcp-review-ask-feedback-text ewd-upcp-hidden'>Help us make the plugin better! Please take a minute to rate the plugin. Thanks!</p>
						<p class='ewd-upcp-review-ask-review-text ewd-upcp-hidden'>Please let us know what we could do to make the plugin better!<br /><span>(If you would like a response, please include your email address.)</span></p>
						<p class='ewd-upcp-review-ask-thank-you-text ewd-upcp-hidden'>Thank you for taking the time to help us!</p>
					</div>
					<div class='ewd-upcp-review-ask-actions'>
						<div class='ewd-upcp-review-ask-action ewd-upcp-review-ask-not-really ewd-upcp-review-ask-white'>Not Really</div>
						<div class='ewd-upcp-review-ask-action ewd-upcp-review-ask-yes ewd-upcp-review-ask-green'>Yes!</div>
						<div class='ewd-upcp-review-ask-action ewd-upcp-review-ask-no-thanks ewd-upcp-review-ask-white ewd-upcp-hidden'>No Thanks</div>
						<a href='https://wordpress.org/support/plugin/ultimate-product-catalogue/reviews/' target='_blank'>
							<div class='ewd-upcp-review-ask-action ewd-upcp-review-ask-review ewd-upcp-review-ask-green ewd-upcp-hidden'>OK, Sure</div>
						</a>
					</div>
					<div class='ewd-upcp-review-ask-feedback-form ewd-upcp-hidden'>
						<div class='ewd-upcp-review-ask-feedback-explanation'>
							<textarea></textarea>
							<br>
							<input type="email" name="feedback_email_address" placeholder="<?php _e('Email Address', 'ultimate-product-catalogue'); ?>">
						</div>
						<div class='ewd-upcp-review-ask-send-feedback ewd-upcp-review-ask-action ewd-upcp-review-ask-green'>Send Feedback</div>
					</div>
					<div class='ewd-upcp-clear'></div>
				</div>

			<?php
			}
		}
		else {
			wp_dequeue_script( 'ewd-upcp-review-ask-js' );
			wp_dequeue_style( 'ewd-upcp-review-ask-css' );
		}
	}

	public function enqueue_review_ask_scripts() {
		wp_enqueue_style( 'ewd-upcp-review-ask-css', EWD_UPCP_PLUGIN_URL . '/assets/css/dashboard-review-ask.css' );
		wp_enqueue_script( 'ewd-upcp-review-ask-js', EWD_UPCP_PLUGIN_URL . '/assets/js/dashboard-review-ask.js', array( 'jquery' ), EWD_UPCP_VERSION, true  );
	}

	public function hide_review_ask() {

		$ask_review_time = sanitize_text_field($_POST['ask_review_time']);

    	if ( get_option( 'ewd-upcp-review-ask-time' ) < time() + 3600*24 * $ask_review_time ) {
    		update_option( 'ewd-upcp-review-ask-time', time() + 3600*24 * $ask_review_time );
    	}

    	die();
	}

	public function send_feedback() {
		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
	    $feedback = sanitize_text_field($_POST['feedback']);
 		$feedback .= '<br /><br />Email Address: ';
    	$feedback .= sanitize_text_field($_POST['email_address']);

    	wp_mail('contact@etoilewebdesign.com', 'UPCP Feedback - Dashboard Form', $feedback, $headers);

    	die();
	} 
}

}