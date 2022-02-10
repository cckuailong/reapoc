<?php
/**
 * Sets up membership block, does not format frontend
 *
 * @package blocks/membership
 **/

namespace PMPro\blocks\membership;

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
	register_block_type( 'pmpro/membership', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}

/**
 * Server rendering for membership block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes, $content ) {
	if ( ! array_key_exists( 'levels', $attributes ) || ! is_array( $attributes['levels'] ) ) {
		if ( pmpro_hasMembershipLevel() ) {
			return do_blocks( $content );
		}
	} else {
		if ( pmpro_hasMembershipLevel( $attributes['levels'] ) ) {
			return do_blocks( $content );
		}
	}
}
