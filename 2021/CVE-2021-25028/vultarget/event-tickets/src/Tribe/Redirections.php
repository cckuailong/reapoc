<?php

/**
 * Class Tribe__Tickets__Redirections
 *
 * @since 4.7
 */
class Tribe__Tickets__Redirections {
	/**
	 * Conditionally redirects the user if a URL is specified in the GET request.
	 *
	 * @since 4.7.3
	 */
	public function maybe_redirect() {
		if ( empty( $_GET['tribe_tickets_redirect_to'] ) ) {
			return;
		}

		$url = rawurldecode( $_GET['tribe_tickets_redirect_to'] );
		wp_redirect( $url );
		die();
	}
}
