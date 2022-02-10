<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Shortcodes__Success
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Shortcodes__Success implements Tribe__Tickets__Commerce__PayPal__Shortcodes__Interface {

	/**
	 * Returns the shortcode tag.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function tag() {
		return 'tribe-tpp-success';
	}

	/**
	 * Renders the shortcode.
	 *
	 * @since 4.7
	 *
	 * @param string|array $attributes An array of shortcode attributes.
	 * @param string       $content    The shortcode content if any.
	 *
	 * @return string
	 */
	public function render( $attributes, $content ) {
		$template = tribe( 'tickets.commerce.paypal.endpoints.templates.success' );
		$template->enqueue_resources();

		return $template->render();
	}
}
