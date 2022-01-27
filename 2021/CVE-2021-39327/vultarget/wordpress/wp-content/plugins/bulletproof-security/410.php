<?php ob_start(); ?>
<?php session_cache_limiter('nocache'); ?>
<?php session_start(); ?>
<?php error_reporting(0); ?>
<?php session_destroy(); ?>
<?php
# BEGIN HEADERS
header($_SERVER['SERVER_PROTOCOL'].' 410 Gone', true, 410);
header('Status: 410 Gone');
header('Content-type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate' ); 
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache' );
# END HEADERS
#
# Forum Topic with Help Info & Examples: https://forum.ait-pro.com/forums/topic/410-htaccess-redirect-redirect-html-files-redirect-query-strings-redirect-posts-or-categories/
#
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>410 Gone</title>
<style type="text/css">
<!--
body { 
	/* If you want to add a background image uncomment the CSS properties below */
	/* background-image:url(http://www.example.com/wp-content/plugins/bulletproof-security/abstract-blue-bg.jpg); /*
	/* background-repeat:repeat; */
	background-color:#CCCCCC;
	line-height: normal;
}

#bpsMessage {
	text-align:center; 
	background-color: #F7F8F9; 
	border:5px solid #000000; 
	padding:10px;
}

p {
    font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size:18px;
	font-weight:bold;
}
-->
</style>
</head>

<body>
<div id="bpsMessage">
	<p><?php $bps_hostname = str_replace( 'www.', '', htmlspecialchars( $_SERVER['SERVER_NAME'], ENT_QUOTES ) );
	echo $bps_hostname; ?> 410 Gone Request</p>
	<p>This page no longer exists.</p>
    <p style="font-size:12px">BPS Plugin 410 Error Page</p>
</div>

<?php 
if ( file_exists( dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php' ) ) {
	require_once('../../../wp-load.php');
}

// NOTE: fwrite is faster in benchmark tests than file_put_contents for successive writes
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

		$event = '410 Gone';
		$solution = 'N/A - 410 Gone - Not an Attack';	
	
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {

@$log_contents = "\r\n" . '[410 Gone POST Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT']."\r\n";

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

	if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
	
@$log_contents = "\r\n" . '[410 Gone GET Request: ' . $timestamp . ']' . "\r\n" . 'BPS: ' . $bps_version . "\r\n" . 'WP: ' . $wp_version . "\r\n" . 'Event Code: ' . $event . "\r\n" . 'Solution: ' . $solution . "\r\n" . 'REMOTE_ADDR: '.$bpsPro_remote_addr . "\r\n" . 'Host Name: ' . $hostname . "\r\n" . 'SERVER_PROTOCOL: ' . $_SERVER['SERVER_PROTOCOL'] . "\r\n" . 'HTTP_CLIENT_IP: ' . $bpsPro_http_client_ip . "\r\n" . 'HTTP_FORWARDED: ' . $bpsPro_http_forwarded . "\r\n" . 'HTTP_X_FORWARDED_FOR: ' . $bpsPro_http_x_forwarded_for . "\r\n" . 'HTTP_X_CLUSTER_CLIENT_IP: ' . $bpsPro_http_x_cluster_client_ip."\r\n" . 'REQUEST_METHOD: '.$_SERVER['REQUEST_METHOD']."\r\n" . 'HTTP_REFERER: '.$_SERVER['HTTP_REFERER']."\r\n" . 'REQUEST_URI: '.$_SERVER['REQUEST_URI']."\r\n" . 'QUERY_STRING: '.$query_string."\r\n" . 'HTTP_USER_AGENT: '.$_SERVER['HTTP_USER_AGENT']."\r\n";

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

?>
</body>
</html>
<?php ob_end_flush(); ?>