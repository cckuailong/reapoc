<?php

namespace TEC\Tickets\Commerce;

/**
 * Class Currency
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Currency {

	/**
	 * The option key that stores the currency code in Tickets Commerce.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $currency_code_option = 'tickets-commerce-currency-code';

	/**
	 * The option key that was used to store the currency code in Tribe Commerce.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $legacy_currency_code_option = 'ticket-commerce-currency-code';

	/**
	 * The fallback currency code to use if none is found.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	public static $currency_code_fallback = 'USD';

	/**
	 * Retrieves the working currency code.
	 *
	 * @since TBD
	 *
	 * @return string
	 */

	public static function get_currency_code() {
		return tribe_get_option( static::$currency_code_option );
	}

	/**
	 * Retrieve a fallback currency code.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public static function get_currency_code_fallback() {

		// Check if we have a value set from Tribe Commerce
		$currency_code = tribe_get_option( static::$legacy_currency_code_option, static::$currency_code_fallback );

		// Duplicate the currency code in the Tickets Commerce key.
		tribe_update_option( static::$currency_code_option, $currency_code );

		return $currency_code;
	}

}