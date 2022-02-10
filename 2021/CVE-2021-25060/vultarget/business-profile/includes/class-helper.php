<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bpfwpHelper' ) ) {
/**
 * Class to to provide helper functions
 *
 * @since 2.1.6
 */
class bpfwpHelper {

  // Hold the class instance.
  private static $instance = null;

  /**
   * The constructor is private
   * to prevent initiation with outer code.
   * 
   **/
  private function __construct() {}

  /**
   * The object is created from within the class itself
   * only if the class has no instance.
   */
  public static function getInstance() {

    if ( self::$instance == null ) {

      self::$instance = new bpfwpHelper();
    }
 
    return self::$instance;
  }

  /**
   * Handle ajax requests from the admin bookings area from logged out users
   * @since 2.1.6
   */
  public static function admin_nopriv_ajax() {

    wp_send_json_error(
      array(
        'error' => 'loggedout',
        'msg' => sprintf( __( 'You have been logged out. Please %slogin again%s.', 'business-profile' ), '<a href="' . wp_login_url( admin_url( 'admin.php?page=bpfwp-dashboard' ) ) . '">', '</a>' ),
      )
    );
  }

  /**
   * Handle ajax requests where an invalid nonce is passed with the request
   * @since 2.1.6
   */
  public static function bad_nonce_ajax() {

    wp_send_json_error(
      array(
        'error' => 'badnonce',
        'msg' => __( 'The request has been rejected because it does not appear to have come from this site.', 'business-profile' ),
      )
    );
  }
}

}