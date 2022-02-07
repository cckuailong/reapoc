<?php

namespace Never5\DownloadMonitor\Shop\Session;

class Factory {

	/**
	 * Generate key
	 *
	 * @return string
	 */
	private function generate_key() {
		return md5( uniqid( 'dlm_shop_session_key', true ) . $_SERVER['REMOTE_ADDR'] );
	}

	/**
	 * Generate hash
	 *
	 * @param $key
	 *
	 * @return string
	 */
	private function generate_hash( $key ) {
		$nonce = ( defined( 'NONCE_SALT' ) ? NONCE_SALT : 'nononce' );

		return md5( uniqid( 'dlm_shop_session_hash', true ) . mt_rand( 0, 99 ) . $_SERVER['REMOTE_ADDR'] . $nonce . $key );
	}

	/**
	 * Make new session with unique key and hash
	 *
	 * @return Session
	 */
	public function make() {

		$session = new Session();
		$session->set_key( $this->generate_key() );
		$session->set_hash( $this->generate_hash( $session->get_key() ) );

		$session->set_items( array() );
		$session->set_coupons( array() );

		$session->reset_expiry();

		return $session;

	}

}