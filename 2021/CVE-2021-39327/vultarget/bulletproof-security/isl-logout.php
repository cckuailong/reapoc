<?php
error_reporting(0);
/*
Notes: 
This ISL template file can be directly accessed in a Browser by everyone/anyone without affecting anything or anyone else negatively.
wp_logout(): Log the current user out, by destroying the current user session. 
removeEventListener: is Client Browser specific and does not affect anyone else except for the Browser that calls this template file.
*/
if ( file_exists( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' ) ) {
	require_once('../../../wp-load.php');
}

require( ABSPATH . WPINC . '/pluggable.php' );

	wp_logout();	

	$BPS_ISL_options = get_option('bulletproof_security_options_idle_session');	
	$bpsProLog = WP_CONTENT_DIR . '/bps-backup/logs/http_error_log.txt';
	$hostname = @gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$timeNow = time();
	$gmt_offset = get_option( 'gmt_offset' ) * 3600;
	$query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

	if ( ! get_option( 'gmt_offset' ) ) {
		$timestamp = date("F j, Y g:i a", time() );
	} else {
		$timestamp = date_i18n(get_option('date_format'), strtotime("11/15-1976")) . ' - ' . date_i18n(get_option('time_format'), $timeNow + $gmt_offset);
	}

@$log_contents = "\r\n" . '[Idle Session Logout: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT']."\r\n";

	if ( is_writable( $bpsProLog ) ) {

	if ( ! $handle = fopen( $bpsProLog, 'a' ) ) {
         exit;
    }

    if ( fwrite( $handle, $log_contents) === FALSE ) {
        exit;
    }

    fclose($handle);
	}

	if ( $BPS_ISL_options['bps_isl_logout_url'] != '' && $BPS_ISL_options['bps_isl_logout_url'] != plugins_url('/bulletproof-security/isl-logout.php') ) {
		header("Location: ". $BPS_ISL_options['bps_isl_logout_url']);
		exit;	
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login Session Expired</title>

<style type="text/css">
<!--
body {<?php echo $BPS_ISL_options['bps_isl_custom_css_1']; ?>}
#bpsMessage {<?php echo $BPS_ISL_options['bps_isl_custom_css_2']; ?>}
#bpsMessageTextBox {<?php echo $BPS_ISL_options['bps_isl_custom_css_3']; ?>}
p {<?php echo $BPS_ISL_options['bps_isl_custom_css_4']; ?>}
-->
</style>

</head>

<body>

<script type="text/javascript">
/* <![CDATA[ */
// Remove load event handler
window.removeEventListener("load", bpsClearTimeout);
// Remove keypress event handler
document.removeEventListener("keypress", bpsClearTimeout);
// Remove mousemove event handler
document.removeEventListener("mousemove", bpsClearTimeout);
// Remove mousedown event handler
document.removeEventListener("mousedown", bpsClearTimeout);
// Remove wheel event handler
document.removeEventListener("wheel", bpsClearTimeout);
// Remove touchstart event handler.
document.removeEventListener("touchstart", bpsClearTimeout);
// Remove touchmove event handler
document.removeEventListener("touchmove", bpsClearTimeout);

var bpsTimeout;

function bpsClearTimeout() {
	clearTimeout(bpsTimeout);
}
/* ]]> */
</script>

<div id="bpsMessage"> 

<?php 
	$bps_hostname = str_replace( 'www.', '', htmlspecialchars( $_SERVER['SERVER_NAME'], ENT_QUOTES ) );
	
	if ( $BPS_ISL_options['bps_isl_login_url'] != '' ) {
		$url = $BPS_ISL_options['bps_isl_login_url'];
	} else {
		$url = site_url( '/wp-login.php' );
	}
	
	if ( $BPS_ISL_options['bps_isl_custom_message'] != '' ) {
		
		echo '<div id="bpsMessageTextBox">';
		$custom_message = preg_replace( '/\n/', "<br>", esc_html($BPS_ISL_options['bps_isl_custom_message']) );
		echo '<p>' . $custom_message . '</p>';
		if ( $BPS_ISL_options['bps_isl_login_url'] != 'No' && $BPS_ISL_options['bps_isl_login_url'] != 'no' ) {
		echo '<p><a href="' . filter_var( $url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED ) . '" style="text-decoration:underline;">Login</a></p>';
		echo '<p style="font-size:12px">BPS Plugin Idle Session Logout Page</p>';
		}
		echo '</div>';
	
	} else {

		echo '<div id="bpsMessageTextBox">';
		echo '<p>' . $bps_hostname . ' Login Session Expired</p>';  
   		echo '<p>Your Login Session has expired due to inactivity.</p>';
		echo '<p>Idle Session Logout (ISL) Time: ' . $BPS_ISL_options['bps_isl_timeout'] . ' minutes.</p>';
		if ( $BPS_ISL_options['bps_isl_login_url'] != 'No' && $BPS_ISL_options['bps_isl_login_url'] != 'no' ) {
		echo '<p><a href="' . filter_var( $url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED ) . '" style="text-decoration:underline;">Login</a> again.</p>';
		echo '<p style="font-size:12px">BPS Plugin Idle Session Logout Page</p>';
		}
		echo '</div>';
	}
?>

</div>
</body>
</html>