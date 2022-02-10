<?php

namespace TEC\Tickets\Commerce\Gateways\Manual;

use TEC\Tickets\Commerce\Gateways\Abstract_Gateway;

/**
 * Class Manual Gateway.
 *
 * @since   5.2.0
 * @package TEC\Tickets\Commerce\Gateways\Manual
 */
class Gateway extends Abstract_Gateway {
	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	protected static $key = 'manual';

	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	public static function get_label() {
		return __( 'Manually Generated', 'event-tickets' );
	}

	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	public static function is_connected() {
		return true;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 5.2.0
	 */
	public static function should_show() {
		return false;
	}
}