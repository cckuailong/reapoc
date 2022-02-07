<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('Damn it.! Dude you are looking for what?');
}
/**
 * The public-facing functionality of the plugin.
 *
 *
 * @link       http://iscode.co/product/404-to-301
 * @since      2.0.0
 * @package    I4T3
 * @subpackage I4T3/public
 * @author     Joel James <me@joelsays.com>
 */
class _404_To_301_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * The database table of plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $table    The name of the database table from db.
	 */
	private $table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $table ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table = $table;
		$this->gnrl_options = get_option( 'i4t3_gnrl_options' );
	}

	
	/**
	 * Create the 404 Log Email to be sent.
	 *
	 * @since    2.0.0
	 * @uses     get_option    To get admin email from database.
	 * @uses     get_bloginfo   To get site title.
	 */
	public function i4t3_send_404_log_email( $log_data ) {
		
		// Filter to change the email address used for admin notifications
		$admin_email = apply_filters( 'i4t3_notify_admin_email_address', get_option( 'admin_email' ) );
		
		// Action hook that will be performed before sending 404 error mail
		do_action( 'i4t3_before_404_email_log', $log_data );
		
		// Get the site name
		$site_name = get_bloginfo( 'name' );
		
		$headers[] = 'From: ' . $site_name . ' <' . $admin_email . '>' . "\r\n";
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$message  = '<p>'. __( 'Bummer! You have one more 404', '404-to-301' ) .'</p>';
		$message .= '<table>';
		$message .= '<tr>';
		$message .= '<th>'. __( 'IP Address', '404-to-301' ) .'</th>';
		$message .= '<td>' . $log_data['ip'] . '</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '<th>'. __( '404 Path', '404-to-301' ) .'</th>';
		$message .= '<td>' . $log_data['url'] . '</td>';
		$message .= '</tr>';
		$message .= '<tr>';
		$message .= '<th>'. __( 'User Agent', '404-to-301' ) .'</th>';
		$message .= '<td>' . $log_data['ua'] . '</td>';
		$message .= '</tr>';
		$message .= '</table>';

		$is_sent = wp_mail(
			$admin_email,
			__( 'Snap! One more 404 on ', '404-to-301' ) . $site_name,
			$message,
			$headers
		);
	}


	/**
	 * The main function to perform redirections and logs on 404s.
	 * Creating log for 404 errors, sending admin notification email if enables,
	 * redirecting visitors to the specific page etc. are done in this function.
	 *
	 * @since    2.0.0
	 * @uses     wp_redirect    To redirect to a given link.
	 * @uses     do_action   To add new action.
	 */
	public function i4t3_redirect_404() {
		
		// Check if 404 page and not admin side
		if ( is_404() && !is_admin() ) {
			
			// Get the settings options
			$logging_status = ( $this->gnrl_options['redirect_log'] ) ? $this->gnrl_options['redirect_log'] : 1;
			$redirect_type = ( $this->gnrl_options['redirect_type'] ) ? $this->gnrl_options['redirect_type'] : '301';
			// Get the email notification settings
			$is_email_send = ( !empty( $this->gnrl_options['email_notify'] ) ) ? true : false;
			// Get error details if emailnotification or log is enabled
			if( $logging_status == 1 || $is_email_send ) {
				
				// Action hook that will be performed before logging 404 errors
				do_action( 'i4t3_before_404_logging' );
				
				global $wpdb;
				$data = array( 
					'date' => current_time('mysql'),
					'url' => $_SERVER['REQUEST_URI']
				);
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$data['ip'] = $_SERVER['HTTP_CLIENT_IP'];
				} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$data['ip'] = $_SERVER['REMOTE_ADDR'];
				}
				$data['ref'] = $_SERVER['HTTP_REFERER'];
				$data['ua'] = $_SERVER['HTTP_USER_AGENT'];
				// trim stuff
				foreach( array( 'url', 'ref', 'ua' ) as $k )
					if( isset( $data[$k] ) )
						$data[$k] = substr( $data[$k], 0, 512 );
			}

			// Add log data to db if log is enabled by user
			if($logging_status == 1) {
			
				$wpdb->insert( $this->table, $data );
				
				// pop old entry if we exceeded the limit
				//$max = intval( $this->options['max_entries'] );
				$max = 500;
				$cutoff = $wpdb->get_var( "SELECT id FROM $this->table ORDER BY id DESC LIMIT $max,1" );
				if( $cutoff ) {
					$wpdb->delete( $this->table, array( 'id' => intval( $cutoff ) ), array( '%d' ) );
				}
			}

			// Send email notification if enabled
			if ($is_email_send) {
				$this->i4t3_send_404_log_email($data);
			}

			// Get redirect settings
			$redirect_to = $this->gnrl_options['redirect_to'];
			
			switch( $redirect_to ) {
				// Do not redirect if none is set
				case 'none':
					break;
				// Redirect to an existing WordPress site inside our site
				case 'page':
					$url = get_permalink( $this->gnrl_options['redirect_page'] );
					break;
				// Redirect to a custom link given by user
				case 'link':
					$naked_url = $this->gnrl_options['redirect_link'];
					$url = (!preg_match("~^(?:f|ht)tps?://~i", $naked_url)) ? "http://" . $naked_url : $naked_url;
					break;
				// If nothing, be chill and do nothing!
				default:
					break;
			}

			// Perform the redirect if $url is set
			if (!empty($url)) {
				// Action hook that will be performed before 404 redirect starts
				//echo $url; exit();
				do_action( 'i4t3_before_404_redirect' );
				wp_redirect( $url, $redirect_type );
				exit(); // exit, because WordPress will not exit automatically
			}
			// Action hook that will be performed after 404 redirect
			do_action( 'i4t3_after_404_redirect' );
		}
	}

}
