<?php
/**
 * Sets up checkout-page block, does not format frontend
 *
 * @package blocks/checkout-page
 **/

namespace PMPro\blocks\checkout_page;

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
	// Need to explicitly register the default level meta
	register_meta( 'post', 'pmpro_default_level', array(
	   'show_in_rest' => true,
	   'single' => true,
	   'type' => 'integer',
   	) );
	
	// Hook server side rendering into render callback.
	register_block_type( 'pmpro/checkout-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
 * Server rendering for checkout-page block.
 *
 * @param array $attributes contains level.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'checkout', 'local', 'pages' );
}

/**
 * Load preheaders/checkout.php if a page has the checkout block.
 */
function load_checkout_preheader() {
	if ( has_block( 'pmpro/checkout-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/checkout.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_checkout_preheader', 1 );
