<?php
if ( ! defined( 'ABSPATH' ) ) {
  // Exit if accessed directly.
  exit;
}

// Use your own prefix, i use "wsm_free_", replace it;
$wsm_icon_path = plugins_url( '/images/icon-128x128.gif' , __FILE__);
$wsm_rating_url = "https://wordpress.org/support/plugin/wp-stats-manager/reviews/?filter=5#new-post";
$wsm_activation_time = 604800; // 7 days in seconds
$wsm_file_version = 2.2;
$wsm_development_mode = false; // Put yes to allow development mode, you will see the rating notice without timers

/**
* @since  1.9
* @version 1.9
* @class wsm_free_Notification
*/

if ( ! class_exists( 'wsm_free_Notification' ) ) :

  class wsm_free_Notification {
	
	/* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

      $this->_hooks();
    }

    /**
    * Hook into actions and filters
    * @since  1.0.0
    * @version 1.2.1
    */
    private function _hooks() {
      add_action( 'admin_init', array( $this, 'wsm_free_review_notice' ) );
    }
	
	/**
  	 * Ask users to review our plugin on wordpress.org
  	 *
  	 * @since 1.0.11
  	 * @return boolean false
  	 * @version 1.1.3
  	 */
  	public function wsm_free_review_notice() {
		
		global $wsm_file_version, $wsm_activation_time, $wsm_development_mode;
		
		$this->wsm_free_review_dismissal();
		
  		$this->wsm_free_review_pending();
		
		$wsm_activation_time 	= get_site_option( 'wsm_free_active_time' );
		
  		$review_dismissal	= get_site_option( 'wsm_free_review_dismiss' );
		
		if ($review_dismissal == 'yes' && !$wsm_development_mode) return;
		
		if ( !$wsm_activation_time && !$wsm_development_mode ) :

  			$wsm_activation_time = time(); // Reset Time to current time.
  			add_site_option( 'wsm_free_active_time', $wsm_activation_time );
			
  		endif;
		if ($wsm_development_mode) $wsm_activation_time = 432001; //This variable used to show the message always for testing purposes only
  		// 432000 = 5 Days in seconds.
  		if ( time() - $wsm_activation_time > 432000 ) :
		
			wp_enqueue_style( 'wsm_free_review_stlye', plugins_url( '/css/style-review.css', __FILE__ ), array(), $wsm_file_version );
			add_action( 'admin_notices' , array( $this, 'wsm_free_review_notice_message' ) );
		
		endif;
  	}

    /**
  	 *	Check and Dismiss review message.
  	 *
  	 *	@since 1.9
  	 */
  	private function wsm_free_review_dismissal() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'wsm_free_review-nonce' ) ||
  			! isset( $_GET['wsm_free_review_dismiss'] ) ) :

  			return;
  		endif;

  		add_site_option( 'wsm_free_review_dismiss', 'yes' );
  	}

    /**
  	 * Set time to current so review notice will popup after 14 days
  	 *
  	 * @since 1.9
  	 */
  	private function wsm_free_review_pending() {

  		if ( ! is_admin() ||
  			! current_user_can( 'manage_options' ) ||
  			! isset( $_GET['_wpnonce'] ) ||
  			! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'wsm_free_review-nonce' ) ||
  			! isset( $_GET['wsm_free_review_later'] ) ) :

  			return;
  		endif;

  		// Reset Time to current time.
  		update_site_option( 'wsm_free_active_time', time() );
  	}

    /**
  	 * Review notice message
  	 *
  	 * @since  1.0.11
  	 */
  	public function wsm_free_review_notice_message() {

  		$scheme      = ( wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ) ) ? '&' : '?';
  		$url         = $_SERVER['REQUEST_URI'] . $scheme . 'wsm_free_review_dismiss=yes';
  		$dismiss_url = wp_nonce_url( $url, 'wsm_free_review-nonce' );

  		$_later_link = $_SERVER['REQUEST_URI'] . $scheme . 'wsm_free_review_later=yes';
  		$later_url   = wp_nonce_url( $_later_link, 'wsm_free_review-nonce' );
		
		global $wsm_icon_path;
		
		global $wsm_rating_url;
      ?>

  		<div class="wsm_free_review-notice">
  			<div class="wsm_free_review-thumbnail">
  				<img src="<?php echo esc_url($wsm_icon_path); ?>" alt="">
  			</div>
  			<div class="wsm_free_review-text">
  				<h3><?php _e( 'Leave A Review?', 'wp-stats-manager' ) ?></h3>
  				<p><?php _e( 'We hope you\'ve enjoyed using Visitor Statistics :) Would you mind taking a few minutes to write a review on WordPress.org?<br>Just writing simple "thank you" will make us happy!', 'wp-stats-manager' ) ?></p>
  				<ul class="wsm_free_review-ul">
            <li><a href="<?php echo esc_url($wsm_rating_url); ?>" target="_blank"><span class="dashicons dashicons-external"></span><?php _e( 'Sure! I\'d love to!', 'wp-stats-manager' ) ?></a></li>
            <li><a href="<?php echo esc_url($dismiss_url); ?>"><span class="dashicons dashicons-smiley"></span><?php _e( 'I\'ve already left a review', 'wp-stats-manager' ) ?></a></li>
            <li><a href="<?php echo esc_url($later_url); ?>"><span class="dashicons dashicons-calendar-alt"></span><?php _e( 'Will Rate Later', 'wp-stats-manager' ) ?></a></li>
            <li><a href="<?php echo esc_url($dismiss_url); ?>"><span class="dashicons dashicons-dismiss"></span><?php _e( 'Hide Forever', 'wp-stats-manager' ) ?></a></li></ul>
  			</div>
  		</div>
  	<?php
  	}
}

endif;
$admincore = '';
	if (isset($_GET['page'])) $admincore = sanitize_text_field($_GET['page']);
	if($admincore != 'wsmoptionspro') {
		new wsm_free_Notification();
	}
?>