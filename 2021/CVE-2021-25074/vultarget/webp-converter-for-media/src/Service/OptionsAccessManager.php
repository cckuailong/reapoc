<?php

namespace WebpConverter\Service;

/**
 * Provides access to options stored in database.
 */
class OptionsAccessManager {

	/**
	 * @param string $option_name   .
	 * @param mixed  $default_value .
	 *
	 * @return mixed|null
	 */
	public static function get_option( string $option_name, $default_value = null ) {
		if ( is_multisite() ) {
			return get_site_option( $option_name, $default_value );
		} else {
			return get_option( $option_name, $default_value );
		}
	}

	/**
	 * @param string $option_name  .
	 * @param mixed  $option_value .
	 *
	 * @return void
	 */
	public static function update_option( string $option_name, $option_value ) {
		if ( is_multisite() ) {
			update_site_option( $option_name, $option_value );
		} else {
			update_option( $option_name, $option_value );
		}
	}

	/**
	 * @param string $option_name .
	 *
	 * @return void
	 */
	public static function delete_option( string $option_name ) {
		if ( is_multisite() ) {
			delete_site_option( $option_name );
		} else {
			delete_option( $option_name );
		}
	}
}
