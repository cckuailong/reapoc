<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Affiliate Link
 * @since 1.5.3
 * @version 1.2
 */
if ( ! function_exists( 'mycred_render_affiliate_link' ) ) :
	function mycred_render_affiliate_link( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'type' => MYCRED_DEFAULT_TYPE_KEY
		), $atts, MYCRED_SLUG . '_affiliate_link' ) );

		return apply_filters( 'mycred_affiliate_link_' . $type, '', $atts, $content );

	}
endif;
add_shortcode( MYCRED_SLUG . '_affiliate_link', 'mycred_render_affiliate_link' );
