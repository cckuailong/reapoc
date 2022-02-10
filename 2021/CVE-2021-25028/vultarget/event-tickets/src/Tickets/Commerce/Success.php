<?php

namespace TEC\Tickets\Commerce;

/**
 * Class Success
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Success {
	/**
	 * Param we use to store the order ID.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $order_id_query_arg = 'tc-order-id';

	/**
	 * Get the Success page ID.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @return int|null
	 */
	public function get_page_id() {
		$success_page = (int) tribe_get_option( Settings::$option_success_page );

		if ( empty( $success_page ) ) {
			return null;
		}

		/**
		 * Allows filtering of the Page ID for the Success page.
		 *
		 * @since 5.1.9
		 *
		 * @param int|null $success_page Which page is used in the settings.
		 */
		return apply_filters( 'tec_tickets_commerce_success_page_id', $success_page );
	}

	/**
	 * Determine the Current success URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_url() {
		$url = home_url( '/' );
		$success_page = $this->get_page_id();

		if ( is_numeric( $success_page ) ) {
			$success_page = get_post( $success_page );
		}

		// Only modify the URL in case we have a success page setup in the settings.
		if ( $success_page instanceof \WP_Post ) {
			$url = get_the_permalink( $success_page );
		}

		/**
		 * Allows modifications to the success url for Tickets Commerce.
		 *
		 * @since 5.1.9
		 *
		 * @param string $url URL for the cart.
		 */
		return (string) apply_filters( 'tec_tickets_commerce_success_url', $url );
	}

	/**
	 * Determines if the current page is the success page.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @return bool
	 */
	public function is_current_page() {
		if ( is_admin() ) {
			return false;
		}

		$current_page = get_queried_object_id();
		$is_current_page = $this->get_page_id() === $current_page;

		/**
		 * @todo determine hte usage of tribe_ticket_redirect_to
		 * 		$redirect = tribe_get_request_var( 'tribe_tickets_redirect_to', null );
		 */

		/**
		 * Allows modifications to the conditional of if we are in the success page.
		 *
		 * @since 5.1.9
		 *
		 * @param bool $is_current_page Are we in the current page for checkout.
		 */
		return tribe_is_truthy( apply_filters( 'tec_tickets_commerce_success_is_current_page', $is_current_page ) );
	}

	/**
	 * If there is any data or request management or parsing that needs to happen on the success page here is where
	 * we do it.
	 *
	 * @since 5.1.9
	 */
	public function parse_request() {
		if ( ! $this->is_current_page() ) {
			return;
		}

		// In case the ID is passed we set the cookie for usage.
		$cookie_param = tribe_get_request_var( Cart::$cookie_query_arg, false );
		if ( $cookie_param ) {
			tribe( Cart::class )->set_cart_hash_cookie( $cookie_param );
		}
	}

	/**
	 * Maybe add a post display state for special Tickets Commerce Success Page in the page list table.
	 *
	 * @since 5.1.10
	 *
	 * @param array   $post_states An array of post display states.
	 * @param WP_Post $post        The current post object.
	 *
	 * @return array  $post_states An array of post display states.
	 */
	public function maybe_add_display_post_states( $post_states, $post ) {

		if ( $this->get_page_id() === $post->ID ) {
			$post_states['tec_tickets_commerce_page_success'] = __( 'Tickets Commerce Success Page', 'event-tickets' );
		}

		return $post_states;
	}

	/**
	 * Determines whether or not the success page option is set.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function is_option_set() {
		$page = $this->get_page_id();
		return ! empty( $page );
	}

	/**
	 * Determines whether or not the success page has the appropriate shortcode in the content.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function page_has_shortcode() {
		if ( ! $this->is_option_set() ) {
			return false;
		}

		$page = get_post( $this->get_page_id() );

		if ( ! $page instanceof \WP_Post ) {
			return false;
		}

		$shortcode = Shortcodes\Success_Shortcode::get_wp_slug();
		return has_shortcode( $page->post_content, $shortcode );
	}
}