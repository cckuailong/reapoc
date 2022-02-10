<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Endpoints
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Endpoints {

	/**
	 * Hooks the actions and filters needed by the class to work.
	 *
	 * @since 4.7
	 */
	public function hook() {
		add_action( 'template_redirect', array( $this, 'maybe_redirect' ) );
	}

	/**
	 * Redirects the
	 */
	public function maybe_redirect() {
		// does not look like a PayPal request
		if ( ! isset( $_GET['tx'], $_GET['cm'] ) ) {
			return;
		}

		$custom_data = Tribe__Tickets__Commerce__PayPal__Custom_Argument::decode( ($_GET['cm']),true );

		// does not look like a PayPal request that PayPal tickets should handle
		if ( ! isset( $custom_data['tribe_handler'] ) || 'tpp' !== $custom_data['tribe_handler'] ) {
			return;
		}

		$post_id = Tribe__Utils__Array::get( $custom_data, 'pid', null );

		wp_safe_redirect( $this->success_url( $_GET['tx'], $post_id ) );
		tribe_exit();
	}

	/**
	 * Returns the full URL to the success endpoint.
	 *
	 * @since 4.7
	 *
	 * @param string $order   The order alphanumeric string.
	 * @param int    $post_id The ID of the post tickets were purchased from.
	 *
	 * @return string
	 */
	public function success_url( $order = '', $post_id = null ) {
		$success_page_id = tribe_get_option( 'ticket-paypal-success-page', false );

		$page = get_post( $success_page_id );

		if ( ! empty( $page ) && 'page' === $page->post_type ) {
			$url = add_query_arg( array( 'p' => $success_page_id, 'tribe-tpp-order' => $order ), home_url() );
		} else {
			// use the post single page; see `Tribe__Tickets__Commerce__PayPal__Errors` for the message code
			$url = add_query_arg( array( 'tribe-tpp-order' => $order, 'tpp_message' => 201 ), get_permalink( $post_id ) );
		}

		return $url;
	}
}
