<?php

class DLM_Ajax_Manager {
	const ENDPOINT = 'dlm-ajax';

	/**a
	 * Setup custom AJAX calls & custom AJAX handler
	 */
	public function setup() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		add_action( 'template_redirect', array( $this, 'handle_ajax' ), 0 );
		$this->add_ajax_actions();
	}

	/**
	 * Add endpoints
	 */
	public function add_endpoints() {
		add_rewrite_tag( '%' . self::ENDPOINT . '%', '([^/]*)' );
		add_rewrite_rule( self::ENDPOINT . '/([^/]*)/?', 'index.php?' . self::ENDPOINT . '=$matches[1]', 'top' );
		add_rewrite_rule( 'index.php/' . self::ENDPOINT . '/([^/]*)/?', 'index.php?' . self::ENDPOINT . '=$matches[1]', 'top' );
	}

	/**
	 * Get AJAX for given ajax
	 *
	 * @param string $action
	 * @param bool $include_nonce
	 *
	 * @return string
	 */
	public static function get_ajax_url( $action, $include_nonce = true ) {
		return untrailingslashit( home_url( sprintf( '?%s=%s&nonce=%s', self::ENDPOINT, $action, wp_create_nonce( 'dlm_ajax_nonce_' . $action ) ) ) );
	}

	/**
	 * Add AJAX actions
	 */
	private function add_ajax_actions() {

		$get_downloads = new DLM_Ajax_GetDownloads();
		$get_downloads->register();

		$get_versions = new DLM_Ajax_GetVersions();
		$get_versions->register();

		$create_page = new DLM_Ajax_CreatePage();
		$create_page->register();

	}

	/**
	 * Handle AJAX requests
	 */
	public function handle_ajax() {
		global $wp_query;

		// set AJAX action if it's set in $_GET
		if ( ! empty( $_GET[ self::ENDPOINT ] ) ) {
			$wp_query->set( self::ENDPOINT, sanitize_text_field( $_GET[ self::ENDPOINT ] ) );
		}

		// check if endpoint is not false or an empty string
		if ( false != $wp_query->get( self::ENDPOINT ) && '' != trim( $wp_query->get( self::ENDPOINT ) ) ) {

			// set AJAX action into var
			$action = sanitize_text_field( $wp_query->get( self::ENDPOINT ) );

			// set DOING AJAX to true
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			// set is_home to false
			$wp_query->is_home = false;

			// run custom AJAX action
			do_action( self::ENDPOINT . $action );

			// bye
			die();

		}
	}
}