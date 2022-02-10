<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Best User
 * Allows database queries in the history table to determen the
 * "best user" based on references, time and point types.
 * @since 1.6.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_render_shortcode_best_user' ) ) :
	function mycred_render_shortcode_best_user( $attr, $content = '' ) {

		extract( shortcode_atts( array(
			'ref'     => 'balance',
			'from'    => '',
			'until'   => '',
			'types'   => MYCRED_DEFAULT_TYPE_KEY,
			'nothing' => 'No user found',
			'order'   => 'DESC',
			'avatar'  => 50
		), $attr, MYCRED_SLUG . '_best_user' ) );

		$args = array(
			'number'       => 1,
			'type'         => $types,
			'order'        => $order,
			'based_on'     => $ref,
			'timeframe'    => $from,
			'now'          => $until,
			'exclude_zero' => 0
		);

		// Construct the leaderboard class
		$leaderboard = mycred_get_leaderboard( $args );

		// Just constructing the class will not yeld any results
		// We need to run the query to populate the leaderboard
		$leaderboard->get_leaderboard_results();

		if ( empty( $leaderboard->leaderboard ) )
			return '<p class="mycred-best-user-no-results text-center">' . $nothing . '</p>';

		$best_user   = $leaderboard->leaderboard[0];
		$amount      = $best_user['cred'];
		$user        = get_userdata( $best_user['ID'] );
		if ( ! isset( $user->display_name ) )
			return '<p class="mycred-best-user-no-results text-center">' . $nothing . '</p>';

		if ( empty( $content ) )
			$content = '<div class="mycred-best-user text-center">%avatar%<h4>%display_name%</h4></div>';

		$content = apply_filters( 'mycred_best_user_content', $content, $attr );

		$content = str_replace( '%display_name%', $user->display_name, $content );
		$content = str_replace( '%first_name%',   $user->first_name,   $content );
		$content = str_replace( '%last_name%',    $user->last_name,    $content );
		$content = str_replace( '%user_email%',   $user->user_email,   $content );
		$content = str_replace( '%user_login%',   $user->user_login,   $content );

		$content = str_replace( '%avatar%',       get_avatar( $user->ID, $avatar ), $content );
		$content = str_replace( '%total%',        $amount, $content );
		$content = str_replace( '%total_abs%',    abs( $amount ), $content );
		//$content = str_replace( '%count%',        $result->count, $content );

		return apply_filters( 'mycred_render_best_user', $content, $leaderboard, $attr );

	}
endif;
add_shortcode( MYCRED_SLUG . '_best_user', 'mycred_render_shortcode_best_user' );
