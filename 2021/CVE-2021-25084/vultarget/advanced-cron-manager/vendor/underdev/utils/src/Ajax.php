<?php
/**
 * Ajax class
 * Handles AJAX calls
 */

namespace underDEV\Utils;

class Ajax {

	/**
	 * Verifies nonce string
	 * @param  string $action action name, as defined while creating nonce hash, required
	 * @param  string $nonce  $_REQUEST array key where to search for nonce, default 'nonce'
	 * @return void           dies when nonce is wrong
	 */
	public function verify_nonce( $action = null, $nonce = 'nonce' ) {

		if ( $action === null ) {
			trigger_error( 'Action cannot be empty' );
		}

		if ( check_ajax_referer( $action, $nonce, false ) == false ) {
	        $this->error( array( 'wrong_nonce' ) );
	    }

	}

	/**
	 * Prints success for JS
	 * @param  mixed $data anything
	 * @return void
	 */
	public function success( $data ) {
		wp_send_json_success( $data );
	}

	/**
	 * Prints error for JS
	 * @param  mixed $data anything
	 * @return void
	 */
	public function error( $data ) {
		wp_send_json_error( $data );
	}

	/**
	 * Responds to JS with message
	 * @param  mixed  $success if empty, nothing will be passed
	 * @param  array  $errors  if not empty, an error will be returned
	 * @return void
	 */
	public function response( $success = null, $errors = array() ) {

		if ( ! empty( $errors ) ) {
			$this->error( $errors );
		}

		$this->success( $success );

	}

}
