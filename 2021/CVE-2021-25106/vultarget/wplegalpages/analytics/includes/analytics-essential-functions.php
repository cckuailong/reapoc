<?php
/**
 * Analytics Essentials functions.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'as_normalize_path' ) ) {
	/**
	 * Normalize path.
	 *
	 * @param string $path Path.
	 * @return mixed|string|string[]|null
	 */
	function as_normalize_path( $path ) {
		if ( function_exists( 'wp_normalize_path' ) ) {
			return wp_normalize_path( $path );
		} else {
			$path = str_replace( '\\', '/', $path );
			$path = preg_replace( '|/+|', '/', $path );

			return $path;
		}
	}
}
