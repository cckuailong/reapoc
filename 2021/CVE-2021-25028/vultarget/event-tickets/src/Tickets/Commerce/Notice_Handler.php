<?php

namespace TEC\Tickets\Commerce;

/**
 * Notice Handler for managing Admin view notices.
 *
 * @since 5.2.0
 *
 * @package TEC\Tickets\Commerce
 */
class Notice_Handler {
	/**
	 * Resets the cache with the key from `\Tribe_Admin_Notices->get_transients()` so that
	 * we can display a given notice on the same page that we are triggering it.
	 *
	 * This is here because of a possible bug in common.
	 *
	 * @since 5.2.0
	 */
	private function clear_request_cache() {
		// Clear the existing notices cache.
		$cache = tribe( 'cache' );
		unset( $cache['transient_admin_notices'] );
	}

	/**
	 * Fetches the expiration in seconds for the transient notice.
	 *
	 * @see   tribe_transient_notice()
	 *
	 * @since 5.2.0
	 *
	 * @param string $slug Slug for the notice.
	 *
	 * @return int
	 */
	protected function get_expiration( $slug ) {
		/**
		 * Filters the available notice messages.
		 *
		 * @since 5.2.0
		 *
		 * @param int    $duration Duration in seconds to expire.
		 * @param string $slug     Slug for the notice to display.
		 */
		return (int) apply_filters( 'tec_tickets_commerce_notice_expiration', 10, $slug );
	}

	/**
	 * Fetches the array of all messages available to display.
	 *
	 * @since 5.2.0
	 *
	 * @return array[]
	 */
	public function get_messages() {
		$messages = [];

		/**
		 * Filters the available notice messages.
		 *
		 * @since 5.2.0
		 *
		 * @param array $messages Array of notice messages.
		 */
		return (array) apply_filters( 'tec_tickets_commerce_notice_messages', $messages );
	}

	/**
	 * Determines if a message exists with a given slug.
	 *
	 * @since 5.2.0
	 *
	 * @param string $slug
	 *
	 * @return bool
	 */
	public function message_slug_exists( $slug ) {
		$messages = $this->get_messages();
		$messages = wp_list_filter( $messages, [ 'slug' => $slug ] );
		$messages = array_values( $messages );

		return ! empty( $messages[0] );
	}

	/**
	 * Gets a given message data by it's slug.
	 *
	 * @since 5.2.0
	 *
	 * @param string $slug Slug to retrieve the message data.
	 *
	 * @return array
	 */
	public function get_message_data( $slug, $overrides = [] ) {

		$default_args = [
			'slug'     => $slug,
			'expire'   => true,
			'wrap'     => 'p',
			'type'     => 'error',
			'content'  => '',
			'priority' => 10,
		];

		// If not found in message array, return with defaults.
		if ( ! $this->message_slug_exists( $slug ) ) {
			return array_merge( $default_args, $overrides );
		}

		$message = array_values( wp_list_filter( $this->get_messages(), [ 'slug' => $slug ] ) )[0];

		return array_merge( $default_args, $message, $overrides );
	}

	/**
	 * Merges the content of a given set of Notice slugs.
	 *
	 * @since 5.2.0
	 *
	 * @param array $slugs Array of slugs that will be merged.
	 *
	 * @return string $html List with message content from each slug.
	 */
	public function merge_contents( array $slugs ) {
		$messages = array_map( [ $this, 'get_message_data' ], $slugs );

		$html[] = '<ul>';
		foreach ( $messages as $message ) {
			$list_class = sanitize_html_class( 'tec-tickets-commerce-notice-item-' . $message['slug'] );
			$html[]     = "<li class='{$list_class}'>";
			$html[]     = $message['content'];
			$html[]     = '</li>';
		}
		$html[] = '</ul>';

		return implode( "\n", $html );
	}

	/**
	 * Add an admin notice that should only show once.
	 *
	 * @since 5.2.0
	 *
	 * @param string $slug Slug to store the notice.
	 * @param array  $args Arguments to set up a notice.
	 *
	 * @see Tribe__Admin__Notices::register for available $args options.
	 */
	public function trigger_admin( $slug, $args = [] ) {

		$message = $this->get_message_data( $slug, $args );

		tribe_transient_notice( $slug, $message['content'], $message, $this->get_expiration( $slug ) );

		// This is here because of a possible bug in common.
		$this->clear_request_cache();
	}
}