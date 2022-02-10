<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Total Points
 * Allows to show total points of a specific point type or add up
 * points from the log based on reference, reference id or user id.
 * @since 1.6.6
 * @version 1.1.2
 */
if ( ! function_exists( 'mycred_render_shortcode_total_points' ) ) :
	function mycred_render_shortcode_total_points( $atts ) {

		extract( shortcode_atts( array(
			'type'      => MYCRED_DEFAULT_TYPE_KEY,
			'ref'       => '',
			'ref_id'    => '',
			'user_id'   => 'current',
			'formatted' => 1
		), $atts, MYCRED_SLUG . '_total_points' ) );

		if ( ! mycred_point_type_exists( $type ) )
			$type = MYCRED_DEFAULT_TYPE_KEY;

		$user_id = mycred_get_user_id( $user_id );
		$mycred  = mycred( $type );

		global $wpdb, $mycred_log_table;

		// Simple
		if ( $ref == '' && $ref_id == '' && $user_id == '' ) {

			// Add up all balances
			$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM( meta_value ) FROM {$wpdb->usermeta} WHERE meta_key = %s", mycred_get_meta_key( $type ) ) );

		}

		// Complex
		else {

			$wheres   = array();
			$wheres[] = $wpdb->prepare( "ctype = %s", mycred_get_meta_key( $type ) );

			if ( strlen( $ref ) > 0 ) {

				// Either we have just one reference
				$multiple = explode( ',', $ref );
				if ( count( $multiple ) == 1 )
					$wheres[] = $wpdb->prepare( "ref = %s", $ref );

				// Or a comma seperated list of references
				else {

					$_clean = array();
					foreach ( $multiple as $ref ) {
						$ref = sanitize_key( $ref );
						if ( strlen( $ref ) > 0 )
							$_clean[] = $ref;
					}

					if ( ! empty( $_clean ) )
						$wheres[] = "ref IN ( '" . implode( "', '", $_clean ) . "' )";

				}

			}

			$ref_id  = absint( $ref_id );
			if ( $ref_id > 0 )
				$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

			$user_id = absint( $user_id );
			if ( $user_id != '' && $user_id != 0 )
				$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );

			$wheres  = implode( " AND ", $wheres );
			$total   = $wpdb->get_var( "SELECT SUM( creds ) FROM {$mycred_log_table} WHERE {$wheres};" );

		}

		if ( $total === NULL )
			$total = 0;

		if ( $formatted == 1 )
			return $mycred->format_creds( $total );

		return $total;

	}
endif;
add_shortcode( MYCRED_SLUG . '_total_points', 'mycred_render_shortcode_total_points' );
