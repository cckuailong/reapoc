<?php

namespace TEC\Tickets\Commerce;

/**
 * Class Checkout
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Checkout {
	/**
	 * Which URL param we use to identify a given page as the checkout.
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	public static $url_query_arg = 'tec-tc-checkout';

	/**
	 * Get the Checkout page ID.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @return int|null
	 */
	public function get_page_id() {
		$checkout_page = (int) tribe_get_option( Settings::$option_checkout_page );

		if ( empty( $checkout_page ) ) {
			return null;
		}

		/**
		 * Allows filtering of the Page ID for the Checkout page.
		 *
		 * @since 5.1.9
		 *
		 * @param int|null $checkout_page Which page is used in the settings.
		 */
		return apply_filters( 'tec_tickets_commerce_checkout_page_id', $checkout_page );
	}

	/**
	 * Determine the Current checkout URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_url() {
		$url = home_url( '/' );
		$checkout_page = $this->get_page_id();

		if ( is_numeric( $checkout_page ) ) {
			$checkout_page = get_post( $checkout_page );
		}

		// Only modify the URL in case we have a checkout page setup in the settings.
		if ( $checkout_page instanceof \WP_Post ) {
			$url = get_the_permalink( $checkout_page );
		}

		/**
		 * Allows modifications to the checkout url for Tickets Commerce.
		 *
		 * @since 5.1.9
		 *
		 * @param string $url URL for the cart.
		 */
		return (string) apply_filters( 'tec_tickets_commerce_checkout_url', $url );
	}

	/**
	 * Determines if the current page is the Checkout page.
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
		 * Allows modifications to the conditional of if we are in the checkout page.
		 *
		 * @since 5.1.9
		 *
		 * @param bool $is_current_page Are we in the current page for checkout.
		 */
		return tribe_is_truthy( apply_filters( 'tec_tickets_commerce_checkout_is_current_page', $is_current_page ) );
	}

	/**
	 * If there is any data or request management or parsing that needs to happen on the Checkout page here is where
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
	 * Get the login URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_login_url() {
		$login_url = get_site_url( null, 'wp-login.php' );

		$login_url = add_query_arg( 'redirect_to', $this->get_url(), $login_url );

		/**
		 * Provides an opportunity to modify the login URL used within frontend
		 * checkout (typically when they need to login before they can proceed).
		 *
		 * @since 5.1.9
		 *
		 * @param string $login_url
		 */
		return apply_filters( 'tec_tickets_commerce_checkout_login_url', $login_url );
	}

	/**
	 * Get the registration URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_registration_url() {
		$registration_url = wp_registration_url();

		$registration_url = add_query_arg( 'redirect_to', $this->get_url(), $registration_url );

		/**
		 * Provides an opportunity to modify the registration URL used within frontend
		 * checkout (typically when they need to login before they can proceed).
		 *
		 * @since 5.1.9
		 *
		 * @param string $login_url
		 */
		return apply_filters( 'tec_tickets_commerce_checkout_registration_url', $registration_url );
	}

	/**
	 * Maybe add a post display state for special Tickets Commerce Checkout Page in the page list table.
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
			$post_states['tec_tickets_commerce_page_checkout'] = __( 'Tickets Commerce Checkout Page', 'event-tickets' );
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

		$shortcode = Shortcodes\Checkout_Shortcode::get_wp_slug();
		return has_shortcode( $page->post_content, $shortcode );
	}
}