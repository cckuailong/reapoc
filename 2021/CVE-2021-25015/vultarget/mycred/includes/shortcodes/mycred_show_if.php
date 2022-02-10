<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Show IF
 * Allows content to be hidden if a user does not fulfil the set points
 * requirements set for this shortcode.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_render_shortcode_show_if' ) ) :
	function mycred_render_shortcode_show_if( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'balance'  => -1,
			'rank'     => -1,
			'ref'      => '',
			'count'    => -1,
			'ctype'    => MYCRED_DEFAULT_TYPE_KEY,
			'visitors' => '',
			'comp'     => 'AND',
			'user_id'  => 'current'
		), $atts, MYCRED_SLUG . '_show_if' ) );

		// Visitors
		if ( ! is_user_logged_in() ) {

			if ( $visitors != '' ) return $visitors;

			return;

		}

		// Get the user ID
		$user_id = mycred_get_user_id( $user_id );

		// You can only use AND or OR for comparisons
		if ( ! in_array( $comp, array( 'AND', 'OR' ) ) )
			$comp = 'AND';

		// Make sure the point type we nominated exists
		if ( ! mycred_point_type_exists( $ctype ) ) return 'invalid point type';

		// Load myCRED with the requested point type
		$mycred = mycred( $ctype );

		// Make sure user is not excluded
		if ( $mycred->exclude_user( $user_id ) ) return;

		// Lets start determening if the user can see the content
		$should_show = false;

		// Balance related requirement
		if ( $balance >= 0 ) {

			$users_balance = $mycred->get_users_balance( $user_id, $ctype );
			$balance       = $mycred->number( $balance );

			// Zero balance requirement
			if ( $balance == $mycred->zero() && $users_balance == $mycred->zero() )
				$should_show = true;

			// Balance must be higher or equal to the amount set
			elseif ( $users_balance >= $balance )
				$should_show = true;

		}

		// Reference related requirement
		if ( MYCRED_ENABLE_LOGGING && strlen( $ref ) > 0 ) {

			$ref_count = mycred_count_ref_instances( $ref, $user_id, $ctype );

			// Combined with a balance requirement we must have references
			if ( $balance >= 0 && $ref_count == 0 && $comp === 'AND' )
				$should_show = false;

			// Ref count must be higher or equal to the count set
			elseif ( $ref_count >= $count )
				$should_show = true;

		}

		// Rank related requirement
		if ( $rank !== -1 && function_exists( 'mycred_get_users_rank' ) ) {

			$rank_id = mycred_get_rank_object_id( $rank );

			// Rank ID provided
			if ( is_numeric( $rank ) )
				$users_rank = mycred_get_users_rank( $user_id, $ctype );

			// Rank title provided
			else
				$users_rank = mycred_get_users_rank( $user_id, $ctype );

			if ( isset( $users_rank->post_id ) && $rank_id !== false ) {

				if ( $users_rank->post_id != $rank_id && $comp === 'AND' )
					$should_show = false;

				elseif ( $users_rank->post_id == $rank_id )
					$should_show = true;

			}

		}

		// Allow others to play
		$should_show = apply_filters( 'mycred_show_if', $should_show, $user_id, $atts, $content );

		// Sorry, no show
		if ( $should_show !== true ) return;

		$content = '<div class="mycred-show-this-content">' . $content . '</div>';

		// Return content
		return do_shortcode( apply_filters( 'mycred_show_if_render', $content, $user_id, $atts, $content ) );

	}
endif;
add_shortcode( MYCRED_SLUG . '_show_if', 'mycred_render_shortcode_show_if' );
