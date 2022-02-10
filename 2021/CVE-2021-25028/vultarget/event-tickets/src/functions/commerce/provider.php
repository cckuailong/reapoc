<?php

use TEC\Tickets\Settings;

/**
 * Checks whether out new Tickets system should load.
 *
 * In order the function will check the `TEC_TICKETS_COMMERCE` constant,
 * the `TEC_TICKETS_COMMERCE` environment variable and, finally, the `Manager::$option_enabled` option.
 *
 * @since 5.1.6
 *
 * @return bool Whether Tickets Commerce is enabled or not.
 */
function tec_tickets_commerce_is_enabled() {
	if ( defined( 'TEC_TICKETS_COMMERCE' ) ) {
		return (bool) TEC_TICKETS_COMMERCE;
	}

	$env_var = getenv( 'TEC_TICKETS_COMMERCE' );
	if ( false !== $env_var ) {
		return (bool) $env_var;
	}

	$enabled = (bool) tribe_get_option( Settings::$tickets_commerce_enabled, false );

	/**
	 * Allows filtering of the Tickets Commerce provider, doing so will render
	 * the methods and classes no longer load-able so keep that in mind.
	 *
	 * @since 5.1.6
	 *
	 * @param boolean $enabled Determining if Tickets Commerce is enabled..
	 */
	return apply_filters( 'tec_tickets_commerce_is_enabled', $enabled );
}


/**
 * Determine whether Tickets Commerce is in sandbox mode.
 *
 * @since 5.1.6
 * @since 5.2.0 Modified the name of the method to `tec_tickets_commerce_is_sandbox_mode`
 *
 * @return bool Whether Tickets Commerce is in test mode.
 */
function tec_tickets_commerce_is_sandbox_mode() {
	$sandbox_mode = tribe_is_truthy( tribe_get_option( \TEC\Tickets\Commerce\Settings::$option_sandbox ) );

	/**
	 * Filter whether we should disable TribeCommerce PayPal or not.
	 *
	 * @since 5.2.0
	 *
	 * @param boolean $sandbox_mode should be available or not.
	 */
	return apply_filters( 'tec_tickets_commerce_is_sandbox_mode', $sandbox_mode );
}

/**
 * Determine whether the legacy TribeCommerce should be shown or not.
 *
 * @since 5.1.10
 *
 * @return boolean
 */
function tec_tribe_commerce_is_available() {

	if ( defined( 'TEC_TRIBE_COMMERCE_AVAILABLE' ) ) {
		return (bool) TEC_TRIBE_COMMERCE_AVAILABLE;
	}

	$env_var = getenv( 'TEC_TRIBE_COMMERCE_AVAILABLE' );

	if ( false !== $env_var ) {
		return (bool) $env_var;
	}

	// Available if PayPal was completely setup previously.
	$available = tribe()->offsetExists( 'tickets.commerce.paypal.handler.ipn' ) ? tribe( 'tickets.commerce.paypal.handler.ipn' )->get_config_status( 'slug' ) === 'complete' : null;

	if ( is_null( $available ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			'tickets.commerce.paypal.handler.ipn - is not a registered callback.',
			'5.1.10'
		);
	}
	$should_be_available = $available && tec_tribe_commerce_has_active_tickets();

	/**
	 * Filter whether we should disable TribeCommerce PayPal or not.
	 *
	 * @since 5.1.10
	 *
	 * @param boolean $available should be available or not.
	 */
	return apply_filters( 'tec_tribe_commerce_is_available', $should_be_available );
}

/**
 * Check if the site has created tickets using TribeCommerce.
 *
 * @since 5.2.0
 *
 * @return bool
 */
function tec_tribe_commerce_has_active_tickets() {
	if ( defined( 'TEC_TRIBE_COMMERCE_HAS_ACTIVE_TICKETS' ) ) {
		return (bool) TEC_TRIBE_COMMERCE_HAS_ACTIVE_TICKETS;
	}

	$env_var = getenv( 'TEC_TRIBE_COMMERCE_HAS_ACTIVE_TICKETS' );

	if ( false !== $env_var ) {
		return (bool) $env_var;
	}

	$cache_key = 'tec_tribe_commerce_has_active_tickets';
	$cached    = (bool) get_transient( $cache_key );

	if ( $cached ) {
		/**
		 * Filter whether we should disable TribeCommerce PayPal or not.
		 *
		 * @since 5.2.0
		 *
		 * @param boolean $available should be available or not.
		 */
		return apply_filters( 'tec_tribe_commerce_has_active_tickets', $cached );
	}

	$has_active_tickets = (bool) tribe_tickets()->by( 'post_type', 'tribe_tpp_tickets' )->where( 'is_active' )->count();

	// Cache the data.
	set_transient( $cache_key, $has_active_tickets, 24 * HOUR_IN_SECONDS );

	/**
	 * Filter whether we should disable TribeCommerce PayPal or not.
	 *
	 * @since 5.2.0
	 *
	 * @param boolean $available should be available or not.
	 */
	return apply_filters( 'tec_tribe_commerce_has_active_tickets', $has_active_tickets );

}