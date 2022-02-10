<?php

namespace Tribe\Tickets\Admin\Settings;

use tad_DI52_ServiceProvider;

/**
 * Class Manager
 *
 * @package Tribe\Tickets\Admin\Settings
 *
 * @since   5.1.2
 */
class Service_Provider extends tad_DI52_ServiceProvider {
	/**
	 * Register the provider singletons.
	 *
	 * @since 5.1.2
	 */
	public function register() {
		$this->container->singleton( 'tickets.admin.settings', self::class );

		$this->hooks();
	}

	/**
	 * Add actions and filters.
	 *
	 * @since 5.1.2
	 */
	protected function hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'tribe_settings_before_content_tab_event-tickets', [ $this, 'render_settings_banner' ] );

		add_filter( 'tec_tickets_commerce_settings', [ $this, 'maybe_render_tickets_commerce_upgrade_banner' ] );
		add_filter( 'tec_tickets_commerce_settings', [ $this, 'maybe_render_tickets_commerce_notice_banner' ] );
	}

	/**
	 * Render the Help banner for the Ticket Settings Tab.
	 *
	 * @since 5.1.2
	 *
	 * @return string The help banner HTML content.
	 */
	public function render_settings_banner() {
		$et_resource_links = [
			[
				'label' => __( 'Getting Started Guide', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aot',
			],
			[
				'label' => __( 'Event Tickets Manual', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aoz',
			],
			[
				'label' => __( 'What is Tickets Commerce?', 'event-tickets' ),
				'href'  => 'https://evnt.is/1axs',
				'new'   => true,
			],
			[
				'label' => __( 'Configuring Tickets Commerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1axt',
				'new'   => true,
			],
			[
				'label' => __( 'Using RSVPs', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aox',
			],
			[
				'label' => __( 'Managing Orders and Attendees', 'event-tickets' ),
				'href'  => 'https://evnt.is/1aoy',
			],
		];

		$etp_resource_links = [
			[
				'label' => __( 'Switching from Tribe Commerce to WooCommerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ao-',
			],
			[
				'label' => __( 'Setting Up E-Commerce Plugins for Selling Tickets', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap0',
			],
			[
				'label' => __( 'Tickets & WooCommerce', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap1',
			],
			[
				'label' => __( 'Creating Tickets', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap2',
			],
			[
				'label' => __( 'Event Tickets and Event Tickets Plus Settings Overview', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap3',
			],
			[
				'label' => __( 'Event Tickets Plus Manual', 'event-tickets' ),
				'href'  => 'https://evnt.is/1ap4',
			],
		];

		$context = [
			'etp_enabled'        => class_exists( 'Tribe__Tickets_Plus__Main' ),
			'et_resource_links'  => $et_resource_links,
			'etp_resource_links' => $etp_resource_links,
		];

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );

		return $admin_views->template( 'settings/getting-started', $context );
	}

	/**
	 * Render the Tickets Commerce Upgrade banner for the Ticket Settings Tab.
	 *
	 * @since 5.2.0
	 *
	 * @return array The help banner HTML content array.
	 */
	public function maybe_render_tickets_commerce_upgrade_banner( $commerce_fields ) {

		// Don't load for new installs, where TribeCommerce settings are not shown.
		if ( ! isset( $commerce_fields['ticket-paypal-heading'] ) ) {
			return $commerce_fields;
		}

		// Check if Tribe Commerce tickets are active.
		$has_active_tickets = tec_tribe_commerce_has_active_tickets();

		if ( ! $has_active_tickets ) {
			return $commerce_fields;
		}

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );
		$banner_html = $admin_views->template( 'settings/tickets-commerce/banner', [
			'banner_title'   => __( 'Upgrade to Tickets Commerce', 'event-tickets' ),
			'banner_content' => __( 'Try our new Tickets Commerce payment system! It’s fast and simple to set up and offers a better experience and features. Best of all, <i>it’s free!</i>', 'event-tickets' ),
			'button_text'    => __( 'Get started', 'event-tickets' ),
			'button_url'     => \Tribe__Settings::instance()->get_url( [ 'tab' => 'payments' ] ),
			'link_text'      => __( 'Learn more', 'event-tickets' ),
			'link_url'       => 'https://evnt.is/1axt',
			'show_new'       => true,
		], false );

		// Add the banner html after the Tribe Commerce settings header.
		$commerce_fields['ticket-paypal-heading']['html'] .= $banner_html;

		return $commerce_fields;
	}

	/**
	 * Render the Tickets Commerce Notice banner for the Ticket Settings Tab.
	 *
	 * @since 5.2.0
	 *
	 * @return array The help banner HTML content array.
	 */
	public function maybe_render_tickets_commerce_notice_banner( $commerce_fields ) {

		// If fields are already set, that means TribeCommerce is active and we should not show this notice.
		if ( isset( $commerce_fields['ticket-paypal-heading'] ) ) {
			return $commerce_fields;
		}

		// Hide the notice, once tickets commerce is enabled.
		if ( tec_tickets_commerce_is_enabled() ) {
			return $commerce_fields;
		}

		// If new install, bail.
		if ( tribe_installed_after( 'Tribe__Tickets__Main', '5.1.10' ) ) {
			return $commerce_fields;
		}

		// If no Tribe Commerce tickets ever created, bail.
		$ticket_count = tribe_tickets()->by( 'post_type', 'tribe_tpp_tickets' )->count();
		if ( 0 === $ticket_count ) {
			return $commerce_fields;
		}

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );
		$banner_html = $admin_views->template( 'settings/tickets-commerce/banner', [
			'banner_title'   => __( 'Upgrade to Tickets Commerce', 'event-tickets' ),
			'banner_content' => __( 'Tribe Commerce has been replaced by our new payments system, Tickets Commerce. It’s fast, free and simple to set up! You can <a href="https://evnt.is/1axu" rel="noopener noreferrer" target="_blank">still continue using Tribe Commerce</a> but we highly recommend upgrading to Tickets Commerce.', 'event-tickets' ),
			'button_text'    => __( 'Get Started', 'event-tickets' ),
			'button_url'     => \Tribe__Settings::instance()->get_url( [ 'tab' => 'payments' ] ),
			'link_text'      => __( 'Learn more', 'event-tickets' ),
			'link_url'       => 'https://evnt.is/1axt',
			'show_new'       => true,
		], false );

		// Add the banner html after the Tribe Commerce settings header.
		$commerce_fields = [
			'ticket-paypal-heading' => [
				'type' => 'html',
				'html' => '<h3>' . __( 'Tribe Commerce', 'event-tickets' ) . '</h3>',
			],
			'ticket-paypal-notice' => [
				'type' => 'html',
				'html' => $banner_html,
			],
		];

		return $commerce_fields;
	}
}
