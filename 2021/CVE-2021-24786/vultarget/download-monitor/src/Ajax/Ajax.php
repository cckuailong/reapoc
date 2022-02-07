<?php

abstract class DLM_Ajax {
	private $tag = '';

	/**
	 * @param string $tag
	 */
	public function __construct( $tag ) {
		$this->tag = $tag;
	}

	/**
	 * Register AJAX action
	 */
	public function register() {
		add_action( DLM_Ajax_Manager::ENDPOINT . $this->tag, array( $this, 'run' ) );
	}

	/**
	 * Checks AJAX nonce
	 */
	protected function check_nonce() {
		check_ajax_referer( 'dlm_ajax_nonce_' . $this->tag, 'nonce' );
	}

	/**
	 * AJAX callback method, must be overridden
	 *
	 * @return void
	 */
	public abstract function run();
}