<!-- BEGIN COPY CODE - BPS Error logging code -->

<?php 
// Copy this Security Log logging code from BEGIN COPY CODE above to END COPY CODE below and paste it right after <?php get_header(); > in
// your Theme's 404.php template file located in your themes folder /wp-content/themes/your-theme-folder-name/404.php.
// NOTE: fwrite is faster in benchmark tests than file_put_contents for successive writes
$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
$hostname = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
$timeNow = time();
$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	
// Setup Wizard Options: GDPR Compliance Global Variables
$GDPR_Options = get_option('bulletproof_security_options_gdpr');

if ( $GDPR_Options['bps_gdpr_on_off'] != 'On' ) {

	$bpsPro_remote_addr = $_SERVER['REMOTE_ADDR'];
	$bpsPro_http_client_ip = $_SERVER['HTTP_CLIENT_IP'];
	$bpsPro_http_forwarded = $_SERVER['HTTP_FORWARDED'];		
	$bpsPro_http_x_forwarded_for = $_SERVER['HTTP_X_FORWARDED_FOR'];	
	$bpsPro_http_x_cluster_client_ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];	

} else {
	
	$bpsPro_remote_addr = 'GDPR Compliance On';
	$bpsPro_http_client_ip = 'GDPR Compliance On';
	$bpsPro_http_forwarded = 'GDPR Compliance On';		
	$bpsPro_http_x_forwarded_for = 'GDPR Compliance On';	
	$bpsPro_http_x_cluster_client_ip = 'GDPR Compliance On';		
}

	$post_limit = get_option('bulletproof_security_options_sec_log_post_limit'); 
	$query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

	if ( $post_limit['bps_security_log_post_none'] == '1' ) {
		$request_body = file_get_contents( 'php://input', NULL, NULL, 0, 5 );
	
	} else {
	
		if ( $post_limit['bps_security_log_post_limit'] == '1' ) {
			$request_body = file_get_contents( 'php://input', NULL, NULL, 0, 500 );
		} else {
			$request_body = file_get_contents( 'php://input', NULL, NULL, 0, 250000 ); // roughly 250KB Max Limit
		}
	}

	if ( ! get_option( 'gmt_offset' ) ) {
		$timestamp = date("F j, Y g:i a", time() );
	} else {
		$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	}

		$event = 'The server has not found anything matching the Request-URI.';
		$solution = 'N/A - 404 Not Found';	

	// 10.4: The BPS Rogue script killer will cause wp-admin 404 errors due to nulling/killing scripts in BPS plugin pages.
	// This condition below will prevent those 404 errors from being logged.
	if ( ! preg_match( '/page=bulletproof-security/', esc_html($_SERVER['HTTP_REFERER']), $matches) ) {

	// .52.7: Request Body condition added
	if ( ! empty($request_body) ) {

		if ( $post_limit['bps_security_log_post_none'] == '1' ) {
			$request_body = 'BPS Security Log option set to: Do Not Log POST Request Body Data';
		}

$log_contents = "\r\n" . '[404 POST Not Found Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT'] . "\r\n" . 'REQUEST BODY: ' . $request_body . "\r\n";

	if ( is_writable( $bpsProLog ) ) {

	if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
         exit;
    }

    if ( fwrite( $handle, $log_contents) === FALSE ) {
        exit;
    }

    fclose($handle);
	}
	}
	
	if ( empty($request_body) ) {

$log_contents = "\r\n" . '[404 GET Not Found Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT']."\r\n";

	if ( is_writable( $bpsProLog ) ) {

	if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
         exit;
    }

    if ( fwrite( $handle, $log_contents) === FALSE ) {
        exit;
    }

    fclose($handle);
	}
	}
	}
?>
<!-- END COPY CODE - BPS Error logging code -->