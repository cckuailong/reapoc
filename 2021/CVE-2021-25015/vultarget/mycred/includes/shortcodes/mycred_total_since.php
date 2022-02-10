<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Total Since
 * Shows the total number of points a user has gained / lost in a given timeframe.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_render_shortcode_total_since' ) ) :
	function mycred_render_shortcode_total_since( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'from'      => 'today',
			'until'     => 'now',
			'type'      => MYCRED_DEFAULT_TYPE_KEY,
			'ref'       => '',
			'user_id'   => 'current',
			'formatted' => 1
		), $atts, MYCRED_SLUG . '_total_since' ) );

		if ( ! mycred_point_type_exists( $type ) )
			$type = MYCRED_DEFAULT_TYPE_KEY;

		if ( $ref == '' ) $ref = NULL;

		$user_id = mycred_get_user_id( $user_id );
		$mycred  = mycred( $type );
		$total   = mycred_get_total_by_time( $from, $until, $ref, $user_id, $type );

		if ( substr( $total, 0, 7 ) != 'Invalid' && $formatted == 1 )
			$total = $mycred->format_creds( $total );

		return $total;

	}
endif;
add_shortcode( MYCRED_SLUG . '_total_since', 'mycred_render_shortcode_total_since' );
