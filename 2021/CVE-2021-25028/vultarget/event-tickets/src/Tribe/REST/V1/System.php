<?php


/**
 * Class Tribe__Tickets__REST__V1__System
 *
 * Handles scanning and asserting the current WordPress installation support of
 * ET REST API.
 */
class Tribe__Tickets__REST__V1__System extends Tribe__REST__System {

	/**
	 * @var string The name of the option that enables or disables the ET REST API support.
	 */
	protected static $disable_option_name = 'et-rest-v1-disabled';

	/**
	 * Whether ET REST API is supported by the WP installation or not.
	 *
	 * @since 4.7.5
	 *
	 * @return bool
	 */
	public function supports_et_rest_api() {
		return $this->supports_wp_rest_api();
	}

	/**
	 * Whether Event Tickets REST API is enabled or not for the WP installation.
	 *
	 * @since 4.7.5
	 *
	 * @return bool
	 */
	public function et_rest_api_is_enabled() {
		$enabled = $this->supports_et_rest_api() && false == tribe_get_option( self::$disable_option_name );

		/**
		 * Filters whether ET REST API is enabled or not for the WP installation.
		 *
		 * @since 4.7.5
		 *
		 * @param bool $enabled
		 */
		return apply_filters( 'tribe_tickets_rest_api_enabled', $enabled );
	}


	/**
	 * Returns the name of the option used to indicate whether ET REST API is enabled or not for
	 * the WP installation.
	 *
	 * The option is stored in Event Tickets options database record: use `tribe_get_option()`
	 * to get it.
	 *
	 * @since 4.7.5
	 *
	 * @return string
	 */
	public static function get_disable_option_name() {
		return self::$disable_option_name;
	}
}
