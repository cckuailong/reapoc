<?php
/**
 * Get params from the redirect page
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'get_param', 'wpcf7r_get_param' );
add_shortcode( 'wpcf7r_posted_param', 'wpcf7r_get_param' );

/**
 * Collect the data from the query string by parameter
 */
function wpcf7r_get_param( $atts ) {
	$atts  = shortcode_atts(
		array(
			'param' => '',
		),
		$atts,
		'wpcf7-redirect'
	);
	$param = '';

	if ( isset( $_GET[ $atts['param'] ] ) && $_GET[ $atts['param'] ] ) {
		$param = esc_attr( wp_kses( $_GET[ $atts['param'] ], array( '' ) ) );
	}

	return $param;
}
