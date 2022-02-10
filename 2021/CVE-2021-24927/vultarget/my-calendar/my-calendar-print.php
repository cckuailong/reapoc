<?php
/**
 * Output the print view.
 *
 * @category Calendar
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', 'my_calendar_print_view' );
/**
 * Redirect to print view if query set.
 */
function my_calendar_print_view() {
	if ( isset( $_GET['cid'] ) && 'mc-print-view' === $_GET['cid'] ) {
		echo my_calendar_print();
		exit;
	}
}

/**
 * Produce print view output.
 */
function my_calendar_print() {
	$url      = plugin_dir_url( __FILE__ );
	$time     = ( isset( $_GET['time'] ) ) ? $_GET['time'] : 'month';
	$category = ( isset( $_GET['mcat'] ) ) ? $_GET['mcat'] : ''; // These are sanitized elsewhere.
	$ltype    = ( isset( $_GET['ltype'] ) ) ? $_GET['ltype'] : '';
	$lvalue   = ( isset( $_GET['lvalue'] ) ) ? $_GET['lvalue'] : '';
	header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );
	if ( mc_file_exists( 'mc-print.css' ) ) {
		$stylesheet = mc_get_file( 'mc-print.css', 'url' );
	} else {
		$stylesheet = $url . 'css/mc-print.css';
	}
	$rtl  = ( is_rtl() ) ? 'rtl' : 'ltr';
	$head = '<!DOCTYPE html>
<html dir="' . $rtl . '" lang="' . get_bloginfo( 'language' ) . '">
<!--<![endif]-->
<head>
<meta charset="' . get_bloginfo( 'charset' ) . '" />
<meta name="viewport" content="width=device-width" />
<title>' . get_bloginfo( 'name' ) . ' - ' . __( 'Calendar: Print View', 'my-calendar' ) . '</title>
<meta name="generator" content="My Calendar for WordPress" />
<meta name="robots" content="noindex,nofollow" />
<!-- Copy mc-print.css to your theme directory if you wish to replace the default print styles -->
<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen,print" />' . do_action( 'mc_print_view_head', '' ) . '
</head>
<body>';
	echo $head;
	$args = array(
		'type'     => 'print',
		'category' => $category,
		'time'     => $time,
		'ltype'    => $ltype,
		'lvalue'   => $lvalue,
	);

	$calendar = array(
		'name'     => 'print',
		'format'   => 'calendar',
		'category' => $category,
		'time'     => $time,
		'ltype'    => $ltype,
		'lvalue'   => $lvalue,
		'id'       => 'mc-print-view',
		'below'    => 'none',
		'above'    => 'none',
	);

	echo my_calendar( $calendar );
	$return_url = mc_get_uri( false, $args );
	$return_url = apply_filters( 'mc_print_return_url', $return_url, $category, $time, $ltype, $lvalue );

	if ( isset( $_GET['href'] ) ) {
		$ref_url = esc_url( urldecode( $_GET['href'] ) );
		if ( $ref_url ) {
			$return_url = $ref_url;
		}
	}

	$add = array_map( 'esc_html', $_GET );
	unset( $add['cid'] );
	unset( $add['feed'] );
	unset( $add['href'] );
	$return_url = apply_filters( 'mc_return_to_calendar', mc_build_url( $add, array( 'feed', 'cid', 'href', 'searched' ), $return_url ), $add );
	if ( $return_url ) {
		echo "<p class='return'>&larr; <a href='$return_url'>" . __( 'Return to calendar', 'my-calendar' ) . '</a></p>';
	}
	echo '
</body>
</html>';
}
