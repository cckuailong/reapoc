<?php

namespace TEC\Tickets\Commerce\Gateways;

/**
 * Class Interface_Gateway
 *
 * @since   5.1.6
 *
 * @package TEC\Tickets\Commerce\Gateways
 */
interface Interface_Gateway {
	/**
	 * Get's the key for this Commerce Gateway.
	 *
	 * @since 5.1.6
	 *
	 * @return string What is the Key used.
	 */
	public static function get_key();

	/**
	 * Get's the provider key for this Commerce Gateway.
	 *
	 * @since 5.1.9
	 *
	 * @return string What is the ORM Provider Key used.
	 */
	public static function get_provider_key();

	/**
	 * Get's the label for this Commerce Gateway.
	 *
	 * @since 5.1.6
	 *
	 * @return string What label we are using for this gateway.
	 */
	public static function get_label();

	/**
	 * Determine whether the gateway is active.
	 *
	 * @since 5.1.6
	 *
	 * @return bool Whether the gateway is active.
	 */
	public static function is_active();

	/**
	 * Determine whether the gateway is connected.
	 *
	 * @since 5.2.0
	 *
	 * @return bool Whether the gateway is connected.
	 */
	public static function is_connected();

	/**
	 * Determine whether the gateway should be shown as an available gateway.
	 *
	 * @since 5.1.6
	 *
	 * @return bool Whether the gateway should be shown as an available gateway.
	 */
	public static function should_show();

	/**
	 * Register the gateway for Tickets Commerce.
	 *
	 * @since 5.1.6
	 *
	 * @param array       $gateways The list of registered Tickets Commerce gateways.
	 *
	 * @return Abstract_Gateway[] The list of registered Tickets Commerce gateways.
	 */
	public function register_gateway( array $gateways );
}