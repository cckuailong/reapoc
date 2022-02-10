<?php

namespace TEC\Tickets\Commerce\Admin;

use \tad_DI52_ServiceProvider;
use TEC\Tickets\Commerce\Checkout;
use TEC\Tickets\Commerce\Success;
use \Tribe__Settings;

/**
 * Class Notices
 *
 * @since 5.2.0
 *
 * @package TEC\Tickets\Commerce\Admin
 */
class Notices extends tad_DI52_ServiceProvider {

	/**
	 * @inheritdoc
	 */
	public function register() {

		tribe_notice(
			'event-tickets-tickets-commerce-checkout-not-set',
			[ $this, 'render_checkout_notice' ],
			[ 'dismiss' => false, 'type' => 'error' ],
			[ $this, 'should_render_checkout_notice' ]
		);

		tribe_notice(
			'event-tickets-tickets-commerce-success-not-set',
			[ $this, 'render_success_notice' ],
			[ 'dismiss' => false, 'type' => 'error' ],
			[ $this, 'should_render_success_notice' ]
		);
	}

	/**
	 * Display a notice when Tickets Commerce is enabled, yet a checkout page is not setup properly.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function should_render_checkout_notice() {
		// If we're not on our own settings page, bail.
		if ( Tribe__Settings::$parent_slug !== tribe_get_request_var( 'page' ) ) {
			return false;
		}

		if ( tribe( Checkout::class )->page_has_shortcode() ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the HTML for the notice that is shown when checkout setting is not set.
	 *
	 * @since 5.2.0
	 *
	 * @return string Notice HTML.
	 */
	public function render_checkout_notice() {
		$notice_link = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( 'https://evnt.is/1axv' ),
			esc_html__( 'Learn More', 'event-tickets' )
		);
		$notice_header = esc_html__( 'Set up your checkout page', 'event-tickets' );
		$notice_text = sprintf(
			// translators: %1$s: Link to knowledgebase article.
			esc_html__( 'In order to start selling with Tickets Commerce, you\'ll need to set up your checkout page. Please configure the setting on Settings > Payments and confirm that the page you have selected has the proper shortcode. %1$s', 'event-tickets' ),
			$notice_link
		);

		return sprintf(
			'<p><strong>%1$s</strong></p><p>%2$s</p>',
			$notice_header,
			$notice_text
		);
	}

	/**
	 * Display a notice when Tickets Commerce is enabled, yet a success page is not setup properly.
	 *
	 * @since 5.2.0
	 */
	public function should_render_success_notice() {
		// If we're not on our own settings page, bail.
		if ( Tribe__Settings::$parent_slug !== tribe_get_request_var( 'page' ) ) {
			return false;
		}

		if ( tribe( Success::class )->page_has_shortcode() ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the HTML for the notice that is shown when checkout setting is not set.
	 *
	 * @since 5.2.0
	 *
	 * @return string Notice HTML.
	 */
	public function render_success_notice() {
		$notice_link = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_url( 'https://evnt.is/1axv' ),
			esc_html__( 'Learn More', 'event-tickets' )
		);
		$notice_header = esc_html__( 'Set up your order success page', 'event-tickets' );
		$notice_text = sprintf(
			// translators: %1$s: Link to knowledgebase article.
			esc_html__( 'In order to start selling with Tickets Commerce, you\'ll need to set up your order success page. Please configure the setting on Settings > Payments and confirm that the page you have selected has the proper shortcode. %1$s', 'event-tickets' ),
			$notice_link
		);

		return sprintf(
			'<p><strong>%1$s</strong></p><p>%2$s</p>',
			$notice_header,
			$notice_text
		);
	}
}
