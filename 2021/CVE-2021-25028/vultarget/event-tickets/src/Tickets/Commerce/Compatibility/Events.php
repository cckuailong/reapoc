<?php
/**
 * Handles Tickets Commerce compatibility with The Events Calendar.
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Compatibility
 */

namespace TEC\Tickets\Commerce\Compatibility;

use TEC\Tickets\Commerce\Checkout;

/**
 * Class Events.
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Compatibility
 */
class Events {

	/**
	 * In cases where Event Tickets is running alongside The Events Calendar and the home page is set to be the Events page, this
	 * redirect will trigger a hook in  The Events Calendar that was designed to prevent funky page loads out of context.
	 * We don't need those checks to run when redirecting to the Cart page in Tickets Commerce so we
	 * short-circuit the context.
	 *
	 * @since 5.2.0
	 *
	 * @param string $location the URL we're redirecting to.
	 * @param int    $status   The redirect status code.
	 *
	 * @return string
	 */
	public function prevent_filter_redirect_canonical( $location, $status ) {

		if ( 302 !== $status || false === strpos( $location, 'tec-tc-cookie=' ) ) {
			return $location;
		}

		// The complete checkout url must be the first thing in the $location string
		if ( 0 !== strpos( $location, tribe( Checkout::class )->get_url() ) ) {
			return $location;
		}

		add_filter( 'tribe_context_view_request', '__return_false' );

		return $location;
	}
}
