<?php
/**
 * Sets up Membership Profile Edit block.
 *
 * @package blocks/membership
 **/

namespace PMPro\blocks\membership_profile_edit;

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
	register_block_type(
		'pmpro/member-profile-edit',
		array(
			'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
		)
	);
}

/**
 * Server rendering for member profile edit block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	if ( function_exists( 'apply_shortcodes' ) ) {
		return apply_shortcodes( '[pmpro_member_profile_edit]' );
	} else {
		return do_shortcode( '[pmpro_member_profile_edit]' );
	}
}
