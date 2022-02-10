<?php
/**
 * Enqueues blocks in editor and dynamic blocks
 *
 * @package blocks
 */
defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

/**
 * Dynamic Block Requires
 */
require_once( 'checkout-button/block.php' );
require_once( 'account-page/block.php' );
require_once( 'account-membership-section/block.php' );
require_once( 'account-profile-section/block.php' );
require_once( 'account-invoices-section/block.php' );
require_once( 'account-links-section/block.php' );
require_once( 'billing-page/block.php' );
require_once( 'cancel-page/block.php' );
require_once( 'checkout-page/block.php' );
require_once( 'confirmation-page/block.php' );
require_once( 'invoice-page/block.php' );
require_once( 'levels-page/block.php' );
require_once( 'membership/block.php' );
require_once( 'member-profile-edit/block.php' );
require_once( 'login/block.php' );

/**
 * Add PMPro block category
 * This callback is used with the block_categories (pre 5.8)
 * and block_categories_all (5.8+) filters. In the first filter,
 * the second parameter is a $post, in the latter it's a $context.
 * We don't use the second parameter yet though.
 */
function pmpro_place_blocks_in_panel( $categories, $post_or_context ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'pmpro',
				'title' => __( 'Paid Memberships Pro', 'paid-memberships-pro' ),
			),
		)
	);
}

// Use the correct filter based on WP version.
if ( function_exists( 'get_default_block_categories' ) ) {
	// 5.8+, context is 2nd parameter.
	add_filter( 'block_categories_all', 'pmpro_place_blocks_in_panel', 10, 2 );
} else {
	// Pre-5.8, post is 2nd parameter.
	add_filter( 'block_categories', 'pmpro_place_blocks_in_panel', 10, 2 );
}

/**
 * Enqueue block editor only JavaScript and CSS
 */
function pmpro_block_editor_scripts() {
	// Enqueue the bundled block JS file.
	wp_enqueue_script(
		'pmpro-blocks-editor-js',
		plugins_url( 'js/blocks.build.js', PMPRO_BASE_FILE ),
		[
			'wp-i18n',
			'wp-element',
			'wp-blocks',
			'wp-components',
			'wp-api',
			'wp-block-editor',
			'pmpro_admin',
		],
		PMPRO_VERSION
	);

	// Enqueue optional editor only styles.
	wp_enqueue_style(
		'pmpro-blocks-editor-css',
		plugins_url( 'css/blocks.editor.css', PMPRO_BASE_FILE ),
		array(),
		PMPRO_VERSION
	);

	// Adding translation functionality to Gutenberg blocks/JS.
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'pmpro-blocks-editor-js', 'paid-memberships-pro' );
	}
}
add_action( 'enqueue_block_editor_assets', 'pmpro_block_editor_scripts' );
