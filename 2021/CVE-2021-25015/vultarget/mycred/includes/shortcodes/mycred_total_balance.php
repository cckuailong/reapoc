<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_total_balance
 * This shortcode will return either the current user or a given users
 * total balance based on either all point types or a comma seperated list
 * of types.
 * @see http://codex.mycred.me/shortcodes/mycred_total_balance/
 * @since 1.4.3
 * @version 1.3
 */
if ( ! function_exists( 'mycred_render_shortcode_total' ) ) :
	function mycred_render_shortcode_total( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'user_id' => 'current',
			'types'   => MYCRED_DEFAULT_TYPE_KEY,
			'raw'     => 0,
			'total'   => 0
		), $atts, MYCRED_SLUG . '_total_balance' ) );

		// If user ID is not set, get the current users ID
		if ( ! is_user_logged_in() && $user_id == 'current' )
			return $content;

		$user_id     = mycred_get_user_id( $user_id );

		// Get the users myCRED account object
		$account     = mycred_get_account( $user_id );
		if ( $account === false ) return;

		// Check for exclusion
		if ( empty( $account->balance ) ) return;

		// Assume we want all balances added up
		$point_types = $account->point_types;

		// If we set types="" to either one or a comma separared list of type keys
		if ( ! empty( $types ) && $types != 'all' ) {

			$types_to_addup = array();
			foreach ( explode( ',', $types ) as $type_key ) {

				$type_key = sanitize_text_field( $type_key );
				if ( ! array_key_exists( $type_key, $account->balance ) ) continue;

				if ( ! in_array( $type_key, $types_to_addup ) )
					$types_to_addup[] = $type_key;

			}
			$point_types   = $types_to_addup;

		}

		// Lets add up
		$balance_sum = 0;
		if ( ! empty( $point_types ) ) {
			foreach ( $point_types as $type_key ) {

				$balance = $account->balance[ $type_key ];
				if ( $balance === false ) continue;

				if ( $total == 1 )
					$balance_sum += $balance->accumulated;
				else
					$balance_sum += $balance->current;

			}
		}

		// If we only added up one, we can format (if set)
		if ( count( $point_types ) == 1 ) {

			$point_type = $account->balance[ $types_to_addup[0] ]->point_type;

			// Format requested
			if ( ! $raw && ! empty( $point_type ) )
				$balance_sum = $point_type->format( $balance_sum );

		}

		return apply_filters( 'mycred_total_balances_output', $balance_sum, $atts );

	}
endif;
add_shortcode( MYCRED_SLUG . '_total_balance', 'mycred_render_shortcode_total' );
