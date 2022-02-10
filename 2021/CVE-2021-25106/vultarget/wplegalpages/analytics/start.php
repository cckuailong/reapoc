<?php
/**
 * Invoke the SDK.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Analytics SDK Version.
 *
 * @var string
 */
$this_sdk_version = '2.4.1';

require_once dirname( __FILE__ ) . '/includes/analytics-essential-functions.php';

if ( ! class_exists( 'Analytics' ) ) {
	if ( ! defined( 'WP_STAT__SDK_VERSION' ) ) {
		define( 'WP_STAT__SDK_VERSION', $this_sdk_version );
	}

	// Load SDK files.
	require_once dirname( __FILE__ ) . '/require.php';

	/**
	 * Dynamic Init.
	 *
	 * @param array <string,string> $module Plugin or Theme details.
	 *
	 * @return Analytics
	 */
	function ras_dynamic_init( $module ) {
		if ( ! isset( $module['plugin_basename'] ) ) {
			$plugin_basename = '';
		} else {
			$plugin_basename = $module['plugin_basename'];
		}
		if ( ! isset( $module['plugin_url'] ) ) {
			$plugin_url = '';
		} else {
			$plugin_url = $module['plugin_url'];
		}
		$ra = Analytics::instance( $module['id'], $module['product_name'], $module['version'], $module['module_type'], $module['slug'], $plugin_basename, $plugin_url );
		$ra->dynamic_init( $module );

		return $ra;
	}

	/**
	 * Quick shortcut to get Analytics for specified plugin.
	 * Used by various templates.
	 *
	 * @param number $module_id Module Id.
	 * @param string $slug Slug.
	 * @param string $product_name Product Name.
	 * @param string $version Product Version.
	 * @param string $module_type Module type.
	 */
	function analytics( $module_id, $product_name, $version, $module_type, $slug ) {
		return Analytics::instance( $module_id, $product_name, $version, $module_type, $slug );
	}
}
