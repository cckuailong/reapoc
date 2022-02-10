<?php
/**
 * Analytics Configurations.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_STAT__DOMAIN_PRODUCTION', 'feedback.cyberchimps.com' );
define( 'WP_STAT__ADDRESS_PRODUCTION', 'https://' . WP_STAT__DOMAIN_PRODUCTION );

if ( ! defined( 'WP_STAT__ADDRESS' ) ) {
	define( 'WP_STAT__ADDRESS', ( WP_STAT__ADDRESS_PRODUCTION ) );
}

/**
 * Module types
 *
 * @since 1.0.0
 */
define( 'WP_STAT__MODULE_TYPE_PLUGIN', 'plugin' );
define( 'WP_STAT__MODULE_TYPE_THEME', 'theme' );

if ( ! defined( 'WP_CYB__DEFAULT_PRIORITY' ) ) {
	define( 'WP_CYB__DEFAULT_PRIORITY', 10 );
}

// Directories.
if ( ! defined( 'WP_STAT__DIR' ) ) {
	define( 'WP_STAT__DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'WP_STAT__DIR_INCLUDES' ) ) {
	define( 'WP_STAT__DIR_INCLUDES', WP_STAT__DIR . '/includes' );
}
if ( ! defined( 'WP_STAT__DIR_TEMPLATES' ) ) {
	define( 'WP_STAT__DIR_TEMPLATES', WP_STAT__DIR . '/templates' );
}
if ( ! defined( 'WP_STAT__DIR_TEMPLATES' ) ) {
	define( 'WP_STAT__DIR_TEMPLATES', WP_STAT__DIR . '/templates' );
}
if ( ! defined( 'WP_STAT__DIR_ASSETS' ) ) {
	define( 'WP_STAT__DIR_ASSETS', WP_STAT__DIR . '/assets' );
}
if ( ! defined( 'WP_STAT__DIR_CSS' ) ) {
	define( 'WP_STAT__DIR_CSS', WP_STAT__DIR_ASSETS . '/css' );
}
if ( ! defined( 'WP_STAT__DIR_JS' ) ) {
	define( 'WP_STAT__DIR_JS', WP_STAT__DIR_ASSETS . '/js' );
}
if ( ! defined( 'WP_STAT__DIR_IMG' ) ) {
	define( 'WP_STAT__DIR_IMG', WP_STAT__DIR_ASSETS . '/img' );
}
if ( ! defined( 'WP_STAT__DIR_SDK' ) ) {
	define( 'WP_STAT__DIR_SDK', WP_STAT__DIR_INCLUDES . '/sdk' );
}
