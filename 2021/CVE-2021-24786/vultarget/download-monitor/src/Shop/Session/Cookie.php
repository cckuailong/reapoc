<?php

namespace Never5\DownloadMonitor\Shop\Session;

use Never5\DownloadMonitor\Shop\Services\Services;

/**
 * Class Cookie
 * @package Never5\DownloadMonitor\Shop\Session
 *
 * This class handles all cookie related things for the session cookie
 */
class Cookie {

	const COOKIE_NAME = 'dlm_session';

	/**
	 * Get session from cookie
	 *
	 * @return Session
	 */
	public function get_session_from_cookie() {
		$session = null;

		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$cookie_data = json_decode( base64_decode( $_COOKIE[ self::COOKIE_NAME ] ), true );

			if ( is_array( $cookie_data ) && ! empty( $cookie_data['key'] ) && ! empty( $cookie_data['hash'] ) ) {

				try {
					$session = Services::get()->service( 'session_repository' )->retrieve( $cookie_data['key'], $cookie_data['hash'] );
				} catch ( \Exception $exception ) {
				}
			}
		}

		return $session;
	}

	/**
	 * Set the session cookie
	 *
	 * @param Session $session
	 */
	public function set_session_cookie( $session ) {
		setcookie( self::COOKIE_NAME, base64_encode( json_encode( array(
			'key'  => $session->get_key(),
			'hash' => $session->get_hash()
		) ) ), $session->get_expiry()->getTimestamp(), COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, false, true );
	}

	/**
	 * Destroy the session cookie
	 */
	public function destroy_session_cookie() {
		setcookie( self::COOKIE_NAME, "", 1, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, false, true );
	}

	/**
	 * Returns true if it's safe to set cookie, false if it's not
	 *
	 * @return bool
	 */
	public function is_cookie_allowed() {
		if ( headers_sent( $file, $line ) ) {
			return false;
		}

		return true;
	}
}