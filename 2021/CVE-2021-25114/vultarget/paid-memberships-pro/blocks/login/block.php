<?php
/**
 * Sets up login form.
 *
 * @package blocks/login
 **/

namespace PMPro\blocks\login_form;

defined( 'ABSPATH' ) || die( 'File cannot be accessed directly' );

// Only load if Gutenberg is available.
if ( ! function_exists( 'register_block_type' ) ) {
	return;
}

add_action( 'init', __NAMESPACE__ . '\register_dynamic_block' );
/**
 * Register the dynamic block.
 *
 * @since 2.3.0
 *
 * @return void
 */
function register_dynamic_block() {

	// Hook server side rendering into render callback.
	register_block_type(
		'pmpro/login-form',
		array(
			'attributes'      => array(
				'display_if_logged_in' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'show_menu'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'show_logout_link'     => array(
					'type'    => 'boolean',
					'default' => true,
				),
			),
			'render_callback' => __NAMESPACE__ . '\render_dynamic_block',
		)
	);
}

/**
 * Server rendering for login block.
 *
 * @param array $attributes contains text, level, and css_class strings.
 * @return string
 **/
function render_dynamic_block( $attributes ) {
	$attributes['display_if_logged_in'] = filter_var( $attributes['display_if_logged_in'], FILTER_VALIDATE_BOOLEAN );
	$attributes['show_menu']            = filter_var( $attributes['show_menu'], FILTER_VALIDATE_BOOLEAN );
	$attributes['show_logout_link']     = filter_var( $attributes['show_logout_link'], FILTER_VALIDATE_BOOLEAN );

	return( pmpro_login_forms_handler( $attributes['show_menu'], $attributes['show_logout_link'], $attributes['display_if_logged_in'], '', false ) );
}
