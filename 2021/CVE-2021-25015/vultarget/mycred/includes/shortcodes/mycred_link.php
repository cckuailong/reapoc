<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_link
 * This shortcode allows you to award or deduct points from the current user
 * when their click on a link. The shortcode will generate an anchor element
 * and call the mycred-click-link jQuery script which will award the points.
 *
 * Note! Only HTML5 anchor attributes are supported and this shortcode is only
 * available if the hook is enabled!
 *
 * @see http://codex.mycred.me/shortcodes/mycred_link/
 * @since 1.1
 * @version 1.4
 */
if ( ! function_exists( 'mycred_render_shortcode_link' ) ) :
	function mycred_render_shortcode_link( $atts, $link_title = '' ) {

		global $mycred_link_points;

		$atts = shortcode_atts( array(
			'id'       => '',
			'rel'      => '',
			'class'    => '',
			'href'     => '',
			'title'    => '',
			'target'   => '',
			'style'    => '',
			'amount'   => 0,
			'ctype'    => MYCRED_DEFAULT_TYPE_KEY,
			'hreflang' => '',
			'media'    => '',
			'type'     => '',
			'onclick'  => ''
		), $atts, MYCRED_SLUG . '_link' );

		// Make sure point type exists
		if ( ! mycred_point_type_exists( $atts['ctype'] ) )
			$atts['ctype'] = MYCRED_DEFAULT_TYPE_KEY;

		// HREF is required
		if ( empty( $atts['href'] ) )
			$atts['href'] = '#';

		// All links must contain the 'mycred-points-link' class
		if ( empty( $atts['class'] ) )
			$atts['class'] = 'mycred-points-link';
		else
			$atts['class'] = 'mycred-points-link ' . $atts['class'];

		// If no id exists, make one
		if ( empty( $atts['id'] ) ) {
			$id         = str_replace( array( 'http://', 'https://', 'http%3A%2F%2F', 'https%3A%2F%2F' ), 'hs', $atts['href'] );
			$id         = str_replace( array( '/', '-', '_', ':', '.', '?', '=', '+', '\\', '%2F' ), '', $id );
			$atts['id'] = $id;
		}

		// Construct anchor attributes
		$attr = array();
		foreach ( $atts as $attribute => $value ) {
			if ( ! empty( $value ) && ! in_array( $attribute, array( 'amount', 'ctype' ) ) ) {
				$attr[] = $attribute . '="' . $value . '"';
			}
		}

		// Add point type as a data attribute
		$attr[] = 'data-type="' . esc_attr( $atts['ctype'] ) . '"';

		// Only usable for members
		if ( is_user_logged_in() ) {

			// If amount is zero, use the amount we set in the hooks settings
			if ( $atts['amount'] == 0 ) {

				// Get hook settings
				$prf_hook = apply_filters( 'mycred_option_id', 'mycred_pref_hooks' );
				$hooks = mycred_get_option( $prf_hook, false );
				if ( $atts['ctype'] != MYCRED_DEFAULT_TYPE_KEY )
					$hooks = mycred_get_option( 'mycred_pref_hooks_' . sanitize_key( $atts['ctype'] ), false );

				// Apply points value
				if ( $hooks !== false && is_array( $hooks ) && array_key_exists( 'link_click', $hooks['hook_prefs'] ) ) {
					$atts['amount'] = $hooks['hook_prefs']['link_click']['creds'];
				}

			}

			// Add key
			$token  = mycred_create_token( array( $atts['amount'], $atts['ctype'], $atts['id'], urlencode( $atts['href'] ) ) );
			$attr[] = 'data-token="' . $token . '"';

			// Make sure jQuery script is called
			$mycred_link_points = true;

		}

		// Return result
		return apply_filters( 'mycred_link', '<a ' . implode( ' ', $attr ) . '>' . do_shortcode( $link_title ) . '</a>', $atts, $link_title );

	}
endif;
add_shortcode( MYCRED_SLUG . '_link', 'mycred_render_shortcode_link' );
