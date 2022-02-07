<?php

namespace Never5\DownloadMonitor\Shop\Session;

use Never5\DownloadMonitor\Shop\Services\Services;

class Manager {

	/** @var Session */
	private $current_session = null;

	/**
	 * Check if there's a session reference cookie available.
	 * If there is, try to fetch that session from DB.
	 * If there is no cookie, or fetching failed, return new session.
	 *
	 * @return Session
	 */
	public function get_session() {
		if ( null === $this->current_session ) {
			$this->current_session = Services::get()->service( 'session_cookie' )->get_session_from_cookie();
		}

		if ( null === $this->current_session ) {
			$this->current_session = Services::get()->service( 'session_factory' )->make();
		}

		return $this->current_session;
	}

	/**
	 * @param $session
	 */
	public function set_session( $session ) {
		$this->current_session = $session;
	}

	/**
	 * Persist the session in the database and store a session reference in the cookie
	 *
	 * @param Session $session
	 */
	public function persist_session( $session ) {

		// also set session cache on persist
		$this->set_session( $session );

		// can't set cookies when headers are already sent
		if ( ! Services::get()->service( 'session_cookie' )->is_cookie_allowed() ) {
			\DLM_Debug_Logger::log( "Couldn't set DLM Session cookie, headers already set." );

			return;
		}

		// store session in database
		Services::get()->service( 'session_repository' )->persist( $session );

		// set the actual cookie
		Services::get()->service( 'session_cookie' )->set_session_cookie( $session );
	}

	/**
	 * Destroys given session and reference cookie
	 *
	 * @param Session $session
	 */
	public function destroy_session( $session ) {

		// can't set cookies when headers are already sent
		if ( ! Services::get()->service( 'session_cookie' )->is_cookie_allowed() ) {
			\DLM_Debug_Logger::log( "Couldn't destroy DLM Session cookie, headers already set." );

			return;
		}

		// remove from DB
		Services::get()->service( 'session_repository' )->remove( $session->get_key(), $session->get_hash() );

		// remove cookie by clearing it and setting it to a negative expire date
		Services::get()->service( 'session_cookie' )->destroy_session_cookie();

		// reset internal session cache
		$this->set_session( null );
	}

	/**
	 * Destroys current session (based on reference cookie)
	 */
	public function destroy_current_session() {
		// destroy current session
		$this->destroy_session( $this->get_session() );
	}

}