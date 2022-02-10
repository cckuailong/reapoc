<?php
/*
	This file handles the support licensing control for Paid Memberships Pro
	and PMPro addons.
	
	How it works:
	- All source code and resource files bundled with this plugin are licensed under the GPLv2 license unless otherwise noted (e.g. included third-party libraries).
	- An additional "support license" can be purchased at https://www.paidmembershipspro.com/pricing/
	  which will simultaneous support the development of this plugin and also give you access to support forums and documentation.
	- Once your license has been purchased, visit Settings --> PMPro License in your WP dashboard to enter your license.
	- Once the license is activated all "nags" will be disabled in the dashboard and member links will be added where appropriate.
    - This plugin will function 100% even if the support license is not installed.
    - If no support license is detected on this site, prompts will show in the admin to encourage you to purchase one.
	- You can override these prompts by setting the PMPRO_LICENSE_NAG constant to false.
*/

/*
	Developers, add this line to your wp-config.php to remove PMPro license nags even if no license has been purchased.
	
	define('PMPRO_LICENSE_NAG', false);	//consider purchasing a license at https://www.paidmembershipspro.com/pricing/
*/

/*
	Constants
*/
define('PMPRO_LICENSE_SERVER', 'https://license.paidmembershipspro.com/');

/**
 * Check if a license key is valid.
 * @param string $key   The key to check.
 * @param string $type  If passed will also check that the key is this type.
 * @param bool   $force If true, will check key against the PMPro server.
 * @return bool True if valid, false if not.
 */
function pmpro_license_isValid($key = NULL, $type = NULL, $force = false) {		
	// Get key from options if non passed in.
	if( empty( $key ) ) {
		$key = get_option("pmpro_license_key", "");
	}
	
	// No key? Clean up options and return false.
	if ( empty( $key ) ) {
		delete_option('pmpro_license_check');
		add_option('pmpro_license_check', array('license'=>false, 'enddate'=>0), NULL, 'no');
		return false;
	}
	
	// If force was passed in, let's check with the server.
	if ( $force ) {
		$pmpro_license_check = pmpro_license_check_key( $key );
	}
	
	// Get license check value from options.
	$pmpro_license_check = get_option( 'pmpro_license_check', false );

	// No license info from server?
	if ( empty( $pmpro_license_check ) ) {
		return false;
	}
	
	// Server check errored out?
	if ( is_wp_error( $pmpro_license_check ) ) {
		return false;
	}
	
	// Check if 30 days past the end date. (We only run the cron every 30 days.)
	if ( $pmpro_license_check['enddate'] < ( current_time( 'timestamp' ) + 86400*31 ) ) {
		return false;
	}
	
	// Check if a specific type.
	if ( ! empty( $type ) && $type != $pmpro_license_check['license'] ) {
		return false;
	}
	
	// If we got here, we should be good.
	return true;
}

/*
	Activation/Deactivation. Check keys once a month.
*/
//activation
function pmpro_license_activation() {
	pmpro_maybe_schedule_event(current_time('timestamp'), 'monthly', 'pmpro_license_check_key');
}
register_activation_hook(__FILE__, 'pmpro_activation');

//deactivation
function pmpro_license_deactivation() {
	wp_clear_scheduled_hook('pmpro_license_check_key');
}
register_deactivation_hook(__FILE__, 'pmpro_deactivation');

/**
 * Check a key against the PMPro license server.
 * Runs via cron every month.
 * @param string          The key to check.
 * @return array|WP_Error Returns an array with the key and enddate
 *                        or WP_Error if invalid or there was an error.
 */
function pmpro_license_check_key($key = NULL) {
	global $pmpro_license_error;
	
	// Get key from options if non passed in.
	if( empty( $key ) ) {
		$key = get_option("pmpro_license_key", "");
	}
	
	// No key? Return error.
	if ( empty ( $key ) ) {
		return new WP_Error ( 'no_key', __( 'Missing key.', 'paid-memberships-pro' ) );
	}
	
	/**
     * Filter to change the timeout for this wp_remote_get() request.
     *
     * @since 1.8.5.1
     *
     * @param int $timeout The number of seconds before the request times out
     */
    $timeout = apply_filters( 'pmpro_license_check_key_timeout', 5 );

	$url = add_query_arg(array('license'=>$key, 'domain'=>site_url()), PMPRO_LICENSE_SERVER);
    $r = wp_remote_get( $url, array( "timeout" => $timeout ) );

    // Trouble connecting?
    if( is_wp_error( $r ) ) {
		// Connection error.
		return new WP_Error( 'connection_error', $r->get_error_message() );
    }
	
	// Bad response code?
	if ( $r['response']['code'] !== 200 ) {
		return new WP_Error( 'bad_response_code', __( sprintf( 'Bad response code %s.', 'paid-memberships-pro' ), $r['response']['code'] ) );
	}
		
    // Process the response.
	$r = json_decode($r['body']);			
	if( $r->active == 1 ) {
		// Get end date. If none, let's set it 1 year out.
		if( ! empty( $r->enddate ) ) {
			$enddate = strtotime( $r->enddate, current_time( 'timestamp' ) );
		} else {
			$enddate = strtotime( '+1 Year', current_time( 'timestamp' ) );
		}			

		$license_check = array( 'license' => $r->license, 'enddate' => $enddate );
		update_option( 'pmpro_license_check', $license_check, 'no' );
		
		return $license_check;
	} elseif ( ! empty( $r->error ) ) {	
		// Invalid key. Let's clear out the option.
		update_option( 'pmpro_license_check', array('license'=>false, 'enddate'=>0), 'no' );
		
		return new WP_Error( 'invalid_key', $r->error );
	} else {
		// Unknown error. We should maybe clear out the option, but we're not.
		return new WP_Error( 'unknown_error', __( 'Unknown error.', 'paid-memberships-pro' ) );
	}
}
add_action('pmpro_license_check_key', 'pmpro_license_check_key');
