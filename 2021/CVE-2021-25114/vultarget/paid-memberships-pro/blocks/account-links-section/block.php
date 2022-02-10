<?php
/**
 * Sets up account-links-section block, does not format frontend
 *
 * @package blocks/account-links-section
 **/

namespace PMPro\blocks\account_links_section;

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
	register_block_type( 'pmpro/account-links-section', [
		'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
	] );
}

/**
 * Server rendering for account-links-section block.
 *
 * @return string
 **/
function render_dynamic_block() {
	return pmpro_shortcode_account( array( 'sections' => 'links' ) );
}
