<?php
/**
 * Sets up account-page block, does not format frontend
 *
 * @package blocks/account-page
 **/

namespace PMPro\blocks\account_page;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

// Only load if Gutenberg is available.
if ( ! function_exists( 'register_block_type' ) ) {
	return;
}

add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );
/**
 * Register the dynamic block.
 *
 * @since 2.1.0
 *
 * @return void
 */
function register_dynamic_block() {
	// Hook server side rendering into render callback.
	register_block_type( 'pmpro/account-page', [
			'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
		]
	);
}

/**
 * Server rendering for account-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	$str_atts = '';
	if ( ! empty( $attributes['membership'] ) ) {
		$str_atts .= 'membership, ';
	}
	if ( ! empty( $attributes['profile'] ) ) {
		$str_atts .= 'profile, ';
	}
	if ( ! empty( $attributes['invoices'] ) ) {
		$str_atts .= 'invoices, ';
	}
	if ( ! empty( $attributes['links'] ) ) {
		$str_atts .= 'links, ';
	}
	if ( strlen( $str_atts ) >= 2 ) {
		$str_atts = substr( $str_atts, 0, -2 );
	}
	$atts = [ 'sections' => $str_atts ];
	return pmpro_shortcode_account( $atts );
}
