<?php
/**
 * Sets up cancel-page block, does not format frontend
 *
 * @package blocks/cancel-page
 **/

namespace PMPro\blocks\cancel_page;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

// Only load if Gutenberg is available.
if ( ! function_exists( 'register_block_type' ) ) {
	return;
}

/**
 * Register the dynamic block.
 *
 * @since 2.1.0
 *
 * @return void
 */
function register_dynamic_block() {
	// Hook server side rendering into render callback.
	register_block_type( 'pmpro/cancel-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
 * Server rendering for cancel-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'cancel', 'local', 'pages' );
}

/**
 * Load preheaders/cancel.php if a page has the checkout block.
 */
function load_cancel_preheader() {
	if ( has_block( 'pmpro/cancel-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/cancel.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_cancel_preheader', 1 );

