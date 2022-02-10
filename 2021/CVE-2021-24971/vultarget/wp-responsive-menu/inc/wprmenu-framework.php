<?php
/**
 * WPRMenu FrameWork
 *
 * @version  3.1.4
 */

defined( 'ABSPATH' ) || exit;

// Don't load if wprmenu_framework_init is already defined
if ( is_admin() && ! function_exists( 'wprmenu_framework_init' ) ) :

function wprmenu_framework_init() {

	//  If user can't edit theme options, exit
	if ( ! current_user_can( 'edit_theme_options' ) )
		return;

	// Loads the required WPRMenu Framework classes.
	require plugin_dir_path( __FILE__ ) . 'includes/class-wprmenu-framework.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class-wprmenu-framework-admin.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class-wprmenu-interface.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class-wprmenu-media-uploader.php';
	require plugin_dir_path( __FILE__ ) . 'includes/class-wprmenu-sanitization.php';

	// Instantiate the main plugin class.
	$wprmenu_framework = new WPRMenu_Framework;
	$wprmenu_framework->init();

	// Instantiate the WPRMenu options page.
	$wprmenu_framework_admin = new WPRMenu_Framework_Admin;
	$wprmenu_framework_admin->init();

	// Instantiate the media uploader class
	$wprmenu_framework_media_uploader = new WPRMenu_Framework_Media_Uploader;
	$wprmenu_framework_media_uploader->init();

}

add_action( 'init', 'wprmenu_framework_init', 20 );

endif;


/**
 * Helper function to return the theme option value.
 * If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * Not in a class to support backwards compatibility in themes.
 */

if ( ! function_exists( 'wpr_of_get_option' ) ) :

function wpr_of_get_option( $name, $default = false ) {
	$config = get_option( 'wpr_optionsframework' );

	if ( ! isset( $config['id'] ) ) {
		return $default;
	}

	$options = get_option( $config['id'] );

	if ( isset( $options[$name] ) ) {
		return $options[$name];
	}

	return $default;
}

endif;