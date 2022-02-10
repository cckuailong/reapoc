<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_leaderboard_position
 * @see http://codex.mycred.me/shortcodes/mycred_leaderboard_position/
 * Replaces the mycred_my_ranking shortcode.
 * @since 1.7
 * @version 1.2
 */
if ( ! function_exists( 'mycred_render_shortcode_leaderbaord_position' ) ) :
	function mycred_render_shortcode_leaderbaord_position( $atts, $content = '' ) {

		$args = shortcode_atts( array(
			'user_id'   => 'current',
			'ctype'     => MYCRED_DEFAULT_TYPE_KEY,
			'type'      => '',
			'based_on'  => 'balance',
			'total'     => 0,
			'missing'   => '-',
			'suffix'    => 0,
			'timeframe' => ''
		), $atts, MYCRED_SLUG . '_leaderboard_position' );

		// Get the user ID we need a position for
		$user_id     = mycred_get_user_id( $args['user_id'] );

		// Backwards comp.
		if ( $args['type'] == '' )
			$args['type'] = $args['ctype'];

		// Construct the leaderboard class
		$leaderboard = mycred_get_leaderboard( $args );

		// Query the users position
		$position    = $leaderboard->get_users_current_position( $user_id, $args['missing'] );

		if ( $position != $args['missing'] && $args['suffix'] == 1 )
			$position = mycred_ordinal_suffix( $position, true );

		return $position;

	}
endif;
add_shortcode( MYCRED_SLUG . '_leaderboard_position', 'mycred_render_shortcode_leaderbaord_position' );
