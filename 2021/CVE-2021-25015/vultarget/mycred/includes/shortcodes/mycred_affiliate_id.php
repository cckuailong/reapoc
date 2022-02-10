<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Affiliate ID
 * @since 1.5.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_render_affiliate_id' ) ) :
	function mycred_render_affiliate_id( $atts, $content = '' ) {

		extract( shortcode_atts( array(
			'type' => MYCRED_DEFAULT_TYPE_KEY
		), $atts, MYCRED_SLUG . '_affiliate_id' ) );

		return apply_filters( 'mycred_affiliate_id_' . $type, '', $atts, $content );

	}
endif;
add_shortcode( MYCRED_SLUG . '_affiliate_id', 'mycred_render_affiliate_id' );
