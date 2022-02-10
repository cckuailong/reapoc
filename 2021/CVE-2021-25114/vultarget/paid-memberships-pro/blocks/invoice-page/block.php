<?php
/**
 * Sets up invoice-page block, does not format frontend
 *
 * @package blocks/invoice-page
 **/

namespace PMPro\blocks\invoice_page;

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
	register_block_type( 'pmpro/invoice-page', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}
add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );

/**
 * Server rendering for invoice-page block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	return pmpro_loadTemplate( 'invoice', 'local', 'pages' );
}

/**
 * Load preheaders/invoice.php if a page has the checkout block.
 */
function load_invoice_preheader() {
	if ( has_block( 'pmpro/invoice-page' ) ) {
		require_once( PMPRO_DIR . "/preheaders/invoice.php" );
	}
}
add_action( 'wp', __NAMESPACE__ . '\load_invoice_preheader', 1 );
