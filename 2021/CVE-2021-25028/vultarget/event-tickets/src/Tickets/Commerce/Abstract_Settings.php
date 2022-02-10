<?php
/**
 *
 * @since 5.1.6
 *
 * @package TEC\Tickets\Commerce
 */

namespace TEC\Tickets\Commerce;

/**
 * The gateway settings related functionality.
 *
 * @since   5.1.6
 * @package Tribe\Tickets\Commerce\Tickets_Commerce\Gateways
 *
 */
abstract class Abstract_Settings {

	/**
	 * Get the list of settings for the gateway.
	 *
	 * @since 5.1.6
	 *
	 * @return array The list of settings for the gateway.
	 */
	abstract public function get_settings();

}
