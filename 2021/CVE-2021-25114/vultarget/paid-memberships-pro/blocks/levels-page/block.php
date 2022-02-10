<?php
/**
 * Sets up levels-page block, does not format frontend
 *
 * @package blocks/levels-page
 **/

namespace PMPro\blocks\levels_page;

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
	register_block_type( 'pmpro/levels-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
* Server rendering for levels-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'levels', 'local', 'pages' );
}

/**
 * Load preheaders/levels.php if a page has the checkout block.
 */
function load_levels_preheader() {
	if ( has_block( 'pmpro/levels-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/levels.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_levels_preheader', 1 );
