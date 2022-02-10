<?php
/**
 * Load template for Twitter player cards
 *
 * @category Core
 * @package  WP Tweets Pro
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-tweets-pro/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MC_TEMPLATES', trailingslashit( dirname( __FILE__ ) ) . 'templates/' );
add_action( 'template_redirect', 'mc_embed_template' );
/**
 * Load Template.
 *
 * This template must be named "my-calendar-template.php".
 *
 * First, this function will look in the child theme
 * then in the parent theme and if no template is found
 * in either theme, the default template will be loaded
 * from the plugin's folder.
 *
 * This function is hooked into the "template_redirect"
 * action and terminates script execution.
 *
 * @return void
 * @since 2020-12-14
 */
function mc_embed_template() {
	// Return early if there is no reason to proceed.
	if ( ! isset( $_GET['embed'] ) ) {
		return;
	}

	// Check to see if there is a template in the theme.
	$template = locate_template( array( 'my-calendar-template.php' ) );
	if ( ! empty( $template ) ) {
		require_once( $template );
		exit;
	} else {
		// Use plugin's template file.
		require_once( MC_TEMPLATES . 'my-calendar-template.php' );
		exit;
	}

	// You've gone too far. Error case.
	header( 'HTTP/1.0 404 Not Found' );
	exit;
}
